<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

$ADMIN_PW = 'mark2024!'; // ← 비밀번호 여기서 변경

$action = $_POST['action'] ?? '';

if ($action === 'login') {
    if (($_POST['password'] ?? '') === $ADMIN_PW) {
        $_SESSION['admin_logged_in'] = true;
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['error' => '비밀번호가 틀렸습니다.']);
    }
    exit;
}

if ($action === 'logout') {
    session_destroy();
    echo json_encode(['success' => true]);
    exit;
}

if ($action === 'check') {
    echo json_encode(['loggedIn' => isset($_SESSION['admin_logged_in'])]);
    exit;
}
