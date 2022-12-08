<?php

namespace frontend\models;

use common\models\User;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "user_profile".
 *
 * @property int $id
 * @property int $user_id
 * @property int $gender_id
 * @property int $photo_id
 * @property string|null $name
 * @property string|null $surname
 *
 * @property Gender $gender
 * @property File $photo
 * @property User $user
 * @property UserProfileComment $userProfileComment
 */
class UserProfile extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_profile';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['user_id', 'gender_id', 'photo_id'], 'integer'],
            [['name', 'surname'], 'string', 'max' => 50],
            [['user_id'], 'unique'],
            [['gender_id'], 'exist', 'skipOnError' => true, 'targetClass' => Gender::class, 'targetAttribute' => ['gender_id' => 'id']],
            [['photo_id'], 'exist', 'skipOnError' => true, 'targetClass' => File::class, 'targetAttribute' => ['photo_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'Пользователь',
            'gender_id' => 'Пол',
            'photo_id' => 'Аватар',
            'name' => 'Имя',
            'surname' => 'Фамилия',
        ];
    }

    /**
     * Gets query for [[Gender]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getGender()
    {
        return $this->hasOne(Gender::class, ['id' => 'gender_id']);
    }

    /**
     * Gets query for [[Photo]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPhoto()
    {
        return $this->hasOne(File::class, ['id' => 'photo_id']);
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * Gets query for [[UserProfileComment]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getComments()
    {
        return $this->hasMany(UserProfileComment::class, ['user_profile_id' => 'id']);
    }
}
