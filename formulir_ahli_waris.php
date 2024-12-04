<?php
include 'config.php';

// Fungsi pembersih input sederhana
function bersihkanInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Fungsi validasi upload dokumen
function validasiUnggahDokumen($file) {
    // Daftar ekstensi yang diizinkan
    $allowed_extensions = ['pdf', 'jpg', 'jpeg', 'png'];
    
    // Cek apakah file dipilih
    if ($file['error'] == UPLOAD_ERR_NO_FILE) {
        return null; // File tidak wajib
    }
    
    // Validasi ukuran file (maks 5MB)
    if ($file['size'] > 5 * 1024 * 1024) {
        throw new Exception("Ukuran file maksimal 5MB");
    }
    
    // Validasi ekstensi file
    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($file_extension, $allowed_extensions)) {
        throw new Exception("Hanya file PDF, JPG, dan PNG yang diperbolehkan");
    }
    
    return $file_extension;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Bersihkan dan validasi input
        $tanggal_pengajuan = isset($_POST['tanggal_pengajuan']) ? bersihkanInput($_POST['tanggal_pengajuan']) : date('Y-m-d');
        $nama = isset($_POST['nama']) ? bersihkanInput($_POST['nama']) : '';
        $nomor_register = isset($_POST['nomor_register']) ? bersihkanInput($_POST['nomor_register']) : '';
        
        // Validasi data yang diperlukan
        $errors = [];
        if (empty($nama)) $errors[] = "Nama harus diisi";
        
        if (!empty($errors)) {
            throw new Exception(implode(", ", $errors));
        }

        // Handle file upload
        $dokumen = null;
        if (isset($_FILES['dokumen']) && $_FILES['dokumen']['error'] == 0) {
            $file_extension = validasiUnggahDokumen($_FILES['dokumen']);
            
            $target_dir = "uploads/dokumen/";
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }

            $dokumen = uniqid() . '.' . $file_extension;
            $target_file = $target_dir . $dokumen;
            
            if (!move_uploaded_file($_FILES["dokumen"]["tmp_name"], $target_file)) {
                throw new Exception("Gagal mengunggah dokumen");
            }
        }

        // Prepare statement untuk insert sesuai
        $query = "INSERT INTO formulir_ahli_waris (nama, nomor_register, dokumen) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($query);
        
        if (!$stmt) {
            throw new Exception("Gagal mempersiapkan statement: " . $conn->error);
        }

        // Bind parameter
        $stmt->bind_param("sss", 
        $nama,
        $nomor_register,
        $dokumen
        );

        // Execute query
        if (!$stmt->execute()) {
            throw new Exception("Gagal menyimpan data: " . $stmt->error);
        }

        // Redirect ke recently_added.php setelah berhasil submit
        echo "<script>
            alert('Pengurusan Ahli Waris berhasil dikirim!');
            window.location.href = 'faq_page.php';
        </script>";
        exit();

    } catch (Exception $e) {
        // Log error
        error_log("Error in form submission: " . $e->getMessage());
        
        // Tampilkan pesan kesalahan
        echo "<script>
            alert('" . addslashes($e->getMessage()) . "');
            window.history.back();
        </script>";
        exit();
    } finally {
        // Tutup statement jika ada
        if (isset($stmt)) {
            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulir Pengurusan Ahli Waris - SIMPENAN</title>
    <link rel="icon" type="image/x-icon" href="assets/Logo Simpenan real.png" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <style>
        body {
            display: flex;
            flex-direction: column;
            font-family: Arial, sans-serif;
            background: linear-gradient(180deg, rgb(0, 141, 172), #ffffff);
            margin: 0;
            padding: 0;
            min-height: 100vh;
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
        main {
            flex: 1;
            padding: 40px 20px;
        }

        .form-container {
            background: white;
            border-radius: 20px;
            padding: 30px;
            width: 90%;
            max-width: 800px;
            margin: 0 auto;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .form-title {
            text-align: center;
            color: #1A6BD0;
            margin-bottom: 30px;
            font-weight: bold;
        }

        .form-section {
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #e0e0e0;
        }

        .form-section h3 {
            color: #35424a;
            font-size: 18px;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            font-weight: 500;
            color: #35424a;
            margin-bottom: 8px;
        }

        .form-control {
            border-radius: 8px;
            border: 1px solid #ced4da;
            padding: 10px 15px;
        }

        .btn-submit {
            background-color: #1A6BD0;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            font-weight: 500;
            width: 100%;
            margin-top: 20px;
        }

        .btn-submit:hover {
            background-color: #1557a0;
        }

        .required {
            color: red;
            margin-left: 3px;
        }

        footer {
            text-align: center;
            padding: 10px 0;
            background: #fff;
            color: #000;
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
        <div class="form-container">
            <h2 class="form-title">Formulir Pengurusan Pengurusan Ahli Waris</h2>
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data">
                <!-- Informasi Pengajuan -->
                <div class="form-section">
                    <h3>Informasi Pengajuan</h3>
                    <div class="form-group">
                        <label>Tanggal Pengajuan</label>
                        <input type="text" class="form-control" name="tanggal_pengajuan" value="<?php echo date('Y-m-d'); ?>" readonly>
                    </div>
                </div>

                <!-- Data Ahli Waris -->
                <div class="form-section">
                    <h3>Data Ahli Waris</h3>
                    <div class="form-group">
                        <label>Nama<span class="required">*</span></label>
                        <input type="text" class="form-control" name="nama" required>
                        <small class="form-text text-muted">Isi dengan Nama Lengkap, Contoh M.Budi Bin Hj.Ali</small>
                    </div>
                </div>

                <!-- Nomor Register -->
                <div class="form-section">
                    <h3>Nomor Register</h3>
                    <div class="form-group">
                        <label>Nomor Surat / Perihal / Lampiran<span class="required">*</span></label>
                        <textarea class="form-control" name="nomor_register" rows="3" required></textarea>
                        <small class="form-text text-muted">Kosongkan jika tidak ada</small>
                </div>

                <!-- Upload Dokumen -->
                <div class="form-section">
                    <h3>Upload Dokumen</h3>
                    <div class="form-group">
                        <label>Unggah Dokumen</label>
                        <input type="file" class="form-control-file" name="dokumen" accept=".pdf,.jpg,.jpeg,.png">
                        <small class="form-text text-muted">Upload dokumen terkait, Format PDF atau Word (opsional)</small>
                    </div>
                </div>

                <button type="submit" class="btn btn-submit">Kirim Pengajuan</button>
            </form>
        </div>
    </main>

    <footer>
        <p>&copy; 2024 - Kecamatan Bintan Timur</p>
    </footer>
</body>
</html>