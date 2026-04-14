<?php 
$page_title = 'Events Management';
ob_start();
?>
<div class="header-actions" style="margin-bottom: 20px;">
    <a href="/2A35/back/Event/create" class="btn btn-primary" style="background:#3498db;color:white;padding:10px 15px;text-decoration:none;border-radius:5px;">+ Add New Event</a>
</div>

<table style="width: 100%; border-collapse: collapse; background: white; box-shadow: 0 1px 3px rgba(0,0,0,0.2);">
    <thead style="background: #2c3e50; color: white;">
        <tr>
            <th style="padding: 12px; text-align: left;">ID</th>
            <th style="padding: 12px; text-align: left;">Name</th>
            <th style="padding: 12px; text-align: left;">Type</th>
            <th style="padding: 12px; text-align: left;">Date</th>
            <th style="padding: 12px; text-align: left;">Location</th>
            <th style="padding: 12px; text-align: left;">Participants</th>
            <th style="padding: 12px; text-align: left;">Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($events as $event): ?>
        <tr style="border-bottom: 1px solid #eee;">
            <td style="padding: 12px;"><?= htmlspecialchars($event['id']) ?></td>
            <td style="padding: 12px;"><?= htmlspecialchars($event['name']) ?></td>
            <td style="padding: 12px;"><?= htmlspecialchars($event['type_label']) ?></td>
            <td style="padding: 12px;"><?= htmlspecialchars($event['date']) ?></td>
            <td style="padding: 12px;"><?= htmlspecialchars($event['location']) ?></td>
            <td style="padding: 12px;"><?= htmlspecialchars($event['number_of_participants']) ?></td>
            <td style="padding: 12px;">
                <a href="/2A35/back/Event/edit/<?= $event['id'] ?>" style="color: #f39c12; text-decoration: none; margin-right: 10px;">Edit</a>
                <a href="/2A35/back/Event/delete/<?= $event['id'] ?>" style="color: #e74c3c; text-decoration: none;" onclick="return confirm('Are you sure?')">Delete</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php
$content = ob_get_clean();
require_once 'View/back/layout.php';
?>
