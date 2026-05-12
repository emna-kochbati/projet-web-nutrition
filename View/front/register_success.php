<?php
// Récupérer les données de session
$email        = $_SESSION['activation_email'] ?? '';
$emailFallback = $_SESSION['email_fallback']  ?? true;
$link         = $_SESSION['activation_link']  ?? '';

// On garde la session pour que l'utilisateur puisse actualiser
// mais on n'efface pas encore les données
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>EcoNutri — Confirmez votre email</title>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,700;9..144,900&family=Instrument+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
body{
  font-family:'Instrument Sans',sans-serif;
  background:#0a1a0d;
  min-height:100vh;
  display:flex;align-items:center;justify-content:center;
  padding:20px;
}

/* Background radial */
body::before{
  content:'';position:fixed;inset:0;
  background:
    radial-gradient(ellipse 50% 60% at 20% 30%, rgba(0,185,107,0.15) 0%, transparent 65%),
    radial-gradient(ellipse 40% 50% at 80% 70%, rgba(0,150,80,0.1) 0%, transparent 65%);
  pointer-events:none;
}

.card{
  position:relative;z-index:1;
  background:#0f1e12;
  border:1px solid rgba(0,185,107,0.2);
  border-radius:24px;
  padding:52px 56px;
  width:100%;max-width:520px;
  text-align:center;
  box-shadow:0 32px 80px rgba(0,0,0,0.5);
  animation:cardIn .5s ease both;
}

@keyframes cardIn{
  from{opacity:0;transform:translateY(20px) scale(.97);}
  to{opacity:1;transform:translateY(0) scale(1);}
}

/* Icon */
.icon-wrap{
  width:80px;height:80px;border-radius:50%;
  background:linear-gradient(135deg,rgba(0,185,107,0.15),rgba(0,185,107,0.25));
  border:1px solid rgba(0,185,107,0.35);
  display:flex;align-items:center;justify-content:center;
  font-size:34px;margin:0 auto 24px;
  position:relative;
}

.icon-wrap::before{
  content:'';
  position:absolute;inset:-6px;border-radius:50%;
  border:1px solid rgba(0,185,107,0.15);
  animation:ripple 2.5s ease-in-out infinite;
}

@keyframes ripple{
  0%,100%{transform:scale(1);opacity:.5;}
  50%{transform:scale(1.06);opacity:1;}
}

.logo{
  display:flex;align-items:center;gap:10px;
  justify-content:center;margin-bottom:32px;
}

.logo-icon{
  width:34px;height:34px;border-radius:10px;
  background:linear-gradient(135deg,#00b96b,#00e676);
  display:flex;align-items:center;justify-content:center;font-size:16px;
}

.logo-name{
  font-family:'Fraunces',serif;font-size:19px;font-weight:700;color:#fff;
}
.logo-name em{color:#00b96b;font-style:normal;}

h1{
  font-family:'Fraunces',serif;
  font-size:26px;font-weight:900;
  color:#fff;letter-spacing:-.5px;margin-bottom:12px;
}

.sub{
  font-size:14px;color:rgba(255,255,255,0.5);
  line-height:1.75;margin-bottom:28px;
}

.email-badge{
  display:inline-flex;align-items:center;gap:8px;
  padding:10px 20px;border-radius:30px;
  background:rgba(0,185,107,0.12);
  border:1px solid rgba(0,185,107,0.25);
  color:#69f0ae;font-size:14px;font-weight:600;
  margin-bottom:28px;
}

/* ── Fallback : afficher le lien direct ── */
.fallback-box{
  background:rgba(255,193,7,0.08);
  border:1px dashed rgba(255,193,7,0.3);
  border-radius:14px;padding:18px 20px;
  margin-bottom:22px;text-align:left;
}

.fallback-title{
  font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;
  color:#ffc107;margin-bottom:8px;display:flex;align-items:center;gap:6px;
}

.fallback-note{
  font-size:12px;color:rgba(255,193,7,0.7);margin-bottom:12px;line-height:1.55;
}

.fallback-link{
  display:block;padding:10px 14px;
  background:rgba(0,185,107,0.1);border:1px solid rgba(0,185,107,0.25);
  border-radius:9px;font-size:11px;color:#69f0ae;
  word-break:break-all;text-decoration:none;
  transition:background .2s;
}

.fallback-link:hover{background:rgba(0,185,107,0.18);}

/* Steps */
.steps-list{
  display:flex;flex-direction:column;gap:10px;
  margin-bottom:28px;text-align:left;
}

.step-row{
  display:flex;align-items:flex-start;gap:12px;
  padding:12px 14px;
  background:rgba(255,255,255,0.03);
  border:1px solid rgba(255,255,255,0.06);
  border-radius:11px;
}

.step-num{
  width:24px;height:24px;border-radius:50%;flex-shrink:0;
  background:rgba(0,185,107,0.15);border:1px solid rgba(0,185,107,0.3);
  display:flex;align-items:center;justify-content:center;
  font-size:11px;font-weight:700;color:#00b96b;
}

.step-text{font-size:13px;color:rgba(255,255,255,0.55);line-height:1.5;padding-top:2px;}
.step-text strong{color:rgba(255,255,255,0.85);}

/* Buttons */
.btn-main{
  display:block;width:100%;padding:14px;
  background:linear-gradient(135deg,#00b96b,#00e676);
  color:#000;font-size:14px;font-weight:700;
  font-family:'Instrument Sans',sans-serif;
  border:none;border-radius:12px;cursor:pointer;
  text-decoration:none;
  transition:all .22s;
  box-shadow:0 6px 20px rgba(0,185,107,0.3);
  margin-bottom:10px;
}

.btn-main:hover{transform:translateY(-2px);box-shadow:0 10px 28px rgba(0,185,107,0.4);}

.btn-ghost{
  display:block;width:100%;padding:13px;
  background:transparent;
  color:rgba(255,255,255,0.4);font-size:13px;font-weight:500;
  font-family:'Instrument Sans',sans-serif;
  border:1px solid rgba(255,255,255,0.1);border-radius:12px;
  cursor:pointer;text-decoration:none;
  transition:all .2s;
}

.btn-ghost:hover{border-color:rgba(255,255,255,0.25);color:rgba(255,255,255,0.7);}

.divider{
  font-size:12px;color:rgba(255,255,255,0.2);
  margin:16px 0;display:flex;align-items:center;gap:12px;
}
.divider::before,.divider::after{content:'';flex:1;height:1px;background:rgba(255,255,255,0.06);}

.resend-note{font-size:12px;color:rgba(255,255,255,0.3);margin-top:12px;text-align:center;}
.resend-note a{color:#00b96b;text-decoration:none;}
.resend-note a:hover{text-decoration:underline;}
</style>
</head>
<body>
<div class="card">

  <div class="logo">
    <div class="logo-icon">🌿</div>
    <div class="logo-name">Eco<em>Nutri</em></div>
  </div>

  <?php if (!$emailFallback): ?>
    <!-- ══ EMAIL RÉEL ENVOYÉ ══ -->
    <div class="icon-wrap">📧</div>
    <h1>Vérifiez votre boîte mail</h1>
    <p class="sub">Un email d'activation a été envoyé à :</p>

    <div class="email-badge">
      <i class="fa fa-envelope" style="font-size:13px;color:#00b96b;"></i>
      <?= htmlspecialchars($email) ?>
    </div>

    <div class="steps-list">
      <div class="step-row">
        <div class="step-num">1</div>
        <div class="step-text">Ouvrez votre boîte <strong><?= htmlspecialchars($email) ?></strong></div>
      </div>
      <div class="step-row">
        <div class="step-num">2</div>
        <div class="step-text">Recherchez un email de <strong>EcoNutri</strong> (vérifiez vos spams si nécessaire)</div>
      </div>
      <div class="step-row">
        <div class="step-num">3</div>
        <div class="step-text">Cliquez sur le bouton <strong>"Activer mon compte"</strong> dans l'email</div>
      </div>
      <div class="step-row">
        <div class="step-num">4</div>
        <div class="step-text">Revenez ici pour vous <strong>connecter</strong> 🎉</div>
      </div>
    </div>

    <a href="index.php?url=User/auth" class="btn-main">
      J'ai activé mon compte — Se connecter →
    </a>

    <div class="divider">ou</div>

    <a href="index.php?url=User/register" class="btn-ghost">
      Réessayer avec un autre email
    </a>

    <div class="resend-note">
      Vous n'avez pas reçu l'email ? Vérifiez vos <strong>spams</strong><br>
      ou <a href="index.php?url=User/auth">contactez le support</a>
    </div>

  <?php else: ?>
    <!-- ══ FALLBACK : lien direct (dev mode ou erreur SMTP) ══ -->
    <div class="icon-wrap">⚠️</div>
    <h1>Activez votre compte</h1>
    <p class="sub">
      L'envoi de l'email a échoué (configuration SMTP incomplète).<br>
      Cliquez sur le lien ci-dessous pour activer votre compte directement.
    </p>

    <div class="fallback-box">
      <div class="fallback-title">
        <i class="fa fa-triangle-exclamation" style="font-size:11px;"></i>
        Mode développement — Lien direct
      </div>
      <div class="fallback-note">
        En production, ce lien sera envoyé par email. Configurez <code>Config/mailer.php</code> avec vos identifiants Gmail.
      </div>
      <?php if ($link): ?>
      <a href="<?= htmlspecialchars($link) ?>" class="fallback-link">
        <i class="fa fa-link" style="font-size:10px;margin-right:4px;"></i>
        <?= htmlspecialchars($link) ?>
      </a>
      <?php endif; ?>
    </div>

    <?php if ($link): ?>
    <a href="<?= htmlspecialchars($link) ?>" class="btn-main">
      Activer mon compte maintenant →
    </a>
    <?php endif; ?>

    <div class="divider">ou</div>
    <a href="index.php?url=User/auth" class="btn-ghost">Retour à la connexion</a>

  <?php endif; ?>

</div>
</body>
</html>
