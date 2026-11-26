<?php

namespace app\modules\orders_listing\services;

use app\modules\orders_listing\dto\GetOrdersQuery;
use app\modules\orders_listing\enums\order\OrderModeEnum;
use app\modules\orders_listing\enums\order\OrderStatusEnum;
use app\modules\orders_listing\repositories\OrderRepository;
use app\modules\orders_listing\repositories\ServiceRepository;
use Generator;
use Yii;

class OrderService
{
    public function __construct(
        private readonly OrderRepository $orderRepository,
        private readonly ServiceRepository $serviceRepository,
    ) {
    }

    public function getOrdersByFilters(GetOrdersQuery $orderDTO, int $page, int $perPage): array
    {
        $services = $this->serviceRepository->getAllServices();
        $result = $this->orderRepository->getOrdersByFilters($orderDTO, $page, $perPage);

        return [
            'services' => $services,
            'dataProvider' => $result->getActiveDataProvider(),
            'serviceCounts' => $result->getServiceCounts(),
        ];
    }

    public function exportOrdersCsv(GetOrdersQuery $queryDTO): Generator
    {
        $services = $this->serviceRepository->getAllServices();
        $servicesById = array_column($services, null, 'id');

        yield [
            Yii::t('orders', 'ID'),
            Yii::t('orders', 'User'),
            Yii::t('orders', 'Link'),
            Yii::t('orders', 'Quantity'),
            Yii::t('orders', 'Service ID'),
            Yii::t('orders', 'Service Name'),
            Yii::t('orders', 'Status'),
            Yii::t('orders', 'Mode'),
            Yii::t('orders', 'Created'),
        ];

        foreach ($this->orderRepository->exportBatch($queryDTO, 1000) as $order) {
            $serviceId = (int) $order['service_id'];
            $fullName = trim((string) ($order['first_name'] ?? '') . ' ' . (string) ($order['last_name'] ?? ''));
            $createdTs = is_numeric($order['created_at']) ? (int) $order['created_at'] : strtotime((string) $order['created_at']);
            $createdAt = $createdTs ? date('Y-m-d H:i:s', $createdTs) : '';
            $statusName = Yii::t('orders', OrderStatusEnum::tryFrom($order['status'])?->name ?? $order['status']);
            $modeName = Yii::t('orders', OrderModeEnum::tryFrom($order['mode'])?->name ?? $order['mode']);

            yield [
                $order['id'],
                $fullName,
                $order['link'],
                $order['quantity'],
                $serviceId,
                $servicesById[$serviceId]['name'] ?? '',
                $statusName,
                $modeName,
                $createdAt,
            ];
        }
    }
}
