<?php
include __DIR__ . '/partials/header.php';
include_once __DIR__ . '/../../Controller/programC.php';

$programC = new ProgramC();
$programs = $programC->read();
?>

<div class="container py-5">
    <br><br><br><br>
    <div class="row align-items-end mb-4">
        <div class="col-lg-8">
            <h1 class="mb-3">Programs</h1>
            <p class="text-muted mb-0">Browse the available training programs and jump into their exercises.</p>
        </div>
    </div>

    <div class="row g-4">
        <?php foreach ($programs as $program) { ?>
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body d-flex flex-column">
                        <span class="badge bg-success align-self-start mb-3">Level <?= htmlspecialchars($program['level']) ?></span>
                        <h4 class="card-title"><?= htmlspecialchars($program['name']) ?></h4>
                        <p class="card-text text-muted flex-grow-1"><?= htmlspecialchars($program['description']) ?></p>
                        <a href="exercises.php?program=<?= urlencode($program['id']) ?>" class="btn btn-primary mt-auto">View exercises</a>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</div>

<?php include __DIR__ . '/partials/footer.php'; ?>