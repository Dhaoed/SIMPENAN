<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="icon" type="image/x-icon" href="assets/Logo Simpenan real.png" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <style>
        html, body {
            height: 100%;
            margin: 0;
        }

        body {
            display: flex;
            flex-direction: column;
            font-family: Arial, sans-serif;
            background: linear-gradient(180deg, rgb(0, 141, 172), #ffffff); /* Mengganti background image dengan gradient */
            margin: 0;
            padding: 0;
            font-family: sans-serif;
            min-height: 100vh; /* Memastikan gradient mencakup seluruh halaman */
        }

        header {
            background: #1A6BD0;
            padding: 15px 0;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        }

        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 30px;
        }

        .logo-wrapper {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .logo-wrapper img {
            width: 45px;
            height: auto;
        }

        .logo-wrapper h1 {
            color: white;
            margin: 0;
            font-size: 28px;
            font-weight: bold;
            letter-spacing: 0.5px;
        }

        nav ul {
            display: flex;
            align-items: center;
            gap: 30px;
            list-style: none;
            margin: 0;
            padding: 0;
        }

        nav ul li a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        nav ul li a:hover {
            color: #e0e0e0;
        }

        .btn-login {
            background-color: white;
            color: #1A6BD0 !important;
            padding: 8px 20px;
            border-radius: 8px;
            font-weight: bold !important;
            transition: all 0.3s ease;
        }

        .btn-login:hover {
            background-color: #f0f0f0;
            transform: translateY(-2px);
        }

        /* Style untuk container dan welcome message */
        .container {
            background: white;
            border-radius: 20px;
            padding: 40px;
            width: 90%;
            max-width: 800px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .welcome-section {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 25px;
            border-bottom: 2px solid #f0f0f0;
        }

        .welcome-title {
            color: #1A6BD0;
            font-size: 32px;
            font-weight: bold;
            margin: 0 0 10px 0;
        }

        .welcome-subtitle {
            color: #666;
            font-size: 16px;
            margin: 0;
        }

        .service-title {
            text-align: center;
            font-size: 24px;
            color: #35424a;
            margin: 0 0 30px 0;
            font-weight: bold;
        }

        h1 {
            margin: 0;
        }

        nav {
            margin: 0; /* Menghapus margin pada nav */
        }

        nav ul {
            list-style: none;
            padding: 0;
            margin: 0; /* Menghapus margin pada ul */
        }

        nav ul li {
            display: inline;
            margin: 0 15px;
        }

        nav ul li a {
            color: #ffffff;
            text-decoration: none;
            font-weight: bold;
        }

        nav ul li a:hover {
            text-decoration: none;
        }

        main {
            padding: 20px;
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding-top: 40px;
        }

        .container {
            background: white;
            border-radius: 20px;
            padding: 30px;
            width: 90%;
            max-width: 800px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .service-title {
            text-align: center;
            font-size: 24px;
            color: #35424a;
            margin-bottom: 30px;
            font-weight: bold;
        }

        .services-vertical {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .service-item {
            display: flex;
            align-items: center;
            background: #ffffff;
            padding: 20px;
            border-radius: 12px;
            text-decoration: none;
            color: #35424a;
            transition: all 0.3s ease;
            border: 1px solid #e0e0e0;
        }

        .service-item:hover {
            background: #f8f9fa;
            transform: translateX(5px);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .service-icon {
            font-size: 24px;
            margin-right: 20px;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f8f9fa;
            border-radius: 10px;
        }

        .service-text {
            flex: 1;
        }

        .service-text h3 {
            margin: 0;
            font-size: 18px;
            color: #35424a;
        }

        .service-text p {
            margin: 5px 0 0;
            font-size: 14px;
            color: #6c757d;
        }

        .arrow {
            font-size: 24px;
            color: #6c757d;
            margin-left: 10px;
        }

        .btn-login {
            background-color: #35424a; /* Warna latar belakang tombol */
            color: #ffffff; /* Warna teks tombol */
            padding: 10px 15px; /* Padding untuk tombol */
            border-radius: 16px; /* Sudut membulat */
            text-decoration: none; /* Menghapus garis bawah */
        }

        .btn-login:hover {
            background-color: #000000; /* Warna latar belakang saat hover */
        }
        footer {
            text-align: center;
            padding: 10px 0; /* Menambahkan padding vertikal */
            background: #fff;
            color: #000;
            position: relative;
            width: 100%;
            bottom: 0;
        }

        .footer-content {
            max-width: 800px;
            margin: 0 auto;
        }

        .footer-content a {
            color: #ffffff;
            text-decoration: none;
        }
    </style>
</head>
<body>
<header>
    <div class="header-container">
        <div class="logo-wrapper">
            <img src="assets/Logo Simpenan.png" alt="Logo SIMPENAN">
            <h1>SIMPENAN</h1>
        </div>
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="about_us.php">About Us</a></li>
                <li><a href="index.php" class="btn btn-login">Login</a></li>
            </ul>
        </nav>
    </div>
</header>
        <main>
            <div class="container">
                <h2 class="service-title">Surat Keterangan Tidak Mampu Untuk Keperluan?</h2>
                <div class="services-vertical">
                    <a href="formulir_sktm.php" class="service-item">
                        <div class="service-icon">📄</div>
                        <div class="service-text">
                            <h3>Pengurusan SKTM Pendidikan</h3>
                            <p>Surat Keterangan Tidak Mampu</p>
                        </div>
                        <div class="arrow">›</div>
                    </a>
                    <a href="dispensasi_nikah.php" class="service-item">
                        <div class="service-icon">💍</div>
                        <div class="service-text">
                            <h3>Pengurusan SKTM Kesehatan</h3>
                            <p>Pengajuan dispensasi biaya nikah</p>
                        </div>
                        <div class="arrow">›</div>
                    </a>
                    <a href="ahli_waris.php" class="service-item">
                        <div class="service-icon">👥</div>
                        <div class="service-text">
                            <h3>Pengurusan SKTM Umum</h3>
                            <p>Surat keterangan ahli waris</p>
                        </div>
                        <div class="arrow">›</div>
                    </a>
                    </a>
                </div>
            </div>
        </main>
    <footer>
        <div class="footer-content">
            <p>&copy; 2024 - Kecamatan Bintan Timur.</p>
            </p>
        </div>
    </footer>
</body>
</html>