<?php

namespace app\modules\orders_listing\services\DTOs\service;

class ServiceCounterDTO
{
    public function __construct(
        protected int $id,
        protected string $name,
        protected int $count,
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCount(): int
    {
        return $this->count;
    }
}
