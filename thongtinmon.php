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

// Lấy ID món ăn từ URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Món ăn không tồn tại!");
}

$id = intval($_GET['id']);

// Lấy thông tin món ăn
$sql = "SELECT * FROM menu_items WHERE id = $id";
$result = $conn->query($sql);
if ($result->num_rows == 0) {
    die("Không tìm thấy món ăn!");
}
$mon = $result->fetch_assoc();

// Đường dẫn ảnh
$imagePath = $mon['image'];
if (!filter_var($imagePath, FILTER_VALIDATE_URL)) { 
    $imagePath = "img/" . $imagePath;
}

?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($mon['name']); ?> - Chi Tiết Món Ăn</title>
    <link rel="stylesheet" href="index.css">
    <link rel="stylesheet" href="thongtinmon.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
                <li><a href="giohang.php">Giỏ hàng</a></li>
                <?php if (isset($_SESSION['user'])): ?>
                    <li><a href="trangcanhan.php">Xin chào, <?php echo htmlspecialchars($_SESSION['user']['fullName']); ?></a></li>
                    <li><a href="dangxuat.php">Đăng xuất</a></li>
                <?php else: ?>
                    <li><a href="dangnhap.php">Đăng nhập</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</header>

<div class="container">
    <div class="product">
        <div class="product-gallery">
            <img class="product-image" src="<?php echo $imagePath; ?>" alt="<?php echo htmlspecialchars($mon['name']); ?>" 
                 onerror="this.onerror=null; this.src='img/default.jpg';">
        </div>

        <div class="product-info">
            <h1><?php echo htmlspecialchars($mon['name']); ?></h1>
            <p><?php echo htmlspecialchars($mon['description']); ?></p>
            
            <span class="original-price"><del><?php echo number_format($mon['price'] * 1.2); ?> VND</del></span>
            <span class="discount-price" id="total-price"><?php echo number_format($mon['price']); ?> VND</span>

            <form method="post" action="themvaogio.php">
                <input type="hidden" name="id" value="<?php echo $mon['id']; ?>">
                <input type="hidden" name="price" id="item-price" value="<?php echo $mon['price']; ?>">
                <input type="number" name="quantity" id="quantity" value="1" min="1" oninput="updatePrice()">
                <button type="submit" class="add-to-cart">Thêm vào giỏ hàng</button>
            </form>
        </div>
    </div>
</div>

<!-- Hiển thị đánh giá -->
<div class="reviews">
    <h2>Đánh giá món ăn</h2>

    <div id="review-list">
        <?php
        $sql_reviews = "SELECT * FROM menu_reviews WHERE menu_item_id = $id ORDER BY created_at DESC";
        $reviews = $conn->query($sql_reviews);
        while ($row = $reviews->fetch_assoc()):
        ?>
            <div class="review-item">
                <p><strong>Người dùng #<?php echo $row['user_id']; ?></strong> - 
                   <span class="rating"><?php echo str_repeat("⭐", $row['rating']); ?></span></p>
                <p><?php echo htmlspecialchars($row['comment']); ?></p>
                <small><?php echo $row['created_at']; ?></small>
            </div>
        <?php endwhile; ?>
    </div>

    <!-- Form đánh giá -->
    <?php if (isset($_SESSION['user'])): ?>
        <h3>Viết đánh giá</h3>
        <form id="review-form">
            <input type="hidden" name="menu_item_id" value="<?php echo $id; ?>">
            <label for="rating">Chọn số sao:</label>
            <select name="rating" id="rating" required>
                <option value="5">⭐ ⭐ ⭐ ⭐ ⭐</option>
                <option value="4">⭐ ⭐ ⭐ ⭐</option>
                <option value="3">⭐ ⭐ ⭐</option>
                <option value="2">⭐ ⭐</option>
                <option value="1">⭐</option>
            </select>
            
            <label for="comment">Bình luận:</label>
            <textarea name="comment" id="comment" rows="4" required></textarea>

            <button type="submit">Gửi đánh giá</button>
        </form>
    <?php else: ?>
        <p><a href="dangnhap.php">Đăng nhập</a> để đánh giá món ăn này.</p>
    <?php endif; ?>
</div>

<footer>
    <div class="container">
        <p>&copy; 2025 Hào Khánh Cường - Bò Bít Tết.</p>
    </div>
</footer>

<script>
$(document).ready(function() {
    $("#review-form").submit(function(e) {
        e.preventDefault(); // Chặn tải lại trang

        let menuItemId = <?php echo json_encode($id); ?>;
        let userId = <?php echo isset($_SESSION['user']['id']) ? json_encode($_SESSION['user']['id']) : 'null'; ?>;
        
        if (!userId) {
            alert("Bạn cần đăng nhập để đánh giá!");
            return;
        }

        let rating = $("#rating").val();
        let comment = $("#comment").val().trim();

        if (comment === "") {
            alert("Bình luận không được để trống!");
            return;
        }

        let formData = {
            menu_item_id: menuItemId,
            user_id: userId,
            rating: rating,
            comment: comment
        };

        $.post("xuly_danhgia.php", formData, function(response) {
            if (response.success) {
                $("#review-list").prepend(`
                    <div class="review-item">
                        <p><strong>Bạn</strong> - 
                           <span class="rating">${'⭐'.repeat(response.data.rating)}</span></p>
                        <p>${response.data.comment}</p>
                        <small>${response.data.created_at}</small>
                    </div>
                `);
                $("#comment").val(""); // Xóa nội dung comment sau khi gửi
            } else {
                alert(response.message);
            }
        }, "json").fail(function() {
            alert("Lỗi kết nối, vui lòng thử lại sau!");
        });
    });
});
</script>

<script>
$(document).ready(function() {
    // Khi thay đổi số lượng, cập nhật giá
    $("input[name='quantity']").on("input", function() {
        let quantity = $(this).val();
        let price = parseInt($("input[name='price']").val()); // Lấy giá gốc từ input ẩn
        let totalPrice = quantity * price;

        // Hiển thị giá mới
        $(".discount-price").text(totalPrice.toLocaleString() + " VND");
    });
});
</script>

</body>
</html>
