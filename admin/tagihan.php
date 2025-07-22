<?php 
// Memuat bagian header
include 'template/header.php'; 

// Proses ketika tombol "Generate" diklik
if(isset($_POST['generate'])){
    $bulan = $_POST['bulan']; 
    $tahun = $_POST['tahun'];

    // Query mencari data penggunaan listrik yang belum memiliki tagihan pada bulan dan tahun tertentu
    $sql = "SELECT * FROM penggunaan pg 
            WHERE NOT EXISTS (
                SELECT 1 FROM tagihan tg 
                WHERE tg.id_penggunaan = pg.id_penggunaan
            ) 
            AND pg.bulan='$bulan' AND pg.tahun='$tahun'";

    $query_penggunaan = mysqli_query($koneksi, $sql);
    $jumlah_generate = 0;

    // Melakukan perulangan untuk tiap data penggunaan yang ditemukan
    while ($penggunaan = mysqli_fetch_array($query_penggunaan)) {
        $id_penggunaan = $penggunaan['id_penggunaan']; 
        $id_pelanggan = $penggunaan['id_pelanggan']; 
        $jumlah_meter = $penggunaan['meter_akhir'] - $penggunaan['meter_awal'];

        // Insert data tagihan berdasarkan data penggunaan
        $insert_tagihan = mysqli_query($koneksi, 
            "INSERT INTO tagihan 
            (id_penggunaan, id_pelanggan, bulan, tahun, jumlah_meter, status) 
            VALUES 
            ('$id_penggunaan', '$id_pelanggan', '$bulan', '$tahun', '$jumlah_meter', 'Belum Lunas')");

        if($insert_tagihan){ $jumlah_generate++; }
    }

    // Tampilkan pesan hasil generate tagihan
    if($jumlah_generate > 0){ 
        echo "<script>alert('Berhasil men-generate {$jumlah_generate} tagihan baru!'); window.location.href='tagihan.php'</script>";
    } else { 
        echo "<script>alert('Tidak ada tagihan baru untuk digenerate pada periode ini.'); window.location.href='tagihan.php'</script>"; 
    }
}

// Proses hapus tagihan berdasarkan parameter GET
if(isset($_GET['hapus'])){
    mysqli_query($koneksi, "DELETE FROM tagihan WHERE id_tagihan='{$_GET['hapus']}'"); // hapus tagihan
    mysqli_query($koneksi, "DELETE FROM pembayaran WHERE id_tagihan='{$_GET['hapus']}'"); // hapus juga data pembayarannya jika ada
    echo "<script>window.location.href='tagihan.php'</script>";
}
?>

<?php include 'template/sidebar.php'; // Menyisipkan sidebar ?>

<div id="content">
    <div class="content-header">
        <h2>Data Tagihan</h2>
    </div>

    <!-- Form untuk generate tagihan otomatis -->
    <div class="card">
        <div class="card-header"><h3>Generate Tagihan</h3></div>
        <form action="" method="POST">
            <p>Pilih periode untuk membuat tagihan secara otomatis dari data penggunaan yang belum ditagih.</p>
            <div class="form-grid" style="grid-template-columns: 1fr 1fr 1fr;">
                <!-- Dropdown bulan -->
                <div class="form-group">
                    <label>Bulan</label>
                    <select name="bulan" required>
                        <?php 
                        $bln_arr = ["Januari","Februari","Maret","April","Mei","Juni","Juli","Agustus","September","Oktober","November","Desember"]; 
                        foreach ($bln_arr as $b) { 
                            echo "<option value='{$b}'>{$b}</option>"; 
                        } 
                        ?>
                    </select>
                </div>
                <!-- Input tahun -->
                <div class="form-group">
                    <label>Tahun</label>
                    <input type="number" name="tahun" value="<?php echo date('Y'); ?>" required>
                </div>
                <!-- Tombol Generate -->
                <div class="form-group" style="align-self: flex-end;">
                    <button type="submit" name="generate" class="btn btn-primary" style="width:100%;" onclick="return confirm('Anda yakin ingin generate tagihan untuk periode ini?')">Generate</button>
                </div>
            </div>
        </form>
    </div>

    <!-- Tabel daftar tagihan -->
    <div class="card">
        <div class="card-header"><h3>Daftar Tagihan</h3></div>
        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Pelanggan</th>
                        <th>Periode</th>
                        <th>Jumlah Meter</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1; 
                    // Query menampilkan semua data tagihan yang di-join dengan nama pelanggan
                    $query = mysqli_query($koneksi, "SELECT t.*, p.nama_pelanggan FROM tagihan t JOIN pelanggan p ON t.id_pelanggan = p.id_pelanggan ORDER BY id_tagihan DESC");
                    while($data = mysqli_fetch_assoc($query)){
                        // Menentukan warna badge berdasarkan status
                        if ($data['status'] == 'Lunas') { 
                            $badge = 'badge-success'; 
                        } else if ($data['status'] == 'Belum Lunas') { 
                            $badge = 'badge-danger'; 
                        } else { 
                            $badge = 'badge-warning'; 
                        }
                    ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td><?php echo $data['nama_pelanggan']; ?></td>
                        <td><?php echo $data['bulan'] . ' ' . $data['tahun']; ?></td>
                        <td><?php echo $data['jumlah_meter']; ?> KWH</td>
                        <td><span class="badge <?php echo $badge; ?>"><?php echo $data['status']; ?></span></td>
                        <td>
                            <!-- Tombol hapus -->
                            <a href="tagihan.php?hapus=<?php echo $data['id_tagihan']; ?>" class="btn btn-danger" onclick="return confirm('Yakin hapus tagihan ini?')">Hapus</a>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'template/footer.php'; // Menyisipkan footer ?>
