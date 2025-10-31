<?php
// ป้องกันการเข้าถึงไฟล์นี้โดยตรง
if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    die('Direct access not permitted.');
}

// เริ่มต้น session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// ตั้งค่าการเชื่อมต่อฐานข้อมูล
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root'); // <-- เปลี่ยนเป็น username ของคุณ
define('DB_PASSWORD', '');     // <-- เปลี่ยนเป็น password ของคุณ
define('DB_NAME', 'gradsystem'); // <-- เปลี่ยนเป็นชื่อฐานข้อมูลของคุณ

// เชื่อมต่อฐานข้อมูล
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("การเชื่อมต่อล้มเหลว: " . $conn->connect_error);
}

// ตั้งค่า character set เป็น utf8mb4
if (!$conn->set_charset("utf8mb4")) {
    printf("Error loading character set utf8mb4: %s\n", $conn->error);
    exit();
}

// ฟังก์ชันสำหรับแปลงประเภทบัณฑิต
function getGraduateType($type) {
    switch ($type) {
        case 1: return 'พระ';
        case 2: return 'คฤหัสถ์ชาย';
        case 3: return 'คฤหัสถ์หญิง';
        default: return 'ไม่ระบุ';
    }
}
?>
