<?php
session_start();
include 'config.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

$delete_error = "";
// Handle deletion of a category if requested
if (isset($_GET['delete_id'])) {
    $cat_id = intval($_GET['delete_id']);
    // Check if any book belongs to this category
    $stmt_check = $conn->prepare("SELECT COUNT(*) as cnt FROM books WHERE category_id = ?");
    $stmt_check->bind_param("i", $cat_id);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();
    $row = $result_check->fetch_assoc();
    if ($row && $row['cnt'] > 0) {
        // Prevent deletion if books under this category exist
        $delete_error = "Không thể xóa danh mục này vì còn sách thuộc danh mục.";
    } else {
        // Safe to delete the category
        $stmt_del = $conn->prepare("DELETE FROM categories WHERE id = ?");
        $stmt_del->bind_param("i", $cat_id);
        $stmt_del->execute();
        $stmt_del->close();
    }
    $stmt_check->close();
}

// Fetch all categories
$categories = [];
$result = $conn->query("SELECT * FROM categories");
if ($result) {
    while ($cat = $result->fetch_assoc()) {
        $categories[] = $cat;
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý danh mục</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="nav">
    <!-- Navigation bar for admin -->
    <a href="dashboard.php">Tổng quan</a> | 
    <a href="books.php">Sách</a> | 
    <a href="categories.php">Danh mục</a> | 
    <a href="publishers.php">Nhà xuất bản</a> | 
    <a href="loans.php">Mượn/Trả sách</a> | 
    <a href="logout.php">Đăng xuất</a>
</div>
<div class="container">
    <h1>Danh mục sách</h1>
    <!-- Show error if deletion was prevented -->
    <?php if (!empty($delete_error)): ?>
        <p class="error"><?php echo $delete_error; ?></p>
    <?php endif; ?>
    <table>
        <tr>
            <th>ID</th>
            <th>Tên danh mục</th>
            <th>Hành động</th>
        </tr>
        <?php foreach ($categories as $cat): ?>
        <tr>
            <td><?php echo $cat['id']; ?></td>
            <td><?php echo htmlspecialchars($cat['name']); ?></td>
            <td>
                <a href="edit_category.php?id=<?php echo $cat['id']; ?>">Sửa</a>
                | <a href="categories.php?delete_id=<?php echo $cat['id']; ?>" onclick="return confirm('Xác nhận xóa danh mục?');">Xóa</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
    <p><a href="add_category.php">Thêm danh mục mới</a></p>
</div>
</body>
</html>
