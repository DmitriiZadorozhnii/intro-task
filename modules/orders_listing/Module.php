<?php

namespace app\modules\orders_listing;

use Yii;
use yii\i18n\PhpMessageSource;

class Module extends \yii\base\Module
{
    public $controllerNamespace = 'app\modules\orders_listing\controllers';
    public $layout = '@app/modules/orders_listing/views/layouts/main';

    public function init()
    {
        parent::init();
        $this->registerTranslations();
    }

    private function registerTranslations(): void
    {
        if (!isset(Yii::$app->i18n->translations['orders'])) {
            Yii::$app->i18n->translations['orders'] = [
                'class' => PhpMessageSource::class,
                'basePath' => '@app/modules/orders_listing/messages',
                'sourceLanguage' => 'en',
            ];
        }
    }
}
