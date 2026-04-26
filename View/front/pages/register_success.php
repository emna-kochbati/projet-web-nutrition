<!-- ======================================================
     FICHIER: register_success.php
     CHEMIN : View/front/pages/register_success.php
====================================================== -->
<?php include __DIR__ . '/../partials/header.php'; ?>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
<style>
  *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
  body{font-family:'DM Sans',sans-serif;background:#0d1f12;min-height:100vh;display:flex;align-items:center;justify-content:center}
  body::before{content:'';position:fixed;inset:0;background:radial-gradient(ellipse 50% 60% at 50% 40%,rgba(46,125,50,0.2) 0%,transparent 70%);pointer-events:none}
  .card{background:#111f14;border:1px solid rgba(76,175,80,0.2);border-radius:24px;padding:52px;width:520px;text-align:center;position:relative;z-index:1;box-shadow:0 30px 80px rgba(0,0,0,0.5);animation:fadeUp .5s ease both}
  @keyframes fadeUp{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:translateY(0)}}
  .mail-icon{font-size:52px;margin-bottom:20px;animation:swing .6s .3s ease both}
  @keyframes swing{0%{transform:rotate(-15deg)}50%{transform:rotate(10deg)}100%{transform:rotate(0)}}
  h1{font-family:'Playfair Display',serif;color:#fff;font-size:26px;margin-bottom:10px}
  .sub{font-size:14px;color:rgba(255,255,255,0.45);line-height:1.7;margin-bottom:28px}
  .email-badge{display:inline-flex;align-items:center;gap:8px;background:rgba(76,175,80,0.1);border:1px solid rgba(76,175,80,0.25);color:#81c784;padding:8px 18px;border-radius:10px;font-size:14px;font-weight:500;margin-bottom:28px}
  .link-box{background:rgba(255,255,255,0.04);border:1px dashed rgba(76,175,80,0.35);border-radius:12px;padding:16px;margin-bottom:24px;text-align:left}
  .link-box .lbl{font-size:11px;text-transform:uppercase;letter-spacing:.06em;color:rgba(255,255,255,0.25);margin-bottom:8px}
  .link-box a{font-size:13px;color:#66bb6a;word-break:break-all;text-decoration:underline}
  .note{font-size:12px;color:rgba(255,255,255,0.25);margin-bottom:24px}
  .btn{display:block;width:100%;padding:14px;background:linear-gradient(135deg,#2e7d32,#43a047);border:none;border-radius:10px;color:#fff;font-size:15px;font-weight:600;font-family:'DM Sans',sans-serif;cursor:pointer;text-decoration:none;transition:transform .2s,box-shadow .2s}
  .btn:hover{transform:translateY(-2px);box-shadow:0 8px 24px rgba(46,125,50,0.4)}
</style>
<div class="card">
  <div class="mail-icon">📧</div>
  <h1>Vérifiez votre email</h1>
  <?php if (!empty($_SESSION['activation_email'])): ?>
    <div class="email-badge">✉ <?= htmlspecialchars($_SESSION['activation_email']) ?></div>
  <?php endif; ?>
  <p class="sub">Un lien d'activation a été envoyé. Cliquez dessus pour activer votre compte EcoNutri et commencer votre parcours.</p>
 
  <?php if (!empty($_SESSION['activation_link'])): ?>
    <div class="link-box">
      <div class="lbl">🔗 Simulation — lien d'activation</div>
      <a href="<?= htmlspecialchars($_SESSION['activation_link']) ?>">
        <?= htmlspecialchars($_SESSION['activation_link']) ?>
      </a>
    </div>
    <?php unset($_SESSION['activation_link'], $_SESSION['activation_email']); ?>
  <?php endif; ?>
 
  <p class="note">En production, ce lien serait envoyé par email automatiquement. Il expire dans 24h.</p>
  <a href="index.php?url=User/auth" class="btn">Retour à la connexion →</a>
</div>
<?php include __DIR__ . '/../partials/footer.php'; ?>