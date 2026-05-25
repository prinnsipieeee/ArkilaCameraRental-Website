<?php
// Include the database connection
require_once 'db_connect.php';

// Query to fetch appointments (added camera_model field)
$sql = "SELECT id, full_name, start_date, end_date, camera_model FROM user_appointments WHERE start_date >= CURDATE()";
$result = $conn->query($sql);

$events = [];

// Convert data into FullCalendar event format
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Ensure correct date format for FullCalendar
        $start = $row['start_date'] . 'T00:00:00'; // Add time for full calendar all-day event
        $end = $row['end_date'] . 'T23:59:59'; // Set to the end of the day for the event
        
        $events[] = [
            'title' => 'Booked', // This will show the 'Booked' status
            'start' => $start, // The date the booking starts
            'end' => $end, // The date the booking ends
            'extendedProps' => [
                'full_name' => $row['full_name'], // Customer's name
                'camera_model' => $row['camera_model'], // Camera model
            ]
        ];
    }
}

// Return data as JSON
header('Content-Type: application/json');
echo json_encode($events);

$conn->close();
?>
