<?php
// api/dang_ky.php
header('Content-Type: application/json; charset=utf-8');
require_once '../config/db.php';

$input = json_decode(file_get_contents('php://input'), true);
$ma_sv   = trim($input['msv'] ?? '');
$ma_lhp  = trim($input['ma_hp'] ?? ''); // Đổi từ ma_hp thành ma_lhp trong backend

if (!$ma_sv || !$ma_lhp) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Thiếu thông tin sinh viên hoặc mã lớp học phần.'], JSON_UNESCAPED_UNICODE);
    exit;
}

$db = getDB();

try {
    $db->beginTransaction();

    // 1. Kiểm tra lớp học phần có tồn tại không và lấy sĩ số tối đa
    $stmt_lhp = $db->prepare("SELECT ma_mon, si_so_toi_da, trang_thai FROM lop_hoc_phan WHERE ma_lhp = :ma_lhp");
    $stmt_lhp->execute([':ma_lhp' => $ma_lhp]);
    $lhp = $stmt_lhp->fetch();

    if (!$lhp) {
        $db->rollBack();
        echo json_encode(['success' => false, 'message' => 'Lớp học phần không tồn tại.'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    if ($lhp['trang_thai'] !== 'DANG_MO' && $lhp['trang_thai'] !== 'CHUA_MO') {
        $db->rollBack();
        echo json_encode(['success' => false, 'message' => 'Lớp học phần này không mở đăng ký.'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // 2. Tính sĩ số hiện tại của lớp học phần
    $stmt_siso = $db->prepare("SELECT COUNT(*) AS si_so_hien FROM dang_ky_hoc_phan WHERE ma_lhp = :ma_lhp AND trang_thai = 'DA_DANG_KY'");
    $stmt_siso->execute([':ma_lhp' => $ma_lhp]);
    $siso = $stmt_siso->fetch();

    if ($siso['si_so_hien'] >= $lhp['si_so_toi_da']) {
        $db->rollBack();
        echo json_encode(['success' => false, 'message' => 'Lớp học phần đã đầy sĩ số.'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // 3. Kiểm tra xem sinh viên đã đăng ký chính lớp này chưa (tránh trùng lặp)
    $stmt_check_registered = $db->prepare("
        SELECT id FROM dang_ky_hoc_phan 
        WHERE ma_sv = :ma_sv AND ma_lhp = :ma_lhp AND trang_thai = 'DA_DANG_KY'
    ");
    $stmt_check_registered->execute([':ma_sv' => $ma_sv, ':ma_lhp' => $ma_lhp]);
    if ($stmt_check_registered->fetch()) {
        $db->rollBack();
        echo json_encode(['success' => false, 'message' => 'Bạn đã đăng ký lớp học phần này rồi.'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // 4. Kiểm tra xem sinh viên đã đăng ký môn học này ở lớp học phần khác chưa (tránh học trùng môn trong một kỳ)
    $stmt_check_subject = $db->prepare("
        SELECT dk.id 
        FROM dang_ky_hoc_phan dk
        JOIN lop_hoc_phan lp ON dk.ma_lhp = lp.ma_lhp
        WHERE dk.ma_sv = :ma_sv 
          AND dk.trang_thai = 'DA_DANG_KY'
          AND lp.ma_mon = :ma_mon
    ");
    $stmt_check_subject->execute([':ma_sv' => $ma_sv, ':ma_mon' => $lhp['ma_mon']]);
    if ($stmt_check_subject->fetch()) {
        $db->rollBack();
        echo json_encode(['success' => false, 'message' => 'Bạn đã đăng ký một lớp khác của môn học này trong học kỳ này rồi.'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // 5. Thực hiện lưu thông tin đăng ký mới vào bảng dang_ky_hoc_phan
    // Do UUID của id có default là uuid_generate_v4() nên ta không cần truyền id vào
    $stmt_insert = $db->prepare("
        INSERT INTO dang_ky_hoc_phan (ma_sv, ma_lhp, trang_thai) 
        VALUES (:ma_sv, :ma_lhp, 'DA_DANG_KY')
    ");
    $stmt_insert->execute([':ma_sv' => $ma_sv, ':ma_lhp' => $ma_lhp]);

    $db->commit();
    echo json_encode(['success' => true, 'message' => 'Đăng ký học phần thành công!'], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    echo json_encode(['success' => false, 'message' => 'Lỗi hệ thống: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
