<?php
// ══════════════════════════════════════════════
// logout.php — GHOSN Platform
// ══════════════════════════════════════════════

session_start();

// مسح كل بيانات الجلسة
$_SESSION = [];

// مسح الـ session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(), '', time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// مسح الـ role cookie المستخدم في JS
setcookie('ghosn_role', '', time() - 3600, '/');

// إنهاء الجلسة
session_destroy();

// الرجوع لصفحة تسجيل الدخول
header('Location: login.php');
exit;
?>
