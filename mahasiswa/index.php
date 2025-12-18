<?php
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: ../auth/login.php");
    exit;
}

include '../config/koneksi.php';

/* Pagination */
$limit = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

/* Search */
$keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';
$search = "%$keyword%";

/* Total data */
$stmtTotal = mysqli_prepare(
    $conn,
    "SELECT COUNT(*) AS total FROM mahasiswa
     WHERE nim LIKE ? OR nama LIKE ? OR jurusan LIKE ?"
);
mysqli_stmt_bind_param($stmtTotal, "sss", $search, $search, $search);
mysqli_stmt_execute($stmtTotal);
$totalResult = mysqli_stmt_get_result($stmtTotal);
$totalRow = mysqli_fetch_assoc($totalResult);
$totalData = $totalRow['total'];
$totalPage = ceil($totalData / $limit);

/* Data */
$stmt = mysqli_prepare(
    $conn,
    "SELECT * FROM mahasiswa
     WHERE nim LIKE ? OR nama LIKE ? OR jurusan LIKE ?
     ORDER BY id DESC
     LIMIT ?, ?"
);
mysqli_stmt_bind_param($stmt, "sssii", $search, $search, $search, $start, $limit);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Data Mahasiswa</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- CSS buatan sendiri -->
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body class="bg-light">

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container">
    <a class="navbar-brand" href="index.php">Sistem Mahasiswa</a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto">
        <li class="nav-item">
          <a class="nav-link active" href="index.php">Data Mahasiswa</a>
        </li>
      </ul>
      <a href="../auth/logout.php" class="btn btn-danger btn-sm">Logout</a>
    </div>
  </div>
</nav>

<?php if (isset($_GET['status'])): ?>
  <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
    <?php
      if ($_GET['status'] == 'tambah') echo "Data berhasil ditambahkan!";
      if ($_GET['status'] == 'edit') echo "Data berhasil diperbarui!";
      if ($_GET['status'] == 'hapus') echo "Data berhasil dihapus!";
    ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
<?php endif; ?>

<div class="container mt-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>Data Mahasiswa</h3>
    </div>

    <!-- Tombol tambah -->
    <div class="mb-3">
        <a href="tambah.php" class="btn btn-primary">+ Tambah Data</a>
    </div>

    <!-- Search -->
    <form method="get" class="row g-2 mb-3">
        <div class="col-md-4">
            <input type="hidden" name="page" value="1">
            <input type="text"
                   name="keyword"
                   class="form-control"
                   placeholder="Cari NIM / Nama / Jurusan"
                   value="<?= htmlspecialchars($keyword); ?>">
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-secondary">Search</button>
        </div>
    </form>

    <!-- Tabel -->
    <div class="d-none d-md-block">
        <?php if ($totalData == 0): ?>
    <div class="alert alert-warning">
        Data tidak ditemukan.
    </div>
        <?php endif; ?>

    <table class="table table-bordered table-striped table-hover">
        <thead class="table-dark">
            <tr>
                <th>No</th>
                <th>NIM</th>
                <th>Nama</th>
                <th>Jurusan</th>
                <th width="160">Aksi</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $no = $start + 1;
        while ($row = mysqli_fetch_assoc($result)) :
        ?>
            <tr>
                <td><?= $no++; ?></td>
                <td><?= htmlspecialchars($row['nim']); ?></td>
                <td><?= htmlspecialchars($row['nama']); ?></td>
                <td><?= htmlspecialchars($row['jurusan']); ?></td>
                <td>
                    <a href="edit.php?id=<?= $row['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                    <a href="hapus.php?id=<?= $row['id']; ?>"
                       onclick="return confirm('Yakin hapus data?')"
                       class="btn btn-danger btn-sm">
                       Hapus
                    </a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

    <div class="d-block d-md-none">
<?php
mysqli_data_seek($result, 0); // reset result
while ($row = mysqli_fetch_assoc($result)) :
?>
  <div class="card mb-3">
    <div class="card-body">
      <h5 class="card-title"><?= htmlspecialchars($row['nama']); ?></h5>
      <p class="card-text">
        <strong>NIM:</strong> <?= htmlspecialchars($row['nim']); ?><br>
        <strong>Jurusan:</strong> <?= htmlspecialchars($row['jurusan']); ?>
      </p>
      <a href="edit.php?id=<?= $row['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
      <a href="hapus.php?id=<?= $row['id']; ?>"
         onclick="return confirm('Yakin hapus data?')"
         class="btn btn-danger btn-sm">Hapus</a>
    </div>
  </div>
<?php endwhile; ?>
</div>

    <!-- Pagination -->
    <?php if ($totalData > 0): ?>
    <nav>
        <ul class="pagination">
            <?php if ($page > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="?page=<?= $page - 1 ?>&keyword=<?= $keyword ?>">Previous</a>
                </li>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $totalPage; $i++): ?>
                <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                    <a class="page-link" href="?page=<?= $i ?>&keyword=<?= $keyword ?>">
                        <?= $i ?>
                    </a>
                </li>
            <?php endfor; ?>

            <?php if ($page < $totalPage): ?>
                <li class="page-item">
                    <a class="page-link" href="?page=<?= $page + 1 ?>&keyword=<?= $keyword ?>">Next</a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>
    <?php endif;?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
