<?php
$host = 'localhost';
$db   = 'karigar';
$user = 'root';
$pass = ''; // Change if you have password

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

header('Content-Type: application/json');
?>