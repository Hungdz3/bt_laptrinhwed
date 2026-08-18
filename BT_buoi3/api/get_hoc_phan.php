<?php
// api/get_hoc_phan.php
header('Content-Type: application/json; charset=utf-8');
require_once '../config/db.php';

$db = getDB();
$keyword = trim($_GET['q'] ?? '');
$ma_sv = trim($_GET['msv'] ?? '');

// 1. Tìm học kỳ đang mở đăng ký hoặc đang học
$stmt_hk = $db->query("SELECT id_hoc_ky FROM hoc_ky WHERE trang_thai IN ('DANG_HOC', 'CHUA_MO') ORDER BY ngay_bat_dau DESC LIMIT 1");
$hk = $stmt_hk->fetch();
$id_hoc_ky = $hk['id_hoc_ky'] ?? null;

if (!$id_hoc_ky) {
    echo json_encode([], JSON_UNESCAPED_UNICODE);
    exit;
}

// 2. Query lớp học phần và kết hợp lịch học, sĩ số, trạng thái đăng ký
$sql = "
    SELECT 
        lhp.ma_lhp,
        mh.ten_mon,
        mh.so_tin_chi,
        gv.ho_ten AS giang_vien,
        lhp.si_so_toi_da AS si_so_max,
        COALESCE(ss.si_so_hien, 0) AS si_so_hien,
        COALESCE(lh_group.lich_hoc, 'Chưa xếp lịch') AS lich_hoc,
        CASE WHEN dk.ma_lhp IS NOT NULL THEN true ELSE false END AS da_dang_ky
    FROM lop_hoc_phan lhp
    JOIN mon_hoc mh ON lhp.ma_mon = mh.ma_mon
    JOIN giao_vien gv ON lhp.ma_gv = gv.ma_gv
    -- Lấy số sinh viên đã đăng ký thực tế
    LEFT JOIN (
        SELECT ma_lhp, COUNT(*) AS si_so_hien 
        FROM dang_ky_hoc_phan 
        WHERE trang_thai = 'DA_DANG_KY' 
        GROUP BY ma_lhp
    ) ss ON lhp.ma_lhp = ss.ma_lhp
    -- Gộp lịch học theo từng lớp học phần
    LEFT JOIN (
        SELECT lh.ma_lhp, 
               STRING_AGG('T' || lh.thu || ' (Tiết ' || th.so_tiet || ')', ', ' ORDER BY lh.thu, th.so_tiet) AS lich_hoc
        FROM lich_hoc lh
        JOIN tiet_hoc th ON lh.id_tiet = th.id_tiet
        GROUP BY lh.ma_lhp
    ) lh_group ON lhp.ma_lhp = lh_group.ma_lhp
    -- Kiểm tra xem sinh viên hiện tại đã đăng ký chưa
    LEFT JOIN dang_ky_hoc_phan dk ON dk.ma_lhp = lhp.ma_lhp AND dk.ma_sv = :ma_sv AND dk.trang_thai = 'DA_DANG_KY'
    WHERE lhp.id_hoc_ky = :id_hoc_ky AND lhp.trang_thai IN ('CHUA_MO', 'DANG_MO')
";

$params = [
    ':ma_sv' => $ma_sv,
    ':id_hoc_ky' => $id_hoc_ky
];

if ($keyword !== '') {
    $sql .= " AND (mh.ten_mon ILIKE :kw OR lhp.ma_lhp ILIKE :kw)";
    $params[':kw'] = "%{$keyword}%";
}

$sql .= " ORDER BY lhp.ma_lhp ASC";

try {
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $data = $stmt->fetchAll();
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Lỗi truy vấn cơ sở dữ liệu: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
