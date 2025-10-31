<?php
// อัปเดต Path: เรียกใช้ config จาก includes/
require_once 'includes/config.php';

// ถ้า login แล้ว ให้ไปหน้า dashboard เลย
if (isset($_SESSION['graduate_user_id'])) {
    header("Location: dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ระบบลงทะเบียนบัณฑิต - เข้าสู่ระบบ</title>
    <!-- อัปเดต Path: เรียกใช้ CSS จาก assets/css/ -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <div class="container">
        <div class="logo-container">
            <img src="assets/img/LOGO.png" class="responsive" alt="" width="400" height="auto">
            <h3>ระบบยืนยันการเข้ารับประทานปริญญาบัตร
                ประจำปี 2568</h3>
                <label class="description">บัณฑิตทุกท่านกรุณาลงทะเบียนเพื่อยืนยันการเข้าร่วมหรือไม่เข้าร่วมพิธี</label>
                <label class="description">กำหนดการพิธีประทานปริญญาบัตร</label>
                <label class="description">ซ้อมใหญ่ : วันที่ 13 ธันวาคม พ.ศ. 2568</label>
                <label class="description">พิธีประทานปริญญาบัตร : วันที่ 14 ธันวาคม พ.ศ. 2568</label>
                <label class="description">ณ มหาวิทยาลัยมหามกุฏราชวิทยาลัย จ.นครปฐม</label>
        </div>
<hr>
        <?php
        if (isset($_GET['error'])) {
            echo '<div class="alert alert-error">ไม่พบข้อมูลบัณฑิต หรือข้อมูลไม่ถูกต้อง</div>';
        }
        // if (isset($_GET['logout'])) {
        //     echo '<div class="alert alert-success">ออกจากระบบเรียบร้อยแล้ว</div>';
        // }
        ?>

        <!-- อัปเดต Path: ส่ง action ไปที่ includes/auth.php -->
        <form action="includes/auth.php" method="POST">
            <div class="form-group">
                <label for="login_id">กรุณากรอก เลขประจำตัวบัณฑิต / รหัสนักศึกษา / เลข ปชช.</label>
                <input type="text" id="login_id" name="login_id" required>
            </div>
            <button type="submit" class="btn btn-primary btn-full">เข้าสู่ระบบ</button>
        </form>
    </div>
</body>

</html>