<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Dirs */

$this->title = '修改資料夾';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="dirs-update">

    <h1><?= Html::encode($this->title) ?></h1>

<div class="dirs-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Update', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

</div>
