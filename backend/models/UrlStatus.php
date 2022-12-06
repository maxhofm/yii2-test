<?php

namespace backend\models;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Connection;

/**
 * User model
 *
 * @property string $hash_string    Хэш url
 * @property string $created_at     Дата создания
 * @property string $updated_at     Дата редактирования
 * @property string $url            URL ресура
 * @property integer $status_code   Код ответа
 * @property integer $query_count   Кол-во запросов
 */
class UrlStatus extends ActiveRecord
{
    const TIMEOUT_CODE = 0;

    /**
     * Установка БД для модели
     * @return mixed|object|Connection|null
     */
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

    /**
     * Поведения модели
     * @return array[]
     */
    public function behaviors(): array
    {
        return [
            [
                // Установка текущего времени для полей created_at и updated_at
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
    public function rules(): array
    {
        return [
            [['hash_string', 'created_at', 'updated_at', 'url'], 'required'],
            [['status_code', 'query_count'], 'integer'],
            [['created_at', 'updated_at'], 'default', 'value' => date("Y-m-d H:i:s")],
            ['status_code', 'default', 'value' => self::TIMEOUT_CODE],
        ];
    }

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
     * Установка кода ответа запроса к внешнему ресурсу
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

    //    public function optimisticLock()
//    {
//        //todo: вынести
//        return 'updated_at';
//    }
}
