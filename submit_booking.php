<?php
$connection = new mysqli('localhost', 'root', '', 'owner_database');

$camera_model = $_POST['camera_model'];
$quantity = $_POST['quantity'];
$booking_date = $_POST['booking_date'];
$booking_time = $_POST['booking_time'];
$customer_name = $_POST['customer_name'];

// Get camera ID by model
$camera_query = "SELECT camera_id FROM cameras WHERE camera_model = '$camera_model'";
$camera_result = $connection->query($camera_query);
$camera_row = $camera_result->fetch_assoc();
$camera_id = $camera_row['camera_id'];

// Insert booking
$booking_query = "INSERT INTO calendar_bookings (camera_id, customer_name, booking_date, booking_time, quantity_booked, status)
                  VALUES ('$camera_id', '$customer_name', '$booking_date', '$booking_time', '$quantity', 'Pending')";

if ($connection->query($booking_query)) {
    echo "Booking successful!";
} else {
    echo "Error: " . $connection->error;
}
?>
