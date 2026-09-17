<?php
require_once __DIR__ . '/connect.php';
require_once __DIR__ . '/page_template.php';

require_admin();
$page_title = 'แดชบอร์ด';

// ---------- สถิติ ----------
$totalMembers  = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'customer'")->fetchColumn();
$totalOrders   = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$totalSales    = $pdo->query("SELECT COALESCE(SUM(total),0) FROM orders")->fetchColumn();
$totalProducts = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();

// ยอดขายรายเดือน (ปีปัจจุบัน)
$salesByMonth = array_fill(1, 12, 0);
$rows = $pdo->query("SELECT MONTH(created_at) m, SUM(total) t FROM orders WHERE YEAR(created_at) = YEAR(CURDATE()) GROUP BY MONTH(created_at)")->fetchAll();
foreach ($rows as $r) { $salesByMonth[(int)$r['m']] = (float)$r['t']; }
$monthLabels = ['ม.ค.','ก.พ.','มี.ค.','เม.ย.','พ.ค.','มิ.ย.','ก.ค.','ส.ค.','ก.ย.','ต.ค.','พ.ย.','ธ.ค.'];

// คำสั่งซื้อล่าสุด
$recentOrders = $pdo->query("SELECT * FROM orders ORDER BY created_at DESC LIMIT 5")->fetchAll();

// สินค้าขายดี
$topProducts = $pdo->query("SELECT * FROM products ORDER BY sold_count DESC LIMIT 4")->fetchAll();

// สถานะคำสั่งซื้อ
$statusRows = $pdo->query("SELECT status, COUNT(*) c FROM orders GROUP BY status")->fetchAll();
$statusMap = ['รอชำระเงิน' => 0, 'กำลังจัดเตรียม' => 0, 'พร้อมรับ' => 0, 'เสร็จสิ้น' => 0];
foreach ($statusRows as $s) { $statusMap[$s['status']] = (int)$s['c']; }

$adminName = $_SESSION['user_name'] ?? 'Admin Waffle';

$statusColors = [
    'รอชำระเงิน'     => ['#fbeecb', '#a9772a'],
    'กำลังจัดเตรียม' => ['#e6d4f2', '#6a3fa0'],
    'พร้อมรับ'       => ['#d9f0e0', '#1e7b34'],
    'เสร็จสิ้น'       => ['#e2e2e2', '#4a4a4a'],
];
function status_style($status, $map) {
    $c = $map[$status] ?? ['#e2e2e2', '#4a4a4a'];
    return 'background:' . $c[0] . ';color:' . $c[1] . ';';
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>แดชบอร์ด - Waffle Hunger Admin</title>
<link rel="stylesheet" href="assets/style.css">
<link rel="stylesheet" href="assets/admin.css">
<!-- CDN FontAwesome Icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
  .top-product-img {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    overflow: hidden;
    background: #f5efe6;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 10px auto;
  }
  .top-product-img img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }
  .top-product-img i {
    font-size: 1.8rem;
    color: #a9772a;
  }
  .admin-sidebar .admin-logo .logo-icon i {
    font-size: 1.8rem;
    color: #e6c374;
  }
</style>
</head>
<body class="admin-body">

<div class="admin-layout">

  <!-- SIDEBAR (อัปเดตตรงกันกับ add_product.php) -->
  <aside class="admin-sidebar">
    <div class="admin-logo">
      <span class="logo-icon"><i class="fa-solid fa-stroopwafel"></i></span>
      <div><strong>Waffle Hunger</strong><small>ADMIN PANEL</small></div>
    </div>

    <nav class="admin-nav">
      <a href="dashboard.php" class="active">
        <i class="fa-solid fa-house"></i> <span>แดชบอร์ด</span>
      </a>
      <a href="add_product.php">
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
        <h1>แดชบอร์ด</h1>
        <p>ยินดีต้อนรับ, <?= htmlspecialchars($adminName) ?></p>
      </div>
      <div class="admin-topbar-actions">
        <button class="admin-bell"><i class="fa-solid fa-bell"></i><span class="admin-bell-badge">5</span></button>
        <div class="admin-date"><i class="fa-regular fa-calendar-days"></i> <?= date('d/m/Y') ?></div>
      </div>
    </header>

    <!-- KPI CARDS -->
    <div class="kpi-grid">
      <div class="kpi-card">
        <div class="kpi-icon" style="background:#e8d9c0; color:#6b4f2c;"><i class="fa-solid fa-users"></i></div>
        <div><small>สมาชิกทั้งหมด</small><h2><?= number_format($totalMembers) ?></h2></div>
      </div>
      <div class="kpi-card">
        <div class="kpi-icon" style="background:#d8c3a0; color:#5c4018;"><i class="fa-solid fa-bag-shopping"></i></div>
        <div><small>คำสั่งซื้อทั้งหมด</small><h2><?= number_format($totalOrders) ?></h2></div>
      </div>
      <div class="kpi-card">
        <div class="kpi-icon" style="background:#e6c374; color:#171310;"><i class="fa-solid fa-sack-dollar"></i></div>
        <div><small>ยอดขายรวม</small><h2>฿<?= number_format($totalSales) ?></h2></div>
      </div>
      <div class="kpi-card">
        <div class="kpi-icon" style="background:#2b2118; color:#e6c374;"><i class="fa-solid fa-box-archive"></i></div>
        <div><small>สินค้าทั้งหมด</small><h2><?= number_format($totalProducts) ?></h2></div>
      </div>
    </div>

    <!-- CHART + RECENT ORDERS -->
    <div class="admin-row">
      <div class="admin-panel" style="flex: 1.5;">
        <div class="admin-panel-head"><h3><i class="fa-solid fa-chart-line"></i> กราฟยอดขาย (บาท)</h3></div>
        <div style="position: relative; width:100%; height: 260px;">
          <canvas id="salesChart"></canvas>
        </div>
      </div>
      <div class="admin-panel" style="flex: 1;">
        <div class="admin-panel-head"><h3><i class="fa-solid fa-clock-rotate-left"></i> คำสั่งซื้อล่าสุด</h3></div>
        <div class="order-list">
          <?php if (empty($recentOrders)): ?>
            <p style="color:var(--text-muted);text-align:center;padding:20px 0">ยังไม่มีคำสั่งซื้อ</p>
          <?php endif; ?>
          <?php foreach ($recentOrders as $o): ?>
            <div class="order-row">
              <div class="order-avatar"><i class="fa-solid fa-user"></i></div>
              <div class="order-info">
                <strong>#ORD-<?= str_pad($o['id'], 4, '0', STR_PAD_LEFT) ?></strong>
                <small><?= htmlspecialchars($o['customer_name'] ?? 'ลูกค้า') ?></small>
              </div>
              <div class="order-time"><?= date('H:i น.', strtotime($o['created_at'])) ?></div>
              <div class="order-amount">฿<?= number_format($o['total']) ?></div>
              <span class="status-pill" style="<?= status_style($o['status'], $statusColors) ?>"><?= htmlspecialchars($o['status']) ?></span>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>

    <!-- TOP PRODUCTS + STATUS DONUT -->
    <div class="admin-row">
      <div class="admin-panel" style="flex: 1.5;">
        <div class="admin-panel-head"><h3><i class="fa-solid fa-fire"></i> สินค้าแนะนำ (ขายดี)</h3></div>
        <div class="top-products">
          <?php $rank = 1; foreach ($topProducts as $p): ?>
            <div class="top-product-card">
              <span class="rank"><?= $rank++ ?></span>
              <div class="top-product-img">
                <?php if (!empty($p['image']) && file_exists($p['image'])): ?>
                  <img src="<?= htmlspecialchars($p['image']) ?>" alt="<?= htmlspecialchars($p['name']) ?>">
                <?php else: ?>
                  <i class="fa-solid fa-cookie-bite"></i>
                <?php endif; ?>
              </div>
              <strong><?= htmlspecialchars($p['name']) ?></strong>
              <small>ขายแล้ว <?= $p['sold_count'] ?? 0 ?> ชิ้น</small>
              <div class="price">฿<?= number_format($p['price']) ?></div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
      <div class="admin-panel" style="flex: 1;">
        <div class="admin-panel-head"><h3><i class="fa-solid fa-chart-pie"></i> สถิติการสั่งซื้อ</h3></div>
        <div style="position: relative; width:100%; height: 200px;">
          <canvas id="statusChart"></canvas>
        </div>
        <div class="status-legend" style="margin-top: 15px; display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
          <div><span class="dot" style="background:#e6c374"></span>รอชำระเงิน <b><?= $statusMap['รอชำระเงิน'] ?></b></div>
          <div><span class="dot" style="background:#a9772a"></span>กำลังจัดเตรียม <b><?= $statusMap['กำลังจัดเตรียม'] ?></b></div>
          <div><span class="dot" style="background:#2b2118"></span>พร้อมรับ <b><?= $statusMap['พร้อมรับ'] ?></b></div>
          <div><span class="dot" style="background:#d8c3a0"></span>เสร็จสิ้น <b><?= $statusMap['เสร็จสิ้น'] ?></b></div>
        </div>
      </div>
    </div>

    <p class="admin-footnote">© <?= date('Y') ?> Waffle Hunger Admin Panel. All rights reserved.</p>
  </main>
</div>

<!-- Chart.js CDN -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.4/chart.umd.min.js"></script>
<script src="assets/admin.js"></script>
<script>
  const monthLabels = <?= json_encode($monthLabels, JSON_UNESCAPED_UNICODE) ?>;
  const salesData = <?= json_encode(array_values($salesByMonth)) ?>;
  const statusData = <?= json_encode(array_values($statusMap)) ?>;

  // Render Sales Line Chart
  const salesCtx = document.getElementById('salesChart');
  if (salesCtx) {
    new Chart(salesCtx, {
      type: 'line',
      data: {
        labels: monthLabels,
        datasets: [{
          label: 'ยอดขาย (บาท)',
          data: salesData,
          borderColor: '#a9772a',
          backgroundColor: 'rgba(169, 119, 42, 0.1)',
          fill: true,
          tension: 0.35,
          borderWidth: 2,
          pointBackgroundColor: '#171310'
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
          y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' } },
          x: { grid: { display: false } }
        }
      }
    });
  }

  // Render Status Donut Chart
  const statusCtx = document.getElementById('statusChart');
  if (statusCtx) {
    new Chart(statusCtx, {
      type: 'doughnut',
      data: {
        labels: ['รอชำระเงิน', 'กำลังจัดเตรียม', 'พร้อมรับ', 'เสร็จสิ้น'],
        datasets: [{
          data: statusData,
          backgroundColor: ['#e6c374', '#a9772a', '#2b2118', '#d8c3a0'],
          borderWidth: 2,
          borderColor: '#ffffff'
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } }
      }
    });
  }
</script>
</body>
</html>