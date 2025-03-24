<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Kết nối database
$conn = new mysqli("localhost", "root", "", "bobittet");

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Mảng giỏ hàng
$cart_items = [];

// Nếu người dùng đã đăng nhập, lấy giỏ hàng từ database
if (isset($_SESSION['user']) && isset($_SESSION['user']['id'])) {
    $user_id = $_SESSION['user']['id'];  // 🔹 Sửa KeyValue thành id
    $sql = "SELECT cart.item_id, cart.quantity, menu_items.name, menu_items.price, menu_items.image 
            FROM cart 
            JOIN menu_items ON cart.item_id = menu_items.id 
            WHERE cart.user_id = '$user_id'";
    $result = $conn->query($sql);

    while ($row = $result->fetch_assoc()) {
        $cart_items[$row['item_id']] = [
            'name' => $row['name'],
            'price' => $row['price'],
            'image' => $row['image'],
            'quantity' => $row['quantity']
        ];
    }
} else {
    // Nếu chưa đăng nhập, lấy từ session
    if (isset($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $item_id => $quantity) {
            $sql = "SELECT * FROM menu_items WHERE id = $item_id";
            $result = $conn->query($sql);
            if ($row = $result->fetch_assoc()) {
                $cart_items[$item_id] = [
                    'name' => $row['name'],
                    'price' => $row['price'],
                    'image' => $row['image'],
                    'quantity' => $quantity
                ];
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giỏ hàng</title>
    <link rel="stylesheet" href="index.css">
</head>
<body>

<header>
    <div class="header-container">
        <div class="logo">
            <a href="index.php"><img src="img/logobobittet.png" alt="Logo"></a>
        </div>
        <nav>
            <ul>
                <li><a href="index.php">Trang chủ</a></li>
                <li><a href="#">Thông tin</a></li>
                <li><a href="thucdon.php">Thực đơn</a></li>
                <li><a href="#">Khuyến mãi</a></li>
                <li><a href="giohang.">Giỏ hàng</a></li>
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

<div class="container">
    <h1>Giỏ hàng của bạn</h1>

    <?php if (empty($cart_items)): ?>
        <p>Giỏ hàng của bạn đang trống.</p>
    <?php else: ?>
        <table>
            <tr>
                <th>Hình ảnh</th>
                <th>Tên món</th>
                <th>Giá</th>
                <th>Số lượng</th>
                <th>Thành tiền</th>
                <th>Hành động</th>
            </tr>
            <?php 
            $total = 0;
            foreach ($cart_items as $item_id => $item): 
                $subtotal = $item['price'] * $item['quantity'];
                $total += $subtotal;
            ?>
                <tr>
                    <td><img src="img/<?php echo $item['image']; ?>" width="50"></td>
                    <td><?php echo $item['name']; ?></td>
                    <td><?php echo number_format($item['price']); ?> VND</td>
                    <td><?php echo $item['quantity']; ?></td>
                    <td><?php echo number_format($subtotal); ?> VND</td>
                    <td>
                        <a href="xoagiohang.php?id=<?php echo $item_id; ?>">Xóa</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            <tr>
                <td colspan="4"><strong>Tổng cộng:</strong></td>
                <td><strong><?php echo number_format($total); ?> VND</strong></td>
                <td></td>
            </tr>
        </table>
        <button onclick="window.location.href='thanhtoan.php'">Thanh toán</button>
    <?php endif; ?>
</div>

<footer>
    <p>&copy; 2025 Hào Khánh Cường - Bò Bít Tết.</p>
</footer>

</body>
</html>
