<?php
session_start();

// Kết nối cơ sở dữ liệu
$conn = new mysqli("localhost", "root", "", "bobittet");

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thực Đơn - LJS Steak</title>
    <link rel="stylesheet" href="index.css">
    <link rel="stylesheet" href="thucdon.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/slick-carousel/slick/slick.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/slick-carousel/slick/slick.min.js"></script>
</head>
<body>

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
                    <li><a href="trangcanhan.php">Xin chào, <?php echo $_SESSION['user']['fullName']; ?></a></li>
                    <li><a href="dangxuat.php">Đăng xuất</a></li>
                <?php else: ?>
                    <li><a href="dangnhap.php">Đăng nhập</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</header>

<div class="menu-container">
    <h1>Thực Đơn</h1>

    <!-- Thanh điều hướng danh mục -->
    <ul class="menu-categories">
        <li class="active" data-category="khai-vi">Appetizers</li>
        <li data-category="mon-chinh">Main Course</li>
        <li data-category="trang-mieng">Desserts</li>
        <li data-category="do-uong">Drinks</li>
    </ul>

    <!-- Danh sách món ăn -->
    <div class="menu-items">
        <?php
        // Mảng ánh xạ danh mục tiếng Anh -> tiếng Việt trong database
        $category_map = [
            "khai-vi" => "Khai vị",
            "mon-chinh" => "Món chính",
            "trang-mieng" => "Tráng miệng",
            "do-uong" => "Đồ uống"
        ];

        foreach ($category_map as $slug => $category_name) {
            echo '<div class="menu-category ' . $slug . '" ' . ($slug != "khai-vi" ? 'style="display: none;"' : '') . '>';
            echo '<h2>' . $category_name . '</h2>';

            // Tránh lỗi SQL Injection
            $safe_category_name = $conn->real_escape_string($category_name);

            // Truy vấn ID danh mục từ bảng categories
            $sql_category = "SELECT id FROM categories WHERE name = '$safe_category_name'";
            $result_category = $conn->query($sql_category);

            if ($result_category && $result_category->num_rows > 0) {
                $category_data = $result_category->fetch_assoc();
                $category_id = $category_data['id'];

                // Truy vấn món ăn theo category_id
                $sql = "SELECT * FROM menu_items WHERE category_id = '$category_id'";
                $result = $conn->query($sql);

                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo '<a href="thongtinmon.php?id=' . $row["id"] . '" class="menu-item">';
                        echo '<img src="img/' . $row["image"] . '" alt="' . $row["name"] . '">';
                        echo '<div class="menu-text">';
                        echo '<h3>' . $row["name"] . '</h3>';
                        echo '<p>' . $row["description"] . '</p>';
                        echo '<span>' . number_format($row["price"]) . ' VND</span>';
                        echo '</div></a>';
                    }
                } else {
                    echo "<p>Không có món nào trong danh mục này.</p>";
                }
            } else {
                echo "<p>Lỗi: Không tìm thấy danh mục '$category_name'.</p>";
            }

            echo '</div>';
        }

        // Đóng kết nối CSDL
        $conn->close();
        ?>
    </div>
</div>

<footer>
    <div class="container">
        <p>&copy; 2025 Hào Khánh Cường - Lee Jong Suk.</p>
    </div>
</footer>

<script>
    $(document).ready(function(){
        $(".menu-categories li").click(function(){
            $(".menu-categories li").removeClass("active");
            $(this).addClass("active");

            var category = $(this).data("category");
            $(".menu-category").hide();
            $("." + category).show();
        });
    });
</script>

</body>
</html>
