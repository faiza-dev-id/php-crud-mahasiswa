<?php
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: index.php?status=edit");
    exit;
}

error_reporting(E_ALL);
ini_set('display_errors', 1);
include '../config/koneksi.php';

if (!isset($_GET['id'])) {
    die("ID tidak ada");
}

$id = (int) $_GET['id'];

/* AMBIL DATA */
$stmt = mysqli_prepare(
    $conn,
    "SELECT * FROM mahasiswa WHERE id=?"
);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);

if (!$row) {
    die("Data mahasiswa tidak ditemukan");
}

/* UPDATE DATA */
if (isset($_POST['submit'])) {
    $nim = $_POST['nim'];
    $nama = $_POST['nama'];
    $jurusan = $_POST['jurusan'];

    $stmt = mysqli_prepare(
        $conn,
        "UPDATE mahasiswa SET nim=?, nama=?, jurusan=? WHERE id=?"
    );
    mysqli_stmt_bind_param($stmt, "sssi", $nim, $nama, $jurusan, $id);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: index.php");
        exit;
    } else {
        echo "Gagal update data";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Data</title>
</head>
<body>

<link rel="stylesheet" href="../assets/edit.css">

<div class="edit-page">
    <div class="edit-card">
        <h2>Edit Data Mahasiswa</h2>


<form method="post">
    <label>NIM</label><br>
    <input type="text" name="nim" value="<?= htmlspecialchars($row['nim']); ?>" required><br><br>

    <label>Nama</label><br>
    <input type="text" name="nama" value="<?= htmlspecialchars($row['nama']); ?>" required><br><br>

    <label>Jurusan</label><br>
    <input type="text" name="jurusan" value="<?= htmlspecialchars($row['jurusan']); ?>" required><br><br>

    <button type="submit" name="submit">Update</button>
</form>

<br>
<a href="index.php">Kembali</a>

</body>
</html>
