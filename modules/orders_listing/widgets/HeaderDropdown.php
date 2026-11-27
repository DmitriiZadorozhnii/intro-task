<?php

namespace app\modules\orders_listing\widgets;

use Yii;
use yii\base\Widget;
use yii\helpers\Url;

class HeaderDropdown extends Widget
{
    public string $label = '';
    public string $param = '';
    public array $items = [];
    public string|int|null $current = null;
    public ?string $allLabel = null;

    public function run(): string
    {
        $current = ($this->current === null || $this->current === '') ? null : (string) $this->current;

        $menu = [[
            'label' => $this->allLabel ?? Yii::t('orders', 'All'),
            'url' => Url::current([$this->param => null, 'page' => null]),
            'active' => $current === null,
            'disabled' => false,
        ]];

        foreach ($this->items as $item) {
            $disabled = (bool) ($item['disabled'] ?? false);
            $menu[] = [
                'prefix' => $item['prefix'] ?? null,
                'label' => $item['label'],
                'url' => $disabled ? '#' : Url::current([$this->param => $item['value'], 'page' => null]),
                'active' => (string) $item['value'] === $current,
                'disabled' => $disabled,
            ];
        }

        return $this->render('header-dropdown', [
            'id' => $this->getId(),
            'label' => $this->label,
            'items' => $menu,
        ]);
    }
}
