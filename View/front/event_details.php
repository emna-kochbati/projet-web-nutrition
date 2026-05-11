<?php include 'View/front/partials/header.php'; ?>

<!-- Page Header Start -->
<div class="container-fluid page-header mb-5 wow fadeIn" data-wow-delay="0.1s" style="background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url('/2A35/assets/img/carousel-1.jpg') center center no-repeat; background-size: cover; padding: 120px 0;">
    <div class="container text-center">
        <h1 class="display-3 mb-3 animated slideInDown text-white font-weight-bold"><?= htmlspecialchars($event['name']) ?></h1>
        <p class="text-white-50 fs-5 mb-4">Discover the details of this amazing event</p>
        <div class="d-flex justify-content-center gap-3">
            <span class="badge bg-primary px-3 py-2" style="border-radius: 20px;"><i class="fa fa-tag me-2"></i><?= htmlspecialchars($event['type_label']) ?></span>
            <span class="badge bg-light text-dark px-3 py-2" style="border-radius: 20px;"><i class="fa fa-map-marker-alt me-2 text-primary"></i><?= htmlspecialchars($event['location']) ?></span>
            <?php 
                $statusColor = $this->getStatusColor($event);
                $statusLabel = $this->getStatusLabel($event);
            ?>
            <span class="badge bg-<?= $statusColor ?> px-3 py-2" style="border-radius: 20px;">
                <i class="fa <?= $this->isPast($event) ? 'fa-history' : 'fa-info-circle' ?> me-2"></i><?= $statusLabel ?>
            </span>
        </div>
    </div>
</div>
<!-- Page Header End -->

<div class="container-xxl py-5">
    <div class="container">
        <div class="row g-5">
            <!-- Event Info Column -->
            <div class="col-lg-7 wow fadeInUp" data-wow-delay="0.1s">
                <div class="card border-0 shadow-sm p-4 mb-4" style="border-radius: 20px;">
                    <h3 class="mb-4 text-primary"><i class="fa fa-info-circle me-3"></i>About the Event</h3>
                    
                    <!-- AI Description Section -->
                    <div id="ai-description-container" class="mb-4">
                        <div class="p-4 bg-light" style="border-radius: 15px; border-left: 5px solid #3cb043;">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0 text-muted italic">AI Generated Description</h6>
                                <button id="generate-desc-btn" class="btn btn-sm btn-outline-primary" style="border-radius: 20px;">
                                    <i class="fa fa-sync-alt me-1"></i> Regenerate
                                </button>
                            </div>
                            <div id="ai-description-text">
                                <p class="mb-0"><?= !empty($event['description']) ? htmlspecialchars($event['description']) : 'No description available yet. Click regenerate to create one with AI.' ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="row g-4 mt-2">
                        <div class="col-sm-6">
                            <div class="d-flex align-items-center">
                                <div class="btn-square bg-primary flex-shrink-0 text-white rounded-circle">
                                    <i class="fa fa-calendar-alt"></i>
                                </div>
                                <div class="ms-3">
                                    <p class="mb-1 text-muted">Date</p>
                                    <h6 class="mb-0"><?= date('F j, Y', strtotime($event['date'])) ?></h6>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="d-flex align-items-center">
                                <div class="btn-square bg-primary flex-shrink-0 text-white rounded-circle">
                                    <i class="fa fa-users"></i>
                                </div>
                                <div class="ms-3">
                                    <p class="mb-1 text-muted">Available Places</p>
                                    <h6 class="mb-0"><?= htmlspecialchars($event['number_of_participants']) ?> People</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- AI FAQ Section -->
                <div class="card border-0 shadow-sm p-4" style="border-radius: 20px;">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h3 class="mb-0 text-primary"><i class="fa fa-question-circle me-3"></i>AI Smart FAQ</h3>
                        <button id="generate-faq-btn" class="btn btn-sm btn-primary" style="border-radius: 20px;">
                            <i class="fa fa-magic me-2"></i>Generate FAQ
                        </button>
                    </div>
                    
                    <div id="ai-faq-container" class="accordion accordion-flush" id="faqAccordion">
                        <p class="text-muted text-center py-4">Click the button above to generate smart answers for this event.</p>
                    </div>
                </div>
            </div>

            <!-- Sidebar / Action Column -->
            <div class="col-lg-5 wow fadeInUp" data-wow-delay="0.5s">
                <div class="card border-0 shadow p-4 sticky-top" style="border-radius: 20px; top: 100px; background: #fff;">
                    <div class="text-center mb-4">
                        <?php 
                            if (!empty($event['type_image'])) {
                                $path = (strpos($event['type_image'], 'assets/') === 0) ? $event['type_image'] : "assets/img/" . $event['type_image'];
                            } else {
                                $path = "assets/img/carousel-1.jpg";
                            }
                        ?>
                        <img src="/2A35/<?= $path ?>" class="img-fluid rounded mb-4 shadow-sm" style="height: 250px; width: 100%; object-fit: cover; border-radius: 15px !important;" alt="">
                        <h4 class="mb-2">Secure Your Spot</h4>
                        <p class="text-muted">Limited places available for this event. Register now to participate!</p>
                    </div>
                    
                    <?php 
                        $canRegister = $this->hasAvailablePlaces($event) && !$this->isPast($event);
                    ?>
                    <?php if ($this->isRegistered($event['id'])): ?>
                        <a href="/2A35/Event/unregister/<?= $event['id'] ?>" class="btn btn-danger w-100 py-3 mb-3" style="border-radius: 10px; font-weight: 700; font-size: 1.1rem;">
                            <i class="fa fa-times-circle me-2"></i>Unregister from Event
                        </a>
                    <?php elseif ($canRegister): ?>
                        <a href="/2A35/Event/register/<?= $event['id'] ?>" class="btn btn-primary w-100 py-3 mb-3" style="border-radius: 10px; font-weight: 700; font-size: 1.1rem;">
                            <i class="fa fa-check-circle me-2"></i>Register for Event
                        </a>
                    <?php else: ?>
                        <button class="btn btn-secondary w-100 py-3 mb-3 disabled" style="border-radius: 10px; font-weight: 700; font-size: 1.1rem; cursor: not-allowed;">
                            <i class="fa fa-times-circle me-2"></i>Registration Closed
                        </button>
                    <?php endif; ?>
                    
                    <a href="/2A35/Event/myEvents" class="btn btn-outline-primary w-100 py-2 mb-2" style="border-radius: 10px;">
                        <i class="fa fa-calendar-check me-2"></i>My Events
                    </a>
                    
                    <button class="btn btn-outline-secondary w-100 py-2" style="border-radius: 10px;">
                        <i class="fa fa-share-alt me-2"></i>Share Event
                    </button>
                    
                    <div class="mt-4 pt-4 border-top">
                        <div class="d-flex align-items-center mb-3">
                            <i class="fa fa-shield-alt text-primary me-3"></i>
                            <span>Secure registration process</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <i class="fa fa-envelope text-primary me-3"></i>
                            <span>Instant confirmation email</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    const eventId = <?= (int)$event['id'] ?>;

    // Load Initial Description
    function generateDescription() {
        $('#ai-description-text').html('<div class="spinner-border spinner-border-sm text-primary" role="status"></div> Generating...').css('opacity', '0.6');
        $.ajax({
            url: '/2A35/Event/generateDescription',
            type: 'GET',
            data: { id: eventId },
            success: function(response) {
                $('#ai-description-text').html('<p class="mb-0">' + response + '</p>').css('opacity', '1');
            },
            error: function() {
                $('#ai-description-text').html('<p class="text-danger">Failed to generate description.</p>');
            }
        });
    }

    // Generate FAQ
    function generateFaq() {
        $('#ai-faq-container').html('<div class="text-center py-4"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">AI is thinking...</p></div>');
        $.ajax({
            url: '/2A35/Event/generateFaq',
            type: 'GET',
            data: { id: eventId },
            dataType: 'json',
            success: function(response) {
                let html = '';
                response.forEach((item, index) => {
                    html += `
                        <div class="accordion-item border-0 mb-3 shadow-sm" style="border-radius: 10px; overflow: hidden;">
                            <h2 class="accordion-header" id="heading${index}">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse${index}" style="font-weight: 600; background: #fff;">
                                    ${item.q}
                                </button>
                            </h2>
                            <div id="collapse${index}" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body bg-white">
                                    ${item.a}
                                </div>
                            </div>
                        </div>
                    `;
                });
                $('#ai-faq-container').html(html);
            },
            error: function() {
                $('#ai-faq-container').html('<p class="text-danger text-center">Failed to generate FAQ.</p>');
            }
        });
    }

    // Event Listeners
    $('#generate-desc-btn').on('click', generateDescription);
    $('#generate-faq-btn').on('click', generateFaq);

    // Run automatically on load only if empty
    if ($('#ai-description-text p').text().includes('No description available')) {
        generateDescription();
    }
});
</script>

<style>
    .btn-square {
        width: 48px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .accordion-button:not(.collapsed) {
        color: #3cb043;
        box-shadow: none;
    }
    .accordion-button::after {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%232c3e50'%3e%3cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3e%3c/svg%3e");
    }
</style>

<?php include 'View/front/partials/footer.php'; ?>
