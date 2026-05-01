<?php 
$page_title = 'Edit Event Type';
ob_start();
?>
<div style="background: white; padding: 20px; border-radius: 5px; box-shadow: 0 1px 3px rgba(0,0,0,0.2); max-width: 600px;">
    <form id="eventTypeForm" action="/2A35/back/EventType/update/<?= $type['id'] ?>" method="POST" enctype="multipart/form-data">
        <div style="margin-bottom: 15px;">
            <label style="display:block; margin-bottom: 5px; font-weight: bold;">Label</label>
            <input type="text" name="label" value="<?= htmlspecialchars($type['label']) ?>" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
        </div>
        <div style="margin-bottom: 15px;">
            <label style="display:block; margin-bottom: 5px; font-weight: bold;">Type Image</label>
            <?php if (!empty($type['image'])): 
                $path = (strpos($type['image'], 'assets/') === 0) ? $type['image'] : "assets/img/" . $type['image'];
            ?>
                <div style="margin-bottom: 10px;">
                    <img src="/2A35/<?= $path ?>" alt="Current Image" style="max-width: 100px; border-radius: 4px; display: block;">
                    <small>Current Image</small>
                </div>
            <?php endif; ?>
            <input type="file" name="image" accept="image/*" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
        </div>
        <div>
            <button type="submit" style="background: #f39c12; color: white; padding: 10px 15px; border: none; border-radius: 5px; cursor: pointer;">Update Type</button>
            <a href="/2A35/back/EventType" style="margin-left: 10px; color: #7f8c8d; text-decoration: none;">Cancel</a>
        </div>
    </form>
</div>
<script src="/2A35/assets/js/eventTypeValidation.js"></script>
<?php
$content = ob_get_clean();
require_once 'View/back/layout.php';
?>
