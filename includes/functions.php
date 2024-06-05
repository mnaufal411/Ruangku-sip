<?php
session_start();

function belumLogin() {
    if (!isset($_SESSION['username'])) {
        header("Location: ../login.php");
        exit();
    }
}

function Manajer() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'Manajer';
}

function Admin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'Admin';
}

function Operator() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'Operator';
}



?>

