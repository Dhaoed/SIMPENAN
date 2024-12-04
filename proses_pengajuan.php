<?php
include 'config.php';

// Ambil data dari formulir
$pengurusan = $_POST['pengurusan'];
$tanggal_pengajuan = $_POST['tanggal_pengajuan'];
$nama = $_POST['nama'];
$alamat = $_POST['alamat'];
$tujuan = $_POST['tujuan'];

// Simpan data ke database
if ($pengurusan == 'kesehatan') {
    $stmt = $conn->prepare("INSERT INTO kesehatan (nama, alamat, tujuan, tanggal) VALUES (?, ?, ?, ?)");
} elseif ($pengurusan == 'pendidikan') {
    $stmt = $conn->prepare("INSERT INTO pendidikan (nama, alamat, tujuan, tanggal) VALUES (?, ?, ?, ?)");
} else {
    $stmt = $conn->prepare("INSERT INTO umum (nama, alamat, tujuan, tanggal) VALUES (?, ?, ?, ?)");
}

$stmt->bind_param("ssss", $nama, $alamat, $tujuan, $tanggal_pengajuan);
$stmt->execute();
$stmt->close();

// Redirect ke halaman sukses atau kembali ke formulir
header("Location: success.php");
exit();
?>