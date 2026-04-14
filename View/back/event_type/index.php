<?php 
$page_title = 'Event Types Management';
ob_start();
?>
<div class="header-actions" style="margin-bottom: 20px;">
    <a href="/2A35/back/EventType/create" class="btn btn-primary" style="background:#3498db;color:white;padding:10px 15px;text-decoration:none;border-radius:5px;">+ Add New Type</a>
</div>

<table style="width: 100%; border-collapse: collapse; background: white; box-shadow: 0 1px 3px rgba(0,0,0,0.2);">
    <thead style="background: #2c3e50; color: white;">
        <tr>
            <th style="padding: 12px; text-align: left;">ID</th>
            <th style="padding: 12px; text-align: left;">Label</th>
            <th style="padding: 12px; text-align: left;">Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($types as $type): ?>
        <tr style="border-bottom: 1px solid #eee;">
            <td style="padding: 12px;"><?= htmlspecialchars($type['id']) ?></td>
            <td style="padding: 12px;"><?= htmlspecialchars($type['label']) ?></td>
            <td style="padding: 12px;">
                <a href="/2A35/back/EventType/edit/<?= $type['id'] ?>" style="color: #f39c12; text-decoration: none; margin-right: 10px;">Edit</a>
                <a href="/2A35/back/EventType/delete/<?= $type['id'] ?>" style="color: #e74c3c; text-decoration: none;" onclick="return confirm('Are you sure? This might fail if types are used in events.')">Delete</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php
$content = ob_get_clean();
require_once 'View/back/layout.php';
?>
