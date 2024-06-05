<?php
include("../includes/config.php");
include("../includes/functions.php");

belumLogin();
if (!Operator()) {
    header("Location: ../index.php");
    exit();
}

function updateRoomStatus($db) {
    $current_time = date('Y-m-d H:i:s');
    $query = "UPDATE ruang r
              JOIN transaksi t ON r.id_ruang = t.id_ruang
              SET r.status = 'tersedia'
              WHERE t.waktu_selesai < ? AND r.status = 'tidak tersedia'";
    $stmt = $db->prepare($query);
    $stmt->bind_param('s', $current_time);
    $stmt->execute();
}

updateRoomStatus($db);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['ganti_status']) && isset($_POST['id_transaksi'])) {
        $id_transaksi = $_POST['id_transaksi'];
        $status_baru = $_POST['status_baru'];

        $query = "UPDATE transaksi SET status_pembayaran = ? WHERE id_transaksi = ?";
        $stmt = $db->prepare($query);
        $stmt->bind_param('si', $status_baru, $id_transaksi);

        if ($stmt->execute()) {
            header("Location: process_rental.php");
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
    } else {
        $nama_pelanggan = $_POST['nama_pelanggan'];
        $nomor_hp = $_POST['nomor_hp'];
        $alamat = $_POST['alamat'];
        $id_ruang = $_POST['id_ruang'];
        $waktu_mulai = $_POST['waktu_mulai'];
        $waktu_selesai = $_POST['waktu_selesai'];
        $id_alat = $_POST['id_alat'];

        $waktu_mulai_obj = new DateTime($waktu_mulai);
        $waktu_selesai_obj = new DateTime($waktu_selesai);
        $interval = $waktu_mulai_obj->diff($waktu_selesai_obj);
        $durasi_jam = $interval->h + ($interval->days * 24);

        mysqli_begin_transaction($db);

        $query_pelanggan = "INSERT INTO pelanggan (nama, nomor_hp, alamat) VALUES (?, ?, ?)";
        $stmt = $db->prepare($query_pelanggan);
        $stmt->bind_param('sss', $nama_pelanggan, $nomor_hp, $alamat);

        if ($stmt->execute()) {
            $id_pelanggan = $stmt->insert_id;

            $query_transaksi = "INSERT INTO transaksi (id_pelanggan, id_ruang, waktu_mulai, waktu_selesai, total_biaya, status_pembayaran) VALUES (?, ?, ?, ?, ?, 'belum lunas')";
            $total_biaya = 0;
            $stmt = $db->prepare($query_transaksi);
            $stmt->bind_param('iissi', $id_pelanggan, $id_ruang, $waktu_mulai, $waktu_selesai, $total_biaya);

            if ($stmt->execute()) {
                $id_transaksi = $stmt->insert_id;
                $sukses = true;

                $query_update_ruang = "UPDATE ruang SET status = 'tidak tersedia' WHERE id_ruang = ?";
                $stmt = $db->prepare($query_update_ruang);
                $stmt->bind_param('i', $id_ruang);
                $stmt->execute();

                $query_tarif_ruang = "SELECT tarif_ruang FROM tarif WHERE id_ruang = ?";
                $stmt = $db->prepare($query_tarif_ruang);
                $stmt->bind_param('i', $id_ruang);
                $stmt->execute();
                $stmt->bind_result($tarif_ruang);
                $stmt->fetch();
                $stmt->close();

                $total_tarif_ruang = $tarif_ruang * $durasi_jam;
                $total_biaya += $total_tarif_ruang;

                foreach ($id_alat as $alat_id) {
                    if ($alat_id) {
                        $query_tarif_alat = "SELECT tarif_alat FROM tarif WHERE id_alat = ?";
                        $stmt = $db->prepare($query_tarif_alat);
                        $stmt->bind_param('i', $alat_id);
                        $stmt->execute();
                        $stmt->bind_result($tarif_alat);
                        $stmt->fetch();
                        $stmt->close();

                        $total_tarif_alat = $tarif_alat * $durasi_jam;
                        $total_biaya += $total_tarif_alat;

                        $query_detail_alat = "INSERT INTO detail_transaksi (id_transaksi, id_alat, tarif_alat) VALUES (?, ?, ?)";
                        $stmt = $db->prepare($query_detail_alat);
                        $stmt->bind_param('iid', $id_transaksi, $alat_id, $total_tarif_alat);
                        if ($stmt->execute()) {
                            // Update status alat
                            $query_update_alat = "UPDATE alat SET status = 'tidak tersedia' WHERE id_alat = ?";
                            $stmt = $db->prepare($query_update_alat);
                            $stmt->bind_param('i', $alat_id);
                            $stmt->execute();
                        } else {
                            $sukses = false;
                            break;
                        }
                    }
                }

                if ($sukses) {
                    $query_update_biaya = "UPDATE transaksi SET total_biaya = ? WHERE id_transaksi = ?";
                    $stmt = $db->prepare($query_update_biaya);
                    $stmt->bind_param('di', $total_biaya, $id_transaksi);
                    $stmt->execute();

                    mysqli_commit($db);
                    header("Location: operator_dashboard.php");
                    exit();
                } else {
                    mysqli_rollback($db);
                    echo "Error: Failed to insert equipment transaction.";
                }
            } else {
                mysqli_rollback($db);
                echo "Error: " . $stmt->error;
            }
        } else {
            mysqli_rollback($db);
            echo "Error: " . $stmt->error;
        }
    }
}

$query_ruang = "SELECT r.*, t.tarif_ruang FROM ruang r LEFT JOIN tarif t ON r.id_ruang = t.id_ruang WHERE r.status = 'tersedia'";
$result_ruang = mysqli_query($db, $query_ruang);
$ruang = mysqli_fetch_all($result_ruang, MYSQLI_ASSOC);

$query_alat = "SELECT e.*, t.tarif_alat FROM alat e LEFT JOIN tarif t ON e.id_alat = t.id_alat WHERE e.status = 'tersedia'";
$result_alat = mysqli_query($db, $query_alat);
$alat = mysqli_fetch_all($result_alat, MYSQLI_ASSOC);

$query_unpaid = "SELECT t.*, p.nama, r.nama_ruang, GROUP_CONCAT(a.nama SEPARATOR ', ') as alat 
                FROM transaksi t
                JOIN pelanggan p ON t.id_pelanggan = p.id_pelanggan
                JOIN ruang r ON t.id_ruang = r.id_ruang
                LEFT JOIN detail_transaksi dt ON t.id_transaksi = dt.id_transaksi
                LEFT JOIN alat a ON dt.id_alat = a.id_alat
                WHERE t.status_pembayaran = 'belum lunas'
                GROUP BY t.id_transaksi";
$result_unpaid = mysqli_query($db, $query_unpaid);
$unpaid_transactions = mysqli_fetch_all($result_unpaid, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Proses Rental</title>
    <link rel="stylesheet" type="text/css" href="../assets/css/op-manager_styles.css">
</head>
<body>
    <div class="header">
        <h1>Proses Rental</h1>
        <a href="operator_dashboard.php">Kembali ke Dashboard</a>
    </div>
    <div class="container">
        <form method="POST" action="">
            <h2>Informasi Pelanggan</h2>
            <input type="text" name="nama_pelanggan" placeholder="Nama Pelanggan" required>
            <input type="text" name="nomor_hp" placeholder="Nomor Telepon Pelanggan" required>
            <input type="text" name="alamat" placeholder="Alamat Pelanggan" required>

            <h2>Informasi Rental</h2>
            <label for="id_ruang">Pilih Ruangan:</label>
            <select name="id_ruang" id="ruang_pilih" onchange="updateBiayaTotal()" required>
                <option value="">Pilih Ruangan</option>
                <?php foreach ($ruang as $r): ?>
                <option value="<?php echo $r['id_ruang']; ?>" data-tarif="<?php echo $r['tarif_ruang']; ?>"><?php echo htmlspecialchars($r['nama_ruang']); ?> (Kapasitas: <?php echo $r['kapasitas']; ?>, Tarif: Rp <?php echo number_format($r['tarif_ruang'], 0, ',', '.'); ?>)</option>
                <?php endforeach; ?>
            </select>

            <label for="id_alat">Pilih Alat:</label>
            <div id="daftar_alat">
                <div class="alat-container">
                    <select name="id_alat[]" onchange="updateBiayaTotal()">
                        <option value="">Tidak Ada</option>
                        <?php foreach ($alat as $a): ?>
                        <option value="<?php echo $a['id_alat']; ?>" data-tarif="<?php echo $a['tarif_alat']; ?>"><?php echo htmlspecialchars($a['nama']); ?> (Tarif: Rp <?php echo number_format($a['tarif_alat'], 0, ',', '.'); ?>, Status: <?php echo htmlspecialchars($a['status']); ?>)</option>
                        <?php endforeach; ?>
                    </select>
                    <button type="button" onclick="hapusAlat(this)">Hapus</button>
                </div>
            </div>
            <button type="button" onclick="tambahAlat()">Tambah Alat</button>

            <input type="datetime-local" name="waktu_mulai" id="waktu_mulai" placeholder="Waktu Mulai" required onchange="updateBiayaTotal()">
            <input type="datetime-local" name="waktu_selesai" id="waktu_selesai" placeholder="Waktu Selesai" required onchange="updateBiayaTotal()">

            <h3>Perkiraan Biaya: Rp <span id="perkiraan_biaya">0</span></h3>

            <input type="submit" value="Proses Rental">
        </form>

        <h2>Transaksi Belum Lunas</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Nama Pelanggan</th>
                <th>Nama Ruang</th>
                <th>Nama Alat</th>
                <th>Waktu Mulai</th>
                <th>Waktu Akhir</th>
                <th>Total Biaya</th>
                <th>Status Pembayaran</th>
                <th>Update</th>
            </tr>
            <?php foreach ($unpaid_transactions as $trans): ?>
            <tr>
                <td><?php echo htmlspecialchars($trans['id_transaksi']); ?></td>
                <td><?php echo htmlspecialchars($trans['nama']); ?></td>
                <td><?php echo htmlspecialchars($trans['nama_ruang']); ?></td>
                <td><?php echo htmlspecialchars($trans['alat']); ?></td>
                <td><?php echo htmlspecialchars($trans['waktu_mulai']); ?></td>
                <td><?php echo htmlspecialchars($trans['waktu_selesai']); ?></td>
                <td>Rp <?php echo number_format($trans['total_biaya'], 0, ',', '.'); ?></td>
                <td><?php echo htmlspecialchars($trans['status_pembayaran']); ?></td>
                <td>
                    <form method="POST" action="">
                        <input type="hidden" name="id_transaksi" value="<?php echo $trans['id_transaksi']; ?>">
                        <select name="status_baru">
                            <option value="belum lunas" <?php echo $trans['status_pembayaran'] == 'belum lunas' ? 'selected' : ''; ?>>Belum Lunas</option>
                            <option value="lunas" <?php echo $trans['status_pembayaran'] == 'lunas' ? 'selected' : ''; ?>>Lunas</option>
                        </select>
                        <button type="submit" name="ganti_status">Ubah Status</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <script>
    function tambahAlat() {
        var daftarAlat = document.getElementById('daftar_alat');
        var alatContainer = document.createElement('div');
        alatContainer.classList.add('alat-container');
        alatContainer.innerHTML = `
            <select name="id_alat[]" onchange="updateBiayaTotal()">
                <option value="">Tidak Ada</option>
                <?php foreach ($alat as $a): ?>
                <option value="<?php echo $a['id_alat']; ?>" data-tarif="<?php echo $a['tarif_alat']; ?>"><?php echo htmlspecialchars($a['nama']); ?> (Tarif: Rp <?php echo number_format($a['tarif_alat'], 0, ',', '.'); ?>, Status: <?php echo htmlspecialchars($a['status']); ?>)</option>
                <?php endforeach; ?>
            </select>
            <button type="button" onclick="hapusAlat(this)">Hapus</button>
        `;
        daftarAlat.appendChild(alatContainer);
        updateBiayaTotal();
    }

    function hapusAlat(button) {
        var alatContainer = button.parentElement;
        alatContainer.remove();
        updateBiayaTotal();
    }

    function updateBiayaTotal() {
        var tarifRuangSelect = document.getElementById('ruang_pilih');
        var tarifRuang = tarifRuangSelect.options[tarifRuangSelect.selectedIndex].dataset.tarif || 0;
        calculateTotal(tarifRuang);
    }

    function calculateTotal(tarifRuang) {
        var waktuMulai = new Date(document.getElementById('waktu_mulai').value);
        var waktuSelesai = new Date(document.getElementById('waktu_selesai').value);
        if (isNaN(waktuMulai) || isNaN(waktuSelesai) || waktuMulai >= waktuSelesai) {
            document.getElementById('perkiraan_biaya').innerText = '0';
            return;
        }

        var durasiJam = (waktuSelesai - waktuMulai) / 3600000;
        var totalTarif = parseInt(tarifRuang) * durasiJam;

        var selectAlat = document.querySelectorAll('select[name="id_alat[]"]');
        selectAlat.forEach(function(select) {
            var tarifAlat = select.options[select.selectedIndex].dataset.tarif || 0;
            totalTarif += parseInt(tarifAlat) * durasiJam;
        });

        document.getElementById('perkiraan_biaya').innerText = totalTarif.toLocaleString('id-ID');
    }
    </script>
</body>
</html>
