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
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
