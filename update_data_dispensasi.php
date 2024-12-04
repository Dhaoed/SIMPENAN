<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['editId'];
    $namaSuami = $_POST['editNamaSuami'];
    $namaIstri = $_POST['editNamaIstri'];
    $nomorSurat = $_POST['editNomorSurat'];
    $pengantarKUA = $_POST['editPengantarKUA'];
    $tanggal = $_POST['editTanggal'];

    $sql = "UPDATE Dispensasi_nikah SET nama_suami = ?, nama_istri = ?, nomor_surat = ?, pengantar_kua = ?, tanggal = ? WHERE ID = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssi", $namaSuami, $namaIstri, $nomorSurat, $pengantarKUA, $tanggal, $id);
    
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