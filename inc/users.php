<?php
if (session_status() == PHP_SESSION_NONE) session_start();

function users_file_path() {
    return __DIR__ . '/../usuarios.json';
}

function users_backup_dir() {
    $d = __DIR__ . '/../dados/backup';
    if (!is_dir($d)) mkdir($d, 0755, true);
    return $d;
}

function load_users() {
    $path = users_file_path();
    if (!file_exists($path)) return [];
    $content = file_get_contents($path);
    $users = json_decode($content, true);
    if (!is_array($users)) return [];
    return $users;
}

function save_users($users) {
    $path = users_file_path();
    // backup previous
    if (file_exists($path)) {
        $ts = date('Ymd_His');
        $backup = users_backup_dir() . '/usuarios_' . $ts . '.json';
        copy($path, $backup);
    }

    // atomic write with temp file and rename
    $tmp = $path . '.tmp';
    $fh = fopen($tmp, 'wb');
    if ($fh === false) return false;
    fwrite($fh, json_encode($users, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
    fclose($fh);
    // try rename
    return rename($tmp, $path);
}

function find_user_by_email($email) {
    $users = load_users();
    foreach ($users as $u) {
        if (isset($u['email']) && strtolower($u['email']) === strtolower($email)) return $u;
    }
    return null;
}

function find_user_by_id($id) {
    $users = load_users();
    foreach ($users as $u) {
        if (isset($u['id']) && $u['id'] === $id) return $u;
    }
    return null;
}

function update_user($id, $newData) {
    $users = load_users();
    foreach ($users as $i => $u) {
        if ($u['id'] === $id) {
            $users[$i] = array_merge($u, $newData);
            if (save_users($users)) return true;
            return false;
        }
    }
    return false;
}

function add_user($user) {
    $users = load_users();
    $users[] = $user;
    return save_users($users);
}

function delete_user($id) {
    $users = load_users();
    foreach ($users as $i => $u) {
        if ($u['id'] === $id) {
            array_splice($users, $i, 1);
            return save_users($users);
        }
    }
    return false;
}

?>