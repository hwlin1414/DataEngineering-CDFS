<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\Files */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '雲端檔案';
foreach($dirs as $dir){
    $this->params['breadcrumbs'][] = [
        'label' => $dir->name,
        'url' => ['/drive/index', 'path' => $dir->path]
    ];
}

?>
<div class="files-index" id='fileIndex'>
    <div class="columns">
        <div class="column is-4 is-offset-8">
            <?= Html::beginForm(['/drive/index', 'path' => '/'], 'GET', ['csrf' => true]) ?>
            <p class="control has-icon has-icon-right">
                <?= Html::textInput('q', $q, ['class' => 'input']) ?>
                <i class="fa fa-search"></i>
            </p>
            <?= Html::endForm() ?>
        </div>
    </div>

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

        echo Html::a('<i class="fa fa-download"></i>', [
            '/dirs/download',
            'id' => $subdir->id,
        ], [
            'class' => 'button is-success is-outlined is-pulled-right',
            'data-method' => 'POST',
        ]);

        echo Html::a(Html::encode($subdir->name), ['/drive/index', 'path' => $subdir->path], ['class' => 'panel-block']);
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
                                '/files/download',
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
            <div id='fileprocess'></div>
        </div>
    </div>
</div>
<?php

$uploadUrl = Url::toRoute(['files/create', 'dir' => $dir_id]);
$token = Yii::$app->request->getCsrfToken();

$script = <<<EOD
	var f_total = 0;
	var f_queue = 0;
	function upload_one(data, fid){
		var form = new FormData();
		form.append("Uploads[files][]", '');
		form.append("Uploads[files][]", data);
        form.append("_csrf", '{$token}');
		$('#fileprocess').append('<div>' + data.name + '<progress class="progress is-primary" id="files_' + fid + '" value="0" max="100"><span>' + data.name + '</span></progress></div>');
		$.ajax({
			xhr: function() {
				var xhr = new window.XMLHttpRequest();
				xhr.upload.addEventListener("progress", function(evt) {
					if (evt.lengthComputable) {
						var percentComplete = evt.loaded / evt.total;
						percentComplete = parseInt(percentComplete * 100);
						$('#files_'+fid).val(percentComplete);
					}
				}, false);
				return xhr;
			},
			url: '{$uploadUrl}',
			type: 'POST',
			data: form,
			processData: false,
			contentType: false,
			success: function(data){
				if(data != 'false'){
					$('#files_'+fid).removeClass('is-primary').addClass('is-success');
				}else{
					$('#files_'+fid).removeClass('is-primary').addClass('is-danger');
				}
                f_queue--;
                if(f_queue == 0) location.reload();;
			}
		});
	}
	var obj = $("#fileIndex");
	obj.on('dragenter', function (e){
		e.stopPropagation();
		e.preventDefault();
		$(this).addClass('box');
	});
	obj.on('dragover', function (e){
		e.stopPropagation();
		e.preventDefault();
		//$(this).css('border', 'none');
	});
	obj.on('drop', function (e){
		$(this).css('border', 'none');
		e.preventDefault();
		var files = e.originalEvent.dataTransfer.files;
		for(var i = 0; i < files.length; i++){
			upload_one(files[i], f_total);
			f_total++;
			f_queue++;
		}
	});
EOD;

$this->registerJs($script, yii\web\view::POS_READY);
