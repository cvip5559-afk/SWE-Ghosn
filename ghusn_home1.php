<?php
// ══════════════════════════════════════════════
// home.php — GHOSN Platform
// ══════════════════════════════════════════════
session_start();

// حماية الصفحة — إذا ما في session يرجع لـ login
if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$role     = $_SESSION['role']     ?? 'unknown';
$userName = $_SESSION['user_name'] ?? 'User';
$userId   = $_SESSION['user_id']   ?? '';

// رابط البروفايل حسب الـ role
$profileHref = ($role === 'volunteer') ? 'volunteerProfile.html' : 'residentProfile.html';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Ghosn — Desert Restoration Project</title>
<meta name="description" content="Transforming desert land into thriving green ecosystems.">
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;0,700;1,300;1,600&family=Outfit:wght@200;300;400;500;600&family=Space+Mono:ital@0;1&display=swap" rel="stylesheet">
<link rel="stylesheet"  href="shared.css"> 
<style>
/* ============================================================
   OASIS — Global Styles
   Desert Restoration Project
   ============================================================ */

@import url('https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;0,700;1,300;1,600&family=Outfit:wght@200;300;400;500;600&family=Space+Mono:ital@0;1&display=swap');

/* ── TOKENS ── */
:root {
  /* Desert palette */
  --sand-deep:    #1a0e05;
  --sand-dark:    #2d1a08;
  --sand-mid:     #8b5e2a;
  --sand-warm:    #c9924a;
  --sand-light:   #e8c478;
  --sand-pale:    #f5dea3;

  /* Sunset */
  --sunset-red:   #c0392b;
  --sunset-orange:#e67e22;
  --sunset-amber: #f39c12;
  --horizon:      #ff6b35;

  /* Oasis greens */
  --oasis-deep:   #0a1f0a;
  --oasis-dark:   #1a3d1a;
  --oasis-mid:    #2d7a2d;
  --oasis-bright: #4caf50;
  --oasis-light:  #81c784;
  --oasis-neon:   #a5d6a7;
  --palm:         #33691e;
  --leaf:         #558b2f;

  /* Water */
  --water-deep:   #0d47a1;
  --water-mid:    #1565c0;
  --water-light:  #42a5f5;
  --water-shimmer:#80deea;

  /* UI */
  --white:        #fdf9f0;
  --cream:        #f8f0d8;
  --text-warm:    rgba(248,240,216,.85);
  --text-muted:   rgba(248,240,216,.45);

  /* Type */
  --font-display: 'Cormorant Garamond', serif;
  --font-body:    'Outfit', sans-serif;
  --font-mono:    'Space Mono', monospace;

  /* Easing */
  --ease-out:  cubic-bezier(.16,1,.3,1);
  --ease-in:   cubic-bezier(.7,0,.84,0);
  --ease-circ: cubic-bezier(.785,.135,.15,.86);
}

/* ── RESET ── */
*, *::before, *::after { margin:0; padding:0; box-sizing:border-box; }
html { scroll-behavior: smooth; font-size: 16px; }
body {
  font-family: var(--font-body);
  background: var(--sand-deep);
  color: var(--white);
  overflow-x: hidden;
  -webkit-font-smoothing: antialiased;
}
img, canvas { display: block; max-width: 100%; }
a { text-decoration: none; color: inherit; }
button { font-family: var(--font-body); cursor: pointer; border: none; outline: none; }
input, textarea { font-family: var(--font-body); outline: none; border: none; }

/* ── CUSTOM CURSOR ── */
#cursor-dot {
  position: fixed; width: 8px; height: 8px;
  background: var(--oasis-neon); border-radius: 50%;
  pointer-events: none; z-index: 9999;
  transform: translate(-50%,-50%);
  transition: transform .1s, background .3s;
  mix-blend-mode: difference;
}
#cursor-ring {
  position: fixed; width: 40px; height: 40px;
  border: 1px solid rgba(165,214,167,.4); border-radius: 50%;
  pointer-events: none; z-index: 9998;
  transform: translate(-50%,-50%);
  transition: all .3s var(--ease-out);
}
body:has(a:hover) #cursor-dot,
body:has(button:hover) #cursor-dot { transform: translate(-50%,-50%) scale(3); }
body:has(a:hover) #cursor-ring,
body:has(button:hover) #cursor-ring { width: 20px; height: 20px; border-color: var(--oasis-bright); }

/* ── SCROLLBAR ── */
::-webkit-scrollbar { width: 3px; }
::-webkit-scrollbar-track { background: var(--sand-deep); }
::-webkit-scrollbar-thumb { background: var(--oasis-mid); border-radius: 2px; }

/* ── UTILITIES ── */
.sr-only { position:absolute; width:1px; height:1px; overflow:hidden; clip:rect(0,0,0,0); }
.grain {
  position: fixed; inset: 0; pointer-events: none; z-index: 9997; opacity: .03;
  background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)'/%3E%3C/svg%3E");
  background-size: 200px;
  animation: grain 8s steps(10) infinite;
}
@keyframes grain {
  0%,100%{transform:translate(0,0)}
  10%{transform:translate(-2%,-3%)}
  20%{transform:translate(3%,1%)}
  30%{transform:translate(-1%,4%)}
  40%{transform:translate(4%,-2%)}
  50%{transform:translate(-3%,3%)}
  60%{transform:translate(2%,-4%)}
  70%{transform:translate(-4%,2%)}
  80%{transform:translate(3%,-1%)}
  90%{transform:translate(-2%,4%)}
}

/* ── SCROLL REVEAL BASE ── */
.reveal { opacity: 0; transform: translateY(60px); transition: opacity .9s var(--ease-out), transform .9s var(--ease-out); }
.reveal.visible { opacity: 1; transform: translateY(0); }
.reveal-left { opacity: 0; transform: translateX(-80px); transition: opacity .9s var(--ease-out), transform .9s var(--ease-out); }
.reveal-left.visible { opacity: 1; transform: translateX(0); }
.reveal-right { opacity: 0; transform: translateX(80px); transition: opacity .9s var(--ease-out), transform .9s var(--ease-out); }
.reveal-right.visible { opacity: 1; transform: translateX(0); }
.reveal-scale { opacity: 0; transform: scale(.85); transition: opacity .9s var(--ease-out), transform .9s var(--ease-out); }
.reveal-scale.visible { opacity: 1; transform: scale(1); }
.delay-1 { transition-delay: .1s; }
.delay-2 { transition-delay: .2s; }
.delay-3 { transition-delay: .3s; }
.delay-4 { transition-delay: .4s; }
.delay-5 { transition-delay: .5s; }
.delay-6 { transition-delay: .6s; }

/* ============================================================
   OASIS — Hero & Page-Specific Styles
   ============================================================ */


/* ── HERO ── */
#hero {
  position: relative; height: 100vh; width: 100%;
  overflow: hidden; display: flex; align-items: center; justify-content: center;
}
#heroCanvas {
  position: absolute; inset: 0; width: 100%; height: 100%;
}
.hero-content {
  position: relative; z-index: 10;
  text-align: center; pointer-events: none;
}
.hero-eyebrow {
  font-family: var(--font-mono);
  font-size: .7rem; letter-spacing: .3em; text-transform: uppercase;
  color: var(--oasis-light); margin-bottom: 1.5rem;
  opacity: 0; animation: fadeSlideUp .8s .5s var(--ease-out) forwards;
}
.hero-title {
  font-family: var(--font-display);
  font-size: clamp(4rem, 12vw, 11rem);
  font-weight: 300; line-height: .9; letter-spacing: -.02em;
  opacity: 0; animation: fadeSlideUp .8s .7s var(--ease-out) forwards;
}
.hero-title .line1 { display: block; color: var(--sand-pale); }
.hero-title .line2 {
  display: block; font-style: italic; font-weight: 600;
  background: linear-gradient(135deg, var(--oasis-light), var(--oasis-neon), var(--water-shimmer));
  -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
}
.hero-title .line3 { display: block; color: rgba(248,240,216,.7); font-size: .45em; font-weight: 400; margin-top: .3em; letter-spacing: .12em; text-transform: uppercase; font-family: var(--font-mono); -webkit-text-fill-color: rgba(248,240,216,.7); }
.hero-subtitle {
  font-size: 1rem; line-height: 1.7; color: var(--text-muted);
  max-width: 440px; margin: 2rem auto;
  opacity: 0; animation: fadeSlideUp .8s .9s var(--ease-out) forwards;
}
.hero-ctas {
  display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;
  opacity: 0; animation: fadeSlideUp .8s 1.1s var(--ease-out) forwards;
  pointer-events: all;
}
.btn-hero-primary {
  padding: 1rem 2.8rem; border-radius: 4px;
  background: linear-gradient(135deg, var(--oasis-mid), var(--oasis-bright));
  color: var(--white); font-size: .9rem; font-weight: 600; letter-spacing: .08em;
  box-shadow: 0 0 40px rgba(76,175,80,.35);
  transition: all .4s var(--ease-out); text-transform: uppercase;
}
.btn-hero-primary:hover { transform: translateY(-3px); box-shadow: 0 0 60px rgba(76,175,80,.5); }
.btn-hero-secondary {
  padding: 1rem 2.8rem; border-radius: 4px;
  background: rgba(248,240,216,.06); border: 1px solid rgba(248,240,216,.2);
  color: var(--cream); font-size: .9rem; font-weight: 400; letter-spacing: .08em;
  transition: all .3s; text-transform: uppercase;
}
.btn-hero-secondary:hover { background: rgba(248,240,216,.12); border-color: rgba(248,240,216,.4); transform: translateY(-2px); }

/* Hero scroll indicator */
.hero-scroll {
  position: absolute; bottom: 2.5rem; left: 50%; transform: translateX(-50%);
  display: flex; flex-direction: column; align-items: center; gap: .5rem;
  opacity: 0; animation: fadeSlideUp .8s 1.6s var(--ease-out) forwards;
  z-index: 10;
}
.hero-scroll span { font-family: var(--font-mono); font-size: .6rem; letter-spacing: .2em; color: var(--text-muted); text-transform: uppercase; }
.scroll-track {
  width: 1px; height: 60px;
  background: linear-gradient(to bottom, var(--oasis-light), transparent);
  animation: scrollTrack 2s ease infinite;
}
@keyframes scrollTrack {
  0% { transform: scaleY(0); transform-origin: top; opacity: 1; }
  50% { transform: scaleY(1); transform-origin: top; opacity: 1; }
  51% { transform-origin: bottom; }
  100% { transform: scaleY(0); transform-origin: bottom; opacity: 0; }
}

/* ── SECTIONS ── */
section { position: relative; }

/* ABOUT */
#about {
  padding: 12rem 6rem;
  background: linear-gradient(180deg, var(--sand-deep) 0%, #0d1a08 100%);
  overflow: hidden;
}
.about-grid {
  max-width: 1200px; margin: 0 auto;
  display: grid; grid-template-columns: 1fr 1.2fr; gap: 8rem; align-items: center;
}
.section-tag {
  font-family: var(--font-mono); font-size: .65rem;
  letter-spacing: .25em; text-transform: uppercase;
  color: var(--oasis-bright); margin-bottom: 1rem; display: block;
}
.section-title {
  font-family: var(--font-display);
  font-size: clamp(3rem, 5vw, 5.5rem);
  font-weight: 300; line-height: 1; letter-spacing: -.02em;
  margin-bottom: 2rem;
}
.section-title em { font-style: italic; color: var(--oasis-light); }
.section-body {
  font-size: .95rem; line-height: 1.85; color: var(--text-muted);
  max-width: 480px;
}
.section-body + .section-body { margin-top: 1.2rem; }
.about-visual { position: relative; }
.about-canvas-wrap {
  width: 100%; aspect-ratio: 1; border-radius: 20px; overflow: hidden;
  border: 1px solid rgba(76,175,80,.15);
  box-shadow: 0 0 80px rgba(76,175,80,.1), 0 40px 80px rgba(0,0,0,.5);
}
#aboutCanvas { width: 100%; height: 100%; }
.about-stat-row {
  display: grid; grid-template-columns: repeat(3,1fr); gap: 1px;
  margin-top: 1.5rem; background: rgba(76,175,80,.12);
  border-radius: 12px; overflow: hidden;
}
.about-stat {
  background: rgba(10,31,10,.8); padding: 1.5rem 1rem; text-align: center;
  transition: background .3s;
}
.about-stat:hover { background: rgba(45,122,45,.15); }
.stat-num {
  font-family: var(--font-display);
  font-size: 2.5rem; font-weight: 600; color: var(--oasis-light); display: block; line-height: 1;
}
.stat-label { font-size: .72rem; color: var(--text-muted); letter-spacing: .1em; margin-top: .3rem; text-transform: uppercase; }

/* VISION */
#vision {
  padding: 12rem 6rem;
  background: linear-gradient(180deg, #0d1a08 0%, #051005 60%, #030a08 100%);
}
.vision-inner { max-width: 1200px; margin: 0 auto; }
.vision-header { text-align: center; margin-bottom: 6rem; }
.vision-quote {
  font-family: var(--font-display);
  font-size: clamp(2.5rem, 5vw, 5rem);
  font-style: italic; font-weight: 300; line-height: 1.2;
  color: var(--sand-pale); max-width: 900px; margin: 1.5rem auto 0;
}
.vision-quote span { color: var(--oasis-light); font-weight: 600; }
.vision-cards {
  display: grid; grid-template-columns: repeat(3,1fr); gap: 2px;
  border: 1px solid rgba(76,175,80,.1); border-radius: 20px; overflow: hidden;
}
.vision-card {
  background: rgba(10,31,10,.5); padding: 3.5rem 2.5rem;
  border-right: 1px solid rgba(76,175,80,.1);
  transition: background .5s;
  position: relative; overflow: hidden;
}
.vision-card:last-child { border-right: none; }
.vision-card::before {
  content: ''; position: absolute; top: 0; left: 0; right: 0; height: 2px;
  background: linear-gradient(90deg, transparent, var(--oasis-bright), transparent);
  transform: scaleX(0); transition: transform .5s var(--ease-out);
}
.vision-card:hover { background: rgba(45,122,45,.12); }
.vision-card:hover::before { transform: scaleX(1); }
.vision-card-icon { font-size: 3rem; margin-bottom: 1.5rem; display: block; }
.vision-card-num {
  font-family: var(--font-mono); font-size: .65rem; color: var(--oasis-mid);
  letter-spacing: .2em; margin-bottom: 1rem;
}
.vision-card-title { font-family: var(--font-display); font-size: 1.8rem; font-weight: 600; margin-bottom: 1rem; }
.vision-card-body { font-size: .88rem; line-height: 1.75; color: var(--text-muted); }

/* RESTORATION */
#restoration {
  padding: 12rem 6rem;
  background: linear-gradient(180deg, #030a08 0%, var(--oasis-deep) 50%, #051008 100%);
  overflow: hidden;
}
.restoration-inner { max-width: 1200px; margin: 0 auto; }
.restoration-header { margin-bottom: 5rem; }
.restoration-timeline {
  display: flex; flex-direction: column; gap: 0;
  border-left: 1px solid rgba(76,175,80,.2); margin-left: 2rem;
}
.timeline-item {
  display: grid; grid-template-columns: 280px 1fr; gap: 4rem;
  padding: 3rem 0 3rem 4rem; position: relative;
  border-bottom: 1px solid rgba(76,175,80,.08);
}
.timeline-item:last-child { border-bottom: none; }
.timeline-item::before {
  content: ''; position: absolute; left: -5px; top: 3.5rem;
  width: 9px; height: 9px; border-radius: 50%;
  background: var(--oasis-mid); border: 2px solid var(--oasis-bright);
  box-shadow: 0 0 20px rgba(76,175,80,.5);
}
.timeline-left {}
.timeline-phase { font-family: var(--font-mono); font-size: .65rem; color: var(--oasis-bright); letter-spacing: .2em; margin-bottom: .75rem; }
.timeline-title { font-family: var(--font-display); font-size: 2rem; font-weight: 600; margin-bottom: .5rem; }
.timeline-meta { font-size: .78rem; color: var(--text-muted); letter-spacing: .08em; }
.timeline-right {}
.timeline-body { font-size: .92rem; line-height: 1.8; color: var(--text-muted); margin-bottom: 1.5rem; }
.tech-tags { display: flex; gap: .5rem; flex-wrap: wrap; }
.tech-tag {
  padding: .35rem .9rem; border-radius: 999px;
  background: rgba(76,175,80,.1); border: 1px solid rgba(76,175,80,.2);
  font-size: .72rem; color: var(--oasis-light); letter-spacing: .06em;
}

/* FUTURE CITIES */
#future {
  padding: 12rem 6rem;
  background: linear-gradient(180deg, #051008 0%, #030d15 60%, var(--sand-deep) 100%);
  overflow: hidden;
}
.future-inner { max-width: 1200px; margin: 0 auto; }
#futureCanvas {
  width: 100%; height: 500px; border-radius: 24px;
  border: 1px solid rgba(76,175,80,.15);
  box-shadow: 0 0 100px rgba(76,175,80,.1);
  margin: 4rem 0;
}
.future-grid {
  display: grid; grid-template-columns: repeat(2,1fr); gap: 2px;
  border-radius: 16px; overflow: hidden; background: rgba(76,175,80,.06);
}
.future-cell {
  padding: 3rem; background: rgba(5,16,8,.8);
  border: 1px solid rgba(76,175,80,.08);
  transition: background .4s;
}
.future-cell:hover { background: rgba(45,122,45,.1); }
.future-cell-icon { font-size: 2.5rem; margin-bottom: 1.2rem; display: block; }
.future-cell-title { font-family: var(--font-display); font-size: 1.5rem; font-weight: 600; margin-bottom: .75rem; }
.future-cell-body { font-size: .88rem; line-height: 1.75; color: var(--text-muted); }

/* CTA BAND */
#cta-band {
  padding: 10rem 6rem; text-align: center;
  background: var(--sand-deep);
  position: relative; overflow: hidden;
}
#ctaCanvas { position: absolute; inset: 0; width: 100%; height: 100%; }
.cta-content { position: relative; z-index: 2; max-width: 700px; margin: 0 auto; }
.cta-title {
  font-family: var(--font-display);
  font-size: clamp(3.5rem, 7vw, 7rem);
  font-weight: 300; line-height: .9; letter-spacing: -.02em;
  margin-bottom: 2rem;
}
.cta-title span { font-style: italic; font-weight: 600; color: var(--oasis-light); }
.cta-sub { font-size: 1rem; color: var(--text-muted); line-height: 1.75; margin-bottom: 3rem; }
.cta-btns { display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap; }
.btn-cta-primary {
  padding: 1.2rem 3rem; border-radius: 4px;
  background: linear-gradient(135deg, var(--oasis-mid), var(--oasis-bright));
  color: var(--white); font-size: 1rem; font-weight: 600; letter-spacing: .1em;
  text-transform: uppercase; transition: all .4s;
  box-shadow: 0 20px 50px rgba(76,175,80,.3);
}
.btn-cta-primary:hover { transform: translateY(-4px); box-shadow: 0 30px 70px rgba(76,175,80,.45); }
.btn-cta-ghost {
  padding: 1.2rem 3rem; border-radius: 4px;
  background: transparent; border: 1px solid rgba(248,240,216,.2);
  color: var(--cream); font-size: 1rem; font-weight: 300; letter-spacing: .1em;
  text-transform: uppercase; transition: all .3s;
}
.btn-cta-ghost:hover { border-color: rgba(248,240,216,.5); transform: translateY(-2px); }



/* ── BIRDS CANVAS ── */
#birdsCanvas {
  position: fixed; inset: 0; width: 100%; height: 100%;
  pointer-events: none; z-index: 300;
}

/* ── INTERACTIVE HOVER STATES ── */
/* About list cards */
.about-item {
  background: #fff; border-radius: 14px; padding: 1.3rem 1.5rem;
  border-left: 3px solid #4caf50; box-shadow: 0 2px 12px rgba(76,175,80,.08);
  transition: transform .25s var(--ease-out), box-shadow .25s, border-color .25s;
  cursor: default;
}
.about-item:hover { transform: translateX(6px); box-shadow: 0 6px 24px rgba(76,175,80,.14); border-color: #2d7a2d; }

/* Platform service cards */
.service-card {
  background: rgba(255,255,255,.05); border: 1px solid rgba(255,255,255,.1);
  border-radius: 16px; padding: 2.2rem 2rem;
  transition: background .3s, transform .3s var(--ease-out), border-color .3s;
  cursor: default;
}
.service-card:hover { background: rgba(106,191,105,.1); transform: translateY(-6px); border-color: rgba(106,191,105,.35); }

/* Join cards */
.join-card {
  background: #fff; border-radius: 18px; padding: 2.5rem 2rem;
  border: 1px solid rgba(76,175,80,.1);
  transition: transform .3s var(--ease-out), box-shadow .3s;
  cursor: default;
}
.join-card:hover { transform: translateY(-8px); box-shadow: 0 20px 50px rgba(76,175,80,.14); }

/* Future vision list items */
.future-item {
  background: rgba(255,255,255,.05); border: 1px solid rgba(255,255,255,.09);
  border-radius: 12px; padding: 1.1rem 1.4rem;
  display: flex; align-items: flex-start; gap: 1.1rem;
  transition: background .25s, border-color .25s, transform .25s var(--ease-out);
  cursor: default;
}
.future-item:hover { background: rgba(106,191,105,.1); border-color: rgba(106,191,105,.3); transform: translateX(5px); }

/* Impact cards */
.impact-card {
  background: rgba(255,255,255,.06); border-radius: 20px; padding: 2.5rem 2rem;
  text-align: left; border: 1px solid rgba(255,255,255,.07); position: relative;
  transition: background .3s, transform .3s var(--ease-out), border-color .3s;
  cursor: default;
}
.impact-card:hover { background: rgba(255,255,255,.1); transform: translateY(-5px); border-color: rgba(106,191,105,.3); }

/* Phase timeline items */
.phase-item {
  padding: 2.5rem 0 2.5rem; border-bottom: 1px solid rgba(76,175,80,.12);
  position: relative; transition: padding-left .25s var(--ease-out);
}
.phase-item:last-child { border-bottom: none; }
.phase-item:hover { padding-left: .75rem; }



/* ── PALM TREE ANIMATION ── */
@keyframes palm-sway-1 {
  0%   { transform: rotate(0deg); }
  20%  { transform: rotate(7deg); }
  50%  { transform: rotate(-6deg); }
  80%  { transform: rotate(5deg); }
  100% { transform: rotate(0deg); }
}
@keyframes palm-sway-2 {
  0%   { transform: rotate(-2deg); }
  30%  { transform: rotate(-9deg); }
  60%  { transform: rotate(5deg); }
  100% { transform: rotate(-2deg); }
}
@keyframes palm-sway-3 {
  0%   { transform: rotate(2deg); }
  35%  { transform: rotate(10deg); }
  65%  { transform: rotate(-5deg); }
  100% { transform: rotate(2deg); }
}
@keyframes frond-l-swing {
  0%,100% { transform: rotate(0deg) scaleX(1); }
  40%     { transform: rotate(-14deg) scaleX(.92); }
  70%     { transform: rotate(8deg) scaleX(1.04); }
}
@keyframes frond-r-swing {
  0%,100% { transform: rotate(0deg) scaleX(1); }
  40%     { transform: rotate(14deg) scaleX(.92); }
  70%     { transform: rotate(-8deg) scaleX(1.04); }
}
@keyframes frond-t-swing {
  0%,100% { transform: rotate(0deg) scaleY(1); }
  35%     { transform: rotate(10deg) scaleY(.9); }
  65%     { transform: rotate(-7deg) scaleY(.95); }
}

.palm-1 { transform-origin: 50% 100%; animation: palm-sway-1 2.4s ease-in-out infinite; }
.palm-2 { transform-origin: 50% 100%; animation: palm-sway-2 2.9s ease-in-out infinite; animation-delay: -.6s; }
.palm-3 { transform-origin: 50% 100%; animation: palm-sway-3 2.2s ease-in-out infinite; animation-delay: -1.1s; }

.palm-1 .frond-l { transform-origin: 50% 55%; animation: frond-l-swing 2.4s ease-in-out infinite; }
.palm-1 .frond-r { transform-origin: 50% 55%; animation: frond-r-swing 2.4s ease-in-out infinite; }
.palm-1 .frond-t { transform-origin: 50% 45%; animation: frond-t-swing 2.4s ease-in-out infinite; }

.palm-2 .frond-l { transform-origin: 50% 55%; animation: frond-l-swing 2.9s ease-in-out infinite; animation-delay: -.6s; }
.palm-2 .frond-r { transform-origin: 50% 55%; animation: frond-r-swing 2.9s ease-in-out infinite; animation-delay: -.6s; }
.palm-2 .frond-t { transform-origin: 50% 45%; animation: frond-t-swing 2.9s ease-in-out infinite; animation-delay: -.6s; }

.palm-3 .frond-l { transform-origin: 50% 55%; animation: frond-l-swing 2.2s ease-in-out infinite; animation-delay: -1.1s; }
.palm-3 .frond-r { transform-origin: 50% 55%; animation: frond-r-swing 2.2s ease-in-out infinite; animation-delay: -1.1s; }
.palm-3 .frond-t { transform-origin: 50% 45%; animation: frond-t-swing 2.2s ease-in-out infinite; animation-delay: -1.1s; }

/* footer palms */
.footer-brand-logo svg { transform-origin: 50% 100%; animation: palm-sway-1 3s ease-in-out infinite; overflow: visible; }

/* ── KEYFRAMES ── */
.impact-num {
  font-family: var(--font-display);
  font-size: clamp(2.8rem,5vw,3.8rem); font-weight:700;
  color:#fff; line-height:1; margin-bottom:.6rem;
}
@keyframes fadeSlideUp {
  from { opacity: 0; transform: translateY(30px); }
  to   { opacity: 1; transform: translateY(0); }
}
@keyframes fadeIn {
  from { opacity: 0; } to { opacity: 1; }
}

/* ── RESPONSIVE ── */
@media (max-width: 1024px) {
  
  #about, #vision, #restoration, #future, #cta-band, footer { padding-left: 2rem; padding-right: 2rem; }
  .about-grid { grid-template-columns: 1fr; gap: 4rem; }
  .vision-cards { grid-template-columns: 1fr; }
  .timeline-item { grid-template-columns: 1fr; gap: 1.5rem; }
  .future-grid { grid-template-columns: 1fr; }
}

</style>
</head>
<body>


<!-- Grain overlay -->
<div class="grain" aria-hidden="true"></div>

<!-- Custom cursor -->
<div id="cursor-dot" aria-hidden="true"></div>
<div id="cursor-ring" aria-hidden="true"></div>

<!-- ══════════════════════════════════════════════
     NAVIGATION
════════════════════════════════════════════════ -->
<nav class="nav" id="mainNav" role="navigation" aria-label="Main navigation">

  <a href="#hero" class="nav-logo">
  <img src="images/logoo.png" alt="Ghosn Logo" 
       style="width:107px; height:107px; object-fit:contain; display:block;">
  
  </div>
</a>

  <ul class="nav-links">
    <li>
      <a href="ghusn_home1.php" id="nav-home">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
        Home
      </a>
    </li>
    <?php if ($role === 'resident'): ?>
    <li>
      <a href="submit.html" id="nav-report">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="3"/><path d="M12 2v3m0 14v3M2 12h3m14 0h3"/></svg>
        Submit Report
      </a>
    </li>
    <?php elseif ($role === 'volunteer'): ?>
    <li>
      <a href="search.html" id="nav-search">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        Search
      </a>
    </li>
    <?php endif; ?>
    <li>
      <a href="<?php echo $profileHref; ?>" id="nav-profile">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        Profile
      </a>
    </li>
  </ul>

  <div class="nav-actions">
    <button class="btn-nav-signout" onclick="signOut()">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9"/></svg>
      Sign Out
    </button>
  </div>
</nav>
<br><br><br>
<!-- ══════════════════════════════════════════════
     HERO — Full-screen Desert-to-Oasis
════════════════════════════════════════════════ -->
<section id="hero" aria-label="Hero section — desert transformation animation">
  <canvas id="heroCanvas" aria-hidden="true"></canvas>

  <div class="hero-content">

    <h1 class="hero-title">
      <span class="line1">City growing</span>
      <span class="line2">with nature</span>
    </h1>

    <p class="hero-subtitle">
      Harnessing technology, community, and science to transform
      arid wastelands into thriving green ecosystems — one tree at a time.
    </p>

    <div class="hero-ctas">
      <a href="#about" class="btn-hero-secondary">Explore the Vision</a>
    </div>
  </div>

  <div class="hero-scroll" aria-hidden="true">
    <span>Scroll</span>
    <div class="scroll-track"></div>
  </div>
</section>

<!-- Birds canvas — fixed overlay across full page -->
<canvas id="birdsCanvas" aria-hidden="true"></canvas>

<!-- ══════════════════════════════════════════════
     01 —
════════════════════════════════════════════════ -->
<section id="about" aria-labelledby="about-title" style="background:#f0ede4; padding:10rem 6rem; overflow:hidden;">
  <div style="max-width:1200px; margin:0 auto; display:grid; grid-template-columns:1fr 1.15fr; gap:7rem; align-items:center;">

    <div>
      <span class="reveal" style="font-family:var(--font-mono); font-size:.65rem; letter-spacing:.25em; text-transform:uppercase; color:#3a7d44; display:block; margin-bottom:1rem;">01 — About the Project</span>
      <h2 class="reveal delay-1" id="about-title" style="font-family:var(--font-display); font-size:clamp(2.8rem,5vw,5rem); font-weight:700; line-height:1.05; color:#1a2e1a; margin-bottom:1.8rem;">
        Turning barren <em style="font-style:italic; color:#4caf50;">sand</em><br>into living<br>ecosystems.
      </h2>
      <p class="reveal delay-2" style="font-size:.93rem; line-height:1.85; color:#5a6a55; max-width:440px; margin-bottom:2.5rem;">
        غصن (Ghusn — Arabic for "branch") combines community action, data tools, and environmental science to reverse desertification across the region.
      </p>

      <div class="reveal delay-3" style="display:flex; flex-direction:column; gap:.85rem;">
        <div style="background:#fff; border-radius:14px; padding:1.3rem 1.5rem; border-left:3px solid #4caf50; box-shadow:0 2px 12px rgba(76,175,80,.08);">
          <div style="display:flex; align-items:flex-start; gap:1.1rem;">
            <span style="font-family:var(--font-mono); font-size:.62rem; color:#4caf50; letter-spacing:.15em; padding-top:.15rem; min-width:22px;">01</span>
            <div>
              <div style="font-weight:600; color:#1a2e1a; font-size:.92rem; margin-bottom:.3rem;">What is Desertification?</div>
              <div style="font-size:.82rem; color:#6a7a65; line-height:1.65;">Degradation of fertile land into desert — affecting 40% of the world's surface and over 1 billion people.</div>
            </div>
          </div>
        </div>
        <div style="background:#fff; border-radius:14px; padding:1.3rem 1.5rem; border-left:3px solid #4caf50; box-shadow:0 2px 12px rgba(76,175,80,.08);">
          <div style="display:flex; align-items:flex-start; gap:1.1rem;">
            <span style="font-family:var(--font-mono); font-size:.62rem; color:#4caf50; letter-spacing:.15em; padding-top:.15rem; min-width:22px;">02</span>
            <div>
              <div style="font-weight:600; color:#1a2e1a; font-size:.92rem; margin-bottom:.3rem;">Why Trees Matter</div>
              <div style="font-size:.82rem; color:#6a7a65; line-height:1.65;">Trees restore ecosystems, absorb CO₂, stabilize soil, and create habitats — nature's most powerful tool.</div>
            </div>
          </div>
        </div>
        <div style="background:#fff; border-radius:14px; padding:1.3rem 1.5rem; border-left:3px solid #4caf50; box-shadow:0 2px 12px rgba(76,175,80,.08);">
          <div style="display:flex; align-items:flex-start; gap:1.1rem;">
            <span style="font-family:var(--font-mono); font-size:.62rem; color:#4caf50; letter-spacing:.15em; padding-top:.15rem; min-width:22px;">03</span>
            <div>
              <div style="font-weight:600; color:#1a2e1a; font-size:.92rem; margin-bottom:.3rem;">Technology as a Catalyst</div>
              <div style="font-size:.82rem; color:#6a7a65; line-height:1.65;">Real-time tracking, community coordination, and digital reporting maximize every action taken.</div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="reveal-right">
      <div style="border-radius:22px; overflow:hidden; border:1px solid rgba(76,175,80,.18); box-shadow:0 20px 60px rgba(76,175,80,.12), 0 8px 30px rgba(0,0,0,.06); position:relative; aspect-ratio:1;">
        <canvas id="aboutCanvas" style="width:100%; height:100%; display:block;"></canvas>
        <div style="position:absolute; bottom:1.5rem; left:1.5rem; background:rgba(255,255,255,.95); border-radius:12px; padding:1rem 1.4rem; backdrop-filter:blur(8px);">
          <div style="font-family:var(--font-display); font-size:2rem; font-weight:700; color:#1a2e1a; line-height:1;">125K+</div>
          <div style="font-size:.78rem; color:#5a7a5a; margin-top:.2rem;">Trees Planted Through غصن</div>
        </div>
      </div>
    </div>

  </div>
</section>

<!-- ══════════════════════════════════════════════
     02 
════════════════════════════════════════════════ -->
<section id="vision" aria-labelledby="vision-title" style="background:#142b1a; padding:10rem 6rem;">
  <div style="max-width:1200px; margin:0 auto;">

    <div class="reveal" style="text-align:center; margin-bottom:5rem;">
      <span style="font-family:var(--font-mono); font-size:.62rem; letter-spacing:.3em; text-transform:uppercase; color:#6abf69; display:block; margin-bottom:1rem;">02 — Platform Services</span>
      <h2 id="vision-title" style="font-family:var(--font-display); font-size:clamp(2.5rem,5vw,4.5rem); font-weight:700; color:#f0ede4; margin-bottom:1rem;">What Our Platform Offers</h2>
      <p style="color:rgba(200,230,190,.6); font-size:.93rem; max-width:560px; margin:0 auto; line-height:1.75;">غصن brings together education, action, and community to create measurable, lasting environmental change.</p>
    </div>

    <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:1.2rem;">

      <div class="reveal" style="background:rgba(255,255,255,.05); border:1px solid rgba(255,255,255,.1); border-radius:16px; padding:2.2rem 2rem; transition:background .3s;">
        <div style="width:42px; height:42px; background:rgba(106,191,105,.15); border-radius:10px; display:flex; align-items:center; justify-content:center; margin-bottom:1.4rem;">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#6abf69" stroke-width="1.8"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
        </div>
        <h3 style="font-family:var(--font-display); font-size:1.1rem; font-weight:600; color:#e8f5e9; margin-bottom:.7rem;">Tree Planting Campaigns</h3>
        <p style="font-size:.82rem; line-height:1.75; color:rgba(200,230,190,.6); margin-bottom:1.2rem;">Join organized, real-world campaigns. Track your contribution and see cumulative community impact.</p>
        <a href="#" style="font-size:.78rem; color:#6abf69; letter-spacing:.04em;">Join a Campaign →</a>
      </div>

      <div class="reveal delay-1" style="background:rgba(255,255,255,.05); border:1px solid rgba(255,255,255,.1); border-radius:16px; padding:2.2rem 2rem; transition:background .3s;">
        <div style="width:42px; height:42px; background:rgba(106,191,105,.15); border-radius:10px; display:flex; align-items:center; justify-content:center; margin-bottom:1.4rem;">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#6abf69" stroke-width="1.8"><circle cx="12" cy="12" r="3"/><path d="M12 2v3m0 14v3M2 12h3m14 0h3"/></svg>
        </div>
        <h3 style="font-family:var(--font-display); font-size:1.1rem; font-weight:600; color:#e8f5e9; margin-bottom:.7rem;">Environmental Reporting</h3>
        <p style="font-size:.82rem; line-height:1.75; color:rgba(200,230,190,.6); margin-bottom:1.2rem;">Report desertified areas on an interactive map. Your data helps identify critical zones needing restoration.</p>
        <a href="#" style="font-size:.78rem; color:#6abf69; letter-spacing:.04em;">Submit a Report →</a>
      </div>

      <div class="reveal delay-2" style="background:rgba(255,255,255,.05); border:1px solid rgba(255,255,255,.1); border-radius:16px; padding:2.2rem 2rem; transition:background .3s;">
        <div style="width:42px; height:42px; background:rgba(106,191,105,.15); border-radius:10px; display:flex; align-items:center; justify-content:center; margin-bottom:1.4rem;">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#6abf69" stroke-width="1.8"><path d="M4 19.5A2.5 2.5 0 016.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 014 19.5v-15A2.5 2.5 0 016.5 2z"/></svg>
        </div>
        <h3 style="font-family:var(--font-display); font-size:1.1rem; font-weight:600; color:#e8f5e9; margin-bottom:.7rem;">Awareness & Education</h3>
        <p style="font-size:.82rem; line-height:1.75; color:rgba(200,230,190,.6); margin-bottom:1.2rem;">Access curated resources, articles, and guides about sustainability and measurable impact.</p>
        <a href="#" style="font-size:.78rem; color:#6abf69; letter-spacing:.04em;">Start Learning →</a>
      </div>

      <div class="reveal delay-1" style="background:rgba(255,255,255,.05); border:1px solid rgba(255,255,255,.1); border-radius:16px; padding:2.2rem 2rem; transition:background .3s;">
        <div style="width:42px; height:42px; background:rgba(106,191,105,.15); border-radius:10px; display:flex; align-items:center; justify-content:center; margin-bottom:1.4rem;">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#6abf69" stroke-width="1.8"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>
        </div>
        <h3 style="font-family:var(--font-display); font-size:1.1rem; font-weight:600; color:#e8f5e9; margin-bottom:.7rem;">Community Participation</h3>
        <p style="font-size:.82rem; line-height:1.75; color:rgba(200,230,190,.6); margin-bottom:1.2rem;">Connect with volunteers, environmental groups, and local organizations to transform landscapes.</p>
        <a href="#" style="font-size:.78rem; color:#6abf69; letter-spacing:.04em;">Meet the Community →</a>
      </div>

      <div class="reveal delay-2" style="background:rgba(255,255,255,.05); border:1px solid rgba(255,255,255,.1); border-radius:16px; padding:2.2rem 2rem; transition:background .3s;">
        <div style="width:42px; height:42px; background:rgba(106,191,105,.15); border-radius:10px; display:flex; align-items:center; justify-content:center; margin-bottom:1.4rem;">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#6abf69" stroke-width="1.8"><rect x="2" y="2" width="20" height="20" rx="3"/><path d="M7 17V11M12 17V7M17 17v-5"/></svg>
        </div>
        <h3 style="font-family:var(--font-display); font-size:1.1rem; font-weight:600; color:#e8f5e9; margin-bottom:.7rem;">Impact Tracking</h3>
        <p style="font-size:.82rem; line-height:1.75; color:rgba(200,230,190,.6); margin-bottom:1.2rem;">Visualize real-time data on trees planted, areas restored, and CO₂ absorbed.</p>
        <a href="#" style="font-size:.78rem; color:#6abf69; letter-spacing:.04em;">View Dashboard →</a>
      </div>

      <div class="reveal delay-3" style="background:rgba(255,255,255,.05); border:1px solid rgba(255,255,255,.1); border-radius:16px; padding:2.2rem 2rem; transition:background .3s;">
        <div style="width:42px; height:42px; background:rgba(106,191,105,.15); border-radius:10px; display:flex; align-items:center; justify-content:center; margin-bottom:1.4rem;">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#6abf69" stroke-width="1.8"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9M13.73 21a2 2 0 01-3.46 0"/></svg>
        </div>
        <h3 style="font-family:var(--font-display); font-size:1.1rem; font-weight:600; color:#e8f5e9; margin-bottom:.7rem;">Alerts & Notifications</h3>
        <p style="font-size:.82rem; line-height:1.75; color:rgba(200,230,190,.6); margin-bottom:1.2rem;">Personalized alerts about campaigns, environmental events, and critical reports in your area.</p>
        <a href="#" style="font-size:.78rem; color:#6abf69; letter-spacing:.04em;">Set Alerts →</a>
      </div>

    </div>
  </div>
</section>

<!-- ══════════════════════════════════════════════
     03 — STEP BY STEP 
════════════════════════════════════════════════ -->
<section id="restoration" aria-labelledby="restoration-title" style="background:#f0ede4; padding:10rem 6rem; overflow:hidden;">
  <div style="max-width:1200px; margin:0 auto; display:grid; grid-template-columns:1fr 1.6fr; gap:8rem; align-items:start;">

    <div>
      <span class="reveal" style="font-family:var(--font-mono); font-size:.62rem; letter-spacing:.28em; text-transform:uppercase; color:#3a7d44; display:block; margin-bottom:1rem;">03 — Step by Step</span>
      <h2 class="reveal delay-1" id="restoration-title" style="font-family:var(--font-display); font-size:clamp(2.5rem,4.5vw,4rem); font-weight:700; line-height:1.05; color:#1a2e1a; margin-bottom:1.5rem;">
        Science-driven<br><em style="font-style:italic; color:#4caf50;">ecosystem rebuilding.</em>
      </h2>
      <p class="reveal delay-2" style="font-size:.9rem; line-height:1.85; color:#5a6a55; max-width:340px;">
        From your first login to planting your hundredth tree — every step is guided, tracked, and impactful.
      </p>
      <!-- decorative wave -->
      <div class="reveal delay-3" style="margin-top:3rem; color:#b8c8b0; font-family:var(--font-display); font-size:2.5rem; font-style:italic; opacity:.4;">∿∿∿</div>
    </div>

    <div style="border-left:2px solid rgba(76,175,80,.25); padding-left:3rem; display:flex; flex-direction:column; gap:0;">

      <div class="reveal" style="padding:2.5rem 0 2.5rem; border-bottom:1px solid rgba(76,175,80,.12); position:relative;">
        <div style="position:absolute; left:-3.45rem; top:2.7rem; width:10px; height:10px; border-radius:50%; background:#4caf50; box-shadow:0 0 0 3px #f0ede4, 0 0 0 5px rgba(76,175,80,.3);"></div>
        <div style="font-family:var(--font-mono); font-size:.6rem; color:#4caf50; letter-spacing:.2em; margin-bottom:.5rem;">PHASE 01</div>
        <h3 style="font-family:var(--font-display); font-size:1.6rem; font-weight:600; color:#1a2e1a; margin-bottom:.25rem;">Create Your Account</h3>
        <p style="font-size:.75rem; color:#8a9a85; margin-bottom:.9rem; letter-spacing:.05em;">Getting Started</p>
        <p style="font-size:.88rem; line-height:1.8; color:#5a6a55; margin-bottom:1rem;">Sign up in seconds. Build your environmental profile and connect with your local region's restoration efforts.</p>
        <div style="display:flex; gap:.5rem; flex-wrap:wrap;">
          <span style="padding:.3rem .8rem; border-radius:999px; border:1px solid rgba(76,175,80,.3); font-size:.7rem; color:#3a7d44; background:rgba(76,175,80,.06);">Free Registration</span>
          <span style="padding:.3rem .8rem; border-radius:999px; border:1px solid rgba(76,175,80,.3); font-size:.7rem; color:#3a7d44; background:rgba(76,175,80,.06);">Profile Setup</span>
          <span style="padding:.3rem .8rem; border-radius:999px; border:1px solid rgba(76,175,80,.3); font-size:.7rem; color:#3a7d44; background:rgba(76,175,80,.06);">Region Selection</span>
        </div>
      </div>

      <div class="reveal delay-1" style="padding:2.5rem 0 2.5rem; border-bottom:1px solid rgba(76,175,80,.12); position:relative;">
        <div style="position:absolute; left:-3.45rem; top:2.7rem; width:10px; height:10px; border-radius:50%; background:#4caf50; box-shadow:0 0 0 3px #f0ede4, 0 0 0 5px rgba(76,175,80,.3);"></div>
        <div style="font-family:var(--font-mono); font-size:.6rem; color:#4caf50; letter-spacing:.2em; margin-bottom:.5rem;">PHASE 02</div>
        <h3 style="font-family:var(--font-display); font-size:1.6rem; font-weight:600; color:#1a2e1a; margin-bottom:.25rem;">Explore the Map</h3>
        <p style="font-size:.75rem; color:#8a9a85; margin-bottom:.9rem; letter-spacing:.05em;">Discover & Assess</p>
        <p style="font-size:.88rem; line-height:1.8; color:#5a6a55; margin-bottom:1rem;">Browse the interactive map to find desertification reports, active campaigns, and volunteer activities near you.</p>
        <div style="display:flex; gap:.5rem; flex-wrap:wrap;">
          <span style="padding:.3rem .8rem; border-radius:999px; border:1px solid rgba(76,175,80,.3); font-size:.7rem; color:#3a7d44; background:rgba(76,175,80,.06);">Interactive Map</span>
          <span style="padding:.3rem .8rem; border-radius:999px; border:1px solid rgba(76,175,80,.3); font-size:.7rem; color:#3a7d44; background:rgba(76,175,80,.06);">Satellite View</span>
          <span style="padding:.3rem .8rem; border-radius:999px; border:1px solid rgba(76,175,80,.3); font-size:.7rem; color:#3a7d44; background:rgba(76,175,80,.06);">Nearby Campaigns</span>
        </div>
      </div>

      <div class="reveal delay-2" style="padding:2.5rem 0 2.5rem; border-bottom:1px solid rgba(76,175,80,.12); position:relative;">
        <div style="position:absolute; left:-3.45rem; top:2.7rem; width:10px; height:10px; border-radius:50%; background:#4caf50; box-shadow:0 0 0 3px #f0ede4, 0 0 0 5px rgba(76,175,80,.3);"></div>
        <div style="font-family:var(--font-mono); font-size:.6rem; color:#4caf50; letter-spacing:.2em; margin-bottom:.5rem;">PHASE 03</div>
        <h3 style="font-family:var(--font-display); font-size:1.6rem; font-weight:600; color:#1a2e1a; margin-bottom:.25rem;">Report & Contribute</h3>
        <p style="font-size:.75rem; color:#8a9a85; margin-bottom:.9rem; letter-spacing:.05em;">Take Action</p>
        <p style="font-size:.88rem; line-height:1.8; color:#5a6a55; margin-bottom:1rem;">Submit desertification reports with GPS data and photos. Every report helps map the problem and accelerate response.</p>
        <div style="display:flex; gap:.5rem; flex-wrap:wrap;">
          <span style="padding:.3rem .8rem; border-radius:999px; border:1px solid rgba(76,175,80,.3); font-size:.7rem; color:#3a7d44; background:rgba(76,175,80,.06);">GPS Reporting</span>
          <span style="padding:.3rem .8rem; border-radius:999px; border:1px solid rgba(76,175,80,.3); font-size:.7rem; color:#3a7d44; background:rgba(76,175,80,.06);">Photo Upload</span>
          <span style="padding:.3rem .8rem; border-radius:999px; border:1px solid rgba(76,175,80,.3); font-size:.7rem; color:#3a7d44; background:rgba(76,175,80,.06);">Expert Review</span>
        </div>
      </div>

      <div class="reveal delay-3" style="padding:2.5rem 0 2.5rem; position:relative;">
        <div style="position:absolute; left:-3.45rem; top:2.7rem; width:10px; height:10px; border-radius:50%; background:#4caf50; box-shadow:0 0 0 3px #f0ede4, 0 0 0 5px rgba(76,175,80,.3);"></div>
        <div style="font-family:var(--font-mono); font-size:.6rem; color:#4caf50; letter-spacing:.2em; margin-bottom:.5rem;">PHASE 04</div>
        <h3 style="font-family:var(--font-display); font-size:1.6rem; font-weight:600; color:#1a2e1a; margin-bottom:.25rem;">Plant & Track Impact</h3>
        <p style="font-size:.75rem; color:#8a9a85; margin-bottom:.9rem; letter-spacing:.05em;">Measure Results</p>
        <p style="font-size:.88rem; line-height:1.8; color:#5a6a55; margin-bottom:1rem;">Join campaigns, plant trees, and watch your personal environmental dashboard grow with every contribution.</p>
        <div style="display:flex; gap:.5rem; flex-wrap:wrap;">
          <span style="padding:.3rem .8rem; border-radius:999px; border:1px solid rgba(76,175,80,.3); font-size:.7rem; color:#3a7d44; background:rgba(76,175,80,.06);">Tree Tracking</span>
          <span style="padding:.3rem .8rem; border-radius:999px; border:1px solid rgba(76,175,80,.3); font-size:.7rem; color:#3a7d44; background:rgba(76,175,80,.06);">CO₂ Metrics</span>
          <span style="padding:.3rem .8rem; border-radius:999px; border:1px solid rgba(76,175,80,.3); font-size:.7rem; color:#3a7d44; background:rgba(76,175,80,.06);">Live Dashboard</span>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- ══════════════════════════════════════════════
     04 — OUR IMPACT 
════════════════════════════════════════════════ -->
<section id="impact" aria-label="Our impact" style="background:#142b1a; padding:9rem 6rem; text-align:center;">
  <div style="max-width:1100px; margin:0 auto;">
    <span class="reveal" style="font-family:var(--font-mono); font-size:.62rem; letter-spacing:.3em; text-transform:uppercase; color:#6abf69; display:block; margin-bottom:1rem;">04 — Our Impact</span>
    <h2 class="reveal delay-1" style="font-family:var(--font-display); font-size:clamp(2.8rem,5vw,4.5rem); font-weight:700; color:#f0ede4; line-height:1.1; margin-bottom:.8rem;">
      Growing impact,
    </h2>
    <h2 class="reveal delay-1" style="font-family:var(--font-display); font-size:clamp(2.8rem,5vw,4.5rem); font-weight:300; font-style:italic; color:#6abf69; line-height:1.1; margin-bottom:1.5rem;">
      one tree at a time.
    </h2>
    <p class="reveal delay-2" style="font-size:.9rem; color:rgba(200,230,190,.55); max-width:520px; margin:0 auto 4rem; line-height:1.75;">Real numbers from real communities — every statistic is a step forward.</p>

    <div id="impactCards" class="reveal delay-2" style="display:grid; grid-template-columns:repeat(3,1fr); gap:1.2rem;">

      <div class="impact-card">
        <div class="impact-num" id="counter-trees" data-target="125000" data-suffix="K" data-divisor="1000">0</div>
        <div style="font-size:.85rem; font-weight:500; color:#e8f5e9; margin-bottom:.2rem;">Trees Planted</div>
        <div style="font-size:.75rem; color:rgba(200,230,190,.45);">Across 6 regions</div>
        <div style="position:absolute; bottom:1.5rem; left:2rem; width:2.5rem; height:2px; background:#6abf69; border-radius:1px;"></div>
      </div>

      <div class="impact-card">
        <div class="impact-num" id="counter-km" data-target="850" data-suffix="">0</div>
        <div style="font-size:.85rem; font-weight:500; color:#e8f5e9; margin-bottom:.2rem;">km² Areas Restored</div>
        <div style="font-size:.75rem; color:rgba(200,230,190,.45);">Land brought back to life</div>
        <div style="position:absolute; bottom:1.5rem; left:2rem; width:2.5rem; height:2px; background:#6abf69; border-radius:1px;"></div>
      </div>

      <div class="impact-card">
        <div class="impact-num" id="counter-volunteers" data-target="12400" data-suffix="K" data-divisor="1000">0</div>
        <div style="font-size:.85rem; font-weight:500; color:#e8f5e9; margin-bottom:.2rem;">Community Volunteers</div>
        <div style="font-size:.75rem; color:rgba(200,230,190,.45);">And growing every day</div>
        <div style="position:absolute; bottom:1.5rem; left:2rem; width:2.5rem; height:2px; background:#6abf69; border-radius:1px;"></div>
      </div>

    </div>
  </div>
</section>

<!-- ══════════════════════════════════════════════
     05 — JOIN THE MOVEMENT 
════════════════════════════════════════════════ -->
<section id="join" style="background:#f0ede4; padding:9rem 6rem; position:relative; overflow:hidden;">

  <!-- floating leaves bg decoration -->
  <div aria-hidden="true" style="position:absolute; inset:0; pointer-events:none; overflow:hidden; opacity:.35;">
    <svg width="100%" height="100%" viewBox="0 0 1400 800" preserveAspectRatio="xMidYMid slice" xmlns="http://www.w3.org/2000/svg">
      <ellipse cx="200" cy="120" rx="18" ry="9" fill="#a5d6a7" transform="rotate(-30 200 120)"/>
      <ellipse cx="400" cy="60" rx="14" ry="7" fill="#81c784" transform="rotate(20 400 60)"/>
      <ellipse cx="900" cy="180" rx="20" ry="10" fill="#c8e6c9" transform="rotate(-45 900 180)"/>
      <ellipse cx="1200" cy="90" rx="16" ry="8" fill="#a5d6a7" transform="rotate(15 1200 90)"/>
      <ellipse cx="1350" cy="300" rx="22" ry="11" fill="#81c784" transform="rotate(-20 1350 300)"/>
      <ellipse cx="100" cy="500" rx="15" ry="7" fill="#c8e6c9" transform="rotate(35 100 500)"/>
      <ellipse cx="700" cy="700" rx="19" ry="9" fill="#a5d6a7" transform="rotate(-15 700 700)"/>
      <ellipse cx="1100" cy="650" rx="17" ry="8" fill="#81c784" transform="rotate(25 1100 650)"/>
      <ellipse cx="500" cy="420" rx="13" ry="6" fill="#c8e6c9" transform="rotate(-40 500 420)"/>
      <ellipse cx="300" cy="750" rx="21" ry="10" fill="#a5d6a7" transform="rotate(10 300 750)"/>
    </svg>
  </div>

  <div style="max-width:1100px; margin:0 auto; position:relative; z-index:1;">

    <!-- quote -->
    <div class="reveal" style="text-align:center; margin-bottom:7rem;">
      <blockquote style="font-family:var(--font-display); font-size:clamp(1.6rem,3vw,2.6rem); font-style:italic; font-weight:300; color:#1a2e1a; line-height:1.45; max-width:800px; margin:0 auto 1rem;">
        "Every tree planted is a promise kept — to the land, to the next generation, and to ourselves."
      </blockquote>
      <cite style="font-size:.78rem; color:#7a9a75; letter-spacing:.1em;">— غصن Environmental Platform</cite>
    </div>

    <div style="text-align:center; margin-bottom:4rem;">
      <span class="reveal" style="font-family:var(--font-mono); font-size:.62rem; letter-spacing:.3em; text-transform:uppercase; color:#3a7d44; display:block; margin-bottom:1rem;">05 — Join the Movement</span>
      <h2 class="reveal delay-1" style="font-family:var(--font-display); font-size:clamp(2.5rem,5vw,4.5rem); font-weight:700; color:#1a2e1a; line-height:1.1;">
        Become part of<br><em style="font-style:italic; color:#4caf50; font-weight:400;">the solution.</em>
      </h2>
      <p class="reveal delay-2" style="font-size:.9rem; color:#6a7a65; max-width:500px; margin:1.5rem auto 0; line-height:1.75;">Thousands of volunteers across the region are already making a real difference. Your journey starts here.</p>
    </div>

    <div class="reveal delay-2" style="display:grid; grid-template-columns:repeat(3,1fr); gap:1.5rem;">

      <div style="background:#fff; border-radius:18px; padding:2.5rem 2rem; border:1px solid rgba(76,175,80,.1); transition:transform .3s, box-shadow .3s;">
        <div style="width:46px; height:46px; background:#e8f5e9; border-radius:11px; display:flex; align-items:center; justify-content:center; margin-bottom:1.5rem;">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#3a7d44" stroke-width="1.8"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        </div>
        <h3 style="font-family:var(--font-display); font-size:1.2rem; font-weight:600; color:#1a2e1a; margin-bottom:.75rem;">Create Your Profile</h3>
        <p style="font-size:.85rem; line-height:1.75; color:#6a7a65;">Sign up in seconds and build your environmental profile. Tell us your region and your goals — we'll match you to the right campaigns.</p>
      </div>

      <div style="background:#fff; border-radius:18px; padding:2.5rem 2rem; border:1px solid rgba(76,175,80,.1); transition:transform .3s, box-shadow .3s;">
        <div style="width:46px; height:46px; background:#e8f5e9; border-radius:11px; display:flex; align-items:center; justify-content:center; margin-bottom:1.5rem;">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#3a7d44" stroke-width="1.8"><circle cx="12" cy="12" r="3"/><path d="M12 2v3m0 14v3M2 12h3m14 0h3"/></svg>
        </div>
        <h3 style="font-family:var(--font-display); font-size:1.2rem; font-weight:600; color:#1a2e1a; margin-bottom:.75rem;">Map the Problem</h3>
        <p style="font-size:.85rem; line-height:1.75; color:#6a7a65;">Use our reporting tools to document degraded land near you. Every GPS pin you drop helps focus restoration efforts where they matter most.</p>
      </div>

      <div style="background:#fff; border-radius:18px; padding:2.5rem 2rem; border:1px solid rgba(76,175,80,.1); transition:transform .3s, box-shadow .3s;">
        <div style="width:46px; height:46px; background:#e8f5e9; border-radius:11px; display:flex; align-items:center; justify-content:center; margin-bottom:1.5rem;">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#3a7d44" stroke-width="1.8"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
        </div>
        <h3 style="font-family:var(--font-display); font-size:1.2rem; font-weight:600; color:#1a2e1a; margin-bottom:.75rem;">Plant & Watch It Grow</h3>
        <p style="font-size:.85rem; line-height:1.75; color:#6a7a65;">Join real tree-planting campaigns and track your impact in real time. Watch your personal dashboard grow with every tree you plant.</p>
      </div>

    </div>
  </div>
</section>

<!-- ══════════════════════════════════════════════
     06 — FUTURE VISION 
════════════════════════════════════════════════ -->
<section id="future" aria-labelledby="future-title" style="background:#142b1a; padding:10rem 6rem;">
  <div style="max-width:1200px; margin:0 auto; display:grid; grid-template-columns:1fr 1fr; gap:6rem; align-items:center;">

    <div>
      <span class="reveal" style="font-family:var(--font-mono); font-size:.62rem; letter-spacing:.3em; text-transform:uppercase; color:#6abf69; display:block; margin-bottom:1rem;">06 — Future Vision</span>
      <h2 class="reveal delay-1" id="future-title" style="font-family:var(--font-display); font-size:clamp(2.5rem,4.5vw,4rem); font-weight:700; color:#f0ede4; line-height:1.1; margin-bottom:1.5rem;">
        Cities that <em style="font-style:italic; color:#6abf69;">breathe,</em><br>deserts that thrive.
      </h2>
      <p class="reveal delay-2" style="font-size:.9rem; line-height:1.8; color:rgba(200,230,190,.6); max-width:420px; margin-bottom:3rem;">
        By 2050, we envision green corridors connecting restored land to urban forests — a living planetary network powered by community action.
      </p>

      <div class="reveal delay-2" style="display:flex; flex-direction:column; gap:.8rem;">
        <div style="background:rgba(255,255,255,.05); border:1px solid rgba(255,255,255,.09); border-radius:12px; padding:1.1rem 1.4rem; display:flex; align-items:flex-start; gap:1.1rem;">
          <div style="width:34px; height:34px; background:rgba(106,191,105,.15); border-radius:8px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#6abf69" stroke-width="1.8"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
          </div>
          <div>
            <div style="font-weight:600; color:#e8f5e9; font-size:.9rem; margin-bottom:.25rem;">Greener Cities</div>
            <div style="font-size:.8rem; color:rgba(200,230,190,.55); line-height:1.6;">Urban tree corridors and green infrastructure integrated into city planning.</div>
          </div>
        </div>
        <div style="background:rgba(255,255,255,.05); border:1px solid rgba(255,255,255,.09); border-radius:12px; padding:1.1rem 1.4rem; display:flex; align-items:flex-start; gap:1.1rem;">
          <div style="width:34px; height:34px; background:rgba(106,191,105,.15); border-radius:8px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#6abf69" stroke-width="1.8"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 010 20M12 2a15.3 15.3 0 000 20"/></svg>
          </div>
          <div>
            <div style="font-weight:600; color:#e8f5e9; font-size:.9rem; margin-bottom:.25rem;">Reduced Desertification</div>
            <div style="font-size:.8rem; color:rgba(200,230,190,.55); line-height:1.6;">Data and community action to halt land degradation at scale.</div>
          </div>
        </div>
        <div style="background:rgba(255,255,255,.05); border:1px solid rgba(255,255,255,.09); border-radius:12px; padding:1.1rem 1.4rem; display:flex; align-items:flex-start; gap:1.1rem;">
          <div style="width:34px; height:34px; background:rgba(106,191,105,.15); border-radius:8px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#6abf69" stroke-width="1.8"><polyline points="22 7 13.5 15.5 8.5 10.5 2 17"/><polyline points="16 7 22 7 22 13"/></svg>
          </div>
          <div>
            <div style="font-weight:600; color:#e8f5e9; font-size:.9rem; margin-bottom:.25rem;">Sustainable Practices</div>
            <div style="font-size:.8rem; color:rgba(200,230,190,.55); line-height:1.6;">Long-term, community-driven land-use approaches that last generations.</div>
          </div>
        </div>
        <div style="background:rgba(255,255,255,.05); border:1px solid rgba(255,255,255,.09); border-radius:12px; padding:1.1rem 1.4rem; display:flex; align-items:flex-start; gap:1.1rem;">
          <div style="width:34px; height:34px; background:rgba(106,191,105,.15); border-radius:8px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#6abf69" stroke-width="1.8"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
          </div>
          <div>
            <div style="font-weight:600; color:#e8f5e9; font-size:.9rem; margin-bottom:.25rem;">AI-Powered Monitoring</div>
            <div style="font-size:.8rem; color:rgba(200,230,190,.55); line-height:1.6;">Satellite imagery and AI to monitor land health in real time.</div>
          </div>
        </div>
      </div>
    </div>

    <div class="reveal-right">
      <div style="border-radius:20px; overflow:hidden; border:1px solid rgba(106,191,105,.2); aspect-ratio:1; box-shadow:0 30px 80px rgba(0,0,0,.4);">
        <canvas id="futureCanvas" style="width:100%; height:100%; display:block;"></canvas>
      </div>
    </div>

  </div>
</section>

<!-- ══════════════════════════════════════════════
     CTA BAND
════════════════════════════════════════════════ -->
<section id="cta-band" aria-label="Call to action">
  <canvas id="ctaCanvas" aria-hidden="true"></canvas>
  <div class="cta-content">
    <h2 class="cta-title reveal">
      Begin your<br><span>green journey</span><br>today.
    </h2>
    <p class="cta-sub reveal delay-1">
      Join thousands of change-makers transforming the world's deserts.
      Register now and plant your first virtual tree — we'll make it real.
    </p>
    <div class="cta-btns reveal delay-2">
      
    </div>
  </div>
</section>

<!-- ══════════════════════════════════════════════
     FOOTER
════════════════════════════════════════════════ -->
<footer>
  <!-- Animated tree silhouettes -->
  <canvas class="footer-trees" id="footerTreesCanvas" aria-hidden="true"></canvas>

  <div class="footer-main">
    <div class="footer-top">

      <div class="footer-brand">
        <div class="footer-brand-logo">
          <svg viewBox="0 0 28 36" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M14 34 C13 28 12 22 14 16" stroke="#6abf69" stroke-width="2" stroke-linecap="round"/>
            <path d="M14 16 C10 14 5 12 2 8" stroke="#4caf50" stroke-width="1.8" stroke-linecap="round"/>
            <path d="M14 18 C9 17 4 18 1 15" stroke="#4caf50" stroke-width="1.5" stroke-linecap="round"/>
            <path d="M14 15 C11 11 8 7 9 3" stroke="#4caf50" stroke-width="1.5" stroke-linecap="round"/>
            <path d="M14 16 C18 14 23 12 26 8" stroke="#81c784" stroke-width="1.8" stroke-linecap="round"/>
            <path d="M14 18 C19 17 24 18 27 15" stroke="#81c784" stroke-width="1.5" stroke-linecap="round"/>
            <path d="M14 15 C17 11 20 7 19 3" stroke="#81c784" stroke-width="1.5" stroke-linecap="round"/>
            <path d="M14 14 C14 10 13 6 14 2" stroke="#a5d6a7" stroke-width="1.5" stroke-linecap="round"/>
            <path d="M14 34 C11 33 8 34 6 33" stroke="#6abf69" stroke-width="1.5" stroke-linecap="round"/>
            <path d="M14 34 C17 33 20 34 22 33" stroke="#6abf69" stroke-width="1.5" stroke-linecap="round"/>
          </svg>
          <span class="footer-brand-name">Ghosn <span>غصن</span></span>
        </div>
        <p class="footer-brand-desc">Combining community action, data tools, and environmental science to reverse desertification across the region.</p>
        <div class="footer-social">
          <a href="#" aria-label="Twitter">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c9 5 20 0 20-11.5a4.5 4.5 0 00-.08-.83A7.72 7.72 0 0023 3z"/></svg>
          </a>
          <a href="#" aria-label="Instagram">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1112.63 8 4 4 0 0116 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
          </a>
          <a href="#" aria-label="LinkedIn">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M16 8a6 6 0 016 6v7h-4v-7a2 2 0 00-2-2 2 2 0 00-2 2v7h-4v-7a6 6 0 016-6z"/><rect x="2" y="9" width="4" height="12"/><circle cx="4" cy="4" r="2"/></svg>
          </a>
        </div>
      </div>

      <div>
        <div class="footer-col-title">Navigation</div>
        <div class="footer-col-links">
          <a href="#hero">Home</a>
          <a href="#about">About</a>
          <a href="#vision">Platform</a>
          <a href="#restoration">How It Works</a>
          <a href="#impact">Our Impact</a>
          <a href="#join">Join</a>
          <a href="#future">Future Vision</a>
        </div>
      </div>

      <div>
        <div class="footer-col-title">Platform</div>
        <div class="footer-col-links">
          <a href="#">Submit a Report</a>
          <a href="#">Tree Campaigns</a>
          <a href="#">Interactive Map</a>
          <a href="#">Impact Dashboard</a>
          <a href="#">Community Forum</a>
          <a href="#">Learning Hub</a>
        </div>
      </div>

      <div>
        <div class="footer-col-title">Contact</div>
        <div class="footer-col-links">
          <a href="#">hello@ghosn.eco</a>
          <a href="#">Partner With Us</a>
          <a href="#">Press & Media</a>
          <a href="#">Privacy Policy</a>
          <a href="#">Terms of Use</a>
        </div>
        <div style="margin-top:1.5rem; padding:1rem 1.2rem; background:rgba(106,191,105,.08); border:1px solid rgba(106,191,105,.15); border-radius:10px;">
          <div style="font-size:.72rem; color:var(--oasis-bright); letter-spacing:.1em; text-transform:uppercase; font-family:var(--font-mono); margin-bottom:.4rem;">Status</div>
          <div style="display:flex; align-items:center; gap:.5rem;">
            <span style="width:7px; height:7px; border-radius:50%; background:#4caf50; animation:pulse-dot 2s infinite;"></span>
            <span style="font-size:.8rem; color:rgba(180,220,180,.7);">All systems operational</span>
          </div>
        </div>
      </div>

    </div>

    <div class="footer-bottom-row">
      <span class="footer-copy">© 2025 Ghosn Environmental Platform — Built for a greener planet</span>
      <div class="footer-badge">
        <span class="footer-badge-dot"></span>
        <span>125K+ Trees Planted</span>
      </div>
    </div>
  </div>
</footer>

<!-- ══════════════════════════════════════════════
     SCRIPTS
════════════════════════════════════════════════ -->







<script>
/* ============================================================
   OASIS — Hero Canvas Animation
   Desert → Green Oasis transformation
   ============================================================ */

class HeroAnimation {
  constructor(canvasId) {
    this.canvas = document.getElementById(canvasId);
    this.ctx = this.canvas.getContext('2d');
    this.t = 0;
    this.progress = 0; // 0 = full desert, 1 = full oasis
    this.autoProgress = true;
    this.W = 0; this.H = 0;

    // Scene elements
    this.plants = [];
    this.palms = [];
    this.particles = [];
    this.stars = [];
    this.waterRipples = [];

    this.init();
  }

  init() {
    this.resize();
    window.addEventListener('resize', () => this.resize());
    this.buildStars();
    this.buildParticles();
    this.animate();
  }

  resize() {
    this.W = this.canvas.width = window.innerWidth;
    this.H = this.canvas.height = window.innerHeight;
    this.groundY = this.H * 0.62;
  }

  buildStars() {
    this.stars = [];
    for (let i = 0; i < 180; i++) {
      this.stars.push({
        x: Math.random(),
        y: Math.random() * 0.45,
        r: Math.random() * 1.5 + 0.3,
        phase: Math.random() * Math.PI * 2,
        speed: 0.015 + Math.random() * 0.02,
      });
    }
  }

  buildParticles() {
    this.particles = [];
    // Dust + pollen
    for (let i = 0; i < 80; i++) {
      const isGreen = i > 40;
      this.particles.push({
        x: Math.random() * 2,
        y: 0.5 + Math.random() * 0.5,
        vx: (Math.random() - 0.5) * 0.0008,
        vy: -(Math.random() * 0.001 + 0.0002),
        size: Math.random() * 3 + 1,
        green: isGreen,
        life: Math.random(),
        maxLife: 0.4 + Math.random() * 0.6,
        alpha: Math.random() * 0.6 + 0.2,
      });
    }
  }

  drawSky() {
    const { ctx, W, H, progress, t } = this;
    const gY = H * 0.62;

    // Sky gradient: sunset desert → twilight → night-green
    const grad = ctx.createLinearGradient(0, 0, 0, gY);
    const desertTop = `rgba(180,80,20,`;
    const desertMid = `rgba(220,130,40,`;
    const desertHor = `rgba(255,160,60,`;

    const oasisTop = `rgba(5,15,8,`;
    const oasisMid = `rgba(8,25,12,`;
    const oasisHor = `rgba(30,80,30,`;

    const lerp = (a, b, t) => a + (b - a) * t;

    // Blend colours based on progress
    const topR = lerp(180, 5, progress);
    const topG = lerp(80, 15, progress);
    const topB = lerp(20, 8, progress);

    const midR = lerp(220, 8, progress);
    const midG = lerp(130, 25, progress);
    const midB = lerp(40, 12, progress);

    const horR = lerp(255, 30, progress);
    const horG = lerp(160, 80, progress);
    const horB = lerp(60, 30, progress);

    grad.addColorStop(0, `rgb(${topR},${topG},${topB})`);
    grad.addColorStop(0.5, `rgb(${midR},${midG},${midB})`);
    grad.addColorStop(1, `rgb(${horR},${horG},${horB})`);
    ctx.fillStyle = grad;
    ctx.fillRect(0, 0, W, gY);

    // Sun / Moon
    if (progress < 0.5) {
      // Sun setting
      const sunY = gY - (1 - progress * 2) * H * 0.3;
      const sunR = 40 - progress * 20;
      const sunGrad = ctx.createRadialGradient(W * 0.72, sunY, 0, W * 0.72, sunY, sunR * 3);
      sunGrad.addColorStop(0, `rgba(255,220,80,${0.9 - progress})`);
      sunGrad.addColorStop(0.3, `rgba(255,150,30,${0.5 - progress * 0.3})`);
      sunGrad.addColorStop(1, 'rgba(255,100,20,0)');
      ctx.fillStyle = sunGrad;
      ctx.fillRect(0, 0, W, gY);
    } else {
      // Moon rising
      const mp = (progress - 0.5) * 2;
      const moonY = gY * 0.25 - mp * gY * 0.15;
      ctx.beginPath();
      ctx.arc(W * 0.78, moonY, 18, 0, Math.PI * 2);
      ctx.fillStyle = `rgba(200,230,210,${mp * 0.6})`;
      ctx.fill();
      // Moon glow
      const mg = ctx.createRadialGradient(W * 0.78, moonY, 0, W * 0.78, moonY, 60);
      mg.addColorStop(0, `rgba(120,200,140,${mp * 0.12})`);
      mg.addColorStop(1, 'rgba(120,200,140,0)');
      ctx.fillStyle = mg;
      ctx.fillRect(0, 0, W, H);
    }
  }

  drawStars() {
    const { ctx, W, H, progress, t } = this;
    const gY = H * 0.62;
    this.stars.forEach(s => {
      s.phase += s.speed * 0.05;
      const alpha = Math.min(progress * 2, 1) * (0.3 + Math.sin(s.phase) * 0.3);
      ctx.beginPath();
      ctx.arc(s.x * W, s.y * gY, s.r, 0, Math.PI * 2);
      ctx.fillStyle = `rgba(180,240,200,${alpha})`;
      ctx.fill();
    });
  }

  drawMountains() {
    const { ctx, W, H, progress, t } = this;
    const gY = H * 0.62;

    // Mountain silhouettes
    const mountains = [
      { x: 0.0, h: 0.22, w: 0.35 },
      { x: 0.2, h: 0.32, w: 0.3 },
      { x: 0.45, h: 0.18, w: 0.25 },
      { x: 0.6, h: 0.28, w: 0.4 },
      { x: 0.85, h: 0.20, w: 0.3 },
    ];

    mountains.forEach((m, i) => {
      // Color shifts from orange-purple to dark blue-green
      const r = Math.round(80 - progress * 60);
      const g = Math.round(40 + progress * 30);
      const b = Math.round(60 + progress * 20);
      ctx.fillStyle = `rgb(${r},${g},${b})`;

      ctx.beginPath();
      const mx = m.x * W;
      const my = gY - m.h * H;
      const mw = m.w * W;
      ctx.moveTo(mx, gY);
      ctx.lineTo(mx + mw * 0.5, my);
      ctx.lineTo(mx + mw, gY);
      ctx.closePath();
      ctx.fill();
    });

    // Distant range (lighter)
    ctx.fillStyle = `rgba(${60 - progress * 40},${50 + progress * 40},${70 + progress * 30},0.5)`;
    ctx.beginPath();
    ctx.moveTo(0, gY);
    for (let x = 0; x <= W; x += 40) {
      const y = gY - Math.abs(Math.sin(x * 0.006 + 0.5) * H * 0.14 + Math.sin(x * 0.011 + 1) * H * 0.06);
      ctx.lineTo(x, y);
    }
    ctx.lineTo(W, gY);
    ctx.closePath();
    ctx.fill();
  }

  drawGround() {
    const { ctx, W, H, progress, t } = this;
    const gY = H * 0.62;

    // Ground gradient — sand to green
    const grad = ctx.createLinearGradient(0, gY, 0, H);
    const topR = Math.round(80 + (1 - progress) * 80);
    const topG = Math.round(60 + progress * 80);
    const topB = Math.round(20 + progress * 10);
    const botR = Math.round(50 + (1 - progress) * 60);
    const botG = Math.round(35 + progress * 55);
    const botB = Math.round(10);
    grad.addColorStop(0, `rgb(${topR},${topG},${topB})`);
    grad.addColorStop(1, `rgb(${botR},${botG},${botB})`);
    ctx.fillStyle = grad;

    // Wavy ground line
    ctx.beginPath();
    ctx.moveTo(0, gY);
    for (let x = 0; x <= W; x += 20) {
      const wave = Math.sin(x * 0.01 + t * 0.3) * 4 + Math.sin(x * 0.02 + t * 0.2) * 2;
      ctx.lineTo(x, gY + wave);
    }
    ctx.lineTo(W, H);
    ctx.lineTo(0, H);
    ctx.closePath();
    ctx.fill();

    // Cracked ground texture (fades with progress)
    if (progress < 0.7) {
      const crackAlpha = (1 - progress / 0.7) * 0.25;
      ctx.strokeStyle = `rgba(40,20,5,${crackAlpha})`;
      ctx.lineWidth = 0.8;
      // Draw cracks
      const cracks = [
        [[0.1,0.68],[0.15,0.75],[0.12,0.82]],
        [[0.3,0.70],[0.35,0.78],[0.32,0.88]],
        [[0.5,0.65],[0.55,0.72],[0.53,0.80]],
        [[0.68,0.70],[0.73,0.76],[0.70,0.85]],
        [[0.82,0.67],[0.87,0.74],[0.85,0.83]],
        [[0.2,0.73],[0.25,0.68],[0.28,0.76]],
        [[0.45,0.78],[0.42,0.85],[0.48,0.82]],
      ];
      cracks.forEach(pts => {
        ctx.beginPath();
        pts.forEach(([rx, ry], i) => {
          const px = rx * W + Math.sin(rx * 10 + t * 0.1) * 3;
          const py = ry * H;
          i === 0 ? ctx.moveTo(px, py) : ctx.lineTo(px, py);
        });
        ctx.stroke();
      });
    }

    // Sand dunes (fade out with progress)
    if (progress < 0.6) {
      const duneAlpha = 1 - progress / 0.6;
      const dunes = [
        { x: 0.1, w: 0.25, h: 0.06 },
        { x: 0.35, w: 0.20, h: 0.04 },
        { x: 0.6, w: 0.30, h: 0.07 },
        { x: 0.82, w: 0.22, h: 0.05 },
      ];
      dunes.forEach(d => {
        const g2 = ctx.createLinearGradient(0, gY, 0, gY + d.h * H);
        g2.addColorStop(0, `rgba(160,100,40,${duneAlpha * 0.6})`);
        g2.addColorStop(1, `rgba(120,70,20,0)`);
        ctx.fillStyle = g2;
        ctx.beginPath();
        ctx.ellipse(d.x * W + d.w * W * 0.5, gY + 10, d.w * W * 0.5, d.h * H, 0, 0, Math.PI);
        ctx.fill();
      });
    }

    // Grass patches (appear with progress)
    if (progress > 0.2) {
      const gp = Math.min((progress - 0.2) / 0.8, 1);
      const patches = [0.08, 0.18, 0.28, 0.4, 0.52, 0.62, 0.72, 0.82, 0.92];
      patches.forEach((px, i) => {
        const x = px * W;
        const bladeCount = 6;
        for (let b = 0; b < bladeCount; b++) {
          const bx = x + (b - bladeCount / 2) * 8 + Math.sin(i * 5) * 5;
          const by = gY + 8 + Math.sin(i * 3) * 5;
          const bh = (15 + Math.sin(i * 7 + b) * 8) * gp;
          const sway = Math.sin(this.t * 1.5 + i * 0.7 + b * 0.3) * 3;
          ctx.beginPath();
          ctx.moveTo(bx, by);
          ctx.quadraticCurveTo(bx + sway, by - bh * 0.5, bx + sway * 1.5, by - bh);
          ctx.strokeStyle = `rgba(${60 + Math.sin(i) * 20},${130 + Math.sin(i * 0.5) * 40},${40},${gp * 0.8})`;
          ctx.lineWidth = 1.5;
          ctx.lineCap = 'round';
          ctx.stroke();
        }
      });
    }
  }

  drawWater() {
    const { ctx, W, H, progress, t } = this;
    if (progress < 0.4) return;

    const wp = Math.min((progress - 0.4) / 0.6, 1);
    const gY = H * 0.62;

    // Oasis pool
    const poolCX = W * 0.48, poolCY = gY + H * 0.06;
    const poolRX = W * 0.12 * wp, poolRY = H * 0.03 * wp;

    // Water reflection
    const wg = ctx.createRadialGradient(poolCX, poolCY, 0, poolCX, poolCY, poolRX);
    wg.addColorStop(0, `rgba(64,164,220,${wp * 0.85})`);
    wg.addColorStop(0.5, `rgba(30,100,180,${wp * 0.7})`);
    wg.addColorStop(1, `rgba(20,60,120,${wp * 0.4})`);
    ctx.fillStyle = wg;
    ctx.beginPath();
    ctx.ellipse(poolCX, poolCY, poolRX, poolRY, 0, 0, Math.PI * 2);
    ctx.fill();

    // Ripples
    for (let r = 0; r < 3; r++) {
      const rPhase = (t * 0.8 + r * 0.7) % (Math.PI * 2);
      const rScale = (rPhase / (Math.PI * 2));
      ctx.beginPath();
      ctx.ellipse(poolCX, poolCY, poolRX * (0.4 + rScale * 0.7), poolRY * (0.4 + rScale * 0.7), 0, 0, Math.PI * 2);
      ctx.strokeStyle = `rgba(130,200,240,${(1 - rScale) * wp * 0.4})`;
      ctx.lineWidth = 1;
      ctx.stroke();
    }

    // Shimmer highlights
    for (let s = 0; s < 8; s++) {
      const sx = poolCX + Math.cos(t * 0.5 + s) * poolRX * 0.5;
      const sy = poolCY + Math.sin(t * 0.3 + s) * poolRY * 0.5;
      const sr = (1 + Math.sin(t * 2 + s)) * 2;
      ctx.beginPath();
      ctx.arc(sx, sy, sr, 0, Math.PI * 2);
      ctx.fillStyle = `rgba(200,240,255,${wp * 0.6})`;
      ctx.fill();
    }
  }

  drawSmallPlants() {
    const { ctx, W, H, progress, t } = this;
    if (progress < 0.1) return;

    const gY = H * 0.62;
    const pp = Math.min(progress / 0.5, 1);

    const plantSpots = [
      { x: 0.15, delay: 0.0 }, { x: 0.25, delay: 0.05 },
      { x: 0.35, delay: 0.1 }, { x: 0.55, delay: 0.08 },
      { x: 0.65, delay: 0.12 }, { x: 0.75, delay: 0.06 },
      { x: 0.85, delay: 0.04 }, { x: 0.92, delay: 0.15 },
      { x: 0.05, delay: 0.09 }, { x: 0.44, delay: 0.11 },
    ];

    plantSpots.forEach((sp, i) => {
      const localP = Math.max(0, Math.min((pp - sp.delay) / (1 - sp.delay), 1));
      if (localP <= 0) return;

      const px = sp.x * W;
      const py = gY + 5 + Math.sin(i * 3.7) * 8;
      const stems = 3 + (i % 3);

      for (let s = 0; s < stems; s++) {
        const offset = (s - stems / 2) * 8;
        const stemH = (20 + Math.sin(i * 2 + s) * 10) * localP;
        const sway = Math.sin(t * 1.2 + i * 0.8 + s * 0.5) * 4;

        ctx.beginPath();
        ctx.moveTo(px + offset, py);
        ctx.quadraticCurveTo(px + offset + sway, py - stemH * 0.6, px + offset + sway * 1.5, py - stemH);
        const r = 30 + Math.sin(i) * 20;
        const g = 120 + Math.sin(i * 0.5) * 40;
        ctx.strokeStyle = `rgba(${r},${g},30,${localP * 0.9})`;
        ctx.lineWidth = 2;
        ctx.lineCap = 'round';
        ctx.stroke();

        // Leaf tip
        if (localP > 0.6) {
          const lp = (localP - 0.6) / 0.4;
          ctx.beginPath();
          ctx.ellipse(px + offset + sway * 1.5, py - stemH - 3, 5 * lp, 3 * lp, sway * 0.2, 0, Math.PI * 2);
          ctx.fillStyle = `rgba(60,160,40,${lp * 0.8})`;
          ctx.fill();
        }
      }
    });
  }

  drawPalmTrees() {
    const { ctx, W, H, progress, t } = this;
    if (progress < 0.35) return;

    const gY = H * 0.62;
    const palmData = [
      { x: 0.32, h: 0.32, lean: -0.08, delay: 0.35 },
      { x: 0.48, h: 0.38, lean: 0.02,  delay: 0.45 },
      { x: 0.56, h: 0.30, lean: 0.06,  delay: 0.40 },
      { x: 0.68, h: 0.34, lean: -0.04, delay: 0.55 },
      { x: 0.22, h: 0.26, lean: -0.10, delay: 0.60 },
      { x: 0.79, h: 0.36, lean: 0.09,  delay: 0.50 },
      { x: 0.88, h: 0.28, lean: 0.12,  delay: 0.58 },
    ];

    palmData.forEach((pd, i) => {
      const localP = Math.max(0, Math.min((progress - pd.delay) / (1 - pd.delay), 1));
      if (localP <= 0) return;

      const px = pd.x * W;
      const baseY = gY + 8;
      const palmH = pd.h * H * localP;
      const topX = px + pd.lean * W * localP;
      const topY = baseY - palmH;

      // Trunk
      ctx.save();
      ctx.beginPath();
      ctx.moveTo(px - 6, baseY);
      ctx.quadraticCurveTo(px + pd.lean * W * 0.5, baseY - palmH * 0.5, topX, topY);
      ctx.quadraticCurveTo(topX - pd.lean * W * 0.3, baseY - palmH * 0.5, px + 6, baseY);
      const trunkG = ctx.createLinearGradient(px - 6, baseY, topX, topY);
      trunkG.addColorStop(0, `rgba(90,55,20,${localP})`);
      trunkG.addColorStop(0.5, `rgba(110,70,25,${localP})`);
      trunkG.addColorStop(1, `rgba(80,50,18,${localP})`);
      ctx.fillStyle = trunkG;
      ctx.fill();

      // Trunk rings
      for (let r = 0.15; r < 1; r += 0.12) {
        const rx = px + (topX - px) * r;
        const ry = baseY + (topY - baseY) * r;
        const rw = (6 - r * 4) * localP;
        ctx.beginPath();
        ctx.ellipse(rx, ry, rw, 2, 0, 0, Math.PI * 2);
        ctx.fillStyle = `rgba(60,35,10,${localP * 0.4})`;
        ctx.fill();
      }

      // Fronds
      if (localP > 0.4) {
        const fp = (localP - 0.4) / 0.6;
        const frondCount = 7;
        const windSway = Math.sin(t * 0.8 + i * 1.2) * 0.22;

        for (let f = 0; f < frondCount; f++) {
          const angle = (f / frondCount) * Math.PI * 2 + windSway + t * 0.05;
          const fLen = (80 + Math.sin(i * 3 + f) * 20) * fp;
          const ex = topX + Math.cos(angle) * fLen;
          const ey = topY + Math.sin(angle) * fLen * 0.6 + fLen * 0.35;

          // Frond stem
          ctx.beginPath();
          ctx.moveTo(topX, topY);
          ctx.quadraticCurveTo(topX + Math.cos(angle) * fLen * 0.5, topY + Math.sin(angle) * fLen * 0.25 - 10, ex, ey);
          ctx.strokeStyle = `rgba(30,80,20,${fp * 0.9})`;
          ctx.lineWidth = 2.5;
          ctx.lineCap = 'round';
          ctx.stroke();

          // Leaflets
          const leafCount = 5;
          for (let l = 0; l < leafCount; l++) {
            const lt = (l + 1) / (leafCount + 1);
            const lx = topX + (ex - topX) * lt;
            const ly = topY + (ey - topY) * lt;
            const leafA = angle + (l % 2 === 0 ? 0.5 : -0.5);
            const leafL = 15 * fp;

            ctx.beginPath();
            ctx.moveTo(lx, ly);
            ctx.lineTo(lx + Math.cos(leafA) * leafL, ly + Math.sin(leafA) * leafL);
            ctx.strokeStyle = `rgba(${40 + l * 5},${130 + l * 8},${25},${fp * 0.7})`;
            ctx.lineWidth = 2;
            ctx.stroke();
          }
        }
      }

      ctx.restore();
    });
  }

  drawParticles() {
    const { ctx, W, H, progress, t } = this;
    this.particles.forEach(p => {
      p.life += 0.004;
      if (p.life > p.maxLife) {
        p.life = 0;
        p.x = Math.random() * 1.2;
        p.y = 0.55 + Math.random() * 0.45;
      }
      p.x += p.vx;
      p.y += p.vy;

      const lifeRatio = 1 - (p.life / p.maxLife);
      const alpha = lifeRatio * p.alpha;

      if (p.green) {
        // Pollen / spores (appear more with progress)
        const a = alpha * Math.min(progress * 2, 1);
        ctx.beginPath();
        ctx.arc(p.x * W, p.y * H, p.size * 0.8, 0, Math.PI * 2);
        ctx.fillStyle = `rgba(100,200,80,${a})`;
        ctx.fill();
      } else {
        // Dust (disappears with progress)
        const a = alpha * (1 - progress * 0.8);
        ctx.beginPath();
        ctx.arc(p.x * W, p.y * H, p.size, 0, Math.PI * 2);
        ctx.fillStyle = `rgba(180,130,60,${a})`;
        ctx.fill();
      }
    });
  }

  drawAtmosphere() {
    const { ctx, W, H, progress } = this;
    if (progress < 0.3) return;
    const ap = Math.min((progress - 0.3) / 0.7, 1);

    // Green mist rising from ground
    const gY = H * 0.62;
    const mistGrad = ctx.createLinearGradient(0, gY, 0, gY - H * 0.25);
    mistGrad.addColorStop(0, `rgba(30,80,30,${ap * 0.25})`);
    mistGrad.addColorStop(1, 'rgba(30,80,30,0)');
    ctx.fillStyle = mistGrad;
    ctx.fillRect(0, gY - H * 0.25, W, H * 0.25);
  }

  frame() {
    const { ctx, W, H } = this;
    ctx.clearRect(0, 0, W, H);
    this.t += 0.016;

    // Auto-advance progress (full cycle in ~8 seconds)
    if (this.autoProgress) {
      this.progress = Math.min(this.progress + 0.007, 1);//////////////////////////////////////////////////////////////////////////
    }

    this.drawSky();
    this.drawStars();
    this.drawMountains();
    this.drawAtmosphere();
    this.drawGround();
    this.drawWater();
    this.drawSmallPlants();
    this.drawPalmTrees();
    this.drawParticles();
  }

  animate() {
    this.frame();
    requestAnimationFrame(() => this.animate());
  }
}

// Export for use
window.HeroAnimation = HeroAnimation;

/* ============================================================
   OASIS — Section Canvases & Scroll Animations
   ============================================================ */

/* ── ABOUT CANVAS — Growing ecosystem ── */
class AboutCanvas {
  constructor(id) {
    this.canvas = document.getElementById(id);
    if (!this.canvas) return;
    this.ctx = this.canvas.getContext('2d');
    this.t = 0;
    this.resize();
    window.addEventListener('resize', () => this.resize());
    this.animate();
  }
  resize() {
    this.W = this.canvas.width = this.canvas.offsetWidth;
    this.H = this.canvas.height = this.canvas.offsetHeight;
  }
  draw() {
    const { ctx, W, H, t } = this;
    ctx.clearRect(0, 0, W, H);

    // Deep forest bg
    const bg = ctx.createRadialGradient(W/2, H/2, 0, W/2, H/2, W*0.7);
    bg.addColorStop(0, '#0d2810');
    bg.addColorStop(1, '#050f06');
    ctx.fillStyle = bg;
    ctx.fillRect(0, 0, W, H);

    // Ground
    const gY = H * 0.72;
    const gg = ctx.createLinearGradient(0, gY, 0, H);
    gg.addColorStop(0, '#1a4020');
    gg.addColorStop(1, '#0a1a0a');
    ctx.fillStyle = gg;
    ctx.beginPath();
    ctx.moveTo(0, gY);
    for (let x = 0; x <= W; x += 15) {
      ctx.lineTo(x, gY + Math.sin(x * 0.02 + t * 0.5) * 4);
    }
    ctx.lineTo(W, H); ctx.lineTo(0, H); ctx.closePath();
    ctx.fill();

    // Trees (simplified)
    const trees = [
      { x: 0.15, h: 0.55 }, { x: 0.3, h: 0.65 },
      { x: 0.5, h: 0.70 }, { x: 0.7, h: 0.60 },
      { x: 0.85, h: 0.50 },
    ];

    trees.forEach((tr, i) => {
      const tx = tr.x * W;
      const base = gY;
      const palmH = tr.h * H;
      const sway = Math.sin(t * 0.6 + i * 1.4) * 6;

      // Trunk
      ctx.beginPath();
      ctx.moveTo(tx, base);
      ctx.quadraticCurveTo(tx + sway * 0.3, base - palmH * 0.5, tx + sway, base - palmH);
      ctx.strokeStyle = 'rgba(70,40,15,.8)';
      ctx.lineWidth = 5 - i * 0.3;
      ctx.lineCap = 'round';
      ctx.stroke();

      // Canopy glow
      const cg = ctx.createRadialGradient(tx + sway, base - palmH, 0, tx + sway, base - palmH, 40);
      cg.addColorStop(0, `rgba(60,160,50,${0.4 + Math.sin(t + i) * 0.1})`);
      cg.addColorStop(1, 'rgba(60,160,50,0)');
      ctx.fillStyle = cg;
      ctx.fillRect(tx + sway - 40, base - palmH - 40, 80, 80);

      // Fronds
      for (let f = 0; f < 6; f++) {
        const fa = (f / 6) * Math.PI * 2 + sway * 0.02;
        const fl = 35 + Math.sin(i * 2 + f) * 10;
        ctx.beginPath();
        ctx.moveTo(tx + sway, base - palmH);
        ctx.lineTo(
          tx + sway + Math.cos(fa) * fl,
          base - palmH + Math.sin(fa) * fl * 0.4 + fl * 0.2
        );
        ctx.strokeStyle = `rgba(${40 + f * 5},${130 + f * 5},30,0.7)`;
        ctx.lineWidth = 2;
        ctx.stroke();
      }
    });

    // Fireflies / bioluminescent particles
    for (let i = 0; i < 20; i++) {
      const fx = (Math.sin(t * 0.3 + i * 2.4) * 0.4 + 0.5) * W;
      const fy = gY * 0.3 + (Math.cos(t * 0.25 + i * 1.7) * 0.35 + 0.5) * gY * 0.7;
      const glow = 0.3 + Math.sin(t * 2 + i) * 0.3;
      ctx.beginPath();
      ctx.arc(fx, fy, 2, 0, Math.PI * 2);
      ctx.fillStyle = `rgba(150,255,120,${glow})`;
      ctx.fill();
      const fg = ctx.createRadialGradient(fx, fy, 0, fx, fy, 10);
      fg.addColorStop(0, `rgba(100,220,80,${glow * 0.3})`);
      fg.addColorStop(1, 'rgba(100,220,80,0)');
      ctx.fillStyle = fg;
      ctx.fillRect(fx - 10, fy - 10, 20, 20);
    }

    // Stars
    for (let i = 0; i < 60; i++) {
      const sx = ((i * 137.5) % 1) * W;
      const sy = ((i * 73.1) % 0.4) * H;
      const sa = 0.2 + Math.sin(t * 1.5 + i) * 0.2;
      ctx.beginPath();
      ctx.arc(sx, sy, 1, 0, Math.PI * 2);
      ctx.fillStyle = `rgba(200,240,210,${sa})`;
      ctx.fill();
    }
  }
  animate() { this.t += 0.016; this.draw(); requestAnimationFrame(() => this.animate()); }
}

/* ── FUTURE CITIES CANVAS — Sci-fi green cityscape ── */
class FutureCitiesCanvas {
  constructor(id) {
    this.canvas = document.getElementById(id);
    if (!this.canvas) return;
    this.ctx = this.canvas.getContext('2d');
    this.t = 0;
    this.resize();
    window.addEventListener('resize', () => this.resize());
    this.animate();
  }
  resize() {
    this.W = this.canvas.width = this.canvas.offsetWidth;
    this.H = this.canvas.height = this.canvas.offsetHeight;
  }
  draw() {
    const { ctx, W, H, t } = this;
    ctx.clearRect(0, 0, W, H);

    // Night sky
    const sky = ctx.createLinearGradient(0, 0, 0, H);
    sky.addColorStop(0, '#020d08');
    sky.addColorStop(0.6, '#041a0e');
    sky.addColorStop(1, '#0a2814');
    ctx.fillStyle = sky;
    ctx.fillRect(0, 0, W, H);

    // Nebula / aurora
    const aurora = ctx.createRadialGradient(W * 0.5, H * 0.2, 0, W * 0.5, H * 0.2, W * 0.6);
    aurora.addColorStop(0, `rgba(30,120,60,${0.06 + Math.sin(t * 0.3) * 0.02})`);
    aurora.addColorStop(0.5, `rgba(20,80,40,${0.04 + Math.sin(t * 0.2) * 0.02})`);
    aurora.addColorStop(1, 'rgba(0,0,0,0)');
    ctx.fillStyle = aurora;
    ctx.fillRect(0, 0, W, H);

    // Stars
    for (let i = 0; i < 120; i++) {
      const sx = ((i * 137.508) % 1) * W;
      const sy = ((i * 29.173) % 0.55) * H;
      const sa = 0.2 + Math.sin(t * 1.2 + i * 0.4) * 0.25;
      ctx.beginPath();
      ctx.arc(sx, sy, 0.8 + (i % 3) * 0.4, 0, Math.PI * 2);
      ctx.fillStyle = `rgba(200,240,220,${sa})`;
      ctx.fill();
    }

    const gY = H * 0.6;

    // Buildings
    const buildings = [
      { x: 0.04, w: 0.06, h: 0.28, floors: 8 },
      { x: 0.11, w: 0.05, h: 0.38, floors: 12 },
      { x: 0.17, w: 0.08, h: 0.22, floors: 6 },
      { x: 0.26, w: 0.06, h: 0.50, floors: 15 },
      { x: 0.33, w: 0.07, h: 0.35, floors: 10 },
      { x: 0.41, w: 0.05, h: 0.44, floors: 14 },
      { x: 0.47, w: 0.10, h: 0.62, floors: 20 }, // tallest
      { x: 0.58, w: 0.07, h: 0.48, floors: 16 },
      { x: 0.66, w: 0.06, h: 0.36, floors: 11 },
      { x: 0.73, w: 0.08, h: 0.28, floors: 8 },
      { x: 0.82, w: 0.05, h: 0.42, floors: 13 },
      { x: 0.88, w: 0.07, h: 0.32, floors: 9 },
      { x: 0.95, w: 0.05, h: 0.24, floors: 7 },
    ];

    buildings.forEach((b, i) => {
      const bx = b.x * W;
      const bw = b.w * W;
      const bh = b.h * H;
      const by = gY - bh;

      // Building body
      const bg2 = ctx.createLinearGradient(bx, by, bx + bw, by);
      bg2.addColorStop(0, `rgba(${15 + i * 2},${35 + i},${20 + i * 2},.95)`);
      bg2.addColorStop(0.5, `rgba(${20 + i * 2},${45 + i},${25 + i * 2},.95)`);
      bg2.addColorStop(1, `rgba(${10 + i * 2},${28 + i},${15 + i * 2},.95)`);
      ctx.fillStyle = bg2;
      ctx.fillRect(bx, by, bw, bh);

      // Windows grid
      const wCols = Math.floor(bw / 10);
      const wRows = b.floors;
      for (let wr = 0; wr < wRows; wr++) {
        for (let wc = 0; wc < wCols; wc++) {
          const wx = bx + wc * (bw / wCols) + 2;
          const wy = by + wr * (bh / wRows) + 3;
          const ww = bw / wCols - 4;
          const wh = bh / wRows - 5;
          const lit = Math.sin(i * 7.3 + wr * 2.1 + wc * 3.7 + t * 0.1) > 0.2;
          if (lit) {
            const wc2 = Math.random() < 0.1 ? `rgba(150,255,150,` : `rgba(80,200,120,`;
            ctx.fillStyle = `${wc2}${0.5 + Math.sin(t * 0.5 + wr + wc) * 0.2})`;
            ctx.fillRect(wx, wy, Math.max(ww, 2), Math.max(wh, 2));
          }
        }
      }

      // Vertical green light strip
      const strip = ctx.createLinearGradient(bx + bw * 0.9, by, bx + bw * 0.9, gY);
      strip.addColorStop(0, `rgba(60,200,80,${0.3 + Math.sin(t * 0.4 + i) * 0.1})`);
      strip.addColorStop(1, 'rgba(60,200,80,0)');
      ctx.fillStyle = strip;
      ctx.fillRect(bx + bw * 0.88, by, bw * 0.04, bh);

      // Rooftop garden glow
      const roofGlow = ctx.createRadialGradient(bx + bw / 2, by, 0, bx + bw / 2, by, bw * 2);
      roofGlow.addColorStop(0, `rgba(60,180,60,${0.15 + Math.sin(t * 0.3 + i * 0.7) * 0.05})`);
      roofGlow.addColorStop(1, 'rgba(60,180,60,0)');
      ctx.fillStyle = roofGlow;
      ctx.fillRect(bx - bw, by - bw, bw * 3, bw * 2);

      // Antenna / spire on tall buildings
      if (b.h > 0.4) {
        ctx.beginPath();
        ctx.moveTo(bx + bw / 2, by);
        ctx.lineTo(bx + bw / 2, by - 20);
        ctx.strokeStyle = `rgba(100,220,140,0.6)`;
        ctx.lineWidth = 1.5;
        ctx.stroke();
        // Blinking light
        const blink = Math.sin(t * 3 + i) > 0.7;
        ctx.beginPath();
        ctx.arc(bx + bw / 2, by - 22, 2.5, 0, Math.PI * 2);
        ctx.fillStyle = blink ? 'rgba(255,120,120,.9)' : 'rgba(255,120,120,.2)';
        ctx.fill();
      }
    });

    // Ground / street
    const st = ctx.createLinearGradient(0, gY, 0, H);
    st.addColorStop(0, '#0d2010');
    st.addColorStop(1, '#060e07');
    ctx.fillStyle = st;
    ctx.fillRect(0, gY, W, H - gY);

    // Street lights & ground glow
    for (let s = 0; s < 8; s++) {
      const sx = (s / 7) * W;
      // Pole
      ctx.beginPath();
      ctx.moveTo(sx, gY + 2);
      ctx.lineTo(sx, gY - 40);
      ctx.strokeStyle = 'rgba(60,80,60,0.6)';
      ctx.lineWidth = 2;
      ctx.stroke();
      // Light
      const slg = ctx.createRadialGradient(sx, gY - 42, 0, sx, gY - 42, 30);
      slg.addColorStop(0, `rgba(120,240,140,${0.4 + Math.sin(t * 0.5 + s) * 0.1})`);
      slg.addColorStop(1, 'rgba(120,240,140,0)');
      ctx.fillStyle = slg;
      ctx.fillRect(sx - 30, gY - 72, 60, 60);
    }

    // Floating drones
    for (let d = 0; d < 4; d++) {
      const dx = W * (0.1 + d * 0.25) + Math.sin(t * 0.4 + d * 2) * 40;
      const dy = H * (0.2 + d * 0.06) + Math.cos(t * 0.3 + d * 1.5) * 15;
      ctx.beginPath();
      ctx.arc(dx, dy, 3, 0, Math.PI * 2);
      ctx.fillStyle = 'rgba(200,255,200,0.7)';
      ctx.fill();
      // Trail
      const trail = ctx.createLinearGradient(dx - 20, dy, dx, dy);
      trail.addColorStop(0, 'rgba(100,220,120,0)');
      trail.addColorStop(1, 'rgba(100,220,120,0.4)');
      ctx.fillStyle = trail;
      ctx.fillRect(dx - 20, dy - 1, 20, 2);
    }

    // Reflection in water
    const refGrad = ctx.createLinearGradient(0, gY, 0, H);
    refGrad.addColorStop(0, 'rgba(20,80,40,0.3)');
    refGrad.addColorStop(1, 'rgba(5,20,10,0.8)');
    ctx.fillStyle = refGrad;
    ctx.fillRect(0, gY + 2, W, H - gY);

    // Ripple lines
    for (let r = 0; r < 5; r++) {
      const ry = gY + 10 + r * 15 + Math.sin(t * 0.8 + r) * 3;
      ctx.beginPath();
      ctx.moveTo(0, ry);
      for (let x = 0; x <= W; x += 20) {
        ctx.lineTo(x, ry + Math.sin(x * 0.03 + t * 1.2) * 1.5);
      }
      ctx.strokeStyle = `rgba(60,150,80,${0.08 - r * 0.01})`;
      ctx.lineWidth = 1;
      ctx.stroke();
    }
  }
  animate() { this.t += 0.016; this.draw(); requestAnimationFrame(() => this.animate()); }
}

/* ── CTA CANVAS — Particle field ── */
class CtaCanvas {
  constructor(id) {
    this.canvas = document.getElementById(id);
    if (!this.canvas) return;
    this.ctx = this.canvas.getContext('2d');
    this.t = 0;
    this.pts = [];
    this.resize();
    window.addEventListener('resize', () => this.resize());
    for (let i = 0; i < 60; i++) {
      this.pts.push({
        x: Math.random(), y: Math.random(),
        vx: (Math.random() - 0.5) * 0.0003,
        vy: -Math.random() * 0.0004 - 0.0001,
        size: Math.random() * 3 + 1,
        phase: Math.random() * Math.PI * 2,
      });
    }
    this.animate();
  }
  resize() {
    this.W = this.canvas.width = this.canvas.offsetWidth;
    this.H = this.canvas.height = this.canvas.offsetHeight;
  }
  draw() {
    const { ctx, W, H, t } = this;
    ctx.clearRect(0, 0, W, H);

    // Gradient bg
    const g = ctx.createLinearGradient(0, 0, 0, H);
    g.addColorStop(0, '#010a02');
    g.addColorStop(1, '#020f04');
    ctx.fillStyle = g;
    ctx.fillRect(0, 0, W, H);

    // Center glow
    const cg = ctx.createRadialGradient(W/2, H/2, 0, W/2, H/2, W*0.4);
    cg.addColorStop(0, `rgba(40,140,60,${0.08 + Math.sin(t * 0.3) * 0.03})`);
    cg.addColorStop(1, 'rgba(0,0,0,0)');
    ctx.fillStyle = cg;
    ctx.fillRect(0, 0, W, H);

    // Particles + connections
    this.pts.forEach(p => {
      p.x += p.vx; p.y += p.vy;
      if (p.y < -0.1) { p.y = 1.1; p.x = Math.random(); }
      if (p.x < 0 || p.x > 1) p.vx *= -1;
      p.phase += 0.02;

      const px = p.x * W, py = p.y * H;
      const a = 0.3 + Math.sin(p.phase) * 0.3;

      ctx.beginPath();
      ctx.arc(px, py, p.size, 0, Math.PI * 2);
      ctx.fillStyle = `rgba(80,200,100,${a})`;
      ctx.fill();
    });

    // Draw connections
    for (let i = 0; i < this.pts.length; i++) {
      for (let j = i + 1; j < this.pts.length; j++) {
        const dx = (this.pts[i].x - this.pts[j].x) * W;
        const dy = (this.pts[i].y - this.pts[j].y) * H;
        const dist = Math.sqrt(dx*dx + dy*dy);
        if (dist < 120) {
          ctx.beginPath();
          ctx.moveTo(this.pts[i].x * W, this.pts[i].y * H);
          ctx.lineTo(this.pts[j].x * W, this.pts[j].y * H);
          ctx.strokeStyle = `rgba(60,160,80,${(1 - dist/120) * 0.12})`;
          ctx.lineWidth = 0.5;
          ctx.stroke();
        }
      }
    }
  }
  animate() { this.t += 0.016; this.draw(); requestAnimationFrame(() => this.animate()); }
}

/* ── AUTH CANVAS — Desert night with shooting stars ── */
class AuthCanvas {
  constructor(id) {
    this.canvas = document.getElementById(id);
    if (!this.canvas) return;
    this.ctx = this.canvas.getContext('2d');
    this.t = 0;
    this.shooters = [];
    this.resize();
    window.addEventListener('resize', () => this.resize());
    this.animate();
  }
  resize() {
    this.W = this.canvas.width = window.innerWidth;
    this.H = this.canvas.height = window.innerHeight;
  }
  spawnShooter() {
    this.shooters.push({
      x: Math.random() * 0.8 + 0.1,
      y: Math.random() * 0.3,
      vx: 0.003 + Math.random() * 0.004,
      vy: 0.001 + Math.random() * 0.002,
      life: 0, maxLife: 0.8,
      tail: [],
    });
  }
  draw() {
    const { ctx, W, H, t } = this;
    ctx.clearRect(0, 0, W, H);

    // Night sky
    const sky = ctx.createLinearGradient(0, 0, 0, H);
    sky.addColorStop(0, '#020508');
    sky.addColorStop(0.5, '#050d0a');
    sky.addColorStop(1, '#0a1a0e');
    ctx.fillStyle = sky;
    ctx.fillRect(0, 0, W, H);

    // Subtle nebula
    const nb = ctx.createRadialGradient(W*0.3, H*0.3, 0, W*0.3, H*0.3, W*0.4);
    nb.addColorStop(0, 'rgba(20,60,30,.05)');
    nb.addColorStop(1, 'rgba(0,0,0,0)');
    ctx.fillStyle = nb;
    ctx.fillRect(0, 0, W, H);

    // Stars
    for (let i = 0; i < 200; i++) {
      const sx = ((i * 137.508) % 1) * W;
      const sy = ((i * 29.173) % 0.75) * H;
      const sa = 0.15 + Math.sin(t * 1.5 + i * 0.3) * 0.15;
      const sr = 0.5 + (i % 4) * 0.3;
      ctx.beginPath();
      ctx.arc(sx, sy, sr, 0, Math.PI * 2);
      ctx.fillStyle = `rgba(200,240,215,${sa})`;
      ctx.fill();
    }

    // Shooting stars
    if (Math.random() < 0.008) this.spawnShooter();
    this.shooters = this.shooters.filter(s => s.life < s.maxLife);
    this.shooters.forEach(s => {
      s.life += 0.016;
      s.tail.push({ x: s.x, y: s.y });
      if (s.tail.length > 20) s.tail.shift();
      s.x += s.vx; s.y += s.vy;

      s.tail.forEach((pt, i) => {
        const a = (i / s.tail.length) * (1 - s.life / s.maxLife) * 0.8;
        ctx.beginPath();
        ctx.arc(pt.x * W, pt.y * H, 1.5, 0, Math.PI * 2);
        ctx.fillStyle = `rgba(180,255,200,${a})`;
        ctx.fill();
      });
    });

    // Dunes silhouette
    const gY = H * 0.7;
    ctx.fillStyle = 'rgba(8,20,12,.95)';
    ctx.beginPath();
    ctx.moveTo(0, H);
    ctx.lineTo(0, gY);
    for (let x = 0; x <= W; x += 30) {
      const y = gY + Math.sin(x * 0.008) * H * 0.08 + Math.sin(x * 0.015 + 1) * H * 0.04;
      ctx.lineTo(x, y);
    }
    ctx.lineTo(W, H);
    ctx.closePath();
    ctx.fill();

    // Horizon glow
    const hg = ctx.createLinearGradient(0, gY - 60, 0, gY + 20);
    hg.addColorStop(0, 'rgba(30,90,50,.0)');
    hg.addColorStop(0.5, 'rgba(30,90,50,.08)');
    hg.addColorStop(1, 'rgba(30,90,50,.0)');
    ctx.fillStyle = hg;
    ctx.fillRect(0, gY - 60, W, 80);

    // Occasional firefly
    for (let f = 0; f < 6; f++) {
      const fx = (Math.sin(t * 0.2 + f * 2.1) * 0.4 + 0.5) * W;
      const fy = gY - 30 + Math.cos(t * 0.15 + f * 1.8) * 40;
      const fa = 0.2 + Math.sin(t * 2 + f) * 0.2;
      ctx.beginPath();
      ctx.arc(fx, fy, 2, 0, Math.PI * 2);
      ctx.fillStyle = `rgba(120,255,140,${fa})`;
      ctx.fill();
    }
  }
  animate() { this.t += 0.016; this.draw(); requestAnimationFrame(() => this.animate()); }
}

window.AboutCanvas = AboutCanvas;
window.FutureCitiesCanvas = FutureCitiesCanvas;
window.CtaCanvas = CtaCanvas;
window.AuthCanvas = AuthCanvas;

/* ============================================================
   OASIS — API Connector
   Ready to connect to backend — replace BASE_URL and implement
   ============================================================ */

const API = (() => {
  // ── CONFIG ──
  // Replace with your actual backend URL
  const BASE_URL = '/api'; // e.g. 'https://your-backend.com/api'

  // ── HELPERS ──
  const getToken = () => localStorage.getItem('oasis_token');
  const setToken = (t) => localStorage.setItem('oasis_token', t);
  const removeToken = () => localStorage.removeItem('oasis_token');

  const headers = (auth = false) => {
    const h = { 'Content-Type': 'application/json' };
    if (auth) h['Authorization'] = `Bearer ${getToken()}`;
    return h;
  };

  const request = async (method, path, body = null, auth = false) => {
    const opts = { method, headers: headers(auth) };
    if (body) opts.body = JSON.stringify(body);
    try {
      const res = await fetch(`${BASE_URL}${path}`, opts);
      const data = await res.json();
      if (!res.ok) throw new Error(data.message || 'Request failed');
      return { success: true, data };
    } catch (err) {
      return { success: false, error: err.message };
    }
  };

  // ── AUTH ENDPOINTS ──
  const auth = {
    /**
     * Register new user
     * POST /auth/register
     * Body: { fullName, email, password }
     */
    register: async ({ fullName, email, password }) => {
      return request('POST', '/auth/register', { fullName, email, password });
    },

    /**
     * Login user
     * POST /auth/login
     * Body: { email, password }
     */
    login: async ({ email, password }) => {
      const result = await request('POST', '/auth/login', { email, password });
      if (result.success && result.data.token) {
        setToken(result.data.token);
      }
      return result;
    },

    /**
     * Logout user
     */
    logout: () => {
      removeToken();
      localStorage.removeItem('oasis_user');
      window.location.href = 'index.html';
    },

    /**
     * Check if user is authenticated
     */
    isAuthenticated: () => !!getToken(),

    /**
     * Get current user from localStorage
     */
    getUser: () => {
      try {
        return JSON.parse(localStorage.getItem('oasis_user'));
      } catch { return null; }
    },
  };

  // ── REPORTS ENDPOINTS (future) ──
  const reports = {
    getAll: () => request('GET', '/reports', null, true),
    getById: (id) => request('GET', `/reports/${id}`, null, true),
    create: (data) => request('POST', '/reports', data, true),
    update: (id, data) => request('PUT', `/reports/${id}`, data, true),
    delete: (id) => request('DELETE', `/reports/${id}`, null, true),
  };

  return { auth, reports };
})();

/* ── FORM VALIDATION ── */
const Validate = {
  email: (v) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v),
  password: (v) => v.length >= 8,
  name: (v) => v.trim().length >= 2,
  passwordMatch: (a, b) => a === b,
  passwordStrength: (v) => {
    let score = 0;
    if (v.length >= 8) score++;
    if (/[A-Z]/.test(v)) score++;
    if (/[0-9]/.test(v)) score++;
    if (/[^A-Za-z0-9]/.test(v)) score++;
    return score; // 0-4
  },
};

window.API = API;
window.Validate = Validate;


/* ── CURSOR ── */
const dot = document.getElementById('cursor-dot');
const ring = document.getElementById('cursor-ring');
let mx = 0, my = 0, rx = 0, ry = 0;
document.addEventListener('mousemove', e => {
  mx = e.clientX; my = e.clientY;
  dot.style.left = mx + 'px'; dot.style.top = my + 'px';
});
(function animRing() {
  rx += (mx - rx) * 0.1; ry += (my - ry) * 0.1;
  ring.style.left = rx + 'px'; ring.style.top = ry + 'px';
  requestAnimationFrame(animRing);
})();

/* ── NAV SCROLL ── */
const nav = document.getElementById('mainNav');
window.addEventListener('scroll', () => {
  nav.classList.toggle('scrolled', window.scrollY > 60);
}, { passive: true });

/* ── INIT CANVASES ── */
new HeroAnimation('heroCanvas');
new AboutCanvas('aboutCanvas');
new FutureCitiesCanvas('futureCanvas');
new CtaCanvas('ctaCanvas');

/* ── FLYING BIRDS ── */
(function() {
  const canvas = document.getElementById('birdsCanvas');
  if (!canvas) return;
  const ctx = canvas.getContext('2d');
  let W, H;

  function resize() {
    W = canvas.width = window.innerWidth;
    H = canvas.height = window.innerHeight;
  }
  resize();
  window.addEventListener('resize', resize);

  // Each bird: position, velocity, wing phase, size, opacity
  const birds = [];
  for (let i = 0; i < 22; i++) {
    birds.push({
      x: Math.random() * window.innerWidth,
      y: Math.random() * window.innerHeight,
      vx: (1.2 + Math.random() * 1.6) * (Math.random() < 0.5 ? 1 : -1),
      vy: (Math.random() - 0.5) * 0.5,
      phase: Math.random() * Math.PI * 2,
      speed: 0.08 + Math.random() * 0.07,
      size: 5 + Math.random() * 7,
      alpha: 0.25 + Math.random() * 0.35,
      waveAmp: 0.3 + Math.random() * 0.4,
    });
  }

  function drawBird(x, y, size, wingPhase) {
    const flap = Math.sin(wingPhase) * size * 0.7;
    ctx.beginPath();
    // Left wing
    ctx.moveTo(x, y);
    ctx.quadraticCurveTo(x - size, y - flap, x - size * 2, y);
    // Right wing
    ctx.moveTo(x, y);
    ctx.quadraticCurveTo(x + size, y - flap, x + size * 2, y);
    ctx.stroke();
  }

  function animate() {
    ctx.clearRect(0, 0, W, H);

    birds.forEach(b => {
      b.phase += b.speed;
      b.x += b.vx;
      b.y += b.vy + Math.sin(b.phase * 0.3) * b.waveAmp;

      // wrap around screen
      if (b.x > W + b.size * 3) b.x = -b.size * 3;
      if (b.x < -b.size * 3) b.x = W + b.size * 3;
      if (b.y < -40) b.y = H + 40;
      if (b.y > H + 40) b.y = -40;

      // color adapts to section background under the bird
      const scrollY = window.scrollY;
      const absY = scrollY + b.y;
      const totalH = document.body.scrollHeight;
      const rel = absY / totalH;

      // dark sections: light bird, light sections: dark bird
      const isDarkBg = (rel > 0.22 && rel < 0.42) ||
                       (rel > 0.62 && rel < 0.72) ||
                       (rel > 0.85);

      ctx.strokeStyle = isDarkBg
        ? `rgba(200,230,200,${b.alpha})`
        : `rgba(45,100,50,${b.alpha * 0.7})`;
      ctx.lineWidth = 1.2;
      ctx.lineCap = 'round';

      ctx.save();
      if (b.vx < 0) {
        ctx.scale(-1, 1);
        drawBird(-b.x, b.y, b.size, b.phase);
      } else {
        drawBird(b.x, b.y, b.size, b.phase);
      }
      ctx.restore();
    });

    requestAnimationFrame(animate);
  }
  animate();
})();

/* ── SCROLL REVEAL ── */
const revealObs = new IntersectionObserver(entries => {
  entries.forEach(e => { if (e.isIntersecting) e.target.classList.add('visible'); });
}, { threshold: 0.1, rootMargin: '0px 0px -60px 0px' });
document.querySelectorAll('.reveal, .reveal-left, .reveal-right, .reveal-scale')
  .forEach(el => revealObs.observe(el));

/* ── IMPACT COUNTERS ── */
const impactCounterObs = new IntersectionObserver(entries => {
  entries.forEach(e => {
    if (!e.isIntersecting) return;
    e.target.querySelectorAll('[data-target]').forEach(el => {
      const target = parseInt(el.dataset.target);
      const divisor = parseInt(el.dataset.divisor) || 1;
      const suffix = el.dataset.suffix || '';
      const duration = 2200;
      const start = performance.now();
      const tick = (now) => {
        const elapsed = Math.min(now - start, duration);
        const eased = 1 - Math.pow(1 - elapsed / duration, 4);
        const val = Math.round(eased * target);
        if (divisor > 1) {
          el.textContent = (val / divisor).toFixed(1) + suffix;
        } else {
          el.textContent = val.toLocaleString() + suffix;
        }
        if (elapsed < duration) requestAnimationFrame(tick);
        else {
          if (divisor > 1) el.textContent = (target / divisor).toFixed(1) + suffix;
          else el.textContent = target.toLocaleString() + suffix;
        }
      };
      requestAnimationFrame(tick);
    });
    impactCounterObs.unobserve(e.target);
  });
}, { threshold: 0.4 });
const impactSection = document.getElementById('impactCards');
if (impactSection) impactCounterObs.observe(impactSection);

/* ── OLD COUNTERS (about section kept) ── */
const counterObs = new IntersectionObserver(entries => {
  entries.forEach(e => {
    if (!e.isIntersecting) return;
    e.target.querySelectorAll('.counter').forEach(el => {
      const target = parseInt(el.dataset.target);
      const duration = 2000;
      const startTime = performance.now();
      const tick = (now) => {
        const elapsed = Math.min(now - startTime, duration);
        const eased = 1 - Math.pow(1 - elapsed / duration, 3);
        const value = Math.round(eased * target);
        el.textContent = value > 999 ? (value > 999999 ? (value / 1000000).toFixed(1) + 'M' : value.toLocaleString()) : value;
        if (elapsed < duration) requestAnimationFrame(tick);
        else el.textContent = target > 999999 ? (target / 1000000).toFixed(1) + 'M' : target.toLocaleString();
      };
      requestAnimationFrame(tick);
    });
    counterObs.unobserve(e.target);
  });
}, { threshold: 0.5 });
document.querySelectorAll('.about-stat-row').forEach(el => counterObs.observe(el));

/* ── NAV ACTIVE LINK on scroll ── */
const navSections = [
  { id: 'hero', nav: 'nav-home' },
  { id: 'about', nav: 'nav-home' },
  { id: 'vision', nav: 'nav-search' },
  { id: 'restoration', nav: 'nav-report' },
  { id: 'impact', nav: 'nav-home' },
  { id: 'join', nav: 'nav-profile' },
  { id: 'future', nav: 'nav-home' },
];
window.addEventListener('scroll', () => {
  const sy = window.scrollY + 100;
  let current = 'nav-home';
  navSections.forEach(s => {
    const el = document.getElementById(s.id);
    if (el && el.offsetTop <= sy) current = s.nav;
  });
  document.querySelectorAll('.nav-links a').forEach(a => a.classList.remove('active'));
  const activeEl = document.getElementById(current);
  if (activeEl) activeEl.classList.add('active');
}, { passive: true });

/* ── FOOTER TREES ANIMATION ── */
(function() {
  const fc = document.getElementById('footerTreesCanvas');
  if (!fc) return;
  const fctx = fc.getContext('2d');
  let fw, fh;
  function resizeFc() {
    fw = fc.width = fc.offsetWidth;
    fh = fc.height = fc.offsetHeight;
  }
  resizeFc();
  window.addEventListener('resize', resizeFc);

  const ftrees = [];
  for (let i = 0; i < 18; i++) {
    ftrees.push({
      x: (i / 18) * 1.1 + Math.random() * 0.05,
      h: 40 + Math.random() * 50,
      w: 8 + Math.random() * 14,
      sway: Math.random() * Math.PI * 2,
      swaySpeed: 0.008 + Math.random() * 0.01,
    });
  }

  let ft = 0;
  function animFt() {
    if (!fw) { requestAnimationFrame(animFt); return; }
    fctx.clearRect(0, 0, fw, fh);
    ft += 0.016;
    ftrees.forEach(tr => {
      tr.sway += tr.swaySpeed;
      const sway = Math.sin(tr.sway) * 4;
      const x = tr.x * fw;
      const base = fh;
      const top = base - tr.h;
      // trunk
      fctx.beginPath();
      fctx.moveTo(x, base);
      fctx.quadraticCurveTo(x + sway * 0.3, base - tr.h * 0.5, x + sway, top);
      fctx.strokeStyle = 'rgba(100,180,100,.6)';
      fctx.lineWidth = 2;
      fctx.stroke();
      // canopy
      fctx.beginPath();
      fctx.arc(x + sway, top, tr.w, 0, Math.PI * 2);
      fctx.fillStyle = 'rgba(80,160,80,.4)';
      fctx.fill();
      // highlight
      fctx.beginPath();
      fctx.arc(x + sway - tr.w * 0.2, top - tr.w * 0.2, tr.w * 0.5, 0, Math.PI * 2);
      fctx.fillStyle = 'rgba(130,210,130,.25)';
      fctx.fill();
    });
    requestAnimationFrame(animFt);
  }
  animFt();
})();

/* ── SMOOTH SCROLL FOR ANCHORS ── */
document.querySelectorAll('a[href^="#"]').forEach(a => {
  a.addEventListener('click', e => {
    e.preventDefault();
    const target = document.querySelector(a.getAttribute('href'));
    if (target) target.scrollIntoView({ behavior: 'smooth', block: 'start' });
  });
});

/* ── PARALLAX ON HERO ── */
window.addEventListener('scroll', () => {
  const scrolled = window.scrollY;
  const heroContent = document.querySelector('.hero-content');
  if (heroContent && scrolled < window.innerHeight) {
    heroContent.style.transform = `translateY(${scrolled * 0.3}px)`;
    heroContent.style.opacity = 1 - (scrolled / (window.innerHeight * 0.6));
  }
}, { passive: true });

</script>

<script src="shared.js"></script>
</body>
</html>
