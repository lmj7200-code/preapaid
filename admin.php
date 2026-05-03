<?php session_start(); ?>
<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>관리자 | 마크통신 소식 글쓰기</title>
  <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+KR:wght@400;600;700;900&display=swap" rel="stylesheet" />
  <link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet" />
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'Noto Sans KR', sans-serif; background: #f0f4ff; color: #1a1a2e; }
    :root { --blue: #1a56db; --grad: linear-gradient(135deg,#4f8ef7,#1a56db,#6c2fd4); }

    /* 로그인 화면 */
    .login-wrap {
      min-height: 100vh; display: flex; align-items: center; justify-content: center;
    }
    .login-box {
      background: #fff; border-radius: 20px; padding: 48px 40px;
      box-shadow: 0 8px 40px rgba(79,142,247,.15); width: 100%; max-width: 380px;
      text-align: center;
    }
    .login-box h1 { font-size: 1.4rem; font-weight: 900; margin-bottom: 6px; }
    .login-box p { color: #888; font-size: .85rem; margin-bottom: 28px; }
    .login-box input {
      width: 100%; padding: 12px 16px; border-radius: 10px;
      border: 1.5px solid #ddd; font-size: .95rem; font-family: inherit;
      margin-bottom: 14px; outline: none; transition: border-color .2s;
    }
    .login-box input:focus { border-color: var(--blue); }
    .login-box button {
      width: 100%; padding: 13px; border-radius: 10px;
      background: var(--grad); color: #fff;
      font-weight: 700; font-size: 1rem; border: none; cursor: pointer;
      font-family: inherit;
    }
    .login-err { color: #e53e3e; font-size: .83rem; margin-top: 10px; }

    /* 관리자 레이아웃 */
    .admin-wrap { display: none; min-height: 100vh; }
    header {
      background: #fff; border-bottom: 1px solid #eef0f6;
      height: 62px; display: flex; align-items: center;
      padding: 0 32px; justify-content: space-between;
      box-shadow: 0 2px 12px rgba(79,142,247,.08);
      position: sticky; top: 0; z-index: 100;
    }
    .admin-logo {
      font-size: 1.1rem; font-weight: 900;
      background: var(--grad);
      -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
    }
    .header-btns { display: flex; gap: 10px; align-items: center; }
    .btn {
      padding: 8px 18px; border-radius: 8px; font-size: .85rem;
      font-weight: 700; cursor: pointer; font-family: inherit; border: none;
      transition: opacity .2s;
    }
    .btn:hover { opacity: .85; }
    .btn-primary { background: var(--grad); color: #fff; }
    .btn-outline { background: #fff; color: var(--blue); border: 1.5px solid var(--blue); }
    .btn-danger  { background: #fff; color: #e53e3e; border: 1.5px solid #e53e3e; }
    .btn-sm { padding: 5px 12px; font-size: .78rem; }

    .admin-body { display: flex; gap: 0; }

    /* 사이드바 */
    .sidebar {
      width: 280px; min-height: calc(100vh - 62px);
      background: #fff; border-right: 1px solid #eef0f6;
      padding: 24px 16px; flex-shrink: 0;
    }
    .sidebar h3 { font-size: .8rem; font-weight: 700; color: #aaa; letter-spacing: .1em; text-transform: uppercase; margin-bottom: 12px; padding: 0 8px; }
    .post-list-item {
      padding: 12px 10px; border-radius: 10px; cursor: pointer;
      transition: background .15s; margin-bottom: 4px;
      border: 1px solid transparent;
    }
    .post-list-item:hover { background: #f0f4ff; }
    .post-list-item.active { background: #e8f0fe; border-color: rgba(26,86,219,.2); }
    .post-list-item .pi-title { font-size: .88rem; font-weight: 700; color: #1a1a2e; line-height: 1.3; margin-bottom: 4px; }
    .post-list-item .pi-meta { font-size: .75rem; color: #aaa; display: flex; gap: 8px; }
    .pi-cat {
      display: inline-block; font-size: .68rem; font-weight: 700;
      padding: 2px 8px; border-radius: 10px;
    }
    .cat-개통가이드 { background:#e8f0fe; color:#1a56db; }
    .cat-선불폰    { background:#f3e8ff; color:#6c2fd4; }
    .cat-유심      { background:#e8fff3; color:#0a7c4a; }
    .cat-요금제    { background:#fff3e8; color:#b45309; }
    .cat-꿀팁      { background:#fce8ff; color:#a21caf; }

    .new-post-btn {
      width: 100%; padding: 11px; border-radius: 10px;
      background: var(--grad); color: #fff;
      font-weight: 700; font-size: .88rem; border: none; cursor: pointer;
      font-family: inherit; margin-bottom: 16px;
    }

    /* 에디터 영역 */
    .editor-area { flex: 1; padding: 32px; overflow-y: auto; }
    .editor-card {
      background: #fff; border-radius: 16px;
      box-shadow: 0 4px 24px rgba(79,142,247,.1);
      padding: 32px; max-width: 860px; margin: 0 auto;
    }
    .editor-card h2 { font-size: 1.1rem; font-weight: 900; margin-bottom: 24px; color: var(--blue); }
    .form-row { margin-bottom: 18px; }
    .form-row label { display: block; font-size: .82rem; font-weight: 700; color: #555; margin-bottom: 6px; }
    .form-row input, .form-row select, .form-row textarea {
      width: 100%; padding: 11px 14px; border-radius: 10px;
      border: 1.5px solid #ddd; font-size: .92rem; font-family: inherit;
      outline: none; transition: border-color .2s;
    }
    .form-row input:focus, .form-row select:focus, .form-row textarea:focus { border-color: var(--blue); }
    .form-row textarea { resize: vertical; min-height: 80px; }
    .form-2col { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }

    /* Quill 에디터 */
    .quill-wrap { border-radius: 10px; overflow: hidden; border: 1.5px solid #ddd; }
    .quill-wrap:focus-within { border-color: var(--blue); }
    #editor { min-height: 400px; font-size: .95rem; font-family: 'Noto Sans KR', sans-serif; }
    .ql-toolbar { border: none !important; border-bottom: 1px solid #eee !important; background: #fafafa; }
    .ql-container { border: none !important; }

    .form-actions { display: flex; gap: 10px; justify-content: flex-end; margin-top: 24px; }
    .toast {
      position: fixed; bottom: 32px; right: 32px; z-index: 9999;
      background: #1a56db; color: #fff; padding: 12px 24px; border-radius: 10px;
      font-weight: 600; font-size: .9rem;
      box-shadow: 0 4px 20px rgba(26,86,219,.3);
      display: none; animation: slideUp .3s ease;
    }
    @keyframes slideUp { from { opacity:0; transform:translateY(10px); } to { opacity:1; transform:translateY(0); } }

    .empty-sidebar { text-align: center; padding: 24px 8px; color: #ccc; font-size: .85rem; }
  </style>
</head>
<body>

  <!-- 로그인 화면 -->
  <div class="login-wrap" id="loginWrap">
    <div class="login-box">
      <h1>🔐 관리자 로그인</h1>
      <p>마크통신 소식 관리 페이지</p>
      <input type="password" id="pwInput" placeholder="비밀번호 입력" onkeydown="if(event.key==='Enter')doLogin()" />
      <button onclick="doLogin()">로그인</button>
      <div class="login-err" id="loginErr"></div>
    </div>
  </div>

  <!-- 관리자 화면 -->
  <div class="admin-wrap" id="adminWrap">
    <header>
      <div class="admin-logo">마크통신 · 소식 관리</div>
      <div class="header-btns">
        <a href="news.html" target="_blank" class="btn btn-outline">소식 페이지 보기</a>
        <button class="btn btn-outline" onclick="doLogout()">로그아웃</button>
      </div>
    </header>

    <div class="admin-body">
      <!-- 사이드바: 글 목록 -->
      <aside class="sidebar">
        <button class="new-post-btn" onclick="newPost()">＋ 새 글 작성</button>
        <h3>게시물 목록</h3>
        <div id="postListEl"></div>
      </aside>

      <!-- 에디터 -->
      <main class="editor-area">
        <div class="editor-card">
          <h2 id="editorTitle">새 글 작성</h2>
          <input type="hidden" id="postId" value="0" />

          <div class="form-2col">
            <div class="form-row">
              <label>카테고리</label>
              <select id="postCat">
                <option>개통가이드</option>
                <option>선불폰</option>
                <option>유심</option>
                <option>요금제</option>
                <option>꿀팁</option>
              </select>
            </div>
            <div class="form-row">
              <label>제목</label>
              <input type="text" id="postTitle" placeholder="글 제목을 입력하세요" />
            </div>
          </div>

          <div class="form-row">
            <label>요약 (목록에 표시되는 짧은 설명)</label>
            <textarea id="postSummary" placeholder="2~3줄 요약을 입력하세요"></textarea>
          </div>

          <div class="form-row">
            <label>본문</label>
            <div class="quill-wrap">
              <div id="editor"></div>
            </div>
          </div>

          <div class="form-actions">
            <button class="btn btn-outline" onclick="newPost()">초기화</button>
            <button class="btn btn-primary" onclick="savePost()">💾 저장하기</button>
          </div>
        </div>
      </main>
    </div>
  </div>

  <div class="toast" id="toast"></div>

  <script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
  <script>
    // Quill 에디터 초기화
    const quill = new Quill('#editor', {
      theme: 'snow',
      placeholder: '본문 내용을 작성하세요...',
      modules: {
        toolbar: [
          [{ header: [1, 2, 3, false] }],
          ['bold', 'italic', 'underline', 'strike'],
          [{ color: [] }, { background: [] }],
          [{ list: 'ordered' }, { list: 'bullet' }],
          [{ align: [] }],
          ['link', 'image'],
          ['blockquote', 'code-block'],
          ['clean']
        ]
      }
    });

    let posts = [];

    // 로그인 상태 확인
    fetch('login.php', { method:'POST', body: new URLSearchParams({ action:'check' }) })
      .then(r => r.json())
      .then(d => { if (d.loggedIn) showAdmin(); });

    function doLogin() {
      const pw = document.getElementById('pwInput').value;
      fetch('login.php', { method:'POST', body: new URLSearchParams({ action:'login', password: pw }) })
        .then(r => r.json())
        .then(d => {
          if (d.success) { showAdmin(); }
          else { document.getElementById('loginErr').textContent = d.error; }
        });
    }

    function doLogout() {
      fetch('login.php', { method:'POST', body: new URLSearchParams({ action:'logout' }) })
        .then(() => location.reload());
    }

    function showAdmin() {
      document.getElementById('loginWrap').style.display = 'none';
      document.getElementById('adminWrap').style.display = 'block';
      loadPosts();
    }

    function loadPosts() {
      fetch('save_post.php?action=list')
        .then(r => r.json())
        .then(data => {
          posts = data;
          renderSidebar();
        });
    }

    function renderSidebar() {
      const el = document.getElementById('postListEl');
      if (!posts.length) {
        el.innerHTML = '<div class="empty-sidebar">작성된 글이 없습니다</div>';
        return;
      }
      el.innerHTML = posts.slice().reverse().map(p => `
        <div class="post-list-item" onclick="loadPost(${p.id})" id="pi-${p.id}">
          <div class="pi-title">${p.title}</div>
          <div class="pi-meta">
            <span class="pi-cat cat-${p.cat}">${p.cat}</span>
            <span>${p.date}</span>
          </div>
        </div>
      `).join('');
    }

    function newPost() {
      document.getElementById('postId').value = '0';
      document.getElementById('postTitle').value = '';
      document.getElementById('postSummary').value = '';
      document.getElementById('postCat').value = '개통가이드';
      quill.root.innerHTML = '';
      document.getElementById('editorTitle').textContent = '새 글 작성';
      document.querySelectorAll('.post-list-item').forEach(el => el.classList.remove('active'));
    }

    function loadPost(id) {
      const p = posts.find(x => x.id === id);
      if (!p) return;
      document.getElementById('postId').value = p.id;
      document.getElementById('postTitle').value = p.title;
      document.getElementById('postSummary').value = p.summary;
      document.getElementById('postCat').value = p.cat;
      quill.root.innerHTML = p.content;
      document.getElementById('editorTitle').textContent = '글 수정';
      document.querySelectorAll('.post-list-item').forEach(el => el.classList.remove('active'));
      const el = document.getElementById('pi-' + id);
      if (el) el.classList.add('active');
    }

    function savePost() {
      const id      = document.getElementById('postId').value;
      const title   = document.getElementById('postTitle').value.trim();
      const cat     = document.getElementById('postCat').value;
      const summary = document.getElementById('postSummary').value.trim();
      const content = quill.root.innerHTML;

      if (!title) { showToast('제목을 입력해주세요!', true); return; }

      const data = new URLSearchParams({ action:'save', id, title, cat, summary, content });
      fetch('save_post.php', { method:'POST', body: data })
        .then(r => r.json())
        .then(d => {
          if (d.success) {
            showToast('저장되었습니다! ✓');
            loadPosts();
          } else {
            showToast(d.error || '오류가 발생했습니다.', true);
          }
        });
    }

    function deletePost() {
      const id = document.getElementById('postId').value;
      if (!id || id === '0') { showToast('삭제할 글을 선택해주세요.', true); return; }
      if (!confirm('이 글을 삭제하시겠습니까?')) return;
      fetch('save_post.php', { method:'POST', body: new URLSearchParams({ action:'delete', id }) })
        .then(r => r.json())
        .then(d => {
          if (d.success) { showToast('삭제되었습니다.'); newPost(); loadPosts(); }
        });
    }

    function showToast(msg, isErr = false) {
      const t = document.getElementById('toast');
      t.textContent = msg;
      t.style.background = isErr ? '#e53e3e' : '#1a56db';
      t.style.display = 'block';
      setTimeout(() => { t.style.display = 'none'; }, 2500);
    }
  </script>
</body>
</html>
