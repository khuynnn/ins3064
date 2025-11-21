<?php
session_start();

// Nếu đã đăng nhập thì chuyển đến Dashboard
if (isset($_SESSION["user"])) {
    header("Location: dashboard.php");
    exit();
} else {
    // Nếu chưa đăng nhập thì chuyển đến trang Login
    header("Location: login.php");
    exit();
}
?>
