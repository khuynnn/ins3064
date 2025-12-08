<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

// Get publisher ID to edit
$pub_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$publisher = null;

if ($pub_id > 0) {
    $stmt = $conn->prepare("SELECT id, name, phone, address FROM publishers WHERE id = ?");
    $stmt->bind_param("i", $pub_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $publisher = $result->fetch_assoc();
    $stmt->close();
}

if (!$publisher) {
    echo "Nhà xuất bản không tồn tại.";
    exit();
}

$update_error = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name    = trim($_POST['name'] ?? '');
    $phone   = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');

    if ($name === "") {
        $update_error = "Tên NXB không được để trống.";
    } else {
        $stmt2 = $conn->prepare("UPDATE publishers SET name = ?, phone = ?, address = ? WHERE id = ?");
        $stmt2->bind_param("sssi", $name, $phone, $address, $pub_id);

        if ($stmt2->execute()) {
            $stmt2->close();
            header("Location: publishers.php");
            exit();
        } else {
            $update_error = "Lỗi khi cập nhật NXB: " . $conn->error;
        }
        $stmt2->close();
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sửa nhà xuất bản</title>
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
    <h1>Sửa nhà xuất bản</h1>

    <?php if (!empty($update_error)): ?>
        <p class="error"><?php echo htmlspecialchars($update_error); ?></p>
    <?php endif; ?>

    <form method="post" action="">
        <label for="name">Tên nhà xuất bản:</label><br>
        <input type="text" id="name" name="name"
               value="<?php echo htmlspecialchars($publisher['name']); ?>" required><br><br>

        <label for="phone">Số điện thoại:</label><br>
        <input type="text" id="phone" name="phone"
               value="<?php echo htmlspecialchars($publisher['phone'] ?? ''); ?>"><br><br>

        <label for="address">Địa chỉ:</label><br>
        <input type="text" id="address" name="address"
               value="<?php echo htmlspecialchars($publisher['address'] ?? ''); ?>"><br><br>

        <button type="submit">Cập nhật</button>
        <a href="publishers.php" style="margin-left:10px;">Quay lại</a>
    </form>
</div>
</body>
</html>
