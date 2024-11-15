<?php
include './function/koneksi_login.php';
session_start();
session_destroy();
header("location:../index.php");
?>