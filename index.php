<?php
require_once __DIR__ . '/connect.php';
require_once __DIR__ . '/page_template.php';

$page_title = 'หน้าแรก';

// ตรวจสอบสถานะการเข้าสู่ระบบของผู้ใช้
$is_logged_in = isset($_SESSION['user_id']) || isset($_SESSION['user']);

$featured = $pdo->query("SELECT p.*, c.slug AS cat_slug FROM products p JOIN categories c ON c.id = p.category_id WHERE p.is_featured = 1 LIMIT 4")->fetchAll();
if (count($featured) < 4) {
    $featured = $pdo->query("SELECT p.*, c.slug AS cat_slug FROM products p JOIN categories c ON c.id = p.category_id ORDER BY p.sold_count DESC LIMIT 4")->fetchAll();
}
$promos = $pdo->query("SELECT * FROM promotions WHERE active = 1 LIMIT 3")->fetchAll();

render_header('index');
?>

<style>
  /* Layout Hero Grid 50:50 ได้สัดส่วนพอดี */
  .hero-slide {
    display: none;
    align-items: center;
    justify-content: center;
    padding: 60px 0;
    min-height: 520px;
  }
  .hero-slide.active {
    display: flex;
  }
  
  .hero-container {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 40px;
    align-items: center;
    width: 100%;
    max-width: 1100px;
    margin: 0 auto;
    padding: 0 20px;
    position: relative;
    z-index: 2;
  }

  .hero-img-wrapper {
    position: relative;
    width: 100%;
    height: 380px;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 20px 40px rgba(0,0,0,0.5);
    border: 1px solid rgba(255,255,255,0.1);
  }
  
  .hero-img-wrapper img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center;
    transition: transform 0.5s ease;
  }

  .hero-img-wrapper:hover img {
    transform: scale(1.05);
  }

  .hero-content {
    text-align: left;
  }

  /* Style สำหรับ Card สินค้า */
  .product-img-box {
    width: 100%;
    height: 180px;
    background-color: rgba(0, 0, 0, 0.05);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    margin-bottom: 12px;
  }

  .product-img-box img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }

  .product-img-box .placeholder-icon {
    font-size: 3rem;
    color: var(--gold, #d4af37);
  }

  /* --- CUSTOM MODAL POPUP --- */
  .custom-modal-backdrop {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.75);
    backdrop-filter: blur(4px);
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    visibility: hidden;
    transition: opacity 0.3s ease, visibility 0.3s ease;
  }

  .custom-modal-backdrop.show {
    opacity: 1;
    visibility: visible;
  }

  .custom-modal {
    background: #1a1a1a;
    border: 1px solid rgba(212, 175, 55, 0.3);
    border-radius: 16px;
    padding: 30px 25px;
    width: 90%;
    max-width: 400px;
    text-align: center;
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.6);
    transform: scale(0.8);
    transition: transform 0.3s ease;
    color: #fff;
  }

  .custom-modal-backdrop.show .custom-modal {
    transform: scale(1);
  }

  .custom-modal-icon {
    width: 60px;
    height: 60px;
    background: rgba(212, 175, 55, 0.15);
    color: #d4af37;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.8rem;
    margin: 0 auto 15px auto;
  }

  .custom-modal h3 {
    margin: 0 0 10px 0;
    font-size: 1.4rem;
    color: #d4af37;
  }

  .custom-modal p {
    color: #ccc;
    font-size: 0.95rem;
    margin-bottom: 25px;
    line-height: 1.5;
  }

  .custom-modal-actions {
    display: flex;
    gap: 10px;
    justify-content: center;
  }

  .custom-modal-actions .btn {
    flex: 1;
    padding: 10px 0;
    font-size: 0.95rem;
    cursor: pointer;
  }

  @media (max-width: 768px) {
    .hero-container {
      grid-template-columns: 1fr;
      text-align: center;
    }
    .hero-content {
      text-align: center;
    }
    .hero-img-wrapper {
      height: 280px;
    }
  }
</style>

<!-- HERO -->
<section class="hero">
  <div class="hero-slide active">
    <div class="hero-bg"></div>
    <div class="hero-container">
      <div class="hero-img-wrapper">
        <img src="hero-waffle.jpg" alt="Waffle Hunger Premium">
      </div>
      <div class="hero-content">
        <span class="hero-eyebrow"><i class="fa-solid fa-sparkles"></i> Premium Waffle Café</span>
        <h1>WAFFLE<br><span>HUNGER</span></h1>
        <p><?= htmlspecialchars(SITE_TAGLINE) ?><br>สัมผัสความอร่อยของวาฟเฟิลกรอบนอก นุ่มใน จากวัตถุดิบพรีเมียม คัดสรรอย่างดี</p>
        <div class="hero-actions">
          <a href="menu.php" class="btn btn-gold">ดูเมนูทั้งหมด</a>
          <a href="javascript:void(0)" onclick="handleOrderClick()" class="btn btn-outline" style="color:#fff;border-color:rgba(255,255,255,.5)">สั่งเลย</a>
        </div>
      </div>
    </div>
  </div>

  <div class="hero-slide">
    <div class="hero-bg"></div>
    <div class="hero-container">
      <div class="hero-img-wrapper">
        <img src="strawberry-waffle.jpg" alt="สตรอว์เบอร์รี วาฟเฟิล">
      </div>
      <div class="hero-content">
        <span class="hero-eyebrow"><i class="fa-solid fa-fire"></i> เมนูขายดีอันดับ 1</span>
        <h1>สตรอว์เบอร์รี<br><span>วาฟเฟิล</span></h1>
        <p>ท็อปด้วยสตรอว์เบอร์รีสดและซอสสูตรพิเศษ หวานฉ่ำในทุกคำ</p>
        <div class="hero-actions">
          <a href="javascript:void(0)" onclick="handleOrderClick()" class="btn btn-gold">สั่งเมนูนี้</a>
        </div>
      </div>
    </div>
  </div>

  <div class="hero-slide">
    <div class="hero-bg"></div>
    <div class="hero-container">
      <div class="hero-img-wrapper">
        <img src="matcha-waffle.jpg" alt="มัทฉะ วาฟเฟิล โปรโมชั่น">
      </div>
      <div class="hero-content">
        <span class="hero-eyebrow"><i class="fa-solid fa-gift"></i> โปรโมชั่นวันนี้</span>
        <h1>ลด 10%<br><span>ทุกเมนู</span></h1>
        <p>เมื่อสั่งครบ 300 บาท พร้อมส่งฟรีเมื่อสั่งครบ 500 บาท</p>
        <div class="hero-actions">
          <a href="promotion.php" class="btn btn-gold">ดูโปรโมชั่น</a>
        </div>
      </div>
    </div>
  </div>

  <div class="hero-dots">
    <button class="active"></button><button></button><button></button>
  </div>
</section>

<!-- FEATURES BAR -->
<div class="features-bar">
  <div class="container">
    <div class="features-grid">
      <div class="feature-item"><div class="icon"><i class="fa-solid fa-award"></i></div><div><h4>วัตถุดิบพรีเมียม</h4><p>คัดสรรอย่างดี</p></div></div>
      <div class="feature-item"><div class="icon"><i class="fa-solid fa-fire-burner"></i></div><div><h4>อบสดใหม่ทุกออเดอร์</h4><p>หอม กรอบ อร่อย</p></div></div>
      <div class="feature-item"><div class="icon"><i class="fa-solid fa-shield-heart"></i></div><div><h4>ไม่ใส่สารกันเสีย</h4><p>ปลอดภัย ต่อสุขภาพ</p></div></div>
      <div class="feature-item"><div class="icon"><i class="fa-solid fa-truck-fast"></i></div><div><h4>จัดส่งรวดเร็ว</h4><p>ภายใน 1-2 วัน</p></div></div>
    </div>
  </div>
</div>

<!-- FEATURED MENU -->
<section class="section section-cream">
  <div class="container">
    <div class="section-head">
      <div>
        <span class="tag" style="background: #cf9a3f; color: #171310; font-weight: bold; padding: 4px 12px; border-radius: 20px;">Recommended</span>
        <h2 style="color: #171310 !important;">เมนูแนะนำ</h2>
      </div>
      <a href="menu.php" class="link-more" style="color: #a9772a !important; font-weight: bold;">ดูทั้งหมด <i class="fa-solid fa-arrow-right"></i></a>
    </div>
    <div class="grid grid-4">
      <?php foreach ($featured as $p): ?>
        <div class="product-card" style="background: #ffffff; border: 1px solid rgba(0,0,0,0.08); box-shadow: 0 4px 15px rgba(0,0,0,0.05); border-radius: 16px; padding: 15px;">
          <a href="menu.php?id=<?= $p['id'] ?>">
            <div class="product-img-box">
              <?php if (!empty($p['image'])): ?>
                <img src="<?= htmlspecialchars($p['image']) ?>" alt="<?= htmlspecialchars($p['name']) ?>">
              <?php else: ?>
                <i class="fa-solid fa-utensils placeholder-icon"></i>
              <?php endif; ?>
            </div>
          </a>
          <div class="product-body">
            <div class="product-cat" style="color: #8c7662 !important; font-size: 0.85rem; font-weight: bold;"><?= $p['cat_slug'] === 'dessert' ? 'ขนมหวาน' : 'เครื่องดื่ม' ?></div>
            <h3 style="color: #171310 !important; font-size: 1.1rem; margin: 6px 0;"><?= htmlspecialchars($p['name']) ?></h3>
            <div class="product-foot" style="display: flex; justify-content: space-between; align-items: center; margin-top: 10px;">
              <div class="price" style="color: #a9772a !important; font-weight: bold; font-size: 1.1rem;"><?= number_format($p['price']) ?> <span style="font-size: 0.85rem; color: #666;">บาท</span></div>
              <button class="add-btn" onclick="addToCartCheck(event, <?= $p['id'] ?>, '<?= htmlspecialchars($p['name'], ENT_QUOTES) ?>', <?= $p['price'] ?>)" style="background: #171310; color: #e6c374; border: none; width: 36px; height: 36px; border-radius: 50%; cursor: pointer;"><i class="fa-solid fa-plus"></i></button>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- PROMOTIONS STRIP -->
<section class="section section-dark">
  <div class="container">
    <div class="section-head">
      <div>
        <span class="tag" style="color: #e6c374 !important; font-weight: bold;">Don't miss out</span>
        <h2 style="color: #ffffff !important;">โปรโมชั่นพิเศษ</h2>
      </div>
      <a href="promotion.php" class="link-more" style="color: #e6c374 !important; font-weight: bold;">ดูทั้งหมด <i class="fa-solid fa-arrow-right"></i></a>
    </div>
    <div class="grid grid-3">
      <?php foreach ($promos as $promo): ?>
        <div class="promo-card" style="background: #231c17; border: 1px solid rgba(212, 175, 55, 0.25); border-radius: 16px; padding: 25px; text-align: center;">
          <span class="tag" style="background: #cf9a3f; color: #171310; font-weight: bold; padding: 4px 12px; border-radius: 20px; display: inline-block; margin-bottom: 12px;"><?= htmlspecialchars($promo['tag']) ?></span>
          <div class="icon" style="font-size: 2rem; color: #e6c374; margin-bottom: 10px;"><i class="<?= htmlspecialchars($promo['icon']) ?>"></i></div>
          <h3 style="color: #ffffff !important; margin-bottom: 8px; font-size: 1.25rem;"><?= htmlspecialchars($promo['title']) ?></h3>
          <p style="color: #d9cfc1 !important; font-size: 0.9rem; margin-bottom: 20px;"><?= htmlspecialchars($promo['description']) ?></p>
          <a href="promotion.php" class="btn btn-gold btn-sm" style="background: #d4af37; color: #171310; font-weight: bold; text-decoration: none; padding: 8px 18px; border-radius: 20px; display: inline-block;">ดูรายละเอียด</a>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- CTA -->
<section class="section section-cream">
  <div class="container">
    <div class="cta" style="background: #171310; border: 1px solid rgba(212, 175, 55, 0.25); padding: 40px 20px; border-radius: 20px; text-align: center;">
      <h2 style="color: #ffffff !important; font-size: 2rem; margin-bottom: 10px;">หิววาฟเฟิลแล้วใช่ไหม?</h2>
      <p style="color: #d9cfc1 !important; margin-bottom: 25px; font-size: 1rem;">สั่งเลยวันนี้ รับความอร่อยถึงมือคุณภายใน 1-2 วัน</p>
      <div class="hero-actions" style="display: flex; justify-content: center;">
        <a href="javascript:void(0)" onclick="handleOrderClick()" class="btn btn-gold" style="background: #d4af37; color: #171310; font-weight: bold; text-decoration: none; padding: 12px 28px; border-radius: 25px; display: inline-flex; align-items: center; gap: 8px;"><i class="fa-solid fa-utensils"></i> สั่งเมนูตอนนี้</a>
      </div>
    </div>
  </div>
</section>

<!-- CUSTOM POPUP MODAL -->
<div id="loginAlertModal" class="custom-modal-backdrop">
  <div class="custom-modal">
    <div class="custom-modal-icon">
      <i class="fa-solid fa-user-lock"></i>
    </div>
    <h3>กรุณาเข้าสู่ระบบ</h3>
    <p id="modalMessage">คุณจำเป็นต้องเข้าสู่ระบบก่อนทำการสั่งซื้อสินค้าหรือเพิ่มสินค้าลงตะกร้า</p>
    <div class="custom-modal-actions">
      <button class="btn btn-outline" style="color:#fff; border-color:rgba(255,255,255,0.3);" onclick="closeLoginModal()">ยกเลิก</button>
      <a href="login.php" class="btn btn-gold">เข้าสู่ระบบ</a>
    </div>
  </div>
</div>

<!-- Script ตรวจสอบการเข้าสู่ระบบและควบคุม Pop-up -->
<script>
  const isLoggedIn = <?= json_encode($is_logged_in) ?>;

  function showLoginModal(message) {
    if(message) {
      document.getElementById('modalMessage').innerText = message;
    }
    document.getElementById('loginAlertModal').classList.add('show');
  }

  function closeLoginModal() {
    document.getElementById('loginAlertModal').classList.remove('show');
  }

  function handleOrderClick() {
    if (!isLoggedIn) {
      showLoginModal('กรุณาเข้าสู่ระบบก่อนเลือกสั่งซื้อสินค้า');
    } else {
      window.location.href = 'menu.php';
    }
  }

  function addToCartCheck(event, id, name, price) {
    event.preventDefault();
    if (!isLoggedIn) {
      showLoginModal('กรุณาเข้าสู่ระบบก่อนทำการเพิ่ม ' + name + ' ลงในตะกร้า');
    } else {
      if (typeof addToCart === 'function') {
        addToCart(id, name, price);
      } else {
        alert('เพิ่ม ' + name + ' ลงในตะกร้าเรียบร้อยแล้ว');
      }
    }
  }
</script>

<?php render_footer(); ?>