<?php

namespace backend\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * User model
 *
 * @property string $hash_string
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $url
 * @property integer $status_code
 * @property integer $query_count
 */
class UrlStatus extends ActiveRecord
{
    protected $query_count;

    const TIMEOUT_CODE = 0;

    public static function getDb()
    {
        return Yii::$app->dbSqlite;
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%url_status}}';
    }


//    private $_volume;
//
//    public function setHashString($volume)
//    {
//        $this->_volume = (float) $volume;
//    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['hash_string', 'created_at', 'updated_at', 'url'], 'required'],
            [['status_code', 'query_count'], 'integer'],
            ['status_code', 'default', 'value' => self::TIMEOUT_CODE],
        ];
    }

//    public function optimisticLock()
//    {
//        // вынести
//        return 'updated_at';
//    }


    /**
     * Обновить счетик запросов
     * @return void
     */
    public function updateQueryCount()
    {
        $count = $this->query_count;
        $this->query_count = is_null($count) ? 1 : $count++;
    }


    /**
     * Поиск модели по хэшу
     * @param string $hash
     * @return UrlStatus|null
     */
    protected static function findByHash(string $hash): ?UrlStatus
    {
        return static::findOne(['hash_string' => $hash]);
    }

    /**
     * Поиск модели по url
     * @param string $url
     * @return UrlStatus|null
     */
    public static function findByUrl(string $url): ?UrlStatus
    {
        return self::findByHash(self::generateHashByUrl($url));
    }

    /**
     * Сгенерировать хэш по предоставленному url
     * @param string $url
     * @return string
     */
    public static function generateHashByUrl(string $url): string
    {
        return md5($url);
    }
}
