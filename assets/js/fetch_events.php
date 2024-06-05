<?php
include("../../includes/config.php");

$query = "
    SELECT 
        transaksi.id_transaksi, 
        ruang.nama_ruang, 
        transaksi.waktu_mulai, 
        transaksi.waktu_selesai
    FROM transaksi
    JOIN ruang ON transaksi.id_ruang = ruang.id_ruang";

$result = mysqli_query($db, $query);

$events = [];

while ($row = mysqli_fetch_assoc($result)) {
    $events[] = [
        'title' => $row['nama_ruang'],
        'start' => $row['waktu_mulai'],
        'end' => $row['waktu_selesai']
    ];
}

header('Content-Type: application/json');
echo json_encode($events);
?>
