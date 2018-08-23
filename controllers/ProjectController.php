<?php
namespace app\controllers;
use Yii;
use app\models\ClientCase;
use app\models\Tasks;
use app\models\TaskInstruct;
use app\models\PriorityProject;
use app\models\ProjectSecurity;
use app\models\ProjectRequestType;
use app\models\TaskInstructServicetask;
use app\models\TasksTemplates;
use app\models\User;
use app\models\Evidence;
use app\models\EvidenceProductionBates;
use app\models\EvidenceProduction;
use app\models\TeamlocationMaster;
use app\models\Servicetask;
use app\models\CaseXteam;
use app\models\TeamserviceSla;
use app\models\TeamserviceSlaBusinessHours;
use app\models\TeamserviceSlaHolidays;
use app\models\Options;
use app\models\FormBuilder;
use app\models\SettingsEmail;
use app\models\FormInstructionValues;
use app\models\search\ProjectSearch;
use app\models\search\TaskSearch;
use app\models\ClientXteam;
use app\models\Unit;
use app\models\EmailCron;

use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\filters\VerbFilter;
use yii\widgets\ActiveForm;
use yii\db\Query;

/**
 * ProjectController implements the CRUD actions for Tasks model.
 */
class ProjectController extends Controller
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
     * Lists all Tasks models.
     * @return mixed
     */
    public function actionIndex()
    {
		$searchModel = new ProjectSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Tasks model.
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
     * Creates a new Project model.
     * If creation is successful, the browser will be redirected to the 'case projects list' Or 'saved projects list' page.
     * @return mixed
     */
    public function actionAdd($case_id)
    {
		//$date = (new Tasks)->fnGetUpdatedDueDate('17/05/2017', '16:30', '-20');
		
		if(isset($_POST['properties'])) {
			foreach($_POST['properties'] as $id=>$vals) {
				$element='';
				foreach($vals as $el=>$val) {
					$element=$el;
				}
				if(isset($element) && trim($element)!='') {
					if(isset($_POST[$id][$element])) {
						if(is_array($_POST[$id][$element])) {
							foreach($_POST[$id][$element] as $k=>$v) {
								$_POST[$id][$element][$k]=htmlentities($v);
							}
						} else {
							$_POST[$id][$element]=htmlentities($_POST[$id][$element]);
						}
					}
				}
			}
		}
		//echo "<pre>",print_r(($_POST)),"</pre>";
		//die;
		

        $this->layout = "mycase";
        $model = new Tasks();
        $modelInstruct = new TaskInstruct();
        if (Yii::$app->request->post()) {
            $post_data = Yii::$app->request->post();
            if ($post_data['flag'] == 'save') {
                $model->saveProject($post_data, $_FILES);
				return $this->redirect(['case-projects/load-saved-projects', 'case_id' => $post_data['case_id']]);
            }
            if ($post_data['flag'] == 'submit') {
                $model->submitProject($post_data, $_FILES);
				return $this->redirect(['case-projects/index', 'case_id' => $post_data['case_id']]);
            }
        } else {
            $priorityList = ArrayHelper::map(PriorityProject::find()->select(['id', 'priority'])->where(['remove' => 0])->orderBy('project_priority_order ASC')->all(), 'id', 'priority');
            /* IRT-19 Starts */
            $role_id = Yii::$app->user->identity->role_id;
			
            $role_sql = '';
            if ($role_id != 0) {
                $role_sql = " AND id IN(select project_request_type_id FROM tbl_project_request_type_roles where role_id = $role_id)";
            }
            $projectReqType_data = ArrayHelper::map(ProjectRequestType::find()->select(['id', 'request_type'])->orderBy('request_type ASC')->where('remove=0' . $role_sql)->all(), 'id', 'request_type');
            /* IRT-19 Ends */
            $listSalesRepo = ArrayHelper::map(User::find()->select(['id', "CONCAT(usr_first_name,' ',usr_lastname) as full_name"])->orderBy('full_name ASC')->all(), 'id', 'full_name');
            //$case_productions = EvidenceProduction::find()->where(['client_case_id' => $case_id])->orderBy('created desc')->all();
            //$case_media=(new TaskInstructServicetask())->getCaseMedias($case_id);
            $serviceTaskTemplate_data = TasksTemplates::find()->select(['tbl_tasks_templates.id', 'temp_name'])->joinWith('tasksTemplatesServiceTasks')->orderby('temp_sortorder ASC')->asArray()->all();
            $holidayAr = ArrayHelper::map(TeamserviceSlaHolidays::find()->select('holidaydate')->all(), 'holidaydate', 'holidaydate');

            $tasks_instruct_length = (new User)->getTableFieldLimit('tbl_task_instruct');
			$userId  = Yii::$app->user->identity->id;
			$optionModel = Options::find()->where(['user_id'=>$userId])->one();
			$filtersavedlocnames="";
			//if(isset($optionModel->set_loc) && $optionModel->set_loc!="") {
				//$filter_saved_loc=json_decode($optionModel->set_loc,true);
				//$filtersavedlocnames=ArrayHelper::map(TeamlocationMaster::find()->select(['id','team_location_name'])->orderBy(['team_location_name' => 'ASC'])->where(['id'=>$filter_saved_loc,'remove'=>0])->all(),'team_location_name','team_location_name');
			//}
            return $this->render('add', [
                'model' => $model,
                'modelInstruct' => $modelInstruct,
                'case_id' => $case_id,
                'priorityList' => $priorityList,
                'projectReqType_data' => $projectReqType_data,
                'listSalesRepo' => $listSalesRepo,
                //'case_productions'=>$case_productions,
                //'case_media'=>$case_media,
                'serviceTaskTemplate_data' => $serviceTaskTemplate_data,
                'serviceTask_data' => $_data,
                'teamservice_locations' => $teamservice_locations,
                'teamserviceName' => $teamserviceName,
                'teamLocation' => $teamLocation,
                'holidayAr' => $holidayAr,
                'tasks_instruct_length' => $tasks_instruct_length,
				'optionModel'=>$optionModel,
				'filtersavedlocnames'=>$filtersavedlocnames
             ]);
        }
    }

    /**
     * Attach a Production to the Project.
     * @return mixed
     */
    public function actionAttachProduction()
    {
    	$this->layout = "mycase";
    	$case_id 	 = Yii::$app->request->get('case_id');
    	$instruct_id = Yii::$app->request->get('instruct_id');
    	$prod_id = Yii::$app->request->get('prod_id',0);
    	$prod_by = Yii::$app->request->get('prod_by',0);
    	$attach_production = Yii::$app->request->get('attach_production');
    	$attach_production_media = Yii::$app->request->get('attach_production_media');
    	$attach_production_media_content = Yii::$app->request->get('attach_production_media_content');
    	if($instruct_id==0)
    		$modelInstruct = new TaskInstruct();
    	else
    		$modelInstruct = TaskInstruct::findOne($instruct_id);
    	
    	$query = EvidenceProduction::find()->select(['tbl_evidence_production.*','tbl_evidence_production_media.prod_id','tbl_evidence_production_media.evid_id'])->where(['tbl_evidence_production.client_case_id' => $case_id])->limit(100)->orderBy('tbl_evidence_production.created desc');
    	$query->join('INNER JOIN','tbl_evidence_production_media','tbl_evidence_production_media.prod_id=tbl_evidence_production.id');
    	$query->join('INNER JOIN','tbl_evidence','tbl_evidence.id=tbl_evidence_production_media.evid_id AND tbl_evidence.status NOT IN(3,5)');
    	$query->join('LEFT JOIN','tbl_evidence_contents','tbl_evidence.id=tbl_evidence_contents.evid_num_id');
    	if($prod_id!=""){
    		$query->andWhere('tbl_evidence_production.id IN('.$prod_id.')');
    	}
    	if($prod_by!=""){
    		$query->andWhere(['like', 'prod_party', $prod_by]);
    	}
    	if(isset($attach_production_media) && $attach_production_media!=""){
    		if(count(explode(",",$attach_production_media)) > 1){
    			$str="";
    			foreach (explode(",",$attach_production_media) as $pm){if($str==""){$str="'".$pm."'";}else{$str=$str.","."'".$pm."'";}}
    			$query->andWhere("CONCAT(prod_id,'_',evid_id) NOT IN (".$str.")");
    		}else{
    			$query->andWhere("CONCAT(prod_id,'_',evid_id) NOT IN ('".$attach_production_media."')");
    		}
    	}
    	if(isset($attach_production_media_content) && $attach_production_media_content!=""){
    		if(count(explode(",",$attach_production_media_content)) > 1){
    			$strc="";
    			foreach (explode(",",$attach_production_media) as $pmc){if($strc==""){$strc="'".$pmc."'";}else{$strc=$strc.","."'".$pmc."'";}}
    			$query->andWhere("CONCAT(CONCAT(prod_id,'_',evid_id),'_',tbl_evidence_contents.id) NOT IN (".$strc.")");
    		}else{
    			$query->andWhere("CONCAT(CONCAT(prod_id,'_',evid_id),'_',tbl_evidence_contents.id) NOT IN ('".$attach_production_media_content."')");
    		}
    	}
    	$query->andWhere('tbl_evidence.status NOT IN(3,5)');
    	$query->distinct();
    	$case_productions = $query->all();
    	$where="";
    	return $this->renderAjax('attach-production', ['case_id'=>$case_id,'case_productions'=>$case_productions,'modelInstruct'=>$modelInstruct,'attach_production'=>$attach_production,'attach_production_media'=>$attach_production_media,'attach_production_media_content'=>$attach_production_media_content]);
    }
    
    public function actionSearchProduction($q = null) {
    	$case_id = Yii::$app->request->get('case_id',0);
    	\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    	$out = ['results' => ['id' => '', 'text' => '']];
    	$query = new Query;
    	//$query->select('id, id AS text')->from('tbl_evidence_production')->where('client_case_id IN ('.$case_id.')')->andWhere('id in (select prod_id from tbl_evidence_production_media)');
    	$query = EvidenceProduction::find()->select('tbl_evidence_production.id, tbl_evidence_production.id AS text');
    	$query->join('INNER JOIN','tbl_evidence_production_media','tbl_evidence_production_media.prod_id=tbl_evidence_production.id');
    	$query->join('INNER JOIN','tbl_evidence','tbl_evidence.id=tbl_evidence_production_media.evid_id AND tbl_evidence.status NOT IN(3,5)');
    	$query->where('client_case_id IN ('.$case_id.')')->andWhere('tbl_evidence_production.id in (select prod_id from tbl_evidence_production_media)');
    	$query->join('LEFT JOIN','tbl_evidence_contents','tbl_evidence.id=tbl_evidence_contents.evid_num_id');
    	if (!is_null($q)) {
    		$query->andWhere(['like', 'tbl_evidence_production.id', $q]);
    	}
    	$query->distinct();
    	$query->orderBy('tbl_evidence_production.id desc');
    	$query->limit(100);
    	$command = $query->createCommand();
    	$data = $command->queryAll();
    	$out['results'] = array_values($data);
    	return $out;
    }
    
    public function actionSearchProductionBy($q = null) {
    	$case_id = Yii::$app->request->get('case_id',0);
    	\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    	$out = ['results' => ['id' => '', 'text' => '']];
    	$query = new Query;
    	//$query->select('prod_party as id, prod_party AS text')->from('tbl_evidence_production')->where('client_case_id IN ('.$case_id.')')->andWhere('id in (select prod_id from tbl_evidence_production_media)');
    	$query = EvidenceProduction::find()->select('tbl_evidence_production.prod_party as id, tbl_evidence_production.prod_party AS text');
    	$query->join('INNER JOIN','tbl_evidence_production_media','tbl_evidence_production_media.prod_id=tbl_evidence_production.id');
    	$query->join('INNER JOIN','tbl_evidence','tbl_evidence.id=tbl_evidence_production_media.evid_id AND tbl_evidence.status NOT IN(3,5)');
    	$query->where('client_case_id IN ('.$case_id.')')->andWhere('tbl_evidence_production.id in (select prod_id from tbl_evidence_production_media)');
    	$query->join('LEFT JOIN','tbl_evidence_contents','tbl_evidence.id=tbl_evidence_contents.evid_num_id');
    	if (!is_null($q)) {
    		$query->andWhere(['like', 'prod_party', $q]);
    	}
    	
    	$query->distinct();
    	$query->orderBy('tbl_evidence_production.prod_party desc');
    	$query->limit(100);
    	$command = $query->createCommand();
    	$data = $command->queryAll();
    	$out['results'] = array_values($data);
    	return $out;
    }
    public function actionShowattachprod($case_id){
    	$instruct_id = Yii::$app->request->post('instruct_id');
    	$attach_production = Yii::$app->request->post('attach_production');
    	$attach_media = Yii::$app->request->post('attach_media');
    	$attach_media_content = Yii::$app->request->post('attach_media_content');
    	if($instruct_id==0)
    		$modelInstruct = new TaskInstruct();
    	else
    		$modelInstruct = TaskInstruct::findOne($instruct_id);
    	 
    	$case_productions = EvidenceProduction::find()->where(['client_case_id' => $case_id])->andWhere('id in (select prod_id from tbl_evidence_production_media Where prod_id IN('.$attach_production.'))')->orderBy('created desc')->all();
    	return $this->renderAjax('Showattachproduction', ['case_id'=>$case_id,'case_productions'=>$case_productions,'attach_production'=>$attach_production,'attach_media'=>$attach_media,'attach_media_content'=>$attach_media_content,'modelInstruct'=>$modelInstruct]);
    }
    public function actionGetSearchproduction()
    {
    	$case_id = Yii::$app->request->get('case_id',0);
    	$prod_id = Yii::$app->request->get('prod_id',0);
    	$prod_by = Yii::$app->request->get('prod_by',0);
    	$offset= Yii::$app->request->get('offset',0);
    	$attach_production = Yii::$app->request->post('attach_production');
    	$attach_production_media = Yii::$app->request->post('attach_media');
    	$attach_production_media_content = Yii::$app->request->post('attach_media_content');
    	
    	$query = EvidenceProduction::find()->select(['tbl_evidence_production.*','tbl_evidence_production_media.prod_id','tbl_evidence_production_media.evid_id'])->where(['tbl_evidence_production.client_case_id' => $case_id])->offset($offset)->limit(10)->orderBy('tbl_evidence_production.created desc');
    	$query->join('INNER JOIN','tbl_evidence_production_media','tbl_evidence_production_media.prod_id=tbl_evidence_production.id');
    	$query->join('INNER JOIN','tbl_evidence','tbl_evidence.id=tbl_evidence_production_media.evid_id');
    	$query->join('LEFT JOIN','tbl_evidence_contents','tbl_evidence.id=tbl_evidence_contents.evid_num_id');
    	if($prod_id!=""){
            $query->andWhere('tbl_evidence_production.id IN('.$prod_id.')');
    	}
    	if($prod_by!=""){
            $query->andWhere(['like', 'prod_party', $prod_by]);
    	}
    	if(isset($attach_production_media) && $attach_production_media!=""){
            if(count(explode(",",$attach_production_media)) > 1){
                $str="";
                foreach (explode(",",$attach_production_media) as $pm){if($str==""){$str="'".$pm."'";}else{$str=$str.","."'".$pm."'";}}
                if($str!="")
                    $query->andWhere("CONCAT(prod_id,'_',evid_id) NOT IN (".$str.")");
            } else {
                    $query->andWhere("CONCAT(prod_id,'_',evid_id) NOT IN ('".$attach_production_media."')");
            }
    	}
    	if(isset($attach_production_media_content) && $attach_production_media_content!=""){
            if(count(explode(",",$attach_production_media_content)) > 1) {
                $strc="";
                foreach (explode(",",$attach_production_media) as $pmc){if($strc==""){$strc="'".$pmc."'";}else{$strc=$strc.","."'".$pmc."'";}}
                if($strc!="")
                    $query->andWhere("CONCAT(CONCAT(prod_id,'_',evid_id),'_',tbl_evidence_contents.id) NOT IN (".$strc.")");
            } else {
                    $query->andWhere("CONCAT(CONCAT(prod_id,'_',evid_id),'_',tbl_evidence_contents.id) NOT IN ('".$attach_production_media_content."')");
            }
    	}
    	$query->andWhere('tbl_evidence.status NOT IN(3,5)');
    	$case_productions = $query->all();
    	return $this->renderAjax('attach-search-production', ['case_id'=>$case_id,'case_productions'=>$case_productions,'modelInstruct'=>$modelInstruct,'attach_production'=>$attach_production,'attach_production_media'=>$attach_production_media,'attach_production_media_content'=>$attach_production_media_content]);
    	/*$query = EvidenceProduction::find()->where(['client_case_id' => $case_id])->offset($offset)->limit(10)->orderBy('created desc');
    	if($prod_id!=""){
            $query->andWhere('id in (select prod_id from tbl_evidence_production_media Where prod_id IN('.$prod_id.'))');
    	}else{
            $query->andWhere('id in (select prod_id from tbl_evidence_production_media)');
    	}
    	if($prod_by!=""){
            $query->andWhere(['like', 'prod_party', $prod_by]);
    	}
    	$case_productions = $query->all();
    	return $this->renderAjax('attach-search-production', ['case_id'=>$case_id,'case_productions'=>$case_productions,'modelInstruct'=>$modelInstruct,'attach_production'=>$attach_production,'attach_media'=>$attach_media,'attach_media_content'=>$attach_media_content]);
    	*/
    }
    /**
     * Attach a Media to the Project.
     * @return mixed
     */
    public function actionAttachMedia()
    {
    	$this->layout = "mycase";
    	$case_id 	 = Yii::$app->request->get('case_id');
    	$instruct_id = Yii::$app->request->get('instruct_id');
    	$attach_media = Yii::$app->request->get('attach_media');
    	$attach_media_content = Yii::$app->request->get('attach_media_content');
    	if($instruct_id==0)
    		$modelInstruct = new TaskInstruct();
    	else 
    		$modelInstruct = TaskInstruct::findOne($instruct_id);
    	
    	$case_media=(new TaskInstructServicetask())->getCaseMedias($case_id,100,0,$attach_media,$attach_media_content);
    	return $this->renderAjax('attach-media', ['case_id'=>$case_id,'case_media'=>$case_media,'modelInstruct'=>$modelInstruct]);
    }
    public function actionShowattachmedia($case_id){
    	$instruct_id = Yii::$app->request->post('instruct_id');
    	$attach_media = Yii::$app->request->post('attach_media');
    	$attach_media_content = Yii::$app->request->post('attach_media_content');
    	if($instruct_id==0)
    		$modelInstruct = new TaskInstruct();
    	else
    		$modelInstruct = TaskInstruct::findOne($instruct_id);
    	
    	$case_media = (new TaskInstructServicetask())->getSelectedMedias($case_id,$attach_media,$attach_media_content);
    	return $this->renderAjax('Showattachmedia', ['case_id'=>$case_id,'case_media'=>$case_media,'modelInstruct'=>$modelInstruct]);
    }
    
    public function actionSearchMedia($q = null) {
    	$case_id = Yii::$app->request->get('case_id',0);
    	$client_case_sql="SELECT evid_num_id FROM tbl_client_case_evidence WHERE client_case_id =".$case_id." GROUP BY tbl_client_case_evidence.evid_num_id ";
    	\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    	$out = ['results' => ['id' => '', 'text' => '']];
    	$query = new Query;
    	$query->select('id, id AS text')->from('tbl_evidence')->where('id IN ('.$client_case_sql.') AND status NOT IN(3,5)');
    	if (!is_null($q)) {
    		$query->andWhere(['like', 'id', $q]);
    	}
    	$query->limit(100);
    	$command = $query->createCommand();
    	$data = $command->queryAll();
    	$out['results'] = array_values($data);
    	return $out;
    }
    
    public function actionSearchMediaType($q = null) {
    	$case_id = Yii::$app->request->get('case_id',0);
    	$client_case_sql="SELECT evid_num_id FROM tbl_client_case_evidence WHERE client_case_id =".$case_id." GROUP BY tbl_client_case_evidence.evid_num_id ";
    	\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    	$out = ['results' => ['id' => '', 'text' => '']];
    	$query = new Query;
    	$query->select('tbl_evidence_type.evidence_name AS id, tbl_evidence_type.evidence_name AS text')->from('tbl_evidence')->where('tbl_evidence.id IN ('.$client_case_sql.') AND status NOT IN(3,5)');
    	//$query->join(['INNER JOIN','tbl_evidencetype','tbl_evidencetype.id = tbl_evidence.evid_type']);
    	$query->join('LEFT JOIN', 'tbl_evidence_type','tbl_evidence_type.id = tbl_evidence.evid_type');
    	if (!is_null($q)) {
    		$query->andWhere(['like', 'tbl_evidence_type.evidence_name', $q]);
    	}
    	$query->limit(100);
    	$command = $query->createCommand();
    	$data = $command->queryAll();
    	$out['results'] = array_values($data);
    	return $out;
    }
    
    public function actionGetSearchmedia(){
    	$case_id = Yii::$app->request->get('case_id',0);
    	$media_id = Yii::$app->request->get('media_id',0);
    	$media_type = Yii::$app->request->get('media_type',0);
    	$offset= Yii::$app->request->get('offset',0);
    	$attach_media = Yii::$app->request->get('attach_media','');
    	$attach_media_content = Yii::$app->request->get('attach_media_content','');
    	$case_media=(new TaskInstructServicetask())->getSearchCaseMedias($case_id,100,$offset,$attach_media,$attach_media_content,$media_id,$media_type);
    	return $this->renderAjax('attach-search-media', ['case_id'=>$case_id,'case_media'=>$case_media,'modelInstruct'=>$modelInstruct]);
    }
    /**
     * Saved Project model.
     * If creation is successful, the browser will be redirected to the 'case projects list' Or 'saved projects list' page.
     * @return mixed
     */
    public function actionSaved($instruction_id,$case_id){
    	$this->layout = "mycase";
    	$model = new Tasks();
    	$modelInstruct = TaskInstruct::findOne($instruction_id);
    	if($modelInstruct->saved == 0){
    		return $this->redirect(['case-projects/index', 'case_id' => $case_id]);
    	}
    	if (Yii::$app->request->post()) {
    		$post_data = Yii::$app->request->post();
    		//echo "<pre>",print_r($post_data),"</pre>";
    		//echo "<pre>",print_r($_FILES),"</pre>";
    		//die; 
    		if($post_data['flag']=='save'){
    			$model->updateSaveProject($post_data,$_FILES,$instruction_id);
    			return $this->redirect(['case-projects/load-saved-projects', 'case_id' => $post_data['case_id']]);
    		}
    		if($post_data['flag']=='submit'){
				$post_data['fromSaved'] = 1;
    			$model->submitProject($post_data,$_FILES,$instruction_id);
    			return $this->redirect(['case-projects/index', 'case_id' => $post_data['case_id']]);
    		}
    	} else {
    		$priorityList =ArrayHelper::map(PriorityProject::find()->select(['id', 'priority'])->where(['remove' => 0])->all(),'id', 'priority');
    		$projectReqType_data = ArrayHelper::map(ProjectRequestType::find()->select(['id', 'request_type'])->orderBy('request_type ASC')->where('remove=0')->all(), 'id', 'request_type');
    		$listSalesRepo = ArrayHelper::map(User::find()->select(['id', "CONCAT(usr_first_name,' ',usr_lastname) as full_name"])->orderBy('full_name ASC')->all(), 'id', 'full_name');
    		//$case_productions = EvidenceProduction::find()->where(['client_case_id' => $case_id])->orderBy('id desc')->all();
    		//$case_media=(new TaskInstructServicetask())->getCaseMedias($case_id);
    	
    		$serviceTaskTemplate_data = TasksTemplates::find()->select(['tbl_tasks_templates.id','temp_name'])->joinWith('tasksTemplatesServiceTasks')->orderby('temp_sortorder ASC')->asArray()->all();
    		$holidayAr = ArrayHelper::map(TeamserviceSlaHolidays::find()->select('holidaydate')->all(),'holidaydate','holidaydate');
    	
			$modelInstruct->task_duedate='';
			$modelInstruct->task_timedue='';
			$userId  = Yii::$app->user->identity->id;
			$optionModel = Options::find()->where(['user_id'=>$userId])->one();
			$filtersavedlocnames="";
			//if(isset($optionModel->set_loc) && $optionModel->set_loc!="") {
			//	$filter_saved_loc=json_decode($optionModel->set_loc,true);
			//	$filtersavedlocnames=ArrayHelper::map(TeamlocationMaster::find()->select(['id','team_location_name'])->orderBy(['team_location_name' => 'ASC'])->where(['id'=>$filter_saved_loc,'remove'=>0])->all(),'team_location_name','team_location_name');
			//}
    		return $this->render('saved', [
				'model' => $model,
				'modelInstruct'=>$modelInstruct,
				'case_id'=>$case_id,
				'priorityList'=>$priorityList,
				'projectReqType_data'=>$projectReqType_data,
				'listSalesRepo'=>$listSalesRepo,
				//'case_productions'=>$case_productions,
				//'case_media'=>$case_media,
				'serviceTaskTemplate_data'=>$serviceTaskTemplate_data,
				'serviceTask_data'=>$_data,
				'teamservice_locations'=>$teamservice_locations,
				'teamserviceName'=>$teamserviceName,
				'teamLocation'=>$teamLocation,
				'holidayAr'=>$holidayAr,
				'optionModel'=>$optionModel,
				'filtersavedlocnames'=>$filtersavedlocnames

    		]);
    	}
    }
    /* Get formbuilder data for selected service tasks on add, change project page */
    public function actionGetformbuilderdata()
    {
        $servicetask_ids   = Yii::$app->request->post('servicetask_ids',null);
        $project_id        = Yii::$app->request->post('project_id',0);
        $instruction_id	   = Yii::$app->request->post('instruction_id',0);
        $flag	   		   = Yii::$app->request->post('flag');
        $loadprevoius	   = Yii::$app->request->post('loadprevoius',0);
        $new_servicetask_id= Yii::$app->request->post('new_servicetask_ids',[]);
        
        $formValues = array();
        $unitValues = array();
        $activeinstruction_id=0;
        if($project_id > 0) {
        	$activeinstruction_id = TaskInstruct::find()->select('id')->where('isactive = 1 AND task_id = '.$project_id)->one()->id;
        	$forminstrval = FormInstructionValues::find()->select(['form_builder_id','element_value','element_unit'])->where(['task_instruct_id'=>$activeinstruction_id])->all();
			//$formValues=ArrayHelper::map(FormInstructionValues::find()->select(['form_builder_id','element_value'])->where(['task_instruct_id'=>$activeinstruction_id])->all(),'form_builder_id','element_value');
			
        	if(!empty($forminstrval)){
				foreach($forminstrval as $instrval){
					$formValues[$instrval['form_builder_id']] = $instrval['element_value'];
					$unitValues[$instrval['form_builder_id']] = $instrval['element_unit'];
				}
			}
        } else {
        	if($instruction_id > 0) {
        		$activeinstruction_id = $instruction_id;
        		//$formValues=ArrayHelper::map(FormInstructionValues::find()->select(['form_builder_id','element_value'])->where(['task_instruct_id'=>$activeinstruction_id])->all(),'form_builder_id','element_value');
        		
        		$forminstrval = FormInstructionValues::find()->select(['form_builder_id','element_value','element_unit'])->where(['task_instruct_id'=>$activeinstruction_id])->all();
				
				if(!empty($forminstrval)){
					foreach($forminstrval as $instrval){
						$formValues[$instrval['form_builder_id']] = $instrval['element_value'];
						$unitValues[$instrval['form_builder_id']] = $instrval['element_unit'];
					}
				}
        	}
        }
        
        $servicetasks = explode(",",$servicetask_ids);
        $model = new FormBuilder();
        $formbuilder_data = array();
        foreach($servicetasks as $val) {
        	if(is_numeric($val)) {
        		if(!is_array($formbuilder_data[$val])){$formbuilder_data[$val] = array();}
                            if($flag == 'Edit') {
                                    $form_builder_mode = 'formvalues';
                            } else {
                                    // For New INS and data form
                                    $form_builder_mode = 'formbuilder';
                            }
                            if($loadprevoius > 0 && $project_id > 0){
                                $form_mode = 'getOnlyActiveElements';
                            }else{
                                $form_mode = 'front';
                            }                                   
                                        
        		$formbuilder_data[$val] = array_merge($model->getFromData($val,1,'DESC',$form_builder_mode,$instruction_id,$form_mode), $formbuilder_data[$val]);
        	}
        }
//		echo "<pre>";print_r($formbuilder_data); exit;
		return $this->renderAjax('_formbuilderdetails', [
    			'formbuilder_data'=>$formbuilder_data,
        		'formValues'=>$formValues,
        		'unitValues' => $unitValues,
        		'activeinstruction_id'=>$activeinstruction_id,
        		'flag'=>$flag,
        		'project_id' => $project_id,
        		'loadprevoius' => $loadprevoius,
        		'new_servicetask_id' => $new_servicetask_id
    	]);
    }
    /**
     * Show Instruction Attachment service wise by project id or instruction id.
     * */
    public function actionShowAttachment($project_id,$instruction_id){
    	$this->layout=false;
    	$service_attachments=array();
    	$model = new TaskInstruct();
    	if($project_id > 0){
    		$model = TaskInstruct::find()->where("isactive = '1' AND task_id = ".$project_id)->one();
    	}else{
    		if($instruction_id > 0){
    			$model = TaskInstruct::findOne($instruction_id);
    		}
    	}
    	if(!empty($model->taskInstructServicetasks)){
    		foreach ($model->taskInstructServicetasks as $taskInstructServicetasks){
    			if(!empty($taskInstructServicetasks->instructionAttachments)){
    				foreach ($taskInstructServicetasks->instructionAttachments as $attachment){
    					$service_attachments[$taskInstructServicetasks->servicetask_id][] = array('id'=>$attachment->id,'name'=>$attachment->fname);
    				}
    			}
    		}
    	}
    	return json_encode($service_attachments);
    }
    
    /**
     * Get Workflow Template and Workflow Service task for add,change save project 
     * */
    public function actionWorkflow($case_id)
    {
    	$loc_ids   		= Yii::$app->request->get('loc_ids',null);
    	$request_type   = Yii::$app->request->get('request_type',null);
		$flag 			= Yii::$app->request->get('flag',null);
    	$serviceTaskTemplate_servicedata = '';
    	$serviceTaskTemplate_data = '';
    	
    	/*if($loc_ids!='' && $loc_ids!=null){
			$locations = explode(",",$loc_ids);
			foreach ($locations as $key=>$loc){if($loc==""){unset($locations[$key]);}}
			$serviceTaskTemplate_servicedata=(new TasksTemplates)->processWorkflowData($serviceTaskTemplate_data,$locations,$case_id);
		}*/
		
		//$where='';
		//if($request_type!='' && $request_type!=null)
		//	$where = 'WHERE project_request_type_id = '.$request_type;
		
		//$serviceTaskTemplate_data = TasksTemplates::find()->select(['tbl_tasks_templates.id','temp_name'])->joinWith('tasksTemplatesServiceTasks')->where('tbl_tasks_templates.id IN (SELECT task_template_id  FROM tbl_templates_request_types '.$where.')')->orderby('temp_sortorder ASC')->asArray()->all();
    	$serviceTaskTemplate_servicedata = '';
        $serviceTaskTemplate_data = '';
     	$locations=array();
     	if($loc_ids!='' && $loc_ids!=null){
        $locations = explode(",",$loc_ids);
        foreach ($locations as $key=>$loc){if($loc==""){unset($locations[$key]);}}
        	//$serviceTaskTemplate_servicedata=(new TasksTemplates)->processWorkflowData($serviceTaskTemplate_data,$locations,$case_id);
        }
        $where ='';
      	if($request_type!='' && $request_type!=null)
      		$where = 'WHERE project_request_type_id = '.$request_type;
		
		if(is_array($locations) && !empty($locations)) {
			if($where!=""){
				$serviceTaskTemplate_data = TasksTemplates::find()->select(['tbl_tasks_templates.id','temp_name'])->joinWith(['tasksTemplatesServiceTasks'],false)->where('tbl_tasks_templates.id IN (SELECT task_template_id  FROM tbl_templates_request_types '.$where.')')->andWhere(['tbl_tasks_templates_service_tasks.team_loc'=>$locations])->orderby('temp_sortorder ASC')->asArray()->all();
			}else{
				$serviceTaskTemplate_data = TasksTemplates::find()->select(['tbl_tasks_templates.id','temp_name'])->joinWith(['tasksTemplatesServiceTasks'],false)->where(['tbl_tasks_templates_service_tasks.team_loc'=>$locations])->orderby('temp_sortorder ASC')->asArray()->all();	
			}
		} else {
			if($where!=""){
				$serviceTaskTemplate_data = TasksTemplates::find()->select(['tbl_tasks_templates.id','temp_name'])->joinWith(['tasksTemplatesServiceTasks'],false)->where('tbl_tasks_templates.id IN (SELECT task_template_id  FROM tbl_templates_request_types '.$where.')')->orderby('temp_sortorder ASC')->asArray()->all();
			}else{
				$serviceTaskTemplate_data = TasksTemplates::find()->select(['tbl_tasks_templates.id','temp_name'])->joinWith(['tasksTemplatesServiceTasks'],false)->orderby('temp_sortorder ASC')->asArray()->all();
			}
		}
    	//$serviceTaskTemplate_servicedata=(new TasksTemplates)->processWorkflowData($serviceTaskTemplate_data,$locations,$case_id);
    	
    	$teamservice_locations=array();
    	$teamLocation=array();
    	$teamserviceName=array();
    	//$sql_exculdeservice_cases= "SELECT COUNT(*) FROM tbl_case_xteam INNER JOIN tbl_teamservice ON tbl_case_xteam.teamservice_id = tbl_teamservice.id WHERE ((client_case_id=$case_id) AND (team_loc=tbl_servicetask_team_locs.team_loc) AND (teamservice_id=teamservice_id)) AND (tbl_teamservice.teamid=teamId)";
		$sql_exculdeservice_cases= "SELECT COUNT(*) FROM tbl_case_xteam INNER JOIN tbl_teamservice  as tt ON tbl_case_xteam.teamservice_id = tt.id WHERE ((client_case_id=$case_id) AND (tbl_case_xteam.team_loc=tbl_servicetask_team_locs.team_loc) AND (tbl_case_xteam.teamservice_id=tbl_teamservice.id)) AND (tt.teamid=teamId)";
		$_sql="SELECT tbl_teamservice.id as teamservice_id, tbl_teamservice.teamid as teamId,tbl_teamservice.service_name,tbl_servicetask_team_locs.team_loc ,service_task,tbl_servicetask.id,tbl_teamlocation_master.team_location_name,($sql_exculdeservice_cases) as exculdeservice_cases 
		FROM tbl_servicetask
		left join tbl_servicetask_team_locs on tbl_servicetask_team_locs.servicetask_id=tbl_servicetask.id
		inner join tbl_teamservice on tbl_teamservice.id=tbl_servicetask.teamservice_id
		left join tbl_teamlocation_master on tbl_teamlocation_master.id=tbl_servicetask_team_locs.team_loc
		inner join tbl_team on tbl_team.id=tbl_teamservice.teamId
		WHERE publish=1 and task_hide=0 AND ( tbl_servicetask_team_locs.team_loc in (select team_loc from tbl_teamservice_locs where tbl_teamservice_locs.team_loc=tbl_servicetask_team_locs.team_loc and tbl_teamservice_locs.teamservice_id=tbl_servicetask.teamservice_id) OR tbl_servicetask_team_locs.team_loc=0)
		ORDER BY tbl_team.sort_order ASC,tbl_teamservice.sort_order ASC,tbl_servicetask.service_order ASC";
    	//echo $_sql;die;
    	$_data=Yii::$app->db->createCommand($_sql)->queryAll();
    	/* Count If any one service is enabled */
    	$total_excluded_services = CaseXteam::find()->Where(['client_case_id'=>$case_id])->select(['id'])->count();
    	$client_id 				 = (new ClientCase)->getClientId($case_id);
    	foreach ($_data as $k=>$my_data){
    		if($my_data['teamId']==1){
    			$my_data['team_loc']=0;
    		}
    		$teamserviceName[$my_data['teamservice_id']]=$my_data['service_name'];
    		$teamLocation[$my_data['team_loc']]=$my_data['team_location_name'];
			$teamId=$my_data['teamId'];	
    		$exculdeservice_cases=$my_data['exculdeservice_cases'];
			/*CaseXteam::find()->where(['client_case_i'=>$case_id,'team_loc'=>$my_data['team_loc'],'teamservice_id'=>$my_data['teamservice_id']])->select(['id'])
			->innerJoinWith(['teamservice' => function(\yii\db\ActiveQuery $query) use ($teamId) {
				$query->where(['tbl_teamservice.teamid'=>$teamId]);
			}],false)->count();*/
						
    		
			/* Start: IRT-5 
			* Modified Date : 15-02-2017 
			* Modified By   : Nelson Rana */ 
			if($total_excluded_services == 0) { 
				$exculdeservice_clients = ClientXteam::find()->where(['client_id'=>$client_id,'team_loc'=>$my_data['team_loc'],'teamservice_id'=>$my_data['teamservice_id']])->select(['id'])
				->innerJoinWith(['teamservice' => function(\yii\db\ActiveQuery $query) use ($teamId) {
				$query->where(['tbl_teamservice.teamid'=>$teamId]);
				}],false)->count();
				if($exculdeservice_clients == 0) {								
					if(!empty($locations)){					
						if(in_array($my_data['team_loc'],$locations)){
							$teamservice_locations[$my_data['teamservice_id']][$my_data['team_loc']][$my_data['id']]=$my_data;
						}
					}else{
						$teamservice_locations[$my_data['teamservice_id']][$my_data['team_loc']][$my_data['id']]=$my_data;
					}
				}								
			} else {
				if($exculdeservice_cases==0) {								
					if(!empty($locations)) {					
						if(in_array($my_data['team_loc'],$locations)) {
							$teamservice_locations[$my_data['teamservice_id']][$my_data['team_loc']][$my_data['id']]=$my_data;
						}
					} else {
						$teamservice_locations[$my_data['teamservice_id']][$my_data['team_loc']][$my_data['id']]=$my_data;
					}
				}
			}
			/* Ends: IRT- 5 */	    				
    	}

		//echo "<pre>",print_r($serviceTaskTemplate_data),"</pre>";die;
    	
		$sttemplateList = [];
		foreach($serviceTaskTemplate_data as $tmpdata) {
		    $template = [];
			$template['title'] = $tmpdata['temp_name'];
			$template['isFolder'] = false;
			$template['key'] = $tmpdata['id'];
			$sttemplateList[] = $template;
		}   
		$stasklateList = [];
		if(!empty($teamservice_locations)){
			foreach ($teamservice_locations as $key => $value) {
            	foreach ($teamservice_locations[$key] as $tlkey => $data) { 
					$template = [];
					$template['title'] = $teamserviceName[$key];
					if(isset($teamLocation[$tlkey])) {
						$template['title']=$template['title'].' - '.$teamLocation[$tlkey];
					}
					$template['isFolder'] = true;
					$template['key'] = $key . $tlkey;

					if (isset($teamservice_locations[$key][$tlkey]) && !empty($teamservice_locations[$key][$tlkey])) {
						$locs = [];
						foreach ($teamservice_locations[$key][$tlkey] as $service_list) {
							$locs['title'] = $service_list['service_task'];
							$locs['key'] = $service_list['id']."_".$tlkey;
							$locs['select'] = false;
							$template['children'][] = $locs;
						}
						if(!empty($template['children']))
							$stasklateList[] = $template;
					}
				}
			}
		}

		

		$userId  = Yii::$app->user->identity->id;
		$optionModel = Options::find()->where(['user_id'=>$userId])->one();
		$filtersavedlocnames="";
		$filter_saved_loc=array();
		if(isset($optionModel->set_loc) && $optionModel->set_loc!="") {
			$filter_saved_loc=json_decode($optionModel->set_loc,true);
			if(is_array($filter_saved_loc) && !empty($filter_saved_loc)){
				$filtersavedlocnames=ArrayHelper::map(TeamlocationMaster::find()->select(['id','team_location_name'])->orderBy(['team_location_name' => 'ASC'])->where(['id'=>$filter_saved_loc,'remove'=>0])->all(),'team_location_name','team_location_name');
			}else{
				$filter_saved_loc=array();
				$filtersavedlocnames="";
			}
		}
		$filter_ids = $filter_saved_loc;
    	$locations = ArrayHelper::map(TeamlocationMaster::find()->select(['id','team_location_name'])->orderBy(['team_location_name' => 'ASC'])->where(['remove'=>0])->all(),'id','team_location_name');
    	if(!in_array(0,array_keys($locations))){
			$locations[0]='Case Location';
		}
    	ksort($locations);
		$locList = [];
		foreach($locations as $loc_id=>$loc_name) {
		    $loc = [];
			$loc['title'] = $loc_name;
			$loc['isFolder'] = false;
			if(isset($filter_ids) && $filter_ids!="" && in_array($loc_id,$filter_ids)) {
				$loc['select'] = true;
			} else {
				$loc['select'] = false;
			}
			$loc['key'] = $loc_id;
			$locList[] = $loc;
		}

    	return $this->renderAjax('workflow', [
    		'case_id'=>$case_id,
    		'serviceTaskTemplate_data'=>$serviceTaskTemplate_data,
    		//'serviceTaskTemplate_servicedata'=>$serviceTaskTemplate_servicedata,
    		'serviceTask_data'=>$_data,
    		'teamservice_locations'=>$teamservice_locations,
    		'teamserviceName'=>$teamserviceName,
    		'teamLocation'=>$teamLocation,
			'sttemplateList'=>$sttemplateList,
			'stasklateList'=>$stasklateList,
			'flag'=>$flag,
			'filtersavedlocnames'=>$filtersavedlocnames,
			'optionModel'=>$optionModel,
			'locList'=>$locList
    	]);
    }
	/**
     * get servicetask json for create/save/change project process
     * */
	public function actionGetServicetaskJson() { 
		$post_data    = Yii::$app->request->post();
		$loc_ids      = Yii::$app->request->post('loc_ids',null);
    	$request_type = Yii::$app->request->post('request_type',null);
		$case_id      = Yii::$app->request->post('case_id',0);
		$locations=array();
		if($loc_ids!='' && $loc_ids!=null){
        $locations = explode(",",$loc_ids);
        foreach ($locations as $key=>$loc){if($loc==""){unset($locations[$key]);}}
        }
		$service_task_data=array();
		if($post_data['currrent_tab']=='Workflow Templates') {
			$teamp_ids=json_decode(str_replace("'", '"',$post_data['wfstask']),true);
			if(!empty($teamp_ids)) {
				$serviceTaskTemplate_data = TasksTemplates::find()->select(['tbl_tasks_templates.id','temp_name'])->joinWith(['tasksTemplatesServiceTasks'],false)->where(['tbl_tasks_templates.id'=>$teamp_ids])->orderby('temp_sortorder ASC')->asArray()->all();
    			$serviceTaskTemplate_servicedata=(new TasksTemplates)->processWorkflowData($serviceTaskTemplate_data,$locations,$case_id);
			
				if(!empty($serviceTaskTemplate_servicedata)) {
					foreach($serviceTaskTemplate_servicedata as $k=>$servicetasktemp_vals) {
						if(is_array($serviceTaskTemplate_servicedata[$k])) {
							foreach($serviceTaskTemplate_servicedata[$k] as $val) {
								$service_task_data[]=$val;
							}
						} else {
							$service_task_data[]=$servicetasktemp_vals;
						}
					}
				}
			}
		} else {
			$service_ids=json_decode(str_replace("'", '"',$post_data['wfstask']),true);
			if(!empty($service_ids)) {
				$serviceytask_ids = "'" . implode ( "', '", $service_ids ) . "'";
				$sql2 = "SELECT tbl_teamservice.teamid as teamId, tbl_teamservice.id as teamservice_id, service_name,tbl_servicetask.id as servicetask_id, tbl_servicetask.service_task, tbl_servicetask_team_locs.team_loc, tbl_teamlocation_master.team_location_name
						FROM tbl_teamservice
						INNER JOIN tbl_team ON tbl_teamservice.teamid = tbl_team.id
						INNER JOIN tbl_servicetask ON tbl_teamservice.id = tbl_servicetask.teamservice_id
						INNER JOIN tbl_servicetask_team_locs ON tbl_servicetask_team_locs.servicetask_id = tbl_servicetask.id
						INNER JOIN tbl_teamlocation_master ON tbl_teamlocation_master.id = tbl_servicetask_team_locs.team_loc
						WHERE tbl_servicetask.publish=1 and tbl_servicetask.task_hide=0
						AND concat(tbl_servicetask.id,'_',tbl_servicetask_team_locs.team_loc) IN (".$serviceytask_ids.")
						";
				$servicesdata = Yii::$app->db->createCommand($sql2)->queryAll();
				foreach($servicesdata as $servicetask) {
					$service_task_data[]=$servicetask;
				}
			}
		}
		echo json_encode($service_task_data,true);
		die;
	}
	public function actionSavelocation(){
		$userId=Yii::$app->user->identity->id;;
		$locationId=Yii::$app->request->post('loc',null);
		$optionModel = Options::find()->where(['user_id'=>$userId])->one();
		if(isset($optionModel->user_id))
		{
			if(!empty($locationId))
			{
				$optionModel->user_id = $userId;
				$optionModel->set_loc = $locationId;
				$optionModel->save(false);
			}
			else {
				$optionModel->user_id = $userId;
				$optionModel->set_loc = NULL;
				$optionModel->save(false);
			}
		}
		else {
			$model = new Options();
			$model->user_id = $userId;
			$model->set_loc = $locationId;
			$model->save(false);
			
		}
		return;
	}
    /**
     * Load Locations for filter workflow in add,change,save project
     * */
    public function actionFilterlocation($case_id){
    	$filter_ids = Yii::$app->request->get('filter_ids');
    	$locations = ArrayHelper::map(TeamlocationMaster::find()->select(['id','team_location_name'])->orderBy(['team_location_name' => 'ASC'])->where(['remove'=>0])->all(),'id','team_location_name');
    	$locations[0]='Case Manager';
    	ksort($locations);
		$locList = [];
		foreach($locations as $loc_id=>$loc_name) {
		    $loc = [];
			$loc['title'] = $loc_name;
			$loc['isFolder'] = false;
			if(isset($filter_ids) && $filter_ids!="" && in_array($loc_id,explode(",",$filter_ids))) {
				$loc['select'] = true;
			} else {
				$loc['select'] = false;
			}
			$loc['key'] = $loc_id;
			$locList[] = $loc;
		}
		return $this->renderAjax('Filterlocation', [
    			'case_id'=>$case_id,
    			'locations'=>$locations,
    			'filter_ids'=>$filter_ids,
				'locList'=>$locList
    	]);
    }
	/**
     * Load Previous new design with client case list
     * */
	public function actionLoadPreviousNew($case_id) {
		
		$case_data=ClientCase::findOne($case_id);
		$client_case_name=$case_data->client->client_name." - ".$case_data->case_name;
		return $this->renderAjax('LoadPreviousNew', [
    			'case_id'=>$case_id,
    			'client_case_name'=>$client_case_name,
    	]);
	}
    /**
     * Load Previous popup with client case list
     * */
    public function actionLoadPrevious($case_id){
    	$role_id = Yii::$app->user->identity->role_id;
    	$user_id = Yii::$app->user->identity->id;
    	if($role_id != 0){
    		$securitySQL = "SELECT client_case_id FROM tbl_project_security WHERE (user_id=$user_id) AND (team_id=0)";
    		$clientCaseList = ArrayHelper::map(ClientCase::find()->select(['tbl_client_case.id','case_name','tbl_client_case.client_id'])->innerJoinWith(['client' => function(\yii\db\ActiveQuery $query){$query->select(['client_name','tbl_client.id']);}])->where(['is_close'=>0])->andWhere('tbl_client_case.id IN ('.$securitySQL.')')->orderBy('client_name,case_name')->all(),'id',function($model, $defaultValue) {
    			return $model['client']['client_name']. " - ". $model['case_name'];
    		});
    	}else{
    		$clientCaseList = ArrayHelper::map(ClientCase::find()->select(['tbl_client_case.id','case_name','tbl_client_case.client_id'])->innerJoinWith(['client' => function(\yii\db\ActiveQuery $query){$query->select(['client_name','tbl_client.id']);}])->where(['is_close'=>0])->orderBy('client_name,case_name')->all(),'id',function($model, $defaultValue) {
    			return $model['client']['client_name']. " - ". $model['case_name'];
    		});
    	}
    	return $this->renderAjax('LoadPrevious', [
    			'case_id'=>$case_id,
    			'clientCaseList'=>$clientCaseList,
    	]);
    }
	/**
     * Load Previous Porject dd by case
     * */
    public function actionLoadProjectDropdown($case_id){
    	return $this->renderAjax('LoadProjectdropdown', [
    			'case_id' => $case_id,
    	]);
    }

	public function actionGetprojectjsonlist($case_id){
		$params = Yii::$app->request->queryParams;
		$page=isset($params['page'])?$params['page']:1;
		$limit=50;
		$mssql="OFFSET 0 ROWS FETCH NEXT $limit ROWS ONLY;";
        $mysql="LIMIT $limit OFFSET 0";
		$offset=( ( $page - 1 ) * $limit );
        if(Yii::$app->db->driverName == 'mysql') {
        	$mysql="LIMIT $limit OFFSET $offset";
            $limit_sql=$mysql;
        } else {
        	$mssql="OFFSET $offset ROWS FETCH NEXT $limit ROWS ONLY;";
            $limit_sql=$mssql;
        }
		\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		$out['items'] = array();
		$out['total_count']=0;
		$out['pagination']['more']=false;
		$q=$params['q'];
		$filterWhere="";
		if(trim($q)!=""){
			$filterWhere=" AND (CONCAT(tbl_tasks.id,' ', tbl_task_instruct.project_name) LIKE '%$q%') ";
		}
		$sql="SELECT tbl_tasks.id, tbl_task_instruct.project_name FROM tbl_tasks LEFT JOIN tbl_client_case ON tbl_tasks.client_case_id = tbl_client_case.id LEFT JOIN tbl_task_instruct ON tbl_tasks.id = tbl_task_instruct.task_id LEFT JOIN tbl_priority_project ON tbl_task_instruct.task_priority = tbl_priority_project.id LEFT JOIN tbl_priority_team ON tbl_tasks.team_priority = tbl_priority_team.id WHERE ((((tbl_client_case.is_close=0) AND (tbl_tasks.client_case_id=$case_id)) AND (task_cancel=0)) AND (task_closed=0)) AND (isactive=1) $filterWhere ORDER BY tbl_tasks.id DESC $limit_sql";
        $countsql="SELECT count(*) FROM tbl_tasks LEFT JOIN tbl_client_case ON tbl_tasks.client_case_id = tbl_client_case.id LEFT JOIN tbl_task_instruct ON tbl_tasks.id = tbl_task_instruct.task_id LEFT JOIN tbl_priority_project ON tbl_task_instruct.task_priority = tbl_priority_project.id LEFT JOIN tbl_priority_team ON tbl_tasks.team_priority = tbl_priority_team.id WHERE ((((tbl_client_case.is_close=0) AND (tbl_tasks.client_case_id=$case_id)) AND (task_cancel=0)) AND (task_closed=0)) AND (isactive=1) $filterWhere ";
		$projectList = Yii::$app->db->createCommand($sql)->queryAll();
		$out['total_count'] = Yii::$app->db->createCommand($countsql)->queryScalar();
		if(!empty($projectList)){
			foreach($projectList as $project){
				if(isset($project['project_name']) && trim($project['project_name'])!="")
					$val=Html::decode($project['id']." - Project Name is ".$project['project_name']);
				else
					$val=Html::decode($project['id']);	

				$out['items'][] = ['id' => $project['id'], 'text' => $val];
			}
		}
		if($out['total_count'] > 0 && ($page * $limit) < $out['total_count']){
			$out['pagination']['more']=true;
		}
		return $out;
	}
    /**
     * Load Previous Porject Gird by case
     * */
    public function actionLoadProjectGird($case_id){
    	$searchModel = new TaskSearch();
    	$dataProvider = $searchModel->searchLoadPrevious(Yii::$app->request->queryParams,$params);
    	return $this->renderAjax('LoadProjectGird', [
    			'searchModel' => $searchModel,
    			'dataProvider' => $dataProvider,
    			'case_id' => $case_id,
    	]);
    }
    public function actionLoadPreviousProjectWorkflow(){
    	$project_id = Yii::$app->request->get('project_id');
    	$projectWorkflow = (new TaskInstructServicetask)->getProjectWorkflow($project_id);
    	return $this->renderAjax('LoadPreviousProjectWorkflow', [
    			'projectWorkflow' => $projectWorkflow,
    			'case_id' => $case_id,
    			'project_id'=>$project_id
    	]);
    }
    /**
     * Calculate SLA logic for Porject
     *  */
    public function actionGetSlaProjectedTime(){
    
    	date_default_timezone_set($_SESSION['usrTZ']);
    
    	$post_data = Yii::$app->request->post();
		
    	$priority = $post_data['priority'];
    	$services_ar = $post_data['service'];
    	$exiting_current_date = "";
    	$taskInfo = "";
    	$existservices = array();
    	if($post_data['current_date']!="" && $post_data['current_time']!=""){
    		$dates = explode("/",$post_data['current_date']);
    		$time = $_REQUEST['current_time'];
    		$ymd = $dates[2]."-".$dates[0]."-".$dates[1];
    		$exiting_current_date = date("$ymd $time:s");
    	}
    
		$evidences = explode(",",$post_data['evidence']);
		$size = 0;
		$unit = 0;
		$evid_ar = array();
		$evid = array();
		$totalkbs = 0;
		foreach($evidences as $evidence1) {
			if(!in_array($evidence1,$evid)){
				$evidDetail = Evidence::find()->where("id=$evidence1")->select(['contents_total_size','unit','contents_total_size_comp','comp_unit'])->one();
				//echo "<pre>",print_r($evidDetail);
				if(!empty($evidDetail)){
					if($evidDetail->contents_total_size != 0 && $evidDetail->contents_total_size != ""){
						$size = $evidDetail->contents_total_size;
						$unit = $evidDetail->evidenceunit->unitMasters->unit_type;
						if($size > 0 && $unit != '')
							$evid_ar[$unit] += ($size * $evidDetail->evidenceunit->unitMasters->unit_size);
        			} else if($evidDetail->contents_total_size_comp != 0 && $evidDetail->contents_total_size_comp != "") {
						$size = $evidDetail->contents_total_size_comp;
						$unit = $evidDetail->evidencecompunit->unitMasters->unit_type;
						if($size > 0 && $unit != '')
							$evid_ar[$unit] += ($size * $evidDetail->evidencecompunit->unitMasters->unit_size);
					}
				}
		        $evid[$evidence1] = $evidence1;
			}
		}
		//echo "<pre>",print_r($evid_ar);
        if(!empty($evid_ar)){
            $evid_ar_data = [];
            foreach($evid_ar as $unit_type => $totalsize){
                $converted = (new Unit)->formatSizeUnits($totalsize,$unit_type,'yes');
                $evid_ar_data[$converted['unit_id']] = $converted['size'];
            }
			
            $evid_ar = $evid_ar_data;
		}
		
    	$businesshours = TeamserviceSlaBusinessHours::find()->one();
    	$workinghours = $businesshours->workinghours;
    	$totalhours = 0;
    	$hours = 0;
    	$service_wise_ar = array();
    	$teamservices = array();
    	foreach($services_ar as $services_arr) {
    		if(!is_numeric($services_arr['id'])){ continue; }
    		$services_str = $services_arr['id'];
    		$loc = $services_arr['loc'];
    		$services = Servicetask::find()->select('teamservice_id')->where("id = $services_str")->one();
    		$service = $services->teamservice_id;
    		$teamserviceSla = TeamserviceSla::find()->where("teamservice_id=$service")->all();
    		$projected_project_time = 0;
    		$sla_logic_id = array();
    		
    		foreach($evid_ar as $unit => $size) {
    			if(!empty($teamserviceSla)){
    				foreach($teamserviceSla as $serviceSla) {
    
    					if($priority == $serviceSla->project_priority_id && $unit == $serviceSla->size_start_unit_id && $unit == $serviceSla->size_end_unit_id && $loc == $serviceSla->team_loc_id) {
    						if($this->isLogicExist($size,$serviceSla->start_logic,$serviceSla->end_logic,$serviceSla->start_qty,$serviceSla->end_qty)){
    							//echo "\n1 ".$this->isLogicExist($size,$serviceSla->start_logic,$serviceSla->end_logic,$serviceSla->start_qty,$serviceSla->end_qty);
    							if($serviceSla->del_time_unit == 2) // $serviceSla->del_time_unit == 2 : Days
    								$projected_project_time += $serviceSla->del_qty * $workinghours; // To convert from days to hours
    							else
    								$projected_project_time += $serviceSla->del_qty;
    							$sla_logic_id[]=$serviceSla->id;
    							$totalhours = 0;
    						}
    					}
    					else if($serviceSla->project_priority_id == 0 && $unit == $serviceSla->size_start_unit_id && $unit == $serviceSla->size_end_unit_id && $loc == $serviceSla->team_loc_id) {
    						if($this->isLogicExist($size,$serviceSla->start_logic,$serviceSla->end_logic,$serviceSla->start_qty,$serviceSla->end_qty)){
    							//echo "\n2 -> ".$this->isLogicExist($size,$serviceSla->start_logic,$serviceSla->end_logic,$serviceSla->start_qty,$serviceSla->end_qty);
    							if($serviceSla->del_time_unit == 2) // $serviceSla->del_time_unit == 2 : Days
    								$projected_project_time += $serviceSla->del_qty * $workinghours; // To convert from days to hours
    							else
    								$projected_project_time += $serviceSla->del_qty;
    							$sla_logic_id[]=$serviceSla->id;
    							$totalhours = 0;
    						}
    					} else {
    						$totalhours = "";
    					}
    				}
    			} else {
    				$totalhours = "";
    			}
    		}
			//echo "<pre>",print_r($sla_logic_id);
    		$sla_logic = "";
    		if(!empty($sla_logic_id))
    			$sla_logic = implode(",",array_unique($sla_logic_id));
    			
    		if(!in_array($service,$teamservices)){
    			$teamservices[$service][$services_str]['sla_logic'] = $sla_logic;
    			$teamservices[$service][$services_str]['time'] = $projected_project_time;
    			$hours += $projected_project_time;
    		}
    			
    		//$service_wise_ar[] = json_encode(array("teamservice"=>$service,"service_id"=>$services_str,'sla_logic'=>$sla_logic,"time"=>$projected_project_time));
    	}
    
    //	echo "<pre>",print_r($teamservices),"</pre>";
    	foreach($teamservices as $teamservice_id=>$service_ids_ar){
    		$service_ids = count($teamservices[$teamservice_id]);
    		foreach ($service_ids_ar as $service_id => $detail){
    			$time = $detail['time'];
    			$sla_logic = $detail['sla_logic'];
				$divided = floor(($time/$service_ids)* 100) / 100;
    			$eachtime = number_format($divided,2);
    			$service_wise_ar[] = json_encode(array("service_id"=>$service_id,'sla_logic'=>$sla_logic,"time"=>$eachtime));
    			//echo "<pre>",print_r(array("service_id"=>$service_id,'sla_logic'=>$sla_logic,"time"=>$eachtime)),"</pre>";
    		}
    			
    	}
    	if($hours > 0){
    		$json_hours = json_decode((new Tasks)->getHours($hours,$exiting_current_date,'getSlaProjectedTime'),true);
    		$json_hours['service_task'] = json_encode($service_wise_ar);
    		$totalhours = json_encode($json_hours);
    	}
    	date_default_timezone_set("UTC");
    	echo $totalhours;exit;
    }
    
	public function actionGetTotalHours()
	{
		$post_data = Yii::$app->request->post();
		$post_data['removed_servicetask_id'] = Yii::$app->request->post('removed_servicetask_id',0);

		//echo "<pre>",print_r($post_data),"</pre>";die;
		$priority = $post_data['priority'];
    	$services_ar = $post_data['service'];
    	$evidences = explode(",",$post_data['evidence']);
		$size = 0;
		$unit = 0;
		$evid_ar = array();
		$evid = array();
		//echo "<pre>";
		foreach($evidences as $evidence1) {
			if($evidence1!='' && $evidence1!=0 && !in_array($evidence1,$evid)){
				$evidDetail = Evidence::find()->where("id=$evidence1")->select(['contents_total_size','unit','contents_total_size_comp','comp_unit'])->one();
				if(!empty($evidDetail)){
					if($evidDetail->contents_total_size != 0 && $evidDetail->contents_total_size != ""){
						$size = $evidDetail->contents_total_size;
						//echo "unit: ",print_r($evidDetail->evidenceunit->unitMasters);
						$unit = $evidDetail->evidenceunit->unitMasters->unit_type;
						if($size > 0 && $unit != '')
							$evid_ar[$unit] += ($size * $evidDetail->evidenceunit->unitMasters->unit_size);
        			} else {
						$size = $evidDetail->contents_total_size_comp;
						//echo "unitcomp: ",print_r($evidDetail->evidencecompunit->unitMasters);
						$unit = $evidDetail->evidencecompunit->unitMasters->unit_type;
						if($size > 0 && $unit != '')
							$evid_ar[$unit] += ($size * $evidDetail->evidencecompunit->unitMasters->unit_size);
					}
					//echo "<pre>",print_r($evid_ar);
				}
		        $evid[$evidence1] = $evidence1;
			}
		}
        if(!empty($evid_ar)){
            $evid_ar_data = [];
            foreach($evid_ar as $unit_type => $totalsize){
				//echo "<br/>",$totalsize,' - ',$unit_type;
                $converted = (new Unit)->formatSizeUnits($totalsize,$unit_type,'yes');
				//echo "<pre>",print_r($converted);
                $evid_ar_data[$converted['unit_id']] = $converted['size'];
            }
            $evid_ar = $evid_ar_data;
		}
		//echo "<pre>",print_r($evid_ar);
    	$businesshours = TeamserviceSlaBusinessHours::find()->one();
    	$workinghours = $businesshours->workinghours;
    	$totalhours = 0;
    	$hours = 0;
    	$service_wise_ar = array();
    	$teamservices = array();
		$existingSTAR = [];
		$diffHours = 0;
    	foreach($services_ar as $services_arr) {

			if(!is_numeric($services_arr['id'])) continue;
			
			$services_str = $services_arr['id'];
    		$loc = $services_arr['loc'];
			$services = Servicetask::find()->select('teamservice_id')->where("id = $services_str")->one();
    		$service = $services->teamservice_id;

			$existingSTAR[$service][$loc]['sla_logic'] = $services_arr['hdn_service_logic'];
			$existingSTAR[$service][$loc]['hours'] += $services_arr['hours'];

    		if($post_data['removed_servicetask_id'] == $services_arr['id']) continue; 
    		    		
			$teamserviceSla = TeamserviceSla::find()->where("teamservice_id=$service")->all();
    		$projected_project_time = 0;
    		$sla_logic_id = array();
    		//echo "<pre>",print_r($teamserviceSla);
    		foreach($evid_ar as $unit => $size) {
    			if(!empty($teamserviceSla)){
    				foreach($teamserviceSla as $serviceSla) {
						//echo "<pre>",print_r($serviceSla),"</pre>";
						//echo  $unit,' = ',$serviceSla->size_start_unit_id,' => ',$size,' -> start : ',$serviceSla->start_qty,' end : ',$serviceSla->end_qty, ' => ', $loc,' loc : loc ',$serviceSla->team_loc_id,' => ',$priority ,' : pr - pr : ',$serviceSla->project_priority_id,"<br/><br/>";
    					if($priority == $serviceSla->project_priority_id && $unit == $serviceSla->size_start_unit_id && $unit == $serviceSla->size_end_unit_id && $loc == $serviceSla->team_loc_id) {
							if($this->isLogicExist($size,$serviceSla->start_logic,$serviceSla->end_logic,$serviceSla->start_qty,$serviceSla->end_qty)){
    							//echo "\n1 ".$this->isLogicExist($size,$serviceSla->start_logic,$serviceSla->end_logic,$serviceSla->start_qty,$serviceSla->end_qty);
    							if($serviceSla->del_time_unit == 2) // $serviceSla->del_time_unit == 2 : Days
    								$projected_project_time += $serviceSla->del_qty * $workinghours; // To convert from days to hours
    							else
    								$projected_project_time += $serviceSla->del_qty;
    							$sla_logic_id[]=$serviceSla->id;
    							//$totalhours = 0;
    						}
    					}
    					else if(($serviceSla->project_priority_id == 0 || $serviceSla->project_priority_id == '') && $unit == $serviceSla->size_start_unit_id && $unit == $serviceSla->size_end_unit_id && $loc == $serviceSla->team_loc_id) {
							//echo $unit,'|',$size,' = ',$serviceSla->start_logic, ' - ', $serviceSla->start_qty,'<br/>';
    						if($this->isLogicExist($size,$serviceSla->start_logic,$serviceSla->end_logic,$serviceSla->start_qty,$serviceSla->end_qty)){
    							//echo "\n2 -> ".$this->isLogicExist($size,$serviceSla->start_logic,$serviceSla->end_logic,$serviceSla->start_qty,$serviceSla->end_qty);
    							if($serviceSla->del_time_unit == 2) // $serviceSla->del_time_unit == 2 : Days
    								$projected_project_time += $serviceSla->del_qty * $workinghours; // To convert from days to hours
    							else
    								$projected_project_time += $serviceSla->del_qty;
    							$sla_logic_id[]=$serviceSla->id;
    							//$totalhours = 0;
    						}
    					} else {
    						//$totalhours = 0;
    					}
    				}
    			} else {
    				//$totalhours = 0;
    			}
    		}

			//echo "<pre>",print_r($sla_logic_id);
    		$sla_logic = "";
    		if(!empty($sla_logic_id))
    			$sla_logic = implode(",",array_unique($sla_logic_id));
    		
    		if(!isset($teamservices[$service][$loc])){
				//$teamservices[$service]['servicetask_id'] = $services_str;
    			$teamservices[$service][$loc]['sla_logic'] = $sla_logic;
    			$teamservices[$service][$loc]['time'] = $projected_project_time;

				//$existingSTAR[$service]['servicetask_id'] = $services_str;
   			
				$hours += $projected_project_time;
    		}

		}

		if(!empty($teamservices)){
			foreach($teamservices as $ser => $arr1){
				foreach($arr1 as $loc => $arr){
					//if(isset($existingSTAR[$ser][$loc]['hours'])){
					if((ceil($existingSTAR[$ser][$loc]['hours']) - $arr['time'])!=0 && ($arr['sla_logic']!=0 || $existingSTAR[$ser][$loc]['sla_logic']!=0))
						$diffHours += $arr['time'] - ceil($existingSTAR[$ser][$loc]['hours']);
					/*} else {
						$diffHours += ceil($arr['hours']);
					}*/
				}
			}
		}

		if(!empty($existingSTAR)){
			foreach($existingSTAR as $serv => $val){
				foreach($val as $loc => $arr){
					if(!isset($teamservices[$serv][$loc]))
						$diffHours -= ceil($arr['hours']);
				}
			}
		}

		//echo "<pre>$diffHours",print_r($existingSTAR),print_r($teamservices);
		//echo json_encode($existingSTAR);die;
		$totalhours = json_encode(['diffHours'=>$diffHours]);
    	
		echo $totalhours;die;
	}

	

	public function actionGetUpdatedDueDate()
	{
		$post_data = Yii::$app->request->post();
		
		if($post_data['due_date']!="" && $post_data['due_time']!="") {
			$due_date = $post_data['due_date'];
			$due_time = $post_data['due_time'];
			$totalhours = $post_data['diffslahours'];
			$slackHours = $post_data['slackHours'];

			$totalhours = $totalhours - $slackHours;
			$duedateAr = (new Tasks)->fnGetUpdatedDueDate($due_date, $due_time, $totalhours);
			
			if($slackHours>0){
				$duedatetimeAr = json_decode($duedateAr,true);
				$slackdatetime = (new Tasks)->fnGetUpdatedDueDate($duedatetimeAr['current_date'], $duedatetimeAr['current_time'], $slackHours);
				$slackdatetimeAr = json_decode($slackdatetime,true);
				$duedatetimeAr['slackdate'] = $slackdatetimeAr['current_date'];
				$duedatetimeAr['slacktime'] = $slackdatetimeAr['current_time'];
				$duedateAr = json_encode($duedatetimeAr);
			}
			echo $duedateAr;die;
		}

	}

	public function actionGetHoursManualProjectedTime() {
		
		date_default_timezone_set($_SESSION['usrTZ']);
        
    	$post_data = Yii::$app->request->post();
		//echo "<pre>",print_r($post_data),"</pre>";die;

		$task_id = "";
		$taskInfo = "";
		$ymd = "";
		$time = "";
		$current_date = '';
		$existservices = array();
		
		if($post_data['current_date']!="" && $post_data['current_time']!="" && $post_data['taskId'] != "") {
			$task_id = $post_data['taskId'];
			$taskInfo = Taskindividual::find()->where(['tasks_id'=>$task_id,'isactive'=>'1'])->select(['service_task'])->one();
			if(!empty($taskInfo)){
				$tasksservices = json_decode($taskInfo->service_task);
				foreach($tasksservices as $teamservice){
					$existservices[$teamservice->teamservice_id] = $teamservice->teamservice_id;
				}
			}
			$time = $post_data['current_time'];
			$ymd = $post_data['current_date'];
			$current_date = date($post_data['current_date']." ".$post_data['current_time']);
		} else if ($post_data['current_date']!="" && $post_data['current_time']!="" && $post_data['taskId'] == "") {
			$time = $post_data['current_time'];
			$ymd = $post_data['current_date'];
			$current_date = date($post_data['current_date']." ".$post_data['current_time']);
		} else {
			$time = date("H:i");
			$ymd = date("Y-m-d");
			$current_date = date("$ymd $time:s");
		}
		
		$totalhours = 0;
		$elsehours = 0;
		$services_ar = $post_data['service'];
		$services = array();
		
		if(!empty($services_ar)) {
			foreach($services_ar as $key => $service) {
				if($service['logic_id'] > 0) {
					$totalhours += $service['time']; 
				} else {
					$elsehours += $service['time'];
				}
			}
		}
		
		$totalhours = $totalhours + $elsehours;
		
		$hours = array();
		if($totalhours > 0) {
			$totalhours = round(number_format($totalhours,2));
			$hoursarr = (new Tasks)->getHours($totalhours, $current_date,"getHoursManualProjectedTime");
			$hours = json_decode($hoursarr,true);

			$hours['slackdate'] = '';
			$hours['slacktime'] = '';
			$slackHours = $post_data['slackhours'];
			if($slackHours>0){
				$slackdatetime = (new Tasks)->fnGetUpdatedDueDate($hours['due_date'], $hours['due_time'], $slackHours);
				$slackdatetimeAr = json_decode($slackdatetime,true);
				$hours['slackdate'] = $slackdatetimeAr['current_date'];
				$hours['slacktime'] = $slackdatetimeAr['current_time'];
			}
        }

		date_default_timezone_set("UTC");

    	return $this->renderPartial('getHoursManualProjectedTime',array('data'=>$hours),false,true);
	}

    /**
     * Validate Add,change save Project Section next previous steps
     * */
    public function actionValidatesteps() {
    	$step=Yii::$app->request->get('step');
    	if($step==1) {
    		$model = new TaskInstruct();
    		if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
    			Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    			return ActiveForm::validate($model);
    		}	
    	}
    	return "[]";
    }
    

    /**
     * Updates an existing Tasks model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
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
     * Deletes an existing Tasks model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        return $this->redirect(['index']);
    }
    /**
     * get task requestor list auto completed action.
     * @param integer $case_id
     * @param string $trem
     * @return json
     */
    public function actionBringProjectname(){
    	$params=Yii::$app->request->get();
    	$data=array();
    	if (isset($params['term'])) {
    		$sql="SELECT id FROM tbl_tasks WHERE client_case_id=".$params['case_id'];
    		$data=ArrayHelper::map(TaskInstruct::find()->select('project_name')->where("task_id IN (".$sql.")")->andWhere(['like','project_name',$params['term']])->all(),'project_name','project_name');
    	}
    	return json_encode($data);
    }
    /**
     * get task requestor list auto completed action.
     * @param integer $case_id
     * @param string $trem
     * @return json
     */
     public function actionBringRequestor(){
     	$params=Yii::$app->request->get();
     	$data=array();
     	if (isset($params['term'])) {
     		$sql="SELECT id FROM tbl_tasks WHERE client_case_id=".$params['case_id'];
     		$data=ArrayHelper::map(TaskInstruct::find()->select('requestor')->where('task_id IN ('.$sql.')')->andWhere(['like','requestor',$params['term']])->all(),'requestor','requestor');
     	}	
     	return json_encode($data);
     }
     
     /**
     * Update an Existing Project model.
     * If creation is successful, the browser will be redirected to the 'case projects list' Or 'saved projects list' page.
     * @return mixed
     */
    public function actionEdit($case_id,$task_id)
    {
    	$this->layout = "mycase";
        $model = new Tasks();
        $modelInstruct = TaskInstruct::find()->Where(['task_id'=>$task_id,'isactive'=>1])->orderby('id DESC')->one();
        $tasks_instruct_length = (new User)->getTableFieldLimit('tbl_task_instruct'); 			
        if (Yii::$app->request->post()){
        	$post_data = Yii::$app->request->post();
			if($post_data['flag']=='resubmit'){
                $post_data['task_id']=$task_id;
        		$model->changeProject($post_data,$_FILES); 

        		// change instructions sending email
        		//SettingsEmail::sendEmail
				EmailCron::saveBackgroundEmail(4,'changed_instructions',$data=array('case_id'=>$case_id,'project_id'=>$task_id,'instrid'=>$modelInstruct->id));
				// case-project
        		// return $this->redirect(['case-projects/change-project', 'case_id' => $post_data['case_id'],'task_id'=>$task_id]);
        		return $this->redirect(['case-projects/index', 'case_id' => $post_data['case_id']]);
        	}
        } else {
            /* IRT-202 Starts*/
        	$priorityList =ArrayHelper::map(PriorityProject::find()->select(['id', 'priority'])
			->where('(remove = 0 OR id IN (select task_priority from tbl_task_instruct where isactive=1 and task_id='.$task_id.'))')
			->orderBy('project_priority_order ASC')
			->all(),'id', 'priority');
			//->where(['remove' => 0])
            /* IRT-202 Ends*/
        	/* IRT-19 Starts */
        	$role_id = Yii::$app->user->identity->role_id;
        	$role_sql = '';
			if($role_id != 0){
				$role_sql = " AND id IN(select project_request_type_id FROM tbl_project_request_type_roles where role_id = $role_id)";
			}
        	$projectReqType_data = ArrayHelper::map(ProjectRequestType::find()->select(['id', 'request_type'])->orderBy('request_type ASC')->where('remove=0'.$role_sql)->all(), 'id', 'request_type');
        	/* IRT-19 Ends*/        	
        	//$projectReqType_data = ArrayHelper::map(ProjectRequestType::find()->select(['id', 'request_type'])->orderBy('request_type ASC')->where('remove=0')->all(), 'id', 'request_type');
        	$listSalesRepo = ArrayHelper::map(User::find()->select(['id', "CONCAT(usr_first_name,' ',usr_lastname) as full_name"])->orderBy('full_name ASC')->all(), 'id', 'full_name');
               // $case_productions = EvidenceProduction::find()->where(['client_case_id' => $case_id])->orderBy('id desc')->all();
                //$case_media=(new TaskInstructServicetask())->getCaseMedias($case_id);
           	$serviceTaskTemplate_data = TasksTemplates::find()->select(['tbl_tasks_templates.id','temp_name'])->joinWith('tasksTemplatesServiceTasks')->orderby('temp_sortorder ASC')->asArray()->all();
            $holidayAr = ArrayHelper::map(TeamserviceSlaHolidays::find()->select('holidaydate')->all(),'holidaydate','holidaydate');
            //echo "<pre>",print_r($model), print_r($modelInstruct),"</pre>";die;
			$userId  = Yii::$app->user->identity->id;
			$optionModel = Options::find()->where(['user_id'=>$userId])->one();
			$filtersavedlocnames="";
			//if(isset($optionModel->set_loc) && $optionModel->set_loc!="") {
			//	$filter_saved_loc=json_decode($optionModel->set_loc,true);
			//	$filtersavedlocnames=ArrayHelper::map(TeamlocationMaster::find()->select(['id','team_location_name'])->orderBy(['team_location_name' => 'ASC'])->where(['id'=>$filter_saved_loc,'remove'=>0])->all(),'team_location_name','team_location_name');
			//}
			
            return $this->render('edit', [
                 'model' => $model,
                 'modelInstruct'=>$modelInstruct,
                 'case_id'=>$case_id,
                 'task_id'=>$task_id,
                 'priorityList'=>$priorityList,
                 'projectReqType_data'=>$projectReqType_data,
                 'listSalesRepo'=>$listSalesRepo,
                 //'case_productions'=>$case_productions,
                 //'case_media'=>$case_media,
                 'serviceTaskTemplate_data'=>$serviceTaskTemplate_data,
                 'serviceTask_data'=>$_data,
                 'teamservice_locations'=>$teamservice_locations,
                 'teamserviceName'=>$teamserviceName,
                 'teamLocation'=>$teamLocation,
                 'holidayAr'=>$holidayAr,
                 'tasks_instruct_length' => $tasks_instruct_length,
				 'optionModel'=>$optionModel,
				 'filtersavedlocnames'=>$filtersavedlocnames
            ]);
        }
    }
    /**
     * Finds the Tasks model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Tasks the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Tasks::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    
    public function isLogicExist($size, $startLogic, $endLogic, $startQty, $endQty){
    	$startlogicSign = Yii::$app->params['startlogicSign'];
    	$endlogicSign   = Yii::$app->params['endlogicSign'];
    	$status = false;
    
    	if($startlogicSign[$startLogic] == ">" && $endlogicSign[$endLogic] == '<' && $size > $startQty && $size < $endQty) {
    		$status = true;
    	} else if ($startlogicSign[$startLogic] == ">" && $endlogicSign[$endLogic] == '<=' && $size > $startQty && $size <= $endQty) {
    		$status = true;
    	} else if ($startlogicSign[$startLogic] == ">=" && $endlogicSign[$endLogic] == '<' && $size >= $startQty && $size < $endQty) {
    		$status = true;
    	} else if ($startlogicSign[$startLogic] == ">=" && $endlogicSign[$endLogic] == '<=' && $size >= $startQty && $size <= $endQty) {
    		$status = true;
    	}
    	return $status;
    }
    
	public function actionAddestime() {
    	$params=Yii::$app->request->post();
    	return $this->renderPartial('loadaddestime',['val'=>$params['val'],"stask_id"=>$params['stask_id']]);
    }
    
    public function actionGetslackhours() {
		
		date_default_timezone_set($_SESSION['usrTZ']);
		$current_date = "";
		
		if($_REQUEST['current_date']!="" && $_REQUEST['current_time']!="") {
			$dates = explode("/",$_REQUEST['current_date']);
			$time = $_REQUEST['current_time'];
			$ymd = $dates[2]."-".$dates[0]."-".$dates[1];
			$current_date = date("$ymd $time:00");
		} else {
			$current_date = date("Y-m-d H:i:00");

			$businesshours = TeamserviceSlaBusinessHours::find()->one();
			$workinghours = $businesshours->workinghours;
			$start_time = $businesshours->start_time;
			$end_time = $businesshours->end_time;
			$workingdays = json_decode($businesshours->workingdays,true);

			if(date("i", strtotime($current_date)) > 30){
				if(date("Y-m-d H:i:00", strtotime($current_date." +1 Hour")) > date("Y-m-d $end_time:00",strtotime($current_date))){
					$current_date = date("Y-m-d $start_time:00", strtotime($current_date." +1 days"));
				} else if(date("Y-m-d H:i:00", strtotime($current_date)) < date("Y-m-d $start_time:00",strtotime($current_date))) {
					$current_date = date("Y-m-d $start_time:00", strtotime($current_date));
				} else {
					$current_date = date("Y-m-d H:00:00", strtotime($current_date." +1 hour"));
				}
			}
			if(date("i", strtotime($current_date)) > 0 && date("i",strtotime($current_date)) < 30){
				if(date("Y-m-d H:i:00", strtotime($current_date." +30 minutes")) > date("Y-m-d $end_time:s",strtotime($current_date))){
					$current_date = date("Y-m-d $start_time:00", strtotime($current_date." +1 days"));
				} else if(date("Y-m-d H:i:00",strtotime($current_date)) < date("Y-m-d $start_time:00", strtotime($current_date))) {
					$current_date = date("Y-m-d $start_time:00", strtotime($current_date));
				} else {
					$current_date = date("Y-m-d H:30:00",strtotime($current_date));
				}
			}

			$slaenddatetime = date("Y-m-d $end_time:00",strtotime($current_date));

			if(strtotime($current_date) == strtotime($slaenddatetime) || strtotime($current_date) > strtotime($slaenddatetime)) {
				$current_date = date("Y-m-d $start_time:00", strtotime($current_date." +1 days"));
			}
		}
		$adjusted_date = "";
		if($_REQUEST['adjusted_date']!=""){
			$dates = explode("/",$_REQUEST['adjusted_date']);
			$ymd = $dates[2]."-".$dates[0]."-".$dates[1];
			if($_REQUEST['adjusted_time']!=""){
				$time = $_REQUEST['adjusted_time'];	
				$adjusted_date = date("$ymd $time:00");
			} else {
				$time = date("H:i");	
				$adjusted_date = date("$ymd $time:00");
			}
			
			/* To check and set SLA hours */
			if(strtotime($adjusted_date) < strtotime($current_date)){
				$current_time = $_REQUEST['current_time'];
				$adjusted_date = date("Y-m-d $current_time:00",strtotime($current_date)); //date("$ymd $time:00");
			}
			
			$businesshours = TeamserviceSlaBusinessHours::find()->one();
			$workinghours = $businesshours->workinghours;
			$start_time = $businesshours->start_time;
			$end_time = $businesshours->end_time;
			$workingdays = json_decode($businesshours->workingdays);
			
			$holidaysRec = TeamserviceSlaHolidays::find()->all();
			$holidayAr = array();
			foreach ($holidaysRec as $hol) {
				$holidayAr[] = $hol->holidaydate;
			}
			
			$occupiedhours = 0;
			$days = 0;
			$lasthours = 0;
			$lastminutes = 0;
			while(strtotime($current_date) <= strtotime($adjusted_date)){
			 
				$currentday = date("N",strtotime($current_date));
				$currentdateforholiday = date("m/d/Y",strtotime($current_date));
				
				if(in_array($currentday, $workingdays) && !in_array($currentdateforholiday,$holidayAr)) {
					$todayremainhours = date("H:i:00",strtotime($current_date));
					
					if(date("Y-m-d",strtotime($current_date)) == date("Y-m-d",strtotime($adjusted_date))){
						$end_time = date("H:i",strtotime($adjusted_date));
					}
					//echo "<br/>",$todayremainhours,' -- ',$end_time;
					if($todayremainhours=='00:00:00' && $end_time=='24:00')
					{
						$workingHours1 = '24';
						$workingHours2 = '00';
					} 
					else 
					{
						$time1 = new \DateTime($todayremainhours);
						$time2 = new \DateTime("$end_time:00");
						$interval = $time1->diff($time2);
						$workingHours1 = $interval->format('%H');
						$workingHours2 = $interval->format('%i');
					}

					$occupiedhours += $workingHours1;
					$lasthours = $workingHours1;
					if($workingHours2 == 30){
						$lasthours += 0.5;
						$occupiedhours = $occupiedhours + 0.5;
					}

					$days += 1;
				}
				$current_date = date("Y-m-d $start_time:00",strtotime($current_date." +1 days"));
			}

			if($occupiedhours > $workinghours){
				$days = floor($occupiedhours / $workinghours);
			} else {
				$days = 0;
			}

			$seconds = ($occupiedhours * 3600);
			$array = array();
			$array['totalhours'] =  $occupiedhours;
			$array['days'] = $days;
			$array['hours'] = $lasthours;
			$array['workingHours'] = $workinghours;
			$array['symbol'] = $symbol;
			
			date_default_timezone_set("UTC");
			echo json_encode($array);
		}
	}
}
