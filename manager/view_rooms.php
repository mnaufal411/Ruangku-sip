<?php
include("../includes/config.php");
include("../includes/functions.php");

belumLogin();
if (!Manajer()) {
    header("Location: ../index.php");
    exit();
}

function updateRoomStatus($db) {
    $current_time = date('Y-m-d H:i:s');

    $query = "UPDATE ruang r
              JOIN transaksi t ON r.id_ruang = t.id_ruang
              SET r.status = 'Available'
              WHERE t.waktu_selesai < ? AND r.status = 'Not Available'";
    $stmt = $db->prepare($query);
    $stmt->bind_param('s', $current_time);
    $stmt->execute();
    $stmt->close();
    
    $query = "UPDATE ruang r
              JOIN transaksi t ON r.id_ruang = t.id_ruang
              SET r.status = 'Not Available'
              WHERE t.waktu_mulai <= ? AND t.waktu_selesai >= ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param('ss', $current_time, $current_time);
    $stmt->execute();
    $stmt->close();
}

updateRoomStatus($db);

$query = "
    SELECT 
        ruang.id_ruang, ruang.nama_ruang, ruang.kapasitas, 
        CASE 
            WHEN EXISTS (
                SELECT 1 FROM transaksi 
                WHERE transaksi.id_ruang = ruang.id_ruang 
                AND transaksi.waktu_mulai <= NOW() 
                AND transaksi.waktu_selesai >= NOW()
            ) 
            THEN 'Not Available' 
            ELSE 'Available' 
        END AS status 
    FROM ruang";

$result = mysqli_query($db, $query);
$rooms = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title>View Rooms</title>
    <link rel="stylesheet" type="text/css" href="../assets/css/op-manager_styles.css">
</head>
<body>
    <div class="header">
        <h1>View Rooms</h1>
        <a href="manager_dashboard.php">Back to Dashboard</a>
    </div>
    <div class="container">
        <h2>Room Information</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Capacity</th>
                <th>Status</th>
            </tr>
            <?php foreach ($rooms as $room): ?>
            <tr>
                <td><?php echo htmlspecialchars($room['id_ruang']); ?></td>
                <td><?php echo htmlspecialchars($room['nama_ruang']); ?></td>
                <td><?php echo htmlspecialchars($room['kapasitas']); ?></td>
                <td><?php echo htmlspecialchars($room['status']); ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>
