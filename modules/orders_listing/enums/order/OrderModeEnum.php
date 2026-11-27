<?php

namespace app\modules\orders_listing\enums\order;

use Yii;

enum OrderModeEnum: int
{
    case Manual = 0;
    case Auto = 1;

    public static function toArray(): array
    {
        return array_column(self::cases(), 'value', 'name');
    }

    public static function getNames(): array
    {
        return array_column(self::cases(), 'name');
    }

    public static function getValueByName(string $name): ?int
    {
        return self::toArray()[$name] ?? null;
    }

    public function label(): string
    {
        return Yii::t('orders', match ($this) {
            self::Manual => 'Manual',
            self::Auto => 'Auto',
        });
    }

    public static function labelFor(int|string|null $value): string
    {
        return self::tryFrom((int) $value)?->label() ?? (string) $value;
    }

    public static function dropdownItems(): array
    {
        return array_map(
            static fn (self $case): array => ['value' => $case->name, 'label' => $case->label()],
            self::cases()
        );
    }
}
