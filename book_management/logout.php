<?php
// logout.php
session_start();

// Phải trùng với index.php
define('REMEMBER_COOKIE', 'remember_token');

// ====== Xoá token remember lưu trên server (file tạm) ======
if (!empty($_SESSION['user_id'])) {
    $uid = (int)$_SESSION['user_id'];
    $file = sys_get_temp_dir() . "/book_mgmt_remember/u_" . $uid . ".txt";
    if (file_exists($file)) {
        @unlink($file);
    }
}

// ====== Xoá cookie remember_token trên trình duyệt ======
if (isset($_COOKIE[REMEMBER_COOKIE])) {
    // Xoá cookie bằng cách set hết hạn
    setcookie(
        REMEMBER_COOKIE,
        '',
        [
            'expires'  => time() - 3600,
            'path'     => '/',
            'secure'   => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
            'httponly' => true,
            'samesite' => 'Lax',
        ]
    );
}

// ====== Xoá session ======
$_SESSION = [];
session_unset();

// Xoá luôn cookie session PHPSESSID (tốt hơn)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
    );
}

session_destroy();

// Redirect về trang login
header("Location: index.php?logout=success");
exit();
?>
