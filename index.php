<?php
session_start();
include('config.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Menggunakan prepared statement
    $stmt = $conn->prepare("SELECT id, password FROM admin WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        // Gunakan password_verify jika password di-hash
        if ($password === $row['password']) {
            $_SESSION['id'] = $row['id'];
            header("Location: dashboard_admin.php");
            exit();
        } else {
            $error = "Invalid username and password";
        }
    } else {
        $error = "Invalid username and password";
    }

    $stmt->close();
    $conn->close();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Informasi Pengarsipan Kecamatan</title>
    <link rel="icon" type="image/x-icon" href="assets/Logo Simpenan real.png" />
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <div class="logo-text">
                <img src="assets/Logo Simpenan real.png" alt="Logo SIMPENAN">
                <h1>SIMPENAN</h1>
            </div>
            <div class="logo-text-2">
                <img src="assets/Logo Bintan.png" alt="Logo Kecamatan">
                <h2>Kecamatan Bintan Timur</h2>
            </div>
            <form action="" method="POST">
                <h3>Login</h3>
                <p>Masuk ke Akun</p>

                <div class="label">
                    <h4>Username</h4>
                </div>

                <div class="input-group">
                    <i class="icon-user"></i>
                    <input type="text" name="username" placeholder="Username" required>
                </div>

                <div class="label">
                    <h4>Password</h4>
                </div>

                <div class="input-group">
                    <i class="icon-lock"></i>
                    <input type="password" name="password" placeholder="Password" required>
                </div>
                <button type="submit">Login</button>
                <?php if (isset($error)) { echo '<p class="error">'.$error.'</p>'; } ?>
            </form>
            <div class="link">
                <p>Tidak punya akun Admin? <a href="dashboard_user.php">Not Admin</a></p>
            </div>
            <hr>
            <div class="footer">
                &copy; 2024 / Kecamatan Bintan Timur
            </div>
        </div>
        <div class="info-box">
            <h3>SISTEM INFORMASI PENGARSIPAN KECAMATAN</h3>
            <img src="assets/Foto KC 2.jpeg" alt="Gambar 1" class="active">
            <img src="assets/Foto KC 1.jpeg" alt="Gambar 2">
            <img src="assets/Foto Kantor Camat.png" alt="Gambar 3">
            <p>Punya masalah dengan surat, dokumen, dan berkas? <br> Tenang Kami punya solusinya...</p>
            <div class="bullets">
                <div class="bullet active" data-index="0"></div>
                <div class="bullet" data-index="1"></div>
                <div class="bullet" data-index="2"></div>
            </div>
        </div>
    </div>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const bullets = document.querySelectorAll('.info-box .bullet');
        const images = document.querySelectorAll('.info-box img');
        let currentIndex = 0;

        function changeImage() {
            // Remove active class from all bullets and images
            bullets.forEach(b => b.classList.remove('active'));
            images.forEach(img => img.classList.remove('active'));

            // Add active class to the current bullet and image
            bullets[currentIndex].classList.add('active');
            images[currentIndex].classList.add('active');

            // Move to next image
            currentIndex = (currentIndex + 1) % images.length;
        }

        // Change image every 3 seconds (5000 milliseconds)
        setInterval(changeImage, 3000);

        // Optional: Allow manual navigation when bullets are clicked
        bullets.forEach((bullet, index) => {
            bullet.addEventListener('click', () => {
                currentIndex = index;
                changeImage();
            });
        });
    });
</script>
</body>
</html>
