<?php

namespace backend\controllers;

use backend\models\UrlStatus;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;
use Yii;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\Response;
use yii\web\ServerErrorHttpException;

/**
 * Api controller
 */
class ApiController extends Controller
{
//    /**
//     * {@inheritdoc}
//     */
//    public function behaviors()
//    {
//        return [
//            'access' => [
//                'class' => AccessControl::class,
//                'rules' => [
//                    [
//                        'actions' => ['check-status'],
//                        'allow' => true,
//                    ],
//                ],
//            ],
//        ];
//    }

    /**
     * Запрос к api для проверки статуса url
     *
     * @return string
     * @throws BadRequestHttpException
     * @throws GuzzleException
     * @throws ServerErrorHttpException
     */
    public function actionCheckStatus(): string
    {
        // todo: исключения

        // Получаем данные запроса
        $request = $this->request;
        $urls = $request->post('url');
        $response = Yii::$app->response;
        $response->format = Response::FORMAT_JSON;

        // Проверяем наличие списка url в запросе
        if (!is_array($urls) || empty($urls)) {
            throw new BadRequestHttpException();
        }

        $codes = [];
        $nowDate =  date("Y-m-d H:i:s");

        // Обрабатываем каждый url
        foreach ($urls as $url) {
            $urlStatus = UrlStatus::findByUrl($url);

            if (!empty($urlStatus)) {
                // Если прошло 10 минут с момента последгего запроса
                if (strtotime($urlStatus->updated_at) < strtotime("-10 minutes")) {
                    // Делаем запрос по url и обновляем модель
                    $response = $this->makeExternalRequest('get', $url);
                    $urlStatus->status_code = $response->getStatusCode();
                    $urlStatus->updated_at = $nowDate;
                }
                $urlStatus->updateQueryCount();
            } else {
                // Делаем запрос по url и создаем модель
                $response = $this->makeExternalRequest('get', $url);
                $urlStatus = new UrlStatus();
                $data = [
                    'hash_string' => UrlStatus::generateHashByUrl($url),
                    'created_at' => $nowDate,
                    'updated_at' => $nowDate,
                    'url' => $url,
                    'status_code' => $response->getStatusCode(),
                    'query_count' => 1,
                ];
                $urlStatus->load($data, '');
            }

            $codes[] = [
                'url' => $url,
                'code' => $urlStatus->status_code,
            ];
            if (!$urlStatus->save()) {
                $er = $urlStatus->errors;
                Yii::error("Не удалось сохранить модель в базу: ");
                throw new ServerErrorHttpException();
            }
        }

        $response->data = ['codes' => $codes];
    }

    /**
     * @param string $method
     * @param string $url
     * @return ResponseInterface
     * @throws GuzzleException|ServerErrorHttpException
     */
    protected function makeExternalRequest(string $method, string $url): ResponseInterface
    {
        try {
            $client = new Client();
            return $client->request($method, $url, ['connect_timeout' => 5]);
        } catch (Exception $e) {
            throw new ServerErrorHttpException();
        }
    }

}
