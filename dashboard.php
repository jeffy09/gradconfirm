<?php
session_start();

/*
 * 1. ลบ Array $campus_schedules ที่ hardcode ไว้ออก
 * (เพราะเราจะไปดึงจากฐานข้อมูลแทน)
 */
// $campus_schedules = [ ... ];


/* * 2. ลบฟังก์ชัน getGraduateType() ออกจากไฟล์นี้
 * (เนื่องจากถูกประกาศไว้ใน 'includes/config.php' แล้วตาม Error)
 */
// function getGraduateType($type) { ... }

// อัปเดต Path: เรียกใช้ config จาก includes/
require_once 'includes/config.php';

// ตรวจสอบว่า login หรือยัง
if (!isset($_SESSION['graduate_user_id'])) {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['graduate_user_id'];
$action = $_GET['action'] ?? 'default'; // (ตรรกะเดิมของคุณ)

// 3. แก้ไข SQL Query: เพิ่ม c.schedule_details (คอลัมน์ใหม่จากตาราง campus)
$stmt = $conn->prepare("SELECT g.*, c.campus_name, c.schedule_details FROM graduates g LEFT JOIN campus c ON g.campus_id = c.campus_id WHERE g.id = ?");
if ($stmt === false) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$graduate = $result->fetch_assoc();


if (!$graduate) {
    // ไม่พบบัณฑิต (อาจเกิดข้อผิดพลาด)
    session_destroy();
    header("Location: index.php?error=data");
    exit;
}

$status = $graduate['registration_status'];

// 4. แก้ไขส่วนดึงข้อมูลกำหนดการ: ให้ดึงจาก $graduate['schedule_details'] ที่ query มาแทน
$user_campus_name = $graduate['campus_name'];
$schedule_info = $graduate['schedule_details']; // ดึงจากฐานข้อมูลโดยตรง

// ตรวจสอบเผื่อว่าใน DB เป็นค่าว่าง (NULL)
if (empty($schedule_info)) {
    $schedule_info = "ไม่พบข้อมูลกำหนดการสำหรับวิทยาเขตของคุณ";
}


// ปิดการเชื่อมต่อ (ควรทำหลังจากดึงข้อมูลเสร็จ)
$stmt->close();
$conn->close();

// ถ้ากด "แก้ไข" (action=edit) หรือยังไม่ลงทะเบียน (status=0) ให้แสดงฟอร์ม (ตรรกะเดิมของคุณ)
$show_form = ($action == 'edit' || $status == 0);

?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ระบบลงทะเบียนบัณฑิต - หน้าหลัก</title>
    <!-- อัปเดต Path: เรียกใช้ CSS จาก assets/css/ -->
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <div class="container">
 <img src="assets/img/LOGO.png" class="responsive" alt="" width="400" height="auto">
        <!-- 5. ส่วนแสดงผล HTML (เหมือนเดิม) แต่ตอนนี้ $schedule_info มาจาก DB -->
        <div class="schedule-box">
            <h4>กำหนดการรับปริญญา: <?php echo htmlspecialchars($user_campus_name); ?></h4>
            <p><?php echo htmlspecialchars($schedule_info); // ใช้ htmlspecialchars เพื่อความปลอดภัย 
                ?></p>
        </div>

        <?php if ($show_form): ?>

            <?php if ($status == 0): ?>
                <h2>กรุณาลงทะเบียนยืนยันการเข้ารับปริญญา</h2>
            <?php else: ?>
                <h2>แก้ไขสถานะการลงทะเบียน</h2>
            <?php endif; ?>

            <!-- แสดงข้อมูลบัณฑิต (โค้ดเดิมของคุณ) -->
            <h3>ข้อมูลบัณฑิต</h3>
            <div class="pali">
                <p><strong>เลขประจำตัวบัณฑิต:</strong> <?php echo htmlspecialchars($graduate['graduate_id']); ?></p>
                <p><strong>ชื่อ-สกุล:</strong> <?php echo htmlspecialchars($graduate['fname_th'] . ' ' . $graduate['mname_th'] . ' ' . $graduate['lname_th']); ?></p>
                <p><strong>หลักสูตร:</strong> <?php echo htmlspecialchars($graduate['program'] . ' ' . $graduate['major']); ?></p>
                <p><strong>เกียรตินิยม:</strong> <?php echo htmlspecialchars($graduate['honors'] ?? '-'); ?></p>
            </div>

            <!-- ฟอร์มลงทะเบียน (โค้ดเดิมของคุณ) -->
            <!-- อัปเดต Path: ส่ง action ไปที่ includes/submit.php -->
            <form id="regForm" action="includes/submit.php" method="POST">
                <h3>ยืนยันการเข้าร่วม</h3>
                <div class="radio-group">

                    <input type="radio" name="status" value="1" <?php if ($status == 1) echo 'checked'; ?> required>
                    <label>
                        <div class="radio-label"><font color="green"><u>ประสงค์ </u></font> เข้าร่วมพิธี</div>
                    </label>
                    </br>
                    <input type="radio" name="status" value="2" <?php if ($status == 2) echo 'checked'; ?>>
                    <label>
                        <div class="radio-label"><font color="red"><u>ไม่ประสงค์ </u></font> เข้าร่วมพิธี</div>
                    </label>
                </div>
                <br>
                <button type="submit" class="btn btn-primary">ยืนยันการลงทะเบียน</button>
                <?php if ($status != 0): // ถ้ากำลังแก้ไข ให้มีปุ่มยกเลิก (กลับหน้า dashboard) 
                ?>
                    <a href="dashboard.php" class="btn btn-secondary">ยกเลิก</a>
                <?php else: // ถ้าลงทะเบียนครั้งแรก (status=0) ให้เป็นปุ่มออกจากระบบ 
                ?>
                    <a href="includes/logout.php" class="btn btn-logout">ออกจากระบบ</a>
                <?php endif; ?>
            </form>

        <?php elseif ($status == 1): ?>

            <!-- สถานะ 1: เข้าร่วม (โค้ดเดิมของคุณ) -->
            <h2>สถานะการลงทะเบียน</h2>
            <div class="alert alert-success">
                <strong>คุณลงทะเบียนแล้ว (ยืนยันเข้าร่วมรับปริญญา)</strong>
                <p>วันที่ลงทะเบียน: <?php echo date('d/m/Y H:i', strtotime($graduate['registration_date'])); ?> น.</p>
            </div>
            <a href="card.php" class="btn btn-primary">ดูบัตรลงทะเบียน</a>
            <a href="dashboard.php?action=edit" class="btn btn-secondary">เปลี่ยนสถานะ</a>
            <!-- อัปเดต Path: เรียกใช้ logout.php จาก includes/ -->
            <a href="includes/logout.php" class="btn btn-logout">ออกจากระบบ</a>

        <?php elseif ($status == 2): ?>

            <!-- สถานะ 2: ไม่เข้าร่วม (โค้ดเดิมของคุณ) -->
            <h2>สถานะการลงทะเบียน</h2>
            <div class="alert alert-error">
                <strong>คุณลงทะเบียนแล้ว (ยืนยันไม่เข้าร่วมรับปริญญา)</strong>
                <p>วันที่ลงทะเบียน: <?php echo date('d/m/Y H:i', strtotime($graduate['registration_date'])); ?> น.</p>
            </div>
            <a href="dashboard.php?action=edit" class="btn btn-secondary">เปลี่ยนสถานะ</a>
            <!-- อัปเดต Path: เรียกใช้ logout.php จาก includes/ -->
            <a href="includes/logout.php" class="btn btn-logout">ออกจากระบบ</a>

        <?php endif; ?>

    </div>

    <script>
        // (โค้ด JavaScript เดิมของคุณ)
        // ตรวจสอบว่ามี form#regForm ในหน้านี้หรือไม่
        const regForm = document.getElementById('regForm');
        if (regForm) {
            regForm.addEventListener('submit', function(e) {
                e.preventDefault(); // หยุดการ submit ปกติ

                const statusRadio = document.querySelector('input[name="status"]:checked');
                if (!statusRadio) {
                    Swal.fire('ข้อผิดพลาด', 'กรุณาเลือกสถานะการเข้าร่วม', 'error');
                    return;
                }

                const status = statusRadio.value;
                const statusText = (status == 1) ? 'ยืนยันเข้าร่วม' : 'ยืนยันไม่เข้าร่วม';

                Swal.fire({
                    title: 'ยืนยันการเลือก?',
                    text: 'คุณต้องการ "' + statusText + '" ใช่หรือไม่',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#d97706', // ปรับสีให้เข้าธีม
                    cancelButtonColor: '#888',
                    confirmButtonText: 'ยืนยัน',
                    cancelButtonText: 'ยกเลิก'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // ถ้ากดยืนยัน ให้ submit form
                        e.target.submit();
                    }
                });
            });
        }
    </script>
</body>

</html>