<?php
// includes/header.php
// Nhận $sinh_vien từ trang gọi vào

$ten_sv  = $sinh_vien['ho_ten']  ?? 'Sinh viên';
$msv     = $sinh_vien['ma_sv']   ?? '';
$lop     = $sinh_vien['ten_lop'] ?? '';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Đăng Ký Học Phần — HNDA</title>
  <link rel="stylesheet" href="assets/css/style.css">
  <script src="assets/js/global.js"></script>
</head>
<body>

<!-- Header -->
<header class="site-header">
  <div class="header-left">
    <!-- Có thể đổi nguồn logo thành ảnh thật nếu có, tạm thời dùng CSS/Text đại diện -->
    <div class="logo-placeholder">🏫</div>
    <div>
      <strong>TRƯỜNG ĐẠI HỌC HNDA</strong>
      <span>Cổng thông tin sinh viên</span>
    </div>
  </div>
  <div class="header-right">
    <div class="user-text">
      <span class="user-name"><?= htmlspecialchars($ten_sv) ?></span>
      <small class="user-info">MSV: <?= htmlspecialchars($msv) ?> • Lớp: <?= htmlspecialchars($lop) ?></small>
    </div>
    <div class="avatar"><?= mb_strtoupper(mb_substr($ten_sv, 0, 1, 'UTF-8'), 'UTF-8') ?></div>
  </div>
</header>

<!-- Navbar -->
<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<nav class="navbar">
  <a href="trang-chu.php" class="<?= $current_page == 'trang-chu.php' ? 'active' : '' ?>">Trang Chủ</a>
  <a href="Notifications.php" class="<?= $current_page == 'Notifications.php' ? 'active' : '' ?>">Thông Báo</a>
  <a href="index.php" class="<?= $current_page == 'index.php' ? 'active' : '' ?>">Đăng Ký Học Phần</a>
  <a href="lich-hoc.php" class="<?= $current_page == 'lich-hoc.php' ? 'active' : '' ?>">Lịch Học</a>
  <a href="sinh-vien.php" class="<?= $current_page == 'sinh-vien.php' ? 'active' : '' ?>">Sinh Viên</a>
</nav>
