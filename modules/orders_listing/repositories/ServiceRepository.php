<?php

namespace app\modules\orders_listing\repositories;

use app\modules\orders_listing\models\Service;

class ServiceRepository
{
    public function getAllServices(): array
    {
        return Service::find()->select(['id', 'name'])->asArray()->all();
    }
}
