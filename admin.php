<?php
// 1. เชื่อมต่อฐานข้อมูลและนับจำนวน (ส่วนคำนวณ)
include 'connect.php';

// นับรายการทั้งหมด
$res1 = mysqli_query($conn, "SELECT COUNT(*) as total FROM bookings");
$total = mysqli_fetch_assoc($res1)['total'];

// นับรายการที่รอการยืนยัน
$res2 = mysqli_query($conn, "SELECT COUNT(*) as pending FROM bookings WHERE status = 'pending'");
$pending = mysqli_fetch_assoc($res2)['pending'];

// ดึงรายการจองทั้งหมดมาโชว์ในตาราง
$all_bookings = mysqli_query($conn, "SELECT * FROM bookings ORDER BY booking_date DESC");
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>จัดการระบบเช่าชุด - YIM</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #fce4ec; text-align: center; margin: 0; padding: 20px; }
        h1 { color: #333; }
        
        /* ส่วนกล่องสถิติ */
        .stats-container { display: flex; justify-content: center; gap: 20px; margin-top: 20px; margin-bottom: 30px; }
        .stat-box { padding: 20px; border-radius: 15px; color: white; width: 180px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        .purple { background-color: #9c27b0; }
        .pink { background-color: #e91e63; }
        .stat-box h3 { margin: 0; font-size: 18px; }
        .stat-box h2 { margin: 10px 0 0; font-size: 35px; }

        /* ส่วนตาราง */
        .table-container { display: flex; justify-content: center; }
        table { border-collapse: collapse; width: 90%; max-width: 1000px; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        th { background-color: #f06292; color: white; padding: 15px; }
        td { border-bottom: 1px solid #ddd; padding: 12px; color: #333; }
        tr:hover { background-color: #f1f1f1; }

        /* ส่วนปุ่ม */
        .btn { padding: 6px 12px; text-decoration: none; border-radius: 5px; font-size: 13px; color: white; display: inline-block; margin: 2px; }
        .btn-confirm { background-color: #4caf50; }
        .btn-delete { background-color: #f44336; }
        .btn-back { margin-top: 20px; color: #e91e63; text-decoration: none; font-weight: bold; }
    </style>
</head>
<body>

    <h1>หน้าจัดการสมาชิกและรายการจอง</h1>

    <div class="stats-container">
        <div class="stat-box purple">
            <h3>รายการทั้งหมด</h3>
            <h2><?php echo $total; ?></h2>
        </div>
        <div class="stat-box pink">
            <h3>รอยืนยัน</h3>
            <h2><?php echo $pending; ?></h2>
        </div>
    </div>

    <h3>รายการลูกค้าที่จองชุด</h3>
    
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>ชื่อลูกค้า</th>
                    <th>ชื่อชุด</th>
                    <th>วันที่จอง</th>
                    <th>สถานะ / จัดการ</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = mysqli_fetch_assoc($all_bookings)) { ?>
                <tr>
                    <td><?php echo $row['customer_name']; ?></td>
                    <td><?php echo $row['product_name']; ?></td>
                    <td><?php echo $row['booking_date']; ?></td>
                    <td>
                        <strong><?php echo $row['status']; ?></strong><br><br>
                        
                        <a href="update_status.php?id=<?php echo $row['id']; ?>&status=confirmed" 
                           class="btn btn-confirm">ยืนยัน</a>
                        
                        <a href="delete_booking.php?id=<?php echo $row['id']; ?>" 
                           class="btn btn-delete" 
                           onclick="return confirm('แน่ใจนะว่าจะลบรายการนี้?')">ลบ</a>
                    </td>
                </tr>
                <?php } ?>
                
                <?php if(mysqli_num_rows($all_bookings) == 0) { ?>
                    <tr><td colspan="4">ยังไม่มีข้อมูลการจองในขณะนี้</td></tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <br>
    <a href="YIM.php" class="btn-back">← กลับหน้าหลัก</a>

</body>
</html>