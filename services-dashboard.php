<?php
$server = "localhost";
$username = "root";
$password = "";
$dbname = "karigar";

session_start();

$conn = mysqli_connect($server, $username, $password, $dbname);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'addService') {
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $description = mysqli_real_escape_string($conn, $_POST['availability']);
        $price = floatval($_POST['rate']);

        $sql = "INSERT INTO services (title, description, price) 
                VALUES ('$name', '$description', $price)";
        echo mysqli_query($conn, $sql) ? 'success' : 'error';
        exit;
    }

    if ($action === 'editService') {
        $id = intval($_POST['id']);
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $description = mysqli_real_escape_string($conn, $_POST['availability']);
        $price = floatval($_POST['rate']);

        $sql = "UPDATE services SET title='$name', description='$description', price=$price 
                WHERE id=$id";
        echo mysqli_query($conn, $sql) ? 'success' : 'error';
        exit;
    }

    if ($action === 'deleteService') {
        $id = intval($_POST['id']);
        $sql = "DELETE FROM services WHERE id=$id";
        echo mysqli_query($conn, $sql) ? 'success' : 'error';
        exit;
    }

    if ($action === 'updateRequestStatus') {
        $id = intval($_POST['id']);
        $status = $_POST['status'];

        $validStatuses = ['Pending', 'Accepted', 'Rejected', 'Completed'];
        if (!in_array($status, $validStatuses)) {
            echo "invalid";
            exit;
        }

        $stmt = mysqli_prepare($conn, "UPDATE service_requests SET status = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "si", $status, $id);
        echo mysqli_stmt_execute($stmt) ? 'success' : 'error';
        mysqli_stmt_close($stmt);
        exit;
    }
}

$services = [];
$services_sql = "SELECT * FROM services";
$services_result = mysqli_query($conn, $services_sql);
if ($services_result) {
    $services = mysqli_fetch_all($services_result, MYSQLI_ASSOC);
} else {
    error_log("Services query failed: " . mysqli_error($conn));
}

$requests_sql = "
SELECT sr.id as request_id, sr.status, sr.requested_time, sr.customer_name, s.title as service_title
FROM service_requests sr
JOIN services s ON sr.service_id = s.id
";
$requests_result = mysqli_query($conn, $requests_sql);
$service_requests = [];
if ($requests_result) {
    $service_requests = mysqli_fetch_all($requests_result, MYSQLI_ASSOC);
} else {
    error_log("Service requests query failed: " . mysqli_error($conn));
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Karigar | Services Dashboard</title>
  <link rel="stylesheet" href="../style/dash.css" />
  <style>
    .modal { display: none; position: fixed; z-index: 1000; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); }
    .modal-content { background: white; padding: 20px; margin: 10% auto; width: 400px; border-radius: 8px; position: relative; }
    .close { position: absolute; top: 10px; right: 20px; font-size: 24px; cursor: pointer; }
    textarea { width: 100%; padding: 8px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 4px; }
  </style>
</head>
<body>
<div class="dashboard-container">
  <aside class="sidebar">
    <h2 class="logo">Karigar</h2>
    <nav>
      <ul>
        <li><a href="#">Dashboard</a></li>
        <li><a href="profile.php">Profile</a></li>
        <li><a href="#" id="logoutLink">Logout</a></li>
      </ul>
    </nav>
  </aside>

  <main class="main-content">
    <header class="dashboard-header">
      <h1>Welcome to the Services Dashboard</h1>
      <p>Manage your offered services and customer requests</p>
    </header>

    <section class="dashboard-section">
      <div class="section-header">
        <h2>Your Services</h2>
        <button id="addServiceBtn" class="btn">+ Add New Service</button>
      </div>
      <div class="materials-list" id="servicesList">
        <?php foreach ($services as $service): ?>
          <div class="material-card" data-id="<?= $service['id'] ?>">
            <h3><?= htmlspecialchars($service['title']) ?></h3>
            <p>Rate: Rs. <?= number_format($service['price'], 2) ?></p>
            <p>Availability: <?= htmlspecialchars($service['description']) ?></p>
            <div class="card-actions">
              <button class="btn edit" onclick="openEditServiceModal(<?= htmlspecialchars(json_encode($service)) ?>)">Edit</button>
              <button class="btn delete" onclick="deleteService(<?= $service['id'] ?>)">Delete</button>
            </div>
          </div>
        <?php endforeach; ?>
        <?php if (empty($services)): ?>
          <p>No services added yet.</p>
        <?php endif; ?>
      </div>
    </section>

    <section class="dashboard-section">
      <h2>Service Requests</h2>
      <table class="order-table">
        <thead>
        <tr>
          <th>Customer</th>
          <th>Service</th>
          <th>Requested Time</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
        </thead>
        <tbody id="serviceRequestTable">
        <?php $service_requests = $service_requests ?? [];
          foreach ($service_requests as $request): ?>
          <tr data-id="<?= $request['request_id'] ?>">
            <td><?= htmlspecialchars($request['customer_name']) ?></td>
            <td><?= htmlspecialchars($request['service_title']) ?></td>
            <td><?= htmlspecialchars($request['requested_time']) ?></td>
            <td><?= htmlspecialchars($request['status']) ?></td>
            <td>
              <?php if ($request['status'] === 'Pending'): ?>
                <button class="btn btn-accept" onclick="updateRequestStatus(<?= $request['request_id'] ?>, 'Accepted')">Accept</button>
                <button class="btn btn-reject" onclick="updateRequestStatus(<?= $request['request_id'] ?>, 'Rejected')">Reject</button>
              <?php elseif ($request['status'] === 'Accepted'): ?>
                <button class="btn btn-complete" onclick="updateRequestStatus(<?= $request['request_id'] ?>, 'Completed')">Mark Completed</button>
              <?php else: ?>
                <span style="color:gray;">No actions</span>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (empty($service_requests)): ?>
          <tr><td colspan="5">No service requests found.</td></tr>
        <?php endif; ?>
        </tbody>
      </table>
    </section>
  </main>
</div>

<!-- Add/Edit Service Modal -->
<div id="serviceModal" class="modal">
  <div class="modal-content">
    <span class="close" onclick="closeModal()">&times;</span>
    <h2 id="serviceModalTitle">Add New Service</h2>
    <form id="serviceForm">
      <input type="hidden" name="id" id="serviceId" />
      <input type="text" name="name" id="serviceName" placeholder="Service Name" required />
      <input type="number" name="rate" id="serviceRate" placeholder="Rate (Rs.)" step="0.01" required />
      <textarea name="availability" id="serviceAvailability" placeholder="Availability Description" required></textarea>
      <button type="submit" class="btn full-width">Save Service</button>
    </form>
  </div>
</div>
<script>
  document.addEventListener('DOMContentLoaded', () => {
  const addServiceBtn = document.getElementById('addServiceBtn');
  const serviceModal = document.getElementById('serviceModal');
  const serviceForm = document.getElementById('serviceForm');
  const serviceModalTitle = document.getElementById('serviceModalTitle');

  addServiceBtn?.addEventListener('click', () => {
    serviceModalTitle.textContent = 'Add New Service';
    serviceForm.reset();
    document.getElementById('serviceId').value = '';
    serviceModal.style.display = 'block';
  });

  window.closeModal = () => {
    serviceModal.style.display = 'none';
  };

  window.openEditServiceModal = (service) => {
    serviceModalTitle.textContent = 'Edit Service';
    document.getElementById('serviceId').value = service.id;
    document.getElementById('serviceName').value = service.title;
    document.getElementById('serviceRate').value = service.price;
    document.getElementById('serviceAvailability').value = service.description;
    serviceModal.style.display = 'block';
  };

  serviceForm?.addEventListener('submit', (e) => {
    e.preventDefault();
    const formData = new FormData(serviceForm);
    const action = formData.get('id') ? 'editService' : 'addService';

    fetch(window.location.href, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
      },
      body: new URLSearchParams({
        action: action,
        id: formData.get('id'),
        name: formData.get('name'),
        rate: formData.get('rate'),
        availability: formData.get('availability')
      })
    })
    .then(response => response.text())
    .then(result => {
      if (result === 'success') {
        window.location.reload();
      } else {
        alert('Error saving service');
      }
    });
  });

  window.deleteService = (id) => {
    if (confirm('Are you sure you want to delete this service?')) {
      fetch(window.location.href, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
          action: 'deleteService',
          id: id
        })
      })
      .then(response => response.text())
      .then(result => {
        if (result === 'success') {
          window.location.reload();
        } else {
          alert('Error deleting service');
        }
      });
    }
  };

  window.updateRequestStatus = (id, status) => {
    fetch(window.location.href, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
      },
      body: new URLSearchParams({
        action: 'updateRequestStatus',
        id: id,
        status: status
      })
    })
    .then(response => response.text())
    .then(result => {
      if (result === 'success') {
        window.location.reload();
      } else {
        alert('Error updating request status');
      }
    });
  };

  
  const logoutLink = document.getElementById('logoutLink');
  logoutLink?.addEventListener('click', (e) => {
    e.preventDefault();
    fetch('../php/logout.php').then(() => window.location.href = '../index.html');
  });
});

</script>
</body>
</html>
<?php $conn->close(); ?>