<?php
class ContactController {

    public function index(): void {
        require_once 'View/front/contact.php';
    }

    public function send(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /2A35/Contact'); exit;
        }
        // Ici vous pouvez ajouter l'envoi d'email avec mail() ou un service SMTP
        // Pour l'instant on redirige avec un flag de succès
        header('Location: /2A35/Contact?sent=1'); exit;
    }
}
