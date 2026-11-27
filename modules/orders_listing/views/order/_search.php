<?php

use app\modules\orders_listing\enums\order\SearchTypeEnum;
use app\modules\orders_listing\models\GetOrdersFilter;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var GetOrdersFilter $filter */

$keep = array_diff_key($filter->toQueryParams(), ['search' => true, 'search-type' => true]);
?>
<?= Html::beginForm(Url::to(['index']), 'get', ['class' => 'form-inline']) ?>
    <?php foreach ($keep as $name => $value): ?>
        <?= Html::hiddenInput($name, (string) $value) ?>
    <?php endforeach ?>
    <div class="input-group">
        <?= Html::textInput('search', $filter->search, [
            'class' => 'form-control',
            'placeholder' => Yii::t('orders', 'Search orders'),
        ]) ?>
        <span class="input-group-btn search-select-wrap">
            <?= Html::dropDownList(
                'search-type',
                $filter->searchType ?? SearchTypeEnum::id->value,
                SearchTypeEnum::options(),
                ['class' => 'form-control search-select']
            ) ?>
            <button type="submit" class="btn btn-default">
                <span class="glyphicon glyphicon-search" aria-hidden="true"></span>
            </button>
        </span>
    </div>
<?= Html::endForm() ?>
