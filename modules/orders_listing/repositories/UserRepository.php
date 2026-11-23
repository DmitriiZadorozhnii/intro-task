<?php

namespace app\modules\orders_listing\repositories;

use app\modules\orders_listing\models\User;

class UserRepository
{
    public function getAllUsers(): array
    {
        return User::find()->all();
    }
}
