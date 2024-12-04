<?php
session_start();
include 'config.php';

// Function to get table structure
function getTableColumns($table) {
    global $conn;
    $query = "SHOW COLUMNS FROM $table";
    $result = $conn->query($query);
    if (!$result) {
        error_log("Failed to get columns for table $table: " . $conn->error);
        return false;
    }
    $columns = [];
    while ($row = $result->fetch_assoc()) {
        $columns[] = $row['Field'];
    }
    return $columns;
}

// Function to get all submissions from formulir_sktm
function getSubmissionsByType($type) {
    global $conn;
    
    $query = "SELECT * FROM formulir_sktm WHERE jenis_sktm = ? ORDER BY tanggal_pengajuan DESC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $type);
    $stmt->execute();
    $result = $stmt->get_result();
    
    return $result;
}

// New function to get dispensasi nikah submissions
function getDispensasiSubmissions() {
    global $conn;
    
    $query = "SELECT * FROM formulir_dispensasi_nikah ORDER BY tanggal_pengajuan DESC";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $result = $stmt->get_result();
    
    return $result;
}


// New function to get uang duka submissions
function getUangDukaSubmissions() {
    global $conn;
    
    $query = "SELECT * FROM formulir_uang_duka ORDER BY tanggal_pengajuan DESC";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $result = $stmt->get_result();
    
    return $result;
}

// New function to get ahli waris submissions
function getAhliWarisSubmissions() {
    global $conn;
    
    $query = "SELECT * FROM formulir_ahli_waris ORDER BY tanggal_pengajuan DESC";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $result = $stmt->get_result();
    
    return $result;
}

// Function to delete submission
function deleteSubmission($id, $type) {
    global $conn;
    
    if ($type === 'dispensasi') {
        // Delete from dispensasi nikah table
        $query_find_doc = "SELECT dokumen FROM formulir_dispensasi_nikah WHERE id = ?";
        $query_delete = "DELETE FROM formulir_dispensasi_nikah WHERE id = ?";
        // Delete from uang duka table
    } elseif ($type === 'ahliwaris') {
        // Delete from dispensasi nikah table
        $query_find_doc = "SELECT dokumen FROM formulir_ahli_waris WHERE id = ?";
        $query_delete = "DELETE FROM formulir_ahli_waris WHERE id = ?";
        // Delete from uang duka table
    } elseif ($type === 'uangduka') {
        $query_find_doc = "SELECT dokumen FROM formulir_uang_duka WHERE id = ?";
        $query_delete = "DELETE FROM formulir_uang_duka WHERE id = ?";
    } else {
        // Delete from SKTM table
        $query_find_doc = "SELECT dokumen FROM formulir_sktm WHERE id = ?";
        $query_delete = "DELETE FROM formulir_sktm WHERE id = ?";
    }
    
    // Find document to delete
    $stmt_find_doc = $conn->prepare($query_find_doc);
    $stmt_find_doc->bind_param("i", $id);
    $stmt_find_doc->execute();
    $result = $stmt_find_doc->get_result();
    $row = $result->fetch_assoc();
    
    // Delete document file if exists
    if ($row['dokumen'] && file_exists('uploads/dokumen/' . $row['dokumen'])) {
        unlink('uploads/dokumen/' . $row['dokumen']);
    }
    
    // Delete data from database
    $stmt_delete = $conn->prepare($query_delete);
    $stmt_delete->bind_param("i", $id);
    
    if ($stmt_delete->execute()) {
        return true;
    } else {
        error_log("Failed to delete data: " . $stmt_delete->error);
        return false;
    }
}

// Process deletion if request exists
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_id'])) {
    $delete_id = $_POST['delete_id'];
    $delete_type = $_POST['delete_type'] ?? 'sktm'; // Default to 'sktm' if not specified
    
    if (deleteSubmission($delete_id, $delete_type)) {
        $_SESSION['message'] = "Data berhasil dihapus.";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Gagal menghapus data.";
        $_SESSION['message_type'] = "danger";
    }
    header("Location: recently_added.php");
    exit();
}

// Get submissions by type
$kesehatan = getSubmissionsByType('Kesehatan');
$pendidikan = getSubmissionsByType('Pendidikan');
$umum = getSubmissionsByType('Umum');
$dispensasiNikah = getDispensasiSubmissions();
$penerimaanUangNikah = getUangDukaSubmissions();
$pengurusanAhliWaris = getAhliWarisSubmissions();

// Function to safely display SKTM table data with delete option
function displayTableDataWithDelete($result) {
    if ($result && $result->num_rows > 0) {
        $no = 1;
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $no++ . "</td>";
            echo "<td>" . date('d/m/Y', strtotime($row['tanggal_pengajuan'])) . "</td>";
            echo "<td>" . htmlspecialchars($row['jenis_sktm']) . "</td>";
            echo "<td>" . htmlspecialchars($row['nama']) . "</td>";
            echo "<td>" . htmlspecialchars($row['pengurusan']) . "</td>";
            echo "<td>" . htmlspecialchars($row['alamat']) . "</td>";
            
            // Document column
            echo "<td>";
            if ($row['dokumen']) {
                echo "<a href='uploads/dokumen/" . htmlspecialchars($row['dokumen']) . "' class='document-link' target='_blank'>Lihat Dokumen</a>";
            } else {
                echo "Tidak ada";
            }
            echo "</td>";
            
            // Action column with delete button
            echo "<td>";
            echo "<button class='btn btn-danger btn-sm delete-btn' data-id='" . $row['id'] . "' data-type='sktm'>Hapus</button>";
            echo "</td>";
            
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='8' class='text-center'>Tidak ada data pengajuan</td></tr>";
    }
}

// New function to display dispensasi nikah table data
function displayDispensasiTableData($result) {
    if ($result && $result->num_rows > 0) {
        $no = 1;
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $no++ . "</td>";
            echo "<td>" . date('d/m/Y', strtotime($row['tanggal_pengajuan'])) . "</td>";
            echo "<td>" . htmlspecialchars($row['nama_suami']) . "</td>";
            echo "<td>" . htmlspecialchars($row['nama_istri']) . "</td>";
            echo "<td>" . htmlspecialchars($row['nomor_surat']) . "</td>";
            echo "<td>" . htmlspecialchars($row['pengantar_KUA']) . "</td>";
            
            // Document column
            echo "<td>";
            if ($row['dokumen']) {
                echo "<a href='uploads/dokumen/" . htmlspecialchars($row['dokumen']) . "' class='document-link' target='_blank'>Lihat Dokumen</a>";
            } else {
                echo "Tidak ada";
            }
            echo "</td>";
            
            // Action column with delete button
            echo "<td>";
            echo "<button class='btn btn-danger btn-sm delete-btn' data-id='" . $row['id'] . "' data-type='dispensasi'>Hapus</button>";
            echo "</td>";
            
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='8' class='text-center'>Tidak ada data pengajuan dispensasi nikah</td></tr>";
    }
}

// New function to display Uang Duka table data
function displayUangaDukaTableData($result) {
    if ($result && $result->num_rows > 0) {
        $no = 1;
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $no++ . "</td>";
            echo "<td>" . date('d/m/Y', strtotime($row['tanggal_pengajuan'])) . "</td>";
            echo "<td>" . htmlspecialchars($row['nama']) . "</td>";
            echo "<td>" . htmlspecialchars($row['tanggal_wafat']) . "</td>";
            echo "<td>" . htmlspecialchars($row['ahli_waris']) . "</td>";
            echo "<td>" . htmlspecialchars($row['alamat']) . "</td>";
            echo "<td>" . htmlspecialchars($row['kelurahan']) . "</td>";
            
            // Document column
            echo "<td>";
            if ($row['dokumen']) {
                echo "<a href='uploads/dokumen/" . htmlspecialchars($row['dokumen']) . "' class='document-link' target='_blank'>Lihat Dokumen</a>";
            } else {
                echo "Tidak ada";
            }
            echo "</td>";
            
            // Action column with delete button
            echo "<td>";
            echo "<button class='btn btn-danger btn-sm delete-btn' data-id='" . $row['id'] . "' data-type='uangduka'>Hapus</button>";
            echo "</td>";
            
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='8' class='text-center'>Tidak ada data pengajuan dispensasi nikah</td></tr>";
    }
}

// New function to display Ahli Waris table data
function displayAhliWarisTableData($result) {
    if ($result && $result->num_rows > 0) {
        $no = 1;
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $no++ . "</td>";
            echo "<td>" . date('d/m/Y', strtotime($row['tanggal_pengajuan'])) . "</td>";
            echo "<td>" . htmlspecialchars($row['nama']) . "</td>";
            echo "<td>" . htmlspecialchars($row['nomor_register']) . "</td>";
            
            // Document column
            echo "<td>";
            if ($row['dokumen']) {
                echo "<a href='uploads/dokumen/" . htmlspecialchars($row['dokumen']) . "' class='document-link' target='_blank'>Lihat Dokumen</a>";
            } else {
                echo "Tidak ada";
            }
            echo "</td>";
            
            // Action column with delete button
            echo "<td>";
            echo "<button class='btn btn-danger btn-sm delete-btn' data-id='" . $row['id'] . "' data-type='ahliwaris'>Hapus</button>";
            echo "</td>";
            
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='8' class='text-center'>Tidak ada data pengajuan dispensasi nikah</td></tr>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Pengajuan SKTM - SIMPENAN</title>
    <link rel="icon" type="image/x-icon" href="assets/Logo Simpenan real.png" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <style>
        /* Gaya CSS yang sama seperti sebelumnya */
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

        main {
            flex: 1;
            padding: 40px 20px;
        }

        .data-container {
            background: white;
            border-radius: 20px;
            padding: 30px;
            width: 95%;
            max-width: 1200px;
            margin: 0 auto;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .section-title {
            color: #1A6BD0;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #1A6BD0;
        }

        .table-responsive {
            margin-bottom: 30px;
        }

        .table {
            background-color: white;
            border-radius: 8px;
            overflow: hidden;
        }

        .table th {
            background-color: #1A6BD0;
            color: white;
            font-weight: 500;
            border-bottom: none;
        }

        .table td {
            vertical-align: middle;
        }

        .nav-tabs {
            border-bottom: 2px solid #1A6BD0;
            margin-bottom: 20px;
        }

        .nav-tabs .nav-link {
            color: #1A6BD0;
            border: none;
            font-weight: 500;
        }

        .nav-tabs .nav-link.active {
            color: white;
            background-color: #1A6BD0;
            border-radius: 8px 8px 0 0;
        }

        .document-link {
            color: #1A6BD0;
            text-decoration: none;
        }

        .document-link:hover {
            text-decoration: underline;
        }

        footer {
            text-align: center;
            padding: 10px 0;
            background: #fff;
            color: #000;
        }

        /* Tambahan untuk notifikasi */
        .alert {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 1050;
            width: 80%;
            max-width: 500px;
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
        </div>
    </header>

    <main>
        <!-- Tambahkan atribut data-title pada setiap tab link -->
        <div class="data-container">
            <h2 class="text-center mb-4" id="pageTitle">Data Pengajuan SKTM</h2>

            <ul class="nav nav-tabs" id="submissionTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="kesehatan-tab" data-toggle="tab" href="#kesehatan" role="tab" 
                    data-title="Data Pengajuan SKTM">Kesehatan</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="pendidikan-tab" data-toggle="tab" href="#pendidikan" role="tab"
                    data-title="Data Pengajuan SKTM">Pendidikan</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="umum-tab" data-toggle="tab" href="#umum" role="tab"
                    data-title="Data Pengajuan SKTM">Umum</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="dispensasi-tab" data-toggle="tab" href="#dispensasi" role="tab"
                    data-title="Data Pengajuan Dispensasi Nikah">Dispensasi Nikah</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="ahliwaris-tab" data-toggle="tab" href="#ahliwaris" role="tab"
                    data-title="Data Pengurusan Ahli Waris">Pengurusan Ahli Waris</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="uangduka-tab" data-toggle="tab" href="#uangduka" role="tab"
                    data-title="Data Permohonan Uang Duka">Permohonan Uang Duka</a>
                </li>
            </ul>

            <div class="tab-content" id="submissionTabsContent">
                <!-- Kesehatan Tab -->
                <div class="tab-pane fade show active" id="kesehatan" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>Jenis SKTM</th>
                                    <th>Nama</th>
                                    <th>Pengurusan</th>
                                    <th>Alamat</th>
                                    <th>Dokumen</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php displayTableDataWithDelete($kesehatan); ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Pendidikan Tab -->
                <div class="tab-pane fade" id="pendidikan" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>Jenis SKTM</th>
                                    <th>Nama</th>
                                    <th>Pengurusan</th>
                                    <th>Alamat</th>
                                    <th>Dokumen</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php displayTableDataWithDelete($pendidikan); ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Umum Tab -->
                <div class="tab-pane fade" id="umum" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>Jenis SKTM</th>
                                    <th>Nama</th>
                                    <th>Pengurusan</th>
                                    <th>Alamat</th>
                                    <th>Dokumen</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php displayTableDataWithDelete($umum); ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Dispensasi Tab -->
                <div class="tab-pane fade" id="dispensasi" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>Nama Suami</th>
                                    <th>Nama Istri</th>
                                    <th>Nomor Surat</th>
                                    <th>Pengantar KUA</th>
                                    <th>Dokumen</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php displayDispensasiTableData($dispensasiNikah); ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Ahli Waris Tab -->
                <div class="tab-pane fade" id="ahliwaris" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>Nama</th>
                                    <th>Nomor Register</th>
                                    <th>Dokumen</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php displayAhliWarisTableData($pengurusanAhliWaris); ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Uang Duka Tab -->
                <div class="tab-pane fade" id="uangduka" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>Nama</th>
                                    <th>Tanggal Wafat</th>
                                    <th>Ahli Waris</th>
                                    <th>Alamat</th>
                                    <th>Kelurahan</th>
                                    <th>Dokumen</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php displayUangaDukaTableData($penerimaanUangNikah); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal Konfirmasi Hapus -->
    <div class="modal fade" id="deleteConfirmModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Konfirmasi Hapus</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Anda yakin ingin menghapus data ini?
                </div>
                <div class="modal-footer">
                    <form id="deleteForm" method="POST">
                        <input type="hidden" name="delete_id" id="deleteId">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <footer>
        <p>&copy; 2024 - Kecamatan Bintan Timur</p>
    </footer>

    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <script>
    $(document).ready(function() {
        // Tangani klik tombol hapus
        $('.delete-btn').on('click', function() {
        var id = $(this).data('id');
        var type = $(this).data('type');
        $('#deleteId').val(id);
        $('#deleteType').val(type);
        $('#deleteConfirmModal').modal('show');
        });

        // Add hidden input for delete type in the form
        $('#deleteForm').append('<input type="hidden" name="delete_type" id="deleteType">');

        // Handler untuk perubahan tab yang akan mengubah judul
        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        var title = $(e.target).data('title');
        $('#pageTitle').text(title);
        });

        // Tampilkan notifikasi jika ada
        <?php if(isset($_SESSION['message'])): ?>
        var messageType = "<?php echo $_SESSION['message_type']; ?>";
        var message = "<?php echo $_SESSION['message']; ?>";
        
        $('body').prepend('<div class="alert alert-' + messageType + ' alert-dismissible fade show" role="alert">' +
            message +
            '<button type="button" class="close" data-dismiss="alert">&times;</button>' +
            '</div>');
        
        // Hapus pesan dari session setelah ditampilkan
        <?php 
        unset($_SESSION['message']); 
        unset($_SESSION['message_type']); 
        ?>
        <?php endif; ?>

        // Sembunyikan notifikasi otomatis setelah 5 detik
        setTimeout(function() {
            $('.alert').alert('close');
        }, 5000);
    });
    </script>
</body>
</html>