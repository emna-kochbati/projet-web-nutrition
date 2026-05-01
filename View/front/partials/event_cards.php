<?php 
$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
?>
<?php if (!empty($events)): ?>
    <?php foreach ($events as $event): ?>
    <div class="col-lg-4 col-md-6 <?= $isAjax ? '' : 'wow fadeInUp' ?>" data-wow-delay="0.1s">
        <div class="h-100 rounded overflow-hidden" style="background: #fff; box-shadow: 0 5px 20px rgba(0,0,0,0.05); transition: transform 0.3s ease; border: 1px solid #f0f0f0;">
            <!-- Top decorative banner -->
            <?php 
                if (!empty($event['type_image'])) {
                    // Check if the image path already includes 'assets/'
                    $path = (strpos($event['type_image'], 'assets/') === 0) ? $event['type_image'] : "assets/img/" . $event['type_image'];
                    $bannerImage = "url('/2A35/" . $path . "')";
                } else {
                    $bannerImage = "linear-gradient(135deg, #3cb043, #72c875)";
                }
            ?>
            <div style="height: 120px; background: <?= $bannerImage ?> center/cover no-repeat; position: relative;">
                <span class="badge bg-white text-success position-absolute" style="top: 15px; left: 15px; padding: 8px 15px; font-size: 14px; border-radius: 20px;">
                    <i class="fa fa-tag me-2"></i><?= htmlspecialchars($event['type_label']) ?>
                </span>
            </div>
            
            <div class="p-4" style="position: relative; margin-top: -30px; background: white; border-radius: 15px 15px 0 0;">
                <h4 class="mb-3">
                    <a href="/2A35/Event/show/<?= $event['id'] ?>" style="color: #2c3e50; font-weight: 700; text-decoration: none; transition: color 0.3s;" onmouseover="this.style.color='#3cb043'" onmouseout="this.style.color='#2c3e50'">
                        <?= htmlspecialchars($event['name']) ?>
                    </a>
                </h4>
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
                
                <div class="d-flex gap-2">
                    <a class="btn btn-outline-primary py-2 px-3 flex-grow-1" href="/2A35/Event/show/<?= $event['id'] ?>" style="border-radius: 5px; font-weight: bold;">Details</a>
                    <a class="btn btn-primary py-2 px-3 flex-grow-1" href="/2A35/Event/register/<?= $event['id'] ?>" style="border-radius: 5px; font-weight: bold;">Register</a>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
<?php else: ?>
    <div class="col-12 text-center py-5">
        <div style="background: #fff; box-shadow: 0 5px 20px rgba(0,0,0,0.05); padding: 50px; border-radius: 10px;">
            <i class="fa fa-calendar-times text-muted mb-4" style="font-size: 48px;"></i>
            <h3 class="text-muted">No events found matching your search.</h3>
            <p>Try different keywords or check back later!</p>
        </div>
    </div>
<?php endif; ?>

<!-- Pagination Links -->
<?php if ($totalPages > 1): ?>
<div class="col-12 text-center wow fadeInUp" data-wow-delay="0.1s">
    <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center mb-0 mt-4">
            <!-- Previous Page -->
            <li class="page-item <?= ($currentPage <= 1) ? 'disabled' : '' ?>">
                <a class="page-link pagination-link" href="#" data-page="<?= $currentPage - 1 ?>" aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>

            <!-- Page Numbers -->
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?= ($currentPage == $i) ? 'active' : '' ?>">
                    <a class="page-link pagination-link" href="#" data-page="<?= $i ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>

            <!-- Next Page -->
            <li class="page-item <?= ($currentPage >= $totalPages) ? 'disabled' : '' ?>">
                <a class="page-link pagination-link" href="#" data-page="<?= $currentPage + 1 ?>" aria-label="Next">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
        </ul>
    </nav>
</div>
<?php endif; ?>

<style>
    .pagination .page-link {
        color: #3cb043;
        border-radius: 5px;
        margin: 0 3px;
        border: 1px solid #f0f0f0;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    .pagination .page-item.active .page-link {
        background-color: #3cb043;
        border-color: #3cb043;
        color: #fff;
    }
    .pagination .page-link:hover {
        background-color: #f8f9fa;
        border-color: #3cb043;
        color: #3cb043;
    }
    .pagination .page-item.disabled .page-link {
        color: #ccc;
    }
</style>
