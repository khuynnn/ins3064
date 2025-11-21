<?php
session_start();
include("connection.php");

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    $result = mysqli_query($link, "SELECT * FROM users WHERE (username='$username') AND password='$password'");
    if (mysqli_num_rows($result) == 1) {
        $_SESSION["user"] = $username;
        header("Location: dashboard.php");
        exit();
    } else {
        $message = "Sai email hoặc mật khẩu!";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h2>Đăng nhập</h2>
    <form method="POST">
        <input type="text" name="username" placeholder="Username" required><br>
        <input type="password" name="password" placeholder="Mật khẩu" required><br>
        <button type="submit">Đăng nhập</button>
    </form>
    <p class="error"><?= $message ?></p>
    <p>Chưa có tài khoản? <a href="signup.php">Đăng ký ngay</a></p>
</div>
</body>
</html>
