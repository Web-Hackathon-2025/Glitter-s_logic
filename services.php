<?php
session_start();
require 'config.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    // Search providers
    $category = $_GET['category'] ?? '';
    $location = $_GET['location'] ?? '';

    $sql = "SELECT u.id, u.name, u.email, u.location, p.services, p.average_rating 
            FROM users u 
            LEFT JOIN provider_profiles p ON u.id = p.user_id 
            WHERE u.role = 'provider' AND u.location LIKE ?";
    $stmt = $conn->prepare($sql);
    $loc = "%$location%";
    $stmt->bind_param("s", $loc);
    $stmt->execute();
    $result = $stmt->get_result();
    $providers = [];

    while ($row = $result->fetch_assoc()) {
        if ($category === '' || strpos(json_encode($row['services']), $category) !== false) {
            $providers[] = $row;
        }
    }
    echo json_encode($providers);

} elseif ($method === 'POST' || $method === 'PUT') {
    // Save provider profile
    if (!isset($_SESSION['user_id'])) exit(json_encode(['success' => false]));

    $data = json_decode(file_get_contents('php://input'), true);
    $services = json_encode($data['services'] ?? []);
    $availability = json_encode($data['availability'] ?? []);

    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("INSERT INTO provider_profiles (user_id, services, availability) 
                            VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE services = ?, availability = ?");
    $stmt->bind_param("issss", $user_id, $services, $availability, $services, $availability);
    $stmt->execute();

    echo json_encode(['success' => true]);
}
?>