<?php
// borrow_book.php - Xử lý việc mượn sách của người dùng

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
// Trang này dành cho user thường (tuy nhiên nếu admin truy cập vẫn có thể mượn như user, không cấm nhưng thông thường admin không mượn sách)

require 'config.php';

// Lấy ID sách từ tham số (id của sách muốn mượn)
$book_id = intval($_GET['id'] ?? 0);
if ($book_id <= 0) {
    die("Yêu cầu không hợp lệ.");
}

// Lấy thông tin sách xem còn sách không
$res = $mysqli->query("SELECT * FROM books WHERE id = $book_id");
if (!$res || $res->num_rows == 0) {
    die("Không tìm thấy sách.");
}
$book = $res->fetch_assoc();
if ($book['quantity'] <= 0) {
    die("Sách này hiện không còn sẵn có để mượn.");
}

// Thực hiện tạo phiếu mượn trong bảng loans
$user_id = $_SESSION['user_id'];
// Tạo ngày mượn hiện tại
$date_now = date('Y-m-d');
$insert = $mysqli->query("INSERT INTO loans (user_id, book_id, loan_date, returned) VALUES ($user_id, $book_id, '$date_now', 0)");
if ($insert) {
    // Cập nhật số lượng sách (giảm 1)
    $mysqli->query("UPDATE books SET quantity = quantity - 1 WHERE id = $book_id");
    // Chuyển hướng đến trang danh sách sách với thông báo (có thể dùng query string hoặc session flash message)
    header("Location: books.php?msg=borrow_success");
    exit();
} else {
    die("Có lỗi xảy ra, không thể mượn sách.");
}
?>
