<?php
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: index.php?status=tambah");
    exit;
}

error_reporting(E_ALL);
ini_set('display_errors', 1);
include '../config/koneksi.php';

if (isset($_POST['submit'])) {
    $nim = $_POST['nim'];
    $nama = $_POST['nama'];
    $jurusan = $_POST['jurusan'];

    $stmt = mysqli_prepare(
        $conn,
        "INSERT INTO mahasiswa (nim, nama, jurusan) VALUES (?, ?, ?)"
    );
    mysqli_stmt_bind_param($stmt, "sss", $nim, $nama, $jurusan);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: index.php");
        exit;
    } else {
        echo "Gagal tambah data";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tambah Data</title>
</head>
<body>

<link rel="stylesheet" href="../assets/edit.css">

<div class="edit-page">
    <div class="edit-card">
        <h2>Tambah Data Mahasiswa</h2>
        
<form method="post">
    <label>NIM</label><br>
    <input type="text" name="nim" required><br><br>

    <label>Nama</label><br>
    <input type="text" name="nama" required><br><br>

    <label>Jurusan</label><br>
    <input type="text" name="jurusan" required><br><br>

    <button type="submit" name="submit">Simpan</button>
</form>

<br>
<a href="index.php">Kembali</a>

</body>
</html>
