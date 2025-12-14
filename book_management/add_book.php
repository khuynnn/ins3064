<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

// Fetch categories and publishers
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
    $quantity     = (int)($_POST['quantity'] ?? 0);
    $category_id  = (int)($_POST['category'] ?? 0);
    $publisher_id = (int)($_POST['publisher'] ?? 0);

    // xử lý ảnh
    // xử lý ảnh
    $imageName = null;

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {

        $allowed = ['jpg', 'jpeg', 'png'];
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed)) {
            $add_error = "Chỉ cho phép ảnh JPG, JPEG, PNG.";
        } elseif ($_FILES['image']['size'] > 2 * 1024 * 1024) {
            $add_error = "Ảnh không được vượt quá 2MB.";
        } else {
            $imageName = uniqid('book_', true) . '.' . $ext;
            $target = __DIR__ . '/uploads/books/' . $imageName;

            if (!move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
                $add_error = "Upload ảnh thất bại.";
            }
        }
    }


    if ($add_error === "") {
        if ($title === "" || $author === "" || $category_id <= 0 || $publisher_id <= 0) {
            $add_error = "Vui lòng điền đầy đủ thông tin sách.";
        } elseif ($quantity < 0) {
            $add_error = "Số lượng không được âm.";
        } else {
            $stmt = $conn->prepare("
                INSERT INTO books (title, author, quantity, category_id, publisher_id, image)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->bind_param(
                "ssiiis",
                $title,
                $author,
                $quantity,
                $category_id,
                $publisher_id,
                $imageName
            );

            if ($stmt->execute()) {
                $stmt->close();
                header("Location: books.php");
                exit();
            } else {
                $add_error = "Lỗi khi thêm sách: " . $conn->error;
            }
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

    <!-- PHẢI có enctype -->
    <form method="post" enctype="multipart/form-data">
        <label>Tiêu đề sách:</label><br>
        <input type="text" name="title" required><br><br>

        <label>Tác giả:</label><br>
        <input type="text" name="author" required><br><br>

        <label>Số lượng:</label><br>
        <input type="number" name="quantity" min="0" value="0" required><br><br>

        <label>Danh mục:</label><br>
        <select name="category" required>
            <option value="">-- Chọn danh mục --</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?php echo (int)$cat['id']; ?>">
                    <?php echo htmlspecialchars($cat['name']); ?>
                </option>
            <?php endforeach; ?>
        </select><br><br>

        <label>Nhà xuất bản:</label><br>
        <select name="publisher" required>
            <option value="">-- Chọn NXB --</option>
            <?php foreach ($publishers as $pub): ?>
                <option value="<?php echo (int)$pub['id']; ?>">
                    <?php echo htmlspecialchars($pub['name']); ?>
                </option>
            <?php endforeach; ?>
        </select><br><br>

        <label>Ảnh bìa sách:</label><br>
        <input type="file" name="image" accept=".jpg,.jpeg,.png"><br><br>

        <button type="submit">Thêm sách</button>
    </form>
</div>

</body>
</html>
