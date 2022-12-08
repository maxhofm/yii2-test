<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%user_profile}}`.
 */
class m221207_133942_create_user_profile_table extends Migration
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

        $this->createTable('user_profile', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull()->unique(),
            'gender_id' => $this->integer(),
            'photo_id' => $this->integer(),
            'name' => $this->string(50),
            'surname' => $this->string(50),
        ], $tableOptions);

        // creates index for column `user_id`
        $this->createIndex(
            'idx-user_profile-user_id',
            'user_profile',
            'user_id'
        );
        // add foreign key for table `user`
        $this->addForeignKey(
            'fk-user_profile-user_id',
            'user_profile',
            'user_id',
            'user',
            'id',
            'CASCADE'
        );

        // creates index for column `gender_id`
        $this->createIndex(
            'idx-user_profile-gender_id',
            'user_profile',
            'gender_id'
        );
        // add foreign key for table `gender`
        $this->addForeignKey(
            'fk-user_profile-gender_id',
            'user_profile',
            'gender_id',
            'gender',
            'id',
            'CASCADE'
        );

        // add foreign key for table `photo`
        $this->addForeignKey(
            'fk-user_profile-photo_id',
            'user_profile',
            'photo_id',
            'file',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `user`
        $this->dropForeignKey(
            'fk-user_profile-user_id',
            'user_profile'
        );
        // drops index for column `user_id`
        $this->dropIndex(
            'idx-user_profile-user_id',
            'user_profile'
        );

        // drops foreign key for table `user`
        $this->dropForeignKey(
            'fk-user_profile-gender_id',
            'user_profile'
        );
        // drops index for column `gender_id`
        $this->dropIndex(
            'idx-user_profile-gender_id',
            'user_profile'
        );

        // drops foreign key for table `file`
        $this->dropForeignKey(
            'fk-user_profile-photo_id',
            'user_profile'
        );

        $this->dropTable('user_profile');
    }
}
