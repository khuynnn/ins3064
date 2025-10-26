<?php
include "connection.php";

// Kiểm tra có id không
if (!isset($_GET["id"])) {
    header("Location: index.php");
    exit();
}

$id = $_GET["id"];

// Nếu người dùng bấm nút "Yes" trong form
if (isset($_POST["yes"])) {
    mysqli_query($link, "DELETE FROM table1 WHERE id = $id");
    echo "<script>
        alert('Record deleted successfully!');
        window.location.href = 'index.php';
    </script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Confirm Delete</title>

    <!-- Bootstrap CSS & JS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

    <style>
        body {
            background-color: #f8f9fa;
        }
        .modal-dialog {
            margin-top: 20%;
        }
        .modal-header {
            background-color: #d9534f;
            color: white;
        }
    </style>
</head>
<body>

<!-- Modal xác nhận -->
<div id="confirmDeleteModal" class="modal fade in" tabindex="-1" role="dialog" style="display:block;">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Confirm Deletion</h4>
      </div>
      <div class="modal-body">
        <p>Are you sure you want to delete this record (ID: <?php echo $id; ?>)?</p>
      </div>
      <div class="modal-footer">
        <form method="post">
            <button type="submit" name="yes" class="btn btn-danger">Yes</button>
            <a href="index.php" class="btn btn-default">No</a>
        </form>
      </div>
    </div>
  </div>
</div>

</body>
</html>
