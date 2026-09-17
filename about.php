<?php
require_once __DIR__ . '/connect.php';
require_once __DIR__ . '/page_template.php';

$page_title = 'เกี่ยวกับเรา';
render_header('about');
?>

<style>
  .page-header {
    text-align: center !important;
  }
  .about-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 50px;
    align-items: center;
    margin-bottom: 60px;
  }
  .about-img-box {
    width: 100%;
    height: 380px;
    border-radius: 20px;
    overflow: hidden;
    border: 1px solid rgba(212, 175, 55, 0.3);
    box-shadow: 0 15px 30px rgba(0,0,0,0.15);
  }
  .about-img-box img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }
  .values-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 30px;
    margin-top: 40px;
  }
  .value-card {
    background: #171310; /* การ์ดสีดำเข้ม */
    padding: 30px 20px;
    border-radius: 16px;
    text-align: center;
    border: 1px solid rgba(212, 175, 55, 0.2);
    box-shadow: 0 10px 25px rgba(0,0,0,0.08);
  }
  .value-card .icon-box {
    width: 70px;
    height: 70px;
    background: rgba(212, 175, 55, 0.15);
    color: #e6c374;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    margin: 0 auto 20px auto;
  }
  @media (max-width: 768px) {
    .about-grid, .values-grid { grid-template-columns: 1fr; }
    .about-img-box { height: 280px; }
  }
</style>

<!-- ส่วนหัวข้อหลัก จัดกึ่งกลางเรียบร้อย -->
<div class="page-header" style="text-align: center;">
  <div class="container">
    <h1><i class="fa-solid fa-store"></i> เกี่ยวกับ Waffle Hunger</h1>
    <p>เรื่องราวความแพสชันในการสร้างสรรค์วาฟเฟิลพรีเมียม</p>
  </div>
</div>

<section class="section section-cream">
  <div class="container">
    <div class="about-grid">
      <div class="about-img-box">
        <img src="https://images.unsplash.com/photo-1562376552-0d160a2f238d?auto=format&fit=crop&w=800&q=80" alt="เกี่ยวกับเรา Waffle Hunger">
      </div>
      <div>
        <span class="tag" style="background: #cf9a3f; color: #171310; padding: 6px 16px; border-radius: 20px; font-weight: bold; display: inline-block;">Our Story</span>
        
        <h2 style="font-size: 2.2rem; margin: 15px 0; color: #171310 !important;">จุดเริ่มต้นของความอร่อย</h2>
        
        <p style="color: #3a2e2b !important; line-height: 1.8; margin-bottom: 15px;">
          <strong style="color: #171310 !important;">Waffle Hunger</strong> ก่อตั้งขึ้นด้วยความตั้งใจที่อยากให้ทุกคนได้ลิ้มลองวาฟเฟิลสไตล์เบลเยียมแท้ๆ ที่ผสมผสานวัตถุดิบนำเข้าพรีเมียม อบสดใหม่ชิ้นต่อชิ้นด้วยความใส่ใจ
        </p>
        <p style="color: #554840 !important; line-height: 1.8;">
          เราคัดสรรทั้งเนยแท้จากฝรั่งเศส แป้งสูตรพิเศษที่ไม่ใส่สารกันเสีย และท็อปปิ้งผลไม้สดคุณภาพสูง เพื่อส่งมอบประสบการณ์ความอร่อยที่กรอบนอก นุ่มใน หอมฉ่ำในทุกๆ คำ
        </p>
      </div>
    </div>

    <!-- Core Values -->
    <div style="text-align: center; margin-top: 60px;">
      <h2 style="color: #171310 !important;"><i class="fa-solid fa-heart" style="color: #a9772a;"></i> หัวใจสำคัญของเรา</h2>
      
      <div class="values-grid">
        <div class="value-card">
          <div class="icon-box"><i class="fa-solid fa-wheat-awn"></i></div>
          <h3 style="color: #ffffff !important; margin-bottom: 10px;">วัตถุดิบพรีเมียม</h3>
          <p style="color: #d9cfc1 !important; font-size: 0.9rem;">คัดสรรเนยแท้ แป้งคุณภาพสูง ไม่ใส่สารกันเสีย ปลอดภัยต่อสุขภาพ</p>
        </div>
        <div class="value-card">
          <div class="icon-box"><i class="fa-solid fa-fire-burner"></i></div>
          <h3 style="color: #ffffff !important; margin-bottom: 10px;">อบสดใหม่ทุกออเดอร์</h3>
          <p style="color: #d9cfc1 !important; font-size: 0.9rem;">เพื่อรสชาติที่ดีที่สุด วาฟเฟิลทุกชิ้นจะถูกอบเมื่อมีคำสั่งซื้อเท่านั้น</p>
        </div>
        <div class="value-card">
          <div class="icon-box"><i class="fa-solid fa-face-smile-beam"></i></div>
          <h3 style="color: #ffffff !important; margin-bottom: 10px;">ใส่ใจในบริการ</h3>
          <p style="color: #d9cfc1 !important; font-size: 0.9rem;">จัดส่งรวดเร็ว คงความสดใหม่ ประทับใจในทุกการสั่งซื้อ</p>
        </div>
      </div>
    </div>
  </div>
</section>

<?php render_footer(); ?>