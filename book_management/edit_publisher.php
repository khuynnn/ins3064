<?php
// edit_publisher.php - Trang sửa thông tin NXB

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
if (empty($_SESSION['is_admin']) || $_SESSION['is_admin'] == 0) {
    die("Bạn không có quyền truy cập trang này.");
}

require 'config.php';

$pub_id = intval($_GET['id'] ?? 0);
if ($pub_id <= 0) {
    die("ID NXB không hợp lệ.");
}

$res = $mysqli->query("SELECT * FROM publishers WHERE id = $pub_id");
if (!$res || $res->num_rows == 0) {
    die("Không tìm thấy NXB.");
}
$publisher = $res->fetch_assoc();
$name = $publisher['name'];

$error_msg = "";
$success_msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    if (empty($name)) {
        $error_msg = "Tên NXB không được để trống.";
    } else {
        $name_esc = $mysqli->real_escape_string($name);
        $update = $mysqli->query("UPDATE publishers SET name='$name_esc' WHERE id = $pub_id");
        if ($update) {
            $success_msg = "Đã cập nhật NXB.";
        } else {
            $error_msg = "Lỗi: Không thể cập nhật.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sửa NXB</title>
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
        <h2>Sửa Nhà xuất bản</h2>
        <?php if ($error_msg): ?>
            <p class="error"><?php echo $error_msg; ?></p>
        <?php endif; ?>
        <?php if ($success_msg): ?>
            <p class="success"><?php echo $success_msg; ?></p>
        <?php endif; ?>
        <form method="post" action="edit_publisher.php?id=<?php echo $pub_id; ?>">
            <label>Tên NXB:</label>
            <input type="text" name="name" value="<?php echo htmlspecialchars($name); ?>" required>
            <br>
            <input type="submit" value="Lưu thay đổi">
        </form>
    </div>
</body>
</html>
