<?php

namespace app\modules\orders_listing;

/**
 * orders module definition class
 */
class Module extends \yii\base\Module
{
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'app\modules\orders_listing\controllers';

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        //$this->setViewPath('@app/modules/orders_listing/views');

        \Yii::$app->i18n->translations['modules/orders_listing/*'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'basePath' => '@app/modules/orders_listing/messages',
            'sourceLanguage' => 'en-US',
        ];
    }
}
