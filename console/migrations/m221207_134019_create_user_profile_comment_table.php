<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%user_profile_comment}}`.
 */
class m221207_134019_create_user_profile_comment_table extends Migration
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

        $this->createTable('user_profile_comment', [
            'id' => $this->primaryKey(),
            'user_profile_id' => $this->integer()->notNull(),
            'text' => $this->text()->notNull(),
        ], $tableOptions);

        // add foreign key for table `user_profile`
        $this->addForeignKey(
            'fk-user_profile_comment-user_profile_id',
            'user_profile_comment',
            'user_profile_id',
            'user_profile',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `user_profile_comment`
        $this->dropForeignKey(
            'fk-user_profile_comment-user_profile_id',
            'user_profile_comment'
        );

        $this->dropTable('user_profile_comment');
    }
}
