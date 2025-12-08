<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database configuration
$hostname = "localhost";
$username = "root";
$password = "";
$database = "book_management";

// Connect to MySQL database
$conn = mysqli_connect($hostname, $username, $password, $database);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// UTF-8 cho tiếng Việt
mysqli_set_charset($conn, "utf8");
?>
