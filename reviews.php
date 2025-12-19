<?php
session_start();
require 'config.php';

$data = json_decode(file_get_contents('php://input'), true);
$request_id = $data['request_id'];
$rating = $data['rating'];
$comment = $data['comment'] ?? '';

$stmt = $conn->prepare("INSERT INTO reviews (request_id, rating, comment) VALUES (?, ?, ?)");
$stmt->bind_param("iis", $request_id, $rating, $comment);
$stmt->execute();

// Update average rating
$req = $conn->query("SELECT provider_id FROM service_requests WHERE id = $request_id")->fetch_assoc();
$provider_id = $req['provider_id'];
$avg = $conn->query("SELECT AVG(rating) as avg FROM reviews r 
                     JOIN service_requests sr ON r.request_id = sr.id 
                     WHERE sr.provider_id = $provider_id")->fetch_assoc();

$avg_rating = round($avg['avg'], 2);
$conn->query("UPDATE provider_profiles SET average_rating = $avg_rating WHERE user_id = $provider_id");

echo json_encode(['success' => true]);
?>