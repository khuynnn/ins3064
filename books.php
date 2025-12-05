<?php
session_start();
include('connection.php');

$result = mysqli_query($link, "SELECT * FROM books");
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Danh sách Sách</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h1>📖 Danh sách Sách</h1>

    <table border="1" cellpadding="10" cellspacing="0">
        <tr>
            <th>ID</th>
            <th>Tên sách</th>
            <th>Tác giả</th>
            <th>Phân loại</th>
            <th>Năm</th>
            
            <th>so luong</th>
            <th>Hành động</th>
        </tr>

        <?php while($row = mysqli_fetch_assoc($result)) { ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= $row['title'] ?></td>
            <td><?= $row['author'] ?></td>
            <td><?= $row['category'] ?></td>
            <td><?= $row['year'] ?></td>
            <td><?= $row['soluong'] ?></td>
            <td>
                <a href="edit_book.php?id=<?= $row['id'] ?>" class="btn small">✏️ Sửa</a>
                <a href="delete_book.php?id=<?= $row['id'] ?>" class="btn small delete">🗑 Xóa</a>
            </td>
        </tr>
        <?php } ?>
    </table>

    <div class="btn-group">
        <a href="dashboard.php" class="btn">🏠 Quay lại Dashboard</a>
        <a href="add_book.php" class="btn">➕ Thêm sách mới</a>
        <a href="logout.php" class="btn logout">🚪 Đăng xuất</a>
    </div>
</div>
</body>
</html>
