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

// Tentukan tabel berdasarkan jenis
$jenis = isset($_GET['jenis']) ? $_GET['jenis'] : 'Uang_duka';
$tableName = $jenis;

// Pengaturan paginasi
$limit = 25;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

// Query dasar
$baseQuery = "SELECT ID, nama, tanggal_wafat, ahli_waris, tanggal_terima, alamat, kelurahan, tanggal_kirim FROM $tableName WHERE 1=1";

// Filter berdasarkan tahun jika ada
if (isset($_GET['year']) && !empty($_GET['year'])) {
    $year = $conn->real_escape_string($_GET['year']);
    $baseQuery .= " AND YEAR(STR_TO_DATE(tanggal_kirim, '%Y-%m-%d')) = '$year'";
}

// Pencarian
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = $conn->real_escape_string($_GET['search']);
    $baseQuery .= " AND (nama LIKE '%$search%' OR tanggal_wafat LIKE '%$search%' OR ahli_waris LIKE '%$search%')";
}

// Query untuk menghitung total rows
$total_rows = $conn->query($baseQuery)->num_rows;
$total_pages = ceil($total_rows / $limit);

// Query final dengan LIMIT untuk paginasi
$baseQuery .= " LIMIT $start, $limit";

// Eksekusi query
$result = $conn->query($baseQuery);
if (!$result) {
    die("Query failed: " . $conn->error);
}

// Fungsi untuk membuat link paginasi
function getPaginationLink($i, $jenis, $year, $search) {
    $link = "?jenis=$jenis&page=$i";
    if ($year !== null) $link .= "&year=$year";
    if ($search !== null) $link .= "&search=" . urlencode($search);
    return $link;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Penerima Uang Duka | Admin</title>
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
        .table {
            font-size: 0.9rem;
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
        .table td:nth-child(2) { /* Kolom keempat */
            text-align: left;
        }

        .table td:nth-child(4) { /* Kolom keempat */
            text-align: left;
        }

        .table td:nth-child(6) { /* Kolom keempat */
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
    <h1 style="text-align: left; font-weight:800; margin-top: 40px; margin-bottom: 40px;">Registrasi Penerima Uang Duka</h1>

        <div class="d-flex justify-content-between align-items-center mb-3">
        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#tambahDataModal">+ Tambah Data</button>

            <form method="GET" action="" class="d-flex align-items-center">
                <input type="hidden" name="jenis" value="<?php echo $jenis; ?>">
                <select name="year" id="yearSelect" class="form-control form-control-sm mr-2" onchange="this.form.submit()">
                    <option value="">Semua Tahun</option>
                    <?php
                    $currentYear = date('Y');
                    for ($year = $currentYear; $year >= $currentYear - 10; $year--) {
                        echo "<option value='$year'" . (isset($_GET['year']) && $_GET['year'] == $year ? ' selected' : '') . ">$year</option>";
                    }
                    ?>
                </select>
                <input type="text" name="search" class="form-control form-control-sm mr-2" placeholder="Cari..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                <button type="submit" class="btn btn-primary btn-sm">Cari</button>

                <div id="debugInfo" style="display:none;">
                    <pre id="debugText"></pre>
                </div>
            </form>
        </div>

        <div class="table-container">
        <table class="table table-striped table-sm mt-3">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 10%;">Nama</th>
                <th style="width: 15%;">Tanggal Wafat</th>
                <th style="width: 10%;">Ahli Waris</th>
                <th style="width: 15%;">Tanggal Terima</th>
                <th style="width: 25%;">Alamat</th>
                <th style="width: 5%;">Kelurahan</th>
                <th style="width: 15%;">Tanggal Kirim</th>
                <th style="width: 5%;">Aksi</th>
            </tr>
        </thead>
            <tbody>
                <?php
                $no = $start + 1;
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $no++ . "</td>";
                    echo "<td>" . htmlspecialchars($row['nama']) . "</td>";
                    echo "<td>" . date('d-m-Y', strtotime($row['tanggal_wafat'])) . "</td>";
                    echo "<td>" . htmlspecialchars($row['ahli_waris']) . "</td>";
                    echo "<td>" . date('d-m-Y', strtotime($row['tanggal_terima'])) . "</td>";
                    echo "<td>" . htmlspecialchars($row['alamat']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['kelurahan']) . "</td>";
                    echo "<td>" . date('d-m-Y', strtotime($row['tanggal_kirim'])) . "</td>";
                    echo "<td>
                                <div class='btn-group btn-group-sm' role='group'>
                                    <button class='btn btn-secondary btn-warning edit-data mr-1' data-id='" . $row['ID'] . "' data-nama='" . htmlspecialchars($row['nama']) . "' data-tanggal_wafat='" . $row['tanggal_wafat'] . "' data-ahli_waris='" . htmlspecialchars($row['ahli_waris']) . "' data-tanggal_terima='" . $row['tanggal_terima'] . "' data-alamat='" . htmlspecialchars($row['alamat']) . "' data-kelurahan='" . htmlspecialchars($row['kelurahan']) . "' data-tanggal_kirim='" . $row['tanggal_kirim'] . "'title='Edit'><i class='fas fa-edit'></i></button>
                                    <button class='btn btn-primary btn-danger hapus-data' data-id='" . $row['ID'] . "'title='Hapus'><i class='fas fa-trash'></i></button>
                                    <button class='btn btn-success upload-dokumen' data-id='" . $row['ID'] . "' title='Unggah Dokumen'><i class='fas fa-upload'></i></button>
                                    <button class='btn btn-info download-berkas' data-id='" . $row['ID'] . "' title='Unduh Berkas'><i class='fas fa-download'></i></button>
                                </div>
                            </td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>

        <!-- Pagination -->
        <ul class="pagination">
            <?php
            $start_page = max(1, $page - 2);
            $end_page = min($total_pages, $page + 2);

            $current_year = isset($_GET['year']) ? $_GET['year'] : null;
            $current_search = isset($_GET['search']) ? $_GET['search'] : null;

            if ($page > 1) {
                echo "<li><a href='" . getPaginationLink(1, $jenis, $current_year, $current_search) . "'>First</a></li>";
                echo "<li><a href='" . getPaginationLink($page - 1, $jenis, $current_year, $current_search) . "'>Previous</a></li>";
            }

            for ($i = $start_page; $i <= $end_page; $i++) {
                echo "<li" . ($page == $i ? " class='active'" : "") . "><a href='" . getPaginationLink($i, $jenis, $current_year, $current_search) . "'>$i</a></li>";
            }

            if ($page < $total_pages) {
                echo "<li><a href='" . getPaginationLink($page + 1, $jenis, $current_year, $current_search) . "'>Next</a></li>";
                echo "<li><a href='" . getPaginationLink($total_pages, $jenis, $current_year, $current_search) . "'>Last</a></li>";
            }
            ?>
        </ul>
    </div>
</div>

<!-- Modal Tambah Data -->
<div class="modal fade" id="tambahDataModal" tabindex="-1" role="dialog" aria-labelledby="tambahDataModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="tambahDataModalLabel">Tambah Data</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="formTambahData">
          <div class="form-group">
            <label for="nama">Nama</label>
            <input type="text" class="form-control" id="nama" name="nama" required>
          </div>
          <div class="form-group">
            <label for="tanggalWafat">Tanggal Wafat</label>
            <input type="date" class="form-control" id="tanggalWafat" name="tanggalWafat" required>
          </div>
          <div class="form-group">
            <label for="ahliWaris">Ahli Waris</label>
            <input type="text" class="form-control" id="ahliWaris" name="ahliWaris" required>
          </div>
          <div class="form-group">
            <label for="tanggalTerima">Tanggal Terima</label>
            <input type="date" class="form-control" id="tanggalTerima" name="tanggalTerima" required>
          </div>
          <div class="form-group">
            <label for="alamat">Alamat</label>
            <textarea class="form-control" id="alamat" name="alamat" required></textarea>
          </div>
          <div class="form-group">
            <label for="kelurahan">Kelurahan</label>
            <input type="text" class="form-control" id="kelurahan" name="kelurahan" required>
          </div>
          <div class="form-group">
            <label for="tanggalKirim">Tanggal Kirim</label>
            <input type="date" class="form-control" id="tanggalKirim" name="tanggalKirim" required>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
        <button type="button" class="btn btn-primary" id="btnSimpan">Simpan</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Edit Data -->
<div class="modal fade" id="editDataModal" tabindex="-1" role="dialog" aria-labelledby="editDataModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editDataModalLabel">Edit Data</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="formEditData">
          <input type="hidden" id="editId" name="id">
          <div class="form-group">
            <label for="editNama">Nama</label>
            <input type="text" class="form-control" id="editNama" name="nama" required>
          </div>
          <div class="form-group">
            <label for="editTanggalWafat">Tanggal Wafat</label>
            <input type="date" class="form-control" id="editTanggalWafat" name="tanggalWafat" required>
          </div>
          <div class="form-group">
            <label for="editAhliWaris">Ahli Waris</label>
            <input type="text" class="form-control" id="editAhliWaris" name="ahliWaris" required>
          </div>
          <div class="form-group">
            <label for="editTanggalTerima">Tanggal Terima</label>
            <input type="date" class="form-control" id="editTanggalTerima" name="tanggalTerima" required>
          </div>
          <div class="form-group">
            <label for="editAlamat">Alamat</label>
            <textarea class="form-control" id="editAlamat" name="alamat" required></textarea>
          </div>
          <div class="form-group">
            <label for="editKelurahan">Kelurahan</label>
            <input type="text" class="form-control" id="editKelurahan" name="kelurahan" required>
          </div>
          <div class="form-group">
            <label for="editTanggalKirim">Tanggal Kirim</label>
            <input type="date" class="form-control" id="editTanggalKirim" name="tanggalKirim" required>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-primary" id="btnSimpanEdit">Simpan Perubahan</button>
      </div>
    </div>
  </div>
</div>

<!-- Tambahkan modal untuk unggah dokumen -->
<div class="modal fade" id="uploadDokumenModal" tabindex="-1" role="dialog" aria-labelledby="uploadDokumenModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="uploadDokumenModalLabel">Unggah Dokumen</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="formUploadDokumen" enctype="multipart/form-data">
          <input type="hidden" id="uploadId" name="id">
          <div class="form-group">
            <label for="dokumen">Pilih Dokumen</label>
            <input type="file" class="form-control-file" id="dokumen" name="dokumen" required>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-primary" id="btnUploadDokumen">Unggah</button>
      </div>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js"></script>
<script>
$(document).ready(function() {
  $('#btnSimpan').click(function() {
    var formData = $('#formTambahData').serialize();
    $.ajax({
      url: 'tambah_data_uang_duka.php',
      type: 'POST',
      data: formData,
      success: function(response) {
        if(response == 'success') {
          alert('Data berhasil ditambahkan');
          $('#tambahDataModal').modal('hide');
          location.reload(); // Refresh halaman untuk menampilkan data baru
        } else {
          alert('Gagal menambahkan data');
        }
      },
      error: function() {
        alert('Terjadi kesalahan');
      }
    });
  });
});

// Fungsi untuk menangani klik tombol hapus
$('.hapus-data').click(function() {
        var id = $(this).data('id');
        if (confirm('Apakah Anda yakin ingin menghapus data ini?')) {
            $.ajax({
                url: 'hapus_data_uang_duka.php',
                type: 'POST',
                data: {id: id},
                success: function(response) {
                    if(response == 'success') {
                        alert('Data berhasil dihapus');
                        location.reload(); // Refresh halaman
                    } else {
                        alert('Gagal menghapus data');
                    }
                },
                error: function() {
                    alert('Terjadi kesalahan');
                }
            });
        }
});

$(document).ready(function() {$(document).ready(function() {
    // Fungsi untuk menangani klik tombol edit
    // Ganti fungsi untuk menangani klik tombol edit
    $('.edit-data').click(function() {
    var id = $(this).data('id');
    var nama = $(this).data('nama');
    var tanggalWafat = $(this).data('tanggal_wafat');
    var ahliWaris = $(this).data('ahli_waris');
    var tanggalTerima = $(this).data('tanggal_terima');
    var alamat = $(this).data('alamat');
    var kelurahan = $(this).data('kelurahan');
    var tanggalKirim = $(this).data('tanggal_kirim');

    $('#editId').val(id);
    $('#editNama').val(nama);
    $('#editTanggalWafat').val(tanggalWafat);
    $('#editAhliWaris').val(ahliWaris);
    $('#editTanggalTerima').val(tanggalTerima);
    $('#editAlamat').val(alamat);
    $('#editKelurahan').val(kelurahan);
    $('#editTanggalKirim').val(tanggalKirim);
    
    $('#editDataModal').modal('show');
});

    // Fungsi untuk menyimpan perubahan
    $('#btnSimpanEdit').click(function() {
        var formData = $('#formEditData').serialize();
        $.ajax({
            url: 'update_data_uang_duka.php',
            type: 'POST',
            data: formData,
            success: function(response) {
                console.log(response); // Untuk debugging
                if(response.trim() == 'success') {
                    alert('Data berhasil diperbarui');
                    $('#editDataModal').modal('hide');
                    location.reload(); // Refresh halaman
                } else {
                    alert('Gagal memperbarui data: ' + response);
                }
            },
            error: function(xhr, status, error) {
                console.log(xhr.responseText); // Untuk debugging
                alert('Terjadi kesalahan: ' + error);
            }
        });
    });
});

    // Fungsi untuk menyimpan perubahan
    $('#btnSimpanEdit').click(function() {
        var formData = $('#formEditData').serialize();
        $.ajax({
            url: 'update_data_uang_duka.php',
            type: 'POST',
            data: formData,
            success: function(response) {
                if(response == 'success') {
                    alert('Data berhasil diperbarui');
                    $('#editDataModal').modal('hide');
                    location.reload(); // Refresh halaman
                } else {
                    alert('Gagal memperbarui data');
                }
            },
            error: function() {
                alert('Terjadi kesalahan');
            }
        });
    });
});

$(document).ready(function() {
    // Handler untuk tombol unggah dokumen
    $('.upload-dokumen').click(function() {
        var id = $(this).data('id');
        $('#uploadId').val(id);
        $('#uploadDokumenModal').modal('show');
    });

    // Handler untuk submit form unggah dokumen
    $('#btnUploadDokumen').click(function() {
    var formData = new FormData($('#formUploadDokumen')[0]);
    $.ajax({
        url: 'upload_dokumen_uang_duka.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            if(response.status == 'success') {
                alert('Dokumen berhasil diunggah');
                $('#uploadDokumenModal').modal('hide');
                location.reload();
            } else {
                alert('Gagal mengunggah dokumen: ' + response.message);
            }
        },
        error: function() {
            alert('Terjadi kesalahan saat mengunggah dokumen');
        }
    });
});

    // Handler untuk tombol unduh berkas
    $('.download-berkas').click(function() {
        var id = $(this).data('id');
        window.location.href = 'download_berkas_uang_duka.php?id=' + id;
    });
});

    document.getElementById('sidebartoggle').addEventListener('click', function() {
        document.querySelector('.sidebar').classList.toggle('active');
        document.querySelector('.content').classList.toggle('active');
        document.querySelector('.header').classList.toggle('active');
    });

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
        var sktmToggle = document.getElementById('sktmToggle');
        var sktmSubmenu = document.getElementById('sktmSubmenu');

        sktmToggle.addEventListener('click', function(e) {
            e.preventDefault();
            sktmSubmenu.classList.toggle('active');
        });

        // Menandai submenu yang aktif
        var currentPage = window.location.pathname.split("/").pop();
        var submenuLinks = sktmSubmenu.getElementsByTagName('a');
        for (var i = 0; i < submenuLinks.length; i++) {
            if (submenuLinks[i].getAttribute('href') === currentPage) {
                submenuLinks[i].classList.add('active');
                sktmSubmenu.classList.add('active');
                break;
            }
        }
    });

    document.getElementById('yearSelect').addEventListener('change', function() {
        this.form.submit();
    });
</script>
</body>
</html>