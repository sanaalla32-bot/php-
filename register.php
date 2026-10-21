<?php
require __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('index.php');
}
if (!csrf_check()) {
    flash('error', 'انتهت صلاحية الجلسة، حاولي مرة أخرى.');
    redirect('index.php#register');
}

$name = trim($_POST['name'] ?? '');
$email = strtolower(trim($_POST['email'] ?? ''));
$phone = preg_replace('/[\s-]/', '', $_POST['phone'] ?? '');
$city = trim($_POST['city'] ?? '');
$password = $_POST['password'] ?? '';
$password2 = $_POST['password2'] ?? '';

$errors = [];
if (mb_strlen($name) < 3) {
    $errors[] = 'الاسم لا يقل عن 3 أحرف.';
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'البريد الإلكتروني غير صحيح.';
}
if ($phone !== '' && !preg_match('/^\+?[0-9]{9,15}$/', $phone)) {
    $errors[] = 'رقم الهاتف غير صحيح.';
}
if (strlen($password) < 6) {
    $errors[] = 'كلمة المرور لا تقل عن 6 أحرف.';
}
if ($password !== $password2) {
    $errors[] = 'كلمتا المرور غير متطابقتين.';
}
if (empty($_POST['terms'])) {
    $errors[] = 'يجب الموافقة على الشروط.';
}

if (!$errors) {
    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        $errors[] = 'هذا البريد مسجل من قبل.';
    }
}

if ($errors) {
    flash('error', $errors[0]);
    redirect('index.php#register');
}

$stmt = $pdo->prepare('INSERT INTO users (name, email, phone, city, password_hash) VALUES (?, ?, ?, ?, ?)');
$stmt->execute([$name, $email, $phone, $city, password_hash($password, PASSWORD_DEFAULT)]);

session_regenerate_id(true);
$_SESSION['user'] = ['id' => (int)$pdo->lastInsertId(), 'name' => $name, 'email' => $email];
flash('success', 'تم إنشاء حسابك بنجاح، أهلاً بك ' . $name . ' 🌸');
redirect('about.php');
