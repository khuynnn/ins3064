<?php
// add_category.php - Trang thêm thể loại mới

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
if (empty($_SESSION['is_admin']) || $_SESSION['is_admin'] == 0) {
    die("Bạn không có quyền truy cập trang này.");
}

require 'config.php';

$error_msg = "";
$success_msg = "";
$name = "";

// Xử lý khi gửi form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    if (empty($name)) {
        $error_msg = "Tên thể loại không được để trống.";
    } else {
        $name_esc = $mysqli->real_escape_string($name);
        $insert = $mysqli->query("INSERT INTO categories (name) VALUES ('$name_esc')");
        if ($insert) {
            $success_msg = "Đã thêm thể loại mới.";
            $name = ""; // reset trường input
        } else {
            $error_msg = "Lỗi: Không thể thêm thể loại.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm thể loại</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .menu { background: #f0f0f0; padding: 10px; margin-bottom: 20px; }
        .menu a { margin-right: 15px; text-decoration: none; }
        .form-container { width: 300px; margin: 0 auto; }
        form { border: 1px solid #ccc; padding: 20px; }
        label { display: block; margin-top: 10px; }
        input[type=text] { width: 100%; padding: 5px; }
        .error { color: red; }
        .success { color: green; }
    </style>
</head>
<body>
    <!-- Menu -->
    <div class="menu">
        <a href="dashboard.php">Trang chủ</a>
        <a href="books.php">Danh sách Sách</a>
        <a href="categories.php">Quản lý Thể loại</a>
        <a href="publishers.php">Quản lý NXB</a>
        <a href="loans.php">Quản lý mượn sách</a>
        <a href="logout.php">Đăng xuất</a>
    </div>

    <div class="form-container">
        <h2>Thêm thể loại</h2>
        <?php if ($error_msg): ?>
            <p class="error"><?php echo $error_msg; ?></p>
        <?php endif; ?>
        <?php if ($success_msg): ?>
            <p class="success"><?php echo $success_msg; ?></p>
        <?php endif; ?>
        <form method="post" action="add_category.php">
            <label>Tên thể loại:</label>
            <input type="text" name="name" value="<?php echo htmlspecialchars($name); ?>" required>
            <br>
            <input type="submit" value="Thêm thể loại">
        </form>
    </div>
</body>
</html>
