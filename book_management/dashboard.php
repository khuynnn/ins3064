<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

// Fetch summary counts for dashboard
$count_books = 0;
$count_cats  = 0;
$count_pubs  = 0;
$count_users = 0;
$count_loans_active = 0;

$res = $conn->query("SELECT IFNULL(SUM(quantity),0) AS cnt FROM books");
if ($res) { $row = $res->fetch_assoc(); $count_books = (int)$row['cnt']; }


$res = $conn->query("SELECT COUNT(*) as cnt FROM categories");
if ($res) { $row = $res->fetch_assoc(); $count_cats = $row['cnt']; }

$res = $conn->query("SELECT COUNT(*) as cnt FROM publishers");
if ($res) { $row = $res->fetch_assoc(); $count_pubs = $row['cnt']; }

// Count only normal users (exclude admins)
$res = $conn->query("SELECT COUNT(*) as cnt FROM users WHERE role = 'user'");
if ($res) { $row = $res->fetch_assoc(); $count_users = $row['cnt']; }

// Count active (not yet returned) loans  (dùng cột is_returned bạn đang có)
$res = $conn->query("SELECT COUNT(*) as cnt FROM loans WHERE is_returned = 0");
if ($res) { $row = $res->fetch_assoc(); $count_loans_active = $row['cnt']; }

// Tên hiển thị: ưu tiên $_SESSION['name'] / fullname / username
$display_name = isset($_SESSION['name'])
    ? $_SESSION['name']
    : (isset($_SESSION['fullname']) ? $_SESSION['fullname'] : $_SESSION['username']);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Trang quản trị</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="nav">
    <a href="dashboard.php">Tổng quan</a>
    <a href="books.php">Sách</a>
    <a href="categories.php">Danh mục</a>
    <a href="publishers.php">Nhà xuất bản</a>
    <a href="loans.php">Mượn/Trả sách</a>
    <a href="logout.php">Đăng xuất</a>
</div>

<div class="container dashboard">
    <div class="dashboard-header">
        <div>
            <h1>Xin chào, <?php echo htmlspecialchars($display_name); ?> 👋</h1>
            <p class="subtitle">Bạn đang đăng nhập với quyền <strong>Quản trị viên</strong>.</p>
        </div>
    </div>

    <h2 class="section-title">Thống kê nhanh</h2>

<div class="stats-wrapper">

    <div class="stat-box box-books">
        <div class="stat-icon">📚</div>
        <div class="stat-content">
            <p class="stat-title">Tổng số sách</p>
            <p class="stat-value"><?php echo $count_books; ?></p>
        </div>
    </div>

    <div class="stat-box box-categories">
        <div class="stat-icon">🗂️</div>
        <div class="stat-content">
            <p class="stat-title">Số danh mục</p>
            <p class="stat-value"><?php echo $count_cats; ?></p>
        </div>
    </div>

    <div class="stat-box box-publishers">
        <div class="stat-icon">🏢</div>
        <div class="stat-content">
            <p class="stat-title">Nhà xuất bản</p>
            <p class="stat-value"><?php echo $count_pubs; ?></p>
        </div>
    </div>

    <div class="stat-box box-users">
        <div class="stat-icon">👤</div>
        <div class="stat-content">
            <p class="stat-title">Người dùng</p>
            <p class="stat-value"><?php echo $count_users; ?></p>
        </div>
    </div>

    <div class="stat-box box-loans">
        <div class="stat-icon">📖</div>
        <div class="stat-content">
            <p class="stat-title">Đang mượn</p>
            <p class="stat-value"><?php echo $count_loans_active; ?></p>
        </div>
    </div>

</div>


    <h2 class="section-title">Tác vụ nhanh</h2>
    <div class="quick-actions">
        <a class="qa-btn" href="add_book.php">+ Thêm sách mới</a>
        <a class="qa-btn" href="add_category.php">+ Thêm danh mục</a>
        <a class="qa-btn" href="add_publisher.php">+ Thêm nhà xuất bản</a>
        <a class="qa-btn" href="loans.php">📚 Quản lý mượn / trả</a>
    </div>
</div>
</body>
</html>
