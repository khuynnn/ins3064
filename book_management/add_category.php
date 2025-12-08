<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

$add_error = "";
$add_success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name'] ?? '');

    if ($name === "") {
        $add_error = "Vui lòng nhập tên danh mục.";
    } else {
        // Kiểm tra trùng tên
        $stmt_check = $conn->prepare("SELECT id FROM categories WHERE name = ?");
        $stmt_check->bind_param("s", $name);
        $stmt_check->execute();
        $stmt_check->store_result();

        if ($stmt_check->num_rows > 0) {
            $add_error = "Danh mục này đã tồn tại.";
        } else {
            $stmt = $conn->prepare("INSERT INTO categories (name) VALUES (?)");
            $stmt->bind_param("s", $name);
            if ($stmt->execute()) {
                $stmt->close();
                header("Location: categories.php");
                exit();
            } else {
                $add_error = "Lỗi khi thêm danh mục.";
            }
        }

        $stmt_check->close();
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm danh mục mới</title>
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
    <h1>Thêm danh mục mới</h1>

    <?php if (!empty($add_error)): ?>
        <p class="error"><?php echo $add_error; ?></p>
    <?php endif; ?>

    <?php if (!empty($add_success)): ?>
        <p class="success"><?php echo $add_success; ?></p>
    <?php endif; ?>

    <form method="post" action="">
        <label for="name">Tên danh mục:</label>
        <input type="text" id="name" name="name" required>

        <button type="submit">Lưu danh mục</button>
        <a href="categories.php">Quay lại</a>
    </form>
</div>
</body>
</html>
