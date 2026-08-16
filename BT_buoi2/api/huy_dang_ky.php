<?php
// api/huy_dang_ky.php
header('Content-Type: application/json; charset=utf-8');
require_once '../config/db.php';

$input = json_decode(file_get_contents('php://input'), true);
$ma_sv   = trim($input['msv'] ?? '');
$ma_lhp  = trim($input['ma_hp'] ?? ''); // Map ma_hp ở JS sang ma_lhp ở CSDL

if (!$ma_sv || !$ma_lhp) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Thiếu thông tin để hủy đăng ký học phần.'], JSON_UNESCAPED_UNICODE);
    exit;
}

$db = getDB();

try {
    $db->beginTransaction();

    // Thực hiện xoá bản ghi đăng ký khỏi cơ sở dữ liệu
    $stmt = $db->prepare("
        DELETE FROM dang_ky_hoc_phan 
        WHERE ma_sv = :ma_sv AND ma_lhp = :ma_lhp
    ");
    $stmt->execute([':ma_sv' => $ma_sv, ':ma_lhp' => $ma_lhp]);

    $db->commit();
    echo json_encode(['success' => true, 'message' => 'Hủy học phần thành công.'], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    echo json_encode(['success' => false, 'message' => 'Lỗi hệ thống: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
