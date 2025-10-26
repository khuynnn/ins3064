<?php
session_start();

// Kiểm tra đăng nhập
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Kết nối CSDL
$con = mysqli_connect('localhost', 'root', '', 'loginreg');
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

// Lấy thông tin user
$username = $_SESSION['username'];
$sql = "SELECT * FROM userreg WHERE name='$username'";
$result = mysqli_query($con, $sql);
if (mysqli_num_rows($result) == 1) {
    $data = mysqli_fetch_assoc($result);
} else {
    echo "User not found!";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome <?php echo htmlspecialchars($data['name']); ?></title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet"
          href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
          integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T"
          crossorigin="anonymous">
</head>

<body>
    <div class="container">
        <div class="home-box text-center">
            <h1 class="mb-4">🎉 Welcome!</h1>
        
            <p><strong>Student ID:</strong> <?php echo htmlspecialchars($data['student_id']); ?></p>
            <p><strong>Class Name:</strong> <?php echo htmlspecialchars($data['class_name']); ?></p>
            <p><strong>Country:</strong> <?php echo htmlspecialchars($data['country']); ?></p>

            <a href="logout.php" class="btn btn-danger mt-3">Logout</a>
        </div>
    </div>
</body>
</html>
