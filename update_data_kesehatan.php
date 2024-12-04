<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $nama = $_POST['nama'];
    $pengurusan = $_POST['pengurusan'];
    $alamat = $_POST['alamat'];
    $tanggal = $_POST['tanggal'];
    $penanggungJawab = $_POST['penanggungJawab'];

    $sql = "UPDATE Kesehatan SET nama = ?, pengurusan = ?, alamat = ?, tanggal = ?, penanggung_jawab = ? WHERE ID = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssi", $nama, $pengurusan, $alamat, $tanggal, $penanggungJawab, $id);
    
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