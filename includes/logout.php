<?php
require_once 'config.php';

// ล้างค่า session ทั้งหมด
session_unset();

// ทำลาย session
session_destroy();

// กลับไปยังหน้า login พร้อมแจ้งว่า logout แล้ว
header("Location: ../index.php?logout=true");
exit;
?>
