<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $nama = $_POST['nama'];
    $tanggalWafat = $_POST['tanggalWafat'];
    $ahliWaris = $_POST['ahliWaris'];
    $tanggalTerima = $_POST['tanggalTerima'];
    $alamat = $_POST['alamat'];
    $kelurahan = $_POST['kelurahan'];
    $tanggalKirim = $_POST['tanggalKirim'];

    $sql = "UPDATE Uang_duka SET nama = ?, tanggal_wafat = ?, ahli_waris = ?, tanggal_terima = ?, alamat = ?, kelurahan = ?, tanggal_kirim = ? WHERE ID = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssi", $nama, $tanggalWafat, $ahliWaris, $tanggalTerima, $alamat, $kelurahan, $tanggalKirim, $id);
    
    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request";
}
?>