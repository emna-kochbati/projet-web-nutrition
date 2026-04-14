<?php
include __DIR__ . '/partials/header.php';
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
    } elseif (!filter_var($level, FILTER_VALIDATE_INT, array('options' => array('min_range' => 1)))) {
        $error = 'Level must be a positive integer.';
    } else {
        $updatedProgram = new Program($name, $description, (int) $level);
        $programC->update($updatedProgram, $id);
    }

    $program = array('id' => $id, 'name' => $name, 'description' => $description, 'level' => $level);
}
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="bg-light p-5 rounded">
                <h1 class="mb-4">Update Program</h1>
                <?php if ($error !== '') { ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php } ?>
                <?php if ($program) { ?>
                    <form id="programForm" method="post" action="">
                        <input type="hidden" name="id" value="<?= htmlspecialchars($program['id']) ?>">
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" name="name" id="name" value="<?= htmlspecialchars($program['name']) ?>" required>
                            <div class="text-danger small" id="nameError"></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" id="description" rows="4" required><?= htmlspecialchars($program['description']) ?></textarea>
                            <div class="text-danger small" id="descriptionError"></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Level</label>
                            <input type="number" class="form-control" name="level" id="level" min="1" step="1" value="<?= htmlspecialchars($program['level']) ?>" required>
                            <div class="text-danger small" id="levelError"></div>
                        </div>
                        <button type="submit" class="btn btn-primary">Update</button>
                        <a href="programs.php" class="btn btn-outline-secondary">Back</a>
                    </form>
                <?php } else { ?>
                    <div class="alert alert-warning">Program not found.</div>
                    <a href="programs.php" class="btn btn-outline-secondary">Back</a>
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

            if (!/^[1-9]\d*$/.test(level)) {
                document.getElementById('levelError').textContent = 'Level must be a positive integer.';
                valid = false;
            }

            if (!valid) {
                event.preventDefault();
            }
        });
    }
</script>

<?php include __DIR__ . '/partials/footer.php'; ?>