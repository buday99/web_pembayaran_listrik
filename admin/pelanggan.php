<?php 
// Memuat bagian header
include 'template/header.php';

// LOGIKA SIMPAN DATA (TAMBAH / EDIT)
if(isset($_POST['simpan'])){
    // Ambil data dari form
    $nama_pelanggan = $_POST['nama_pelanggan'];
    $nomor_kwh = $_POST['nomor_kwh'];
    $alamat = $_POST['alamat'];
    $id_tarif = $_POST['id_tarif'];
    $username = $_POST['username'];

    // Jika mode edit data
    if(isset($_GET['edit'])){
        $id = $_GET['edit'];
        // Jika password diisi, update semuanya termasuk password
        if(!empty($_POST['password'])){
            $password = md5($_POST['password']); // Enkripsi MD5
            mysqli_query($koneksi, "UPDATE pelanggan SET 
                nama_pelanggan='$nama_pelanggan', 
                nomor_kwh='$nomor_kwh', 
                alamat='$alamat', 
                id_tarif='$id_tarif', 
                username='$username', 
                password='$password' 
                WHERE id_pelanggan='$id'");
        } else {
            // Jika password dikosongkan, update selain password
            mysqli_query($koneksi, "UPDATE pelanggan SET 
                nama_pelanggan='$nama_pelanggan', 
                nomor_kwh='$nomor_kwh', 
                alamat='$alamat', 
                id_tarif='$id_tarif', 
                username='$username' 
                WHERE id_pelanggan='$id'");
        }
    } else {
        // Jika mode tambah data baru
        $password = md5($_POST['password']);
        if(empty($_POST['password'])){
            echo "<script>alert('Password tidak boleh kosong!');</script>";
        } else {
            mysqli_query($koneksi, "INSERT INTO pelanggan 
                (nama_pelanggan, nomor_kwh, alamat, id_tarif, username, password) VALUES 
                ('$nama_pelanggan', '$nomor_kwh', '$alamat', '$id_tarif', '$username', '$password')");
        }
    }

    // Setelah simpan, redirect kembali ke halaman pelanggan
    echo "<script>window.location.href='pelanggan.php'</script>";
}

// LOGIKA HAPUS DATA
if(isset($_GET['hapus'])){
    mysqli_query($koneksi, "DELETE FROM pelanggan WHERE id_pelanggan='{$_GET['hapus']}'");
    echo "<script>window.location.href='pelanggan.php'</script>";
}

// SET NILAI DEFAULT FORM
$p = [
    'nama_pelanggan' => '',
    'nomor_kwh' => '',
    'alamat' => '',
    'id_tarif' => '',
    'username' => ''
];

// Jika mode edit, ambil data pelanggan untuk diisi ke form
if(isset($_GET['edit'])){
    $query = mysqli_query($koneksi, "SELECT * FROM pelanggan WHERE id_pelanggan='{$_GET['edit']}'");
    $p = mysqli_fetch_assoc($query);
}
?>

<?php include 'template/sidebar.php'; // Menyisipkan menu/sidebar ?>

<div id="content">
    <div class="content-header">
        <h2>Data Pelanggan</h2>
    </div>

    <!-- FORM TAMBAH / EDIT DATA PELANGGAN -->
    <div class="card">
        <div class="card-header">
            <h3><?php echo isset($_GET['edit']) ? 'Edit' : 'Tambah'; ?> Data Pelanggan</h3>
        </div>
        <form action="" method="POST">
            <div class="form-grid">
                <!-- Input Nama -->
                <div class="form-group">
                    <label>Nama Pelanggan</label>
                    <input type="text" name="nama_pelanggan" value="<?php echo $p['nama_pelanggan']; ?>" required>
                </div>

                <!-- Input Nomor KWH -->
                <div class="form-group">
                    <label>Nomor KWH</label>
                    <input type="text" name="nomor_kwh" value="<?php echo $p['nomor_kwh']; ?>" required>
                </div>

                <!-- Input Username -->
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" value="<?php echo $p['username']; ?>" required>
                </div>

                <!-- Input Password -->
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" <?php if(!isset($_GET['edit'])) echo 'required'; ?>>
                    <small><?php if(isset($_GET['edit'])) echo 'Kosongkan jika tidak ingin ganti password.'; ?></small>
                </div>

                <!-- Pilih Daya (Tarif) -->
                <div class="form-group">
                    <label>Daya</label>
                    <select name="id_tarif" required>
                        <option value="">-- Pilih Daya --</option>
                        <?php 
                        $query_tarif = mysqli_query($koneksi, "SELECT * FROM tarif");
                        while ($tarif = mysqli_fetch_assoc($query_tarif)) {
                            $selected = ($tarif['id_tarif'] == $p['id_tarif']) ? 'selected' : '';
                            echo "<option value='{$tarif['id_tarif']}' {$selected}>{$tarif['daya']} VA</option>";
                        }
                        ?>
                    </select>
                </div>

                <!-- Input Alamat -->
                <div class="form-group">
                    <label>Alamat</label>
                    <textarea name="alamat" rows="2" required><?php echo $p['alamat']; ?></textarea>
                </div>
            </div>

            <!-- Tombol Simpan dan Batal -->
            <div class="form-actions">
                <button type="submit" name="simpan" class="btn btn-success">Simpan</button>
                <?php if(isset($_GET['edit'])): ?>
                    <a href="pelanggan.php" class="btn btn-secondary">Batal</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- === TABEL DAFTAR PELANGGAN === -->
    <div class="card">
        <div class="card-header">
            <h3>Daftar Pelanggan</h3>
        </div>
        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>No. KWH</th>
                        <th>Daya</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1;
                    // Ambil semua data pelanggan dan gabungkan dengan tarif untuk menampilkan daya
                    $query = mysqli_query($koneksi, "SELECT p.*, t.daya FROM pelanggan p JOIN tarif t ON p.id_tarif = t.id_tarif ORDER BY id_pelanggan DESC");
                    while($data = mysqli_fetch_assoc($query)){
                    ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td><?php echo $data['nama_pelanggan']; ?></td>
                        <td><?php echo $data['nomor_kwh']; ?></td>
                        <td><?php echo $data['daya']; ?> VA</td>
                        <td>
                            <a href="pelanggan.php?edit=<?php echo $data['id_pelanggan']; ?>" class="btn btn-warning">Edit</a>
                            <a href="pelanggan.php?hapus=<?php echo $data['id_pelanggan']; ?>" class="btn btn-danger" onclick="return confirm('Yakin hapus?')">Hapus</a>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'template/footer.php'; // Menyisipkan bagian footer ?>
