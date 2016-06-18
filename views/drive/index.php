<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\Files */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '雲端檔案';
foreach($dirs as $dir){
    $this->params['breadcrumbs'][] = [
        'label' => $dir->name,
        'url' => ['index', 'path' => $dir->path]
    ];
}

?>
<div class="files-index">
    <div class="columns">
        <div class="column is-4">
            <nav class="panel">
              <p class="panel-heading">
                目錄
              </p>
<?php
    foreach($subdirs as $subdir){
        //echo '<div class="panel-block">';
        echo Html::a('<i class="fa fa-trash"></i>', [
            '/dirs/delete',
            'id' => $subdir->id,
        ], [
            'class' => 'button is-danger is-pulled-right',
            'data-method' => 'POST',
        ]);

        echo Html::a('<i class="fa fa-pencil"></i>', [
            '/dirs/update',
            'id' => $subdir->id,
        ], [
            'class' => 'button is-info is-outlined is-pulled-right',
            'data-method' => 'POST',
        ]);
        echo Html::a($subdir->name, ['/drive/index', 'path' => $subdir->path], ['class' => 'panel-block']);
        //echo '</div>';
    }
?>
            </nav>
            <?= Html::a('新增', ['/dirs/create', 'dir' => $dir_id], ['class' => 'button is-success is-outlined']) ?>
        </div>
        <div class="column">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'summary' => '',
                'columns' => [
                    [
                        'attribute' => 'name',
                        'format' => 'raw',
                        'value' => function ($model, $key, $index, $grid){
                            return Html::a($model->name, [
                                '/files/view',
                                'id' => $model->id,
                            ]);
                        }
                    ],
                    'created_at',
                    'updated_at',
                    [
                        'format' => 'raw',
                        'value' => function ($model, $key, $index, $grid){
                            return Html::a('<i class="fa fa-pencil"></i>', [
                                '/files/update',
                                'id' => $model->id,
                            ], [
                                'class' => 'button is-info is-outlined',
                                'data-method' => 'POST',
                            ]) . ' '. Html::a('<i class="fa fa-trash"></i>', [
                                '/files/delete',
                                'id' => $model->id,
                            ], [
                                'class' => 'button is-danger',
                                'data-method' => 'POST',
                            ]);
                        }
                    ],
                ],
            ]); ?>
            <p>
                <?= Html::a('上傳', ['/files/create', 'dir' => $dir_id], ['class' => 'button is-success is-outlined']) ?>
            </p>
        </div>
    </div>
</div>
