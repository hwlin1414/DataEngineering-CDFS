<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Files */

$this->title = '修改檔案';
foreach($dirs as $dir){
    $this->params['breadcrumbs'][] = [
        'label' => $dir->name,
        'url' => ['/drive/index', 'path' => $dir->path]
    ];
}
$this->params['breadcrumbs'][] = $model->name;
?>
<div class="files-update">

<div class="files-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'autofocus' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('修改', ['class' => 'button is-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

</div>
