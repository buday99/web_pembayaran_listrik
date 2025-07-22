<?php
// Menghubungkan ke file koneksi database
include 'config/koneksi.php';

// Jika tombol "Daftar" ditekan
if (isset($_POST['daftar'])) {
    // Mengamankan data input dari form
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = md5($_POST['password']); // Mengenkripsi password menggunakan MD5
    $nomor_kwh = mysqli_real_escape_string($koneksi, $_POST['nomor_kwh']);
    $nama_pelanggan = mysqli_real_escape_string($koneksi, $_POST['nama_pelanggan']);
    $alamat = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    $id_tarif = mysqli_real_escape_string($koneksi, $_POST['id_tarif']);

    // Cek apakah username atau nomor KWH sudah ada di database
    $cek_user = mysqli_query($koneksi, "SELECT * FROM pelanggan WHERE username='$username' OR nomor_kwh='$nomor_kwh'");
    if (mysqli_num_rows($cek_user) > 0) {
        // Jika ditemukan, tampilkan pesan error
        echo "<script>alert('Pendaftaran Gagal! Username atau Nomor KWH sudah terdaftar.');</script>";
    } else {
        // Jika belum terdaftar, simpan data pelanggan ke database
        $sql = "INSERT INTO pelanggan (username, password, nomor_kwh, nama_pelanggan, alamat, id_tarif) 
                VALUES ('$username', '$password', '$nomor_kwh', '$nama_pelanggan', '$alamat', '$id_tarif')";
        $query = mysqli_query($koneksi, $sql);

        // Beri notifikasi jika berhasil atau gagal
        if ($query) {
            echo "<script>alert('Pendaftaran Berhasil! Silakan login dengan akun Anda.'); window.location.href='login.php';</script>";
        } else {
            echo "<script>alert('Pendaftaran Gagal! Terjadi kesalahan pada server.');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Daftar Akun - Aplikasi Pembayaran Listrik</title>
    <!-- Menghubungkan CSS untuk tampilan -->
    <link rel="stylesheet" type="text/css" href="assets/css/style.css">
</head>
<body class="login-body">
    <!-- Container utama form -->
    <div class="login-container" style="width: 450px;">
        <div class="login-header">
            <h2>DAFTAR AKUN BARU</h2>
            <p>Silakan isi form di bawah ini untuk mendaftar.</p>
        </div>

        <!-- Form pendaftaran -->
        <form action="" method="POST" class="login-form">
            <!-- Input nama lengkap -->
            <div class="form-group">
                <label for="nama_pelanggan">Nama Lengkap</label>
                <input type="text" id="nama_pelanggan" name="nama_pelanggan" required>
            </div>

            <!-- Input nomor KWH -->
            <div class="form-group">
                <label for="nomor_kwh">Nomor KWH</label>
                <input type="text" id="nomor_kwh" name="nomor_kwh" required>
            </div>

            <!-- Input alamat pelanggan -->
            <div class="form-group">
                <label for="alamat">Alamat</label>
                <textarea id="alamat" name="alamat" rows="2" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box;" required></textarea>
            </div>

            <!-- Pilihan daya listrik -->
            <div class="form-group">
                <label for="id_tarif">Daya Listrik</label>
                <select id="id_tarif" name="id_tarif" required>
                    <option value="">-- Pilih Daya --</option>
                    <?php
                        // Menampilkan pilihan daya listrik dari database
                        $query_tarif = mysqli_query($koneksi, "SELECT * FROM tarif");
                        while ($tarif = mysqli_fetch_assoc($query_tarif)) {
                            echo "<option value='{$tarif['id_tarif']}'>{$tarif['daya']} VA</option>";
                        }
                    ?>
                </select>
            </div>

            <!-- Garis pemisah -->
            <hr style="margin: 20px 0; border: 1px solid #eee;">

            <!-- Input username -->
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>

            <!-- Input password -->
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>

            <!-- Tombol submit -->
            <button type="submit" name="daftar">Daftar</button>
        </form>

        <!-- Link ke halaman login jika sudah punya akun -->
        <div style="margin-top: 20px; text-align: center;">
            <p>Sudah punya akun? <a href="login.php">Login di sini</a></p>
        </div>
    </div>
</body>
</html>
