<?php
require "session.php";
require "../koneksi.php";

// Fungsi generik: hapus data dari tabel manapun
function hapusData($con, $table, $id) {
    $stmt = $con->prepare("DELETE FROM $table WHERE id = ?");
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}

// Fungsi generik: ambil data dengan join, where, dan parameter fleksibel
function getData($con, $sql, $params = [], $types = '') {
    $stmt = $con->prepare($sql);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    return $stmt->get_result();
}

// Handle hapus produk
if (isset($_GET['action']) && $_GET['action'] === 'hapus' && isset($_GET['id'])) {
    $id_produk = (int) $_GET['id'];
    if (hapusData($con, 'produk', $id_produk)) {
        $success_message = "Produk berhasil dihapus.";
    } else {
        $error_message = "Gagal menghapus produk: " . $con->error;
    }
}

// Validasi sorting
$allowedSorts = ['nama', 'harga', 'nama_kategori', 'ketersediaan_stok'];
$allowedDirections = ['asc', 'desc'];
$sortCriteria = in_array($_GET['sort'] ?? '', $allowedSorts) ? $_GET['sort'] : 'nama';
$sortDirection = in_array(strtolower($_GET['direction'] ?? ''), $allowedDirections) ? strtolower($_GET['direction']) : 'asc';

// Ambil data produk
$keyword = $_GET['keyword'] ?? '';
$sql = "SELECT a.*, b.nama AS nama_kategori FROM produk a JOIN kategori b ON a.kategori_id = b.id WHERE a.nama LIKE ? ORDER BY $sortCriteria $sortDirection";
$result = getData($con, $sql, ["%$keyword%"], "s");

$produkArray = $result->fetch_all(MYSQLI_ASSOC);
$jumlahProduk = count($produkArray);
?>

<!-- HTML START -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel | Produk</title>
    <link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../fontawesome/css/fontawesome.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php require "navbar.php"; ?>
<div class="container mt-5">
    <h2>List Produk</h2>
    <p>Jumlah produk: <?= $jumlahProduk ?></p>

    <!-- Pencarian -->
    <form action="produk.php" method="GET" class="my-3">
        <div class="input-group">
            <input type="search" class="form-control" placeholder="Cari produk" name="keyword" value="<?= htmlspecialchars($keyword) ?>">
            <button class="btn btn-outline-secondary" type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
        </div>
    </form>

    <!-- Sort & Tambah -->
    <div class="d-flex justify-content-between my-3">
        <div>
            <select id="sortCriteria" onchange="sortProducts()">
                <?php foreach ($allowedSorts as $sort) : ?>
                    <option value="<?= $sort ?>" <?= $sortCriteria === $sort ? 'selected' : '' ?>><?= ucfirst($sort) ?></option>
                <?php endforeach; ?>
            </select>
            <button onclick="toggleSortDirection()">
                <?= strtoupper($sortDirection) ?>
            </button>
        </div>
        <a href="tambah-produk.php" class="btn btn-success">Tambah Produk</a>
    </div>

    <!-- Pesan -->
    <?php if (isset($success_message)) : ?>
        <div class="alert alert-success"> <?= $success_message ?> </div>
    <?php elseif (isset($error_message)) : ?>
        <div class="alert alert-danger"> <?= $error_message ?> </div>
    <?php endif; ?>

    <!-- Tabel -->
    <table class="table">
        <thead>
        <tr>
            <th>No.</th>
            <th>Nama</th>
            <th>Kategori</th>
            <th>Harga</th>
            <th>Stok</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        <?php if ($jumlahProduk === 0) : ?>
            <tr><td colspan="6" class="text-center">Tidak ada data</td></tr>
        <?php else : $no = 1; foreach ($produkArray as $data) : ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= htmlspecialchars($data['nama']) ?></td>
                <td><?= htmlspecialchars($data['nama_kategori']) ?></td>
                <td>Rp <?= number_format($data['harga'], 0, ',', '.') ?></td>
                <td><?= $data['ketersediaan_stok'] ?></td>
                <td>
                    <a href="produk-detail.php?id=<?= $data['id'] ?>" class="btn btn-info btn-sm">Ubah</a>
                    <a href="produk.php?action=hapus&id=<?= $data['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus?');">Hapus</a>
                </td>
            </tr>
        <?php endforeach; endif; ?>
        </tbody>
    </table>
</div>

<script>
function sortProducts() {
    const criteria = document.getElementById('sortCriteria').value;
    const direction = '<?= $sortDirection ?>';
    window.location.href = `produk.php?sort=${criteria}&direction=${direction}`;
}
function toggleSortDirection() {
    const current = '<?= $sortDirection ?>';
    const newDirection = current === 'asc' ? 'desc' : 'asc';
    const criteria = document.getElementById('sortCriteria').value;
    window.location.href = `produk.php?sort=${criteria}&direction=${newDirection}`;
}
</script>
<script src="../bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../fontawesome/js/all.min.js"></script>
</body>
</html>
