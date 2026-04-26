<?php
$msg = $_GET['msg'] ?? '';
if ($msg) {
    file_put_contents('js_debug.txt', date('H:i:s') . ' - ' . $msg . "\n", FILE_APPEND);
}
