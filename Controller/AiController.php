<?php
require_once 'Model/Ingredient.php';

/**
 * AiController — Génération automatique de description + suggestions d'ingrédients
 * Logique locale, sans API externe
 * Route : POST /2A35/Admin/Ai/chat
 */
class AiController {

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
        // _t permet de forcer une variante différente lors de la régénération
        $extra       = (int)($context['_t']        ?? 0);

        if ($nom === '') {
            echo json_encode(['error' => 'Veuillez d\'abord saisir le nom de la recette.']); exit;
        }

        $description = null;
        $suggestions = [];

        if ($type === 'description' || $type === 'tout') {
            $description = $this->genererDescription($nom, $categorie, $difficulte, $duree, $calories, $ingredients, $extra);
        }

        if ($type === 'ingredients' || $type === 'tout') {
            // Passer le timestamp pour varier les suggestions à chaque appel
            $suggestions = $this->suggererIngredients($nom, $categorie, $ingredients, $extra);
        }

        echo json_encode([
            'description' => $description,
            'suggestions' => $suggestions,
        ]);
        exit;
    }

    // =========================================================================
    // Génération de description enrichie
    // =========================================================================
    private function genererDescription(
        string $nom, string $categorie, string $difficulte,
        int $duree, int $calories, array $ingredients, int $extra = 0
    ): string {

        // Seed basé sur le nom + extra (timestamp lors régénération) pour varier
        $seed = abs(crc32($nom . $extra));

        $catVariantes = [
            'petit-dejeuner' => [
                ['intro' => 'un petit-déjeuner énergisant et nourrissant',   'fin' => 'pour bien démarrer la journée avec vitalité.'],
                ['intro' => 'un petit-déjeuner gourmand et revigorant',      'fin' => 'pour commencer la journée du bon pied.'],
                ['intro' => 'un petit-déjeuner sain et savoureux',           'fin' => 'qui vous donnera toute l\'énergie nécessaire pour la matinée.'],
            ],
            'dejeuner' => [
                ['intro' => 'un déjeuner complet et savoureux',              'fin' => 'parfaite pour un repas du midi équilibré, elle ravira les papilles de toute la famille.'],
                ['intro' => 'un déjeuner généreux et parfumé',               'fin' => 'idéale pour un midi convivial et rassasiant.'],
                ['intro' => 'un déjeuner équilibré aux saveurs authentiques','fin' => 'qui fera le bonheur des petits et des grands à table.'],
            ],
            'diner' => [
                ['intro' => 'un dîner raffiné et réconfortant',              'fin' => 'pour terminer la journée en beauté autour d\'un repas chaleureux.'],
                ['intro' => 'un dîner savoureux aux arômes envoûtants',      'fin' => 'qui transformera votre soirée en véritable moment de plaisir.'],
                ['intro' => 'un dîner élégant et plein de caractère',        'fin' => 'parfait pour impressionner vos convives lors d\'un repas du soir.'],
            ],
            'collation' => [
                ['intro' => 'une collation légère et gourmande',             'fin' => 'pour une pause plaisir saine à tout moment de la journée.'],
                ['intro' => 'une collation fraîche et revigorante',          'fin' => 'idéale pour recharger les batteries entre les repas.'],
                ['intro' => 'une collation savoureuse et équilibrée',        'fin' => 'parfaite pour combler une petite faim sans culpabiliser.'],
            ],
            'dessert' => [
                ['intro' => 'un dessert gourmand et irrésistible',           'fin' => 'pour finir le repas sur une note sucrée et mémorable.'],
                ['intro' => 'un dessert délicat aux saveurs sucrées',        'fin' => 'qui fera fondre de plaisir tous les amateurs de douceurs.'],
                ['intro' => 'un dessert généreux et plein de saveurs',       'fin' => 'incontournable pour clôturer un repas en beauté.'],
            ],
            'vegetarien' => [
                ['intro' => 'un plat végétarien sain, coloré et savoureux',  'fin' => 'pour une alimentation naturelle, équilibrée et pleine de bienfaits.'],
                ['intro' => 'un plat végétarien riche en saveurs et en nutriments', 'fin' => 'qui prouve que manger sans viande peut être délicieux.'],
                ['intro' => 'un plat végétarien généreux et parfumé',        'fin' => 'idéal pour varier les plaisirs tout en prenant soin de sa santé.'],
            ],
            'regime' => [
                ['intro' => 'un plat diététique léger, nutritif et équilibré','fin' => 'pour prendre soin de votre ligne sans sacrifier le goût.'],
                ['intro' => 'un plat minceur savoureux et rassasiant',       'fin' => 'qui prouve qu\'un régime peut rimer avec plaisir culinaire.'],
                ['intro' => 'un plat léger aux saveurs fraîches et naturelles','fin' => 'parfait pour allier bien-être et plaisir à chaque repas.'],
            ],
            'sportif' => [
                ['intro' => 'un repas sportif riche en protéines et en énergie','fin' => 'pour soutenir vos performances et optimiser votre récupération.'],
                ['intro' => 'un repas énergétique conçu pour les sportifs',  'fin' => 'qui vous aidera à atteindre vos objectifs physiques.'],
                ['intro' => 'un repas complet et boosté en nutriments essentiels','fin' => 'idéal avant ou après l\'effort pour performer au maximum.'],
            ],
        ];

        $diffVariantes = [
            'facile' => [
                ['label' => 'facile à réaliser',          'detail' => 'même les débutants en cuisine peuvent la préparer sans difficulté'],
                ['label' => 'simple et rapide à préparer','detail' => 'elle ne nécessite aucune technique particulière et convient à tous'],
                ['label' => 'accessible à tous',          'detail' => 'quelques gestes simples suffisent pour obtenir un résultat délicieux'],
            ],
            'moyen' => [
                ['label' => 'de difficulté intermédiaire','detail' => 'elle nécessite quelques techniques de base mais reste accessible'],
                ['label' => 'à la portée des cuisiniers confirmés','detail' => 'avec un peu de pratique, vous obtiendrez un résultat remarquable'],
                ['label' => 'modérément technique',       'detail' => 'elle demande un peu d\'attention mais le résultat en vaut largement la peine'],
            ],
            'difficile' => [
                ['label' => 'élaborée',                   'detail' => 'elle demande de la maîtrise et de la précision pour un résultat digne d\'un chef'],
                ['label' => 'technique et raffinée',      'detail' => 'réservée aux cuisiniers expérimentés qui souhaitent relever un beau défi'],
                ['label' => 'exigeante mais gratifiante', 'detail' => 'chaque étape requiert soin et précision pour un plat d\'exception'],
            ],
        ];

        $dureeVariantes = [
            'court' => [
                'En seulement {duree} minutes chrono,',
                'Ultra rapide avec {duree} minutes de préparation,',
                'Prête en {duree} minutes à peine,',
            ],
            'moyen' => [
                'Prête en {duree} minutes,',
                'En {duree} minutes de préparation,',
                'Avec {duree} minutes en cuisine,',
            ],
            'long' => [
                'Avec environ {duree} minutes de préparation,',
                'En {duree} minutes de cuisine soignée,',
                'Après {duree} minutes de préparation minutieuse,',
            ],
            'tres_long' => [
                'Cette recette, qui demande {duree} minutes de préparation,',
                'Avec {duree} minutes de préparation,',
                'Nécessitant {duree} minutes de cuisine,',
            ],
        ];

        // Choisir les variantes selon le seed
        $catOptions  = $catVariantes[$categorie]   ?? $catVariantes['dejeuner'];
        $diffOptions = $diffVariantes[$difficulte] ?? $diffVariantes['moyen'];

        $cat  = $catOptions[$seed % count($catOptions)];
        $diff = $diffOptions[($seed >> 2) % count($diffOptions)];

        // ── Ingrédients ───────────────────────────────────────────────────────
        $nomsIng = array_values(array_filter(array_column($ingredients, 'nom')));
        $ingVariantes = [
            'mettant à l\'honneur',
            'sublimant',
            'alliant harmonieusement',
            'combinant avec soin',
            'révélant les saveurs de',
            'misant sur',
        ];
        $ingVerbe = $ingVariantes[$seed % count($ingVariantes)];

        $ingPhrase = '';
        if (!empty($nomsIng)) {
            $nomsMin = array_map('strtolower', $nomsIng);
            if (count($nomsMin) === 1) {
                $ingPhrase = ', ' . $ingVerbe . ' ' . $nomsMin[0];
            } elseif (count($nomsMin) === 2) {
                $ingPhrase = ', ' . $ingVerbe . ' ' . $nomsMin[0] . ' et ' . $nomsMin[1];
            } else {
                $dernier   = array_pop($nomsMin);
                $ingPhrase = ', ' . $ingVerbe . ' ' . implode(', ', $nomsMin) . ' et ' . $dernier;
            }
        }

        // ── Durée ─────────────────────────────────────────────────────────────
        $dureePhrase = '';
        if ($duree > 0) {
            if ($duree <= 15)     $groupe = 'court';
            elseif ($duree <= 30) $groupe = 'moyen';
            elseif ($duree <= 60) $groupe = 'long';
            else                  $groupe = 'tres_long';

            $opts = $dureeVariantes[$groupe];
            $tpl  = $opts[($seed >> 1) % count($opts)];
            $dureePhrase = str_replace('{duree}', $duree, $tpl);
        }

        // ── Calories ──────────────────────────────────────────────────────────
        $calPhrase = '';
        if ($calories > 0) {
            $calVariantes = [
                'legere' => [
                    'Très légère avec seulement ' . $calories . ' kcal par portion, elle s\'intègre parfaitement dans un régime équilibré.',
                    'Avec seulement ' . $calories . ' kcal, c\'est un choix idéal pour manger sainement sans se priver.',
                ],
                'moderee' => [
                    'Avec ' . $calories . ' kcal par portion, elle offre un excellent équilibre nutritionnel.',
                    'Comptant ' . $calories . ' kcal, elle constitue un repas complet et bien dosé.',
                ],
                'consistante' => [
                    'Généreuse avec ' . $calories . ' kcal, elle apporte l\'énergie nécessaire pour la journée.',
                    'Avec ' . $calories . ' kcal, elle est idéale pour les repas copieux et rassasiants.',
                ],
                'riche' => [
                    'Riche et consistante avec ' . $calories . ' kcal, elle est idéale pour les grandes occasions.',
                    'Avec ' . $calories . ' kcal, c\'est un plat généreux réservé aux moments de fête.',
                ],
            ];
            if ($calories < 300)      $cg = 'legere';
            elseif ($calories <= 600) $cg = 'moderee';
            elseif ($calories <= 900) $cg = 'consistante';
            else                      $cg = 'riche';

            $opts      = $calVariantes[$cg];
            $calPhrase = $opts[($seed >> 3) % count($opts)];
        }

        // ── Construction finale ───────────────────────────────────────────────
        $phrases = [];

        // Phrase 1 : présentation
        $phrases[] = ucfirst($nom) . ' est ' . $cat['intro'] . $ingPhrase . '.';

        // Phrase 2 : durée + difficulté
        if ($dureePhrase) {
            $phrases[] = $dureePhrase . ' cette recette est ' . $diff['label'] . ' — ' . $diff['detail'] . '.';
        } else {
            $phrases[] = 'Cette recette est ' . $diff['label'] . ' — ' . $diff['detail'] . '.';
        }

        // Phrase 3 : calories
        if ($calPhrase) {
            $phrases[] = $calPhrase;
        }

        // Phrase 4 : accroche finale
        $phrases[] = ucfirst($cat['fin']);

        return implode(' ', $phrases);
    }

    // =========================================================================
    // Suggestions d'ingrédients depuis la BDD — variées selon la recette
    // =========================================================================
    private function suggererIngredients(string $nom, string $categorie, array $dejaSélectionnés, int $extra = 0): array {
        try {
            $ingModel        = new Ingredient();
            $tousIngredients = $ingModel->getAll();

            if (empty($tousIngredients)) return [];

            // IDs déjà dans le formulaire — exclus des suggestions
            $idsExistants = array_map('intval', array_column($dejaSélectionnés, 'id'));

            // Mots-clés du nom (> 2 caractères)
            $motsCles = array_filter(
                explode(' ', strtolower($nom)),
                fn($m) => strlen($m) > 2
            );

            // Types prioritaires par catégorie
            $typesPrioritaires = [
                'petit-dejeuner' => ['cereale', 'fruit', 'produit-laitier'],
                'dejeuner'       => ['viande', 'legume', 'cereale'],
                'diner'          => ['viande', 'legume', 'epice'],
                'collation'      => ['fruit', 'produit-laitier', 'cereale'],
                'dessert'        => ['fruit', 'produit-laitier', 'cereale'],
                'vegetarien'     => ['legume', 'fruit', 'cereale', 'epice'],
                'regime'         => ['legume', 'fruit', 'produit-laitier'],
                'sportif'        => ['viande', 'cereale', 'produit-laitier'],
            ];
            $typesPrio = $typesPrioritaires[$categorie] ?? ['legume', 'viande', 'cereale'];

            // ── Unités correctes par type d'ingrédient ────────────────────────
            // Produits laitiers LIQUIDES → ml, SOLIDES → g
            $liquidesLaitiers = ['lait', 'crème', 'cream', 'yaourt liquide', 'kéfir', 'laban'];

            $quantites = [
                'legume'          => ['quantite' => 150, 'unite' => 'g'],
                'fruit'           => ['quantite' => 120, 'unite' => 'g'],
                'viande'          => ['quantite' => 200, 'unite' => 'g'],
                'cereale'         => ['quantite' => 100, 'unite' => 'g'],
                'epice'           => ['quantite' => 5,   'unite' => 'g'],
                'autre'           => ['quantite' => 100, 'unite' => 'g'],
                // produit-laitier géré dynamiquement ci-dessous
            ];

            // ── Calcul des scores ─────────────────────────────────────────────
            $scores = [];
            foreach ($tousIngredients as $ing) {
                if (in_array((int)$ing['id'], $idsExistants)) continue;

                $nomIng = strtolower($ing['nom']);
                $score  = 0;

                // +5 si le nom de l'ingrédient apparaît dans le nom de la recette
                foreach ($motsCles as $mot) {
                    if (str_contains($nomIng, $mot) || str_contains($mot, $nomIng)) {
                        $score += 5;
                    }
                }

                // +3 si le type correspond à la catégorie
                if (in_array($ing['type'] ?? '', $typesPrio)) {
                    $score += 3;
                }

                // Variation basée sur nom + recette + extra (timestamp) pour éviter répétitions
                $variation = abs(crc32($ing['nom'] . $nom . $extra)) % 5;
                $score += $variation;

                if ($score > 0) {
                    $scores[] = ['ing' => $ing, 'score' => $score];
                }
            }

            // Compléter si moins de 6 résultats
            if (count($scores) < 6) {
                foreach ($tousIngredients as $ing) {
                    if (in_array((int)$ing['id'], $idsExistants)) continue;
                    $dejaDans = array_filter($scores, fn($s) => $s['ing']['id'] == $ing['id']);
                    if (!empty($dejaDans)) continue;
                    if (in_array($ing['type'] ?? '', $typesPrio)) {
                        $variation = abs(crc32($ing['nom'] . $nom . $extra)) % 5;
                        $scores[]  = ['ing' => $ing, 'score' => 1 + $variation];
                    }
                }
            }

            // Trier par score décroissant
            usort($scores, fn($a, $b) => $b['score'] - $a['score']);

            // Prendre les 6 meilleurs
            $top = array_slice($scores, 0, 6);

            // ── Construire le résultat avec unités correctes ──────────────────
            return array_map(function($item) use ($quantites, $liquidesLaitiers) {
                $ing    = $item['ing'];
                $type   = $ing['type'] ?? 'autre';
                $nomIng = strtolower($ing['nom']);

                if ($type === 'produit-laitier') {
                    // Liquide → ml, solide (fromage, yaourt, beurre...) → g
                    $estLiquide = false;
                    foreach ($liquidesLaitiers as $motLiquide) {
                        if (str_contains($nomIng, $motLiquide)) {
                            $estLiquide = true;
                            break;
                        }
                    }
                    $q = $estLiquide
                        ? ['quantite' => 200, 'unite' => 'ml']
                        : ['quantite' => 100, 'unite' => 'g'];
                } else {
                    $q = $quantites[$type] ?? ['quantite' => 100, 'unite' => 'g'];
                }

                return [
                    'id'       => $ing['id'],
                    'nom'      => $ing['nom'],
                    'quantite' => $q['quantite'],
                    'unite'    => $q['unite'],
                ];
            }, $top);

        } catch (Exception $e) {
            return [];
        }
    }
}
