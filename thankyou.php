<?php
// อัปเดต Path: เรียกใช้ config จาก includes/
require_once 'includes/config.php';

// ตรวจสอบว่า login หรือยัง
if (!isset($_SESSION['graduate_user_id'])) {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ขอบคุณที่ลงทะเบียน</title>
    <!-- อัปเดต Path: เรียกใช้ CSS จาก assets/css/ -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <h2>ขอบคุณที่ลงทะเบียน</h2>
        <div class="alert alert-success">
            <strong>การลงทะเบียนของคุณเสร็จสมบูรณ์แล้ว</strong>
            <p>เราได้บันทึกสถานะ "ไม่เข้าร่วมรับปริญญา" ของคุณไว้ในระบบแล้ว</p>
        </div>
        <a href="dashboard.php" class="btn btn-secondary">กลับหน้าหลัก</a>
        <!-- อัปเดต Path: เรียกใช้ logout.php จาก includes/ -->
        <a href="includes/logout.php" class="btn btn-logout">ออกจากระบบ</a>
    </div>
</body>
</html>

