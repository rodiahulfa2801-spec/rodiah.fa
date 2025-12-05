<?php
// config.php
// Sesuaikan credential DB jika perlu
$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'edukasi';

$mysqli = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($mysqli->connect_errno) {
    die('Connect Error: ' . $mysqli->connect_error);
}
$mysqli->set_charset('utf8mb4');
session_start();
?>