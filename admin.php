<?php
session_start();
require 'config.php';

if ($_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Access denied']);
    exit();
}

// Get all users, requests, etc.
$users = $conn->query("SELECT * FROM users")->fetch_all(MYSQLI_ASSOC);
$requests = $conn->query("SELECT * FROM service_requests")->fetch_all(MYSQLI_ASSOC);
$reviews = $conn->query("SELECT * FROM reviews")->fetch_all(MYSQLI_ASSOC);

echo json_encode([
    'users' => $users,
    'requests' => $requests,
    'reviews' => $reviews
]);
?>