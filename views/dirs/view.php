<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Dirs */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Dirs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="dirs-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'name',
            'user_id',
            'dir_id',
            'created_at',
            'updated_at',
            'deleted_at',
        ],
    ]) ?>

</div>
