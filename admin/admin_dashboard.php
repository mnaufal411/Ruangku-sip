<?php
include("../includes/config.php");
include("../includes/functions.php");

belumLogin();
if (!Admin()) {
    header("Location: ../index.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" type="text/css" href="../assets/css/admin_styles.css">
</head>
<body>
    <div class="header">
        <h1>Admin Dashboard</h1>
    </div>
    <div id="menu" class="menu">
        <a href="manage_users.php">Manage Users</a>
        <a href="manage_rooms.php">Manage Rooms</a>
        <a href="manage_equipment.php">Manage Equipment</a>
        <a href="../logout.php">Logout</a>
    </div>
    <div class="main-content">
        <div class="section">
            <div class="existing-section">
                <h2>Users</h2>
                <table>
                    <tr>
                        <th>Nama Pengguna</th>
                        <th>Peran</th>
                    </tr>
                    <?php
                    $query = "SELECT * FROM pengguna";
                    if ($result = mysqli_query($db, $query)) {
                        while ($user = mysqli_fetch_assoc($result)) {
                            echo "<tr><td>{$user['nama_pengguna']}</td><td>{$user['peran']}</td></tr>";
                        }
                    } else {
                        echo "<tr><td colspan='2'>Error: " . mysqli_error($db) . "</td></tr>";
                    }
                    ?>
                </table>
            </div>
        </div>
        
        <div class="section">
            <div class="existing-section">
                <h2>Rooms</h2>
                <table>
                    <tr>
                        <th>ID Ruang</th>
                        <th>Nama Ruang</th>
                        <th>Kapasitas</th>
                        <th>Status</th>
                    </tr>
                    <?php
                    $query = "SELECT * FROM ruang";
                    if ($result = mysqli_query($db, $query)) {
                        while ($room = mysqli_fetch_assoc($result)) {
                            echo "<tr><td>{$room['id_ruang']}</td><td>{$room['nama_ruang']}</td><td>{$room['kapasitas']}</td><td>{$room['status']}</td></tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4'>Error: " . mysqli_error($db) . "</td></tr>";
                    }
                    ?>
                </table>
            </div>
        </div>
        
        <div class="section">
            <div class="existing-section">
                <h2>Equipment</h2>
                <table>
                    <tr>
                        <th>ID Alat</th>
                        <th>Nama</th>
                        <th>Deskripsi</th>
                        <th>Status</th>
                    </tr>
                    <?php
                    $query = "SELECT * FROM alat";
                    if ($result = mysqli_query($db, $query)) {
                        while ($equipment = mysqli_fetch_assoc($result)) {
                            echo "<tr><td>{$equipment['id_alat']}</td><td>{$equipment['nama']}</td><td>{$equipment['deskripsi']}</td><td>{$equipment['status']}</td></tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4'>Error: " . mysqli_error($db) . "</td></tr>";
                    }
                    ?>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
