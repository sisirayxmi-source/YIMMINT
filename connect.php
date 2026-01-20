<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "yimmint"; // ชื่อฐานข้อมูลตามในรูปของคุณ

$conn = mysqli_connect($host, $user, $pass, $dbname);

if (!$conn) {
    die("เชื่อมต่อไม่สำเร็จ: " . mysqli_connect_error());
}

// นี่คือจุดที่ใช้ utf8mb4 เพื่อให้พิมพ์ไทยได้ครับ
mysqli_set_charset($conn, "utf8mb4");
?>