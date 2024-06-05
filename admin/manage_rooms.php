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
        $id_ruang = $_POST['id_ruang'];
        $nama_ruang = $_POST['nama_ruang'];
        $kapasitas = $_POST['kapasitas'];
        $status = $_POST['status'];
        $stmt = $db->prepare("INSERT INTO ruang (id_ruang, nama_ruang, kapasitas, status) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isis", $id_ruang, $nama_ruang, $kapasitas, $status);
        $stmt->execute();
        $stmt->close();
    } elseif (isset($_POST['update'])) {
        $id_ruang = $_POST['id_ruang'];
        $nama_ruang = $_POST['nama_ruang'];
        $kapasitas = $_POST['kapasitas'];
        $status = $_POST['status'];
        $stmt = $db->prepare("UPDATE ruang SET nama_ruang = ?, kapasitas = ?, status = ? WHERE id_ruang = ?");
        $stmt->bind_param("sisi", $nama_ruang, $kapasitas, $status, $id_ruang);
        $stmt->execute();
        $stmt->close();
    } elseif (isset($_POST['delete'])) {
        $id_ruang = $_POST['id_ruang'];
        $stmt = $db->prepare("DELETE FROM ruang WHERE id_ruang = ?");
        $stmt->bind_param("i", $id_ruang);
        $stmt->execute();
        $stmt->close();
    }
}

$result = $db->query("SELECT * FROM ruang");
$ruang = $result->fetch_all(MYSQLI_ASSOC);
$result->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Kelola Ruangan</title>
    <link rel="stylesheet" type="text/css" href="../assets/css/admin_styles.css">
</head>
<body>
    <div class="header">
        <h1>Kelola Ruangan</h1>
        <a href="admin_dashboard.php" class="btn">Back to Dashboard</a>
    </div>
    <div class="container">
        <h2>Create Room</h2>
        <form method="POST" action="">
            <input type="number" name="id_ruang" placeholder="ID Ruang" required>
            <input type="text" name="nama_ruang" placeholder="Nama Ruang" required>
            <input type="number" name="kapasitas" placeholder="Kapasitas" min="7" max="20" required>
            <select name="status" required>
                <option value="tersedia">Tersedia</option>
                <option value="tidak tersedia">Tidak Tersedia</option>
            </select>
            <input type="submit" name="create" value="Create" class="btn">
        </form>
        <h2>Rooms</h2>
        <table class="table">
            <tr>
                <th>ID Ruang</th>
                <th>Nama Ruang</th>
                <th>Kapasitas</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($ruang as $room) { ?>
            <tr>
                <form method="POST" action="">
                    <td>
                        <input type="number" name="id_ruang" value="<?php echo $room['id_ruang']; ?>" required>
                    </td>
                    <td>
                        <input type="text" name="nama_ruang" value="<?php echo $room['nama_ruang']; ?>" required>
                    </td>
                    <td>
                        <input type="number" name="kapasitas" value="<?php echo $room['kapasitas']; ?>" min="7" max="20" required>
                    </td>
                    <td>
                        <select name="status" required>
                            <option value="tersedia" <?php if ($room['status'] == 'tersedia') echo 'selected'; ?>>Tersedia</option>
                            <option value="tidak tersedia" <?php if ($room['status'] == 'tidak tersedia') echo 'selected'; ?>>Tidak Tersedia</option>
                        </select>
                    </td>
                    <td>
                        <input type="hidden" name="id_ruang" value="<?php echo $room['id_ruang']; ?>">
                        <input type="submit" name="update" value="Update" class="btn">
                        <input type="submit" name="delete" value="Delete" class="btn">
                    </td>
                </form>
            </tr>
            <?php } ?>
        </table>
    </div>
</body>
</html>
