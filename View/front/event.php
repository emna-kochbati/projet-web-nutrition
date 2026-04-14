<?php include 'View/front/partials/header.php'; ?>

<!-- Page Header Start -->
<div class="container-fluid page-header mb-5 wow fadeIn" data-wow-delay="0.1s" style="background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('/2A35/assets/img/carousel-1.jpg') center center no-repeat; background-size: cover; padding: 100px 0;">
    <div class="container">
        <h1 class="display-3 mb-3 animated slideInDown text-white font-weight-bold">Upcoming Events</h1>
        <nav aria-label="breadcrumb" class="animated slideInDown">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a class="text-body" style="color: #fff !important;" href="/2A35/Home">Home</a></li>
                <li class="breadcrumb-item"><a class="text-body" style="color: #fff !important;" href="#">Pages</a></li>
                <li class="breadcrumb-item text-white active" aria-current="page">Events</li>
            </ol>
        </nav>
    </div>
</div>
<!-- Page Header End -->

<!-- Events Section Start -->
<div class="container-xxl py-5">
    <div class="container">
        <div class="section-header text-center mx-auto mb-5 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 500px;">
            <h1 class="display-5 mb-3">Our Upcoming Events</h1>
            <p>Join us at our upcoming agricultural, foody, and networking events around the world! Connect, learn, and grow together.</p>
        </div>

        <div class="row g-4">
            <?php if (!empty($events)): ?>
                <?php foreach ($events as $event): ?>
                <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                    <div class="h-100 rounded overflow-hidden" style="background: #fff; box-shadow: 0 5px 20px rgba(0,0,0,0.05); transition: transform 0.3s ease; border: 1px solid #f0f0f0;">
                        <!-- Dynamic Photo Logic Example (Commented Out) -->
                        <?php 
                        /* 
                        // You can use this logic to show a photo based on the type
                        $bgImage = '';
                        $typeStr = strtolower(trim($event['type_label']));
                        if ($typeStr === 'Sport') {
                            $bgImage = "url('/2A35/assets/img/Sport.jpg')";
                        } elseif ($typeStr === 'Food') {
                            $bgImage = "url('/2A35/assets/img/Food.jpg')";
                        } elseif ($typeStr === 'Networking') {
                            $bgImage = "url('/2A35/assets/img/Networking.jpg')";
                        } else {
                            $bgImage = "url('/2A35/assets/img/default-event.jpg')";
                        }
                        */
                        ?>

                        <!-- Top decorative banner -->
                        <!-- To use the dynamic image, you would replace the background property below with: background: <?= $bgImage ?> center/cover no-repeat; -->
                        <div style="height: 120px; background: linear-gradient(135deg, #3cb043, #72c875); position: relative;">
                            <span class="badge bg-white text-success position-absolute" style="top: 15px; left: 15px; padding: 8px 15px; font-size: 14px; border-radius: 20px;">
                                <i class="fa fa-tag me-2"></i><?= htmlspecialchars($event['type_label']) ?>
                            </span>
                        </div>
                        
                        <div class="p-4" style="position: relative; margin-top: -30px; background: white; border-radius: 15px 15px 0 0;">
                            <h4 class="mb-3" style="color: #2c3e50; font-weight: 700;"><?= htmlspecialchars($event['name']) ?></h4>
                            <div class="d-flex flex-column mb-4" style="color: #7f8c8d; font-size: 15px;">
                                <div class="mb-2">
                                    <i class="fa fa-calendar-alt text-primary me-2" style="width: 20px; text-align: center;"></i>
                                    <strong>Date:</strong> <?= date('F j, Y', strtotime($event['date'])) ?>
                                </div>
                                <div class="mb-2">
                                    <i class="fa fa-map-marker-alt text-primary me-2" style="width: 20px; text-align: center;"></i>
                                    <strong>Location:</strong> <?= htmlspecialchars($event['location']) ?>
                                </div>
                                <div class="mb-2">
                                    <i class="fa fa-users text-primary me-2" style="width: 20px; text-align: center;"></i>
                                    <strong>Capacity:</strong> <?= htmlspecialchars($event['number_of_participants']) ?> People
                                </div>
                            </div>
                            
                            <a class="btn btn-outline-primary py-2 px-4 w-100" href="/2A35/Event/register/<?= $event['id'] ?>" style="border-radius: 5px; font-weight: bold;">Register Now</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <div style="background: #fff; box-shadow: 0 5px 20px rgba(0,0,0,0.05); padding: 50px; border-radius: 10px;">
                        <i class="fa fa-calendar-times text-muted mb-4" style="font-size: 48px;"></i>
                        <h3 class="text-muted">No events currently scheduled.</h3>
                        <p>Check back later for exciting upcoming events!</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<!-- Events Section End -->

<!-- Add some quick inline custom styling to ensure hover effects work nicely -->
<style>
    .col-lg-4 .h-100:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.1) !important;
    }
</style>

<?php include 'View/front/partials/footer.php'; ?>
