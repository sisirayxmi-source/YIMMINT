<?php 
include 'Omyim.php'; 
?>
<!doctype html>
<html lang="th">
 <head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ระบบเช่าชุด</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap');
    
    * {
      font-family: 'Sarabun', sans-serif;
    }
    
    body {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    
    .lace-pattern {
      background-color: #FFB6D9;
      background-image: 
        repeating-linear-gradient(45deg, transparent, transparent 10px, rgba(255,255,255,.15) 10px, rgba(255,255,255,.15) 20px),
        repeating-linear-gradient(-45deg, transparent, transparent 10px, rgba(255,255,255,.15) 10px, rgba(255,255,255,.15) 20px);
    }
    
    .outfit-card {
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .outfit-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    }
    
    .loading-spinner {
      border: 3px solid rgba(255,255,255,0.3);
      border-top: 3px solid white;
      border-radius: 50%;
      width: 24px;
      height: 24px;
      animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }
    
    .toast {
      position: fixed;
      top: 20px;
      right: 20px;
      padding: 16px 24px;
      border-radius: 12px;
      color: white;
      font-weight: 600;
      z-index: 1000;
      animation: slideIn 0.3s ease;
    }
    
    @keyframes slideIn {
      from {
        transform: translateX(400px);
        opacity: 0;
      }
      to {
        transform: translateX(0);
        opacity: 1;
      }
    }
    
    .success-toast {
      background-color: #10b981;
    }
    
    .error-toast {
      background-color: #ef4444;
    }

    .filter-btn.active {
      background: #EC4899 !important;
      color: white !important;
    }
  </style>
  <style>@view-transition { navigation: auto; }</style>
  <script src="/_sdk/data_sdk.js" type="text/javascript"></script>
  <script src="/_sdk/element_sdk.js" type="text/javascript"></script>
 </head>
 <body>
  <div id="app"></div>
  <script>
    const config = {
      shop_name: 'VIva style',
      shop_tagline: 'ให้เช่าชุดสวยงาม คุณภาพดี ราคาย่อมเยา',
      qr_payment_note: 'สแกน QR Code เพื่อชำระค่ามัดจำ',
      qr_code_jpg: 'คิวอาร์.jfif',
      background_color: '#FFB6D9',
      card_color: '#FFFFFF',
      text_color: '#1F2937',
      primary_button_color: '#EC4899',
      secondary_button_color: '#8B5CF6'
    };

    // ใช้ localStorage แทน Data SDK
    let currentBookings = <?php echo $bookings_json; ?>;
    let currentView = 'home';
    let currentUser = null;
    let selectedOutfitForBooking = null;

    const outfits = [
      // ชุดสูท
      { id: 'suit-1', name: 'ชุดสูทสีกรมท่า', category: 'suit', price: 1500, deposit: 500, stock: 3, image: 'https://i.pinimg.com/736x/7f/4e/f0/7f4ef0b426c95f3fbc13d94f89bd9343.jpg" viewBox="0 0 200 250"%3E%3Crect fill="%23000080" width="200" height="250"/%3E%3Cpath d="M60 50 L70 80 L60 250 L90 250 L90 80 Z" fill="%23001050"/%3E%3Cpath d="M140 50 L130 80 L140 250 L110 250 L110 80 Z" fill="%23001050"/%3E%3Crect x="70" y="80" width="60" height="170" fill="%23FFF"/%3E%3Cpath d="M90 90 L100 120 L110 90" fill="%23000080"/%3E%3C/svg%3E' },
      { id: 'suit-2', name: 'ชุดสูทสีดำหรูหรา', category: 'suit', price: 1800, deposit: 600, stock: 2, image: 'https://i.pinimg.com/736x/f7/1c/38/f71c38751ea8e431a1d53f0088bfa7b8.jpg" viewBox="0 0 200 250"%3E%3Crect fill="%23000" width="200" height="250"/%3E%3Cpath d="M60 50 L70 80 L60 250 L90 250 L90 80 Z" fill="%23111"/%3E%3Cpath d="M140 50 L130 80 L140 250 L110 250 L110 80 Z" fill="%23111"/%3E%3Crect x="70" y="80" width="60" height="170" fill="%23FFF"/%3E%3Crect x="95" y="90" width="10" height="40" fill="%23000"/%3E%3C/svg%3E' },
      { id: 'suit-3', name: 'ชุดสูทสีเทาเงิน', category: 'suit', price: 1600, deposit: 500, stock: 4, image: 'https://i.pinimg.com/736x/52/45/a7/5245a7d73190eeb359c20a77bedde407.jpg" viewBox="0 0 200 250"%3E%3Crect fill="%23808080" width="200" height="250"/%3E%3Cpath d="M60 50 L70 80 L60 250 L90 250 L90 80 Z" fill="%23606060"/%3E%3Cpath d="M140 50 L130 80 L140 250 L110 250 L110 80 Z" fill="%23606060"/%3E%3Crect x="70" y="80" width="60" height="170" fill="%23E0E0E0"/%3E%3C/svg%3E' },
      { id: 'suit-4', name: 'ชุดสูทสีน้ำเงินเข้ม', category: 'suit', price: 1700, deposit: 550, stock: 3, image: 'https://i.pinimg.com/736x/0a/05/46/0a054669a3e061b14955567c0542292f.jpg" viewBox="0 0 200 250"%3E%3Crect fill="%23003366" width="200" height="250"/%3E%3Cpath d="M60 50 L70 80 L60 250 L90 250 L90 80 Z" fill="%23002244"/%3E%3Cpath d="M140 50 L130 80 L140 250 L110 250 L110 80 Z" fill="%23002244"/%3E%3Crect x="70" y="80" width="60" height="170" fill="%23CCE5FF"/%3E%3C/svg%3E' },
      { id: 'suit-5', name: 'ชุดสูทสีน้ำตาล', category: 'suit', price: 1550, deposit: 500, stock: 2, image: 'https://i.pinimg.com/1200x/31/37/91/313791557a174dd105606ffc160c973f.jpg" viewBox="0 0 200 250"%3E%3Crect fill="%23654321" width="200" height="250"/%3E%3Cpath d="M60 50 L70 80 L60 250 L90 250 L90 80 Z" fill="%23543210"/%3E%3Cpath d="M140 50 L130 80 L140 250 L110 250 L110 80 Z" fill="%23543210"/%3E%3Crect x="70" y="80" width="60" height="170" fill="%23FFF8DC"/%3E%3C/svg%3E' },
      { id: 'suit-6', name: 'ชุดสูทสีเบจ', category: 'suit', price: 1500, deposit: 500, stock: 5, image: 'https://i.pinimg.com/1200x/63/79/8c/63798c95022643f63673ba10117696e1.jpg" viewBox="0 0 200 250"%3E%3Crect fill="%23D2B48C" width="200" height="250"/%3E%3Cpath d="M60 50 L70 80 L60 250 L90 250 L90 80 Z" fill="%23C1A37C"/%3E%3Cpath d="M140 50 L130 80 L140 250 L110 250 L110 80 Z" fill="%23C1A37C"/%3E%3Crect x="70" y="80" width="60" height="170" fill="%23FFFAF0"/%3E%3C/svg%3E' },
      { id: 'suit-7', name: 'ชุดสูทสีชมพูอ่อน', category: 'suit', price: 1650, deposit: 550, stock: 2, image: 'https://i.pinimg.com/736x/54/b8/17/54b8173401755ee1b7cd9db32c9eef10.jpg" viewBox="0 0 200 250"%3E%3Crect fill="%23FFB6C1" width="200" height="250"/%3E%3Cpath d="M60 50 L70 80 L60 250 L90 250 L90 80 Z" fill="%23FFA5B0"/%3E%3Cpath d="M140 50 L130 80 L140 250 L110 250 L110 80 Z" fill="%23FFA5B0"/%3E%3Crect x="70" y="80" width="60" height="170" fill="%23FFF"/%3E%3C/svg%3E' },
      { id: 'suit-8', name: 'ชุดสูทสีเขียวมรกต', category: 'suit', price: 1750, deposit: 600, stock: 1, image: 'https://i.pinimg.com/736x/d1/34/21/d134211d131c7d33ec9a1f65c23c7c2e.jpg" viewBox="0 0 200 250"%3E%3Crect fill="%23046307" width="200" height="250"/%3E%3Cpath d="M60 50 L70 80 L60 250 L90 250 L90 80 Z" fill="%23035206"/%3E%3Cpath d="M140 50 L130 80 L140 250 L110 250 L110 80 Z" fill="%23035206"/%3E%3Crect x="70" y="80" width="60" height="170" fill="%23E8F5E9"/%3E%3C/svg%3E' },
      { id: 'suit-9', name: 'ชุดสูทสีแดงเข้ม', category: 'suit', price: 1700, deposit: 550, stock: 2, image: 'https://i.pinimg.com/1200x/60/30/b8/6030b8280ddfbafc02d8e8233ec8345f.jpg" viewBox="0 0 200 250"%3E%3Crect fill="%238B0000" width="200" height="250"/%3E%3Cpath d="M60 50 L70 80 L60 250 L90 250 L90 80 Z" fill="%23700000"/%3E%3Cpath d="M140 50 L130 80 L140 250 L110 250 L110 80 Z" fill="%23700000"/%3E%3Crect x="70" y="80" width="60" height="170" fill="%23FFF5F5"/%3E%3C/svg%3E' },
      { id: 'suit-10', name: 'ชุดสูทสีขาวครีม', category: 'suit', price: 1600, deposit: 500, stock: 4, image: 'https://i.pinimg.com/736x/ab/6f/73/ab6f733c86dc20b5076564b1e36ce832.jpg" viewBox="0 0 200 250"%3E%3Crect fill="%23FFFDD0" width="200" height="250"/%3E%3Cpath d="M60 50 L70 80 L60 250 L90 250 L90 80 Z" fill="%23FFEFC0"/%3E%3Cpath d="M140 50 L130 80 L140 250 L110 250 L110 80 Z" fill="%23FFEFC0"/%3E%3Crect x="70" y="80" width="60" height="170" fill="%23FFF"/%3E%3C/svg%3E' },
      
      // ชุดราตรี
      { id: 'evening-1', name: 'ชุดราตรีสีแดงเลื่อม', category: 'evening', price: 2500, deposit: 800, stock: 2, image: 'https://i.pinimg.com/1200x/cb/b3/7e/cbb37ec2072dd7658ea2ba8a2a00d4ad.jpg" viewBox="0 0 200 280"%3E%3Cpath d="M100 20 Q70 40 60 80 L60 280 L140 280 L140 80 Q130 40 100 20 Z" fill="%23DC143C"/%3E%3Cpath d="M70 60 Q100 70 130 60" stroke="%23FFD700" stroke-width="2" fill="none"/%3E%3Ccircle cx="80" cy="100" r="3" fill="%23FFD700"/%3E%3Ccircle cx="120" cy="100" r="3" fill="%23FFD700"/%3E%3Ccircle cx="100" cy="120" r="3" fill="%23FFD700"/%3E%3C/svg%3E' },
      { id: 'evening-2', name: 'ชุดราตรีสีดำยาว', category: 'evening', price: 2800, deposit: 900, stock: 1, image: 'https://i.pinimg.com/736x/a5/f4/fa/a5f4faa60b65f7a40d731e00c1f7f41b.jpg" viewBox="0 0 200 280"%3E%3Cpath d="M100 20 Q70 40 60 80 L50 280 L150 280 L140 80 Q130 40 100 20 Z" fill="%23000"/%3E%3Cpath d="M70 60 Q100 80 130 60" stroke="%23333" stroke-width="1" fill="none"/%3E%3Cpath d="M60 150 Q100 160 140 150" stroke="%23222" stroke-width="1" fill="none"/%3E%3C/svg%3E' },
      { id: 'evening-3', name: 'ชุดราตรีสีทองมันเงา', category: 'evening', price: 3000, deposit: 1000, stock: 1, image: 'https://i.pinimg.com/736x/74/e8/0c/74e80c40af496cd5ac622461efeb2fb3.jpg" viewBox="0 0 200 280"%3E%3Cpath d="M100 20 Q70 40 60 80 L60 280 L140 280 L140 80 Q130 40 100 20 Z" fill="%23FFD700"/%3E%3Cpath d="M70 100 L130 100" stroke="%23FFA500" stroke-width="2"/%3E%3Cpath d="M70 150 L130 150" stroke="%23FFA500" stroke-width="2"/%3E%3Ccircle cx="100" cy="80" r="8" fill="%23FFF"/%3E%3C/svg%3E' },
      { id: 'evening-4', name: 'ชุดราตรีสีน้ำเงินกรมท่า', category: 'evening', price: 2600, deposit: 850, stock: 3, image: 'https://i.pinimg.com/1200x/e7/d5/2e/e7d52ecfef29b47655d1d3bc3254143a.jpg" viewBox="0 0 200 280"%3E%3Cpath d="M100 20 Q70 40 60 80 L60 280 L140 280 L140 80 Q130 40 100 20 Z" fill="%23000080"/%3E%3Cpath d="M70 60 Q100 70 130 60" stroke="%234169E1" stroke-width="2" fill="none"/%3E%3Cpath d="M80 100 L85 110 L75 110 Z" fill="%23FFF"/%3E%3Cpath d="M120 100 L125 110 L115 110 Z" fill="%23FFF"/%3E%3C/svg%3E' },
      { id: 'evening-5', name: 'ชุดราตรีสีเขียวมรกต', category: 'evening', price: 2700, deposit: 900, stock: 2, image: 'https://i.pinimg.com/736x/c7/76/12/c7761294d7b266eb6eb76a92e694b2dd.jpg" viewBox="0 0 200 280"%3E%3Cpath d="M100 20 Q70 40 60 80 L60 280 L140 280 L140 80 Q130 40 100 20 Z" fill="%2350C878"/%3E%3Cpath d="M70 70 Q100 85 130 70" stroke="%23228B22" stroke-width="2" fill="none"/%3E%3Ccircle cx="100" cy="100" r="5" fill="%23FFD700"/%3E%3C/svg%3E' },
      { id: 'evening-6', name: 'ชุดราตรีสีม่วงลาเวนเดอร์', category: 'evening', price: 2550, deposit: 800, stock: 2, image: 'https://i.pinimg.com/736x/cf/8c/00/cf8c00060c6297ad9bae110aa9d754e0.jpg" viewBox="0 0 200 280"%3E%3Cpath d="M100 20 Q70 40 60 80 L60 280 L140 280 L140 80 Q130 40 100 20 Z" fill="%23967BB6"/%3E%3Cpath d="M60 80 L70 120 L130 120 L140 80" fill="%23E6E6FA"/%3E%3Ccircle cx="90" cy="90" r="4" fill="%23FFF"/%3E%3Ccircle cx="110" cy="90" r="4" fill="%23FFF"/%3E%3C/svg%3E' },
      { id: 'evening-7', name: 'ชุดราตรีสีเงินวับวาว', category: 'evening', price: 2900, deposit: 950, stock: 1, image: 'https://i.pinimg.com/1200x/b4/fa/d0/b4fad0a76a48471e734cac2ddb9e6d8b.jpg" viewBox="0 0 200 280"%3E%3Cpath d="M100 20 Q70 40 60 80 L60 280 L140 280 L140 80 Q130 40 100 20 Z" fill="%23C0C0C0"/%3E%3Ccircle cx="80" cy="100" r="3" fill="%23FFF"/%3E%3Ccircle cx="120" cy="100" r="3" fill="%23FFF"/%3E%3Ccircle cx="100" cy="150" r="3" fill="%23FFF"/%3E%3Ccircle cx="90" cy="180" r="3" fill="%23FFF"/%3E%3Ccircle cx="110" cy="180" r="3" fill="%23FFF"/%3E%3C/svg%3E' },
      { id: 'evening-8', name: 'ชุดราตรีสีชมพูบานเย็น', category: 'evening', price: 2650, deposit: 850, stock: 2, image: 'https://i.pinimg.com/736x/3c/06/08/3c0608777ede7ee1a7f34b5319a84fd0.jpg" viewBox="0 0 200 280"%3E%3Cpath d="M100 20 Q70 40 60 80 L60 280 L140 280 L140 80 Q130 40 100 20 Z" fill="%23FF69B4"/%3E%3Cpath d="M70 70 Q100 80 130 70" stroke="%23FFC0CB" stroke-width="3" fill="none"/%3E%3Ccircle cx="100" cy="100" r="6" fill="%23FFF"/%3E%3C/svg%3E' },
      { id: 'evening-9', name: 'ชุดราตรีสีขาวไข่มุก', category: 'evening', price: 2800, deposit: 900, stock: 2, image: 'https://i.pinimg.com/1200x/15/4b/3c/154b3c1e754ef8b935aed24bebb8ceb6.jpg" viewBox="0 0 200 280"%3E%3Cpath d="M100 20 Q70 40 60 80 L60 280 L140 280 L140 80 Q130 40 100 20 Z" fill="%23F5F5F5"/%3E%3Cpath d="M70 60 Q100 70 130 60" stroke="%23E0E0E0" stroke-width="2" fill="none"/%3E%3Ccircle cx="85" cy="90" r="4" fill="%23DDD"/%3E%3Ccircle cx="115" cy="90" r="4" fill="%23DDD"/%3E%3Ccircle cx="100" cy="110" r="4" fill="%23DDD"/%3E%3C/svg%3E' },
      { id: 'evening-10', name: 'ชุดราตรีสีแชมเปญทอง', category: 'evening', price: 2750, deposit: 900, stock: 1, image: 'https://i.pinimg.com/1200x/59/66/e7/5966e7e79d075259651ab73f461d7fd6.jpg" viewBox="0 0 200 280"%3E%3Cpath d="M100 20 Q70 40 60 80 L60 280 L140 280 L140 80 Q130 40 100 20 Z" fill="%23F7E7CE"/%3E%3Cpath d="M70 70 Q100 85 130 70" stroke="%23D4AF37" stroke-width="2" fill="none"/%3E%3Ccircle cx="90" cy="100" r="3" fill="%23FFD700"/%3E%3Ccircle cx="110" cy="100" r="3" fill="%23FFD700"/%3E%3C/svg%3E' },
      
      // ชุดผ้าไทย
      { id: 'thai-1', name: 'ชุดไทยจักรพรรดิสีทอง', category: 'thai', price: 2200, deposit: 700, stock: 2, image: 'https://i.pinimg.com/1200x/bd/28/36/bd283613d733a366f01a50b297a5424d.jpg" viewBox="0 0 200 280"%3E%3Cpath d="M60 60 L140 60 L140 120 Q100 140 60 120 Z" fill="%23FFD700"/%3E%3Cpath d="M70 120 L70 280 L130 280 L130 120" fill="%23FF1493"/%3E%3Cpath d="M70 130 L130 130" stroke="%23FFD700" stroke-width="3"/%3E%3Cpath d="M70 150 L130 150" stroke="%23FFD700" stroke-width="3"/%3E%3Crect x="85" y="60" width="30" height="60" fill="%23FFF"/%3E%3C/svg%3E' },
      { id: 'thai-2', name: 'ชุดไทยบรมพิมานสีแดง', category: 'thai', price: 2400, deposit: 750, stock: 1, image: 'https://i.pinimg.com/1200x/72/e4/69/72e469205c3bb4db1dfe42715b1e13f8.jpg" viewBox="0 0 200 280"%3E%3Cpath d="M60 60 L140 60 L140 120 Q100 140 60 120 Z" fill="%23FF0000"/%3E%3Cpath d="M70 120 L70 280 L130 280 L130 120" fill="%23FFD700"/%3E%3Cpath d="M80 140 L120 140" stroke="%23FF0000" stroke-width="4"/%3E%3Cpath d="M80 170 L120 170" stroke="%23FF0000" stroke-width="4"/%3E%3Crect x="85" y="60" width="30" height="60" fill="%23FFF8DC"/%3E%3C/svg%3E' },
      { id: 'thai-3', name: 'ชุดไทยจักรีสีเขียว', category: 'thai', price: 2100, deposit: 700, stock: 3, image: 'https://i.pinimg.com/736x/1e/fd/1b/1efd1bcd6baa79541eb8a6c6f9a78dbf.jpg" viewBox="0 0 200 280"%3E%3Cpath d="M60 60 L140 60 L140 120 Q100 140 60 120 Z" fill="%23228B22"/%3E%3Cpath d="M70 120 L70 280 L130 280 L130 120" fill="%23FFD700"/%3E%3Cpath d="M75 135 L125 135" stroke="%23228B22" stroke-width="3"/%3E%3Cpath d="M75 160 L125 160" stroke="%23228B22" stroke-width="3"/%3E%3Crect x="85" y="60" width="30" height="60" fill="%23FFFACD"/%3E%3C/svg%3E' },
      { id: 'thai-4', name: 'ชุดไทยศิวาลัยสีน้ำเงิน', category: 'thai', price: 2300, deposit: 750, stock: 2, image: 'https://i.pinimg.com/1200x/62/bb/ff/62bbff46757275f36965685a58fe5706.jpg" viewBox="0 0 200 280"%3E%3Cpath d="M60 60 L140 60 L140 120 Q100 140 60 120 Z" fill="%23000080"/%3E%3Cpath d="M70 120 L70 280 L130 280 L130 120" fill="%23FFE4B5"/%3E%3Cpath d="M75 140 L125 140" stroke="%23000080" stroke-width="3"/%3E%3Cpath d="M75 165 L125 165" stroke="%23000080" stroke-width="3"/%3E%3Crect x="85" y="60" width="30" height="60" fill="%23E6F3FF"/%3E%3C/svg%3E' },
      { id: 'thai-5', name: 'ชุดไทยดุสิตสีม่วง', category: 'thai', price: 2250, deposit: 700, stock: 2, image: 'https://i.pinimg.com/736x/e1/1b/3c/e11b3c07005b5783f1219baccbf2b0d4.jpg" viewBox="0 0 200 280"%3E%3Cpath d="M60 60 L140 60 L140 120 Q100 140 60 120 Z" fill="%238B008B"/%3E%3Cpath d="M70 120 L70 280 L130 280 L130 120" fill="%23FFD700"/%3E%3Cpath d="M75 138 L125 138" stroke="%238B008B" stroke-width="3"/%3E%3Cpath d="M75 163 L125 163" stroke="%238B008B" stroke-width="3"/%3E%3Crect x="85" y="60" width="30" height="60" fill="%23F8E6FF"/%3E%3C/svg%3E' },
      { id: 'thai-6', name: 'ชุดไทยอมรินทร์สีชมพู', category: 'thai', price: 2150, deposit: 700, stock: 3, image: 'https://i.pinimg.com/1200x/98/9b/3d/989b3d849a0b017dfac99141b9a2e856.jpg" viewBox="0 0 200 280"%3E%3Cpath d="M60 60 L140 60 L140 120 Q100 140 60 120 Z" fill="%23FF1493"/%3E%3Cpath d="M70 120 L70 280 L130 280 L130 120" fill="%23FFE4E1"/%3E%3Cpath d="M75 135 L125 135" stroke="%23FF1493" stroke-width="3"/%3E%3Cpath d="M75 160 L125 160" stroke="%23FF1493" stroke-width="3"/%3E%3Crect x="85" y="60" width="30" height="60" fill="%23FFF"/%3E%3C/svg%3E' },
      { id: 'thai-7', name: 'ชุดไทยบุรพาภิรมย์สีฟ้า', category: 'thai', price: 2200, deposit: 700, stock: 2, image: 'https://i.pinimg.com/736x/a8/f8/d5/a8f8d56a39b043efc2db16cc1cb4c5fa.jpg" viewBox="0 0 200 280"%3E%3Cpath d="M60 60 L140 60 L140 120 Q100 140 60 120 Z" fill="%2387CEEB"/%3E%3Cpath d="M70 120 L70 280 L130 280 L130 120" fill="%23FFF5EE"/%3E%3Cpath d="M75 137 L125 137" stroke="%2387CEEB" stroke-width="3"/%3E%3Cpath d="M75 162 L125 162" stroke="%2387CEEB" stroke-width="3"/%3E%3Crect x="85" y="60" width="30" height="60" fill="%23E6F7FF"/%3E%3C/svg%3E' },
      { id: 'thai-8', name: 'ชุดไทยจิตรลดาสีเหลือง', category: 'thai', price: 2100, deposit: 700, stock: 3, image: 'https://i.pinimg.com/736x/09/a6/f7/09a6f7cdbdebe08ff9443f8ce342ad7f.jpg" viewBox="0 0 200 280"%3E%3Cpath d="M60 60 L140 60 L140 120 Q100 140 60 120 Z" fill="%23FFD700"/%3E%3Cpath d="M70 120 L70 280 L130 280 L130 120" fill="%23FFF8DC"/%3E%3Cpath d="M75 135 L125 135" stroke="%23FFD700" stroke-width="3"/%3E%3Cpath d="M75 160 L125 160" stroke="%23FFD700" stroke-width="3"/%3E%3Crect x="85" y="60" width="30" height="60" fill="%23FFFACD"/%3E%3C/svg%3E' },
      { id: 'thai-9', name: 'ชุดไทยเรือนต้นสีครีม', category: 'thai', price: 2050, deposit: 650, stock: 4, image: 'https://i.pinimg.com/736x/d9/a0/c7/d9a0c75f92f5d503f23fd35140ce8777.jpg" viewBox="0 0 200 280"%3E%3Cpath d="M60 60 L140 60 L140 120 Q100 140 60 120 Z" fill="%23FFFDD0"/%3E%3Cpath d="M70 120 L70 280 L130 280 L130 120" fill="%23DEB887"/%3E%3Cpath d="M75 135 L125 135" stroke="%23D2B48C" stroke-width="3"/%3E%3Cpath d="M75 160 L125 160" stroke="%23D2B48C" stroke-width="3"/%3E%3Crect x="85" y="60" width="30" height="60" fill="%23FFF"/%3E%3C/svg%3E' },
      { id: 'thai-10', name: 'ชุดไทยจักรีสีแดงสด', category: 'thai', price: 2350, deposit: 750, stock: 1, image: 'https://i.pinimg.com/1200x/f4/2b/77/f42b77e109f45d3aea9b9a66c1bbe6c4.jpg"0 0 200 280"%3E%3Cpath d="M60 60 L140 60 L140 120 Q100 140 60 120 Z" fill="%23DC143C"/%3E%3Cpath d="M70 120 L70 280 L130 280 L130 120" fill="%23FFFACD"/%3E%3Cpath d="M75 138 L125 138" stroke="%23DC143C" stroke-width="4"/%3E%3Cpath d="M75 163 L125 163" stroke="%23DC143C" stroke-width="4"/%3E%3Crect x="85" y="60" width="30" height="60" fill="%23FFF0F5"/%3E%3C/svg%3E' }
    ];

    function saveBookings() {
      localStorage.setItem('bookings', JSON.stringify(currentBookings));
    }

    function renderHomeView() {
      currentView = 'home';
      const app = document.getElementById('app');
      app.innerHTML = `
        <div style="min-height: 100vh; padding: 32px 16px; background: linear-gradient(135deg, ${config.background_color} 0%, #FFF0F5 100%);">
          <div style="max-width: 1200px; margin: 0 auto;">
            <div style="text-align: center; margin-bottom: 48px;">
              <h1 class="shop-name" style="font-size: 40px; color: ${config.text_color}; margin-bottom: 8px; font-weight: 700; text-shadow: 2px 2px 4px rgba(0,0,0,0.1);">
                ${config.shop_name}
              </h1>
              <p class="shop-tagline" style="font-size: 18px; color: ${config.text_color}; opacity: 0.85; font-weight: 400;">
                ${config.shop_tagline}
              </p>
              <div style="margin-top: 24px; display: flex; gap: 12px; justify-content: center; flex-wrap: wrap;">
                <button onclick="showLoginModal()" style="background: ${config.primary_button_color}; color: white; padding: 12px 32px; border-radius: 12px; border: none; cursor: pointer; font-weight: 600; font-size: 16px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); transition: all 0.3s ease;">
                  📝 สมัครสมาชิก / เข้าสู่ระบบ
                </button>
                <button onclick="renderAdminView()" style="background: ${config.secondary_button_color}; color: white; padding: 12px 32px; border-radius: 12px; border: none; cursor: pointer; font-weight: 600; font-size: 16px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); transition: all 0.3s ease;">
                   จัดการสมาชิก
                </button>
              </div>
            </div>

            <div style="margin-bottom: 32px;">
              <div style="display: flex; gap: 12px; justify-content: center; flex-wrap: wrap;">
                <button onclick="filterOutfits('all')" class="filter-btn active" data-category="all" style="padding: 10px 24px; background: ${config.primary_button_color}; color: white; border: 2px solid ${config.primary_button_color}; border-radius: 24px; cursor: pointer; font-weight: 600; font-size: 14px; transition: all 0.3s ease;">
                  ทั้งหมด (30)
                </button>
                <button onclick="filterOutfits('suit')" class="filter-btn" data-category="suit" style="padding: 10px 24px; background: white; border: 2px solid ${config.primary_button_color}; color: ${config.primary_button_color}; border-radius: 24px; cursor: pointer; font-weight: 600; font-size: 14px; transition: all 0.3s ease;">
                  👔 ชุดสูท (10)
                </button>
                <button onclick="filterOutfits('evening')" class="filter-btn" data-category="evening" style="padding: 10px 24px; background: white; border: 2px solid ${config.primary_button_color}; color: ${config.primary_button_color}; border-radius: 24px; cursor: pointer; font-weight: 600; font-size: 14px; transition: all 0.3s ease;">
                  👗 ชุดราตรี (10)
                </button>
                <button onclick="filterOutfits('thai')" class="filter-btn" data-category="thai" style="padding: 10px 24px; background: white; border: 2px solid ${config.primary_button_color}; color: ${config.primary_button_color}; border-radius: 24px; cursor: pointer; font-weight: 600; font-size: 14px; transition: all 0.3s ease;">
                  👘 ชุดผ้าไทย (10)
                </button>
              </div>
            </div>

            <div id="outfits-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 24px;">
              ${outfits.map(outfit => `
                <div class="outfit-card" data-category="${outfit.category}" style="background: ${config.card_color}; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.1); cursor: pointer;" onclick="showOutfitDetail('${outfit.id}')">
                  <div style="width: 100%; height: 220px; background: linear-gradient(135deg, #f5f5f5 0%, #e0e0e0 100%); display: flex; align-items: center; justify-content: center; overflow: hidden;">
                    <img src="${outfit.image}" alt="${outfit.name}" style="width: 100%; height: 100%; object-fit: contain;">
                  </div>
                  <div style="padding: 16px;">
                    <h3 style="font-size: 18px; color: ${config.text_color}; margin-bottom: 8px; font-weight: 600;">${outfit.name}</h3>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                      <span style="color: ${config.primary_button_color}; font-weight: 700; font-size: 18px;">฿${outfit.price}</span>
                      <span style="color: ${config.text_color}; opacity: 0.7; font-size: 14px;">มัดจำ ฿${outfit.deposit}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                      <span style="color: ${config.text_color}; opacity: 0.8; font-size: 14px;">คงเหลือ: ${outfit.stock} ชุด</span>
                      <button onclick="event.stopPropagation(); selectOutfit('${outfit.id}')" style="background: ${config.primary_button_color}; color: white; padding: 8px 16px; border-radius: 8px; border: none; cursor: pointer; font-weight: 600; font-size: 14px;">
                        เลือกเช่า
                      </button>
                    </div>
                  </div>
                </div>
              `).join('')}
            </div>
          </div>
        </div>
      `;
    }

    function filterOutfits(category) {
      const cards = document.querySelectorAll('.outfit-card');
      const buttons = document.querySelectorAll('.filter-btn');
      
      buttons.forEach(btn => {
        btn.classList.remove('active');
        if (btn.dataset.category === category) {
          btn.classList.add('active');
        }
      });
      
      cards.forEach(card => {
        if (category === 'all' || card.dataset.category === category) {
          card.style.display = 'block';
        } else {
          card.style.display = 'none';
        }
      });
    }

    function showOutfitDetail(outfitId) {
      const outfit = outfits.find(o => o.id === outfitId);
      if (!outfit) return;
      
      const app = document.getElementById('app');
      app.innerHTML = `
        <div style="min-height: 100vh; padding: 32px 16px; background: linear-gradient(135deg, ${config.background_color} 0%, #FFF0F5 100%);">
          <div style="max-width: 800px; margin: 0 auto;">
            <button onclick="renderHomeView()" style="background: ${config.secondary_button_color}; color: white; padding: 10px 20px; border-radius: 8px; border: none; cursor: pointer; margin-bottom: 24px; font-weight: 600; font-size: 16px;">
              ← กลับหน้าหลัก
            </button>
            
            <div style="background: ${config.card_color}; border-radius: 20px; overflow: hidden; box-shadow: 0 8px 24px rgba(0,0,0,0.15);">
              <div style="width: 100%; height: 400px; background: linear-gradient(135deg, #f5f5f5 0%, #e0e0e0 100%); display: flex; align-items: center; justify-content: center;">
                <img src="${outfit.image}" alt="${outfit.name}" style="width: 100%; height: 100%; object-fit: contain;">
              </div>
              
              <div style="padding: 32px;">
                <h2 style="font-size: 28px; color: ${config.text_color}; margin-bottom: 16px; font-weight: 700;">${outfit.name}</h2>
                
                <div style="background: linear-gradient(135deg, ${config.background_color} 0%, #FFF0F5 100%); border-radius: 12px; padding: 20px; margin-bottom: 24px;">
                  <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px;">
                    <div>
                      <p style="color: ${config.text_color}; opacity: 0.7; font-size: 14px; margin-bottom: 4px;">ราคาเช่า</p>
                      <p style="color: ${config.primary_button_color}; font-size: 24px; font-weight: 700;">฿${outfit.price}</p>
                    </div>
                    <div>
                      <p style="color: ${config.text_color}; opacity: 0.7; font-size: 14px; margin-bottom: 4px;">ค่ามัดจำ</p>
                      <p style="color: ${config.secondary_button_color}; font-size: 24px; font-weight: 700;">฿${outfit.deposit}</p>
                    </div>
                    <div>
                      <p style="color: ${config.text_color}; opacity: 0.7; font-size: 14px; margin-bottom: 4px;">จำนวนคงเหลือ</p>
                      <p style="color: ${config.text_color}; font-size: 20px; font-weight: 600;">${outfit.stock} ชุด</p>
                    </div>
                    <div>
                      <p style="color: ${config.text_color}; opacity: 0.7; font-size: 14px; margin-bottom: 4px;">ประเภท</p>
                      <p style="color: ${config.text_color}; font-size: 20px; font-weight: 600;">${outfit.category === 'suit' ? '👔 ชุดสูท' : outfit.category === 'evening' ? '👗 ชุดราตรี' : '👘 ชุดผ้าไทย'}</p>
                    </div>
                  </div>
                </div>
                
                <button onclick="selectOutfit('${outfit.id}')" style="width: 100%; background: ${config.primary_button_color}; color: white; padding: 16px; border-radius: 12px; border: none; cursor: pointer; font-weight: 700; font-size: 18px; box-shadow: 0 4px 12px rgba(0,0,0,0.2);">
                  เลือกเช่าชุดนี้
                </button>
              </div>
            </div>
          </div>
        </div>
      `;
    }

    function selectOutfit(outfitId) {
      if (!currentUser) {
        showToast('กรุณาเข้าสู่ระบบก่อนทำการเช่า', 'error');
        showLoginModal();
        return;
      }
      
      const outfit = outfits.find(o => o.id === outfitId);
      if (!outfit) return;
      
      if (outfit.stock === 0) {
        showToast('ชุดนี้ไม่มีให้เช่าแล้ว', 'error');
        return;
      }
      
      selectedOutfitForBooking = outfit;
      showBookingModal();
    }

    function showLoginModal() {
      const app = document.getElementById('app');
      app.innerHTML = `
        <div style="min-height: 100vh; padding: 32px 16px; background: linear-gradient(135deg, ${config.background_color} 0%, #FFF0F5 100%); display: flex; align-items: center; justify-content: center;">
          <div style="background: ${config.card_color}; border-radius: 20px; padding: 40px; max-width: 500px; width: 100%; box-shadow: 0 8px 32px rgba(0,0,0,0.15);">
            <h2 style="font-size: 28px; color: ${config.text_color}; margin-bottom: 24px; text-align: center; font-weight: 700;">📝 สมัครสมาชิก / เข้าสู่ระบบ</h2>
            
            <form id="login-form" onsubmit="handleLogin(event)">
              <div style="margin-bottom: 20px;">
                <label for="firstName" style="display: block; color: ${config.text_color}; margin-bottom: 8px; font-weight: 600; font-size: 14px;">ชื่อจริง *</label>
                <input type="text" id="firstName" required style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 16px; box-sizing: border-box;">
              </div>
              
              <div style="margin-bottom: 20px;">
                <label for="lastName" style="display: block; color: ${config.text_color}; margin-bottom: 8px; font-weight: 600; font-size: 14px;">นามสกุล *</label>
                <input type="text" id="lastName" required style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 16px; box-sizing: border-box;">
              </div>
              
              <div style="margin-bottom: 20px;">
                <label for="email" style="display: block; color: ${config.text_color}; margin-bottom: 8px; font-weight: 600; font-size: 14px;">อีเมล (Gmail) *</label>
                <input type="email" id="email" required style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 16px; box-sizing: border-box;">
              </div>
              
              <div style="margin-bottom: 20px;">
                <label for="phone" style="display: block; color: ${config.text_color}; margin-bottom: 8px; font-weight: 600; font-size: 14px;">เบอร์โทรศัพท์ *</label>
                <input type="tel" id="phone" required pattern="[0-9]{10}" placeholder="0812345678" style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 16px; box-sizing: border-box;">
              </div>
              
              <div style="margin-bottom: 24px;">
                <label for="idCard" style="display: block; color: ${config.text_color}; margin-bottom: 8px; font-weight: 600; font-size: 14px;">แนบรูปบัตรประชาชน (สำหรับยืนยันตัวตน) *</label>
                <input type="file" id="idCard" accept="image/*" required style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 14px; box-sizing: border-box;">
                <p style="color: ${config.text_color}; opacity: 0.6; font-size: 12px; margin-top: 4px;">ข้อมูลจะถูกเก็บรักษาไว้เป็นความลับ</p>
              </div>
              
              <button type="submit" id="login-submit-btn" style="width: 100%; background: ${config.primary_button_color}; color: white; padding: 14px; border-radius: 12px; border: none; cursor: pointer; font-weight: 700; font-size: 18px; box-shadow: 0 4px 12px rgba(0,0,0,0.2);">
                เข้าสู่ระบบ
              </button>
              
              <button type="button" onclick="renderHomeView()" style="width: 100%; background: transparent; color: ${config.text_color}; padding: 14px; border-radius: 12px; border: 2px solid ${config.text_color}; cursor: pointer; font-weight: 600; font-size: 16px; margin-top: 12px;">
                ยกเลิก
              </button>
            </form>
          </div>
        </div>
      `;
    }

    function handleLogin(event) {
      event.preventDefault();
      
      const firstName = document.getElementById('firstName').value;
      const lastName = document.getElementById('lastName').value;
      const email = document.getElementById('email').value;
      const phone = document.getElementById('phone').value;
      const idCardFile = document.getElementById('idCard').files[0];
      
      if (!idCardFile) {
        showToast('กรุณาแนบรูปบัตรประชาชน', 'error');
        return;
      }
      
      currentUser = {
        firstName,
        lastName,
        email,
        phone,
        idCardUploaded: true,
        verified: false
      };
      
      showToast(`ยินดีต้อนรับ ${firstName} ${lastName}! คุณสามารถเช่าชุดได้แล้ว`, 'success');
      renderHomeView();
    }

    function showBookingModal() {
      if (!selectedOutfitForBooking) return;
      
      const app = document.getElementById('app');
      app.innerHTML = `
        <div style="min-height: 100vh; padding: 32px 16px; background: linear-gradient(135deg, ${config.background_color} 0%, #FFF0F5 100%);">
          <div style="max-width: 800px; margin: 0 auto;">
            <button onclick="renderHomeView()" style="background: ${config.secondary_button_color}; color: white; padding: 10px 20px; border-radius: 8px; border: none; cursor: pointer; margin-bottom: 24px; font-weight: 600; font-size: 16px;">
              ← กลับหน้าหลัก
            </button>
            
            <div style="background: ${config.card_color}; border-radius: 20px; padding: 32px; box-shadow: 0 8px 24px rgba(0,0,0,0.15);">
              <h2 style="font-size: 28px; color: ${config.text_color}; margin-bottom: 24px; font-weight: 700;">📋 ยืนยันการเช่า</h2>
              
              <div style="background: linear-gradient(135deg, ${config.background_color} 0%, #FFF0F5 100%); border-radius: 12px; padding: 20px; margin-bottom: 24px;">
                <h3 style="font-size: 20px; color: ${config.text_color}; margin-bottom: 16px; font-weight: 600;">ข้อมูลผู้เช่า</h3>
                <p style="color: ${config.text_color}; margin-bottom: 8px; font-size: 16px;"><strong>ชื่อ:</strong> ${currentUser.firstName} ${currentUser.lastName}</p>
                <p style="color: ${config.text_color}; margin-bottom: 8px; font-size: 16px;"><strong>อีเมล:</strong> ${currentUser.email}</p>
                <p style="color: ${config.text_color}; font-size: 16px;"><strong>เบอร์โทร:</strong> ${currentUser.phone}</p>
              </div>
              
              <div style="background: linear-gradient(135deg, ${config.background_color} 0%, #FFF0F5 100%); border-radius: 12px; padding: 20px; margin-bottom: 24px;">
                <h3 style="font-size: 20px; color: ${config.text_color}; margin-bottom: 16px; font-weight: 600;">รายละเอียดชุดที่เลือก</h3>
                <div style="display: flex; gap: 20px; align-items: center;">
                  <div style="width: 120px; height: 150px; background: #f5f5f5; border-radius: 8px; overflow: hidden; flex-shrink: 0;">
                    <img src="${selectedOutfitForBooking.image}" alt="${selectedOutfitForBooking.name}" style="width: 100%; height: 100%; object-fit: contain;">
                  </div>
                  <div style="flex: 1;">
                    <p style="color: ${config.text_color}; font-weight: 600; margin-bottom: 8px; font-size: 18px;">${selectedOutfitForBooking.name}</p>
                    <p style="color: ${config.text_color}; margin-bottom: 4px; font-size: 16px;">ราคาเช่า: <span style="color: ${config.primary_button_color}; font-weight: 700;">฿${selectedOutfitForBooking.price}</span></p>
                    <p style="color: ${config.text_color}; font-size: 16px;">ค่ามัดจำ: <span style="color: ${config.secondary_button_color}; font-weight: 700;">฿${selectedOutfitForBooking.deposit}</span></p>
                  </div>
                </div>
              </div>
              
             <form id="booking-form" onsubmit="submitBooking(event)">
    <div style="margin-bottom: 20px;">
        <label style="display: block; color: ${config.text_color}; margin-bottom: 8px; font-weight: 600;">ชื่อ-นามสกุล ผู้เช่า:</label>
        <input type="text" name="customer_name" required placeholder="กรุณาระบุชื่อผู้เช่า" 
               style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px;">
    </div>

    <div style="margin-bottom: 20px;">
        <label style="display: block; color: ${config.text_color}; margin-bottom: 8px; font-weight: 600;">วันที่ต้องการเช่า:</label>
        <input type="date" name="booking_date" required 
               style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px;">
    </div>

    <div style="margin-bottom: 20px;">
        <label for="quantity" style="display: block; color: ${config.text_color}; margin-bottom: 8px; font-weight: 600;">จำนวน:</label>
        <input type="number" id="quantity" name="quantity" min="1" max="${selectedOutfitForBooking.stock}" value="1" required 
               style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px;">
        <p style="color: ${config.text_color}; opacity: 0.6; font-size: 12px; margin-top: 4px;">คงเหลือ ${selectedOutfitForBooking.stock} ชุด</p>
    </div>

    <div style="margin-bottom: 24px;">
        <label for="address" style="display: block; color: ${config.text_color}; margin-bottom: 8px; font-weight: 600;">ที่อยู่จัดส่ง:</label>
        <textarea id="address" name="address" rows="3" required placeholder="กรุณาระบุที่อยู่สำหรับจัดส่งชุด" 
                  style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px;"></textarea>
    </div>

    <div style="background: linear-gradient(135deg, #f0f0f0 0%, #e0e0e0 100%); border-radius: 12px; padding: 20px; text-align: center;">
        <p style="color: ${config.text_color}; margin-bottom: 16px; font-weight: 600;">สแกน QR Code เพื่อชำระค่ามัดจำ</p>
        <div style="width: 200px; height: 200px; background: white; margin: 0 auto; border-radius: 8px; display: flex; align-items: center; justify-content: center; overflow: hidden;">
            <img src="${config.qr_code_image}" style="width: 100%; height: 100%; object-fit: contain;" onerror="this.src='https://via.placeholder.com/200?text=QR+Not+Found'">
        </div>
        <p style="color: ${config.text_color}; margin-top: 12px; font-size: 14px; opacity: 0.7;">QR Code สำหรับชำระเงิน</p>
    </div>

    <button type="submit" id="booking-submit-btn" style="width: 100%; background: ${config.primary_button_color}; color: white; border: none; padding: 16px; border-radius: 12px; font-size: 18px; font-weight: 600; cursor: pointer; margin-top: 24px;">
        ยืนยันการเช่า
    </button>
</form>
</div>
                  </div>
                  <p style="color: ${config.text_color}; margin-top: 12px; font-size: 14px; opacity: 0.7;">QR Code สำหรับชำระเงิน</p>
                </div>
                
                <button type="submit" id="booking-submit-btn" style="width: 100%; background: ${config.primary_button_color}; color: white; padding: 14px; border-radius: 12px; border: none; cursor: pointer; font-weight: 700; font-size: 18px; box-shadow: 0 4px 12px rgba(0,0,0,0.2);">
                  ยืนยันการเช่า
                </button>
                
                <button type="button" onclick="renderHomeView()" style="width: 100%; background: transparent; color: ${config.text_color}; padding: 14px; border-radius: 12px; border: 2px solid ${config.text_color}; cursor: pointer; font-weight: 600; font-size: 16px; margin-top: 12px;">
                  ยกเลิก
                </button>
              </form>
            </div>
          </div>
        </div>
      `;
    }

    function submitBooking(event) {
      event.preventDefault();
      
      const quantity = parseInt(document.getElementById('quantity').value);
      const address = document.getElementById('address').value;
      
      const bookingData = {
        id: Date.now().toString(),
        type: 'booking',
        firstName: currentUser.firstName,
        lastName: currentUser.lastName,
        email: currentUser.email,
        phone: currentUser.phone,
        selectedOutfit: selectedOutfitForBooking.id,
        outfitName: selectedOutfitForBooking.name,
        outfitPrice: selectedOutfitForBooking.price,
        deposit: selectedOutfitForBooking.deposit,
        quantity: quantity,
        address: address,
        idCardUploaded: currentUser.idCardUploaded,
        verified: false,
        createdAt: new Date().toISOString()
      };
      
      currentBookings.push(bookingData);
      saveBookings();
      
      showToast('จองสำเร็จ! รอการยืนยันจากทางร้าน', 'success');
      selectedOutfitForBooking = null;
      renderHomeView();
    }

    function renderAdminView() {
      currentView = 'admin';
      const app = document.getElementById('app');
      
      const bookingsHtml = currentBookings.length === 0 
        ? `<div style="text-align: center; padding: 48px; color: ${config.text_color}; opacity: 0.5;">
             <p style="font-size: 20px;">ยังไม่มีรายการจอง</p>
           </div>`
        : currentBookings.map((booking, index) => `
            <div style="background: ${config.card_color}; border-radius: 12px; padding: 20px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); margin-bottom: 16px;">
              <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 16px;">
                <div style="flex: 1;">
                  <h3 style="font-size: 20px; color: ${config.text_color}; margin-bottom: 8px; font-weight: 600;">${booking.customer_name} ${booking.lastName}</h3>
                  <p style="color: ${config.text_color}; opacity: 0.7; font-size: 14px; margin-bottom: 4px;">📧 ${booking.email}</p>
                  <p style="color: ${config.text_color}; opacity: 0.7; font-size: 14px;">📱 ${booking.phone || 'ไม่ระบุ'}</p>
                </div>
                <span style="background: ${booking.status === 'confirmed' ?'#10b981' : '#f59e0b'}; color: white; padding: 6px 12px; border-radius: 6px; font-size: 14px; font-weight: 600;">
                  ${booking.status === 'confirmed' ? '✓ ยืนยันแล้ว' : '⏳ รอยืนยัน'}
                </span>
              </div>
              
              <div style="background: linear-gradient(135deg, ${config.background_color} 0%, #FFF0F5 100%); border-radius: 8px; padding: 16px; margin-bottom: 12px;">
                <p style="color: ${config.text_color}; margin-bottom: 8px; font-size: 16px;"><strong>ชุดที่เช่า:</strong> ${booking.booking_date}</p>
                <p style="color: ${config.text_color}; margin-bottom: 8px; font-size: 16px;"><strong>จำนวน:</strong> ${booking.quantity} ชุด</p>
                <p style="color: ${config.text_color}; margin-bottom: 8px; font-size: 16px;"><strong>ราคารวม:</strong> ฿${booking.outfitPrice * booking.quantity}</p>
                <p style="color: ${config.text_color}; margin-bottom: 8px; font-size: 16px;"><strong>ค่ามัดจำรวม:</strong> ฿${booking.deposit * booking.quantity}</p>
                <p style="color: ${config.text_color}; font-size: 16px;"><strong>ที่อยู่จัดส่ง:</strong> ${booking.address}</p>
              </div>
              
              <div style="display: flex; gap: 8px;">
                ${booking.status !== 'confirmed' ? `
                  <button onclick="verifyBooking(${index})" style="flex: 1; background: ${config.primary_button_color}; color: white; padding: 10px; border-radius: 8px; border: none; cursor: pointer; font-weight: 600; font-size: 14px;">
                    ✓ ยืนยัน
                  </button>
                ` : ''}
                <button onclick="if(confirm('ยืนยันการลบรายการนี้?')) { window.location.href='delete_booking.php?id=${booking.booking_id}'; }" 
        style="flex: 1; background: #ef4444; color: white; padding: 10px; border-radius: 8px; border: none; cursor: pointer;">
    🗑️ ลบ
</button>
              </div>
            </div>
          `).join('');
      
      app.innerHTML = `
        <div style="min-height: 100vh; padding: 32px 16px; background: linear-gradient(135deg, ${config.background_color} 0%, #FFF0F5 100%);">
          <div style="max-width: 1200px; margin: 0 auto;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 32px; flex-wrap: wrap; gap: 16px;">
              <h1 style="font-size: 32px; color: ${config.text_color}; font-weight: 700;">👤 จัดการสมาชิก</h1>
              <button onclick="renderHomeView()" style="background: ${config.secondary_button_color}; color: white; padding: 12px 24px; border-radius: 12px; border: none; cursor: pointer; font-weight: 600; font-size: 16px;">
                ← กลับหน้าหลัก
              </button>
            </div>
            
            <div style="background: ${config.card_color}; border-radius: 16px; padding: 24px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); margin-bottom: 24px;">
              <h2 style="font-size: 24px; color: ${config.text_color}; margin-bottom: 16px; font-weight: 600;">📊 สถิติ</h2>
              <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
                <div style="background: linear-gradient(135deg, ${config.primary_button_color} 0%, ${config.secondary_button_color} 100%); border-radius: 12px; padding: 20px; text-align: center; color: white;">
                  <p style="font-size: 32px; font-weight: 700; margin-bottom: 4px;"><?php echo $total_bookings; ?></p>
                  <p style="font-size: 14px; opacity: 0.9;">รายการทั้งหมด</p>
                </div>
                <div style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); border-radius: 12px; padding: 20px; text-align: center; color: white;">
                  <p style="font-size: 32px; font-weight: 700; margin-bottom: 4px;"><?php echo $confirmed_bookings; ?></p>
                  <p style="font-size: 14px; opacity: 0.9;">ยืนยันแล้ว</p>
                </div>
                <div style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); border-radius: 12px; padding: 20px; text-align: center; color: white;">
                  <p style="font-size: 32px; font-weight: 700; margin-bottom: 4px;"><?php echo ($total_bookings - $confirmed_bookings); ?></p>
                  <p style="font-size: 14px; opacity: 0.9;">รอยืนยัน</p>
                </div>
              </div>
            </div>
            
            <div style="background: ${config.card_color}; border-radius: 16px; padding: 24px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
              <h2 style="font-size: 24px; color: ${config.text_color}; margin-bottom: 20px; font-weight: 600;">📋 รายการจอง</h2>
              ${bookingsHtml}
            </div>
          </div>
        </div>
      `;
    }

    function verifyBooking(index) {
      currentBookings[index].verified = true;
      saveBookings();
      showToast('ยืนยันการจองเรียบร้อย', 'success');
      renderAdminView();
    }

    function deleteBooking(index) {
      currentBookings.splice(index, 1);
      saveBookings();
      showToast('ลบรายการเรียบร้อย', 'success');
      renderAdminView();
    }

    function showToast(message, type) {
      const toast = document.createElement('div');
      toast.className = `toast ${type === 'success' ? 'success-toast' : 'error-toast'}`;
      toast.textContent = message;
      document.body.appendChild(toast);
      
      setTimeout(() => {
        toast.remove();
      }, 3000);
    }

    window.filterOutfits = filterOutfits;
    window.showOutfitDetail = showOutfitDetail;
    window.selectOutfit = selectOutfit;
    window.showLoginModal = showLoginModal;
    window.handleLogin = handleLogin;
    window.showBookingModal = showBookingModal;
    window.submitBooking = submitBooking;
    window.renderAdminView = renderAdminView;
    window.renderHomeView = renderHomeView;
    window.verifyBooking = verifyBooking;
    window.deleteBooking = deleteBooking;

    renderHomeView();
  </script>
 <script>(function(){function c(){var b=a.contentDocument||a.contentWindow.document;if(b){var d=b.createElement('script');d.innerHTML="window.__CF$cv$params={r:'9be1d46b61924f52',t:'MTc2ODQ0Mjk1My4wMDAwMDA='};var a=document.createElement('script');a.nonce='';a.src='/cdn-cgi/challenge-platform/scripts/jsd/main.js';document.getElementsByTagName('head')[0].appendChild(a);";b.getElementsByTagName('head')[0].appendChild(d)}}if(document.body){var a=document.createElement('iframe');a.height=1;a.width=1;a.style.position='absolute';a.style.top=0;a.style.left=0;a.style.border='none';a.style.visibility='hidden';document.body.appendChild(a);if('loading'!==document.readyState)c();else if(window.addEventListener)document.addEventListener('DOMContentLoaded',c);else{var e=document.onreadystatechange||function(){};document.onreadystatechange=function(b){e(b);'loading'!==document.readyState&&(document.onreadystatechange=e,c())}}}})();</script></body>
</html>