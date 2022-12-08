<?php

namespace frontend\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "gender".
 *
 * @property int $id
 * @property string $name
 *
 * @property UserProfile[] $userProfiles
 */
class Gender extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'gender';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 50],
            [['name'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
        ];
    }

    /**
     * Gets query for [[UserProfiles]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUserProfiles()
    {
        return $this->hasMany(UserProfile::class, ['gender_id' => 'id']);
    }

    /**
     * @return array|ActiveRecord
     */
    public static function findAllAsArray()
    {
        $items = self::find()
            ->select(['id', 'name'])
            ->orderBy(['id' => SORT_ASC])
            ->indexBy('id')
            ->asArray()
            ->all();

        if (is_array($items) && !empty($items)) {
            array_walk($items, function ($value, $key) use (&$items) {
                $items[$key] = $value['name'];
            });
        }
        return $items;
    }
}
