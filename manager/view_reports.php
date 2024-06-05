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
        t.id_transaksi,
        p.nama AS nama_pelanggan,
        r.nama_ruang,
        t.waktu_mulai,
        t.waktu_selesai,
        t.total_biaya,
        t.status_pembayaran,
        GROUP_CONCAT(a.nama SEPARATOR ', ') AS nama_alat
    FROM 
        transaksi t
    JOIN 
        pelanggan p ON t.id_pelanggan = p.id_pelanggan
    JOIN 
        ruang r ON t.id_ruang = r.id_ruang
    LEFT JOIN 
        detail_transaksi dt ON t.id_transaksi = dt.id_transaksi
    LEFT JOIN 
        alat a ON dt.id_alat = a.id_alat
    GROUP BY 
        t.id_transaksi, p.nama, r.nama_ruang, t.waktu_mulai, t.waktu_selesai, t.total_biaya, t.status_pembayaran
";
$stmt = $db->prepare($query);
$stmt->execute();
$result = $stmt->get_result();
$transactions = $result->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title>View Reports</title>
    <link rel="stylesheet" type="text/css" href="../assets/css/op-manager_styles.css">
</head>
<body>
    <div class="header">
        <h1>View Reports</h1>
        <a href="manager_dashboard.php" class="btn">Back to Dashboard</a>
    </div>
    <div class="container">
        <h2>Transaction Reports</h2>
        <table class="table">
            <tr>
                <th>Transaction ID</th>
                <th>Customer</th>
                <th>Room</th>
                <th>Equipment</th>
                <th>Start Time</th>
                <th>End Time</th>
                <th>Total Cost</th>
                <th>Payment Status</th>
            </tr>
            <?php foreach ($transactions as $transaction) { ?>
            <tr>
                <td><?php echo htmlspecialchars($transaction['id_transaksi']); ?></td>
                <td><?php echo htmlspecialchars($transaction['nama_pelanggan']); ?></td>
                <td><?php echo htmlspecialchars($transaction['nama_ruang']); ?></td>
                <td><?php echo htmlspecialchars($transaction['nama_alat']); ?></td>
                <td><?php echo htmlspecialchars($transaction['waktu_mulai']); ?></td>
                <td><?php echo htmlspecialchars($transaction['waktu_selesai']); ?></td>
                <td>Rp <?php echo number_format($transaction['total_biaya'], 0, ',', '.'); ?></td>
                <td><?php echo htmlspecialchars($transaction['status_pembayaran']); ?></td>
            </tr>
            <?php } ?>
        </table>
    </div>
</body>
</html>
