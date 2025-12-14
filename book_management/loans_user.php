<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: index.php");
    exit();
}

$user_id = (int)$_SESSION['user_id'];

// Lấy tên hiển thị: ưu tiên session name, nếu chưa có thì lấy từ DB
if (empty($_SESSION['name'])) {
    $stmt_name = $conn->prepare("SELECT name, username FROM users WHERE id = ?");
    $stmt_name->bind_param("i", $user_id);
    $stmt_name->execute();
    $rs_name = $stmt_name->get_result();
    if ($rs_name && $rs_name->num_rows > 0) {
        $u = $rs_name->fetch_assoc();
        if (!empty($u['name'])) $_SESSION['name'] = $u['name'];
        if (empty($_SESSION['username']) && !empty($u['username'])) $_SESSION['username'] = $u['username'];
    }
    $stmt_name->close();
}

$display_name = !empty($_SESSION['name']) ? $_SESSION['name'] : ($_SESSION['username'] ?? 'Bạn');

// Fetch loans of the logged-in user
$loans = [];
$sql = "SELECT 
            loans.id,
            books.title,
            books.image,
            loans.borrow_date,
            loans.is_returned,
            loans.return_date
        FROM loans
        JOIN books ON loans.book_id = books.id
        WHERE loans.user_id = ?
        ORDER BY loans.is_returned ASC, loans.borrow_date DESC, loans.id DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result) {
    while ($loan = $result->fetch_assoc()) {
        $loans[] = $loan;
    }
}
$stmt->close();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sách đã mượn</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="nav">
    <a href="borrow_book.php">Mượn sách</a> |
    <a href="loans_user.php"><strong>Sách đã mượn</strong></a> |
    <a href="logout.php">Đăng xuất</a>
</div>

<div class="container">
    <h1>Xin chào, <?php echo htmlspecialchars($display_name); ?> 👋</h1>
    <p class="subtitle">Dưới đây là danh sách sách bạn đã mượn.</p>

    <table>
        <tr>
            <th>Ảnh</th>
            <th>Tiêu đề sách</th>
            <th>Ngày mượn</th>
            <th>Trạng thái</th>
        </tr>

        <?php if (empty($loans)): ?>
            <tr>
                <td colspan="3" style="text-align:center;">Bạn chưa mượn sách nào.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($loans as $loan): ?>
            <tr>
                <td style="text-align:center;">
                    <?php if (!empty($loan['image'])): ?>
                        <img src="uploads/books/<?php echo htmlspecialchars($loan['image']); ?>"
                            width="60"
                            style="border-radius:4px;">
                    <?php else: ?>
                        <img src="uploads/books/no-image.png"
                            width="60"
                            style="opacity:0.6;">
                    <?php endif; ?>
                </td>

                <td><?php echo htmlspecialchars($loan['title']); ?></td>
                <td><?php echo htmlspecialchars($loan['borrow_date']); ?></td>
                <td>
                    <?php if ((int)$loan['is_returned'] === 1): ?>
                        Đã trả<?php echo !empty($loan['return_date']) ? " (" . htmlspecialchars($loan['return_date']) . ")" : ""; ?>
                    <?php else: ?>
                        Đang mượn
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>

        <?php endif; ?>
    </table>

    <p style="margin-top: 15px;">
        <a class="qa-btn" href="borrow_book.php">Mượn thêm sách</a>
    </p>
</div>

</body>
</html>
