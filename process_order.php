<?php
require_once __DIR__ . '/connect.php';
require_once __DIR__ . '/page_template.php';

$page_title = 'ยืนยันคำสั่งซื้อ';

$cartData = json_decode($_POST['cart_data'] ?? '[]', true);
$orderSaved = false;
$orderId = null;
$errorMsg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($cartData)) {

    // ถ้ายืนยันฟอร์มที่อยู่จัดส่งแล้ว (ขั้นตอนที่ 2)
    if (isset($_POST['confirm_order'])) {
        $name  = trim($_POST['customer_name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $addr  = trim($_POST['address'] ?? '');
        $items = json_decode($_POST['cart_data'], true);

        if ($name === '' || $phone === '' || empty($items)) {
            $errorMsg = 'กรุณากรอกข้อมูลให้ครบถ้วน';
        } else {
            $total = 0;
            foreach ($items as $it) {
                $total += floatval($it['price']) * intval($it['qty']);
            }

            $userId = $_SESSION['user_id'] ?? null;

            $stmt = $pdo->prepare("INSERT INTO orders (user_id, customer_name, phone, address, total) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$userId, $name, $phone, $addr, $total]);
            $orderId = $pdo->lastInsertId();

            $itemStmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, qty, price) VALUES (?, ?, ?, ?)");
            foreach ($items as $it) {
                $itemStmt->execute([$orderId, $it['id'], $it['qty'], $it['price']]);
                // อัปเดตยอดขาย
                $pdo->prepare("UPDATE products SET sold_count = sold_count + ? WHERE id = ?")->execute([$it['qty'], $it['id']]);
            }
            $orderSaved = true;
        }
    }
}

render_header('');
?>

<section class="section" style="padding-top:50px;min-height:60vh">
  <div class="container" style="max-width:640px">

    <?php if ($orderSaved): ?>
      <div class="form-card" style="text-align:center">
        <div style="font-size:60px;margin-bottom:16px">✅</div>
        <h2 style="margin-bottom:10px">สั่งซื้อสำเร็จ!</h2>
        <p style="color:var(--text-muted);margin-bottom:24px">หมายเลขคำสั่งซื้อของคุณคือ #ORD-<?= str_pad($orderId, 4, '0', STR_PAD_LEFT) ?><br>ทีมงานจะติดต่อกลับเพื่อยืนยันคำสั่งซื้อเร็วๆ นี้</p>
        <a href="menu.php" class="btn btn-gold">สั่งเมนูอื่นเพิ่ม</a>
        <script>localStorage.removeItem('waffle_hunger_cart');</script>
      </div>

    <?php elseif (!empty($cartData)): ?>
      <div class="form-card">
        <h2 style="margin-bottom:20px">📋 ยืนยันคำสั่งซื้อ</h2>

        <?php if ($errorMsg): ?><div class="alert alert-error">⚠️ <?= htmlspecialchars($errorMsg) ?></div><?php endif; ?>

        <div style="margin-bottom:24px">
          <?php $total = 0; foreach ($cartData as $it): $sub = $it['price'] * $it['qty']; $total += $sub; ?>
            <div style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid var(--cream-dark)">
              <span><?= $it['icon'] ?> <?= htmlspecialchars($it['name']) ?> x<?= $it['qty'] ?></span>
              <span><?= number_format($sub) ?> บาท</span>
            </div>
          <?php endforeach; ?>
          <div class="cart-total" style="margin-top:14px"><span>ยอดรวม</span><span><?= number_format($total) ?> บาท</span></div>
        </div>

        <form method="POST" action="process_order.php">
          <input type="hidden" name="cart_data" value='<?= htmlspecialchars(json_encode($cartData), ENT_QUOTES) ?>'>
          <input type="hidden" name="confirm_order" value="1">
          <div class="form-group">
            <label>ชื่อผู้รับ</label>
            <input type="text" name="customer_name" required value="<?= htmlspecialchars($_SESSION['user_name'] ?? '') ?>">
          </div>
          <div class="form-group">
            <label>เบอร์โทรศัพท์</label>
            <input type="text" name="phone" required>
          </div>
          <div class="form-group">
            <label>ที่อยู่จัดส่ง</label>
            <textarea name="address" rows="3" required></textarea>
          </div>
          <button type="submit" class="btn btn-gold btn-block">ยืนยันสั่งซื้อ</button>
        </form>
      </div>

    <?php else: ?>
      <div class="form-card" style="text-align:center">
        <div style="font-size:60px;margin-bottom:16px">🧺</div>
        <h2>ไม่พบข้อมูลตะกร้าสินค้า</h2>
        <p style="color:var(--text-muted);margin:14px 0 24px">กรุณาเลือกเมนูก่อนทำการสั่งซื้อ</p>
        <a href="menu.php" class="btn btn-gold">ไปเลือกเมนู</a>
      </div>
    <?php endif; ?>

  </div>
</section>

<?php render_footer(); ?>
