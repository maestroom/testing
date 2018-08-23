<?php

namespace app\controllers;

use Yii;
use app\models\Unit;
use app\models\search\UnitSearch;
use app\models\User;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Url;

/**
 * UnitController implements the CRUD actions for Unit model.
 */
class UnitController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['get'],
                ],
            ],
        ];
    }

    /**
     * Lists all Unit models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UnitSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		/*IRT 67,68,86,87,258*/
		/*IRT 96,398 */
		$filter_type=\app\models\User::getFilterType(['tbl_unit.id','tbl_unit.unit_name','tbl_unit.default_unit'],['tbl_unit']);
		$config = ['default_unit'=>['All'=>'All','1'=>'Yes','0'=>'No']];
		$config_widget_options = [];		
		$filterWidgetOption=\app\models\User::getFilterWidgetOption($filter_type,Url::toRoute(['unit/ajax-filter']),$config,$config_widget_options);
		/*IRT 67,68,86,87,258*/
		
        return $this->renderAjax('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'filter_type'=>$filter_type,
			'filterWidgetOption'=>$filterWidgetOption
        ]);
    }
    
    /**
	 * Filter GridView with Ajax
	 * */
	public function actionAjaxFilter(){
		$searchModel = new UnitSearch();
		$params = Yii::$app->request->queryParams;				
		$dataProvider = $searchModel->searchFilter($params);
		$out['results']=array();		
		foreach ($dataProvider as $key=>$val){
			$val1 = $val;
			$val2 = $val;
			
			if($val == ''){
				$val1 = '0';
				$val='0';
				$val2='0';
			}							
			
			$out['results'][] = ['id' => $val1, 'text' => $val,'label' => $val2];
		}
		//print_r($out);die;
		return json_encode($out);
	}

    /**
     * Displays a single Unit model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Unit model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Unit();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return 'OK';//$this->redirect(['view', 'id' => $model->id]);
        } else {
			$unit_length = (new User)->getTableFieldLimit('tbl_unit'); 
            return $this->renderAjax('create', [
                'model' => $model,
                'unit_length'=>$unit_length
            ]);
        }
    }

	public function actionSortunits()
	{
		$sort_ids = explode(",",Yii::$app->request->post('sort_ids',0));
    	if(!empty($sort_ids)){
    		$transaction = \Yii::$app->db->beginTransaction();
    		try {
    			foreach ($sort_ids as $order => $id) {
    				$model = Unit::findOne($id);
    				if ($model === null) {
    					throw new yii\web\BadRequestHttpException();
    				}
    				$model->sort_order = $order + 1;
    				$model->save(false);
    			}
    			$transaction->commit();
    			return 'OK';
    		} catch (\Exception $e) {
    			$transaction->rollBack();
    		}
    	}
    	return 'Error';
	}

	/**
	 * Updates an existing Unit Model with Is Hidden value
	 * @param $id integer
	 * @param $is_hidden boolean
	 */
	public function actionUpdateUnitHidden($id,$is_hidden)
	{
		$model = $this->findModel($id);
     	$model->is_hidden = $is_hidden;
     	if ($model->save()) {
     		return 'OK';
     	}
     	exit;
	}

    /**
     * Updates an existing Unit model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return 'OK';//$this->redirect(['view', 'id' => $model->id]);
        } else {
			$unit_length = (new User)->getTableFieldLimit('tbl_unit'); 
            return $this->renderAjax('update', [
                'model' => $model,
				'unit_length'=>$unit_length
            ]);
        }
    }

    /**
     * Deletes an existing Unit model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
	public function actionDelete($id){
     	$model = $this->findModel($id);
     	$model->remove = 1;
     	if ($model->save()) {
     		return 'OK';
     	}
     	exit;
    }
    /**
     * Deletes an selected existing CaseCloseType model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    
    public function actionDeleteselected() {
    	if (isset($_POST['keylist'])) {
    		$keys = implode(",",$_POST['keylist']);
    		Unit::updateAll(['remove' => 1], 'id IN ('.$keys.') AND remove = 0' );
    		return 'OK';
    	}
    }
    /**
     * Finds the Unit model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Unit the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Unit::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
