<?php
session_start();
include 'db.php';

if (isset($_SESSION['user'])) {
    header("Location: index.php"); // Chuyển hướng về trang chủ
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $phoneNumber = $_POST['phoneNumber'];
    $password = $_POST['password'];

    // Kiểm tra kết nối database
    if (!$conn) {
        die("Lỗi kết nối database: " . mysqli_connect_error());
    }

    $sql = "SELECT * FROM users WHERE phoneNumber = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $phoneNumber);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            // Lưu session đúng key
            $_SESSION['user'] = [
                'id' => $user['KeyValue'],  // Đảm bảo cột ID chính xác
                'fullName' => $user['fullName']
            ];

            header("Location: index.php"); // Chuyển hướng về trang chủ
            exit();
        } else {
            $error = "Sai mật khẩu!";
        }
    } else {
        $error = "Số điện thoại không tồn tại!";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Nhập</title>
    <link rel="stylesheet" href="dangnhap.css">
</head>
<body>
<div class="login-container">
    <form method="POST" action="">
        <h2>Đăng nhập</h2>
        
        <label for="phoneNumber">Số điện thoại:</label>
        <input type="text" name="phoneNumber" placeholder="Nhập số điện thoại" required>

        <label for="password">Mật khẩu:</label>
        <input type="password" name="password" placeholder="Nhập mật khẩu" required>

        <button type="submit" class="btn-login">Đăng Nhập Ngay</button>
        
        <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>

        <div class="create-account">
            <a href="dangky.php">Tạo tài khoản mới</a>
        </div>
    </form>
</div>

</body>
</html>
