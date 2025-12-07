<?php
// technician.php
require_once __DIR__ . '/../../config/database.php';

$pdo = Database::getInstance()->getConnection();

// ถ้าช่างอัปเดตสถานะ
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id     = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $status = $_POST['status'] ?? '';

    $allowedStatuses = ['pending', 'repairing', 'done'];

    if ($id > 0 && in_array($status, $allowedStatuses, true)) {
        $sql = "UPDATE repair_requests
                SET status = :status
                WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':status' => $status,
            ':id'     => $id,
        ]);

        header('Location: technician.php?success=1');
        exit;
    } else {
        header('Location: technician.php?error=1');
        exit;
    }
}

// ดึงรายการทั้งหมด
$sql = "SELECT * FROM repair_requests
        ORDER BY created_at DESC";
$requests = $pdo->query($sql)->fetchAll();
?>
<!doctype html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <title>ระบบแจ้งซ่อมอุปกรณ์ - ช่าง</title>
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
                <p class="text-uppercase small fw-bold text-secondary mb-1">Technician View</p>
                <h1 class="mac-title h2 mb-1">ระบบแจ้งซ่อมอุปกรณ์ (ช่าง)</h1>
                <p class="mac-subtitle mb-0">ดูคิวงานทั้งหมด ปรับสถานะได้รวดเร็วบนหน้าจอใสสไตล์ macOS</p>
            </div>
            <a href="../../public/index.php" class="btn mac-btn-secondary">กลับหน้าแรก</a>
        </div>
    </div>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success mac-shell border-0">อัปเดตสถานะเรียบร้อยแล้ว</div>
    <?php elseif (isset($_GET['error'])): ?>
        <div class="alert alert-danger mac-shell border-0">ไม่สามารถอัปเดตสถานะได้</div>
    <?php endif; ?>

    <div class="table-responsive mac-shell p-3">
        <table class="table table-hover align-middle mac-table mb-0">
            <thead>
            <tr>
                <th>#</th>
                <th>วันที่แจ้ง</th>
                <th>ผู้แจ้ง</th>
                <th>ประเภทอุปกรณ์</th>
                <th>รายละเอียด</th>
                <th>สถานะปัจจุบัน</th>
                <th>อัปเดตสถานะ</th>
            </tr>
            </thead>
            <tbody>
            <?php if (empty($requests)): ?>
                <tr>
                    <td colspan="7" class="text-center text-muted">ยังไม่มีรายการแจ้งซ่อม</td>
                </tr>
            <?php else: ?>
                <?php foreach ($requests as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['id']) ?></td>
                        <td><?= htmlspecialchars($row['created_at']) ?></td>
                        <td><?= htmlspecialchars($row['requester']) ?></td>
                        <td><?= htmlspecialchars($row['device_type']) ?></td>
                        <td style="max-width: 250px;">
                            <?= nl2br(htmlspecialchars($row['problem_detail'])) ?>
                        </td>
                        <td>
                            <?php
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
                        <td>
                            <form method="post" class="d-flex gap-2 flex-wrap">
                                <input type="hidden" name="id" value="<?= htmlspecialchars($row['id']) ?>">
                                <select name="status" class="form-select form-select-sm mac-form-select">
                                    <option value="pending"   <?= $row['status'] === 'pending'   ? 'selected' : '' ?>>รอซ่อม</option>
                                    <option value="repairing" <?= $row['status'] === 'repairing' ? 'selected' : '' ?>>กำลังซ่อม</option>
                                    <option value="done"      <?= $row['status'] === 'done'      ? 'selected' : '' ?>>เสร็จสิ้น</option>
                                </select>
                                <button type="submit" class="btn btn-sm mac-btn-primary">บันทึก</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>
<?php include __DIR__ . '/../layouts/footer.php'; ?>
</body>
</html>
