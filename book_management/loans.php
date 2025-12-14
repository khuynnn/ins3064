<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

// ==========================
// Handle marking a book as returned + update book quantity
// ==========================
if (isset($_GET['return_id'])) {
    $loan_id = (int)$_GET['return_id'];

    // Chỉ xử lý nếu phiếu mượn còn chưa trả
    $stmt_get = $conn->prepare(
        "SELECT book_id FROM loans WHERE id = ? AND is_returned = 0"
    );
    $stmt_get->bind_param("i", $loan_id);
    $stmt_get->execute();
    $rs = $stmt_get->get_result();
    $loanRow = $rs ? $rs->fetch_assoc() : null;
    $stmt_get->close();

    if ($loanRow) {
        $book_id = (int)$loanRow['book_id'];

        $conn->begin_transaction();
        try {
            // 1) Đánh dấu đã trả
            $stmt1 = $conn->prepare(
                "UPDATE loans SET is_returned = 1, return_date = CURDATE() WHERE id = ?"
            );
            $stmt1->bind_param("i", $loan_id);
            $stmt1->execute();
            $stmt1->close();

            // 2) Cộng lại số lượng sách
            $stmt2 = $conn->prepare(
                "UPDATE books SET quantity = quantity + 1 WHERE id = ?"
            );
            $stmt2->bind_param("i", $book_id);
            $stmt2->execute();
            $stmt2->close();

            $conn->commit();
        } catch (Exception $e) {
            $conn->rollback();
        }
    }

    // Tránh refresh bị cộng kho nhiều lần
    header("Location: loans.php");
    exit();
}

// ==========================
// Fetch all loan records
// ==========================
$loans = [];
$sql = "SELECT 
            loans.id,
            books.title,
            books.image,
            users.name AS user_name,
            loans.borrow_date,
            loans.is_returned,
            loans.return_date
        FROM loans
        JOIN books ON loans.book_id = books.id
        JOIN users ON loans.user_id = users.id
        ORDER BY loans.borrow_date DESC, loans.id DESC";

$result = $conn->query($sql);
if ($result) {
    while ($loan = $result->fetch_assoc()) {
        $loans[] = $loan;
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý mượn trả</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="nav">
    <a href="dashboard.php">Tổng quan</a> |
    <a href="books.php">Sách</a> |
    <a href="categories.php">Danh mục</a> |
    <a href="publishers.php">Nhà xuất bản</a> |
    <a href="loans.php"><strong>Mượn/Trả sách</strong></a> |
    <a href="logout.php">Đăng xuất</a>
</div>

<div class="container">
    <h1>Quản lý mượn / trả sách</h1>

    <table>
        <tr>
            <th>Bìa sách</th>
            <th>Người mượn</th>
            <th>Tên sách</th>
            <th>Ngày mượn</th>
            <th>Trạng thái</th>
            <th>Hành động</th>
        </tr>

        <?php if (empty($loans)): ?>
            <tr>
                <td colspan="6" style="text-align:center;">
                    Chưa có phiếu mượn nào.
                </td>
            </tr>
        <?php else: ?>
            <?php foreach ($loans as $loan): ?>
                <tr>
                    <td>
                        <?php if (!empty($loan['image'])): ?>
                            <img src="uploads/books/<?php echo htmlspecialchars($loan['image']); ?>"
                                 width="50"
                                 style="border-radius:4px;">
                        <?php else: ?>
                            —
                        <?php endif; ?>
                    </td>

                    <td><?php echo htmlspecialchars($loan['user_name']); ?></td>
                    <td><?php echo htmlspecialchars($loan['title']); ?></td>
                    <td><?php echo htmlspecialchars($loan['borrow_date']); ?></td>

                    <td>
                        <?php if ((int)$loan['is_returned'] === 1): ?>
                            Đã trả (<?php echo htmlspecialchars($loan['return_date']); ?>)
                        <?php else: ?>
                            Chưa trả
                        <?php endif; ?>
                    </td>

                    <td>
                        <?php if ((int)$loan['is_returned'] === 0): ?>
                            <a href="loans.php?return_id=<?php echo (int)$loan['id']; ?>"
                               onclick="return confirm('Đánh dấu sách này đã trả?');">
                                Đánh dấu đã trả
                            </a>
                        <?php else: ?>
                            —
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </table>
</div>

</body>
</html>
