<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../src/img/icon.png" type="image/x-icon">
    <title>JurnalKu</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200">
    <link rel="stylesheet" href="../src/css/sidebar.css">
</head>

<body>
    <aside class="sidebar">
        <div class="logo">
            <img src="../src/img/logo.png" alt="logo">
            <h2>JurnalKu</h2>
        </div>
        <ul class="links">
            <h4>Main Menu</h4>
            <li>
                <span class="material-symbols-outlined">dashboard</span>
                <a href="./dashboard.php?token=<?php echo htmlspecialchars($_SESSION['token']); ?>">Dashboard</a>
            </li>
            <li>
                <span class="material-symbols-outlined">table_view</span>
                <a href="./log.php?token=<?php echo htmlspecialchars($_SESSION['token']); ?>">Log</a>
            </li>
            <li>
                <span class="material-symbols-outlined">flag</span>
                <a href="#">Reports</a>
            </li>
            <li>
                <span class="material-symbols-outlined">person</span>
                <a href="./user.php?token=<?php echo htmlspecialchars($_SESSION['token']); ?>">User</a>
            </li>
            <li class="logout-link">
                <span class="material-symbols-outlined">logout</span>
                <a href="../function/logout.php">Logout</a>
            </li>
        </ul>
    </aside>
</body>

</html>