<?php

namespace app\controllers;

use Yii;
use app\models\ProjectRequestType;
use app\models\search\ProjectRequestTypeSearch;
use app\models\User;
use app\models\ProjectRequestTypeRoles;
use app\models\Role;
use app\models\TemplatesRequestTypes;
use app\models\TaskInstruct;

use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\db\Query;
use yii\data\ActiveDataProvider;
use yii\data\SqlDataProvider;


/**
 * ProjectRequestTypeController implements the CRUD actions for ProjectRequestType model.
 */
class ProjectRequestTypeController extends Controller
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
     * Lists all ProjectRequestType models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ProjectRequestTypeSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		
		/*IRT 67,68,86,87,258*/
		/*IRT 96,398 */
		$filter_type=\app\models\User::getFilterType(['tbl_project_request_type.id','tbl_project_request_type.request_type'],['tbl_project_request_type']);
		$config = [];       
		$config_widget_options = [];		
		$filterWidgetOption=\app\models\User::getFilterWidgetOption($filter_type,Url::toRoute(['project-request-type/ajax-filter']),$config,$config_widget_options);
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
		$searchModel = new ProjectRequestTypeSearch();
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
     * Creates a new ProjectRequestType model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ProjectRequestType();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
                $id=$model->id;
                $sql="INSERT INTO tbl_project_request_type_roles(project_request_type_id, role_id) SELECT $id as tbl_project_request_type_roles, tbl_role.id as role_id FROM tbl_role WHERE id!=0";
                Yii::$app->db->createCommand($sql)->execute();
                /*$role_data = ArrayHelper::map(Role::find()->where('id!=0')->select(['id'])->all(),'id','id');
                if(!empty($role_data) && isset($role_data)){				  
                        foreach($role_data as $single){
                                $rows[] = ['project_request_type_id'=>$model->id,'role_id'=>$single];
                        }
                }
                if(!empty($rows)){
                        $coulmns = (new ProjectRequestTypeRoles)->attributes();				  
                        unset($coulmns[array_search('id',$coulmns)]);				                        
                        Yii::$app->db->createCommand()->batchInsert(ProjectRequestTypeRoles::tableName(),$coulmns,$rows)->execute();				  				  
                }*/
            return 'OK';//$this->redirect(['view', 'id' => $model->id]);
        } else {
			$prt_length = (new User)->getTableFieldLimit('tbl_project_request_type'); 
            return $this->renderAjax('create', [
                'model' => $model,
                'prt_length' => $prt_length
            ]);
        }
    }

    /**
     * Updates an existing ProjectRequestType model.
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
			$prt_length = (new User)->getTableFieldLimit('tbl_project_request_type'); 
            return $this->renderAjax('update', [
                'model' => $model,
                'prt_length' => $prt_length
            ]);
        }
    }

    /**
     * Deletes an existing ProjectRequestType model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
	public function actionDelete($id)
	{
		$is_available = TaskInstruct::find()->where(['task_projectreqtype' => $id,'isactive'=>1])->count(); // Task Instruct
		$is_used = TemplatesRequestTypes::find()->where(['project_request_type_id' => $id])->count(); // Templates Request Types
		if($is_used > 0 || $is_available > 0) {
			return 'Fail';
		} else {
			$model = $this->findModel($id);
			$model->remove = 1;
			if ($model->save()) {
				ProjectRequestTypeRoles::deleteAll(['project_request_type_id' => $id]);
				return 'OK';
			}
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
    		ProjectRequestType::updateAll(['remove' => 1], 'id IN ('.$keys.') AND remove = 0' );
                ProjectRequestTypeRoles::deleteAll('project_request_type_id IN ('.$keys.')');	
    		return 'OK';
    	}
    }
    /**
     * Finds the ProjectRequestType model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ProjectRequestType the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ProjectRequestType::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    /* IRT-19
     * Get All Roles with the project request type ID
     * */
     public function actionGetProjectRequestTypeRoles($id){
		 $prtmodel = new ProjectRequestTypeRoles();		 		 
		 $role_details = ArrayHelper::map(Role::find()->where('id!=0')->select(['id','role_name'])->all(),'id','role_name');
		 $request_type_roles = ArrayHelper::map(ProjectRequestTypeRoles::find()->where(['project_request_type_id'=>$id])->select(['project_request_type_id','role_id'])->all(),'role_id','project_request_type_id');		
		 return $this->renderAjax('get_project_request_type_roles',[
				'role_details'=>$role_details,	
				'prtmodel'=>$prtmodel,
				'project_request_type_id' => $id,
				'request_type_roles' => $request_type_roles
		 ]);
	 }
	 /*
	  * IRT-19
	  * Update Roles according to the Request Type
	  * */
	  public function actionUpdateProjectRequestTypeRoles($id){
		  if(Yii::$app->request->post()){
			  $rows = [];
			  $model = new ProjectRequestTypeRoles();
			  $post_data = Yii::$app->request->post('role_ids');
			  $all_select = Yii::$app->request->post('chkselectall');
			  if(!empty($post_data) && isset($post_data)){				  
                            foreach($post_data as $single){
                                $rows[] = ['project_request_type_id'=>$id,'role_id'=>$single];
                            }
			  }                          
                          ProjectRequestTypeRoles::deleteAll(['project_request_type_id'=>$id]);				 
			  if(!empty($rows)){
                                $coulmns = (new ProjectRequestTypeRoles)->attributes();				  
                                unset($coulmns[array_search('id',$coulmns)]);				                                
                                Yii::$app->db->createCommand()->batchInsert(ProjectRequestTypeRoles::tableName(),$coulmns,$rows)->execute();				  				  
			  }
			  if(isset($all_select ) && $all_select != '' && $all_select == 'on'){
				  return 'ALL';				  
			  }else{
				return 'Partial';
			  }
		  }else{
			return 'ERROR';			
		  }
		 
	  }
}
