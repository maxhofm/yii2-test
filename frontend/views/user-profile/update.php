<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var frontend\models\UserProfileForm $model */
/** @var array $genders */

$this->title = 'Профиль';
$this->params['breadcrumbs'][] = ['label' => 'User Profiles', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->profile->id, 'url' => ['view', 'id' => $model->profile->id]];
$this->params['breadcrumbs'][] = 'Редактировать';
?>
<div class="user-profile-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_updateForm', [
        'model' => $model,
        'genders' => $genders
    ]) ?>

</div>
