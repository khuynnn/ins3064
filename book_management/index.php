<?php
session_start();
include 'config.php';

// If already logged in, redirect to appropriate dashboard
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'admin') {
        header("Location: dashboard.php");
    } else {
        header("Location: loans_user.php");
    }
    exit();
}

// Handle login form submission
$login_error = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form input
    $username_input = $_POST['username'] ?? '';
    $password_input = $_POST['password'] ?? '';
    $role_input     = $_POST['role']     ?? '';
    
    // Prepare and execute query to find user with matching username and role
    $stmt = $conn->prepare("SELECT id, name, username, password, role FROM users WHERE username = ? AND role = ?");
    $stmt->bind_param("ss", $username_input, $role_input);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
        // Verify entered password against the hashed password in DB
        if (password_verify($password_input, $user['password'])) {
            // Credentials are correct: initialize session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['role'] = $user['role'];
            // Redirect based on role
            if ($user['role'] === 'admin') {
                header("Location: dashboard.php");
            } else {
                header("Location: loans_user.php");
            }
            exit();
        } else {
            // Password did not match
            $login_error = "Tên đăng nhập hoặc mật khẩu không đúng.";
        }
    } else {
        // No user found with given username/role
        $login_error = "Tên đăng nhập, mật khẩu hoặc quyền không đúng.";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng nhập</title>
    <link rel="stylesheet" href="style.css"> <!-- Link to CSS for styling -->
</head>
<body>
<div class="container">
    <h1>Đăng nhập</h1>
    <!-- Display error message if login fails -->
    <?php if (!empty($login_error)): ?>
        <p class="error"><?php echo $login_error; ?></p>
    <?php endif; ?>
    <!-- Display success message after registration -->
    <?php if (isset($_GET['signup']) && $_GET['signup'] == 'success'): ?>
        <p class="success">Tạo tài khoản thành công! Mời bạn đăng nhập.</p>
    <?php endif; ?>
    <form method="post" action="">
        <label for="username">Tên đăng nhập:</label><br>
        <input type="text" id="username" name="username" required><br><br>
        
        <label for="password">Mật khẩu:</label><br>
        <input type="password" id="password" name="password" required><br><br>
        
        <!-- Role selection added for login -->
        <label for="role">Đăng nhập với quyền:</label><br>
        <select id="role" name="role" required>
            <option value="user">Người dùng</option>
            <option value="admin">Quản trị viên</option>
        </select>
        <br><br>
        
        <button type="submit">Đăng nhập</button>
    </form>
    <p>Chưa có tài khoản? <a href="signup.php">Đăng ký</a></p>
</div>
</body>
</html>
