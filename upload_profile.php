<?php
session_start();
include 'config.php';

$response = ['success' => false, 'message' => '', 'filename' => ''];

if(isset($_FILES['profileImage'])) {
    $file = $_FILES['profileImage'];
    $userId = $_SESSION['id'];

    // Validasi file
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    if(!in_array($file['type'], $allowedTypes)) {
        $response['message'] = 'Tipe file tidak diizinkan. Gunakan JPG, PNG, atau GIF.';
        echo json_encode($response);
        exit;
    }

    // Generate nama file unik
    $filename = uniqid() . '_' . $file['name'];
    $uploadPath = 'uploads/' . $filename;

    if(move_uploaded_file($file['tmp_name'], $uploadPath)) {
        // Update database
        $stmt = $conn->prepare("UPDATE admin SET profile_picture = ? WHERE id = ?");
        $stmt->bind_param("si", $filename, $userId);
        
        if($stmt->execute()) {
            $response['success'] = true;
            $response['filename'] = $filename;
            $response['message'] = 'Gambar profil berhasil diperbarui.';
        } else {
            $response['message'] = 'Gagal memperbarui database.';
        }
    } else {
        $response['message'] = 'Gagal mengupload file.';
    }
} else {
    $response['message'] = 'Tidak ada file yang diunggah.';
}

echo json_encode($response);
?>