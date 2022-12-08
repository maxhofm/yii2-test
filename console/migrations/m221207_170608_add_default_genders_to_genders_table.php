<?php

use yii\db\Migration;

/**
 * Class m221207_170608_add_default_genders_to_genders_table
 */
class m221207_170608_add_default_genders_to_genders_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->getDb()->createCommand()->batchInsert('gender', ['name'], [
            [
                'name' => 'Мужской',
            ],
            [
                'name' => 'Женский'
            ]
        ])->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->getDb()->createCommand()->delete('gender');

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221207_170608_add_default_genders_to_genders_table cannot be reverted.\n";

        return false;
    }
    */
}
