<?php

$host = "localhost";
$user = "root";       

$pass = "";           

$db   = "oemah_keboen";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Koneksi Database Gagal: " . $conn->connect_error);
}

date_default_timezone_set('Asia/Makassar');
?>