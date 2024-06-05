<?php
include("../includes/config.php");
include("../includes/functions.php");

belumLogin();
if (!Manajer()) {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    $type = $_POST['type'];
    $reference_id = $_POST['reference_id'];
    $rate = $_POST['rate'];

    if ($action == 'create') {
        if ($type == 'Room') {
            $stmt = $db->prepare("INSERT INTO tarif (id_ruang, tarif_ruang) VALUES (?, ?)");
            $stmt->bind_param("id", $reference_id, $rate);
        } else {
            $stmt = $db->prepare("INSERT INTO tarif (id_alat, tarif_alat) VALUES (?, ?)");
            $stmt->bind_param("id", $reference_id, $rate);
        }
        $stmt->execute();
    } elseif ($action == 'update') {
        $rate_id = $_POST['rate_id'];
        if ($type == 'Room') {
            $stmt = $db->prepare("UPDATE tarif SET tarif_ruang = ? WHERE id_tarif = ?");
            $stmt->bind_param("di", $rate, $rate_id);
        } else {
            $stmt = $db->prepare("UPDATE tarif SET tarif_alat = ? WHERE id_tarif = ?");
            $stmt->bind_param("di", $rate, $rate_id);
        }
        $stmt->execute();
    } elseif ($action == 'delete') {
        $rate_id = $_POST['rate_id'];
        $stmt = $db->prepare("DELETE FROM tarif WHERE id_tarif = ?");
        $stmt->bind_param("i", $rate_id);
        $stmt->execute();
    }

    header("Location: manage_rates.php");
    exit();
}

$rooms = $db->query("SELECT * FROM ruang")->fetch_all(MYSQLI_ASSOC);
$equipment = $db->query("SELECT * FROM alat")->fetch_all(MYSQLI_ASSOC);
$room_rates = $db->query("SELECT tarif.*, ruang.nama_ruang FROM tarif JOIN ruang ON tarif.id_ruang = ruang.id_ruang")->fetch_all(MYSQLI_ASSOC);
$equipment_rates = $db->query("SELECT tarif.*, alat.nama FROM tarif JOIN alat ON tarif.id_alat = alat.id_alat")->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Rates</title>
    <link rel="stylesheet" type="text/css" href="../assets/css/op-manager_styles.css">
    <script type="text/javascript">
        function updateReferenceOptions() {
            var typeSelect = document.getElementsByName('type')[0];
            var referenceSelect = document.getElementsByName('reference_id')[0];
            var rooms = <?php echo json_encode($rooms); ?>;
            var equipment = <?php echo json_encode($equipment); ?>;

            referenceSelect.innerHTML = '';

            if (typeSelect.value === 'Room') {
                var optgroup = document.createElement('optgroup');
                optgroup.label = 'Rooms';
                rooms.forEach(function(room) {
                    var option = document.createElement('option');
                    option.value = room.id_ruang;
                    option.text = room.nama_ruang;
                    optgroup.appendChild(option);
                });
                referenceSelect.appendChild(optgroup);
            } else if (typeSelect.value === 'Equipment') {
                var optgroup = document.createElement('optgroup');
                optgroup.label = 'Equipment';
                equipment.forEach(function(item) {
                    var option = document.createElement('option');
                    option.value = item.id_alat;
                    option.text = item.nama;
                    optgroup.appendChild(option);
                });
                referenceSelect.appendChild(optgroup);
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            var typeSelect = document.getElementsByName('type')[0];
            typeSelect.addEventListener('change', updateReferenceOptions);
            updateReferenceOptions(); 
        });
    </script>
</head>
<body>
    <div class="header">
        <h1>Manage Rates</h1>
        <a href="manager_dashboard.php">Back to Dashboard</a>
    </div>
    <div class="container">
        <h2>Add Rate</h2>
        <form method="POST" action="">
            <input type="hidden" name="action" value="create">
            <label for="type">Type:</label>
            <select name="type" required>
                <option value="Room">Room</option>
                <option value="Equipment">Equipment</option>
            </select>

            <label for="reference_id">Reference:</label>
            <select name="reference_id" required>
            </select>

            <label for="rate">Rate (per hour):</label>
            <input type="number" step="0.01" name="rate" required>
            
            <input type="submit" value="Add Rate">
        </form>

        <h2>Current Room Rates</h2>
        <table>
            <tr>
                <th>Room Name</th>
                <th>Rate (per hour)</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($room_rates as $rate): ?>
            <tr>
                <td><?php echo $rate['nama_ruang']; ?></td>
                <td><?php echo $rate['tarif_ruang']; ?> per hour</td>
                <td>
                    <form method="POST" action="" style="display:inline;">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="rate_id" value="<?php echo $rate['id_tarif']; ?>">
                        <input type="submit" value="Delete">
                    </form>
                    <form method="POST" action="" style="display:inline;">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="type" value="Room">
                        <input type="hidden" name="rate_id" value="<?php echo $rate['id_tarif']; ?>">
                        <input type="hidden" name="reference_id" value="<?php echo $rate['id_ruang']; ?>">
                        <input type="number" step="0.01" name="rate" value="<?php echo $rate['tarif_ruang']; ?>" required>
                        <input type="submit" value="Update">
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>

        <h2>Current Equipment Rates</h2>
        <table>
            <tr>
                <th>Equipment Name</th>
                <th>Rate (per hour)</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($equipment_rates as $rate): ?>
            <tr>
                <td><?php echo $rate['nama']; ?></td>
                <td><?php echo $rate['tarif_alat']; ?> per hour</td>
                <td>
                    <form method="POST" action="" style="display:inline;">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="rate_id" value="<?php echo $rate['id_tarif']; ?>">
                        <input type="submit" value="Delete">
                    </form>
                    <form method="POST" action="" style="display:inline;">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="type" value="Equipment">
                        <input type="hidden" name="rate_id" value="<?php echo $rate['id_tarif']; ?>">
                        <input type="hidden" name="reference_id" value="<?php echo $rate['id_alat']; ?>">
                        <input type="number" step="0.01" name="rate" value="<?php echo $rate['tarif_alat']; ?>" required>
                        <input type="submit" value="Update">
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>
