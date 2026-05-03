<?php
/**
 * ══════════════════════════════════════════════
 *  EcoNutri — Mailer (PHPMailer + Gmail SMTP)
 * ══════════════════════════════════════════════
 *
 *  INSTALLATION :
 *  composer require phpmailer/phpmailer
 *
 *  CONFIGURATION GMAIL :
 *  1. Activez la validation en 2 étapes sur votre compte Google
 *  2. Allez sur : https://myaccount.google.com/apppasswords
 *  3. Créez un mot de passe d'application "EcoNutri"
 *  4. Remplacez GMAIL_APP_PASSWORD ci-dessous par le mot de passe généré
 *  5. Remplacez VOTRE_EMAIL@gmail.com par votre vrai email Gmail
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Autoload Composer
$composerAutoload = __DIR__ . '/../../vendor/autoload.php';
if (file_exists($composerAutoload)) {
    require_once $composerAutoload;
}

/* ─────────────────────────────────────
   ⚙️  CONFIGURATION — À PERSONNALISER
───────────────────────────────────── */
define('MAIL_HOST',     'smtp.gmail.com');
define('MAIL_PORT',     587);
define('MAIL_USERNAME', 'VOTRE_EMAIL@gmail.com');      // ← Votre adresse Gmail
define('MAIL_PASSWORD', 'GMAIL_APP_PASSWORD');          // ← Mot de passe d'application (16 caractères)
define('MAIL_FROM',     'VOTRE_EMAIL@gmail.com');       // ← Même adresse
define('MAIL_FROM_NAME','EcoNutri');
define('MAIL_ENCRYPTION', PHPMailer::ENCRYPTION_STARTTLS);

class EcoNutriMailer
{
    /**
     * Envoyer un email d'activation de compte
     *
     * @param string $toEmail  Email du destinataire
     * @param string $toName   Prénom / nom du destinataire
     * @param string $link     Lien d'activation complet
     * @return bool            true si envoyé, false sinon
     */
    public static function sendActivation(string $toEmail, string $toName, string $link): bool
    {
        // Vérifier si PHPMailer est disponible
        if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) {
            error_log('[EcoNutri Mailer] PHPMailer non installé. Lancez : composer require phpmailer/phpmailer');
            return false;
        }

        // Vérifier si les identifiants sont configurés
        if (MAIL_USERNAME === 'VOTRE_EMAIL@gmail.com') {
            error_log('[EcoNutri Mailer] Identifiants Gmail non configurés dans Config/mailer.php');
            return false;
        }

        $mail = new PHPMailer(true);

        try {
            // ── Serveur SMTP ──
            $mail->isSMTP();
            $mail->Host       = MAIL_HOST;
            $mail->SMTPAuth   = true;
            $mail->Username   = MAIL_USERNAME;
            $mail->Password   = MAIL_PASSWORD;
            $mail->SMTPSecure = MAIL_ENCRYPTION;
            $mail->Port       = MAIL_PORT;
            $mail->CharSet    = 'UTF-8';

            // ── Expéditeur / Destinataire ──
            $mail->setFrom(MAIL_FROM, MAIL_FROM_NAME);
            $mail->addAddress($toEmail, $toName);
            $mail->addReplyTo(MAIL_FROM, MAIL_FROM_NAME);

            // ── Contenu ──
            $mail->isHTML(true);
            $mail->Subject = '🌿 EcoNutri — Activez votre compte';
            $mail->Body    = self::activationTemplate($toName, $link);
            $mail->AltBody = "Bonjour {$toName},\n\nActivez votre compte EcoNutri en cliquant sur ce lien :\n{$link}\n\nCe lien est valable 24 heures.\n\nL'équipe EcoNutri";

            $mail->send();
            return true;

        } catch (Exception $e) {
            error_log('[EcoNutri Mailer] Erreur envoi activation : ' . $mail->ErrorInfo);
            return false;
        }
    }

    /**
     * Template HTML pour l'email d'activation
     */
    private static function activationTemplate(string $name, string $link): string
    {
        $firstName = explode(' ', $name)[0];
        return <<<HTML
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Activez votre compte EcoNutri</title>
</head>
<body style="margin:0;padding:0;font-family:'Segoe UI',Arial,sans-serif;background:#f0f6f3;">

<table width="100%" cellpadding="0" cellspacing="0" style="background:#f0f6f3;padding:40px 20px;">
  <tr>
    <td align="center">
      <table width="560" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:20px;overflow:hidden;box-shadow:0 8px 32px rgba(0,0,0,0.08);">

        <!-- Header -->
        <tr>
          <td style="background:linear-gradient(135deg,#007a47,#00c853);padding:40px;text-align:center;">
            <div style="width:50px;height:50px;background:rgba(255,255,255,0.2);border-radius:14px;display:inline-flex;align-items:center;justify-content:center;font-size:24px;margin-bottom:14px;">🌿</div>
            <h1 style="color:#ffffff;font-size:26px;font-weight:800;margin:0;letter-spacing:-0.5px;">EcoNutri</h1>
            <p style="color:rgba(255,255,255,0.75);font-size:13px;margin:6px 0 0;">Alimentation durable & nutrition intelligente</p>
          </td>
        </tr>

        <!-- Body -->
        <tr>
          <td style="padding:44px 48px;">
            <h2 style="color:#0d1f0f;font-size:22px;font-weight:700;margin:0 0 12px;letter-spacing:-0.3px;">
              Bonjour {$firstName} 👋
            </h2>
            <p style="color:#4a6352;font-size:15px;line-height:1.7;margin:0 0 28px;">
              Bienvenue sur <strong>EcoNutri</strong> ! Votre compte a bien été créé.<br>
              Il vous suffit de cliquer sur le bouton ci-dessous pour l'activer et commencer votre parcours nutrition.
            </p>

            <!-- CTA Button -->
            <table cellpadding="0" cellspacing="0" style="margin:0 0 32px;">
              <tr>
                <td style="background:linear-gradient(135deg,#00b96b,#00e676);border-radius:12px;box-shadow:0 6px 20px rgba(0,185,107,0.35);">
                  <a href="{$link}" style="display:block;padding:16px 40px;color:#000000;font-size:15px;font-weight:700;text-decoration:none;letter-spacing:0.2px;">
                    ✅ Activer mon compte →
                  </a>
                </td>
              </tr>
            </table>

            <p style="color:#8aa898;font-size:13px;line-height:1.6;margin:0 0 8px;">
              Si le bouton ne fonctionne pas, copiez ce lien dans votre navigateur :
            </p>
            <p style="margin:0;">
              <a href="{$link}" style="color:#00b96b;font-size:12px;word-break:break-all;text-decoration:none;">{$link}</a>
            </p>

            <!-- Divider -->
            <hr style="border:none;border-top:1px solid #e2ede8;margin:32px 0;">

            <!-- Features -->
            <p style="color:#0d1f0f;font-size:14px;font-weight:600;margin:0 0 16px;">Ce qui vous attend sur EcoNutri :</p>
            <table width="100%" cellpadding="0" cellspacing="0">
              <tr>
                <td style="padding:8px 0;">
                  <span style="font-size:18px;margin-right:10px;">🥗</span>
                  <span style="color:#4a6352;font-size:13px;">320+ recettes saines validées par nos nutritionnistes</span>
                </td>
              </tr>
              <tr>
                <td style="padding:8px 0;">
                  <span style="font-size:18px;margin-right:10px;">📊</span>
                  <span style="color:#4a6352;font-size:13px;">Suivi IMC, calories et macronutriments personnalisé</span>
                </td>
              </tr>
              <tr>
                <td style="padding:8px 0;">
                  <span style="font-size:18px;margin-right:10px;">🏋️</span>
                  <span style="color:#4a6352;font-size:13px;">Programmes sport adaptés à vos objectifs</span>
                </td>
              </tr>
            </table>
          </td>
        </tr>

        <!-- Warning -->
        <tr>
          <td style="background:#f7faf8;padding:20px 48px;border-top:1px solid #e2ede8;">
            <p style="color:#8aa898;font-size:12px;margin:0;line-height:1.6;">
              ⚠️ Ce lien est valable <strong>24 heures</strong>. Si vous n'avez pas créé de compte sur EcoNutri, vous pouvez ignorer cet email.
            </p>
          </td>
        </tr>

        <!-- Footer -->
        <tr>
          <td style="padding:24px 48px;text-align:center;">
            <p style="color:#8aa898;font-size:12px;margin:0;">
              © 2025 EcoNutri — Tous droits réservés<br>
              <a href="#" style="color:#00b96b;text-decoration:none;">Se désabonner</a>
            </p>
          </td>
        </tr>

      </table>
    </td>
  </tr>
</table>

</body>
</html>
HTML;
    }
}