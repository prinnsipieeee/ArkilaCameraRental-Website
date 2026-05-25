<?php
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $fullName = isset($_POST['full_name']) ? trim($_POST['full_name']) : '';
    $contactNumber = isset($_POST['contact_number']) ? trim($_POST['contact_number']) : '';
    $age = isset($_POST['age']) ? trim($_POST['age']) : '';
    $completeAddress = isset($_POST['complete_address']) ? trim($_POST['complete_address']) : '';
    $deliveryAddress = isset($_POST['delivery_address']) ? trim($_POST['delivery_address']) : '';
    $landmark = isset($_POST['landmark']) ? trim($_POST['landmark']) : '';
    $facebookLink = isset($_POST['facebook_link']) ? trim($_POST['facebook_link']) : '';
    $startDate = isset($_POST['start_date']) ? $_POST['start_date'] : '';
    $endDate = isset($_POST['end_date']) ? $_POST['end_date'] : '';
    $cameraModel = isset($_POST['camera_model']) ? trim($_POST['camera_model']) : '';
    $cameraBundle = isset($_POST['camera_bundle']) ? trim($_POST['camera_bundle']) : '';
    $paymentAmount = isset($_POST['payment_amount']) ? floatval($_POST['payment_amount']) : 0.0; // Ensure amount is a float

    // Check if email is empty
    if (empty($email)) {
        die('Email is required.');
    }

    // Initialize variables for uploaded files
    $idImagePath = '';
    $paymentProofPath = '';

    // Handle ID image upload
    if (isset($_FILES['id_image']) && $_FILES['id_image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/';

        // Ensure the upload directory exists
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Get file details
        $fileTmpPath = $_FILES['id_image']['tmp_name'];
        $fileName = uniqid() . '-' . $_FILES['id_image']['name'];
        $destination = $uploadDir . $fileName;

        // Move the file to the uploads directory
        if (move_uploaded_file($fileTmpPath, $destination)) {
            $idImagePath = $destination;
        } else {
            die('Error moving the uploaded ID image.');
        }
    } else {
        die('Error uploading the ID image.');
    }

    // Handle payment proof upload
    if (isset($_FILES['payment_proof']) && $_FILES['payment_proof']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['payment_proof']['tmp_name'];
        $fileName = uniqid() . '-' . $_FILES['payment_proof']['name'];
        $destination = $uploadDir . $fileName;

        // Move the file to the uploads directory
        if (move_uploaded_file($fileTmpPath, $destination)) {
            $paymentProofPath = $destination;
        } else {
            die('Error moving the uploaded payment proof.');
        }
    } else {
        die('Error uploading the payment proof.');
    }

    // Insert appointment into the database
    $query = "INSERT INTO user_appointments (email, full_name, contact_number, age, complete_address, delivery_address, landmark, facebook_link, start_date, end_date, camera_model, camera_bundle, id_image)
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($query);
    if ($stmt) {
        $stmt->bind_param(
            'sssssssssssss',
            $email,
            $fullName,
            $contactNumber,
            $age,
            $completeAddress,
            $deliveryAddress,
            $landmark,
            $facebookLink,
            $startDate,
            $endDate,
            $cameraModel,
            $cameraBundle,
            $idImagePath
        );

        if ($stmt->execute()) {
            $appointmentId = $stmt->insert_id; // Get the inserted appointment ID

            // Insert into sales table
            $salesQuery = "INSERT INTO sales (appointment_id, camera_model, payment_amount, payment_proof, date)
                           VALUES (?, ?, ?, ?, ?)";
            $salesStmt = $conn->prepare($salesQuery);
            if ($salesStmt) {
                $currentDate = date('Y-m-d');

                $salesStmt->bind_param(
                    'issss',
                    $appointmentId,
                    $cameraModel,
                    $paymentAmount,
                    $paymentProofPath,
                    $currentDate
                );

                if ($salesStmt->execute()) {
                    // Floating message and redirect
                    echo "<script>
                        alert('Appointment and sales saved successfully.');
                        window.location.href = 'package.html';
                    </script>";
                } else {
                    echo 'Error saving sales: ' . $salesStmt->error;
                }

                $salesStmt->close();
            } else {
                echo 'Error preparing sales statement: ' . $conn->error;
            }
        } else {
            echo 'Error saving appointment: ' . $stmt->error;
        }

        $stmt->close();
    } else {
        echo 'Error preparing the statement: ' . $conn->error;
    }

    $conn->close();
}
?>
