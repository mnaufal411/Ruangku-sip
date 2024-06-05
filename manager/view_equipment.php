<?php
include("../includes/config.php");
include("../includes/functions.php");

belumLogin();
if (!Manajer()) {
    header("Location: ../index.php");
    exit();
}

$query = "
    SELECT 
        alat.id_alat, alat.nama, alat.deskripsi, 
        CASE 
            WHEN EXISTS (
                SELECT 1 FROM detail_transaksi 
                JOIN transaksi ON detail_transaksi.id_transaksi = transaksi.id_transaksi 
                WHERE detail_transaksi.id_alat = alat.id_alat 
                AND transaksi.waktu_mulai <= NOW() 
                AND transaksi.waktu_selesai >= NOW()
            ) 
            THEN 'Not Available' 
            ELSE 'Available' 
        END AS status 
    FROM alat";

$result = mysqli_query($db, $query);
$equipment = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title>View Equipment</title>
    <link rel="stylesheet" type="text/css" href="../assets/css/op-manager_styles.css">
</head>
<body>
    <div class="header">
        <h1>View Equipment</h1>
        <a href="manager_dashboard.php">Back to Dashboard</a>
    </div>
    <div class="container">
        <h2>Equipment Information</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Description</th>
                <th>Status</th>
            </tr>
            <?php foreach ($equipment as $item): ?>
            <tr>
                <td><?php echo htmlspecialchars($item['id_alat']); ?></td>
                <td><?php echo htmlspecialchars($item['nama']); ?></td>
                <td><?php echo htmlspecialchars($item['deskripsi']); ?></td>
                <td><?php echo htmlspecialchars($item['status']); ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>
