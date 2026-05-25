<?php
// Database connection
include 'db_connect.php';

header('Content-Type: application/json');

try {
    // Query for daily sales
    $query = "SELECT DATE(date) AS date, SUM(payment_amount) AS total_sales 
              FROM sales 
              GROUP BY DATE(date) 
              ORDER BY DATE(date) ASC";

    $result = $conn->query($query);
    $salesData = [];

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $salesData[] = [
                'date' => $row['date'],
                'total_sales' => (float) $row['total_sales'] // Use the aggregate result
            ];
        }
    }

    echo json_encode($salesData);
} catch (mysqli_sql_exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
