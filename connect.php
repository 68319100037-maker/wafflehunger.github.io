<?php
/**
 * connect.php
 * ----------------------------------------------------
 * สร้างการเชื่อมต่อฐานข้อมูลด้วย PDO
 * ให้ include ไฟล์นี้ในทุกหน้าที่ต้องคุยกับฐานข้อมูล
 * ----------------------------------------------------
 */

require_once __DIR__ . '/db.php';

$dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (PDOException $e) {
    die('เชื่อมต่อฐานข้อมูลไม่สำเร็จ: ' . htmlspecialchars($e->getMessage()) .
        '<br>กรุณาตรวจสอบว่าได้สร้างฐานข้อมูลและรัน seed_data.php แล้ว');
}
