<?php
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: index.php?status=hapus");
    exit;
}

include '../config/koneksi.php';


$id = $_GET['id'];

$stmt = mysqli_prepare(
    $conn,
    "DELETE FROM mahasiswa WHERE id=?"
);
mysqli_stmt_bind_param($stmt, "i", $id);

if (mysqli_stmt_execute($stmt)) {
    header("Location: index.php");
    exit;
} else {
    echo "Gagal hapus data";
}
?>
