<?php

namespace app\modules\orders_listing\repositories;

use app\modules\orders_listing\dto\GetOrdersQuery;
use app\modules\orders_listing\dto\OrdersResultDTO;
use app\modules\orders_listing\enums\order\SearchTypeEnum;
use app\modules\orders_listing\models\Order;
use app\modules\orders_listing\models\User;
use Generator;
use yii\data\ActiveDataProvider;
use yii\data\Pagination;
use yii\db\Query;

class OrderRepository
{
    /**
     * @param GetOrdersQuery $queryDTO
     * @param int $pageSize
     * @return OrdersResultDTO
     */
    public function getOrdersByFilters(GetOrdersQuery $queryDTO, int $pageSize): OrdersResultDTO
    {
        $serviceCounts = $this->getServiceCounts($queryDTO);

        $serviceId = $queryDTO->getServiceId();
        $totalCount = $serviceId === null
            ? (int) array_sum($serviceCounts)
            : ($serviceCounts[$serviceId] ?? 0);

        $pageCount = max(1, (int) ceil($totalCount / $pageSize));
        $pagination = new Pagination([
            'page' => min($queryDTO->getPage(), $pageCount) - 1,
            'pageSize' => $pageSize,
            'totalCount' => $totalCount,
            'pageSizeParam' => false,
        ]);

        $query = $this->buildListQuery($queryDTO)->orderBy(['orders.id' => SORT_DESC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => $pagination,
            'totalCount' => $totalCount,
            'sort' => false,
        ]);

        return new OrdersResultDTO($dataProvider, $serviceCounts);
    }

    /**
     * @param GetOrdersQuery $queryDTO
     * @param int $batchSize
     * @return Generator
     */
    public function exportBatch(GetOrdersQuery $queryDTO, int $batchSize = 1000): Generator
    {
        $lastId = null;
        do {
            $query = $this->buildListQuery($queryDTO)
                ->orderBy(['orders.id' => SORT_DESC])
                ->limit($batchSize);
            if ($lastId !== null) {
                $query->andWhere(['<', 'orders.id', $lastId]);
            }
            $rows = $query->all();
            foreach ($rows as $row) {
                yield $row;
            }
            $lastId = $rows ? $rows[array_key_last($rows)]['id'] : null;
        } while (count($rows) === $batchSize);
    }

    private function buildListQuery(GetOrdersQuery $queryDTO): Query
    {
        $query = (new Query())
            ->select([
                'orders.id', 'orders.link', 'orders.quantity', 'orders.status', 'orders.mode', 'orders.created_at',
                'orders.service_id', 'users.first_name', 'users.last_name',
            ])
            ->from(['orders' => Order::tableName()])
            ->innerJoin(['users' => User::tableName()], 'orders.user_id = users.id');

        $this->applyCommonFilters($query, $queryDTO);
        $this->applyServiceFilter($query, $queryDTO);

        return $query;
    }

    /**
     * @param GetOrdersQuery $queryDTO
     * @return array<int, int> [service_id => count]
     */
    private function getServiceCounts(GetOrdersQuery $queryDTO): array
    {
        $query = (new Query())
            ->select(['orders.service_id', 'cnt' => 'COUNT(*)'])
            ->from(['orders' => Order::tableName()])
            ->groupBy('orders.service_id');

        if ($this->isUsernameSearch($queryDTO)) {
            $query->innerJoin(['users' => User::tableName()], 'orders.user_id = users.id');
        }

        $this->applyCommonFilters($query, $queryDTO);

        return array_map('intval', array_column($query->all(), 'cnt', 'service_id'));
    }

    private function applyCommonFilters(Query $query, GetOrdersQuery $queryDTO): void
    {
        $query->andFilterWhere(['orders.status' => $queryDTO->getStatus()])
            ->andFilterWhere(['orders.mode' => $queryDTO->getMode()]);

        if (!$queryDTO->hasSearch()) {
            return;
        }

        $search = $queryDTO->getSearch();
        switch ($queryDTO->getSearchType()) {
            case SearchTypeEnum::id->value:
                $query->andWhere(['orders.id' => $search]);
                break;
            case SearchTypeEnum::link->value:
                $query->andWhere(['orders.link' => $search]);
                break;
            case SearchTypeEnum::username->value:
                $firstAndLastName = explode(' ', cleanLetters($search), 2);
                $firstName = !empty($firstAndLastName[0]) ? $firstAndLastName[0] : null;
                $lastName = !empty($firstAndLastName[1]) ? $firstAndLastName[1] : null;
                $query->andFilterWhere(['like', 'users.first_name', $firstName])
                    ->andFilterWhere(['like', 'users.last_name', $lastName]);
                break;
        }
    }

    private function applyServiceFilter(Query $query, GetOrdersQuery $queryDTO): void
    {
        $query->andFilterWhere(['orders.service_id' => $queryDTO->getServiceId()]);
    }

    private function isUsernameSearch(GetOrdersQuery $queryDTO): bool
    {
        return $queryDTO->hasSearch() && $queryDTO->getSearchType() === SearchTypeEnum::username->value;
    }
}
