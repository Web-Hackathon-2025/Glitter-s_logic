<?php
session_start();
require 'config.php';

$method = $_SERVER['REQUEST_METHOD'];
$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    echo json_encode(['success' => false, 'message' => 'Login required']);
    exit();
}

if ($method === 'GET') {
    if ($_SESSION['role'] === 'customer') {
        $stmt = $conn->prepare("SELECT r.*, u.name as provider_name FROM service_requests r 
                                JOIN users u ON r.provider_id = u.id WHERE r.customer_id = ?");
        $stmt->bind_param("i", $user_id);
    } else {
        $stmt = $conn->prepare("SELECT r.*, u.name as customer_name FROM service_requests r 
                                JOIN users u ON r.customer_id = u.id WHERE r.provider_id = ?");
        $stmt->bind_param("i", $user_id);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $requests = $result->fetch_all(MYSQLI_ASSOC);
    echo json_encode($requests);

} elseif ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $provider_id = $data['provider_id'];
    $service_name = $data['service_name'];
    $details = $data['details'];
    $requested_date = $data['requested_date'];

    $stmt = $conn->prepare("INSERT INTO service_requests (customer_id, provider_id, service_name, details, requested_date) 
                            VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iisss", $user_id, $provider_id, $service_name, $details, $requested_date);
    $stmt->execute();
    echo json_encode(['success' => true]);

} elseif ($method === 'PUT') {
    $data = json_decode(file_get_contents('php://input'), true);
    $request_id = $data['id'];
    $status = $data['status'];

    $stmt = $conn->prepare("UPDATE service_requests SET status = ? WHERE id = ? AND provider_id = ?");
    $stmt->bind_param("sii", $status, $request_id, $user_id);
    $stmt->execute();
    echo json_encode(['success' => true]);
}
?>