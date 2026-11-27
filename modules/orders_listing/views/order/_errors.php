<?php

use app\modules\orders_listing\models\GetOrdersFilter;
use yii\helpers\Html;

/** @var GetOrdersFilter $filter */
?>
<div class="alert alert-danger" role="alert">
    <ul class="list-unstyled m-a-0">
        <?php foreach ($filter->getErrorMessages() as $message): ?>
            <li><?= Html::encode($message) ?></li>
        <?php endforeach ?>
    </ul>
</div>
