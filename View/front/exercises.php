<?php
include_once __DIR__ . '/../../Controller/exerciseC.php';
include_once __DIR__ . '/../../Controller/programC.php';

$programFilter = $_GET['program'] ?? null;

if ($programFilter === null || $programFilter === '') {
    header('Location: programs.php');
    exit;
}

$exerciseC = new ExerciseC();
$programC = new ProgramC();
$program = $programC->findone($programFilter);

if (!$program) {
    header('Location: programs.php');
    exit;
}

$exercises = $exerciseC->readByProgram($programFilter);

include __DIR__ . '/partials/header.php';
?>

<div class="container py-5" style="margin-top: 140px;">
    <div class="row align-items-end mb-4">
        <div class="col-lg-8">
            <h1 class="mb-3">Exercises</h1>
            <p class="text-muted mb-0">
                Exercises for <strong><?= htmlspecialchars($program['name']) ?></strong>
            </p>
        </div>
    </div>

    <div class="row g-4">
        <?php foreach ($exercises as $exercise) { ?>
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body d-flex flex-column">
                        <span class="badge bg-primary align-self-start mb-3"><?= htmlspecialchars($exercise['program_name'] ?? '') ?></span>
                        <h4 class="card-title"><?= htmlspecialchars($exercise['name']) ?></h4>
                        <p class="card-text text-muted">Reps: <?= htmlspecialchars($exercise['reps']) ?>, Sets: <?= htmlspecialchars($exercise['sets']) ?></p>
                        <p class="card-text small text-muted mb-0">Program level: <?= htmlspecialchars($exercise['program_level'] ?? '') ?></p>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</div>

<?php include __DIR__ . '/partials/footer.php'; ?>