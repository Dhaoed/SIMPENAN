<?php
session_start();
if (!isset($_SESSION['id'])) {
    header("Location: index.php");
    exit();
}

include 'config.php';

// Ambil informasi pengguna dari database berdasarkan ID sesi
$userId = $_SESSION['id'];
$queryUser = "SELECT username, profile_picture FROM admin WHERE id = ?";
$stmt = $conn->prepare($queryUser);
$stmt->bind_param("i", $userId);
$stmt->execute();
$resultUser = $stmt->get_result();
$userData = $resultUser->fetch_assoc();

$username = $userData['username'];
$profilePicture = $userData['profile_picture'];

// Modifikasi query untuk menggabungkan total SKTM
$querySKTM = "SELECT 
    (SELECT COUNT(*) FROM kesehatan) +
    (SELECT COUNT(*) FROM pendidikan) +
    (SELECT COUNT(*) FROM umum) as total";
$queryAhliWaris = "SELECT COUNT(*) as total FROM ahli_waris";
$queryUangDuka = "SELECT COUNT(*) as total FROM uang_duka";
$queryDispensasiNikah = "SELECT COUNT(*) as total FROM dispensasi_nikah";

$resultSKTM = $conn->query($querySKTM) or die($conn->error);
$resultAhliWaris = $conn->query($queryAhliWaris) or die($conn->error);
$resultUangDuka = $conn->query($queryUangDuka) or die($conn->error);
$resultDispensasiNikah = $conn->query($queryDispensasiNikah) or die($conn->error);

$totalSKTM = $resultSKTM->fetch_assoc()['total'];
$ahliWaris = $resultAhliWaris->fetch_assoc()['total'];
$uangDuka = $resultUangDuka->fetch_assoc()['total'];
$dispensasiNikah = $resultDispensasiNikah->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Admin</title>
    <link rel="icon" type="image/x-icon" href="assets/Logo Simpenan real.png" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            min-height: 100vh;
            margin: 0;
        }
        .sidebar {
            height: 100vh;
            background-color: white;
            width: 250px;
            position: fixed;
            top: 0;
            left: 0;
            overflow-y: auto;
            border-right: 1px solid #ddd;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .sidebar-content {
            flex-grow: 1;
        }

        /* .sidebar-button {
            margin-top: auto;
            width: calc(100% - 100px);
            box-sizing: border-box;
            align-items: center;
        } */

        .sidebar-header {
            background-color: #1A6BD0;
            width: 100%;
            padding: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .sidebar-header img {
            width: 30px;
            margin-right: 8px;
            margin-bottom: 3px;
        }
        .sidebar-header h2 {
            color: white;
            margin: 0;
            font-size: 18px;
            text-decoration: underline;
        }
        .sidebar img {
            max-width: 100px;
            max-height: 100px;
            margin-top: 10px;
        }
        .sidebar h4 {
            color: black;
            font-weight: 800;
            text-align: center;
            font-size: 20px;
            margin-top: 5px;
            margin-bottom: 10px;
        }
        .sidebar a {
            color: black;
            display: flex;
            align-items: center;
            padding: 8px 15px;
            text-decoration: none;
            font-weight: bold;
            width: calc(100% - 20px);
            box-sizing: border-box;
            font-size: 14px;
        }
        .sidebar a img {
            height: 20px;
            width: 20px;
            margin-right: 15px;
            margin-bottom: 8px;
        }
        .sidebar a:hover {
            background-color: #52B0ED;
            border-radius: 8px;
        }

        .sidebar a.active {
            background-color: #52B0ED;
            color: white;
            border-radius: 8px;
        }

        .submenu {
            display: none;
            flex-direction: column;
            width: 100%;
            padding-left: 15px;
        }
        .submenu.active {
            display: flex;
        }

        /*.sidebar a:hover + .submenu,
        .submenu:hover {
            display: flex;
        }*/
        
        .submenu a {
            color: black;
            display: flex;
            align-items: center;
            padding: 10px 35px;
            text-decoration: none;
            width: calc(100% - 10px);
            box-sizing: border-box;
            font-size: 13px;
        }
        .submenu a:hover {
            background-color: #D7D7D7;
            color: black;
            border-radius: 8px;
        }
        .content {
            background-color: #f8f9fa;
            margin-left: 250px;
            width: calc(100% - 250px);
            padding: 15px;
        }
        .header {
            background-color: #1A6BD0;
            padding: 29px;
            color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 20px;
            position: fixed;
            top: 0;
            left: 220px;
            width: calc(100% - 220px);
            z-index: 1000;
        }
        .header h2 {
            margin: 0;
            font-size: 20px;
            text-align: center;
        }
        .header .user-info {
            position: absolute;
            right: 25px;
            display: flex;
            align-items: center;
        }
        .header .user-info img {
            border-radius: 50%;
            margin-left: 10px;
            cursor: pointer;
        }
        .quick-access {
            display: flex;
            flex-direction: column;
            margin-top: 70px;
        }
        .quick-access-items {
            display: flex;
            justify-content: center;
            width: 100%;
            height: 150px;
        }
        .quick-access-items div {
            background-color: #fff;
            padding: 20px;
            text-align: center;
            margin: 0 10px;
            border-radius: 5px;
            color: #000;
            flex-basis: 30%;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .quick-access-items div img {
            width: 70px;
            height: 70px;
            margin-bottom: 10px;
        }
        /*mengatur kotak aktivity */
        .card-deck {
            display: grid;
            grid-template-columns: repeat(2, 1fr); /*mengatur ukuran kotak aktivity */
            gap: 20px;
            margin-top: 10px;
        }
        .card-deck .card {
            display: flex;
            background-color: #ffffff;
            border: none;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            height: 180px;
        }
        .card-icon {
            background-color: #003366;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 80px;
            width: 140px;
            flex-shrink: 0;
        }
        .card-icon img {
            width: 50px;
            height: 50px;
            filter: brightness(0) invert(1);
        }
        .card-content {
            flex: 1;
            padding: 10px 15px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: flex-end;
        }
        .card-title {
            margin: 0 0 0;
            font-size: 0.9rem;
            color: #333;
            text-align: right;
        }
        .card-text {
            font-size: 2rem;
            font-weight: bold;
            margin: 0;
            color: #000;
        }
        .quick-access-item, .clickable-card {
            cursor: pointer;
            transition: box-shadow 0.3s;
        }
        .quick-access-item:hover, .clickable-card:hover {
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        #sidebartoggle {
            position: fixed;
            color: white;
            top: 20px;
            left: 260px; /* Sesuaikan dengan lebar sidebar + sedikit margin */
            cursor: pointer;
            z-index: 1001;
            font-size: 30px;
        }
        .sidebar, .content, .header, .sidebartoggle {
            transition: all 0.3s ease;
        }
        .sidebar a.active {
            background-color: #52B0ED;
            border-radius: 8px;
            color: black;
        }

        .header .user-info {
            position: absolute;
            right: 25px;
            display: flex;
            align-items: center;
            gap: 15px; /* Add some space between elements */
        }
        
        .notification-container {
            position: relative;
            cursor: pointer;
        }
        .notification-icon {
            width: 32px;
            height: 32px;
            transition: transform 0.2s ease;
        }
        .notification-icon:hover {
            transform: scale(1.1);
        }
        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: red;
            color: white;
            border-radius: 50%;
            width: 16px;
            height: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="sidebar-header">
        <img src="assets/Logo Simpenan.png" alt="Logo SIMPENAN">
        <h2>SIMPENAN</h2>
    </div>
    <img src="assets/Logo Bintan.png" alt="Logo">
    <h4>SIMPENAN</h4>
    <a href="dashboard_admin.php"><img src="assets/home.svg" alt="Dashboard Icon" width="20" height="20"> Dashboard</a>
    
    <a href="#" id="sktmToggle"><img src="assets/penduduk.svg" alt="Data Icon" width="20" height="20"> Pelayanan SKTM ▼</a>
    <div class="submenu" id="sktmSubmenu">
        <a href="pelayanan_sktm_kesehatan_admin.php">● Kesehatan</a>
        <a href="pelayanan_sktm_pendidikan_admin.php">● Pendidikan</a>
        <a href="pelayanan_sktm_umum_admin.php">● Umum</a>
    </div>

    <a href="ahli_waris_admin.php"><img src="assets/Logo Ahli Waris.svg" alt="Waris Icon" width="20" height="20">Pengurusan Ahli Waris</a>
    <a href="uang_duka_admin.php"><img src="assets/Logo Uang Duka.svg" alt="Uang Icon" width="20" height="20"> Penerima Uang Duka</a>
    <a href="dispensasi_nikah_admin.php"><img src="assets/Logo Dispensasi.svg" alt="Nikah Icon" width="20" height="20"> Dispensasi Nikah</a>
    <a href="Faq_admin.php"><img src="assets/Logo Faq.svg" alt="Logout Icon" width="20" height="20"> Saran & Pertanyaan </a>
    <a href="logout.php"><img src="assets/keluar.svg" alt="Logout Icon" width="20" height="20"> Keluar</a>

    <!-- <div class="sidebar-button">
        <a href="recently_added.php"><img src="assets/information.png" alt="About Us Icon" width="20" height="20">Pengajuan</a>
    </div> -->
</div>

<div id="sidebartoggle">☰</div>
<div class="content">
    <div class="header">
        <h2>SELAMAT DATANG</h2>
        <div class="user-info">
            <span><?php echo $username; ?> | Administrator</span>
            <img src="uploads/<?php echo $profilePicture; ?>" alt="User" width="30" height="30" id="profileImage">
            <input type="file" id="profileImageInput" style="display: none;">
            
            <a href="recently_added.php" class="notification-container">
                <img src="assets/bell.png" alt="Notifications" class="notification-icon">
            <!-- <div class="notification-badge">3</div> -->
            </a>
        </div>
    </div>
    
    <div class="quick-access">
        <h1 style="text-align: left; font-weight:800; margin-top: 20px;">DASHBOARD</h1>
        <h4 style="text-align: left; margin-bottom: 20px; margin-top: 10px;">Quick Access</h4>
        <div class="quick-access-items">
            <div class="quick-access-item" data-href="pelayanan_sktm_kesehatan_admin.php">
                <img src="assets/penduduk.svg" alt="pelayanan SKTM Icon">
                <h5>Pelayanan SKTM</h5>
            </div>
            <div class="quick-access-item" data-href="ahli_waris_admin.php">
                <img src="assets/Logo Ahli Waris.svg" alt="waris Icon">
                <h5>Pengurusan Ahli Waris</h5>
            </div>
            <div class="quick-access-item" data-href="uang_duka_admin.php">
                <img src="assets/Logo Uang Duka.svg" alt="Uang Duka Icon">
                <h5>Penerima Uang Duka</h5>
            </div>
            <div class="quick-access-item" data-href="dispensasi_nikah_admin.php">
                <img src="assets/Logo Dispensasi.svg" alt="Dispensasi Icon">
                <h5>Dispensasi Nikah</h5>
            </div>
        </div>
    </div>

    <h4 style="text-align: left; margin-top: 40px;">Rekap Aktivitas</h4>
        <div class="card-deck">
        <div class="card">
            <div class="card-icon">
                <img src="assets/surat.svg" alt="SKTM Icon">
            </div>
            <div class="card-content">
                <h5 class="card-title">Pengurusan SKTM</h5>
                <p class="card-text"><?php echo sprintf("%02d", $totalSKTM); ?></p>
            </div>
        </div>

        <div class="card">
            <div class="card-icon">
                <img src="assets/surat.svg" alt="Total Surat Icon">
            </div>
            <div class="card-content">
                <h5 class="card-title">Daftar Ahli Waris</h5>
                <p class="card-text"><?php echo sprintf("%02d", $ahliWaris); ?></p>
            </div>
        </div>

        <div class="card">
            <div class="card-icon">
                <img src="assets/surat.svg" alt="Total Surat Icon">
            </div>
            <div class="card-content">
                <h5 class="card-title">Data Penerima Uang Duka</h5>
                <p class="card-text"><?php echo sprintf("%02d", $uangDuka); ?></p>
            </div>
        </div>

        <div class="card">
            <div class="card-icon">
                <img src="assets/surat.svg" alt="Total Surat Icon">
            </div>
            <div class="card-content">
                <h5 class="card-title">Pengurusan Dispensasi Nikah</h5>
                <p class="card-text"><?php echo sprintf("%02d", $dispensasiNikah); ?></p>
            </div>
        </div>

    </div>
</div>

<script>
    document.getElementById('profileImage').addEventListener('click', function() {
        document.getElementById('profileImageInput').click();
    });

    document.getElementById('profileImageInput').addEventListener('change', function() {
        var formData = new FormData();
        formData.append('profileImage', this.files[0]);

        fetch('upload_profile.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Perbarui src gambar profil dengan timestamp untuk menghindari cache
                var timestamp = new Date().getTime();
                document.getElementById('profileImage').src = 'uploads/' + data.filename + '?t=' + timestamp;
            } else {
                alert('Gagal mengupload gambar: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat mengupload gambar.');
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
    var sktmToggle = document.getElementById('sktmToggle');
    var sktmSubmenu = document.getElementById('sktmSubmenu');

    sktmToggle.addEventListener('click', function(e) {
        e.preventDefault();
        sktmSubmenu.classList.toggle('active');
    });


    // Kode untuk elemen yang dapat diklik lainnya
    var clickableElements = document.querySelectorAll('.quick-access-item, .clickable-card');
    
    clickableElements.forEach(function(element) {
        element.addEventListener('click', function() {
            var href = this.getAttribute('data-href');
            if (href) {
                window.location.href = href;
            }
        });
    });
    
});

document.getElementById('sidebartoggle').addEventListener('click', function() {
    var sidebar = document.querySelector('.sidebar');
    var content = document.querySelector('.content');
    var header = document.querySelector('.header');
    
    if (sidebar.style.marginLeft === '-250px') {
        sidebar.style.marginLeft = '0';
        content.style.marginLeft = '250px';
        content.style.width = 'calc(100% - 250px)';
        header.style.left = '245px';
        header.style.width = 'calc(100% - 250px)';
        sidebartoggle.style.left = '260px';
    } else {
        sidebar.style.marginLeft = '-250px';
        content.style.marginLeft = '0';
        content.style.width = '100%';
        header.style.left = '0';
        header.style.width = '100%';
        sidebartoggle.style.left = '25px';
    }
});

document.addEventListener('DOMContentLoaded', function() {
    const notificationIcon = document.getElementById('notificationIcon');
    const notificationDropdown = document.getElementById('notificationDropdown');
    const notificationContainer = document.getElementById('notificationContainer');

    notificationIcon.addEventListener('click', function(e) {
        e.stopPropagation();
        notificationDropdown.classList.toggle('active');
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!notificationContainer.contains(e.target)) {
            notificationDropdown.classList.remove('active');
        }
    });
});
</script>
</body>
</html>