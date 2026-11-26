<?php

namespace app\modules\orders_listing\repositories;

use app\modules\orders_listing\dto\GetOrdersQuery;
use app\modules\orders_listing\dto\OrdersResultDTO;
use app\modules\orders_listing\enums\order\SearchTypeEnum;
use app\modules\orders_listing\models\Order;
use app\modules\orders_listing\models\User;
use yii\data\ActiveDataProvider;
use yii\data\Pagination;
use yii\db\Query;
use Generator;

class OrderRepository
{
    public function getOrdersByFilters(GetOrdersQuery $queryDTO, int $page = 1, int $perPage = 100): OrdersResultDTO
    {
        $query = (new Query())->select([
            'orders.id', 'orders.link', 'orders.quantity', 'orders.status', 'orders.mode', 'orders.created_at',
            'orders.service_id', 'users.first_name', 'users.last_name'
        ])->from(['orders' => Order::tableName()])
            ->innerJoin(['users' => User::tableName()], 'orders.user_id = users.id');

        $this->applyFilters($query, $queryDTO);

        //dd($query->createCommand()->getRawSql());

        $serviceCounts = $this->getServiceCounts($query);
        $totalCount = (int) array_sum(array_column($serviceCounts, 'cnt'));
        $pagination = new Pagination([
            'page' => max(0, $page - 1),
            'pageSize' => $perPage,
            'totalCount' => $totalCount,
        ]);
        $query->orderBy(['orders.id' => SORT_DESC]);
        $serviceCounts = array_column($serviceCounts, 'cnt', 'service_id');
        arsort($serviceCounts);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => $pagination,
            'sort' => false
        ]);

        return new OrdersResultDTO(
            $dataProvider,
            $serviceCounts,
        );
    }

    public function exportBatch(GetOrdersQuery $queryDTO, int $batchSize = 1000): Generator
    {
        $query = (new Query())
            ->select([
                'orders.id', 'orders.link', 'orders.quantity', 'orders.status', 'orders.mode', 'orders.created_at',
                'orders.service_id', 'users.first_name', 'users.last_name',
            ])
            ->from(['orders' => Order::tableName()])
            ->innerJoin(['users' => User::tableName()], 'orders.user_id = users.id')
            ->orderBy(['orders.id' => SORT_DESC]);

        $this->applyFilters($query, $queryDTO);

        foreach ($query->each($batchSize) as $row) {
            yield $row;
        }
    }

    private function applyFilters(Query $query, GetOrdersQuery $queryDTO): void
    {
        $query->andFilterWhere(['orders.status' => $queryDTO->getStatus()])
            ->andFilterWhere(['orders.mode' => $queryDTO->getMode()])
            ->andFilterWhere(['orders.service_id' => $queryDTO->getServiceId()]);

        if ($queryDTO->getSearch() !== null && $queryDTO->getSearch() !== '') {
            $searchType = $queryDTO->getSearchType();
            if ($searchType === SearchTypeEnum::id->value) {
                $query->andWhere(['orders.id' => $queryDTO->getSearch()]);
            }
            if ($searchType === SearchTypeEnum::link->value) {
                $query->andWhere(['orders.link' => $queryDTO->getSearch()]);
            }
            if ($searchType === SearchTypeEnum::username->value) {
                $firstAndLastName = explode(' ', cleanLetters($queryDTO->getSearch()), 2);
                $firstName = !empty($firstAndLastName[0]) ? $firstAndLastName[0] : null;
                $lastName = !empty($firstAndLastName[1]) ? $firstAndLastName[1] : null;
                $query->andFilterWhere((['like', 'users.first_name', $firstName]))
                    ->andFilterWhere((['like', 'users.last_name', $lastName]));
            }
        }
    }

    private function getServiceCounts(Query $query): array
    {
        return (clone $query)
            ->select(['orders.service_id', 'cnt' => 'COUNT(*)'])
            ->groupBy('orders.service_id')
            ->all();
    }
}
