<?php

use app\modules\orders_listing\dto\OrdersIndexResultDTO;
use app\modules\orders_listing\enums\order\OrderModeEnum;
use app\modules\orders_listing\enums\order\OrderStatusEnum;
use app\modules\orders_listing\helpers\OrdersSummaryHelper;
use app\modules\orders_listing\models\GetOrdersFilter;
use app\modules\orders_listing\widgets\HeaderDropdown;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\LinkPager;

/** @var View $this */
/** @var GetOrdersFilter $filter */
/** @var OrdersIndexResultDTO $result */

$dataProvider = $result->dataProvider;
$servicesById = $result->getServicesById();
$formatter = Yii::$app->formatter;
?>
<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'tableOptions' => ['class' => 'table order-table'],
    'summary' => OrdersSummaryHelper::template($dataProvider),
    'summaryOptions' => ['tag' => false],
    'emptyText' => Yii::t('orders', 'No orders found'),
    'layout' => "{items}\n"
        . '<div class="row">'
        . '<div class="col-sm-8"><nav>{pager}</nav></div>'
        . '<div class="col-sm-4 pagination-counters">{summary}</div>'
        . '</div>',
    'pager' => [
        'class' => LinkPager::class,
        'maxButtonCount' => 10,
        'prevPageLabel' => '&laquo;',
        'nextPageLabel' => '&raquo;',
        'hideOnSinglePage' => true,
    ],
    'columns' => [
        [
            'label' => Yii::t('orders', 'ID'),
            'value' => fn (array $r) => $r['id'],
        ],
        [
            'label' => Yii::t('orders', 'User'),
            'value' => fn (array $r) => trim($r['first_name'] . ' ' . $r['last_name']),
        ],
        [
            'label' => Yii::t('orders', 'Link'),
            'value' => fn (array $r) => $r['link'],
            'contentOptions' => ['class' => 'link'],
        ],
        [
            'label' => Yii::t('orders', 'Quantity'),
            'value' => fn (array $r) => $r['quantity'],
        ],
        [
            'header' => HeaderDropdown::widget([
                'label' => Yii::t('orders', 'Service'),
                'param' => 'serviceId',
                'current' => $filter->serviceId,
                'allLabel' => Yii::t('orders', 'All') . ' (' . $result->getTotalOrdersCount() . ')',
                'items' => $result->getServiceDropdownItems(),
            ]),
            'headerOptions' => ['class' => 'dropdown-th'],
            'contentOptions' => ['class' => 'service'],
            'format' => 'raw',
            'value' => fn (array $r) => Html::tag('span', Html::encode($r['service_id']), ['class' => 'label-id'])
                . ' ' . Html::encode($servicesById[$r['service_id']]['name'] ?? ''),
        ],
        [
            'label' => Yii::t('orders', 'Status'),
            'value' => fn (array $r) => OrderStatusEnum::labelFor($r['status']),
        ],
        [
            'header' => HeaderDropdown::widget([
                'label' => Yii::t('orders', 'Mode'),
                'param' => 'mode',
                'current' => $filter->mode,
                'items' => OrderModeEnum::dropdownItems(),
            ]),
            'headerOptions' => ['class' => 'dropdown-th'],
            'value' => fn (array $r) => OrderModeEnum::labelFor($r['mode']),
        ],
        [
            'label' => Yii::t('orders', 'Created'),
            'format' => 'raw',
            'value' => fn (array $r) => Html::tag('span', $formatter->asDate($r['created_at'], 'php:Y-m-d'), ['class' => 'nowrap'])
                . Html::tag('span', $formatter->asTime($r['created_at'], 'php:H:i:s'), ['class' => 'nowrap']),
        ],
    ],
]); ?>
