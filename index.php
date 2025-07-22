<?php
session_start();
if (isset($_SESSION['id_admin'])) {
    header('location:admin/');
} elseif (isset($_SESSION['id_pelanggan'])) {
    header('location:pelanggan/');
} else {
    header('location:login.php');
}
?>