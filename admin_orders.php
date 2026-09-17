<?php
require_once __DIR__ . '/connect.php';
require_once __DIR__ . '/page_template.php';

require_admin();
$page_title = 'จัดการคำสั่งซื้อ';

$msg = '';
$error = '';

// อัปเดตสถานะคำสั่งซื้อ
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $order_id = (int)$_POST['order_id'];
    $new_status = trim($_POST['status']);
    
    $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
    if ($stmt->execute([$new_status, $order_id])) {
        $msg = "อัปเดตสถานะคำสั่งซื้อ #ORD-" . str_pad($order_id, 4, '0', STR_PAD_LEFT) . " เรียบร้อยแล้ว";
    } else {
        $error = "เกิดข้อผิดพลาดในการอัปเดตสถานะ";
    }
}

// ดึงคำสั่งซื้อทั้งหมด
$orders = $pdo->query("SELECT * FROM orders ORDER BY created_at DESC")->fetchAll();
$adminName = $_SESSION['user_name'] ?? 'Admin Waffle';

$statusColors = [
    'รอชำระเงิน'     => ['#fbeecb', '#a9772a'],
    'กำลังจัดเตรียม' => ['#e6d4f2', '#6a3fa0'],
    'พร้อมรับ'       => ['#d9f0e0', '#1e7b34'],
    'เสร็จสิ้น'       => ['#e2e2e2', '#4a4a4a'],
];
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>จัดการคำสั่งซื้อ - Waffle Hunger Admin</title>
<link rel="stylesheet" href="assets/style.css">
<link rel="stylesheet" href="assets/admin.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="admin-body">

<div class="admin-layout">
  <!-- SIDEBAR -->
  <aside class="admin-sidebar">
    <div class="admin-logo">
      <span class="logo-icon"><i class="fa-solid fa-stroopwafel"></i></span>
      <div><strong>Waffle Hunger</strong><small>ADMIN PANEL</small></div>
    </div>

    <nav class="admin-nav">
      <a href="dashboard.php"><i class="fa-solid fa-house"></i> <span>แดชบอร์ด</span></a>
      <a href="add_product.php"><i class="fa-solid fa-box-open"></i> <span>จัดการสินค้า</span></a>
      <a href="admin_orders.php" class="active"><i class="fa-solid fa-receipt"></i> <span>จัดการคำสั่งซื้อ</span></a>
      <a href="promotion.php"><i class="fa-solid fa-ticket"></i> <span>จัดการโปรโมชั่น</span></a>
      <a href="#"><i class="fa-solid fa-users"></i> <span>จัดการสมาชิก</span></a>
      <a href="#"><i class="fa-solid fa-gear"></i> <span>ตั้งค่าเว็บไซต์</span></a>
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
        <h1>จัดการคำสั่งซื้อ</h1>
        <p>ตรวจสอบและอัปเดตสถานะรายการสั่งซื้อของลูกค้า</p>
      </div>
    </header>

    <?php if ($msg): ?>
      <div style="background:#d4edda; color:#155724; padding:12px 20px; border-radius:10px; margin-bottom:20px;">
        <i class="fa-solid fa-circle-check"></i> <?= htmlspecialchars($msg) ?>
      </div>
    <?php endif; ?>

    <div class="admin-panel">
      <div class="admin-panel-head">
        <h3><i class="fa-solid fa-list"></i> รายการคำสั่งซื้อทั้งหมด (<?= count($orders) ?>)</h3>
      </div>
      <div style="overflow-x: auto;">
        <table style="width:100%; border-collapse:collapse; text-align:left;">
          <thead>
            <tr style="border-bottom: 2px solid #eee; color:#666;">
              <th style="padding:12px;">หมายเลข</th>
              <th style="padding:12px;">ลูกค้า</th>
              <th style="padding:12px;">เบอร์โทร</th>
              <th style="padding:12px;">ยอดรวม</th>
              <th style="padding:12px;">วันที่สั่งซื้อ</th>
              <th style="padding:12px;">สถานะปัจจุบัน</th>
              <th style="padding:12px; text-align:center;">เปลี่ยนสถานะ</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($orders as $o): ?>
              <tr style="border-bottom: 1px solid #eee;">
                <td style="padding:12px;"><strong>#ORD-<?= str_pad($o['id'], 4, '0', STR_PAD_LEFT) ?></strong></td>
                <td style="padding:12px;"><?= htmlspecialchars($o['customer_name'] ?? 'ไม่ระบุ') ?></td>
                <td style="padding:12px;"><?= htmlspecialchars($o['phone'] ?? '-') ?></td>
                <td style="padding:12px; font-weight:bold; color:#a9772a;">฿<?= number_format($o['total']) ?></td>
                <td style="padding:12px; color:#666;"><?= date('d/m/Y H:i', strtotime($o['created_at'])) ?></td>
                <td style="padding:12px;">
                  <span style="padding:4px 10px; border-radius:20px; font-size:0.85rem; background:<?= $statusColors[$o['status']][0] ?? '#eee' ?>; color:<?= $statusColors[$o['status']][1] ?? '#333' ?>;">
                    <?= htmlspecialchars($o['status'] ?? 'รอชำระเงิน') ?>
                  </span>
                </td>
                <td style="padding:12px; text-align:center;">
                  <form method="POST" style="display:inline-flex; gap:5px;">
                    <input type="hidden" name="order_id" value="<?= $o['id'] ?>">
                    <select name="status" style="padding:5px; border-radius:5px; border:1px solid #ddd;">
                      <option value="รอชำระเงิน" <?= ($o['status'] ?? '') == 'รอชำระเงิน' ? 'selected' : '' ?>>รอชำระเงิน</option>
                      <option value="กำลังจัดเตรียม" <?= ($o['status'] ?? '') == 'กำลังจัดเตรียม' ? 'selected' : '' ?>>กำลังจัดเตรียม</option>
                      <option value="พร้อมรับ" <?= ($o['status'] ?? '') == 'พร้อมรับ' ? 'selected' : '' ?>>พร้อมรับ</option>
                      <option value="เสร็จสิ้น" <?= ($o['status'] ?? '') == 'เสร็จสิ้น' ? 'selected' : '' ?>>เสร็จสิ้น</option>
                    </select>
                    <button type="submit" name="update_status" class="btn btn-gold" style="padding:5px 10px; font-size:0.8rem; border:none; cursor:pointer;">บันทึก</button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </main>
</div>
</body>
</html>