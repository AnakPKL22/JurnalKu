<?php
session_start(); // Mulai session

include "../function/koneksi.php";

// Jika form dikirim
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari form
    $nisn = $_POST['nisn'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $nama_siswa = $_POST['nama_siswa'];

    // Validasi input form (pastikan tidak ada yang kosong)
    if (empty($nisn) || empty($username) || empty($password) || empty($nama_siswa)) {
        echo "Error: Semua kolom harus diisi.";
        exit; // Berhenti jika ada input yang kosong
    }

    // Hash password menggunakan MD5
    $hashed_password = md5($password);

    // Cek apakah NISN sudah ada di tabel data_siswa
    $check_nisn = $koneksi->prepare("SELECT nisn FROM data_siswa WHERE nisn = ?");
    $check_nisn->bind_param("s", $nisn);
    $check_nisn->execute();
    $check_nisn->store_result();

    // Jika NISN belum ada di tabel data_siswa, tambahkan
    if ($check_nisn->num_rows == 0) {
        // Tambahkan data ke tabel data_siswa dengan nama siswa dari form
        $insert_siswa = $koneksi->prepare("INSERT INTO data_siswa (nisn, nama_siswa) VALUES (?, ?)");
        $insert_siswa->bind_param("ss", $nisn, $nama_siswa);
        $insert_siswa->execute();
        $insert_siswa->close();
    }

    // Lanjutkan dengan memasukkan data ke tabel akun
    $sql = "INSERT INTO akun (username, password, bagian, nisn) VALUES (?, ?, 'siswa', ?)";
    $stmt = $koneksi->prepare($sql);
    $stmt->bind_param("sss", $username, $hashed_password, $nisn);

    if ($stmt->execute()) {
        // Jika berhasil, tampilkan alert dan redirect ke index.php
        echo "<script>
                alert('Registrasi berhasil!');
                window.location.href = '../index.php'; // Redirect ke index.php
              </script>";
        exit(); // Hentikan eksekusi setelah alert
    } else {
        echo "Error: " . $stmt->error;
    }

    // Tutup koneksi
    $stmt->close();
    $check_nisn->close();
    $koneksi->close();
}
?>

<html>

<head>
    <title>JurnalKu</title>
    <link rel="shortcut icon" href="../src/img/icon.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .container {
            margin-top: 50px;
        }

        .form-container {
            border: 1px solid #ccc;
            padding: 30px;
            border-radius: 5px;
        }

        .form-title {
            font-size: 2rem;
            font-weight: bold;
            color: #003366;
            margin-bottom: 20px;
        }

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="form-container">
                    <div class="form-title">Register</div>
                    <form method="post">
                        <div class="mb-3">
                            <label for="nisn" class="form-label">NISN</label>
                            <input type="number" class="form-control" id="nisn" name="nisn" placeholder="NISN">
                        </div>
                        <div class="mb-3">
                            <label for="nama_siswa" class="form-label">Nama Lengkap Siswa</label>
                            <input type="text" class="form-control" id="nama_siswa" name="nama_siswa" placeholder="Nama Lengkap Siswa">
                        </div>
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username" placeholder="Username">
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Password">
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Register</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

</html>