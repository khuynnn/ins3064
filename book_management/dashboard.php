<?php
include("connection.php");
$bookCount = mysqli_fetch_array(mysqli_query($link, "SELECT COUNT(*) AS total FROM books"))['total'];
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Bảng điều khiển</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h1>📚 Quản lý Sách</h1>
    <p>Tổng số sách: <strong><?= $bookCount ?></strong></p>

    <div class="btn-group">
      <a href="books.php" class="btn">📖 Xem danh sách</a>
      <a href="add_book.php" class="btn">➕ Thêm sách mới</a>
      <a href="logout.php" class="btn logout">🚪 Đăng xuất</a>
    </div>
</div>
</body>
</html>
