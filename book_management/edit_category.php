<?php
require 'config.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

// Get category to edit
$category = null;
if (isset($_GET['id'])) {
    $catId = intval($_GET['id']);
    $res = mysqli_query($conn, "SELECT id, name FROM categories WHERE id = $catId");
    if ($res && mysqli_num_rows($res) > 0) {
        $category = mysqli_fetch_assoc($res);
    } else {
        header("Location: categories.php");
        exit;
    }
} else {
    header("Location: categories.php");
    exit;
}

$editError = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newName = mysqli_real_escape_string($conn, $_POST['category_name']);
    $catId   = intval($_POST['cat_id']);

    if (!empty($newName)) {
        $sql = "UPDATE categories SET name = '$newName' WHERE id = $catId";
        if (mysqli_query($conn, $sql)) {
            header("Location: categories.php");
            exit;
        } else {
            $editError = "Failed to update category. Please try again.";
        }
    } else {
        $editError = "Category name cannot be empty.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Category</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">

    <h2>Edit Category</h2>

    <!-- Navigation -->
    <nav class="nav">
        <a href="dashboard.php">Dashboard</a>
        <a href="categories.php">Back to Categories</a>
        <a href="logout.php">Logout</a>
    </nav>

    <hr>

    <!-- Error message -->
    <?php if ($editError): ?>
        <p class="error"><?php echo $editError; ?></p>
    <?php endif; ?>

    <!-- Edit form -->
    <?php if ($category): ?>
        <form method="post" action="edit_category.php?id=<?php echo $category['id']; ?>">
            <input type="hidden" name="cat_id" value="<?php echo $category['id']; ?>">

            <label for="category_name">Category Name</label>
            <input
                type="text"
                name="category_name"
                id="category_name"
                value="<?php echo htmlspecialchars($category['name']); ?>"
                required
            >

            <button type="submit" class="btn">Save Changes</button>
        </form>
    <?php endif; ?>

</div>

</body>
</html>
