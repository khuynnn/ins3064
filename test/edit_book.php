<?php
// edit_book.php - Trang chỉnh sửa sách, chỉ dành cho admin

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
if (empty($_SESSION['is_admin']) || $_SESSION['is_admin'] == 0) {
    die("Bạn không có quyền truy cập trang này.");
}

require 'config.php';

// Lấy ID sách từ tham số GET
$book_id = intval($_GET['id'] ?? 0);
if ($book_id <= 0) {
    die("ID sách không hợp lệ.");
}

// Truy vấn thông tin sách hiện tại theo ID
$res = $mysqli->query("SELECT * FROM books WHERE id = $book_id");
if (!$res || $res->num_rows == 0) {
    die("Không tìm thấy sách với ID đã cho.");
}
$book = $res->fetch_assoc();

// Lấy danh sách thể loại và NXB để hiển thị trong dropdown (giống add_book)
$categories_res = $mysqli->query("SELECT id, name FROM categories");
$publishers_res = $mysqli->query("SELECT id, name FROM publishers");

// Khởi tạo các biến giá trị ban đầu từ sách truy vấn được
$title = $book['title'];
$author = $book['author'];
$category_id = $book['category_id'];
$publisher_id = $book['publisher_id'];
$quantity = $book['quantity'];

$error_msg = "";
$success_msg = "";

// Xử lý khi admin cập nhật form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $author = $_POST['author'] ?? '';
    $category_id = $_POST['category_id'] ?? '';
    $publisher_id = $_POST['publisher_id'] ?? '';
    $quantity = $_POST['quantity'] ?? '';

    if (empty($title) || empty($author) || empty($quantity) || $category_id == '' || $publisher_id == '') {
        $error_msg = "Vui lòng điền đầy đủ thông tin.";
    } elseif (!is_numeric($quantity) || intval($quantity) < 0) {
        $error_msg = "Số lượng không hợp lệ.";
    } else {
        $title_esc = $mysqli->real_escape_string($title);
        $author_esc = $mysqli->real_escape_string($author);
        $cat_id = intval($category_id);
        $pub_id = intval($publisher_id);
        $qty = intval($quantity);

        $update = $mysqli->query("UPDATE books 
                                  SET title='$title_esc', author='$author_esc', category_id=$cat_id, publisher_id=$pub_id, quantity=$qty 
                                  WHERE id = $book_id");
        if ($update) {
            $success_msg = "Cập nhật sách thành công.";
        } else {
            $error_msg = "Lỗi: Không thể cập nhật sách.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chỉnh sửa sách</title>
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
        <h2>Chỉnh sửa sách</h2>
        <?php if ($error_msg): ?>
            <p class="error"><?php echo $error_msg; ?></p>
        <?php endif; ?>
        <?php if ($success_msg): ?>
            <p class="success"><?php echo $success_msg; ?></p>
        <?php endif; ?>
        <form method="post" action="edit_book.php?id=<?php echo $book_id; ?>">
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
            <input type="submit" value="Lưu thay đổi">
        </form>
    </div>
</body>
</html>
