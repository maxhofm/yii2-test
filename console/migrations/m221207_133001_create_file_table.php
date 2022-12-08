<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%file}}`.
 */
class m221207_133001_create_file_table extends Migration
{
    protected function getDb()
    {
        return Yii::$app->db;
    }

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // todo: test
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('file', [
            'id' => $this->primaryKey(),
            'path' => $this->string(100)->unique(),
        ], $tableOptions);

        // creates index for column `user_id`
        $this->createIndex(
            'idx-file-path',
            'file',
            'path'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops index for column `path`
        $this->dropIndex(
            'idx-file-path',
            'file'
        );

        $this->dropTable('file');
    }
}
