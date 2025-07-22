<?php 
// Memuat bagian header
include 'template/header.php';

// Proses penyimpanan data ketika form disubmit
if(isset($_POST['simpan'])){
    // Ambil data dari form
    $id_pelanggan = $_POST['id_pelanggan'];
    $bulan = $_POST['bulan'];
    $tahun = $_POST['tahun'];
    $meter_awal = $_POST['meter_awal'];
    $meter_akhir = $_POST['meter_akhir'];

    // Cek apakah data penggunaan untuk pelanggan dan periode tersebut sudah ada (hanya untuk tambah, bukan edit)
    $cek = mysqli_query($koneksi, "SELECT * FROM penggunaan WHERE id_pelanggan='$id_pelanggan' AND bulan='$bulan' AND tahun='$tahun'");
    if (mysqli_num_rows($cek) > 0 && !isset($_GET['edit'])) {
        echo "<script>alert('Data penggunaan untuk pelanggan dan periode ini sudah ada!');</script>";
    } else {
        // Jika edit data
        if(isset($_GET['edit'])) {
            mysqli_query($koneksi, "UPDATE penggunaan SET 
                id_pelanggan='$id_pelanggan', 
                bulan='$bulan', 
                tahun='$tahun', 
                meter_awal='$meter_awal', 
                meter_akhir='$meter_akhir' 
                WHERE id_penggunaan='{$_GET['edit']}'");
        } else {
            // Jika data baru, insert
            mysqli_query($koneksi, "INSERT INTO penggunaan 
                (id_pelanggan, bulan, tahun, meter_awal, meter_akhir) 
                VALUES 
                ('$id_pelanggan', '$bulan', '$tahun', '$meter_awal', '$meter_akhir')");
        }
        // Redirect kembali ke halaman utama
        echo "<script>window.location.href='penggunaan.php'</script>";
    }
}

// Proses hapus data
if(isset($_GET['hapus'])){
    mysqli_query($koneksi, "DELETE FROM penggunaan WHERE id_penggunaan='{$_GET['hapus']}'");
    echo "<script>window.location.href='penggunaan.php'</script>";
}

// Default nilai awal form
$p = [
    'id_pelanggan' => '',
    'bulan' => '',
    'tahun' => date('Y'), // Default tahun saat ini
    'meter_awal' => '',
    'meter_akhir' => ''
];

// Jika sedang dalam mode edit, ambil data penggunaan yang akan diedit
if(isset($_GET['edit'])){
    $query = mysqli_query($koneksi, "SELECT * FROM penggunaan WHERE id_penggunaan='{$_GET['edit']}'");
    $p = mysqli_fetch_assoc($query);
}
?>

<?php include 'template/sidebar.php'; // Sidebar navigasi ?>

<div id="content">
    <div class="content-header">
        <h2>Data Penggunaan Listrik</h2>
    </div>

    <!-- Form Input/Edit Penggunaan -->
    <div class="card">
        <div class="card-header">
            <h3><?php echo isset($_GET['edit']) ? 'Edit' : 'Input'; ?> Penggunaan</h3>
        </div>
        <form action="" method="POST">
            <div class="form-grid">
                <!-- Pilih pelanggan -->
                <div class="form-group" style="grid-column: 1 / -1;">
                    <label>Pelanggan</label>
                    <select name="id_pelanggan" required>
                        <option value="">-- Pilih Pelanggan --</option>
                        <?php 
                        $q_pelanggan = mysqli_query($koneksi, "SELECT * FROM pelanggan");
                        while ($pel = mysqli_fetch_assoc($q_pelanggan)) {
                            $sel = ($pel['id_pelanggan'] == $p['id_pelanggan']) ? 'selected' : '';
                            echo "<option value='{$pel['id_pelanggan']}' {$sel}>{$pel['nomor_kwh']} - {$pel['nama_pelanggan']}</option>";
                        }
                        ?>
                    </select>
                </div>

                <!-- Pilih bulan -->
                <div class="form-group">
                    <label>Bulan</label>
                    <select name="bulan" required>
                        <?php 
                        $bln_arr = ["Januari","Februari","Maret","April","Mei","Juni","Juli","Agustus","September","Oktober","November","Desember"];
                        foreach ($bln_arr as $b) {
                            $sel = ($b == $p['bulan']) ? 'selected' : '';
                            echo "<option value='{$b}' {$sel}>{$b}</option>";
                        }
                        ?>
                    </select>
                </div>

                <!-- Input tahun -->
                <div class="form-group">
                    <label>Tahun</label>
                    <input type="number" name="tahun" value="<?php echo $p['tahun']; ?>" required>
                </div>

                <!-- Input meter awal -->
                <div class="form-group">
                    <label>Meter Awal</label>
                    <input type="number" name="meter_awal" value="<?php echo $p['meter_awal']; ?>" required>
                </div>

                <!-- Input meter akhir -->
                <div class="form-group">
                    <label>Meter Akhir</label>
                    <input type="number" name="meter_akhir" value="<?php echo $p['meter_akhir']; ?>" required>
                </div>
            </div>

            <!-- Tombol Simpan dan Batal -->
            <div class="form-actions">
                <button type="submit" name="simpan" class="btn btn-success">Simpan</button>
                <?php if(isset($_GET['edit'])): ?>
                    <a href="penggunaan.php" class="btn btn-secondary">Batal</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- Tabel Riwayat Penggunaan -->
    <div class="card">
        <div class="card-header">
            <h3>Riwayat Penggunaan</h3>
        </div>
        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Pelanggan</th>
                        <th>Periode</th>
                        <th>Meter Awal</th>
                        <th>Meter Akhir</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1;
                    $query = mysqli_query($koneksi, 
                        "SELECT pg.*, pl.nama_pelanggan 
                        FROM penggunaan pg 
                        JOIN pelanggan pl ON pg.id_pelanggan = pl.id_pelanggan 
                        ORDER BY tahun DESC, bulan DESC");

                    while($data = mysqli_fetch_assoc($query)){
                    ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td><?php echo $data['nama_pelanggan']; ?></td>
                        <td><?php echo $data['bulan'] . ' ' . $data['tahun']; ?></td>
                        <td><?php echo $data['meter_awal']; ?></td>
                        <td><?php echo $data['meter_akhir']; ?></td>
                        <td>
                            <!-- Tombol edit dan hapus -->
                            <a href="penggunaan.php?edit=<?php echo $data['id_penggunaan']; ?>" class="btn btn-warning">Edit</a>
                            <a href="penggunaan.php?hapus=<?php echo $data['id_penggunaan']; ?>" class="btn btn-danger" onclick="return confirm('Yakin hapus?')">Hapus</a>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'template/footer.php'; // Memuat bagian footer ?>
