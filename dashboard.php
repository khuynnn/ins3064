<?php
// dashboard.php - Trang dashboard sau khi đăng nhập, hiển thị menu và thông tin chung

session_start();
if (!isset($_SESSION['user_id'])) {
    // Nếu chưa đăng nhập, chuyển về trang đăng nhập
    header("Location: index.php");
    exit();
}

// Kết nối CSDL để có thể truy vấn thông tin nếu cần
require 'config.php';

// Lấy thông tin tên người dùng và quyền để sử dụng trong trang
$username = $_SESSION['username'] ?? '';
$is_admin = $_SESSION['is_admin'] ?? 0;
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .menu { background: #f0f0f0; padding: 10px; }
        .menu a { margin-right: 15px; text-decoration: none; }
        .welcome { margin: 20px; }
    </style>
</head>
<body>
    <!-- Menu điều hướng chung -->
    <div class="menu">
        <a href="dashboard.php">Trang chủ</a>
        <a href="books.php">Danh sách Sách</a>
        <?php if ($is_admin): ?>
            <a href="categories.php">Quản lý Thể loại</a>
            <a href="publishers.php">Quản lý NXB</a>
            <a href="loans.php">Quản lý mượn sách</a>
        <?php else: ?>
            <a href="loans_user.php">Sách đang mượn</a>
        <?php endif; ?>
        <a href="logout.php">Đăng xuất</a>
    </div>

    <!-- Nội dung chính của dashboard -->
    <div class="welcome">
        <h2>Xin chào, <?php echo htmlspecialchars($username); ?>!</h2>
        <?php if ($is_admin): ?>
            <p>Bạn đang đăng nhập với vai trò <strong>Admin</strong>. Bạn có thể quản lý sách, thể loại, nhà xuất bản và theo dõi phiếu mượn.</p>
        <?php else: ?>
            <p>Bạn đang đăng nhập với vai trò <strong>Người dùng</strong>. Bạn có thể xem danh sách sách và mượn sách.</p>
        <?php endif; ?>
    </div>
</body>
</html>
