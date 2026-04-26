<?php include __DIR__ . '/../partials/header.php'; ?>

<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">

<style>
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

  body {
    font-family: 'DM Sans', sans-serif;
    background: #0d1f12;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
  }

  /* Background animated leaves */
  body::before {
    content: '';
    position: fixed;
    inset: 0;
    background:
      radial-gradient(ellipse 60% 50% at 20% 30%, rgba(46,125,50,0.18) 0%, transparent 70%),
      radial-gradient(ellipse 40% 60% at 80% 70%, rgba(27,94,32,0.15) 0%, transparent 70%);
    pointer-events: none;
  }

  /* Floating particles */
  .particle {
    position: fixed;
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: rgba(76,175,80,0.25);
    animation: float linear infinite;
    pointer-events: none;
  }

  @keyframes float {
    0%   { transform: translateY(100vh) rotate(0deg); opacity: 0; }
    10%  { opacity: 1; }
    90%  { opacity: 1; }
    100% { transform: translateY(-10vh) rotate(720deg); opacity: 0; }
  }

  /* CARD */
  .auth-card {
    position: relative;
    width: 920px;
    height: 560px;
    display: flex;
    border-radius: 24px;
    overflow: hidden;
    box-shadow: 0 30px 80px rgba(0,0,0,0.5), 0 0 0 1px rgba(76,175,80,0.15);
    z-index: 1;
  }

  /* LEFT PANEL */
  .auth-left {
    width: 45%;
    background: #0d1f12;
    padding: 50px 44px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    position: relative;
    z-index: 2;
    flex-shrink: 0;
    border-right: 1px solid rgba(76,175,80,0.12);
  }

  .brand {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 36px;
  }

  .brand-icon {
    width: 36px;
    height: 36px;
    background: linear-gradient(135deg, #2e7d32, #66bb6a);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
  }

  .brand-name {
    font-family: 'Playfair Display', serif;
    font-size: 22px;
    color: #fff;
    letter-spacing: -0.3px;
  }

  .brand-name span { color: #66bb6a; }

  .panel-title {
    font-family: 'Playfair Display', serif;
    font-size: 30px;
    color: #fff;
    line-height: 1.2;
    margin-bottom: 8px;
  }

  .panel-sub {
    font-size: 13px;
    color: rgba(255,255,255,0.45);
    margin-bottom: 32px;
  }

  /* ALERT */
  .alert-box {
    padding: 10px 14px;
    border-radius: 10px;
    font-size: 13px;
    margin-bottom: 16px;
    display: flex;
    align-items: center;
    gap: 8px;
  }
  .alert-box.error   { background: rgba(229,57,53,0.15); color: #ef9a9a; border: 1px solid rgba(229,57,53,0.25); }
  .alert-box.success { background: rgba(46,125,50,0.2);  color: #a5d6a7; border: 1px solid rgba(46,125,50,0.3); }

  /* FORM */
  .form-group {
    position: relative;
    margin-bottom: 14px;
  }

  .form-group input {
    width: 100%;
    padding: 12px 16px;
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 10px;
    color: #fff;
    font-size: 14px;
    font-family: 'DM Sans', sans-serif;
    outline: none;
    transition: border-color .25s, background .25s;
  }

  .form-group input::placeholder { color: rgba(255,255,255,0.3); }

  .form-group input:focus {
    border-color: #4caf50;
    background: rgba(76,175,80,0.08);
  }

  .form-group .icon {
    position: absolute;
    right: 14px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 15px;
    pointer-events: none;
  }

  .btn-main {
    width: 100%;
    padding: 13px;
    background: linear-gradient(135deg, #2e7d32, #43a047);
    border: none;
    border-radius: 10px;
    color: #fff;
    font-size: 15px;
    font-weight: 600;
    font-family: 'DM Sans', sans-serif;
    cursor: pointer;
    transition: transform .2s, box-shadow .2s;
    margin-top: 6px;
    letter-spacing: 0.2px;
  }

  .btn-main:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(46,125,50,0.4);
  }

  .switch-link {
    text-align: center;
    margin-top: 18px;
    font-size: 13px;
    color: rgba(255,255,255,0.4);
  }

  .switch-link a {
    color: #66bb6a;
    text-decoration: none;
    font-weight: 600;
    cursor: pointer;
  }

  .forgot-link {
    text-align: right;
    margin-top: -8px;
    margin-bottom: 10px;
  }

  .forgot-link a {
    font-size: 12px;
    color: #66bb6a;
    text-decoration: none;
    cursor: pointer;
  }

  /* RIGHT PANEL */
  .auth-right {
    flex: 1;
    position: relative;
    overflow: hidden;
  }

  .auth-right img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    filter: brightness(0.45) saturate(1.2);
    transition: transform 8s ease;
  }

  .auth-right:hover img {
    transform: scale(1.05);
  }

  .right-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, rgba(13,31,18,0.7) 0%, rgba(46,125,50,0.2) 100%);
    display: flex;
    flex-direction: column;
    justify-content: flex-end;
    padding: 40px;
  }

  .right-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 14px;
    background: rgba(76,175,80,0.2);
    border: 1px solid rgba(76,175,80,0.35);
    border-radius: 20px;
    color: #a5d6a7;
    font-size: 12px;
    font-weight: 500;
    margin-bottom: 14px;
    width: fit-content;
  }

  .right-title {
    font-family: 'Playfair Display', serif;
    font-size: 32px;
    color: #fff;
    line-height: 1.25;
    margin-bottom: 12px;
  }

  .right-title span { color: #81c784; }

  .right-sub {
    font-size: 13px;
    color: rgba(255,255,255,0.5);
    line-height: 1.7;
    max-width: 260px;
  }

  /* REGISTER PANEL */
  .register-panel {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.7);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 100;
    opacity: 0;
    pointer-events: none;
    transition: opacity .3s;
    backdrop-filter: blur(4px);
  }

  .register-panel.open {
    opacity: 1;
    pointer-events: all;
  }

  .register-box {
    background: #0d1f12;
    border: 1px solid rgba(76,175,80,0.2);
    border-radius: 20px;
    padding: 40px 44px;
    width: 520px;
    max-height: 90vh;
    overflow-y: auto;
    transform: translateY(20px);
    transition: transform .3s;
    scrollbar-width: thin;
    scrollbar-color: #2e7d32 transparent;
  }

  .register-panel.open .register-box {
    transform: translateY(0);
  }

  .register-box h2 {
    font-family: 'Playfair Display', serif;
    color: #fff;
    font-size: 26px;
    margin-bottom: 6px;
  }

  .register-box .sub {
    font-size: 13px;
    color: rgba(255,255,255,0.4);
    margin-bottom: 24px;
  }

  .form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
  }

  .register-box select {
    width: 100%;
    padding: 12px 16px;
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 10px;
    color: rgba(255,255,255,0.7);
    font-size: 14px;
    font-family: 'DM Sans', sans-serif;
    outline: none;
    transition: border-color .25s;
    appearance: none;
    margin-bottom: 14px;
  }

  .register-box select:focus { border-color: #4caf50; }

  .close-btn {
    position: absolute;
    top: 16px;
    right: 16px;
    background: rgba(255,255,255,0.08);
    border: none;
    color: rgba(255,255,255,0.6);
    width: 30px;
    height: 30px;
    border-radius: 50%;
    cursor: pointer;
    font-size: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    width: auto;
    padding: 6px 12px;
    border-radius: 8px;
    font-size: 13px;
    float: right;
    margin-top: -10px;
    margin-bottom: 20px;
  }

  .section-label {
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: .08em;
    color: rgba(255,255,255,0.25);
    margin-bottom: 8px;
    margin-top: 4px;
  }
</style>

<!-- Particles -->
<div class="particle" style="left:10%;animation-duration:12s;animation-delay:0s;"></div>
<div class="particle" style="left:25%;animation-duration:18s;animation-delay:3s;width:4px;height:4px;"></div>
<div class="particle" style="left:50%;animation-duration:14s;animation-delay:6s;width:8px;height:8px;"></div>
<div class="particle" style="left:70%;animation-duration:16s;animation-delay:1s;"></div>
<div class="particle" style="left:85%;animation-duration:20s;animation-delay:9s;width:4px;height:4px;"></div>

<!-- AUTH CARD -->
<div class="auth-card">

  <!-- LEFT: LOGIN -->
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

    <form method="POST" action="index.php?url=User/login">
      <div class="form-group">
        <input type="email" name="email" placeholder="Adresse email" required autocomplete="email">
        <span class="icon">✉</span>
      </div>
      <div class="form-group">
        <input type="password" name="password" placeholder="Mot de passe" required autocomplete="current-password">
        <span class="icon">🔒</span>
      </div>
      <div class="forgot-link">
        <a href="index.php?url=User/resetPassword">Mot de passe oublié ?</a>
      </div>
      <button type="submit" class="btn-main">Se connecter →</button>
    </form>

    <div class="switch-link">
      Pas encore membre ? <a onclick="openRegister()">Créer un compte</a>
    </div>

  </div>

  <!-- RIGHT: IMAGE -->
  <div class="auth-right">
    <img src="https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=800&q=80" alt="Alimentation saine">
    <div class="right-overlay">
      <div class="right-badge">🌱 Alimentation durable</div>
      <div class="right-title">Mangez mieux,<br>vivez <span>plus vert</span>.</div>
      <div class="right-sub">Suivez votre nutrition intelligente et adoptez une alimentation durable au quotidien.</div>
    </div>
  </div>

</div>

<!-- REGISTER MODAL -->
<div class="register-panel" id="registerPanel">
  <div class="register-box">

    <button class="close-btn" onclick="closeRegister()">✕ Fermer</button>

    <h2>Créer un compte 🌱</h2>
    <p class="sub">Rejoignez EcoNutri et commencez votre parcours</p>

    <form method="POST" action="index.php?url=User/register">

      <div class="section-label">Informations personnelles</div>
      <div class="form-row">
        <div class="form-group"><input type="text" name="nom" placeholder="Nom complet" required></div>
        <div class="form-group"><input type="email" name="email" placeholder="Email" required></div>
      </div>
      <div class="form-group">
        <input type="password" name="password" placeholder="Mot de passe (min. 8 caractères)" required>
      </div>

      <div class="section-label">Données santé</div>
      <div class="form-row">
        <div class="form-group"><input type="number" name="age" placeholder="Âge" min="10" max="120"></div>
        <div class="form-group"><input type="number" name="poids" placeholder="Poids (kg)" step="0.1"></div>
      </div>
      <div class="form-row">
        <div class="form-group"><input type="number" name="taille" placeholder="Taille (cm)"></div>
        <div class="form-group"><input type="text" name="maladie" placeholder="Maladie (optionnel)"></div>
      </div>

      <div class="section-label">Préférences</div>
      <select name="objectif">
        <option value="">🎯 Objectif nutritionnel</option>
        <option value="Perte de poids">Perte de poids</option>
        <option value="Prise de masse">Prise de masse</option>
        <option value="Équilibre alimentaire">Équilibre alimentaire</option>
        <option value="Végétarien">Mode végétarien</option>
      </select>

      <select name="activite">
        <option value="">🏃 Niveau d'activité</option>
        <option value="Faible">Faible — sédentaire</option>
        <option value="Moyen">Moyen — actif</option>
        <option value="Élevé">Élevé — sportif</option>
      </select>

      <input type="hidden" name="role" value="user">

      <button type="submit" class="btn-main" style="margin-top:8px;">Créer mon compte →</button>

    </form>
  </div>
</div>

<script>
  function openRegister() {
    document.getElementById('registerPanel').classList.add('open');
    document.body.style.overflow = 'hidden';
  }

  function closeRegister() {
    document.getElementById('registerPanel').classList.remove('open');
    document.body.style.overflow = '';
  }

  // Fermer en cliquant outside
  document.getElementById('registerPanel').addEventListener('click', function(e) {
    if (e.target === this) closeRegister();
  });
</script>

<?php include __DIR__ . '/../partials/footer.php'; ?>