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
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../src/img/icon.png" type="image/x-icon">
    <title>JurnalKu</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200">
    <link rel="stylesheet" href="../src/css/person.css">
</head>

<body>
    <?php include './sidebar.php' ?>

    <div class="container">
        <div class="header">
            <a href="./input.php?token=<?php echo htmlspecialchars($_SESSION['token']); ?>"><button>Edit</button></a>
            <h2>Data Siswa</h2>
        </div>
        <div class="profile">
            <img src="../src/img/smk5.png" alt="">
        </div>
        <div class="details">
            <div>
                <p><span>NISN</span>: <?php echo htmlspecialchars($siswa['nisn']); ?></p>
                <p><span>Nama Lengkap</span>: <?php echo htmlspecialchars($siswa['nama_siswa']); ?></p>
                <p><span>Kelas</span>: <?php echo htmlspecialchars($siswa['kelas']); ?></p>
                <p><span>Telp</span>: <?php echo htmlspecialchars($siswa['no_telepon']); ?></p>
                <p><span>Jurusan</span>: <?php echo htmlspecialchars($siswa['jurusan']); ?></p>
            </div>
        </div>
        <div class="section-title">
            Penempatan Prakerin
        </div>
        <div class="details">
            <div>
                <p><span>Nama Pembimbing</span>: <?php echo htmlspecialchars($siswa['nama_pembimbing']); ?></p>
                <p><span>Nama Industri</span>: <?php echo htmlspecialchars($siswa['nama_industri']); ?></p>
                <p><span>Bidang Kerja</span>: <?php echo htmlspecialchars($siswa['bidang']); ?></p>
            </div>
        </div>
    </div>
</body>

</html>