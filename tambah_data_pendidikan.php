<?php
include 'config.php'; // File koneksi database

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = $_POST['nama'];
    $pengurusan = $_POST['pengurusan'];
    $alamat = $_POST['alamat'];
    $tanggal = $_POST['tanggal'];
    $penanggungJawab = $_POST['penanggungJawab'];

    $sql = "INSERT INTO Pendidikan (nama, pengurusan, alamat, tanggal, penanggung_jawab) 
            VALUES (?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $nama, $pengurusan, $alamat, $tanggal, $penanggungJawab);
    
    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error";
    }

    $stmt->close();
    $conn->close();
}
?>