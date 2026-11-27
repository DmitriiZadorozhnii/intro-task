<?php

namespace app\modules\orders_listing\dto;

use yii\data\ActiveDataProvider;

class OrdersResultDTO
{
    public function __construct(
        protected ActiveDataProvider $activeDataProvider,
        protected array $serviceCounts,
    ) {
    }

    public function getActiveDataProvider(): ActiveDataProvider
    {
        return $this->activeDataProvider;
    }

    public function getServiceCounts(): array
    {
        return $this->serviceCounts;
    }
}
