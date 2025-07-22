<?php 
// Memuat bagian header
include 'template/header.php';

// LOGIKA KONFIRMASI PEMBAYARAN
if(isset($_GET['konfirmasi'])) {
    $id_pembayaran = $_GET['konfirmasi'];
    
    // Update status pembayaran jadi 'Lunas'
    $update_pembayaran = mysqli_query($koneksi, "UPDATE pembayaran SET status='Lunas' WHERE id_pembayaran='$id_pembayaran'");
    
    // Ambil ID tagihan yang terkait dengan pembayaran tersebut
    $q_tagihan = mysqli_query($koneksi, "SELECT id_tagihan FROM pembayaran WHERE id_pembayaran='$id_pembayaran'");
    $d_tagihan = mysqli_fetch_assoc($q_tagihan);
    $id_tagihan = $d_tagihan['id_tagihan'];

    // Update juga status tagihan jadi 'Lunas'
    $update_tagihan = mysqli_query($koneksi, "UPDATE tagihan SET status='Lunas' WHERE id_tagihan='$id_tagihan'");

    // Tampilkan notifikasi berdasarkan keberhasilan query
    if($update_pembayaran && $update_tagihan){
        echo "<script>alert('Pembayaran berhasil dikonfirmasi!'); window.location.href='pembayaran.php'</script>";
    } else {
        echo "<script>alert('Gagal mengonfirmasi pembayaran!'); window.location.href='pembayaran.php'</script>";
    }
}
?>

<?php include 'template/sidebar.php'; // Sidebar navigasi ?>

<div id="content">
    <div class="content-header">
        <h2>Data Pembayaran</h2>
    </div>

    <!-- FORM CETAK LAPORAN PDF -->
    <div class="card">
        <div class="card-header">
            <h3>Cetak Laporan Pembayaran</h3>
        </div>
        <form action="laporan_pembayaran_pdf.php" method="POST" target="_blank">
            <div class="form-grid" style="grid-template-columns: 1fr 1fr;">
                <!-- Input tanggal awal laporan -->
                <div class="form-group">
                    <label>Tanggal Awal</label>
                    <input type="date" name="tgl_awal" class="form-control" required>
                </div>

                <!-- Input tanggal akhir laporan -->
                <div class="form-group">
                    <label>Tanggal Akhir</label>
                    <input type="date" name="tgl_akhir" class="form-control" required>
                </div>
            </div>

            <!-- Tombol cetak PDF -->
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Cetak PDF</button>
            </div>
        </form>
    </div>

    <!-- TABEL DAFTAR PEMBAYARAN -->
    <div class="card">
        <div class="card-header">
            <h3>Daftar Pembayaran</h3>
        </div>
        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Pelanggan</th>
                        <th>Tgl Bayar</th>
                        <th>Periode</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Bukti</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1;
                    // Ambil semua data pembayaran beserta informasi pelanggan dan periode tagihan
                    $query = mysqli_query($koneksi, "
                        SELECT pembayaran.*, pelanggan.nama_pelanggan, tagihan.bulan, tagihan.tahun 
                        FROM pembayaran 
                        JOIN pelanggan ON pembayaran.id_pelanggan = pelanggan.id_pelanggan 
                        JOIN tagihan ON pembayaran.id_tagihan = tagihan.id_tagihan
                        ORDER BY pembayaran.id_pembayaran DESC
                    ");
                    
                    // Loop untuk menampilkan setiap baris data pembayaran
                    while($data = mysqli_fetch_assoc($query)) {
                        // Atur tampilan badge status
                        $badge = ($data['status'] == 'Lunas') ? 'badge-success' : 'badge-warning';
                    ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td><?php echo $data['nama_pelanggan']; ?></td>
                        <td><?php echo date('d-m-Y', strtotime($data['tanggal_pembayaran'])); ?></td>
                        <td><?php echo $data['bulan'] . ' ' . $data['tahun']; ?></td>
                        <td>Rp <?php echo number_format($data['total_bayar']); ?></td>
                        <td><span class="badge <?php echo $badge; ?>"><?php echo $data['status']; ?></span></td>
                        
                        <!-- Kolom bukti pembayaran -->
                        <td>
                            <?php if(!empty($data['bukti'])): ?>
                                <a href="../uploads/<?php echo $data['bukti']; ?>" target="_blank" class="btn btn-info" style="padding: 5px 10px; font-size: 0.8rem; color: black;">Lihat</a>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>

                        <!-- Kolom aksi (tombol konfirmasi jika belum lunas) -->
                        <td>
                            <?php if ($data['status'] != 'Lunas'): ?>
                                <a href="pembayaran.php?konfirmasi=<?php echo $data['id_pembayaran']; ?>" 
                                   class="btn btn-success" 
                                   onclick="return confirm('Anda yakin ingin konfirmasi pembayaran ini?')">
                                   Konfirmasi
                                </a>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php } // End while ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'template/footer.php'; // Menyisipkan bagian footer ?>
