<?php 
// Memuat file header
include 'template/header.php'; 

$id_pelanggan = $_SESSION['id_pelanggan'];

// Cek apakah form telah disubmit
if(isset($_POST['simpan'])){
    // Ambil data dari form
    $nama = $_POST['nama_pelanggan'];
    $alamat = $_POST['alamat'];
    $username = $_POST['username'];
    
    // Cek apakah password diisi 
    if(!empty($_POST['password'])){
        // Enkripsi password dengan MD5 sebelum disimpan
        $password = md5($_POST['password']);
        // Query update dengan mengganti password
        mysqli_query($koneksi, "UPDATE pelanggan SET nama_pelanggan='$nama', alamat='$alamat', username='$username', password='$password' WHERE id_pelanggan='$id_pelanggan'");
    } else {
        // Query update tanpa mengganti password
        mysqli_query($koneksi, "UPDATE pelanggan SET nama_pelanggan='$nama', alamat='$alamat', username='$username' WHERE id_pelanggan='$id_pelanggan'");
    }

    // Tampilkan notifikasi dan redirect ke halaman profil
    echo "<script>alert('Profil berhasil diperbarui!'); window.location.href='profil.php'</script>";
}

// Ambil data profil pelanggan dari database
$query = mysqli_query($koneksi, "SELECT * FROM pelanggan WHERE id_pelanggan='$id_pelanggan'");
$p = mysqli_fetch_assoc($query); // $p berisi data pelanggan
?>

<?php 
// Menampilkan sidebar
include 'template/sidebar.php'; 
?>

<!-- Bagian konten utama -->
<div id="content">
    <div class="content-header">
        <h2>Profil Saya</h2>
    </div>

    <!-- Kartu untuk menampilkan form edit profil -->
    <div class="card">
        <div class="card-header">
            <h3>Edit Profil</h3>
        </div>

        <!-- Form untuk mengubah data pelanggan -->
        <form action="" method="POST">
            <div class="form-group">
                <label>Nama Lengkap</label>
                <!-- Input nama pelanggan -->
                <input type="text" name="nama_pelanggan" value="<?php echo $p['nama_pelanggan']; ?>" required>
            </div>

            <div class="form-group">
                <label>Nomor KWH</label>
                <!-- Input nomor KWH (tidak bisa diedit) -->
                <input type="text" value="<?php echo $p['nomor_kwh']; ?>" disabled>
            </div>

            <div class="form-group">
                <label>Alamat</label>
                <!-- Textarea untuk alamat pelanggan -->
                <textarea name="alamat" rows="3" required><?php echo $p['alamat']; ?></textarea>
            </div>

            <hr>

            <div class="form-group">
                <label>Username</label>
                <!-- Input username pelanggan -->
                <input type="text" name="username" value="<?php echo $p['username']; ?>" required>
            </div>

            <div class="form-group">
                <label>Password</label>
                <!-- Input password baru (opsional) -->
                <input type="password" name="password">
                <small>Kosongkan jika tidak ingin mengubah password.</small>
            </div>

            <!-- Tombol submit -->
            <button type="submit" name="simpan" class="btn btn-success">Simpan Perubahan</button>
        </form>
    </div>
</div>

<?php 
// Memuat file footer
include 'template/footer.php'; 
?>
