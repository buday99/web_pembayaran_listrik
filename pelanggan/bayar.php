<?php 
// Memuat bagian header
include 'template/header.php';

// Jika tidak ada parameter ID tagihan di URL, redirect ke halaman tagihan
if(!isset($_GET['id']) || empty($_GET['id'])){
    header('Location: tagihan.php');
}

$id_tagihan = $_GET['id'];

// LOGIKA PHP UNTUK PROSES PEMBAYARAN
if(isset($_POST['bayar'])){
    $biaya_admin = 2500; // Biaya admin tetap
    $total_bayar = $_POST['total_bayar']; // Total tagihan dari form
    $tanggal_pembayaran = date('Y-m-d H:i:s'); // Tanggal dan waktu saat ini

    // --- PROSES UPLOAD BUKTI PEMBAYARAN ---
    $bukti = $_FILES['bukti']['name'];
    $lokasi = $_FILES['bukti']['tmp_name'];
    $nama_bukti_baru = uniqid() . "_" . $bukti; // Nama unik untuk file bukti
    move_uploaded_file($lokasi, "../uploads/".$nama_bukti_baru); // Pindah file ke folder uploads

    // --- INSERT DATA PEMBAYARAN KE DATABASE ---
    $sql = "INSERT INTO pembayaran (id_tagihan, id_pelanggan, tanggal_pembayaran, biaya_admin, total_bayar, status, bukti)
            VALUES ('$id_tagihan', '$id_pelanggan', '$tanggal_pembayaran', '$biaya_admin', '$total_bayar', 'Menunggu Konfirmasi', '$nama_bukti_baru')";
    $query_bayar = mysqli_query($koneksi, $sql);

    // --- JIKA BERHASIL, UPDATE STATUS TAGIHAN ---
    if($query_bayar){
        mysqli_query($koneksi, "UPDATE tagihan SET status='Menunggu Konfirmasi' WHERE id_tagihan='$id_tagihan'");
        echo "<script>alert('Terima kasih! Pembayaran Anda sedang menunggu konfirmasi dari admin.'); window.location.href='tagihan.php'</script>";
    } else {
        echo "<script>alert('Gagal memproses pembayaran!');</script>";
    }
}

// ---------------- AMBIL DETAIL TAGIHAN ------------------
$query_tagihan = mysqli_query($koneksi, "
    SELECT tagihan.*, pelanggan.nama_pelanggan, pelanggan.nomor_kwh, tarif.tarif_per_kwh 
    FROM tagihan 
    JOIN pelanggan ON tagihan.id_pelanggan = pelanggan.id_pelanggan 
    JOIN tarif ON pelanggan.id_tarif = tarif.id_tarif 
    WHERE tagihan.id_tagihan='$id_tagihan' AND tagihan.id_pelanggan='$id_pelanggan'");
$t = mysqli_fetch_assoc($query_tagihan);

// Jika tagihan tidak ditemukan atau bukan milik user yang sedang login
if(mysqli_num_rows($query_tagihan) == 0){
    header('Location: tagihan.php');
}

// Hitung subtotal dan total tagihan
$biaya_admin = 2500;
$subtotal = $t['jumlah_meter'] * $t['tarif_per_kwh'];
$total_tagihan = $subtotal + $biaya_admin;
?>

<?php include 'template/sidebar.php'; ?>

<!-- Tampilan Halaman -->
<div id="content">
    <div class="content-header">
        <h2>Konfirmasi Pembayaran</h2>
    </div>

    <!-- Kartu Informasi Detail Tagihan -->
    <div class="card">
        <div class="card-header">
            <h3>Detail Tagihan</h3>
        </div>
        <table class="table">
            <tr>
                <th>Nama Pelanggan</th>
                <td><?php echo $t['nama_pelanggan']; ?></td>
            </tr>
            <tr>
                <th>Nomor KWH</th>
                <td><?php echo $t['nomor_kwh']; ?></td>
            </tr>
            <tr>
                <th>Periode</th>
                <td><?php echo $t['bulan'] . ' ' . $t['tahun']; ?></td>
            </tr>
            <tr>
                <th>Total Meter</th>
                <td><?php echo $t['jumlah_meter']; ?> KWH</td>
            </tr>
            <tr>
                <th>Subtotal (Rp <?php echo number_format($t['tarif_per_kwh']); ?>/KWH)</th>
                <td>Rp <?php echo number_format($subtotal); ?></td>
            </tr>
            <tr>
                <th>Biaya Admin</th>
                <td>Rp <?php echo number_format($biaya_admin); ?></td>
            </tr>
            <tr>
                <th>Total Tagihan</th>
                <td><strong>Rp <?php echo number_format($total_tagihan); ?></strong></td>
            </tr>
        </table>
    </div>

    <!-- Kartu Form Pembayaran -->
    <div class="card">
        <div class="card-header">
            <h3>Form Pembayaran</h3>
        </div>
        <p>Silakan lakukan transfer ke rekening XXXX a.n. PT. Listrik Jaya lalu unggah bukti pembayaran Anda di sini.</p>
        
        <!-- Form Upload Bukti -->
        <form action="" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="total_bayar" value="<?php echo $total_tagihan; ?>">
            <div class="form-group">
                <label>Upload Bukti Pembayaran</label>
                <input type="file" name="bukti" required>
            </div>
            <button type="submit" name="bayar" class="btn btn-primary">Konfirmasi Pembayaran</button>
            <a href="tagihan.php" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>

<?php include 'template/footer.php'; ?>
