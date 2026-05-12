<?php include __DIR__ . '/../partials/header.php'; ?>

<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">

<?php
  // Déterminer l'étape courante
  $step = 'phone'; // étape 1 par défaut
  if (!empty($_SESSION['reset_step'])) {
    $step = $_SESSION['reset_step']; // 'verify' ou 'newpwd'
  }
?>

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
    background: radial-gradient(ellipse 50% 60% at 30% 40%, rgba(46,125,50,0.15) 0%, transparent 70%);
    pointer-events: none;
  }

  .reset-card {
    background: #111f14;
    border: 1px solid rgba(76,175,80,0.15);
    border-radius: 24px;
    padding: 50px 52px;
    width: 480px;
    position: relative;
    z-index: 1;
    box-shadow: 0 30px 80px rgba(0,0,0,0.5);
  }

  .back-link {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
    color: rgba(255,255,255,0.35);
    text-decoration: none;
    margin-bottom: 32px;
    transition: color .2s;
  }
  .back-link:hover { color: #66bb6a; }

  /* STEPS INDICATOR */
  .steps {
    display: flex;
    align-items: center;
    gap: 0;
    margin-bottom: 36px;
  }

  .step-item {
    display: flex;
    align-items: center;
    gap: 8px;
    flex: 1;
  }

  .step-dot {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    font-weight: 600;
    flex-shrink: 0;
    transition: all .3s;
  }

  .step-dot.done    { background: #2e7d32; color: #fff; }
  .step-dot.active  { background: #4caf50; color: #fff; box-shadow: 0 0 0 4px rgba(76,175,80,0.2); }
  .step-dot.pending { background: rgba(255,255,255,0.06); color: rgba(255,255,255,0.3); border: 1px solid rgba(255,255,255,0.1); }

  .step-label {
    font-size: 11px;
    color: rgba(255,255,255,0.35);
    white-space: nowrap;
  }
  .step-label.active { color: #81c784; }

  .step-line {
    flex: 1;
    height: 1px;
    background: rgba(255,255,255,0.1);
    margin: 0 8px;
    max-width: 40px;
  }
  .step-line.done { background: #2e7d32; }

  /* CONTENT */
  .card-icon {
    font-size: 40px;
    margin-bottom: 14px;
    display: block;
  }

  h1 {
    font-family: 'Playfair Display', serif;
    color: #fff;
    font-size: 26px;
    margin-bottom: 8px;
  }

  .sub {
    font-size: 14px;
    color: rgba(255,255,255,0.4);
    line-height: 1.6;
    margin-bottom: 28px;
  }

  .alert-box {
    padding: 11px 14px;
    border-radius: 10px;
    font-size: 13px;
    margin-bottom: 18px;
    display: flex;
    align-items: center;
    gap: 8px;
  }
  .alert-box.error { background: rgba(229,57,53,0.15); color: #ef9a9a; border: 1px solid rgba(229,57,53,0.25); }
  .alert-box.info  { background: rgba(46,125,50,0.15);  color: #a5d6a7; border: 1px solid rgba(46,125,50,0.25); }

  /* SMS CODE simulation badge */
  .sms-sim {
    background: rgba(76,175,80,0.12);
    border: 1px dashed rgba(76,175,80,0.4);
    border-radius: 10px;
    padding: 12px 16px;
    margin-bottom: 18px;
    font-size: 13px;
    color: #a5d6a7;
    display: flex;
    align-items: center;
    justify-content: space-between;
  }
  .sms-sim strong {
    font-size: 20px;
    letter-spacing: 4px;
    color: #81c784;
  }

  .phone-row {
    display: flex;
    gap: 10px;
    margin-bottom: 14px;
  }

  .prefix-select {
    width: 90px;
    flex-shrink: 0;
    padding: 12px 10px;
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 10px;
    color: rgba(255,255,255,0.7);
    font-size: 14px;
    font-family: 'DM Sans', sans-serif;
    outline: none;
    appearance: none;
    text-align: center;
  }

  .form-group {
    position: relative;
    margin-bottom: 14px;
  }

  .form-group input {
    width: 100%;
    padding: 13px 16px;
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 10px;
    color: #fff;
    font-size: 15px;
    font-family: 'DM Sans', sans-serif;
    outline: none;
    transition: border-color .25s, background .25s;
  }

  .form-group input::placeholder { color: rgba(255,255,255,0.25); }
  .form-group input:focus { border-color: #4caf50; background: rgba(76,175,80,0.07); }

  /* OTP inputs */
  .otp-row {
    display: flex;
    gap: 10px;
    justify-content: space-between;
    margin-bottom: 20px;
  }

  .otp-input {
    width: 52px !important;
    height: 58px;
    text-align: center;
    font-size: 22px;
    font-weight: 600;
    padding: 0 !important;
    border-radius: 12px !important;
    letter-spacing: 0;
  }

  /* Strength bar */
  .pwd-strength {
    height: 4px;
    background: rgba(255,255,255,0.08);
    border-radius: 2px;
    overflow: hidden;
    margin: -8px 0 14px;
  }
  .pwd-bar {
    height: 100%;
    width: 0;
    border-radius: 2px;
    transition: width .3s, background .3s;
  }

  .eye-btn {
    position: absolute;
    right: 14px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: rgba(255,255,255,0.3);
    cursor: pointer;
    font-size: 16px;
    width: auto;
    padding: 0;
    margin: 0;
  }

  .btn-main {
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
    transition: transform .2s, box-shadow .2s;
    letter-spacing: 0.2px;
  }
  .btn-main:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(46,125,50,0.4);
  }

  .btn-ghost {
    width: 100%;
    padding: 12px;
    background: transparent;
    border: 1px solid rgba(76,175,80,0.3);
    border-radius: 10px;
    color: #66bb6a;
    font-size: 14px;
    font-family: 'DM Sans', sans-serif;
    cursor: pointer;
    margin-top: 10px;
    transition: background .2s;
  }
  .btn-ghost:hover { background: rgba(76,175,80,0.08); }
</style>

<div class="reset-card">

  <a href="index.php?url=User/auth" class="back-link">← Retour à la connexion</a>

  <!-- ÉTAPES -->
  <div class="steps">
    <div class="step-item">
      <div class="step-dot <?= $step === 'phone' ? 'active' : 'done' ?>">
        <?= $step === 'phone' ? '1' : '✓' ?>
      </div>
      <span class="step-label <?= $step === 'phone' ? 'active' : '' ?>">Téléphone</span>
    </div>
    <div class="step-line <?= in_array($step, ['verify','newpwd']) ? 'done' : '' ?>"></div>
    <div class="step-item">
      <div class="step-dot <?= $step === 'verify' ? 'active' : ($step === 'newpwd' ? 'done' : 'pending') ?>">
        <?= $step === 'newpwd' ? '✓' : '2' ?>
      </div>
      <span class="step-label <?= $step === 'verify' ? 'active' : '' ?>">Code SMS</span>
    </div>
    <div class="step-line <?= $step === 'newpwd' ? 'done' : '' ?>"></div>
    <div class="step-item">
      <div class="step-dot <?= $step === 'newpwd' ? 'active' : 'pending' ?>">3</div>
      <span class="step-label <?= $step === 'newpwd' ? 'active' : '' ?>">Nouveau mot de passe</span>
    </div>
  </div>

  <!-- ALERT -->
  <?php if (!empty($_SESSION['reset_error'])): ?>
    <div class="alert-box error">⚠ <?= htmlspecialchars($_SESSION['reset_error']) ?></div>
    <?php unset($_SESSION['reset_error']); ?>
  <?php endif; ?>

  <!-- ========== ÉTAPE 1 : TÉLÉPHONE ========== -->
  <?php if ($step === 'phone'): ?>

    <span class="card-icon">📱</span>
    <h1>Mot de passe oublié ?</h1>
    <p class="sub">Entrez votre numéro de téléphone. Nous vous enverrons un code de vérification à 6 chiffres.</p>

    <form method="POST" action="index.php?url=User/requestReset">
      <div class="phone-row">
        <select class="prefix-select" name="prefix">
          <option>+216</option>
          <option>+33</option>
          <option>+212</option>
          <option>+213</option>
        </select>
        <div class="form-group" style="flex:1;margin-bottom:0;">
          <input type="tel" name="phone" placeholder="XX XXX XXX" required maxlength="12">
        </div>
      </div>
      <button type="submit" class="btn-main">Envoyer le code →</button>
    </form>

  <!-- ========== ÉTAPE 2 : CODE SMS ========== -->
  <?php elseif ($step === 'verify'): ?>

    <span class="card-icon">🔢</span>
    <h1>Vérification SMS</h1>
    <p class="sub">Code envoyé au <strong style="color:#81c784"><?= htmlspecialchars($_SESSION['reset_phone'] ?? '') ?></strong>. Il expire dans <strong style="color:#81c784">10 minutes</strong>.</p>

    <?php if (!empty($_SESSION['reset_sms_sim'])): ?>
      <div class="sms-sim">
        <span>📲 Simulation SMS</span>
        <strong><?= $_SESSION['reset_sms_sim'] ?></strong>
      </div>
    <?php endif; ?>

    <form method="POST" action="index.php?url=User/verifyReset" id="otpForm">
      <div class="otp-row">
        <?php for ($i = 0; $i < 6; $i++): ?>
          <input class="form-group input otp-input" type="text" maxlength="1"
                 id="otp<?= $i ?>" name="otp<?= $i ?>"
                 oninput="otpNext(this, <?= $i ?>)"
                 onkeydown="otpBack(this, <?= $i ?>, event)"
                 inputmode="numeric" pattern="[0-9]"
                 style="width:52px;height:58px;text-align:center;font-size:22px;font-weight:600;
                        padding:0;border-radius:12px;background:rgba(255,255,255,0.05);
                        border:1px solid rgba(255,255,255,0.1);color:#fff;font-family:'DM Sans',sans-serif;outline:none;">
        <?php endfor; ?>
      </div>
      <input type="hidden" name="code" id="hiddenCode">
      <button type="submit" class="btn-main" onclick="combineOTP()">Vérifier le code →</button>
    </form>

    <form method="POST" action="index.php?url=User/requestReset" style="margin-top:0;">
      <input type="hidden" name="phone" value="<?= htmlspecialchars($_SESSION['reset_phone'] ?? '') ?>">
      <button type="submit" class="btn-ghost">Renvoyer le code</button>
    </form>

  <!-- ========== ÉTAPE 3 : NOUVEAU MOT DE PASSE ========== -->
  <?php elseif ($step === 'newpwd'): ?>

    <span class="card-icon">🔐</span>
    <h1>Nouveau mot de passe</h1>
    <p class="sub">Choisissez un mot de passe fort d'au moins 8 caractères.</p>

    <form method="POST" action="index.php?url=User/changePassword">
      <div class="form-group">
        <input type="password" name="password" id="pwd" placeholder="Nouveau mot de passe"
               required minlength="8" oninput="checkStrength(this.value)">
        <button type="button" class="eye-btn" onclick="toggleEye('pwd', this)">👁</button>
      </div>
      <div class="pwd-strength"><div class="pwd-bar" id="pwdBar"></div></div>
      <div class="form-group">
        <input type="password" name="confirm" id="cpwd" placeholder="Confirmer le mot de passe" required>
        <button type="button" class="eye-btn" onclick="toggleEye('cpwd', this)">👁</button>
      </div>
      <button type="submit" class="btn-main">Enregistrer →</button>
    </form>

  <?php endif; ?>

</div>

<script>
  function otpNext(el, idx) {
    el.value = el.value.replace(/\D/, '');
    if (el.value && idx < 5) {
      document.getElementById('otp' + (idx + 1)).focus();
    }
    // Highlight filled
    el.style.borderColor = el.value ? '#4caf50' : 'rgba(255,255,255,0.1)';
    el.style.background  = el.value ? 'rgba(76,175,80,0.1)' : 'rgba(255,255,255,0.05)';
  }

  function otpBack(el, idx, e) {
    if (e.key === 'Backspace' && !el.value && idx > 0) {
      document.getElementById('otp' + (idx - 1)).focus();
    }
  }

  function combineOTP() {
    let code = '';
    for (let i = 0; i < 6; i++) {
      code += document.getElementById('otp' + i).value;
    }
    document.getElementById('hiddenCode').value = code;
  }

  function checkStrength(v) {
    const bar = document.getElementById('pwdBar');
    let score = Math.min(100, v.length * 8
      + (/[A-Z]/.test(v) ? 15 : 0)
      + (/[0-9]/.test(v) ? 15 : 0)
      + (/[^A-Za-z0-9]/.test(v) ? 20 : 0));
    bar.style.width = score + '%';
    bar.style.background = score < 40 ? '#e53935' : score < 70 ? '#fb8c00' : '#43a047';
  }

  function toggleEye(id, btn) {
    const inp = document.getElementById(id);
    inp.type = inp.type === 'password' ? 'text' : 'password';
    btn.textContent = inp.type === 'text' ? '🙈' : '👁';
  }
</script>

<?php include __DIR__ . '/../partials/footer.php'; ?>
