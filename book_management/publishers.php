<?php
// publishers.php - Trang danh sách Nhà xuất bản

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
if (empty($_SESSION['is_admin']) || $_SESSION['is_admin'] == 0) {
    die("Bạn không có quyền truy cập trang này.");
}

require 'config.php';

// Xử lý xóa NXB (tương tự thể loại)
if (isset($_GET['action']) && $_GET['action'] == 'delete') {
    $pub_id = intval($_GET['id'] ?? 0);
    if ($pub_id > 0) {
        $mysqli->query("DELETE FROM publishers WHERE id = $pub_id");
        // Lưu ý: nếu có sách thuộc NXB này, cần xử lý ràng buộc hoặc cấm xóa.
    }
    header("Location: publishers.php");
    exit();
}

// Truy vấn tất cả NXB
$res = $mysqli->query("SELECT * FROM publishers ORDER BY name");
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý NXB</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .menu { background: #f0f0f0; padding: 10px; margin-bottom: 20px; }
        .menu a { margin-right: 15px; text-decoration: none; }
        table { border-collapse: collapse; width: 50%; margin: 0 auto; }
        th, td { border: 1px solid #999; padding: 8px; text-align: left; }
        th { background: #ddd; }
        .actions a { margin-right: 5px; }
    </style>
</head>
<body>
    <!-- Menu -->
    <div class="menu">
        <a href="dashboard.php">Trang chủ</a>
        <a href="books.php">Danh sách Sách</a>
        <a href="categories.php">Quản lý Thể loại</a>
        <a href="publishers.php"><strong>Quản lý NXB</strong></a>
        <a href="loans.php">Quản lý mượn sách</a>
        <a href="logout.php">Đăng xuất</a>
    </div>

    <h2 style="text-align:center;">Danh sách Nhà xuất bản</h2>
    <p style="text-align:center;"><a href="add_publisher.php">+ Thêm NXB</a></p>
    <table>
        <tr>
            <th>Tên NXB</th>
            <th>Hành động</th>
        </tr>
        <?php if ($res): ?>
            <?php while($pub = $res->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($pub['name']); ?></td>
                    <td class="actions">
                        <a href="edit_publisher.php?id=<?php echo $pub['id']; ?>">Sửa</a> | 
                        <a href="publishers.php?action=delete&id=<?php echo $pub['id']; ?>"
                           onclick="return confirm('Xóa NXB này?');">
                            Xóa
                        </a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php endif; ?>
    </table>
</body>
</html>
