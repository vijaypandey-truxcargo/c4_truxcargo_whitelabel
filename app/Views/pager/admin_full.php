<?php

use CodeIgniter\Pager\PagerRenderer;

/** @var PagerRenderer $pager */
$pager->setSurroundCount(2);
?>
<nav aria-label="Page navigation">
    <ul class="pagination">
        <?php if ($pager->hasPreviousPage()) : ?>
            <li><a href="<?= $pager->getPreviousPage(); ?>" rel="prev">Previous</a></li>
        <?php endif; ?>

        <?php foreach ($pager->links() as $link) : ?>
            <li <?= $link['active'] ? 'class="active"' : ''; ?>>
                <a href="<?= $link['uri']; ?>"><?= $link['title']; ?></a>
            </li>
        <?php endforeach; ?>

        <?php if ($pager->hasNextPage()) : ?>
            <li><a href="<?= $pager->getNextPage(); ?>" rel="next">Next</a></li>
        <?php endif; ?>
    </ul>
</nav>
