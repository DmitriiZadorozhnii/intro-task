<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%services}}`.
 */
class m261123_113938_create_services_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            CREATE TABLE `services` (
              `id` int NOT NULL AUTO_INCREMENT,
              `name` varchar(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=COMPACT
        ");

        $this->execute("ALTER TABLE `services` AUTO_INCREMENT = 18");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute("DROP TABLE IF EXISTS `services`");
    }
}
