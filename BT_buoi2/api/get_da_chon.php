<?php
// api/get_da_chon.php
header('Content-Type: application/json; charset=utf-8');
require_once '../config/db.php';

$ma_sv = trim($_GET['msv'] ?? '');

if (!$ma_sv) {
    http_response_code(400);
    echo json_encode(['error' => 'Thiếu mã sinh viên.'], JSON_UNESCAPED_UNICODE);
    exit;
}

$db = getDB();

try {
    // Truy vấn danh sách lớp học phần sinh viên đã đăng ký thành công
    $stmt = $db->prepare("
        SELECT 
            dk.ma_lhp, 
            mh.ten_mon, 
            mh.so_tin_chi
        FROM dang_ky_hoc_phan dk
        JOIN lop_hoc_phan lhp ON dk.ma_lhp = lhp.ma_lhp
        JOIN mon_hoc mh ON lhp.ma_mon = mh.ma_mon
        WHERE dk.ma_sv = :ma_sv AND dk.trang_thai = 'DA_DANG_KY'
        ORDER BY dk.thoi_gian_dang_ky ASC
    ");
    $stmt->execute([':ma_sv' => $ma_sv]);
    $data = $stmt->fetchAll();

    echo json_encode($data, JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Lỗi cơ sở dữ liệu: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
