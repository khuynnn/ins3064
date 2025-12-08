<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

$result = mysqli_query($conn, "SELECT id, name, phone, address FROM publishers ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8" />
    <title>Nhà xuất bản</title>
    <link rel="stylesheet" href="style.css" />
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

<div class="container">
    <h1>Danh sách nhà xuất bản</h1>

    <p><a class="qa-btn" href="add_publisher.php">+ Thêm nhà xuất bản</a></p>

    <table>
        <tr>
            <th>ID</th>
            <th>Tên nhà xuất bản</th>
            <th>Số điện thoại</th>
            <th>Địa chỉ</th>
            <th>Hành động</th>
        </tr>

        <?php if (!$result || mysqli_num_rows($result) == 0): ?>
            <tr><td colspan="5" style="text-align:center;">Chưa có nhà xuất bản nào.</td></tr>
        <?php else: ?>
            <?php while ($pub = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?php echo (int)$pub['id']; ?></td>
                    <td><?php echo htmlspecialchars($pub['name']); ?></td>
                    <td><?php echo htmlspecialchars($pub['phone'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($pub['address'] ?? ''); ?></td>
                    <td>
                        <a href="edit_publisher.php?id=<?php echo (int)$pub['id']; ?>">Sửa</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php endif; ?>
    </table>
</div>

</body>
</html>
