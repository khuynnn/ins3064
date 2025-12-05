<?php
// categories.php - Trang danh sách thể loại sách (chỉ admin)

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
if (empty($_SESSION['is_admin']) || $_SESSION['is_admin'] == 0) {
    die("Bạn không có quyền truy cập trang này.");
}

require 'config.php';

// Xử lý xóa thể loại nếu có yêu cầu (qua tham số GET)
if (isset($_GET['action']) && $_GET['action'] == 'delete') {
    $cat_id = intval($_GET['id'] ?? 0);
    if ($cat_id > 0) {
        // Xóa thể loại với id tương ứng
        $mysqli->query("DELETE FROM categories WHERE id = $cat_id");
        // Lưu ý: nếu có sách thuộc thể loại này, xóa sẽ thất bại (hoặc xóa cascade nếu đặt quan hệ).
    }
    header("Location: categories.php");
    exit();
}

// Truy vấn lấy tất cả thể loại
$res = $mysqli->query("SELECT * FROM categories ORDER BY name");
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Thể loại</title>
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
        <a href="categories.php"><strong>Quản lý Thể loại</strong></a>
        <a href="publishers.php">Quản lý NXB</a>
        <a href="loans.php">Quản lý mượn sách</a>
        <a href="logout.php">Đăng xuất</a>
    </div>

    <h2 style="text-align:center;">Danh mục Thể loại</h2>
    <p style="text-align:center;"><a href="add_category.php">+ Thêm thể loại</a></p>
    <table>
        <tr>
            <th>Tên thể loại</th>
            <th>Hành động</th>
        </tr>
        <?php if ($res): ?>
            <?php while($cat = $res->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($cat['name']); ?></td>
                    <td class="actions">
                        <a href="edit_category.php?id=<?php echo $cat['id']; ?>">Sửa</a> | 
                        <a href="categories.php?action=delete&id=<?php echo $cat['id']; ?>"
                           onclick="return confirm('Xóa thể loại này?');">
                            Xóa
                        </a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php endif; ?>
    </table>
</body>
</html>
