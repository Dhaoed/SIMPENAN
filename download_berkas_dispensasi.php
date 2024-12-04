<?php
session_start();
include 'config.php';

if (!isset($_SESSION['id'])) {
    exit('Unauthorized access');
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    if (!is_numeric($id)) {
        exit('Invalid ID');
    }

    // Ambil dokumen dari database
    $sql = "SELECT dokumen_pdf, dokumen_nama FROM Dispensasi_nikah WHERE ID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $dokumenPdf = $row['dokumen_pdf'];
        $dokumenNama = $row['dokumen_nama'];

        if ($dokumenPdf) {
            // Set headers untuk download
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="'.$dokumenNama.'"');
            header('Content-Length: ' . strlen($dokumenPdf));
            header('Cache-Control: no-cache, no-store, must-revalidate');
            header('Pragma: no-cache');
            header('Expires: 0');

            ob_clean();
            flush();
            
            // Output file contents
            echo $dokumenPdf;
            exit;
        } else {
            echo 'File not found';
        }
    } else {
        echo 'Record not found';
    }

        error_log("Download attempt for ID: " . $id);
    if (!$dokumenPdf) {
        error_log("Document not found for ID: " . $id);
    }

    $stmt->close();
} else {
    echo 'Invalid request';
}

$conn->close();
?>