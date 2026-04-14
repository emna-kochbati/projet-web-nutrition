<?php
include __DIR__ . '/partials/header.php';
include_once __DIR__ . '/../../Controller/programC.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $level = trim($_POST['level'] ?? '');

    if ($name === '' || $description === '' || $level === '') {
        $error = 'All fields are required.';
    } elseif (strlen($name) < 2 || strlen($description) < 5) {
        $error = 'Name and description must contain enough characters.';
    } elseif (!filter_var($level, FILTER_VALIDATE_INT, array('options' => array('min_range' => 1)))) {
        $error = 'Level must be a positive integer.';
    } else {
        $program = new Program($name, $description, (int) $level);
        $programC = new ProgramC();
        $programC->create($program);
    }
}
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="bg-light p-5 rounded">
                <h1 class="mb-4">Add Program</h1>
                <?php if ($error !== '') { ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php } ?>
                <form id="programForm" method="post" action="">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" class="form-control" name="name" id="name" required>
                        <div class="text-danger small" id="nameError"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" id="description" rows="4" required></textarea>
                        <div class="text-danger small" id="descriptionError"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Level</label>
                        <input type="number" class="form-control" name="level" id="level" min="1" step="1" required>
                        <div class="text-danger small" id="levelError"></div>
                    </div>
                    <button type="submit" class="btn btn-primary">Save</button>
                    <a href="programs.php" class="btn btn-outline-secondary">Back</a>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    const form = document.getElementById('programForm');
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
</script>

<?php include __DIR__ . '/partials/footer.php'; ?>