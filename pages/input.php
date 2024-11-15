<?php
session_start();
require_once '../function/koneksi.php';

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

// Jika form dikirim
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_siswa = $_POST['nama_siswa'];
    $nisn = $_POST['nisn'];
    $kelas = $_POST['kelas'];
    $jurusan = $_POST['jurusan'];
    $no_telepon = $_POST['no_telepon'];
    $nama_pembimbing = $_POST['nama_pembimbing'];
    $nama_industri = $_POST['nama_industri'];
    $bidang = $_POST['bidang'];

    // Query untuk update data
    $sql = "UPDATE data_siswa SET nama_siswa=?, kelas=?, jurusan=?, no_telepon=?, nama_pembimbing=?, nama_industri=?, bidang=? WHERE nisn=?";
    $update_stmt = $koneksi->prepare($sql);

    $update_stmt->bind_param("sssssssi", $nama_siswa, $kelas, $jurusan, $no_telepon, $nama_pembimbing, $nama_industri, $bidang, $nisn);

    // Eksekusi query
    if ($update_stmt->execute()) {
        // Jika berhasil, tampilkan alert dan redirect
        echo "<script>
        alert('Mengisi data berhasil!');
        window.location.href = './dashboard.php?token=" . htmlspecialchars($_SESSION['token']) . "';
    </script>";
        exit(); // Hentikan eksekusi setelah alert
    } else {
        echo "Error: " . $update_stmt->error;
    }


    // Tutup statement dan koneksi
    $update_stmt->close();
    $koneksi->close();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../src/img/icon.png" type="image/x-icon">
    <title>JurnalKu</title>
</head>
<style>
    @import url("https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap");

    body {
        height: 100%;
        margin: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        background-color: #eef5fe;
        background-size: cover;
        font-family: "Poppins", sans-serif;
    }

    .container {
        width: 80%;
        max-width: 800px;
        background-color: #fff;
        border-radius: 5px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        padding: 20px;
        margin: 20px;
    }

    .header {
        display: flex;
        justify-content: center;
        align-items: center;
        position: relative;
        border-bottom: 1px solid #ddd;
        padding-bottom: 10px;
        height: 60px;
    }

    .header h2 {
        text-align: center;
        margin: 0;
        font-size: 25px;
    }

    button.back {
        border-style: none;
        border-radius: 5px;
        color: white;
        background-color: #dc3545;
        position: absolute;
        left: 0;
        transform: translateY(-50%);
        padding: 5px 10px;
        font-size: 14px;
        height: auto;
    }

    button.back:hover {
        background-color: #b52a38;
    }

    .details {
        display: flex;
        justify-content: space-between;
        margin: 20px 0;
    }

    .details div {
        width: 80%;
    }

    .details div p {
        margin: 5px 0;
    }

    .details div p span {
        font-weight: bold;
    }

    .section-title {
        text-align: center;
        font-size: 20px;
        margin: 20px 0;
        border-top: 1px solid #ddd;
        padding-top: 10px;
    }

    button.simpan {
        background-color: #007bff;
        color: #fff;
        border: none;
        padding: 10px 20px;
        border-radius: 5px;
        cursor: pointer;
    }

    button.simpan:hover {
        background-color: #0056b3;
    }
</style>

<body>
    <div class="container">
        <div class="header">
            <a href="./user.php?token=<?php echo htmlspecialchars($_SESSION['token']); ?>">
                <button class="back">Back</button>
            </a>
            <h2>Data Siswa</h2>
        </div>
        <form action="" method="POST" enctype="multipart/form-data">
            <div class="details">
                <div>
                    <p><span>NISN</span>: <input type="text" name="nisn" value="<?php echo htmlspecialchars($siswa['nisn']); ?>" readonly></p>
                    <p><span>Nama Lengkap</span>: <input type="text" name="nama_siswa" value="<?php echo htmlspecialchars($siswa['nama_siswa']); ?>"></p>
                    <p><span>Kelas</span>: <input type="text" name="kelas" value="<?php echo htmlspecialchars($siswa['kelas']); ?>"></p>
                    <p><span>Telp</span>: <input type="number" name="no_telepon" value="<?php echo htmlspecialchars($siswa['no_telepon']); ?>"></p>
                    <p><span>Jurusan</span>: <input type="text" name="jurusan" value="<?php echo htmlspecialchars($siswa['jurusan']); ?>"></p>
                </div>
                <div>
                </div>
            </div>
            <div class="section-title">
                Penempatan Prakerin
            </div>
            <div class="details">
                <div>
                    <p><span>Nama Pembimbing</span>: <input type="text" name="nama_pembimbing" value="<?php echo htmlspecialchars($siswa['nama_pembimbing']); ?>"></p>
                    <p><span>Nama Industri</span>: <input type="text" name="nama_industri" value="<?php echo htmlspecialchars($siswa['nama_industri']); ?>"></p>
                    <p><span>Bidang Kerja</span>: <input type="text" name="bidang" value="<?php echo htmlspecialchars($siswa['bidang']); ?>"></p>
                </div>
            </div>
            <button type="submit" class="simpan">Simpan</button>
        </form>
    </div>

</body>

</html>