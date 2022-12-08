<?php

namespace frontend\models;

use yii\helpers\Url;
use Yii;
use yii\base\Model;

/**
 * User Profile form
 */
class UserProfileForm extends Model
{
    public $id;
    public $name;
    public $surname;
    public $gender_id;
    public $imgFile;
    private $_profile;
    private $_fileId;
    private $_fileUrl;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['imgFile'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg'],
            [['gender_id'], 'integer'],
            [['name', 'surname'], 'string', 'max' => 50],
            [['gender_id'], 'exist', 'skipOnError' => true, 'targetClass' => Gender::class, 'targetAttribute' => ['gender_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'gender_id' => 'Пол',
            'imgFile' => 'Выбрать фото',
            'name' => 'Имя',
            'surname' => 'Фамилия',
        ];
    }

    public function loadFromProfileId($id)
    {
        $this->id = $id;
        if (!empty($this->profile)) {
            $this->name = $this->profile->name;
            $this->surname = $this->profile->surname;
            $this->gender_id = $this->profile->gender_id;
        }
    }

    public function save()
    {
        if (!$this->validate()) {
            return false;
        }

        // Пытаемся сохранить файл если есть
        if ($this->saveFile()) {
            // Сохраняем профиль
            $this->profile->name = $this->name;
            $this->profile->surname = $this->surname;
            $this->profile->gender_id = $this->gender_id;
            $this->profile->photo_id = $this->fileId;
            if (!$this->profile->save()) {
                return false;
            }
        }

        return true;
    }

    protected function saveFile()
    {
        if (!is_null($this->imgFile)) {
            // Сохраняем файл
            $newFileName = Yii::$app->security->generateRandomString(12);
            $path = Yii::getAlias('@frontend') . "/web/files/img/{$newFileName}.{$this->imgFile->extension}";
            if (!$this->imgFile->saveAs($path)) {
                return false;
            }
            // Сохраняем модель в базу
            $file = new File();
            $file->path = $path;
            if (!$file->save()) {
                unlink($path);
                return false;
            }
            $this->fileId = $file->id;
        }
        return true;
    }

    /**
     * @param mixed $fileId
     */
    public function setFileId($fileId): void
    {
        $this->_fileId = $fileId;
    }

    /**
     * @return mixed
     */
    public function getFileId()
    {
        return $this->_fileId;
    }

    /**
     * @return mixed
     */
    public function getFileUrl()
    {
        if (is_null($this->_fileUrl) && !empty($this->profile->photo_id)) {
            $this->_fileUrl = Url::to(['file/load/', 'id' => $this->profile->photo_id]);
        }
        return $this->_fileUrl;
    }

    /**
     * Finds user by [[username]]
     *
     * @return UserProfile|null
     */
    protected function getProfile()
    {
        if ($this->_profile === null) {
            $this->_profile = UserProfile::findOne($this->id);
        }
        return $this->_profile;
    }

}
