<?php session_start(); ?>
<div class="navbar">
    <a href="dashboard.php" class="logo">📚 Book Management</a>
    <?php if (isset($_SESSION['user'])): ?>
        <div class="nav-links">
            <a href="dashboard.php">Dashboard</a>
            <a href="books.php">Books</a>
            <a href="add_book.php">Add Book</a>
            <a href="logout.php">Logout</a>
        </div>
    <?php endif; ?>
</div>
