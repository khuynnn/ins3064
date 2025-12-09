<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database configuration
$hostname = "sql100.infinityfree.com";
$username = "if0_40506385";
$password = "cQS41DJilwFGr";
$database = "if0_40506385_book_management";

// Connect to MySQL database
$conn = mysqli_connect($hostname, $username, $password, $database);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// UTF-8 cho tiếng Việt
mysqli_set_charset($conn, "utf8");
?>
