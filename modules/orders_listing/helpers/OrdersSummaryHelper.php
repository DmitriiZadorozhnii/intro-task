<?php

namespace app\modules\orders_listing\helpers;

use Yii;
use yii\data\DataProviderInterface;

final class OrdersSummaryHelper
{
    /**
     * @param DataProviderInterface $dataProvider
     * @return string
     */
    public static function template(DataProviderInterface $dataProvider): string
    {
        $pagination = $dataProvider->getPagination();
        $pageSize = $pagination !== false ? $pagination->pageSize : 0;

        return $dataProvider->getTotalCount() < $pageSize
            ? Yii::t('orders', 'Total: {totalCount}')
            : Yii::t('orders', '{begin} to {end} of {totalCount}');
    }
}
