<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

// Fetch categories and publishers for the form dropdowns
$categories = [];
$publishers = [];

$res1 = $conn->query("SELECT id, name FROM categories ORDER BY name");
if ($res1) {
    while ($cat = $res1->fetch_assoc()) $categories[] = $cat;
}

$res2 = $conn->query("SELECT id, name FROM publishers ORDER BY name");
if ($res2) {
    while ($pub = $res2->fetch_assoc()) $publishers[] = $pub;
}

$add_error = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title        = trim($_POST['title'] ?? '');
    $author       = trim($_POST['author'] ?? '');
    $quantity     = $_POST['quantity'] ?? '';
    $category_id  = $_POST['category'] ?? '';
    $publisher_id = $_POST['publisher'] ?? '';

    // ép kiểu an toàn
    $quantity = (int)$quantity;
    $category_id = (int)$category_id;
    $publisher_id = (int)$publisher_id;

    if ($title === "" || $author === "" || $category_id <= 0 || $publisher_id <= 0) {
        $add_error = "Vui lòng điền đầy đủ thông tin sách.";
    } elseif ($quantity < 0) {
        $add_error = "Số lượng không được âm.";
    } else {
        // Insert new book into database
        $stmt = $conn->prepare("INSERT INTO books (title, author, quantity, category_id, publisher_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssiii", $title, $author, $quantity, $category_id, $publisher_id);

        if ($stmt->execute()) {
            $stmt->close();
            header("Location: books.php");
            exit();
        } else {
            $add_error = "Lỗi khi thêm sách: " . $conn->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm sách</title>
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
    <h1>Thêm sách mới</h1>

    <?php if (!empty($add_error)): ?>
        <p class="error"><?php echo htmlspecialchars($add_error); ?></p>
    <?php endif; ?>

    <form method="post" action="">
        <label for="title">Tiêu đề sách:</label><br>
        <input type="text" id="title" name="title" required><br><br>

        <label for="author">Tác giả:</label><br>
        <input type="text" id="author" name="author" required><br><br>

        <label for="quantity">Số lượng:</label><br>
        <input type="number" id="quantity" name="quantity" min="0" value="0" required><br><br>

        <label for="category">Danh mục:</label><br>
        <select id="category" name="category" required>
            <option value="">-- Chọn danh mục --</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?php echo (int)$cat['id']; ?>">
                    <?php echo htmlspecialchars($cat['name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <br><br>

        <label for="publisher">Nhà xuất bản:</label><br>
        <select id="publisher" name="publisher" required>
            <option value="">-- Chọn NXB --</option>
            <?php foreach ($publishers as $pub): ?>
                <option value="<?php echo (int)$pub['id']; ?>">
                    <?php echo htmlspecialchars($pub['name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <br><br>

        <button type="submit">Thêm sách</button>
    </form>
</div>
</body>
</html>
