<?php
require_once 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $login_id = $_POST['login_id'];

    if (empty($login_id)) {
        header("Location: ../index.php?error=empty");
        exit;
    }

    // ใช้ Prepared Statements เพื่อป้องกัน SQL Injection
    $stmt = $conn->prepare("SELECT id, registration_status FROM graduates WHERE graduate_id = ? OR student_id = ? OR id_card = ?");
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("sss", $login_id, $login_id, $login_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        // พบข้อมูล
        $row = $result->fetch_assoc();
        
        // เก็บ ID ของบัณฑิต (Primary Key) ลงใน Session
        // นี่คือวิธีที่ไม่ต้องแสดง ID ใน URL
        $_SESSION['graduate_user_id'] = $row['id'];
        
        // ส่งต่อไปยัง dashboard
        header("Location: ../dashboard.php");
        exit;
    } else {
        // ไม่พบข้อมูล
        header("Location: ../index.php?error=notfound");
        exit;
    }

    $stmt->close();
    $conn->close();

} else {
    // ถ้าไม่ได้ POST มา ให้กลับไปหน้าแรก
    header("Location: ../index.php");
    exit;
}
?>
