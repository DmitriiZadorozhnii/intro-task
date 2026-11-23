<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%users}}`.
 */
class m261123_113928_create_users_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            CREATE TABLE `users` (
              `id` int NOT NULL AUTO_INCREMENT,
              `first_name` varchar(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
              `last_name` varchar(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=COMPACT
        ");

        $this->execute("ALTER TABLE `users` AUTO_INCREMENT = 101");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute("DROP TABLE IF EXISTS `users`");
    }
}
