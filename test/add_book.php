<?php
// add_book.php - Trang thêm sách mới, chỉ dành cho admin

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
// Chỉ cho phép admin truy cập trang này
if (empty($_SESSION['is_admin']) || $_SESSION['is_admin'] == 0) {
    die("Bạn không có quyền truy cập trang này.");
}

require 'config.php';

// Lấy danh sách thể loại và NXB từ CSDL để phục vụ cho dropdown chọn
$categories_res = $mysqli->query("SELECT id, name FROM categories");
$publishers_res = $mysqli->query("SELECT id, name FROM publishers");

$title = $author = "";
$category_id = $publisher_id = "";
$quantity = 1;
$error_msg = "";
$success_msg = "";

// Xử lý form khi người dùng bấm "Thêm"
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $author = $_POST['author'] ?? '';
    $category_id = $_POST['category_id'] ?? '';
    $publisher_id = $_POST['publisher_id'] ?? '';
    $quantity = $_POST['quantity'] ?? '';

    // Kiểm tra nhanh các trường bắt buộc
    if (empty($title) || empty($author) || empty($quantity) || $category_id == '' || $publisher_id == '') {
        $error_msg = "Vui lòng điền đầy đủ thông tin sách.";
    } elseif (!is_numeric($quantity) || intval($quantity) < 0) {
        $error_msg = "Số lượng không hợp lệ.";
    } else {
        // Thêm sách vào CSDL
        $title_esc = $mysqli->real_escape_string($title);
        $author_esc = $mysqli->real_escape_string($author);
        $cat_id = intval($category_id);
        $pub_id = intval($publisher_id);
        $qty = intval($quantity);

        $insert = $mysqli->query("INSERT INTO books (title, author, category_id, publisher_id, quantity) 
                                  VALUES ('$title_esc', '$author_esc', $cat_id, $pub_id, $qty)");
        if ($insert) {
            $success_msg = "Đã thêm sách mới thành công!";
            // Xóa giá trị form sau khi thêm thành công
            $title = $author = "";
            $category_id = $publisher_id = "";
            $quantity = 1;
        } else {
            $error_msg = "Lỗi: Không thể thêm sách. Vui lòng thử lại.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm sách mới</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .menu { background: #f0f0f0; padding: 10px; margin-bottom: 20px; }
        .menu a { margin-right: 15px; text-decoration: none; }
        .form-container { width: 400px; margin: 0 auto; }
        form { border: 1px solid #ccc; padding: 20px; }
        label { display: block; margin-top: 10px; }
        input[type=text], select, input[type=number] { width: 100%; padding: 5px; }
        .error { color: red; }
        .success { color: green; }
    </style>
</head>
<body>
    <!-- Menu -->
    <div class="menu">
        <a href="dashboard.php">Trang chủ</a>
        <a href="books.php">Danh sách Sách</a>
        <a href="categories.php">Quản lý Thể loại</a>
        <a href="publishers.php">Quản lý NXB</a>
        <a href="loans.php">Quản lý mượn sách</a>
        <a href="logout.php">Đăng xuất</a>
    </div>

    <div class="form-container">
        <h2>Thêm sách mới</h2>
        <?php if ($error_msg): ?>
            <p class="error"><?php echo $error_msg; ?></p>
        <?php endif; ?>
        <?php if ($success_msg): ?>
            <p class="success"><?php echo $success_msg; ?></p>
        <?php endif; ?>
        <form method="post" action="add_book.php">
            <label>Tựa sách:</label>
            <input type="text" name="title" value="<?php echo htmlspecialchars($title); ?>" required>

            <label>Tác giả:</label>
            <input type="text" name="author" value="<?php echo htmlspecialchars($author); ?>" required>

            <label>Thể loại:</label>
            <select name="category_id" required>
                <option value="">-- Chọn thể loại --</option>
                <?php if ($categories_res): ?>
                    <?php while($cat = $categories_res->fetch_assoc()): ?>
                        <option value="<?php echo $cat['id']; ?>" 
                            <?php if ($cat['id'] == $category_id) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($cat['name']); ?>
                        </option>
                    <?php endwhile; ?>
                <?php endif; ?>
            </select>

            <label>Nhà xuất bản:</label>
            <select name="publisher_id" required>
                <option value="">-- Chọn NXB --</option>
                <?php if ($publishers_res): ?>
                    <?php while($pub = $publishers_res->fetch_assoc()): ?>
                        <option value="<?php echo $pub['id']; ?>" 
                            <?php if ($pub['id'] == $publisher_id) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($pub['name']); ?>
                        </option>
                    <?php endwhile; ?>
                <?php endif; ?>
            </select>

            <label>Số lượng sách (có sẵn):</label>
            <input type="number" name="quantity" min="0" value="<?php echo htmlspecialchars($quantity); ?>" required>

            <br>
            <input type="submit" value="Thêm sách">
        </form>
    </div>
</body>
</html>
