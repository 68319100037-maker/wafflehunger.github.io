<?php
require_once __DIR__ . '/connect.php';
require_once __DIR__ . '/page_template.php';

$page_title = 'เมนูอาหาร';
$is_logged_in = isset($_SESSION['user_id']) || isset($_SESSION['user']);

$category_filter = $_GET['cat'] ?? 'all';
$search = trim($_GET['q'] ?? '');

$sql = "SELECT p.*, c.name AS cat_name, c.slug AS cat_slug FROM products p JOIN categories c ON c.id = p.category_id WHERE 1=1";
$params = [];

if ($category_filter !== 'all') {
    $sql .= " AND c.slug = ?";
    $params[] = $category_filter;
}
if (!empty($search)) {
    $sql .= " AND (p.name LIKE ? OR p.description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}
$sql .= " ORDER BY p.id ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

$categories = $pdo->query("SELECT * FROM categories ORDER BY id ASC")->fetchAll();

render_header('menu');
?>

<style>
  .page-header {
    text-align: center !important;
  }
  .product-img-box {
    width: 100%;
    height: 200px;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    margin-bottom: 15px;
    border: 1px solid rgba(255, 255, 255, 0.1);
  }
  .product-img-box img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
  }
  .product-card:hover .product-img-box img {
    transform: scale(1.06);
  }
  .product-img-box .placeholder-icon {
    font-size: 3.5rem;
    color: var(--gold, #d4af37);
  }

  /* CUSTOM MODAL POPUP */
  .custom-modal-backdrop {
    position: fixed; top: 0; left: 0; width: 100%; height: 100%;
    background: rgba(0, 0, 0, 0.75); backdrop-filter: blur(4px);
    z-index: 9999; display: flex; align-items: center; justify-content: center;
    opacity: 0; visibility: hidden; transition: opacity 0.3s ease, visibility 0.3s ease;
  }
  .custom-modal-backdrop.show { opacity: 1; visibility: visible; }
  .custom-modal {
    background: #1a1a1a; border: 1px solid rgba(212, 175, 55, 0.3);
    border-radius: 16px; padding: 30px 25px; width: 90%; max-width: 400px;
    text-align: center; box-shadow: 0 15px 35px rgba(0, 0, 0, 0.6);
    transform: scale(0.8); transition: transform 0.3s ease; color: #fff;
  }
  .custom-modal-backdrop.show .custom-modal { transform: scale(1); }
  .custom-modal-icon {
    width: 60px; height: 60px; background: rgba(212, 175, 55, 0.15);
    color: #d4af37; border-radius: 50%; display: flex; align-items: center;
    justify-content: center; font-size: 1.8rem; margin: 0 auto 15px auto;
  }
  .custom-modal h3 { margin: 0 0 10px 0; font-size: 1.4rem; color: #d4af37; }
  .custom-modal p { color: #ccc; font-size: 0.95rem; margin-bottom: 25px; line-height: 1.5; }
  .custom-modal-actions { display: flex; gap: 10px; justify-content: center; }
  .custom-modal-actions .btn { flex: 1; padding: 10px 0; font-size: 0.95rem; cursor: pointer; }
</style>

<!-- ส่วนหัวข้อหลัก จัดกึ่งกลางเรียบร้อย -->
<div class="page-header" style="text-align: center;">
  <div class="container">
    <h1><i class="fa-solid fa-utensils"></i> เมนูทั้งหมด</h1>
    <p>เลือกสรรความอร่อยจากวาฟเฟิลอบสดใหม่และเครื่องดื่มเข้มข้น</p>
  </div>
</div>

<section class="section section-cream">
  <div class="container">
    <!-- Filter Categories -->
    <div class="menu-filters" style="display: flex; gap: 12px; justify-content: center; margin-bottom: 35px; flex-wrap: wrap;">
      <a href="menu.php" class="btn <?= $category_filter === 'all' ? 'btn-gold' : 'btn-outline' ?>" style="<?= $category_filter !== 'all' ? 'color:#171310; border-color:#171310;' : '' ?>">
        <i class="fa-solid fa-border-all"></i> ทั้งหมด
      </a>
      <?php foreach ($categories as $cat): ?>
        <a href="menu.php?cat=<?= $cat['slug'] ?>" class="btn <?= $category_filter === $cat['slug'] ? 'btn-gold' : 'btn-outline' ?>" style="<?= $category_filter !== $cat['slug'] ? 'color:#171310; border-color:#171310;' : '' ?>">
          <i class="fa-solid <?= $cat['slug'] === 'dessert' ? 'fa-cookie-bite' : 'fa-mug-hot' ?>"></i> <?= htmlspecialchars($cat['name']) ?>
        </a>
      <?php endforeach; ?>
    </div>

    <!-- Products Grid -->
    <div class="grid grid-4">
      <?php if (count($products) > 0): ?>
        <?php foreach ($products as $p): ?>
          <div class="product-card" style="background: #ffffff; border: 1px solid rgba(0,0,0,0.08); box-shadow: 0 4px 15px rgba(0,0,0,0.05); border-radius: 16px; padding: 15px;">
            <div class="product-img-box">
              <?php 
                $img_src = !empty($p['image']) ? $p['image'] : (!empty($p['image_url']) ? $p['image_url'] : '');
                if (!empty($img_src)): 
              ?>
                <img src="<?= htmlspecialchars($img_src) ?>" alt="<?= htmlspecialchars($p['name']) ?>" loading="lazy">
              <?php else: ?>
                <i class="fa-solid fa-utensils placeholder-icon"></i>
              <?php endif; ?>
            </div>
            <div class="product-body">
              <div class="product-cat" style="color: #8c7662 !important; font-size: 0.85rem; font-weight: bold;"><?= htmlspecialchars($p['cat_name']) ?></div>
              <h3 style="color: #171310 !important; font-size: 1.1rem; margin: 6px 0;"><?= htmlspecialchars($p['name']) ?></h3>
              <p style="font-size:0.85rem; color:#666 !important; margin-bottom:10px; line-height: 1.4;"><?= htmlspecialchars($p['description'] ?? '') ?></p>
              <div class="product-foot" style="display: flex; justify-content: space-between; align-items: center; margin-top: 10px;">
                <div class="price" style="color: #a9772a !important; font-weight: bold; font-size: 1.1rem;"><?= number_format($p['price']) ?> <span style="font-size: 0.85rem; color: #666;">บาท</span></div>
                <button class="add-btn" onclick="addToCartCheck(event, <?= $p['id'] ?>, '<?= htmlspecialchars($p['name'], ENT_QUOTES) ?>', <?= $p['price'] ?>)" style="background: #171310; color: #e6c374; border: none; width: 36px; height: 36px; border-radius: 50%; cursor: pointer;">
                  <i class="fa-solid fa-plus"></i>
                </button>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div style="grid-column: 1/-1; text-align: center; padding: 50px 0; color: #666;">
          <i class="fa-solid fa-magnifying-glass" style="font-size: 3rem; margin-bottom: 15px; color: #a9772a;"></i>
          <p style="font-size: 1.1rem; font-weight: 500;">ไม่พบรายการสินค้าที่ค้นหา</p>
        </div>
      <?php endif; ?>
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

<script>
  const isLoggedIn = <?= json_encode($is_logged_in) ?>;

  function showLoginModal(message) {
    if(message) document.getElementById('modalMessage').innerText = message;
    document.getElementById('loginAlertModal').classList.add('show');
  }

  function closeLoginModal() {
    document.getElementById('loginAlertModal').classList.remove('show');
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