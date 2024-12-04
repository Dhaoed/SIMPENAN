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

    // Baca konten file
    $dokumenPdf = file_get_contents($file['tmp_name']);
    $dokumenNama = $file['name'];

    // Update database
    $sql = "UPDATE Umum SET dokumen_pdf = ?, dokumen_nama = ? WHERE ID = ?";
    $stmt = $conn->prepare($sql);
    $null = NULL; // Diperlukan untuk binding parameter BLOB
    $stmt->bind_param("bsi", $null, $dokumenNama, $id);
    $stmt->send_long_data(0, $dokumenPdf);

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