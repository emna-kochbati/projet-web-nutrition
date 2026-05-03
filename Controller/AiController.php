<?php
require_once 'Model/Ingredient.php';

/**
 * AiController
 * ─────────────────────────────────────────────────────────────────────────────
 * - Description  : générée via l'API Google Gemini (IA externe)
 * - Suggestions  : calculées localement depuis la BDD (logique métier)
 *
 * Route : POST /2A35/Admin/Ai/chat
 *
 * ► Clé Gemini gratuite : https://aistudio.google.com/app/apikey
 *   Collez-la dans la constante GEMINI_API_KEY ci-dessous.
 */
class AiController {

    // ── Clé API Gemini ────────────────────────────────────────────────────────
    // Remplacez par votre clé : https://aistudio.google.com/app/apikey
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
