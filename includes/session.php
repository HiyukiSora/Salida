<?php
$sessionPath = sys_get_temp_dir() . '/sessions';
if (!is_dir($sessionPath)) {
    @mkdir($sessionPath, 0777, true);
}
session_save_path($sessionPath);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}