<?php
session_start();

$conn = mysqli_connect("localhost", "root", "", "karigar");
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE id = $user_id LIMIT 1";
$result = mysqli_query($conn, $sql);
if (!$result || mysqli_num_rows($result) == 0) {
    die("User not found.");
}

$user = mysqli_fetch_assoc($result);
$name = $user['name'];
$email = $user['email'];
$role = $user['role'];
$joined = date("F Y", strtotime($user['created_at']));
$username = explode('@', $email)[0];
$location = $user['location'] ?? "N/A";
$bio = $user['bio'] ?? "No bio provided.";
$profile_image = $user['profile_image'] ?? "../images/profileImage.jpg";

$bookings = 0;
$reviews = 0;
$rating = "4.5 ⭐";
$material_requests = 0;
$activity_log = [];

if ($role == "customer") {
    $sql = "SELECT COUNT(*) AS total FROM service_requests WHERE customer_name = '$name'";
    $res = mysqli_query($conn, $sql);
    if ($row = mysqli_fetch_assoc($res)) $bookings = $row['total'];

    $sql = "SELECT COUNT(*) AS total FROM orders WHERE customer_name = '$name'";
    $res = mysqli_query($conn, $sql);
    if ($row = mysqli_fetch_assoc($res)) $material_requests = $row['total'];

    $activity_log[] = "Requested plumbing service";
    $activity_log[] = "Ordered wall paint";
    $activity_log[] = "Reviewed AC repair";
} elseif ($role == "serviceprovider") {
    $sql = "SELECT COUNT(*) AS total FROM service_requests"; 
    $res = mysqli_query($conn, $sql);
    if ($row = mysqli_fetch_assoc($res)) $bookings = $row['total'];

    $reviews = 7;
    $activity_log[] = "Completed plumbing job for Ali";
    $activity_log[] = "Received 5★ rating";
    $activity_log[] = "Accepted AC service request";
} elseif ($role == "supplier") {
    $sql = "SELECT COUNT(*) AS total FROM orders"; 
    $res = mysqli_query($conn, $sql);
    if ($row = mysqli_fetch_assoc($res)) $material_requests = $row['total'];

    $activity_log[] = "Shipped 20 PVC pipes";
    $activity_log[] = "Restocked Cement (50kg)";
    $activity_log[] = "Delivered wall paint to DHA";
}

$dashboard_url = "";
if ($role === "customer") {
    $dashboard_url = "user-dashboard.php";
} elseif ($role === "provider") {
    $dashboard_url = "services-dashboard.php";
} elseif ($role === "supplier") {
    $dashboard_url = "material-dashboard.php";
}

mysqli_close($conn);

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Karigar - User Profile</title>
  <link rel="stylesheet" href="../style/profile.css" />
</head>
<body>
  <div class="profile-container">
    <aside class="sidebar">
      <h2>Karigar</h2>
      <nav class="sidebar-nav">
        <a href="<?= htmlspecialchars($dashboard_url) ?>">Dashboard</a>

        <?php if ($role === 'customer'): ?>
          <a href="../services.html">Services</a>
          <a href="../material.html">Materials</a>
        <?php endif; ?>

        <a class="active" href="#">Profile</a>
        <a href="#" id="logoutLink">Logout</a>
      </nav>
    </aside>

    <main class="profile-main">
      <div class="profile-header">
        <div class="profile-image">
          <img src="<?= htmlspecialchars($profile_image) ?>" alt="User Photo" />
        </div>
        <div class="profile-info">
          <h2><?= htmlspecialchars($name) ?> <span class="username">@<?= htmlspecialchars($username) ?></span></h2>
          <p class="location">📍 <?= htmlspecialchars($location) ?> · Joined <?= htmlspecialchars($joined) ?></p>
          <p class="bio"><?= htmlspecialchars($bio) ?></p>
          <div class="profile-stats">
            <div><strong><?= intval($bookings) ?></strong><span>Bookings</span></div>
            <div><strong><?= htmlspecialchars($rating) ?></strong><span>Rating</span></div>
            <div><strong><?= intval($reviews) ?></strong><span>Reviews</span></div>
          </div>
          <div class="profile-actions">
            <button class="edit-btn">Edit Profile</button>
          </div>
        </div>
      </div>

      <section class="profile-stats">
        <div class="stat-box">
          <h3>Bookings</h3>
          <p><?= intval($bookings) ?></p>
        </div>
        <div class="stat-box">
          <h3>Reviews</h3>
          <p><?= intval($reviews) ?></p>
        </div>
        <div class="stat-box">
          <h3>Material Requests</h3>
          <p><?= intval($material_requests) ?></p>
        </div>
      </section>

      <section class="activity-log">
        <h3>Recent Activity</h3>
        <ul id="activity-list">
          <?php foreach ($activity_log as $activity): ?>
            <li><?= htmlspecialchars($activity) ?></li>
          <?php endforeach; ?>
        </ul>
      </section>
    </main>
  </div>

  <!-- Modal -->
  <div id="editProfileModal" class="custom-modal">
    <div class="custom-modal-content">
      <span class="custom-close" id="closeEditModal">&times;</span>
      <h2>Edit Profile</h2>
        <form id="editProfileForm" method="POST" action="update-profile.php" enctype="multipart/form-data">
        <input name="name" type="text" id="editName" placeholder="Your Name" value="<?= htmlspecialchars($name) ?>" required />
        <input name="username" type="text" id="editUsername" placeholder="Username" value="<?= htmlspecialchars($username) ?>" required />
        <input name="location" type="text" id="editLocation" placeholder="City" value="<?= htmlspecialchars($location) ?>" />
        <textarea name="bio" id="editBio" placeholder="Short Bio..."><?= htmlspecialchars($bio) ?></textarea>
        <button type="submit" class="btn full-width">Save Changes</button>
      </form>
    </div>
  </div>

  <script>
    const logoutLink = document.getElementById('logoutLink');
    logoutLink?.addEventListener('click', (e) => {
      e.preventDefault();
      fetch('../php/logout.php')
        .then(() => window.location.href = '../index.html');
    });
  </script>
  <script src="../js/profile.js"></script>
</body>
</html>
