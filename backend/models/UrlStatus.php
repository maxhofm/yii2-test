<?php

namespace backend\models;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * User model
 *
 * @property string $hash_string
 * @property string $created_at
 * @property string $updated_at
 * @property string $url
 * @property integer $status_code
 * @property integer $query_count
 */
class UrlStatus extends ActiveRecord
{
    const TIMEOUT_CODE = 0;

    public static function getDb()
    {
        return Yii::$app->dbSqlite;
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%url_status}}';
    }

    public function behaviors(): array
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    self::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    self::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
                 'value' => date("Y-m-d H:i:s"),
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['hash_string', 'created_at', 'updated_at', 'url'], 'required'],
            [['status_code', 'query_count'], 'integer'],
            [['created_at', 'updated_at'], 'default', 'value' => date("Y-m-d H:i:s")],
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
        if (is_null($this->query_count)) {
            $this->query_count = 0;
        }
        $this->query_count++;
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

    /**
     * @param string $method
     * @param string $url
     * @return void
     * @throws GuzzleException
     */
    public function setExternalRequestCode(string $method, string $url): void
    {
        try {
            $client = new Client();
            $response = $client->request($method, $url, ['connect_timeout' => 5]);
            $code = $response->getStatusCode();
        } catch (Exception $e) {
            $code = UrlStatus::TIMEOUT_CODE;
        }
        $this->status_code = $code;
    }

    /**
     * Получение статистики по запросам за последние 24 часа у которых статус не 200
     * @return array
     */
    public static function getStatisticAsArray(): array
    {
        return self::find()
            ->select(['url', 'status_code'])
            ->where(['!=', 'status_code', 200])
            ->andWhere(['>', 'updated_at', date("Y-m-d H:i:s", strtotime("-1 day"))])
            ->orderBy(['updated_at' => SORT_DESC])
            ->asArray()
            ->all();
    }

}
