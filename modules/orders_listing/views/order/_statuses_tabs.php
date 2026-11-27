<?php

use app\modules\orders_listing\enums\order\OrderStatusEnum;
use app\modules\orders_listing\models\GetOrdersFilter;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/** @var View $this */
/** @var GetOrdersFilter $filter */

$reset = ['serviceId' => null, 'mode' => null, 'page' => null];
?>
<ul class="nav nav-tabs p-b">
    <li<?= $filter->status === null ? ' class="active"' : '' ?>>
        <a href="<?= Html::encode(Url::current(array_merge(['status' => null], $reset))) ?>">
            <?= Html::encode(Yii::t('orders', 'All orders')) ?>
        </a>
    </li>
    <?php foreach (OrderStatusEnum::cases() as $status): ?>
        <li<?= $filter->status === $status->name ? ' class="active"' : '' ?>>
            <a href="<?= Html::encode(Url::current(array_merge(['status' => $status->name], $reset))) ?>">
                <?= Html::encode($status->label()) ?>
            </a>
        </li>
    <?php endforeach ?>
    <li class="pull-right custom-search">
        <?= $this->render('_search', ['filter' => $filter]) ?>
    </li>
    <?php if (!$filter->hasErrors()): ?>
        <li class="pull-right export-li">
            <a class="export" href="<?= Html::encode(Url::to(array_merge(['export'], $filter->toQueryParams()))) ?>">
                <span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span>
                    <?= Html::encode(Yii::t('orders', 'Save result')) ?>
            </a>
        </li>
    <?php endif ?>
</ul>
