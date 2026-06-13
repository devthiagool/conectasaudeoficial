<?php
if (session_status() == PHP_SESSION_NONE) session_start();

function log_dir() {
    $d = __DIR__ . '/../logs';
    if (!is_dir($d)) mkdir($d, 0755, true);
    return $d;
}

function audit_log($action, $details = '') {
    $d = log_dir();
    $file = $d . '/audit.log';
    $user = $_SESSION['usuario_email'] ?? ($_SERVER['REMOTE_ADDR'] ?? 'system');
    $line = date('Y-m-d H:i:s') . " | " . $user . " | " . $action . " | " . $details . PHP_EOL;
    file_put_contents($file, $line, FILE_APPEND | LOCK_EX);
}

?>