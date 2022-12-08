<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var frontend\models\UserProfile $model */
/** @var frontend\models\UserProfileComment $comment */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'User Profiles', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="user-profile-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div>
        <?php
            if (!empty($model->photo)) {
                echo Html::img($model->photo->getUrl(), ['alt' => 'Аватар', 'width' => 200, 'height' => 200]);
            }
        ?>
    </div>
    <br>
    <p>
        <?php
            $user = Yii::$app->user->getIdentity();
            if ($user->id === $model->user_id) {
                echo Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']);
            }
        ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'name',
            'surname',
            [
                'attribute' => 'gender_id',
                'value' => $model->gender->name ?? null,
            ]
        ],
    ]) ?>

    <?= $this->render('_addCommentForm', [
        'model' => $comment,
        'profileId' => $model->id,
    ]) ?>

    <br>
    <?php
        if (!empty($model->comments)) {
            echo Html::tag('p', 'Комментарии:');

            foreach ($model->comments as $comment) {
                echo Html::tag('p', $comment->text);
                echo Html::tag('hr');
            }
        }
    ?>

</div>
