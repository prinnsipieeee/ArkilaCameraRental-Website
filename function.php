<?php
// Function to calculate total price
function calculateTotalPrice($bundle_id, $start_date, $end_date) {
    include 'db_connect.php'; // Include database connection

    // Fetch bundle prices
    $query = $conn->prepare("SELECT price_first_day, price_per_day FROM bundles WHERE id = ?");
    $query->bind_param("i", $bundle_id);
    $query->execute();
    $result = $query->get_result();
    $bundle = $result->fetch_assoc();

    $price_first_day = $bundle['price_first_day'];
    $price_per_day = $bundle['price_per_day'];

    // Calculate rental duration
    $start = new DateTime($start_date);
    $end = new DateTime($end_date);
    $days = $end->diff($start)->days + 1;

    // Calculate total price
    if ($days == 1) {
        $total_price = $price_first_day;
    } else {
        $total_price = $price_first_day + ($days - 1) * $price_per_day;
    }

    return $total_price;
}
?>
