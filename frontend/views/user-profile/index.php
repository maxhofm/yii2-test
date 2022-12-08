<?php

use frontend\models\UserProfile;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var frontend\models\UserProfileSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'User Profiles';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-profile-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'user_id',
            'gender_id',
            'photo_id',
            'name',
            'surname',
            [
                'class' => ActionColumn::className(),
                'buttons' => [
                    'view' => function ($url, $model) {
                        return Html::a('View', $url, [
                            'title' => Yii::t('app', 'View'),
                            'class'=>'btn btn-primary btn-xs',
                        ]);
                    },
                    'update' => function ($url, $model) {
                        if (Yii::$app->user->isGuest) {
                            return false;
                        }
                        $user = Yii::$app->user->getIdentity();
                        if ($user->id !== $model->user_id) {
                            return false;
                        }

                        return Html::a('Edit', $url, [
                            'title' => Yii::t('app', 'Edit'),
                            'class'=>'btn btn-primary btn-xs',
                        ]);
                    },
                    'delete' => function () { return false; }
                ],
            ],
        ],
    ]); ?>


</div>
