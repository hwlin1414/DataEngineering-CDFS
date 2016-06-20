<?php

namespace app\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\models\Dirs;
use app\models\Files;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class DriveController extends \yii\web\Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all Files models.
     * @return mixed
     */
    public function actionIndex($path = '', $q = '')
    {
        $path = \yii\helpers\FileHelper::normalizePath($path);
        $paths = explode('/', $path);
        if($paths[0] == '') array_shift($paths);

        $dirs = [];
        $dir_id = null;
        foreach($paths as $p){
            $model = Dirs::find()->where([
                'user_id' => Yii::$app->user->identity->id,
                'dir_id' => $dir_id,
                'name' => $p
            ])->One();
            if($model == null){
                throw new NotFoundHttpException('你所要求的目錄不存在');
            }
            $dirs[] = $model;
            $dir_id = $model->id;
        }
        $query = Dirs::find()->where([
            'user_id' => Yii::$app->user->identity->id,
        ]);
        if($q != "") $query->andFilterWhere(['LIKE', 'name', $q]);
        else $query->andWhere(['dir_id' => $dir_id]);
        $query->orderBy(['name' => SORT_ASC]);
        $subdirs = $query->All();

        $query = Files::find()->andWhere([
            'user_id' => Yii::$app->user->identity->id,
        ]);
        if($q != "") $query->andFilterWhere(['LIKE', 'name', $q]);
        else $query->andWhere(['dir_id' => $dir_id]);
        $query->orderBy(['name' => SORT_ASC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        return $this->render('index', [
            'q' => $q,
            'dataProvider' => $dataProvider,
            'dirs' => $dirs,
            'dir_id' => $dir_id,
            'subdirs' => $subdirs,
        ]);
    }
}
