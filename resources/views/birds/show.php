<div class="card">
    <div style="display:flex;gap:1.5rem;flex-wrap:wrap">
        <?php if (!empty($bird['photo_path'])): ?>
            <img src="<?= htmlspecialchars($bird['photo_path']) ?>" alt="" style="width:180px;height:180px;object-fit:cover;border-radius:10px">
        <?php endif; ?>
        <div>
            <h1><?= htmlspecialchars($bird['ring_number']) ?> <?= $bird['name'] ? '— '.htmlspecialchars($bird['name']) : '' ?></h1>
            <p><strong>Вид:</strong> <?= species_label($bird['species']) ?> | <strong>Пол:</strong> <?= sex_label($bird['sex']) ?> | <strong>Статус:</strong> <?= status_label($bird['status']) ?></p>
            <?php if ($bird['strain']): ?><p><strong>Линия:</strong> <?= htmlspecialchars($bird['strain']) ?></p><?php endif; ?>
            <p>
                <a href="/dashboard/birds/<?= (int)$bird['id'] ?>/edit" class="btn btn-outline btn-sm">Редакция</a>
                <a href="/dashboard/birds/<?= (int)$bird['id'] ?>/pedigree" class="btn btn-primary btn-sm">Родословие</a>
            </p>
        </div>
    </div>
</div>
<form method="post" action="/dashboard/birds/<?= (int)$bird['id'] ?>/delete" onsubmit="return confirm('Сигурни ли сте?')">
    <?= csrf_field() ?>
    <button type="submit" class="btn btn-danger btn-sm">Изтрий птицата</button>
</form>
