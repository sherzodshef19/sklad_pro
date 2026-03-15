<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/db.php';

$is_logged_in = isset($_SESSION['user_id']);
$current_page = basename($_SERVER['PHP_SELF']);

if (!$is_logged_in && $current_page != 'login.php') {
    header("Location: login.php");
    exit;
}
?>
