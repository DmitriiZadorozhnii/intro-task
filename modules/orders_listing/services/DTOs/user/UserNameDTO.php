<?php

namespace app\modules\orders_listing\services\DTOs\user;

class UserNameDTO
{
    public function __construct(protected string $firstName, protected string $lastName)
    {
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getFullName(): string
    {
        return $this->lastName . ' ' . $this->firstName;
    }
}
