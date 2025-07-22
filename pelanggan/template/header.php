<?php
session_start();
// PERBAIKAN: Path dikembalikan ke ../config/koneksi.php
include '../config/koneksi.php'; 

if (!isset($_SESSION['id_pelanggan'])) {
    // Path untuk header Location dihitung dari file ini, jadi ../login.php sudah benar
    header("Location: ../login.php");
    exit();
}
$id_pelanggan = $_SESSION['id_pelanggan'];
$nama_pelanggan = $_SESSION['nama_pelanggan'];
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pelanggan</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="wrapper">