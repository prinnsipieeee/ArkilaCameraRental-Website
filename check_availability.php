<?php
$connection = new mysqli('localhost', 'root', '', 'owner_database');

$booking_date = $_POST['booking_date'];

$query = "SELECT c.camera_id, c.camera_model, c.quantity - 
          COALESCE(SUM(b.quantity_booked), 0) AS available
          FROM cameras c
          LEFT JOIN calendar_bookings b 
          ON c.camera_id = b.camera_id AND b.booking_date = '$booking_date'
          GROUP BY c.camera_id, c.camera_model";

$result = $connection->query($query);

$output = "<h3>Available Cameras on " . $booking_date . "</h3><ul>";
while ($row = $result->fetch_assoc()) {
    $output .= "<li>" . $row['camera_model'] . ": " . $row['available'] . " available</li>";
}
$output .= "</ul>";

echo $output;
?>
