<?php

namespace app\modules\orders_listing\dto;

use yii\data\ActiveDataProvider;

class OrdersIndexResultDTO
{
    /**
     * @param array<int, int> $serviceCounts [service_id => count]
     * @param array<int, array{id: int|string, name: string}> $services
     */
    public function __construct(
        public readonly ActiveDataProvider $dataProvider,
        public readonly array $serviceCounts,
        public readonly array $services,
    ) {
    }

    public function getTotalOrdersCount(): int
    {
        return (int) array_sum($this->serviceCounts);
    }

    /**
     * @return array<int, array{value: int, prefix: int, label: string, count: int, disabled: bool}
     */
    public function getServiceDropdownItems(): array
    {
        $items = [];
        foreach ($this->services as $service) {
            $id = (int) $service['id'];
            $count = $this->serviceCounts[$id] ?? 0;
            $items[] = [
                'value' => $id,
                'prefix' => $id,
                'label' => (string) $service['name'],
                'count' => $count,
                'disabled' => $count === 0,
            ];
        }
        usort(
            $items,
            static fn (array $a, array $b): int => [$b['count'], $a['value']] <=> [$a['count'], $b['value']]
        );
        return $items;
    }

    public function getServicesById(): array
    {
        return array_column($this->services, null, 'id');
    }
}
