<div class="card">
    <h1><?= htmlspecialchars($bird['ring_number']) ?> <?= $bird['name'] ? '— '.htmlspecialchars($bird['name']) : '' ?></h1>
    <p><strong>Вид:</strong> <?= species_label($bird['species']) ?> | <strong>Пол:</strong> <?= sex_label($bird['sex']) ?> | <strong>Статус:</strong> <?= status_label($bird['status']) ?></p>
    <p><a href="/dashboard/birds/<?= (int)$bird['id'] ?>/edit" class="btn btn-outline btn-sm">Редакция</a>
    <a href="/dashboard/birds/<?= (int)$bird['id'] ?>/pedigree" class="btn btn-primary btn-sm">Родословна</a></p>
</div>
