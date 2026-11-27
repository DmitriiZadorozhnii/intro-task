<?php

namespace app\modules\orders_listing\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "users".
 *
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 */
class User extends ActiveRecord
{
    public static function tableName()
    {
        return 'users';
    }

    public function getOrders()
    {
        return $this->hasMany(Order::class, ['user_id' => 'id']);
    }
}
