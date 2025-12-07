<?php
// students.php
require_once __DIR__ . '/../../config/database.php';

$pdo = Database::getInstance()->getConnection();

// ถ้ามีการ submit ฟอร์มแจ้งซ่อมใหม่
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $requester     = trim($_POST['requester'] ?? '');
    $device_type   = $_POST['device_type'] ?? 'other';
    $problem_detail = trim($_POST['problem_detail'] ?? '');

    if ($requester !== '' && $problem_detail !== '') {
        // กันค่า device_type ผิด
        $allowed = ['computer', 'projector', 'printer', 'other'];
        if (!in_array($device_type, $allowed, true)) {
            $device_type = 'other';
        }

        $sql = "INSERT INTO repair_requests (requester, device_type, problem_detail, status)
                VALUES (:requester, :device_type, :problem_detail, 'pending')";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':requester'      => $requester,
            ':device_type'    => $device_type,
            ':problem_detail' => $problem_detail,
        ]);

        // redirect เพื่อกันปัญหา F5 แล้ว insert ซ้ำ
        header('Location: students.php?name=' . urlencode($requester) . '&success=1');
        exit;
    } else {
        header('Location: students.php?error=missing_fields');
        exit;
    }
}

// ชื่อที่ใช้ filter รายการของผู้แจ้ง
$requesterName = trim($_GET['name'] ?? '');

// ดึงรายการแจ้งซ่อมของคนนี้
$requests = [];
if ($requesterName !== '') {
    $sql = "SELECT * FROM repair_requests
            WHERE requester = :requester
            ORDER BY created_at DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':requester' => $requesterName]);
    $requests = $stmt->fetchAll();
}
?>
<!doctype html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <title>แจ้งซ่อมอุปกรณ์ - ผู้แจ้ง</title>
    <meta name="viewport" content="width=device-width, initial-scale=1"> <!-- รองรับมือถือ -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../../public/assets/mac.css" rel="stylesheet">
</head>
<body class="mac">
<?php include __DIR__ . '/../layouts/navbar.php'; ?>
<div class="container py-4">

    <div class="mac-hero mb-4">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <p class="text-uppercase small fw-bold text-secondary mb-1">Student View</p>
                <h1 class="mac-title h2 mb-1">ระบบแจ้งซ่อมอุปกรณ์ (ผู้แจ้ง)</h1>
                <p class="mac-subtitle mb-0">กรอกข้อมูล แจ้งซ่อม และติดตามสถานะด้วยหน้าตาใสแบบ macOS</p>
            </div>
            <a href="../../index.php" class="btn mac-btn-secondary">กลับหน้าแรก</a>
        </div>
    </div>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success mac-shell border-0">บันทึกการแจ้งซ่อมเรียบร้อยแล้ว</div>
    <?php elseif (isset($_GET['error'])): ?>
        <div class="alert alert-danger mac-shell border-0">กรุณากรอกข้อมูลให้ครบถ้วน</div>
    <?php endif; ?>

    <!-- ฟอร์มระบุชื่อเพื่อดูประวัติของตน -->
    <form class="row g-2 mb-4 mac-shell p-3" method="get">
        <div class="col-12 col-md-auto">
            <input type="text" name="name" class="form-control mac-form-control"
                   placeholder="กรอกชื่อเพื่อดูประวัติของคุณ"
                   value="<?= htmlspecialchars($requesterName) ?>">
        </div>
        <div class="col-12 col-md-auto">
            <button class="btn mac-btn-primary w-100">ดูรายการ</button>
        </div>
    </form>

    <!-- ฟอร์มแจ้งซ่อมใหม่ -->
    <div class="card mac-card mb-4">
        <div class="card-header">แจ้งซ่อมใหม่</div>
        <div class="card-body">
            <form method="post">
                <div class="mb-3">
                    <label class="form-label">ชื่อผู้แจ้ง</label>
                    <input type="text" name="requester" class="form-control mac-form-control" required
                           value="<?= htmlspecialchars($requesterName) ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label">ประเภทอุปกรณ์</label>
                    <select name="device_type" class="form-select mac-form-select" required>
                        <option value="computer">คอมพิวเตอร์</option>
                        <option value="projector">โปรเจคเตอร์</option>
                        <option value="printer">เครื่องพิมพ์</option>
                        <option value="other">อื่น ๆ</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">รายละเอียดปัญหา</label>
                    <textarea name="problem_detail" class="form-control mac-textarea" rows="3" required></textarea>
                </div>

                <button type="submit" class="btn mac-btn-primary">ส่งแจ้งซ่อม</button>
            </form>
        </div>
    </div>

    <!-- ตารางแสดงรายการแจ้งซ่อมของผู้แจ้ง -->
    <h2 class="h5 mb-3">รายการแจ้งซ่อมของคุณ</h2>
    <?php if ($requesterName === ''): ?>
        <p class="text-muted mac-shell p-3">กรุณากรอกชื่อด้านบนเพื่อดูรายการของคุณ</p>
    <?php elseif (empty($requests)): ?>
        <p class="text-muted mac-shell p-3">ยังไม่มีรายการแจ้งซ่อมในชื่อ <?= htmlspecialchars($requesterName) ?></p>
    <?php else: ?>
        <div class="table-responsive mac-shell p-3">
            <table class="table table-striped align-middle mac-table mb-0">
                <thead>
                <tr>
                    <th>#</th>
                    <th>วันที่แจ้ง</th>
                    <th>ประเภทอุปกรณ์</th>
                    <th>รายละเอียด</th>
                    <th>สถานะ</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($requests as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['id']) ?></td>
                        <td><?= htmlspecialchars($row['created_at']) ?></td>
                        <td><?= htmlspecialchars($row['device_type']) ?></td>
                        <td style="max-width: 250px;">
                            <?= nl2br(htmlspecialchars($row['problem_detail'])) ?>
                        </td>
                        <td>
                            <?php
                            // แปลง status เป็นภาษาไทยเล็กน้อย
                            $status = $row['status'];
                            $label = match ($status) {
                                'pending'   => 'รอซ่อม',
                                'repairing' => 'กำลังซ่อม',
                                'done'      => 'เสร็จสิ้น',
                                default     => $status,
                            };
                            $badgeClass = $status === 'pending' ? 'pending' : ($status === 'repairing' ? 'repairing' : 'done');
                            ?>
                            <span class="mac-badge <?= htmlspecialchars($badgeClass) ?>">
                                <?= htmlspecialchars($label) ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

</div>
<?php include __DIR__ . '/../layouts/footer.php'; ?>
</body>
</html>
