<?php
include("../includes/config.php");
include("../includes/functions.php");

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

belumLogin();
if (!Admin()) {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['create'])) {
        $nama = mysqli_real_escape_string($db, $_POST['nama']);
        $alamat = mysqli_real_escape_string($db, $_POST['alamat']);
        $telepon = mysqli_real_escape_string($db, $_POST['telepon']);
        $nama_pengguna = mysqli_real_escape_string($db, $_POST['nama_pengguna']);
        $kata_sandi = mysqli_real_escape_string($db, $_POST['kata_sandi']);
        $peran = mysqli_real_escape_string($db, $_POST['peran']);
        $stmt = $db->prepare("INSERT INTO pengguna (nama, alamat, telepon, nama_pengguna, kata_sandi, peran) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $nama, $alamat, $telepon, $nama_pengguna, $kata_sandi, $peran);
        $stmt->execute();
    } elseif (isset($_POST['update'])) {
        $id_pengguna = mysqli_real_escape_string($db, $_POST['id_pengguna']);
        $nama = mysqli_real_escape_string($db, $_POST['nama']);
        $alamat = mysqli_real_escape_string($db, $_POST['alamat']);
        $telepon = mysqli_real_escape_string($db, $_POST['telepon']);
        $nama_pengguna = mysqli_real_escape_string($db, $_POST['nama_pengguna']);
        $kata_sandi = mysqli_real_escape_string($db, $_POST['kata_sandi']);
        $peran = mysqli_real_escape_string($db, $_POST['peran']);
        $stmt = $db->prepare("UPDATE pengguna SET nama = ?, alamat = ?, telepon = ?, nama_pengguna = ?, kata_sandi = ?, peran = ? WHERE id_pengguna = ?");
        $stmt->bind_param("ssssssi", $nama, $alamat, $telepon, $nama_pengguna, $kata_sandi, $peran, $id_pengguna);
        $stmt->execute();
    } elseif (isset($_POST['delete'])) {
        $id_pengguna = mysqli_real_escape_string($db, $_POST['id_pengguna']);
        $stmt = $db->prepare("DELETE FROM pengguna WHERE id_pengguna = ?");
        $stmt->bind_param("i", $id_pengguna);
        $stmt->execute();
    }
}

$users = $db->query("SELECT * FROM pengguna")->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Users</title>
    <link rel="stylesheet" type="text/css" href="../assets/css/admin_styles.css">
</head>
<body>
    <div class="header">
        <h1>Kelola Pengguna</h1>
        <a href="admin_dashboard.php" class="btn">Back to Dashboard</a>
    </div>
    <div class="container">
        <h2>Create User</h2>
        <form method="POST" action="">
            <input type="text" name="nama" placeholder="Nama" required>
            <textarea name="alamat" placeholder="Alamat" required></textarea>
            <input type="text" name="telepon" placeholder="Telepon" required>
            <input type="text" name="nama_pengguna" placeholder="Nama Pengguna" required>
            <div class="password-wrapper">
                <input type="password" name="kata_sandi" placeholder="Kata Sandi" required>
                <span class="toggle-password" onclick="togglePassword(this)">👁️</span>
            </div>
            <select name="peran">
                <option value="Admin">Admin</option>
                <option value="Operator">Operator</option>
                <option value="Manajer">Manajer</option>
            </select>
            <input type="submit" name="create" value="Create" class="btn">
        </form>
        <h2>Users</h2>
        <table class="table">
            <tr>
                <th>Nama</th>
                <th>Alamat</th>
                <th>Telepon</th>
                <th>Nama Pengguna</th>
                <th>Kata Sandi</th>
                <th>Peran</th>
                <th>Update/Delete</th>
            </tr>
            <?php foreach ($users as $user) { ?>
            <tr>
                <form method="POST" action="">
                    <td>
                        <input type="text" name="nama" value="<?php echo $user['nama']; ?>" required>
                    </td>
                    <td>
                        <textarea name="alamat" required><?php echo $user['alamat']; ?></textarea>
                    </td>
                    <td>
                        <input type="text" name="telepon" value="<?php echo $user['telepon']; ?>" required>
                    </td>
                    <td>
                        <input type="text" name="nama_pengguna" value="<?php echo $user['nama_pengguna']; ?>" required>
                    </td>
                    <td>
                        <div class="password-wrapper">
                            <input type="password" name="kata_sandi" placeholder="New Kata Sandi" required>
                            <span class="toggle-password" onclick="togglePassword(this)">👁️</span>
                        </div>
                    </td>
                    <td>
                        <select name="peran">
                            <option value="Admin" <?php if ($user['peran'] == 'Admin') echo 'selected'; ?>>Admin</option>
                            <option value="Operator" <?php if ($user['peran'] == 'Operator') echo 'selected'; ?>>Operator</option>
                            <option value="Manajer" <?php if ($user['peran'] == 'Manajer') echo 'selected'; ?>>Manajer</option>
                        </select>
                    </td>
                    <td>
                        <input type="hidden" name="id_pengguna" value="<?php echo $user['id_pengguna']; ?>">
                        <input type="submit" name="update" value="Update" class="btn">
                        <input type="submit" name="delete" value="Delete" class="btn">
                    </td>
                </form>
            </tr>
            <?php } ?>
        </table>
    </div>
    <script>
        function togglePassword(elem) {
            var input = elem.previousElementSibling;
            if (input.type === "password") {
                input.type = "text";
            } else {
                input.type = "password";
            }
        }
    </script>
</body>
</html>
