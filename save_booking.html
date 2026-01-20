<?php
include 'connect.php';

// 1. รับค่าจากหน้าเว็บ
$customer_name = $_POST['customer_name'] ?? 'ลูกค้าทั่วไป';
$outfit_name   = $_POST['product_name'] ?? 'ชุดไทยประยุกต์';
$booking_date  = date('Y-m-d H:i:s');
$address       = $_POST['address'] ?? '';

// 2. คำสั่งบันทึกลงฐานข้อมูล (ใช้ชื่อ outfit_name ที่คุณเพิ่งเพิ่มใน DB)
$sql = "INSERT INTO bookings (customer_name, outfit_name, booking_date, status) 
        VALUES ('$customer_name', '$outfit_name', '$booking_date', 'pending')";

if (mysqli_query($conn, $sql)) {
    
    // --- 3. ส่วนส่งข้อความ LINE แจ้งเตือน ---
    $access_token = 'braUSwo6YuXQWMsPaeXd42Ppa4C8BuAtx2E535oah0B0zkY6y8aP4O/ForxHicshlhH2Avf9JGXCKLV7FCb52JKVL/7Rukn7mXXXJEAYl...'; 
    $user_id = 'Uccb4b07a841775f50c608c9f2e838e9a';

    $messages = [
        'type' => 'text',
        'text' => "🔔 มีรายการจองใหม่!\n👗 ชุด: $outfit_name\n👤 ชื่อ: $customer_name\n📍 ที่อยู่: $address\n📅 วันที่: $booking_date"
    ];

    $url = 'https://api.line.me/v2/bot/message/push';
    $post = json_encode(['to' => $user_id, 'messages' => [$messages]]);
    $headers = [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $access_token
    ];
$ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    
    // 1. ตั้งค่าปิด SSL ก่อน (ต้องอยู่ก่อน exec)
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 

    // 2. สั่งส่งข้อมูลจริงเก็บค่าไว้ใน $result
    $result = curl_exec($ch);
    curl_close($ch);

    // 3. แสดงคำตอบจาก LINE (เพื่อดูว่าสำเร็จไหม)
    // echo "LINE Response: " . $result; // ถ้าอยากเช็คให้ลบ // ข้างหน้าออกค่ะ

    // 4. เมื่อทุกอย่างเสร็จให้แจ้งเตือนผู้ใช้
    echo "<script>alert('จองสำเร็จและแจ้งเตือน LINE แล้ว!'); window.location='admin.php';</script>";
}
?>