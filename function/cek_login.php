<?php
session_start(); // Memulai session

include './koneksi.php'; // Menyertakan file koneksi

$username = $_POST['username'];
$password = md5($_POST['password']); // Menggunakan MD5 (sebaiknya gunakan hashing yang lebih aman seperti password_hash)

// Menjalankan query untuk memeriksa username dan password
$sql = mysqli_query($koneksi, "SELECT * FROM akun WHERE username='$username' AND password='$password'");
$user = mysqli_fetch_assoc($sql); // Mengambil hasil query sebagai array asosiatif

// Cek apakah user ditemukan
if ($user) {
    // Membuat token unik untuk session dan cookie
    $token = md5(uniqid());

    // Set session
    $_SESSION['token'] = $token;
    $_SESSION['id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['bagian'] = $user['bagian'];
    $_SESSION['nisn'] = $user['nisn']; // Simpan NISN ke dalam sesi

    // Set cookie token untuk validasi di halaman berikutnya
    setcookie('token', $token, time() + 86400, "/"); // Cookie valid selama 1 hari

    // Redirect berdasarkan bagian user (Admin atau lainnya)
    if ($user['bagian'] == 'admin') {
        header('Location: ../pages/admin.php?token=' .  $token);
    } else {
        header('Location: ../pages/dashboard.php?token=' .  $token);
    }
    exit;
} else {
    // Setel pesan error ke session
    $_SESSION['login_error'] = "Username atau password salah.";

    // Redirect kembali ke halaman login dengan parameter pesan error
    header("location:../index.php?pesan=gagal");
    exit;
}
?>
