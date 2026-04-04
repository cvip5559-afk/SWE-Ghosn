// ══════════════════════════════════════════════
// GHOSN — Shared JavaScript (all pages)
// ══════════════════════════════════════════════

document.addEventListener('DOMContentLoaded', () => {
  applyRoleNav();   // ← يشتغل أول شي قبل أي شيء آخر
  initNav();

  // ❌ امنع الطيور في صفحة السيرتش
  if (!document.body.classList.contains("no-birds")) {
    initGlobalBirds();
  }

  initScrollReveal();
  initFooterCanvas();
});

// ── Sign Out ──────────────────────────────────
function signOut() {
  localStorage.removeItem('userRole');
  window.location.href = 'login.html';
}

// ── Nav ───────────────────────────────────────
function initNav() {
  const nav = document.getElementById('mainNav');
  if (!nav) return;
  window.addEventListener('scroll', () => {
    nav.classList.toggle('solid', window.scrollY > 55);
  }, { passive: true });
}

function toggleNav() {
  const links = document.getElementById('nav-links');
  if (links) links.classList.toggle('open');
}

// ══════════════════════════════════════════════
// Role-based Nav — يشتغل على كل الصفحات
// ══════════════════════════════════════════════
function applyRoleNav() {
  var role = localStorage.getItem('userRole'); // 'resident' | 'volunteer'

  // ── حماية الصفحات ─────────────────────────
  var page = window.location.pathname.split('/').pop() || 'index.html';

  // صفحات Resident فقط
  var residentOnly = ['submit.html', 'residentProfile.html'];
  // صفحات Volunteer فقط
  var volunteerOnly = ['search.html', 'volunteerProfile.html'];

  if (residentOnly.indexOf(page) !== -1 && role !== 'resident') {
    // إذا ما عنده role أو Volunteer يحاول يدخل
    window.location.replace(role === 'volunteer' ? 'ghusn_home1.html' : 'login.html');
    return;
  }

  if (volunteerOnly.indexOf(page) !== -1 && role !== 'volunteer') {
    window.location.replace(role === 'resident' ? 'ghusn_home1.html' : 'login.html');
    return;
  }

  // ── Role Badge في الهيدر ───────────────────
  var navActions = document.querySelector('.nav-actions');
  if (navActions && role) {
    var existingBadge = document.getElementById('role-badge');
    if (!existingBadge) {
      var badge = document.createElement('span');
      badge.id = 'role-badge';
      badge.textContent = role === 'volunteer' ? '🌿 Volunteer' : '🏠 Resident';
      badge.style.cssText = [
        'display:inline-flex',
        'align-items:center',
        'padding:.28rem .75rem',
        'border-radius:999px',
        'font-size:.75rem',
        'font-weight:700',
        'letter-spacing:.04em',
        'background:' + (role === 'volunteer' ? 'rgba(93,158,65,.15)' : 'rgba(59,130,246,.13)'),
        'color:' + (role === 'volunteer' ? 'var(--g300,#6abf69)' : '#60a5fa'),
        'border:1px solid ' + (role === 'volunteer' ? 'rgba(93,158,65,.3)' : 'rgba(96,165,250,.3)'),
        'margin-right:.5rem',
        'white-space:nowrap'
      ].join(';');
      navActions.insertBefore(badge, navActions.firstChild);
    }
  }

  // ── إخفاء/إظهار روابط الهيدر ──────────────
  var reportEl = document.getElementById('nav-report');
  var searchEl = document.getElementById('nav-search');
  var profileEl = document.getElementById('nav-profile');

  // Volunteer → يشوف Search, Home, Profile فقط (يخفي Submit Report)
  if (role === 'volunteer') {
    if (reportEl && reportEl.parentElement) {
      reportEl.parentElement.style.display = 'none';
    }
  }

  // Resident → يشوف Submit Report, Home, Profile فقط (يخفي Search)
  if (role === 'resident') {
    if (searchEl && searchEl.parentElement) {
      searchEl.parentElement.style.display = 'none';
    }
  }

  // إذا ما في role → اخفي كل شيء ما عدا Home
  if (!role) {
    if (reportEl && reportEl.parentElement) reportEl.parentElement.style.display = 'none';
    if (searchEl && searchEl.parentElement) searchEl.parentElement.style.display = 'none';
  }

  // ── Profile → وجّه لصفحة البروفايل الصحيحة ──
  if (profileEl) {
    profileEl.addEventListener('click', function(e) {
      e.preventDefault();
      if (role === 'volunteer') {
        window.location.href = 'volunteerProfile.html';
      } else if (role === 'resident') {
        window.location.href = 'residentProfile.html';
      } else {
        window.location.href = 'login.html';
      }
    });
    profileEl.style.cursor = 'pointer';
    profileEl.removeAttribute('href'); // امنع الانتقال المباشر
  }
}


// ── Global Birds ──────────────────────────────
function initGlobalBirds() {
  const canvas = document.createElement('canvas');
  canvas.id = 'birds-canvas';
  document.body.appendChild(canvas);
  const ctx = canvas.getContext('2d');

  function resize() {
    canvas.width  = window.innerWidth;
    canvas.height = window.innerHeight;
  }
  resize();
  window.addEventListener('resize', resize);

  const birds = Array.from({ length: 28 }, (_, i) => ({
    x:          Math.random() * window.innerWidth,
    bandFrac:   Math.random(),
    vy:         (Math.random() - .5) * .09,
    vx:         (i % 2 === 0 ? 1 : -1) * (.42 + Math.random() * .72),
    wing:       Math.random() * Math.PI * 2,
    size:       7 + Math.random() * 17,
    drift:      Math.random() * 46 - 23,
    alpha:      .38 + Math.random() * .32,
  }));

  let scrollY = 0;
  window.addEventListener('scroll', () => { scrollY = window.scrollY; }, { passive: true });

  function animate() {
    const W = canvas.width, H = canvas.height;
    ctx.clearRect(0, 0, W, H);

    birds.forEach(b => {
      const pageH   = Math.max(document.body.scrollHeight, 1200);
      const screenY = b.bandFrac * pageH - scrollY + b.drift;

      b.x    += b.vx;
      b.wing += .088 + b.size * .002;
      b.drift += b.vy;
      if (b.drift >  44) b.vy = -Math.abs(b.vy);
      if (b.drift < -44) b.vy =  Math.abs(b.vy);
      if (b.x >  W + 100) b.x = -100;
      if (b.x < -100)     b.x =  W + 100;
      if (screenY < -90 || screenY > H + 90) return;

      const wf = Math.sin(b.wing) * b.size * .72;
      ctx.save();
      ctx.translate(b.x, screenY);
      if (b.vx < 0) ctx.scale(-1, 1);
      ctx.strokeStyle = `rgba(14,44,20,${b.alpha})`;
      ctx.lineWidth   = 1.6 + b.size * .04;
      ctx.lineCap     = 'round';
      ctx.beginPath(); ctx.moveTo(0, 0);
      ctx.quadraticCurveTo(-b.size * .62, -wf, -b.size * 1.2, -wf * .32); ctx.stroke();
      ctx.beginPath(); ctx.moveTo(0, 0);
      ctx.quadraticCurveTo( b.size * .62, -wf,  b.size * 1.2, -wf * .32); ctx.stroke();
      ctx.fillStyle = `rgba(14,44,20,${b.alpha * .65})`;
      ctx.beginPath(); ctx.arc(0, 0, 2.2, 0, Math.PI * 2); ctx.fill();
      ctx.restore();
    });
    requestAnimationFrame(animate);
  }
  animate();
}

// ── Scroll Reveal ─────────────────────────────
function initScrollReveal() {
  const sel = '.reveal,.reveal-l,.reveal-r,.reveal-u,.step,.joinus-card,.quote-card';
  const els = document.querySelectorAll(sel);
  const obs = new IntersectionObserver(entries => {
    entries.forEach(e => { if (e.isIntersecting) e.target.classList.add('in'); });
  }, { threshold: .10, rootMargin: '0px 0px -36px 0px' });
  els.forEach(el => obs.observe(el));
  document.querySelectorAll('.step').forEach((s, i) => s.style.transitionDelay = `${i * .12}s`);
  document.querySelectorAll('.joinus-card').forEach((c, i) => c.style.transitionDelay = `${i * .14}s`);
}

// ── Footer Sky Canvas ─────────────────────────
function initFooterCanvas() {
  const canvas = document.getElementById('footer-canvas');
  if (!canvas) return;
  const ctx = canvas.getContext('2d');
  let fr = 0;

  function resize() {
    const r = canvas.parentElement.getBoundingClientRect();
    canvas.width  = r.width  || 800;
    canvas.height = r.height || 110;
  }
  resize();

  const W = () => canvas.width, H = () => canvas.height;

  const stars = Array.from({ length: 55 }, () => ({
    x: Math.random(), y: Math.random() * .75,
    r: Math.random() * 1.6 + .3,
    op: Math.random() * .55 + .15,
    twinkleOffset: Math.random() * Math.PI * 2,
  }));

  function animate() {
    fr++;
    const w = W(), h = H();
    ctx.clearRect(0, 0, w, h);

    const sky = ctx.createLinearGradient(0, 0, 0, h);
    sky.addColorStop(0, '#060f06');
    sky.addColorStop(1, '#0a1a0a');
    ctx.fillStyle = sky;
    ctx.fillRect(0, 0, w, h);

    stars.forEach(s => {
      const twinkle = s.op + Math.sin(fr * .028 + s.twinkleOffset) * .12;
      ctx.fillStyle = `rgba(200,240,175,${twinkle})`;
      ctx.beginPath();
      ctx.arc(s.x * w, s.y * h, s.r, 0, Math.PI * 2);
      ctx.fill();
    });

    const mx = w * .85, my = h * .28, mr = h * .22;
    const moonGlow = ctx.createRadialGradient(mx, my, 0, mx, my, mr * 2.5);
    moonGlow.addColorStop(0, 'rgba(220,255,200,.18)');
    moonGlow.addColorStop(1, 'rgba(220,255,200,0)');
    ctx.fillStyle = moonGlow;
    ctx.beginPath(); ctx.arc(mx, my, mr * 2.5, 0, Math.PI * 2); ctx.fill();

    ctx.fillStyle = 'rgba(228,250,205,.92)';
    ctx.beginPath(); ctx.arc(mx, my, mr, 0, Math.PI * 2); ctx.fill();
    ctx.fillStyle = '#060f06';
    ctx.beginPath(); ctx.arc(mx + mr * .38, my - mr * .08, mr * .88, 0, Math.PI * 2); ctx.fill();

    const px = w * .72, py = h;
    const ph = h * .88;
    const ptx = px - ph * .12, pty = py - ph;

    ctx.strokeStyle = 'rgba(168,216,120,.55)';
    ctx.lineWidth = Math.max(2, ph * .044);
    ctx.lineCap = 'round';
    ctx.beginPath();
    ctx.moveTo(px, py);
    ctx.quadraticCurveTo(px - ph * .06, py - ph * .5, ptx, pty);
    ctx.stroke();

    const frondAngles = [-.7, -.35, 0, .35, .7, 1.0, -.05];
    frondAngles.forEach((angle, fi) => {
      const sw = Math.sin(fr * .018 + fi * .6) * .06;
      const fa  = angle + sw;
      const fl  = ph * .38;
      ctx.strokeStyle = `rgba(130,200,100,${.38 + fi * .04})`;
      ctx.lineWidth   = Math.max(1, ph * .016);
      ctx.beginPath();
      ctx.moveTo(ptx, pty);
      ctx.quadraticCurveTo(
        ptx + Math.cos(fa - .2) * fl * .5,
        pty + Math.sin(fa - .2) * fl * .28 + fl * .08,
        ptx + Math.cos(fa) * fl,
        pty + Math.sin(fa) * fl * .46 + fl * .18
      );
      ctx.stroke();
    });

    ctx.fillStyle = '#0a1a0a';
    ctx.fillRect(0, h * .82, w, h * .18);

    const hg = ctx.createLinearGradient(0, h * .75, 0, h * .85);
    hg.addColorStop(0, 'rgba(93,158,65,.12)');
    hg.addColorStop(1, 'rgba(93,158,65,0)');
    ctx.fillStyle = hg;
    ctx.fillRect(0, h * .75, w, h * .12);

    requestAnimationFrame(animate);
  }
  animate();
}

function updateStatus(select) {
  select.classList.remove("planned", "progress", "completed");
  const value = select.value;
  select.classList.add(value);
}
