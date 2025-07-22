<?php 
// Memuat bagian header
include 'template/header.php'; 

// Menyertakan sidebar navigasi
include 'template/sidebar.php'; 
?>

<div id="content">
    <!-- Judul Halaman -->
    <div class="content-header">
        <h2>Informasi Tagihan Anda</h2>
    </div>

    <!-- Kartu untuk menampilkan riwayat tagihan -->
    <div class="card">
        <div class="card-header">
            <h3>Riwayat Tagihan</h3>
        </div>
        
        <!-- Tabel daftar tagihan -->
        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>Bulan/Tahun</th>
                        <th>Jumlah Meter</th>
                        <th>Total Bayar</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    // Ambil data tagihan berdasarkan id pelanggan yang sedang login
                    $q_tagihan = mysqli_query($koneksi, 
                        "SELECT t.*, pl.id_tarif 
                         FROM tagihan t 
                         JOIN pelanggan pl ON t.id_pelanggan = pl.id_pelanggan 
                         WHERE t.id_pelanggan='$id_pelanggan' 
                         ORDER BY t.tahun DESC, t.bulan DESC"
                    );

                    // Looping tiap tagihan
                    while($t = mysqli_fetch_assoc($q_tagihan)){
                        // Ambil tarif per KWH berdasarkan id_tarif dari pelanggan
                        $q_tarif = mysqli_query($koneksi, 
                            "SELECT tarif_per_kwh FROM tarif WHERE id_tarif='{$t['id_tarif']}'"
                        );
                        $tarif = mysqli_fetch_assoc($q_tarif);

                        // Hitung total bayar: jumlah meter × tarif + biaya admin (misalnya 2500)
                        $total_bayar = ($t['jumlah_meter'] * $tarif['tarif_per_kwh']) + 2500;

                        // Tentukan warna badge status (Lunas, Belum Lunas, dll)
                        if ($t['status'] == 'Lunas') { 
                            $badge = 'badge-success'; 
                        } else if ($t['status'] == 'Belum Lunas') { 
                            $badge = 'badge-danger'; 
                        } else { 
                            $badge = 'badge-warning'; 
                        }
                    ?>
                    <!-- Tampilkan data tagihan dalam baris tabel -->
                    <tr>
                        <td><?php echo $t['bulan'] . ' ' . $t['tahun']; ?></td>
                        <td><?php echo $t['jumlah_meter']; ?> KWH</td>
                        <td><strong>Rp <?php echo number_format($total_bayar); ?></strong></td>
                        <td>
                            <span class="badge <?php echo $badge; ?>">
                                <?php echo $t['status']; ?>
                            </span>
                        </td>
                        <td>
                            <?php if($t['status'] == 'Belum Lunas'): ?>
                                <!-- Tampilkan tombol Bayar jika belum lunas -->
                                <a href="bayar.php?id=<?php echo $t['id_tagihan']; ?>" class="btn btn-primary">Bayar</a>
                            <?php elseif($t['status'] == 'Lunas'): ?>
                                <!-- Tampilkan tombol Cetak jika sudah lunas -->
                                <a href="invoice_pdf.php?id=<?php echo $t['id_tagihan']; ?>" target="_blank" class="btn btn-info" style="color: black;">Cetak</a>
                            <?php else: echo '-'; endif; ?>
                        </td>
                    </tr>
                    <?php } // End While ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php 
// Menyertakan footer halaman
include 'template/footer.php'; 
?>
