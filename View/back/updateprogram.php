<?php
include_once __DIR__ . '/../../Controller/programC.php';

$programC = new ProgramC();
$program = null;
$error = '';
$id = $_GET['update'] ?? null;

if ($id !== null) {
    $program = $programC->findone($id);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $level = trim($_POST['level'] ?? '');
    $id = $_POST['id'] ?? $id;

    if ($name === '' || $description === '' || $level === '') {
        $error = 'All fields are required.';
    } elseif (strlen($name) < 2 || strlen($description) < 5) {
        $error = 'Name and description must contain enough characters.';
    } elseif (!filter_var($level, FILTER_VALIDATE_INT, array('options' => array('min_range' => 1, 'max_range' => 10)))) {
        $error = 'Level must be a number between 1 and 10.';
    } else {
        $programC->update(new Program($name, $description, (int) $level), $id);
    }

    $program = array('id' => $id, 'name' => $name, 'description' => $description, 'level' => $level);
}

$pageTitle = 'Update program';
$pageHeading = 'Update program';
include __DIR__ . '/partials/header.php';
?>
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title fw-semibold mb-4">Update program</h5>
                <?php if ($error !== '') { ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php } ?>
                <?php if ($program) { ?>
                    <form method="post" id="programForm">
                        <input type="hidden" name="id" value="<?= htmlspecialchars($program['id']) ?>">
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" name="name" id="name" class="form-control" value="<?= htmlspecialchars($program['name']) ?>">
                            <div class="text-danger small" id="nameError"></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" id="description" class="form-control" rows="4"><?= htmlspecialchars($program['description']) ?></textarea>
                            <div class="text-danger small" id="descriptionError"></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Level</label>
                            <input type="text" name="level" id="level" class="form-control" value="<?= htmlspecialchars($program['level']) ?>">
                            <div class="text-danger small" id="levelError"></div>
                        </div>
                        <button class="btn btn-primary" type="submit">Update</button>
                        <a class="btn btn-secondary" href="programs.php">Back</a>
                    </form>
                <?php } else { ?>
                    <div class="alert alert-warning">Program not found.</div>
                    <a class="btn btn-secondary" href="programs.php">Back</a>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
<script>
    const form = document.getElementById('programForm');
    if (form) {
        form.addEventListener('submit', function(event) {
            let valid = true;
            const name = document.getElementById('name').value.trim();
            const description = document.getElementById('description').value.trim();
            const level = document.getElementById('level').value.trim();

            document.getElementById('nameError').textContent = '';
            document.getElementById('descriptionError').textContent = '';
            document.getElementById('levelError').textContent = '';

            if (name.length < 2) {
                document.getElementById('nameError').textContent = 'Name must contain at least 2 characters.';
                valid = false;
            }
            if (description.length < 5) {
                document.getElementById('descriptionError').textContent = 'Description must contain at least 5 characters.';
                valid = false;
            }
            if (!/^(10|[1-9])$/.test(level)) {
                document.getElementById('levelError').textContent = 'Pick a number between 1 and 10.';
                valid = false;
            }
            if (!valid) {
                event.preventDefault();
            }
        });

        document.getElementById('level').addEventListener('input', function() {
            const level = this.value.trim();
            if (level === '' || /^(10|[1-9])$/.test(level)) {
                document.getElementById('levelError').textContent = '';
                return;
            }
            document.getElementById('levelError').textContent = 'Pick a number between 1 and 10.';
        });
    }
</script>
<?php include __DIR__ . '/partials/footer.php'; ?>