<?php
// RepairRequestController.php

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/RepairRequest.php';
require_once __DIR__ . '/RepairRequestRepository.php';

class RepairRequestController
{
    private RepairRequestRepository $repo;

    public function __construct()
    {
        $pdo = Database::getInstance()->getConnection();
        $this->repo = new RepairRequestRepository($pdo);
    }

    /**
     * ผู้แจ้ง: สร้างแจ้งซ่อมใหม่จากฟอร์ม POST
     */
    public function storeFromPost(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        $requesterName  = trim($_POST['requester_name'] ?? '');
        $deviceType     = $_POST['device_type'] ?? RepairRequest::DEVICE_OTHER;
        $problemDetail  = trim($_POST['problem_detail'] ?? '');

        if ($requesterName === '' || $problemDetail === '') {
            // ง่าย ๆ: เก็บ error ไว้ใน session หรือ query string ก็ได้
            header('Location: student.php?error=missing_fields');
            exit;
        }

        // กันค่า device_type พิมพ์มั่ว
        $allowedDeviceTypes = [
            RepairRequest::DEVICE_COMPUTER,
            RepairRequest::DEVICE_PROJECTOR,
            RepairRequest::DEVICE_PRINTER,
            RepairRequest::DEVICE_OTHER,
        ];

        if (!in_array($deviceType, $allowedDeviceTypes, true)) {
            $deviceType = RepairRequest::DEVICE_OTHER;
        }

        $req = new RepairRequest(
            id: null,
            requester_name: $requesterName,
            device_type: $deviceType,
            problem_detail: $problemDetail,
            status: RepairRequest::STATUS_PENDING
        );

        $this->repo->create($req);

        // เสร็จแล้ว redirect กลับไปหน้าผู้แจ้ง พร้อมชื่อผู้แจ้งเพื่อ filter รายการ
        header('Location: student.php?name=' . urlencode($requesterName) . '&success=1');
        exit;
    }

    /**
     * ผู้แจ้ง: ดึงรายการแจ้งซ่อมของตัวเอง
     */
    public function listForRequester(string $requesterName): array
    {
        if ($requesterName === '') {
            return [];
        }

        return $this->repo->findByRequesterName($requesterName);
    }

    /**
     * ช่าง: ดึงรายการแจ้งซ่อมทั้งหมด
     */
    public function listAll(): array
    {
        return $this->repo->findAll();
    }

    /**
     * ช่าง: เปลี่ยนสถานะงานซ่อมจาก POST
     */
    public function updateStatusFromPost(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        $id     = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        $status = $_POST['status'] ?? '';

        $allowedStatuses = [
            RepairRequest::STATUS_PENDING,
            RepairRequest::STATUS_REPAIRING,
            RepairRequest::STATUS_DONE,
        ];

        if ($id <= 0 || !in_array($status, $allowedStatuses, true)) {
            header('Location: technician.php?error=invalid_data');
            exit;
        }

        $this->repo->updateStatus($id, $status);

        header('Location: technician.php?success=1');
        exit;
    }
}
