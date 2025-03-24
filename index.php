<?php
    session_start();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Le Monde Steak - Bít Tết Kiểu Pháp</title>
    
    <!-- Thêm link đến CSS -->
    <link rel="stylesheet" href="index.css">

    <!-- Slick Carousel -->
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
                <li><a href="index.php" class="active">Trang chủ</a></li>
                <li><a href="#">Thông tin</a></li>
                <li><a href="thucdon.php">Thực đơn</a></li>
                <li><a href="#">Khuyến mãi</a></li>
                <li><a href="giohang.php">Giỏ hàng</a></li>

                <?php if (isset($_SESSION['user']) && is_array($_SESSION['user'])): ?>
                    <li><a href="trangcanhan.php">Xin chào, <?php echo htmlspecialchars($_SESSION['user']['fullName']); ?></a></li>
                    <li><a href="dangxuat.php">Đăng xuất</a></li>
                <?php else: ?>
                    <li><a href="dangnhap.php">Đăng nhập</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</header>

<div class="slider-container">
    <ul class="feature-slides">
        <li><img src="img/bodatbiet.png" alt="Menu Steak & Pasta"></li>
        <li><img src="img/bokhoaitay.png" alt="Ưu đãi tháng 3"></li>
        <li><img src="img/bokobe300g.png" alt="Không gian sang trọng"></li>
        <li><img src="img/bokobe500g.png" alt="Đặc sản bò thượng hạng"></li>
        <li><img src="img/boxucxich.png" alt="Khuyến mãi lớn"></li>
    </ul>
</div>

<section class="about">
    <div class="container">
        <h2>Chào mừng đến LJS</h2>
        <div class="underline"></div>
        <?php if (isset($_SESSION['user']) && is_array($_SESSION['user'])): ?>
            <p>Chúc bạn có bữa ăn ngon miệng, <?php echo htmlspecialchars($_SESSION['user']['fullName']); ?>!</p>
        <?php else: ?>
            <p>Trải qua hành trình 6 năm phát triển, chúng tôi luôn không ngừng cải tiến để mang đến trải nghiệm ẩm thực hoàn hảo nhất. Vui lòng <a href="dangnhap.php">đăng nhập</a> để đặt bàn và nhận ưu đãi thành viên.</p>
        <?php endif; ?>
    </div>
</section>

<footer>
    <div class="container">
        <p>&copy; 2025 Hào Khánh Cường - Lee Jong Suk.</p>
    </div>
</footer>

<script>
    $(document).ready(function(){
        $('.feature-slides').slick({
            autoplay: true,
            autoplaySpeed: 3000,
            dots: true,
            arrows: false,
            infinite: true,
            speed: 500,
            slidesToShow: 1,
            slidesToScroll: 1,
            dotsClass: 'slick-dots custom-dots'
        });
    });
</script>

</body>
</html>
