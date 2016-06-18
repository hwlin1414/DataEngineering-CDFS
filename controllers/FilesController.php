<?php

namespace app\controllers;

use Yii;
use app\models\Files;
use app\models\search\Files as FilesSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

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
        Yii::$app->response->sendFile('uploads/' . $id)->send();
        return;
    }

    /**
     * Creates a new Files model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($dir = null)
    {
        $model = new Files(['scenario' => 'create']);

        if ($model->load(Yii::$app->request->post())){
            $model->user_id = Yii::$app->user->identity->id;
            $model->dir_id = $dir;
            $model->save();

            $path = '';
            if($model->dir != null) $path = $model->dir->path;

            return $this->redirect([
                '/drive/index',
                'path' => $path,
            ]);
        } else {
            return $this->render('create', [
                'model' => $model,
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
            return $this->render('update', [
                'model' => $model,
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
