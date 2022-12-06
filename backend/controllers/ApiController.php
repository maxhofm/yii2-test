<?php

namespace backend\controllers;

use backend\models\UrlStatus;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Promise;
use GuzzleHttp\Exception\GuzzleException;
use Yii;
use yii\filters\auth\HttpBearerAuth;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\Response;
use yii\web\ServerErrorHttpException;

/**
 * Api controller
 */
class ApiController extends Controller
{
    /**
     * Поведения контроллера
     * @return array
     */
    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
        // Аутентификация перед запросом к API
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::class,
        ];
        return $behaviors;
    }

    /**
     * Запрос к api для проверки статуса url
     * @return array[]
     * @throws BadRequestHttpException
     * @throws GuzzleException
     * @throws ServerErrorHttpException
     */
    public function actionCheckStatus(): array
    {
        // Получаем данные запроса
        $request = $this->request;
        $urls = $request->post('url');
        $response = Yii::$app->response;
        $response->format = Response::FORMAT_JSON;

        // Проверяем наличие списка url в запросе
        if (!is_array($urls) || empty($urls)) {
            throw new BadRequestHttpException();
        }

        try {
            $client = new Client();
            $urls = array_unique($urls);
            $promises = [];
            $codes = [];

            // Находим запросы которые были сделаны меннее 10 мин назад
            $recentUrls = UrlStatus::getRecentRequestsStatisticByUrl($urls);

            // Создаем промисы на url по которым необходимо сделать запросы
            foreach ($urls as $url) {
                if (!isset($recentUrls[$url])) {
                    $promises[$url] = $client->getAsync($url);
                }
            }

            // Выполняем запросы ассинхронно
            $urlResponses = Promise\Utils::settle($promises)->wait();
        } catch (Exception $e) {
            throw new ServerErrorHttpException();
        }

        // Обрабатываем каждый url
        foreach ($urls as $url) {
            try {
                if (isset($urlResponses[$url]['state']) && $urlResponses[$url]['state'] === 'rejected') {
                    throw new Exception("Не удалось сделать запрос к {$url}");
                }

                // Находим модель в базе либо создаем новую
                $urlStatus = UrlStatus::findByUrl($url);
                if (empty($urlStatus)) {
                    $urlStatus = new UrlStatus();
                    $nowDate = date("Y-m-d H:i:s");
                    $data = [
                        'hash_string' => UrlStatus::generateHashByUrl($url),
                        'url' => $url,
                        'query_count' => 1,
                        'created_at' => $nowDate,
                        'updated_at' => $nowDate,
                    ];
                    $urlStatus->load($data, '');
                }

                // Если прошло 10 минут с момента последнего запроса или новый url
                if (!isset($recentUrls[$url])) {
                    // Берем код из ответа промиса
                    $urlStatus->status_code = $urlResponses[$url]['value']->getStatusCode();
                }

                // Обновляем счечик запросов
                $urlStatus->updateQueryCount();

                if (!$urlStatus->save()) {
                    throw new \yii\db\Exception("Не удалось сохранить модель в базу");
                }
                $code = $urlStatus->status_code;
            } catch (Exception $e) {
                $code = -1;
                Yii::error($e->getMessage());
            }

            $codes[] = [
                'url' => $url,
                'code' => $code,
            ];
        }

        return ['codes' => $codes];
    }
}
