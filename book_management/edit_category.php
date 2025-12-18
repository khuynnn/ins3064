<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

// Get category ID
$cat_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$category = null;

if ($cat_id > 0) {
    $stmt = $conn->prepare("SELECT id, name FROM categories WHERE id = ?");
    $stmt->bind_param("i", $cat_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $category = $result->fetch_assoc();
    $stmt->close();
}

if (!$category) {
    header("Location: categories.php");
    exit();
}

$update_error = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST['name'] ?? '');

    if ($name === "") {
        $update_error = "Tên danh mục không được để trống.";
    } else {
        $stmt2 = $conn->prepare(
            "UPDATE categories SET name = ? WHERE id = ?"
        );
        $stmt2->bind_param("si", $name, $cat_id);

        if ($stmt2->execute()) {
            $stmt2->close();
            header("Location: categories.php");
            exit();
        } else {
            $update_error = "Lỗi khi cập nhật danh mục.";
        }
        $stmt2->close();
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sửa danh mục</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="nav">
    <a href="dashboard.php">Tổng quan</a> |
    <a href="books.php">Sách</a> |
    <a href="categories.php">Danh mục</a> |
    <a href="publishers.php">Nhà xuất bản</a> |
    <a href="loans.php">Mượn/Trả sách</a> |
    <a href="logout.php">Đăng xuất</a>
</div>

<div class="container">
    <h1>Sửa danh mục</h1>

    <?php if (!empty($update_error)): ?>
        <p class="error"><?php echo htmlspecialchars($update_error); ?></p>
    <?php endif; ?>

    <form method="post" action="">
        <label for="name">Tên danh mục:</label><br>
        <input
            type="text"
            id="name"
            name="name"
            value="<?php echo htmlspecialchars($category['name']); ?>"
            required
        ><br><br>

        <button type="submit">Cập nhật</button>
        <a href="categories.php" style="margin-left:10px;">Quay lại</a>
    </form>
</div>

</body>
</html>
