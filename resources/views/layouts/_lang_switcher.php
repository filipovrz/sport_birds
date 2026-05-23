<?php
/** @var string|null $variant inline|header */
$variant = $variant ?? 'inline';
$current = locale();
$other = $current === 'bg' ? 'en' : 'bg';
$return = $_SERVER['REQUEST_URI'] ?? '/';
?>
<span class="lang-switcher lang-switcher--<?= htmlspecialchars($variant) ?>">
    <a href="/locale/<?= $other ?>?return=<?= rawurlencode($return) ?>" class="lang-switcher__link" hreflang="<?= $other ?>" title="<?= htmlspecialchars(__('lang.switch')) ?>">
        <?= $current === 'bg' ? 'EN' : 'BG' ?>
    </a>
</span>
