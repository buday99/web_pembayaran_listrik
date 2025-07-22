<?php 
// Memuat bagian header
include 'template/header.php'; 

// ==== LOGIKA TAMBAH & EDIT & HAPUS DATA TARIF ====
if(isset($_POST['simpan'])){
    // Ambil input dari form
    $daya = $_POST['daya'];
    $tarif_per_kwh = $_POST['tarif_per_kwh'];

    // Jika sedang dalam mode edit
    if(isset($_GET['edit'])){
        $id = $_GET['edit'];
        // Perbarui data tarif berdasarkan ID
        mysqli_query($koneksi, "UPDATE tarif SET daya='$daya', tarif_per_kwh='$tarif_per_kwh' WHERE id_tarif='$id'");
    } else {
        // Jika bukan edit, berarti tambah data baru
        mysqli_query($koneksi, "INSERT INTO tarif (daya, tarif_per_kwh) VALUES ('$daya', '$tarif_per_kwh')");
    }

    // Redirect kembali ke halaman tarif
    echo "<script>window.location.href='tarif.php'</script>";
}

// Jika parameter 'hapus' dikirim lewat GET, maka hapus data tarif
if(isset($_GET['hapus'])){
    $id = $_GET['hapus'];
    mysqli_query($koneksi, "DELETE FROM tarif WHERE id_tarif='$id'");
    echo "<script>window.location.href='tarif.php'</script>";
}

// AMBIL DATA TARIF UNTUK FORM EDIT
$daya = "";
$tarif_per_kwh = "";
if(isset($_GET['edit'])){
    $id = $_GET['edit'];
    // Ambil data tarif berdasarkan ID untuk diisi ke form edit
    $query = mysqli_query($koneksi, "SELECT * FROM tarif WHERE id_tarif='$id'");
    $data = mysqli_fetch_assoc($query);
    $daya = $data['daya'];
    $tarif_per_kwh = $data['tarif_per_kwh'];
}
?>

<?php 
// Menyertakan sidebar
include 'template/sidebar.php'; 
?>

<div id="content">
    <!-- Header Konten -->
    <div class="content-header">
        <h2>Data Tarif</h2>
    </div>

    <!-- FORM TAMBAH/EDIT TARIF -->
    <div class="card">
        <div class="card-header">
            <h3><?php echo isset($_GET['edit']) ? 'Edit' : 'Tambah'; ?> Data Tarif</h3>
        </div>
        <form action="" method="POST">
            <label>Daya (VA)</label>
            <input type="text" name="daya" value="<?php echo $daya; ?>" required>

            <label>Tarif per KWH</label>
            <input type="number" name="tarif_per_kwh" value="<?php echo $tarif_per_kwh; ?>" required>

            <button type="submit" name="simpan" class="btn btn-success">Simpan</button>
        </form>
    </div>

    <!-- TABEL DAFTAR TARIF -->
    <div class="card">
        <div class="card-header">
            <h3>Daftar Tarif</h3>
        </div>
        <table class="table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Daya</th>
                    <th>Tarif / KWH</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                // Tampilkan semua data tarif dari database
                $no = 1;
                $query = mysqli_query($koneksi, "SELECT * FROM tarif ORDER BY id_tarif DESC");
                while($data = mysqli_fetch_assoc($query)){
                ?>
                <tr>
                    <td><?php echo $no++; ?></td>
                    <td><?php echo $data['daya']; ?> VA</td>
                    <td>Rp <?php echo number_format($data['tarif_per_kwh']); ?></td>
                    <td>
                        <!-- Tombol Edit dan Hapus -->
                        <a href="tarif.php?edit=<?php echo $data['id_tarif']; ?>" class="btn btn-warning">Edit</a>
                        <a href="tarif.php?hapus=<?php echo $data['id_tarif']; ?>" class="btn btn-danger" onclick="return confirm('Yakin hapus?')">Hapus</a>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php 
// Menyertakan file footer
include 'template/footer.php'; 
?>
