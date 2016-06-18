<?php

namespace app\controllers;

use Yii;
use app\models\Dirs;
use app\models\Files;
use app\models\Uploads;
use app\models\search\Files as FilesSearch;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

/**
 * FilesController implements the CRUD actions for Files model.
 */
class FilesController extends Controller
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
     * Displays a single Files model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        Yii::$app->response->sendFile('../uploads/' . $id, $model->name)->send();
        return;
    }

    /**
     * Creates a new Files model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($dir = null)
    {

        $model = new Uploads();
        if (Yii::$app->request->isPost || Yii::$app->request->isAjax){
            $model->files = UploadedFile::getInstances($model, 'files');
            $result = $model->upload($dir);

            if(Yii::$app->request->isAjax){
                echo $result;
                exit();
            }

            $path = '';
            if($dir != null) $path = Dirs::findOne($dir)->path;

            return $this->redirect([
                '/drive/index',
                'path' => $path,
            ]);
        } else {
            $dirs = [];
            if($dir != null) $dirs = Dirs::findOne($dir)->parents;
            return $this->render('create', [
                'model' => $model,
                'dirs' => $dirs,
            ]);
        }
    }

    /**
     * Updates an existing Files model.
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

            $path = '';
            if($model->dir != null) $path = $model->dir->path;
            return $this->redirect([
                '/drive/index',
                'path' => $path,
            ]);
        } else {
            $dirs = [];
            if($model->dir != null) $dirs = $model->dir->parents;
            return $this->render('update', [
                'model' => $model,
                'dirs' => $dirs,
            ]);
        }
    }

    /**
     * Deletes an existing Files model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $path = '';

        $model = $this->findModel($id);
        if($model->dir != null) $path = $model->dir->path;
        $model->delete();
        unlink("../uploads/{$id}");

        return $this->redirect(['/drive/index', 'path' => $path]);
    }

    /**
     * Finds the Files model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Files the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Files::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
