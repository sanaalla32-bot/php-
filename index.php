<?php
require __DIR__ . '/config.php';
$title = 'تسجيل الدخول';
$active = 'index';
$home = true;
require __DIR__ . '/includes/header.php';
?>
<?php $f = get_flash(); if ($f): ?>
  <div class="flash flash-<?= e($f['type']) ?>"><?= e($f['msg']) ?></div>
<?php endif; ?>
  <main class="container narrow">

    <section class="hero reveal">
      <h1 class="brand-title">see the world from my view</h1>
      <p class="slogan">نحن نصنع السعادة ونقدمها بأسلوب عصري</p>
      <p class="greeting" id="greeting"></p>
    </section>

    <section class="card reveal">
    <?php if ($user): ?>
    <div class="notice" style="margin-bottom:14px">أنتِ مسجلة الدخول باسم <b><?= e($user['name']) ?></b> — <a href="about.php">تابعي إلى المتجر</a></div>
    <?php endif; ?>
  <div class="tabs">
        <button type="button" class="tab active" data-tab="login">تسجيل الدخول</button>
        <button type="button" class="tab" data-tab="register">حساب جديد</button>
      </div>

      <div class="tab-panel active" id="tab-login">
        <form id="loginForm" method="post" action="login.php" novalidate>
          <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
        <div class="field">
          <label for="email">البريد الإلكتروني</label>
          <div class="input-wrap"><input type="email" id="email" name="email" autocomplete="email"></div>
          <div class="err"></div>
        </div>
        <div class="field">
          <label for="password">كلمة المرور</label>
          <div class="input-wrap"><input type="password" id="password" name="password" autocomplete="current-password"><button type="button" class="toggle-pass" data-target="password" title="إظهار/إخفاء">👁</button></div>
          <div class="err"></div>
        </div>
          <div id="loginMsg" class="form-msg"></div>
          <button class="btn btn-block" type="submit">دخول</button>
        </form>
      </div>

      <div class="tab-panel" id="tab-register">
        <form id="registerForm" method="post" action="register.php" novalidate>
          <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
        <div class="field">
          <label for="rname">الاسم الكامل</label>
          <div class="input-wrap"><input type="text" id="rname" name="name" ></div>
          <div class="err"></div>
        </div>
        <div class="field">
          <label for="remail">البريد الإلكتروني</label>
          <div class="input-wrap"><input type="email" id="remail" name="email" ></div>
          <div class="err"></div>
        </div>
        <div class="field">
          <label for="rphone">رقم الهاتف (اختياري)</label>
          <div class="input-wrap"><input type="tel" id="rphone" name="phone" ></div>
          <div class="err"></div>
        </div>
        <div class="field">
          <label for="rcity">المدينة</label>
          <select id="rcity" name="city"><option value="">اختاري المدينة</option><option>ود مدني</option><option>الخرطوم</option><option>أم درمان</option><option>بورتسودان</option><option>كسلا</option><option>أخرى</option></select>
          <div class="err"></div>
        </div>
        <div class="field">
          <label for="rpass">كلمة المرور (6 أحرف على الأقل)</label>
          <div class="input-wrap"><input type="password" id="rpass" name="password" ><button type="button" class="toggle-pass" data-target="rpass" title="إظهار/إخفاء">👁</button></div>
          <div class="err"></div>
        </div>
        <div class="field">
          <label for="rpass2">تأكيد كلمة المرور</label>
          <div class="input-wrap"><input type="password" id="rpass2" name="password2" ><button type="button" class="toggle-pass" data-target="rpass2" title="إظهار/إخفاء">👁</button></div>
          <div class="err"></div>
        </div>
        <div class="field">
          <label class="check"><input type="checkbox" id="terms" name="terms" value="1"> أوافق على شروط المتجر</label>
          <div class="err"></div>
        </div>
          <div id="registerMsg" class="form-msg"></div>
          <button class="btn btn-block" type="submit">إنشاء الحساب</button>
        </form>
      </div>
    </section>

  </main>
<?php require __DIR__ . '/includes/footer.php'; ?>
