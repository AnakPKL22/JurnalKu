<?php
session_start();
require_once '../function/koneksi.php';

// Cek apakah token valid
if (!isset($_SESSION['token']) || $_SESSION['token'] !== $_GET['token']) {
    header('Location: ../index.php');
    exit;
}

// Ambil bagian dan nisn dari sesi
$bagian = $_SESSION['bagian'];
$nisn = $_SESSION['nisn'];

// Hanya pengguna dengan bagian 'siswa' yang diizinkan
if ($bagian !== 'siswa') {
    header('Location: ../index.php?token=' . urlencode($_SESSION['token']) . '&error=not_allowed');
    exit;
}

// Ambil data siswa dari database berdasarkan NISN
$query = $koneksi->prepare("SELECT * FROM data_siswa WHERE nisn = ?");
$query->bind_param("s", $nisn);
$query->execute();
$result = $query->get_result();

if ($result->num_rows > 0) {
    $siswa = $result->fetch_assoc(); // Ambil data siswa
} else {
    echo "Data siswa tidak ditemukan.";
    exit;
}

// Pagination logic
$limit = 8; // Maximum data per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Current page
$offset = ($page - 1) * $limit; // Offset for SQL query

// Hitung jumlah total data untuk pagination
$total_query = $koneksi->prepare("SELECT COUNT(*) AS total FROM jurnal WHERE nisn = ?");
$total_query->bind_param("s", $nisn);
$total_query->execute();
$total_result = $total_query->get_result();
$total_data = $total_result->fetch_assoc()['total'];

// Hitung jumlah total halaman
$total_pages = ceil($total_data / $limit);

// Ambil data jurnal dari database dengan limit dan offset
$sql = $koneksi->prepare("SELECT * FROM jurnal WHERE nisn = ? LIMIT ? OFFSET ?");
$sql->bind_param("sii", $nisn, $limit, $offset);
$sql->execute();
$jurnal_result = $sql->get_result();

// Proses update kegiatan jika ada request POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id']) && isset($_POST['kegiatan'])) {
    $id = $_POST['id'];
    $kegiatan = $_POST['kegiatan'];

    // Update kegiatan di database
    $stmt = $koneksi->prepare("UPDATE jurnal SET kegiatan = ? WHERE id = ?");
    $stmt->bind_param("si", $kegiatan, $id);

    if ($stmt->execute()) {
        echo "Kegiatan berhasil diperbarui!";
    } else {
        echo "Gagal memperbarui kegiatan.";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../src/img/icon.png" type="image/x-icon">
    <title>JurnalKu</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200">
    <link rel="stylesheet" href="../src/css/log.css">
</head>

<body>
    <?php include './sidebar.php' ?>

    <div class="container">
        <h2>Log Aktivitas PKL</h2>
        <div class="header">
            <label for="tanggal-awal">Pilih Tanggal Awal:</label>
            <input type="date" name="tanggal-awal" id="tanggal-awal" required>

            <label for="tanggal-akhir">Pilih Tanggal Akhir:</label>
            <input type="date" name="tanggal-akhir" id="tanggal-akhir" required>

            <button id="download-pdf">
                <i class="fa-solid fa-print"></i> Export-PDF
            </button>
        </div>
        <div class="details">
            <table>
                <tr>
                    <th>No.</th>
                    <th>Tanggal</th>
                    <th>Clock In</th>
                    <th>Clock Out</th>
                    <th>Kegiatan</th>
                    <th>Aksi</th>
                </tr>
                <?php
                $no = $offset + 1;
                while ($tampil = $jurnal_result->fetch_assoc()) {
                    echo "
                    <tr>
                        <td>$no</td>
                        <td>{$tampil['tanggal']}</td>
                        <td>{$tampil['clock_in']}</td>
                        <td>{$tampil['clock_out']}</td>
                        <td contenteditable='true' class='editable' data-id='{$tampil['id']}'>{$tampil['kegiatan']}</td>
                        <td><button class='save-btn' data-id='{$tampil['id']}' style='background: #28a745; color:white; padding:3%'>Simpan</button></td>
                    </tr>
                    ";
                    $no++;
                }
                ?>
            </table>
        </div>

        <div class="pagination">
            <!-- Tombol Prev -->
            <?php if ($page > 1): ?>
                <a href="?page=<?= $page - 1 ?>&token=<?= $_SESSION['token'] ?>" class="prev">Prev</a>
            <?php else: ?>
                <a class="prev disabled">Prev</a> <!-- Disabled button -->
            <?php endif; ?>

            <!-- Tampilkan nomor halaman -->
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?page=<?= $i ?>&token=<?= $_SESSION['token'] ?>" class="<?= $i == $page ? 'active' : '' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>

            <!-- Tombol Next -->
            <?php if ($page < $total_pages): ?>
                <a href="?page=<?= $page + 1 ?>&token=<?= $_SESSION['token'] ?>" class="next">Next</a>
            <?php else: ?>
                <a class="next disabled">Next</a> <!-- Disabled button -->
            <?php endif; ?>
        </div>

    </div>

    <script>
        // Script untuk menyimpan perubahan kegiatan
        document.querySelectorAll('.save-btn').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const kegiatanElement = document.querySelector(`.editable[data-id='${id}']`);
                const kegiatan = kegiatanElement.innerText;

                // Kirim perubahan via AJAX ke server untuk disimpan di database
                const xhr = new XMLHttpRequest();
                xhr.open('POST', '', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        alert('Kegiatan berhasil diperbarui!');
                    } else {
                        alert('Gagal memperbarui kegiatan.');
                    }
                };
                xhr.send(`id=${id}&kegiatan=${encodeURIComponent(kegiatan)}`);
            });
        });


        document.getElementById('download-pdf').addEventListener('click', function(e) {
            e.preventDefault();
            exportpdf();
        });

        function exportpdf() {
            // Ambil data input dari tanggal awal dan tanggal akhir
            const tanggalAwal = document.getElementById('tanggal-awal').value;
            const tanggalAkhir = document.getElementById('tanggal-akhir').value;

            let url = `../proses/pdf1.php?tanggal-awal=${encodeURIComponent(tanggalAwal)}&tanggal-akhir=${encodeURIComponent(tanggalAkhir)}&token=${encodeURIComponent('<?php echo $_SESSION['token']; ?>')}`;
            window.location.href = url; // Redirect untuk download PDF
        }
    </script>

</body>

</html>