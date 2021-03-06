<?php

namespace app\controllers;

use Yii;
use app\models\Dirs;
use app\models\search\Dirs as DirsSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * DirsController implements the CRUD actions for Dirs model.
 */
class DirsController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Creates a new Dirs model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($dir = null)
    {
        $model2 = null;
        if($dir != null) $model2 = $this->findModel($dir);

        $model = new Dirs(['scenario' => 'create']);

        if ($model->load(Yii::$app->request->post())){
            $model->user_id = Yii::$app->user->identity->id;
            $model->dir_id = $dir;
            $model->save();
            return $this->redirect([
                '/drive/index',
                'path' => $model->parent,
            ]);
        } else {
            return $this->render('create', [
                'model' => $model,
                'dirs' => ($model2 == null)?([]):($model2->parents),
            ]);
        }
    }

    /**
     * Updates an existing Dirs model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->scenario = 'update';

        if ($model->load(Yii::$app->request->post())){
            $model->save();
            return $this->redirect([
                '/drive/index',
                'path' => $model->parent,
            ]);
        } else {
            return $this->render('update', [
                'model' => $model,
                'dirs' => $model->parents,
            ]);
        }
    }

    /**
     * Deletes an existing Dirs model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $path = '';
        if($model->dir != null) $path = $model->dir->parent;
        $model->delete();

        return $this->redirect(['/drive/index', 'path' => $path]);
    }

    public function actionDownload($id)
    {
        $model = $this->findModel($id);

        $zip = new \ZipArchive;
        $zip->open('../uploads/'.$id.'.zip', \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
        $model->addZip($zip);
        $zip->close();
        
        Yii::$app->response->sendFile('../uploads/' . $model->id . '.zip', $model->name . '.zip')->send();
        return;
    }

    /**
     * Finds the Dirs model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Dirs the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Dirs::findOne($id)) !== null) {
            if($model->user->id != Yii::$app->user->identity->id){
                throw new ForbiddenHttpException('檔案禁止存取');
            }
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
