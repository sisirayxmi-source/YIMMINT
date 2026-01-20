<?php
include 'connect.php';

// นับรายการทั้งหมด
$sql_all = "SELECT COUNT(*) as total FROM bookings";
$res_all = mysqli_query($conn, $sql_all);
$row_all = mysqli_fetch_assoc($res_all);
$total_bookings = $row_all['total'];

// นับรายการที่ยืนยันแล้ว
$sql_done = $sql_done = "SELECT COUNT(*) as total FROM bookings WHERE status = 'confirmed'";
$res_done = mysqli_query($conn, $sql_done);
$row_done = mysqli_fetch_assoc($res_done);
$confirmed_bookings = $row_done['total'];
// ดึงข้อมูลรายการจองทั้งหมด
$sql_list = "SELECT * FROM bookings ORDER BY booking_date DESC";
$res_list = mysqli_query($conn, $sql_list);
$all_bookings = [];

while($row = mysqli_fetch_assoc($res_list)) {
    $all_bookings[] = $row;
}

// แปลงเป็น JSON ให้หน้าเว็บอ่านออก
$bookings_json = json_encode($all_bookings);
?>