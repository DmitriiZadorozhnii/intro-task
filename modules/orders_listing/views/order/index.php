<?php

/**
 * @var yii\web\View $this
 * @var app\modules\orders_listing\services\DTOs\OrderListingDTO $orders
 * @var yii\data\Pagination $pagination
 * @var int $totalCount
 *
 * @var string $currentStatus   текущий выбранный статус ('' = все)
 * @var array $statuses         ['' => 'All orders', 'pending' => 'Pending', ...]
 *
 * @var string $currentMode     текущий mode ('' | 'manual' | 'auto')
 * @var array $modes            ['' => 'All', 'manual' => 'Manual', 'auto' => 'Auto']
 *
 * @var string $currentService  текущий service_id ('' = все)
 * @var array $serviceCounts    [service_id => ['name' => string, 'count' => int]], отсортировано по count desc
 *
 * @var string $searchQuery
 * @var string $searchType      '1' (Order ID) | '2' (Link) | '3' (Username)
 */

use app\modules\orders_listing\services\DTOs\OrderListingDTO;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;

$this->title = Yii::t('orders', 'Orders');

/**
 * Собираем ссылку для фильтра статуса.
 * При смене статуса mode и service всегда сбрасываются (п. ТЗ).
 */
$buildStatusUrl = function (string $status) use ($searchQuery, $searchType) {
    $params = ['status' => $status];
    if ($searchQuery !== '') {
        $params['search'] = $searchQuery;
        $params['search-type'] = $searchType;
    }
    return Url::to(array_merge(['index'], $params));
};

/**
 * Ссылка для фильтра mode — сохраняет текущий статус и поиск, сбрасывает service.
 */
$buildModeUrl = function (string $mode) use ($currentStatus, $searchQuery, $searchType) {
    $params = ['mode' => $mode];
    if ($currentStatus !== '') {
        $params['status'] = $currentStatus;
    }
    if ($searchQuery !== '') {
        $params['search'] = $searchQuery;
        $params['search-type'] = $searchType;
    }
    return Url::to(array_merge(['index'], $params));
};

/**
 * Ссылка для фильтра service — сохраняет текущий статус, mode и поиск.
 */
$buildServiceUrl = function (string $serviceId) use ($currentStatus, $currentMode, $searchQuery, $searchType) {
    $params = ['service' => $serviceId];
    if ($currentStatus !== '') {
        $params['status'] = $currentStatus;
    }
    if ($currentMode !== '') {
        $params['mode'] = $currentMode;
    }
    if ($searchQuery !== '') {
        $params['search'] = $searchQuery;
        $params['search-type'] = $searchType;
    }
    return Url::to(array_merge(['index'], $params));
};
?>

<ul class="nav nav-tabs p-b">
    <?php foreach ($statuses as $statusKey => $statusLabel): ?>
        <li class="<?= $statusKey === $currentStatus ? 'active' : '' ?>">
            <a href="<?= Html::encode($buildStatusUrl($statusKey)) ?>">
                <?= Html::encode(Yii::t('orders', $statusLabel)) ?>
            </a>
        </li>
    <?php endforeach; ?>

    <li class="pull-right custom-search">
        <?= Html::beginForm(['index'], 'get', ['class' => 'form-inline']) ?>
        <?php if ($currentStatus !== ''): ?>
            <?= Html::hiddenInput('status', $currentStatus) ?>
        <?php endif; ?>
        <div class="input-group">
            <?= Html::textInput('search', $searchQuery, [
                'class' => 'form-control',
                'placeholder' => Yii::t('orders', 'Search orders'),
            ]) ?>
            <span class="input-group-btn search-select-wrap">
                        <?= Html::dropDownList('search-type', $searchType, [
                            '1' => Yii::t('orders', 'Order ID'),
                            '2' => Yii::t('orders', 'Link'),
                            '3' => Yii::t('orders', 'Username'),
                        ], ['class' => 'form-control search-select']) ?>
                        <button type="submit" class="btn btn-default">
                            <span class="glyphicon glyphicon-search" aria-hidden="true"></span>
                        </button>
                    </span>
        </div>
        <?= Html::endForm() ?>
    </li>
</ul>

<table class="table order-table">
    <thead>
    <tr>
        <th><?= Yii::t('orders', 'ID') ?></th>
        <th><?= Yii::t('orders', 'User') ?></th>
        <th><?= Yii::t('orders', 'Link') ?></th>
        <th><?= Yii::t('orders', 'Quantity') ?></th>
        <th class="dropdown-th">
            <div class="dropdown">
                <button class="btn btn-th btn-default dropdown-toggle" type="button"
                        id="dropdownService" data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="true">
                    <?= Yii::t('orders', 'Service') ?>
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu" aria-labelledby="dropdownService">
                    <li class="<?= $currentService === '' ? 'active' : '' ?>">
                        <a href="<?= Html::encode($buildServiceUrl('')) ?>">
                            <?= Yii::t('orders', 'All') ?> (<?= $totalCount ?>)
                        </a>
                    </li>
                    <?php foreach ($serviceCounts as $serviceId => $service): ?>
                        <?php $disabled = $service['count'] === 0; ?>
                        <li class="<?= (string) $serviceId === $currentService ? 'active' : '' ?> <?= $disabled ? 'disabled' : '' ?>">
                            <a href="<?= $disabled ? '#' : Html::encode($buildServiceUrl((string) $serviceId)) ?>"
                                <?= $disabled ? 'tabindex="-1" aria-disabled="true"' : '' ?>>
                                <span class="label-id"><?= Html::encode($serviceId) ?></span>
                                <?= Html::encode($service['name']) ?>
                                (<?= $service['count'] ?>)
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </th>
        <th><?= Yii::t('orders', 'Status') ?></th>
        <th class="dropdown-th">
            <div class="dropdown">
                <button class="btn btn-th btn-default dropdown-toggle" type="button"
                        id="dropdownMode" data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="true">
                    <?= Yii::t('orders', 'Mode') ?>
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu" aria-labelledby="dropdownMode">
                    <?php foreach ($modes as $modeKey => $modeLabel): ?>
                        <li class="<?= $modeKey === $currentMode ? 'active' : '' ?>">
                            <a href="<?= Html::encode($buildModeUrl($modeKey)) ?>">
                                <?= Yii::t('orders', $modeLabel) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </th>
        <th><?= Yii::t('orders', 'Created') ?></th>
    </tr>
    </thead>
    <tbody>
    <?php if (empty($orders)): ?>
        <tr>
            <td colspan="8" class="text-center">
                <?= Yii::t('orders', 'No orders found.') ?>
            </td>
        </tr>
    <?php endif; ?>
    <?php foreach ($orders as $order): /** @var OrderListingDTO $order */ ?>
        <tr>
            <td><?= Html::encode($order->getOrderId()) ?></td>
            <td><?= Html::encode($order->getUserNameDTO()->getFullName()) ?></td>
            <td class="link"><?= Html::encode($order->getLink()) ?></td>
            <td><?= Html::encode($order->getQuantity()) ?></td>
            <td class="service">
                <span class="label-id"><?= Html::encode($order->getServiceCounter()->getId()) ?></span>
                <?= Html::encode($order->getServiceCounter()->getName()) ?>
            </td>
            <td><?= Html::encode(Yii::t('orders', $order->getStatus())) ?></td>
            <td><?= Html::encode(Yii::t('orders', $order->getMode())) ?></td>
            <td><span class="nowrap"><?= Html::encode($order->getCreatedAt()) ?></span><span class="nowrap"><?= Html::encode($order->getCreatedAt()) ?></span></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<div class="row">
    <div class="col-sm-8">
        <?php if ($pagination->pageCount > 1): ?>
            <nav>
                <?= LinkPager::widget([
                    'pagination' => $pagination,
                    'prevPageLabel' => '&laquo;',
                    'nextPageLabel' => '&raquo;',
                    'maxButtonCount' => 10,
                ]) ?>
            </nav>
        <?php endif; ?>
    </div>
    <div class="col-sm-4 pagination-counters">
        <?php
        $from = $totalCount === 0 ? 0 : $pagination->offset + 1;
        $to = min($pagination->offset + $pagination->limit, $totalCount);
        ?>
        <?php if ($pagination->pageCount > 1): ?>
            <?= Yii::t('orders', '{from} to {to} of {total}', [
                'from' => $from,
                'to' => $to,
                'total' => $totalCount,
            ]) ?>
        <?php else: ?>
            <?= Yii::t('orders', '{total} orders', ['total' => $totalCount]) ?>
        <?php endif; ?>

        <div>
            <?= Html::a(
                Yii::t('orders', 'Save result'),
                array_merge(['export'], Yii::$app->request->queryParams)
            ) ?>
        </div>
    </div>
</div>
