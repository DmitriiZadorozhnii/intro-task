<?php

namespace app\modules\orders_listing\models;

use app\modules\orders_listing\enums\order\OrderModeEnum;
use app\modules\orders_listing\enums\order\OrderStatusEnum;
use app\modules\orders_listing\enums\order\SearchTypeEnum;
use Yii;
use yii\base\Model;

class GetOrdersFilter extends Model
{
    public const DEFAULT_PAGE = 1;

    public mixed $status = null;
    public mixed $search = null;
    public mixed $searchType = null;
    public mixed $serviceId = null;
    public mixed $mode = null;
    public mixed $page = self::DEFAULT_PAGE;

    public static function fromRequest(array $params): self
    {
        $filter = new self();
        $filter->load($params);
        $filter->validate();

        return $filter;
    }

    public function getErrorMessages(): array
    {
        return array_values($this->getFirstErrors());
    }

    public function attributeLabels(): array
    {
        return [
            'status' => Yii::t('orders', 'Status'),
            'search' => Yii::t('orders', 'Search'),
            'searchType' => Yii::t('orders', 'Search type'),
            'serviceId' => Yii::t('orders', 'Service'),
            'mode' => Yii::t('orders', 'Mode'),
            'page' => Yii::t('orders', 'Page'),
        ];
    }

    public function formName(): string
    {
        return '';
    }

    public function rules(): array
    {
        $idInvalid = Yii::t('orders', 'Order ID must be a positive integer');

        return [
            [['status', 'search', 'mode', 'serviceId'], 'trim', 'skipOnArray' => true],
            [['status', 'search', 'mode', 'serviceId', 'searchType'], 'default', 'value' => null],

            ['status', 'string', 'max' => 30],
            ['status', 'in', 'range' => OrderStatusEnum::getNames()],

            ['searchType', 'default', 'value' => SearchTypeEnum::id->value, 'when' => fn () => $this->search !== null],
            ['searchType', 'integer'],
            ['searchType', 'in', 'range' => SearchTypeEnum::getValues()],

            [
                'search',
                'integer',
                'min' => 1,
                'message' => $idInvalid,
                'tooSmall' => $idInvalid,
                'when' => fn () => $this->isIdSearch(),
            ],
            [
                'search',
                'string',
                'max' => 610,
                'message' => Yii::t('orders', 'Search query must be a string'),
                'tooLong' => Yii::t('orders', 'Search query must not exceed {max} characters'),
                'when' => fn () => !$this->isIdSearch(),
            ],

            ['serviceId', 'integer', 'min' => 0],

            ['mode', 'string', 'max' => 30],
            ['mode', 'in', 'range' => OrderModeEnum::getNames()],

            ['page', 'default', 'value' => self::DEFAULT_PAGE],
            ['page', 'integer', 'min' => 1],
        ];
    }

    public function load($data, $formName = null): bool
    {
        if (!is_array($data)) {
            return false;
        }
        if (array_key_exists('search-type', $data)) {
            $data['searchType'] = $data['search-type'];
        }

        return parent::load($data, $formName);
    }

    public function toQueryParams(): array
    {
        return array_filter([
            'status' => $this->status,
            'search' => $this->search,
            'search-type' => $this->search !== null ? $this->searchType : null,
            'serviceId' => $this->serviceId,
            'mode' => $this->mode,
        ], static fn ($value) => is_scalar($value) && $value !== '');
    }

    private function isIdSearch(): bool
    {
        return $this->search !== null
            && is_scalar($this->searchType)
            && (int) $this->searchType === SearchTypeEnum::id->value;
    }
}
