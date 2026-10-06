/* ==========================================================
   QuickCheck — helper JavaScript bersama
   (tema, toast, modal, fetch JSON + CSRF, QR & scanner)
   ========================================================== */

/* ---------- Tema terang/gelap ---------- */
function applyTheme(t) {
  document.documentElement.setAttribute('data-theme', t);
  const i = document.querySelector('#themeBtn i');
  if (i) i.className = t === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
}
function toggleTheme() {
  const next = document.documentElement.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
  try { localStorage.setItem('qc-theme', next); } catch (e) {}
  applyTheme(next);
}
document.addEventListener('DOMContentLoaded', () => {
  applyTheme(document.documentElement.getAttribute('data-theme') || 'light');
});

/* ---------- Toast ---------- */
function toast(type, title, msg, ms = 4200) {
  const wrap = document.getElementById('toastWrap');
  if (!wrap) return;
  const icons = { success: 'fa-circle-check', error: 'fa-circle-xmark', warning: 'fa-triangle-exclamation' };
  const el = document.createElement('div');
  el.className = 'toast ' + type;
  const icon = document.createElement('i');
  icon.className = 'fas ' + (icons[type] || 'fa-circle-info');
  const body = document.createElement('div');
  const b = document.createElement('b'); b.textContent = title;
  body.appendChild(b);
  if (msg) { const s = document.createElement('span'); s.textContent = msg; body.appendChild(s); }
  el.append(icon, body);
  wrap.appendChild(el);
  setTimeout(() => el.remove(), ms);
}

/* ---------- Modal ---------- */
function openModal(id) { document.getElementById(id)?.classList.add('open'); }
function closeModal(id) { document.getElementById(id)?.classList.remove('open'); }
document.addEventListener('click', (e) => {
  if (e.target.classList?.contains('modal')) e.target.classList.remove('open');
});
document.addEventListener('keydown', (e) => {
  if (e.key === 'Escape') document.querySelectorAll('.modal.open').forEach((m) => m.classList.remove('open'));
});

/* ---------- Konfirmasi pada form berbahaya: <form data-confirm="Yakin?"> ---------- */
document.addEventListener('submit', (e) => {
  const msg = e.target.dataset?.confirm;
  if (msg && !window.confirm(msg)) e.preventDefault();
});

/* ---------- Kirim JSON dengan token CSRF Laravel ---------- */
async function postJson(url, data) {
  const token = document.querySelector('meta[name="csrf-token"]')?.content || '';
  const res = await fetch(url, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'X-CSRF-TOKEN': token,
      'X-Requested-With': 'XMLHttpRequest',
    },
    body: JSON.stringify(data),
    credentials: 'same-origin',
  });
  let json = {};
  try { json = await res.json(); } catch (e) {}
  // Error validasi Laravel (422) berbentuk {message, errors:{field:[..]}}
  if (!res.ok && !json.message) json.message = 'Terjadi kesalahan (' + res.status + ').';
  return { ok: res.ok, status: res.status, json };
}

/* ---------- QR Code (library qrcodejs) ---------- */
function renderQr(container, text, size = 260) {
  container.innerHTML = '';
  /* global QRCode */
  new QRCode(container, { text, width: size, height: size, correctLevel: QRCode.CorrectLevel.M });
}
function downloadQr(container, filename) {
  const canvas = container.querySelector('canvas');
  const img = container.querySelector('img');
  const href = canvas ? canvas.toDataURL('image/png') : img?.src;
  if (!href) return;
  const a = document.createElement('a');
  a.href = href; a.download = filename; a.click();
}

/* ---------- Pemindai QR (library html5-qrcode) ---------- */
function createScanner(elementId, onDecode) {
  let inst = null;
  let running = false;
  return {
    get running() { return running; },
    async start() {
      if (running) return;
      /* global Html5Qrcode */
      inst = inst || new Html5Qrcode(elementId);
      try {
        await inst.start(
          { facingMode: 'environment' },
          { fps: 10, qrbox: { width: 240, height: 240 } },
          (text) => onDecode(text),
          () => {}
        );
        running = true;
      } catch (err) {
        running = false;
        toast('error', 'Kamera tidak bisa dibuka', 'Izinkan akses kamera dan buka lewat HTTPS atau localhost.');
      }
    },
    async stop() {
      if (inst && running) {
        try { await inst.stop(); } catch (e) {}
        running = false;
      }
    },
  };
}
