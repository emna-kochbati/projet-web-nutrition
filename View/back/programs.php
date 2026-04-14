<?php
include_once __DIR__ . '/../../Controller/programC.php';
include_once __DIR__ . '/../../Controller/exerciseC.php';

$programC = new ProgramC();

if (isset($_GET['delete'])) {
    $programC->delete();
}

$programs = $programC->read();

$pageTitle = 'Programs';
$pageHeading = 'Programs';
include __DIR__ . '/partials/header.php';
?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="card-title fw-semibold mb-0">Manage programs</h5>
                    <a href="addprogram.php" class="btn btn-primary">Add program</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Level</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($programs as $program) { ?>
                                <tr>
                                    <td><?= htmlspecialchars($program['id']) ?></td>
                                    <td><?= htmlspecialchars($program['name']) ?></td>
                                    <td><?= htmlspecialchars($program['description']) ?></td>
                                    <td><?= htmlspecialchars($program['level']) ?></td>
                                    <td class="text-end">
                                        <a class="btn btn-sm btn-info" href="exercises.php?program=<?= urlencode($program['id']) ?>">View exercises</a>
                                        <a class="btn btn-sm btn-warning" href="updateprogram.php?update=<?= urlencode($program['id']) ?>">Edit</a>
                                        <a class="btn btn-sm btn-danger" href="programs.php?delete=<?= urlencode($program['id']) ?>" onclick="return confirm('Delete this program?')">Delete</a>
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