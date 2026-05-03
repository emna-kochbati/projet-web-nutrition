<?php
/**
 * ══════════════════════════════════════════════
 *  EcoNutri — SMS via Twilio
 * ══════════════════════════════════════════════
 *
 *  INSTALLATION :
 *  composer require twilio/sdk
 *
 *  CONFIGURATION TWILIO :
 *  1. Créez un compte sur https://www.twilio.com (essai gratuit)
 *  2. Allez dans Console → Account Info
 *  3. Copiez Account SID et Auth Token
 *  4. Achetez un numéro Twilio (gratuit en trial)
 *     → Console → Phone Numbers → Buy a Number
 *  5. Renseignez les 3 valeurs ci-dessous
 *
 *  ⚠️  COMPTE TRIAL Twilio :
 *  En mode trial, vous ne pouvez envoyer qu'à des numéros vérifiés.
 *  Allez dans Console → Verified Caller IDs pour ajouter vos numéros de test.
 *  Pour envoyer à tous les numéros → upgradez votre compte (≈ 20$/mois)
 */

/* ─────────────────────────────────────
   ⚙️  CONFIGURATION — À PERSONNALISER
───────────────────────────────────── */
define('TWILIO_ACCOUNT_SID', 'ACxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx');  // ← Votre Account SID
define('TWILIO_AUTH_TOKEN',  'your_auth_token_here');                // ← Votre Auth Token
define('TWILIO_FROM_NUMBER', '+1xxxxxxxxxx');                        // ← Votre numéro Twilio (format E.164)

class EcoNutriSMS
{
    /**
     * Envoyer un SMS via Twilio
     *
     * @param string $to      Numéro destinataire format E.164 (ex: +21612345678)
     * @param string $message Contenu du SMS (max 160 caractères recommandé)
     * @return bool           true si envoyé, false sinon
     */
    public static function send(string $to, string $message): bool
    {
        // Vérifier si le SDK Twilio est installé
        if (!class_exists('Twilio\Rest\Client')) {
            error_log('[EcoNutri SMS] SDK Twilio non installé. Lancez : composer require twilio/sdk');
            return false;
        }

        // Vérifier si les identifiants sont configurés
        if (TWILIO_ACCOUNT_SID === 'ACxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx') {
            error_log('[EcoNutri SMS] Identifiants Twilio non configurés dans Config/sms.php');
            return false;
        }

        // Valider le format du numéro (doit commencer par +)
        if (!preg_match('/^\+[1-9]\d{7,14}$/', $to)) {
            error_log("[EcoNutri SMS] Numéro invalide : {$to}");
            return false;
        }

        try {
            $client = new Twilio\Rest\Client(TWILIO_ACCOUNT_SID, TWILIO_AUTH_TOKEN);

            $sms = $client->messages->create($to, [
                'from' => TWILIO_FROM_NUMBER,
                'body' => $message,
            ]);

            // Vérifier le statut d'envoi
            if (in_array($sms->status, ['queued', 'sent', 'delivered'])) {
                error_log("[EcoNutri SMS] SMS envoyé à {$to} — SID: {$sms->sid} — Status: {$sms->status}");
                return true;
            }

            error_log("[EcoNutri SMS] Statut inattendu : {$sms->status}");
            return false;

        } catch (\Twilio\Exceptions\RestException $e) {
            error_log('[EcoNutri SMS] Erreur Twilio : ' . $e->getMessage() . ' (Code: ' . $e->getCode() . ')');
            return false;
        } catch (\Exception $e) {
            error_log('[EcoNutri SMS] Erreur inattendue : ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Vérifier si Twilio est correctement configuré
     * Utile pour le panneau admin
     */
    public static function isConfigured(): bool
    {
        return (
            class_exists('Twilio\Rest\Client') &&
            TWILIO_ACCOUNT_SID !== 'ACxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx' &&
            TWILIO_AUTH_TOKEN  !== 'your_auth_token_here' &&
            TWILIO_FROM_NUMBER !== '+1xxxxxxxxxx'
        );
    }
}