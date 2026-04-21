<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title ?? 'Dashboard') ?> — EcoNutri Admin</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; background: #f4f6f8; }

        .layout-wrapper {
            display: flex;
            min-height: 100vh;
        }

        /* Le sidebar fait 220px — défini dans sidebar.php */

        .main-area {
            margin-left: 220px;
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .top-header {
            background: #ffffff;
            padding: 14px 28px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 6px rgba(0,0,0,0.07);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .top-header h1 {
            font-size: 1.2rem;
            color: #1a1a1a;
            font-weight: 700;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.9rem;
            color: #444;
            font-weight: 600;
        }

        .page-content {
            padding: 28px;
            flex: 1;
        }
    </style>
</head>
<body>
<div class="layout-wrapper">

    <!-- Sidebar -->
    <?php include 'View/back/partials/sidebar.php'; ?>

    <!-- Contenu principal -->
    <div class="main-area">
        <header class="top-header">
            <h1><?= htmlspecialchars($page_title ?? 'Dashboard') ?></h1>
            <div class="user-info">
                <span>👤</span>
                <span>Admin</span>
            </div>
        </header>

        <div class="page-content">
            <?= $content ?>
        </div>
    </div>

</div>
</body>
</html>

