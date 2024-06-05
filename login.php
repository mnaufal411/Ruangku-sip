<?php
session_start();
include 'includes/config.php';
include 'includes/functions.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = "SELECT * FROM pengguna WHERE nama_pengguna = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && $password === $user['kata_sandi']) {
        $_SESSION['username'] = $user['nama_pengguna'];
        $_SESSION['role'] = $user['peran'];

        if ($user['peran'] === 'Admin') {
            header("Location: admin/admin_dashboard.php");
        } elseif ($user['peran'] === 'Operator') {
            header("Location: operator/operator_dashboard.php");
        } elseif ($user['peran'] === 'Manajer') {
            header("Location: manager/manager_dashboard.php");
        }
        exit();
    } else {
        $_SESSION['error_message'] = "Login gagal, periksa kembali username dan password Anda.";
        header("Location: index.php");
        exit();
    }
} else {
    header("Location: index.php");
    exit();
}
?>

