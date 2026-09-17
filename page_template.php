<?php
/**
 * page_template.php
 * ----------------------------------------------------
 * ฟังก์ชันส่วนหัว (Navigation) และส่วนท้าย (Footer)
 * ธีม Warm Minimal Cafe (โทนน้ำตาลอบอุ่น มินิมอล)
 * ----------------------------------------------------
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function is_admin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function require_admin() {
    if (!is_logged_in() || !is_admin()) {
        header('Location: login.php');
        exit();
    }
}

function require_login() {
    if (!is_logged_in()) {
        header('Location: login.php');
        exit();
    }
}

function render_header($active = '') {
    $navItems = [
        'index'     => ['index.php', 'หน้าแรก'],
        'menu'      => ['menu.php', 'เมนู'],
        'promotion' => ['promotion.php', 'โปรโมชั่น'],
        'about'     => ['about.php', 'เกี่ยวกับเรา'],
        'contact'   => ['contact.php', 'ติดต่อเรา'],
    ];
    ?>
    <!DOCTYPE html>
    <html lang="th">
    <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($GLOBALS['page_title']) ? htmlspecialchars($GLOBALS['page_title']) . ' - ' : '' ?><?= SITE_NAME ?? 'Waffle Hunger' ?></title>
    
    <!-- Font Awesome 6 CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/style.css">
    <style>
      /* --- GLOBAL WARM MINIMAL THEME OVERRIDES --- */
      :root {
        --bg-main: #f9f6f0;           /* สีครีมมินิมอลอ่อน */
        --bg-card: #ffffff;           /* การ์ดสีขาวสะอาด */
        --bg-dark: #2b2118;           /* น้ำตาลเอสเพรสโซ่เข้ม (ใช้แทนสีดำ) */
        --bg-dark-card: #3a2e2b;      /* น้ำตาลการ์ด */
        --primary-gold: #c8934a;      /* สีทองอบอุ่น/คาราเมล */
        --primary-gold-hover: #b07d37;
        --text-dark: #332822;         /* ข้อความสีน้ำตาลเข้มอ่านง่าย */
        --text-muted: #7a6e65;        /* ข้อความรอง */
        --border-color: #e8e2d8;
      }

      body {
        background-color: var(--bg-main) !important;
        color: var(--text-dark) !important;
      }

      /* Fix สีข้อความในหน้า About ที่มองไม่เห็น */
      .about-content, .about-text, h2, h3, p {
        color: var(--text-dark);
      }

      /* --- NAVBAR WARM MINIMAL STYLE --- */
      .navbar {
        width: 100%;
        background-color: var(--bg-dark);
        border-bottom: 1px solid rgba(200, 147, 74, 0.2);
        position: sticky;
        top: 0;
        z-index: 1000;
        box-shadow: 0 4px 15px rgba(43, 33, 24, 0.15);
      }

      .nav-inner {
        max-width: 1200px;
        margin: 0 auto;
        padding: 12px 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
      }

      /* โลโก้ + ชื่อร้าน */
      .nav-logo {
        display: flex;
        align-items: center;
        gap: 12px;
        text-decoration: none;
      }

      .nav-logo-img {
        width: 46px;
        height: 46px;
        border-radius: 50%;
        object-fit: cover;
        background-color: #ffffff;
        padding: 2px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        transition: transform 0.3s ease;
      }

      .nav-logo-text {
        display: flex;
        flex-direction: column;
      }

      .nav-logo-title {
        color: #e2be8a;
        font-weight: 800;
        font-size: 1.15rem;
        letter-spacing: 0.5px;
        line-height: 1;
        text-transform: uppercase;
      }

      .nav-logo-subtitle {
        color: #a89a8e;
        font-size: 0.65rem;
        letter-spacing: 1.2px;
        text-transform: uppercase;
        margin-top: 3px;
      }

      .nav-logo:hover .nav-logo-img {
        transform: scale(1.05);
      }

      /* เมนูตรงกลาง */
      .nav-links {
        display: flex;
        align-items: center;
        gap: 6px;
        list-style: none;
        margin: 0;
        padding: 0;
      }

      .nav-links li a {
        color: #e0d8d0;
        text-decoration: none;
        padding: 8px 18px;
        border-radius: 20px;
        font-size: 0.95rem;
        font-weight: 500;
        transition: all 0.3s ease;
      }

      .nav-links li a:hover {
        color: #e2be8a;
        background: rgba(226, 190, 138, 0.12);
      }

      .nav-links li a.active {
        color: #2b2118;
        background: #e2be8a;
        font-weight: 600;
      }

      /* ฝั่งขวา (ตะกร้า/ล็อกอิน) */
      .nav-actions {
        display: flex;
        align-items: center;
        gap: 12px;
      }

      .nav-icon-btn {
        position: relative;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.12);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 1rem;
        transition: all 0.3s ease;
      }

      .nav-icon-btn:hover {
        background: rgba(226, 190, 138, 0.2);
        color: #e2be8a;
        border-color: #e2be8a;
      }

      .cart-count {
        position: absolute;
        top: -3px;
        right: -3px;
        background: #e2be8a;
        color: #2b2118;
        font-size: 0.7rem;
        font-weight: bold;
        width: 18px;
        height: 18px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
      }

      .btn-gold {
        background: #e2be8a;
        color: #2b2118 !important;
        font-weight: 600;
        padding: 8px 18px;
        border-radius: 20px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 0.88rem;
        border: none;
        transition: all 0.3s ease;
      }

      .btn-gold:hover {
        background: #d4a96a;
        transform: translateY(-1px);
      }

      /* --- FOOTER WARM MINIMAL STYLE --- */
      .footer {
        background-color: var(--bg-dark) !important;
        color: #d0c4b8 !important;
        padding: 50px 0 20px;
        margin-top: 60px;
        border-top: 1px solid rgba(200, 147, 74, 0.15);
      }

      .footer h4 {
        color: #e2be8a !important;
      }

      .footer a {
        color: #b8aba0 !important;
      }

      .footer a:hover {
        color: #e2be8a !important;
      }

      .footer-logo-img {
        width: 55px;
        height: 55px;
        border-radius: 50%;
        object-fit: cover;
        background-color: #ffffff;
        padding: 2px;
      }

      .footer-social a {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: rgba(255,255,255,0.08);
        color: #e2be8a !important;
        transition: all 0.3s ease;
      }

      .footer-social a:hover {
        background: #e2be8a;
        color: #2b2118 !important;
      }

      .footer-bottom {
        border-top: 1px solid rgba(255, 255, 255, 0.08) !important;
        color: #887b70 !important;
      }
    </style>
    </head>
    <body>

    <nav class="navbar">
      <div class="nav-inner">
        <!-- โลโก้ + ชื่อร้านค้า -->
        <a href="index.php" class="nav-logo">
          <img src="logo.png" alt="Waffle Hunger Logo" class="nav-logo-img">
          <div class="nav-logo-text">
            <span class="nav-logo-title">Waffle Hunger</span>
            <span class="nav-logo-subtitle">Minimal Café</span>
          </div>
        </a>

        <!-- เมนูหลัก -->
        <ul class="nav-links" id="navLinks">
          <?php foreach ($navItems as $key => $item): ?>
            <li><a href="<?= $item[0] ?>" class="<?= $active === $key ? 'active' : '' ?>"><?= $item[1] ?></a></li>
          <?php endforeach; ?>
        </ul>

        <!-- ปุ่มระบบ + ตะกร้า -->
        <div class="nav-actions">
          <button class="nav-icon-btn" data-cart-open title="ตะกร้าสินค้า">
            <i class="fa-solid fa-cart-shopping"></i><span class="cart-count">0</span>
          </button>

          <?php if (is_logged_in()): ?>
            <div class="nav-user" style="display:flex; align-items:center; gap:8px; color:#fff; font-size:0.88rem;">
              <span><i class="fa-regular fa-user"></i> <?= htmlspecialchars($_SESSION['user_name'] ?? 'ผู้ใช้') ?></span>
              <?php if (is_admin()): ?>
                <a href="dashboard.php" class="btn-gold" style="padding:6px 12px; font-size:0.8rem;"><i class="fa-solid fa-gauge"></i> หลังบ้าน</a>
              <?php endif; ?>
              <a href="logout.php" class="nav-icon-btn" title="ออกจากระบบ"><i class="fa-solid fa-arrow-right-from-bracket"></i></a>
            </div>
          <?php else: ?>
            <a href="login.php" class="btn-gold"><i class="fa-solid fa-right-to-bracket"></i> เข้าสู่ระบบ</a>
          <?php endif; ?>
        </div>
      </div>
    </nav>

    <!-- Cart Drawer -->
    <div class="cart-overlay" id="cartOverlay"></div>
    <div class="cart-drawer" id="cartDrawer">
      <div class="cart-head">
        <h3><i class="fa-solid fa-cart-shopping"></i> ตะกร้าของคุณ</h3>
        <button id="cartClose"><i class="fa-solid fa-xmark"></i></button>
      </div>
      <div class="cart-items" id="cartItems"></div>
      <div class="cart-foot">
        <div class="cart-total"><span>ยอดรวม</span><span id="cartTotal">฿0</span></div>
        <button class="btn btn-gold btn-block" id="checkoutBtn"><i class="fa-solid fa-credit-card"></i> ยืนยันสั่งซื้อ</button>
      </div>
    </div>
    <?php
}

function render_footer() {
    ?>
    <footer class="footer">
      <div class="container">
        <div class="footer-grid">
          <div>
            <a href="index.php" style="display:flex; align-items:center; gap:12px; text-decoration:none; margin-bottom:12px;">
              <img src="logo.png" alt="Waffle Hunger Logo" class="footer-logo-img">
              <div>
                <span style="color:#e2be8a; font-weight:800; font-size:1.1rem; display:block;">WAFFLE HUNGER</span>
                <span style="color:#a89a8e; font-size:0.68rem; letter-spacing:1px;">MINIMAL CAFÉ</span>
              </div>
            </a>
            <p style="font-size:14px; max-width:260px; line-height:1.6; color:#a89a8e;">วาฟเฟิลที่ทำให้คุณ... หิวอีกครั้ง<br>อบสดใหม่ หอม นุ่ม นวล ในสไตล์มินิมอลคาเฟ่</p>
            <div class="footer-social" style="display:flex; gap:8px; margin-top:16px;">
              <a href="#" title="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
              <a href="#" title="Instagram"><i class="fa-brands fa-instagram"></i></a>
              <a href="#" title="TikTok"><i class="fa-brands fa-tiktok"></i></a>
              <a href="#" title="LINE"><i class="fa-solid fa-comment"></i></a>
            </div>
          </div>
          <div class="footer-col">
            <h4>เมนู</h4>
            <ul style="list-style:none; padding:0;">
              <li><a href="menu.php">เมนูทั้งหมด</a></li>
              <li><a href="menu.php#dessert">ขนมหวาน</a></li>
              <li><a href="menu.php#drink">เครื่องดื่ม</a></li>
            </ul>
          </div>
          <div class="footer-col">
            <h4>ข้อมูล</h4>
            <ul style="list-style:none; padding:0;">
              <li><a href="promotion.php">โปรโมชั่น</a></li>
              <li><a href="about.php">เกี่ยวกับเรา</a></li>
              <li><a href="contact.php">ติดต่อเรา</a></li>
            </ul>
          </div>
          <div class="footer-col">
            <h4>บริการลูกค้า</h4>
            <ul style="list-style:none; padding:0;">
              <li><a href="contact.php">คำถามที่พบบ่อย</a></li>
              <li><a href="contact.php">การจัดส่งและรับสินค้า</a></li>
            </ul>
          </div>
          <div class="footer-col">
            <h4>ข่าวสาร</h4>
            <p style="font-size:13px; color:#a89a8e;">รับโปรโมชั่นและเมนูใหม่ก่อนใคร</p>
            <form class="footer-newsletter" onsubmit="event.preventDefault(); alert('ขอบคุณที่ติดตามเรา!');">
              <input type="email" placeholder="อีเมลของคุณ" style="background:#3a2e2b; border:1px solid #4a3e3b; color:#fff; padding:8px 12px; border-radius:15px; width:100%;" required>
            </form>
          </div>
        </div>
        <div class="footer-bottom" style="text-align:center; padding-top:20px; margin-top:30px; font-size:0.85rem;">
          &copy; <?= date('Y') ?> Waffle Hunger Café. All Rights Reserved.
        </div>
      </div>
    </footer>

    <script src="assets/app.js"></script>
    </body>
    </html>
    <?php
}