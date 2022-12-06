<?php

namespace backend\controllers;

use backend\models\UrlStatus;
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
     *
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

        $codes = [];

        // Обрабатываем каждый url
        foreach ($urls as $url) {
            $urlStatus = UrlStatus::findByUrl($url);

            if (!empty($urlStatus)) {
                // Если прошло 10 минут с момента последгего запроса
                if (strtotime($urlStatus->updated_at) < strtotime("-10 minutes")) {
                    // Делаем запрос по url и обновляем модель
                    $urlStatus->setExternalRequestCode('get', $url);
                }
                $urlStatus->updateQueryCount();
            } else {
                // Делаем запрос по url и создаем модель
                $urlStatus = new UrlStatus();
                $urlStatus->setExternalRequestCode('get', $url);
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

            if (!$urlStatus->save()) {
                Yii::error("Не удалось сохранить модель в базу");
                throw new ServerErrorHttpException();
            }
            $codes[] = [
                'url' => $url,
                'code' => $urlStatus->status_code,
            ];
        }

        return ['codes' => $codes];
    }
}
