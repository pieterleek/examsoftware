<?php
session_start();
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Werk Inleveren</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
  <style>
    :root {
      --bg: #f0f4ff;
      --surface: #ffffff;
      --surface2: #f7f9ff;
      --border: #d8e0f5;
      --text: #141c38;
      --text-soft: #6370a0;
      --accent: #3a56e8;
      --accent-hover: #2941cc;
      --accent-light: #eef1fd;
      --success: #18b06a;
      --success-bg: #edfaf4;
      --danger: #e84444;
      --danger-bg: #fff0f0;
      --shadow: 0 8px 40px rgba(58,86,232,0.10);
    }

    * { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      font-family: "DM Sans", sans-serif;
      background: var(--bg);
      color: var(--text);
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      align-items: center;
      padding: 3rem 1rem 5rem;
      background-image:
        radial-gradient(circle at 0% 0%, #d6dffe 0%, transparent 45%),
        radial-gradient(circle at 100% 100%, #dff0e8 0%, transparent 40%);
    }

    header {
      width: min(680px, 100%);
      margin-bottom: 2.5rem;
      animation: slide-in 500ms ease-out;
    }

    .school-tag {
      display: inline-flex;
      align-items: center;
      gap: 0.4rem;
      background: var(--accent-light);
      color: var(--accent);
      font-size: 0.78rem;
      font-weight: 600;
      letter-spacing: 0.08em;
      text-transform: uppercase;
      padding: 0.35rem 0.85rem;
      border-radius: 999px;
      margin-bottom: 1rem;
      border: 1px solid #c5d0fa;
    }

    header h1 {
      font-family: "Syne", sans-serif;
      font-size: clamp(1.9rem, 5vw, 2.8rem);
      font-weight: 800;
      line-height: 1.1;
      letter-spacing: -0.02em;
    }

    header h1 span { color: var(--accent); }

    header p {
      margin-top: 0.6rem;
      color: var(--text-soft);
      font-size: 1rem;
    }

    .card {
      width: min(680px, 100%);
      background: var(--surface);
      border-radius: 24px;
      box-shadow: var(--shadow);
      padding: 2.5rem;
      border: 1px solid var(--border);
      animation: slide-in 550ms ease-out;
    }

    .section-label {
      font-size: 0.72rem;
      font-weight: 700;
      letter-spacing: 0.12em;
      text-transform: uppercase;
      color: var(--text-soft);
      margin-bottom: 1rem;
    }

    .fields-row {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 1rem;
      margin-bottom: 2rem;
    }

    .field {
      display: flex;
      flex-direction: column;
      gap: 0.45rem;
    }

    .field label {
      font-size: 0.88rem;
      font-weight: 600;
      color: var(--text);
    }

    .field input {
      border: 1.5px solid var(--border);
      border-radius: 12px;
      padding: 0.8rem 1rem;
      font: 500 0.95rem "DM Sans", sans-serif;
      color: var(--text);
      background: var(--surface2);
      transition: border-color 180ms, box-shadow 180ms;
    }

    .field input:focus {
      outline: none;
      border-color: var(--accent);
      box-shadow: 0 0 0 4px rgba(58,86,232,0.12);
      background: #fff;
    }

    .field input::placeholder { color: #aab2d0; }

    .divider {
      height: 1px;
      background: var(--border);
      margin: 0 -2.5rem 2rem;
    }

    .drop-zone {
      border: 2px dashed var(--border);
      border-radius: 16px;
      padding: 3rem 2rem;
      text-align: center;
      cursor: pointer;
      background: var(--surface2);
      transition: border-color 200ms, background 200ms, transform 150ms;
      position: relative;
    }

    .drop-zone:hover,
    .drop-zone.drag-over {
      border-color: var(--accent);
      background: var(--accent-light);
      transform: scale(1.005);
    }

    .drop-zone input[type="file"] {
      position: absolute;
      inset: 0;
      opacity: 0;
      cursor: pointer;
      width: 100%;
      height: 100%;
    }

    .drop-icon {
      width: 56px;
      height: 56px;
      background: var(--accent-light);
      border-radius: 16px;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 1rem;
      border: 1.5px solid #c5d0fa;
      transition: background 200ms;
    }

    .drop-zone:hover .drop-icon,
    .drop-zone.drag-over .drop-icon {
      background: #dde3fc;
    }

    .drop-icon svg { width: 26px; height: 26px; }

    .drop-zone h3 {
      font-family: "Syne", sans-serif;
      font-size: 1.05rem;
      font-weight: 700;
      margin-bottom: 0.3rem;
    }

    .drop-zone p {
      font-size: 0.88rem;
      color: var(--text-soft);
    }

    .drop-zone p strong {
      color: var(--accent);
      font-weight: 600;
    }

    .allowed-types {
      margin-top: 0.75rem;
      display: flex;
      justify-content: center;
      gap: 0.4rem;
      flex-wrap: wrap;
    }

    .type-badge {
      font-size: 0.73rem;
      font-weight: 600;
      padding: 0.2rem 0.6rem;
      border-radius: 6px;
      background: var(--border);
      color: var(--text-soft);
      letter-spacing: 0.04em;
    }

    .upload-note {
      margin-top: 0.9rem;
      font-size: 0.82rem;
      color: var(--text-soft);
    }

    #file-list {
      margin-top: 1.25rem;
      display: flex;
      flex-direction: column;
      gap: 0.55rem;
    }

    .file-item {
      display: flex;
      align-items: center;
      gap: 0.9rem;
      background: var(--surface2);
      border: 1.5px solid var(--border);
      border-radius: 12px;
      padding: 0.75rem 1rem;
      animation: pop-in 250ms cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    .file-icon {
      width: 38px;
      height: 38px;
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.1rem;
      flex-shrink: 0;
    }

    .file-info { flex: 1; min-width: 0; }

    .file-name {
      font-weight: 600;
      font-size: 0.9rem;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .file-size {
      font-size: 0.78rem;
      color: var(--text-soft);
      margin-top: 0.1rem;
    }

    .file-remove {
      background: none;
      border: none;
      cursor: pointer;
      color: #aab2d0;
      padding: 0.2rem;
      border-radius: 6px;
      display: flex;
      align-items: center;
      transition: color 150ms, background 150ms;
    }

    .file-remove:hover {
      color: var(--danger);
      background: var(--danger-bg);
    }

    .message {
      margin-bottom: 1.5rem;
      padding: 1rem 1.1rem;
      border-radius: 14px;
      font-size: 0.92rem;
      border: 1px solid;
    }

    .message.ok {
      background: var(--success-bg);
      border-color: #b0eed3;
      color: #0e7a4a;
    }

    .message.partial {
      background: #fff8e8;
      border-color: #f1d58a;
      color: #8d6700;
    }

    .message.error {
      background: var(--danger-bg);
      border-color: #f4b3b3;
      color: #b13030;
    }

    .submit-row {
      margin-top: 2rem;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 1rem;
      flex-wrap: wrap;
    }

    #file-count {
      font-size: 0.88rem;
      color: var(--text-soft);
      font-weight: 500;
    }

    #file-count span {
      color: var(--accent);
      font-weight: 700;
    }

    .btn-submit {
      border: 0;
      border-radius: 12px;
      padding: 0.9rem 2rem;
      background: var(--accent);
      color: #fff;
      font: 700 0.97rem "Syne", sans-serif;
      letter-spacing: 0.02em;
      cursor: pointer;
      display: flex;
      align-items: center;
      gap: 0.5rem;
      transition: background 180ms, transform 180ms, box-shadow 180ms;
      box-shadow: 0 4px 16px rgba(58,86,232,0.25);
    }

    .btn-submit:hover {
      background: var(--accent-hover);
      transform: translateY(-2px);
      box-shadow: 0 8px 24px rgba(58,86,232,0.32);
    }

    .btn-submit:active { transform: translateY(0); }

    .btn-submit:disabled {
      background: #b0bbdf;
      box-shadow: none;
      cursor: not-allowed;
      transform: none;
    }

    @keyframes slide-in {
      from { opacity: 0; transform: translateY(16px); }
      to   { opacity: 1; transform: translateY(0); }
    }

    @keyframes pop-in {
      from { opacity: 0; transform: scale(0.9); }
      to   { opacity: 1; transform: scale(1); }
    }

    @media (max-width: 560px) {
      .card { padding: 1.5rem; }
      .fields-row { grid-template-columns: 1fr; }
      .divider { margin: 0 -1.5rem 1.5rem; }
      .drop-zone { padding: 2rem 1rem; }
      .submit-row { flex-direction: column; align-items: stretch; }
      .btn-submit { justify-content: center; }
    }
  </style>
</head>
<body>

<header>
  <div class="school-tag">
    <svg width="10" height="10" viewBox="0 0 10 10">
      <circle cx="5" cy="5" r="4" fill="currentColor"/>
    </svg>
    Digitaal Inleveren
  </div>
  <h1>Lever je <span>werk in</span></h1>
  <p>Upload hier je codebestanden en lever je opdracht veilig in.</p>
</header>

<div class="card">

  <div id="status-message"></div>

  <form id="upload-form" action="upload.php" method="post" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
    <p class="section-label">Jouw gegevens</p>

    <div class="fields-row">
      <div class="field">
        <label for="naam">Naam</label>
        <input type="text" id="naam" name="naam" placeholder="Voor- en achternaam" required>
      </div>

      <div class="field">
        <label for="studentnummer">Studentnummer</label>
        <input type="text" id="studentnummer" name="studentnummer" placeholder="bijv. 10129229" required>
      </div>

      <div class="field">
        <label for="klas">Klas</label>
        <input type="text" id="klas" name="klas" placeholder="bijv. ICT-3A" required>
      </div>
    </div>

    <div class="divider"></div>

    <p class="section-label">Bestanden</p>

    <div class="drop-zone" id="drop-zone">
      <input
        type="file"
        name="bestanden[]"
        id="file-input"
        multiple
        accept=".php,.html,.css,.js,.env"
      >

      <div class="drop-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="var(--accent)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
          <polyline points="17 8 12 3 7 8"/>
          <line x1="12" y1="3" x2="12" y2="15"/>
        </svg>
      </div>

      <h3>Sleep bestanden hierheen</h3>
      <p>of <strong>klik om te bladeren</strong></p>

      <div class="allowed-types">
        <span class="type-badge">PHP</span>
        <span class="type-badge">HTML</span>
        <span class="type-badge">CSS</span>
        <span class="type-badge">JS</span>
        <span class="type-badge">ENV</span>
      </div>

      <p class="upload-note">Alleen .php, .html, .css, .js en .env bestanden toegestaan</p>
    </div>

    <div id="file-list"></div>

    <div class="submit-row">
      <p id="file-count">Geen bestanden geselecteerd</p>
      <button type="submit" class="btn-submit" id="submit-btn" disabled>
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
          <line x1="22" y1="2" x2="11" y2="13"/>
          <polygon points="22 2 15 22 11 13 2 9 22 2"/>
        </svg>
        Inleveren
      </button>
    </div>
  </form>
</div>

<script>
const dropZone = document.getElementById('drop-zone');
const fileInput = document.getElementById('file-input');
const fileList = document.getElementById('file-list');
const fileCount = document.getElementById('file-count');
const submitBtn = document.getElementById('submit-btn');
const form = document.getElementById('upload-form');
const statusMessage = document.getElementById('status-message');

let selectedFiles = [];

function showStatusMessage() {
  const params = new URLSearchParams(window.location.search);
  const status = params.get('status');
  const msg = params.get('msg');

  if (!msg) return;

  const div = document.createElement('div');
  div.className = 'message ' + (status || 'ok');
  div.textContent = msg;
  statusMessage.innerHTML = '';
  statusMessage.appendChild(div);
}

function fileStyle(name) {
  const lower = name.toLowerCase();

  if (lower === '.env') {
    return { icon: '⚙️', bg: '#eef6ff', color: '#2980e8' };
  }

  const ext = lower.split('.').pop();

  const map = {
    php:  { icon: '🐘', bg: '#f3eeff', color: '#7c3aed' },
    html: { icon: '🌐', bg: '#eefaf3', color: '#18b06a' },
    css:  { icon: '🎨', bg: '#fef3e8', color: '#e8830a' },
    js:   { icon: '⚡', bg: '#fffbe8', color: '#c9a000' },
    env:  { icon: '⚙️', bg: '#eef6ff', color: '#2980e8' }
  };

  return map[ext] || { icon: '📁', bg: '#f0f4ff', color: '#3a56e8' };
}

function formatSize(bytes) {
  if (bytes < 1024) return bytes + ' B';
  if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
  return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
}

function isAllowedFile(file) {
  const name = file.name.toLowerCase();

  if (name === '.env') {
    return true;
  }

  return /\.(php|html|css|js|env)$/i.test(name);
}

function syncFileInput() {
  const dt = new DataTransfer();
  selectedFiles.forEach(file => dt.items.add(file));
  fileInput.files = dt.files;
}

function renderFiles() {
  fileList.innerHTML = '';

  selectedFiles.forEach((file, i) => {
    const { icon, bg, color } = fileStyle(file.name);

    const item = document.createElement('div');
    item.className = 'file-item';
    item.innerHTML = `
      <div class="file-icon" style="background:${bg}; color:${color}">${icon}</div>
      <div class="file-info">
        <div class="file-name">${file.name}</div>
        <div class="file-size">${formatSize(file.size)}</div>
      </div>
      <button type="button" class="file-remove" data-i="${i}" title="Verwijder">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
          <line x1="18" y1="6" x2="6" y2="18"/>
          <line x1="6" y1="6" x2="18" y2="18"/>
        </svg>
      </button>
    `;
    fileList.appendChild(item);
  });

  const n = selectedFiles.length;
  fileCount.innerHTML = n === 0
    ? 'Geen bestanden geselecteerd'
    : `<span>${n}</span> bestand${n !== 1 ? 'en' : ''} geselecteerd`;

  submitBtn.disabled = n === 0;

  fileList.querySelectorAll('.file-remove').forEach(btn => {
    btn.addEventListener('click', () => {
      selectedFiles.splice(Number(btn.dataset.i), 1);
      syncFileInput();
      renderFiles();
    });
  });
}

function addFiles(newFiles) {
  let invalidFound = false;

  Array.from(newFiles).forEach(file => {
    if (!isAllowedFile(file)) {
      invalidFound = true;
      return;
    }

    const exists = selectedFiles.some(
      f => f.name === file.name && f.size === file.size && f.lastModified === file.lastModified
    );

    if (!exists) {
      selectedFiles.push(file);
    }
  });

  syncFileInput();
  renderFiles();

  if (invalidFound) {
    alert('Eén of meer bestanden zijn overgeslagen. Alleen .php, .html, .css, .js en .env zijn toegestaan.');
  }
}

fileInput.addEventListener('change', () => {
  selectedFiles = [];
  addFiles(fileInput.files);
});

dropZone.addEventListener('dragover', e => {
  e.preventDefault();
  dropZone.classList.add('drag-over');
});

dropZone.addEventListener('dragleave', () => {
  dropZone.classList.remove('drag-over');
});

dropZone.addEventListener('drop', e => {
  e.preventDefault();
  dropZone.classList.remove('drag-over');
  addFiles(e.dataTransfer.files);
});

form.addEventListener('submit', () => {
  submitBtn.disabled = true;
  submitBtn.innerHTML = '⏳ Verzenden…';
});

showStatusMessage();
</script>
</body>
</html>
