<?php

namespace app\modules\orders_listing\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "services".
 *
 * @property int $id
 * @property string $name
 */
class Service extends ActiveRecord
{
    public static function tableName()
    {
        return 'services';
    }
}
