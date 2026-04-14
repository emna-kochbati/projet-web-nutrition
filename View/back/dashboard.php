<?php 
$page_title = 'Dashboard';
ob_start();
?>

<!-- Blank Page Content -->

<?php
$content = ob_get_clean();
require_once 'View/back/layout.php';
?>
