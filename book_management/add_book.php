<?php
include("connection.php");
$message = "";

if (isset($_POST["add"])) {
    $title = $_POST["title"];
    $author = $_POST["author"];
    $category = $_POST["category"];
    $year = $_POST["year"];

    if (!empty($title) && !empty($author)) {
        mysqli_query($link, "INSERT INTO books (title, author, category, year) VALUES ('$title', '$author', '$category', '$year')");
        $message = "Thêm sách thành công!";
    } else {
        $message = "Vui lòng nhập đầy đủ thông tin!";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm Sách</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h2>Thêm sách mới</h2>
    <form method="POST">
        <input type="text" name="title" placeholder="Tên sách" required><br>
        <input type="text" name="author" placeholder="Tác giả" required><br>
        <input type="text" name="category" placeholder="Phân loại"><br>
        <input type="number" name="year" placeholder="Năm xuất bản"><br>
        <button type="submit" name="add">Thêm</button>
    </form>
    <p class="success"><?= $message ?></p>
    <a href="books.php" class="btn-back">← Quay lại danh sách</a>
</div>
</body>
</html>
