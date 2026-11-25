<?php

namespace app\modules\orders_listing\repositories;

use app\modules\orders_listing\forms\entities\GetOrdersListingEntity;
use app\modules\orders_listing\models\Order;
use app\modules\orders_listing\models\User;
use yii\db\Query;

class OrderRepository
{
    // TODO: extract page and perPage
    public function getOrdersByFilters(GetOrdersListingEntity $entity, int $page = 1, int $perPage = 100): array
    {
        $page = max(1, $page);

        $query = (new Query())->select([
            'orders.id', 'orders.link', 'orders.quantity', 'orders.status', 'orders.mode', 'orders.created_at',
            'orders.service_id', 'users.first_name', 'users.last_name'
        ])
            ->from(['orders' => Order::tableName()])
            ->innerJoin(['users' => User::tableName()], 'orders.user_id = users.id');
        $query->andFilterWhere(['orders.id' => $entity->getId()])
            ->andFilterWhere(['orders.status' => $entity->getStatus()])
            ->andFilterWhere(['orders.mode' => $entity->getMode()])
            ->andFilterWhere(['service_id' => $entity->getServiceId()])
            ->andFilterWhere(['link' => $entity->getLink()]);
            // TODO: сделать красиво
            //->andFilterWhere(['like', 'users.first_name', $entity->getUserFirstName() . '%'])
            //->andFilterWhere(['like', 'users.last_name', $entity->getUserLastName() . '%']);
        if (!is_null($entity->getUserFirstName())) {
            $query->andWhere(['users.first_name' => $entity->getUserFirstName() . '%']);
        }
        if (!is_null($entity->getUserLastName())) {
            $query->andWhere(['users.last_name' => $entity->getUserLastName() . '%']);
        }
        //dd($query->createCommand()->getRawSql());
        $serviceCounts = $this->getServiceCounts($query);
        $totalCount = (int) array_sum(array_column($serviceCounts, 'cnt'));

        $orders = $query
            ->orderBy(['orders.id' => SORT_DESC])
            ->offset(($page - 1) * $perPage)
            ->limit($perPage)
            ->all();

        return [
            'orders' => $orders,
            'serviceCounts' => $serviceCounts,
            'page' => $page,
            'perPage' => $perPage,
            'totalCount' => $totalCount,
            'totalPages' => (int) ceil($totalCount / $perPage),
        ];
    }

    private function getServiceCounts(Query $query): array
    {
        return (clone $query)
            ->select(['orders.service_id', 'cnt' => 'COUNT(*)'])
            ->groupBy('service_id')
            ->all();
    }
}
