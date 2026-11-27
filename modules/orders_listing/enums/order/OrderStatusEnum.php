<?php

namespace app\modules\orders_listing\enums\order;

use Yii;

enum OrderStatusEnum: int
{
    case Pending = 0;
    case In_progress = 1;
    case Completed = 2;
    case Canceled = 3;
    case Error = 4;

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
            self::Pending => 'Pending',
            self::In_progress => 'In progress',
            self::Completed => 'Completed',
            self::Canceled => 'Canceled',
            self::Error => 'Error',
        });
    }

    public static function labelFor(int|string|null $value): string
    {
        return self::tryFrom((int) $value)?->label() ?? (string) $value;
    }
}
