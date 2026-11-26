<?php

namespace app\modules\orders_listing;

class Module extends \yii\base\Module
{
    public $controllerNamespace = 'app\modules\orders_listing\controllers';
    public $layout = '@app/modules/orders_listing/views/layouts/main';

    public function init()
    {
        parent::init();
    }
}
