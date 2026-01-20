const config = {
    shop_name: 'YIM Shop', 
    qr_code_image: 'myqr.jpg' // อย่าลืมใส่ไฟล์รูปชื่อนี้ในโฟลเดอร์นะคะ
};

function renderHome() {
    const app = document.getElementById('app');
    
    // สร้างเนื้อหา HTML ผ่าน JS
    app.innerHTML = `
        <h1 class="text-4xl font-bold text-pink-600 mb-6">${config.shop_name}</h1>
        
        <div class="qr-container">
            <img src="${config.qr_code_image}" alt="QR Payment" class="w-64 h-64 object-cover">
        </div>

        <div class="bg-white rounded-xl p-4 shadow-sm border border-pink-100">
            <p class="text-gray-600 font-medium">สแกนเพื่อจ่ายมัดจำ</p>
            <p class="text-sm text-pink-400 mt-2">รายการจองล่าสุด: ${currentBookings.length} รายการ</p>
        </div>

        <button onclick="location.reload()" class="btn-refresh">
            รีเฟรชข้อมูล
        </button>
    `;
}

// สั่งให้ทำงานเมื่อโหลดหน้าเว็บเสร็จ
document.addEventListener('DOMContentLoaded', renderHome);