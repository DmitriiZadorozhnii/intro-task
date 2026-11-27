<?php

use yii\helpers\Html;

/** @var string $id */
/** @var string $label */
/** @var array $items */
?>
<div class="dropdown">
    <button class="btn btn-th btn-default dropdown-toggle" type="button" id="<?= Html::encode($id) ?>"
            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <?= Html::encode($label) ?>
        <span class="caret"></span>
    </button>
    <ul class="dropdown-menu" aria-labelledby="<?= Html::encode($id) ?>">
        <?php foreach ($items as $item): ?>
            <?php
            $classes = trim(
                (($item['active'] ?? false) ? 'active' : '') . ' ' . (($item['disabled'] ?? false) ? 'disabled' : '')
            );
            ?>
            <li<?= $classes !== '' ? ' class="' . $classes . '"' : '' ?>>
                <a href="<?= Html::encode($item['url']) ?>"<?= ($item['disabled'] ?? false) ? ' tabindex="-1" aria-disabled="true"' : '' ?>>
                    <?php if (($item['prefix'] ?? null) !== null): ?>
                        <span class="label-id"><?= Html::encode($item['prefix']) ?></span>
                    <?php endif ?>
                    <?= Html::encode($item['label']) ?>
                </a>
            </li>
        <?php endforeach ?>
    </ul>
</div>
