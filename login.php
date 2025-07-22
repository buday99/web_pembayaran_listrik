<?php
session_start();

// Jika pengguna sudah login sebagai pelanggan atau admin, redirect ke halaman utama
if (isset($_SESSION['id_pelanggan']) || isset($_SESSION['id_admin'])) {
    header('Location: index.php');
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login - Aplikasi Pembayaran Listrik</title>
    <!-- Link ke file CSS eksternal -->
    <link rel="stylesheet" type="text/css" href="assets/css/style.css">
</head>
<body class="login-body">
    <!-- Kontainer utama login -->
    <div class="login-container">
        <!-- Header login -->
        <div class="login-header">
            <h2>LOGIN</h2>
            <p>Aplikasi Pembayaran Listrik Pascabayar</p>
        </div>

        <!-- Form login -->
        <form action="config/auth.php" method="POST" class="login-form">
            <!-- Input Username -->
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>

            <!-- Input Password -->
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>

            <!-- Pilihan Role (Pelanggan/Admin) -->
            <div class="form-group">
                <label for="role">Login Sebagai</label>
                <select id="role" name="role" required>
                    <option value="pelanggan">Pelanggan</option>
                    <option value="admin">Admin</option>
                </select>
            </div>

            <!-- Tombol Login -->
            <button type="submit" name="login">Login</button>
        </form>

        <!-- Link menuju halaman pendaftaran jika belum punya akun -->
        <div style="margin-top: 20px; text-align: center;">
            <p>Belum punya akun? <a href="daftar.php">Daftar sekarang</a></p>
        </div>
    </div>
</body>
</html>
