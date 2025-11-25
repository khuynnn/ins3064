<?php
session_start();
include("connection.php");

$message = "";

// Nếu user đã đăng nhập → chuyển thẳng đến dashboard
if (isset($_SESSION["user"])) {
    header("Location: dashboard.php");
    exit();
}

// Nếu user nhấn nút đăng nhập
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];

    $sql = "SELECT * FROM users 
            WHERE (email='$email' OR username='$email') 
            AND password='$password'";

    $result = mysqli_query($link, $sql);

    if (mysqli_num_rows($result) == 1) {
        $_SESSION["user"] = $email;
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
        <input type="text" name="email" placeholder="Email hoặc Username" required><br>
        <input type="password" name="password" placeholder="Mật khẩu" required><br>
        <button type="submit">Đăng nhập</button>
    </form>

    <p class="error"><?= $message ?></p>

    <p>Chưa có tài khoản? <a href="signup.php">Đăng ký ngay</a></p>
</div>
</body>
</html>
