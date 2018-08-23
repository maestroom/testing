<?php

namespace app\controllers;

use Yii;
use app\models\PriorityProject;
use app\models\search\PriorityProjectSearch;
use app\models\User;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Url;

/**
 * PriorityProjectController implements the CRUD actions for PriorityProject model.
 */
class PriorityProjectController extends Controller
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
    public function actions()
    {
    	return [
    			'sorting' => [
    					'class' => \kotchuprik\sortable\actions\Sorting::className(),
    					'query' => \app\models\PriorityProject::find()->where(['remove'=>0]),
    					'orderAttribute'=>'priority_order',
    			],
    	];
    }
    /**
     * Lists all PriorityProject models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PriorityProjectSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		/*IRT 67,68,86,87,258*/
		/*IRT 96,398 */
                /*IRT 202 Starts*/
		$filter_type=\app\models\User::getFilterType(['tbl_priority_project.id','tbl_priority_project.priority','tbl_priority_project.project_priority_order'],['tbl_priority_project']);                
		$config = [];       
		$config_widget_options = [];		
		$filterWidgetOption=\app\models\User::getFilterWidgetOption($filter_type,Url::toRoute(['priority-project/ajax-filter']),$config,$config_widget_options);
//                echo '<pre>';
//                print_r($filterWidgetOption);
//                die;
		/*IRT 67,68,86,87,258*/
                /*IRT 202 Ends*/
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
		$searchModel = new PriorityProjectSearch();
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
     * Displays a single PriorityProject model.
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
     * Creates a new PriorityProject model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new PriorityProject();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return 'OK'; //$this->redirect(['view', 'id' => $model->id]);
        } else {
            $pp_length = (new User)->getTableFieldLimit('tbl_priority_project');   
            $maxPriorityProjectOrder = PriorityProject::find()->select('project_priority_order')->orderBy('project_priority_order DESC')->one()->project_priority_order + 1;            
            return $this->renderAjax('create', [
                        'model' => $model,
                        'pp_length' => $pp_length,
                        'maxPriorityProjectOrder' => $maxPriorityProjectOrder
            ]);
        }
    }

    /**
     * Updates an existing PriorityProject model.
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
			$pp_length = (new User)->getTableFieldLimit('tbl_priority_project'); 
            return $this->renderAjax('update', [
                'model' => $model,
                'pp_length' =>$pp_length
            ]);
        }
    }

    /**
     * Deletes an existing PriorityProject model.
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
    		PriorityProject::updateAll(['remove' => 1], 'id IN ('.$keys.') AND remove = 0' );
    		return 'OK';
    	}
    }

    /**
     * Finds the PriorityProject model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return PriorityProject the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = PriorityProject::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    /*
     * IRT-202
     * Validate the project priority order 
     * return true
     * @param integer $id
     * @return true/false
     */
    public function actionValidateProjectPriorityOrder(){
        if (Yii::$app->request->post()) {
            $post_data = Yii::$app->request->post();
            $condition = "project_priority_order = " . $post_data['projectPriorityOrder']." AND remove = 0";
            if ($post_data['form_mode'] == 'update') {
                $condition .= " AND id != " . $post_data['id'];
            } 
            $counter = PriorityProject::find()->where($condition)->count();
            if ($counter == 0) {
                return 'OK';
            } else {
                return 'Exists';
            }         
        }
    }
}
