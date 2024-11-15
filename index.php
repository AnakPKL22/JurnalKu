<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>JurnalKu</title>
    <link rel="shortcut icon" href="./src/img/icon.png" type="image/x-icon">
    <link rel="stylesheet" href="./src/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
        integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>
    <div class="header">
        <p class="judul">Login</p>
        <p class="subjudul">Laporan PKL (Praktik Kerja Lapangan)</p>
    </div>

    <div class="container">
        <form action="./function/cek_login.php" method="post">
            <div class="input-group">
                <i class="fa-solid fa-user"></i>
                <input type="text" name="username" id="username" placeholder="Username" required>
            </div>
            <div class="input-group">
                <i class="fa-solid fa-lock"></i>
                <input type="password" name="password" id="password" placeholder="Password" required maxlength="20">
            </div>

            <!-- Cek apakah ada pesan error -->
            <?php
            if (isset($_GET['pesan'])) {
                if ($_GET['pesan'] == "gagal") {
                    echo "<div style='color: red; margin-bottom: 10px;'>Username atau password salah!</div>";
                }
            }
            ?>

            <button type="submit" name="login">Login</button>
            <p>Don't Have An Account? <a href="./pages/register.php">Register Now</a></p>
        </form>
    </div>
</body>

</html>