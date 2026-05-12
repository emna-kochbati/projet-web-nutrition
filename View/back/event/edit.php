<?php 
$page_title = 'Edit Event';
ob_start();
?>
<div style="background: white; padding: 20px; border-radius: 5px; box-shadow: 0 1px 3px rgba(0,0,0,0.2); max-width: 600px;">
    <form id="eventForm" action="/2A35/Admin/evenement/update/<?= $event['id'] ?>" method="POST">
        <div style="margin-bottom: 15px;">
            <label style="display:block; margin-bottom: 5px; font-weight: bold;">Name</label>
            <input type="text" name="name" value="<?= htmlspecialchars($event['name']) ?>" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
        </div>
        <div style="margin-bottom: 15px;">
            <label style="display:block; margin-bottom: 5px; font-weight: bold;">Type</label>
            <select name="id_type" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
                <?php foreach ($types as $type): ?>
                    <option value="<?= $type['id'] ?>" <?= $type['id'] == $event['id_type'] ? 'selected' : '' ?>><?= htmlspecialchars($type['label']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div style="margin-bottom: 15px;">
            <label style="display:block; margin-bottom: 5px; font-weight: bold;">Date</label>
            <input type="date" name="date" value="<?= htmlspecialchars($event['date']) ?>" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
        </div>
        <div style="margin-bottom: 15px;">
            <label style="display:block; margin-bottom: 5px; font-weight: bold;">Location</label>
            <input type="text" name="location" value="<?= htmlspecialchars($event['location']) ?>" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
        </div>
        <div style="margin-bottom: 15px;">
            <label style="display:block; margin-bottom: 5px; font-weight: bold;">Available places</label>
            <input type="number" name="number_of_participants" value="<?= htmlspecialchars($event['number_of_participants']) ?>" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
        </div>
        <div>
            <button type="submit" style="background: #f39c12; color: white; padding: 10px 15px; border: none; border-radius: 5px; cursor: pointer;">Update Event</button>
            <a href="/2A35/Admin/evenement" style="margin-left: 10px; color: #7f8c8d; text-decoration: none;">Cancel</a>
        </div>
    </form>
</div>
<script src="/2A35/assets/js/eventValidation.js?v=<?= time() ?>"></script>
<?php
$content = ob_get_clean();
require_once 'View/back/layout.php';
?>
