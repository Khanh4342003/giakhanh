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

// Lấy thông tin người dùng từ database
$sql = "SELECT fullName, email, phoneNumber, address, birthDate, gender FROM users WHERE KeyValue = ?";
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
    <title>Trang Cá Nhân - LJS Steak</title>
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
                <li><a href="giohang.php">Giỏ hàng</a></li>

                <?php if (isset($_SESSION['user'])): ?>
                    <li><a href="trangcanhan.php" class="active">Xin chào, <?php echo $_SESSION['user']['fullName']; ?></a></li>
                    <li><a href="dangxuat.php">Đăng xuất</a></li>
                <?php else: ?>
                    <li><a href="dangnhap.php">Đăng nhập</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</header>

<!-- THÔNG TIN CÁ NHÂN -->
<div class="container">
    <h1>Thông Tin Cá Nhân</h1>
    <div class="profile-card">
        <div class="profile-info">
            <p><strong>Họ và Tên:</strong> <?php echo htmlspecialchars($user['fullName']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
            <p><strong>Số điện thoại:</strong> <?php echo htmlspecialchars($user['phoneNumber']); ?></p>
            <p><strong>Địa chỉ:</strong> <?php echo htmlspecialchars($user['address']); ?></p>
            <p><strong>Ngày sinh:</strong> <?php echo htmlspecialchars($user['birthDate']); ?></p>
            <p><strong>Giới tính:</strong> <?php echo ($user['gender'] === 'male') ? 'Nam' : 'Nữ'; ?></p>
        </div>
        <a href="chinhsuathongtin.php" class="edit-button">Chỉnh sửa thông tin</a>
    </div>
</div>

<!-- FOOTER -->
<footer>
    <div class="container">
        <p>&copy; 2025 Hào Khánh Cường - LJS Steak.</p>
    </div>
</footer>

</body>
</html>
