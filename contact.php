<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ติดต่อเรา — Waffle Hunger</title>
  
  <!-- Google Fonts & Style (ดึงจากโฟลเดอร์ assets) -->
  <link rel="stylesheet" href="assets/style.css">
  
  <!-- FontAwesome Icon -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

  <!-- Navigation Bar -->
  <!-- Navigation Bar (แก้ไขให้ตรงกับหน้าอื่น) -->
<nav class="navbar">
  <div class="nav-inner">
    <!-- โลโก้ใช้รูปจริงเหมือนหน้าอื่น -->
    <a href="index.php" class="nav-logo">
      <img src="logo.png" alt="Waffle Hunger Logo" style="height: 42px; border-radius: 50%;">
      <div class="logo-text">WAFFLE HUNGER <span>MINIMAL CAFÉ</span></div>
    </a>
    
    <!-- เมนูหลัก -->
    <div class="nav-links">
      <a href="index.php">หน้าแรก</a>
      <a href="menu.php">เมนู</a>
      <a href="promotion.php">โปรโมชั่น</a>
      <a href="about.php">เกี่ยวกับเรา</a>
      <a href="contact.php" class="active">ติดต่อเรา</a>
    </div>

    <!-- ปุ่มตระกร้าและเข้าสู่ระบบที่ขาดหายไป -->
    <div class="nav-actions">
      <button class="nav-icon-btn" onclick="toggleCart()">
        <i class="fa-solid fa-cart-shopping"></i>
        <span class="cart-count">0</span>
      </button>
      <a href="login.php" class="btn btn-gold btn-sm">
        <i class="fa-solid fa-right-to-bracket"></i> เข้าสู่ระบบ
      </a>
    </div>
  </div>
</nav>

  <!-- Content Section -->
  <main class="section">
    <div class="container">
      <div class="contact-wrap">
        
        <!-- ฝั่งซ้าย: ข้อมูลการติดต่อ -->
        <div class="contact-info-card">
          <h3>ข้อมูลติดต่อ</h3>
          
          <div class="contact-row">
            <div class="contact-icon-box">
              <i class="fa-solid fa-location-dot"></i>
            </div>
            <div>
              <h5>ที่อยู่</h5>
              <p>123 ถนนวาฟเฟิล แขวงหอมหวาน<br>เขตอร่อย กรุงเทพฯ 10110</p>
            </div>
          </div>

          <div class="contact-row">
            <div class="contact-icon-box">
              <i class="fa-solid fa-phone"></i>
            </div>
            <div>
              <h5>โทรศัพท์</h5>
              <p>099-123-4567</p>
            </div>
          </div>

          <div class="contact-row">
            <div class="contact-icon-box">
              <i class="fa-solid fa-envelope"></i>
            </div>
            <div>
              <h5>อีเมล</h5>
              <p>wafflehunger@gmail.com</p>
            </div>
          </div>

          <div class="contact-row">
            <div class="contact-icon-box">
              <i class="fa-regular fa-clock"></i>
            </div>
            <div>
              <h5>เวลาทำการ</h5>
              <p>ทุกวัน 09:00 - 21:00 น.</p>
            </div>
          </div>

          <!-- แผนที่ OpenStreetMap -->
          <div class="map-frame">
            <iframe 
              src="https://www.openstreetmap.org/export/embed.html?bbox=100.48%2C13.73%2C100.55%2C13.77&layer=mapnik" 
              loading="lazy">
            </iframe>
          </div>
        </div>

        <!-- ฝั่งขวา: ฟอร์มส่งข้อความ -->
        <div class="form-card">
          <h2 style="margin-bottom: 20px; font-size: 24px; color: var(--text);">ส่งข้อความถึงเรา</h2>
          <form action="#" method="POST">
            <div class="form-row">
              <div class="form-group">
                <label>ชื่อ-นามสกุล</label>
                <input type="text" placeholder="กรอกชื่อของคุณ" required>
              </div>
              <div class="form-group">
                <label>เบอร์โทรศัพท์</label>
                <input type="tel" placeholder="08X-XXX-XXXX">
              </div>
            </div>
            
            <div class="form-group">
              <label>อีเมล</label>
              <input type="email" placeholder="yourname@example.com" required>
            </div>

            <div class="form-group">
              <label>หัวข้อติดต่อ</label>
              <input type="text" placeholder="เช่น สอบถามเรื่องจัดเลี้ยง / ข้อเสนอแนะ">
            </div>

            <div class="form-group">
              <label>ข้อความ</label>
              <textarea rows="4" placeholder="พิมพ์ข้อความของคุณที่นี่..." required></textarea>
            </div>

            <button type="submit" class="btn btn-gold btn-block">
              <i class="fa-solid fa-paper-plane"></i> ส่งข้อความ
            </button>
          </form>
        </div>

      </div>
    </div>
  </main>

  <!-- Footer -->
  <footer class="footer">
    <div class="footer-bottom">
      <p>&copy; 2026 Waffle Hunger. All rights reserved.</p>
    </div>
  </footer>

</body>
</html>