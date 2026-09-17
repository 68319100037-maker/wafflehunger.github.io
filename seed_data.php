<?php
/**
 * seed_data.php
 * ----------------------------------------------------
 * รีเซ็ตฐานข้อมูลทั้งหมด + เปลี่ยน Emoji เป็น Font Awesome Icons
 * ----------------------------------------------------
 */

require_once __DIR__ . '/db.php';

try {
    $pdo = new PDO('mysql:host=' . DB_HOST . ';charset=' . DB_CHARSET, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `" . DB_NAME . "`");
} catch (PDOException $e) {
    die('สร้างฐานข้อมูลไม่สำเร็จ: ' . htmlspecialchars($e->getMessage()));
}

$log = [];

// ---------- 1. Drop Tables ----------
$pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
$tables = ['order_items', 'orders', 'products', 'categories', 'promotions', 'contacts', 'users', 'notifications', 'website_contents', 'order_details'];
foreach ($tables as $table) {
    $pdo->exec("DROP TABLE IF EXISTS `$table`");
}
$pdo->exec("SET FOREIGN_KEY_CHECKS = 1");

// ---------- 2. Create Tables ----------
$pdo->exec("CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    phone VARCHAR(30) DEFAULT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('customer','admin') NOT NULL DEFAULT 'customer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

$pdo->exec("CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

$pdo->exec("CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    name VARCHAR(200) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    icon VARCHAR(100) DEFAULT 'fa-solid fa-stroopwafel',
    is_featured TINYINT(1) DEFAULT 0,
    sold_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

$pdo->exec("CREATE TABLE promotions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    description VARCHAR(255) DEFAULT NULL,
    tag VARCHAR(50) DEFAULT NULL,
    icon VARCHAR(100) DEFAULT 'fa-solid fa-gift',
    active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

$pdo->exec("CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT DEFAULT NULL,
    customer_name VARCHAR(150) NOT NULL,
    phone VARCHAR(30) NOT NULL,
    address TEXT,
    total DECIMAL(10,2) NOT NULL DEFAULT 0,
    status ENUM('รอชำระเงิน','กำลังจัดเตรียม','พร้อมรับ','เสร็จสิ้น') DEFAULT 'รอชำระเงิน',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

$pdo->exec("CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    qty INT NOT NULL DEFAULT 1,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

$pdo->exec("CREATE TABLE contacts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    email VARCHAR(150) DEFAULT NULL,
    phone VARCHAR(30) DEFAULT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

// ---------- 3. Insert Categories ----------
$stmtCat = $pdo->prepare("INSERT INTO categories (name, slug) VALUES (?, ?)");
$stmtCat->execute(['ขนมหวาน', 'dessert']);
$dessertId = $pdo->lastInsertId();

$stmtCat->execute(['เครื่องดื่ม', 'drink']);
$drinkId = $pdo->lastInsertId();

// ---------- 4. Insert Products (เปลี่ยน Emoji เป็น FontAwesome Vector Icons) ----------
$desserts = [
    ['วาฟเฟิลช็อกโกแลต', 129, 'fa-solid fa-cookie', 1],
    ['วาฟเฟิลสตรอว์เบอร์รี', 139, 'fa-solid fa-stroopwafel', 1],
    ['วาฟเฟิลคาราเมลกล้วย', 129, 'fa-solid fa-stroopwafel', 1],
    ['วาฟเฟิลบลูเบอร์รี่ครีมชีส', 139, 'fa-solid fa-ice-cream', 1],
    ['วาฟเฟิลพิสตาชิโอถั่ว', 129, 'fa-solid fa-stroopwafel', 0],
    ['วาฟเฟิลช็อกโกแลตอบราวนี่', 149, 'fa-solid fa-cookie-bite', 0],
    ['วาฟเฟิลทีรามิสุ', 139, 'fa-solid fa-cake-candles', 0],
    ['วาฟเฟิลเผือกครีม', 129, 'fa-solid fa-stroopwafel', 0],
    ['วาฟเฟิลโอรีโอ', 129, 'fa-solid fa-cookie', 0],
    ['วาฟเฟิลมัทฉะ', 129, 'fa-solid fa-stroopwafel', 0],
];
$drinks = [
    ['อเมริกาโน่เย็น', 89, 'fa-solid fa-mug-hot'],
    ['ลาเต้เย็น', 99, 'fa-solid fa-glass-water'],
    ['คาปูชิโน่เย็น', 99, 'fa-solid fa-mug-saucer'],
    ['มอคค่าเย็น', 109, 'fa-solid fa-wine-glass'],
    ['ชาไทยเย็น', 89, 'fa-solid fa-glass-water-droplet'],
    ['มัทฉะลาเต้เย็น', 109, 'fa-solid fa-mug-saucer'],
    ['สตรอว์เบอร์รี่สมูทตี้', 109, 'fa-solid fa-whiskey-glass'],
];

$stmt = $pdo->prepare("INSERT INTO products (category_id, name, description, price, icon, is_featured, sold_count) VALUES (?, ?, ?, ?, ?, ?, ?)");
foreach ($desserts as $d) {
    $stmt->execute([$dessertId, $d[0], 'วาฟเฟิลกรอบนอกนุ่มใน เสิร์ฟพร้อมไอศกรีมวานิลลาและท็อปปิ้งสูตรพิเศษ', $d[1], $d[2], $d[3], rand(30, 130)]);
}
foreach ($drinks as $d) {
    $stmt->execute([$drinkId, $d[0], 'เครื่องดื่มสูตรพิเศษของร้าน ชงสดใหม่ทุกแก้ว หอม กลมกล่อม', $d[1], $d[2], 0, rand(30, 130)]);
}

// ---------- 5. Insert Promotions ----------
$pdo->exec("INSERT INTO promotions (title, description, tag, icon) VALUES
    ('ลด 10% ทุกเมนู', 'เมื่อสั่งครบ 300 บาท', 'ยอดฮิต', 'fa-solid fa-fire'),
    ('ซื้อ 3 แถม 1', 'เฉพาะหมวดเครื่องดื่ม', 'คุ้มสุด', 'fa-solid fa-mug-hot'),
    ('ส่งฟรี', 'เมื่อสั่งครบ 500 บาท', 'ทั่วเมือง', 'fa-solid fa-truck-fast')");

// ---------- 6. Insert Admin ----------
$stmt = $pdo->prepare("INSERT INTO users (name, email, phone, password, role) VALUES (?, ?, ?, ?, 'admin')");
$stmt->execute(['Admin Waffle', 'admin@wafflehunger.com', '099-123-4567', password_hash('admin1234', PASSWORD_DEFAULT)]);

echo "<h1>✅ ปรับโครงสร้างข้อมูลแบบใช้ Professional Vector Icons เรียบร้อยแล้ว!</h1>";
echo "<a href='index.php'>กลับหน้าหลัก</a>";