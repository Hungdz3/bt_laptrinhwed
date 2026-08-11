<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giới Thiệu Bản Thân</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: #0f111a;
            color: #e0e0e0;
            line-height: 1.7;
            padding: 40px 20px;
        }
        .container {
            max-width: 720px;
            margin: 0 auto;
        }
        .header {
            text-align: center;
            padding-bottom: 32px;
            border-bottom: 1px solid #1e2235;
            margin-bottom: 36px;
        }
        .avatar {
            font-size: 4rem;
            margin-bottom: 12px;
        }
        .name {
            font-size: 1.8rem;
            font-weight: 700;
            color: #fff;
        }
        .subtitle {
            color: #7a8bbf;
            font-size: 0.95rem;
            margin-top: 4px;
        }
        .section {
            margin-bottom: 36px;
        }
        .section-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: #8a9bff;
            margin-bottom: 14px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
        }
        .info-table td {
            padding: 10px 12px;
            border-bottom: 1px solid #1e2235;
            font-size: 0.92rem;
        }
        .info-table td:first-child {
            color: #7a8bbf;
            width: 140px;
            font-weight: 600;
        }
        .info-table a {
            color: #8a9bff;
            text-decoration: none;
        }
        .info-table a:hover {
            text-decoration: underline;
        }
        .project {
            background: #161929;
            border: 1px solid #1e2235;
            border-radius: 10px;
            padding: 18px 20px;
            margin-bottom: 14px;
        }
        .project-name {
            font-weight: 700;
            color: #fff;
            font-size: 1rem;
            margin-bottom: 4px;
        }
        .project-name a {
            color: #8a9bff;
            text-decoration: none;
        }
        .project-name a:hover {
            text-decoration: underline;
        }
        .project-desc {
            color: #7a8bbf;
            font-size: 0.88rem;
        }
        .project-tech {
            margin-top: 8px;
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }
        .tech-tag {
            background: #1e2235;
            color: #8a9bff;
            font-size: 0.75rem;
            padding: 3px 10px;
            border-radius: 20px;
        }
        .bio {
            color: #b0b8d0;
            font-size: 0.93rem;
        }
        .footer {
            text-align: center;
            color: #3e4460;
            font-size: 0.8rem;
            margin-top: 48px;
            padding-top: 24px;
            border-top: 1px solid #1e2235;
        }
    </style>
</head>
<body>
<?php
$name       = 'Nguyễn Duy Hùng';
$class      = 'CNTT D2024B';
$school     = 'Đại học thủ đô Hà Nội';
$major      = 'Công nghệ Thông tin';
$bio        = 'Sở thích : Yêu thể thao và động vật';
$auth       = 'CTP1';

$contacts = [
    'Email'     => ['value' => 'nguyenduyhung1542k6@gmail.com',         'link' => 'mailto:nguyenduyhung1542k6@gmail.com'],
    'SĐT'      => ['value' => '077892006',                 'link' => 'tel:0877892006'],
    'Facebook'  => ['value' => 'https://www.facebook.com/hung.nguyenduy.90813236',            'link' => 'https://www.facebook.com/hung.nguyenduy.90813236'],
    'GitHub'    => ['value' => 'https://github.com/Hungdz3',        'link' => 'https://github.com/Hungdz3'],
];
$projects = [
    [
        'name' => 'Chat bot cho giáo viên tiểu học',
        'desc' => 'Hệ thống hỗ trợ giúp đỡ các giáo viên tiểu học.',
        'link' => 'https://github.com/Hungdz3/EduAi',
        'tech' => ['node.js', 'supabase', 'Bootstrap'],
    ],

    [
        'name' => 'Hệ Thống quản lý khóa học và đăng ký học phần',
        'desc' => 'Hệ thống hỗ trợ giúp đỡ học sinh và giáo viên trong việc ra soát đăng kí học phần.',
        'link' => 'https://github.com/Hungdz3/BTL-L-p_tr-nh_wed',
        'tech' => ['php', 'MySQL', 'Bootstrap'],
    ],
];
$year = date('Y');
?>
<div class="container">
    <div class="section">
        <h2 class="section-title">Giới thiệu</h2>
        <p class="bio"><?= htmlspecialchars($bio) ?></p>
        <table class="info-table" style="margin-top: 16px;">
            <tr><td>Họ tên</td><td><?= htmlspecialchars($name) ?></td></tr>
            <tr><td>Lớp</td><td><?= htmlspecialchars($class) ?></td></tr>
            <tr><td>Trường</td><td><?= htmlspecialchars($school) ?></td></tr>
            <tr><td>Chuyên ngành</td><td><?= htmlspecialchars($major) ?></td></tr>
        </table>
    </div>
    <div class="section">
        <h2 class="section-title">Liên hệ</h2>
        <table class="info-table">
            <?php foreach ($contacts as $label => $c): ?>
            <tr>
                <td><?= htmlspecialchars($label) ?></td>
                <td>
                    <?php if ($c['link']): ?>
                        <a href="<?= htmlspecialchars($c['link']) ?>" target="_blank"><?= htmlspecialchars($c['value']) ?></a>
                    <?php else: ?>
                        <?= htmlspecialchars($c['value']) ?>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
    <div class="section">
        <h2 class="section-title">Dự án đã làm</h2>
        <?php foreach ($projects as $p): ?>
        <div class="project">
            <div class="project-name">
                <?php if ($p['link']): ?>
                    <a href="<?= htmlspecialchars($p['link']) ?>" target="_blank"><?= htmlspecialchars($p['name']) ?> ↗</a>
                <?php else: ?>
                    <?= htmlspecialchars($p['name']) ?>
                <?php endif; ?>
            </div>
            <div class="project-desc"><?= htmlspecialchars($p['desc']) ?></div>
            <?php if (!empty($p['tech'])): ?>
            <div class="project-tech">
                <?php foreach ($p['tech'] as $t): ?>
                <span class="tech-tag"><?= htmlspecialchars($t) ?></span>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>
    <div class="footer">
        © <?= $year ?> <?= htmlspecialchars($auth) ?>
    </div>
</div>
</body>
</html>