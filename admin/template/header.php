<?php
session_start();
include '../config/koneksi.php';
if (!isset($_SESSION['id_admin'])) {
    header("Location: ../login.php");
    exit();
}
$nama_admin = $_SESSION['nama_admin'];
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="wrapper">