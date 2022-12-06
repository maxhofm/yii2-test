<?php

namespace console\controllers;

use backend\models\UrlStatus;
use yii\console\Controller;
use yii\helpers\Json;

/**
 * CheckStatusController controller
 */
class CheckStatusController extends Controller
{
    /**
     *  Получение статистики по запросам ()
     *
     * @return void
     */
    public function actionStatistics(): void
    {
        $result = UrlStatus::getStatisticAsArray();
        $this->stdout(Json::encode($result)."\r\n");
    }
}
