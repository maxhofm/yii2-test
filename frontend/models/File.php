<?php

namespace frontend\models;

use yii\db\ActiveRecord;
use yii\helpers\Url;

/**
 * This is the model class for table "file".
 *
 * @property int $id
 * @property string|null $path
 *
 * @property UserProfile[] $userProfiles
 */
class File extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'file';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['path'], 'string', 'max' => 100],
            [['path'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'path' => 'Path',
        ];
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return Url::to(['file/load/', 'id' => $this->id]);
    }

    /**
     * Gets query for [[UserProfiles]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUserProfiles()
    {
        return $this->hasMany(UserProfile::class, ['photo_id' => 'id']);
    }
}
