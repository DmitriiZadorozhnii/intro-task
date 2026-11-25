<?php

namespace app\modules\orders_listing\services;

use app\modules\orders_listing\forms\entities\GetOrdersListingEntity;
use app\modules\orders_listing\repositories\OrderRepository;
use app\modules\orders_listing\repositories\ServiceRepository;
use app\modules\orders_listing\services\DTOs\OrderListingDTO;
use app\modules\orders_listing\services\DTOs\PaginationDTO;
use app\modules\orders_listing\services\DTOs\service\ServiceCounterDTO;
use app\modules\orders_listing\services\DTOs\user\UserNameDTO;

class OrderService
{
    public function __construct(
        private readonly OrderRepository $orderRepository,
        private readonly ServiceRepository $serviceRepository,
    ) {
    }

    // TODO: добавить список сервисов с каутами (глобальный) и общий каунт на все записи
    public function getOrdersByFilters(GetOrdersListingEntity $entity, int $page, int $perPage): array
    {
        $services = $this->serviceRepository->getAllServices();
        $data = $this->orderRepository->getOrdersByFilters($entity, $page, $perPage);
        $orders = $data['orders'];
        $serviceCounts = $data['serviceCounts'];

        $services = array_column($services, 'name', 'id');
        $serviceCounts = array_column($serviceCounts, 'cnt', 'service_id');

        $items = [];
        foreach ($orders as $order) {
            $serviceId = (int) $order['service_id'];

            $items[] = new OrderListingDTO(
                (int) $order['id'],
                new UserNameDTO(
                    (string) $order['first_name'],
                    (string) $order['last_name'],
                ),
                (string) $order['link'],
                (int) $order['quantity'],
                new ServiceCounterDTO(
                    $serviceId,
                    $services[$serviceId],
                    $serviceCounts[$serviceId] ?? 0,
                ),
                (string) $order['status'],
                (string) $order['mode'],
                \Yii::$app->formatter->asDatetime($order['created_at']),
            );
        }

        return [
            'items' => $items,
            'pagination' => new PaginationDTO($data['page'], $data['perPage'], $data['totalCount'], $data['totalPages']),
        ];
    }
}
