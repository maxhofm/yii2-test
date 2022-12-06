<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%url_status}}`.
 */
class m221205_193058_create_url_status_table extends Migration
{
    protected function getDb()
    {
        return Yii::$app->dbSqlite;
    }

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%url_status}}', [
            'hash_string' => $this->string(32)->unique()->notNull(),
            'created_at' => $this->dateTime()->notNull(),
            'updated_at' => $this->dateTime()->notNull(),
            'url' => $this->string(255)->notNull()->unique(),
            'status_code' => $this->integer(3),
            'query_count' => $this->integer(),
            'PRIMARY KEY(hash_string)',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // Запрещаем откат миграции на продакшн среде
        if (!YII_ENV_PROD) {
            $this->dropTable('{{%url_status}}');
        }
    }
}
