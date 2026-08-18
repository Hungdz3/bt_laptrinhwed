<?php
// Notifications.php
session_start();
require_once 'config/db.php';
require_once 'Notifications_functions.php';

$ma_sv = $_SESSION['ma_sv'] ?? 'SV001';
$db = getDB();

$sinh_vien = null;
try {
    // Lấy thông tin sinh viên cho Header
    $stmt = $db->prepare("
        SELECT sv.ma_sv, sv.ho_ten, lsv.ten_lop, k.ten_khoa
        FROM sinh_vien sv
        JOIN lop_sinh_vien lsv ON sv.ma_lop_sv = lsv.ma_lop_sv
        JOIN nganh n ON lsv.ma_nganh = n.ma_nganh
        JOIN khoa k ON n.ma_khoa = k.ma_khoa
        WHERE sv.ma_sv = :ma_sv
    ");
    $stmt->execute([':ma_sv' => $ma_sv]);
    $sinh_vien = $stmt->fetch();
} catch (Exception $e) {
    // Silent catch
}

if (!$sinh_vien) {
    $sinh_vien = [
        'ma_sv' => $ma_sv,
        'ho_ten' => 'NGUYỄN VĂN A',
        'ten_lop' => 'CNTT D2024A',
        'ten_khoa' => 'Khoa Toán - CNTT'
    ];
}

// Khởi tạo danh sách thông báo mẫu trong Session nếu chưa có
if (!isset($_SESSION['notifications'])) {
    $_SESSION['notifications'] = [
        [
            "id" => 1,
            "title" => "Công khai luận án",
            "description" => "Công khai thông tin luận án Tiến sĩ của nghiên cứu sinh Trần Thị Thịnh trước khi bảo vệ luận án cấp Trường Đại học Thủ đô Hà Nội. Sinh viên và giảng viên quan tâm có thể đến tham dự tại phòng hội thảo trung tâm nhà A.",
            "image_url" => "https://hnmu.edu.vn/sites/default/files/2026-08/anh-cong-khai.png"
        ],
        [
            "id" => 2,
            "title" => "Thông điệp đầu năm học mới",
            "description" => "Thông điệp năm học 2026 - 2027 của Hiệu trưởng gửi tới toàn thể các cán bộ giảng viên, công nhân viên và các bạn sinh viên toàn trường nhân dịp khai giảng.",
            "image_url" => "https://hnmu.edu.vn/sites/default/files/2026-08/logo-hnmu-1.ai_.png"
        ],
        [
            "id" => 3,
            "title" => "Thông báo nộp học phí học kỳ mới",
            "description" => "Nhà trường thông báo thời gian nộp học phí cho học kỳ mới. Sinh viên cần hoàn thành đúng thời hạn trước ngày 30/09/2026 thông qua chuyển khoản định danh.",
            "image_url" => "https://hnmu.edu.vn/sites/default/files/2026-06/quyet-dinh_4.jpg"
        ]
    ];
}

$notifications = &$_SESSION['notifications'];

$title = "";
$description = "";
$image_url = "";

$error_title = "";
$error_description = "";
$error_image = "";

$success_msg = $_SESSION['success_notify'] ?? '';
unset($_SESSION['success_notify']);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = trim($_POST["title"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $image_url = trim($_POST["image_url"] ?? "");

    if ($title === "") {
        $error_title = "Vui lòng nhập tiêu đề.";
    }

    if ($description === "") {
        $error_description = "Vui lòng nhập nội dung.";
    }

    if ($image_url === "") {
        $error_image = "Vui lòng nhập URL hình ảnh.";
    } elseif (!filter_var($image_url, FILTER_VALIDATE_URL)) {
        $error_image = "Image URL không hợp lệ.";
    } elseif (!preg_match('/\.(jpg|jpeg|png|gif|webp|svg)(\?.*)?$/i', $image_url)) {
        $error_image = "URL phải là link hình ảnh (.jpg, .jpeg, .png, .gif, .webp hoặc .svg).";
    }

    if ($error_title === "" && $error_description === "" && $error_image === "") {
        $id = count($notifications) + 1;
        $notifications[] = [
            "id" => $id,
            "title" => $title,
            "description" => $description,
            "image_url" => $image_url
        ];

        $_SESSION['success_notify'] = "Thêm thông báo thành công!";
        header("Location: Notifications.php");
        exit;
    }
}

require_once 'includes/header.php';
?>

<section class="banner-title-row">
  <h2>TIN TỨC & THÔNG BÁO CHUNG</h2>
  <p class="subtitle-text">Xem các tin tức đào tạo từ nhà trường và bổ sung thông báo nội bộ</p>
</section>

<div class="notifications-layout">

  <!-- Cột trái: Danh sách thông báo dạng Card -->
  <div class="notifications-list-col">
    <h3>DANH SÁCH THÔNG BÁO</h3>
    
    <?php if (empty($notifications)): ?>
      <div class="no-data-card">Không có thông báo nào được tìm thấy.</div>
    <?php else: ?>
      <?php 
      // Hiển thị thông báo mới nhất lên đầu
      $display_list = array_reverse($notifications);
      foreach ($display_list as $n): 
        $status = getNotificationStatus($n['title'], $n['description']);
        $badge_class = ($status === "Thông báo chi tiết") ? "status-badge-detailed" : "status-badge-short";
      ?>
        <div class="notification-item-card">
          <div class="notification-img-wrapper">
            <img class="notification-img" src="<?= htmlspecialchars($n['image_url']) ?>" alt="<?= htmlspecialchars($n['title']) ?>">
          </div>
          <div class="notification-body">
            <div>
              <h4 class="notification-item-title"><?= htmlspecialchars($n['title']) ?></h4>
              <p class="notification-item-desc"><?= htmlspecialchars($n['description']) ?></p>
            </div>
            <div class="notification-meta-row">
              <span><strong>Mã số:</strong> #<?= $n['id'] ?></span>
              <span><strong>Độ dài:</strong> <span class="<?= $badge_class ?>"><?= htmlspecialchars($status) ?></span></span>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>

  <!-- Cột phải: Form thêm thông báo -->
  <aside class="notifications-form-col">
    <h3>THÊM THÔNG BÁO MỚI</h3>
    <form method="POST" action="Notifications.php" novalidate>
      
      <div class="form-group-notify">
        <label for="title">Tiêu đề thông báo</label>
        <input type="text" id="title" name="title" value="<?= htmlspecialchars($title) ?>" placeholder="Nhập tiêu đề ngắn gọn...">
        <?php if ($error_title !== ""): ?>
          <span class="error-msg-notify">⚠️ <?= htmlspecialchars($error_title) ?></span>
        <?php endif; ?>
      </div>

      <div class="form-group-notify">
        <label for="description">Nội dung chi tiết</label>
        <textarea id="description" name="description" rows="6" placeholder="Nhập nội dung đầy đủ của thông báo..."><?= htmlspecialchars($description) ?></textarea>
        <?php if ($error_description !== ""): ?>
          <span class="error-msg-notify">⚠️ <?= htmlspecialchars($error_description) ?></span>
        <?php endif; ?>
      </div>

      <div class="form-group-notify">
        <label for="image_url">Link ảnh minh họa (URL)</label>
        <input type="text" id="image_url" name="image_url" value="<?= htmlspecialchars($image_url) ?>" placeholder="https://example.com/image.png">
        <?php if ($error_image !== ""): ?>
          <span class="error-msg-notify">⚠️ <?= htmlspecialchars($error_image) ?></span>
        <?php endif; ?>
      </div>

      <button type="submit" class="btn-submit-notify">
        📢 Thêm thông báo
      </button>

    </form>
  </aside>

</div>

<?php if ($success_msg !== ""): ?>
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      showToast("<?= htmlspecialchars($success_msg) ?>", "success");
    });
  </script>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>
