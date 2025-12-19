<?php
session_start();

$server = "localhost";
$username = "root";
$password = "";
$dbname = "karigar";

Connect to DB
$conn = mysqli_connect($server, $username, $password, $dbname);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if (!isset($_SESSION['user_id'])) {
    die("User not logged in.");
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

Fetch service requests
$sql_services = "
  SELECT sr.id, s.title AS service_title, sr.customer_name, sr.requested_time, sr.status
  FROM service_requests sr
  JOIN services s ON sr.service_id = s.id
  WHERE sr.customer_name = ?
  ORDER BY sr.created_at DESC
  LIMIT 5";

$stmt = $conn->prepare($sql_services);
if (!$stmt) {
    die("Prepare failed for services: (" . $conn->errno . ") " . $conn->error);
}
$stmt->bind_param("s", $user_name);
$stmt->execute();
$result_services = $stmt->get_result();
$service_requests = $result_services->fetch_all(MYSQLI_ASSOC);
$stmt->close();

Fetch materials ordered (using orders table)
$sql_materials = "
  SELECT o.id AS order_id, m.name AS material_name, o.quantity, o.status
  FROM orders o
  JOIN materials m ON o.material_id = m.id
  WHERE o.customer_name = ?
  ORDER BY o.id DESC
  LIMIT 5";

$stmt = $conn->prepare($sql_materials);
if (!$stmt) {
    die("Prepare failed for materials: (" . $conn->errno . ") " . $conn->error);
}
$stmt->bind_param("s", $user_name);
$stmt->execute();
$result_materials = $stmt->get_result();
$material_orders = $result_materials->fetch_all(MYSQLI_ASSOC);
$stmt->close();

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Karigar | Customer Dashboard</title>
  <link rel="stylesheet" href="../style/dash.css" />
</head>
<body>
  <div class="dashboard-container">
    <aside class="sidebar">
      <h2 class="logo">Karigar</h2>
      <nav>
        <ul>
          <li><a href="#" class="active">Dashboard</a></li>
          <li><a href="../services.html">Services</a></li>
          <li><a href="../material.html">Materials</a></li>
          <li><a href="profile.php">Profile</a></li>
          <li><a href="#" id="logoutLink">Logout</a></li>
        </ul>
      </nav>
    </aside>

    <main class="main-content">
      <header class="dashboard-header">
        <h1>Welcome, Customer</h1>
        <p>Here's an overview of your service and material activity</p>
      </header>

      <section class="dashboard-section">
        <div class="section-header">
          <h2>Recent Bookings</h2>
          <a href="../services.html"><button class="btn">+ Book New Service</button></a>
        </div>
        <table class="order-table">
          <thead>
            <tr>
              <th>Service</th>
              <th>Provider</th>
              <th>Date</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($service_requests)): ?>
              <?php foreach ($service_requests as $request): ?>
                <tr>
                  <td><?= htmlspecialchars($request['service_title']) ?></td>
                  <td><?= htmlspecialchars($request['provider_name']) ?></td>
                  <td><?= htmlspecialchars($request['requested_time']) ?></td>
                  <td><?= htmlspecialchars($request['status']) ?></td>
                  <td>
                    <button class="btn btn-secondary">Invoice</button>
                    <?php if ($request['status'] === 'Completed'): ?>
                      <div class="star-rating" data-service-id="<?= $request['request_id'] ?>">
                        <?php for ($i=1; $i<=5; $i++): ?>
                          <span data-value="<?= $i ?>">☆</span>
                        <?php endfor; ?>
                      </div>
                    <?php elseif ($request['status'] === 'In Progress'): ?>
                      <button class="btn btn-reject">Cancel</button>
                    <?php else: ?>
                      <span style="color:gray;">No actions</span>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="5">No recent service bookings found.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </section>

      <section class="dashboard-section">
        <h2>Materials Ordered</h2>
        <table class="order-table">
          <thead>
            <tr>
              <th>Material</th>
              <th>Supplier</th>
              <th>Quantity</th>
              <th>Date</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($material_orders)): ?>
              <?php foreach ($material_orders as $order): ?>
                <tr>
                  <td><?= htmlspecialchars($order['material_name']) ?></td>
                  <td><?= htmlspecialchars($order['supplier_name']) ?></td>
                  <td><?= htmlspecialchars($order['quantity']) ?></td>
                  <td><?= htmlspecialchars($order['order_date']) ?></td>
                  <td><?= htmlspecialchars($order['status']) ?></td>
                  <td>
                    <button class="btn btn-secondary">Invoice</button>
                    <?php if ($order['status'] === 'Delivered'): ?>
                      <div class="star-rating" data-service-id="<?= $order['order_id'] ?>">
                        <?php for ($i=1; $i<=5; $i++): ?>
                          <span data-value="<?= $i ?>">☆</span>
                        <?php endfor; ?>
                      </div>
                    <?php elseif ($order['status'] === 'Pending'): ?>
                      <button class="btn btn-reject">Cancel</button>
                    <?php else: ?>
                      <span style="color:gray;">No actions</span>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="6">No material orders found.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </section>

      <section class="dashboard-section">
        <h2>Suggested Services</h2>
        <div class="materials-list">
          <div class="material-card">
            <h3>AC Repair</h3>
            <p>Starting from Rs. 1200</p>
            <p>Available: Today</p>
            <div class="card-actions">
              <button class="btn">Book Now</button>
            </div>
          </div>
          <div class="material-card">
            <h3>Carpenter</h3>
            <p>Starting from Rs. 1000</p>
            <p>Available: Tomorrow</p>
            <div class="card-actions">
              <button class="btn">Book Now</button>
            </div>
          </div>
        </div>
      </section>
    </main>
  </div>

  <script>
    const logoutLink = document.getElementById('logoutLink');
logoutLink?.addEventListener('click', (e) => {
  e.preventDefault();
  fetch('../php/logout.php')
    .then(() => window.location.href = '../index.html');
});
  </script>
  <script src="../js/user-dash.js"></script>
  
</body>
</html>
