<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: index.php");
    exit();
}

$user_id = (int)$_SESSION['user_id'];
$message = "";

// ==========================
// HANDLE BORROW
// ==========================
if (isset($_GET['book_id'])) {
    $book_id = (int)$_GET['book_id'];

    $conn->begin_transaction();
    try {
        $stmt_check = $conn->prepare("SELECT quantity FROM books WHERE id = ? FOR UPDATE");
        $stmt_check->bind_param("i", $book_id);
        $stmt_check->execute();
        $rs = $stmt_check->get_result();
        $bookRow = $rs ? $rs->fetch_assoc() : null;
        $stmt_check->close();

        if (!$bookRow) {
            throw new Exception("Sách không tồn tại.");
        }

        if ((int)$bookRow['quantity'] <= 0) {
            $conn->rollback();
            echo "<script>alert('Sách hiện đã hết.'); window.location.href='borrow_book.php';</script>";
            exit();
        }

        $stmt_upd = $conn->prepare("UPDATE books SET quantity = quantity - 1 WHERE id = ? AND quantity > 0");
        $stmt_upd->bind_param("i", $book_id);
        $stmt_upd->execute();
        $affected = $stmt_upd->affected_rows;
        $stmt_upd->close();

        if ($affected <= 0) {
            $conn->rollback();
            echo "<script>alert('Sách vừa hết, vui lòng thử lại.'); window.location.href='borrow_book.php';</script>";
            exit();
        }

        $stmt_ins = $conn->prepare("
            INSERT INTO loans (user_id, book_id, borrow_date, is_returned)
            VALUES (?, ?, CURDATE(), 0)
        ");
        $stmt_ins->bind_param("ii", $user_id, $book_id);
        $stmt_ins->execute();
        $stmt_ins->close();

        $conn->commit();
        header("Location: loans_user.php");
        exit();
    } catch (Exception $e) {
        $conn->rollback();
        echo "<script>alert('Lỗi khi mượn sách'); window.location.href='borrow_book.php';</script>";
        exit();
    }
}

// ==========================
// FETCH BOOKS (THÊM IMAGE)
// ==========================
$books = [];
$sql = "SELECT 
            books.id,
            books.title,
            books.author,
            books.quantity,
            books.image,
            categories.name AS category,
            publishers.name AS publisher
        FROM books
        LEFT JOIN categories ON books.category_id = categories.id
        LEFT JOIN publishers ON books.publisher_id = publishers.id
        ORDER BY books.title ASC";

$result = $conn->query($sql);
if ($result) {
    while ($book = $result->fetch_assoc()) {
        $books[] = $book;
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Mượn sách</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="nav">
    <a href="borrow_book.php"><strong>Mượn sách</strong></a> |
    <a href="loans_user.php">Sách đã mượn</a> |
    <a href="logout.php">Đăng xuất</a>
</div>

<div class="container">
    <h1>Danh sách sách</h1>

    <table>
        <tr>
            <th>Ảnh</th>
            <th>Tiêu đề</th>
            <th>Tác giả</th>
            <th>Danh mục</th>
            <th>Nhà xuất bản</th>
            <th>Số lượng</th>
            <th>Trạng thái</th>
            <th></th>
        </tr>

        <?php if (empty($books)): ?>
            <tr><td colspan="8" style="text-align:center;">Chưa có sách nào.</td></tr>
        <?php else: ?>
            <?php foreach ($books as $book): ?>
                <?php $qty = (int)$book['quantity']; ?>
                <tr>
                    <td>
                        <?php if (!empty($book['image'])): ?>
                            <img src="uploads/books/<?php echo htmlspecialchars($book['image']); ?>" width="60">
                        <?php else: ?>
                            —
                        <?php endif; ?>
                    </td>
                    <td><?php echo htmlspecialchars($book['title']); ?></td>
                    <td><?php echo htmlspecialchars($book['author']); ?></td>
                    <td><?php echo htmlspecialchars($book['category'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($book['publisher'] ?? ''); ?></td>
                    <td><?php echo $qty; ?></td>
                    <td>
                        <?php if ($qty <= 0): ?>
                            <span class="unavailable">Hết sách</span>
                        <?php else: ?>
                            Có sẵn
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($qty > 0): ?>
                            <a href="borrow_book.php?book_id=<?php echo (int)$book['id']; ?>"
                               onclick="return confirm('Xác nhận mượn sách này?');">
                                Mượn
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
