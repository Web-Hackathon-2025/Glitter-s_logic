<?php
$server = "localhost";
$username = "root";
$password = "";
$dbname = "karigar";

$conn = new mysqli($server, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'addMaterial') {
        $name = $conn->real_escape_string($_POST['name']);
        $category = $conn->real_escape_string($_POST['category']);
        $price = floatval($_POST['price']);
        $stock = intval($_POST['stock']);

        $sql = "INSERT INTO materials (name, category, price, stock) 
                VALUES ('$name', '$category', $price, $stock)";
        echo $conn->query($sql) ? 'success' : 'error';
        exit;
    }

    if ($action === 'editMaterial') {
        $id = intval($_POST['id']);
        $name = $conn->real_escape_string($_POST['name']);
        $category = $conn->real_escape_string($_POST['category']);
        $price = floatval($_POST['price']);
        $stock = intval($_POST['stock']);

        $sql = "UPDATE materials SET name='$name', category='$category', price=$price, stock=$stock 
                WHERE id=$id";
        echo $conn->query($sql) ? 'success' : 'error';
        exit;
    }

    if ($action === 'deleteMaterial') {
        $id = intval($_POST['id']);
        $sql = "DELETE FROM materials WHERE id=$id";
        echo $conn->query($sql) ? 'success' : 'error';
        exit;
    }
    if ($action === 'updateOrderStatus') {
    $id = intval($_POST['id']);
    $status = $_POST['status'];

    $validStatuses = ['Pending', 'Accepted', 'Rejected', 'Shipped', 'Delivered'];
    if (!in_array($status, $validStatuses)) {
        echo "invalid";
        exit;
    }

    $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $id);
    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error";
    }
    exit;
}
}


$materials_sql = "SELECT * FROM materials";
$materials_result = $conn->query($materials_sql);

$orders_sql = "
SELECT orders.*, materials.name AS material_name 
FROM orders 
JOIN materials ON orders.material_id = materials.id
";
$orders_result = $conn->query($orders_sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Karigar | Materials Dashboard</title>
  <link rel="stylesheet" href="../style/dash.css" />
  <style>
    .modal { display: none; position: fixed; z-index: 1000; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); }
    .modal-content { background: white; padding: 20px; margin: 10% auto; width: 400px; border-radius: 8px; position: relative; }
    .close { position: absolute; top: 10px; right: 20px; font-size: 24px; cursor: pointer; }
  </style>
</head>
<body>
<div class="dashboard-container">
  <aside class="sidebar">
    <h2 class="logo">Karigar</h2>
    <nav>
      <ul>
        <li><a href="#" class="active">Dashboard</a></li>
        <li><a href="profile.php">Profile</a></li>
        <li><a href="#" id="logoutLink">Logout</a></li>
      </ul>
    </nav>
  </aside>

  <main class="main-content">
    <header class="dashboard-header">
      <h1>Welcome to the Material Dashboard</h1>
      <p>Manage available materials and track customer orders</p>
    </header>

    <section class="dashboard-section">
      <div class="section-header">
        <h2>Materials</h2>
        <button id="addMaterialBtn" class="btn">+ Add New Material</button>
      </div>
      <div class="materials-list" id="materialsList">
        <?php while ($row = $materials_result->fetch_assoc()): ?>
          <div class="material-card" data-id="<?= $row['id'] ?>">
            <h3><?= htmlspecialchars($row['name']) ?></h3>
            <p>Category: <?= htmlspecialchars($row['category']) ?></p>
            <p>Price: Rs. <?= number_format($row['price']) ?></p>
            <p>Stock: <?= (int)$row['stock'] ?> units</p>
            <div class="card-actions">
              <button class="btn edit" onclick="openEditModal(<?= htmlspecialchars(json_encode($row)) ?>)">Edit</button>
              <button class="btn delete" onclick="deleteMaterial(<?= $row['id'] ?>)">Delete</button>
            </div>
          </div>
        <?php endwhile; ?>
      </div>
    </section>

    <section class="dashboard-section">
      <h2>Customer Orders</h2>
      <table class="order-table">
        <thead>
        <tr>
          <th>Customer</th>
          <th>Material</th>
          <th>Quantity</th>
          <th>Location</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
        </thead>
        <tbody id="orderRequestTable">
        <?php
        $orders = $conn->query("
            SELECT o.*, m.name AS material_name 
            FROM orders o
            JOIN materials m ON o.material_id = m.id
        ");

        while ($order = $orders->fetch_assoc()): ?>
          <tr data-id="<?= $order['id'] ?>">
            <td><?= htmlspecialchars($order['customer_name']) ?></td>
            <td><?= htmlspecialchars($order['material_name']) ?></td>
            <td><?= htmlspecialchars($order['quantity']) ?></td>
            <td><?= htmlspecialchars($order['customer_address']) ?></td>
            <td><?= htmlspecialchars($order['status']) ?></td>
            <td>
              <?php if ($order['status'] === 'Pending'): ?>
                <button class="btn btn-accept" onclick="updateOrderStatus(<?= $order['id'] ?>, 'Accepted')">Accept</button>
                <button class="btn btn-reject" onclick="updateOrderStatus(<?= $order['id'] ?>, 'Rejected')">Reject</button>
              <?php elseif ($order['status'] === 'Accepted'): ?>
                <button class="btn btn-ship" onclick="updateOrderStatus(<?= $order['id'] ?>, 'Shipped')">Mark Shipped</button>
              <?php elseif ($order['status'] === 'Shipped'): ?>
                <button class="btn btn-deliver" onclick="updateOrderStatus(<?= $order['id'] ?>, 'Delivered')">Mark Delivered</button>
              <?php endif; ?>
              <button class="btn btn-secondary" onclick="showCustomerInfo('<?= $order['customer_name'] ?>', '<?= $order['customer_phone'] ?>', '<?= $order['customer_address'] ?>')">Customer Info</button>
            </td>
          </tr>
          <?php endwhile; ?>

        </tbody>
      </table>
    </section>
  </main>
</div>

<!-- Add/Edit Material Modal -->
<div id="materialModal" class="modal">
  <div class="modal-content">
    <span class="close" onclick="closeModal()">&times;</span>
    <h2 id="modalTitle">Add New Material</h2>
    <form id="materialForm">
      <input type="hidden" name="id" id="materialId" />
      <input type="text" name="name" id="materialName" placeholder="Material Name" required />
      <select name="category" id="materialCategory" required>
        <option value="">Select Category</option>
        <option value="Cement">Cement</option>
        <option value="Bricks">Bricks</option>
        <option value="Tiles">Tiles</option>
        <option value="Paint">Paint</option>
        <option value="Pipes">Pipes</option>
      </select>
      <input type="number" name="price" id="materialPrice" placeholder="Price (Rs.)" required />
      <input type="number" name="stock" id="materialStock" placeholder="Stock (Units)" required />
      <button type="submit" class="btn full-width">Save Material</button>
    </form>
  </div>
</div>

<!-- Customer Info Modal -->
<div id="customerModal" class="modal">
  <div class="modal-content">
    <span class="close" onclick="document.getElementById('customerModal').style.display='none'">&times;</span>
    <h2>Customer Info</h2>
    <p><strong>Name:</strong> <span id="custName"></span></p>
    <p><strong>Phone:</strong> <span id="custPhone"></span></p>
    <p><strong>Address:</strong> <span id="custAddress"></span></p>
  </div>
</div>

<script src="../js/dashboard.js"></script>
</body>
</html>
<?php $conn->close(); ?>
