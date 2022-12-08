<?php

namespace frontend\controllers;

use frontend\models\File;
use Yii;
use yii\web\Controller;
use yii\web\HttpException;

/**
 * Site controller
 */
class FileController extends Controller
{
    /**
     * {@inheritdoc}
     */
//    public function behaviors()
//    {
//        return [
//            'access' => [
//                'class' => AccessControl::class,
//                'only' => ['logout', 'signup'],
//                'rules' => [
//                    [
//                        'actions' => ['signup'],
//                        'allow' => true,
//                        'roles' => ['?'],
//                    ],
//                    [
//                        'actions' => ['logout'],
//                        'allow' => true,
//                        'roles' => ['@'],
//                    ],
//                ],
//            ]
//        ];
//    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => \yii\web\ErrorAction::class,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionLoad($id)
    {
        $file = File::findOne($id);
        if (empty($file)) {
            throw new HttpException('no file');
        }
        return Yii::$app->response->sendFile($file->path);
    }
}
