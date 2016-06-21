<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = '登入';
?>
<div class="site-login">

    <div class="columns">
        <div class="column is-6">
        <h1 class="title is-2">登入</h1>
    <?php $form = ActiveForm::begin([
        'id' => 'login-form',
        'options' => ['class' => 'form-horizontal'],
        'fieldConfig' => [
            'template' => "{label}\n<div class=\"col-lg-8\">{input}</div>\n<div class=\"col-lg-8\">{error}</div>",
            'labelOptions' => ['class' => 'col-lg-2 control-label'],
        ],
    ]); ?>

        <?= $form->field($model, 'username')->textInput(['autofocus' => true]) ?>

        <?= $form->field($model, 'password')->passwordInput() ?>

        <?= $form->field($model, 'rememberMe')->checkbox([
            'template' => "<div class=\"col-lg-offset-1 col-lg-3\">{input} {label}</div>\n<div class=\"col-lg-8\">{error}</div>",
        ]) ?>

        <?= Html::submitButton('登入', ['class' => 'button is-primary is-outlined', 'name' => 'login-button']) ?>
    <?php ActiveForm::end(); ?>
        </div>

        <div class="column is-6">
        <h1 class="title is-2">註冊</h1>
    <?php $form = ActiveForm::begin([
        'id' => 'login-form',
        'options' => ['class' => 'form-horizontal'],
        'fieldConfig' => [
            'template' => "{label}\n<div class=\"col-lg-8\">{input}</div>\n<div class=\"col-lg-8\">{error}</div>",
            'labelOptions' => ['class' => 'col-lg-2 control-label'],
        ],
    ]); ?>

        <?= $form->field($model2, 'name')->textInput() ?>

        <?= $form->field($model2, 'password')->passwordInput() ?>

        <?= Html::submitButton('註冊', ['class' => 'button is-success is-outlined', 'name' => 'login-button']) ?>

    <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
