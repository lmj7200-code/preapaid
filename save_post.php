<?php
session_start();

// 비밀번호 확인
if (!isset($_SESSION['admin_logged_in'])) {
    http_response_code(401);
    echo json_encode(['error' => '로그인 필요']);
    exit;
}

header('Content-Type: application/json; charset=utf-8');

$POSTS_FILE = __DIR__ . '/posts.json';

function loadPosts($file) {
    if (!file_exists($file)) return [];
    $json = file_get_contents($file);
    return json_decode($json, true) ?: [];
}

function savePosts($file, $posts) {
    file_put_contents($file, json_encode(array_values($posts), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';

// 글 목록 조회
if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'list') {
    $posts = loadPosts($POSTS_FILE);
    echo json_encode($posts, JSON_UNESCAPED_UNICODE);
    exit;
}

// 글 저장 (신규 + 수정)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'save') {
    $posts = loadPosts($POSTS_FILE);
    $id      = intval($_POST['id'] ?? 0);
    $title   = trim($_POST['title'] ?? '');
    $cat     = trim($_POST['cat'] ?? '');
    $summary = trim($_POST['summary'] ?? '');
    $content = $_POST['content'] ?? '';
    $author  = '마크통신 선불폰';
    $date    = date('Y.m.d');

    if (!$title) {
        echo json_encode(['error' => '제목을 입력해주세요.']);
        exit;
    }

    if ($id > 0) {
        // 수정
        foreach ($posts as &$p) {
            if ($p['id'] === $id) {
                $p['title']   = $title;
                $p['cat']     = $cat;
                $p['summary'] = $summary;
                $p['content'] = $content;
                break;
            }
        }
    } else {
        // 신규
        $maxId = 0;
        foreach ($posts as $p) { if ($p['id'] > $maxId) $maxId = $p['id']; }
        $posts[] = [
            'id'      => $maxId + 1,
            'title'   => $title,
            'cat'     => $cat,
            'date'    => $date,
            'summary' => $summary,
            'content' => $content,
            'author'  => $author,
        ];
    }

    savePosts($POSTS_FILE, $posts);
    echo json_encode(['success' => true]);
    exit;
}

// 글 삭제
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'delete') {
    $posts = loadPosts($POSTS_FILE);
    $id    = intval($_POST['id'] ?? 0);
    $posts = array_filter($posts, fn($p) => $p['id'] !== $id);
    savePosts($POSTS_FILE, $posts);
    echo json_encode(['success' => true]);
    exit;
}

// 로그인
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'login') {
    $pw = $_POST['password'] ?? '';
    $ADMIN_PW = 'mark2024!'; // ← 비밀번호 여기서 변경
    if ($pw === $ADMIN_PW) {
        $_SESSION['admin_logged_in'] = true;
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['error' => '비밀번호가 틀렸습니다.']);
    }
    exit;
}

echo json_encode(['error' => '잘못된 요청']);
