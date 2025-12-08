<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'config.php';

// If already logged in, redirect away from signup
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'admin') {
        header("Location: dashboard.php");
    } else {
        header("Location: loans_user.php");
    }
    exit();
}

$signup_error = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name            = $_POST['name']     ?? '';
    $username_input  = $_POST['username'] ?? '';
    $password_input  = $_POST['password'] ?? '';
    $role_input      = $_POST['role']     ?? '';
    
    // Basic validation for empty fields
    if ($name == "" || $username_input == "" || $password_input == "" || $role_input == "") {
        $signup_error = "Vui lòng điền đầy đủ thông tin.";
    } else {
        // Check if username already exists (regardless of role)
        $stmt_check = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt_check->bind_param("s", $username_input);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();
        if ($result_check && $result_check->num_rows > 0) {
            $signup_error = "Tên đăng nhập đã tồn tại. Vui lòng chọn tên khác.";
        } else {
            // Hash the password before storing (for security)
            $hashed_password = password_hash($password_input, PASSWORD_DEFAULT);
            // Insert new user into database
            $stmt = $conn->prepare("INSERT INTO users (name, username, password, role) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $name, $username_input, $hashed_password, $role_input);
            if ($stmt->execute()) {
                // Registration successful: redirect to login with success message
                $stmt->close();
                $stmt_check->close();
                header("Location: index.php?signup=success");
                exit();
            } else {
                $signup_error = "Đã có lỗi xảy ra. Vui lòng thử lại.";
            }
        }
        $stmt_check->close();
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng ký</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h1>Đăng ký tài khoản</h1>
    <!-- Display error message if any -->
    <?php if (!empty($signup_error)): ?>
        <p class="error"><?php echo $signup_error; ?></p>
    <?php endif; ?>
    <form method="post" action="">
        <label for="name">Họ và tên:</label><br>
        <input type="text" id="name" name="name" required><br><br>
        
        <label for="username">Tên đăng nhập:</label><br>
        <input type="text" id="username" name="username" required><br><br>
        
        <label for="password">Mật khẩu:</label><br>
        <input type="password" id="password" name="password" required><br><br>
        
        <!-- Role selection added for signup -->
        <label for="role">Đăng ký với quyền:</label><br>
        <select id="role" name="role" required>
            <option value="user">Người dùng</option>
            <option value="admin">Quản trị viên</option>
        </select>
        <br><br>
        
        <button type="submit">Đăng ký</button>
        <p>Đã có tài khoản? <a href="index.php">Đăng nhập</a></p>
    </form>
</div>
</body>
</html>
