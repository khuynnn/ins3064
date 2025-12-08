<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

// Xử lý xóa sách (và xóa luôn lịch sử mượn sách liên quan)
if (isset($_GET['delete_id'])) {
    $book_id = intval($_GET['delete_id']);

    // Xóa loan trước cho chắc
    $stmt_del2 = $conn->prepare("DELETE FROM loans WHERE book_id = ?");
    $stmt_del2->bind_param("i", $book_id);
    $stmt_del2->execute();
    $stmt_del2->close();

    // Xóa sách
    $stmt_del = $conn->prepare("DELETE FROM books WHERE id = ?");
    $stmt_del->bind_param("i", $book_id);
    $stmt_del->execute();
    $stmt_del->close();
}

// Lấy danh sách sách + tên danh mục + NXB + tác giả + số lượng
$books = [];
$sql = "SELECT 
            books.id, 
            books.title,
            books.author,
            books.quantity,
            categories.name AS category, 
            publishers.name AS publisher
        FROM books 
        LEFT JOIN categories ON books.category_id = categories.id 
        LEFT JOIN publishers ON books.publisher_id = publishers.id
        ORDER BY books.id DESC";
$result = $conn->query($sql);
if ($result) {
    while ($book = $result->fetch_assoc()) {
        $books[] = $book;
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý sách</title>
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

<div class="container">
    <h1>Danh sách sách</h1>

    <p style="margin-bottom: 15px;">
        <a class="qa-btn" href="add_book.php">+ Thêm sách mới</a>
    </p>

    <table>
        <tr>
            <th>ID</th>
            <th>Tiêu đề</th>
            <th>Tác giả</th>
            <th>Số lượng</th>
            <th>Danh mục</th>
            <th>Nhà xuất bản</th>
            <th>Hành động</th>
        </tr>

        <?php if (empty($books)): ?>
            <tr>
                <td colspan="7" style="text-align:center;">Chưa có sách nào trong hệ thống.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($books as $book): ?>
                <tr>
                    <td><?php echo (int)$book['id']; ?></td>
                    <td><?php echo htmlspecialchars($book['title']); ?></td>
                    <td><?php echo htmlspecialchars($book['author']); ?></td>
                    <td><?php echo (int)$book['quantity']; ?></td>
                    <td><?php echo htmlspecialchars($book['category'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($book['publisher'] ?? ''); ?></td>
                    <td>
                        <a href="edit_book.php?id=<?php echo (int)$book['id']; ?>">Sửa</a>
                        |
                        <a href="books.php?delete_id=<?php echo (int)$book['id']; ?>"
                           onclick="return confirm('Xác nhận xóa sách?');">Xóa</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </table>
</div>
</body>
</html>
