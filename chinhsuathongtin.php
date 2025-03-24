<?php
session_start();

// Kiểm tra đăng nhập
if (!isset($_SESSION['user']) || empty($_SESSION['user']['id'])) {
    echo "<script>alert('Bạn chưa đăng nhập!'); window.location.href='dangnhap.php';</script>";
    exit();
}

// Kết nối database
$conn = new mysqli("localhost", "root", "", "bobittet");

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

$user_id = $_SESSION['user']['id'];

// Xử lý khi người dùng nhấn "Lưu thay đổi"
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullName = $_POST['fullName'];
    $email = $_POST['email'];
    $phoneNumber = $_POST['phoneNumber'];
    $address = $_POST['address'];
    $birthDate = $_POST['birthDate'];
    $gender = $_POST['gender'];

    // Cập nhật thông tin người dùng
    $sql = "UPDATE users SET fullName=?, email=?, phoneNumber=?, address=?, birthDate=?, gender=? WHERE KeyValue=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssi", $fullName, $email, $phoneNumber, $address, $birthDate, $gender, $user_id);

    if ($stmt->execute()) {
        echo "<script>alert('Cập nhật thông tin thành công!'); window.location.href='trangcanhan.php';</script>";
        exit();
    } else {
        echo "<script>alert('Lỗi khi cập nhật!');</script>";
    }
}

// Lấy thông tin người dùng
$sql = "SELECT fullName, email, phoneNumber, address, birthDate, gender FROM users WHERE KeyValue=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    die("Không tìm thấy thông tin người dùng!");
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chỉnh Sửa Thông Tin - LJS Steak</title>
    <link rel="stylesheet" href="index.css">
    <link rel="stylesheet" href="trangcanhan.css">
</head>
<body>

<!-- HEADER -->
<header>
    <div class="header-container">
        <div class="logo">
            <a href="index.php">
                <img src="img/logobobittet.png" alt="LJS Logo">
            </a>
        </div>
        <nav>
            <ul>
                <li><a href="index.php">Trang chủ</a></li>
                <li><a href="#">Thông tin</a></li>
                <li><a href="thucdon.php">Thực đơn</a></li>
                <li><a href="#">Khuyến mãi</a></li>
                <li><a href="#">Giỏ hàng</a></li>

                <?php if (isset($_SESSION['user'])): ?>
                    <li><a href="trangcanhan.php">Xin chào, <?php echo $_SESSION['user']['fullName']; ?></a></li>
                    <li><a href="dangxuat.php">Đăng xuất</a></li>
                <?php else: ?>
                    <li><a href="dangnhap.php">Đăng nhập</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</header>

<!-- FORM CHỈNH SỬA -->
<div class="container">
    <h1>Chỉnh Sửa Thông Tin</h1>
    <form method="POST">
        <label for="fullName">Họ và Tên:</label>
        <input type="text" name="fullName" value="<?php echo htmlspecialchars($user['fullName']); ?>" required>

        <label for="email">Email:</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

        <label for="phoneNumber">Số điện thoại:</label>
        <input type="text" name="phoneNumber" value="<?php echo htmlspecialchars($user['phoneNumber']); ?>" required>

        <label for="address">Địa chỉ:</label>
        <input type="text" name="address" value="<?php echo htmlspecialchars($user['address']); ?>" required>

        <label for="birthDate">Ngày sinh:</label>
        <input type="date" name="birthDate" value="<?php echo htmlspecialchars($user['birthDate']); ?>" required>

        <label for="gender">Giới tính:</label>
        <select name="gender" required>
            <option value="male" <?php echo ($user['gender'] === 'male') ? 'selected' : ''; ?>>Nam</option>
            <option value="female" <?php echo ($user['gender'] === 'female') ? 'selected' : ''; ?>>Nữ</option>
        </select>

        <button type="submit" class="edit-button">Lưu thay đổi</button>
    </form>
</div>

<!-- FOOTER -->
<footer>
    <div class="container">
        <p>&copy; 2025 Hào Khánh Cường - LJS Steak.</p>
    </div>
</footer>

</body>
</html>
