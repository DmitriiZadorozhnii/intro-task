<?php

namespace app\modules\orders_listing\forms\entities;

class GetOrdersListingEntity
{
    public function __construct(
        protected readonly ?int $id,
        protected readonly ?int $status,
        protected readonly ?int $mode,
        protected readonly ?string $link,
        protected readonly ?int $serviceId,
        protected readonly ?string $userFirstName,
        protected readonly ?string $userLastName,
    ) {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function getMode(): ?int
    {
        return $this->mode;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function getServiceId(): ?int
    {
        return $this->serviceId;
    }

    public function getUserFirstName(): ?string
    {
        return $this->userFirstName;
    }

    public function getUserLastName(): ?string
    {
        return $this->userLastName;
    }
}
