<?php
include __DIR__ . '/partials/header.php';
include_once __DIR__ . '/../../Controller/exerciseC.php';
include_once __DIR__ . '/../../Controller/programC.php';

$exerciseC = new ExerciseC();
$programC = new ProgramC();
$programs = $programC->read();
$exercise = null;
$error = '';
$id = $_GET['update'] ?? null;

if ($id !== null) {
    $exercise = $exerciseC->findone($id);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $programId = trim($_POST['program_id'] ?? '');
    $name = trim($_POST['name'] ?? '');
    $reps = trim($_POST['reps'] ?? '');
    $sets = trim($_POST['sets'] ?? '');
    $id = $_POST['id'] ?? $id;

    if ($programId === '' || $name === '' || $reps === '' || $sets === '') {
        $error = 'All fields are required.';
    } elseif (!filter_var($programId, FILTER_VALIDATE_INT, array('options' => array('min_range' => 1)))) {
        $error = 'Select a valid program.';
    } elseif (strlen($name) < 2) {
        $error = 'Name must contain at least 2 characters.';
    } elseif (!filter_var($reps, FILTER_VALIDATE_INT, array('options' => array('min_range' => 1)))) {
        $error = 'Reps must be a positive integer.';
    } elseif (!filter_var($sets, FILTER_VALIDATE_INT, array('options' => array('min_range' => 1)))) {
        $error = 'Sets must be a positive integer.';
    } else {
        $updatedExercise = new Exercise((int) $programId, $name, (int) $reps, (int) $sets);
        $exerciseC->update($updatedExercise, $id);
    }

    $exercise = array('id' => $id, 'program_id' => $programId, 'name' => $name, 'reps' => $reps, 'sets' => $sets);
}
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="bg-light p-5 rounded">
                <h1 class="mb-4">Update Exercise</h1>
                <?php if ($error !== '') { ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php } ?>
                <?php if ($exercise) { ?>
                    <form id="exerciseForm" method="post" action="">
                        <input type="hidden" name="id" value="<?= htmlspecialchars($exercise['id']) ?>">
                        <div class="mb-3">
                            <label class="form-label">Program</label>
                            <select class="form-select" name="program_id" id="program_id" required>
                                <option value="">Choose a program</option>
                                <?php foreach ($programs as $program) { ?>
                                    <option value="<?= htmlspecialchars($program['id']) ?>" <?= (string) $program['id'] === (string) $exercise['program_id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($program['name']) ?> - level <?= htmlspecialchars($program['level']) ?>
                                    </option>
                                <?php } ?>
                            </select>
                            <div class="text-danger small" id="programError"></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" name="name" id="name" value="<?= htmlspecialchars($exercise['name']) ?>" required>
                            <div class="text-danger small" id="nameError"></div>
                        </div>
                        <div class="mb-3 row">
                            <div class="col-md-6">
                                <label class="form-label">Reps</label>
                                <input type="number" class="form-control" name="reps" id="reps" min="1" step="1" value="<?= htmlspecialchars($exercise['reps']) ?>" required>
                                <div class="text-danger small" id="repsError"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Sets</label>
                                <input type="number" class="form-control" name="sets" id="sets" min="1" step="1" value="<?= htmlspecialchars($exercise['sets']) ?>" required>
                                <div class="text-danger small" id="setsError"></div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Update</button>
                        <a href="exercises.php" class="btn btn-outline-secondary">Back</a>
                    </form>
                <?php } else { ?>
                    <div class="alert alert-warning">Exercise not found.</div>
                    <a href="exercises.php" class="btn btn-outline-secondary">Back</a>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

<script>
    const form = document.getElementById('exerciseForm');
    if (form) {
        form.addEventListener('submit', function(event) {
            let valid = true;
            const programId = document.getElementById('program_id').value.trim();
            const name = document.getElementById('name').value.trim();
            const reps = document.getElementById('reps').value.trim();
            const sets = document.getElementById('sets').value.trim();

            document.getElementById('programError').textContent = '';
            document.getElementById('nameError').textContent = '';
            document.getElementById('repsError').textContent = '';
            document.getElementById('setsError').textContent = '';

            if (programId === '') {
                document.getElementById('programError').textContent = 'Choose a program.';
                valid = false;
            }

            if (name.length < 2) {
                document.getElementById('nameError').textContent = 'Name must contain at least 2 characters.';
                valid = false;
            }

            if (!/^[1-9]\d*$/.test(reps)) {
                document.getElementById('repsError').textContent = 'Reps must be a positive integer.';
                valid = false;
            }

            if (!/^[1-9]\d*$/.test(sets)) {
                document.getElementById('setsError').textContent = 'Sets must be a positive integer.';
                valid = false;
            }

            if (!valid) {
                event.preventDefault();
            }
        });
    }
</script>

<?php include __DIR__ . '/partials/footer.php'; ?>