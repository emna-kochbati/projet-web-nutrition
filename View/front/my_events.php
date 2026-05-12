<?php include 'View/front/partials/header.php'; ?>

<!-- Page Header Start -->
<div class="container-fluid page-header mb-5 wow fadeIn" data-wow-delay="0.1s" style="background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url('/2A35/assets/img/carousel-1.jpg') center center no-repeat; background-size: cover; padding: 100px 0;">
    <div class="container text-center">
        <h1 class="display-3 mb-3 animated slideInDown text-white font-weight-bold">My Registered Events</h1>
        <p class="text-white-50 mb-0">Track all your upcoming and past event participations</p>
    </div>
</div>
<!-- Page Header End -->

<div class="container-xxl py-5">
    <div class="container">
        <?php if (empty($registrations)): ?>
            <div class="text-center py-5 wow fadeInUp" data-wow-delay="0.1s">
                <div class="mb-4">
                    <i class="fa fa-calendar-times fa-4x text-primary opacity-25"></i>
                </div>
                <h3 class="mb-3">No registrations found</h3>
                <p class="text-muted mb-4">You haven't registered for any events yet. Check out our upcoming events!</p>
                <a href="/2A35/Event" class="btn btn-primary py-3 px-5" style="border-radius: 30px;">Browse Events</a>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($registrations as $event): ?>
                    <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                        <div class="event-item bg-white shadow-sm h-100" style="border-radius: 20px; overflow: hidden; transition: 0.3s; border: 1px solid #eee;">
                            <div class="position-relative">
                                <?php 
                                    $imagePath = !empty($event['type_image']) ? $event['type_image'] : 'carousel-1.jpg';
                                    if (strpos($imagePath, 'assets/') !== 0) $imagePath = 'assets/img/' . $imagePath;
                                ?>
                                <img class="img-fluid w-100" src="/2A35/<?= $imagePath ?>" alt="<?= htmlspecialchars($event['name']) ?>" style="height: 200px; object-fit: cover;">
                                <div class="bg-primary text-white fw-bold position-absolute top-0 start-0 m-3 py-1 px-3" style="border-radius: 20px; font-size: 12px;">
                                    <?= htmlspecialchars($event['type_label']) ?>
                                </div>
                            </div>
                            <div class="p-4">
                                <div class="d-flex align-items-center mb-2">
                                    <small class="text-primary me-3"><i class="fa fa-calendar-alt me-2"></i><?= date('d M, Y', strtotime($event['date'])) ?></small>
                                    <small class="text-primary"><i class="fa fa-map-marker-alt me-2"></i><?= htmlspecialchars($event['location']) ?></small>
                                </div>
                                <h5 class="mb-3"><?= htmlspecialchars($event['name']) ?></h5>
                                <div class="bg-light p-2 mb-3" style="border-radius: 10px; font-size: 13px;">
                                    <span class="text-muted"><i class="fa fa-check-circle text-success me-2"></i>Registered on: <?= date('d/m/Y H:i', strtotime($event['registration_date'])) ?></span>
                                </div>
                                <div class="d-flex gap-2 mt-auto">
                                    <a href="/2A35/Event/show/<?= $event['id'] ?>" class="btn btn-outline-primary px-3 flex-grow-1" style="border-radius: 20px; font-size: 13px;">View Details</a>
                                    <a href="/2A35/Event/unregister/<?= $event['id'] ?>" class="btn btn-danger px-3 flex-grow-1" style="border-radius: 20px; font-size: 13px;" onclick="return confirm('Etes-vous sûr de vouloir vous désinscrire de cet événement ?')">Unregister</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
    .event-item:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.1) !important;
    }
</style>

<?php include 'View/front/partials/footer.php'; ?>
