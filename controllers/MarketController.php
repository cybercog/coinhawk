<?php

namespace app\controllers;

use Yii;
use app\models\Market;
use app\models\ScheduledEmailTask;
use app\models\search\MarketSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\VerbFilter;
use app\components\TimeHelper;

/**
 * MarketController implements the CRUD actions for Market model.
 */
class MarketController extends Controller
{
	public function behaviors()
	{
		return [
			'verbs' => [
				'class' => VerbFilter::className(),
				'actions' => [
					'delete' => ['post'],
				],
			],
		];
	}

	/**
	 * Lists all Market models.
	 * @return mixed
	 */
	public function actionIndex()
	{
		$searchModel = new MarketSearch;
		$dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

		return $this->render('index', [
			'dataProvider' => $dataProvider,
			'searchModel' => $searchModel,
		]);
	}

	/**
	 * Displays a single Market model.
	 * @param string $id
	 * @return mixed
	 */
	public function actionView($id)
    {
        $model = $this->findModel($id);
        $times = TimeHelper::last7days();
        $history = $model->getHistoryData($id, $times[0], $times[1]);

		return $this->render('view', [
			'model' => $model,
            'history' => $history,
		]);
	}

    public function actionFetchChartData($id, $time)
    {
        $model = $this->findModel($id);
        $times = call_user_func('app\components\TimeHelper::'. $time);
        $history = $model->getHistoryData($id, $times[0], $times[1]);
        header('Content-type: application/json');
        echo $history;
    }

    public function actionFullListing($id)
    {
        $markets =  Market::find()->where(['exchange_id'=>$id])->all();

        echo $this->render('fullListing', ['markets' => $markets]);
    }

	/**
	 * Creates a new Market model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 * @return mixed
	 */
	public function actionCreate()
	{
		$model = new Market;

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['view', 'id' => $model->id]);
		} else {
			return $this->render('create', [
				'model' => $model,
			]);
		}
	}

	/**
	 * Updates an existing Market model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param string $id
	 * @return mixed
	 */
	public function actionUpdate($id)
	{
		$model = $this->findModel($id);

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['view', 'id' => $model->id]);
		} else {
			return $this->render('update', [
				'model' => $model,
			]);
		}
	}

	/**
	 * Deletes an existing Market model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 * @param string $id
	 * @return mixed
	 */
	public function actionDelete($id)
	{
		$this->findModel($id)->delete();
		return $this->redirect(['index']);
	}

	/**
	 * Finds the Market model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 * @param string $id
	 * @return Market the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel($id)
	{
		if ($id !== null && ($model = Market::find($id)) !== null) {
			return $model;
		} else {
			throw new NotFoundHttpException('The requested page does not exist.');
		}
	}
}
