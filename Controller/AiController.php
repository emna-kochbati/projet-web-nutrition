<?php
require_once 'Model/Ingredient.php';
require_once 'Model/Recette.php';

/**
 * AiController
 * ─────────────────────────────────────────────────────────────────────────────
 * - Description  : générée via l'API Google Gemini (IA externe)
 * - Suggestions  : calculées localement depuis la BDD (logique métier)
 * - Recommandation : recettes personnalisées selon profil utilisateur
 *
 * ► Clé Gemini gratuite : https://aistudio.google.com/app/apikey
 */
class AiController {

    // ── Clé API Gemini ────────────────────────────────────────────────────────
    private const GEMINI_API_KEY = 'VOTRE_CLE_GEMINI_ICI';
    private const GEMINI_URL     = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent';

    // =========================================================================
    // Point d'entrée AJAX
    // =========================================================================
    public function chat(): void {
        header('Content-Type: application/json; charset=utf-8');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['error' => 'Méthode non autorisée.']); exit;
        }

        $body    = json_decode(file_get_contents('php://input'), true);
        $context = $body['context'] ?? [];
        $type    = trim($body['type'] ?? 'tout'); // description | ingredients | tout

        $nom         = trim($context['nom']        ?? '');
        $categorie   = trim($context['categorie']  ?? '');
        $difficulte  = trim($context['difficulte'] ?? '');
        $duree       = (int)($context['duree']     ?? 0);
        $calories    = (int)($context['calories']  ?? 0);
        $ingredients = $context['ingredients']     ?? [];
        $extra       = (int)($context['_t']        ?? 0);

        if ($nom === '') {
            echo json_encode(['error' => 'Veuillez d\'abord saisir le nom de la recette.']); exit;
        }

        $description = null;
        $suggestions = [];

        // ── Description via Gemini ────────────────────────────────────────────
        if ($type === 'description' || $type === 'tout') {
            if (self::GEMINI_API_KEY !== 'VOTRE_CLE_GEMINI_ICI' && self::GEMINI_API_KEY !== '') {
                // Clé configurée → appel Gemini
                $result = $this->genererDescriptionGemini($nom, $categorie, $difficulte, $duree, $calories, $ingredients);
                $description = $result['description'];
                // Si Gemini échoue, fallback sur la logique locale
                if ($description === null) {
                    $description = $this->genererDescriptionLocale($nom, $categorie, $difficulte, $duree, $calories, $ingredients, $extra);
                }
            } else {
                // Pas de clé → logique locale
                $description = $this->genererDescriptionLocale($nom, $categorie, $difficulte, $duree, $calories, $ingredients, $extra);
            }
        }

        // ── Suggestions depuis la BDD (logique métier locale) ─────────────────
        if ($type === 'ingredients' || $type === 'tout') {
            $suggestions = $this->suggererIngredients($nom, $categorie, $ingredients, $extra);
        }

        echo json_encode([
            'description' => $description,
            'suggestions' => $suggestions,
            'source'      => (self::GEMINI_API_KEY !== 'VOTRE_CLE_GEMINI_ICI') ? 'gemini' : 'local',
        ]);
        exit;
    }

    // =========================================================================
    // ANALYSE IA — Frontoffice : analyse nutritionnelle d'une recette
    // =========================================================================
    public function analyser(): void {
        header('Content-Type: application/json; charset=utf-8');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['error' => 'Méthode non autorisée.']); exit;
        }

        $body    = json_decode(file_get_contents('php://input'), true);
        $recette = $body['recette'] ?? [];
        $profil  = $body['profil']  ?? []; // Profil utilisateur si disponible

        $nom         = trim($recette['nom']         ?? '');
        $calories    = (float)($recette['calories'] ?? 0);
        $ingredients = $recette['ingredients']      ?? [];

        if ($nom === '') {
            echo json_encode(['error' => 'Données de la recette manquantes.']); exit;
        }

        // ── 1. Récupérer les valeurs nutritionnelles depuis la BDD ────────────
        $recetteModel = new Recette();
        $nutri        = $recetteModel->calculerNutriScore((int)($recette['id'] ?? 0));
        $details      = $nutri['details'] ?? [
            'proteines' => 0, 'calcium' => 0,
            'glucides'  => 0, 'lipides' => 0, 'calories' => $calories
        ];

        // ── 2. Logique métier : calcul des profils de santé ───────────────────
        $profils = $this->calculerProfils($details);

        // ── 3. Analyse Gemini personnalisée selon le profil utilisateur ───────
        $analyse = null;
        $source  = 'local';

        if (self::GEMINI_API_KEY !== 'VOTRE_CLE_GEMINI_ICI' && self::GEMINI_API_KEY !== '') {
            $result = $this->analyserGemini($nom, $details, $ingredients, $profils, $profil);
            if ($result !== null) {
                $analyse = $result;
                $source  = 'gemini';
            }
        }

        if ($analyse === null) {
            $analyse = $this->analyseLocale($nom, $details, $profils, $profil);
        }

        echo json_encode([
            'profils' => $profils,
            'analyse' => $analyse,
            'source'  => $source,
            'profil'  => $profil, // Renvoyer le profil pour l'affichage
        ]);
        exit;
    }

    // ── Calcul des profils de santé (logique métier pure) ─────────────────────
    private function calculerProfils(array $d): array {
        $prot = (float)($d['proteines'] ?? 0);
        $gluc = (float)($d['glucides']  ?? 0);
        $lip  = (float)($d['lipides']   ?? 0);
        $kcal = (float)($d['calories']  ?? 0);

        // Diabétique : faible en glucides
        $diabetique = $gluc <= 15  ? 'adapte' : ($gluc <= 25 ? 'modere' : 'non');

        // Sportif : riche en protéines OU calories suffisantes pour l'effort
        // Minimum modéré — jamais déconseillé pour un sportif sauf si vraiment mauvais
        if ($prot >= 12)       $sportif = 'adapte';
        elseif ($prot >= 5)    $sportif = 'modere';
        elseif ($kcal >= 150)  $sportif = 'modere';
        else                   $sportif = 'modere'; // Toujours au moins modéré

        // Végétarien : faible en lipides et calories modérées (pas de viande supposée)
        $vegetarien = ($lip <= 8 && $kcal <= 400) ? 'adapte' : ($kcal <= 600 ? 'modere' : 'non');

        // Normal : toujours adapté si valeurs raisonnables
        $normal     = ($kcal <= 600) ? 'adapte' : ($kcal <= 900 ? 'modere' : 'non');

        return [
            'diabetique' => ['statut' => $diabetique],
            'sportif'    => ['statut' => $sportif],
            'vegetarien' => ['statut' => $vegetarien],
            'normal'     => ['statut' => $normal],
        ];
    }

    // ── Analyse textuelle via Gemini ──────────────────────────────────────────
    private function analyserGemini(string $nom, array $d, array $ingredients, array $profils, array $profil = []): ?string {
        $ingList = implode(', ', array_slice($ingredients, 0, 8));

        $profilsTexte = '';
        $labels = ['diabetique' => 'Diabétiques', 'sportif' => 'Sportifs', 'regime' => 'Régime', 'energie' => 'Énergie'];
        $statutLabels = ['adapte' => 'Adapté', 'modere' => 'Modéré', 'non' => 'Déconseillé'];
        foreach ($profils as $key => $val) {
            $profilsTexte .= '- ' . ($labels[$key] ?? $key) . ' : ' . ($statutLabels[$val['statut']] ?? '') . "\n";
        }

        // Ajouter le profil utilisateur si disponible
        $profilUtilisateur = '';
        if (!empty($profil['objectif']) || !empty($profil['regime']) || !empty($profil['activite'])) {
            $labelsP = [
                'objectif' => ['perte-poids' => 'perte de poids', 'prise-masse' => 'prise de masse', 'maintien' => 'maintien'],
                'regime'   => ['diabetique' => 'diabétique', 'vegetarien' => 'végétarien', 'sportif' => 'sportif', 'normal' => 'normal'],
                'activite' => ['sedentaire' => 'sédentaire', 'modere' => 'modéré', 'sportif' => 'sportif intensif'],
            ];
            $profilUtilisateur  = "\nPROFIL DE L'UTILISATEUR :\n";
            if (!empty($profil['objectif'])) $profilUtilisateur .= "- Objectif : " . ($labelsP['objectif'][$profil['objectif']] ?? $profil['objectif']) . "\n";
            if (!empty($profil['regime']))   $profilUtilisateur .= "- Régime : "   . ($labelsP['regime'][$profil['regime']]     ?? $profil['regime'])   . "\n";
            if (!empty($profil['activite'])) $profilUtilisateur .= "- Activité : " . ($labelsP['activite'][$profil['activite']] ?? $profil['activite']) . "\n";
        }

        $prompt  = "Tu es un nutritionniste expert. Analyse cette recette de manière professionnelle.\n\n";
        $prompt .= "Recette : $nom\n";
        $prompt .= "Valeurs pour 100g : protéines {$d['proteines']}g, glucides {$d['glucides']}g, lipides {$d['lipides']}g, calories {$d['calories']} kcal\n";
        if ($ingList) $prompt .= "Ingrédients : $ingList\n";
        $prompt .= "\nCompatibilité nutritionnelle :\n$profilsTexte";
        $prompt .= $profilUtilisateur;
        $prompt .= "\nDonne une analyse courte (3-4 phrases) en français :\n";
        if (!empty($profilUtilisateur)) {
            $prompt .= "1. Est-ce que cette recette est adaptée au profil de l'utilisateur ? Pourquoi ?\n";
            $prompt .= "2. Quels sont les bienfaits pour ce profil spécifique ?\n";
            $prompt .= "3. Un conseil personnalisé pour cet utilisateur.\n";
        } else {
            $prompt .= "1. Les bienfaits principaux de cette recette\n";
            $prompt .= "2. Pour qui elle est particulièrement recommandée\n";
            $prompt .= "3. Une suggestion concrète pour l'améliorer\n";
        }
        $prompt .= "Sois direct et professionnel. Pas de titre, juste le texte.";

        $payload = json_encode([
            'contents' => [['role' => 'user', 'parts' => [['text' => $prompt]]]],
            'generationConfig' => ['temperature' => 0.6, 'maxOutputTokens' => 250]
        ]);

        $ch = curl_init(self::GEMINI_URL . '?key=' . self::GEMINI_API_KEY);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $payload,
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
            CURLOPT_TIMEOUT        => 15,
            CURLOPT_SSL_VERIFYPEER => false,
        ]);
        $result   = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) return null;
        $data = json_decode($result, true);
        $text = trim($data['candidates'][0]['content']['parts'][0]['text'] ?? '');
        return $text ?: null;
    }

    // ── Analyse locale (fallback sans Gemini) ─────────────────────────────────
    private function analyseLocale(string $nom, array $d, array $profils, array $profil = []): string {
        $prot = (float)($d['proteines'] ?? 0);
        $gluc = (float)($d['glucides']  ?? 0);
        $kcal = (float)($d['calories']  ?? 0);

        $points = [];
        if ($prot >= 15) $points[] = "riche en protéines ({$prot}g/100g)";
        if ($gluc <= 15) $points[] = "faible en glucides ({$gluc}g/100g)";
        if ($kcal <= 250) $points[] = "légère en calories ({$kcal} kcal/100g)";

        $adaptes = array_keys(array_filter($profils, fn($p) => $p['statut'] === 'adapte'));
        $labels  = ['diabetique' => 'diabétiques', 'sportif' => 'sportifs', 'vegetarien' => 'végétariens', 'normal' => 'alimentation normale'];
        $adaptesTxt = implode(', ', array_map(fn($k) => $labels[$k] ?? $k, $adaptes));

        $analyse = ucfirst($nom) . ' est une recette ' . (empty($points) ? 'équilibrée' : implode(', ', $points)) . '.';

        // Personnaliser selon le profil utilisateur si disponible
        if (!empty($profil['objectif']) || !empty($profil['regime'])) {
            $labelsObj = ['perte-poids' => 'perte de poids', 'prise-masse' => 'prise de masse', 'maintien' => 'maintien'];
            $labelsReg = ['diabetique' => 'diabétique', 'vegetarien' => 'végétarien', 'sportif' => 'sportif', 'normal' => 'normal'];
            $obj = $labelsObj[$profil['objectif'] ?? ''] ?? '';
            $reg = $labelsReg[$profil['regime']   ?? ''] ?? '';

            if ($obj) $analyse .= " Pour votre objectif de $obj, cette recette " . ($kcal <= 300 ? "est bien adaptée grâce à ses calories modérées." : "peut convenir avec des portions contrôlées.");
            if ($reg === 'diabétique') $analyse .= " Pour un régime diabétique, " . ($gluc <= 20 ? "elle est recommandée car faible en glucides." : "surveillez les glucides.");
        } elseif ($adaptesTxt) {
            $analyse .= " Elle est particulièrement adaptée aux profils : $adaptesTxt.";
        }

        $analyse .= " Vous pouvez modifier les quantités d'ingrédients pour ajuster les valeurs nutritionnelles selon vos besoins.";
        return $analyse;
    }

    // =========================================================================
    // RECOMMANDATION — Recettes personnalisées selon profil utilisateur
    // =========================================================================
    public function recommander(): void {
        header('Content-Type: application/json; charset=utf-8');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['error' => 'Méthode non autorisée.']); exit;
        }

        $body    = json_decode(file_get_contents('php://input'), true);
        $profil  = $body['profil'] ?? [];

        $objectif = trim($profil['objectif'] ?? '');
        $regime   = trim($profil['regime']   ?? '');
        $activite = trim($profil['activite'] ?? '');

        if (!$objectif && !$regime && !$activite) {
            echo json_encode(['error' => 'Veuillez remplir votre profil.']); exit;
        }

        // ── 1. Récupérer toutes les recettes avec leurs valeurs nutritionnelles
        $recetteModel = new Recette();
        $toutes       = $recetteModel->getAll();

        if (empty($toutes)) {
            echo json_encode(['error' => 'Aucune recette dans la base.']); exit;
        }

        // ── 2. Calculer le score de compatibilité pour chaque recette
        $recettesScorées = [];
        foreach ($toutes as $r) {
            $nutri = $recetteModel->calculerNutriScore((int)$r['id']);
            $score = $this->calculerScoreProfil($r, $nutri, $objectif, $regime, $activite);
            $recettesScorées[] = [
                'recette'    => $r,
                'nutriscore' => $nutri,
                'score'      => $score['score'],
                'statut'     => $score['statut'],
                'couleur'    => $score['couleur'],
            ];
        }

        // ── 3. Trier par score décroissant → top 6
        usort($recettesScorées, fn($a, $b) => $b['score'] - $a['score']);
        $top6 = array_slice($recettesScorées, 0, 6);

        // ── 4. Analyse Gemini du profil utilisateur
        $analyseGlobale = $this->analyserProfilGemini($objectif, $regime, $activite, $top6);

        echo json_encode([
            'recettes'       => $top6,
            'analyseGlobale' => $analyseGlobale,
            'source'         => (self::GEMINI_API_KEY !== 'VOTRE_CLE_GEMINI_ICI') ? 'gemini' : 'local',
        ]);
        exit;
    }

    // ── Score de compatibilité recette ↔ profil (logique métier PHP) ──────────
    private function calculerScoreProfil(array $r, array $nutri, string $objectif, string $regime, string $activite): array {
        $score = 30;

        $details = $nutri['details'] ?? [];
        $prot    = (float)($details['proteines'] ?? 0);
        $gluc    = (float)($details['glucides']  ?? 0);
        $lip     = (float)($details['lipides']   ?? 0);
        $kcal    = (float)($details['calories']  ?? 0);
        $lettre  = $nutri['lettre'] ?? '?';

        // Si pas de valeurs nutritionnelles calculées → utiliser les calories de la recette
        if ($kcal <= 0 && isset($r['calories']) && $r['calories'] > 0) {
            $kcal = (float)$r['calories'];
            // Estimer protéines/glucides/lipides depuis les calories et la catégorie
            $cat = $r['categorie'] ?? '';
            if (in_array($cat, ['sportif','dejeuner','diner'])) {
                $prot = $kcal * 0.25 / 4;  // 25% des calories en protéines
                $gluc = $kcal * 0.45 / 4;  // 45% en glucides
                $lip  = $kcal * 0.30 / 9;  // 30% en lipides
            } else {
                $prot = $kcal * 0.15 / 4;
                $gluc = $kcal * 0.55 / 4;
                $lip  = $kcal * 0.30 / 9;
            }
        }

        // ── Bonus Nutri-Score ─────────────────────────────────────────────────
        $score += match($lettre) {
            'A' => 15, 'B' => 10, 'C' => 3, 'D' => -8, 'E' => -15, default => 5
        };

        // ── Objectif ─────────────────────────────────────────────────────────
        if ($objectif === 'perte-poids') {
            if ($kcal > 0) {
                if ($kcal <= 150)     $score += 20;
                elseif ($kcal <= 300) $score += 10;
                elseif ($kcal <= 450) $score += 0;
                else                  $score -= 15;
            }
            if ($lip > 0)  { if ($lip <= 3) $score += 8; elseif ($lip > 10) $score -= 10; }
            if ($prot > 0) { if ($prot >= 12) $score += 8; }
        } elseif ($objectif === 'prise-masse') {
            if ($prot > 0) {
                if ($prot >= 20)      $score += 20;
                elseif ($prot >= 12)  $score += 12;
                else                  $score -= 5;
            }
            if ($kcal > 0) {
                if ($kcal >= 350)     $score += 12;
                elseif ($kcal < 150)  $score -= 10;
            }
            if ($gluc > 0 && $gluc >= 25) $score += 8;
        } elseif ($objectif === 'maintien') {
            if ($kcal > 0 && $kcal >= 150 && $kcal <= 350) $score += 12;
            if ($prot > 0 && $prot >= 8) $score += 8;
        }

        // ── Régime ────────────────────────────────────────────────────────────
        if ($regime === 'diabetique') {
            if ($gluc > 0) {
                if ($gluc <= 10)      $score += 20;
                elseif ($gluc <= 20)  $score += 10;
                elseif ($gluc <= 30)  $score -= 5;
                else                  $score -= 20;
            } else {
                // Pas de valeurs → se baser sur la catégorie
                if (in_array($r['categorie'] ?? '', ['regime','vegetarien'])) $score += 10;
                elseif (in_array($r['categorie'] ?? '', ['dessert','collation'])) $score -= 10;
            }
        } elseif ($regime === 'vegetarien') {
            if (in_array($r['categorie'] ?? '', ['vegetarien','regime','dessert','collation'])) $score += 15;
            else $score -= 5;
        } elseif ($regime === 'sportif') {
            if ($prot > 0) {
                if ($prot >= 15)      $score += 18;
                elseif ($prot >= 8)   $score += 8;
                else                  $score -= 8;
            }
            if (($r['categorie'] ?? '') === 'sportif') $score += 10;
        } elseif ($regime === 'normal') {
            $score += 5;
        }

        // ── Activité ──────────────────────────────────────────────────────────
        if ($activite === 'sportif') {
            if ($prot > 0 && $prot >= 15) $score += 10;
            if ($kcal > 0 && $kcal >= 250) $score += 8;
            elseif ($kcal > 0 && $kcal < 100) $score -= 8;
        } elseif ($activite === 'sedentaire') {
            if ($kcal > 0 && $kcal <= 200) $score += 12;
            elseif ($kcal > 0 && $kcal > 400) $score -= 12;
            if ($lip > 0 && $lip <= 5) $score += 8;
        } elseif ($activite === 'modere') {
            if ($kcal > 0 && $kcal >= 150 && $kcal <= 350) $score += 8;
        }

        // ── Bonus catégorie selon profil ──────────────────────────────────────
        $cat = $r['categorie'] ?? '';
        if ($objectif === 'perte-poids' && in_array($cat, ['regime','vegetarien'])) $score += 8;
        if ($objectif === 'prise-masse' && $cat === 'sportif') $score += 8;
        if ($regime === 'diabetique'    && $cat === 'regime')   $score += 5;

        $score = max(0, min(100, $score));
        $statut  = $score >= 70 ? 'adapte'  : ($score >= 45 ? 'modere' : 'non');
        $couleur = $score >= 70 ? '#2e7d32' : ($score >= 45 ? '#f57c00' : '#c62828');

        return ['score' => $score, 'statut' => $statut, 'couleur' => $couleur];
    }

    // ── Analyse Gemini du profil utilisateur ──────────────────────────────────
    private function analyserProfilGemini(string $objectif, string $regime, string $activite, array $top6): string {
        $nomsRecettes = implode(', ', array_map(fn($r) => $r['recette']['nom'], array_slice($top6, 0, 3)));

        $labels = [
            'objectif' => ['perte-poids' => 'perte de poids', 'prise-masse' => 'prise de masse', 'maintien' => 'maintien du poids'],
            'regime'   => ['diabetique' => 'diabétique', 'vegetarien' => 'végétarien', 'sportif' => 'sportif', 'normal' => 'normal'],
            'activite' => ['sedentaire' => 'sédentaire', 'modere' => 'modéré', 'sportif' => 'sportif intensif'],
        ];

        $objLabel = $labels['objectif'][$objectif] ?? $objectif;
        $regLabel = $labels['regime'][$regime]     ?? $regime;
        $actLabel = $labels['activite'][$activite] ?? $activite;

        if (self::GEMINI_API_KEY !== 'VOTRE_CLE_GEMINI_ICI' && self::GEMINI_API_KEY !== '') {
            $prompt  = "Tu es un nutritionniste expert. Analyse ce profil utilisateur en 3-4 phrases en français :\n";
            $prompt .= "- Objectif : $objLabel\n";
            $prompt .= "- Régime : $regLabel\n";
            $prompt .= "- Niveau d'activité : $actLabel\n";
            $prompt .= "- Recettes recommandées : $nomsRecettes\n\n";
            $prompt .= "Donne des conseils nutritionnels personnalisés et explique pourquoi ces recettes sont adaptées. Sois direct et professionnel.";

            $payload = json_encode([
                'contents' => [['role' => 'user', 'parts' => [['text' => $prompt]]]],
                'generationConfig' => ['temperature' => 0.6, 'maxOutputTokens' => 200]
            ]);

            $ch = curl_init(self::GEMINI_URL . '?key=' . self::GEMINI_API_KEY);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true, CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $payload,
                CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
                CURLOPT_TIMEOUT => 15, CURLOPT_SSL_VERIFYPEER => false,
            ]);
            $result   = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode === 200) {
                $data = json_decode($result, true);
                $text = trim($data['candidates'][0]['content']['parts'][0]['text'] ?? '');
                if ($text) return $text;
            }
        }

        // Fallback local
        $conseils = [
            'perte-poids' => 'Pour votre objectif de perte de poids, privilégiez les recettes faibles en calories et riches en protéines pour maintenir la satiété.',
            'prise-masse' => 'Pour la prise de masse, choisissez des recettes riches en protéines et en glucides complexes pour soutenir vos efforts.',
            'maintien'    => 'Pour maintenir votre poids, optez pour des recettes équilibrées avec un bon apport en protéines et des glucides modérés.',
        ];
        return ($conseils[$objectif] ?? 'Voici les recettes les mieux adaptées à votre profil.') .
               " Avec un régime $regLabel et une activité $actLabel, ces recettes ont été sélectionnées pour leur compatibilité nutritionnelle.";
    }

    // =========================================================================
    // UNSPLASH — Génération d'image pour un INGRÉDIENT
    // =========================================================================
    public function genererImageIngredient(): void {
        header('Content-Type: application/json; charset=utf-8');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['error' => 'Méthode non autorisée.']); exit;
        }

        $body = json_decode(file_get_contents('php://input'), true);
        $nom  = trim($body['nom'] ?? '');

        if ($nom === '') {
            echo json_encode(['error' => 'Nom de l\'ingrédient manquant.']); exit;
        }

        $accessKey = 'VOTRE_CLE_UNSPLASH_ICI';

        // Traduire les noms français courants vers anglais pour Unsplash
        $traductions = [
            'raisin' => 'grape', 'pomme' => 'apple', 'poire' => 'pear',
            'banane' => 'banana', 'fraise' => 'strawberry', 'cerise' => 'cherry',
            'citron' => 'lemon', 'orange' => 'orange', 'mangue' => 'mango',
            'ananas' => 'pineapple', 'peche' => 'peach', 'pêche' => 'peach',
            'tomate' => 'tomato', 'carotte' => 'carrot', 'oignon' => 'onion',
            'ail' => 'garlic', 'poivron' => 'bell pepper', 'courgette' => 'zucchini',
            'aubergine' => 'eggplant', 'brocoli' => 'broccoli', 'epinard' => 'spinach',
            'épinard' => 'spinach', 'laitue' => 'lettuce', 'concombre' => 'cucumber',
            'poulet' => 'chicken', 'boeuf' => 'beef', 'bœuf' => 'beef',
            'porc' => 'pork', 'agneau' => 'lamb', 'saumon' => 'salmon',
            'thon' => 'tuna', 'crevette' => 'shrimp', 'oeuf' => 'egg', 'œuf' => 'egg',
            'lait' => 'milk', 'fromage' => 'cheese', 'beurre' => 'butter',
            'yaourt' => 'yogurt', 'creme' => 'cream', 'crème' => 'cream',
            'farine' => 'flour', 'riz' => 'rice', 'pates' => 'pasta', 'pâtes' => 'pasta',
            'pain' => 'bread', 'sucre' => 'sugar', 'sel' => 'salt',
            'huile' => 'oil', 'vinaigre' => 'vinegar', 'miel' => 'honey',
            'cannelle' => 'cinnamon', 'cumin' => 'cumin', 'paprika' => 'paprika',
            'piment' => 'chili pepper', 'persil' => 'parsley', 'basilic' => 'basil',
            'menthe' => 'mint', 'thym' => 'thyme', 'romarin' => 'rosemary',
        ];
        $nomRecherche = strtolower(trim($nom));
        $nomAnglais   = $traductions[$nomRecherche] ?? $nom;

        $query = urlencode($nomAnglais . ' fresh ingredient food isolated');
        $page  = rand(1, 3);
        $url       = 'https://api.unsplash.com/search/photos?query=' . $query
                   . '&per_page=10&page=' . $page . '&orientation=squarish&client_id=' . $accessKey;

        $ch = curl_init($url);
        curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 10, CURLOPT_SSL_VERIFYPEER => false, CURLOPT_HTTPHEADER => ['Accept-Version: v1']]);
        $result   = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            // Fallback : utiliser Gemini pour décrire et Pollinations pour l'image
            $prompt   = urlencode($nom . ' ingredient, food photography, isolated, white background');
            $imageUrl = 'https://image.pollinations.ai/prompt/' . $prompt . '?width=400&height=400&nologo=true';
            $ch2 = curl_init($imageUrl);
            curl_setopt_array($ch2, [CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 30, CURLOPT_SSL_VERIFYPEER => false, CURLOPT_FOLLOWLOCATION => true]);
            $imageData2 = curl_exec($ch2);
            curl_close($ch2);
            if (!empty($imageData2) && strlen($imageData2) > 1000) {
                $dir = 'assets/uploads/ingredients/';
                if (!is_dir($dir)) mkdir($dir, 0755, true);
                $fichier = 'gen_' . uniqid() . '.jpg';
                file_put_contents($dir . $fichier, $imageData2);
                echo json_encode(['fichier' => $fichier, 'auteur' => 'IA', 'source' => 'pollinations']); exit;
            }
            echo json_encode(['error' => 'Service image indisponible (code ' . $httpCode . '). Confirmez votre email Unsplash ou réessayez.']); exit;
        }

        $data    = json_decode($result, true);
        $results = $data['results'] ?? [];

        if (empty($results)) {
            $urlFallback = 'https://api.unsplash.com/search/photos?query=fresh+ingredient+food&per_page=10&page=' . rand(1,5) . '&orientation=squarish&client_id=' . $accessKey;
            $ch2 = curl_init($urlFallback);
            curl_setopt_array($ch2, [CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 10, CURLOPT_SSL_VERIFYPEER => false, CURLOPT_HTTPHEADER => ['Accept-Version: v1']]);
            $r2      = json_decode(curl_exec($ch2), true);
            curl_close($ch2);
            $results = $r2['results'] ?? [];
        }

        if (empty($results)) { echo json_encode(['error' => 'Aucune photo trouvée.']); exit; }

        $photo    = $results[array_rand($results)];
        $imageUrl = $photo['urls']['regular'] ?? $photo['urls']['small'] ?? '';
        $auteur   = $photo['user']['name'] ?? 'Unsplash';

        $ch3 = curl_init($imageUrl);
        curl_setopt_array($ch3, [CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 15, CURLOPT_SSL_VERIFYPEER => false, CURLOPT_FOLLOWLOCATION => true]);
        $imageData = curl_exec($ch3);
        curl_close($ch3);

        if (empty($imageData)) { echo json_encode(['error' => 'Impossible de télécharger l\'image.']); exit; }

        $dir = 'assets/uploads/ingredients/';
        if (!is_dir($dir)) mkdir($dir, 0755, true);
        $fichier = 'unsplash_' . uniqid() . '.jpg';
        file_put_contents($dir . $fichier, $imageData);

        echo json_encode(['fichier' => $fichier, 'auteur' => $auteur, 'source' => 'unsplash']);
        exit;
    }

    // =========================================================================
    // UNSPLASH API — Recherche de photo pour une recette (gratuit, instantané)
    // Votre app → https://api.unsplash.com → photo professionnelle
    // =========================================================================
    public function genererImage(): void {
        header('Content-Type: application/json; charset=utf-8');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['error' => 'Méthode non autorisée.']); exit;
        }

        $body = json_decode(file_get_contents('php://input'), true);
        $nom  = trim($body['nom'] ?? '');

        if ($nom === '') {
            echo json_encode(['error' => 'Nom de la recette manquant.']); exit;
        }

        // ── Clé Unsplash (gratuite sur unsplash.com/developers) ──────────────
        $accessKey = 'VOTRE_CLE_UNSPLASH_ICI';

        // Traduire les noms de recettes français → anglais pour Unsplash
        $traductionsRecettes = [
            'couscous' => 'couscous', 'pizza' => 'pizza', 'pates' => 'pasta',
            'pâtes' => 'pasta', 'salade' => 'salad', 'soupe' => 'soup',
            'poulet' => 'chicken dish', 'boeuf' => 'beef dish', 'poisson' => 'fish dish',
            'gateau' => 'cake', 'gâteau' => 'cake', 'tarte' => 'tart',
            'quiche' => 'quiche', 'omelette' => 'omelette', 'crepe' => 'crepe',
            'crêpe' => 'crepe', 'risotto' => 'risotto', 'tajine' => 'tagine',
            'burger' => 'burger', 'sandwich' => 'sandwich', 'sushi' => 'sushi',
        ];
        $nomLower     = strtolower(trim($nom));
        $nomRecherche = $traductionsRecettes[$nomLower] ?? $nom;

        $query = urlencode($nomRecherche . ' food recipe dish');
        $page  = rand(1, 3); // Page aléatoire pour varier les résultats
        $url   = 'https://api.unsplash.com/search/photos?query=' . $query
               . '&per_page=10&page=' . $page . '&orientation=landscape&client_id=' . $accessKey;

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTPHEADER     => ['Accept-Version: v1'],
        ]);

        $result   = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlErr  = curl_error($ch);
        curl_close($ch);

        if ($curlErr) {
            echo json_encode(['error' => 'Erreur réseau : ' . $curlErr]); exit;
        }
        if ($httpCode !== 200) {
            // Fallback Pollinations si Unsplash indisponible
            $prompt   = urlencode($nom . ', food photography, professional, appetizing');
            $imgUrl   = 'https://image.pollinations.ai/prompt/' . $prompt . '?width=800&height=600&nologo=true';
            $ch2 = curl_init($imgUrl);
            curl_setopt_array($ch2, [CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 30, CURLOPT_SSL_VERIFYPEER => false, CURLOPT_FOLLOWLOCATION => true]);
            $imgData = curl_exec($ch2);
            curl_close($ch2);
            if (!empty($imgData) && strlen($imgData) > 1000) {
                $dir = 'assets/uploads/recettes/';
                if (!is_dir($dir)) mkdir($dir, 0755, true);
                $fichier = 'gen_' . uniqid() . '.jpg';
                file_put_contents($dir . $fichier, $imgData);
                echo json_encode(['fichier' => $fichier, 'auteur' => 'IA', 'source' => 'pollinations']); exit;
            }
            echo json_encode(['error' => 'Service image indisponible (code ' . $httpCode . '). Confirmez votre email Unsplash.']); exit;
        }

        $data    = json_decode($result, true);
        $results = $data['results'] ?? [];

        if (empty($results)) {
            // Fallback : chercher "food" si aucun résultat pour le nom
            $urlFallback = 'https://api.unsplash.com/search/photos?query=food+recipe&per_page=10&page=' . rand(1,5) . '&orientation=landscape&client_id=' . $accessKey;
            $ch3 = curl_init($urlFallback);
            curl_setopt_array($ch3, [CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 10, CURLOPT_SSL_VERIFYPEER => false, CURLOPT_HTTPHEADER => ['Accept-Version: v1']]);
            $r2      = json_decode(curl_exec($ch3), true);
            curl_close($ch3);
            $results = $r2['results'] ?? [];
        }

        if (empty($results)) {
            echo json_encode(['error' => 'Aucune photo trouvée. Réessayez avec un autre nom.']); exit;
        }

        // Prendre une photo aléatoire parmi les résultats
        $photo    = $results[array_rand($results)];
        $imageUrl = $photo['urls']['regular'] ?? $photo['urls']['small'] ?? '';
        $auteur   = $photo['user']['name'] ?? 'Unsplash';

        if (empty($imageUrl)) {
            // Fallback : chercher juste "food" si aucun résultat pour le nom
            $urlFallback = 'https://api.unsplash.com/search/photos?query=food+recipe&per_page=10&page=' . rand(1,5) . '&orientation=landscape&client_id=' . $accessKey;
            $ch3 = curl_init($urlFallback);
            curl_setopt_array($ch3, [CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 10, CURLOPT_SSL_VERIFYPEER => false]);
            $r2 = json_decode(curl_exec($ch3), true);
            curl_close($ch3);
            $results2 = $r2['results'] ?? [];
            if (!empty($results2)) {
                $photo    = $results2[array_rand($results2)];
                $imageUrl = $photo['urls']['regular'] ?? '';
                $auteur   = $photo['user']['name'] ?? 'Unsplash';
            }
        }

        if (empty($imageUrl)) {
            echo json_encode(['error' => 'Aucune photo trouvée pour "' . $nom . '".']); exit;
        }

        // ── Télécharger et sauvegarder l'image ────────────────────────────────
        $ch2 = curl_init($imageUrl);
        curl_setopt_array($ch2, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 15,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_FOLLOWLOCATION => true,
        ]);
        $imageData = curl_exec($ch2);
        curl_close($ch2);

        if (empty($imageData)) {
            echo json_encode(['error' => 'Impossible de télécharger l\'image.']); exit;
        }

        $dir = 'assets/uploads/recettes/';
        if (!is_dir($dir)) mkdir($dir, 0755, true);
        $fichier = 'unsplash_' . uniqid() . '.jpg';
        file_put_contents($dir . $fichier, $imageData);

        echo json_encode([
            'fichier' => $fichier,
            'auteur'  => $auteur,
            'source'  => 'unsplash',
        ]);
        exit;
    }

    // =========================================================================
    // EDAMAM FOOD DATABASE API — Valeurs nutritionnelles officielles
    // Votre app → https://api.edamam.com → valeurs réelles pour 100g
    // =========================================================================
    public function edamam(): void {
        header('Content-Type: application/json; charset=utf-8');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['error' => 'Méthode non autorisée.']); exit;
        }

        $body = json_decode(file_get_contents('php://input'), true);
        $nom  = trim($body['nom'] ?? '');

        if ($nom === '') {
            echo json_encode(['error' => 'Nom de l\'ingrédient manquant.']); exit;
        }

        // ── Clés Edamam ───────────────────────────────────────────────────────
        $appId  = 'VOTRE_EDAMAM_APP_ID';
        $appKey = 'VOTRE_EDAMAM_APP_KEY';

        // ── Appel à l'API Edamam Food Database ────────────────────────────────
        $url = 'https://api.edamam.com/api/food-database/v2/parser?'
             . http_build_query([
                 'ingr'    => $nom,
                 'app_id'  => $appId,
                 'app_key' => $appKey,
             ]);

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTPHEADER     => ['Accept: application/json'],
        ]);

        $result   = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlErr  = curl_error($ch);
        curl_close($ch);

        if ($curlErr) {
            echo json_encode(['error' => 'Erreur réseau : ' . $curlErr]); exit;
        }
        if ($httpCode !== 200) {
            echo json_encode(['error' => 'Edamam indisponible (code ' . $httpCode . '). Réessayez.']); exit;
        }

        $data  = json_decode($result, true);
        $hints = $data['hints'] ?? [];

        if (empty($hints)) {
            echo json_encode(['error' => 'Aucun aliment trouvé pour "' . $nom . '" dans Edamam.']); exit;
        }

        // Prendre le premier résultat
        $food    = $hints[0]['food'] ?? [];
        $nutri   = $food['nutrients'] ?? [];
        $label   = $food['label'] ?? $nom;

        // Edamam retourne les valeurs pour 100g directement
        $proteines = round((float)($nutri['PROCNT'] ?? 0), 2); // Protéines
        $glucides  = round((float)($nutri['CHOCDF'] ?? 0), 2); // Glucides
        $lipides   = round((float)($nutri['FAT']    ?? 0), 2); // Lipides
        $calcium   = round((float)($nutri['CA']     ?? 0), 2); // Calcium en mg

        echo json_encode([
            'proteines' => $proteines,
            'calcium'   => $calcium,
            'glucides'  => $glucides,
            'lipides'   => $lipides,
            'label'     => $label,
            'source'    => 'edamam',
        ]);
        exit;
    }

    // =========================================================================
    // GEMINI — Valeurs nutritionnelles d'un ingrédient (pour 100g)
    // =========================================================================
    public function nutrition(): void {
        header('Content-Type: application/json; charset=utf-8');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['error' => 'Méthode non autorisée.']); exit;
        }

        $body = json_decode(file_get_contents('php://input'), true);
        $nom  = trim($body['nom'] ?? '');

        if ($nom === '') {
            echo json_encode(['error' => 'Nom de l\'ingrédient manquant.']); exit;
        }

        if (self::GEMINI_API_KEY !== 'VOTRE_CLE_GEMINI_ICI' && self::GEMINI_API_KEY !== '') {
            $result = $this->getNutritionGemini($nom);
            if ($result !== null) {
                $result['source'] = 'gemini';
                echo json_encode($result); exit;
            }
        }

        // Fallback : valeurs estimées par type d'aliment
        $result = $this->getNutritionLocale($nom);
        $result['source'] = 'local';
        echo json_encode($result);
        exit;
    }

    private function getNutritionGemini(string $nom): ?array {
        $prompt = "Tu es un expert en nutrition. Donne-moi les valeurs nutritionnelles moyennes pour 100g de \"$nom\".\n";
        $prompt .= "Réponds UNIQUEMENT avec un objet JSON valide, sans texte avant ni après, sans markdown :\n";
        $prompt .= '{"proteines": X.X, "calcium": X.X, "glucides": X.X, "lipides": X.X}' . "\n";
        $prompt .= "Où X.X est un nombre décimal. proteines en g, calcium en mg, glucides en g, lipides en g.";

        $payload = json_encode([
            'contents' => [
                ['role' => 'user', 'parts' => [['text' => $prompt]]]
            ],
            'generationConfig' => [
                'temperature'     => 0.1,  // Très bas pour des valeurs précises
                'maxOutputTokens' => 100,
            ]
        ]);

        $ch = curl_init(self::GEMINI_URL . '?key=' . self::GEMINI_API_KEY);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $payload,
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
            CURLOPT_TIMEOUT        => 15,
            CURLOPT_SSL_VERIFYPEER => false,
        ]);

        $result   = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) return null;

        $data = json_decode($result, true);
        $text = trim($data['candidates'][0]['content']['parts'][0]['text'] ?? '');

        // Nettoyer le texte (enlever markdown si présent)
        $text = preg_replace('/```json|```/i', '', $text);
        $text = trim($text);

        $json = json_decode($text, true);
        if (!$json) {
            // Tentative d'extraction par regex si le JSON est mal formaté
            preg_match('/"proteines"\s*:\s*([\d.]+)/', $text, $mp);
            preg_match('/"calcium"\s*:\s*([\d.]+)/',   $text, $mc);
            preg_match('/"glucides"\s*:\s*([\d.]+)/',  $text, $mg);
            preg_match('/"lipides"\s*:\s*([\d.]+)/',   $text, $ml);
            if ($mp && $mc && $mg && $ml) {
                return [
                    'proteines' => round((float)$mp[1], 2),
                    'calcium'   => round((float)$mc[1], 2),
                    'glucides'  => round((float)$mg[1], 2),
                    'lipides'   => round((float)$ml[1], 2),
                ];
            }
            return null;
        }

        return [
            'proteines' => round((float)($json['proteines'] ?? 0), 2),
            'calcium'   => round((float)($json['calcium']   ?? 0), 2),
            'glucides'  => round((float)($json['glucides']  ?? 0), 2),
            'lipides'   => round((float)($json['lipides']   ?? 0), 2),
        ];
    }

    private function getNutritionLocale(string $nom): array {
        // Valeurs moyennes par catégorie d'aliment (fallback)
        $nomLower = strtolower($nom);

        $bases = [
            ['mots' => ['poulet','dinde','veau','boeuf','agneau','porc','viande'], 'val' => ['proteines'=>25,'calcium'=>15,'glucides'=>0,'lipides'=>5]],
            ['mots' => ['saumon','thon','sardine','poisson','cabillaud'],          'val' => ['proteines'=>22,'calcium'=>20,'glucides'=>0,'lipides'=>8]],
            ['mots' => ['lait','yaourt','fromage','beurre','crème'],               'val' => ['proteines'=>8,'calcium'=>120,'glucides'=>5,'lipides'=>10]],
            ['mots' => ['riz','pâtes','semoule','farine','pain','céréale'],        'val' => ['proteines'=>8,'calcium'=>20,'glucides'=>75,'lipides'=>1]],
            ['mots' => ['tomate','carotte','courgette','épinard','légume'],        'val' => ['proteines'=>2,'calcium'=>30,'glucides'=>5,'lipides'=>0.3]],
            ['mots' => ['pomme','banane','orange','fraise','fruit'],               'val' => ['proteines'=>1,'calcium'=>10,'glucides'=>15,'lipides'=>0.2]],
            ['mots' => ['huile','olive'],                                          'val' => ['proteines'=>0,'calcium'=>1,'glucides'=>0,'lipides'=>99]],
            ['mots' => ['oeuf','œuf'],                                             'val' => ['proteines'=>13,'calcium'=>55,'glucides'=>1,'lipides'=>11]],
        ];

        foreach ($bases as $base) {
            foreach ($base['mots'] as $mot) {
                if (str_contains($nomLower, $mot)) {
                    return $base['val'];
                }
            }
        }

        // Valeur par défaut
        return ['proteines' => 5, 'calcium' => 20, 'glucides' => 10, 'lipides' => 2];
    }

    // =========================================================================
    // GEMINI — Génération de description via API externe
    // =========================================================================
    private function genererDescriptionGemini(
        string $nom, string $categorie, string $difficulte,
        int $duree, int $calories, array $ingredients
    ): array {

        // Construire le prompt
        $nomsIng = implode(', ', array_filter(array_column($ingredients, 'nom')));

        $prompt = "Tu es un assistant culinaire expert. Génère une description appétissante et professionnelle ";
        $prompt .= "pour la recette suivante. La description doit faire 3 à 4 phrases maximum, ";
        $prompt .= "être en français, naturelle et donner envie de cuisiner cette recette.\n\n";
        $prompt .= "Informations de la recette :\n";
        $prompt .= "- Nom : $nom\n";
        if ($categorie)  $prompt .= "- Catégorie : $categorie\n";
        if ($difficulte) $prompt .= "- Difficulté : $difficulte\n";
        if ($duree > 0)  $prompt .= "- Durée de préparation : $duree minutes\n";
        if ($calories > 0) $prompt .= "- Calories : $calories kcal\n";
        if ($nomsIng)    $prompt .= "- Ingrédients principaux : $nomsIng\n";
        $prompt .= "\nRéponds UNIQUEMENT avec la description, sans titre ni explication.";

        // Appel API Gemini
        $payload = json_encode([
            'contents' => [
                ['role' => 'user', 'parts' => [['text' => $prompt]]]
            ],
            'generationConfig' => [
                'temperature'     => 0.8,
                'maxOutputTokens' => 300,
            ]
        ]);

        $ch = curl_init(self::GEMINI_URL . '?key=' . self::GEMINI_API_KEY);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $payload,
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
            CURLOPT_TIMEOUT        => 15,
            CURLOPT_SSL_VERIFYPEER => false,
        ]);

        $result   = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlErr  = curl_error($ch);
        curl_close($ch);

        if ($curlErr || $httpCode !== 200) {
            return ['description' => null, 'error' => $curlErr ?: 'Erreur API ' . $httpCode];
        }

        $data = json_decode($result, true);
        $text = trim($data['candidates'][0]['content']['parts'][0]['text'] ?? '');

        return ['description' => $text ?: null];
    }

    // =========================================================================
    // LOCAL — Fallback si pas de clé Gemini
    // =========================================================================
    private function genererDescriptionLocale(
        string $nom, string $categorie, string $difficulte,
        int $duree, int $calories, array $ingredients, int $extra = 0
    ): string {

        $seed = abs(crc32($nom . $extra));

        $catVariantes = [
            'petit-dejeuner' => [
                ['intro' => 'un petit-déjeuner énergisant et nourrissant',    'fin' => 'pour bien démarrer la journée avec vitalité.'],
                ['intro' => 'un petit-déjeuner gourmand et revigorant',       'fin' => 'pour commencer la journée du bon pied.'],
                ['intro' => 'un petit-déjeuner sain et savoureux',            'fin' => 'qui vous donnera toute l\'énergie nécessaire pour la matinée.'],
            ],
            'dejeuner' => [
                ['intro' => 'un déjeuner complet et savoureux',               'fin' => 'parfaite pour un repas du midi équilibré, elle ravira les papilles de toute la famille.'],
                ['intro' => 'un déjeuner généreux et parfumé',                'fin' => 'idéale pour un midi convivial et rassasiant.'],
                ['intro' => 'un déjeuner équilibré aux saveurs authentiques', 'fin' => 'qui fera le bonheur des petits et des grands à table.'],
            ],
            'diner' => [
                ['intro' => 'un dîner raffiné et réconfortant',               'fin' => 'pour terminer la journée en beauté autour d\'un repas chaleureux.'],
                ['intro' => 'un dîner savoureux aux arômes envoûtants',       'fin' => 'qui transformera votre soirée en véritable moment de plaisir.'],
                ['intro' => 'un dîner élégant et plein de caractère',         'fin' => 'parfait pour impressionner vos convives lors d\'un repas du soir.'],
            ],
            'collation' => [
                ['intro' => 'une collation légère et gourmande',              'fin' => 'pour une pause plaisir saine à tout moment de la journée.'],
                ['intro' => 'une collation fraîche et revigorante',           'fin' => 'idéale pour recharger les batteries entre les repas.'],
                ['intro' => 'une collation savoureuse et équilibrée',         'fin' => 'parfaite pour combler une petite faim sans culpabiliser.'],
            ],
            'dessert' => [
                ['intro' => 'un dessert gourmand et irrésistible',            'fin' => 'pour finir le repas sur une note sucrée et mémorable.'],
                ['intro' => 'un dessert délicat aux saveurs sucrées',         'fin' => 'qui fera fondre de plaisir tous les amateurs de douceurs.'],
                ['intro' => 'un dessert généreux et plein de saveurs',        'fin' => 'incontournable pour clôturer un repas en beauté.'],
            ],
            'vegetarien' => [
                ['intro' => 'un plat végétarien sain, coloré et savoureux',   'fin' => 'pour une alimentation naturelle, équilibrée et pleine de bienfaits.'],
                ['intro' => 'un plat végétarien riche en saveurs et en nutriments', 'fin' => 'qui prouve que manger sans viande peut être délicieux.'],
                ['intro' => 'un plat végétarien généreux et parfumé',         'fin' => 'idéal pour varier les plaisirs tout en prenant soin de sa santé.'],
            ],
            'regime' => [
                ['intro' => 'un plat diététique léger, nutritif et équilibré','fin' => 'pour prendre soin de votre ligne sans sacrifier le goût.'],
                ['intro' => 'un plat minceur savoureux et rassasiant',        'fin' => 'qui prouve qu\'un régime peut rimer avec plaisir culinaire.'],
                ['intro' => 'un plat léger aux saveurs fraîches et naturelles','fin' => 'parfait pour allier bien-être et plaisir à chaque repas.'],
            ],
            'sportif' => [
                ['intro' => 'un repas sportif riche en protéines et en énergie','fin' => 'pour soutenir vos performances et optimiser votre récupération.'],
                ['intro' => 'un repas énergétique conçu pour les sportifs',   'fin' => 'qui vous aidera à atteindre vos objectifs physiques.'],
                ['intro' => 'un repas complet et boosté en nutriments essentiels','fin' => 'idéal avant ou après l\'effort pour performer au maximum.'],
            ],
        ];

        $diffVariantes = [
            'facile'    => [
                ['label' => 'facile à réaliser',           'detail' => 'même les débutants en cuisine peuvent la préparer sans difficulté'],
                ['label' => 'simple et rapide à préparer', 'detail' => 'elle ne nécessite aucune technique particulière et convient à tous'],
                ['label' => 'accessible à tous',           'detail' => 'quelques gestes simples suffisent pour obtenir un résultat délicieux'],
            ],
            'moyen'     => [
                ['label' => 'de difficulté intermédiaire', 'detail' => 'elle nécessite quelques techniques de base mais reste accessible'],
                ['label' => 'à la portée des cuisiniers confirmés', 'detail' => 'avec un peu de pratique, vous obtiendrez un résultat remarquable'],
                ['label' => 'modérément technique',        'detail' => 'elle demande un peu d\'attention mais le résultat en vaut largement la peine'],
            ],
            'difficile' => [
                ['label' => 'élaborée',                    'detail' => 'elle demande de la maîtrise et de la précision pour un résultat digne d\'un chef'],
                ['label' => 'technique et raffinée',       'detail' => 'réservée aux cuisiniers expérimentés qui souhaitent relever un beau défi'],
                ['label' => 'exigeante mais gratifiante',  'detail' => 'chaque étape requiert soin et précision pour un plat d\'exception'],
            ],
        ];

        $dureeVariantes = [
            'court'     => ['En seulement {d} minutes chrono,', 'Ultra rapide avec {d} minutes,', 'Prête en {d} minutes à peine,'],
            'moyen'     => ['Prête en {d} minutes,', 'En {d} minutes de préparation,', 'Avec {d} minutes en cuisine,'],
            'long'      => ['Avec environ {d} minutes de préparation,', 'En {d} minutes de cuisine soignée,', 'Après {d} minutes de préparation minutieuse,'],
            'tres_long' => ['Cette recette, qui demande {d} minutes,', 'Avec {d} minutes de préparation,', 'Nécessitant {d} minutes de cuisine,'],
        ];

        $catOpts  = $catVariantes[$categorie]   ?? $catVariantes['dejeuner'];
        $diffOpts = $diffVariantes[$difficulte] ?? $diffVariantes['moyen'];
        $cat  = $catOpts[$seed % count($catOpts)];
        $diff = $diffOpts[($seed >> 2) % count($diffOpts)];

        $nomsIng  = array_values(array_filter(array_column($ingredients, 'nom')));
        $verbes   = ['mettant à l\'honneur', 'sublimant', 'alliant harmonieusement', 'combinant avec soin', 'révélant les saveurs de', 'misant sur'];
        $verbe    = $verbes[$seed % count($verbes)];
        $ingPhrase = '';
        if (!empty($nomsIng)) {
            $min = array_map('strtolower', $nomsIng);
            if (count($min) === 1)      $ingPhrase = ', ' . $verbe . ' ' . $min[0];
            elseif (count($min) === 2)  $ingPhrase = ', ' . $verbe . ' ' . $min[0] . ' et ' . $min[1];
            else { $d = array_pop($min); $ingPhrase = ', ' . $verbe . ' ' . implode(', ', $min) . ' et ' . $d; }
        }

        $dureePhrase = '';
        if ($duree > 0) {
            $g    = $duree <= 15 ? 'court' : ($duree <= 30 ? 'moyen' : ($duree <= 60 ? 'long' : 'tres_long'));
            $opts = $dureeVariantes[$g];
            $dureePhrase = str_replace('{d}', $duree, $opts[($seed >> 1) % count($opts)]);
        }

        $calPhrase = '';
        if ($calories > 0) {
            $cals = [
                'legere'      => ['Très légère avec seulement ' . $calories . ' kcal, elle s\'intègre parfaitement dans un régime équilibré.', 'Avec seulement ' . $calories . ' kcal, c\'est un choix idéal pour manger sainement.'],
                'moderee'     => ['Avec ' . $calories . ' kcal par portion, elle offre un excellent équilibre nutritionnel.', 'Comptant ' . $calories . ' kcal, elle constitue un repas complet et bien dosé.'],
                'consistante' => ['Généreuse avec ' . $calories . ' kcal, elle apporte l\'énergie nécessaire pour la journée.', 'Avec ' . $calories . ' kcal, elle est idéale pour les repas copieux.'],
                'riche'       => ['Riche avec ' . $calories . ' kcal, elle est idéale pour les grandes occasions.', 'Avec ' . $calories . ' kcal, c\'est un plat généreux réservé aux moments de fête.'],
            ];
            $cg = $calories < 300 ? 'legere' : ($calories <= 600 ? 'moderee' : ($calories <= 900 ? 'consistante' : 'riche'));
            $calPhrase = $cals[$cg][($seed >> 3) % 2];
        }

        $phrases   = [];
        $phrases[] = ucfirst($nom) . ' est ' . $cat['intro'] . $ingPhrase . '.';
        $phrases[] = ($dureePhrase ? $dureePhrase . ' cette recette est ' : 'Cette recette est ') . $diff['label'] . ' — ' . $diff['detail'] . '.';
        if ($calPhrase) $phrases[] = $calPhrase;
        $phrases[] = ucfirst($cat['fin']);

        return implode(' ', $phrases);
    }

    // =========================================================================
    // Suggestions d'ingrédients — logique métier locale (BDD)
    // =========================================================================
    private function suggererIngredients(string $nom, string $categorie, array $dejaSélectionnés, int $extra = 0): array {
        try {
            $ingModel        = new Ingredient();
            $tousIngredients = $ingModel->getAll();
            if (empty($tousIngredients)) return [];

            $idsExistants = array_map('intval', array_column($dejaSélectionnés, 'id'));
            $motsCles     = array_filter(explode(' ', strtolower($nom)), fn($m) => strlen($m) > 2);

            $typesPrio = [
                'petit-dejeuner' => ['cereale', 'fruit', 'produit-laitier'],
                'dejeuner'       => ['viande', 'legume', 'cereale'],
                'diner'          => ['viande', 'legume', 'epice'],
                'collation'      => ['fruit', 'produit-laitier', 'cereale'],
                'dessert'        => ['fruit', 'produit-laitier', 'cereale'],
                'vegetarien'     => ['legume', 'fruit', 'cereale', 'epice'],
                'regime'         => ['legume', 'fruit', 'produit-laitier'],
                'sportif'        => ['viande', 'cereale', 'produit-laitier'],
            ][$categorie] ?? ['legume', 'viande', 'cereale'];

            $liquidesLaitiers = ['lait', 'crème', 'cream', 'yaourt liquide', 'kéfir', 'laban'];
            $quantites = [
                'legume'  => ['quantite' => 150, 'unite' => 'g'],
                'fruit'   => ['quantite' => 120, 'unite' => 'g'],
                'viande'  => ['quantite' => 200, 'unite' => 'g'],
                'cereale' => ['quantite' => 100, 'unite' => 'g'],
                'epice'   => ['quantite' => 5,   'unite' => 'g'],
                'autre'   => ['quantite' => 100, 'unite' => 'g'],
            ];

            $scores = [];
            foreach ($tousIngredients as $ing) {
                if (in_array((int)$ing['id'], $idsExistants)) continue;
                $nomIng = strtolower($ing['nom']);
                $score  = 0;
                foreach ($motsCles as $mot) {
                    if (str_contains($nomIng, $mot) || str_contains($mot, $nomIng)) $score += 5;
                }
                if (in_array($ing['type'] ?? '', $typesPrio)) $score += 3;
                $score += abs(crc32($ing['nom'] . $nom . $extra)) % 5;
                if ($score > 0) $scores[] = ['ing' => $ing, 'score' => $score];
            }

            if (count($scores) < 6) {
                foreach ($tousIngredients as $ing) {
                    if (in_array((int)$ing['id'], $idsExistants)) continue;
                    if (!empty(array_filter($scores, fn($s) => $s['ing']['id'] == $ing['id']))) continue;
                    if (in_array($ing['type'] ?? '', $typesPrio)) {
                        $scores[] = ['ing' => $ing, 'score' => 1 + abs(crc32($ing['nom'] . $nom . $extra)) % 5];
                    }
                }
            }

            usort($scores, fn($a, $b) => $b['score'] - $a['score']);

            return array_map(function($item) use ($quantites, $liquidesLaitiers) {
                $ing    = $item['ing'];
                $type   = $ing['type'] ?? 'autre';
                $nomIng = strtolower($ing['nom']);
                if ($type === 'produit-laitier') {
                    $estLiquide = !empty(array_filter($liquidesLaitiers, fn($m) => str_contains($nomIng, $m)));
                    $q = $estLiquide ? ['quantite' => 200, 'unite' => 'ml'] : ['quantite' => 100, 'unite' => 'g'];
                } else {
                    $q = $quantites[$type] ?? ['quantite' => 100, 'unite' => 'g'];
                }
                return ['id' => $ing['id'], 'nom' => $ing['nom'], 'quantite' => $q['quantite'], 'unite' => $q['unite']];
            }, array_slice($scores, 0, 6));

        } catch (Exception $e) {
            return [];
        }
    }
}
