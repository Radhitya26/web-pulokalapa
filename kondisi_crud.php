<?php
include 'koneksi.php';

// Tambah
if (isset($_POST['tambah'])) {
    $nama = $_POST['nama_dusun'];
    $rw = $_POST['jumlah_rw'];
    $rt = $_POST['jumlah_rt'];
    mysqli_query($conn, "INSERT INTO dusun (nama_dusun, jumlah_rw, jumlah_rt) VALUES ('$nama', '$rw', '$rt')");
    header('Location: kondisi_crud.php');
    exit;
}

// Update
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $nama = $_POST['nama_dusun'];
    $rw = $_POST['jumlah_rw'];
    $rt = $_POST['jumlah_rt'];
    mysqli_query($conn, "UPDATE dusun SET nama_dusun='$nama', jumlah_rw=$rw, jumlah_rt=$rt WHERE id=$id");
    header('Location: kondisi_crud.php');
    exit;
}

// Hapus
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM dusun WHERE id = $id");
    header('Location: kondisi_crud.php');
    exit;
}

// Ambil data semua dusun
$dusun = mysqli_query($conn, "SELECT * FROM dusun");

// Jika sedang dalam mode edit
$edit_mode = false;
if (isset($_GET['edit'])) {
    $edit_mode = true;
    $edit_id = $_GET['edit'];
    $edit_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM dusun WHERE id = $edit_id"));
}

$data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM penduduk LIMIT 1"));

if (isset($_POST['simpan'])) {
    $id = $_POST['id'];
    $laki = $_POST['laki_laki'];
    $perempuan = $_POST['perempuan'];
    $kk = $_POST['jumlah_kk'];
    $jumlah = $laki + $perempuan;

    if ($dusun) {
        mysqli_query($conn, "UPDATE penduduk SET laki_laki=$laki, perempuan=$perempuan, jumlah=$jumlah, jumlah_kk=$kk WHERE id=$id");
    } else {
        mysqli_query($conn, "INSERT INTO penduduk (laki_laki, perempuan, jumlah, jumlah_kk) VALUES ($laki, $perempuan, $jumlah, $kk)");
    }

    header('Location: kondisi_crud.php');
}



// Tambah data
if (isset($_POST['tambah'])) {
    $nama = $_POST['nama_lembaga'];
    $ket = $_POST['keterangan'];
    mysqli_query($conn, "INSERT INTO sumberdaya_kelembagaan (nama_lembaga, keterangan) VALUES ('$nama', '$ket')");
    header('Location: sumberdaya_crud.php');
    exit;
}

// Update dari dropdown
if (isset($_POST['update_keterangan'])) {
    $id = $_POST['id'];
    $keterangan = $_POST['keterangan'];
    mysqli_query($conn, "UPDATE sumberdaya_kelembagaan SET keterangan='$keterangan' WHERE id=$id");
    exit;
}

// Hapus data
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM sumberdaya_kelembagaan WHERE id = $id");
    header('Location: sumberdaya_crud.php');
    exit;
}

$lembaga = mysqli_query($conn, "SELECT * FROM sumberdaya_kelembagaan");
?>

<!DOCTYPE html>
<html lang="id">
<?php include 'navbar.php'; ?>

<body>
    <!-- tabel dusun start-->
    <section name="dusun">
        <div class="container mt-5">
            <h3 class="mb-4"><?= $edit_mode ? 'Edit Dusun' : 'Tambah Dusun' ?></h3>

            <form method="post" class="row g-3 mb-4">
                <?php if ($edit_mode): ?>
                    <input type="hidden" name="id" value="<?= $edit_data['id'] ?>">
                <?php endif; ?>
                <div class="col-md-4">
                    <input type="text" name="nama_dusun" class="form-control" placeholder="Nama Dusun"
                        value="<?= $edit_mode ? htmlspecialchars($edit_data['nama_dusun']) : '' ?>" required>
                </div>
                <div class="col-md-3">
                    <input type="number" name="jumlah_rw" class="form-control" placeholder="Jumlah RW"
                        value="<?= $edit_mode ? $edit_data['jumlah_rw'] : '' ?>" required>
                </div>
                <div class="col-md-3">
                    <input type="number" name="jumlah_rt" class="form-control" placeholder="Jumlah RT"
                        value="<?= $edit_mode ? $edit_data['jumlah_rt'] : '' ?>" required>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-<?= $edit_mode ? 'warning' : 'primary' ?> w-100"
                        name="<?= $edit_mode ? 'update' : 'tambah' ?>">
                        <?= $edit_mode ? 'Update' : 'Tambah' ?>
                    </button>
                </div>
            </form>

            <hr>
            <h5>Data Dusun</h5>
            <table class="table table-bordered mt-3">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Nama Dusun</th>
                        <th>Jumlah RW</th>
                        <th>Jumlah RT</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1;
                    while ($row = mysqli_fetch_assoc($dusun)): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= htmlspecialchars($row['nama_dusun']) ?></td>
                            <td><?= $row['jumlah_rw'] ?></td>
                            <td><?= $row['jumlah_rt'] ?></td>
                            <td>
                                <a href="?edit=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                                <a href="?hapus=<?= $row['id'] ?>" class="btn btn-sm btn-danger"
                                    onclick="return confirm('Hapus data ini?')">Hapus</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </section>
    <!-- tabel dusun end -->

    <!-- tabel penduduk start -->
    <section name="penduduk">
        <div class="container mt-5">
            <h3 class="mb-4">Kelola Data Penduduk</h3>
            <form method="post" class="row g-3">
                <input type="hidden" name="id" value="<?= $data['id'] ?? '' ?>">
                <div class="col-md-3">
                    <label class="form-label">Laki-laki</label>
                    <input type="number" name="laki_laki" class="form-control" value="<?= $data['laki_laki'] ?? '' ?>"
                        required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Perempuan</label>
                    <input type="number" name="perempuan" class="form-control" value="<?= $data['perempuan'] ?? '' ?>"
                        required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Jumlah KK</label>
                    <input type="number" name="jumlah_kk" class="form-control" value="<?= $data['jumlah'] ?? '' ?>"
                        required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Jumlah KK</label>
                    <input type="number" name="jumlah_kk" class="form-control" value="<?= $data['jumlah_kk'] ?? '' ?>"
                        required>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button class="btn btn-primary w-100" name="simpan">Simpan</button>
                </div>
            </form>
        </div>
    </section>
    <!-- tabel penduduk start -->

    <!-- tabel sumber daya kelembagaan start -->
    <section name="sumberdaya">
        <div class="container mt-5">
            <h3 class="mb-4">Kelola Sumber Daya Kelembagaan</h3>

            <form method="post" class="row g-3 mb-4">
                <div class="col-md-6">
                    <input type="text" name="nama_lembaga" class="form-control" placeholder="Nama Lembaga" required>
                </div>
                <div class="col-md-4">
                    <select name="keterangan" class="form-select" required>
                        <option value="Aktif">Aktif</option>
                        <option value="Tidak Aktif">Tidak Aktif</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-primary w-100" name="tambah">Tambah</button>
                </div>
            </form>

            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Nama Lembaga</th>
                        <th>Keterangan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1;
                    while ($row = mysqli_fetch_assoc($lembaga)): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= htmlspecialchars($row['nama_lembaga']) ?></td>
                            <td>
                                <select class="form-select form-select-sm update-keterangan" data-id="<?= $row['id'] ?>">
                                    <option value="Aktif" <?= $row['keterangan'] == 'Aktif' ? 'selected' : '' ?>>Aktif</option>
                                    <option value="Tidak Aktif" <?= $row['keterangan'] == 'Tidak Aktif' ? 'selected' : '' ?>>
                                        Tidak
                                        Aktif</option>
                                </select>
                            </td>
                            <td>
                                <a href="?hapus=<?= $row['id'] ?>" class="btn btn-sm btn-danger"
                                    onclick="return confirm('Yakin hapus?')">Hapus</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </section>

    <script>
        document.querySelectorAll('.update-keterangan').forEach(select => {
            select.addEventListener('change', function () {
                const id = this.dataset.id;
                const value = this.value;

                fetch('sumberdaya_crud.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `update_keterangan=1&id=${id}&keterangan=${value}`
                }).then(res => {
                    if (res.ok) {
                        console.log(`Keterangan updated for ID ${id} to ${value}`);
                    }
                });
            });
        });
    </script>
    <!-- tabel sumber daya kelembagaan end -->

    <?php include 'footer2.php'; ?>
</body>

</html>