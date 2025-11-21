<?php
include("connection.php");
$id = $_GET["id"];
$message = "";

// Lấy dữ liệu hiện tại
$res = mysqli_query($link, "SELECT * FROM books WHERE id=$id");
$row = mysqli_fetch_assoc($res);

if (isset($_POST["update"])) {
    $title = $_POST["title"];
    $author = $_POST["author"];
    $category = $_POST["category"];
    $year = $_POST["year"];

    mysqli_query($link, "UPDATE books SET title='$title', author='$author', category='$category', year='$year' WHERE id=$id");
    $message = "Cập nhật thành công!";
    header("Location: books.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sửa sách</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h2>Sửa thông tin sách</h2>
    <form method="POST">
        <input type="text" name="title" value="<?= $row['title'] ?>" required><br>
        <input type="text" name="author" value="<?= $row['author'] ?>" required><br>
        <input type="text" name="category" value="<?= $row['category'] ?>"><br>
        <input type="number" name="year" value="<?= $row['year'] ?>"><br>
        <button type="submit" name="update">Cập nhật</button>
    </form>
    <p class="success"><?= $message ?></p>
</div>
</body>
</html>
