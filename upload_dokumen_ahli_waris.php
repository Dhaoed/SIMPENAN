<?php
session_start();
include 'config.php';

if (!isset($_SESSION['id'])) {
    exit(json_encode(['status' => 'error', 'message' => 'Unauthorized access']));
}

if (isset($_POST['id']) && isset($_FILES['dokumen'])) {
    $id = $_POST['id'];
    $file = $_FILES['dokumen'];

    // Validasi file
    $allowedTypes = ['application/pdf'];
    $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
    $detectedType = finfo_file($fileInfo, $file['tmp_name']);
    
    if (!in_array($detectedType, $allowedTypes)) {
        exit(json_encode(['status' => 'error', 'message' => 'Invalid file type. Only PDF files are allowed.']));
    }

    // Cek ukuran file (500MB dalam bytes)
    $maxFileSize = 500 * 1024 * 1024; // 500MB
    if ($file['size'] > $maxFileSize) {
        exit(json_encode(['status' => 'error', 'message' => 'File size exceeds the limit of 500MB.']));
    }

    // Baca konten file
    $dokumenPdf = file_get_contents($file['tmp_name']);
    $dokumenNama = $file['name'];

    // Konfigurasi untuk file besar
    ini_set('memory_limit', '512M');
    ini_set('post_max_size', '500M');
    ini_set('upload_max_filesize', '500M');

    // Update database
    $sql = "UPDATE Ahli_waris SET dokumen_pdf = ?, dokumen_nama = ? WHERE ID = ?";
    $stmt = $conn->prepare($sql);
    $null = NULL; // Diperlukan untuk binding parameter BLOB
    $stmt->bind_param("bsi", $null, $dokumenNama, $id);
    
    // Gunakan metode alternatif untuk mengirim data besar
    if (!$stmt->send_long_data(0, $dokumenPdf)) {
        exit(json_encode(['status' => 'error', 'message' => 'Failed to upload large document: ' . $stmt->error]));
    }

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Document uploaded successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to upload document: ' . $stmt->error]);
    }

    error_log("Uploading document for ID: " . $id);
    error_log("File name: " . $dokumenNama);
    error_log("File size: " . strlen($dokumenPdf) . " bytes");

    $stmt->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
}

$conn->close();
?>