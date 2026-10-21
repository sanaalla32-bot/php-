<?php
require __DIR__ . '/config.php';

if (isset($_GET['logout'])) {
    unset($_SESSION['is_admin']);
    redirect('admin.php');
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['admin_pass'])) {
    if (csrf_check() && hash_equals(ADMIN_PASS, (string)$_POST['admin_pass'])) {
        session_regenerate_id(true);
        $_SESSION['is_admin'] = true;
        redirect('admin.php');
    }
    $error = 'كلمة السر غير صحيحة.';
}

$isAdmin = !empty($_SESSION['is_admin']);
$title = 'لوحة الإدارة';
$active = '';
$home = false;
require __DIR__ . '/includes/header.php';
?>
  <main class="container">
    <section class="hero">
      <h2>🔐 لوحة الإدارة</h2>
    </section>

<?php if (!$isAdmin): ?>
    <section class="card" style="max-width:420px;margin-left:auto;margin-right:auto">
      <form method="post" action="admin.php">
        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
        <div class="field">
          <label for="ap">كلمة سر الإدارة</label>
          <input type="password" id="ap" name="admin_pass" required>
        </div>
        <?php if ($error): ?><div class="form-msg error"><?= e($error) ?></div><?php endif; ?>
        <button class="btn btn-block" type="submit">دخول</button>
      </form>
    </section>
<?php else: ?>
<?php
$users = $pdo->query('SELECT id, name, email, phone, city, created_at FROM users ORDER BY id DESC')->fetchAll();
$orders = $pdo->query('SELECT o.id, o.customer_name, o.phone, o.address, o.delivery_date, o.extras, o.total, o.created_at, u.email AS user_email
                       FROM orders o LEFT JOIN users u ON u.id = o.user_id ORDER BY o.id DESC')->fetchAll();
$itemsStmt = $pdo->prepare('SELECT name, qty, price FROM order_items WHERE order_id = ?');
?>
    <p><a href="admin.php?logout=1">تسجيل خروج الإدارة</a></p>

    <section class="card">
      <h2>👥 المسجّلون (<?= count($users) ?>)</h2>
      <?php if (!$users): ?><p class="empty">لا يوجد مسجّلون بعد.</p><?php else: ?>
      <div class="table-wrap">
        <table class="data-table">
          <tr><th>#</th><th>الاسم</th><th>البريد الإلكتروني</th><th>الهاتف</th><th>المدينة</th><th>تاريخ التسجيل</th></tr>
          <?php foreach ($users as $u): ?>
          <tr>
            <td><?= (int)$u['id'] ?></td>
            <td><?= e($u['name']) ?></td>
            <td><?= e($u['email']) ?></td>
            <td><?= e($u['phone']) ?></td>
            <td><?= e($u['city']) ?></td>
            <td><?= e($u['created_at']) ?></td>
          </tr>
          <?php endforeach; ?>
        </table>
      </div>
      <?php endif; ?>
    </section>

    <section class="card">
      <h2>🛍️ الطلبات (<?= count($orders) ?>)</h2>
      <?php if (!$orders): ?><p class="empty">لا توجد طلبات بعد.</p><?php else: ?>
      <div class="table-wrap">
        <table class="data-table">
          <tr><th>#</th><th>الاسم</th><th>الهاتف</th><th>العنوان</th><th>المنتجات</th><th>الإضافات</th><th>التسليم</th><th>الإجمالي</th><th>التاريخ</th></tr>
          <?php foreach ($orders as $o): ?>
          <?php $itemsStmt->execute([$o['id']]); $items = $itemsStmt->fetchAll(); ?>
          <tr>
            <td><?= (int)$o['id'] ?></td>
            <td><?= e($o['customer_name']) ?><br><small><?= e($o['user_email']) ?></small></td>
            <td><?= e($o['phone']) ?></td>
            <td><?= e($o['address']) ?></td>
            <td>
              <?php foreach ($items as $it): ?>
                <?= e($it['name']) ?> × <?= (int)$it['qty'] ?><br>
              <?php endforeach; ?>
            </td>
            <td><?= e($o['extras']) ?></td>
            <td><?= e($o['delivery_date']) ?></td>
            <td><?= number_format((int)$o['total']) ?> جنيه</td>
            <td><?= e($o['created_at']) ?></td>
          </tr>
          <?php endforeach; ?>
        </table>
      </div>
      <?php endif; ?>
    </section>
<?php endif; ?>
  </main>
<?php require __DIR__ . '/includes/footer.php'; ?>
