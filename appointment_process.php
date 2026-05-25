<?php
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $appointment_id = $_POST['appointment_id'];
    $camera_id = $_POST['camera_id'];
    $amount = $_POST['amount']; // Price for the rental
    $date = date('Y-m-d'); // Current date

    // Insert appointment details
    $sql_appointment = "INSERT INTO appointments (camera_id, date) VALUES ('$camera_id', '$date')";
    if (mysqli_query($conn, $sql_appointment)) {
        $appointment_id = mysqli_insert_id($conn);

        // Insert sales record
        $sql_sales = "INSERT INTO sales (appointment_id, camera_id, amount, date) 
                      VALUES ('$appointment_id', '$camera_id', '$amount', '$date')";
        if (mysqli_query($conn, $sql_sales)) {
            echo "Appointment and sales recorded successfully!";
        } else {
            echo "Error recording sales: " . mysqli_error($conn);
        }
    } else {
        echo "Error recording appointment: " . mysqli_error($conn);
    }
}
?>
