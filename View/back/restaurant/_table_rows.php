<?php if (empty($restaurants)): ?>
    <p style="text-align:center;color:#888;padding:40px;">Aucun restaurant trouvé.</p>
<?php else: ?>
<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Image</th>
            <th>Nom</th>
            <th>Adresse</th>
            <th>Cuisine</th>
            <th>Téléphone</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($restaurants as $r): ?>
        <tr>
            <td><?= $r['id'] ?></td>
            <td>
                <?php if ($r['image']): ?>
                    <img src="/2A35/assets/uploads/restaurants/<?= htmlspecialchars($r['image']) ?>" class="img-thumb" alt="">
                <?php else: ?>
                    <div class="no-img">🍴</div>
                <?php endif; ?>
            </td>
            <td><strong><?= htmlspecialchars($r['nom']) ?></strong></td>
            <td><?= htmlspecialchars($r['adresse']) ?></td>
            <td><span class="badge badge-cuisine"><?= htmlspecialchars($r['type_cuisine']) ?></span></td>
            <td><?= htmlspecialchars($r['telephone'] ?? '—') ?></td>
            <td>
                <div class="actions">
                    <a href="/2A35/Admin/restaurant/show/<?= $r['id'] ?>" class="btn-show">👁 Voir</a>
                    <a href="/2A35/Admin/restaurant/edit/<?= $r['id'] ?>" class="btn-edit">✏️ Modifier</a>
                    <form method="POST" action="/2A35/Admin/restaurant/delete/<?= $r['id'] ?>" onsubmit="return confirm('Supprimer ce restaurant ?')">
                        <button type="submit" class="btn-danger">🗑 Supprimer</button>
                    </form>
                </div>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>
