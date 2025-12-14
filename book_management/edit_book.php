<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

// Get book ID
$book_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$book = null;

if ($book_id > 0) {
    $stmt = $conn->prepare("
        SELECT id, title, author, quantity, category_id, publisher_id, image
        FROM books WHERE id = ?
    ");
    $stmt->bind_param("i", $book_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $book = $result->fetch_assoc();
    $stmt->close();
}

if (!$book) {
    echo "Sách không tồn tại.";
    exit();
}

// Fetch categories & publishers
$categories = [];
$publishers = [];

$res1 = $conn->query("SELECT id, name FROM categories ORDER BY name");
if ($res1) while ($cat = $res1->fetch_assoc()) $categories[] = $cat;

$res2 = $conn->query("SELECT id, name FROM publishers ORDER BY name");
if ($res2) while ($pub = $res2->fetch_assoc()) $publishers[] = $pub;

$update_error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title        = trim($_POST['title'] ?? '');
    $author       = trim($_POST['author'] ?? '');
    $quantity     = (int)($_POST['quantity'] ?? 0);
    $category_id  = (int)($_POST['category'] ?? 0);
    $publisher_id = (int)($_POST['publisher'] ?? 0);

    // ===== XỬ LÝ ẢNH =====
    $newImage = $book['image']; // mặc định giữ ảnh cũ

    if (!empty($_FILES['image']['name'])) {
        $allowed = ['jpg', 'jpeg', 'png'];
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed)) {
            $update_error = "Chỉ cho phép ảnh JPG, JPEG, PNG.";
        } else {
            $newImage = time() . '_' . basename($_FILES['image']['name']);
            $target = "uploads/books/" . $newImage;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
                // Xóa ảnh cũ
                if (!empty($book['image'])) {
                    $oldFile = "uploads/books/" . $book['image'];
                    if (file_exists($oldFile)) unlink($oldFile);
                }
            } else {
                $update_error = "Upload ảnh mới thất bại.";
            }
        }
    }

    if ($update_error === "") {
        if ($title === "" || $author === "" || $category_id <= 0 || $publisher_id <= 0) {
            $update_error = "Vui lòng điền đầy đủ thông tin.";
        } elseif ($quantity < 0) {
            $update_error = "Số lượng không được âm.";
        } else {
            $stmt2 = $conn->prepare("
                UPDATE books
                SET title = ?, author = ?, quantity = ?, category_id = ?, publisher_id = ?, image = ?
                WHERE id = ?
            ");
            $stmt2->bind_param(
                "ssiiisi",
                $title,
                $author,
                $quantity,
                $category_id,
                $publisher_id,
                $newImage,
                $book_id
            );

            if ($stmt2->execute()) {
                $stmt2->close();
                header("Location: books.php");
                exit();
            } else {
                $update_error = "Lỗi khi cập nhật sách.";
            }
            $stmt2->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sửa thông tin sách</title>
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
    <h1>Sửa thông tin sách</h1>

    <?php if (!empty($update_error)): ?>
        <p class="error"><?php echo htmlspecialchars($update_error); ?></p>
    <?php endif; ?>

    <!-- PHẢI có enctype -->
    <form method="post" enctype="multipart/form-data">

        <label>Tiêu đề sách:</label><br>
        <input type="text" name="title"
               value="<?php echo htmlspecialchars($book['title']); ?>" required><br><br>

        <label>Tác giả:</label><br>
        <input type="text" name="author"
               value="<?php echo htmlspecialchars($book['author']); ?>" required><br><br>

        <label>Số lượng:</label><br>
        <input type="number" name="quantity" min="0"
               value="<?php echo (int)$book['quantity']; ?>" required><br><br>

        <label>Danh mục:</label><br>
        <select name="category" required>
            <?php foreach ($categories as $cat): ?>
                <option value="<?php echo (int)$cat['id']; ?>"
                    <?php echo ((int)$cat['id'] === (int)$book['category_id']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($cat['name']); ?>
                </option>
            <?php endforeach; ?>
        </select><br><br>

        <label>Nhà xuất bản:</label><br>
        <select name="publisher" required>
            <?php foreach ($publishers as $pub): ?>
                <option value="<?php echo (int)$pub['id']; ?>"
                    <?php echo ((int)$pub['id'] === (int)$book['publisher_id']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($pub['name']); ?>
                </option>
            <?php endforeach; ?>
        </select><br><br>

        <label>Ảnh hiện tại:</label><br>
        <?php if (!empty($book['image'])): ?>
            <img src="uploads/books/<?php echo htmlspecialchars($book['image']); ?>" width="100"><br>
        <?php else: ?>
            <em>Chưa có ảnh</em><br>
        <?php endif; ?>
        <br>

        <label>Đổi ảnh mới (không bắt buộc):</label><br>
        <input type="file" name="image" accept=".jpg,.jpeg,.png"><br><br>

        <button type="submit">Cập nhật</button>
        <a href="books.php">Quay lại</a>
    </form>
</div>

</body>
</html>
