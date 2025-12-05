<?php
// index.php - Trang đăng nhập người dùng (và là trang đầu tiên của ứng dụng)

// Khởi động phiên làm việc
session_start();

// Nếu đã đăng nhập (có session) thì chuyển hướng đến trang dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

// Kết nối CSDL
require 'config.php';

// Xử lý trường hợp có cookie "remember me" để tự động đăng nhập
if (!isset($_SESSION['user_id']) && isset($_COOKIE['user_id'])) {
    // Cookie lưu ID người dùng, tiến hành đăng nhập tự động
    $uid = $_COOKIE['user_id'];
    $uid = intval($uid); // Chuyển thành số nguyên để tránh lỗi hoặc tấn công
    $result = $mysqli->query("SELECT * FROM users WHERE id = $uid");
    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
        // Tạo session đăng nhập từ thông tin trong cookie
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['is_admin'] = $user['is_admin'];
        // Chuyển hướng vào trang dashboard sau khi đăng nhập tự động
        header("Location: dashboard.php");
        exit();
    }
}

// Biến thông báo lỗi (nếu đăng nhập sai)
$error_msg = "";

// Xử lý khi người dùng gửi form đăng nhập
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']); // checkbox "Ghi nhớ đăng nhập"

    // Truy vấn kiểm tra user trong CSDL (tìm theo username)
    $username_esc = $mysqli->real_escape_string($username);
    $res = $mysqli->query("SELECT * FROM users WHERE username = '$username_esc'");
    if ($res && $res->num_rows > 0) {
        $user = $res->fetch_assoc();
        // Kiểm tra mật khẩu bằng password_verify với hash lưu trong DB
        if (password_verify($password, $user['password'])) {
            // Mật khẩu đúng -> Tạo session đăng nhập
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['is_admin'] = $user['is_admin'];
            // Nếu chọn "ghi nhớ đăng nhập" thì tạo cookie lưu user_id trong 30 ngày
            if ($remember) {
                setcookie('user_id', $user['id'], time() + (30*24*60*60), "/"); 
                // Lưu ý bảo mật: Sử dụng trực tiếp user_id trong cookie có thể bị giả mạo:contentReference[oaicite:6]{index=6}.
                // Giải pháp tốt hơn là tạo token ngẫu nhiên lưu ở cả cookie và DB, nhưng ở đây dùng cách đơn giản cho minh họa.
            }
            // Chuyển hướng đến dashboard sau khi đăng nhập thành công
            header("Location: dashboard.php");
            exit();
        } else {
            // Mật khẩu không đúng
            $error_msg = "Sai mật khẩu. Vui lòng thử lại.";
        }
    } else {
        // Không tìm thấy user với username đã nhập
        $error_msg = "Tên đăng nhập không tồn tại.";
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng nhập</title>
    <style>
        /* Một số style cơ bản cho form đăng nhập */
        body { font-family: Arial, sans-serif; }
        .login-form { width: 300px; margin: 100px auto; border: 1px solid #ccc; padding: 20px; }
        .login-form h2 { text-align: center; }
        .login-form label { display: block; margin-top: 10px; }
        .login-form input[type=text], .login-form input[type=password] {
            width: 100%; padding: 5px; box-sizing: border-box;
        }
        .login-form input[type=submit] { margin-top: 15px; width: 100%; }
        .error { color: red; text-align: center; }
    </style>
</head>
<body>
    <div class="login-form">
        <h2>Đăng nhập</h2>
        <?php if ($error_msg): ?>
            <p class="error"><?php echo $error_msg; ?></p>
        <?php endif; ?>
        <form method="post" action="index.php">
            <label>Tên đăng nhập:</label>
            <input type="text" name="username" required>
            <label>Mật khẩu:</label>
            <input type="password" name="password" required>
            <label style="display:flex; align-items:center; margin-top:5px;">
                <input type="checkbox" name="remember" style="margin-right:5px;">
                Ghi nhớ đăng nhập
            </label>
            <input type="submit" value="Đăng nhập">
        </form>
        <p style="text-align:center; margin-top:10px;">
            Chưa có tài khoản? <a href="signup.php">Đăng ký</a>
        </p>
    </div>
</body>
</html>
