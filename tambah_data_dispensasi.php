<?php
include 'config.php'; // File koneksi database

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $namaSuami = $_POST['namaSuami'];
    $namaIstri = $_POST['namaIstri'];
    $nomorSurat = $_POST['nomorSurat'];
    $pengantarKUA = $_POST['pengantarKUA'];
    $tanggal = $_POST['tanggal'];

    $sql = "INSERT INTO Dispensasi_nikah (nama_suami, nama_istri, nomor_surat, pengantar_kua, tanggal) 
            VALUES (?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $namaSuami, $namaIstri, $nomorSurat, $pengantarKUA, $tanggal);
    
    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error";
    }

    $stmt->close();
    $conn->close();
}
?>