<?php
session_start();
header('Content-Type: application/json');

// Kết nối database
$conn = new mysqli("localhost", "root", "", "bobittet");
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Lỗi kết nối database!"]);
    exit;
}

// Kiểm tra người dùng đã đăng nhập chưa
if (!isset($_SESSION['user']) || !isset($_SESSION['user']['id'])) {
    echo json_encode(["success" => false, "message" => "Bạn cần đăng nhập để đánh giá!"]);
    exit;
}

$user_id = $_SESSION['user']['id'];
$menu_item_id = isset($_POST['menu_item_id']) ? intval($_POST['menu_item_id']) : 0;
$rating = isset($_POST['rating']) ? intval($_POST['rating']) : 0;
$comment = trim($_POST['comment'] ?? "");

// 🚨 Kiểm tra dữ liệu đầu vào
if ($menu_item_id <= 0) {
    echo json_encode(["success" => false, "message" => "Món ăn không hợp lệ!"]);
    exit;
}

if ($rating < 1 || $rating > 5) {
    echo json_encode(["success" => false, "message" => "Số sao phải từ 1 đến 5!"]);
    exit;
}

if (empty($comment)) {
    echo json_encode(["success" => false, "message" => "Bình luận không được để trống!"]);
    exit;
}

// Kiểm tra xem món ăn có tồn tại không
$sql_check = "SELECT id FROM menu_items WHERE id = ?";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("i", $menu_item_id);
$stmt_check->execute();
$stmt_check->store_result();

if ($stmt_check->num_rows == 0) {
    echo json_encode(["success" => false, "message" => "Món ăn không tồn tại!"]);
    exit;
}
$stmt_check->close();

// Chèn đánh giá vào database
$sql = "INSERT INTO menu_reviews (menu_item_id, user_id, rating, comment, created_at) VALUES (?, ?, ?, ?, NOW())";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iiis", $menu_item_id, $user_id, $rating, $comment);

if ($stmt->execute()) {
    echo json_encode([
        "success" => true,
        "message" => "Đánh giá đã được gửi thành công!",
        "data" => [
            "user_id" => $user_id,
            "rating" => $rating,
            "comment" => htmlspecialchars($comment), // Ngăn chặn XSS
            "created_at" => date("Y-m-d H:i:s")
        ]
    ]);
} else {
    echo json_encode(["success" => false, "message" => "Lỗi khi gửi đánh giá!"]);
}

$stmt->close();
$conn->close();
?>
