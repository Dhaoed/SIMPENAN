<?php
include 'config.php'; // File koneksi database

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = $_POST['nama'];
    $tanggalWafat = $_POST['tanggalWafat'];
    $ahliWaris = $_POST['ahliWaris'];
    $tanggalTerima = $_POST['tanggalTerima'];
    $alamat = $_POST['alamat'];
    $kelurahan = $_POST['kelurahan'];
    $tanggalKirim = $_POST['tanggalKirim'];

    $sql = "INSERT INTO Uang_duka (nama, tanggal_wafat, ahli_waris, tanggal_terima, alamat, kelurahan, tanggal_kirim) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssss", $nama, $tanggalWafat, $ahliWaris, $tanggalTerima, $alamat, $kelurahan, $tanggalKirim);
    
    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error";
    }

    $stmt->close();
    $conn->close();
}
?>