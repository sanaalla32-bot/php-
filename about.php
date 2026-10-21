<?php
require __DIR__ . '/config.php';
$title = 'عن المتجر';
$active = 'about';
$home = false;
require __DIR__ . '/includes/header.php';
?>
<?php $f = get_flash(); if ($f): ?>
  <div class="flash flash-<?= e($f['type']) ?>"><?= e($f['msg']) ?></div>
<?php endif; ?>
  <main class="container">

    <section class="hero reveal">
      <h2>عن المتجر</h2>
      <p class="greeting" id="greeting"></p>
    </section>

    <section class="card reveal">
      <h2>🌸 نبذة عن المتجر</h2>
      <p>متجر زهور يجمع بين جمال الطبيعة والذوق العصري. نختار كل زهرة بعناية لتصل إليك طازجة وبتنسيق يعبّر عن مشاعرك في كل مناسبة.</p>
    </section>

    <section class="card reveal">
      <h2>✨ عن البراند</h2>
      <p><b>see the world from my view</b> فكرة بسيطة: لكل شخص طريقته في رؤية العالم، ونحن نترجمها إلى زهور.<br>شعارنا «نحن نصنع السعادة ونقدمها بأسلوب عصري».</p>
    </section>

    <section class="card reveal">
      <h2>💐 خدماتنا</h2>
      <h3>الأصص</h3>
      <p>أصص نباتات وزهور طبيعية بأحجام وتصاميم مختلفة، تناسب المنزل والمكتب وتصلح كهدية تدوم طويلاً.</p>
      <p><a class="btn btn-outline" href="pots.php">تصفّحي الأصص</a></p>
      <h3>البوكيهات</h3>
      <p>بوكيهات زهور منسقة بأسلوب عصري للمناسبات.</p>
      <p><a class="btn btn-outline" href="bouquets.php">تصفّحي البوكيهات</a></p>
      <h3>المناسبات التي نخدمها</h3>
      <ul>
        <li>أعياد الميلاد</li>
        <li>التخرج</li>
        <li>الخطوبة والزواج</li>
        <li>التهنئة والزيارات</li>
      </ul>
    </section>

    <section class="card reveal">
      <h2>📋 كيف تطلبين؟</h2>
      <ol>
        <li>اختاري منتجاتك من صفحة الأصص أو البوكيهات وأضيفيها للسلة.</li>
        <li>راجعي السلة واضغطي «إتمام الطلب».</li>
        <li>املئي نموذج الطلب أدناه.</li>
        <li>نتواصل معك لتأكيد الطلب ونسلّمه في الموعد.</li>
      </ol>
    </section>

    <section class="offer reveal" id="countdown">
      <h3>⏰ آخر موعد لطلبات اليوم الساعة 8 مساءً</h3>
      <div class="timer">
        <div><b id="cdS">00</b>ثانية</div>
        <div><b id="cdM">00</b>دقيقة</div>
        <div><b id="cdH">00</b>ساعة</div>
      </div>
    </section>

    <section class="card reveal">
      <h2>❓ الأسئلة الشائعة</h2>
      <details><summary>كم تستغرق مدة التوصيل؟</summary><p>من يوم إلى يومين حسب موقعك.</p></details>
      <details><summary>هل يمكن تخصيص البوكيه؟</summary><p>نعم، تختارين الزهور والألوان وتضيفين بطاقة إهداء.</p></details>
      <details><summary>كيف أعتني بالأصيص؟</summary><p>موقع مضيء بدون شمس مباشرة، وسقاية منتظمة حسب نوع النبات.</p></details>
    </section>

    <section class="card reveal" id="order">
      <h2>🛍️ اطلبي الآن</h2>
      <p class="notice" id="loginNote" hidden>للطلب، <a href="index.php">سجّلي الدخول</a> أو أنشئي حساباً أولاً.</p>
      <div class="summary" id="orderSummary"></div>
      <form id="orderForm" method="post" action="order.php" novalidate>
        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="cart" id="cartInput" value="[]">
        <div class="field">
          <label for="oname">الاسم</label>
          <div class="input-wrap"><input type="text" id="oname" name="name" ></div>
          <div class="err"></div>
        </div>
        <div class="field">
          <label for="ophone">رقم الهاتف</label>
          <div class="input-wrap"><input type="tel" id="ophone" name="phone" ></div>
          <div class="err"></div>
        </div>
        <div class="field">
          <label for="oaddress">العنوان</label>
          <textarea id="oaddress" name="address" rows="2"></textarea>
          <div class="err"></div>
        </div>
        <div class="field">
          <label for="odate">تاريخ التسليم</label>
          <div class="input-wrap"><input type="date" id="odate" name="date" ></div>
          <div class="err"></div>
        </div>
        <fieldset>
          <legend>إضافات</legend>
          <?php foreach (EXTRAS as $key => $x): ?>
          <label class="check"><input type="checkbox" name="extras[]" value="<?= e($key) ?>"> <?= e($x['name']) ?> (+<?= number_format($x['price']) ?> جنيه)</label>
          <?php endforeach; ?>
        </fieldset>
        <div class="field">
          <label for="onote">ملاحظات أو رسالة الإهداء</label>
          <textarea id="onote" name="note" rows="3"></textarea>
        </div>
        <div id="orderMsg" class="form-msg"></div>
        <button class="btn btn-block" type="submit">إرسال الطلب</button>
      </form>
    </section>

    <section class="card reveal">
      <h2>📍 موقع المتجر</h2>
      <!-- غيّري عنوان المكان في q= إلى موقع متجرك -->
      <iframe title="موقع المتجر" loading="lazy" src="https://www.google.com/maps?q=Wad+Madani&amp;output=embed"></iframe>
    </section>

  </main>
<?php require __DIR__ . '/includes/footer.php'; ?>
