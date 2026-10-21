<?php
require __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('about.php#order');
}

$user = current_user();
if (!$user) {
    flash('error', 'سجّلي الدخول أولاً لإتمام الطلب.');
    redirect('index.php');
}
if (!csrf_check()) {
    flash('error', 'انتهت صلاحية الجلسة، حاولي مرة أخرى.');
    redirect('about.php#order');
}

$name = trim($_POST['name'] ?? '');
$phone = preg_replace('/[\s-]/', '', $_POST['phone'] ?? '');
$address = trim($_POST['address'] ?? '');
$date = trim($_POST['date'] ?? '');
$note = trim($_POST['note'] ?? '');
$cartRaw = json_decode($_POST['cart'] ?? '[]', true);
$extrasPosted = $_POST['extras'] ?? [];

$errors = [];
if (mb_strlen($name) < 3) {
    $errors[] = 'الاسم لا يقل عن 3 أحرف.';
}
if (!preg_match('/^\+?[0-9]{9,15}$/', $phone)) {
    $errors[] = 'رقم الهاتف غير صحيح.';
}
if (mb_strlen($address) < 5) {
    $errors[] = 'اكتبي عنواناً واضحاً.';
}
if ($date !== '') {
    $d = DateTime::createFromFormat('Y-m-d', $date);
    if (!$d || $d->format('Y-m-d') !== $date || $date < date('Y-m-d')) {
        $errors[] = 'تاريخ التسليم غير صحيح.';
    }
} else {
    $date = null;
}
if (mb_strlen($note) > 500) {
    $note = mb_substr($note, 0, 500);
}

// الأسعار تُقرأ دائماً من قاعدة البيانات (لا نثق بالأسعار القادمة من المتصفح)
$items = [];
$subtotal = 0;
if (is_array($cartRaw)) {
    $find = $pdo->prepare('SELECT id, name, price FROM products WHERE id = ?');
    foreach ($cartRaw as $row) {
        $pid = (int)($row['id'] ?? 0);
        $qty = (int)($row['qty'] ?? 0);
        if ($pid <= 0 || $qty < 1 || $qty > 50) {
            continue;
        }
        $find->execute([$pid]);
        $p = $find->fetch();
        if (!$p) {
            continue;
        }
        $items[] = ['id' => (int)$p['id'], 'name' => $p['name'], 'price' => (int)$p['price'], 'qty' => $qty];
        $subtotal += (int)$p['price'] * $qty;
    }
}
if (!$items) {
    $errors[] = 'السلة فارغة.';
}

$extrasChosen = [];
$extrasTotal = 0;
if (is_array($extrasPosted)) {
    foreach ($extrasPosted as $k) {
        if (is_string($k) && isset(EXTRAS[$k]) && !isset($extrasChosen[$k])) {
            $extrasChosen[$k] = EXTRAS[$k]['name'];
            $extrasTotal += EXTRAS[$k]['price'];
        }
    }
}

if ($errors) {
    flash('error', $errors[0]);
    redirect('about.php#order');
}

$total = $subtotal + $extrasTotal;

try {
    $pdo->beginTransaction();
    $ins = $pdo->prepare('INSERT INTO orders (user_id, customer_name, phone, address, delivery_date, note, extras, total)
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
    $ins->execute([$user['id'], $name, $phone, $address, $date, $note, implode('، ', $extrasChosen), $total]);
    $orderId = (int)$pdo->lastInsertId();

    $insItem = $pdo->prepare('INSERT INTO order_items (order_id, product_id, name, price, qty) VALUES (?, ?, ?, ?, ?)');
    foreach ($items as $it) {
        $insItem->execute([$orderId, $it['id'], $it['name'], $it['price'], $it['qty']]);
    }
    $pdo->commit();
} catch (PDOException $ex) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    flash('error', 'حدث خطأ أثناء حفظ الطلب، حاولي مرة أخرى.');
    redirect('about.php#order');
}

flash('success', 'تم استلام طلبك رقم #' . $orderId . ' ✅ سنتواصل معك قريباً.');
redirect('orders.php?new=1');
