<?php
// index.php
?>
<!doctype html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <title>ระบบแจ้งซ่อมอุปกรณ์ห้องปฏิบัติการ</title>
    <meta name="viewport" content="width=device-width, initial-scale=1"> <!-- รองรับมือถือ -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="./public/assets/mac.css" rel="stylesheet">
</head>
<body class="mac">
<div class="container py-5">
    <div class="mac-hero mb-5">
        <div class="row align-items-center">
            <div class="col-12 col-lg-7">
                <p class="text-uppercase small fw-bold text-secondary mb-1">LabFix</p>
                <h1 class="mac-title display-5">ระบบแจ้งซ่อมอุปกรณ์ห้องปฏิบัติการ</h1>
                <p class="mac-subtitle fs-5 mb-4">จัดการงานซ่อมด้วยหน้าตาใหม่สไตล์ macOS ใส ลื่นตา พร้อมใช้งานทั้งผู้แจ้งและช่าง</p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="./views/student/students.php" class="btn mac-btn-primary px-4">เข้าสู่ระบบผู้แจ้ง</a>
                    <a href="./views/technician/technician.php" class="btn mac-btn-secondary px-4">เข้าสู่ระบบช่าง</a>
                </div>
            </div>
            <div class="col-12 col-lg-5 mt-4 mt-lg-0">
                <div class="mac-card p-4 h-100">
                    <p class="fw-bold mb-2">ภาพรวมการใช้งาน</p>
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div>
                            <div class="mac-subtitle small mb-1">หน้าหลัก</div>
                            <div class="fw-bold">เข้าสู่ระบบได้ทันที</div>
                        </div>
                        <span class="mac-badge done">พร้อมใช้งาน</span>
                    </div>
                    <hr>
                    <p class="mac-subtitle small mb-2">จุดเด่น</p>
                    <ul class="mb-0 text-secondary">
                        <li>ส่งเรื่องแจ้งซ่อม</li>
                        <li>ปรับเปลี่ยนสถานะ</li>
                        <li>เลย์เอาท์สบายตาใช้ง่าย</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-12 col-md-6">
            <div class="card mac-card h-100">
                <div class="card-body d-flex flex-column">
                    <h2 class="h4 mb-2">มุมมองผู้แจ้ง (นักศึกษา)</h2>
                    <p class="mac-subtitle mb-3">ส่งคำขอซ่อมใหม่ ตรวจสอบสถานะงานของตนได้ทันที</p>
                    <ul class="text-secondary mb-4">
                        <li>แจ้งซ่อมอุปกรณ์ใหม่</li>
                        <li>ดูรายการแจ้งซ่อมของตนเอง</li>
                    </ul>
                    <div class="mt-auto">
                        <a href="./views/student/students.php" class="btn mac-btn-primary w-100">เข้าสู่ระบบผู้แจ้ง</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6">
            <div class="card mac-card h-100">
                <div class="card-body d-flex flex-column">
                    <h2 class="h4 mb-2">มุมมองช่าง</h2>
                    <p class="mac-subtitle mb-3">จัดการคิวงาน ติดตามสถานะแบบรวดเร็ว</p>
                    <ul class="text-secondary mb-4">
                        <li>ดูรายการแจ้งซ่อมทั้งหมด</li>
                        <li>อัปเดตสถานะ รอซ่อม / กำลังซ่อม / เสร็จสิ้น</li>
                    </ul>
                    <div class="mt-auto">
                        <a href="./views/technician/technician.php" class="btn mac-btn-secondary w-100">เข้าสู่ระบบช่าง</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
