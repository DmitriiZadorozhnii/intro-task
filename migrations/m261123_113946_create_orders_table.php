<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%orders}}`.
 */
class m261123_113946_create_orders_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            CREATE TABLE `orders` (
              `id` int NOT NULL AUTO_INCREMENT,
              `user_id` int NOT NULL,
              `link` varchar(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_estonian_ci NOT NULL,
              `quantity` int NOT NULL,
              `service_id` int NOT NULL,
              `status` tinyint(1) NOT NULL COMMENT '0 - Pending, 1 - In progress, 2 - Completed, 3 - Canceled, 4 - Fail',
              `created_at` int NOT NULL,
              `mode` tinyint(1) NOT NULL COMMENT '0 - Manual, 1 - Auto',
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=COMPACT
        ");

        $this->execute("ALTER TABLE `orders` AUTO_INCREMENT = 100001");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute("DROP TABLE IF EXISTS `orders`");
    }
}
