<?php

namespace app\controllers;

use Yii;
use app\models\TeamlocationMaster;
use app\models\search\TeamlocationMasterSearch;
use app\models\User;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Url;
/**
 * TeamlocationController implements the CRUD actions for TeamlocationMaster model.
 */
class TeamlocationController extends Controller
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
     * Lists all TeamlocationMaster models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TeamlocationMasterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		/*IRT 67,68,86,87,258*/
		/*IRT 96,398 */
		$filter_type=\app\models\User::getFilterType(['tbl_teamlocation_master.id','tbl_teamlocation_master.team_location_name'],['tbl_teamlocation_master']);
		$config = [];       
		$config_widget_options = [];		
		$filterWidgetOption=\app\models\User::getFilterWidgetOption($filter_type,Url::toRoute(['teamlocation/ajax-filter']),$config,$config_widget_options);
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
		$searchModel = new TeamlocationMasterSearch();
		$params = Yii::$app->request->queryParams;				
		$dataProvider = $searchModel->searchFilter($params);
		$out['results']=array();		
		foreach ($dataProvider as $key=>$val){
			$val1 = $val;
			$val2 = $val;
			
			if($val == ''){
				$val1 = '(not set)';
				$val='(not set)';
				$val2='(not set)';
			}							
			
			$out['results'][] = ['id' => $val1, 'text' => $val,'label' => $val2];
		}
		//print_r($out);die;
		return json_encode($out);
	}

    /**
     * Displays a single TeamlocationMaster model.
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
     * Creates a new TeamlocationMaster model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TeamlocationMaster();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return 'OK';//$this->redirect(['view', 'id' => $model->id]);
        } else {
			$tlm_length = (new User)->getTableFieldLimit('tbl_teamlocation_master'); 
            return $this->renderAjax('create', [
                'model' => $model,
                'tlm_length'=>$tlm_length
            ]);
        }
    }

    /**
     * Updates an existing TeamlocationMaster model.
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
			$tlm_length = (new User)->getTableFieldLimit('tbl_teamlocation_master'); 
            return $this->renderAjax('update', [
                'model' => $model,
                 'tlm_length'=>$tlm_length
            ]);
        }
    }

    /**
     * Deletes an existing TeamlocationMaster model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
	public function actionDelete($id){
     	$model = $this->findModel($id);
     	$chkTeam = $model->checkIsTeamUsed($id);
     	if($chkTeam == "N"){
	     	$model->remove = 1;
	     	if ($model->save()) {
	     		return 'OK';
	     	}
     	}else{
     		return $chkTeam;
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
    		TeamlocationMaster::updateAll(['remove' => 1], 'id IN ('.$keys.') AND remove = 0' );
    		return 'OK';
    	}
    }

    /**
     * Finds the TeamlocationMaster model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TeamlocationMaster the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TeamlocationMaster::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
