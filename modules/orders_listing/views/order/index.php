<?php

declare(strict_types=1);

use app\modules\orders_listing\enums\order\OrderModeEnum;
use app\modules\orders_listing\enums\order\OrderStatusEnum;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\LinkPager;

/** @var View $this */
/** @var ActiveDataProvider $dataProvider */
/** @var array<int, int> $serviceCounts */
/** @var array $services */
/** @var array $currentParams */

$this->title = Yii::t('orders', 'Orders');

$orders = $dataProvider->getModels();
$pagination = $dataProvider->getPagination();
$totalCount = $pagination->totalCount;

$statuses = [
    null => Yii::t('orders', 'All orders'),
    'pending' => Yii::t('orders', 'Pending'),
    'in_progress' => Yii::t('orders', 'In progress'),
    'completed' => Yii::t('orders', 'Completed'),
    'canceled' => Yii::t('orders', 'Canceled'),
    'error' => Yii::t('orders', 'Error'),
];

$activeStatus = $currentParams['status'] ?? null;
$searchValue = $currentParams['search'] ?? '';
$searchType = (int)($currentParams['search-type'] ?? 1);
?>

<ul class="nav nav-tabs p-b">
    <?php foreach ($statuses as $statusKey => $statusLabel): ?>
        <?php
        $isActive = $activeStatus == $statusKey;
        $tabParams = array_merge($currentParams, [
            'status' => $statusKey,
        ]);
        ?>
        <li class="<?= $isActive ? 'active' : '' ?>">
            <a href="<?= Html::encode(Url::to(array_merge(['/orders_listing/order/index'], array_filter($tabParams, static fn($v) => $v !== null && $v !== '')))) ?>">
                <?= Html::encode($statusLabel) ?>
            </a>
        </li>
    <?php endforeach; ?>

    <li class="pull-right custom-search">
        <form class="form-inline" action="<?= Html::encode(Url::to(['/orders_listing/order/index'])) ?>" method="get">
            <?php if ($activeStatus !== null): ?>
                <input type="hidden" name="status" value="<?= Html::encode($activeStatus) ?>">
            <?php endif; ?>

            <a href="<?= Html::encode(Url::to(array_merge(['/orders_listing/order/export'], $currentParams))) ?>"
               class="btn btn-default pull-left"
               style="margin-right: 10px;">
                <span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span>
                <?= Yii::t('orders', 'Save result') ?>
            </a>

            <div class="input-group">
                <input type="text" name="search" class="form-control" value="<?= Html::encode($searchValue) ?>" placeholder=<?php echo Yii::t('orders', 'Search orders') ?>>
                <span class="input-group-btn search-select-wrap">
                    <select class="form-control search-select" name="search-type">
                        <option value="1" <?= $searchType === 1 ? 'selected' : '' ?>><?= Yii::t('orders', 'Order ID') ?></option>
                        <option value="2" <?= $searchType === 2 ? 'selected' : '' ?>><?= Yii::t('orders', 'Link') ?></option>
                        <option value="3" <?= $searchType === 3 ? 'selected' : '' ?>><?= Yii::t('orders', 'Username') ?></option>
                    </select>
                    <button type="submit" class="btn btn-default">
                        <span class="glyphicon glyphicon-search" aria-hidden="true"></span>
                    </button>
                </span>
            </div>
        </form>
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
                <button class="btn btn-th btn-default dropdown-toggle" type="button" id="dropdownService" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                    <?= Yii::t('orders', 'Service') ?>
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu" aria-labelledby="dropdownService">
                    <?php $allServiceTotal = array_sum($serviceCounts); ?>
                    <li class="<?= empty($currentParams['serviceId']) ? 'active' : '' ?>">
                        <a href="<?= Html::encode(Url::to(array_merge(
                            ['/orders_listing/order/index'],
                            array_filter(
                                array_merge($currentParams, ['serviceId' => null, 'page' => null]),
                                static fn($v) => $v !== null && $v !== ''
                            )
                        ))) ?>">
                            <?= Yii::t('orders', 'All') ?> (<?= (int) $allServiceTotal ?>)
                        </a>
                    </li>

                    <?php
                    // 1. Сервисы с заказами — активные, в порядке убывания cnt
                    $renderedIds = [];
                    foreach ($serviceCounts as $sId => $cnt):
                        $sId = (int) $sId;
                        $cnt = (int) $cnt;

                        // Ищем сервис в $services, чтобы взять имя
                        $serviceName = null;
                        foreach ($services as $s) {
                            if ((int) $s['id'] === $sId) {
                                $serviceName = $s['name'];
                                break;
                            }
                        }
                        // Если сервиса нет в списке (удалён, но заказы остались) — пропускаем
                        if ($serviceName === null) {
                            continue;
                        }

                        $renderedIds[$sId] = true;
                        $isSelectedService = (int) ($currentParams['serviceId'] ?? 0) === $sId;
                        $sParams = array_filter(
                            array_merge($currentParams, ['serviceId' => $sId, 'page' => null]),
                            static fn($v) => $v !== null && $v !== ''
                        );
                        ?>
                        <li class="<?= $isSelectedService ? 'active' : '' ?>">
                            <a href="<?= Html::encode(Url::to(array_merge(['/orders_listing/order/index'], $sParams))) ?>">
                                <span class="label-id"><?= $cnt ?></span> <?= Html::encode($serviceName) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>

                    <?php
                    // 2. Остальные сервисы — неактивные
                    foreach ($services as $service):
                        $sId = (int) $service['id'];
                        if (isset($renderedIds[$sId])) {
                            continue; // уже отрисован как активный
                        }
                        ?>
                        <li class="disabled">
                            <a href="javascript:void(0)" tabindex="-1" aria-disabled="true">
                                <span class="label-id">0</span> <?= Html::encode($service['name']) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </th>
        <th><?= Yii::t('orders', 'Status')?></th>
        <th class="dropdown-th">
            <div class="dropdown">
                <button class="btn btn-th btn-default dropdown-toggle" type="button" id="dropdownMode" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                    <?= Yii::t('orders', 'Mode') ?>
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu" aria-labelledby="dropdownMode">
                    <?php foreach ([null => 'All', 'manual' => 'Manual', 'auto' => 'Auto'] as $modeKey => $modeLabel): ?>
                        <?php
                        $isModeActive = ($currentParams['mode'] ?? null) === $modeKey;
                        $mParams = array_filter(array_merge($currentParams, ['mode' => $modeKey, 'page' => null]), static fn($v) => $v !== null && $v !== '');
                        ?>
                        <li class="<?= $isModeActive ? 'active' : '' ?>">
                            <a href="<?= Html::encode(Url::to(array_merge(['/orders_listing/order/index'], $mParams))) ?>">
                                <?= Html::encode(Yii::t('orders', $modeLabel)) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </th>
        <th><?= Yii::t('orders', 'Created')?></th>
    </tr>
    </thead>
    <tbody>
    <?php $servicesById = array_column($services, null, 'id'); ?>
    <?php foreach ($orders as $order): ?>
        <?php
        $serviceId = (int) $order['service_id'];
        $fullName = trim((string)($order['first_name'] ?? '') . ' ' . (string)($order['last_name'] ?? ''));
        $createdTs = is_numeric($order['created_at']) ? (int)$order['created_at'] : strtotime((string)$order['created_at']);
        $datePart = $createdTs ? date('Y-m-d', $createdTs) : '';
        $timePart = $createdTs ? date('H:i:s', $createdTs) : '';
        $statusName = Yii::t('orders', OrderStatusEnum::tryFrom($order['status'])->name);
        $modeName = Yii::t('orders', OrderModeEnum::tryFrom($order['mode'])->name);
        ?>
        <tr>
            <td><?= Html::encode((string)$order['id']) ?></td>
            <td><?= Html::encode($fullName) ?></td>
            <td class="link"><?= Html::encode((string)$order['link']) ?></td>
            <td><?= Html::encode((string)$order['quantity']) ?></td>
            <td class="service">
                <span class="label-id"><?= Html::encode($serviceCounts[$serviceId]) ?></span><?= Html::encode((string)($servicesById[$serviceId]['name'])) ?>
            </td>
            <td><?= Html::encode($statusName) ?></td>
            <td><?= Html::encode($modeName) ?></td>
            <td>
                <span class="nowrap"><?= Html::encode($datePart) ?></span>
                <span class="nowrap"><?= Html::encode($timePart) ?></span>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<div class="row">
    <div class="col-sm-8">
        <?= LinkPager::widget([
            'pagination' => $pagination,
            'options' => ['class' => 'pagination'],
            'maxButtonCount' => 10,
            'prevPageLabel' => '&laquo;',
            'nextPageLabel' => '&raquo;',
            'firstPageLabel' => false,
            'lastPageLabel' => false,
        ]) ?>
    </div>
    <div class="col-sm-4 pagination-counters">
        <?php if ($totalCount === 0): ?>
            <?= Yii::t('orders', '{total} orders', ['total' => 0]) ?>
        <?php elseif ($totalCount <= 100): ?>
            <?= Yii::t('orders', '{total} orders', ['total' => $totalCount]) ?>
        <?php else: ?>
            <?php
            $from = $pagination->offset + 1;
            $to = min($pagination->offset + $pagination->limit, $totalCount);
            ?>
            <?= Yii::t('orders', '{from} to {to} of {total}', [
                'from' => $from,
                'to' => $to,
                'total' => $totalCount,
            ]) ?>
        <?php endif; ?>
    </div>
</div>
