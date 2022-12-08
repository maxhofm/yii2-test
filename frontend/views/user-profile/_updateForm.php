<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var frontend\models\UserProfile $model */
/** @var array $genders */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="user-profile-form">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

    <?php
        if (!empty($model->fileUrl)) {
            echo Html::img($model->fileUrl, ['alt' => 'Аватар', 'width' => 200, 'height' => 200]);
        }
    ?>

    <br>
    <br>

    <?= $form->field($model, 'imgFile')->fileInput() ?>

    <?= $form->field($model, 'gender_id')->dropDownList($genders) ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'surname')->textInput(['maxlength' => true]) ?>

    <br>
    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
