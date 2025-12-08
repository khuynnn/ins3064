<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

$add_error = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $pub_name = trim($_POST['name'] ?? '');
    $phone    = trim($_POST['phone'] ?? '');
    $address  = trim($_POST['address'] ?? '');

    if ($pub_name === "") {
        $add_error = "Vui lòng nhập tên nhà xuất bản.";
    } else {
        $stmt = $conn->prepare("INSERT INTO publishers (name, phone, address) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $pub_name, $phone, $address);

        if ($stmt->execute()) {
            $stmt->close();
            header("Location: publishers.php");
            exit();
        } else {
            $add_error = "Lỗi khi thêm nhà xuất bản: " . $conn->error;
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm nhà xuất bản</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="nav">
    <a href="dashboard.php">Tổng quan</a> |
    <a href="books.php">Sách</a> |
    <a href="categories.php">Danh mục</a> |
    <a href="publishers.php">Nhà xuất bản</a> |
    <a href="loans.php">Mượn/Trả sách</a> |
    <a href="logout.php">Đăng xuất</a>
</div>

<div class="container">
    <h1>Thêm nhà xuất bản mới</h1>

    <?php if (!empty($add_error)): ?>
        <p class="error"><?php echo htmlspecialchars($add_error); ?></p>
    <?php endif; ?>

    <form method="post" action="">
        <label for="name">Tên nhà xuất bản:</label><br>
        <input type="text" id="name" name="name" required><br><br>

        <label for="phone">Số điện thoại:</label><br>
        <input type="text" id="phone" name="phone" placeholder="VD: 090xxxxxxx"><br><br>

        <label for="address">Địa chỉ:</label><br>
        <input type="text" id="address" name="address" placeholder="VD: Hà Nội, VN"><br><br>

        <button type="submit">Thêm</button>
        <a href="publishers.php" style="margin-left:10px;">Quay lại</a>
    </form>
</div>
</body>
</html>
