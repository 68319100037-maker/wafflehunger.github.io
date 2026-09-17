<?php
require_once __DIR__ . '/connect.php';
require_once __DIR__ . '/page_template.php';

require_admin();
$page_title = 'จัดการสินค้า';

$msg = '';
$error = '';

// จัดการการลบสินค้า
if (isset($_GET['delete_id'])) {
    $del_id = (int)$_GET['delete_id'];
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    if ($stmt->execute([$del_id])) {
        $msg = 'ลบสินค้าเรียบร้อยแล้ว';
    } else {
        $error = 'เกิดข้อผิดพลาดในการลบสินค้า';
    }
}

// จัดการการเพิ่มสินค้า
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $name = trim($_POST['name'] ?? '');
    $category_id = (int)($_POST['category_id'] ?? 0);
    $price = (float)($_POST['price'] ?? 0);
    $description = trim($_POST['description'] ?? '');
    $icon = trim($_POST['icon'] ?? 'fa-solid fa-cookie-bite');
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;

    if (!empty($name) && $category_id > 0 && $price > 0) {
        $stmt = $pdo->prepare("INSERT INTO products (category_id, name, description, price, icon, is_featured) VALUES (?, ?, ?, ?, ?, ?)");
        if ($stmt->execute([$category_id, $name, $description, $price, $icon, $is_featured])) {
            $msg = 'เพิ่มสินค้าเรียบร้อยแล้ว';
        } else {
            $error = 'เกิดข้อผิดพลาดในการบันทึกข้อมูล';
        }
    } else {
        $error = 'กรุณากรอกข้อมูลสำคัญให้ครบถ้วน';
    }
}

// ดึงข้อมูลหมวดหมู่และสินค้า
$categories = $pdo->query("SELECT * FROM categories ORDER BY id ASC")->fetchAll();
$products = $pdo->query("SELECT p.*, c.name AS cat_name FROM products p JOIN categories c ON c.id = p.category_id ORDER BY p.id DESC")->fetchAll();
$adminName = $_SESSION['user_name'] ?? 'Admin Waffle';
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>จัดการสินค้า - Waffle Hunger Admin</title>
<link rel="stylesheet" href="assets/style.css">
<link rel="stylesheet" href="assets/admin.css">
<!-- CDN FontAwesome Icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
  .admin-sidebar .admin-logo .logo-icon i {
    font-size: 1.8rem;
    color: #e6c374;
  }
  .product-table-icon {
    width: 36px;
    height: 36px;
    background: #f5efe6;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #a9772a;
    font-size: 1.2rem;
  }
  .product-table-img {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    object-fit: cover;
  }
</style>
</head>
<body class="admin-body">

<div class="admin-layout">

  <!-- SIDEBAR (ปรับแก้เป็นเมนู Admin ทั้งหมด) -->
  <aside class="admin-sidebar">
    <div class="admin-logo">
      <span class="logo-icon"><i class="fa-solid fa-stroopwafel"></i></span>
      <div><strong>Waffle Hunger</strong><small>ADMIN PANEL</small></div>
    </div>

    <nav class="admin-nav">
      <a href="dashboard.php">
        <i class="fa-solid fa-house"></i> <span>แดชบอร์ด</span>
      </a>
      <a href="add_product.php" class="active">
        <i class="fa-solid fa-box-open"></i> <span>จัดการสินค้า</span>
      </a>
      <a href="#">
        <i class="fa-solid fa-list-check"></i> <span>จัดการหมวดหมู่</span>
      </a>
      <a href="#">
        <i class="fa-solid fa-receipt"></i> <span>จัดการคำสั่งซื้อ</span>
      </a>
      <a href="#">
        <i class="fa-solid fa-users"></i> <span>จัดการสมาชิก</span>
      </a>
      <a href="#">
        <i class="fa-solid fa-ticket"></i> <span>จัดการโปรโมชั่น</span>
      </a>
      <a href="#">
        <i class="fa-solid fa-star"></i> <span>รีวิวจากลูกค้า</span>
      </a>
      <a href="#">
        <i class="fa-solid fa-pen-to-square"></i> <span>เนื้อหาเว็บไซต์</span>
      </a>
      <a href="#">
        <i class="fa-solid fa-chart-pie"></i> <span>รายงานยอดขาย</span>
      </a>
      <a href="#">
        <i class="fa-solid fa-bell"></i> <span>การแจ้งเตือน</span>
      </a>
      <a href="#">
        <i class="fa-solid fa-gear"></i> <span>ตั้งค่าเว็บไซต์</span>
      </a>
    </nav>

    <div class="admin-user-box">
      <div class="admin-user-info">
        <div class="avatar"><i class="fa-solid fa-user-gear"></i></div>
        <div><strong><?= htmlspecialchars($adminName) ?></strong><small>Super Admin</small></div>
      </div>
      <a href="logout.php" class="admin-logout"><i class="fa-solid fa-right-from-bracket"></i> <span>ออกจากระบบ</span></a>
    </div>
  </aside>

  <!-- MAIN -->
  <main class="admin-main">
    <header class="admin-topbar">
      <div>
        <h1>จัดการสินค้า</h1>
        <p>เพิ่ม แก้ไข และลบข้อมูลสินค้าทั้งหมดของร้าน</p>
      </div>
      <div class="admin-topbar-actions">
        <button class="admin-bell"><i class="fa-solid fa-bell"></i><span class="admin-bell-badge">5</span></button>
        <div class="admin-date"><i class="fa-regular fa-calendar-days"></i> <?= date('d/m/Y') ?></div>
      </div>
    </header>

    <?php if ($msg): ?>
      <div style="background:#d4edda; color:#155724; padding:12px 20px; border-radius:10px; margin-bottom:20px;">
        <i class="fa-solid fa-circle-check"></i> <?= htmlspecialchars($msg) ?>
      </div>
    <?php endif; ?>

    <?php if ($error): ?>
      <div style="background:#f8d7da; color:#721c24; padding:12px 20px; border-radius:10px; margin-bottom:20px;">
        <i class="fa-solid fa-circle-exclamation"></i> <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <!-- ฟอร์มเพิ่มสินค้า -->
    <div class="admin-panel" style="margin-bottom: 30px;">
      <div class="admin-panel-head">
        <h3><i class="fa-solid fa-plus" style="color: #a9772a;"></i> เพิ่มสินค้าใหม่</h3>
      </div>
      <form method="POST" action="add_product.php">
        <input type="hidden" name="add_product" value="1">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 15px;">
          <div>
            <label style="display:block; margin-bottom:8px; font-weight:bold;">ชื่อสินค้า</label>
            <input type="text" name="name" required placeholder="เช่น วาฟเฟิลมะม่วง" style="width:100%; padding:10px; border-radius:8px; border:1px solid #ddd;">
          </div>
          <div>
            <label style="display:block; margin-bottom:8px; font-weight:bold;">หมวดหมู่</label>
            <select name="category_id" required style="width:100%; padding:10px; border-radius:8px; border:1px solid #ddd; background:#fff;">
              <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 15px;">
          <div>
            <label style="display:block; margin-bottom:8px; font-weight:bold;">ราคา (บาท)</label>
            <input type="number" step="0.01" name="price" required placeholder="129" style="width:100%; padding:10px; border-radius:8px; border:1px solid #ddd;">
          </div>
          <div>
            <label style="display:block; margin-bottom:8px; font-weight:bold;">ไอคอน FontAwesome (ถ้ามี)</label>
            <input type="text" name="icon" placeholder="fa-solid fa-cookie-bite" style="width:100%; padding:10px; border-radius:8px; border:1px solid #ddd;">
          </div>
        </div>

        <div style="margin-bottom: 15px;">
          <label style="display:block; margin-bottom:8px; font-weight:bold;">รายละเอียดสินค้า</label>
          <textarea name="description" rows="3" placeholder="คำอธิบายสั้นๆ เกี่ยวกับเมนูนี้" style="width:100%; padding:10px; border-radius:8px; border:1px solid #ddd;"></textarea>
        </div>

        <div style="margin-bottom: 20px;">
          <label style="display:inline-flex; align-items:center; gap:8px; cursor:pointer;">
            <input type="checkbox" name="is_featured" value="1"> แสดงเป็นเมนูแนะนำในหน้าแรก
          </label>
        </div>

        <button type="submit" class="btn btn-gold" style="padding: 10px 25px; border:none; cursor:pointer;">
          <i class="fa-solid fa-floppy-disk"></i> บันทึกสินค้า
        </button>
      </form>
    </div>

    <!-- ตารางรายการสินค้า -->
    <div class="admin-panel">
      <div class="admin-panel-head">
        <h3><i class="fa-solid fa-list-check"></i> รายการสินค้าทั้งหมด (<?= count($products) ?>)</h3>
      </div>
      <div style="overflow-x: auto;">
        <table style="width:100%; border-collapse:collapse; text-align:left;">
          <thead>
            <tr style="border-bottom: 2px solid #eee; color:#666; font-size:0.9rem;">
              <th style="padding:12px;">สินค้า</th>
              <th style="padding:12px;">หมวดหมู่</th>
              <th style="padding:12px;">ราคา</th>
              <th style="padding:12px;">ขายแล้ว</th>
              <th style="padding:12px;">แนะนำ</th>
              <th style="padding:12px; text-align:center;">จัดการ</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($products as $p): ?>
              <tr style="border-bottom: 1px solid #eee;">
                <td style="padding:12px; display:flex; align-items:center; gap:12px;">
                  <?php if (!empty($p['image']) && file_exists($p['image'])): ?>
                    <img src="<?= htmlspecialchars($p['image']) ?>" class="product-table-img">
                  <?php else: ?>
                    <div class="product-table-icon">
                      <i class="<?= !empty($p['icon']) ? htmlspecialchars($p['icon']) : 'fa-solid fa-cookie-bite' ?>"></i>
                    </div>
                  <?php endif; ?>
                  <strong><?= htmlspecialchars($p['name']) ?></strong>
                </td>
                <td style="padding:12px; color:#666;"><?= htmlspecialchars($p['cat_name']) ?></td>
                <td style="padding:12px; font-weight:bold; color:#a9772a;">฿<?= number_format($p['price']) ?></td>
                <td style="padding:12px; color:#666;"><?= $p['sold_count'] ?? 0 ?></td>
                <td style="padding:12px;">
                  <?= !empty($p['is_featured']) ? '<i class="fa-solid fa-star" style="color:#e6c374;"></i>' : '-' ?>
                </td>
                <td style="padding:12px; text-align:center;">
                  <a href="add_product.php?delete_id=<?= $p['id'] ?>" onclick="return confirm('ยืนยันการลบสินค้านี้?')" style="color:#d9534f; text-decoration:none; font-weight:bold;">
                    <i class="fa-solid fa-trash-can"></i> ลบ
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>

    <p class="admin-footnote">© <?= date('Y') ?> Waffle Hunger Admin Panel. All rights reserved.</p>
  </main>
</div>

</body>
</html>