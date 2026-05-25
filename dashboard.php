<?php
// Start the session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database connection settings
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "owner_database";

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to handle updates for each table
function updateTable($conn, $table, $data, $whereColumn) {
    unset($data['ajax_update'], $data['table']); // Clean extra flags

    $setValues = [];
    foreach ($data as $column => $value) {
        if (!empty($value)) {
            $setValues[] = "$column = '" . $conn->real_escape_string($value) . "'"; 
        }
    }

    // Check if we have set values to update
    if (count($setValues) === 0) {
        return ['status' => 'error', 'message' => 'No valid data to update.'];
    }

    $setValuesStr = implode(", ", $setValues);
    $whereValue = isset($data[$whereColumn]) ? $conn->real_escape_string($data[$whereColumn]) : null;
    
    if (!$whereValue) {
        return ['status' => 'error', 'message' => "Missing $whereColumn value for update"];
    }

    $updateQuery = "UPDATE $table SET $setValuesStr WHERE $whereColumn = '$whereValue'";

    if ($conn->query($updateQuery) === TRUE) {
        return ['status' => 'success', 'message' => "Record updated successfully in $table"];
    } else {
        return ['status' => 'error', 'message' => "Error updating $table: " . $conn->error];
    }
}

// Handle AJAX update
if (isset($_POST['ajax_update'])) {
    $table = $_POST['table'];
    $data = $_POST;

    // Determine the appropriate column for the WHERE clause
    $whereColumn = isset($data['users_id']) ? 'users_id' : (isset($data['sale_id']) ? 'sale_id' : 'appointment_id');
    
    $updateResult = updateTable($conn, $table, $data, $whereColumn);
    
    header('Content-Type: application/json');
    echo json_encode($updateResult);
    exit; 
}

// Fetch data for each table
$salesResult = $conn->query("SELECT * FROM sales");
$usersResult = $conn->query("SELECT * FROM users");
$appointmentsResult = $conn->query("SELECT * FROM user_appointments");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Owner Dashboard - Camera Rental</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            margin: 0;
            background-color: #f9f9f9;
            color: #333;
        }

        .sidebar {
            width: 250px;
            background-color: #333;
            color: white;
            padding-top: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
        }

        .sidebar h2 {
            margin-bottom: 30px;
            font-size: 24px;
        }

        .sidebar button {
            width: 80%;
            background-color: #444;
            color: white;
            padding: 15px;
            border: none;
            margin: 5px 0;
            cursor: pointer;
            text-align: left;
        }

        .sidebar button:hover {
            background-color: #555;
        }

        .content {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ccc;
        }

        th {
            background-color: #4CAF50;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .form-input {
            padding: 5px;
            margin: 2px;
            border: 1px solid #ccc;
            border-radius: 3px;
            width: 90%;
        }

        .update-btn {
            padding: 5px 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }

        .update-btn:hover {
            background-color: green;
        }

        .table-section {
            display: none;
            width: 100%;
            margin-top: 50px;
        }
    </style>
    <script>
        function toggleTable(tableId) {
            var tables = document.querySelectorAll('.table-section');
            tables.forEach(table => table.style.display = 'none');
            document.getElementById(tableId).style.display = 'block';
        }

        function updateRecord(form, tableName) {
            const formData = new FormData(form);
            formData.append('ajax_update', true);
            formData.append('table', tableName);

            fetch('dashboard.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    alert(data.message);
                    location.reload();
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });

            return false;
        }
    </script>
</head>
<body>

<div class="sidebar">
    <h2>Dashboard</h2>
    <button onclick="toggleTable('sales')">💵 Sales</button>
    <button onclick="toggleTable('users')">👥 Users</button>
    <button onclick="toggleTable('appointments')">📅 User Appointments</button>
    <button onclick="window.location.href='sales.html';">📊 Sales Dashboard</button>
    <button onclick="window.location.href='display.php';">📤 Upload Picture/Payment Proof</button>
    <form action="logout.php" method="POST">
        <button type="submit" class="update-btn" style="width: 100%; background-color: red;">Logout</button>
    </form>
</div>

<div class="content">
    <h1>Welcome!</h1>

    <!-- Sales Table -->
<div id="sales" class="table-section">
    <h2>Sales</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Appointment ID</th>
            <th>Camera Model</th>
            <th>Date</th>
            <th>Payment Amount</th>
            <th>Payment Proof</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = $salesResult->fetch_assoc()): ?>
            <form onsubmit="return updateRecord(this, 'sales')">
                <tr>
                    <td><input type="text" name="sale_id" value="<?= $row['id'] ?>" readonly /></td>
                    <td><input type="text" name="appointment_id" value="<?= $row['appointment_id'] ?>" /></td>
                    <td><input type="text" name="camera_model" value="<?= $row['camera_model'] ?>" /></td>
                    <td><input type="date" name="date" value="<?= date('Y-m-d', strtotime($row['date'])) ?>" /></td>
                    <td><input type="number" step="0.01" name="payment_amount" value="<?= $row['payment_amount'] ?>" /></td>
                    <td><input type="text" name="payment_proof" value="<?= $row['payment_proof'] ?>" /></td>
                    <td><button type="submit" class="update-btn">Update</button></td>
                </tr>
            </form>
        <?php endwhile; ?>
    </table>
</div>


    <!-- Users Table -->
    <div id="users" class="table-section">
        <h2>Users</h2>
        <table>
            <tr>
                <th>ID</th><th>Username</th><th>Email</th><th>Password</th><th>Actions</th>
            </tr>
            <?php while ($row = $usersResult->fetch_assoc()): ?>
                <form onsubmit="return updateRecord(this, 'users')">
                    <tr>
                        <td><input type="text" name="users_id" value="<?= $row['id'] ?>" readonly /></td>
                        <td><input type="text" name="username" value="<?= $row['username'] ?>" /></td>
                        <td><input type="email" name="email" value="<?= $row['email'] ?>" /></td>
                        <td><input type="text" name="password" value="<?= $row['password'] ?>" /></td>
                        <td><button type="submit" class="update-btn">Update</button></td>
                    </tr>
                </form>
            <?php endwhile; ?>
        </table>
    </div>

    <!-- User Appointments Table -->
<div id="appointments" class="table-section">
    <h2>User Appointments</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Email</th>
            <th>Full Name</th>
            <th>Contact Number</th>
            <th>Age</th>
            <th>Complete Address</th>
            <th>Delivery Address</th>
            <th>Landmark</th>
            <th>Facebook Link</th>
            <th>Created At</th>
            <th>Start Date</th>
            <th>End Date</th>
            <th>Camera Model</th>
            <th>Camera Bundle</th>
            <th>ID Image</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = $appointmentsResult->fetch_assoc()): ?>
            <form onsubmit="return updateRecord(this, 'user_appointments')">
                <tr>
                    <td><input type="text" name="appointment_id" value="<?= $row['id'] ?>" readonly /></td>
                    <td><input type="email" name="email" value="<?= $row['email'] ?>" /></td>
                    <td><input type="text" name="full_name" value="<?= $row['full_name'] ?>" /></td>
                    <td><input type="text" name="contact_number" value="<?= $row['contact_number'] ?>" /></td>
                    <td><input type="number" name="age" value="<?= $row['age'] ?>" /></td>
                    <td><textarea name="complete_address"><?= $row['complete_address'] ?></textarea></td>
                    <td><textarea name="delivery_address"><?= $row['delivery_address'] ?></textarea></td>
                    <td><textarea name="landmark"><?= $row['landmark'] ?></textarea></td>
                    <td><input type="url" name="facebook_link" value="<?= $row['facebook_link'] ?>" /></td>
                    <td><input type="text" name="created_at" value="<?= $row['created_at'] ?>" readonly /></td>
                    <td><input type="date" name="start_date" value="<?= date('Y-m-d', strtotime($row['start_date'])) ?>" /></td>
                    <td><input type="date" name="end_date" value="<?= date('Y-m-d', strtotime($row['end_date'])) ?>" /></td>
                    <td><input type="text" name="camera_model" value="<?= $row['camera_model'] ?>" /></td>
                    <td><input type="text" name="camera_bundle" value="<?= $row['camera_bundle'] ?>" /></td>
                    <td><input type="text" name="id_image" value="<?= $row['id_image'] ?>" /></td>
                    <td><button type="submit" class="update-btn">Update</button></td>
                </tr>
            </form>
        <?php endwhile; ?>
    </table>
</div>


</body>
</html>
