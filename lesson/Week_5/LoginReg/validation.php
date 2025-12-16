<?php
session_start();

$con = mysqli_connect('localhost', 'root', '', 'loginreg');
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

$name = mysqli_real_escape_string($con, $_POST['user']);
$pass = mysqli_real_escape_string($con, $_POST['password']);

// Lấy thông tin user từ database
$sql = "SELECT * FROM userreg WHERE name='$name'";
$result = mysqli_query($con, $sql);

if (mysqli_num_rows($result) == 1) {
    $row = mysqli_fetch_assoc($result);

    // So sánh password người dùng nhập với password mã hóa trong DB
    if (password_verify($pass, $row['password'])) {
        $_SESSION['username'] = $name;
        header("Location: home.php");
        exit();
    } else {
        header("Location: login.php?error=1");
        exit();
    }
} else {
    header("Location: login.php?error=1");
    exit();
}
?>
