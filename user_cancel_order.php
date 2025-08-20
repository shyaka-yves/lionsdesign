<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?redirect=orders.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'])) {
    $orderId = (int)$_POST['order_id'];
    $userId = (int)$_SESSION['user_id'];

    $ok = cancelOrder($conn, $orderId, $userId);
    if ($ok) {
        header('Location: track_order.php?id=' . $orderId . '&msg=cancelled');
        exit();
    } else {
        header('Location: track_order.php?id=' . $orderId . '&error=cannot_cancel');
        exit();
    }
}

header('Location: orders.php');
exit();
?>

