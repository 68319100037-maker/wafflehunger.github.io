<?php
require_once __DIR__ . '/connect.php';
require_once __DIR__ . '/page_template.php';

$page_title = 'โปรโมชั่นพิเศษ';
$promos = $pdo->query("SELECT * FROM promotions WHERE active = 1 ORDER BY id DESC")->fetchAll();

render_header('promotion');
?>

<style>
  .page-header {
    text-align: center !important;
  }
  .promo-banner-box {
    width: 100%;
    height: 200px;
    border-radius: 12px;
    overflow: hidden;
    margin-bottom: 15px;
    position: relative;
    border: 1px solid rgba(0, 0, 0, 0.08);
  }
  .promo-banner-box img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }
  .promo-banner-placeholder {
    width: 100%;
    height: 100%;
    background: #2a241e;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #e6c374;
    font-size: 3.5rem;
  }
</style>

<!-- ส่วนหัวข้อหลัก จัดกึ่งกลาง -->
<div class="page-header" style="text-align: center;">
  <div class="container">
    <h1><i class="fa-solid fa-tags"></i> โปรโมชั่นประจำเดือน</h1>
    <p>สิทธิพิเศษและส่วนลดสุดคุ้มสำหรับคนรักวาฟเฟิล</p>
  </div>
</div>

<!-- เปลี่ยนจาก section-dark เป็น section-cream ให้กลมกลืนเป็นสีครีมทั้งหน้า -->
<section class="section section-cream">
  <div class="container">
    <div class="grid grid-3">
      <?php if (count($promos) > 0): ?>
        <?php foreach ($promos as $promo): ?>
          <div class="promo-card" style="background: #171310; padding: 25px; border-radius: 16px; border: 1px solid rgba(212, 175, 55, 0.25); text-align: center; box-shadow: 0 10px 25px rgba(0,0,0,0.08);">
            <div class="promo-banner-box">
              <?php if (!empty($promo['image']) && file_exists($promo['image'])): ?>
                <img src="<?= htmlspecialchars($promo['image']) ?>" alt="<?= htmlspecialchars($promo['title']) ?>">
              <?php else: ?>
                <div class="promo-banner-placeholder">
                  <i class="<?= !empty($promo['icon']) ? htmlspecialchars($promo['icon']) : 'fa-solid fa-gift' ?>"></i>
                </div>
              <?php endif; ?>
            </div>
            <span class="tag" style="background: #cf9a3f; color: #171310; padding: 4px 14px; border-radius: 20px; font-size: 0.85rem; font-weight: bold; display: inline-block; margin-bottom: 10px;">
              <i class="fa-solid fa-star"></i> <?= htmlspecialchars($promo['tag'] ?? 'Special Offer') ?>
            </span>
            <h3 style="margin: 10px 0; color: #ffffff !important; font-size: 1.3rem;"><?= htmlspecialchars($promo['title']) ?></h3>
            <p style="color: #d9cfc1 !important; font-size: 0.9rem; line-height: 1.6; margin-bottom: 20px;"><?= htmlspecialchars($promo['description']) ?></p>
            <a href="menu.php" class="btn btn-gold btn-sm" style="width: 100%; text-align: center; display: inline-block; padding: 10px 0;">
              <i class="fa-solid fa-basket-shopping"></i> ใช้สิทธิ์สั่งเลย
            </a>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="promo-card" style="background: #171310; padding: 25px; border-radius: 16px; border: 1px solid rgba(212, 175, 55, 0.25); text-align: center; box-shadow: 0 10px 25px rgba(0,0,0,0.08);">
          <div class="promo-banner-box">
            <div class="promo-banner-placeholder"><i class="fa-solid fa-percent"></i></div>
          </div>
          <span class="tag" style="background: #cf9a3f; color: #171310; padding: 4px 14px; border-radius: 20px; font-size: 0.85rem; font-weight: bold; display: inline-block; margin-bottom: 10px;">ลดทันที 10%</span>
          <h3 style="margin: 10px 0; color: #ffffff !important; font-size: 1.3rem;">ต้อนรับสมาชิกใหม่</h3>
          <p style="color: #d9cfc1 !important; font-size: 0.9rem; line-height: 1.6; margin-bottom: 20px;">ส่วนลด 10% สำหรับการสั่งซื้อครั้งแรก เมื่อสั่งซื้อครบ 300 บาทขึ้นไป</p>
          <a href="menu.php" class="btn btn-gold btn-sm" style="width: 100%; text-align: center; display: inline-block; padding: 10px 0;">สั่งซื้อทันที</a>
        </div>
      <?php endif; ?>
    </div>
  </div>
</section>

<?php render_footer(); ?>