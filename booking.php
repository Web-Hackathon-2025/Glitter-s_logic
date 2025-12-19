<?php
header('Content-Type: application/json');

$conn = mysqli_connect("localhost", "root", "", "karigar");
if (!$conn) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Database connection failed."]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

$materialId = (int)($data['materialId'] ?? 0);
$quantity = (int)($data['quantity'] ?? 0);
$location = mysqli_real_escape_string($conn, $data['location'] ?? '');

if ($materialId <= 0 || $quantity <= 0 || $location == '') {
    echo json_encode(["success" => false, "message" => "Invalid input."]);
    exit;
}

$stockResult = mysqli_query($conn, "SELECT stock FROM materials WHERE id = $materialId");
if (!$stockResult || mysqli_num_rows($stockResult) == 0) {
    echo json_encode(["success" => false, "message" => "Material not found."]);
    exit;
}
$row = mysqli_fetch_assoc($stockResult);
if ($quantity > $row['stock']) {
    echo json_encode(["success" => false, "message" => "Not enough stock available."]);
    exit;
}

$insert = mysqli_query($conn, "INSERT INTO bookings (material_id, quantity, location) VALUES ($materialId, $quantity, '$location')");

if ($insert) {
    mysqli_query($conn, "UPDATE materials SET stock = stock - $quantity WHERE id = $materialId");
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "message" => "Booking failed."]);
}

mysqli_close($conn);
?>
