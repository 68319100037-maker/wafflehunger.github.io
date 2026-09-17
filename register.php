<?php
require_once __DIR__ . '/connect.php';
require_once __DIR__ . '/page_template.php';

$page_title = 'สมัครสมาชิก';
$error = '';
$success = false;

if (is_logged_in()) {
    header('Location: index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $phone    = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';

    if ($name === '' || $email === '' || $password === '') {
        $error = 'กรุณากรอกข้อมูลให้ครบถ้วน';
    } elseif ($password !== $confirm) {
        $error = 'รหัสผ่านทั้งสองช่องไม่ตรงกัน';
    } elseif (strlen($password) < 6) {
        $error = 'รหัสผ่านต้องมีอย่างน้อย 6 ตัวอักษร';
    } else {
        $check = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $check->execute([$email]);
        if ($check->fetch()) {
            $error = 'อีเมลนี้ถูกใช้งานแล้ว';
        } else {
            $stmt = $pdo->prepare("INSERT INTO users (name, email, phone, password, role) VALUES (?, ?, ?, ?, 'customer')");
            $stmt->execute([$name, $email, $phone, password_hash($password, PASSWORD_DEFAULT)]);
            $success = true;
        }
    }
}

render_header('');
?>

<div class="auth-wrap">
  <div class="auth-visual">
    <div class="emoji">🍓</div>
    <h2>สมัครสมาชิกวันนี้</h2>
    <p>รับสิทธิพิเศษ โปรโมชั่นก่อนใคร และสั่งซื้อได้สะดวกยิ่งขึ้น</p>
  </div>
  <div class="auth-form-side">
    <div class="auth-box">
      <h1>สมัครสมาชิก</h1>
      <p class="sub">สร้างบัญชีใหม่เพื่อเริ่มสั่งวาฟเฟิลสุดโปรด</p>

      <?php if ($success): ?>
        <div class="alert alert-success">✅ สมัครสมาชิกสำเร็จ! <a href="login.php">เข้าสู่ระบบ</a> ได้เลย</div>
      <?php endif; ?>
      <?php if ($error): ?>
        <div class="alert alert-error">⚠️ <?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <?php if (!$success): ?>
      <form method="POST" action="register.php">
        <div class="form-group">
          <label>ชื่อ-นามสกุล</label>
          <input type="text" name="name" required>
        </div>
        <div class="form-group">
          <label>อีเมล</label>
          <input type="email" name="email" required>
        </div>
        <div class="form-group">
          <label>เบอร์โทรศัพท์</label>
          <input type="text" name="phone">
        </div>
        <div class="form-group">
          <label>รหัสผ่าน</label>
          <input type="password" name="password" required>
        </div>
        <div class="form-group">
          <label>ยืนยันรหัสผ่าน</label>
          <input type="password" name="confirm_password" required>
        </div>
        <button type="submit" class="btn btn-gold btn-block">สมัครสมาชิก</button>
      </form>
      <?php endif; ?>

      <div class="switch">มีบัญชีอยู่แล้ว? <a href="login.php">เข้าสู่ระบบ</a></div>
    </div>
  </div>
</div>

<?php render_footer(); ?>
