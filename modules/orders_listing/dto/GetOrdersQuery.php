<?php

namespace app\modules\orders_listing\dto;

use app\modules\orders_listing\enums\order\OrderModeEnum;
use app\modules\orders_listing\enums\order\OrderStatusEnum;
use app\modules\orders_listing\enums\order\SearchTypeEnum;
use app\modules\orders_listing\models\GetOrdersFilter;

class GetOrdersQuery
{
    public function __construct(
        protected ?int $status,
        protected ?string $search,
        protected ?int $searchType,
        protected ?int $serviceId,
        protected ?int $mode,
    ) {
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function getSearch(): ?string
    {
        return $this->search;
    }

    public function getSearchType(): ?int
    {
        return $this->searchType;
    }

    public function getServiceId(): ?int
    {
        return $this->serviceId;
    }

    public function getMode(): ?int
    {
        return $this->mode;
    }

    public static function fromFilter(GetOrdersFilter $filter): self
    {
        $searchType = $filter->searchType !== null ? (int) $filter->searchType : null;
        $serviceId = $filter->serviceId !== null ? (int) $filter->serviceId : null;
        $search = $filter->search;
        if ($search !== null && $searchType === SearchTypeEnum::id->value) {
            $search = (string) (int) $search;
        }
        return new self(
            !is_null($filter->status) ? OrderStatusEnum::getValueByName($filter->status) : null,
            $search,
            $searchType,
            $serviceId,
            !is_null($filter->mode) ? OrderModeEnum::getValueByName($filter->mode) : null,
        );
    }
}
