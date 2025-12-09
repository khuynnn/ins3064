<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

$msg = "";

// Khi submit form thì nâng quyền
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user_id = (int)($_POST['user_id'] ?? 0);

    if ($user_id > 0) {
        // chỉ nâng những người đang là user
        $stmt = $conn->prepare("UPDATE users SET role='admin' WHERE id=? AND role='user'");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();

        $msg = ($stmt->affected_rows > 0)
            ? "✅ Nâng quyền thành công!"
            : "⚠️ Không nâng được (có thể user đã là admin hoặc không tồn tại).";
    }
}

// Lấy danh sách user để chọn
$users = [];
$res = $conn->query("SELECT id, username, name FROM users WHERE role='user' ORDER BY id DESC");
if ($res) {
    while ($row = $res->fetch_assoc()) $users[] = $row;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Nâng quyền</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="nav">
    <a href="dashboard.php">Tổng quan</a>
    <a href="logout.php">Đăng xuất</a>
</div>

<div class="container">
    <h1>Nâng user lên admin</h1>

    <?php if ($msg != ""): ?>
        <p><?php echo htmlspecialchars($msg); ?></p>
    <?php endif; ?>

    <?php if (count($users) === 0): ?>
        <p>Không có user nào để nâng quyền.</p>
    <?php else: ?>
        <form method="post">
            <label>Chọn user:</label>
            <select name="user_id" required>
                <?php foreach ($users as $u): ?>
                    <option value="<?php echo (int)$u['id']; ?>">
                        #<?php echo (int)$u['id']; ?> -
                        <?php echo htmlspecialchars($u['username']); ?>
                        <?php if (!empty($u['name'])) echo " (" . htmlspecialchars($u['name']) . ")"; ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <button class="qa-btn" type="submit">Nâng quyền</button>
        </form>
    <?php endif; ?>
</div>

</body>
</html>
