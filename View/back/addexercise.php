<?php
include_once __DIR__ . '/../../Controller/exerciseC.php';
include_once __DIR__ . '/../../Controller/programC.php';

$programC = new ProgramC();
$programs = $programC->read();
$error = '';
$selectedProgram = $_GET['program'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $programId = trim($_POST['program_id'] ?? '');
    $name = trim($_POST['name'] ?? '');
    $reps = trim($_POST['reps'] ?? '');
    $sets = trim($_POST['sets'] ?? '');

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
        (new ExerciseC())->create(new Exercise((int) $programId, $name, (int) $reps, (int) $sets));
    }
}

$pageTitle = 'Add exercise';
$pageHeading = 'Add exercise';
include __DIR__ . '/partials/header.php';
?>
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title fw-semibold mb-4">Create exercise</h5>
                <?php if ($error !== '') { ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php } ?>
                <form method="post" id="exerciseForm">
                    <div class="mb-3">
                        <label class="form-label">Program</label>
                        <select name="program_id" id="program_id" class="form-control">
                            <option value="">Choose a program</option>
                            <?php foreach ($programs as $program) { ?>
                                <option value="<?= htmlspecialchars($program['id']) ?>" <?= (string) $selectedProgram === (string) $program['id'] ? 'selected' : '' ?>><?= htmlspecialchars($program['name']) ?></option>
                            <?php } ?>
                        </select>
                        <div class="text-danger small" id="programError"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" id="name" class="form-control">
                        <div class="text-danger small" id="nameError"></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Reps</label>
                            <input type="number" name="reps" id="reps" class="form-control">
                            <div class="text-danger small" id="repsError"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Sets</label>
                            <input type="number" name="sets" id="sets" class="form-control">
                            <div class="text-danger small" id="setsError"></div>
                        </div>
                    </div>
                    <button class="btn btn-primary" type="submit">Save</button>
                    <a class="btn btn-secondary" href="exercises.php<?= $selectedProgram ? '?program=' . urlencode($selectedProgram) : '' ?>">Back</a>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    const form = document.getElementById('exerciseForm');
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
</script>
<?php include __DIR__ . '/partials/footer.php'; ?>