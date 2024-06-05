<?php
include("../includes/config.php");
include("../includes/functions.php");

belumLogin();
if (!Operator()) {
    header("Location: ../index.php");
    exit();
}

$username = $_SESSION['username'];
$stmt = $db->prepare("SELECT * FROM pengguna WHERE nama_pengguna = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$operator = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Operator Dashboard</title>
    <link rel="stylesheet" type="text/css" href="../assets/css/op-manager_styles.css">
</head>
<body>
    <div class="header">
        <h1>Operator Dashboard</h1>
        <div id="menu" class="menu">
            <a href="search_rooms.php">Search Rooms</a>
            <a href="search_equipment.php">Search Equipment</a>
            <a href="process_rental.php">Process Rental</a>
            <a href="../logout.php">Logout</a>
        </div>
    </div>
    <div class="main-content">
        <div class="section">
            <h2>Welcome, <?php echo $operator['nama']; ?>!</h2>
            <h2 id="profile">Operator Profile</h2>
            <table class="biodata-table">
                <tr>
                    <th>Nama</th>
                    <td><?php echo $operator['nama']; ?></td>
                </tr>
                <tr>
                    <th>Alamat</th>
                    <td><?php echo $operator['alamat']; ?></td>
                </tr>
                <tr>
                    <th>Telepon</th>
                    <td><?php echo $operator['telepon']; ?></td>
                </tr>
                <tr>
                    <th>Nama Pengguna</th>
                    <td><?php echo $operator['nama_pengguna']; ?></td>
                </tr>
                <tr>
                    <th>Peran</th>
                    <td><?php echo $operator['peran']; ?></td>
                </tr>
                <tr>
                    <th>Dibuat Pada</th>
                    <td><?php echo $operator['dibuat_pada']; ?></td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>
