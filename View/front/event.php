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

        <!-- Search & Statistics Bar Start -->
        <div class="row mb-5 justify-content-center align-items-center">
            <div class="col-md-6">
                <form id="search-form" onsubmit="return false;">
                    <div class="position-relative shadow-sm" style="border-radius: 30px; overflow: hidden;">
                        <input type="text" id="event-search" class="form-control border-0 py-3 ps-4 pe-5" placeholder="Search events by name, location or type..." style="border-radius: 30px;">
                        <button type="button" id="search-btn" class="btn btn-primary position-absolute top-0 end-0 h-100 px-4" style="border-radius: 0 30px 30px 0;">
                            <i class="fa fa-search"></i>
                        </button>
                    </div>
                </form>
            </div>
            <div class="col-md-auto mt-3 mt-md-0">
                <button type="button" class="btn btn-secondary py-3 px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#statsModal" style="border-radius: 30px; font-weight: 600;">
                    <i class="fa fa-chart-pie me-2"></i> View Statistics
                </button>
            </div>
        </div>
        <!-- Type Filter Box Start -->
        <div class="row mb-5">
            <div class="col-12">
                <div class="d-flex overflow-auto pb-2 filter-scroll" style="gap: 10px; white-space: nowrap;">
                    <button type="button" class="btn btn-outline-primary active type-filter" data-type="0" style="border-radius: 25px; padding: 8px 25px; font-weight: 600;">
                        All Events
                    </button>
                    <?php if (!empty($allTypes)): ?>
                        <?php foreach ($allTypes as $type): ?>
                        <button type="button" class="btn btn-outline-primary type-filter" data-type="<?= $type['id'] ?>" style="border-radius: 25px; padding: 8px 25px; font-weight: 600;">
                            <?= htmlspecialchars($type['label']) ?>
                        </button>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <!-- Type Filter Box End -->

        <div class="row g-4" id="event-container">
            <?php include 'View/front/partials/event_cards.php'; ?>
        </div>
    </div>
</div>
<!-- Events Section End -->

<!-- Statistics Modal Start -->
<div class="modal fade" id="statsModal" tabindex="-1" aria-labelledby="statsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content" style="border-radius: 20px; border: none; overflow: hidden;">
            <div class="modal-header bg-primary text-white border-0 py-3">
                <h5 class="modal-title" id="statsModalLabel"><i class="fa fa-chart-bar me-2"></i> Event Insights & Statistics</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 bg-light">
                <div class="row g-4">
                    <!-- Pie Chart: Event Types -->
                    <div class="col-lg-4">
                        <div class="card h-100 border-0 shadow-sm p-3" style="border-radius: 15px;">
                            <h6 class="text-center mb-4">Events by Category</h6>
                            <canvas id="typePieChart"></canvas>
                        </div>
                    </div>
                    <!-- Bar Chart: Locations -->
                    <div class="col-lg-4">
                        <div class="card h-100 border-0 shadow-sm p-3" style="border-radius: 15px;">
                            <h6 class="text-center mb-4">Events by Location</h6>
                            <canvas id="locationBarChart"></canvas>
                        </div>
                    </div>
                    <!-- Bar Chart: Participants -->
                    <div class="col-lg-4">
                        <div class="card h-100 border-0 shadow-sm p-3" style="border-radius: 15px;">
                            <h6 class="text-center mb-4">Most Popular Events (Participants)</h6>
                            <canvas id="participantBarChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Statistics Modal End -->

<!-- Chart.js & AJAX Script -->
<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    let searchTimeout = null;
    let selectedType = 0;

    function performSearch(page = 1) {
        const query = $('#event-search').val();
        
        $.ajax({
            url: '/2A35/Event/search',
            type: 'GET',
            data: { 
                q: query,
                type: selectedType,
                page: page
            },
            success: function(response) {
                $('#event-container').html(response);
                // Smooth scroll to results if on mobile or if page changed
                if (page > 1) {
                    $('html, body').animate({
                        scrollTop: $("#event-container").offset().top - 150
                    }, 500);
                }
            },
            error: function() {
                console.error('Error fetching search results');
            }
        });
    }

    // Handle Type Filter Click
    $('.type-filter').on('click', function() {
        $('.type-filter').removeClass('active');
        $(this).addClass('active');
        selectedType = $(this).data('type');
        performSearch(1); // Reset to page 1 on filter change
    });

    // Handle Search Input (Reset to page 1)
    $('#event-search').on('keyup', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            clearTimeout(searchTimeout);
            performSearch(1);
            return;
        }
        
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => performSearch(1), 300);
    });

    // Handle Pagination Click (Delegated because links are dynamic)
    $(document).on('click', '.pagination-link', function(e) {
        e.preventDefault();
        const page = $(this).data('page');
        if (page) {
            performSearch(page);
        }
    });

    $('#search-btn').on('click', function() {
        clearTimeout(searchTimeout);
        performSearch(1);
    });

    $('#search-form').on('submit', function(e) {
        e.preventDefault();
        return false;
    });

    // Statistics Logic
    let typeChart = null;
    let locationChart = null;
    let participantChart = null;

    $('#statsModal').on('shown.bs.modal', function () {
        $.ajax({
            url: '/2A35/Event/getStatistics',
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                renderCharts(data);
            },
            error: function() {
                console.error('Error fetching statistics');
            }
        });
    });

    function renderCharts(data) {
        // Destroy existing charts to prevent memory leaks/overlap
        if(typeChart) typeChart.destroy();
        if(locationChart) locationChart.destroy();
        if(participantChart) participantChart.destroy();

        // 1. Pie Chart: Types
        const typeCtx = document.getElementById('typePieChart').getContext('2d');
        typeChart = new Chart(typeCtx, {
            type: 'pie',
            data: {
                labels: data.types.map(t => t.label),
                datasets: [{
                    data: data.types.map(t => t.count),
                    backgroundColor: [
                        '#3cb043', // Green
                        '#ff9800', // Orange
                        '#2196f3', // Blue
                        '#9c27b0', // Purple
                        '#f44336', // Red
                        '#00bcd4', // Cyan
                        '#ffeb3b'  // Yellow
                    ],
                    borderWidth: 1
                }]
            },
            options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
        });

        // 2. Bar Chart: Locations
        const locCtx = document.getElementById('locationBarChart').getContext('2d');
        locationChart = new Chart(locCtx, {
            type: 'bar',
            data: {
                labels: data.locations.map(l => l.location),
                datasets: [{
                    label: 'Number of Events',
                    data: data.locations.map(l => l.count),
                    backgroundColor: '#3cb043'
                }]
            },
            options: { 
                responsive: true, 
                indexAxis: 'y', // Horizontal bar chart for better location names readability
                plugins: { legend: { display: false } } 
            }
        });

        // 3. Bar Chart: Participants
        const partCtx = document.getElementById('participantBarChart').getContext('2d');
        participantChart = new Chart(partCtx, {
            type: 'bar',
            data: {
                labels: data.participants.map(p => p.name.substring(0, 15) + '...'),
                datasets: [{
                    label: 'Participants',
                    data: data.participants.map(p => p.number_of_participants),
                    backgroundColor: '#2c3e50'
                }]
            },
            options: { 
                responsive: true, 
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true } }
            }
        });
    }
});
</script>

<!-- Add some quick inline custom styling to ensure hover effects work nicely -->
<style>
    .col-lg-4 .h-100:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.1) !important;
    }

    /* Hide scrollbar for Chrome, Safari and Opera */
    .filter-scroll::-webkit-scrollbar {
        display: none;
    }

    /* Hide scrollbar for IE, Edge and Firefox */
    .filter-scroll {
        -ms-overflow-style: none;  /* IE and Edge */
        scrollbar-width: none;  /* Firefox */
    }
</style>

<?php include 'View/front/partials/footer.php'; ?>
