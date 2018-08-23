<?php
namespace app\controllers;
use Yii;
use app\models\PriorityTeam;
use app\models\PriorityTeamLoc;
use app\models\search\PriorityTeamSearch;
use app\models\search\PriorityTeamLocSearch;
use app\models\User;
use app\models\Team;
use app\models\TeamlocationMaster;
use app\models\TasksTeams;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

/**
 * PriorityTeamController implements the CRUD actions for PriorityTeam model.
 */
class PriorityTeamController extends Controller
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
    			'query' => \app\models\PriorityTeam::find()->where(['remove'=>0]),
    			'orderAttribute'=>'priority_order',
    		],
    	];
    }
    /**
     * IRT 169
     * changed function 
     * Lists all PriorityTeam models.
     * @return mixed
     */
    public function actionIndex()
    {
		$searchModel = new PriorityTeamLocSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
	
		/*IRT 67,68,86,87,258*/
		/*IRT 96,398 */
		$filter_type = \app\models\User::getFilterType(['tbl_priority_team_loc.team_id', 'tbl_priority_team_loc.team_loc_id'], ['tbl_priority_team_loc']);
		$config = [];       
		$config_widget_options = [];		
		$filterWidgetOption = \app\models\User::getFilterWidgetOption($filter_type, Url::toRoute(['priority-team/ajax-filter']), $config, $config_widget_options);
		/* IRT 67,68,86,87,258 */
		return $this->renderAjax('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'filter_type' => $filter_type,
			'filterWidgetOption' => $filterWidgetOption
        ]);
    }
    
    /**
     * IRT 169
	 * Filter GridView with Ajax
	 * */
	public function actionAjaxFilter()
	{
		$searchModel = new PriorityTeamLocSearch();
		$params = Yii::$app->request->queryParams;				
		$dataProvider = $searchModel->searchFilter($params);
		$out['results']=array();		
		foreach ($dataProvider as $key=>$val) {
			$val1 = $val;
			$val2 = $val;
			if($val == '') {
				$val1 = '(not set)';
				$val='(not set)';
				$val2='(not set)';
			}							
			$out['results'][] = ['id' => $val1, 'text' => $val,'label' => $val2];
		}
		return json_encode($out);
	}

    /**
     * Creates a new PriorityTeam model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * IRT 169 Changes 
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new PriorityTeamLoc();
	    if ($model->load(Yii::$app->request->post())) 
	    {
			$post_data=Yii::$app->request->post();
			if(!empty($post_data)){
				$team_location = $post_data['PriorityTeamLoc']['team_loc_id'];
				$team_teamloc = ''; $team_loc_id = array(); 
				foreach($team_location as $key => $val)
				{
					$team_teamloc = explode("-", $val);
					foreach($post_data['priority_id'] as $key => $value) 
					{
						$model = new PriorityTeamLoc();
						$sql = 'SELECT Max(priority_order) priority_order FROM tbl_priority_team_loc WHERE team_id = '.$team_teamloc[0].' AND team_loc_id = '.$team_teamloc[1].'';
						$priority_order = \Yii::$app->db->createCommand($sql)->queryAll();
						if($priority_order[0]['priority_order']!='') { 
							$model->priority_order = ++$priority_order[0]['priority_order']; 
						} else {
							$model->priority_order = $key;
						}
						$model->priority_team_id = $value;
						$model->team_id = $team_teamloc[0];
						$model->team_loc_id = $team_teamloc[1];
						$model->save();
					}
				}
			}
			return 'OK';
		} else {
			$pt_length = (new User)->getTableFieldLimit('tbl_priority_team_loc'); 
			$query = "SELECT CONCAT(tbl_team.id,' - ',tbl_teamlocation_master.id) id, CONCAT(tbl_team.team_name,' - ',tbl_teamlocation_master.team_location_name) full_name 
			FROM tbl_team 
			INNER JOIN tbl_team_locs ON tbl_team.id = tbl_team_locs.team_id 
			INNER JOIN tbl_teamlocation_master ON tbl_teamlocation_master.id = tbl_team_locs.team_loc
			WHERE CONCAT(tbl_team.id,' - ',tbl_teamlocation_master.id) NOT IN (SELECT CONCAT(team_id,' - ',team_loc_id) as  prior_team_loc FROM tbl_priority_team_loc)";
			$sql = \Yii::$app->db->createCommand($query)->queryAll();
			$team_location = ArrayHelper::map($sql, 'id', 'full_name');
			
			return $this->renderAjax('create', [
                'model' => $model,
                'pt_length' => $pt_length,
                'team_location' => $team_location
            ]);
        }
    }
    
    /**
     * IRT 169
     * Add Priority Team 
     * @return 
     */
     public function actionProjectPriorityTeam()
     {
		 $priority_id = Yii::$app->request->post('priority_id');
		 $data = Yii::$app->request->post();
		 if(isset($priority_id) && $priority_id != '') {
			$priority = PriorityTeam::findOne($priority_id);
			$priority->tasks_priority_name = $data['PriorityTeam']['tasks_priority_name'];
			$priority->priority_desc = $data['PriorityTeam']['priority_desc'];
			
			if($priority->save()) {
				return 'Ok';
			} else {
				return 'Fail';
			}
		 } else {
			$model = new PriorityTeam();
			if($model->load(Yii::$app->request->post()) && $model->save()){
				$lastId = Yii::$app->db->getLastInsertID();
				return 'Ok'; 
			} else
				return 'Fail';	
		 }
	 }

    /**
     * IRT 169 changes 
     * Updates an existing PriorityTeam model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($team_id, $team_loc_id)
    {
		$post_data = Yii::$app->request->post();
	    if(!empty($post_data)) 
	    {
			if(!empty($post_data['priority_id']))
			{
				$delete = "DELETE FROM tbl_priority_team_loc WHERE priority_team_id IN (".implode(",", $post_data['priority_id']).") AND team_id = ".$team_id." AND team_loc_id = ".$team_loc_id."";
				$delete_res = \Yii::$app->db->createCommand($delete)->execute();
				foreach($post_data['priority_id'] as $key => $value)
				{
					$model = new PriorityTeamLoc();
					$sql = 'SELECT Max(priority_order) priority_order FROM tbl_priority_team_loc WHERE team_id = '.$team_id.' AND team_loc_id = '.$team_loc_id.'';
					$priority_order = \Yii::$app->db->createCommand($sql)->queryAll();
					if($priority_order[0]['priority_order']!='') { 
						$model->priority_order = ++$priority_order[0]['priority_order'];
					} else {
						$model->priority_order = $key;
					}
					$model->priority_team_id = $value;
					$model->team_id = $team_id;
					$model->team_loc_id = $team_loc_id;
					$model->save();
				}
			}
		    return 'OK';
		} else {
			$pt_length = (new User)->getTableFieldLimit('tbl_priority_team_loc');
			$query = 'SELECT * FROM tbl_priority_team_loc 
				INNER JOIN tbl_priority_team ON tbl_priority_team_loc.priority_team_id = tbl_priority_team.id
				WHERE tbl_priority_team_loc.team_id = '.$team_id.' AND tbl_priority_team_loc.team_loc_id = '.$team_loc_id.' 
				ORDER BY tbl_priority_team_loc.priority_order ASC';
			$priority_details = \Yii::$app->db->createCommand($query)->queryAll();
			return $this->renderAjax('update', [
             	'pt_length'=>$pt_length,
				'priority_details' => $priority_details
		    ]);
        }
    }
    
    /**
     * Custodian Validate
     */
    public function actionPriorityvalidate(){
    	$model = new PriorityTeam();
    	if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
    		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    		return ActiveForm::validate($model);
    	}
    }

    /**
     * Deletes an existing PriorityTeam model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
	public function actionDelete($team_id, $team_loc) 
	{
		$avail = TasksTeams::find()->where(['team_id' => $team_id, 'team_loc' => $team_loc])->count();
		if(empty($avail)) {
			 $priority_loc_delete = "DELETE FROM tbl_priority_team_loc WHERE team_id = ".$team_id." AND team_loc_id = ".$team_loc; //." AND priority_team_id = ".$priority_id
			 $delete = Yii::$app->db->createCommand($priority_loc_delete)->execute();
			 echo "OK";
		} else {
			 echo "Used";
		}
	 	die();
    }
    
    /**
     * Delete Priority Team & Priority Team location
     * @return 
     * IRT 169 changes
     */
     public function actionDeleteTeamPriority()
     {
		$priority_id = Yii::$app->request->post('priority_id');
		$avail = TasksTeams::find()->where(['team_loc_prority' => $priority_id])->count();
		if(empty($avail)) 
		{ 
			//$priority_delete = "DELETE FROM tbl_priority_team WHERE id = ".$priority_id;
			$priority_delete = "UPDATE tbl_priority_team SET remove = '1' WHERE id = ".$priority_id;
			$delete = Yii::$app->db->createCommand($priority_delete)->execute();
			$priority_loc_delete = "DELETE FROM tbl_priority_team_loc WHERE priority_team_id = ".$priority_id;
			$delete = Yii::$app->db->createCommand($priority_loc_delete)->execute();
			echo "Ok";
		} else {
			echo "Used";
		}
		die();
	 }
    
    /**
     * Deletes an selected existing CaseCloseType model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDeleteselected() 
    {
		if (isset($_POST['keylist'])) {
			$finalkey = array(); $cnt = 0;  $i = 0;
			foreach($_POST['keylist'] as $keyval){
				$keys = json_decode($keyval);
				$sql = "SELECT count(*) cnt FROM tbl_tasks_teams WHERE team_id = ".$keys->team_id." AND team_loc=".$keys->team_loc;
				$available = \Yii::$app->db->createCommand($sql)->queryAll();
				$cnt = $cnt + $available[0]['cnt']; // count records
				if($cnt == 0){
					$finalkey[$i]['team_id'] = $keys->team_id;
					$finalkey[$i]['team_loc'] = $keys->team_loc;
					$i++;
				}
			}
			if($cnt == 0){
				foreach($finalkey as $tval){
					$delete = "DELETE FROM tbl_priority_team_loc WHERE team_id = ".$tval['team_id']." AND team_loc_id = ".$tval['team_loc'];
					$result = \Yii::$app->db->createCommand($delete)->execute();
				}
				echo "Ok";
			} else {
				echo "Used";
			}
		}
    	die();
    }
    
    /**
     * Get an all AssociatedTeamLocation.
     */
    public function actionAssociatedTeamLoc() 
    {
		$priority_id = Yii::$app->request->post();
		$model = new PriorityTeam();
		$sql = 'SELECT DISTINCT(tbl_priority_team.id) as id, tbl_priority_team.tasks_priority_name, tbl_priority_team.priority_desc FROM tbl_priority_team 
			LEFT JOIN tbl_priority_team_loc ON tbl_priority_team.id = tbl_priority_team_loc.priority_team_id
			WHERE tbl_priority_team.remove = 0'; 
		$myteams = \Yii::$app->db->createCommand($sql)->queryAll();
		return $this->renderAjax('priority_project_team', [
			'myteams' => $myteams,
			'model' => $model,
			'priority_id' => $priority_id,
			'last_id' => $last_id
		]);	
	}
	
	/**
	 * Team Edit Priority 
	 */
	 public function actionEditTeamPriority()
	 {
		 $priority_id = Yii::$app->request->post('priority_id');
		 $model = PriorityTeam::find()->where(['id' => $priority_id])->One();
		 return $this->renderAjax('_form_project_priority_team', [
			'model' => $model,
			'priority_id' => $priority_id
		 ]);
		 die();
	 }
}
