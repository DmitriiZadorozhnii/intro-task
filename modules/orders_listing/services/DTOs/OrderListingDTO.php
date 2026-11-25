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
        protected string $createdAt,
    ) {
    }

    public function getOrderId(): int
    {
        return $this->orderId;
    }

    public function getUserNameDTO(): UserNameDTO
    {
        return $this->userNameDTO;
    }

    public function getLink(): string
    {
        return $this->link;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getServiceCounter(): ServiceCounterDTO
    {
        return $this->serviceCounter;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getMode(): string
    {
        return $this->mode;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }
}
