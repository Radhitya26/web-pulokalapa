<?php
include 'koneksi.php';

// Ambil data dusun
$dusun_result = mysqli_query($conn, "SELECT * FROM dusun");

// Ambil data penduduk (ambil 1 baris saja)
$penduduk_result = mysqli_query($conn, "SELECT * FROM penduduk LIMIT 1");
$penduduk = mysqli_fetch_assoc($penduduk_result);

// Ambil data sumber daya kelembagaan
$sumberdaya_result = mysqli_query($conn, "SELECT * FROM sumberdaya_kelembagaan");
?>

<!DOCTYPE html>
<html lang="id">
<?php include 'navbar.php'; ?>

<body>
    <section id="Kondisi" class="Kondisi">
        <br>
        <div class="container" data-aos="fade-up">
            <header class="section-header">
                <p>Kondisi Desa</p>
            </header>

            <div class="entry-content">
                <p>
                    Desa Pulokalapa merupakan desa yang memiliki karakteristik wilayah agraris, di mana sebagian besar
                    lahannya dimanfaatkan sebagai areal pertanian sawah dan sebagian lainnya merupakan kawasan
                    pemukiman. Kondisi ini menjadikan sektor pertanian sebagai tulang punggung perekonomian desa.
                </p>
                <p>
                    Mayoritas penduduk Desa Pulokalapa menggantungkan hidupnya dari hasil pertanian padi. Selain itu,
                    terdapat pula mata pencaharian lain yang cukup signifikan di kalangan masyarakat, seperti
                    wiraswasta, pedagang, dan buruh harian lepas, terutama di sektor pertanian. Dalam kehidupan
                    sehari-hari, masyarakat menggunakan bahasa Sunda sebagai alat komunikasi utama.
                </p>
                <p>
                    Secara geografis dan iklim, Desa Pulokalapa memiliki kondisi yang tidak jauh berbeda dengan
                    desa-desa lain di sekitarnya. Suhu rata-rata harian berkisar 32°C pada siang hari dan 27°C pada
                    malam hari. Curah hujan tahunan rata-rata mencapai 2.800 mm/tahun, dengan intensitas tertinggi
                    terjadi pada periode bulan Desember hingga April.
                </p>
                <p>
                    Secara Administratif, Desa Pulokalapa terletak di Lemahabang, Kabupaten Karawang.
                    Adapun batas-batas wilayah Desa Pulokalapa adalah sebagai berikut:
                </p>
                <ul>
                    <li>Sebelah Utara : Desa Sukajaya</li>
                    <li>Sebelah Timur : Desa Kiara & Desa Pulojaya</li>
                    <li>Sebelah Selatan : Desa Tegalwaru</li>
                    <li>Sebelah Barat : Sungai Cilamaya (berbatasan dengan Kab. Subang)</li>
                </ul>

                <h5 class="mt-4">Data Dusun</h5>
                <table class="table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Dusun</th>
                            <th>Jumlah RW</th>
                            <th>Jumlah RT</th>
                        </tr>
                    </thead>
                    <tbody class="table-group-divider">
                        <?php $no = 1; while ($dusun = mysqli_fetch_assoc($dusun_result)): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= htmlspecialchars($dusun['nama_dusun']) ?></td>
                                <td><?= $dusun['jumlah_rw'] ?></td>
                                <td><?= $dusun['jumlah_rt'] ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <section id="Penduduk" class="Penduduk">
        <div class="container" data-aos="fade-up">
            <header class="section-header">
                <p>Penduduk</p>
            </header>

            <table class="table">
                <thead>
                    <tr>
                        <th>Laki-Laki</th>
                        <th>Perempuan</th>
                        <th>Jumlah</th>
                        <th>Jumlah KK</th>
                    </tr>
                </thead>
                <tbody class="table-group-divider">
                    <?php if ($penduduk): ?>
                        <tr>
                            <td><?= $penduduk['laki_laki'] ?></td>
                            <td><?= $penduduk['perempuan'] ?></td>
                            <td><?= $penduduk['jumlah'] ?></td>
                            <td><?= $penduduk['jumlah_kk'] ?></td>
                        </tr>
                    <?php else: ?>
                        <tr><td colspan="4">Data belum tersedia.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>

    <section id="sumberdaya" class="sumberdaya">
        <div class="container" data-aos="fade-up">
            <header class="section-header">
                <p>Sumber Daya Kelembagaan</p>
            </header>

            <table class="table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Jenis Organisasi/Kelembagaan</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody class="table-group-divider">
                    <?php $no = 1; while ($row = mysqli_fetch_assoc($sumberdaya_result)): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= htmlspecialchars($row['nama_lembaga']) ?></td>
                            <td><?= htmlspecialchars($row['keterangan']) ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </section>

    <?php include 'footer2.php'; ?>
</body>
</html>
