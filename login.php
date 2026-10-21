<?php
require __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('index.php');
}
if (!csrf_check()) {
    flash('error', 'انتهت صلاحية الجلسة، حاولي مرة أخرى.');
    redirect('index.php');
}

$email = strtolower(trim($_POST['email'] ?? ''));
$password = $_POST['password'] ?? '';

if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
    flash('error', 'أدخلي البريد الإلكتروني وكلمة المرور بشكل صحيح.');
    redirect('index.php');
}

$stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
$stmt->execute([$email]);
$user = $stmt->fetch();

if ($user && password_verify($password, $user['password_hash'])) {
    session_regenerate_id(true);
    $_SESSION['user'] = ['id' => (int)$user['id'], 'name' => $user['name'], 'email' => $user['email']];
    flash('success', 'أهلاً بك ' . $user['name'] . ' 🌸');
    redirect('about.php');
}

flash('error', 'البريد الإلكتروني أو كلمة المرور غير صحيحة.');
redirect('index.php');
