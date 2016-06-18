<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;


/* @var $this yii\web\View */
/* @var $model app\models\Dirs */

$this->title = '新增資料夾';
foreach($dirs as $dir){
    $this->params['breadcrumbs'][] = [
        'label' => $dir->name,
        'url' => ['/drive/index', 'path' => $dir->path]
    ];
}
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="dirs-create">

<div class="dirs-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'autofocus' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('新增', ['class' => 'button is-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

</div>
