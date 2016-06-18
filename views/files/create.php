<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Files */

$this->title = '上傳檔案';
foreach($dirs as $dir){
    $this->params['breadcrumbs'][] = [
        'label' => $dir->name,
        'url' => ['/drive/index', 'path' => $dir->path]
    ];
}
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="files-create">

<div class="files-form">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

    <?= $form->field($model, 'files[]')->fileInput(['multiple' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('上傳', ['class' => 'button is-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

</div>
