<?php
include("../includes/config.php");
include("../includes/functions.php");

belumLogin();
if (!Manajer()) {
    header("Location: ../index.php");
    exit();
}

$username = $_SESSION['username'];
$stmt = $db->prepare("SELECT * FROM pengguna WHERE nama_pengguna = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$manager = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manager Dashboard</title>
    <link rel="stylesheet" type="text/css" href="../assets/css/op-manager_styles.css">
</head>
<body>
    <div class="header">
        <h1>Manager Dashboard</h1>
        <div id="menu" class="menu">
            <a href="manage_rates.php">Manage Rates</a>
            <a href="view_rooms.php">View Rooms</a>
            <a href="view_equipment.php">View Equipment</a>
            <a href="view_reports.php">View Reports</a>
            <a href="view_statistics.php">View Statistics</a>
            <a href="../logout.php">Logout</a>
        </div>
    </div>
    <div class="main-content">
        <h2>Welcome, <?php echo $manager['nama']; ?>!</h2>
        <h2 id="profile">Manager Profile</h2>
        <table class="biodata-table">
            <tr>
                <th>Nama</th>
                <td><?php echo $manager['nama']; ?></td>
            </tr>
            <tr>
                <th>Alamat</th>
                <td><?php echo $manager['alamat']; ?></td>
            </tr>
            <tr>
                <th>Telepon</th>
                <td><?php echo $manager['telepon']; ?></td>
            </tr>
            <tr>
                <th>Nama Pengguna</th>
                <td><?php echo $manager['nama_pengguna']; ?></td>
            </tr>
            <tr>
                <th>Peran</th>
                <td><?php echo $manager['peran']; ?></td>
            </tr>
            <tr>
                <th>Dibuat Pada</th>
                <td><?php echo $manager['dibuat_pada']; ?></td>
            </tr>
        </table>
    </div>
</body>
</html>
