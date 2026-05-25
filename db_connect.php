<?php
$servername = "localhost"; // Typically "localhost" when using XAMPP
$username = "root"; // Replace with your MySQL username, e.g., "root" for XAMPP
$password = ""; // Replace with your MySQL password (often empty by default in XAMPP)
$dbname = "owner_database"; // Name of your database

// Create a new connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
