<?php $user = current_user(); ?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>see the world from my view | <?= e($title) ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
</head>
<body class="<?= !empty($home) ? 'home' : '' ?>" data-backend="php" data-about="about.php" data-index="index.php">
  <div id="top"></div>

  <header class="navbar">
    <a class="brand" href="index.php">🌸 see the world from my view</a>
    <nav class="nav-links">
      <a href="index.php"<?= $active === 'index' ? ' class="active"' : '' ?>>تسجيل الدخول</a>
      <a href="about.php"<?= $active === 'about' ? ' class="active"' : '' ?>>عن المتجر</a>
      <a href="pots.php"<?= $active === 'pots' ? ' class="active"' : '' ?>>الأصص</a>
      <a href="bouquets.php"<?= $active === 'bouquets' ? ' class="active"' : '' ?>>البوكيهات</a>
    </nav>
    <div class="nav-tools">
      <span id="userBox" class="user-box">
      <?php if ($user): ?>
        <span>أهلاً <?= e($user['name']) ?></span>
        <a href="orders.php">طلباتي</a>
        <a href="logout.php">خروج</a>
      <?php else: ?>
        <a href="index.php"<?= $active === 'index' ? ' class="active"' : '' ?>>دخول</a>
      <?php endif; ?>
      </span>
      <button class="icon-btn" id="themeBtn" type="button" title="الوضع الليلي">🌙</button>
      <button class="icon-btn" id="cartBtn" type="button" title="سلة المشتريات">🛒<span class="badge cart-count">0</span></button>
    </div>
  </header>
