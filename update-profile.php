<?php
session_start();
header('Content-Type: application/json');

$response = ['success' => false];

if (!isset($_SESSION['user_id'])) {
    $response['error'] = 'User not logged in.';
    echo json_encode($response);
    exit;
}

$conn = mysqli_connect("localhost", "root", "", "karigar");
if (!$conn) {
    $response['error'] = 'Database connection failed.';
    echo json_encode($response);
    exit;
}

$user_id = $_SESSION['user_id'];
$name = trim($_POST['name'] ?? '');
$location = trim($_POST['location'] ?? '');
$bio = trim($_POST['bio'] ?? '');

if (empty($name)) {
    $response['error'] = 'Name is required.';
    echo json_encode($response);
    exit;
}

$sql = "UPDATE users SET name = ?, location = ?, bio = ? WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "sssi", $name, $location, $bio, $user_id);

if (mysqli_stmt_execute($stmt)) {
    $response['success'] = true;
} else {
    $response['error'] = 'Failed to update profile.';
}

mysqli_stmt_close($stmt);
mysqli_close($conn);

echo json_encode($response);
?>
