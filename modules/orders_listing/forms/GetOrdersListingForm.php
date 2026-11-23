<?php

namespace app\modules\orders_listing\forms;

use app\modules\orders_listing\enums\order\OrderModeEnum;
use app\modules\orders_listing\enums\order\OrderStatusEnum;
use app\modules\orders_listing\forms\entities\GetOrdersListingEntity;
use yii\base\Model;

class GetOrdersListingForm extends Model
{
    public $id;
    public $status;
    public $mode;
    public $link;
    public $service_id;
    public $user_first_name;
    public $user_last_name;

    public function rules()
    {
        return [
            [['id', 'status', 'mode', 'link', 'service_id', 'user_first_name', 'user_last_name'], 'nullable'],
            ['id', 'integer'],
            ['status', 'in', 'range' => OrderStatusEnum::getNames()],
            ['mode', 'in', 'range' => OrderModeEnum::getNames()],
            ['link', 'string', 'max' => 255],
            ['service_id', 'validateServiceId', 'skipOnEmpty' => true],
            ['user_first_name', 'string', 'max' => 255],
            ['user_last_name', 'string', 'max' => 255],
        ];
    }

    public function entity(): null|GetOrdersListingEntity
    {
        if (!$this->validate()) {
            return null;
        }
        return new GetOrdersListingEntity(
            $this->id,
            OrderStatusEnum::getValueByName($this->status),
            OrderModeEnum::getValueByName($this->mode),
            $this->link,
            $this->service_id,
            $this->user_first_name,
            $this->user_last_name
        );
    }
}
