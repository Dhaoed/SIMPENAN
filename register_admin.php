<?php
include('config.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Hash password sebelum menyimpannya ke database
    $password_hashed = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO admin (username, password) VALUES (?, ?)");
    $stmt->bind_param("ss", $username, $password_hashed);
    if ($stmt->execute()) {
        echo "Registrasi berhasil!";
    } else {
        echo "Registrasi gagal: " . $stmt->error;
    }
    $stmt->close();
}
?>
