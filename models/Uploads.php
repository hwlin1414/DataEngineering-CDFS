<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\web\UploadedFile;

class Uploads extends Model
{
    /**
     * @var UploadedFile
     */
    public $files;

    public function rules()
    {
        return [
            [['files'], 'file', 'skipOnEmpty' => false, 'maxFiles' => 16],
        ];
    }
    
    public function upload($dir = null)
    {
        $count = 0;
        foreach($this->files as $file){
            $model2 = new Files(['scenario' => 'create']);
            $model2->user_id = Yii::$app->user->identity->id;
            $model2->dir_id = $dir;
            $model2->name = $file->name;
            $model2->save();
            $file->saveAs('../uploads/' . $model2->id);
            $count++;
        }
        return $count;
    }

    public function attributeLabels()
    {
        return [
            'files' => '檔案',
        ];
    }
}
