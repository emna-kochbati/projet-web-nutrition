<?php include __DIR__ . '/../partials/header.php'; ?>

<link href="https://fonts.googleapis.com/css2?family=Clash+Display:wght@400;500;600;700&family=Cabinet+Grotesk:wght@300;400;500;700;800&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;0,800;1,700&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
/* ═══════════════════════════════════════════════
   ROOT & RESET
═══════════════════════════════════════════════ */
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

:root {
  --g:    #00d68f;
  --g2:   #00ff9d;
  --gd:   #007a52;
  --or:   #ff5722;
  --or2:  #ff8a65;
  --bg:   #050a05;
  --bg2:  #080f08;
  --bg3:  #0c160c;
  --tx:   #e8f5e9;
  --tx2:  rgba(232,245,233,0.55);
  --tx3:  rgba(232,245,233,0.2);
  --fh:   'Playfair Display', serif;
  --fb:   'Plus Jakarta Sans', sans-serif;
}

html { scroll-behavior: smooth; overflow-x: hidden; }

body {
  font-family: var(--fb) !important;
  background: var(--bg) !important;
  color: var(--tx) !important;
  overflow-x: hidden;
  cursor: none;
}

/* ── CURSEUR CUSTOM ── */
.cursor {
  width: 10px; height: 10px;
  background: var(--g);
  border-radius: 50%;
  position: fixed; pointer-events: none; z-index: 99999;
  transform: translate(-50%,-50%);
  transition: transform .08s, width .2s, height .2s, opacity .2s;
  mix-blend-mode: difference;
}

.cursor-ring {
  width: 36px; height: 36px;
  border: 1.5px solid rgba(0,214,143,0.5);
  border-radius: 50%;
  position: fixed; pointer-events: none; z-index: 99998;
  transform: translate(-50%,-50%);
  transition: transform .18s, width .25s, height .25s;
}

body:hover .cursor { opacity: 1; }
a:hover ~ .cursor, button:hover ~ .cursor { width: 18px; height: 18px; }

/* ── NOISE OVERLAY ── */
body::before {
  content: '';
  position: fixed; inset: 0; z-index: 9000;
  pointer-events: none;
  background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='0.03'/%3E%3C/svg%3E");
  opacity: .45;
}

/* ═══════════════════════════════════════════════
   SCROLLBAR
═══════════════════════════════════════════════ */
::-webkit-scrollbar { width: 3px; }
::-webkit-scrollbar-track { background: var(--bg); }
::-webkit-scrollbar-thumb { background: var(--g); border-radius: 10px; }

/* ═══════════════════════════════════════════════
   NAVBAR
═══════════════════════════════════════════════ */
.nav {
  position: fixed; top: 0; left: 0; right: 0; z-index: 500;
  padding: 0 5%;
  height: 72px;
  display: flex; align-items: center; justify-content: space-between;
  transition: all .4s;
}

.nav.solid {
  background: rgba(5,10,5,0.94);
  backdrop-filter: blur(20px) saturate(1.4);
  border-bottom: 1px solid rgba(0,214,143,0.1);
}

.nav-logo {
  display: flex; align-items: center; gap: 12px;
  text-decoration: none; position: relative;
}

.nav-logo-mark {
  width: 38px; height: 38px; border-radius: 12px;
  background: linear-gradient(135deg, var(--g), var(--g2));
  display: flex; align-items: center; justify-content: center;
  font-size: 19px; position: relative; overflow: hidden;
}

.nav-logo-mark::after {
  content: '';
  position: absolute; inset: 0;
  background: linear-gradient(135deg, rgba(255,255,255,.2), transparent);
}

.nav-logo-text {
  font-family: var(--fh); font-size: 22px; font-weight: 700; color: var(--tx);
  letter-spacing: -.5px;
}

.nav-logo-text em { color: var(--g); font-style: normal; }

.nav-center {
  display: flex; align-items: center; gap: 2px;
  background: rgba(255,255,255,.04);
  border: 1px solid rgba(255,255,255,.07);
  border-radius: 50px; padding: 4px;
}

.nav-center a {
  padding: 7px 18px; border-radius: 50px;
  font-size: 13px; font-weight: 500; color: var(--tx2);
  text-decoration: none; transition: all .2s;
}

.nav-center a:hover { color: var(--tx); background: rgba(255,255,255,.07); }

.nav-right { display: flex; align-items: center; gap: 10px; }

.nav-btn-ghost {
  padding: 8px 20px; border-radius: 50px;
  border: 1px solid rgba(255,255,255,.15);
  color: var(--tx2); background: transparent; font-family: var(--fb);
  font-size: 13px; font-weight: 500; text-decoration: none;
  transition: all .2s; cursor: none;
}

.nav-btn-ghost:hover { border-color: var(--g); color: var(--g); }

.nav-btn-filled {
  padding: 9px 22px; border-radius: 50px;
  background: var(--g); color: #000;
  font-size: 13px; font-weight: 700; font-family: var(--fb);
  text-decoration: none; border: none;
  transition: all .25s; cursor: none;
  box-shadow: 0 0 20px rgba(0,214,143,0.3);
}

.nav-btn-filled:hover {
  background: var(--g2);
  box-shadow: 0 0 36px rgba(0,214,143,0.5);
  transform: translateY(-1px);
}

/* ═══════════════════════════════════════════════
   HERO
═══════════════════════════════════════════════ */
.hero {
  min-height: 100vh;
  position: relative;
  display: grid;
  grid-template-columns: 1fr 1fr;
  align-items: center;
  overflow: hidden;
  padding: 100px 5% 60px;
  gap: 60px;
}

/* Background radial */
.hero::before {
  content: '';
  position: absolute;
  width: 800px; height: 800px;
  border-radius: 50%;
  background: radial-gradient(circle, rgba(0,214,143,0.12) 0%, transparent 70%);
  top: -200px; right: -200px;
  pointer-events: none;
  animation: orb 8s ease-in-out infinite alternate;
}

.hero::after {
  content: '';
  position: absolute;
  width: 600px; height: 600px;
  border-radius: 50%;
  background: radial-gradient(circle, rgba(255,87,34,0.07) 0%, transparent 70%);
  bottom: -100px; left: 10%;
  pointer-events: none;
  animation: orb 12s ease-in-out infinite alternate-reverse;
}

@keyframes orb {
  from { transform: scale(1) translate(0,0); }
  to   { transform: scale(1.2) translate(30px,-20px); }
}

.hero-left { position: relative; z-index: 1; }

.hero-eyebrow {
  display: inline-flex; align-items: center; gap: 10px;
  margin-bottom: 28px;
}

.hero-eyebrow-line {
  width: 32px; height: 2px; background: var(--g);
  position: relative; overflow: hidden;
}

.hero-eyebrow-line::after {
  content: '';
  position: absolute; top: 0; left: -100%;
  width: 100%; height: 100%;
  background: white;
  animation: shimmer 2.5s infinite;
}

@keyframes shimmer { to { left: 100%; } }

.hero-eyebrow-text {
  font-size: 11px; font-weight: 700; text-transform: uppercase;
  letter-spacing: .18em; color: var(--g);
}

.hero-h1 {
  font-family: var(--fh);
  font-size: clamp(46px, 5.5vw, 78px);
  font-weight: 800;
  line-height: 1.04;
  letter-spacing: -2.5px;
  color: var(--tx);
  margin-bottom: 24px;
}

.hero-h1 .word-g    { color: var(--g); }
.hero-h1 .word-or   { color: var(--or2); }
.hero-h1 .word-it   { font-style: italic; }

.hero-p {
  font-size: 17px; color: var(--tx2); line-height: 1.8;
  max-width: 480px; margin-bottom: 40px; font-weight: 400;
}

.hero-actions {
  display: flex; align-items: center; gap: 16px; flex-wrap: wrap;
  margin-bottom: 52px;
}

.cta-main {
  display: inline-flex; align-items: center; gap: 10px;
  padding: 15px 32px; border-radius: 50px;
  background: linear-gradient(135deg, var(--g), var(--g2));
  color: #000; font-size: 15px; font-weight: 800; font-family: var(--fb);
  text-decoration: none; transition: all .25s; cursor: none; border: none;
  box-shadow: 0 8px 32px rgba(0,214,143,0.35);
  position: relative; overflow: hidden;
}

.cta-main::before {
  content: '';
  position: absolute; inset: 0;
  background: linear-gradient(135deg, rgba(255,255,255,.2), transparent);
  opacity: 0; transition: opacity .2s;
}

.cta-main:hover { transform: translateY(-3px); box-shadow: 0 16px 44px rgba(0,214,143,0.5); }
.cta-main:hover::before { opacity: 1; }

.cta-main-arrow {
  width: 22px; height: 22px; border-radius: 50%;
  background: rgba(0,0,0,.2);
  display: flex; align-items: center; justify-content: center;
  font-size: 11px; transition: transform .2s;
}

.cta-main:hover .cta-main-arrow { transform: translateX(3px); }

.cta-ghost {
  display: inline-flex; align-items: center; gap: 10px;
  padding: 15px 28px; border-radius: 50px;
  border: 1.5px solid rgba(255,255,255,.15);
  color: var(--tx2); font-size: 14px; font-weight: 500; font-family: var(--fb);
  text-decoration: none; transition: all .22s; cursor: none;
}

.cta-ghost:hover { border-color: rgba(255,255,255,.35); color: var(--tx); }

.hero-trust {
  display: flex; align-items: center; gap: 16px;
}

.trust-avatars {
  display: flex;
}

.trust-av {
  width: 34px; height: 34px; border-radius: 50%;
  border: 2px solid var(--bg);
  background: linear-gradient(135deg, var(--g), var(--gd));
  display: flex; align-items: center; justify-content: center;
  font-size: 12px; font-weight: 700; color: #000; margin-left: -10px;
}

.trust-av:first-child { margin-left: 0; }

.trust-text { font-size: 13px; color: var(--tx2); }
.trust-text strong { color: var(--tx); font-weight: 700; }

/* HERO RIGHT - VISUAL COLLAGE */
.hero-right {
  position: relative; z-index: 1;
  height: 580px;
}

.hero-img-main {
  position: absolute; top: 0; right: 0;
  width: 78%; height: 420px;
  border-radius: 28px; object-fit: cover;
  box-shadow: 0 32px 80px rgba(0,0,0,.6);
  filter: saturate(1.2) brightness(.95);
  animation: floatImg 7s ease-in-out infinite alternate;
}

@keyframes floatImg {
  from { transform: translateY(0); }
  to   { transform: translateY(-12px); }
}

.hero-img-accent {
  position: absolute; bottom: 0; left: 0;
  width: 52%; height: 280px;
  border-radius: 22px; object-fit: cover;
  box-shadow: 0 20px 50px rgba(0,0,0,.5);
  border: 3px solid var(--bg);
  filter: saturate(1.3);
  animation: floatImg 9s 1s ease-in-out infinite alternate;
}

/* Floating card */
.hero-float-card {
  position: absolute; top: 28%; right: -12%;
  background: rgba(8,15,8,0.85);
  backdrop-filter: blur(20px);
  border: 1px solid rgba(0,214,143,0.2);
  border-radius: 18px;
  padding: 16px 20px;
  min-width: 180px;
  box-shadow: 0 16px 40px rgba(0,0,0,.4);
  animation: floatCard 6s ease-in-out infinite alternate;
}

@keyframes floatCard {
  from { transform: translateY(0) rotate(-2deg); }
  to   { transform: translateY(-10px) rotate(0deg); }
}

.hfc-label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .1em; color: var(--g); margin-bottom: 8px; }
.hfc-val   { font-family: var(--fh); font-size: 28px; font-weight: 700; color: var(--tx); letter-spacing: -1px; line-height: 1; }
.hfc-sub   { font-size: 11px; color: var(--tx3); margin-top: 2px; }

.hfc-bar { height: 4px; background: rgba(255,255,255,.08); border-radius: 2px; margin-top: 10px; overflow: hidden; }
.hfc-fill { height: 100%; border-radius: 2px; background: linear-gradient(90deg, var(--g), var(--g2)); animation: fillAnim 3s ease infinite alternate; }
@keyframes fillAnim { from{width:60%} to{width:80%} }

/* ═══════════════════════════════════════════════
   MARQUEE / TICKER
═══════════════════════════════════════════════ */
.marquee-wrap {
  overflow: hidden;
  border-top: 1px solid rgba(0,214,143,0.1);
  border-bottom: 1px solid rgba(0,214,143,0.1);
  padding: 18px 0;
  background: rgba(0,214,143,0.03);
}

.marquee-track {
  display: flex; gap: 60px; width: max-content;
  animation: marquee 22s linear infinite;
}

@keyframes marquee { from{transform:translateX(0)} to{transform:translateX(-50%)} }

.marquee-item {
  display: flex; align-items: center; gap: 12px;
  font-size: 13px; font-weight: 600; color: var(--tx2);
  white-space: nowrap; text-transform: uppercase; letter-spacing: .1em;
}

.marquee-item::before {
  content: '';
  width: 6px; height: 6px; border-radius: 50%;
  background: var(--g); flex-shrink: 0;
}

/* ═══════════════════════════════════════════════
   SECTION HELPERS
═══════════════════════════════════════════════ */
.sec { padding: 110px 5%; position: relative; }
.sec-alt { background: var(--bg2); }

.tag {
  display: inline-flex; align-items: center; gap: 8px;
  font-size: 11px; font-weight: 700; text-transform: uppercase;
  letter-spacing: .14em; color: var(--g); margin-bottom: 16px;
}

.tag-dot {
  width: 24px; height: 2px; background: var(--g); border-radius: 2px;
  position: relative; overflow: hidden;
}

.tag-dot::after {
  content: '';
  position: absolute; left: -100%; top: 0;
  width: 100%; height: 100%; background: #fff;
  animation: shimmer 2s infinite;
}

.sh {
  font-family: var(--fh);
  font-size: clamp(34px,4vw,58px);
  font-weight: 800; line-height: 1.08;
  letter-spacing: -2px; color: var(--tx);
  margin-bottom: 18px;
}

.sh .g  { color: var(--g); }
.sh .or { color: var(--or2); }
.sh .it { font-style: italic; }

.sp {
  font-size: 16px; color: var(--tx2); line-height: 1.8;
  max-width: 520px; font-weight: 400;
}

/* ═══════════════════════════════════════════════
   SERVICES — GRID MASONRY STYLE
═══════════════════════════════════════════════ */
.svc-masonry {
  display: grid;
  grid-template-columns: repeat(12, 1fr);
  grid-template-rows: auto;
  gap: 16px;
  margin-top: 64px;
}

.svc-tile {
  border-radius: 22px; overflow: hidden; position: relative;
  cursor: none;
  transition: transform .35s cubic-bezier(.25,.46,.45,.94), box-shadow .35s;
}

.svc-tile:hover { transform: scale(1.025); box-shadow: 0 28px 64px rgba(0,0,0,.5); }

.svc-tile:nth-child(1) { grid-column: 1 / 6; grid-row: 1; height: 340px; }
.svc-tile:nth-child(2) { grid-column: 6 / 13; grid-row: 1; height: 340px; }
.svc-tile:nth-child(3) { grid-column: 1 / 5; grid-row: 2; height: 280px; }
.svc-tile:nth-child(4) { grid-column: 5 / 9; grid-row: 2; height: 280px; }
.svc-tile:nth-child(5) { grid-column: 9 / 13; grid-row: 2; height: 280px; }

.svc-tile img {
  width: 100%; height: 100%; object-fit: cover; display: block;
  transition: transform .6s cubic-bezier(.25,.46,.45,.94), filter .4s;
  filter: brightness(.55) saturate(1.3);
}

.svc-tile:hover img { transform: scale(1.06); filter: brightness(.7) saturate(1.4); }

.svc-content {
  position: absolute; inset: 0;
  background: linear-gradient(to top, rgba(0,0,0,.9) 0%, rgba(0,0,0,.1) 55%, transparent 100%);
  display: flex; flex-direction: column; justify-content: flex-end;
  padding: 26px;
}

.svc-badge {
  display: inline-flex; align-items: center; gap: 6px;
  padding: 5px 12px; border-radius: 20px;
  background: rgba(255,255,255,.1);
  backdrop-filter: blur(8px);
  font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: .08em;
  color: rgba(255,255,255,.8); margin-bottom: 10px; width: fit-content;
}

.svc-badge.g-badge { background: rgba(0,214,143,.2); color: var(--g2); }
.svc-badge.o-badge { background: rgba(255,87,34,.2);  color: var(--or2); }

.svc-title {
  font-family: var(--fh); font-size: 22px; font-weight: 700; margin-bottom: 5px;
  line-height: 1.2;
}

.svc-desc { font-size: 12px; color: rgba(255,255,255,.6); }

.svc-arrow {
  position: absolute; top: 20px; right: 20px;
  width: 36px; height: 36px; border-radius: 50%;
  background: rgba(255,255,255,.1); backdrop-filter: blur(8px);
  border: 1px solid rgba(255,255,255,.15);
  display: flex; align-items: center; justify-content: center;
  font-size: 13px; color: rgba(255,255,255,.7);
  transform: rotate(-45deg);
  transition: all .25s;
}

.svc-tile:hover .svc-arrow { background: var(--g); color: #000; transform: rotate(0deg); }

/* ═══════════════════════════════════════════════
   STATS ROW
═══════════════════════════════════════════════ */
.stats-row {
  display: grid; grid-template-columns: repeat(4,1fr);
  gap: 1px; background: rgba(255,255,255,.06);
  border-radius: 20px; overflow: hidden; margin-top: 64px;
}

.stat-box {
  background: var(--bg2); padding: 36px 32px;
  text-align: center; position: relative; overflow: hidden;
  transition: background .25s;
}

.stat-box:hover { background: var(--bg3); }

.stat-box::before {
  content: '';
  position: absolute; top: 0; left: 50%; transform: translateX(-50%);
  width: 40%; height: 1px;
  background: linear-gradient(90deg, transparent, var(--g), transparent);
  opacity: 0; transition: opacity .25s;
}

.stat-box:hover::before { opacity: 1; }

.stat-num {
  font-family: var(--fh); font-size: 48px; font-weight: 700;
  color: var(--tx); letter-spacing: -2.5px; line-height: 1;
  margin-bottom: 8px; display: block;
}

.stat-num span { color: var(--g); }

.stat-label { font-size: 13px; color: var(--tx2); font-weight: 500; }

/* ═══════════════════════════════════════════════
   ABOUT — SIDE BY SIDE EDITORIAL
═══════════════════════════════════════════════ */
.about-grid {
  display: grid; grid-template-columns: 1fr 1.1fr;
  gap: 80px; align-items: center; margin-top: 64px;
}

.about-visual { position: relative; }

.about-main-img {
  width: 100%; height: 520px; border-radius: 28px; object-fit: cover;
  display: block; filter: saturate(1.2);
  box-shadow: 0 36px 80px rgba(0,0,0,.5);
}

.about-overlay-card {
  position: absolute; bottom: 28px; left: -28px;
  background: rgba(5,10,5,0.9);
  backdrop-filter: blur(20px);
  border: 1px solid rgba(0,214,143,0.2);
  border-radius: 20px; padding: 20px 24px;
  min-width: 220px;
  box-shadow: 0 16px 40px rgba(0,0,0,.4);
}

.aoc-title { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .1em; color: var(--g); margin-bottom: 14px; }

.aoc-row {
  display: flex; align-items: center; gap: 10px;
  padding: 6px 0; border-bottom: 1px solid rgba(255,255,255,.05);
}

.aoc-row:last-child { border-bottom: none; }

.aoc-icon { font-size: 16px; width: 20px; }
.aoc-text { font-size: 13px; color: var(--tx2); }
.aoc-val  { margin-left: auto; font-size: 13px; font-weight: 700; color: var(--tx); }

.about-text-block {}

.about-features-list { display: flex; flex-direction: column; gap: 12px; margin-top: 36px; }

.afl-item {
  display: flex; align-items: flex-start; gap: 16px;
  padding: 18px 20px;
  background: rgba(255,255,255,.025);
  border: 1px solid rgba(255,255,255,.06);
  border-radius: 16px;
  transition: all .22s;
}

.afl-item:hover {
  background: rgba(0,214,143,.04);
  border-color: rgba(0,214,143,.2);
  transform: translateX(6px);
}

.afl-num {
  font-family: var(--fh); font-size: 22px; font-weight: 700;
  color: rgba(0,214,143,.25); line-height: 1; flex-shrink: 0;
  transition: color .22s;
}

.afl-item:hover .afl-num { color: var(--g); }

.afl-body {}
.afl-title { font-size: 15px; font-weight: 700; margin-bottom: 4px; }
.afl-desc  { font-size: 13px; color: var(--tx2); line-height: 1.65; }

/* ═══════════════════════════════════════════════
   RECETTES — BENTO GRID
═══════════════════════════════════════════════ */
.bento-grid {
  display: grid;
  grid-template-columns: repeat(12, 1fr);
  gap: 14px;
  margin-top: 64px;
}

.bento {
  border-radius: 20px; overflow: hidden;
  background: rgba(255,255,255,.03);
  border: 1px solid rgba(255,255,255,.06);
  transition: all .28s;
  cursor: none;
}

.bento:hover { border-color: rgba(0,214,143,.25); transform: translateY(-4px); box-shadow: 0 20px 48px rgba(0,0,0,.4); }

.bento:nth-child(1) { grid-column: 1 / 6; }
.bento:nth-child(2) { grid-column: 6 / 9; }
.bento:nth-child(3) { grid-column: 9 / 13; }
.bento:nth-child(4) { grid-column: 1 / 5; }
.bento:nth-child(5) { grid-column: 5 / 13; }

.bento-img { width: 100%; height: 190px; object-fit: cover; display: block; transition: transform .5s; }
.bento:hover .bento-img { transform: scale(1.04); }

.bento-body { padding: 18px; }

.bento-tags { display: flex; gap: 6px; margin-bottom: 10px; }

.btag {
  font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: .08em;
  padding: 3px 9px; border-radius: 20px;
}

.btag.g  { background: rgba(0,214,143,.15); color: var(--g); }
.btag.o  { background: rgba(255,87,34,.15);  color: var(--or2); }
.btag.w  { background: rgba(255,255,255,.08); color: var(--tx2); }

.bento-title { font-family: var(--fh); font-size: 17px; font-weight: 700; margin-bottom: 6px; line-height: 1.3; }
.bento-desc  { font-size: 12px; color: var(--tx2); line-height: 1.65; margin-bottom: 14px; }

.bento-footer {
  display: flex; align-items: center; justify-content: space-between;
  font-size: 12px; color: var(--tx3);
  padding-top: 12px; border-top: 1px solid rgba(255,255,255,.05);
}

.bento-footer i { color: var(--g); margin-right: 4px; font-size: 11px; filter: none; }

/* ═══════════════════════════════════════════════
   CONSEILS — NUMBERED GRID
═══════════════════════════════════════════════ */
.conseils-wrap {
  display: grid; grid-template-columns: repeat(3,1fr);
  gap: 18px; margin-top: 64px;
}

.conseil {
  padding: 28px;
  background: rgba(255,255,255,.025);
  border: 1px solid rgba(255,255,255,.06);
  border-radius: 20px;
  position: relative; overflow: hidden;
  transition: all .25s; cursor: none;
}

.conseil:hover {
  background: rgba(0,214,143,.04);
  border-color: rgba(0,214,143,.2);
  transform: translateY(-5px);
  box-shadow: 0 18px 44px rgba(0,0,0,.35);
}

.conseil::before {
  content: '';
  position: absolute; top: 0; left: 0; right: 0; height: 2px;
  background: linear-gradient(90deg, transparent, var(--g), transparent);
  opacity: 0; transition: opacity .25s;
}

.conseil:hover::before { opacity: 1; }

.conseil-bg-num {
  position: absolute; top: -10px; right: 16px;
  font-family: var(--fh); font-size: 90px; font-weight: 800;
  color: rgba(0,214,143,.04); line-height: 1;
  pointer-events: none; user-select: none;
  transition: color .25s;
}

.conseil:hover .conseil-bg-num { color: rgba(0,214,143,.08); }

.conseil-ico {
  width: 48px; height: 48px; border-radius: 14px;
  background: rgba(0,214,143,.1);
  display: flex; align-items: center; justify-content: center;
  font-size: 22px; margin-bottom: 16px;
  transition: background .22s, transform .22s;
}

.conseil:hover .conseil-ico { background: rgba(0,214,143,.2); transform: scale(1.08); }

.conseil-title { font-size: 17px; font-weight: 700; margin-bottom: 10px; }
.conseil-text  { font-size: 13px; color: var(--tx2); line-height: 1.75; }

.conseil-tag {
  display: inline-block; margin-top: 16px;
  font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .1em;
  color: var(--g); background: rgba(0,214,143,.1); border-radius: 20px;
  padding: 3px 10px;
}

/* ═══════════════════════════════════════════════
   SPORT — FULL WIDTH CARDS
═══════════════════════════════════════════════ */
.sport-scroll {
  display: grid; grid-template-columns: repeat(3,1fr);
  gap: 18px; margin-top: 64px;
}

.sport-item {
  border-radius: 24px; overflow: hidden; position: relative;
  height: 460px; cursor: none;
  transition: transform .3s, box-shadow .3s;
}

.sport-item:hover { transform: scale(1.02); box-shadow: 0 32px 64px rgba(0,0,0,.55); }

.sport-item img {
  width: 100%; height: 100%; object-fit: cover; display: block;
  filter: brightness(.5) saturate(1.3);
  transition: filter .35s, transform .5s;
}

.sport-item:hover img { filter: brightness(.65) saturate(1.4); transform: scale(1.04); }

.sport-overlay {
  position: absolute; inset: 0;
  background: linear-gradient(to top, rgba(0,0,0,.92) 0%, rgba(0,0,0,.15) 50%, transparent 100%);
  display: flex; flex-direction: column; justify-content: flex-end;
  padding: 30px;
}

.sport-cat  { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .14em; color: var(--g); margin-bottom: 8px; }
.sport-name { font-family: var(--fh); font-size: 24px; font-weight: 700; margin-bottom: 8px; line-height: 1.2; }
.sport-desc { font-size: 13px; color: rgba(255,255,255,.6); line-height: 1.6; margin-bottom: 18px; }

.sport-btn {
  display: inline-flex; align-items: center; gap: 8px;
  padding: 10px 20px; border-radius: 50px;
  background: rgba(255,255,255,.12); backdrop-filter: blur(8px);
  border: 1px solid rgba(255,255,255,.2);
  font-size: 12px; font-weight: 600; color: #fff;
  text-decoration: none; width: fit-content;
  transition: all .2s;
}

.sport-btn:hover { background: var(--g); border-color: var(--g); color: #000; }

/* ═══════════════════════════════════════════════
   TÉMOIGNAGES
═══════════════════════════════════════════════ */
.temos {
  display: grid; grid-template-columns: repeat(3,1fr);
  gap: 18px; margin-top: 64px;
}

.temo {
  padding: 30px;
  background: rgba(255,255,255,.025);
  border: 1px solid rgba(255,255,255,.07);
  border-radius: 22px;
  transition: all .25s; cursor: none;
  position: relative; overflow: hidden;
}

.temo:hover { border-color: rgba(0,214,143,.2); background: rgba(0,214,143,.03); }

.temo-quote {
  font-size: 48px; line-height: 1; color: rgba(0,214,143,.2);
  font-family: var(--fh); margin-bottom: 12px; font-weight: 800;
}

.temo-stars { display: flex; gap: 3px; margin-bottom: 16px; }
.temo-stars i { font-size: 13px; color: var(--or2); filter: none; }

.temo-text {
  font-size: 14px; color: rgba(255,255,255,.7); line-height: 1.8;
  font-style: italic; margin-bottom: 22px;
}

.temo-person { display: flex; align-items: center; gap: 12px; }

.temo-av {
  width: 42px; height: 42px; border-radius: 50%;
  background: linear-gradient(135deg, var(--g), var(--gd));
  display: flex; align-items: center; justify-content: center;
  font-family: var(--fh); font-size: 16px; font-weight: 700; color: #000;
  flex-shrink: 0;
}

.temo-name   { font-size: 14px; font-weight: 700; }
.temo-status { font-size: 11px; color: var(--tx3); margin-top: 1px; }
.temo-res    { margin-left: auto; font-size: 12px; font-weight: 700; color: var(--g); }

/* ═══════════════════════════════════════════════
   CTA FINALE
═══════════════════════════════════════════════ */
.cta-sec {
  margin: 60px 5% 80px;
  border-radius: 32px; overflow: hidden;
  position: relative; min-height: 360px;
  display: flex; align-items: center;
}

.cta-sec-bg {
  position: absolute; inset: 0;
  background:
    linear-gradient(135deg, rgba(0,0,0,.7) 0%, rgba(0,40,20,.4) 100%),
    url('https://images.unsplash.com/photo-1490645935967-10de6ba17061?w=1400&q=85');
  background-size: cover; background-position: center;
}

.cta-sec-bg::before {
  content: '';
  position: absolute; inset: 0;
  background: radial-gradient(ellipse at 70% 50%, rgba(0,214,143,.15) 0%, transparent 60%);
}

.cta-sec-content {
  position: relative; z-index: 1;
  width: 100%; padding: 70px 8%;
  display: flex; align-items: center; justify-content: space-between; gap: 48px; flex-wrap: wrap;
}

.cta-sec-text h2 {
  font-family: var(--fh); font-size: clamp(28px,3.5vw,50px);
  font-weight: 800; letter-spacing: -1.5px; line-height: 1.15; margin-bottom: 12px;
}

.cta-sec-text h2 em { font-style: italic; color: var(--g); }

.cta-sec-text p { font-size: 16px; color: var(--tx2); }

.cta-sec-btns { display: flex; gap: 12px; flex-wrap: wrap; flex-shrink: 0; }

/* ═══════════════════════════════════════════════
   SCROLL ANIMATIONS
═══════════════════════════════════════════════ */
[data-reveal] {
  opacity: 0; transform: translateY(32px);
  transition: opacity .8s ease, transform .8s ease;
}

[data-reveal].in { opacity: 1; transform: translateY(0); }
[data-reveal="left"]  { transform: translateX(-32px); }
[data-reveal="left"].in  { transform: translateX(0); }
[data-reveal="right"] { transform: translateX(32px); }
[data-reveal="right"].in { transform: translateX(0); }
[data-reveal="scale"] { transform: scale(.92); }
[data-reveal="scale"].in { transform: scale(1); }

[data-delay="1"] { transition-delay: .1s; }
[data-delay="2"] { transition-delay: .2s; }
[data-delay="3"] { transition-delay: .3s; }
[data-delay="4"] { transition-delay: .4s; }
[data-delay="5"] { transition-delay: .5s; }

/* ═══════════════════════════════════════════════
   RESPONSIVE
═══════════════════════════════════════════════ */
@media (max-width: 1100px) {
  .hero { grid-template-columns: 1fr; min-height: auto; padding-top: 120px; }
  .hero-right { height: 320px; }
  .svc-masonry { grid-template-columns: 1fr 1fr; grid-template-rows: auto; }
  .svc-tile { grid-column: auto !important; grid-row: auto !important; height: 260px !important; }
  .bento { grid-column: auto !important; }
  .bento-grid { grid-template-columns: 1fr 1fr; }
  .about-grid { grid-template-columns: 1fr; gap: 40px; }
}

@media (max-width: 768px) {
  .nav-center { display: none; }
  .stats-row { grid-template-columns: 1fr 1fr; }
  .conseils-wrap, .sport-scroll, .temos { grid-template-columns: 1fr; }
  .hero { padding: 100px 5% 50px; }
  .sec { padding: 80px 5%; }
  .cta-sec-content { flex-direction: column; text-align: center; }
  .cta-sec-btns { justify-content: center; }
}
</style>

<!-- ── CURSEUR ── -->
<div class="cursor" id="cursor"></div>
<div class="cursor-ring" id="cursorRing"></div>

<!-- ══ NAVBAR ══ -->
<nav class="nav" id="mainNav">
  <a href="#" class="nav-logo">
    <div class="nav-logo-mark">🌿</div>
    <div class="nav-logo-text">Eco<em>Nutri</em></div>
  </a>
  <div class="nav-center">
    <a href="#services">Services</a>
    <a href="#about">À propos</a>
    <a href="#recipes">Recettes</a>
    <a href="#sport">Sport</a>
    <a href="#temoignages">Avis</a>
  </div>
  <div class="nav-right">
    <a href="index.php?url=User/auth" class="nav-btn-ghost">Connexion</a>
    <a href="index.php?url=User/auth" class="nav-btn-filled">Commencer →</a>
  </div>
</nav>

<!-- ══ HERO ══ -->
<section class="hero">
  <div class="hero-left">
    <div class="hero-eyebrow">
      <div class="hero-eyebrow-line"></div>
      <div class="hero-eyebrow-text">Alimentation durable & nutrition intelligente</div>
    </div>

    <h1 class="hero-h1">
      Mangez<br>
      <span class="word-it word-g">mieux</span>,<br>
      vivez <span class="word-or">plus fort</span>.
    </h1>

    <p class="hero-p">
      EcoNutri transforme votre rapport à l'alimentation grâce à un suivi nutritionnel personnalisé,
      des recettes saines et des plans sport adaptés à votre mode de vie.
    </p>

    <div class="hero-actions">
      <a href="index.php?url=User/auth" class="cta-main">
        Créer mon espace
        <span class="cta-main-arrow"><i class="fa fa-arrow-right" style="filter:none;font-size:10px;"></i></span>
      </a>
      <a href="#services" class="cta-ghost">
        <i class="fa fa-play" style="font-size:10px;filter:none;color:var(--g);"></i>
        Découvrir
      </a>
    </div>

    <div class="hero-trust">
      <div class="trust-avatars">
        <div class="trust-av">S</div>
        <div class="trust-av" style="background:linear-gradient(135deg,#ff5722,#ff8a65);">K</div>
        <div class="trust-av" style="background:linear-gradient(135deg,#7c4dff,#b388ff);">L</div>
        <div class="trust-av" style="background:linear-gradient(135deg,#00bcd4,#80deea);">M</div>
      </div>
      <div class="trust-text">
        <strong>1 240+</strong> membres nous font confiance
      </div>
    </div>
  </div>

  <div class="hero-right">
    <img class="hero-img-main"
         src="https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=900&q=85"
         alt="EcoNutri nutrition">
    <img class="hero-img-accent"
         src="https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=600&q=80"
         alt="Sport & santé">
    <div class="hero-float-card">
      <div class="hfc-label">Score nutrition</div>
      <div class="hfc-val">82<small style="font-size:16px;font-weight:400;color:var(--tx2)">/100</small></div>
      <div class="hfc-sub">Objectif poids normal atteint</div>
      <div class="hfc-bar"><div class="hfc-fill"></div></div>
    </div>
  </div>
</section>

<!-- ══ MARQUEE ══ -->
<div class="marquee-wrap">
  <div class="marquee-track">
    <?php
    $items = ['Nutrition Intelligente','Recettes Saines','Perte de Poids','Prise de Masse','Suivi IMC','Hydratation','Sport Adapté','Bien-Être','Régime Végétarien','Équilibre Alimentaire'];
    $all = array_merge($items,$items);
    foreach($all as $item): ?>
    <div class="marquee-item"><?= $item ?></div>
    <?php endforeach; ?>
  </div>
</div>

<!-- ══ SERVICES ══ -->
<section class="sec sec-alt" id="services">
  <div data-reveal>
    <div class="tag"><div class="tag-dot"></div> Ce que nous offrons</div>
    <h2 class="sh">Nos <span class="g">services</span><br>pour votre <span class="it">santé</span></h2>
    <p class="sp">Un écosystème complet — nutrition, sport, bien-être — pour une transformation durable.</p>
  </div>

  <div class="svc-masonry">
    <?php
    $svcs = [
      ['img'=>'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=800&q=80','badge'=>'Nutrition','badge_cls'=>'g-badge','title'=>'Recettes Saines','desc'=>'320+ recettes équilibrées validées par nos diététiciens'],
      ['img'=>'https://images.unsplash.com/photo-1517836357463-d25dfeac3438?w=800&q=80','badge'=>'Sport','badge_cls'=>'o-badge','title'=>'Programmes Sport','desc'=>'Adapté à votre niveau et vos objectifs physiques'],
      ['img'=>'https://images.unsplash.com/photo-1490645935967-10de6ba17061?w=600&q=80','badge'=>'IMC & Calories','badge_cls'=>'g-badge','title'=>'Suivi Nutritionnel','desc'=>'Calcul personnalisé en temps réel'],
      ['img'=>'https://images.unsplash.com/photo-1506784365847-bbad939e9335?w=600&q=80','badge'=>'Événements','badge_cls'=>'','title'=>'Challenges & Marathons','desc'=>'Défiez-vous avec notre communauté'],
      ['img'=>'https://images.unsplash.com/photo-1555992336-03a23c7b20ee?w=600&q=80','badge'=>'Bien-être','badge_cls'=>'o-badge','title'=>'Hydratation & Sommeil','desc'=>'Les piliers souvent négligés de la nutrition'],
    ];
    foreach($svcs as $i => $s): ?>
    <div class="svc-tile" data-reveal data-delay="<?= $i+1 ?>">
      <img src="<?= $s['img'] ?>" alt="<?= $s['title'] ?>">
      <div class="svc-content">
        <div class="svc-badge <?= $s['badge_cls'] ?>"><?= $s['badge'] ?></div>
        <div class="svc-title"><?= $s['title'] ?></div>
        <div class="svc-desc"><?= $s['desc'] ?></div>
      </div>
      <div class="svc-arrow"><i class="fa fa-arrow-right" style="filter:none;font-size:11px;"></i></div>
    </div>
    <?php endforeach; ?>
  </div>
</section>

<!-- ══ STATS ══ -->
<div class="stats-row" data-reveal="scale">
  <?php
  $stats = [['1 240','+ membres actifs'],['320','+ recettes',''],['94%','de satisfaction',''],['5+','années d\'expertise','']];
  foreach($stats as $st): ?>
  <div class="stat-box">
    <span class="stat-num" data-count="<?= preg_replace('/[^0-9]/','',$st[0]) ?>"><?= $st[0] ?></span>
    <div class="stat-label"><?= $st[1] ?></div>
  </div>
  <?php endforeach; ?>
</div>

<!-- ══ ABOUT ══ -->
<section class="sec" id="about">
  <div class="about-grid">
    <div class="about-visual" data-reveal="left">
      <img class="about-main-img"
           src="https://images.unsplash.com/photo-1490645935967-10de6ba17061?w=800&q=85"
           alt="À propos d'EcoNutri">
      <div class="about-overlay-card">
        <div class="aoc-title">✅ Résultats prouvés</div>
        <div class="aoc-row"><span class="aoc-icon">⚖️</span><span class="aoc-text">Perte de poids</span><span class="aoc-val">-8kg moy.</span></div>
        <div class="aoc-row"><span class="aoc-icon">💪</span><span class="aoc-text">Prise de masse</span><span class="aoc-val">+4kg moy.</span></div>
        <div class="aoc-row"><span class="aoc-icon">❤️</span><span class="aoc-text">Satisfaction</span><span class="aoc-val">94%</span></div>
        <div class="aoc-row"><span class="aoc-icon">⏱️</span><span class="aoc-text">Résultats en</span><span class="aoc-val">4 semaines</span></div>
      </div>
    </div>
    <div data-reveal="right">
      <div class="tag"><div class="tag-dot"></div> Notre approche</div>
      <h2 class="sh">Science &amp;<br><span class="it g">alimentation</span><br>durable.</h2>
      <p class="sp">EcoNutri combine l'intelligence des données et l'expertise diététique pour vous offrir un programme sur mesure, efficace et respectueux de votre corps.</p>

      <div class="about-features-list">
        <?php
        $feats = [
          ['01','Analyse complète de votre profil','IMC, TDEE, macronutriments — calculés selon vos données réelles.'],
          ['02','Plans alimentaires personnalisés','Perte de poids, prise de masse, équilibre ou végétarien : votre plan unique.'],
          ['03','Suivi quotidien intelligent','Tracker eau, calories, activité — avec recommandations adaptatives.'],
        ];
        foreach($feats as $f): ?>
        <div class="afl-item">
          <div class="afl-num"><?= $f[0] ?></div>
          <div class="afl-body">
            <div class="afl-title"><?= $f[0] ?>. <?= $f[1] ?></div>
            <div class="afl-desc"><?= $f[2] ?></div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</section>

<!-- ══ RECETTES ══ -->
<section class="sec sec-alt" id="recipes">
  <div data-reveal>
    <div class="tag"><div class="tag-dot"></div> Cuisine saine</div>
    <h2 class="sh">Recettes <span class="or">gourmandes</span><br>& équilibrées</h2>
    <p class="sp">Chaque recette est conçue par nos nutritionnistes pour le goût ET les apports nutritionnels.</p>
  </div>

  <div class="bento-grid">
    <?php
    $recipes = [
      ['https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=700&q=80','Végétarien','Facile','Bowl quinoa & légumes rôtis','Riche en protéines végétales et fibres. Le déjeuner parfait pour rester en forme toute l\'après-midi.','380 kcal','25 min'],
      ['https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=500&q=80','Protéiné','Sport','Salade poulet avocat','Protéines maigres + bons lipides. Idéal post-entraînement.','450 kcal','15 min'],
      ['https://images.unsplash.com/photo-1555992336-03a23c7b20ee?w=500&q=80','Detox','Sain','Smoothie bowl mangue','Antioxydants & enzymes digestives pour un boost matinal.','280 kcal','10 min'],
      ['https://images.unsplash.com/photo-1506084868230-bb9d95c24759?w=500&q=80','Low-carb','Rapide','Omelette aux légumes','Petit-déjeuner protéiné pour commencer la journée en force.','320 kcal','8 min'],
      ['https://images.unsplash.com/photo-1484723091739-30a097e8f929?w=700&q=80','Équilibré','Complet','Assiette saumon & quinoa','Oméga-3, protéines complètes et glucides complexes — le repas idéal.','520 kcal','20 min'],
    ];
    foreach($recipes as $i => $r): ?>
    <div class="bento" data-reveal data-delay="<?= ($i%3)+1 ?>">
      <img class="bento-img" src="<?= $r[0] ?>" alt="<?= $r[3] ?>">
      <div class="bento-body">
        <div class="bento-tags">
          <span class="btag g"><?= $r[1] ?></span>
          <span class="btag o"><?= $r[2] ?></span>
        </div>
        <div class="bento-title"><?= $r[3] ?></div>
        <div class="bento-desc"><?= $r[4] ?></div>
        <div class="bento-footer">
          <span><i class="fa fa-fire"></i><?= $r[5] ?></span>
          <span><i class="fa fa-clock"></i><?= $r[6] ?></span>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
</section>

<!-- ══ CONSEILS ══ -->
<section class="sec" id="conseils">
  <div data-reveal>
    <div class="tag"><div class="tag-dot"></div> Santé & Science</div>
    <h2 class="sh">6 habitudes qui<br><span class="g it">changent tout</span></h2>
    <p class="sp">Validées par la recherche, accessibles dès aujourd'hui. Pas besoin de révolution — juste des gestes justes.</p>
  </div>

  <div class="conseils-wrap">
    <?php
    $tips = [
      ['💧','Hydratation','Métabolisme','Boire 1,5 à 2,5L d\'eau / jour améliore la concentration, accélère le métabolisme et réduit la fatigue chronique. Commencez chaque matin par un grand verre.'],
      ['🌈','Manger coloré','Vitamines','Chaque couleur = un nutriment différent. Visez 5 couleurs par assiette pour couvrir l\'ensemble de vos besoins micronutritionnels.'],
      ['🥩','Protéines','Satiété','25g de protéines par repas maintient la masse musculaire, prolonge la satiété et stabilise la glycémie. Crucial pour tout objectif.'],
      ['😴','Sommeil','Hormones','7-9h de sommeil régulent leptine et ghréline — hormones de la faim. Mal dormir augmente les fringales de 45% le lendemain.'],
      ['🏃','Mouvement','Cardio','30 min de marche soutenue par jour réduit le risque cardiovasculaire de 35% et boost la sérotonine durablement.'],
      ['🍊','Anti-sucre','Glycémie','Remplacer le sucre raffiné par des fruits entiers réduit l\'impact glycémique de 3× et les fibres comblent la faim plus durablement.'],
    ];
    foreach($tips as $i => $t): ?>
    <div class="conseil" data-reveal data-delay="<?= ($i%3)+1 ?>">
      <div class="conseil-bg-num">0<?= $i+1 ?></div>
      <div class="conseil-ico"><?= $t[0] ?></div>
      <div class="conseil-title"><?= $t[1] ?></div>
      <div class="conseil-text"><?= $t[3] ?></div>
      <span class="conseil-tag"><?= $t[2] ?></span>
    </div>
    <?php endforeach; ?>
  </div>
</section>

<!-- ══ SPORT ══ -->
<section class="sec sec-alt" id="sport">
  <div data-reveal>
    <div class="tag"><div class="tag-dot"></div> Activité physique</div>
    <h2 class="sh">Sport &amp; <span class="or">bien-être</span><br>pour tous</h2>
    <p class="sp">Des programmes progressifs, à la maison ou en salle, pour atteindre vos objectifs physiques.</p>
  </div>

  <div class="sport-scroll">
    <?php
    $sports = [
      ['https://images.unsplash.com/photo-1517963879433-6ad2b056d712?w=700&q=80','Force & Cardio','Fitness & Musculation','Programmes 4 semaines débutants et avancés. Force, endurance, définition.'],
      ['https://images.unsplash.com/photo-1506126613408-eca07ce68773?w=600&q=80','Flexibilité','Yoga & Relaxation','Stretching, méditation, réduction du stress. Corps et esprit en harmonie.'],
      ['https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=600&q=80','Endurance','Running & Cardio','De 0 à 10km en 8 semaines. Plans d\'entraînement progressifs et sûrs.'],
    ];
    foreach($sports as $i => $sp): ?>
    <div class="sport-item" data-reveal data-delay="<?= $i+1 ?>">
      <img src="<?= $sp[0] ?>" alt="<?= $sp[2] ?>">
      <div class="sport-overlay">
        <div class="sport-cat"><?= $sp[1] ?></div>
        <div class="sport-name"><?= $sp[2] ?></div>
        <div class="sport-desc"><?= $sp[3] ?></div>
        <a href="index.php?url=User/auth" class="sport-btn">
          Voir le programme <i class="fa fa-arrow-right" style="font-size:10px;filter:none;"></i>
        </a>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
</section>

<!-- ══ TÉMOIGNAGES ══ -->
<section class="sec" id="temoignages">
  <div data-reveal>
    <div class="tag"><div class="tag-dot"></div> Ils nous font confiance</div>
    <h2 class="sh">Des <span class="it g">résultats</span><br>qui parlent</h2>
  </div>

  <div class="temos">
    <?php
    $temos = [
      ['S','Sonia B.','Perte de poids — -8kg','En 2 mois, j\'ai perdu 8kg sans me priver ni souffrir. Le plan nutritionnel personnalisé est d\'une précision bluffante. EcoNutri a vraiment changé mon quotidien.'],
      ['K','Karim A.','Équilibre — IMC normalisé','Le dashboard m\'a appris à vraiment comprendre ce que je mange. IMC, calories, eau — tout est là, clair et actionnable. Je recommande sans hésitation.'],
      ['L','Leila M.','Prise de masse — +5kg muscle','Sport + nutrition = la vraie formule. En 3 mois j\'ai pris 5kg de muscle. Les plans sont cohérents entre eux, c\'est ce qui fait la différence.'],
    ];
    foreach($temos as $i => $t): ?>
    <div class="temo" data-reveal data-delay="<?= $i+1 ?>">
      <div class="temo-quote">"</div>
      <div class="temo-stars">
        <?php for($s=0;$s<5;$s++): ?><i class="fa fa-star"></i><?php endfor; ?>
      </div>
      <div class="temo-text"><?= $t[3] ?></div>
      <div class="temo-person">
        <div class="temo-av"><?= $t[0] ?></div>
        <div>
          <div class="temo-name"><?= $t[1] ?></div>
          <div class="temo-status"><?= $t[2] ?></div>
        </div>
        <div class="temo-res">✅ Vérifié</div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
</section>

<!-- ══ CTA FINALE ══ -->
<div class="cta-sec" data-reveal="scale">
  <div class="cta-sec-bg"></div>
  <div class="cta-sec-content">
    <div class="cta-sec-text">
      <h2>Prêt à <em>transformer</em><br>votre alimentation ?</h2>
      <p>Rejoignez 1 240 membres qui ont déjà changé leur vie.</p>
    </div>
    <div class="cta-sec-btns">
      <a href="index.php?url=User/auth" class="cta-main">
        Créer mon compte gratuit
        <span class="cta-main-arrow"><i class="fa fa-arrow-right" style="filter:none;font-size:10px;"></i></span>
      </a>
      <a href="#about" class="cta-ghost">En savoir plus</a>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>

<script>
/* ── CURSEUR CUSTOM ── */
const cursor = document.getElementById('cursor');
const ring   = document.getElementById('cursorRing');
let mx = 0, my = 0, rx = 0, ry = 0;

document.addEventListener('mousemove', e => { mx = e.clientX; my = e.clientY; });

(function loop() {
  rx += (mx - rx) * 0.2;
  ry += (my - ry) * 0.2;
  if (cursor) { cursor.style.left = mx + 'px'; cursor.style.top = my + 'px'; }
  if (ring)   { ring.style.left   = rx + 'px'; ring.style.top  = ry + 'px'; }
  requestAnimationFrame(loop);
})();

document.querySelectorAll('a, button').forEach(el => {
  el.addEventListener('mouseenter', () => {
    if (cursor) { cursor.style.width = '18px'; cursor.style.height = '18px'; }
    if (ring)   { ring.style.width   = '52px'; ring.style.height   = '52px'; }
  });
  el.addEventListener('mouseleave', () => {
    if (cursor) { cursor.style.width = '10px'; cursor.style.height = '10px'; }
    if (ring)   { ring.style.width   = '36px'; ring.style.height   = '36px'; }
  });
});

/* ── NAVBAR SCROLL ── */
const nav = document.getElementById('mainNav');
window.addEventListener('scroll', () => {
  nav.classList.toggle('solid', window.scrollY > 60);
}, { passive: true });

/* ── SCROLL REVEAL ── */
const revObs = new IntersectionObserver((entries) => {
  entries.forEach(e => {
    if (e.isIntersecting) { e.target.classList.add('in'); revObs.unobserve(e.target); }
  });
}, { threshold: 0.08, rootMargin: '0px 0px -40px 0px' });

document.querySelectorAll('[data-reveal]').forEach(el => revObs.observe(el));

/* ── COUNTER ANIMATION ── */
function animCount(el, target, suffix) {
  let n = 0;
  const step = Math.max(1, Math.ceil(target / 55));
  const t = setInterval(() => {
    n = Math.min(n + step, target);
    el.textContent = n.toLocaleString() + (suffix || '');
    if (n >= target) clearInterval(t);
  }, 22);
}

const cntObs = new IntersectionObserver((entries) => {
  entries.forEach(e => {
    if (!e.isIntersecting) return;
    e.target.querySelectorAll('[data-count]').forEach(el => {
      const raw = el.dataset.count;
      animCount(el, parseInt(raw), '');
    });
    cntObs.unobserve(e.target);
  });
}, { threshold: 0.4 });

document.querySelectorAll('.stats-row').forEach(el => cntObs.observe(el));
</script>