<?php

namespace app\modules\orders_listing\repositories;

use app\modules\orders_listing\forms\entities\GetOrdersListingEntity;
use app\modules\orders_listing\models\Order;
use yii\db\Query;

class OrderRepository
{
    public function getOrdersByFilters(GetOrdersListingEntity $entity): array
    {
        $query = (new Query())->select(['id', 'link', 'quantity', 'status', 'mode', 'created_at'])
            ->from(Order::tableName());
        if (!is_null($entity->getStatus())) {
            $query->andWhere(['status' => $entity->getStatus()]);
        }

        return ;
    }
}
