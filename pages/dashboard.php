<?php
session_start();
require_once '../function/koneksi.php';

// Atur zona waktu ke Jakarta
date_default_timezone_set('Asia/Jakarta');

// Cek apakah pengguna sudah login
if (!isset($_SESSION['nisn'])) {
    header('Location: ../index.php');
    exit;
}

// Ambil NISN dari sesi
$nisn = $_SESSION['nisn'];
$kegiatan = ''; // Inisialisasi variabel kegiatan

// Proses saat tombol diklik
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tanggal = date('Y-m-d'); // Ambil tanggal hari ini
    $kegiatan = $_POST['kegiatan'] ?? ''; // Ambil kegiatan dari form

    // Cek entri untuk Clock In
    if (isset($_POST['clock_in'])) {
        // Cek apakah sudah ada Clock In di tanggal yang sama
        $checkQuery = $koneksi->prepare("SELECT * FROM jurnal WHERE nisn = ? AND tanggal = ? AND clock_in IS NOT NULL");
        $checkQuery->bind_param("ss", $nisn, $tanggal);
        $checkQuery->execute();
        $checkResult = $checkQuery->get_result();

        if ($checkResult->num_rows > 0) {
            echo "<script>alert('Anda sudah melakukan Clock In pada tanggal ini.');</script>";
        } else {
            $clock_in = date('H:i'); // Ambil waktu saat ini untuk clock in
            // Simpan ke tabel jurnal
            $query = $koneksi->prepare("INSERT INTO jurnal (nisn, tanggal, clock_in, kegiatan) VALUES (?, ?, ?, ?)");
            $query->bind_param("ssss", $nisn, $tanggal, $clock_in, $kegiatan);

            if ($query->execute()) {
                echo "<script>alert('Clock In berhasil disimpan.');</script>";
            } else {
                echo "<script>alert('Gagal menyimpan Clock In: " . $query->error . "');</script>";
            }
        }
        $checkQuery->close();
    }

    // Cek entri untuk Clock Out
    if (isset($_POST['clock_out'])) {
        // Cek apakah sudah ada Clock In di tanggal yang sama dan belum ada Clock Out
        $checkOutQuery = $koneksi->prepare("SELECT * FROM jurnal WHERE nisn = ? AND tanggal = ? AND clock_out IS NULL");
        $checkOutQuery->bind_param("ss", $nisn, $tanggal);
        $checkOutQuery->execute();
        $checkOutResult = $checkOutQuery->get_result();

        if ($checkOutResult->num_rows === 0) {
            echo "<script>alert('Anda belum melakukan Clock In pada tanggal ini.');</script>";
        } else {
            $clock_out = date('H:i'); // Ambil waktu saat ini untuk clock out
            // Update tabel jurnal
            $updateQuery = $koneksi->prepare("UPDATE jurnal SET clock_out = ? WHERE nisn = ? AND tanggal = ? AND clock_out IS NULL");
            $updateQuery->bind_param("sss", $clock_out, $nisn, $tanggal);

            if ($updateQuery->execute()) {
                echo "<script>alert('Clock Out berhasil disimpan.');</script>";
            } else {
                echo "<script>alert('Gagal menyimpan Clock Out: " . $updateQuery->error . "');</script>";
            }
            $updateQuery->close();
        }
        $checkOutQuery->close();
    }
}

// Ambil data kegiatan dari database jika sudah ada
$tanggalSekarang = date('Y-m-d'); // Simpan tanggal sekarang dalam variabel
$queryKegiatan = $koneksi->prepare("SELECT kegiatan FROM jurnal WHERE nisn = ? AND tanggal = ?");
$queryKegiatan->bind_param("ss", $nisn, $tanggalSekarang); // Gunakan variabel tanggalSekarang
$queryKegiatan->execute();
$resultKegiatan = $queryKegiatan->get_result();

if ($resultKegiatan->num_rows > 0) {
    $rowKegiatan = $resultKegiatan->fetch_assoc();
    $kegiatan = $rowKegiatan['kegiatan']; // Mengambil nilai kegiatan
}

$queryKegiatan->close();
$koneksi->close(); // Tutup koneksi setelah semua query selesai
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <script src="../src/js/date.js"></script>
    <link rel="stylesheet" href="../src/css/dashboard.css">
    <link rel="shortcut icon" href="../src/img/icon.png" type="image/x-icon">
</head>

<body>
    <?php include './sidebar.php' ?>

    <div class="date">
        <div class="header">
            <span id="hour"></span>:<span id="minute"></span> <span id="ampm"></span>
        </div>
        <div class="sub">
            <span id="weekday"></span>,
            <span id="day"></span> <span id="month"></span>, <span id="year"></span>
        </div>
    </div>

    <div class="container">
        <div class="header">
            <p>Normal</p>
            <p class="jam">08:00 AM - 05:00 PM</p>
        </div>
        <hr style="width: 90%; margin: 10px auto;">

        <div class="details">
            <form method="POST" action="">
                <div class="button-container">
                    <button class="kiri" type="submit" name="clock_in">Clock In</button>
                    <button class="kanan" type="submit" name="clock_out">Clock Out</button>
                </div>

                <label for="kegiatan">Kegiatan:</label>
                <input type="text" name="kegiatan" id="kegiatan" value="<?php echo htmlspecialchars($kegiatan); ?>" <?php echo $kegiatan ? 'readonly' : ''; ?> required>
            </form>
        </div>
    </div>
</body>

</html>