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

// Kiểm tra dữ liệu gửi lên
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $item_id = intval($_POST['id']);
    $quantity = intval($_POST['quantity']);
    
    if ($quantity < 1) {
        $quantity = 1;
    }

    // Kiểm tra xem món ăn có tồn tại không
    $sql = "SELECT * FROM menu_items WHERE id = $item_id";
    $result = $conn->query($sql);
    if ($result->num_rows == 0) {
        die("Món ăn không tồn tại!");
    }

    // Nếu đã đăng nhập, lưu vào database
    if (isset($_SESSION['user'])) {
        $user_id = $_SESSION['user']['KeyValue'];

        // Kiểm tra xem món đã có trong giỏ hàng chưa
        $check_sql = "SELECT * FROM cart WHERE user_id = '$user_id' AND item_id = $item_id";
        $check_result = $conn->query($check_sql);

        if ($check_result->num_rows > 0) {
            // Nếu đã có, cập nhật số lượng
            $update_sql = "UPDATE cart SET quantity = quantity + $quantity WHERE user_id = '$user_id' AND item_id = $item_id";
            $conn->query($update_sql);
        } else {
            // Nếu chưa có, thêm mới
            $insert_sql = "INSERT INTO cart (user_id, item_id, quantity) VALUES ('$user_id', $item_id, $quantity)";
            $conn->query($insert_sql);
        }
    } else {
        // Nếu chưa đăng nhập, lưu vào session
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        if (isset($_SESSION['cart'][$item_id])) {
            $_SESSION['cart'][$item_id] += $quantity;
        } else {
            $_SESSION['cart'][$item_id] = $quantity;
        }
    }

    // Chuyển hướng về trang giỏ hàng
    header("Location: giohang.php");
    exit();
}

?>
