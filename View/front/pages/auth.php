<?php include __DIR__ . '/../partials/header.php'; ?>

<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
/* ── RESET TOTAL — empêcher le footer de s'afficher ── */
html, body {
  height: 100%;
  overflow: hidden;
}

body {
  font-family: 'DM Sans', sans-serif;
  background: #0a1a0d;
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 100vh;
  margin: 0;
  padding: 0;
}

/* Cacher le footer globalement sur cette page */
footer, .footer { display: none !important; }

/* ── BACKGROUND ── */
body::before {
  content: '';
  position: fixed; inset: 0;
  background:
    radial-gradient(ellipse 55% 60% at 15% 25%, rgba(46,125,50,0.22) 0%, transparent 65%),
    radial-gradient(ellipse 45% 55% at 85% 75%, rgba(27,94,32,0.18) 0%, transparent 65%),
    radial-gradient(ellipse 30% 40% at 50% 50%, rgba(0,0,0,0.3) 0%, transparent 80%);
  pointer-events: none;
  z-index: 0;
}

/* ── PARTICLES ── */
.particle {
  position: fixed;
  border-radius: 50%;
  background: rgba(76,175,80,0.3);
  animation: floatUp linear infinite;
  pointer-events: none;
  z-index: 0;
}

@keyframes floatUp {
  0%   { transform: translateY(100vh) rotate(0deg); opacity: 0; }
  8%   { opacity: 1; }
  92%  { opacity: 1; }
  100% { transform: translateY(-8vh) rotate(720deg); opacity: 0; }
}

/* ── AUTH CARD ── */
.auth-card {
  position: relative;
  z-index: 1;
  width: 940px;
  height: 580px;
  display: flex;
  border-radius: 24px;
  overflow: hidden;
  box-shadow:
    0 40px 100px rgba(0,0,0,0.6),
    0 0 0 1px rgba(76,175,80,0.18),
    inset 0 1px 0 rgba(255,255,255,0.05);
  animation: cardIn .5s ease both;
}

@keyframes cardIn {
  from { opacity: 0; transform: translateY(16px) scale(.98); }
  to   { opacity: 1; transform: translateY(0) scale(1); }
}

/* ── LEFT PANEL ── */
.auth-left {
  width: 48%;
  background: linear-gradient(160deg, #0c1e0f 0%, #0a1a0d 60%, #0d2210 100%);
  padding: 44px 48px;
  display: flex;
  flex-direction: column;
  justify-content: center;
  flex-shrink: 0;
  border-right: 1px solid rgba(76,175,80,0.1);
  position: relative;
  overflow: hidden;
}

.auth-left::before {
  content: '';
  position: absolute;
  bottom: -60px; left: -60px;
  width: 200px; height: 200px;
  border-radius: 50%;
  background: rgba(46,125,50,0.08);
  pointer-events: none;
}

/* ── BRAND ── */
.brand {
  display: flex; align-items: center; gap: 10px;
  margin-bottom: 38px;
}

.brand-icon {
  width: 38px; height: 38px;
  background: linear-gradient(135deg, #2e7d32, #66bb6a);
  border-radius: 11px;
  display: flex; align-items: center; justify-content: center;
  font-size: 19px;
  box-shadow: 0 4px 14px rgba(46,125,50,0.35);
}

.brand-name {
  font-family: 'Playfair Display', serif;
  font-size: 22px; color: #fff; letter-spacing: -.3px;
}

.brand-name span { color: #66bb6a; }

.panel-title {
  font-family: 'Playfair Display', serif;
  font-size: 28px; color: #fff; line-height: 1.2; margin-bottom: 6px;
}

.panel-sub {
  font-size: 13px; color: rgba(255,255,255,0.42); margin-bottom: 28px;
}

/* ── ALERT ── */
.alert-box {
  padding: 10px 14px; border-radius: 10px;
  font-size: 13px; margin-bottom: 16px;
  display: flex; align-items: center; gap: 8px;
}

.alert-box.error   { background: rgba(229,57,53,0.15); color: #ef9a9a; border: 1px solid rgba(229,57,53,0.28); }
.alert-box.success { background: rgba(46,125,50,0.2);  color: #a5d6a7; border: 1px solid rgba(46,125,50,0.32); }

/* ── CHAMP DE FORMULAIRE ── */
.f-group {
  position: relative;
  margin-bottom: 6px;
}

.f-group input {
  width: 100%;
  padding: 12px 42px 12px 16px;
  background: rgba(255,255,255,0.055);
  border: 1.5px solid rgba(255,255,255,0.1);
  border-radius: 11px;
  color: #fff;
  font-size: 14px; font-family: 'DM Sans', sans-serif;
  outline: none;
  transition: border-color .22s, background .22s, box-shadow .22s;
}

.f-group input::placeholder { color: rgba(255,255,255,0.28); }

.f-group input:focus {
  border-color: rgba(76,175,80,0.5);
  background: rgba(76,175,80,0.07);
  box-shadow: 0 0 0 3px rgba(76,175,80,0.1);
}

/* États validation */
.f-group input.v-ok {
  border-color: #43a047 !important;
  background: rgba(67,160,71,0.09) !important;
  box-shadow: 0 0 0 3px rgba(67,160,71,0.12) !important;
}

.f-group input.v-err {
  border-color: #e53935 !important;
  background: rgba(229,57,53,0.07) !important;
  box-shadow: 0 0 0 3px rgba(229,57,53,0.12) !important;
}

/* Icône droite */
.f-icon {
  position: absolute; right: 14px; top: 50%;
  transform: translateY(-50%);
  font-size: 14px; pointer-events: none; color: rgba(255,255,255,0.25);
  transition: color .2s;
}

/* Icône état validation */
.f-state-icon {
  position: absolute; right: 14px; top: 14px;
  font-size: 13px; pointer-events: none;
  opacity: 0; transition: opacity .2s;
}

.f-group input.v-ok  ~ .f-state-icon { opacity: 1; color: #43a047; }
.f-group input.v-err ~ .f-state-icon { opacity: 1; color: #e53935; }
.f-group input.v-ok  ~ .f-state-icon .ico-ok  { display: inline; }
.f-group input.v-ok  ~ .f-state-icon .ico-err { display: none; }
.f-group input.v-err ~ .f-state-icon .ico-ok  { display: none; }
.f-group input.v-err ~ .f-state-icon .ico-err { display: inline; }
.ico-ok, .ico-err { display: none; }

/* Message d'erreur sous le champ */
.f-msg {
  font-size: 11px; color: #ef9a9a;
  padding: 3px 4px 6px;
  display: none;
  animation: msgIn .2s ease;
}

.f-msg.show { display: block; }

@keyframes msgIn { from{opacity:0;transform:translateY(-3px)} to{opacity:1;transform:translateY(0)} }

/* Forgot */
.forgot-link {
  text-align: right; margin-bottom: 12px;
}
.forgot-link a {
  font-size: 12px; color: #66bb6a; text-decoration: none;
  transition: color .15s;
}
.forgot-link a:hover { color: #a5d6a7; }

/* Bouton principal */
.btn-main {
  width: 100%; padding: 13px;
  background: linear-gradient(135deg, #2e7d32, #43a047);
  border: none; border-radius: 11px;
  color: #fff; font-size: 15px; font-weight: 600;
  font-family: 'DM Sans', sans-serif;
  cursor: pointer; letter-spacing: .2px;
  transition: transform .18s, box-shadow .18s;
  box-shadow: 0 4px 16px rgba(46,125,50,0.3);
}
.btn-main:hover { transform: translateY(-2px); box-shadow: 0 8px 28px rgba(46,125,50,0.45); }
.btn-main:active { transform: translateY(0); }

.switch-link {
  text-align: center; margin-top: 18px;
  font-size: 13px; color: rgba(255,255,255,0.38);
}
.switch-link a { color: #66bb6a; text-decoration: none; font-weight: 600; cursor: pointer; }

/* ── RIGHT PANEL ── */
.auth-right { flex: 1; position: relative; overflow: hidden; }

.auth-right img {
  width: 100%; height: 100%;
  object-fit: cover;
  filter: brightness(.42) saturate(1.3);
  transition: transform 10s ease;
}
.auth-right:hover img { transform: scale(1.06); }

.right-overlay {
  position: absolute; inset: 0;
  background: linear-gradient(160deg, rgba(10,26,13,0.65) 0%, rgba(46,125,50,0.18) 100%);
  display: flex; flex-direction: column; justify-content: flex-end; padding: 44px;
}

.right-badge {
  display: inline-flex; align-items: center; gap: 6px;
  padding: 6px 14px; border-radius: 20px;
  background: rgba(76,175,80,0.22); border: 1px solid rgba(76,175,80,0.38);
  color: #a5d6a7; font-size: 12px; font-weight: 500;
  margin-bottom: 16px; width: fit-content;
}

.right-title {
  font-family: 'Playfair Display', serif;
  font-size: 34px; color: #fff; line-height: 1.22; margin-bottom: 14px;
}
.right-title span { color: #81c784; }

.right-sub {
  font-size: 13px; color: rgba(255,255,255,0.48);
  line-height: 1.75; max-width: 270px;
}

.right-stats {
  display: flex; gap: 20px; margin-top: 24px;
}
.right-stat-val { font-size: 22px; font-weight: 700; color: #fff; line-height: 1; }
.right-stat-lbl { font-size: 11px; color: rgba(255,255,255,0.45); margin-top: 2px; }

/* ── REGISTER MODAL ── */
.register-panel {
  position: fixed; inset: 0;
  background: rgba(0,0,0,0.72);
  display: flex; align-items: center; justify-content: center;
  z-index: 200;
  opacity: 0; pointer-events: none;
  transition: opacity .28s;
  backdrop-filter: blur(5px);
}

.register-panel.open { opacity: 1; pointer-events: all; }

.register-box {
  background: linear-gradient(160deg, #0c1e0f, #0a1a0d);
  border: 1px solid rgba(76,175,80,0.2);
  border-radius: 22px;
  padding: 38px 44px;
  width: 540px; max-height: 90vh;
  overflow-y: auto;
  transform: translateY(18px) scale(.98);
  transition: transform .28s;
  scrollbar-width: thin; scrollbar-color: #2e7d32 transparent;
  box-shadow: 0 40px 100px rgba(0,0,0,0.6);
}

.register-panel.open .register-box { transform: translateY(0) scale(1); }

.register-box h2 {
  font-family: 'Playfair Display', serif;
  color: #fff; font-size: 26px; margin-bottom: 6px;
}
.register-box .sub { font-size: 13px; color: rgba(255,255,255,0.38); margin-bottom: 22px; }

/* champs register */
.register-box .f-group input,
.register-box .f-group select {
  width: 100%;
  padding: 11px 16px;
  background: rgba(255,255,255,0.055);
  border: 1.5px solid rgba(255,255,255,0.1);
  border-radius: 10px;
  color: rgba(255,255,255,0.85);
  font-size: 14px; font-family: 'DM Sans', sans-serif;
  outline: none;
  transition: border-color .22s, background .22s, box-shadow .22s;
  appearance: none;
}

.register-box .f-group input::placeholder { color: rgba(255,255,255,0.28); }

.register-box .f-group input:focus,
.register-box .f-group select:focus {
  border-color: rgba(76,175,80,0.5);
  background: rgba(76,175,80,0.07);
  box-shadow: 0 0 0 3px rgba(76,175,80,0.1);
}

.register-box .f-group input.v-ok,
.register-box .f-group select.v-ok {
  border-color: #43a047 !important;
  background: rgba(67,160,71,0.09) !important;
  box-shadow: 0 0 0 3px rgba(67,160,71,0.12) !important;
}

.register-box .f-group input.v-err,
.register-box .f-group select.v-err {
  border-color: #e53935 !important;
  background: rgba(229,57,53,0.07) !important;
  box-shadow: 0 0 0 3px rgba(229,57,53,0.12) !important;
}

.sec-label {
  font-size: 10px; font-weight: 600;
  text-transform: uppercase; letter-spacing: .1em;
  color: rgba(255,255,255,0.25);
  margin: 18px 0 10px;
}
.sec-label:first-child { margin-top: 0; }

.f-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }

/* Jauge force pwd */
.pwd-strength { height: 3px; border-radius: 2px; background: rgba(255,255,255,0.08); overflow: hidden; margin: 4px 0 6px; }
.pwd-bar { height: 100%; border-radius: 2px; width: 0; transition: width .3s, background .3s; }

/* close btn */
.close-btn {
  float: right; margin-top: -4px; margin-bottom: 18px;
  background: rgba(255,255,255,0.07); border: 1px solid rgba(255,255,255,0.1);
  color: rgba(255,255,255,0.5); padding: 5px 12px;
  border-radius: 8px; font-size: 12px; cursor: pointer;
  font-family: 'DM Sans', sans-serif;
  transition: background .15s, color .15s;
}
.close-btn:hover { background: rgba(229,57,53,0.15); color: #ef9a9a; border-color: rgba(229,57,53,0.3); }
</style>

<!-- ── PARTICLES ── -->
<div class="particle" style="left:8%;width:5px;height:5px;animation-duration:13s;animation-delay:0s;"></div>
<div class="particle" style="left:22%;width:4px;height:4px;animation-duration:19s;animation-delay:3.5s;"></div>
<div class="particle" style="left:48%;width:7px;height:7px;animation-duration:15s;animation-delay:7s;"></div>
<div class="particle" style="left:68%;width:4px;height:4px;animation-duration:17s;animation-delay:1.5s;"></div>
<div class="particle" style="left:84%;width:6px;height:6px;animation-duration:21s;animation-delay:9s;"></div>
<div class="particle" style="left:35%;width:3px;height:3px;animation-duration:16s;animation-delay:5s;"></div>

<!-- ══ AUTH CARD ══ -->
<div class="auth-card">

  <!-- LEFT : LOGIN -->
  <div class="auth-left">

    <div class="brand">
      <div class="brand-icon">🌿</div>
      <div class="brand-name">Eco<span>Nutri</span></div>
    </div>

    <?php if (!empty($_SESSION['error'])): ?>
      <div class="alert-box error">⚠ <?= htmlspecialchars($_SESSION['error']) ?></div>
      <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <?php if (!empty($_SESSION['success'])): ?>
      <div class="alert-box success">✓ <?= htmlspecialchars($_SESSION['success']) ?></div>
      <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <div class="panel-title">Bon retour 👋</div>
    <div class="panel-sub">Connectez-vous à votre espace nutrition</div>

    <!-- FORMULAIRE LOGIN -->
    <form id="loginForm" method="POST" action="index.php?url=User/login" novalidate>

      <div class="f-group">
        <input type="text" id="l-email" name="email"
               placeholder="Adresse email"
               autocomplete="email"
               data-rule="email">
        <span class="f-state-icon">
          <i class="fa fa-circle-check ico-ok"></i>
          <i class="fa fa-circle-xmark ico-err"></i>
        </span>
        <div class="f-msg" id="lmsg-email"></div>
      </div>

      <div class="f-group">
        <input type="password" id="l-pwd" name="password"
               placeholder="Mot de passe"
               autocomplete="current-password"
               data-rule="required">
        <span class="f-state-icon">
          <i class="fa fa-circle-check ico-ok"></i>
          <i class="fa fa-circle-xmark ico-err"></i>
        </span>
        <div class="f-msg" id="lmsg-pwd"></div>
      </div>

      <div class="forgot-link">
        <a href="index.php?url=User/resetPassword">Mot de passe oublié ?</a>
      </div>

      <button type="submit" class="btn-main">Se connecter →</button>
    </form>

    <div class="switch-link">
      Pas encore membre ? <a onclick="openRegister()">Créer un compte</a>
    </div>

  </div><!-- /auth-left -->

  <!-- RIGHT : IMAGE -->
  <div class="auth-right">
    <img src="https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=900&q=80" alt="Alimentation saine">
    <div class="right-overlay">
      <div class="right-badge">🌱 Alimentation durable</div>
      <div class="right-title">Mangez mieux,<br>vivez <span>plus vert</span>.</div>
      <div class="right-sub">Suivez votre nutrition intelligente et adoptez une alimentation durable au quotidien.</div>
      <div class="right-stats">
        <div>
          <div class="right-stat-val">1 240</div>
          <div class="right-stat-lbl">Membres actifs</div>
        </div>
        <div>
          <div class="right-stat-val">320+</div>
          <div class="right-stat-lbl">Recettes saines</div>
        </div>
        <div>
          <div class="right-stat-val">94%</div>
          <div class="right-stat-lbl">Satisfaction</div>
        </div>
      </div>
    </div>
  </div>

</div><!-- /auth-card -->

<!-- ══ REGISTER MODAL ══ -->
<div class="register-panel" id="registerPanel">
  <div class="register-box">

    <button class="close-btn" onclick="closeRegister()">✕ Fermer</button>

    <h2>Créer un compte 🌱</h2>
    <p class="sub">Rejoignez EcoNutri et commencez votre parcours</p>

    <form id="registerForm" method="POST" action="index.php?url=User/register" novalidate>

      <div class="sec-label">Informations personnelles</div>
      <div class="f-row">
        <div class="f-group">
          <input type="text" id="r-nom" name="nom"
                 placeholder="Nom complet"
                 data-rule="required">
          <div class="f-msg" id="rmsg-nom"></div>
        </div>
        <div class="f-group">
          <input type="text" id="r-email" name="email"
                 placeholder="Email"
                 data-rule="email">
          <div class="f-msg" id="rmsg-email"></div>
        </div>
      </div>

      <div class="f-group">
        <input type="password" id="r-pwd" name="password"
               placeholder="Mot de passe (min. 8 caractères)"
               data-rule="password"
               oninput="checkPwdStrength(this.value)">
        <div class="pwd-strength"><div class="pwd-bar" id="rPwdBar"></div></div>
        <div class="f-msg" id="rmsg-pwd"></div>
      </div>

      <div class="sec-label">Données santé</div>
      <div class="f-row">
        <div class="f-group">
          <input type="number" id="r-age" name="age"
                 placeholder="Âge" min="10" max="120"
                 data-rule="age">
          <div class="f-msg" id="rmsg-age"></div>
        </div>
        <div class="f-group">
          <input type="number" id="r-poids" name="poids"
                 placeholder="Poids (kg)" step="0.1"
                 data-rule="poids">
          <div class="f-msg" id="rmsg-poids"></div>
        </div>
      </div>
      <div class="f-row">
        <div class="f-group">
          <input type="number" id="r-taille" name="taille"
                 placeholder="Taille (cm)"
                 data-rule="taille">
          <div class="f-msg" id="rmsg-taille"></div>
        </div>
        <div class="f-group">
          <input type="text" id="r-maladie" name="maladie"
                 placeholder="Maladie (optionnel)"
                 data-rule="optional">
          <div class="f-msg" id="rmsg-maladie"></div>
        </div>
      </div>

      <div class="f-group">
        <input type="tel" id="r-phone" name="phone"
               placeholder="📱 Téléphone (ex: 12345678)"
               data-rule="phone">
        <div class="f-msg" id="rmsg-phone"></div>
      </div>

      <div class="sec-label">Préférences</div>
      <div class="f-group">
        <select id="r-objectif" name="objectif" data-rule="select">
          <option value="">🎯 Objectif nutritionnel</option>
          <option value="Perte de poids">Perte de poids</option>
          <option value="Prise de masse">Prise de masse</option>
          <option value="Équilibre alimentaire">Équilibre alimentaire</option>
          <option value="Végétarien">Mode végétarien</option>
        </select>
        <div class="f-msg" id="rmsg-objectif"></div>
      </div>

      <div class="f-group">
        <select id="r-activite" name="activite" data-rule="select">
          <option value="">🏃 Niveau d'activité</option>
          <option value="Faible">Faible — sédentaire</option>
          <option value="Moyen">Moyen — actif</option>
          <option value="Élevé">Élevé — sportif</option>
        </select>
        <div class="f-msg" id="rmsg-activite"></div>
      </div>

      <input type="hidden" name="role" value="user">
      <button type="submit" class="btn-main" style="margin-top:10px;">Créer mon compte →</button>

    </form>
  </div>
</div>

<!-- ══ VALIDATION JS ══ -->
<script>
/* ════════════════════════════════
   RÈGLES DE VALIDATION
════════════════════════════════ */
const RULES = {
  required: v => v.trim() !== ''
    ? null : 'Ce champ est obligatoire.',

  email: v => {
    if (!v.trim()) return 'L\'adresse email est obligatoire.';
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v.trim())
      ? null : 'Email invalide — ex : nom@domaine.com';
  },

  password: v => {
    if (!v) return 'Le mot de passe est obligatoire.';
    if (v.length < 8) return 'Minimum 8 caractères requis.';
    return null;
  },

  age: v => {
    if (!v) return 'L\'âge est obligatoire.';
    const n = parseInt(v);
    return (!isNaN(n) && n >= 10 && n <= 120) ? null : 'Âge invalide (entre 10 et 120 ans).';
  },

  poids: v => {
    if (!v) return 'Le poids est obligatoire.';
    const n = parseFloat(v);
    return (!isNaN(n) && n >= 20 && n <= 300) ? null : 'Poids invalide (entre 20 et 300 kg).';
  },

  taille: v => {
    if (!v) return 'La taille est obligatoire.';
    const n = parseFloat(v);
    return (!isNaN(n) && n >= 100 && n <= 250) ? null : 'Taille invalide (entre 100 et 250 cm).';
  },

  phone: v => {
    if (!v.trim()) return 'Le numéro de téléphone est obligatoire.';
    const n = v.replace(/\s/g,'');
    return /^[0-9]{8,12}$/.test(n) ? null : 'Numéro invalide (8 à 12 chiffres, sans indicatif).';
  },

  select: v => v !== '' ? null : 'Veuillez sélectionner une option.',

  optional: () => null
};

/* ════════════════════════════════
   APPLIQUER ÉTAT SUR UN CHAMP
════════════════════════════════ */
function applyState(inputEl, msgEl, error) {
  if (error) {
    inputEl.classList.remove('v-ok');
    inputEl.classList.add('v-err');
    msgEl.textContent = '⚠ ' + error;
    msgEl.classList.add('show');
  } else if (inputEl.value.trim() !== '') {
    inputEl.classList.remove('v-err');
    inputEl.classList.add('v-ok');
    msgEl.classList.remove('show');
    msgEl.textContent = '';
  } else {
    inputEl.classList.remove('v-ok', 'v-err');
    msgEl.classList.remove('show');
    msgEl.textContent = '';
  }
  return !error;
}

/* ════════════════════════════════
   VALIDER UN CHAMP UNIQUE
════════════════════════════════ */
function validateOne(inputId, msgId) {
  const input = document.getElementById(inputId);
  const msg   = document.getElementById(msgId);
  if (!input || !msg) return true;
  const rule  = input.dataset.rule || 'required';
  const fn    = RULES[rule] || RULES.required;
  return applyState(input, msg, fn(input.value));
}

/* ════════════════════════════════
   ATTACHER VALIDATION TEMPS RÉEL
════════════════════════════════ */
function attachLive(inputId, msgId) {
  const el = document.getElementById(inputId);
  if (!el) return;

  const validate = () => validateOne(inputId, msgId);

  el.addEventListener('blur', validate);

  el.addEventListener('input', () => {
    if (el.value.trim().length > 0 || el.tagName === 'SELECT') {
      validate();
    } else {
      el.classList.remove('v-ok', 'v-err');
      const msg = document.getElementById(msgId);
      if (msg) { msg.classList.remove('show'); msg.textContent = ''; }
    }
  });

  if (el.tagName === 'SELECT') {
    el.addEventListener('change', validate);
  }
}

/* ════════════════════════════════
   JAUGE FORCE MOT DE PASSE
════════════════════════════════ */
function checkPwdStrength(v) {
  const bar = document.getElementById('rPwdBar');
  if (!bar) return;
  let score = 0;
  if (v.length >= 8)          score += 30;
  if (v.length >= 12)         score += 10;
  if (/[A-Z]/.test(v))        score += 20;
  if (/[0-9]/.test(v))        score += 20;
  if (/[^A-Za-z0-9]/.test(v)) score += 20;
  bar.style.width      = Math.min(score, 100) + '%';
  bar.style.background = score < 35 ? '#e53935' : score < 65 ? '#ff9800' : '#43a047';
}

/* ════════════════════════════════
   VALIDATION FORM LOGIN
════════════════════════════════ */
document.addEventListener('DOMContentLoaded', () => {

  // Live login
  attachLive('l-email', 'lmsg-email');
  attachLive('l-pwd',   'lmsg-pwd');

  document.getElementById('loginForm')?.addEventListener('submit', function(e) {
    const ok1 = validateOne('l-email', 'lmsg-email');
    const ok2 = validateOne('l-pwd',   'lmsg-pwd');
    if (!ok1 || !ok2) {
      e.preventDefault();
      if (!ok1) document.getElementById('l-email').focus();
      else       document.getElementById('l-pwd').focus();
    }
  });

  /* ════════════════════════════════
     VALIDATION FORM REGISTER
  ════════════════════════════════ */
  const regFields = [
    ['r-nom',      'rmsg-nom'],
    ['r-email',    'rmsg-email'],
    ['r-pwd',      'rmsg-pwd'],
    ['r-age',      'rmsg-age'],
    ['r-poids',    'rmsg-poids'],
    ['r-taille',   'rmsg-taille'],
    ['r-maladie',  'rmsg-maladie'],
    ['r-objectif', 'rmsg-objectif'],
    ['r-activite', 'rmsg-activite'],
    ['r-phone',    'rmsg-phone'],
  ];

  regFields.forEach(([id, msgId]) => attachLive(id, msgId));

  document.getElementById('registerForm')?.addEventListener('submit', function(e) {
    let allOk = true;
    let firstErr = null;

    regFields.forEach(([id, msgId]) => {
      const ok = validateOne(id, msgId);
      if (!ok) {
        allOk = false;
        if (!firstErr) firstErr = document.getElementById(id);
      }
    });

    if (!allOk) {
      e.preventDefault();
      if (firstErr) firstErr.scrollIntoView({ behavior: 'smooth', block: 'center' });
      firstErr?.focus();
    }
  });

});

/* ════════════════════════════════
   DRAWER REGISTER
════════════════════════════════ */
function openRegister() {
  document.getElementById('registerPanel').classList.add('open');
  document.body.style.overflow = 'hidden';
  setTimeout(() => document.getElementById('r-nom')?.focus(), 300);
}

function closeRegister() {
  document.getElementById('registerPanel').classList.remove('open');
  document.body.style.overflow = 'hidden'; // garder hidden car page auth
}

document.getElementById('registerPanel')?.addEventListener('click', function(e) {
  if (e.target === this) closeRegister();
});
</script>