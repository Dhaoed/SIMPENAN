<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $nama = $_POST['nama'];
    $nomorRegister = $_POST['nomorRegister'];
    $tanggal = $_POST['tanggal'];

    $sql = "UPDATE ahli_waris SET nama = ?, nomor_register = ?, tanggal = ? WHERE ID = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $nama, $nomorRegister, $tanggal, $id);
    
    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request";
}
?>