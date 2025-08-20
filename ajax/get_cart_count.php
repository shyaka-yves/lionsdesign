<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

$session_id = generateSessionId();
$cart_items = getCart($conn, $session_id);
$count = 0;

foreach ($cart_items as $item) {
    $count += $item['quantity'];
}

echo json_encode(['count' => $count]);
?> 