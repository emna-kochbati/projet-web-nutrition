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
  }
  body::before {
    content: '';
    position: fixed;
    inset: 0;
    background: radial-gradient(ellipse 50% 60% at 50% 40%, rgba(46,125,50,0.2) 0%, transparent 70%);
    pointer-events: none;
  }
  .card {
    background: #111f14;
    border: 1px solid rgba(76,175,80,0.2);
    border-radius: 24px;
    padding: 60px 52px;
    width: 460px;
    text-align: center;
    position: relative;
    z-index: 1;
    box-shadow: 0 30px 80px rgba(0,0,0,0.5);
    animation: fadeUp .5s ease both;
  }
  @keyframes fadeUp {
    from { opacity: 0; transform: translateY(20px); }
    to   { opacity: 1; transform: translateY(0); }
  }
  .success-ring {
    width: 90px;
    height: 90px;
    border-radius: 50%;
    background: rgba(46,125,50,0.15);
    border: 2px solid rgba(76,175,80,0.4);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 24px;
    font-size: 40px;
    animation: pop .5s .2s ease both;
  }
  @keyframes pop {
    0%   { transform: scale(0.5); opacity: 0; }
    70%  { transform: scale(1.1); }
    100% { transform: scale(1); opacity: 1; }
  }
  h1 {
    font-family: 'Playfair Display', serif;
    color: #fff;
    font-size: 26px;
    margin-bottom: 12px;
  }
  .sub {
    font-size: 14px;
    color: rgba(255,255,255,0.45);
    line-height: 1.7;
    margin-bottom: 32px;
  }
  .name-badge {
    display: inline-block;
    background: rgba(76,175,80,0.15);
    border: 1px solid rgba(76,175,80,0.3);
    color: #81c784;
    padding: 4px 14px;
    border-radius: 20px;
    font-size: 14px;
    font-weight: 500;
    margin-bottom: 24px;
  }
  .status-row {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    background: rgba(46,125,50,0.1);
    border: 1px solid rgba(76,175,80,0.2);
    border-radius: 12px;
    padding: 12px 20px;
    margin-bottom: 28px;
    font-size: 13px;
    color: rgba(255,255,255,0.5);
  }
  .status-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #4caf50;
    box-shadow: 0 0 8px rgba(76,175,80,0.6);
    animation: pulse 2s infinite;
  }
  @keyframes pulse {
    0%, 100% { box-shadow: 0 0 8px rgba(76,175,80,0.6); }
    50%       { box-shadow: 0 0 16px rgba(76,175,80,0.9); }
  }
  .status-text { color: #81c784; font-weight: 500; }
  .btn-main {
    display: block;
    width: 100%;
    padding: 14px;
    background: linear-gradient(135deg, #2e7d32, #43a047);
    border: none;
    border-radius: 10px;
    color: #fff;
    font-size: 15px;
    font-weight: 600;
    font-family: 'DM Sans', sans-serif;
    cursor: pointer;
    text-decoration: none;
    transition: transform .2s, box-shadow .2s;
    letter-spacing: 0.2px;
  }
  .btn-main:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(46,125,50,0.4);
  }
</style>

<div class="card">
  <div class="success-ring">✓</div>
  <h1>Compte activé !</h1>
  <?php if (!empty($_SESSION['activated_name'])): ?>
    <div class="name-badge">👋 <?= htmlspecialchars($_SESSION['activated_name']) ?></div>
  <?php endif; ?>
  <p class="sub">Votre compte EcoNutri est maintenant actif. Vous pouvez vous connecter et commencer votre parcours nutrition.</p>
  <div class="status-row">
    <div class="status-dot"></div>
    Statut du compte : <span class="status-text">Actif</span>
  </div>
  <a href="index.php?url=User/auth" class="btn-main">Accéder à la connexion →</a>
</div>

<?php unset($_SESSION['activated_name']); ?>
<?php include __DIR__ . '/../partials/footer.php'; ?>