<?php
include 'config.php'; // File koneksi database

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = $_POST['nama'];
    $nomorRegister = $_POST['nomorRegister'];
    $tanggal = $_POST['tanggal'];

    $sql = "INSERT INTO ahli_waris (nama, nomor_register, tanggal) 
            VALUES (?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $nama, $nomorRegister, $tanggal);
    
    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error";
    }

    $stmt->close();
    $conn->close();
}
?>