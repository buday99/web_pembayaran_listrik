<?php
include '../config/koneksi.php';
require('../lib/fpdf/fpdf.php');

$tgl_awal = $_POST['tgl_awal'];
$tgl_akhir = $_POST['tgl_akhir'];

$pdf = new FPDF('P', 'mm', 'A4');
$pdf->AddPage();

// JUDUL LAPORAN
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(190, 7, 'LAPORAN PEMBAYARAN', 0, 1, 'C');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(190, 5, 'Periode: ' . date('d-m-Y', strtotime($tgl_awal)) . ' s/d ' . date('d-m-Y', strtotime($tgl_akhir)), 0, 1, 'C');
$pdf->Ln(8);

// HEADER TABEL
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(10, 7, 'No', 1, 0, 'C');
$pdf->Cell(50, 7, 'Nama Pelanggan', 1, 0, 'C');
$pdf->Cell(25, 7, 'Tgl Bayar', 1, 0, 'C');
$pdf->Cell(30, 7, 'Periode', 1, 0, 'C');
$pdf->Cell(45, 7, 'Total Bayar', 1, 0, 'C');
$pdf->Cell(25, 7, 'Status', 1, 1, 'C');

// ISI TABEL
$pdf->SetFont('Arial', '', 9);
$no = 1;
$total_pendapatan = 0;

$query = mysqli_query($koneksi, "
    SELECT pembayaran.*, pelanggan.nama_pelanggan, tagihan.bulan, tagihan.tahun 
    FROM pembayaran 
    JOIN pelanggan ON pembayaran.id_pelanggan = pelanggan.id_pelanggan 
    JOIN tagihan ON pembayaran.id_tagihan = tagihan.id_tagihan
    WHERE pembayaran.tanggal_pembayaran BETWEEN '$tgl_awal 00:00:00' AND '$tgl_akhir 23:59:59'
    ORDER BY pembayaran.tanggal_pembayaran ASC
");

while ($row = mysqli_fetch_assoc($query)) {
    $pdf->Cell(10, 6, $no++, 1, 0, 'C');
    $pdf->Cell(50, 6, $row['nama_pelanggan'], 1, 0, 'L');
    $pdf->Cell(25, 6, date('d-m-Y', strtotime($row['tanggal_pembayaran'])), 1, 0, 'C');
    $pdf->Cell(30, 6, $row['bulan'] . ' ' . $row['tahun'], 1, 0, 'C');
    $pdf->Cell(45, 6, 'Rp ' . number_format($row['total_bayar']), 1, 0, 'R');
    $pdf->Cell(25, 6, $row['status'], 1, 1, 'C');
    
    if ($row['status'] == 'Lunas') {
        $total_pendapatan += $row['total_bayar'];
    }
}

// TOTAL
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(160, 7, 'TOTAL PENDAPATAN (LUNAS)', 1, 0, 'R');
$pdf->Cell(25, 7, 'Rp ' . number_format($total_pendapatan), 1, 1, 'R');

$pdf->Output('I', 'Laporan-Pembayaran.pdf');