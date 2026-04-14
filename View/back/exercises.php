<?php
include_once __DIR__ . '/../../Controller/exerciseC.php';

$exerciseC = new ExerciseC();
if (isset($_GET['delete'])) {
    $exerciseC->delete();
}

$programFilter = $_GET['program'] ?? null;
if ($programFilter !== null && $programFilter !== '') {
    $exercises = $exerciseC->readByProgram($programFilter);
} else {
    $exercises = $exerciseC->readWithPrograms();
}

$pageTitle = 'Exercises';
$pageHeading = 'Exercises';
include __DIR__ . '/partials/header.php';
?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="card-title fw-semibold mb-0">Manage exercises</h5>
                    <a href="addexercise.php<?= $programFilter ? '?program=' . urlencode($programFilter) : '' ?>" class="btn btn-primary">Add exercise</a>
                </div>
                <?php if ($programFilter !== null && $programFilter !== '') { ?>
                    <div class="alert alert-info">Showing exercises for program #<?= htmlspecialchars($programFilter) ?></div>
                <?php } ?>
                <div class="table-responsive">

                    <div class="table-responsive">
                        <table class="table table-striped align-middle">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Program</th>
                                    <th>Name</th>
                                    <th>Reps</th>
                                    <th>Sets</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($exercises as $exercise) { ?>
                                    <tr>
                                        <td><?= htmlspecialchars($exercise['id']) ?></td>
                                        <td><?= htmlspecialchars($exercise['program_name'] ?? $exercise['program_id']) ?></td>
                                        <td><?= htmlspecialchars($exercise['name']) ?></td>
                                        <td><?= htmlspecialchars($exercise['reps']) ?></td>
                                        <td><?= htmlspecialchars($exercise['sets']) ?></td>
                                        <td class="text-end">
                                            <a class="btn btn-sm btn-warning" href="updateexercise.php?update=<?= urlencode($exercise['id']) ?><?= $programFilter ? '&program=' . urlencode($programFilter) : '' ?>">Edit</a>
                                            <a class="btn btn-sm btn-danger" href="exercises.php?delete=<?= urlencode($exercise['id']) ?><?= $programFilter ? '&program=' . urlencode($programFilter) : '' ?>" onclick="return confirm('Delete this exercise?')">Delete</a>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include __DIR__ . '/partials/footer.php'; ?>