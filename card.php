<?php
// อัปเดต Path: เรียกใช้ config จาก includes/
require_once 'includes/config.php';

// ตรวจสอบว่า login หรือยัง
if (!isset($_SESSION['graduate_user_id'])) {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['graduate_user_id'];

// ดึงข้อมูลบัณฑิต (เฉพาะคนที่ลงทะเบียนสถานะ 1 เท่านั้น)
$stmt = $conn->prepare("SELECT g.*, c.campus_name FROM graduates g LEFT JOIN campus c ON g.campus_id = c.campus_id WHERE g.id = ? AND g.registration_status = 1");
if ($stmt === false) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();
$conn->close();

if ($result->num_rows === 0) {
    // ถ้าไม่พบ (เช่น สถานะไม่ใช่ 1) ให้เด้งกลับ
    header("Location: dashboard.php");
    exit;
}

$graduate = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>บัตรลงทะเบียนบัณฑิต</title>
    <!-- อัปเดต Path: เรียกใช้ CSS จาก assets/css/ -->
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- 1. QR Code Library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrious/4.0.2/qrious.min.js"></script>
    <!-- 2. Save as Image Library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
</head>
<body>
    <div class="container">
        <h2>บัตรลงทะเบียนบัณฑิต (ยืนยันเข้าร่วม)</h2>

        <!-- นี่คือส่วนของบัตรที่จะถูกบันทึก -->
        <div id="registrationCard">
            <img src="assets/img/LOGO_card.png" class="responsive" alt="" width="400" height="auto">
            <h2></h2>
            <p id="card-graduate-id"><?php echo htmlspecialchars($graduate['graduate_id']); ?></p>
             <p id="card-graduate-id"><?php echo htmlspecialchars($graduate['degree_level']); ?></p>
            <div class="pali">
            <h3 style="text-align: center;"><?php echo htmlspecialchars($graduate['fname_th'] . ' ' . $graduate['mname_th'] . ' ' . $graduate['lname_th']); ?></h3>
            </div>
            <!-- แก้ไข: ลบ inline style (display: flex...) ออก -->
            <!-- สไตล์จาก style.css (text-align: center) จะทำงานแทน -->
            <div id="qrcode-container">
                <!-- 3. Element สำหรับ QR Code -->
                <canvas id="qrcode"></canvas>
            </div>
            <!-- 5.2 รายละเอียดข้อมูลบัณฑิต -->
            <div class="pali">
                <p><strong>คณะ:</strong> <?php echo htmlspecialchars($graduate['faculty']); ?></p>
                <p><strong>หลักสูตร:</strong> <?php echo htmlspecialchars($graduate['program'] . ' ' . $graduate['major']); ?></p>
                <p><strong>วิทยาเขต:</strong> <?php echo htmlspecialchars($graduate['campus_name']); ?></p>
            </div>
        </div>
        
        <!-- 5.3 ปุ่มบันทึกบัตร -->
        <br>
        <button id="saveCard" class="btn btn-primary">บันทึกบัตรเป็นรูปภาพ</button>
        <a href="dashboard.php" class="btn btn-secondary">กลับหน้าหลัก</a>

    </div>

    <script>
    // 4. สร้าง QR Code
    new QRious({
        element: document.getElementById('qrcode'),
        value: '<?php echo $graduate['graduate_id']; ?>', // 5.1 ค่าของ QR Code
        size: 250,
        padding: 10,
        foreground: '#000000' // แก้ไข: เปลี่ยนเป็นสีดำ
    });

    // 5.3 ฟังก์ชันบันทึกบัตร
    document.getElementById('saveCard').addEventListener('click', function() {
        const cardElement = document.getElementById('registrationCard');
        
        html2canvas(cardElement, {
            scale: 2 // เพิ่มความละเอียด
        }).then(canvas => {
            const a = document.createElement('a');
            a.href = canvas.toDataURL('image/png');
            a.download = 'registration_card_<?php echo $graduate['graduate_id']; ?>.png';
            a.click();
        });
    });
    </script>
</body>
</html>

