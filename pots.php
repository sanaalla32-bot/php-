<?php
require __DIR__ . '/config.php';
$title = 'الأصص';
$active = 'pots';
$home = false;
require __DIR__ . '/includes/header.php';
?>
<?php $f = get_flash(); if ($f): ?>
  <div class="flash flash-<?= e($f['type']) ?>"><?= e($f['msg']) ?></div>
<?php endif; ?>
  <main class="container">

    <section class="hero reveal">
      <h2>🪴 أصص الزهور</h2>
      <p>تشكيلة من الأصص الطبيعية بتصاميم وأحجام مختلفة.</p>
    </section>

    <div class="strip reveal"><div class="strip-track" id="stripTrack" data-category="pots"></div></div>

    <section class="card toolbar reveal">
      <input type="search" id="search" placeholder="🔍 ابحثي عن منتج...">
      <select id="sizeFilter">
        <option value="">كل الأحجام</option>
        <option>صغير</option>
        <option>وسط</option>
        <option>كبير</option>
      </select>
      <select id="sortSel">
        <option value="">الترتيب الافتراضي</option>
        <option value="price-asc">السعر: الأقل أولاً</option>
        <option value="price-desc">السعر: الأعلى أولاً</option>
        <option value="name">حسب الاسم</option>
      </select>
    </section>

    <section class="grid" id="productGrid" data-category="pots"></section>
    <p class="empty" id="noResults" hidden>لا توجد منتجات مطابقة 🌸</p>

  </main>
<?php require __DIR__ . '/includes/footer.php'; ?>
