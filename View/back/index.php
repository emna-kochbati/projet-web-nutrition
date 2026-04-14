<?php
$pageTitle = 'Admin Dashboard';
$pageHeading = 'Dashboard';
include __DIR__ . '/partials/header.php';
?>
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title fw-semibold mb-4">Admin dashboard</h5>
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="card-title">Programs</h6>
                                <p class="card-text">Create, edit, delete and inspect exercises for each program.</p>
                                <a href="programs.php" class="btn btn-primary">Open programs</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="card-title">Exercises</h6>
                                <p class="card-text">Manage exercises directly or from a program filter.</p>
                                <a href="exercises.php" class="btn btn-primary">Open exercises</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include __DIR__ . '/partials/footer.php'; ?>