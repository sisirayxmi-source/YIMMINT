<?php include 'Omyim.php'; ?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ระบบเช่าชุด - YIM</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-rose-50">

    <main id="app" class="max-w-md mx-auto p-6 text-center">
        </main>

    <script>
        // ข้อมูลนี้ดึงมาจาก Omyim.php
        const currentBookings = <?php echo $bookings_json; ?>;
    </script>
    
    <script src="script.js"></script>
</body>
</html>