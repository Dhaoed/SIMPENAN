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

// Pengaturan paginasi
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

// Ganti kode yang ada dengan:
$countQuery = "SELECT COUNT(*) as total FROM faq WHERE 1=1";

// Tambahkan pencarian jika ada
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = $conn->real_escape_string($_GET['search']);
    $countQuery .= " AND (isi LIKE '%$search%' OR respon LIKE '%$search%')";
}

// Eksekusi query untuk menghitung total
$totalResult = $conn->query($countQuery);
$totalRow = $totalResult->fetch_assoc();
$total_rows = $totalRow['total'];
$total_pages = ceil($total_rows / $limit);

// Query untuk mengambil data
$baseQuery = "SELECT * FROM faq WHERE 1=1";

// Tambahkan pencarian
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = $conn->real_escape_string($_GET['search']);
    $baseQuery .= " AND (isi LIKE '%$search%' OR respon LIKE '%$search%')";
}

// Tambahkan pagination
$baseQuery .= " LIMIT $start, $limit";

// Eksekusi query
$result = $conn->query($baseQuery);
if (!$result) {
    die("Query failed: " . $conn->error . "\nQuery: " . $baseQuery);
}

// Tambahkan handler untuk operasi AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handler untuk tambah FAQ
    if (isset($_POST['action']) && $_POST['action'] == 'tambah_faq') {
        $isi = $_POST['isi'];
        $respon = isset($_POST['respon']) ? $_POST['respon'] : '';

        $query = "INSERT INTO faq (isi, respon) VALUES (?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ss", $isi, $respon);

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Pertanyaan berhasil ditambahkan']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menambahkan pertanyaan']);
        }
        exit();
    }

// Handler untuk edit FAQ
if (isset($_POST['action']) && $_POST['action'] == 'edit_faq') {
    $id = $_POST['id'];
    $isi = trim($_POST['isi']); // Hapus whitespace ekstra
    $respon = trim($_POST['respon']); // Hapus whitespace ekstra

    // Pastikan ID valid
    if (!is_numeric($id)) {
        echo json_encode(['status' => 'error', 'message' => 'ID tidak valid']);
        exit();
    }

    // Buat prepared statement yang hanya mengupdate kolom-kolom yang diisi
    $query = "UPDATE faq SET ";
    $params = [];
    $types = "";

    if (!empty($isi)) {
        $query .= "isi = ?, ";
        $params[] = $isi;
        $types .= "s";
    }

    if (!empty($respon)) {
        $query .= "respon = ?, ";
        $params[] = $respon;
        $types .= "s";
    }

    // Hapus koma terakhir
    $query = rtrim($query, ", ");
    
    // Tambahkan kondisi WHERE
    $query .= " WHERE id = ?";
    $params[] = $id;
    $types .= "i";

    // Hanya lanjutkan jika ada parameter yang diubah
    if (!empty($params)) {
        $stmt = $conn->prepare($query);
        
        // Bind parameters dinamis
        $stmt->bind_param($types, ...$params);

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                echo json_encode(['status' => 'success', 'message' => 'Pertanyaan berhasil diperbarui']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Tidak ada perubahan atau FAQ tidak ditemukan']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal memperbarui pertanyaan']);
        }
        $stmt->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Tidak ada data yang diubah']);
    }
    exit();
}

    // Handler untuk hapus FAQ
    if (isset($_POST['action']) && $_POST['action'] == 'hapus_faq') {
        $id = $_POST['id'];
    
        // Pastikan ID valid
        if (!is_numeric($id)) {
            echo json_encode(['status' => 'error', 'message' => 'ID tidak valid']);
            exit();
        }
    
        $query = "DELETE FROM faq WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id);
    
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                echo json_encode(['status' => 'success', 'message' => 'Pertanyaan berhasil dihapus']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Pertanyaan tidak ditemukan']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus pertanyaan']);
        }
        $stmt->close();
        exit();
    }
}

// Fungsi untuk membuat link paginasi
function getPaginationLink($i, $search, $status) {
    $link = "?page=$i";
    if ($search !== null) $link .= "&search=" . urlencode($search);
    if ($status !== null) $link .= "&status=" . urlencode($status);
    return $link;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Saran & Pertanyaan | Admin</title>
    <link rel="icon" type="image/x-icon" href="assets/Logo Simpenan real.png" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
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

        /*.sidebar-button {
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
            padding: 41px;
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
        .main-content {
            display: flex;
            flex-direction: column;
            margin-top: 70px;
        }
        #sidebartoggle {
            position: fixed;
            color: white;
            top: 20px;
            left: 260px;
            cursor: pointer;
            z-index: 1001;
            font-size: 30px;
        }
        .sidebar, .content, .header, .sidebartoggle {
            transition: all 0.3s ease;
        }
        .pagination {
            display: flex;
            justify-content: center;
            list-style: none;
            padding: 0;
            margin-top: 20px;
        }
        .pagination li {
            margin: 0 5px;
        }
        .pagination li a {
            text-decoration: none;
            padding: 5px 10px;
            border: 1px solid #007bff;
            color: #007bff;
            border-radius: 3px;
        }
        .pagination li.active a {
            background-color: #007bff;
            color: white;
        }
        .submenu a.active {
            background-color: #52B0ED;
            border-radius: 8px;
            color: black;
        }
        .table-container {
            overflow-x: auto;
        }
        .table th, .table td {
            padding: 0.5rem;
            vertical-align: middle;
            text-align: center; /* Menambahkan properti ini */
        }
        .table th {
            background-color: #f8f9fa;
            font-weight: 600;
            vertical-align: middle; /* Memastikan header juga berada di tengah secara vertikal */
        }
        .table td:nth-child(2) { /* Kolom keempat (Nama) */
            text-align: left;
        }

        .table td:nth-child(3) { /* Kolom keempat (Alamat) */
            text-align: left;
        }

        @media (max-width: 768px) {
            
        .table {
                font-size: 0.8rem;
        }
        .table th, .table td {
                padding: 0.3rem;
            }
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
<a href="faq_admin.php"><img src="assets/Logo Faq.svg" alt="Logout Icon" width="20" height="20"> Saran & Pertanyaan </a>
<a href="logout.php"><img src="assets/keluar.svg" alt="Logout Icon" width="20" height="20"> Keluar</a>

<!--<div class="sidebar-button">
    <a href="about_us.php"><img src="assets/information.png" alt="About Us Icon" width="20" height="20"> About Us</a>
</div> -->
</div>

<div id="sidebartoggle">☰</div>
<div class="content">
<div class="header">
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

        <div class="main-content">
        <h1 style="text-align: left; font-weight:800; margin-top: 40px; margin-bottom: 40px;">Saran & Pertanyaan</h1>
            <div class="container-fluid">
                <!-- Filter dan Pencarian -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <form method="GET" class="form-inline">
                            <input type="text" name="search" class="form-control mr-2" placeholder="Cari pertanyaan/respon" 
                                   value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                            <button type="submit" class="btn btn-primary">Cari</button>
                        </form>
                    </div>
                    <div class="col-md-6 text-right">
                        <button class="btn btn-success" data-toggle="modal" data-target="#tambahPertanyaanModal">
                            <i class="fas fa-plus"></i> Tambah Pertanyaan
                        </button>
                    </div>
                </div>

                <!-- Tabel Pertanyaan -->
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Pertanyaan</th>
                                <th>Respon</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = $start + 1;
                            while ($row = $result->fetch_assoc()): 
                            ?>
                            <tr>
                            <td><?php echo $no++; ?></td>
                                <td><?php echo htmlspecialchars($row['isi']); ?></td>
                                <td><?php echo !empty($row['respon']) ? htmlspecialchars($row['respon']) : '<span class="text-danger">Belum direspon</span>'; ?></td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-sm btn-primary edit-faq" 
                                                data-id="<?php echo $row['id']; ?>"
                                                data-isi="<?php echo htmlspecialchars($row['isi']); ?>"
                                                data-respon="<?php echo htmlspecialchars($row['respon']); ?>">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger hapus-faq" 
                                                data-id="<?php echo $row['id']; ?>">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <nav>
                    <ul class="pagination justify-content-center">
                        <?php for($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">
                                <a class="page-link" href="<?php echo getPaginationLink($i, 
                                    isset($_GET['search']) ? $_GET['search'] : null,
                                    isset($_GET['status']) ? $_GET['status'] : null); ?>">
                                    <?php echo $i; ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Pertanyaan -->
    <div class="modal fade" id="tambahPertanyaanModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Pertanyaan Baru</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formTambahPertanyaan">
                        <div class="form-group">
                            <label>Pertanyaan</label>
                            <textarea class="form-control" name="isi" rows="3" required></textarea>
                        </div>
                        <div class="form-group">
                            <label>Respon (Opsional)</label>
                            <textarea class="form-control" name="respon" rows="3"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="btnSimpanPertanyaan">Simpan</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Edit Pertanyaan -->
    <div class="modal fade" id="editPertanyaanModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Pertanyaan</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formEditPertanyaan">
                        <input type="hidden" name="id" id="editId">
                        <div class="form-group">
                            <label>Pertanyaan</label>
                            <textarea class="form-control" name="isi" id="editIsi" rows="3" required></textarea>
                        </div>
                        <div class="form-group">
                            <label>Respon</label>
                            <textarea class="form-control" name="respon" id="editRespon" rows="3"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="btnSimpanEditPertanyaan">Simpan Perubahan</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <script>
    $(document).ready(function() {
    // Tambah Pertanyaan
    $('#btnSimpanPertanyaan').click(function() {
        var formData = $('#formTambahPertanyaan').serialize() + '&action=tambah_faq';
        $.ajax({
            url: '', // request ke file yang sama
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if(response.status == 'success') {
                    alert(response.message);
                    $('#tambahPertanyaanModal').modal('hide');
                    location.reload();
                } else {
                    alert(response.message);
                }
            },
            error: function() {
                alert('Terjadi kesalahan');
            }
        });
    });

    // Edit Pertanyaan - Tampilkan Modal
    $('.edit-faq').click(function() {
        var id = $(this).data('id');
        var isi = $(this).data('isi');
        var respon = $(this).data('respon');

        $('#editId').val(id);
        $('#editIsi').val(isi);
        $('#editRespon').val(respon);
        
        $('#editPertanyaanModal').modal('show');
    });

    // Simpan Perubahan Pertanyaan
    $('#btnSimpanEditPertanyaan').click(function() {
    var id = $('#editId').val();
    var isi = $('#editIsi').val();
    var respon = $('#editRespon').val();

    var formData = {
        action: 'edit_faq',
        id: id,
        isi: isi,
        respon: respon
    };

    $.ajax({
        url: '', 
        type: 'POST',
        data: formData,
        dataType: 'json',
        success: function(response) {
            if(response.status == 'success') {
                alert(response.message);
                $('#editPertanyaanModal').modal('hide');
                location.reload();
            } else {
                alert(response.message);
            }
        },
        error: function() {
            alert('Terjadi kesalahan');
        }
    });
});

    // Hapus Pertanyaan
    $('.hapus-faq').click(function() {
        var id = $(this).data('id');
        if (confirm('Apakah Anda yakin ingin menghapus pertanyaan ini?')) {
            $.ajax({
                url: '', // request ke file yang sama
                type: 'POST',
                data: {id: id, action: 'hapus_faq'},
                dataType: 'json',
                success: function(response) {
                    if(response.status == 'success') {
                        alert(response.message);
                        location.reload();
                    } else {
                        alert(response.message);
                    }
                },
                error: function() {
                    alert('Terjadi kesalahan');
                }
            });
        }
    });
});
</script>
</body>
</html>