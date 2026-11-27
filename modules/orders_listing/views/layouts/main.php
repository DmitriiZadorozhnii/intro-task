<?php

declare(strict_types=1);

use app\modules\orders_listing\assets\OrdersAsset;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/** @var View $this */
/** @var string $content */

OrdersAsset::register($this);

$this->registerCss(<<<'CSS'
.label-default {
    border: 1px solid #ddd;
    background: none;
    color: #333;
    min-width: 30px;
    display: inline-block;
}
CSS);
?>
<?php $this->beginPage() ?>
    <!DOCTYPE html>
    <html lang="<?= Html::encode(Yii::$app->language) ?>">
    <head>
        <meta charset="<?= Html::encode(Yii::$app->charset) ?>">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?= Html::encode($this->title ?: Yii::t('orders', 'Orders')) ?></title>
        <?php $this->head() ?>
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>
    <body>
    <?php $this->beginBody() ?>
    <nav class="navbar navbar-fixed-top navbar-default">
        <div class="container-fluid">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-navbar-collapse">
                    <span class="sr-only"><?= Yii::t('orders', 'Toggle navigation') ?></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
            </div>
            <div class="collapse navbar-collapse" id="bs-navbar-collapse">
                <ul class="nav navbar-nav">
                    <li class="active"><a href="<?= Html::encode(Url::to(['order/index'])) ?>"><?= Yii::t('orders', 'Orders') ?></a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <?= $content ?>
    </div>

    <?php $this->endBody() ?>
    </body>
    </html>
<?php $this->endPage() ?>
