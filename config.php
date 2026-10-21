<?php
// أثناء التطوير: إظهار الأخطاء بدل الصفحة البيضاء (احذفي السطرين بعد التسليم)
ini_set('display_errors', '1');
error_reporting(E_ALL);

// ============ إعدادات قاعدة البيانات (الإعدادات الافتراضية في XAMPP) ============
// 127.0.0.1 بدل localhost يتجنب خطأ "No such file or directory" في تطبيقات الجوال
const DB_HOST = '127.0.0.1';
const DB_USER = 'root';
const DB_PASS = '';
const DB_NAME = 'flower_shop';
// نوع قاعدة البيانات: 'auto' يجرّب MySQL أولاً، وإن لم يعمل يستخدم SQLite (ملف داخل المشروع، لا يحتاج أي إعداد)
// أو اكتبي 'mysql' أو 'sqlite' لتحديد النوع بنفسك.
const DB_DRIVER = 'auto';

// كلمة سر صفحة الإدارة (admin.php) — غيّريها قبل التسليم!
const ADMIN_PASS = 'admin123';
// على جهازك أو الجوال: true (ينشئ القاعدة تلقائياً).
// على الاستضافة: false (القاعدة تُنشأ من لوحة تحكم الاستضافة وتكتبين اسمها أعلاه).
const DB_CREATE = true;

// الإضافات المتاحة في الطلب (الأسعار تُحسب من هنا في الخادم)
const EXTRAS = [
    'card'     => ['name' => 'بطاقة إهداء',  'price' => 1000],
    'wrap'     => ['name' => 'تغليف هدية',   'price' => 3000],
    'delivery' => ['name' => 'توصيل للمنزل', 'price' => 5000],
];

if (session_status() === PHP_SESSION_NONE) {
    // لو مجلد الجلسات الافتراضي غير قابل للكتابة (يحدث في تطبيقات الجوال) نستخدم المجلد المؤقت
    $sp = (string)session_save_path();
    if (strpos($sp, ';') !== false) {
        $sp = substr($sp, strrpos($sp, ';') + 1);
    }
    if ($sp !== '' && (!is_dir($sp) || !is_writable($sp))) {
        session_save_path(sys_get_temp_dir());
    }
    session_start();
}

// ---------------- بدائل لو إضافة mbstring غير مفعّلة (مثل بعض تطبيقات الجوال) ----------------
if (!function_exists('mb_strlen')) {
    function mb_strlen($s)
    {
        return preg_match_all('/./us', (string)$s, $m);
    }
}
if (!function_exists('mb_substr')) {
    function mb_substr($s, $start, $length = null)
    {
        $chars = preg_split('//u', (string)$s, -1, PREG_SPLIT_NO_EMPTY);
        return implode('', array_slice($chars, $start, $length));
    }
}

// ---------------- دوال مساعدة ----------------
function e($s)
{
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}

function redirect($url)
{
    header('Location: ' . $url);
    exit;
}

function flash($type, $msg)
{
    $_SESSION['flash'] = ['type' => $type, 'msg' => $msg];
}

function get_flash()
{
    if (isset($_SESSION['flash'])) {
        $f = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $f;
    }
    return null;
}

function current_user()
{
    return isset($_SESSION['user']) ? $_SESSION['user'] : null;
}

function csrf_token()
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(16));
    }
    return $_SESSION['csrf'];
}

function csrf_check()
{
    return isset($_POST['csrf'], $_SESSION['csrf']) && hash_equals($_SESSION['csrf'], $_POST['csrf']);
}

function setup_tables(PDO $pdo, $driver)
{
    $pk = ($driver === 'sqlite') ? 'INTEGER PRIMARY KEY AUTOINCREMENT' : 'INT AUTO_INCREMENT PRIMARY KEY';
    $tail = ($driver === 'sqlite') ? '' : ' ENGINE=InnoDB DEFAULT CHARSET=utf8mb4';

    $pdo->exec('CREATE TABLE IF NOT EXISTS users (
        id ' . $pk . ',
        name VARCHAR(100) NOT NULL,
        email VARCHAR(150) NOT NULL UNIQUE,
        phone VARCHAR(30) DEFAULT NULL,
        city VARCHAR(60) DEFAULT NULL,
        password_hash VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )' . $tail);

    $pdo->exec('CREATE TABLE IF NOT EXISTS products (
        id ' . $pk . ',
        category VARCHAR(20) NOT NULL,
        name VARCHAR(150) NOT NULL,
        type VARCHAR(50) NOT NULL,
        size VARCHAR(30) NOT NULL,
        price INT NOT NULL,
        image VARCHAR(100) NOT NULL
    )' . $tail);

    $pdo->exec('CREATE TABLE IF NOT EXISTS orders (
        id ' . $pk . ',
        user_id INT NOT NULL,
        customer_name VARCHAR(100) NOT NULL,
        phone VARCHAR(30) NOT NULL,
        address VARCHAR(255) NOT NULL,
        delivery_date DATE DEFAULT NULL,
        note VARCHAR(500) DEFAULT NULL,
        extras VARCHAR(255) DEFAULT NULL,
        total INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )' . $tail);

    $pdo->exec('CREATE TABLE IF NOT EXISTS order_items (
        id ' . $pk . ',
        order_id INT NOT NULL,
        product_id INT NOT NULL,
        name VARCHAR(150) NOT NULL,
        price INT NOT NULL,
        qty INT NOT NULL
    )' . $tail);

    // مزامنة المنتجات: لو اختلف عددها عن القائمة أدناه يُعاد إدخالها (الطلبات القديمة تحتفظ بأسماء منتجاتها)
    $count = (int)$pdo->query('SELECT COUNT(*) FROM products')->fetchColumn();
    $seed = [
            [1, 'pots', 'نبتة هيبوستس في أصيص وردي', 'نبات', 'صغير', 8000, 'ases1'],
            [2, 'pots', 'أصيص ورد جوري أحمر', 'ورد', 'وسط', 15000, 'ases2'],
            [3, 'pots', 'نبتة خضراء في أصيص فخار', 'نبات', 'وسط', 10000, 'ases3'],
            [4, 'pots', 'أصيص كالانشو وردي', 'ورد', 'وسط', 12000, 'ases4'],
            [5, 'pots', 'أصيص ورد جوري كبير', 'ورد', 'كبير', 20000, 'ases5'],
            [6, 'pots', 'تشكيلة مزهريات بأشكال مختلفة', 'مزهرية', 'وسط', 7000, 'ases7'],
            [7, 'pots', 'مزهرية عباد الشمس', 'ورد', 'وسط', 9000, 'ases8'],
            [8, 'pots', 'مزهرية طويلة بأغصان وردية', 'ديكور', 'كبير', 25000, 'ases9'],
            [9, 'pots', 'مزهرية سوداء بأغصان الكرز', 'ديكور', 'وسط', 14000, 'ases10'],
            [10, 'pots', 'مزهرية مخططة بأغصان بيضاء', 'ديكور', 'وسط', 11000, 'ases11'],
            [11, 'bouquets', 'بوكيه ورد جوري أحمر', 'ورد', 'وسط', 25000, 'po'],
            [12, 'bouquets', 'بوكيه توليب أحمر', 'ورد', 'وسط', 22000, 'pokay1'],
            [13, 'bouquets', 'بوكيه ورد وردي وأصفر', 'ورد', 'وسط', 20000, 'pokay5'],
            [14, 'bouquets', 'بوكيه أوريو', 'هدية', 'وسط', 18000, 'pokay6'],
            [15, 'bouquets', 'وردة صفراء مغلفة', 'ورد', 'صغير', 5000, 'pokay7'],
            [16, 'bouquets', 'بوكيه سناكس وورد', 'هدية', 'كبير', 30000, 'pokay8'],
        ];
    if ($count !== count($seed)) {
        $pdo->exec('DELETE FROM products');
        $ins = $pdo->prepare('INSERT INTO products (id, category, name, type, size, price, image) VALUES (?, ?, ?, ?, ?, ?, ?)');
        foreach ($seed as $row) {
            $ins->execute($row);
        }
    }
}

// ---------------- الاتصال بقاعدة البيانات ----------------
function sqlite_file()
{
    $dir = __DIR__ . '/data';
    if (!is_dir($dir)) {
        @mkdir($dir, 0775, true);
    }
    if (!is_dir($dir) || !is_writable($dir)) {
        $dir = sys_get_temp_dir();      // لو المجلد غير قابل للكتابة
    }
    return $dir . '/flower_shop.sqlite';
}

$pdo = null;
$driver = '';
$dbErrors = [];
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

// 1) MySQL
if (DB_DRIVER !== 'sqlite') {
    if (!extension_loaded('pdo_mysql')) {
        $dbErrors[] = 'إضافة pdo_mysql غير مفعّلة.';
    } else {
        try {
            if (DB_CREATE) {
                $pdo = new PDO('mysql:host=' . DB_HOST . ';charset=utf8mb4', DB_USER, DB_PASS, $options);
                $pdo->exec('CREATE DATABASE IF NOT EXISTS `' . DB_NAME . '` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
                $pdo->exec('USE `' . DB_NAME . '`');
            } else {
                $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4', DB_USER, DB_PASS, $options);
            }
            $driver = 'mysql';
        } catch (Throwable $ex) {
            $pdo = null;
            $dbErrors[] = 'MySQL: ' . $ex->getMessage();
        }
    }
}

// 2) SQLite (احتياطي، أو إذا اخترتِه)
if (!$pdo && DB_DRIVER !== 'mysql') {
    if (!extension_loaded('pdo_sqlite')) {
        $dbErrors[] = 'إضافة pdo_sqlite غير مفعّلة.';
    } else {
        try {
            $pdo = new PDO('sqlite:' . sqlite_file(), null, null, $options);
            $driver = 'sqlite';
        } catch (Throwable $ex) {
            $pdo = null;
            $dbErrors[] = 'SQLite: ' . $ex->getMessage();
        }
    }
}

if (!$pdo) {
    die('<p dir="rtl" style="font-family:Tahoma">تعذر الاتصال بقاعدة البيانات.<br>'
        . 'شغّلي MySQL أو جرّبي نسخة PHP أخرى.<br><small>'
        . htmlspecialchars(implode(' | ', $dbErrors)) . '</small></p>');
}

try {
    setup_tables($pdo, $driver);
} catch (Throwable $ex) {
    die('<p dir="rtl" style="font-family:Tahoma">تعذر إنشاء جداول قاعدة البيانات.<br><small>'
        . htmlspecialchars($ex->getMessage()) . '</small></p>');
}
