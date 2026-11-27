<?php

namespace app\modules\orders_listing\services;

use app\modules\orders_listing\dto\GetOrdersQuery;
use app\modules\orders_listing\dto\OrdersIndexResultDTO;
use app\modules\orders_listing\enums\order\OrderModeEnum;
use app\modules\orders_listing\enums\order\OrderStatusEnum;
use app\modules\orders_listing\helpers\CsvHelper;
use app\modules\orders_listing\repositories\OrderRepository;
use app\modules\orders_listing\repositories\ServiceRepository;
use Generator;
use Yii;

class OrderService
{
    private const PAGE_SIZE = 100;
    private const EXPORT_BATCH_SIZE = 1000;

    public function __construct(
        private readonly OrderRepository $orderRepository,
        private readonly ServiceRepository $serviceRepository,
    ) {
    }

    /**
     * @param GetOrdersQuery $queryDTO
     * @return OrdersIndexResultDTO
     */
    public function getOrdersByFilters(GetOrdersQuery $queryDTO): OrdersIndexResultDTO
    {
        $result = $this->orderRepository->getOrdersByFilters($queryDTO, self::PAGE_SIZE);

        return new OrdersIndexResultDTO(
            $result->getActiveDataProvider(),
            $result->getServiceCounts(),
            $this->serviceRepository->getAllServices(),
        );
    }

    /**
     * @return string
     */
    public function getExportFileName(): string
    {
        return 'orders_' . date('Y-m-d_H-i-s') . '.csv';
    }

    /**
     * @param GetOrdersQuery $queryDTO
     * @return Generator<int, string>
     */
    public function exportOrdersCsvStream(GetOrdersQuery $queryDTO): Generator
    {
        // XXX: mark for utf-8
        yield "\xEF\xBB\xBF";

        $buffer = '';
        $count = 0;
        foreach ($this->exportOrdersCsv($queryDTO) as $row) {
            $count++;
            $buffer .= CsvHelper::encodeRow($row);
            if ($count % 1000 === 0) {
                yield $buffer;
                $buffer = '';
            }
        }
        if ($buffer !== '') {
            yield $buffer;
        }
    }

    private function exportOrdersCsv(GetOrdersQuery $queryDTO): Generator
    {
        $servicesById = array_column($this->serviceRepository->getAllServices(), null, 'id');
        $formatter = Yii::$app->formatter;

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

        foreach ($this->orderRepository->exportBatch($queryDTO, self::EXPORT_BATCH_SIZE) as $order) {
            $serviceId = (int) $order['service_id'];
            yield [
                $order['id'],
                trim((string) $order['first_name'] . ' ' . (string) $order['last_name']),
                $order['link'],
                $order['quantity'],
                $serviceId,
                $servicesById[$serviceId]['name'] ?? '',
                OrderStatusEnum::labelFor($order['status']),
                OrderModeEnum::labelFor($order['mode']),
                $formatter->asDatetime($order['created_at'], 'php:Y-m-d H:i:s'),
            ];
        }
    }
}
