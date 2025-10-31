<?php
require_once 'config.php';

// ตรวจสอบว่า login หรือยัง
if (!isset($_SESSION['graduate_user_id'])) {
    header("Location: ../index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['graduate_user_id'];
    $status = $_POST['status']; // ควรเป็น 1 หรือ 2

    // ตรวจสอบค่า status
    if ($status == 1 || $status == 2) {
        
        // อัปเดตฐานข้อมูล
        $stmt = $conn->prepare("UPDATE graduates SET registration_status = ?, registration_date = NOW() WHERE id = ?");
        if ($stmt === false) {
            die("Prepare failed: " . $conn->error);
        }
        
        $stmt->bind_param("ii", $status, $user_id);
        $stmt->execute();
        $stmt->close();
        $conn->close();

        // ตรวจสอบสถานะเพื่อ redirect
        if ($status == 1) {
            // ไปหน้าบัตรลงทะเบียน
            header("Location: ../card.php");
            exit;
        } else {
            // (status == 2) ไปหน้าขอบคุณ
            header("Location: ../thankyou.php");
            exit;
        }

    } else {
        // ถ้าค่า status ไม่ถูกต้อง
        header("Location: ../dashboard.php?error=invalid_status");
        exit;
    }
} else {
    // ถ้าไม่ได้ POST มา
    header("Location: ../dashboard.php");
    exit;
}
?>
