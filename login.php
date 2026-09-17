<?php
require_once __DIR__ . '/connect.php';
require_once __DIR__ . '/page_template.php';

$page_title = 'เข้าสู่ระบบ';
$error = '';

// ถ้าล็อกอินอยู่แล้ว ให้ไปหน้าแรกเลย
if (is_logged_in()) {
    header('Location: index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $error = 'กรุณากรอกอีเมลและรหัสผ่าน';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['role']      = $user['role'];

            // Admin -> ไปหน้าแรก (จะเห็นปุ่ม "หลังบ้าน") / Customer -> ไปหน้าแรกเช่นกัน
            header('Location: index.php');
            exit();
        } else {
            $error = 'อีเมลหรือรหัสผ่านไม่ถูกต้อง';
        }
    }
}

render_header('');
?>

<div class="auth-wrap">
  <div class="auth-visual">
    <div class="emoji">🧇</div>
    <h2>ยินดีต้อนรับกลับมา</h2>
    <p>เข้าสู่ระบบเพื่อสั่งวาฟเฟิลสุดโปรด และติดตามคำสั่งซื้อของคุณได้ง่ายๆ</p>
  </div>
  <div class="auth-form-side">
    <div class="auth-box">
      <h1>เข้าสู่ระบบ</h1>
      <p class="sub">กรอกข้อมูลเพื่อเข้าใช้งานบัญชีของคุณ</p>

      <?php if ($error): ?>
        <div class="alert alert-error">⚠️ <?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="POST" action="login.php">
        <div class="form-group">
          <label>อีเมล</label>
          <input type="email" name="email" required placeholder="you@example.com">
        </div>
        <div class="form-group">
          <label>รหัสผ่าน</label>
          <input type="password" name="password" required placeholder="••••••••">
        </div>
        <button type="submit" class="btn btn-gold btn-block">เข้าสู่ระบบ</button>
      </form>

      <div class="switch">ยังไม่มีบัญชี? <a href="register.php">สมัครสมาชิก</a></div>
      <div class="switch" style="margin-top:8px;font-size:12px">
        บัญชีแอดมินทดสอบ: admin@wafflehunger.com / admin1234
      </div>
    </div>
  </div>
</div>

<?php render_footer(); ?>
