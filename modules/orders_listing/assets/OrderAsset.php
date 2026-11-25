<?php

namespace app\modules\orders_listing\assets;

use yii\web\AssetBundle;

class OrderAsset extends AssetBundle
{
    public function __construct()
    {
        $this->sourcePath = __DIR__;
        parent::__construct();
    }

    public $css = [
        'css/bootstrap.min.css',
        'css/custom.css',
    ];
    public $js = [
        'js/jquery.min.js',
        'js/bootstrap.min.js',
    ];

    public $depends = [];
}
