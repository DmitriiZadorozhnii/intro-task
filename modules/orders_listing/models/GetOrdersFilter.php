<?php

namespace app\modules\orders_listing\models;

use app\modules\orders_listing\enums\order\OrderModeEnum;
use app\modules\orders_listing\enums\order\OrderStatusEnum;
use app\modules\orders_listing\enums\order\SearchTypeEnum;
use yii\base\Model;

class GetOrdersFilter extends Model
{
    public ?string $status = null;
    public ?string $search = null;
    public ?int $searchType = null;
    public ?int $serviceId = null;
    public ?string $mode = null;

    public function formName(): string
    {
        return '';
    }

    public function rules(): array
    {
        return [
            [['status', 'search', 'mode'], 'trim'],
            [['status', 'search', 'mode'], 'default', 'value' => null],

            ['status', 'string', 'max' => 30],
            ['status', 'in', 'range' => OrderStatusEnum::getNames()],

            ['search', 'string', 'max' => 610],

            ['searchType', 'integer'],
            ['searchType', 'in', 'range' => SearchTypeEnum::getValues()],
            ['searchType', 'default', 'value' => SearchTypeEnum::id->value, 'when' => fn () => $this->search !== null],

            [
                'search',
                'integer',
                'min' => 0,
                'when' => fn () => $this->search !== null && (int) $this->searchType === SearchTypeEnum::id->value,
            ],

            ['serviceId', 'integer', 'min' => 0],

            ['mode', 'string', 'max' => 30],
            ['mode', 'in', 'range' => OrderModeEnum::getNames()],
        ];
    }

    public function load($data, $formName = null): bool
    {
        if (!is_array($data)) {
            return false;
        }
        if (array_key_exists('search-type', $data)) {
            $data['searchType'] = $data['search-type'];
            $data['searchType'] = !is_null($data['searchType']) ? (int) $data['searchType'] : null;
        }
        return parent::load($data, $formName);
    }
}
