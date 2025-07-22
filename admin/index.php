<?php include 'template/header.php'; ?>
<?php include 'template/sidebar.php'; ?>

<div id="content">
    <div class="content-header">
        <h2>Dashboard</h2>
        <p>Selamat datang, <?php echo $nama_admin; ?>!</p>
    </div>

    <div class="card">
      <div class="card-header">
        <h3>Statistik</h3>
      </div>
      <p>Jumlah Pelanggan: 
        <?php 
          $q = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM pelanggan");
          echo mysqli_fetch_assoc($q)['total']; 
        ?>
      </p>
       <p>Jumlah Tagihan Belum Lunas: 
        <?php 
          $q = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM tagihan WHERE status='Belum Lunas'");
          echo mysqli_fetch_assoc($q)['total']; 
        ?>
      </p>
    </div>
</div>

<?php include 'template/footer.php'; ?>