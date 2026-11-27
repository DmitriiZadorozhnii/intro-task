<?php

namespace app\modules\orders_listing\enums\order;

use Yii;

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

    public function label(): string
    {
        return Yii::t('orders', match ($this) {
            self::id => 'Order ID',
            self::link => 'Link',
            self::username => 'Username',
        });
    }

    /**
     * @return array<int, string> [value => label]
     */
    public static function options(): array
    {
        $options = [];
        foreach (self::cases() as $case) {
            $options[$case->value] = $case->label();
        }

        return $options;
    }
}
