<?php
// ══════════════════════════════════════════════
// login.php — GHOSN Platform
// ══════════════════════════════════════════════

session_start();
require_once 'includes/connection.php';

$error   = '';
$success_msg = '';

// Show success message if redirected from signup
if (isset($_GET['registered'])) {
    $success_msg = 'Account created successfully! Please sign in.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email    = trim($_POST['email']    ?? '');
    $password = $_POST['password']      ?? '';

    // ── Validation ────────────────────────────
    if (!$email || !$password) {
        $error = 'Email and password are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email format.';
    } else {
        // ── Fetch user by email ────────────────
        $stmt = $conn->prepare(
            'SELECT User_ID, User_name, email, password FROM user WHERE email = ? LIMIT 1'
        );
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            $error = 'Invalid email or password.';
        } else {
            $user = $result->fetch_assoc();

            // ── Verify password ────────────────
            if (!password_verify($password, $user['password'])) {
                $error = 'Invalid email or password.';
            } else {
                // ── Determine role ─────────────
                $userId = $user['User_ID'];
                $role   = null;

                $resStmt = $conn->prepare('SELECT resident_ID FROM resident WHERE resident_ID = ? LIMIT 1');
                $resStmt->bind_param('s', $userId);
                $resStmt->execute();
                $resStmt->store_result();
                if ($resStmt->num_rows > 0) $role = 'resident';
                $resStmt->close();

                if (!$role) {
                    $volStmt = $conn->prepare('SELECT Volunteer_ID FROM volunteer WHERE Volunteer_ID = ? LIMIT 1');
                    $volStmt->bind_param('s', $userId);
                    $volStmt->execute();
                    $volStmt->store_result();
                    if ($volStmt->num_rows > 0) $role = 'volunteer';
                    $volStmt->close();
                }

                // ── Start session ──────────────
                $_SESSION['user_id']   = $userId;
                $_SESSION['user_name'] = $user['User_name'];
                $_SESSION['email']     = $user['email'];
                $_SESSION['role']      = $role ?? 'unknown';

                // ── Set role cookie for JS ──────
                // قابل للقراءة من JavaScript لأن httponly=false
                setcookie('ghosn_role', $role ?? 'unknown', 0, '/');

                $conn->close();

                // ── Redirect by role ───────────
                if ($role === 'volunteer') {
                    header('Location: volunteerProfile.html');
                } else {
                    header('Location: ghusn_home1.php');
                }
                exit;
            }
        }
        $stmt->close();
    }

    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1.0"/>
  <title>Sign In — غصن Platform</title>
  <link rel="stylesheet" href="shared.css"/>
  <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🌴</text></svg>"/>
  <style>
    body { background: var(--surface-1); min-height: 100vh; }

    .auth-layout {
      display: grid;
      grid-template-columns: 1fr 1fr;
      min-height: 100vh;
    }

    /* ── Left panel ── */
    .auth-panel {
      position: relative;
      background: linear-gradient(155deg, var(--g700) 0%, var(--g900) 60%, var(--g950) 100%);
      display: flex; flex-direction: column;
      justify-content: center;
      padding: 4rem 3.5rem;
      overflow: hidden;
    }
    .auth-panel::before {
      content: ''; position: absolute; inset: 0;
      background:
        radial-gradient(ellipse at 20% 30%, rgba(93,158,65,.22) 0%, transparent 55%),
        radial-gradient(ellipse at 80% 70%, rgba(168,216,120,.10) 0%, transparent 50%);
      pointer-events: none;
    }
    #auth-canvas { position: absolute; inset: 0; width: 100%; height: 100%; pointer-events: none; opacity: .55; }

    .auth-panel-content { position: relative; z-index: 1; }

    .auth-logo {
      display: inline-flex; align-items: center; gap: .65rem;
      text-decoration: none; margin-bottom: 3.5rem;
    }
    .auth-logo-icon {
      width: 42px; height: 42px;
      background: rgba(255,255,255,.15); border-radius: 50% 50% 50% 18%;
      display: flex; align-items: center; justify-content: center;
      border: 1px solid rgba(255,255,255,.2);
    }
    .auth-logo-icon svg { width: 22px; height: 22px; fill: white; }
    .auth-logo-name { font-family: 'Cinzel', serif; font-size: 1.55rem; font-weight: 400; color: white; letter-spacing: .07em; }
    .auth-logo-sub { font-size: .68rem; font-weight: 500; color: rgba(255,255,255,.5); letter-spacing: .07em; text-transform: uppercase; display: block; margin-top: -.2rem; }

    .auth-panel-quote {
      font-family: 'Fraunces', serif;
      font-size: clamp(1.55rem, 2.5vw, 2.1rem);
      font-weight: 300; font-style: italic;
      color: white; line-height: 1.38; letter-spacing: -.02em;
      margin-bottom: 2rem;
    }
    .auth-panel-quote strong { font-weight: 700; font-style: normal; color: var(--g100); }
    .auth-panel-source { font-size: .82rem; font-weight: 500; color: rgba(255,255,255,.45); margin-bottom: 3rem; letter-spacing: .04em; }

    .auth-stats { display: flex; flex-direction: column; gap: .8rem; }
    .auth-stat {
      display: flex; align-items: center; gap: 1rem;
      padding: .9rem 1.2rem;
      background: rgba(255,255,255,.07);
      border: 1px solid rgba(255,255,255,.10);
      border-radius: var(--r-md);
      backdrop-filter: blur(6px);
    }
    .auth-stat-ico { width: 36px; height: 36px; flex-shrink: 0; border-radius: var(--r-sm); background: rgba(168,216,120,.18); display: flex; align-items: center; justify-content: center; }
    .auth-stat-ico svg { width: 17px; height: 17px; stroke: var(--g100); fill: none; stroke-width: 1.9; }
    .auth-stat-val { font-family: 'Fraunces', serif; font-size: 1.1rem; font-weight: 700; color: white; line-height: 1; }
    .auth-stat-lbl { font-size: .75rem; color: rgba(255,255,255,.48); margin-top: .1rem; }

    /* ── Right panel (form) ── */
    .auth-form-side {
      display: flex; flex-direction: column;
      justify-content: center; align-items: center;
      padding: 4rem 3rem;
      background: var(--surface-0);
    }
    .auth-form-wrap { width: 100%; max-width: 420px; }

    .auth-back {
      display: inline-flex; align-items: center; gap: .45rem;
      font-size: .82rem; font-weight: 600; color: var(--n500);
      text-decoration: none; margin-bottom: 2.5rem; transition: color .25s;
    }
    .auth-back svg { width: 15px; height: 15px; stroke: currentColor; fill: none; stroke-width: 2; }
    .auth-back:hover { color: var(--g300); }

    .auth-form-title { font-family: 'Fraunces', serif; font-size: 2rem; font-weight: 700; color: var(--n900); letter-spacing: -.028em; margin-bottom: .3rem; }
    .auth-form-sub { font-size: .9rem; color: var(--n500); margin-bottom: 2.2rem; }

    .fg { margin-bottom: 1.15rem; }
    .fg label { display: block; font-size: .8rem; font-weight: 600; color: var(--n700); margin-bottom: .4rem; letter-spacing: .01em; }
    .input-wrap { position: relative; }
    .input-ico { position: absolute; left: .95rem; top: 50%; transform: translateY(-50%); pointer-events: none; }
    .input-ico svg { width: 16px; height: 16px; stroke: var(--n300); fill: none; stroke-width: 1.8; }
    .fg input {
      width: 100%; padding: .82rem .95rem .82rem 2.65rem;
      border: 1.5px solid var(--n100); border-radius: var(--r-sm);
      font-size: .93rem; font-family: 'DM Sans', sans-serif; color: var(--n900);
      background: white; outline: none;
      transition: border-color .3s, box-shadow .3s;
    }
    .fg input:focus { border-color: var(--g300); box-shadow: 0 0 0 3px rgba(93,158,65,.12); }
    .fg input::placeholder { color: var(--n100); }

    .btn-auth {
      width: 100%; padding: .96rem;
      border: none; border-radius: var(--r-sm);
      background: linear-gradient(135deg, var(--g300), var(--g500));
      color: white; font-size: 1rem; font-weight: 700;
      font-family: 'DM Sans', sans-serif; cursor: pointer;
      box-shadow: var(--sh-grn); letter-spacing: .01em;
      transition: all .35s ease; margin-bottom: 1.4rem;
    }
    .btn-auth:hover { transform: translateY(-2px); box-shadow: var(--sh-grn-lg); }
    .btn-auth:disabled { opacity: .7; cursor: not-allowed; transform: none; }

    .auth-divider { display: flex; align-items: center; gap: 1rem; margin-bottom: 1.4rem; }
    .auth-divider span { font-size: .78rem; color: var(--n300); white-space: nowrap; }
    .auth-divider::before, .auth-divider::after { content: ''; flex: 1; height: 1px; background: var(--n100); }

    .auth-switch { text-align: center; font-size: .87rem; color: var(--n500); }
    .auth-switch a { color: var(--g400); font-weight: 700; }

    .auth-error {
      display: none;
      background: #fef2f2; border: 1px solid #fca5a5;
      color: #b91c1c; border-radius: var(--r-sm);
      padding: .75rem 1rem; font-size: .85rem; margin-bottom: 1rem;
    }
    .auth-error.show { display: block; }

    .auth-success {
      display: none;
      background: #f0fdf4; border: 1px solid #86efac;
      color: #166534; border-radius: var(--r-sm);
      padding: .75rem 1rem; font-size: .85rem; margin-bottom: 1rem;
    }
    .auth-success.show { display: block; }

    @media (max-width: 900px) { .auth-layout { grid-template-columns: 1fr; } .auth-panel { display: none; } .auth-form-side { padding: 3rem 1.5rem; } }
  </style>
</head>
<body>

<div class="auth-layout">

  <!-- Left -->
  <div class="auth-panel">
    <canvas id="auth-canvas"></canvas>
    <div class="auth-panel-content">

      <a href="ghusn_home1.html" class="auth-logo">
        <div class="auth-logo-icon">
          <svg viewBox="0 0 24 24" fill="white"><path d="M13 22V12.07c1.5-.5 4-2.5 4-6.07a1 1 0 0 0-2 0c0 2.38-1.5 3.93-3 4.74V4a1 1 0 0 0-2 0v6.74C8.5 9.93 7 8.38 7 6a1 1 0 0 0-2 0c0 3.57 2.5 5.57 4 6.07V22a1 1 0 0 0 2 0z"/></svg>
        </div>
        <div>
          <span class="auth-logo-name">Ghosn</span>
          <span class="auth-logo-sub">GHOSN Platform</span>
        </div>
      </a>

      <p class="auth-panel-quote">
        "Every tree planted is a promise kept — to the land, to the next generation, and to <strong>ourselves.</strong>"
      </p>
      <p class="auth-panel-source">— غصن Environmental Platform</p>

      <div class="auth-stats">
        <div class="auth-stat">
          <div class="auth-stat-ico"><svg viewBox="0 0 24 24"><path d="M17 8C8 10 5.9 16.17 3.82 22H5.71C6.66 19.55 7.9 17.25 9.29 16C9.07 19.93 10.3 22 12 22c2 0 4-2.5 4-8"/></svg></div>
          <div><div class="auth-stat-val">125,000+</div><div class="auth-stat-lbl">Trees Planted</div></div>
        </div>
        <div class="auth-stat">
          <div class="auth-stat-ico"><svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></div>
          <div><div class="auth-stat-val">12,400+</div><div class="auth-stat-lbl">Community Volunteers</div></div>
        </div>
        <div class="auth-stat">
          <div class="auth-stat-ico"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10"/></svg></div>
          <div><div class="auth-stat-val">850 km²</div><div class="auth-stat-lbl">Areas Restored</div></div>
        </div>
      </div>

    </div>
  </div>

  <!-- Right (form) -->
  <div class="auth-form-side">
    <div class="auth-form-wrap">

      <h1 class="auth-form-title">Welcome back</h1>
      <p class="auth-form-sub">Sign in to your غصن account to continue.</p>

      <!-- Success message (after signup redirect) -->
      <div class="auth-success <?= $success_msg ? 'show' : '' ?>"><?= htmlspecialchars($success_msg) ?></div>

      <!-- Error banner -->
      <div class="auth-error <?= $error ? 'show' : '' ?>" id="login-error"><?= htmlspecialchars($error) ?></div>

      <!-- Form -->
      <form method="POST" action="login.php" id="login-form">

        <div class="fg">
          <label for="email">Email Address</label>
          <div class="input-wrap">
            <span class="input-ico"><svg viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg></span>
            <input type="email" id="email" name="email" placeholder="your@email.com" autocomplete="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"/>
          </div>
        </div>

        <div class="fg">
          <label for="password">Password</label>
          <div class="input-wrap">
            <span class="input-ico"><svg viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg></span>
            <input type="password" id="password" name="password" placeholder="Enter your password" autocomplete="current-password" required/>
            <span onclick="togglePw('password', this)" style="position:absolute;right:.9rem;top:50%;transform:translateY(-50%);cursor:pointer;color:var(--n300);">
              <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
            </span>
          </div>
        </div>

        <button type="submit" class="btn-auth" id="login-btn">Sign In</button>

      </form>

      <div class="auth-divider"><span>Don't have an account?</span></div>
      <div class="auth-switch"><a href="signup.php">Create a free account</a></div>

    </div>
  </div>

</div>

<script src="shared.js"></script>
<script>
// Auth canvas
(function(){
  const canvas = document.getElementById('auth-canvas');
  if (!canvas) return;
  const ctx = canvas.getContext('2d');
  let fr = 0;
  function resize(){ canvas.width = canvas.parentElement.offsetWidth || 600; canvas.height = canvas.parentElement.offsetHeight || 800; }
  resize(); window.addEventListener('resize', resize);
  const stars = Array.from({length:60},()=>({x:Math.random(),y:Math.random()*.65,r:Math.random()*1.6+.3,op:Math.random()*.5+.15,tw:Math.random()*Math.PI*2}));
  const leaves = Array.from({length:14},()=>({x:Math.random()*600,y:Math.random()*800,size:Math.random()*14+5,vx:(Math.random()-.5)*.25,vy:-Math.random()*.4-.1,rot:Math.random()*Math.PI*2,vr:(Math.random()-.5)*.012,op:Math.random()*.22+.06,hue:108+Math.random()*38}));
  function animate(){
    fr++;
    const w=canvas.width, h=canvas.height;
    ctx.clearRect(0,0,w,h);
    stars.forEach(s=>{ const tw=s.op+Math.sin(fr*.025+s.tw)*.11; ctx.fillStyle=`rgba(180,235,150,${tw})`; ctx.beginPath(); ctx.arc(s.x*w,s.y*h,s.r,0,Math.PI*2); ctx.fill(); });
    const mx=w*.8, my=h*.2, mr=h*.06;
    const mg=ctx.createRadialGradient(mx,my,0,mx,my,mr*2.8);
    mg.addColorStop(0,'rgba(210,250,185,.28)'); mg.addColorStop(1,'rgba(210,250,185,0)');
    ctx.fillStyle=mg; ctx.beginPath(); ctx.arc(mx,my,mr*2.8,0,Math.PI*2); ctx.fill();
    ctx.fillStyle='rgba(224,252,198,.88)'; ctx.beginPath(); ctx.arc(mx,my,mr,0,Math.PI*2); ctx.fill();
    ctx.fillStyle='rgba(10,26,10,.95)'; ctx.beginPath(); ctx.arc(mx+mr*.38,my-mr*.08,mr*.86,0,Math.PI*2); ctx.fill();
    leaves.forEach(l=>{
      ctx.save(); ctx.translate(l.x,l.y); ctx.rotate(l.rot);
      ctx.globalAlpha=l.op; ctx.fillStyle=`hsl(${l.hue},52%,42%)`;
      ctx.beginPath(); ctx.ellipse(0,0,l.size,l.size*.5,0,0,Math.PI*2); ctx.fill();
      ctx.restore();
      l.x+=l.vx+Math.sin(fr*.016+l.hue)*.18; l.y+=l.vy; l.rot+=l.vr;
      if(l.y<-20){l.y=canvas.height+20; l.x=Math.random()*canvas.width;}
    });
    requestAnimationFrame(animate);
  }
  animate();
})();

function togglePw(id, icon) {
  const input = document.getElementById(id);
  const isText = input.type === 'text';
  input.type = isText ? 'password' : 'text';
  icon.style.opacity = isText ? '1' : '0.4';
}

// Client-side validation before submit
document.getElementById('login-form').addEventListener('submit', function(e) {
  const err   = document.getElementById('login-error');
  const email = document.getElementById('email').value.trim();
  const pw    = document.getElementById('password').value;

  err.classList.remove('show');

  if (!email || !pw) {
    e.preventDefault();
    err.textContent = 'Please fill in all fields.';
    err.classList.add('show');
    return;
  }

  document.getElementById('login-btn').disabled    = true;
  document.getElementById('login-btn').textContent = 'Signing in…';
});
</script>
</body>
</html>
