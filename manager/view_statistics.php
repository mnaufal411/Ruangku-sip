<?php
include("../includes/config.php");
include("../includes/functions.php");

belumLogin();
if (!Manajer()) {
    header("Location: ../index.php");
    exit();
}

$start_date = $_POST['start_date'] ?? null;
$end_date = $_POST['end_date'] ?? null;

$room_occupancy_query = "SELECT nama_ruang, COUNT(*) as count FROM transaksi JOIN ruang ON transaksi.id_ruang = ruang.id_ruang";
if ($start_date && $end_date) {
    $room_occupancy_query .= " WHERE waktu_mulai BETWEEN '$start_date' AND '$end_date'";
}
$room_occupancy_query .= " GROUP BY nama_ruang";
$room_occupancy = $db->query($room_occupancy_query)->fetch_all(MYSQLI_ASSOC);

$equipment_rental_query = "SELECT nama, COUNT(*) as count FROM detail_transaksi JOIN alat ON detail_transaksi.id_alat = alat.id_alat";
if ($start_date && $end_date) {
    $equipment_rental_query .= " JOIN transaksi ON detail_transaksi.id_transaksi = transaksi.id_transaksi";
    $equipment_rental_query .= " WHERE transaksi.waktu_mulai BETWEEN '$start_date' AND '$end_date'";
}
$equipment_rental_query .= " GROUP BY nama";
$equipment_rental = $db->query($equipment_rental_query)->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Statistics</title>
    <link rel="stylesheet" type="text/css" href="../assets/css/op-manager_styles.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="header">
        <h1>View Statistics</h1>
        <a href="manager_dashboard.php" class="btn">Back to Dashboard</a>
    </div>
    <div class="container">
        <form method="POST" action="">
            <label for="start_date">Start Date:</label>
            <input type="date" name="start_date" id="start_date" required>
            <label for="end_date">End Date:</label>
            <input type="date" name="end_date" id="end_date" required>
            <input type="submit" value="Filter" class="btn">
        </form>
        <h2>Room Occupancy</h2>
        <canvas id="roomOccupancyChart"></canvas>
        <script>
            var ctx1 = document.getElementById('roomOccupancyChart').getContext('2d');
            var roomOccupancyChart = new Chart(ctx1, {
                type: 'bar',
                data: {
                    labels: [<?php foreach ($room_occupancy as $data) { echo "'".$data['nama_ruang']."',"; } ?>],
                    datasets: [{
                        label: 'Room Occupancy',
                        data: [<?php foreach ($room_occupancy as $data) { echo $data['count'].","; } ?>],
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        </script>
        <h2>Room Revenue</h2>
        <canvas id="roomRevenueChart"></canvas>
        <script>
            var ctx2 = document.getElementById('roomRevenueChart').getContext('2d');
            var roomRevenueChart = new Chart(ctx2, {
                type: 'bar',
                data: {
                    labels: [<?php foreach ($room_occupancy as $data) { echo "'".$data['nama_ruang']."',"; } ?>],
                    datasets: [{
                        label: 'Room Revenue',
                        data: [<?php foreach ($room_occupancy as $data) { echo $data['count']*1000000 . ","; } ?>], // Assuming each room rental generates revenue of 1,000,000 (example)
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true,
                            callback: function(value, index, values) {
                                return 'Rp ' + value.toLocaleString('id-ID');
                            }
                        }
                    }
                }
            });
        </script>
        <h2>Equipment Rental</h2>
        <canvas id="equipmentRentalChart"></canvas>
        <script>
            var ctx3 = document.getElementById('equipmentRentalChart').getContext('2d');
            var equipmentRentalChart = new Chart(ctx3, {
                type: 'bar',
                data: {
                    labels: [<?php foreach ($equipment_rental as $data) { echo "'".$data['nama']."',"; } ?>],
                    datasets: [{
                        label: 'Equipment Rental',
                        data: [<?php foreach ($equipment_rental as $data) { echo $data['count'].","; } ?>],
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        </script>
        <h2>Equipment Revenue</h2>
        <canvas id="equipmentRevenueChart"></canvas>
        <script>
            var ctx4 = document.getElementById('equipmentRevenueChart').getContext('2d');
            var equipmentRevenueChart = new Chart(ctx4, {
                type: 'bar',
                data: {
                    labels: [<?php foreach ($equipment_rental as $data) { echo "'".$data['nama']."',"; } ?>],
                    datasets: [{
                        label: 'Equipment Revenue',
                        data: [<?php foreach ($equipment_rental as $data) { echo $data['count']*500000 . ","; } ?>], // Assuming each equipment rental generates revenue of 500,000 (example)
                        backgroundColor: 'rgba(255, 206, 86, 0.2)',
                        borderColor: 'rgba(255, 206, 86, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true,
                            callback: function(value, index, values) {
                                return 'Rp ' + value.toLocaleString('id-ID');
                            }
                        }
                    }
                }
            });
        </script>
    </div>
</body>
</html>
