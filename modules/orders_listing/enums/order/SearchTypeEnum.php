<?php

namespace app\modules\orders_listing\enums\order;

enum SearchTypeEnum: int
{
    case id = 1;
    case link = 2;
    case username = 3;

    public static function toArray(): array
    {
        return array_column(self::cases(), 'value', 'name');
    }

    public static function getValues(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function getNames(): array
    {
        return array_column(self::cases(), 'name');
    }

    public static function fromName(string $name): self
    {
        return self::from(self::getValueByName($name));
    }

    public static function getValueByName(string $name): ?int
    {
        return self::toArray()[$name] ?? null;
    }
}
