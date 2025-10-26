<?php
session_start();

/* Kết nối đến database */
$con = mysqli_connect('localhost', 'root', '', 'loginreg');
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

/* Lấy dữ liệu từ form và xử lý an toàn */
$student_id = mysqli_real_escape_string($con, $_POST['student_id'] ?? '');
$class_name = mysqli_real_escape_string($con, $_POST['class_name'] ?? '');
$country = mysqli_real_escape_string($con, $_POST['country'] ?? '');
$name = mysqli_real_escape_string($con, $_POST['user'] ?? '');
$pass = mysqli_real_escape_string($con, $_POST['password'] ?? '');

/* Kiểm tra username đã tồn tại chưa */
$s = "SELECT * FROM userreg WHERE name='$name'";
$result = mysqli_query($con, $s);

if (mysqli_num_rows($result) > 0) {
    echo "<script>alert('Username already exists!'); window.location='login.php';</script>";
    exit();
}

/* Mã hóa mật khẩu */
$hashed_pass = password_hash($pass, PASSWORD_DEFAULT);

/* Thêm user mới vào DB */
$reg = "INSERT INTO userreg (student_id, class_name, country, name, password)
        VALUES ('$student_id', '$class_name', '$country', '$name', '$hashed_pass')";

if (mysqli_query($con, $reg)) {
    echo "<script>alert('Registration successful! Redirecting to login page...'); window.location='login.php';</script>";
    exit();
} else {
    echo "Error: " . mysqli_error($con);
}

mysqli_close($con);
?>
