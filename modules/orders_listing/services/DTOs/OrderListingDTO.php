<?php

namespace app\modules\orders_listing\services\DTOs;

use app\modules\orders_listing\services\DTOs\service\ServiceCounterDTO;
use app\modules\orders_listing\services\DTOs\user\UserNameDTO;
use DateTimeImmutable;

class OrderListingDTO
{
    public function __construct(
        protected int $orderId,
        protected UserNameDTO $userNameDTO,
        protected string $link,
        protected int $quantity,
        protected ServiceCounterDTO $serviceCounter,
        protected string $status,
        protected string $mode,
        protected DateTimeImmutable $createdAt,
    ) {
    }

    public function getOrderId(): int
    {
        return $this->orderId;
    }
}
