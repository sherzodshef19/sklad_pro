<?php
$host = 'localhost';
$db   = 'sklad_db';
$user = 'root';
$pass = ''; // Default for OSPanel
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
     $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
     // If database doesn't exist, we might need to handle it or the user creates it via phpMyAdmin
     // For now, we assume it exists or we show a friendly error
     die("Маълумотлар базасига уланишда хатолик: " . $e->getMessage());
}
?>
