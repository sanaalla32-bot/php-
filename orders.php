<?php
require __DIR__ . '/config.php';

$user = current_user();
if (!$user) {
    flash('error', 'سجّلي الدخول لعرض طلباتك.');
    redirect('index.php');
}

$title = 'طلباتي';
$active = '';
$home = false;
$clearCart = isset($_GET['new']);          // بعد نجاح الطلب نفرّغ السلة في المتصفح

$stmt = $pdo->prepare('SELECT * FROM orders WHERE user_id = ? ORDER BY id DESC');
$stmt->execute([$user['id']]);
$orders = $stmt->fetchAll();
$itemsStmt = $pdo->prepare('SELECT * FROM order_items WHERE order_id = ?');

require __DIR__ . '/includes/header.php';
?>
<?php $f = get_flash(); if ($f): ?>
  <div class="flash flash-<?= e($f['type']) ?>"><?= e($f['msg']) ?></div>
<?php endif; ?>
  <main class="container">
    <section class="hero">
      <h2>📦 طلباتي</h2>
    </section>

    <?php if (!$orders): ?>
      <p class="empty">لا توجد طلبات بعد. <a href="pots.php">ابدئي التسوق</a></p>
    <?php endif; ?>

    <?php foreach ($orders as $o): ?>
      <?php $itemsStmt->execute([$o['id']]); $items = $itemsStmt->fetchAll(); ?>
      <div class="card order-box">
        <h3>طلب رقم #<?= (int)$o['id'] ?></h3>
        <p>التاريخ: <?= e($o['created_at']) ?><?php if ($o['delivery_date']): ?> | موعد التسليم: <?= e($o['delivery_date']) ?><?php endif; ?></p>
        <ul>
          <?php foreach ($items as $it): ?>
            <li><?= e($it['name']) ?> × <?= (int)$it['qty'] ?> — <?= number_format((int)$it['price'] * (int)$it['qty']) ?> جنيه</li>
          <?php endforeach; ?>
        </ul>
        <?php if ($o['extras']): ?><p>الإضافات: <?= e($o['extras']) ?></p><?php endif; ?>
        <p><b>الإجمالي: <?= number_format((int)$o['total']) ?> جنيه</b></p>
      </div>
    <?php endforeach; ?>
  </main>
<?php require __DIR__ . '/includes/footer.php'; ?>
