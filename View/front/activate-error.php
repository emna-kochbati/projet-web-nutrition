<!-- ======================================================
     FICHIER: activate_error.php
     CHEMIN : View/front/pages/activate_error.php
====================================================== -->
<?php include __DIR__ . '/../partials/header.php'; ?>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
<style>
  *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
  body{font-family:'DM Sans',sans-serif;background:#0d1f12;min-height:100vh;display:flex;align-items:center;justify-content:center}
  .card{background:#111f14;border:1px solid rgba(229,57,53,0.2);border-radius:24px;padding:60px 52px;width:460px;text-align:center;box-shadow:0 30px 80px rgba(0,0,0,0.5);animation:fadeUp .5s ease both}
  @keyframes fadeUp{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:translateY(0)}}
  .err-ring{width:90px;height:90px;border-radius:50%;background:rgba(229,57,53,0.1);border:2px solid rgba(229,57,53,0.3);display:flex;align-items:center;justify-content:center;margin:0 auto 24px;font-size:40px}
  h1{font-family:'Playfair Display',serif;color:#fff;font-size:26px;margin-bottom:12px}
  .sub{font-size:14px;color:rgba(255,255,255,0.45);line-height:1.7;margin-bottom:32px}
  .btn{display:block;width:100%;padding:14px;background:linear-gradient(135deg,#2e7d32,#43a047);border:none;border-radius:10px;color:#fff;font-size:15px;font-weight:600;font-family:'DM Sans',sans-serif;cursor:pointer;text-decoration:none;transition:transform .2s}
  .btn:hover{transform:translateY(-2px)}
</style>
<div class="card">
  <div class="err-ring">✕</div>
  <h1>Lien invalide</h1>
  <p class="sub">Ce lien d'activation est invalide ou a déjà été utilisé. Vérifiez votre email ou créez un nouveau compte.</p>
  <a href="index.php?url=User/auth" class="btn">Retour à la connexion →</a>
</div>
