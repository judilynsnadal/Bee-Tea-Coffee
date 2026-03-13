<?php
session_start();
header('Content-Type: application/json');

echo json_encode([
    'session_id' => session_id(),
    'user_id' => isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'NOT SET',
    'all_session_data' => $_SESSION
]);
?>
