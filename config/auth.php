<?php
session_start();
include 'koneksi.php';

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = md5($_POST['password']);
    $role = $_POST['role'];

    if ($role == 'admin') {
        $sql = "SELECT * FROM admin WHERE username='$username' AND password='$password'";
        $query = mysqli_query($koneksi, $sql);
        $data = mysqli_fetch_array($query);
        $jumlah = mysqli_num_rows($query);

        if ($jumlah > 0) {
            $_SESSION['id_admin'] = $data['id_admin'];
            $_SESSION['nama_admin'] = $data['nama_admin'];
            header('Location: ../admin/');
        } else {
            echo "<script>alert('Login Gagal! Username atau Password Salah.'); window.location.href='../login.php';</script>";
        }
    } elseif ($role == 'pelanggan') {
        $sql = "SELECT * FROM pelanggan WHERE username='$username' AND password='$password'";
        $query = mysqli_query($koneksi, $sql);
        $data = mysqli_fetch_array($query);
        $jumlah = mysqli_num_rows($query);

        if ($jumlah > 0) {
            $_SESSION['id_pelanggan'] = $data['id_pelanggan'];
            $_SESSION['nama_pelanggan'] = $data['nama_pelanggan'];
            header('Location: ../pelanggan/');
        } else {
            echo "<script>alert('Login Gagal! Username atau Password Salah.'); window.location.href='../login.php';</script>";
        }
    }
}
?>