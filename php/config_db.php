<?php
// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "Movie-Tube";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection and terminate if failed
if ($conn->connect_error) {
    die("Database connection failed");
}
?>