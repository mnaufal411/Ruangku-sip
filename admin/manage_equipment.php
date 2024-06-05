<?php
include("../includes/config.php");
include("../includes/functions.php");

belumLogin();
if (!Admin()) {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['create'])) {
        $nama = $_POST['name'];
        $deskripsi = $_POST['description'];
        $status = $_POST['status'];
        $stmt = $db->prepare("INSERT INTO alat (nama, deskripsi, status) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $nama, $deskripsi, $status);
        $stmt->execute();
        $stmt->close();
    } elseif (isset($_POST['update'])) {
        $id_alat = $_POST['id_alat'];
        $nama = $_POST['name'];
        $deskripsi = $_POST['description'];
        $status = $_POST['status'];
        $stmt = $db->prepare("UPDATE alat SET nama = ?, deskripsi = ?, status = ? WHERE id_alat = ?");
        $stmt->bind_param("sssi", $nama, $deskripsi, $status, $id_alat);
        $stmt->execute();
        $stmt->close();
    } elseif (isset($_POST['delete'])) {
        $id_alat = $_POST['id_alat'];
        $stmt = $db->prepare("DELETE FROM alat WHERE id_alat = ?");
        $stmt->bind_param("i", $id_alat);
        $stmt->execute();
        $stmt->close();
    }
}

// Ambil data alat
$result = $db->query("SELECT * FROM alat");
$equipment = $result->fetch_all(MYSQLI_ASSOC);
$result->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Kelola Alat-alat</title>
    <link rel="stylesheet" type="text/css" href="../assets/css/admin_styles.css">
</head>
<body>
    <div class="header">
        <h1>Kelola Alat-alat</h1>
        <a href="admin_dashboard.php" class="btn">Kembali ke Dashboard</a>
    </div>
    <div class="container">
        <h2>Buat Alat</h2>
        <form method="POST" action="">
            <input type="text" name="name" placeholder="Nama Alat" required>
            <textarea name="description" placeholder="Deskripsi" required></textarea>
            <select name="status" required>
                <option value="tersedia">Tersedia</option>
                <option value="tidak tersedia">Tidak Tersedia</option>
            </select>
            <input type="submit" name="create" value="Buat" class="btn">
        </form>
        <h2>Alat</h2>
        <table class="table">
            <tr>
                <th>ID Alat</th>
                <th>Nama</th>
                <th>Deskripsi</th>
                <th>Status</th>
                <th>Tindakan</th>
            </tr>
            <?php foreach ($equipment as $equip) { ?>
            <tr>
                <form method="POST" action="">
                    <td>
                        <input type="number" name="id_alat" value="<?php echo $equip['id_alat']; ?>" readonly>
                    </td>
                    <td>
                        <input type="text" name="name" value="<?php echo $equip['nama']; ?>" required>
                    </td>
                    <td>
                        <textarea name="description" required><?php echo $equip['deskripsi']; ?></textarea>
                    </td>
                    <td>
                        <select name="status" required>
                            <option value="tersedia" <?php if ($equip['status'] == 'tersedia') echo 'selected'; ?>>Tersedia</option>
                            <option value="tidak tersedia" <?php if ($equip['status'] == 'tidak tersedia') echo 'selected'; ?>>Tidak Tersedia</option>
                        </select>
                    </td>
                    <td>
                        <input type="hidden" name="id_alat" value="<?php echo $equip['id_alat']; ?>">
                        <input type="submit" name="update" value="Perbarui" class="btn">
                        <input type="submit" name="delete" value="Hapus" class="btn">
                    </td>
                </form>
            </tr>
            <?php } ?>
        </table>
    </div>
</body>
</html>
