<?php
// loans_user.php - Trang dành cho người dùng xem các sách mình đã mượn

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
// Trang này có thể truy cập bởi cả user thường và admin, nhưng chủ yếu phục vụ user thường để xem sách của chính họ

require 'config.php';

$user_id = $_SESSION['user_id'];
// Truy vấn các phiếu mượn của chính người đăng nhập
$sql = "SELECT loans.id, loans.loan_date, loans.return_date, loans.returned,
               books.title 
        FROM loans
        JOIN books ON loans.book_id = books.id
        WHERE loans.user_id = $user_id
        ORDER BY loans.returned, loans.loan_date DESC";
$res = $mysqli->query($sql);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sách đang mượn</title>
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
    <!-- Menu (tái sử dụng) -->
    <div class="menu">
        <a href="dashboard.php">Trang chủ</a>
        <a href="books.php">Danh sách Sách</a>
        <?php if (!empty($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1): ?>
            <a href="categories.php">Quản lý Thể loại</a>
            <a href="publishers.php">Quản lý NXB</a>
            <a href="loans.php">Quản lý mượn sách</a>
        <?php else: ?>
            <a href="loans_user.php"><strong>Sách đang mượn</strong></a>
        <?php endif; ?>
        <a href="logout.php">Đăng xuất</a>
    </div>

    <h2 style="text-align:center;">Sách bạn đã mượn</h2>
    <table>
        <tr>
            <th>ID Phiếu</th>
            <th>Tựa sách</th>
            <th>Ngày mượn</th>
            <th>Trạng thái</th>
        </tr>
        <?php if ($res): ?>
            <?php while($loan = $res->fetch_assoc()): ?>
                <tr class="<?php echo $loan['returned'] ? 'returned' : 'not-returned'; ?>">
                    <td><?php echo $loan['id']; ?></td>
                    <td><?php echo htmlspecialchars($loan['title']); ?></td>
                    <td><?php echo htmlspecialchars($loan['loan_date']); ?></td>
                    <td>
                        <?php if ($loan['returned']): ?>
                            Đã trả <?php echo $loan['return_date'] ? ' ('.$loan['return_date'].')' : ''; ?>
                        <?php else: ?>
                            Chưa trả
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php endif; ?>
    </table>
</body>
</html>
