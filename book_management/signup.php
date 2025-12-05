<?php
// signup.php - Trang đăng ký người dùng mới

session_start();
// Nếu đã đăng nhập rồi thì không cho đăng ký, chuyển hướng về dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

require 'config.php';

$error_msg = "";
$success_msg = "";

// Xử lý khi người dùng gửi form đăng ký
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $password2 = $_POST['password2'] ?? '';

    // Kiểm tra các trường có được điền đầy đủ
    if (empty($username) || empty($password) || empty($password2)) {
        $error_msg = "Vui lòng điền đầy đủ thông tin.";
    } elseif ($password !== $password2) {
        $error_msg = "Mật khẩu nhập lại không khớp.";
    } else {
        // Kiểm tra xem username đã tồn tại chưa
        $username_esc = $mysqli->real_escape_string($username);
        $res = $mysqli->query("SELECT id FROM users WHERE username = '$username_esc'");
        if ($res && $res->num_rows > 0) {
            $error_msg = "Tên đăng nhập đã tồn tại. Vui lòng chọn tên khác.";
        } else {
            // Thực hiện đăng ký tài khoản mới
            $hash = password_hash($password, PASSWORD_DEFAULT);
            // Mặc định người đăng ký qua form này là user thường (is_admin = 0)
            $insert = $mysqli->query("INSERT INTO users (username, password, is_admin) VALUES ('$username_esc', '$hash', 0)");
            if ($insert) {
                // Đăng ký thành công
                $success_msg = "Đăng ký thành công! Bạn có thể đăng nhập.";
            } else {
                $error_msg = "Đã có lỗi xảy ra. Vui lòng thử lại.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng ký tài khoản</title>
    <style>
        /* Style tương tự form đăng nhập */
        body { font-family: Arial, sans-serif; }
        .signup-form { width: 300px; margin: 100px auto; border: 1px solid #ccc; padding: 20px; }
        .signup-form h2 { text-align: center; }
        .signup-form label { display: block; margin-top: 10px; }
        .signup-form input[type=text], .signup-form input[type=password] {
            width: 100%; padding: 5px;
        }
        .signup-form input[type=submit] { margin-top: 15px; width: 100%; }
        .error { color: red; text-align: center; }
        .success { color: green; text-align: center; }
    </style>
</head>
<body>
    <div class="signup-form">
        <h2>Đăng ký</h2>
        <?php if ($error_msg): ?>
            <p class="error"><?php echo $error_msg; ?></p>
        <?php elseif ($success_msg): ?>
            <p class="success"><?php echo $success_msg; ?></p>
        <?php endif; ?>
        <form method="post" action="signup.php">
            <label>Tên đăng nhập:</label>
            <input type="text" name="username" required>
            <label>Mật khẩu:</label>
            <input type="password" name="password" required>
            <label>Nhập lại mật khẩu:</label>
            <input type="password" name="password2" required>
            <input type="submit" value="Đăng ký">
        </form>
        <p style="text-align:center; margin-top:10px;">
            Đã có tài khoản? <a href="index.php">Đăng nhập</a>
        </p>
    </div>
</body>
</html>
