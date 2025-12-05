<?php
// loans.php - Trang quản lý các phiếu mượn sách, dành cho admin

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
if (empty($_SESSION['is_admin']) || $_SESSION['is_admin'] == 0) {
    die("Bạn không có quyền truy cập trang này.");
}

require 'config.php';

// Xử lý yêu cầu đánh dấu trả sách (nếu admin bấm "Đã trả")
if (isset($_GET['action']) && $_GET['action'] == 'return') {
    $loan_id = intval($_GET['id'] ?? 0);
    if ($loan_id > 0) {
        // Cập nhật phiếu mượn thành đã trả
        $mysqli->query("UPDATE loans SET returned = 1, return_date = CURDATE() WHERE id = $loan_id");
        // Lấy book_id tương ứng phiếu mượn để tăng lại số lượng sách
        $bookRes = $mysqli->query("SELECT book_id FROM loans WHERE id = $loan_id");
        if ($bookRes && $bookRes->num_rows > 0) {
            $loanInfo = $bookRes->fetch_assoc();
            $book_id = $loanInfo['book_id'];
            $mysqli->query("UPDATE books SET quantity = quantity + 1 WHERE id = $book_id");
        }
    }
    header("Location: loans.php");
    exit();
}

// Truy vấn danh sách phiếu mượn, join với users và books để lấy thông tin
$sql = "SELECT loans.id, loans.book_id, loans.user_id, loans.loan_date, loans.return_date, loans.returned,
               users.username, books.title 
        FROM loans
        JOIN users ON loans.user_id = users.id
        JOIN books ON loans.book_id = books.id
        ORDER BY loans.returned, loans.loan_date DESC";
$res = $mysqli->query($sql);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Phiếu mượn</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .menu { background: #f0f0f0; padding: 10px; margin-bottom: 20px; }
        .menu a { margin-right: 15px; text-decoration: none; }
        table { border-collapse: collapse; width: 80%; margin: 0 auto; }
        th, td { border: 1px solid #999; padding: 8px; text-align: left; }
        th { background: #ddd; }
        .returned { background: #e0ffe0; }
        .not-returned { background: #ffe0e0; }
    </style>
</head>
<body>
    <!-- Menu -->
    <div class="menu">
        <a href="dashboard.php">Trang chủ</a>
        <a href="books.php">Danh sách Sách</a>
        <a href="categories.php">Quản lý Thể loại</a>
        <a href="publishers.php">Quản lý NXB</a>
        <a href="loans.php"><strong>Quản lý mượn sách</strong></a>
        <a href="logout.php">Đăng xuất</a>
    </div>

    <h2 style="text-align:center;">Danh sách Phiếu mượn</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Người mượn</th>
            <th>Tựa sách</th>
            <th>Ngày mượn</th>
            <th>Trạng thái</th>
            <th>Hành động</th>
        </tr>
        <?php if ($res): ?>
            <?php while($loan = $res->fetch_assoc()): ?>
                <tr class="<?php echo $loan['returned'] ? 'returned' : 'not-returned'; ?>">
                    <td><?php echo $loan['id']; ?></td>
                    <td><?php echo htmlspecialchars($loan['username']); ?></td>
                    <td><?php echo htmlspecialchars($loan['title']); ?></td>
                    <td><?php echo htmlspecialchars($loan['loan_date']); ?></td>
                    <td>
                        <?php if ($loan['returned']): ?>
                            Đã trả <?php echo $loan['return_date'] ? ' ('.$loan['return_date'].')' : ''; ?>
                        <?php else: ?>
                            Chưa trả
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if (!$loan['returned']): ?>
                            <a href="loans.php?action=return&id=<?php echo $loan['id']; ?>"
                               onclick="return confirm('Đánh dấu sách đã được trả?');">
                                Đã trả
                            </a>
                        <?php else: ?>
                            <!-- Nếu đã trả thì không có hành động -->
                            <span style="color: gray;">✔</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php endif; ?>
    </table>
</body>
</html>
