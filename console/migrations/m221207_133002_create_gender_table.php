<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%gender}}`.
 */
class m221207_133002_create_gender_table extends Migration
{
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

        $this->createTable('gender', [
            'id' => $this->primaryKey(),
            'name' => $this->string(50)->unique()->notNull(),
        ]);

        // creates index for column `name`
        $this->createIndex(
            'idx-gender-name',
            'gender',
            'name'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops index for column `name`
        $this->dropIndex(
            'idx-gender-name',
            'gender'
        );

        $this->dropTable('gender');
    }
}
