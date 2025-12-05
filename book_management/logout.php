<?php
// logout.php - Xử lý đăng xuất người dùng

session_start();
// Xóa tất cả thông tin session
$_SESSION = [];
session_destroy();

// Xóa cookie "ghi nhớ đăng nhập" nếu có
if (isset($_COOKIE['user_id'])) {
    setcookie('user_id', '', time() - 3600, "/"); // đặt thời gian hết hạn trong quá khứ để xóa
}

// Chuyển hướng về trang đăng nhập
header("Location: index.php");
exit();
?>
