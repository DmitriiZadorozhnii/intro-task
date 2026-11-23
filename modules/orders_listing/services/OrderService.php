<?php

namespace app\modules\orders_listing\services;

use app\modules\orders_listing\forms\entities\GetOrdersListingEntity;
use app\modules\orders_listing\repositories\OrderRepository;

class OrderService
{
    public function __construct(private readonly OrderRepository $orderRepository)
    {
    }

    public function getOrdersByFilters(GetOrdersListingEntity $entity): array
    {
        $orders = $this->orderRepository->getOrdersByFilters($entity);
    }
}
