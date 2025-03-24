<?php
session_start();
include 'db.php'; // Kết nối database

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    // Kiểm tra kết nối database
    if (!$conn) {
        die("Lỗi kết nối database: " . mysqli_connect_error());
    }

    // Lấy dữ liệu từ form và xử lý
    $fullName = trim($_POST['fullName']);
    $phoneNumber = trim($_POST['phoneNumber']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $address = trim($_POST['address']);
    $birthDate = $_POST['dob']; // Đổi thành birthDate cho đúng với cột database
    $gender = $_POST['gender'];
    $roleId = $_POST['roleId'];

    // Kiểm tra dữ liệu đầu vào
    if (empty($fullName) || empty($phoneNumber) || empty($email) || empty($password) || empty($address) || empty($birthDate) || empty($gender) || empty($roleId)) {
        echo "<script>alert('Vui lòng nhập đầy đủ thông tin!');</script>";
        exit;
    }

    // Kiểm tra email có hợp lệ không
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Email không hợp lệ!');</script>";
        exit;
    }

    // Kiểm tra số điện thoại chỉ chứa số
    if (!preg_match('/^[0-9]+$/', $phoneNumber)) {
        echo "<script>alert('Số điện thoại không hợp lệ!');</script>";
        exit;
    }

    // Mã hóa mật khẩu trước khi lưu vào database
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // Kiểm tra email hoặc số điện thoại đã tồn tại chưa
    $checkQuery = "SELECT KeyValue FROM users WHERE email = ? OR phoneNumber = ?";
    $stmt = $conn->prepare($checkQuery);

    if (!$stmt) {
        die("Lỗi SQL (check tồn tại): " . $conn->error);
    }

    $stmt->bind_param("ss", $email, $phoneNumber);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo "<script>alert('Email hoặc số điện thoại đã tồn tại!');</script>";
    } else {
        // Tạo giá trị KeyValue (có thể là UUID hoặc random string)
        $KeyValue = uniqid('user_', true);

        // Thêm dữ liệu vào database
        $query = "INSERT INTO users (KeyValue, fullName, phoneNumber, email, password, address, birthDate, gender, roleId) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);

        if ($stmt) {
            $stmt->bind_param("ssssssssi", $KeyValue, $fullName, $phoneNumber, $email, $hashedPassword, $address, $birthDate, $gender, $roleId);
            if ($stmt->execute()) {
                echo "<script>alert('Đăng ký thành công!'); window.location.href='dangnhap.php';</script>";
            } else {
                echo "<script>alert('Lỗi khi đăng ký: " . $stmt->error . "');</script>";
            }
        } else {
            echo "<script>alert('Lỗi chuẩn bị câu lệnh SQL: " . $conn->error . "');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký</title>
    <link rel="stylesheet" href="dangky.css">
</head>
<body>
<div class="register-container">
    <form method="POST" action="">
        <h2>Đăng ký tài khoản</h2>

        <label for="fullName">Họ và tên:</label>
        <input type="text" name="fullName" placeholder="Nhập họ và tên" required>

        <label for="phoneNumber">Số điện thoại:</label>
        <input type="text" name="phoneNumber" placeholder="Nhập số điện thoại" required>

        <label for="email">Email:</label>
        <input type="email" name="email" placeholder="Nhập địa chỉ email" required>

        <label for="password">Mật khẩu:</label>
        <input type="password" name="password" placeholder="Nhập mật khẩu" required>

        <label for="address">Địa chỉ:</label>
        <input type="text" name="address" placeholder="Nhập địa chỉ" required>

        <label for="dob">Ngày sinh:</label>
        <input type="date" name="dob" required>

        <label for="gender">Giới tính:</label>
        <select name="gender" required>
            <option value="male">Nam</option>
            <option value="female">Nữ</option>
            <option value="other">Khác</option>
        </select>

        <label for="roleId">Vai trò:</label>
        <select name="roleId">
            <option value="1">Người dùng</option>
            <option value="2">Quản trị viên</option>
        </select>

        <button type="submit" name="register" class="btn-register">Đăng ký</button>

        <div class="back-to-login">
            <a href="dangnhap.php">Quay về trang đăng nhập</a>
        </div>
    </form>
</div>
</body>
</html>
