<?php
$connection = new mysqli('localhost', 'root', '', 'owner_database');

$query = "SELECT b.*, c.camera_model FROM bookings
          JOIN cameras c ON b.camera_id = c.camera_id";
$result = $connection->query($query);

$events = [];
while ($row = $result->fetch_assoc()) {
    $events[] = [
        'title' => $row['camera_model'],
        'start' => $row['booking_date'] . 'T' . $row['booking_time'],
        'quantity' => $row['quantity_booked'],
        'status' => $row['status']
    ];
}

echo json_encode($events);
?>
