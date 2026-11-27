<?php

use app\modules\orders_listing\dto\OrdersIndexResultDTO;
use app\modules\orders_listing\models\GetOrdersFilter;
use yii\web\View;

/** @var View $this */
/** @var GetOrdersFilter $filter */
/** @var OrdersIndexResultDTO|null $result */

$this->title = Yii::t('orders', 'Orders');
$this->registerJs(<<<'JS'
(function () {
    var perf = window.performance;
    var nav = perf && perf.getEntriesByType ? perf.getEntriesByType('navigation')[0] : null;
    var isReload = nav ? nav.type === 'reload' : !!(perf && perf.navigation && perf.navigation.type === 1);
    if (isReload && window.location.search) {
        window.location.replace(window.location.pathname);
    }
})();
JS, View::POS_HEAD);
?>
<?= $this->render('_statuses_tabs', ['filter' => $filter]) ?>
<?php if ($result === null): ?>
    <?= $this->render('_errors', ['filter' => $filter]) ?>
<?php else: ?>
    <?= $this->render('_grid', ['filter' => $filter, 'result' => $result]) ?>
<?php endif ?>
