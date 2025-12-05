<?php
// books.php - Trang hiển thị danh sách tất cả sách (cho cả admin và user)

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

require 'config.php';

// Kiểm tra xem người dùng có phải admin không
$is_admin = $_SESSION['is_admin'] ?? 0;

// Nếu URL có tham số action=delete (admin muốn xóa sách)
if ($is_admin && isset($_GET['action']) && $_GET['action'] == 'delete') {
    $book_id = intval($_GET['id'] ?? 0);
    if ($book_id > 0) {
        // Thực hiện xóa sách có id tương ứng
        $mysqli->query("DELETE FROM books WHERE id = $book_id");
        // Ghi chú: Nên kiểm tra khóa ngoại (ví dụ sách đã có phiếu mượn thì không nên xóa).
    }
    // Sau khi xóa hoặc nếu không tìm thấy id, chuyển hướng lại chính trang sách (để cập nhật danh sách)
    header("Location: books.php");
    exit();
}

// Truy vấn lấy danh sách sách, join để lấy tên thể loại và tên NXB
$sql = "SELECT books.id, books.title, books.author, books.quantity, 
               categories.name AS category_name, publishers.name AS publisher_name
        FROM books 
        LEFT JOIN categories ON books.category_id = categories.id 
        LEFT JOIN publishers ON books.publisher_id = publishers.id
        ORDER BY books.title";
$result = $mysqli->query($sql);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Danh sách Sách</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .menu { background: #f0f0f0; padding: 10px; margin-bottom: 20px; }
        .menu a { margin-right: 15px; text-decoration: none; }
        table { border-collapse: collapse; width: 80%; margin: 0 auto; }
        th, td { border: 1px solid #999; padding: 8px; text-align: left; }
        th { background: #ddd; }
        .actions a { margin-right: 5px; }
        .borrowed { color: red; font-weight: bold; }
    </style>
</head>
<body>
    <!-- Menu (sử dụng cùng menu như dashboard) -->
    <div class="menu">
        <a href="dashboard.php">Trang chủ</a>
        <a href="books.php"><strong>Danh sách Sách</strong></a>
        <?php if ($is_admin): ?>
            <a href="categories.php">Quản lý Thể loại</a>
            <a href="publishers.php">Quản lý NXB</a>
            <a href="loans.php">Quản lý mượn sách</a>
        <?php else: ?>
            <a href="loans_user.php">Sách đang mượn</a>
        <?php endif; ?>
        <a href="logout.php">Đăng xuất</a>
    </div>

    <h2 style="text-align:center;">Danh sách Sách</h2>
    <?php if ($is_admin): ?>
        <p style="text-align:center;"><a href="add_book.php">+ Thêm sách mới</a></p>
    <?php endif; ?>
    <table>
        <tr>
            <th>Tựa sách</th>
            <th>Tác giả</th>
            <th>Thể loại</th>
            <th>NXB</th>
            <th>Số lượng</th>
            <?php if ($is_admin): ?>
                <th>Hành động</th>
            <?php else: ?>
                <th>Mượn sách</th>
            <?php endif; ?>
        </tr>
        <?php if ($result): ?>
            <?php while($book = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($book['title']); ?></td>
                    <td><?php echo htmlspecialchars($book['author']); ?></td>
                    <td><?php echo htmlspecialchars($book['category_name']); ?></td>
                    <td><?php echo htmlspecialchars($book['publisher_name']); ?></td>
                    <td>
                        <?php echo $book['quantity']; ?>
                        <?php if ($book['quantity'] == 0): ?>
                            <!-- Nếu sách hết số lượng, hiển thị cảnh báo -->
                            <span class="borrowed">Hết sách</span>
                        <?php endif; ?>
                    </td>
                    <?php if ($is_admin): ?>
                        <td class="actions">
                            <a href="edit_book.php?id=<?php echo $book['id']; ?>">Sửa</a>
                            | 
                            <a href="books.php?action=delete&id=<?php echo $book['id']; ?>" 
                               onclick="return confirm('Bạn có chắc muốn xóa sách này?');">
                                Xóa
                            </a>
                        </td>
                    <?php else: ?>
                        <td>
                            <?php if ($book['quantity'] > 0): ?>
                                <a href="borrow_book.php?id=<?php echo $book['id']; ?>">Mượn</a>
                            <?php else: ?>
                                <!-- Không cho mượn nếu quantity = 0 -->
                                <span style="color: gray;">Không thể mượn</span>
                            <?php endif; ?>
                        </td>
                    <?php endif; ?>
                </tr>
            <?php endwhile; ?>
        <?php endif; ?>
    </table>
</body>
</html>
