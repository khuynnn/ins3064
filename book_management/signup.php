<?php
include("connection.php");
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = trim($_POST["fullname"]);
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];

    // Kiểm tra xác nhận mật khẩu
    if ($password !== $confirm_password) {
        $message = "Mật khẩu xác nhận không khớp!";
    } else {
        // Kiểm tra trùng username hoặc email
        $checkUser = mysqli_query($link, "SELECT * FROM users WHERE username='$username' OR email='$email'");
        if (mysqli_num_rows($checkUser) > 0) {
            $message = "Username hoặc Email đã tồn tại!";
        } else {
            // Thêm người dùng mới
            mysqli_query($link, "INSERT INTO users (fullname, username, email, password) 
                                VALUES ('$fullname', '$username', '$email', '$password')");
            $message = "Đăng ký thành công! <a href='index.php'>Đăng nhập ngay</a>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sign Up</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h2>Đăng ký tài khoản</h2>
    <form method="POST">
        <input type="text" name="fullname" placeholder="Họ và tên" required><br>
        <input type="text" name="username" placeholder="Tên đăng nhập" required><br>
        <input type="email" name="email" placeholder="Email" required><br>
        <input type="password" name="password" placeholder="Mật khẩu" required><br>
        <input type="password" name="confirm_password" placeholder="Xác nhận mật khẩu" required><br>
        <button type="submit">Đăng ký</button>
    </form>
    <p class="error"><?= $message ?></p>
    <p>Đã có tài khoản? <a href="index.php">Đăng nhập</a></p>
</div>
</body>
</html>
