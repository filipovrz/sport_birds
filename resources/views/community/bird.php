<p><a href="/community">← Общност</a> · <a href="/community/users/<?= (int)$bird['owner_id'] ?>"><?= htmlspecialchars($bird['owner_name']) ?></a></p>
<div class="card">
    <div style="display:flex;gap:1.5rem;flex-wrap:wrap">
        <?php if (!empty($bird['photo_path'])): ?>
        <img src="<?= htmlspecialchars($bird['photo_path']) ?>" alt="" style="width:200px;height:200px;object-fit:cover;border-radius:10px">
        <?php endif; ?>
        <div>
            <h1><?= htmlspecialchars($bird['ring_number']) ?> <?= $bird['name'] ? '— ' . htmlspecialchars($bird['name']) : '' ?></h1>
            <p><strong>Собственик:</strong> <a href="/community/users/<?= (int)$bird['owner_id'] ?>"><?= htmlspecialchars($bird['owner_name']) ?></a><?= $bird['owner_club'] ? ' · ' . htmlspecialchars($bird['owner_club']) : '' ?></p>
            <p><strong>Вид:</strong> <?= species_label($bird['species']) ?> · <strong>Пол:</strong> <?= sex_label($bird['sex']) ?> · <strong>Статус:</strong> <?= status_label($bird['status']) ?></p>
            <?php if ($bird['loft_name']): ?><p><strong>Гълъбарник:</strong> <?= htmlspecialchars($bird['loft_name']) ?></p><?php endif; ?>
            <?php if ($bird['strain']): ?><p><strong>Линия:</strong> <?= htmlspecialchars($bird['strain']) ?></p><?php endif; ?>
            <?php if ($bird['color']): ?><p><strong>Цвят:</strong> <?= htmlspecialchars($bird['color']) ?></p><?php endif; ?>
            <?php if ($bird['birth_date']): ?><p><strong>Роден:</strong> <?= date('d.m.Y', strtotime($bird['birth_date'])) ?></p><?php endif; ?>
            <?php if ($bird['acquisition_date']): ?><p><strong>Придобит:</strong> <?= date('d.m.Y', strtotime($bird['acquisition_date'])) ?></p><?php endif; ?>
            <?php if ($bird['achievements']): ?><p><strong>Постижения:</strong><br><?= nl2br(htmlspecialchars($bird['achievements'])) ?></p><?php endif; ?>
            <?php if ($bird['notes']): ?><p><strong>Бележки:</strong><br><?= nl2br(htmlspecialchars($bird['notes'])) ?></p><?php endif; ?>
            <?php if ($father || $mother): ?>
            <p><strong>Родители:</strong>
                <?php if ($father): ?><a href="/community/birds/<?= (int)$father['id'] ?>"><?= htmlspecialchars($father['ring_number']) ?></a><?php endif; ?>
                <?php if ($father && $mother): ?> · <?php endif; ?>
                <?php if ($mother): ?><a href="/community/birds/<?= (int)$mother['id'] ?>"><?= htmlspecialchars($mother['ring_number']) ?></a><?php endif; ?>
            </p>
            <?php endif; ?>
            <p style="margin-top:1rem">
                <?php if (!empty($bird['is_public_pedigree'])): ?>
                <a href="/pedigree/public/<?= (int)$bird['id'] ?>" class="btn btn-outline btn-sm" target="_blank">Публично родословие</a>
                <?php endif; ?>
                <?php if ($isOwner): ?>
                <a href="/dashboard/birds/<?= (int)$bird['id'] ?>" class="btn btn-primary btn-sm">Моята птица</a>
                <?php endif; ?>
            </p>
        </div>
    </div>
</div>
