<?php
/**
 * ══════════════════════════════════════
 *  EcoNutri — Mailer Gmail SMTP GRATUIT
 * ══════════════════════════════════════
 *
 *  ÉTAPES (5 minutes, tout gratuit) :
 *
 *  1. Ouvre ton Gmail → Paramètres → Sécurité
 *  2. Active "Validation en 2 étapes"
 *  3. Cherche "Mots de passe d'application"
 *     → https://myaccount.google.com/apppasswords
 *  4. Crée un mot de passe → Nom : "EcoNutri"
 *  5. Gmail te donne un code de 16 lettres
 *     → Colle-le dans MAIL_PASSWORD ci-dessous
 *  6. Mets ton adresse Gmail dans MAIL_USERNAME
 */

// ─────────────────────────────────────
//  ⚙️  MET TON GMAIL ICI
// ─────────────────────────────────────
define('MAIL_USERNAME', 'TON_EMAIL@gmail.com');   // ← ton adresse Gmail
define('MAIL_PASSWORD', 'xxxx xxxx xxxx xxxx');   // ← mot de passe d'application (16 lettres)
define('MAIL_FROM_NAME', 'EcoNutri');

// ─────────────────────────────────────
//  NE PAS TOUCHER (config Gmail SMTP)
// ─────────────────────────────────────
define('MAIL_HOST', 'smtp.gmail.com');
define('MAIL_PORT', 587);

class EcoNutriMailer
{
    /**
     * Envoyer l'email d'activation de compte
     */
    public static function sendActivation(string $toEmail, string $toName, string $link): bool
    {
        $prenom = explode(' ', $toName)[0];
        $subject = 'Activez votre compte EcoNutri 🌿';
        $body = self::templateActivation($prenom, $toName, $link);
        return self::send($toEmail, $toName, $subject, $body);
    }

    /**
     * Envoyer le code de réinitialisation par EMAIL
     * (remplace le SMS — 100% gratuit)
     */
    public static function sendResetCode(string $toEmail, string $toName, string $code): bool
    {
        $prenom = explode(' ', $toName)[0];
        $subject = "Votre code de réinitialisation EcoNutri : {$code}";
        $body = self::templateReset($prenom, $code);
        return self::send($toEmail, $toName, $subject, $body);
    }

    /**
     * Envoi générique via Gmail SMTP (sans dépendance Composer)
     * Utilise les sockets PHP natifs — zéro librairie requise !
     */
    private static function send(string $toEmail, string $toName, string $subject, string $htmlBody): bool
    {
        // Vérifier configuration
        if (MAIL_USERNAME === 'TON_EMAIL@gmail.com') {
            error_log('[EcoNutri] Configure ton Gmail dans Config/mailer.php');
            return false;
        }

        // Essayer PHPMailer si disponible (composer require phpmailer/phpmailer)
        $autoload = __DIR__ . '/../../vendor/autoload.php';
        if (file_exists($autoload)) {
            require_once $autoload;
            if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
                return self::sendViaPHPMailer($toEmail, $toName, $subject, $htmlBody);
            }
        }

        // Fallback : mail() natif PHP (si hébergement local avec sendmail)
        $headers  = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=UTF-8\r\n";
        $headers .= "From: " . MAIL_FROM_NAME . " <" . MAIL_USERNAME . ">\r\n";
        $headers .= "Reply-To: " . MAIL_USERNAME . "\r\n";

        $sent = @mail($toEmail, $subject, $htmlBody, $headers);
        if (!$sent) {
            error_log('[EcoNutri] mail() a échoué. Installe PHPMailer : composer require phpmailer/phpmailer');
        }
        return $sent;
    }

    /**
     * Envoi via PHPMailer (recommandé)
     */
    private static function sendViaPHPMailer(string $toEmail, string $toName, string $subject, string $body): bool
    {
        try {
            $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
            $mail->isSMTP();
            $mail->Host       = MAIL_HOST;
            $mail->SMTPAuth   = true;
            $mail->Username   = MAIL_USERNAME;
            $mail->Password   = MAIL_PASSWORD;
            $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = MAIL_PORT;
            $mail->CharSet    = 'UTF-8';
            $mail->setFrom(MAIL_USERNAME, MAIL_FROM_NAME);
            $mail->addAddress($toEmail, $toName);
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $body;
            $mail->AltBody = strip_tags(str_replace(['<br>','<br/>','</p>'], "\n", $body));
            $mail->send();
            return true;
        } catch (\Exception $e) {
            error_log('[EcoNutri] PHPMailer erreur : ' . $e->getMessage());
            return false;
        }
    }

    // ══════════════════════════════════════════════
    //  TEMPLATES EMAIL
    // ══════════════════════════════════════════════

    private static function templateActivation(string $prenom, string $nom, string $link): string
    {
        return <<<HTML
<!DOCTYPE html><html lang="fr"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"></head>
<body style="margin:0;padding:0;background:#f0f6f3;font-family:'Segoe UI',Arial,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f0f6f3;padding:40px 20px;">
<tr><td align="center">
<table width="560" cellpadding="0" cellspacing="0" style="background:#fff;border-radius:20px;overflow:hidden;box-shadow:0 8px 32px rgba(0,0,0,.08);">

  <!-- Header vert -->
  <tr><td style="background:linear-gradient(135deg,#007a47,#00c853);padding:40px;text-align:center;">
    <div style="font-size:36px;margin-bottom:10px;">🌿</div>
    <h1 style="color:#fff;font-size:24px;font-weight:800;margin:0;">EcoNutri</h1>
    <p style="color:rgba(255,255,255,.7);font-size:13px;margin:6px 0 0;">Alimentation durable &amp; nutrition intelligente</p>
  </td></tr>

  <!-- Body -->
  <tr><td style="padding:44px 48px;">
    <h2 style="color:#0d1f0f;font-size:22px;font-weight:700;margin:0 0 12px;">Bonjour {$prenom} 👋</h2>
    <p style="color:#4a6352;font-size:15px;line-height:1.7;margin:0 0 28px;">
      Bienvenue sur <strong>EcoNutri</strong> ! Ton compte a bien été créé.<br>
      Clique sur le bouton ci-dessous pour l'activer et commencer ton parcours nutrition.
    </p>
    <table cellpadding="0" cellspacing="0" style="margin:0 0 32px;">
      <tr><td style="background:linear-gradient(135deg,#00b96b,#00e676);border-radius:12px;box-shadow:0 6px 20px rgba(0,185,107,.35);">
        <a href="{$link}" style="display:block;padding:16px 40px;color:#000;font-size:15px;font-weight:700;text-decoration:none;">
          ✅ Activer mon compte →
        </a>
      </td></tr>
    </table>
    <p style="color:#8aa898;font-size:13px;margin:0 0 8px;">Si le bouton ne marche pas, copie ce lien :</p>
    <a href="{$link}" style="color:#00b96b;font-size:12px;word-break:break-all;">{$link}</a>
    <hr style="border:none;border-top:1px solid #e2ede8;margin:28px 0;">
    <p style="color:#8aa898;font-size:12px;margin:0;">⚠️ Ce lien expire dans <strong>24 heures</strong>. Si tu n'as pas créé de compte, ignore cet email.</p>
  </td></tr>

  <!-- Footer -->
  <tr><td style="background:#f7faf8;padding:20px 48px;text-align:center;border-top:1px solid #e2ede8;">
    <p style="color:#8aa898;font-size:12px;margin:0;">© 2025 EcoNutri — Tous droits réservés</p>
  </td></tr>

</table></td></tr></table>
</body></html>
HTML;
    }

    private static function templateReset(string $prenom, string $code): string
    {
        // Séparer le code en 2 groupes de 3 pour lisibilité : 123 456
        $codeDisplay = substr($code, 0, 3) . ' ' . substr($code, 3, 3);

        return <<<HTML
<!DOCTYPE html><html lang="fr"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"></head>
<body style="margin:0;padding:0;background:#f0f6f3;font-family:'Segoe UI',Arial,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f0f6f3;padding:40px 20px;">
<tr><td align="center">
<table width="520" cellpadding="0" cellspacing="0" style="background:#fff;border-radius:20px;overflow:hidden;box-shadow:0 8px 32px rgba(0,0,0,.08);">

  <!-- Header -->
  <tr><td style="background:linear-gradient(135deg,#007a47,#00c853);padding:36px;text-align:center;">
    <div style="font-size:34px;margin-bottom:8px;">🔐</div>
    <h1 style="color:#fff;font-size:22px;font-weight:800;margin:0;">Code de vérification</h1>
    <p style="color:rgba(255,255,255,.7);font-size:13px;margin:5px 0 0;">EcoNutri</p>
  </td></tr>

  <!-- Body -->
  <tr><td style="padding:44px 48px;text-align:center;">
    <h2 style="color:#0d1f0f;font-size:20px;font-weight:700;margin:0 0 10px;">Bonjour {$prenom} 👋</h2>
    <p style="color:#4a6352;font-size:14px;line-height:1.7;margin:0 0 28px;">
      Tu as demandé à réinitialiser ton mot de passe EcoNutri.<br>
      Voici ton code de vérification :
    </p>

    <!-- Code principal -->
    <div style="background:linear-gradient(135deg,#e6faf2,#f0fdf7);border:2px solid #c3e8d6;border-radius:16px;padding:28px;margin:0 0 28px;display:inline-block;width:100%;">
      <div style="font-size:48px;font-weight:900;letter-spacing:8px;color:#007a47;font-family:'Courier New',monospace;line-height:1;">
        {$codeDisplay}
      </div>
      <div style="font-size:13px;color:#8aa898;margin-top:10px;">Ce code est valable <strong>10 minutes</strong></div>
    </div>

    <p style="color:#4a6352;font-size:14px;line-height:1.7;margin:0 0 20px;">
      Retourne sur EcoNutri et entre ce code dans les cases prévues.
    </p>

    <hr style="border:none;border-top:1px solid #e2ede8;margin:24px 0;">

    <div style="background:#fff8f5;border:1px solid #ffd5bf;border-radius:10px;padding:14px 18px;text-align:left;">
      <p style="color:#c84a15;font-size:12px;margin:0;line-height:1.6;">
        🚫 <strong>Important :</strong> EcoNutri ne te demandera jamais ce code par téléphone ou par message.<br>
        Si tu n'as pas fait cette demande, ignore cet email — ton compte est en sécurité.
      </p>
    </div>
  </td></tr>

  <!-- Footer -->
  <tr><td style="background:#f7faf8;padding:18px 48px;text-align:center;border-top:1px solid #e2ede8;">
    <p style="color:#8aa898;font-size:12px;margin:0;">© 2025 EcoNutri — Tous droits réservés</p>
  </td></tr>

</table></td></tr></table>
</body></html>
HTML;
    }
}