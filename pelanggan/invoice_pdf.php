<?php
session_start();
include '../config/koneksi.php';
require('../lib/fpdf/fpdf.php');

if (!isset($_SESSION['id_pelanggan']) || !isset($_GET['id'])) {
    die("Akses ditolak.");
}

$id_tagihan = $_GET['id'];
$id_pelanggan = $_SESSION['id_pelanggan'];

$sql = "SELECT t.*, p.nama_pelanggan, p.nomor_kwh, p.alamat, tr.tarif_per_kwh, pem.tanggal_pembayaran
        FROM tagihan t
        JOIN pelanggan p ON t.id_pelanggan = p.id_pelanggan
        JOIN tarif tr ON p.id_tarif = tr.id_tarif
        LEFT JOIN pembayaran pem ON t.id_tagihan = pem.id_tagihan
        WHERE t.id_tagihan = '$id_tagihan' AND t.id_pelanggan = '$id_pelanggan' AND t.status = 'Lunas'";

$query = mysqli_query($koneksi, $sql);
$data = mysqli_fetch_assoc($query);

if (!$data) {
    die("Invoice tidak ditemukan atau belum lunas.");
}

$biaya_admin = 2500;
$subtotal = $data['jumlah_meter'] * $data['tarif_per_kwh'];
$total_bayar = $subtotal + $biaya_admin;

// Mulai Membuat PDF
$pdf = new FPDF('P', 'mm', 'A4');
$pdf->AddPage();

// JUDUL
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(190, 10, 'INVOICE PEMBAYARAN LISTRIK', 0, 1, 'C');
$pdf->Ln(5);

// INFO
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(40, 6, 'Invoice No.', 0, 0);
$pdf->Cell(5, 6, ':', 0, 0);
$pdf->Cell(80, 6, 'INV/' . $data['tahun'] . '/' . $data['id_tagihan'], 0, 1);

$pdf->Cell(40, 6, 'Tanggal Lunas', 0, 0);
$pdf->Cell(5, 6, ':', 0, 0);
$pdf->Cell(80, 6, date('d F Y', strtotime($data['tanggal_pembayaran'])), 0, 1);

$pdf->Cell(40, 6, 'Nama Pelanggan', 0, 0);
$pdf->Cell(5, 6, ':', 0, 0);
$pdf->Cell(80, 6, $data['nama_pelanggan'], 0, 1);

$pdf->Cell(40, 6, 'Nomor KWH', 0, 0);
$pdf->Cell(5, 6, ':', 0, 0);
$pdf->Cell(80, 6, $data['nomor_kwh'], 0, 1);

$pdf->Ln(10);

// TABEL DETAIL
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(130, 7, 'Deskripsi', 1, 0, 'C');
$pdf->Cell(60, 7, 'Jumlah', 1, 1, 'C');

$pdf->SetFont('Arial', '', 10);
$pdf->Cell(130, 7, 'Pemakaian Listrik Periode ' . $data['bulan'] . ' ' . $data['tahun'] . ' (' . $data['jumlah_meter'] . ' KWH)', 1, 0, 'L');
$pdf->Cell(60, 7, 'Rp ' . number_format($subtotal), 1, 1, 'R');

$pdf->Cell(130, 7, 'Biaya Administrasi', 1, 0, 'L');
$pdf->Cell(60, 7, 'Rp ' . number_format($biaya_admin), 1, 1, 'R');

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(130, 8, 'TOTAL', 1, 0, 'C');
$pdf->Cell(60, 8, 'Rp ' . number_format($total_bayar), 1, 1, 'R');

$pdf->Ln(10);

// FOOTER
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(190, 8, 'STATUS: LUNAS', 0, 1, 'C');
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(190, 5, 'Terima kasih atas pembayaran Anda.', 0, 1, 'C');

$pdf->Output('I', 'Invoice-' . $data['id_tagihan'] . '.pdf');