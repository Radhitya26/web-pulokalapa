<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}

$host = 'localhost';
$db   = 'pulokalapaa';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

$message = '';

// UMKM CRUD variables
$umkm_message = '';
$umkm_error = '';
$umkm_data = null;

// Create PDO connection
try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}

// Ambil notifikasi sukses dari query string jika ada
if (isset($_GET['msg']) && $_GET['msg'] === 'berita_berhasil') {
    $message = 'Berita berhasil ditambahkan.';
}

// Handle UMKM edit form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['umkm_edit_id'])) {
    $id = intval($_POST['umkm_edit_id']);
    $nama = $_POST['nama_umkm'] ?? '';
    $link = $_POST['link_gmaps'] ?? '';

    // Fetch existing UMKM data
    $stmt = $pdo->prepare('SELECT * FROM umkm WHERE id = ?');
    $stmt->execute([$id]);
    $umkm_data = $stmt->fetch();

    if (!$umkm_data) {
        $umkm_error = 'Data UMKM tidak ditemukan.';
    } else {
        $gambar_name = $umkm_data['gambar'];

        // Handle image upload if any
        if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === 0) {
            $upload_dir = __DIR__ . '/../assets/img/umkm/';
            $nama_file = uniqid() . '-' . basename($_FILES['gambar']['name']);
            $target = $upload_dir . $nama_file;

            if (move_uploaded_file($_FILES['gambar']['tmp_name'], $target)) {
                // Delete old image if exists
                if (!empty($umkm_data['gambar'])) {
                    $old_path = $upload_dir . $umkm_data['gambar'];
                    if (file_exists($old_path)) {
                        unlink($old_path);
                    }
                }
                $gambar_name = $nama_file;
            } else {
                $umkm_error = 'Gagal mengunggah gambar baru.';
            }
        }

        if (empty($umkm_error)) {
            // Update UMKM data
            $stmt = $pdo->prepare('UPDATE umkm SET nama_umkm = ?, link_gmaps = ?, gambar = ? WHERE id = ?');
            $updated = $stmt->execute([$nama, $link, $gambar_name, $id]);
            if ($updated) {
                $umkm_message = 'Data berhasil diperbarui.';
                // Refresh data
                $stmt = $pdo->prepare('SELECT * FROM umkm WHERE id = ?');
                $stmt->execute([$id]);
                $umkm_data = $stmt->fetch();
            } else {
                $umkm_error = 'Gagal menyimpan ke database.';
            }
        }
    }
}

// Handle UMKM edit ID from GET
if (isset($_GET['umkm_edit_id'])) {
    $id = intval($_GET['umkm_edit_id']);
    $stmt = $pdo->prepare('SELECT * FROM umkm WHERE id = ?');
    $stmt->execute([$id]);
    $umkm_data = $stmt->fetch();
    if (!$umkm_data) {
        $umkm_error = 'Data UMKM tidak ditemukan.';
    }
}

// Handle notifikasi sukses dari query string jika ada untuk UMKM
if (isset($_GET['msg']) && $_GET['msg'] === 'umkm_berhasil') {
    $umkm_message = 'UMKM berhasil ditambahkan.';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin Desa Pulokalapa</title>
    <!-- SB Admin 2 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/startbootstrap-sb-admin-2/4.1.4/css/sb-admin-2.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.1]5.4/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .modal-backdrop { z-index: 1040 !important; }
        .modal { z-index: 1100 !important; }
        .message { margin-top: 20px; padding: 10px; border-radius: 4px; }
        .success { background-color: #d4edda; color: #155724; }
        .error { background-color: #f8d7da; color: #721c24; }
    </style>
</head>
<body id="page-top">
    <!-- Page Wrapper -->
    <div id="wrapper">
        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="#">
                <div class="sidebar-brand-icon rotate-n-15">
                    <i class="fas fa-laugh-wink"></i>
                </div>
                <div class="sidebar-brand-text mx-3">Pulokalapa</div>
            </a>
            <hr class="sidebar-divider my-0">
            <li class="nav-item active">
                <a class="nav-link" href="#">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span></a>
            </li>
            <hr class="sidebar-divider">
            <li class="nav-item">
                <a class="nav-link" href="crud_berita.php">
                    <i class="fas fa-fw fa-plus"></i>
                    <span>Tambah Berita</span></a>
            <hr class="sidebar-divider">
            <li class="nav-item">
                <a class="nav-link" href="Dashboard.php?umkm_edit_id=new">
                    <i class="fas fa-fw fa-plus"></i>
                    <span>Tambah UMKM</span></a>
            </li>
            <hr class="sidebar-divider d-none d-md-block">
            <li class="nav-item">
                <a class="nav-link" href="logout.php">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span></a>
            </li>
        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">
            <!-- Main Content -->
            <div id="content">
                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                    <span class="mr-2 d-none d-lg-inline text-gray-600 small">Selamat Datang, <?php echo htmlspecialchars($_SESSION['admin_username']); ?>!</span>
                </nav>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">
                    <?php if ($message): ?>
                        <div class="message <?php echo strpos($message, 'berhasil') !== false ? 'success' : 'error'; ?>">
                            <?php echo htmlspecialchars($message); ?>
                        </div>
                    <?php endif; ?>

                    <!-- Daftar Berita -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Daftar Berita</h6>
                        </div>
                        <div class="card-body">
                        <?php
                        $stmt = $pdo->query('SELECT * FROM berita ORDER BY tanggal DESC, id DESC');
                        $beritaList = $stmt->fetchAll();
                        if (count($beritaList) === 0) {
                            echo '<p>Belum ada berita.</p>';
                        } else {
                            echo '<div class="table-responsive">';
                            echo '<table class="table table-bordered" width="100%" cellspacing="0">';
                            echo '<thead><tr><th>Judul</th><th>Tanggal</th><th>Gambar</th><th>Isi</th><th>Aksi</th></tr></thead><tbody>';
                            foreach ($beritaList as $berita) {
                                echo '<tr>';
                                echo '<td>' . htmlspecialchars($berita['judul']) . '</td>';
                                echo '<td>' . htmlspecialchars($berita['tanggal']) . '</td>';
                                if ($berita['gambar']) {
                                    echo '<td><img src="../assets/img/berita/' . htmlspecialchars($berita['gambar']) . '" alt="Gambar" style="max-width:60px; max-height:60px;"></td>';
                                } else {
                                    echo '<td>-</td>';
                                }
                                echo '<td style="max-width:200px; overflow:auto;">' . nl2br(htmlspecialchars($berita['isi'])) . '</td>';
                                echo '<td>';
                                echo '<a href="edit_berita.php?id=' . $berita['id'] . '" class="btn btn-sm btn-warning mr-1">Edit</a>';
                                echo '<a href="Dashboard.php?delete_id=' . $berita['id'] . '" class="btn btn-sm btn-danger" onclick="return confirm(\'Yakin ingin menghapus berita ini?\')">Hapus</a>';
                                echo '</td>';
                                echo '</tr>';
                            }
                            echo '</tbody></table></div>';
                        }
                        ?>
                        </div>
                    </div>

                    <!-- Daftar UMKM -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex justify-content-between align-items-center">
                            <h6 class="m-0 font-weight-bold text-primary">Daftar UMKM</h6>
                            <a href="Dashboard.php?umkm_edit_id=new" class="btn btn-primary btn-sm">Tambah UMKM</a>
                        </div>
                        <div class="card-body">
                        <?php
                        $stmt = $pdo->query('SELECT * FROM umkm ORDER BY id DESC');
                        $umkmList = $stmt->fetchAll();
                        if (count($umkmList) === 0) {
                            echo '<p>Belum ada UMKM.</p>';
                        } else {
                            echo '<div class="table-responsive">';
                            echo '<table class="table table-bordered" width="100%" cellspacing="0">';
                            echo '<thead><tr><th>Nama UMKM</th><th>Link Google Maps</th><th>Gambar</th><th>Aksi</th></tr></thead><tbody>';
                            foreach ($umkmList as $umkm) {
                                echo '<tr>';
                                echo '<td>' . htmlspecialchars($umkm['nama_umkm']) . '</td>';
                                echo '<td><a href="' . htmlspecialchars($umkm['link_gmaps']) . '" target="_blank">Lihat</a></td>';
                                if ($umkm['gambar']) {
                                    echo '<td><img src="../assets/img/umkm/' . htmlspecialchars($umkm['gambar']) . '" alt="Gambar" style="max-width:60px; max-height:60px;"></td>';
                                } else {
                                    echo '<td>-</td>';
                                }
                                echo '<td>';
                                echo '<a href="Dashboard.php?umkm_edit_id=' . $umkm['id'] . '" class="btn btn-sm btn-warning mr-1">Edit</a>';
                                echo '<a href="Dashboard.php?umkm_delete_id=' . $umkm['id'] . '" class="btn btn-sm btn-danger" onclick="return confirm(\'Yakin ingin menghapus UMKM ini?\')">Hapus</a>';
                                echo '</td>';
                                echo '</tr>';
                            }
                            echo '</tbody></table></div>';
                        }
                        ?>
                        </div>
                    </div>

                    <?php if ($umkm_message): ?>
                        <div class="message success"><?php echo htmlspecialchars($umkm_message); ?></div>
                    <?php elseif ($umkm_error): ?>
                        <div class="message error"><?php echo htmlspecialchars($umkm_error); ?></div>
                    <?php endif; ?>

                    <?php if ($umkm_data): ?>
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary"><?php echo $umkm_data['id'] ? 'Edit UMKM' : 'Tambah UMKM'; ?></h6>
                            </div>
                            <div class="card-body">
                                <form method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="umkm_edit_id" value="<?php echo $umkm_data['id'] ?? 'new'; ?>">
                                    <div class="mb-3">
                                        <label class="form-label">Nama UMKM</label>
                                        <input type="text" class="form-control" name="nama_umkm" required value="<?php echo htmlspecialchars($umkm_data['nama_umkm'] ?? ''); ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Link Google Maps</label>
                                        <input type="url" class="form-control" name="link_gmaps" required value="<?php echo htmlspecialchars($umkm_data['link_gmaps'] ?? ''); ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Gambar Saat Ini</label><br>
                                        <?php if (!empty($umkm_data['gambar'])): ?>
                                            <img src="../assets/img/umkm/<?php echo htmlspecialchars($umkm_data['gambar']); ?>" width="150" class="mb-2">
                                        <?php else: ?>
                                            Tidak ada gambar.
                                        <?php endif; ?>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Ganti Gambar (Opsional)</label>
                                        <input type="file" class="form-control" name="gambar" accept="image/*">
                                        <small class="text-muted">Biarkan kosong jika tidak ingin mengubah gambar</small>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                    <a href="Dashboard.php" class="btn btn-secondary">Batal</a>
                                </form>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                <!-- /.container-fluid -->
            </div>
            <!-- End of Main Content -->
        </div>
        <!-- End of Content Wrapper -->
    </div>
    <!-- End of Page Wrapper -->

    <!-- SB Admin 2 JS, jQuery, Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.2/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/startbootstrap-sb-admin-2/4.1.4/js/sb-admin-2.min.js"></script>

    <!-- Proses hapus berita -->
    <?php
    if (isset($_GET['delete_id'])) {
        $delete_id = intval($_GET['delete_id']);
        $stmt = $pdo->prepare('SELECT gambar FROM berita WHERE id = ?');
        $stmt->execute([$delete_id]);
        $berita = $stmt->fetch();
        if ($berita && $berita['gambar']) {
            $gambarPath = '../assets/img/berita/' . $berita['gambar'];
            if (file_exists($gambarPath)) {
                unlink($gambarPath);
            }
        }
        $stmt = $pdo->prepare('DELETE FROM berita WHERE id = ?');
        $stmt->execute([$delete_id]);
        header('Location: Dashboard.php?msg=berita_dihapus');
        exit();
    }
    ?>
</body>
</html>
