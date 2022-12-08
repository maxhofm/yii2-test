<?php

namespace frontend\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "user_profile_comment".
 *
 * @property int $id
 * @property int $user_profile_id
 * @property string $text
 *
 * @property UserProfile $userProfile
 */
class UserProfileComment extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_profile_comment';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_profile_id', 'text'], 'required'],
            [['user_profile_id'], 'integer'],
            [['text'], 'string'],
            [['user_profile_id'], 'exist', 'skipOnError' => true, 'targetClass' => UserProfile::class, 'targetAttribute' => ['user_profile_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_profile_id' => '',
            'text' => 'Комментарий',
        ];
    }

    /**
     * Gets query for [[UserProfile]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUserProfile()
    {
        return $this->hasOne(UserProfile::class, ['id' => 'user_profile_id']);
    }
}
