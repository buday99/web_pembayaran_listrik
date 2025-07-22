<?php include 'template/header.php'; ?>
<?php include 'template/sidebar.php'; ?>
<?php
$query = mysqli_query($koneksi, "SELECT pelanggan.*, tarif.daya FROM pelanggan JOIN tarif ON pelanggan.id_tarif = tarif.id_tarif WHERE id_pelanggan='$id_pelanggan'");
$pelanggan = mysqli_fetch_assoc($query);
?>

<div id="content">
    <div class="content-header">
        <h2>Dashboard</h2>
        <p>Selamat datang, <?php echo $nama_pelanggan; ?>!</p>
    </div>

    <div class="card">
      <div class="card-header">
        <h3>Informasi Akun</h3>
      </div>
      <p><strong>Nomor KWH:</strong> <?php echo $pelanggan['nomor_kwh']; ?></p>
      <p><strong>Nama:</strong> <?php echo $pelanggan['nama_pelanggan']; ?></p>
      <p><strong>Alamat:</strong> <?php echo $pelanggan['alamat']; ?></p>
      <p><strong>Daya:</strong> <?php echo $pelanggan['daya']; ?> VA</p>
    </div>
</div>

<?php include 'template/footer.php'; ?>