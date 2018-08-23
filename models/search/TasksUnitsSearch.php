<?php

namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\data\SqlDataProvider;
use yii\data\ArrayDataProvider;
use yii\helpers\ArrayHelper;
use app\models\Team;
use app\models\Options;
use app\models\Tasks;
use app\models\User;
use app\models\TasksUnits;
use app\models\TaskInstructServicetask;
use app\models\TaskInstruct;
use app\models\Settings;
use app\models\Servicetask;
use app\models\ClientCase;
use app\models\PriorityProject;
use app\models\PriorityTeam;
/**
 * TeamSearch represents the model behind the search form about `app\models\Team`.
 */
class TasksUnitsSearch extends TasksUnits
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['task_instruct_id',   'is_transition', 'created', 'created_by', 'modified', 'modified_by'], 'required'],
            [['task_instruct_id', 'task_instruct_servicetask_id','created_by', 'modified_by'], 'integer'],
            [['is_transition'], 'string'],
            [['id','duration', 'unit_complete_date', 'created', 'modified','task_id','unit_assigned_to','unit_status','workflow_task','task_priority','team_priority','client_id','client_case_id','project_name','task_duedate','todo_project_name','tasks_unit_id','todo_client_id', 'todo_client_case_id','todo','todo_cat','todo_assigned'], 'safe'],
            [['task_instruct_servicetask_id'], 'exist', 'skipOnError' => true, 'targetClass' => TaskInstructServicetask::className(), 'targetAttribute' => ['task_instruct_servicetask_id' => 'id']],
            [['task_instruct_id'], 'exist', 'skipOnError' => true, 'targetClass' => TaskInstruct::className(), 'targetAttribute' => ['task_instruct_id' => 'id']],

        ];
    }

     public function attributes()
    {
        // add related fields to searchable attributes
        return array_merge(parent::attributes(), ['unit_status','task_id','task_duedate', 'task_priority', 'team_priority','unit_assigned_to','workflow_task','client_case_id','client_id','project_name','task_date_time','task_closed','task_cancel','ispastdue']);
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
	}
	
	/**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function searchMyWorkingTodos($params)
    {
		
		
		$user_id=Yii::$app->user->identity->id;
		$timezoneOffset = (new Options)->getOffsetOfCurrenttimeZone();
		if (Yii::$app->db->driverName == 'mysql') {
			$data_query = "getDueDateTimeByUsersTimezone('{$timezoneOffset}',tbl_task_instruct.task_duedate,tbl_task_instruct.task_timedue,'%Y-%m-%d %H:%i:%s')";
		} else {
				$data_query = "[dbo].getDueDateTimeByUsersTimezone('{$timezoneOffset}',tbl_task_instruct.task_duedate,tbl_task_instruct.task_timedue,'%Y-%m-%d %H:%i:%s')";
		}
		$sqlteams = "SELECT tbl_project_security.team_id FROM tbl_project_security WHERE tbl_project_security.user_id=$user_id and tbl_project_security.team_id!=0 group by tbl_project_security.team_id";
		$sqlpastdue="SELECT COUNT(*) as totalitems
    	FROM tbl_task_instruct
    	INNER JOIN tbl_tasks ON tbl_tasks.id = task_id
		WHERE tbl_task_instruct.task_id = tbl_tasks_units.task_id
		AND isactive=1";
		$todaysdate = (new Options)->ConvertOneTzToAnotherTz(date('Y-m-d H:i:s'), 'UTC', $_SESSION['usrTZ'], "YMDHIS");
    	if (Yii::$app->db->driverName == 'mysql'){
			$task_completedate = " CONVERT_TZ(tbl_tasks.task_complete_date,'+00:00','".$timezoneOffset."') ";
			$sqlpastdue.=" AND $data_query < CASE WHEN tbl_tasks.task_complete_date!='0000-00-00 00:00:00' AND tbl_tasks.task_complete_date!='' AND tbl_tasks.task_status = 4 THEN '$task_duedatetime' ELSE '$todaysdate' END";
        }else{
			$task_completedate = " CAST(switchoffset(todatetimeoffset(tbl_tasks.task_complete_date, '+00:00'), '$timezoneOffset') as DATETIME) ";
        	$sqlpastdue.=" AND $data_query < CASE WHEN CAST(tbl_tasks.task_complete_date as varchar)!='0000-00-00 00:00:00' AND CAST(tbl_tasks.task_complete_date as varchar) IS NOT NULL AND tbl_tasks.task_status = 4 THEN '$task_duedatetime' ELSE '$todaysdate' END";
		}
		
		$pageSize=25;
		if(isset($params['export']) && $params['export']=='export') {
			$pageSize=-1;
		}
		$query = TasksUnits::find();
		$query->select([
			'tbl_tasks_units_todos.id',
			'tbl_tasks_units_todos.tasks_unit_id',
			'tbl_tasks_units.task_id',
			'tbl_tasks_units.team_id', 
			'tbl_tasks_units.team_loc', 
			'tbl_tasks_units_todos.todo',
			'tbl_tasks_units_todos.modified as todo_assigned',
			'tbl_todo_cats.todo_cat',
			'tbl_tasks_units.servicetask_id', 
			'tbl_tasks_units.unit_status', 
			'tbl_tasks_units.unit_assigned_to', 
			'tbl_tasks_units.task_instruct_id',
			'tbl_task_instruct.task_timedue',
			'tbl_task_instruct.task_duedate as task_duedate', 
			'tbl_task_instruct.task_priority',
			'tbl_task_instruct.project_name as todo_project_name',
			'tbl_client_case.client_id as todo_client_id', 
			'tbl_tasks.client_case_id as todo_client_case_id',
			'tbl_tasks.client_case_id',
			'tbl_client_case.case_name',
			'tbl_client.client_name',
			'tbl_tasks.task_closed as task_closed',
			'tbl_tasks.task_cancel as task_cancel',
			"$data_query as task_date_time",
			"($sqlpastdue) as ispastdue"
			]);
		$query->joinWith(['tasks'=>function (\yii\db\ActiveQuery $query){
			$query->joinWith(['clientCase'=>function (\yii\db\ActiveQuery $query){
				$query->joinWith(['client'],false);
			}],false);
		}],false);
		$query->joinWith(['taskInstruct'=>function (\yii\db\ActiveQuery $query){
			$query->joinWith(['taskPriority','taskPriority'],false);
		}],false);
		$query->joinWith(['servicetask','teamLoc','team'],false);
		$query->joinWith(['todos'=>function (\yii\db\ActiveQuery $query){
			$query->joinWith(['todoCats'],false);
		}],false);
		$query->where([
			//'tbl_tasks_units.unit_assigned_to'=>$user_id,
			'tbl_tasks_units_todos.assigned'=>$user_id,
			'isactive'=>1,
			'tbl_tasks.task_closed'=>0,
			'tbl_tasks.task_cancel'=>0,
			'tbl_client_case.is_close'=>0
			]);
		$query->andWhere('tbl_tasks_units_todos.complete !=1 AND tbl_tasks_units.unit_status!=4');
		$query->andWhere('(tbl_tasks.client_case_id IN (SELECT tbl_project_security.client_case_id FROM tbl_project_security WHERE tbl_project_security.user_id= '.$user_id.' and tbl_project_security.client_case_id!=0 group by tbl_project_security.client_case_id)  AND tbl_tasks_units.team_id IN (1)) OR tbl_tasks_units.servicetask_id IN (SELECT t.id FROM tbl_servicetask t INNER JOIN tbl_teamservice ON t.teamservice_id = tbl_teamservice.id WHERE tbl_teamservice.teamid IN ('.$sqlteams.') AND tbl_teamservice.teamid NOT IN (1))');
		$query->andWhere("tbl_tasks_units.unit_assigned_to IS NOT NULL OR tbl_tasks_units.unit_assigned_to != '' OR tbl_tasks_units.unit_assigned_to != '[]'");

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'pagination' => [
				'pageSize' => $pageSize,
			],
			'sort' => [
					'attributes' => ['tasks_unit_id','todo_project_name','todo_client_id','todo_client_case_id','todo','todo_cat','todo_assigned',
					'tasks_unit_id'=> [
						'asc' => ['tbl_tasks_units.id' => SORT_ASC],
						'desc' => ['tbl_tasks_units.id' => SORT_DESC]
					],
					'todo_project_name' => [
						'asc' => ['tbl_task_instruct.project_name' => SORT_ASC],
						'desc' => ['tbl_task_instruct.project_name' => SORT_DESC]
					],
					'todo_client_id' => [
							'asc' => ['tbl_client.client_name' => SORT_ASC],
							'desc' => ['tbl_client.client_name' => SORT_DESC]
						],
					'todo_client_case_id' => [
							'asc' => ['tbl_client_case.case_name' => SORT_ASC],
							'desc' => ['tbl_client_case.case_name' => SORT_DESC]
						],
						'todo' => [
							'asc' => ['todo' => SORT_ASC],
							'desc' => ['todo' => SORT_DESC]
						],
						'todo_cat' => [
							'asc' => ['todo_cat' => SORT_ASC],
							'desc' => ['todo_cat' => SORT_DESC],
						],
						'todo_assigned' => [
							'asc' => ['todo_assigned' => SORT_ASC],
							'desc' => ['todo_assigned' => SORT_DESC],
						]
						
					]
				]
        ]);
        $dataProvider->sort->enableMultiSort=true;
        /*IRT-67*/
		if(isset($params['grid_id']) && $params['grid_id']!=""){
			$grid_id=$params['grid_id'].'_'.Yii::$app->user->identity->id;
			$sql="SELECT * FROM tbl_dynagrid_dtl WHERE id IN (SELECT sort_id FROM tbl_dynagrid WHERE id='$grid_id')";
			$sort_data=Yii::$app->db->createCommand($sql)->queryOne();
			if(!empty($sort_data)){
				$dataProvider->sort->defaultOrder=json_decode($sort_data['data'],true);
			}
		}
		/*multiselect*/
        if ($params['TasksUnitsSearch']['todo'] != null && is_array($params['TasksUnitsSearch']['todo'])) {
			if(!empty($params['TasksUnitsSearch']['todo'])){
				foreach($params['TasksUnitsSearch']['todo'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($params['TasksUnitsSearch']['todo']);
					}
				}
			}
		}
		if ($params['TasksUnitsSearch']['todo_client_case_id'] != null && is_array($params['TasksUnitsSearch']['todo_client_case_id'])) {
			if(!empty($params['TasksUnitsSearch']['todo_client_case_id'])){
				foreach($params['TasksUnitsSearch']['todo_client_case_id'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($params['TasksUnitsSearch']['todo_client_case_id']);
					}
				}
			}
		}
		if ($params['TasksUnitsSearch']['todo_client_id'] != null && is_array($params['TasksUnitsSearch']['todo_client_id'])) {
			if(!empty($params['TasksUnitsSearch']['todo_client_id'])){
				foreach($params['TasksUnitsSearch']['todo_client_id'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($params['TasksUnitsSearch']['todo_client_id']);
					}
				}
			}
		}
		if ($params['TasksUnitsSearch']['todo_cat'] != null && is_array($params['TasksUnitsSearch']['todo_cat'])) {
			if(!empty($params['TasksUnitsSearch']['todo_cat'])){
				foreach($params['TasksUnitsSearch']['todo_cat'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($params['TasksUnitsSearch']['todo_cat']);
					}
				}
			}
		}
		/*multiselect*/

		if(isset($params['TasksUnitsSearch']['tasks_unit_id']) && is_numeric(trim($params['TasksUnitsSearch']['tasks_unit_id'])) ) {
			$query->andFilterWhere(['tbl_tasks_units_todos.tasks_unit_id' => $params['TasksUnitsSearch']['tasks_unit_id']]);
		}
		$query->andFilterWhere(['like', 'project_name', $params['TasksUnitsSearch']['todo_project_name']]);
		//$query->andFilterWhere([' or like', 'todo', $params['TasksUnitsSearch']['todo'],false]);
		$query->andFilterWhere(['or like','todo',$params['TasksUnitsSearch']['todo'],false]);
		$query->andFilterWhere(['or like','todo_cat',$params['TasksUnitsSearch']['todo_cat'],false]);
		if(isset($params['TasksUnitsSearch']['todo_client_case_id']) && $params['TasksUnitsSearch']['todo_client_case_id']!="" && $params['TasksUnitsSearch']['todo_client_case_id']!="All"){
    		$query->andWhere('tbl_client_case.id IN ('.implode(',',$params['TasksUnitsSearch']['todo_client_case_id']).')');
    	}
		if(isset($params['TasksUnitsSearch']['todo_client_id']) && $params['TasksUnitsSearch']['todo_client_id']!="" && $params['TasksUnitsSearch']['todo_client_id']!="All"){
    		$query->andWhere('tbl_client_case.client_id IN ('.implode(',',$params['TasksUnitsSearch']['todo_client_id']).')');
    	}
		if(isset($params['TasksUnitsSearch']['todo_assigned']) && $params['TasksUnitsSearch']['todo_assigned']!=""){
			$task_duedate = explode("-", $params['TasksUnitsSearch']['todo_assigned']);
            $task_duedate_start = explode("/", trim($task_duedate[0]));
            $task_duedate_end = explode("/", trim($task_duedate[1]));
            $task_duedate_s = $task_duedate_start[2] . "-" . $task_duedate_start[0] . "-" . $task_duedate_start[1].' 00:00:00';
            $task_duedate_e = $task_duedate_end[2] . "-" . $task_duedate_end[0] . "-" . $task_duedate_end[1].' 23:59:59';
          	$query->andWhere("tbl_tasks_units_todos.modified >= '$task_duedate_s' AND tbl_tasks_units_todos.modified  <= '$task_duedate_e' ");
        }

		
        $this->load($params);
		

		return $dataProvider;
	}

	public function searchMyWorkingTodosFilter($params)
    {
		
		
		$user_id=Yii::$app->user->identity->id;
		$timezoneOffset = (new Options)->getOffsetOfCurrenttimeZone();
		if (Yii::$app->db->driverName == 'mysql') {
			$data_query = "getDueDateTimeByUsersTimezone('{$timezoneOffset}',tbl_task_instruct.task_duedate,tbl_task_instruct.task_timedue,'%Y-%m-%d %H:%i:%s')";
		} else {
				$data_query = "[dbo].getDueDateTimeByUsersTimezone('{$timezoneOffset}',tbl_task_instruct.task_duedate,tbl_task_instruct.task_timedue,'%Y-%m-%d %H:%i:%s')";
		}
		$sqlteams = "SELECT tbl_project_security.team_id FROM tbl_project_security WHERE tbl_project_security.user_id=$user_id and tbl_project_security.team_id!=0 group by tbl_project_security.team_id";
		$sqlpastdue="SELECT COUNT(*) as totalitems
    	FROM tbl_task_instruct
    	INNER JOIN tbl_tasks ON tbl_tasks.id = task_id
		WHERE tbl_task_instruct.task_id = tbl_tasks_units.task_id
		AND isactive=1";
		$todaysdate = (new Options)->ConvertOneTzToAnotherTz(date('Y-m-d H:i:s'), 'UTC', $_SESSION['usrTZ'], "YMDHIS");
    	if (Yii::$app->db->driverName == 'mysql'){
			$task_completedate = " CONVERT_TZ(tbl_tasks.task_complete_date,'+00:00','".$timezoneOffset."') ";
			$sqlpastdue.=" AND $data_query < CASE WHEN tbl_tasks.task_complete_date!='0000-00-00 00:00:00' AND tbl_tasks.task_complete_date!='' AND tbl_tasks.task_status = 4 THEN '$task_duedatetime' ELSE '$todaysdate' END";
        }else{
			$task_completedate = " CAST(switchoffset(todatetimeoffset(tbl_tasks.task_complete_date, '+00:00'), '$timezoneOffset') as DATETIME) ";
        	$sqlpastdue.=" AND $data_query < CASE WHEN CAST(tbl_tasks.task_complete_date as varchar)!='0000-00-00 00:00:00' AND CAST(tbl_tasks.task_complete_date as varchar) IS NOT NULL AND tbl_tasks.task_status = 4 THEN '$task_duedatetime' ELSE '$todaysdate' END";
		}
		
		$pageSize=25;
		if(isset($params['export']) && $params['export']=='export') {
			$pageSize=-1;
		}
		$query = TasksUnits::find();
		$query->select([
			'tbl_tasks_units.id',
			'tbl_tasks_units.task_id',
			'tbl_tasks_units.team_id', 
			'tbl_tasks_units.team_loc', 
			'tbl_tasks_units_todos.todo',
			'tbl_tasks_units_todos.modified as todo_assigned',
			'tbl_todo_cats.todo_cat',
			'tbl_tasks_units.servicetask_id', 
			'tbl_tasks_units.unit_status', 
			'tbl_tasks_units.unit_assigned_to', 
			'tbl_tasks_units.task_instruct_id',
			'tbl_task_instruct.task_timedue',
			'tbl_task_instruct.task_duedate as task_duedate', 
			'tbl_task_instruct.task_priority',
			'tbl_task_instruct.project_name as todo_project_name',
			'tbl_client_case.client_id as todo_client_id', 
			'tbl_tasks.client_case_id as todo_client_case_id',
			'tbl_tasks.client_case_id',
			'tbl_client_case.case_name',
			'tbl_client.client_name',
			'tbl_tasks.task_closed as task_closed',
			'tbl_tasks.task_cancel as task_cancel',
			"$data_query as task_date_time",
			"($sqlpastdue) as ispastdue"
			]);
		$query->joinWith(['tasks'=>function (\yii\db\ActiveQuery $query){
			$query->joinWith(['clientCase'=>function (\yii\db\ActiveQuery $query){
				$query->joinWith(['client'],false);
			}],false);
		}],false);
		$query->joinWith(['taskInstruct'=>function (\yii\db\ActiveQuery $query){
			$query->joinWith(['taskPriority','taskPriority'],false);
		}],false);
		$query->joinWith(['servicetask','teamLoc','team'],false);
		$query->joinWith(['todos'=>function (\yii\db\ActiveQuery $query){
			$query->joinWith(['todoCats'],false);
		}],false);
		$query->where([
		//	'tbl_tasks_units.unit_assigned_to'=>$user_id,
			'tbl_tasks_units_todos.assigned'=>$user_id,
			'isactive'=>1,
			'tbl_tasks.task_closed'=>0,
			'tbl_tasks.task_cancel'=>0,
			'tbl_client_case.is_close'=>0
			]);
		$query->andWhere('tbl_tasks_units_todos.complete !=1 AND tbl_tasks_units.unit_status!=4');
		$query->andWhere('(tbl_tasks.client_case_id IN (SELECT tbl_project_security.client_case_id FROM tbl_project_security WHERE tbl_project_security.user_id= '.$user_id.' and tbl_project_security.client_case_id!=0 group by tbl_project_security.client_case_id)  AND tbl_tasks_units.team_id IN (1)) OR tbl_tasks_units.servicetask_id IN (SELECT t.id FROM tbl_servicetask t INNER JOIN tbl_teamservice ON t.teamservice_id = tbl_teamservice.id WHERE tbl_teamservice.teamid IN ('.$sqlteams.') AND tbl_teamservice.teamid NOT IN (1))');
		$query->andWhere("tbl_tasks_units.unit_assigned_to IS NOT NULL OR tbl_tasks_units.unit_assigned_to != '' OR tbl_tasks_units.unit_assigned_to != '[]'");

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'pagination' => [
				'pageSize' => $pageSize,
			],
			'sort' => [
					'attributes' => ['tasks_unit_id','todo_project_name','todo_client_id','todo_client_case_id','todo','todo_cat','todo_assigned',
					'tasks_unit_id'=> [
						'asc' => ['tbl_tasks_units.id' => SORT_ASC],
						'desc' => ['tbl_tasks_units.id' => SORT_DESC]
					],
					'todo_project_name' => [
						'asc' => ['tbl_task_instruct.project_name' => SORT_ASC],
						'desc' => ['tbl_task_instruct.project_name' => SORT_DESC]
					],
					'todo_client_id' => [
							'asc' => ['tbl_client.client_name' => SORT_ASC],
							'desc' => ['tbl_client.client_name' => SORT_DESC]
						],
					'todo_client_case_id' => [
							'asc' => ['tbl_client_case.case_name' => SORT_ASC],
							'desc' => ['tbl_client_case.case_name' => SORT_DESC]
						],
						'todo' => [
							'asc' => ['todo' => SORT_ASC],
							'desc' => ['todo' => SORT_DESC]
						],
						'todo_cat' => [
							'asc' => ['todo_cat' => SORT_ASC],
							'desc' => ['todo_cat' => SORT_DESC],
						],
						'todo_assigned' => [
							'asc' => ['todo_assigned' => SORT_ASC],
							'desc' => ['todo_assigned' => SORT_DESC],
						]
						
					]
				]
        ]);
        $dataProvider->sort->enableMultiSort=true;
        /*IRT-67*/
		if(isset($params['grid_id']) && $params['grid_id']!=""){
			$grid_id=$params['grid_id'].'_'.Yii::$app->user->identity->id;
			$sql="SELECT * FROM tbl_dynagrid_dtl WHERE id IN (SELECT sort_id FROM tbl_dynagrid WHERE id='$grid_id')";
			$sort_data=Yii::$app->db->createCommand($sql)->queryOne();
			if(!empty($sort_data)){
				$dataProvider->sort->defaultOrder=json_decode($sort_data['data'],true);
			}
		}
		//echo "<pre>",print_r($params),"</pre>";
		if(isset($params['TasksUnitsSearch']['tasks_unit_id']) && is_numeric(trim($params['TasksUnitsSearch']['tasks_unit_id'])) ) {
			$query->andFilterWhere(['tbl_tasks_units.id' => $params['TasksUnitsSearch']['tasks_unit_id']]);
		}
		$query->andFilterWhere(['like', 'project_name', $params['TasksUnitsSearch']['todo_project_name']]);


		
        $this->load($params);
		

		if($params['field']=='client_case_id'){
			if(isset($params['q']) && $params['q']!=""){
				$query->andWhere("tbl_client_case.case_name like '%".$params['q']."%'");
			}
			$dataProvider = ArrayHelper::map($query->all(),
			function($model){
				return $model->tasks->client_case_id;
			},
			function($model){
				return $model->tasks->clientCase->case_name;
			});
		}
		if($params['field']=='client_id'){
			if(isset($params['q']) && $params['q']!=""){
				$query->andWhere("tbl_client.client_name like '%".$params['q']."%'");
			}
			$dataProvider = ArrayHelper::map($query->all(),
			function($model){
				return $model->tasks->clientCase->client_id;
			},
			function($model){
				return $model->tasks->clientCase->client->client_name;
			});
		}
		if($params['field']=='todo'){
			if(isset($params['q']) && $params['q']!=""){
				$query->andWhere("tbl_tasks_units_todos.todo like '%".$params['q']."%'");
			}
			$dataProvider = ArrayHelper::map($query->all(),
			function($model){
				return $model->todos->todo;
			},
			function($model){
				return $model->todos->todo;
			});
		}
		if($params['field']=='todo_cat'){
			if(isset($params['q']) && $params['q']!=""){
				$query->andWhere("todo_cat like '%".$params['q']."%'");
			}
			$dataProvider = ArrayHelper::map($query->all(),
			function($model){
				return $model->todos->todoCats->todo_cat;
			},
			function($model){
				return $model->todos->todoCats->todo_cat;
			});
		}

		return array('All'=>'All') + $dataProvider;//$dataProvider;
	}
/**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function searchMyActiveTasks($params)
    {
		$user_id=Yii::$app->user->identity->id;
		$timezoneOffset = (new Options)->getOffsetOfCurrenttimeZone();
		if (Yii::$app->db->driverName == 'mysql') {
			$data_query = "getDueDateTimeByUsersTimezone('{$timezoneOffset}',tbl_task_instruct.task_duedate,tbl_task_instruct.task_timedue,'%Y-%m-%d %H:%i:%s')";
		} else {
				$data_query = "[dbo].getDueDateTimeByUsersTimezone('{$timezoneOffset}',tbl_task_instruct.task_duedate,tbl_task_instruct.task_timedue,'%Y-%m-%d %H:%i:%s')";
		}
		$sqlpastdue="SELECT COUNT(*) as totalitems
    	FROM tbl_task_instruct
    	INNER JOIN tbl_tasks ON tbl_tasks.id = task_id
		WHERE tbl_task_instruct.task_id = tbl_tasks_units.task_id
		AND isactive=1";
		$todaysdate = (new Options)->ConvertOneTzToAnotherTz(date('Y-m-d H:i:s'), 'UTC', $_SESSION['usrTZ'], "YMDHIS");
    	if (Yii::$app->db->driverName == 'mysql'){
			$task_completedate = " CONVERT_TZ(tbl_tasks.task_complete_date,'+00:00','".$timezoneOffset."') ";
			$sqlpastdue.=" AND $data_query < CASE WHEN tbl_tasks.task_complete_date!='0000-00-00 00:00:00' AND tbl_tasks.task_complete_date!='' AND tbl_tasks.task_status = 4 THEN '$task_duedatetime' ELSE '$todaysdate' END";
        }else{
			$task_completedate = " CAST(switchoffset(todatetimeoffset(tbl_tasks.task_complete_date, '+00:00'), '$timezoneOffset') as DATETIME) ";
        	$sqlpastdue.=" AND $data_query < CASE WHEN CAST(tbl_tasks.task_complete_date as varchar)!='0000-00-00 00:00:00' AND CAST(tbl_tasks.task_complete_date as varchar) IS NOT NULL AND tbl_tasks.task_status = 4 THEN '$task_duedatetime' ELSE '$todaysdate' END";
		}
		
		$pageSize=25;
		if(isset($params['export']) && $params['export']=='export') {
			$pageSize=-1;
		}
		$query = TasksUnits::find();
		$query->select(['tbl_tasks_units.task_id', 'tbl_tasks_units.team_id', 
		'tbl_tasks_units.team_loc', 
		'tbl_tasks_units.id', 'tbl_tasks_units.servicetask_id', 'tbl_tasks_units.unit_status', 
		'tbl_tasks_units.unit_assigned_to', 'tbl_tasks_units.task_instruct_id',
		'tbl_task_instruct.task_timedue','tbl_task_instruct.task_duedate as task_duedate', 
		'tbl_task_instruct.task_priority','tbl_task_instruct.project_name as project_name',
		'tbl_task_instruct.task_priority','tbl_tasks.client_case_id','tbl_client_case.case_name','tbl_client.client_name',
		'tbl_tasks.task_closed as task_closed',
		'tbl_tasks.task_cancel as task_cancel',
		"$data_query as task_date_time",
		"($sqlpastdue) as ispastdue"
		]);
		$query->joinWith(['tasks'=>function (\yii\db\ActiveQuery $query){
			$query->joinWith(['clientCase'=>function (\yii\db\ActiveQuery $query){
				$query->joinWith(['client'],false);
			}],false);
		}],false);
		$query->joinWith(['taskInstruct'=>function (\yii\db\ActiveQuery $query){
			$query->joinWith(['taskPriority','taskPriority'],false);
		}],false);
		$query->joinWith(['servicetask']);
		
		$query->where([
			'tbl_tasks_units.unit_assigned_to' => $user_id,
			'isactive'=>1,
			'tbl_tasks.task_closed' => 0,
			'tbl_tasks.task_cancel' => 0,
			'tbl_client_case.is_close' => 0
			]);
		$query->andWhere('tbl_tasks_units.unit_status!=4');

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'pagination' => [
				'pageSize' => $pageSize,
			],
			'sort' => [
					'attributes' => ['unit_status','id','client_id','task_duedate','client_case_id','project_name',
					'id'=> [
						'asc' => ['tbl_tasks_units.id' => SORT_ASC],
						'desc' => ['tbl_tasks_units.id' => SORT_DESC]
					],
					'project_name' => [
						'asc' => ['tbl_task_instruct.project_name' => SORT_ASC],
						'desc' => ['tbl_task_instruct.project_name' => SORT_DESC]
					],
					'client_id' => [
							'asc' => ['tbl_client.client_name' => SORT_ASC],
							'desc' => ['tbl_client.client_name' => SORT_DESC]
						],
					'client_case_id' => [
							'asc' => ['tbl_client_case.case_name' => SORT_ASC],
							'desc' => ['tbl_client_case.case_name' => SORT_DESC]
						],
						'task_priority' => [
							'asc' => ['priority' => SORT_ASC],
							'desc' => ['priority' => SORT_DESC]
						],
						'workflow_task' => [
							'asc' => ['tbl_servicetask.service_task' => SORT_ASC],
							'desc' => ['tbl_servicetask.service_task' => SORT_DESC],
						],
						'task_duedate' => [
							'asc' => ['task_date_time' => SORT_ASC],
							'desc' => ['task_date_time' => SORT_DESC],
						],
						'unit_assigned_to' => [
							'asc' => ['assign_user.usr_first_name' => SORT_ASC],
							'desc' => ['assign_user.usr_first_name' => SORT_DESC],
						],
					]
				]
        ]);
        $dataProvider->sort->enableMultiSort=true;
        /*IRT-67*/
		if(isset($params['grid_id']) && $params['grid_id']!=""){
			$grid_id=$params['grid_id'].'_'.Yii::$app->user->identity->id;
			$sql="SELECT * FROM tbl_dynagrid_dtl WHERE id IN (SELECT sort_id FROM tbl_dynagrid WHERE id='$grid_id')";
			$sort_data=Yii::$app->db->createCommand($sql)->queryOne();
			if(!empty($sort_data)){
				$dataProvider->sort->defaultOrder=json_decode($sort_data['data'],true);
			}
		}
		/*IRT-67*/
		/*multiselect*/
        if ($params['TasksUnitsSearch']['unit_status'] != null && is_array($params['TasksUnitsSearch']['unit_status'])) {
			if(!empty($params['TasksUnitsSearch']['unit_status'])){
				foreach($params['TasksUnitsSearch']['unit_status'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($params['TasksUnitsSearch']['unit_status']);
					}
				}
			}
		}
		if ($params['TasksUnitsSearch']['client_case_id'] != null && is_array($params['TasksUnitsSearch']['client_case_id'])) {
			if(!empty($params['TasksUnitsSearch']['client_case_id'])){
				foreach($params['TasksUnitsSearch']['client_case_id'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($params['TasksUnitsSearch']['client_case_id']);
					}
				}
			}
		}
		if ($params['TasksUnitsSearch']['task_priority'] != null && is_array($params['TasksUnitsSearch']['task_priority'])) {
			if(!empty($params['TasksUnitsSearch']['task_priority'])){
				foreach($params['TasksUnitsSearch']['task_priority'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($params['TasksUnitsSearch']['task_priority']);
					}
				}
			}
		}
		if ($params['TasksUnitsSearch']['project_name'] != null && is_array($params['TasksUnitsSearch']['project_name'])) {
			if(!empty($params['TasksUnitsSearch']['project_name'])){
				foreach($params['TasksUnitsSearch']['project_name'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($params['TasksUnitsSearch']['project_name']);
					}
				}
			}
		}
		if ($params['TasksUnitsSearch']['client_id'] != null && is_array($params['TasksUnitsSearch']['client_id'])) {
			if(!empty($params['TasksUnitsSearch']['client_id'])){
				foreach($params['TasksUnitsSearch']['client_id'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($params['TasksUnitsSearch']['client_id']);
					}
				}
			}
		}
		if ($params['TasksUnitsSearch']['workflow_task'] != null && is_array($params['TasksUnitsSearch']['workflow_task'])) {
			if(!empty($params['TasksUnitsSearch']['workflow_task'])){
				foreach($params['TasksUnitsSearch']['workflow_task'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($params['TasksUnitsSearch']['workflow_task']);
					}
				}
			}
		}
		$status = array();
		
		/*multiselect*/
		$query->andFilterWhere(['tbl_tasks_units.id' => $params['TasksUnitsSearch']['id']]);

    	if(isset($params['TasksUnitsSearch']['unit_status']) && $params['TasksUnitsSearch']['unit_status']!="" && $params['TasksUnitsSearch']['unit_status']!="All"){
			$status=$params['TasksUnitsSearch']['unit_status'];
			$statusAr = $status;
			if(in_array(0,$status)){
				$status_pen = array_keys($status, 0);
				$status_index = array_pop($status_pen);
				
				if($status_index!==false) {
					unset($statusAr[$status_index]);
				}
				/*NOT STARTED*/
				$userId = Yii::$app->user->identity->id;
				$service_ids = array();
				if(Yii::$app->db->driverName=="mysql") {
					$subquery="Select unit_status From tbl_tasks_units as tu
			INNER JOIN tbl_task_instruct ON tbl_task_instruct.id = tu.task_instruct_id
			Where tu.sort_order = A.sort_order - 1 And tbl_task_instruct.task_id = A.task_id ORDER BY tu.id DESC LIMIT 1";
				} else {
					$subquery="Select TOP 1 unit_status From tbl_tasks_units as tu
			INNER JOIN tbl_task_instruct ON tbl_task_instruct.id = tu.task_instruct_id
			Where tu.sort_order = A.sort_order - 1 And tbl_task_instruct.task_id = A.task_id ORDER BY tu.id DESC";
				}
				$sql = "Select B.task_unit_id From (
			Select A.* From (
				SELECT t.id as task_unit_id,Taskindividual.task_id as task_id, t.servicetask_id, t.sort_order,t.team_loc,t.team_id,clientCase.case_name,clients.client_name,project.priority,Taskindividual.task_duedate,t.unit_status,Taskindividual.task_timedue,clientCase.id as client_case_id FROM tbl_tasks_units as t
				LEFT JOIN tbl_task_instruct Taskindividual ON (Taskindividual.id=t.task_instruct_id)
				INNER JOIN tbl_tasks task ON (Taskindividual.task_id=task.id)
				LEFT JOIN tbl_client_case clientCase ON (task.client_case_id=clientCase.id)
				LEFT JOIN tbl_client as clients ON clientCase.client_id = clients.id
				LEFT JOIN tbl_priority_project as project ON project.id = Taskindividual.task_priority
				WHERE  t.unit_status=0 AND (t.sort_order not in(0,1)) AND task.task_closed=0 AND task.task_cancel=0 AND Taskindividual.isactive=1 AND clientCase.is_close=0 AND task.task_closed=0 AND t.unit_assigned_to = ".$userId.") as A
			Where ($subquery) = 4
			Union All
			SELECT t.id as task_unit_id,Taskindividual.task_id as task_id, t.servicetask_id, t.sort_order,t.team_loc,t.team_id,clientCase.case_name,clients.client_name,project.priority,Taskindividual.task_duedate,t.unit_status,Taskindividual.task_timedue,clientCase.id as client_case_id  FROM tbl_tasks_units as t
			LEFT JOIN tbl_task_instruct Taskindividual ON (Taskindividual.id=t.task_instruct_id)
			INNER JOIN tbl_tasks task ON (Taskindividual.task_id=task.id)
			LEFT JOIN tbl_client_case clientCase ON (task.client_case_id=clientCase.id)
			LEFT JOIN tbl_client as clients ON clientCase.client_id = clients.id
			LEFT JOIN tbl_priority_project as project ON project.id = Taskindividual.task_priority
			WHERE t.unit_status=0 AND task.task_closed=0 AND task.task_cancel=0 AND Taskindividual.isactive=1 AND clientCase.is_close=0 AND task.task_closed=0 AND t.sort_order=1 AND t.unit_assigned_to = ".$userId.") AS B GROUP BY B.task_unit_id";
		$timezoneOffset = (new Options)->getOffsetOfCurrenttimeZone();
		if (Yii::$app->db->driverName == 'mysql') {
			$data_query = "getDueDateTimeByUsersTimezone('{$timezoneOffset}',tbl_task_instruct.task_duedate,tbl_task_instruct.task_timedue,'%m/%d/%Y %h:%i %p')";
		} else {
			//$data_query = "[dbo].getDueDateTimeByUsersTimezone('{$timezoneOffset}',tbl_task_instruct.task_duedate,tbl_task_instruct.task_timedue,'%m/%d/%Y %h:%i %p')";
			$data_query = "(SELECT duedatetime FROM [dbo].getDueDateTimeByUsersTimezoneTV('{$timezoneOffset}',tbl_task_instruct.task_duedate,tbl_task_instruct.task_timedue,'%m/%d/%Y %h:%i %p'))";
		}
		$taskduedatejoin = " LEFT JOIN (SELECT tbl_task_instruct.id, $data_query as task_date_time FROM tbl_task_instruct WHERE isactive=1 AND task_id IS NOT NULL AND task_id<>'') as C ON Taskindividual.id = C.id ";
			$final_data = "SELECT t.id  FROM tbl_tasks_units as t
			LEFT JOIN tbl_task_instruct Taskindividual ON (Taskindividual.id=t.task_instruct_id)
			$taskduedatejoin
			INNER JOIN tbl_tasks task ON (Taskindividual.task_id=task.id)
			LEFT JOIN tbl_client_case clientCase ON (task.client_case_id=clientCase.id)
			LEFT JOIN tbl_client as clients ON clientCase.client_id = clients.id
			LEFT JOIN tbl_priority_project as project ON project.id = Taskindividual.task_priority
			LEFT JOIN tbl_teamlocation_master as location ON location.id = t.team_loc
			LEFT JOIN tbl_team as team ON team.id = t.team_id
			INNER JOIN tbl_servicetask as taskservice ON taskservice.id = t.servicetask_id
			WHERE t.unit_status=0 AND task.task_closed=0 AND task.task_cancel=0 AND Taskindividual.isactive=1 AND clientCase.is_close=0 AND task.task_closed=0 AND t.unit_assigned_to = ".$userId." AND t.id NOT IN (".$sql.")";
			$query->andWhere('tbl_tasks_units.id IN ('.$final_data.')');
			/*NOT STARTED*/
			}
			if(in_array(7,$status)){
				$status_pen = array_keys($status, 7);
				$status_index = array_pop($status_pen);
				
				if($status_index!==false) {
					unset($statusAr[$status_index]);
				}				
				/*Pending Start*/
				$userId = Yii::$app->user->identity->id;
		$service_ids = array();
		$timezoneOffset = (new Options)->getOffsetOfCurrenttimeZone();
		if (Yii::$app->db->driverName == 'mysql') {
			$data_query = "getDueDateTimeByUsersTimezone('{$timezoneOffset}',tbl_task_instruct.task_duedate,tbl_task_instruct.task_timedue,'%m/%d/%Y %h:%i %p')";
		} else {
			//$data_query = "[dbo].getDueDateTimeByUsersTimezone('{$timezoneOffset}',tbl_task_instruct.task_duedate,tbl_task_instruct.task_timedue,'%m/%d/%Y %h:%i %p')";
			$data_query = "(SELECT duedatetime FROM [dbo].getDueDateTimeByUsersTimezoneTV('{$timezoneOffset}',tbl_task_instruct.task_duedate,tbl_task_instruct.task_timedue,'%m/%d/%Y %h:%i %p'))";
		}
		$taskduedatejoin = " INNER JOIN (SELECT tbl_task_instruct.id, $data_query as task_date_time FROM tbl_task_instruct WHERE isactive=1 AND task_id IS NOT NULL AND task_id<>'') as C ON Taskindividual.id = C.id ";
		if (Yii::$app->db->driverName == 'mysql') {
			$subquery="Select unit_status From tbl_tasks_units as tu
		INNER JOIN tbl_task_instruct ON tbl_task_instruct.id = tu.task_instruct_id
		Where tu.sort_order = A.sort_order - 1 And tbl_task_instruct.task_id = A.task_id ORDER BY tu.id DESC LIMIT 1";
		}else{
			$subquery="Select TOP 1 unit_status From tbl_tasks_units as tu
		INNER JOIN tbl_task_instruct ON tbl_task_instruct.id = tu.task_instruct_id
		Where tu.sort_order = A.sort_order - 1 And tbl_task_instruct.task_id = A.task_id ORDER BY tu.id DESC";
		}
		$sql = "Select B.task_unit_id From (
		Select A.* From (
			SELECT t.id as task_unit_id,Taskindividual.task_id as task_id, t.servicetask_id, t.sort_order,t.team_loc,t.team_id,clientCase.case_name,clients.client_name,project.priority,Taskindividual.task_duedate,t.unit_status,Taskindividual.task_timedue,clientCase.id as client_case_id, C.task_date_time as task_date_time FROM tbl_tasks_units as t
			LEFT JOIN tbl_task_instruct Taskindividual ON (Taskindividual.id=t.task_instruct_id)
			$taskduedatejoin
			INNER JOIN tbl_tasks task ON (Taskindividual.task_id=task.id)
			LEFT JOIN tbl_client_case clientCase ON (task.client_case_id=clientCase.id)
			LEFT JOIN tbl_client as clients ON clientCase.client_id = clients.id
			LEFT JOIN tbl_priority_project as project ON project.id = Taskindividual.task_priority
			WHERE  t.unit_status=0 AND (t.sort_order not in(0,1)) AND task.task_closed=0 AND task.task_cancel=0 AND Taskindividual.isactive=1 AND clientCase.is_close=0 AND task.task_closed=0 AND t.unit_assigned_to = ".$userId." AND ((t.team_id = 1 AND t.team_loc =0) OR (t.team_id != 1 AND t.team_loc !=0))) as A
		Where ( $subquery ) = 4
		Union All
		SELECT t.id as task_unit_id,Taskindividual.task_id as task_id, t.servicetask_id, t.sort_order,t.team_loc,t.team_id,clientCase.case_name,clients.client_name,project.priority,Taskindividual.task_duedate,t.unit_status,Taskindividual.task_timedue,clientCase.id as client_case_id, C.task_date_time as task_date_time FROM tbl_tasks_units as t
		LEFT JOIN tbl_task_instruct Taskindividual ON (Taskindividual.id=t.task_instruct_id)
		$taskduedatejoin
		INNER JOIN tbl_tasks task ON (Taskindividual.task_id=task.id)
		LEFT JOIN tbl_client_case clientCase ON (task.client_case_id=clientCase.id)
		LEFT JOIN tbl_client as clients ON clientCase.client_id = clients.id
		LEFT JOIN tbl_priority_project as project ON project.id = Taskindividual.task_priority
		WHERE t.unit_status=0 AND t.sort_order=1 AND task.task_closed=0 AND task.task_cancel=0 AND Taskindividual.isactive=1 AND clientCase.is_close=0 AND task.task_closed=0 AND t.unit_assigned_to = ".$userId."  AND ((t.team_id = 1 AND t.team_loc =0) OR (t.team_id != 1 AND t.team_loc !=0))
		) AS B INNER JOIN tbl_teamlocation_master as location ON location.id = B.team_loc INNER JOIN tbl_team as team ON team.id = B.team_id INNER JOIN tbl_servicetask as servicetask ON servicetask.id = B.servicetask_id";
				/*Pending End*/
				$query->andWhere('tbl_tasks_units.id IN ('.$sql.')');
			}
			$query->andFilterWhere(['tbl_tasks_units.unit_status'=>$statusAr]);
		}
		//else 	
			//$query->andWhere('tbl_tasks_units.unit_status!=4');


		if(isset($params['TasksUnitsSearch']['task_duedate']) && $params['TasksUnitsSearch']['task_duedate']!=""){
			$task_duedate = explode("-", $params['TasksUnitsSearch']['task_duedate']);
            $task_duedate_start = explode("/", trim($task_duedate[0]));
            $task_duedate_end = explode("/", trim($task_duedate[1]));
            $task_duedate_s = $task_duedate_start[2] . "-" . $task_duedate_start[0] . "-" . $task_duedate_start[1].' 00:00:00';
            $task_duedate_e = $task_duedate_end[2] . "-" . $task_duedate_end[0] . "-" . $task_duedate_end[1].' 23:59:59';
          	$query->andWhere(" $data_query >= '$task_duedate_s' AND $data_query  <= '$task_duedate_e' ");
        }
		if(isset($params['TasksUnitsSearch']['task_priority']) && $params['TasksUnitsSearch']['task_priority']!="" && $params['TasksUnitsSearch']['task_priority']!="All"){
			$query->andFilterWhere(['or like', 'priority', $params['TasksUnitsSearch']['task_priority'],false]);
		}
		if(isset($params['TasksUnitsSearch']['client_case_id']) && $params['TasksUnitsSearch']['client_case_id']!="" && $params['TasksUnitsSearch']['client_case_id']!="All"){
    		$query->andWhere('tbl_client_case.id IN ('.implode(',',$params['TasksUnitsSearch']['client_case_id']).')');
    	}
		if(isset($params['TasksUnitsSearch']['client_id']) && $params['TasksUnitsSearch']['client_id']!="" && $params['TasksUnitsSearch']['client_id']!="All"){
    		$query->andWhere('tbl_client_case.client_id IN ('.implode(',',$params['TasksUnitsSearch']['client_id']).')');
    	}
    	if(isset($params['TasksUnitsSearch']['task_cancel_reason']) && $params['TasksUnitsSearch']['task_cancel_reason']!="" && $params['TasksUnitsSearch']['task_cancel_reason']!="All"){
    		$query->andFilterWhere(['like', 'task_cancel_reason', $params['TasksUnitsSearch']['task_cancel_reason'],false]);
    	}

    	if(isset($params['TasksUnitsSearch']['project_name']) && $params['TasksUnitsSearch']['project_name']!="All"){
			if(is_array($params['TasksUnitsSearch']['project_name'])){
			$project_namequery = "";
			$is_unset=false;
			if(!empty($params['TasksUnitsSearch']['project_name'])) {
				foreach($params['TasksUnitsSearch']['project_name'] as $k=>$v) {
					if($v=='(not set)'){
						$params['TasksUnitsSearch']['project_name'][$k]='';
					} if($project_namequery == "") {
						$project_namequery = "project_name='".$params['TasksUnitsSearch']['project_name'][$k]."'";
						if($params['TasksUnitsSearch']['project_name'][$k]==''){$params['TasksUnitsSearch']['project_name'][$k]='(not set)';}
					} else {
						$project_namequery .= " OR project_name='".$params['TasksUnitsSearch']['project_name'][$k]."'";
						if($params['TasksUnitsSearch']['project_name'][$k]==''){$params['TasksUnitsSearch']['project_name'][$k]='(not set)';}
					}
				}

				if($is_unset==false){
					$query->andWhere("(".$project_namequery.")");
				}
				$this->project_name=$params['TasksUnitsSearch']['project_name'];
			}
			}else{
				$query->andFilterWhere(['like', 'project_name', $params['TasksUnitsSearch']['project_name']]);
				$this->project_name=$params['TasksUnitsSearch']['project_name'];
			}
    	}
		$where_workflow_task="";
		$query->andFilterWhere(['tbl_tasks_units.servicetask_id' => $params['TasksUnitsSearch']['workflow_task']]);
        $this->load($params);
		

		return $dataProvider;
	}

	public function searchMyActiveTasksFilter($params)
    {
		$user_id=Yii::$app->user->identity->id;
		$timezoneOffset = (new Options)->getOffsetOfCurrenttimeZone();
		if (Yii::$app->db->driverName == 'mysql') {
			$data_query = "getDueDateTimeByUsersTimezone('{$timezoneOffset}',tbl_task_instruct.task_duedate,tbl_task_instruct.task_timedue,'%Y-%m-%d %H:%i:%s')";
		} else {
				$data_query = "[dbo].getDueDateTimeByUsersTimezone('{$timezoneOffset}',tbl_task_instruct.task_duedate,tbl_task_instruct.task_timedue,'%Y-%m-%d %H:%i:%s')";
		}
		$sqlpastdue="SELECT COUNT(*) as totalitems
    	FROM tbl_task_instruct
    	INNER JOIN tbl_tasks ON tbl_tasks.id = task_id
		WHERE tbl_task_instruct.task_id = tbl_tasks_units.task_id
		AND isactive=1";
		$todaysdate = (new Options)->ConvertOneTzToAnotherTz(date('Y-m-d H:i:s'), 'UTC', $_SESSION['usrTZ'], "YMDHIS");
    	if (Yii::$app->db->driverName == 'mysql'){
			$task_completedate = " CONVERT_TZ(tbl_tasks.task_complete_date,'+00:00','".$timezoneOffset."') ";
			$sqlpastdue.=" AND $data_query < CASE WHEN tbl_tasks.task_complete_date!='0000-00-00 00:00:00' AND tbl_tasks.task_complete_date!='' AND tbl_tasks.task_status = 4 THEN '$task_duedatetime' ELSE '$todaysdate' END";
        }else{
			$task_completedate = " CAST(switchoffset(todatetimeoffset(tbl_tasks.task_complete_date, '+00:00'), '$timezoneOffset') as DATETIME) ";
        	$sqlpastdue.=" AND $data_query < CASE WHEN CAST(tbl_tasks.task_complete_date as varchar)!='0000-00-00 00:00:00' AND CAST(tbl_tasks.task_complete_date as varchar) IS NOT NULL AND tbl_tasks.task_status = 4 THEN '$task_duedatetime' ELSE '$todaysdate' END";
		}
		
		$pageSize=25;
		if(isset($params['export']) && $params['export']=='export') {
			$pageSize=-1;
		}
		$query = TasksUnits::find();
		$query->select(['tbl_tasks_units.task_id', 
		'tbl_tasks_units.id', 'tbl_tasks_units.servicetask_id', 'tbl_tasks_units.unit_status', 
		'tbl_tasks_units.unit_assigned_to', 'tbl_tasks_units.task_instruct_id',
		'tbl_task_instruct.task_timedue','tbl_task_instruct.task_duedate as task_duedate', 
		'tbl_task_instruct.task_priority','tbl_task_instruct.project_name as project_name',
		'tbl_task_instruct.task_priority','tbl_client_case.case_name','tbl_client.client_name',
		'tbl_tasks.task_closed as task_closed',
		'tbl_tasks.task_cancel as task_cancel',
		"$data_query as task_date_time",
		"($sqlpastdue) as ispastdue"
		]);
		$query->joinWith(['tasks'=>function (\yii\db\ActiveQuery $query){
			$query->joinWith(['clientCase'=>function (\yii\db\ActiveQuery $query){
				$query->joinWith(['client'],false);
			}],false);
		}],false);
		$query->joinWith(['taskInstruct'=>function (\yii\db\ActiveQuery $query){
			$query->joinWith(['taskPriority','taskPriority'],false);
		}],false);
		$query->joinWith(['servicetask']);
		
		$query->where([
			'tbl_tasks_units.unit_assigned_to' => $user_id,
			'isactive'=>1,
			'tbl_tasks.task_closed' => 0,
			'tbl_tasks.task_cancel' => 0,
			'tbl_client_case.is_close' => 0
			]);
		$query->andWhere('tbl_tasks_units.unit_status!=4');

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'pagination' => [
				'pageSize' => $pageSize,
			],
			'sort' => [
					'attributes' => ['unit_status','id','client_id','task_duedate','client_case_id','project_name',
					'id'=> [
						'asc' => ['tbl_tasks_units.id' => SORT_ASC],
						'desc' => ['tbl_tasks_units.id' => SORT_DESC]
					],
					'project_name' => [
						'asc' => ['tbl_task_instruct.project_name' => SORT_ASC],
						'desc' => ['tbl_task_instruct.project_name' => SORT_DESC]
					],
					'client_id' => [
							'asc' => ['tbl_client.client_name' => SORT_ASC],
							'desc' => ['tbl_client.client_name' => SORT_DESC]
						],
					'client_case_id' => [
							'asc' => ['tbl_client_case.case_name' => SORT_ASC],
							'desc' => ['tbl_client_case.case_name' => SORT_DESC]
						],
						'task_priority' => [
							'asc' => ['priority' => SORT_ASC],
							'desc' => ['priority' => SORT_DESC]
						],
						'workflow_task' => [
							'asc' => ['tbl_servicetask.service_task' => SORT_ASC],
							'desc' => ['tbl_servicetask.service_task' => SORT_DESC],
						],
						'task_duedate' => [
							'asc' => ['task_date_time' => SORT_ASC],
							'desc' => ['task_date_time' => SORT_DESC],
						],
						'unit_assigned_to' => [
							'asc' => ['assign_user.usr_first_name' => SORT_ASC],
							'desc' => ['assign_user.usr_first_name' => SORT_DESC],
						],
					]
				]
        ]);
        $dataProvider->sort->enableMultiSort=true;
        /*IRT-67*/
		if(isset($params['grid_id']) && $params['grid_id']!=""){
			$grid_id=$params['grid_id'].'_'.Yii::$app->user->identity->id;
			$sql="SELECT * FROM tbl_dynagrid_dtl WHERE id IN (SELECT sort_id FROM tbl_dynagrid WHERE id='$grid_id')";
			$sort_data=Yii::$app->db->createCommand($sql)->queryOne();
			if(!empty($sort_data)){
				$dataProvider->sort->defaultOrder=json_decode($sort_data['data'],true);
			}
		}
		/*IRT-67*/
		/*multiselect*/
        if ($params['TasksUnitsSearch']['unit_status'] != null && is_array($params['TasksUnitsSearch']['unit_status'])) {
			if(!empty($params['TasksUnitsSearch']['unit_status'])){
				foreach($params['TasksUnitsSearch']['unit_status'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($params['TasksUnitsSearch']['unit_status']);
					}
				}
			}
		}
		if ($params['TasksUnitsSearch']['client_case_id'] != null && is_array($params['TasksUnitsSearch']['client_case_id'])) {
			if(!empty($params['TasksUnitsSearch']['client_case_id'])){
				foreach($params['TasksUnitsSearch']['client_case_id'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($params['TasksUnitsSearch']['client_case_id']);
					}
				}
			}
		}
		if ($params['TasksUnitsSearch']['task_priority'] != null && is_array($params['TasksUnitsSearch']['task_priority'])) {
			if(!empty($params['TasksUnitsSearch']['task_priority'])){
				foreach($params['TasksUnitsSearch']['task_priority'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($params['TasksUnitsSearch']['task_priority']);
					}
				}
			}
		}
		if ($params['TasksUnitsSearch']['project_name'] != null && is_array($params['TasksUnitsSearch']['project_name'])) {
			if(!empty($params['TasksUnitsSearch']['project_name'])){
				foreach($params['TasksUnitsSearch']['project_name'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($params['TasksUnitsSearch']['project_name']);
					}
				}
			}
		}
		if ($params['TasksUnitsSearch']['client_id'] != null && is_array($params['TasksUnitsSearch']['client_id'])) {
			if(!empty($params['TasksUnitsSearch']['client_id'])){
				foreach($params['TasksUnitsSearch']['client_id'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($params['TasksUnitsSearch']['client_id']);
					}
				}
			}
		}
		if ($params['TasksUnitsSearch']['workflow_task'] != null && is_array($params['TasksUnitsSearch']['workflow_task'])) {
			if(!empty($params['TasksUnitsSearch']['workflow_task'])){
				foreach($params['TasksUnitsSearch']['workflow_task'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($params['TasksUnitsSearch']['workflow_task']);
					}
				}
			}
		}
		$status = array();
		
		/*multiselect*/
		$query->andFilterWhere(['tbl_tasks_units.id' => $params['TasksUnitsSearch']['id']]);

    	if(isset($params['TasksUnitsSearch']['unit_status']) && $params['TasksUnitsSearch']['unit_status']!="" && $params['TasksUnitsSearch']['unit_status']!="All"){
			$status=$params['TasksUnitsSearch']['unit_status'];
			$statusAr = $status;
			if(in_array(7,$status)){
				$status_pen = array_keys($status, 7);
				$status_index = array_pop($status_pen);
				
				if($status_index!==false) {
					unset($statusAr[$status_index]);
				}				
				/*Pending Start*/
				$userId = Yii::$app->user->identity->id;
		$service_ids = array();
		$timezoneOffset = (new Options)->getOffsetOfCurrenttimeZone();
		if (Yii::$app->db->driverName == 'mysql') {
			$data_query = "getDueDateTimeByUsersTimezone('{$timezoneOffset}',tbl_task_instruct.task_duedate,tbl_task_instruct.task_timedue,'%m/%d/%Y %h:%i %p')";
		} else {
			//$data_query = "[dbo].getDueDateTimeByUsersTimezone('{$timezoneOffset}',tbl_task_instruct.task_duedate,tbl_task_instruct.task_timedue,'%m/%d/%Y %h:%i %p')";
			$data_query = "(SELECT duedatetime FROM [dbo].getDueDateTimeByUsersTimezoneTV('{$timezoneOffset}',tbl_task_instruct.task_duedate,tbl_task_instruct.task_timedue,'%m/%d/%Y %h:%i %p'))";
		}
		$taskduedatejoin = " INNER JOIN (SELECT tbl_task_instruct.id, $data_query as task_date_time FROM tbl_task_instruct WHERE isactive=1 AND task_id IS NOT NULL AND task_id<>'') as C ON Taskindividual.id = C.id ";
		if (Yii::$app->db->driverName == 'mysql') {
			$subquery="Select unit_status From tbl_tasks_units as tu
		INNER JOIN tbl_task_instruct ON tbl_task_instruct.id = tu.task_instruct_id
		Where tu.sort_order = A.sort_order - 1 And tbl_task_instruct.task_id = A.task_id ORDER BY tu.id DESC LIMIT 1";
		}else{
			$subquery="Select TOP 1 unit_status From tbl_tasks_units as tu
		INNER JOIN tbl_task_instruct ON tbl_task_instruct.id = tu.task_instruct_id
		Where tu.sort_order = A.sort_order - 1 And tbl_task_instruct.task_id = A.task_id ORDER BY tu.id DESC";
		}
		$sql = "Select B.task_unit_id From (
		Select A.* From (
			SELECT t.id as task_unit_id,Taskindividual.task_id as task_id, t.servicetask_id, t.sort_order,t.team_loc,t.team_id,clientCase.case_name,clients.client_name,project.priority,Taskindividual.task_duedate,t.unit_status,Taskindividual.task_timedue,clientCase.id as client_case_id, C.task_date_time as task_date_time FROM tbl_tasks_units as t
			LEFT JOIN tbl_task_instruct Taskindividual ON (Taskindividual.id=t.task_instruct_id)
			$taskduedatejoin
			INNER JOIN tbl_tasks task ON (Taskindividual.task_id=task.id)
			LEFT JOIN tbl_client_case clientCase ON (task.client_case_id=clientCase.id)
			LEFT JOIN tbl_client as clients ON clientCase.client_id = clients.id
			LEFT JOIN tbl_priority_project as project ON project.id = Taskindividual.task_priority
			WHERE  t.unit_status=0 AND (t.sort_order not in(0,1)) AND task.task_closed=0 AND task.task_cancel=0 AND Taskindividual.isactive=1 AND clientCase.is_close=0 AND task.task_closed=0 AND t.unit_assigned_to = ".$userId." AND ((t.team_id = 1 AND t.team_loc =0) OR (t.team_id != 1 AND t.team_loc !=0))) as A
		Where ( $subquery ) = 4
		Union All
		SELECT t.id as task_unit_id,Taskindividual.task_id as task_id, t.servicetask_id, t.sort_order,t.team_loc,t.team_id,clientCase.case_name,clients.client_name,project.priority,Taskindividual.task_duedate,t.unit_status,Taskindividual.task_timedue,clientCase.id as client_case_id, C.task_date_time as task_date_time FROM tbl_tasks_units as t
		LEFT JOIN tbl_task_instruct Taskindividual ON (Taskindividual.id=t.task_instruct_id)
		$taskduedatejoin
		INNER JOIN tbl_tasks task ON (Taskindividual.task_id=task.id)
		LEFT JOIN tbl_client_case clientCase ON (task.client_case_id=clientCase.id)
		LEFT JOIN tbl_client as clients ON clientCase.client_id = clients.id
		LEFT JOIN tbl_priority_project as project ON project.id = Taskindividual.task_priority
		WHERE t.unit_status=0 AND t.sort_order=1 AND task.task_closed=0 AND task.task_cancel=0 AND Taskindividual.isactive=1 AND clientCase.is_close=0 AND task.task_closed=0 AND t.unit_assigned_to = ".$userId."  AND ((t.team_id = 1 AND t.team_loc =0) OR (t.team_id != 1 AND t.team_loc !=0))
		) AS B INNER JOIN tbl_teamlocation_master as location ON location.id = B.team_loc INNER JOIN tbl_team as team ON team.id = B.team_id INNER JOIN tbl_servicetask as servicetask ON servicetask.id = B.servicetask_id";
				/*Pending End*/
				$query->OrWhere('tbl_tasks_units.id IN ('.$sql.')');
			}
			$query->andFilterWhere(['tbl_tasks_units.unit_status'=>$statusAr]);
		}
		//else 	
			//$query->andWhere('tbl_tasks_units.unit_status!=4');


		if(isset($params['TasksUnitsSearch']['task_duedate']) && $params['TasksUnitsSearch']['task_duedate']!=""){
			$task_duedate = explode("-", $params['TasksUnitsSearch']['task_duedate']);
            $task_duedate_start = explode("/", trim($task_duedate[0]));
            $task_duedate_end = explode("/", trim($task_duedate[1]));
            $task_duedate_s = $task_duedate_start[2] . "-" . $task_duedate_start[0] . "-" . $task_duedate_start[1].' 00:00:00';
            $task_duedate_e = $task_duedate_end[2] . "-" . $task_duedate_end[0] . "-" . $task_duedate_end[1].' 23:59:59';
          	$query->andWhere(" $data_query >= '$task_duedate_s' AND $data_query  <= '$task_duedate_e' ");
        }
		if(isset($params['TasksUnitsSearch']['task_priority']) && $params['TasksUnitsSearch']['task_priority']!="" && $params['TasksUnitsSearch']['task_priority']!="All"){
			$query->andFilterWhere(['or like', 'priority', $params['TasksUnitsSearch']['task_priority'],false]);
		}
		if(isset($params['TasksUnitsSearch']['client_case_id']) && $params['TasksUnitsSearch']['client_case_id']!="" && $params['TasksUnitsSearch']['client_case_id']!="All"){
    		$query->andWhere('tbl_client_case.id IN ('.implode(',',$params['TasksUnitsSearch']['client_case_id']).')');
    	}
		if(isset($params['TasksUnitsSearch']['client_id']) && $params['TasksUnitsSearch']['client_id']!="" && $params['TasksUnitsSearch']['client_id']!="All"){
    		$query->andWhere('tbl_client_case.client_id IN ('.implode(',',$params['TasksUnitsSearch']['client_id']).')');
    	}
    	if(isset($params['TasksUnitsSearch']['task_cancel_reason']) && $params['TasksUnitsSearch']['task_cancel_reason']!="" && $params['TasksUnitsSearch']['task_cancel_reason']!="All"){
    		$query->andFilterWhere(['like', 'task_cancel_reason', $params['TasksUnitsSearch']['task_cancel_reason'],false]);
    	}

    	if(isset($params['TasksUnitsSearch']['project_name']) && $params['TasksUnitsSearch']['project_name']!="All"){
			if(is_array($params['TasksUnitsSearch']['project_name'])){
			$project_namequery = "";
			$is_unset=false;
			if(!empty($params['TasksUnitsSearch']['project_name'])) {
				foreach($params['TasksUnitsSearch']['project_name'] as $k=>$v) {
					if($v=='(not set)'){
						$params['TasksUnitsSearch']['project_name'][$k]='';
					} if($project_namequery == "") {
						$project_namequery = "project_name='".$params['TasksUnitsSearch']['project_name'][$k]."'";
						if($params['TasksUnitsSearch']['project_name'][$k]==''){$params['TasksUnitsSearch']['project_name'][$k]='(not set)';}
					} else {
						$project_namequery .= " OR project_name='".$params['TasksUnitsSearch']['project_name'][$k]."'";
						if($params['TasksUnitsSearch']['project_name'][$k]==''){$params['TasksUnitsSearch']['project_name'][$k]='(not set)';}
					}
				}

				if($is_unset==false){
					$query->andWhere("(".$project_namequery.")");
				}
				$this->project_name=$params['TasksUnitsSearch']['project_name'];
			}
			}else{
				$query->andFilterWhere(['like', 'project_name', $params['TasksUnitsSearch']['project_name']]);
				$this->project_name=$params['TasksUnitsSearch']['project_name'];
			}
    	}
		$where_workflow_task="";
		$query->andFilterWhere(['tbl_tasks_units.servicetask_id' => $params['TasksUnitsSearch']['workflow_task']]);
        $this->load($params);
		if($params['field']=='client_case_id'){
			if(isset($params['q']) && $params['q']!=""){
				$query->andWhere("tbl_client_case.case_name like '%".$params['q']."%'");
			}
			$dataProvider = ArrayHelper::map($query->all(),
			function($model){
				return $model->tasks->client_case_id;
			},
			function($model){
				return $model->tasks->clientCase->case_name;
			});
		}
		if($params['field']=='client_id'){
			if(isset($params['q']) && $params['q']!=""){
				$query->andWhere("tbl_client.client_name like '%".$params['q']."%'");
			}
			$dataProvider = ArrayHelper::map($query->all(),
			function($model){
				return $model->tasks->clientCase->client_id;
			},
			function($model){
				return $model->tasks->clientCase->client->client_name;
			});
		}
		if($params['field']=='workflow_task'){
			if(isset($params['q']) && $params['q']!=""){
				$query->andWhere("tbl_servicetask.service_task like '%".$params['q']."%'");
			}
			$dataProvider = ArrayHelper::map($query->all(),
			'servicetask_id',
			function($model){
				return $model->servicetask->service_task;
			});
		}
		if($params['field']=='task_priority'){
			if(isset($params['q']) && $params['q']!=""){
				$query->andWhere("priority like '%".$params['q']."%'");
			}
			$dataProvider = ArrayHelper::map($query->all(),
			function($model){
				return $model->taskInstruct->taskPriority->priority;
			},
			function($model){
				return $model->taskInstruct->taskPriority->priority;
			});
		}

		return array('All'=>'All') + $dataProvider;//$dataProvider;
	}
	
    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
		$pageSize=25;
		if(isset($params['export']) && $params['export']=='export') {
			$pageSize=-1;
		}

    	$dataProvider = array();

    //	echo "<pre>",print_r($params); die;
    	$is_unit_all=false;
		if(isset($params['TasksUnitsSearch']['unit_status']) && $params['TasksUnitsSearch']['unit_status'] == 'all'){
			$is_unit_all=true;
		}
		$timezoneOffset = (new Options)->getOffsetOfCurrenttimeZone();
		if (Yii::$app->db->driverName == 'mysql') {
			$data_query_pastdue = "getDueDateTimeByUsersTimezone('{$timezoneOffset}',tbl_task_instruct.task_duedate,tbl_task_instruct.task_timedue,'%Y-%m-%d %H:%i:%s')";
		}else{
			$data_query_pastdue = "[dbo].getDueDateTimeByUsersTimezone('{$timezoneOffset}',tbl_task_instruct.task_duedate,tbl_task_instruct.task_timedue,'%Y-%m-%d %H:%i:%s')";
		}

        $sqlpastdue="SELECT COUNT(*) as totalitems
    	FROM tbl_task_instruct
    	INNER JOIN tbl_tasks ON tbl_tasks.id = task_id
		WHERE tbl_task_instruct.task_id = tunits.task_id
    	AND isactive=1";

		/*INNER JOIN (SELECT tbl_task_instruct.id, $data_query_pastdue as task_date_time FROM tbl_task_instruct WHERE isactive=1 AND task_id IS NOT NULL AND task_id<>'') as A ON tbl_task_instruct.id = A.id*/
		$todaysdate = (new Options)->ConvertOneTzToAnotherTz(date('Y-m-d H:i:s'), 'UTC', $_SESSION['usrTZ'], "YMDHIS");
    	if (Yii::$app->db->driverName == 'mysql'){
			$task_completedate = " CONVERT_TZ(tbl_tasks.task_complete_date,'+00:00','".$timezoneOffset."') ";
			$sqlpastdue.=" AND A.task_date_time < CASE WHEN tbl_tasks.task_complete_date!='0000-00-00 00:00:00' AND tbl_tasks.task_complete_date!='' AND tbl_tasks.task_status = 4 THEN '$task_duedatetime' ELSE '$todaysdate' END";
        }else{
			$task_completedate = " CAST(switchoffset(todatetimeoffset(tbl_tasks.task_complete_date, '+00:00'), '$timezoneOffset') as DATETIME) ";
        	$sqlpastdue.=" AND A.task_date_time < CASE WHEN CAST(tbl_tasks.task_complete_date as varchar)!='0000-00-00 00:00:00' AND CAST(tbl_tasks.task_complete_date as varchar) IS NOT NULL AND tbl_tasks.task_status = 4 THEN '$task_duedatetime' ELSE '$todaysdate' END";
        }
		//echo $sqlpastdue;die;


		$date = Yii::$app->request->get('dates');
        if (Yii::$app->db->driverName == 'mysql') {
			$data_query = "getDueDateTimeByUsersTimezone('{$timezoneOffset}',tbl_task_instruct.task_duedate,tbl_task_instruct.task_timedue,'%Y-%m-%d %H:%i:%s')";
		} else {
			//$data_query = "[dbo].getDueDateTimeByUsersTimezone('{$timezoneOffset}',tbl_task_instruct.task_duedate,tbl_task_instruct.task_timedue,'%Y-%m-%d %H:%i:%s')";
			$data_query = "(SELECT duedatetime FROM [dbo].getDueDateTimeByUsersTimezoneTV('{$timezoneOffset}',tbl_task_instruct.task_duedate,tbl_task_instruct.task_timedue,'%Y-%m-%d %H:%i:%s'))";
		}
		$taskduedatejoin = " INNER JOIN (SELECT tbl_task_instruct.id, $data_query as task_date_time FROM tbl_task_instruct WHERE isactive=1 AND task_id IS NOT NULL AND task_id<>'') as A ON instruct.id = A.id ";
		$evidence_query = '';
		$select_unit='DISTINCT tunits.id,('.$sqlpastdue.') as ispastdue';
		if(isset($params['TasksUnitsSearch']['unit_status']) && is_array($params['TasksUnitsSearch']['unit_status'])){
			if(in_array(9,$params['TasksUnitsSearch']['unit_status']))
				$select_unit='tunits.id,('.$sqlpastdue.') as ispastdue';
		}
		if(isset($params['onlyEvidTasks'])) {
			$select_unit = "DISTINCT(tunits.id) as id,($sqlpastdue) as ispastdue"; // only unique task assigned lists by unit search

			$joinwherecond = "";
			$joinwherecondcomp = "";
			if($params['unit']!=0)
			{
				$joinwherecond = " AND unit.remove=0";
				$joinwherecondcomp = " AND unitcomp.remove=0";
			}

			$evidence_query = " INNER JOIN tbl_task_instruct_evidence ON instruct.id = tbl_task_instruct_evidence.task_instruct_id
			INNER JOIN tbl_evidence ON tbl_evidence.id = tbl_task_instruct_evidence.evidence_id
			LEFT JOIN tbl_unit as unit ON tbl_evidence.unit = unit.id $joinwherecond
			LEFT JOIN tbl_unit as unitcomp ON tbl_evidence.comp_unit = unitcomp.id $joinwherecondcomp
			LEFT JOIN tbl_unit_master as unitmaster ON unitmaster.unit_id = unit.id
			LEFT JOIN tbl_unit_master as unitmastercomp ON unitmastercomp.unit_id = unitcomp.id ";
		}
		$unit_where =' AND 1=1';

		if(isset($params['unit']) && $params['unit'] != '') {
			$select_unit = "DISTINCT(tunits.id) as id,($sqlpastdue) as ispastdue"; // only unique task assigned lists by unit search
			/*if($params['unit']!=0)
				$unit_where = " AND (unitmaster.unit_type = {$params['unit']} OR unitmastercomp.unit_type={$params['unit']})";
			else
				$unit_where = " AND (unit.id= '".$params['unit_id']."' OR unitcomp.id= '".$params['unit_id']."')";*/

			$evidence_query = " INNER JOIN tbl_task_instruct_evidence ON instruct.id = tbl_task_instruct_evidence.task_instruct_id
			INNER JOIN tbl_evidence ON tbl_evidence.id = tbl_task_instruct_evidence.evidence_id
			LEFT JOIN tbl_unit as unit ON tbl_evidence.unit = unit.id $joinwherecond
			LEFT JOIN tbl_unit as unitcomp ON tbl_evidence.comp_unit = unitcomp.id $joinwherecondcomp
			LEFT JOIN tbl_unit_master as unitmaster ON unitmaster.unit_id = unit.id
			LEFT JOIN tbl_unit_master as unitmastercomp ON unitmastercomp.unit_id = unitcomp.id ";
				
			if($params['unit']!=0)
				$unit_where = " AND (unitmaster.unit_type=(CASE WHEN tbl_evidence.unit<>0 THEN {$params['unit']} ELSE 0 END) OR unitmastercomp.unit_type=(CASE WHEN tbl_evidence.unit=0 THEN {$params['unit']} ELSE 0 END))";
			else
				$unit_where = " AND (unit.id=(CASE WHEN tbl_evidence.unit<>0 THEN '".$params['unit_id']."' ELSE 0 END) OR unitcomp.id=(CASE WHEN tbl_evidence.unit=0 THEN '".$params['unit_id']."' ELSE 0 END))";
		}

		
		/*$sqlteampname=" SELECT tbl_priority_team.tasks_priority_name FROM tbl_priority_team WHERE id IN (SELECT tbl_tasks_teams.team_loc_prority
FROM tbl_tasks_teams INNER JOIN tbl_priority_team_loc ON tbl_priority_team_loc.priority_team_id = tbl_tasks_teams.team_loc_prority
INNER JOIN tbl_priority_team ON tbl_priority_team.id = tbl_priority_team_loc.priority_team_id
WHERE tbl_tasks_teams.task_id=task.id AND tbl_tasks_teams.team_id=tunits.team_loc
AND tbl_tasks_teams.team_loc=tunits.team_loc)";*/
				
		$select= $select_unit.", instruct.task_id,tunits.unit_status,tunits.unit_assigned_to,tunits.sort_order,tunits.team_id,tunits.team_loc,task.task_status,(SELECT CASE WHEN (team_priority IS NULL OR team_priority = 0)THEN ((SELECT count( * )FROM tbl_priority_team) +1) ELSE team.priority_order END AS final_priority FROM tbl_tasks AS task_inner LEFT JOIN tbl_priority_team as team ON task_inner.team_priority = team.id where task_inner.id = task.id) as team_priority_order,project.priority,project.priority_order as project_order,project.id as project_priority_id,stask.service_task,(select service_name from tbl_teamservice where id=stask.teamservice_id) as service_name,loc.team_location_name,clientcase.case_name,client.client_name,tpriority.tasks_priority_name,task.task_closed,task.task_cancel,assign_user.usr_first_name,assign_user.usr_lastname,instruct.task_duedate,instruct.task_timedue,tunits.servicetask_id,task.created,A.task_date_time as task_date_time";
		$fromtable = "tbl_tasks_units as tunits";
		// INNER JOIN tbl_task_instruct_servicetask as servicetask ON tunits.task_instruct_servicetask_id = servicetask.id
		//INNER JOIN tbl_task_instruct as instruct ON tunits.task_instruct_id = instruct.id
		$join = " INNER JOIN tbl_task_instruct as instruct ON tunits.task_instruct_id = instruct.id
			$taskduedatejoin
			LEFT JOIN tbl_priority_project as project ON project.id = instruct.task_priority
			INNER JOIN tbl_servicetask as stask ON tunits.servicetask_id = stask.id
			INNER JOIN tbl_teamlocation_master as loc ON tunits.team_loc = loc.id
			INNER JOIN tbl_tasks as task ON instruct.task_id = task.id
			$evidence_query
			LEFT JOIN tbl_client_case as clientcase ON clientcase.id = task.client_case_id
			LEFT JOIN tbl_client as client ON client.id = clientcase.client_id
			LEFT JOIN tbl_tasks_teams on tbl_tasks_teams.task_id=tunits.task_id and tbl_tasks_teams.team_id=tunits.team_id and tbl_tasks_teams.team_loc=tunits.team_loc
			LEFT JOIN tbl_priority_team as tpriority ON tpriority.id = tbl_tasks_teams.team_loc_prority
			LEFT JOIN tbl_user as assign_user ON assign_user.id = tunits.unit_assigned_to";
		$where = "tunits.team_id = '".$params['team_id']."' AND tunits.team_loc = '".$params['team_loc']."' AND instruct.isactive = 1 AND task.task_closed = 0 AND task.task_cancel = 0 AND clientcase.is_close = 0 $unit_where";
		//echo $select.' FROM '.$fromtable .$join.' WHERE '.$where;die;

		$order = "";
		$defaultSort = "";
		if($params['sort']=="") {

			/* IRT 26 */
			$optionsproject_sort_display=Options::find()->select('project_sort_display')->where(['user_id'=>Yii::$app->user->identity->id])->andWhere('project_sort_display IS NOT NULL')->one()->project_sort_display;

			if(isset($optionsproject_sort_display) && in_array($optionsproject_sort_display, array(0,1,2,3))) {
				$fieldvalue =$optionsproject_sort_display;
			} else {
				$settings_info = Settings::find()->select('fieldvalue')->where("field = 'project_sort'")->one();
				$fieldvalue=$settings_info->fieldvalue;
			}
			/*IRT 26*/
			if ($fieldvalue == '0') {
				// $order.= " order by project.priority_order ASC,instruct.task_duedate DESC,instruct.task_timedue DESC";
				$defaultSort=['task_priority'=>SORT_ASC,'task_duedate'=>SORT_DESC];
			} else if ($fieldvalue == '1') {
				// $order.= " order by instruct.task_duedate DESC,instruct.task_timedue DESC";
				$defaultSort=['task_duedate'=>SORT_DESC];
			} else if ($fieldvalue == '2') {
				// $order.= " order by tunits.task_id DESC";
				$defaultSort=['task_id'=>SORT_DESC];
			} else if($fieldvalue == '3') {
				// $order.= " order by team_priority_order ASC,project.priority_order ASC,instruct.task_duedate DESC,instruct.task_timedue DESC";
				$defaultSort=['team_priority'=>SORT_ASC,'task_priority'=>SORT_ASC,'task_duedate'=>SORT_DESC];
			}
        } else {
			$order = "";
		}

		$task_duedate_last = $params['TasksUnitsSearch']['task_duedate'];

		if(isset($params['task_id']) && $params['task_id']!='' && $params['task_id'] != 0){
    		$where.= ' AND tunits.task_id = '.$params['task_id'];
		}

    	if(isset($params['tasks_units_id']) && $params['tasks_units_id']!='' && $params['tasks_units_id'] != 0){
    		$where.= ' AND tunits.id = '.$params['tasks_units_id'];
    	}
		if(isset($params['servicetask_id']) && $params['servicetask_id']!='' && $params['servicetask_id'] != 0){
			$where.= ' AND tunits.servicetask_id IN ('.$params['servicetask_id'].')';
		}

		if(isset($params['task_duedate']) && $params['task_duedate']!='All'){
			$params['task_duedate'] = date("Y-m-d", strtotime($params['task_duedate']));
			if (Yii::$app->db->driverName == 'mysql') {
    			$where.= " AND DATE_FORMAT(CONVERT_TZ(CONCAT(instruct.`task_duedate` , ' ', STR_TO_DATE(instruct.`task_timedue` , '%l:%i %p' )),'+00:00','{$timezoneOffset}'), '%Y-%m-%d') = '{$params['task_duedate']}'";
			} else {
				$where.= " AND CAST(switchoffset(todatetimeoffset(Cast((CAST(instruct.task_duedate as varchar)  + ' ' + instruct.task_timedue) as datetime), '+00:00'), '{$timezoneOffset}') as date) = '".$params['task_duedate']."'";
			}
		}

		if(isset($params['task_priority']) && $params['task_priority']!=''){
			$where.= " AND project.priority like '".$params['task_priority']."'";
		}

		if(isset($params['team_priority']) && $params['team_priority']!=''){
			$where.= ' AND tpriority.tasks_priority_name = "'.$params['team_priority'].'"';
		}

		$currdate=time();
    	if(isset($params['due'])) {
    		if($params['due']=='past') {
    			if (Yii::$app->db->driverName == 'mysql') {
					$where.= ' AND instruct.task_duedate < "' . date('Y-m-d H:i:s', time()) . '"';
    			} else {
					$where.= " AND Cast((CAST(instruct.task_duedate as varchar)  + ' ' + instruct.task_timedue) as datetime) < '" . date('Y-m-d H:i:s', time()) . "'";
    			}
    			$where.= " AND task.task_status !=4";
    		}
    		if($params['due']=='notpastdue') {
				$where.= " AND task.task_closed = 0 AND task.task_cancel = 0";
    		}
    	}

		/*if(isset($params['client_case']) && $params['client_case'] != ''){
			if(is_numeric($params['client_case'])){
				$where.= ' AND task.client_case_id IN ('.$params['client_case'].')';
			} else {
				$clientcase = explode('/',$params['client_case']);
				// print_r($clientcase); exit;
				if(!empty($clientcase)) {
					$where.= ' AND client.client_name = "'.trim($clientcase[0]).'" AND clientcase.case_name = "'.trim($clientcase[1]).'"';
			    }
			}
		}*/

		if(isset($params['unit_assigned_to']) && $params['unit_assigned_to']!='' && $params['statusFilter']!=8){

			if(isset($params['completedtodo']) && $params['completedtodo']!=''){}else{
				if($params['unit_assigned_to'] == 'assignedonly'){
					$where.= ' AND tunits.unit_assigned_to != 0';
				}else{
					$where.= ' AND tunits.unit_assigned_to = '.$params['unit_assigned_to'];
				}
			}
			if(isset($_REQUEST['dates']) && $_REQUEST['dates']!='All'){
				$transaction_type = '4';
				if($_REQUEST['taskActive']==1 && isset($_REQUEST['taskActive'])){
					$transaction_type = '5,6';
				}
				if (Yii::$app->db->driverName == 'mysql') {
    				/*$where .= " AND (DATE_FORMAT( CONVERT_TZ(t.modified,'UTC','{$_SESSION['usrTZ']}'), '%m/%d/%Y') = '". $_REQUEST['dates'] . "')";*/
    				$where .= " AND tunits.id IN (SELECT tasks_unit_id FROM tbl_tasks_units_transaction_log WHERE transaction_type IN (".$transaction_type.") AND tunits.id = tasks_unit_id AND DATE_FORMAT( CONVERT_TZ(tbl_tasks_units_transaction_log.transaction_date,'+00:00','{$timezoneOffset}'), '%Y-%m-%d') = '".$date."' GROUP BY tbl_tasks_units_transaction_log.transaction_date HAVING tbl_tasks_units_transaction_log.transaction_date = MAX(tbl_tasks_units_transaction_log.transaction_date))";
    			}else{
    				/*$where .= " AND ( CAST(switchoffset(todatetimeoffset(Cast(t.modified as datetime), '+00:00'), '{$timezoneOffset}') as date) = '" . $date . "' )";*/
    				$where .= " AND tunits.id IN (SELECT DISTINCT tasks_unit_id FROM tbl_tasks_units_transaction_log WHERE transaction_type IN (".$transaction_type.") AND tunits.id = tasks_unit_id AND Cast(switchoffset(todatetimeoffset(Cast(tbl_tasks_units_transaction_log.transaction_date as datetime), '+00:00'), '{$timezoneOffset}') as date) >= '".$date."' GROUP BY tbl_tasks_units_transaction_log.transaction_date,tasks_unit_id HAVING tbl_tasks_units_transaction_log.transaction_date = MAX(tbl_tasks_units_transaction_log.transaction_date))";
    			}
    		}

			if(isset($params['task_duedate']) && $params['task_duedate']!='All'){

				$params['TasksUnitsSearch']['task_duedate'] = date("Y-m-d", strtotime($params['TasksUnitsSearch']['task_duedate']));

				if (Yii::$app->db->driverName == 'mysql') {
    				$where.=" AND DATE_FORMAT(CONVERT_TZ(CONCAT(insturct.`task_duedate` , ' ', STR_TO_DATE(insturct.`task_timedue` , '%l:%i %p' )),'+00:00','{$timezoneOffset}'), '%Y-%m-%d') = '{$params['task_duedate']}'";
				} else {
					$where.= " AND CAST(switchoffset(todatetimeoffset(Cast((CAST(insturct.task_duedate as varchar)  + ' ' + insturct.task_timedue) as datetime), '+00:00'), '{$timezoneOffset}') as date) = '".$params['task_duedate']."'";
				}
			}

		}
		/*multiple*/
		if ($params['TasksUnitsSearch']['unit_status'] != null && is_array ($params['TasksUnitsSearch']['unit_status'])) {
			if(!empty($params['TasksUnitsSearch']['unit_status'])){
				foreach($params['TasksUnitsSearch']['unit_status'] as $k=>$v){
					if($v=='All' || trim(urldecode($v))==''){ // || strpos($v,",") !== false
						unset($params['TasksUnitsSearch']['unit_status']);break;
					}
				}
			}
		}
		/*multiple*/
		$status = array();

		if(isset($params['statusFilter']) && $params['statusFilter']!='') {
			$params['statusFilter'] = array($params['statusFilter']);
		}else{
			$params['statusFilter'] = array();
		}
		if(isset($params['TasksUnitsSearch']['unit_status'])) {
			//if(isset($params['TasksUnitsSearch']['unit_status']) && trim(urldecode($params['TasksUnitsSearch']['unit_status']))!= ''){
			if(!is_array($params['TasksUnitsSearch']['unit_status'])) {
				if(trim(urldecode($params['TasksUnitsSearch']['unit_status']))!= ''){
					$params['TasksUnitsSearch']['unit_status'][$params['TasksUnitsSearch']['unit_status']] = $params['TasksUnitsSearch']['unit_status'];
					$params['statusFilter'] = $params['TasksUnitsSearch']['unit_status'];
				}
			} else {
				$params['statusFilter'] = $params['TasksUnitsSearch']['unit_status'];
			}
		}

		if(isset($params['statusFilter']) && in_array(8,$params['statusFilter'])){
			$followup = "";
			if(isset($params['followcat_id']) && $params['followcat_id'] != '' && $params['followcat_id']!= 0 ){
				$followup = "AND tbl_tasks_units_todos.todo_cat_id = {$params['followcat_id']}";
			}
			if(isset($params['source']) && $params['source'] == 'todofollowup' && isset($params['unit_assigned_to']) && $params['unit_assigned_to'] != ''){
				$where.= " AND (tunits.id IN (SELECT tbl_tasks_units_todos.tasks_unit_id FROM tbl_tasks_units_todos WHERE tbl_tasks_units_todos.tasks_unit_id = tunits.id AND tbl_tasks_units_todos.complete=0 AND tbl_tasks_units_todos.assigned={$params['unit_assigned_to']} $followup) AND (tunits.unit_status!=4 AND task.task_closed=0))";
			}
			else if(isset($params['unit_assigned_to']) && $params['unit_assigned_to'] != '') {
				$tododatecondition="";
				$sql_todo = "
				SELECT t.tasks_unit_id
				FROM tbl_tasks_units_todos as t
				INNER JOIN tbl_tasks_units on tbl_tasks_units.id=t.tasks_unit_id
				INNER JOIN tbl_tasks on tbl_tasks.id=tbl_tasks_units.task_id
				INNER JOIN tbl_client_case on tbl_client_case.id=tbl_tasks.client_case_id
				WHERE tbl_tasks.task_cancel = 0 AND tbl_tasks.task_closed = 0 AND tbl_client_case.is_close=0 AND t.complete = 0
				and tbl_tasks_units.unit_status!=4 and t.assigned!=0
				and t.assigned=".$params['unit_assigned_to']."
				and tbl_tasks_units.team_id = ".$params['team_id']."
				and tbl_tasks_units.team_loc = ".$params['team_loc']."
				and t.assigned=".$params['unit_assigned_to']." ".$tododatecondition." ".$followup." ";

				//echo $sql_todo;die;
				/*$where.= " AND tunits.id IN (
				SELECT tbl_tasks_units_todos.tasks_unit_id FROM tbl_tasks_units_todos
				INNER JOIN tbl_tasks_units on tbl_tasks_units.id=tbl_tasks_units_todos.tasks_unit_id
				AND tbl_tasks_units.unit_status!=4
				WHERE tbl_tasks_units_todos.tasks_unit_id = tunits.id AND tbl_tasks_units_todos.complete=0 AND  tunits.team_id = '".$params['team_id']."' AND tunits.team_loc = '".$params['team_loc']."' AND tbl_tasks_units_todos.assigned={$params['unit_assigned_to']} $followup
				) AND task.task_closed=0";*/

				/*$where.= " AND tunits.id IN (
				SELECT tbl_tasks_units_todos.tasks_unit_id FROM tbl_tasks_units_todos
				INNER JOIN tbl_tasks_units on tbl_tasks_units.id=tbl_tasks_units_todos.tasks_unit_id
				AND tbl_tasks_units.unit_status!=4
				WHERE tbl_tasks_units_todos.tasks_unit_id = tunits.id AND tbl_tasks_units_todos.complete=0 AND  tunits.team_id = '".$params['team_id']."' AND tunits.team_loc = '".$params['team_loc']."' AND tbl_tasks_units_todos.assigned={$params['unit_assigned_to']} $followup
				) AND task.task_closed=0";*/

				$where.= " AND tunits.id IN (".$sql_todo.") AND task.task_closed=0";

				if(isset($_REQUEST['dates']) && $_REQUEST['dates']!='All'){
					if (Yii::$app->db->driverName == 'mysql') {
						/*$where .= " AND (DATE_FORMAT( CONVERT_TZ(t.modified,'UTC','{$_SESSION['usrTZ']}'), '%m/%d/%Y') = '". $_REQUEST['dates'] . "')";*/
						$where .= " AND tunits.id IN (SELECT tbl_tasks_units_todos.tasks_unit_id FROM tbl_tasks_units_todos INNER JOIN tbl_tasks_units_todo_transaction_log ON tbl_tasks_units_todo_transaction_log.todo_id = tbl_tasks_units_todos.id WHERE tbl_tasks_units_todos.tasks_unit_id = tunits.id AND transaction_type IN (7,8) AND DATE_FORMAT( CONVERT_TZ(tbl_tasks_units_todo_transaction_log.transaction_date,'+00:00','{$timezoneOffset}'), '%Y-%m-%d') = '".$date."' GROUP BY tbl_tasks_units_todo_transaction_log.transaction_date HAVING tbl_tasks_units_todo_transaction_log.transaction_date = MAX(tbl_tasks_units_todo_transaction_log.transaction_date))";
					}else{
						/*$where .= " AND ( CAST(switchoffset(todatetimeoffset(Cast(t.modified as datetime), '+00:00'), '{$timezoneOffset}') as date) = '" . $date . "' )";*/
						$where .= " AND tunits.id IN (SELECT tbl_tasks_units_todos.tasks_unit_id FROM tbl_tasks_units_todos INNER JOIN tbl_tasks_units_todo_transaction_log ON tbl_tasks_units_todo_transaction_log.todo_id = tbl_tasks_units_todos.id WHERE tbl_tasks_units_todos.tasks_unit_id = tunits.id AND transaction_type IN (7,8) AND  Cast(switchoffset(todatetimeoffset(Cast(tbl_tasks_units_todo_transaction_log.transaction_date as datetime), '+00:00'), '{$timezoneOffset}') as date) >= '".$date."' GROUP BY tbl_tasks_units_todo_transaction_log.transaction_date,tbl_tasks_units_todos.tasks_unit_id  HAVING tbl_tasks_units_todo_transaction_log.transaction_date = MAX(tbl_tasks_units_todo_transaction_log.transaction_date))";
					}
				}

				if(isset($params['task_duedate']) && $params['task_duedate']!='All'){
					$params['TasksUnitsSearch']['task_duedate'] = date("Y-m-d", strtotime($params['TasksUnitsSearch']['task_duedate']));
					if (Yii::$app->db->driverName == 'mysql') {
						$where.= " AND DATE_FORMAT(CONVERT_TZ(CONCAT( instruct.`task_duedate` , ' ', STR_TO_DATE(instruct.`task_timedue` , '%l:%i %p' )),'+00:00','{$timezoneOffset}'), '%Y-%m-%d') = '{$params['task_duedate']}'";
					} else {
						$where.= " AND CAST(switchoffset(todatetimeoffset(Cast((CAST(instruct.task_duedate as varchar)  + ' ' + instruct.task_timedue) as datetime), '+00:00'), '{$timezoneOffset}') as date) = '".$params['task_duedate']."'";
					}
				}
			}
			else{
				$where.= " AND tunits.id IN (SELECT tbl_tasks_units_todos.tasks_unit_id FROM tbl_tasks_units_todos WHERE tbl_tasks_units_todos.tasks_unit_id = tunits.id AND tbl_tasks_units_todos.complete=0 $followup)";
			}
		}
		if(isset($params['statusFilter']) && !empty($params['statusFilter'])) {
			$statusWhere = "";
			$status = $params['statusFilter'];

			if(in_array(5,$status)) {
				$where.= " AND tunits.unit_status IN (0,1,2,3) AND task.task_closed = 0";
			} else if(in_array(6,$status)) {
				$where.= " AND tunits.unit_status IN (0,1,2,3) AND task.task_closed = 0";
				if (Yii::$app->db->driverName == 'mysql') {
					$where.= " AND CONCAT(instruct.task_duedate,' ', STR_TO_DATE(instruct.task_timedue, '%h:%i %p' )) < CASE WHEN task.task_complete_date!='0000-00-00 00:00:00' AND task.task_complete_date!='' THEN task.task_complete_date ELSE '" . date('Y-m-d H:i:s') . "' END";
		        } else {
		        	$where.= " AND CAST(CAST(instruct.task_duedate as varchar)+ ' ' +instruct.task_timedue as datetime) < CASE WHEN CAST(task.task_complete_date as varchar)!='0000-00-00 00:00:00' AND CAST(task.task_complete_date as varchar) IS NOT NULL THEN task.task_complete_date ELSE '" . date('Y-m-d H:i:s') . "' END";
		        }
		    }
			else if(in_array(7,$status) || in_array(8,$status) || in_array(9,$status)) {
				$status_pen = array_keys($status, 7);
				$status_index = array_pop($status_pen);
				$statusAr = $status;
				if($status_index!==false) {
					unset($statusAr[$status_index]);
					if(!empty($statusAr))
						$statusWhere = " OR (tunits.unit_status IN (".implode(',',$statusAr)."))";
				}
			} else {
				if(!empty($status))
					$where.= ' AND tunits.unit_status IN ('.implode(",",$status).')';

				if(in_array(0,$status)){
					//$where.= " AND tunits.id NOT IN (".(new TasksUnits)->getTaskPendingTaskTeamOverviewQuery($params['team_id'],$params['team_loc'],'','notStarted').")";
				}
			}

		} else {
            if(isset($params['completedtodo']) && $params['completedtodo'] =='1'){
					//and tbl_tasks_units.unit_status=4
                	//$compleToDoQuery= 'SELECT DISTINCT tbl_tasks_units.id FROM tbl_tasks_units_todos as t INNER JOIN tbl_tasks_units on tbl_tasks_units.id=t.tasks_unit_id INNER JOIN tbl_tasks on tbl_tasks.id=tbl_tasks_units.task_id INNER JOIN tbl_client_case on tbl_client_case.id=tbl_tasks.client_case_id WHERE tbl_tasks.task_cancel = 0 AND tbl_tasks.task_closed = 0 AND tbl_client_case.is_close=0 AND t.complete = 1 AND tbl_tasks_units.unit_assigned_to != 0 and tbl_tasks_units.team_id = '.$params['team_id'].' and tbl_tasks_units.team_loc = '.$params['team_loc'].' and tbl_tasks_units.unit_assigned_to = '.$params['unit_assigned_to'].' GROUP BY t.assigned,tbl_tasks_units.id';
					 $compleToDoQuery = "
        SELECT DISTINCT tbl_tasks_units.id
FROM tbl_tasks_units_todos as t
INNER JOIN tbl_tasks_units on tbl_tasks_units.id=t.tasks_unit_id
INNER JOIN tbl_tasks on tbl_tasks.id=tbl_tasks_units.task_id
INNER JOIN tbl_client_case on tbl_client_case.id=tbl_tasks.client_case_id
WHERE tbl_tasks.task_cancel = 0 AND tbl_tasks.task_closed = 0 AND tbl_client_case.is_close=0 AND t.complete = 1
and t.assigned!=0
and tbl_tasks_units.team_id = ".$params['team_id']."
and tbl_tasks_units.team_loc = ".$params['team_loc']."
and t.assigned =".$params['unit_assigned_to']."";
                	$where.= ' AND tunits.id IN ('.$compleToDoQuery.') ';
            }else{
				$where.= " AND tunits.unit_status != 4 AND task.task_closed = 0";
            }
		}
		//echo '<pre>';
        //  print_r($status);
        //  print_r($where);
		//die('nelson');
		/*multiple*/
		if ($params['TasksUnitsSearch']['task_id'] != null && is_array ($params['TasksUnitsSearch']['task_id'])) {
			if(!empty($params['TasksUnitsSearch']['task_id'])){
				foreach($params['TasksUnitsSearch']['task_id'] as $k=>$v){
					if($v=='All' || $v==''){ // || strpos($v,",") !== false
						unset($params['TasksUnitsSearch']['task_id']); break;
					}
				}
			}
		}
		if ($params['TasksUnitsSearch']['id'] != null && is_array ($params['TasksUnitsSearch']['id'])) {
			if(!empty($params['TasksUnitsSearch']['id'])){
				foreach($params['TasksUnitsSearch']['id'] as $k=>$v){
					if($v=='All' || $v==''){ // || strpos($v,",") !== false
						unset($params['TasksUnitsSearch']['id']); break;
					}
				}
			}
		}
		if ($params['TasksUnitsSearch']['unit_assigned_to'] != null && is_array ($params['TasksUnitsSearch']['unit_assigned_to'])) {
			if(!empty($params['TasksUnitsSearch']['unit_assigned_to'])){
				foreach($params['TasksUnitsSearch']['unit_assigned_to'] as $k=>$v){
					if($v=='All' || $v==''){ // || strpos($v,",") !== false
						unset($params['TasksUnitsSearch']['unit_assigned_to']); break;
					}
				}
			}
		}
		if ($params['TasksUnitsSearch']['workflow_task'] != null && is_array ($params['TasksUnitsSearch']['workflow_task'])) {
			if(!empty($params['TasksUnitsSearch']['workflow_task'])) {
				foreach($params['TasksUnitsSearch']['workflow_task'] as $k=>$v) {
					if($v=='All' || $v=='') { // || strpos($v,",") !== false
						unset($params['TasksUnitsSearch']['workflow_task']); break;
					}
				}
			}
		}
		if ($params['TasksUnitsSearch']['client_wise'] != null && is_array($params['TasksUnitsSearch']['client_wise'])) {
			if(!empty($params['TasksUnitsSearch']['client_wise'])) {
				foreach($params['TasksUnitsSearch']['client_wise'] as $k=>$v) {
					if($v=='All'){ // || strpos($v,",") !== false
						unset($params['TasksUnitsSearch']['client_wise']);
					}
				}
			}
		}
		if ($params['TasksUnitsSearch']['client_id'] != null && is_array($params['TasksUnitsSearch']['client_id'])) {
			if(!empty($params['TasksUnitsSearch']['client_id'])){
				foreach($params['TasksUnitsSearch']['client_id'] as $k=>$v){
					if($v=='All'){ // || strpos($v,",") !== false
						unset($params['TasksUnitsSearch']['client_id']);
					}
				}
			}
		}
		if ($params['TasksUnitsSearch']['client_case_id'] != null && is_array($params['TasksUnitsSearch']['client_case_id'])) {
			if(!empty($params['TasksUnitsSearch']['client_case_id'])){
				foreach($params['TasksUnitsSearch']['client_case_id'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($params['TasksUnitsSearch']['client_case_id']);
					}
				}
			}
		}
		/*multiple end */
		if(isset($params['TasksUnitsSearch']['task_id']) && $params['TasksUnitsSearch']['task_id']!="" && $params['TasksUnitsSearch']['task_id']!="All") {
            if(is_array($params['TasksUnitsSearch']['task_id']))
				$where.= ' AND tunits.task_id IN ('.implode(",",$params['TasksUnitsSearch']['task_id']).')';
            else
				$where.= ' AND tunits.task_id = '.$params['TasksUnitsSearch']['task_id'];
		}
        if(isset($params['TasksUnitsSearch']['id']) && $params['TasksUnitsSearch']['id']!="" && $params['TasksUnitsSearch']['id']!="All"){
			if(is_array($params['TasksUnitsSearch']['id']))
				$where.= ' AND tunits.id IN ('.implode(",",$params['TasksUnitsSearch']['id']).')';
            else
				$where.= ' AND tunits.id = '.$params['TasksUnitsSearch']['id'];
		}

        if(isset($params['TasksUnitsSearch']['unit_status']) && $params['TasksUnitsSearch']['unit_status']!="" && $params['TasksUnitsSearch']['unit_status']!="all")
        {
        	if(in_array($params['TasksUnitsSearch']['unit_status'],array(0,1,3,4))){
        		$where.= ' AND tunits.unit_status = '.$params['TasksUnitsSearch']['unit_status'];
        	}
        }
		if(isset($params['notin_unit_status']) && $params['notin_unit_status']!="" && $params['notin_unit_status']!="all")
		{
			if(in_array($params['notin_unit_status'],array(0,1,3,4))){
				$where.= ' AND tunits.unit_status != '.$params['notin_unit_status'];
			}
		}

        if(isset($params['TasksUnitsSearch']['unit_assigned_to']) && $params['TasksUnitsSearch']['unit_assigned_to']!="" && $params['TasksUnitsSearch']['unit_assigned_to']!="All"){
            /* if($params['TasksUnitsSearch']['unit_assigned_to'] == 'assignedonly') {
				$where.= ' AND tunits.unit_assigned_to != 0';
			} else if($params['TasksUnitsSearch']['unit_assigned_to'] == 'unassigned') {
				$where.= ' AND tunits.unit_assigned_to = 0';
			} else {
				$where.= ' AND tunits.unit_assigned_to = '.$params['TasksUnitsSearch']['unit_assigned_to'];
			} */

			$where_unitassignedto="";
			if(!empty($params['TasksUnitsSearch']['unit_assigned_to'])){
				foreach($params['TasksUnitsSearch']['unit_assigned_to'] as $k=>$v){
						if($where_unitassignedto==""){
							if($v == 'assignedonly'){
								$where_unitassignedto=" tunits.unit_assigned_to !=0";
							}else if($v == 'unassigned'){
								$where_unitassignedto=" tunits.unit_assigned_to =0";
							}else{
								$where_unitassignedto=" tunits.unit_assigned_to =".$v;
							}
						}else{
							if($v == 'assignedonly'){
								$where_unitassignedto .=" OR tunits.unit_assigned_to !=0";
							}else if($v == 'unassigned'){
								$where_unitassignedto .=" OR tunits.unit_assigned_to =0";
							}else{
								$where_unitassignedto .=" OR tunits.unit_assigned_to =".$v;
							}
						}
				}
			}
			if($where_unitassignedto!="") {
				$where.= " AND ($where_unitassignedto)";
			}
		}
        $where_workflow_task="";
        if(isset($params['TasksUnitsSearch']['workflow_task'])){
			if(!is_array($params['TasksUnitsSearch']['workflow_task'])){
				if(is_numeric($params['TasksUnitsSearch']['workflow_task']) && $params['TasksUnitsSearch']['workflow_task']!=""){
					$res = Servicetask::find()->select(['service_task'])->where(['id' => $selected_val])->asArray()->one();
					$where.= ' AND tunits.servicetask_id = '.$params['TasksUnitsSearch']['workflow_task'];
				}
			}else{
				if(!empty($params['TasksUnitsSearch']['workflow_task'])){
					foreach($params['TasksUnitsSearch']['workflow_task'] as $k=>$v){
						//$service_loc = explode('_',$v);
						if($where_workflow_task=="")
							$where_workflow_task =" CONCAT(tunits.servicetask_id,'_',tunits.team_loc) = '".$v."'";
						else
							$where_workflow_task .=" OR CONCAT(tunits.servicetask_id,'_',tunits.team_loc) = '".$v."'";
					}
					if($where_workflow_task!=""){
						$where.= " AND ($where_workflow_task)";
					}
				}
			}
		}
        /*if(isset($params['TasksUnitsSearch']['workflow_task']) && $params['TasksUnitsSearch']['workflow_task']!="" && $params['TasksUnitsSearch']['workflow_task']!="All"){
			if(is_numeric($params['TasksUnitsSearch']['workflow_task'])){
				$where.= ' AND tunits.servicetask_id = '.$params['TasksUnitsSearch']['workflow_task'];
			}else{
				$service_loc = explode('_',$params['TasksUnitsSearch']['workflow_task']);
				$where.= ' AND tunits.servicetask_id = '.$service_loc[0].' AND tunits.team_loc = '.$service_loc[1];
			}
		}*/

		 if(isset($params['TasksUnitsSearch']['client_wise']) && $params['TasksUnitsSearch']['client_wise']!="" && $params['TasksUnitsSearch']['client_wise']!="All"){
			//$client_data = explode('_',$params['TasksUnitsSearch']['client_wise']);
			//$where.= ' AND clientcase.client_id = '.$client_data[0].' AND task.client_case_id = '.$client_data[1];
			$where_client_wise="";
			if(!empty($params['TasksUnitsSearch']['client_wise'])){
					foreach($params['TasksUnitsSearch']['client_wise'] as $k=>$v){
						$client_data = explode('_',$v);
						if($where_client_wise=="")
							$where_client_wise =" task.client_case_id = $client_data[1]";
						else
							$where_client_wise .=" OR task.client_case_id = $client_data[1]";
					}
					if($where_client_wise!=""){
						$where.= " AND ($where_client_wise)";
					}
			}
		 }
		 if(isset($params['TasksUnitsSearch']['client_case_id']) && $params['TasksUnitsSearch']['client_case_id']!="" && $params['TasksUnitsSearch']['client_case_id']!="All"){
			$where_client_case_id = "";
			if(!empty($params['TasksUnitsSearch']['client_case_id'])){
					foreach($params['TasksUnitsSearch']['client_case_id'] as $k=>$v){
						if($where_client_case_id=="")
							$where_client_case_id =" task.client_case_id = $v";
						else
							$where_client_case_id .=" OR task.client_case_id = $v";
					}
					if($where_client_case_id!=""){
						$where.= " AND ($where_client_case_id)";
					}
			}
		 }
		 if(isset($params['TasksUnitsSearch']['client_id']) && $params['TasksUnitsSearch']['client_id']!="" && $params['TasksUnitsSearch']['client_id']!="All"){
			$where_client_id = "";
			if(!empty($params['TasksUnitsSearch']['client_id'])){
					foreach($params['TasksUnitsSearch']['client_id'] as $k=>$v){
						if($where_client_id == "")
							$where_client_id =" clientcase.client_id = $v";
						else
							$where_client_id .=" OR clientcase.client_id = $v";
					}
					if($where_client_id!=""){
						$where.= " AND ($where_client_id)";
					}
			}
		 }

		/*if(isset($params['TasksUnitsSearch']['task_priority']) && $params['TasksUnitsSearch']['task_priority']!="" && $params['TasksUnitsSearch']['task_priority']!="All"){
    		$where.= " AND project.priority like '".$params['TasksUnitsSearch']['task_priority']."'";
		}*/
		if ($params['TasksUnitsSearch']['task_priority'] != null && is_array($params['TasksUnitsSearch']['task_priority'])) {
			$where_task_priority="";
			if(!empty($params['TasksUnitsSearch']['task_priority'])) {
				foreach($params['TasksUnitsSearch']['task_priority'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($params['TasksUnitsSearch']['task_priority']);
						$where_task_priority="";
						break;
					} else {
						if($where_task_priority=="")
							$where_task_priority=" project.priority like '".$v."'";
						else
							$where_task_priority.=" OR project.priority like '".$v."'";
					}
				}
			}
			if($where_task_priority!="") {
				$where.= " AND ($where_task_priority)";
			}
			//$query->andFilterWhere(["or like","priority",$params['TeamSearch']['priority']]);
		}
    	if ($params['TasksUnitsSearch']['team_priority'] != null && is_array($params['TasksUnitsSearch']['team_priority'])) {
			$where_team_priority="";
			if(!empty($params['TasksUnitsSearch']['team_priority'])) {
				foreach($params['TasksUnitsSearch']['team_priority'] as $k=>$v) {
					if($v=='All') { //  || strpos($v,",") !== false
						unset($params['TasksUnitsSearch']['team_priority']);
						$where_team_priority="";
						break;
					} else {
						if($where_team_priority=="")
							$where_team_priority=" tpriority.tasks_priority_name like '".$v."'";
						else
							$where_team_priority.=" OR tpriority.tasks_priority_name like '".$v."'";
					}
				}
			}
			if($where_team_priority!="") {
				$where.= " AND ($where_team_priority)";
			}
			//$query->andFilterWhere(["or like","priority",$params['TeamSearch']['priority']]);
		}

		if(isset($params['TasksUnitsSearch']['task_duedate']) && $params['TasksUnitsSearch']['task_duedate']!=""){
			$task_duedate=explode("-",$params['TasksUnitsSearch']['task_duedate']);
			$task_duedate_start = explode("/", trim($task_duedate[0]));
            $task_duedate_end = explode("/", trim($task_duedate[1]));
            $task_duedate_s = $task_duedate_start[2] . "-" . $task_duedate_start[0] . "-" . $task_duedate_start[1].' 00:00:00';
            $task_duedate_e = $task_duedate_end[2] . "-" . $task_duedate_end[0] . "-" . $task_duedate_end[1].' 23:59:59';
			$where.=" AND A.task_date_time >= '$task_duedate_s' AND A.task_date_time  <= '$task_duedate_e' ";
        }

		if(in_array(7,$status)) {

			if (Yii::$app->db->driverName == 'mysql') {
				$subquery="Select unit_status
				From
				tbl_tasks_units as tu
				INNER JOIN tbl_task_instruct on tbl_task_instruct.id=tu.task_instruct_id
				INNER JOIN tbl_task_instruct_servicetask as service ON service.id = tu.task_instruct_servicetask_id
				Where service.sort_order = A.sort_order - 1 And tbl_task_instruct.task_id = A.task_id
				ORDER BY tu.id DESC LIMIT 1";
			}else{
				$subquery="Select TOP 1 unit_status From tbl_tasks_units as tu
		    INNER JOIN tbl_task_instruct ON tu.task_instruct_id = tbl_task_instruct.id
			INNER JOIN tbl_task_instruct_servicetask as service ON service.id = tu.task_instruct_servicetask_id
		    Where tu.sort_order = A.sort_order - 1 And tbl_task_instruct.task_id = A.task_id ORDER BY tu.id DESC";
			}
			$sqldatacount = 
			$sqldata = "Select B.* From (
				Select A.* From (
					SELECT $select FROM tbl_tasks_units as tunits $join
					WHERE tunits.team_id=".$params['team_id']." AND tunits.team_loc=".$params['team_loc']." AND tunits.unit_status=0 AND (tunits.sort_order not in(0,1)) AND $where
				) as A
				Where ($subquery) = 4
				Union All
				SELECT $select FROM tbl_tasks_units as tunits $join
				WHERE tunits.team_id=".$params['team_id']." AND tunits.team_loc=".$params['team_loc']." AND ((tunits.unit_status=0 AND tunits.sort_order=1) $statusWhere) AND $where
			) AS B";
    	} else if (in_array(9,$status)) {
			if(!in_array(4,$status)){
				$where .= " AND tunits.unit_status != 4 ";
			}
    		$where .= " AND tunits.unit_assigned_to=0 AND stask.sampling != 0";
    		if (Yii::$app->db->driverName == 'mysql') {
    			$sqldata = 'call tbl_tasks_units_sampling_data("'.$select.'","'.$join.'","'.$where.'");';
				//echo $sqldata;die;
    		} else {
    			//$sqldata = "tbl_tasks_units_sampling_data '$select','$join','$where'";
    			$select = str_replace("'","''",$select);
				$join = str_replace("'","''",$join);
				$where = str_replace("'","''",$where);
				$sqldata = "SET NOCOUNT ON; EXEC tbl_tasks_units_sampling_data '$select','$join','$where';";
    		}

    		$query_data = Yii::$app->db->createCommand($sqldata)->queryAll();
    		$dataProvider = new ArrayDataProvider([
				'allModels' => $query_data,
				'totalCount' => count($query_data),
				'pagination' => [
					'pageSize' => $pageSize,
				],
				'sort' => [
					'attributes' => ['unit_status','id','task_id','task_duedate','unit_assigned_to',
						'team_priority' => [
							'asc' => ['team_priority_order' => SORT_ASC],
							'desc' => ['team_priority_order' => SORT_DESC]
						],
						'task_priority' => [
							'asc' => ['project_order' => SORT_ASC],
							'desc' => ['project_order' => SORT_DESC]
						],
						/* 'client_wise' => [
							'asc' => [ 'client.client_name' => SORT_ASC,'clientcase.case_name' => SORT_ASC ],
							'desc' => [ 'client.client_name' => SORT_DESC,'clientcase.case_name' => SORT_ASC ],
						],	*/
						'workflow_task' => [
							'asc' => ['stask.service_task' => SORT_ASC],
							'desc' => ['stask.service_task' => SORT_DESC],
						],
						'task_duedate' => [
							'asc' => ['A.task_date_time' => SORT_ASC],
							'desc' => ['A.task_date_time' => SORT_DESC],
						],
						'unit_assigned_to' => [
							'asc' => ['assign_user.usr_first_name' => SORT_ASC],
							'desc' => ['assign_user.usr_first_name' => SORT_DESC],
						],
					]
				]
			]);
			$dataProvider->sort->enableMultiSort=true;
			/*IRT-67*/
			if(isset($params['grid_id']) && $params['grid_id']!=""){
				$grid_id=$params['grid_id'].'_'.Yii::$app->user->identity->id;
				$sql="SELECT * FROM tbl_dynagrid_dtl WHERE id IN (SELECT sort_id FROM tbl_dynagrid WHERE id='$grid_id')";
				$sort_data=Yii::$app->db->createCommand($sql)->queryOne();
				if(!empty($sort_data)){
						$defaultSort=json_decode($sort_data['data'],true);
				}
			}
			/*IRT-67*/
			if(!isset($params['sort']) && $defaultSort!=""){
				$dataProvider->sort->defaultOrder = $defaultSort;
			}
			if (isset($params['type']) && $params['type'] == 'all' && $params['data_mode'] == 'bulk_complete_tasks' && isset($params['data_mode'])) {
                $rawSqlQuery = "SELECT " . $select_unit . " FROM " . $fromtable . $join . " WHERE " . $where;
                $allResults = Yii::$app->db->createCommand($rawSqlQuery)->queryAll();
                return $allResults;
            }/*else if(isset($params['type']) && $params['type'] == 'bulkall' && isset($params['data_mode']) && $params['data_mode'] == 'bulkAllTasks'){
				$sqldatacount = "SELECT DISTINCT(tunits.id) as id FROM ".$fromtable.$join." WHERE ".$where;
				$allResults = Yii::$app->db->createCommand($sqldatacount)->queryAll();
				return $allResults;
        	}*/

            $this->load($params);
    		return $dataProvider;
    	} else {
    		$sqldata = "SELECT ".$select." FROM ".$fromtable.$join." WHERE ".$where.$order;
    		$sqldatacount = "SELECT ".$select." FROM ".$fromtable.$join." WHERE ".$where;
    	}
        if(isset($params['type']) && $params['type'] == 'all' && isset($params['data_mode']) && $params['data_mode'] == 'bulk_complete_tasks'){
            $rawSqlQuery = "SELECT " . $select_unit . " FROM " . $fromtable . $join . " WHERE " . $where;
            $allResults = Yii::$app->db->createCommand($rawSqlQuery)->queryAll();
            return $allResults;
        }else if(isset($params['type']) && $params['type'] == 'bulkall' && isset($params['data_mode']) && $params['data_mode'] == 'bulkAllTasks'){
            $sqldatacount = "SELECT tunits.id as id FROM ".$fromtable.$join." WHERE ".$where;
            $allResults = Yii::$app->db->createCommand($sqldatacount)->queryAll();
            return $allResults;
        }
       //echo $sqldata;die;
    	$newsql = "SELECT COUNT(B.id) FROM (".$sqldatacount.") as B";
		$count = Yii::$app->db->createCommand($newsql)->queryScalar();
		$dataProvider = new SqlDataProvider([
    			'sql' => $sqldata,
    			'totalCount' => $count,
                        'pagination' => [
                                        'pageSize' => $pageSize,
                        ],
				'sort' => [
					'attributes' => ['unit_status','id','task_id','task_duedate','unit_assigned_to',
					'team_priority' => [
							'asc' => ['team_priority_order' => SORT_ASC],
							'desc' => ['team_priority_order' => SORT_DESC]
						],
					 'task_priority' => [
							'asc' => ['project_order' => SORT_ASC],
							'desc' => ['project_order' => SORT_DESC]
						],
					  'client_case_id' => [
							'asc' => ['clientcase.case_name' => SORT_ASC],
							'desc' => ['clientcase.case_name' => SORT_DESC],
					  ],
					  'client_id' => [
							'asc' => ['client.client_name' => SORT_ASC],
							'desc' => ['client.client_name' => SORT_DESC],
					  ],
					  'workflow_task' => [
							'asc' => ['stask.service_task' => SORT_ASC],
							'desc' => ['stask.service_task' => SORT_DESC],
					  ],
					  'task_duedate' => [
							'asc' => ['task_date_time' => SORT_ASC],
							'desc' => ['task_date_time' => SORT_DESC],
					  ],
					  'unit_assigned_to' => [
							'asc' => ['assign_user.usr_first_name' => SORT_ASC],
							'desc' => ['assign_user.usr_first_name' => SORT_DESC],
					  ],

					]
				]
    	]);
    	$dataProvider->sort->enableMultiSort=true;
		if(!isset($params['sort']) && $defaultSort!=""){
			$dataProvider->sort->defaultOrder=$defaultSort;
		}
        //       echo "<pre>"; print_r($dataProvider);die;

		if($is_unit_all){$params['TasksUnitsSearch']['unit_status']='all';}
		if(isset($params['TasksUnitsSearch']['task_duedate']) && $params['TasksUnitsSearch']['task_duedate']!=""){

			$this->task_duedate = $task_duedate_last;
		}
        $this->load($params);

      //  echo "<pre>",print_r($dataProvider);    die;
     	if (!$this->validate()) {
            return $dataProvider;
        }
    	return $dataProvider;
	}


	public function searchFilter($params){
			//echo "here";
			//print_r($params);
			//die;
		/*$dataProvider = array();

		$query = TasksUnits::find()->select(['tbl_tasks_units.id','tbl_tasks_units.task_instruct_servicetask_id','tbl_tasks_units.task_id','tbl_tasks_units.unit_assigned_to'])->where('tbl_tasks_units.unit_status != 4')
		->innerJoinWith(['taskInstructServicetask'=>function(\yii\db\Activequery $query)use($params){
			$query->where(['tbl_task_instruct_servicetask.team_id'=>$params['team_id'],'tbl_task_instruct_servicetask.team_loc'=>$params['team_loc']])
			->innerJoinWith(['taskInstruct'=>function(\yii\db\Activequery $query){
					$query->where(['tbl_task_instruct.isactive'=>1])
					->joinWith('taskPriority');
				 }],false)
			->innerJoinWith('servicetask')->innerJoinWith('teamLoc'); }])
		->innerJoinWith(['tasks'=>function(\yii\db\Activequery $query){
				$query->where(['tbl_tasks.task_closed'=>0,'tbl_tasks.task_cancel'=>0])
				->joinwith(['clientCase'=>function(\yii\db\Activequery $query){
						$query->where(['tbl_client_case.is_close'=>0]);
					}],false)
				->joinwith('client',false)
				->joinwith('teamPriority',false);
			}])
		->joinWith('assignedUser',false); */
$teamtaskparams=$params['params'];
		$dataProvider = array();
		//echo "<pre>"; print_r($params); //exit;
		$is_unit_all=false;
		if(isset($teamtaskparams['TasksUnitsSearch']['unit_status']) && $teamtaskparams['TasksUnitsSearch']['unit_status']== 'all'){
			$is_unit_all=true;
			//unset($params['TasksUnitsSearch']['unit_status']);
		}
		//echo "<pre>"; print_r($params); exit;
		$timezoneOffset = (new Options)->getOffsetOfCurrenttimeZone();

		/*$select= "tunits.id,tunits.task_id,tunits.unit_status,tunits.unit_assigned_to,servicetask.sort_order,servicetask.team_id,servicetask.team_loc,task.task_status,(SELECT CASE WHEN (team_priority IS NULL OR team_priority =0)THEN ((SELECT count( * )FROM tbl_priority_team) +1) ELSE team.priority_order END AS final_priority FROM tbl_tasks AS task_inner LEFT JOIN tbl_priority_team as team ON task_inner.team_priority = team.id where task_inner.id = task.id) as team_priority_order,project.priority,project.priority_order as project_order,project.id as project_priority_id,stask.service_task,loc.team_location_name,clientcase.case_name,client.client_name,tpriority.tasks_priority_name,task.task_closed,task.task_cancel,assign_user.usr_first_name,assign_user.usr_lastname,instruct.task_duedate,instruct.task_timedue,servicetask.servicetask_id,task.created";

		$fromtable = "tbl_tasks_units as tunits ";
		$join = "INNER JOIN tbl_task_instruct_servicetask as servicetask ON tunits.task_instruct_servicetask_id = servicetask.id
		INNER JOIN tbl_task_instruct as instruct ON servicetask.task_instruct_id = instruct.id
		LEFT JOIN tbl_priority_project as project ON project.id = instruct.task_priority
		INNER JOIN tbl_servicetask as stask ON servicetask.servicetask_id = stask.id
		INNER JOIN tbl_teamlocation_master as loc ON servicetask.team_loc = loc.id
		INNER JOIN tbl_tasks as task ON tunits.task_id = task.id
		LEFT JOIN tbl_client_case as clientcase ON clientcase.id = task.client_case_id
		LEFT JOIN tbl_client as client ON client.id = task.client_id
		LEFT JOIN tbl_priority_team as tpriority ON tpriority.id = task.team_priority
		LEFT JOIN tbl_user as assign_user ON assign_user.id = tunits.unit_assigned_to";
		$where = "servicetask.team_id = '".$params['team_id']."' AND servicetask.team_loc = '".$params['team_loc']."' AND instruct.isactive = 1 AND task.task_closed = 0 AND task.task_cancel = 0 AND clientcase.is_close = 0";
		*/
    if ($teamtaskparams['TasksUnitsSearch']['task_id'] != null && is_array ($teamtaskparams['TasksUnitsSearch']['task_id'])) {
        if(!empty($teamtaskparams['TasksUnitsSearch']['task_id'])) {
        foreach($teamtaskparams['TasksUnitsSearch']['task_id'] as $k=>$v) {
          if($v == 'All'){ // || strpos($v,",") !== false
            unset($teamtaskparams['TasksUnitsSearch']['task_id']); break;
          }
        }

      }
    }
    if ($teamtaskparams['TasksUnitsSearch']['unit_assigned_to'] != null && is_array ($teamtaskparams['TasksUnitsSearch']['unit_assigned_to'])) {
        if(!empty($teamtaskparams['TasksUnitsSearch']['unit_assigned_to'])) {
        foreach($teamtaskparams['TasksUnitsSearch']['unit_assigned_to'] as $k=>$v) {
          if($v == 'All'){ // || strpos($v,",") !== false
            unset($teamtaskparams['TasksUnitsSearch']['unit_assigned_to']); break;
          }
        }

      }
    }
    if ($teamtaskparams['TasksUnitsSearch']['workflow_task'] != null && is_array ($teamtaskparams['TasksUnitsSearch']['workflow_task'])) {
				if(!empty($teamtaskparams['TasksUnitsSearch']['workflow_task'])) {
				foreach($teamtaskparams['TasksUnitsSearch']['workflow_task'] as $k=>$v) {
					if($v == 'All'){ // || strpos($v,",") !== false
						unset($teamtaskparams['TasksUnitsSearch']['workflow_task']); break;
					}
				}

			}
		}

    if ($teamtaskparams['TasksUnitsSearch']['client_id'] != null && is_array ($teamtaskparams['TasksUnitsSearch']['client_id'])) {
				if(!empty($teamtaskparams['TasksUnitsSearch']['client_id'])) {
				foreach($teamtaskparams['TasksUnitsSearch']['client_id'] as $k=>$v) {
					if($v == 'All'){ // || strpos($v,",") !== false
						unset($teamtaskparams['TasksUnitsSearch']['client_id']); break;
					}
				}

			}
		}

		if (Yii::$app->db->driverName == 'mysql') {
			$data_query = "DATE_FORMAT(CONVERT_TZ(CONCAT(instruct.`task_duedate` , ' ', STR_TO_DATE(instruct.`task_timedue` , '%l:%i %p' )),'+00:00','{$timezoneOffset}'), '%Y-%m-%d %H:%i')";
		}else{
			$data_query = "CAST(switchoffset(todatetimeoffset(Cast((CAST(instruct.task_duedate as varchar)  + ' ' + instruct.task_timedue) as datetime), '+00:00'), '{$timezoneOffset}') as datetime)";
		}
		$select= "tunits.id,instruct.task_id,tunits.unit_status,tunits.unit_assigned_to,tunits.sort_order,tunits.team_id,tunits.team_loc,task.task_status,(SELECT CASE WHEN (team_priority IS NULL OR team_priority = 0)THEN ((SELECT count( * )FROM tbl_priority_team) +1) ELSE team.priority_order END AS final_priority FROM tbl_tasks AS task_inner LEFT JOIN tbl_priority_team as team ON task_inner.team_priority = team.id where task_inner.id = task.id) as team_priority_order,project.priority,project.priority_order as project_order,project.id as project_priority_id,stask.service_task,loc.team_location_name,clientcase.case_name,client.client_name,tpriority.tasks_priority_name,task.task_closed,task.task_cancel,assign_user.usr_first_name,assign_user.usr_lastname,instruct.task_duedate,instruct.task_timedue,tunits.servicetask_id,task.created,$data_query as task_date_time";
		$fromtable = "tbl_tasks_units as tunits";
		//INNER JOIN tbl_task_instruct_servicetask as servicetask ON tunits.task_instruct_servicetask_id = servicetask.id
		$join = "
		INNER JOIN tbl_task_instruct as instruct ON tunits.task_instruct_id = instruct.id
		LEFT JOIN tbl_priority_project as project ON project.id = instruct.task_priority
		INNER JOIN tbl_servicetask as stask ON tunits.servicetask_id = stask.id
		INNER JOIN tbl_teamlocation_master as loc ON tunits.team_loc = loc.id
		INNER JOIN tbl_tasks as task ON instruct.task_id = task.id
		LEFT JOIN tbl_client_case as clientcase ON clientcase.id = task.client_case_id
		LEFT JOIN tbl_client as client ON client.id = clientcase.client_id
		LEFT JOIN tbl_tasks_teams on tbl_tasks_teams.task_id=tunits.task_id and tbl_tasks_teams.team_id=tunits.team_id and tbl_tasks_teams.team_loc=tunits.team_loc
		LEFT JOIN tbl_priority_team as tpriority ON tpriority.id = tbl_tasks_teams.team_loc_prority
		LEFT JOIN tbl_user as assign_user ON assign_user.id = tunits.unit_assigned_to";
		$where = "tunits.team_id = '".$params['team_id']."' AND tunits.team_loc = '".$params['team_loc']."' AND instruct.isactive = 1 AND task.task_closed = 0 AND task.task_cancel = 0 AND clientcase.is_close = 0";
		$order = "";
//		echo $where;die;
		if(!isset($params['sort'])){
			$settings_info = Settings::find()->where("field = 'project_sort'")->one();
			$fieldvalue = $settings_info->fieldvalue;
			/*IRT 26*/
			$optionsproject_sort_display=Options::find()->select('project_sort_display')->where(['user_id'=>Yii::$app->user->identity->id])->andWhere('project_sort_display IS NOT NULL')->one()->project_sort_display;

			if(isset($optionsproject_sort_display) && in_array($optionsproject_sort_display, array(0,1,2,3))) {
				$fieldvalue =$optionsproject_sort_display;
			}
			/*IRT 26*/
			if ($fieldvalue == '0') {
				$order.= " order by project.priority_order ASC,instruct.task_duedate DESC,instruct.task_timedue DESC";
			} else if ($fieldvalue == '1') {
				$order.= " order by instruct.task_duedate DESC,instruct.task_timedue DESC";
			} else if ($fieldvalue == '2') {
				$order.= " order by tunits.task_id DESC";
			} else if($fieldvalue == '3') {
				$order.= " order by team_priority_order ASC,project.priority_order ASC,instruct.task_duedate DESC,instruct.task_timedue DESC";
			}
		}else{
			$order = "";
		}

		if(isset($params['task_id']) && $params['task_id']!='' && $params['task_id'] != 0){
			$where.= '  AND tunits.task_id = '.$params['task_id'];
		}

		if(isset($params['tasks_units_id']) && $params['tasks_units_id']!='' && $params['tasks_units_id'] != 0){
			$where.= ' AND tunits.id = '.$params['tasks_units_id'];
		}

		if(isset($params['servicetask_id']) && $params['servicetask_id']!='' && $params['servicetask_id'] != 0){
			$where.= ' AND servicetask.servicetask_id IN ('.$params['servicetask_id'].')';
		}

		if(isset($params['task_duedate']) && $params['task_duedate']!='All'){
			$params['task_duedate'] = date("Y-m-d", strtotime($params['task_duedate']));
			if (Yii::$app->db->driverName == 'mysql') {
				$where.= " AND DATE_FORMAT( CONVERT_TZ(CONCAT(instruct.`task_duedate` , ' ', STR_TO_DATE(instruct.`task_timedue` , '%l:%i %p' )),'+00:00','{$timezoneOffset}'), '%Y-%m-%d') = '{$params['task_duedate']}'";
			} else {
				$where.= " AND CAST(switchoffset(todatetimeoffset(Cast((CAST(instruct.task_duedate as varchar)  + ' ' + instruct.task_timedue) as datetime), '+00:00'), '{$timezoneOffset}') as date) = '".$params['task_duedate']."'";
			}
			//$query->andWhere($datesql);


		}

		if(isset($params['task_priority']) && $params['task_priority']!=''){
			$where.= ' AND project.priority like "'.$params['task_priority'].'"';
		}

		if(isset($params['team_priority']) && $params['team_priority']!=''){
			$where.= ' AND tpriority.tasks_priority_name = "'.$params['team_priority'].'"';
		}

		$currdate=time();
		if(isset($params['due'])){
			if($params['due']=='past'){
				if (Yii::$app->db->driverName == 'mysql') {
					$where.= ' AND instruct.task_duedate < "' . date('Y-m-d H:i:s', time()) . '"';
				} else {
					$where.= " AND Cast((CAST(instruct.task_duedate as varchar)  + ' ' + instruct.task_timedue) as datetime) < '" . date('Y-m-d H:i:s', time()) . "'";
				}
				$where.= " AND task.task_status !=4";
			}
			if($params['due']=='notpastdue'){
				$where.= " AND task.task_closed = 0 AND task.task_cancel = 0";
			}
		}

		if(isset($params['client_case']) && $params['client_case'] != ''){
			if(is_numeric($params['client_case'])){
				$where.= ' AND task.client_case_id IN ('.$params['client_case'].')';
			}else{
				$clientcase = explode('/',$params['client_case']);
				//print_r($clientcase); exit;
				if(!empty($clientcase)){
					$where.= ' AND client.client_name = "'.trim($clientcase[0]).'" AND clientcase.case_name = "'.trim($clientcase[1]).'"';
				}
			}
		}

		if(isset($params['unit_assigned_to']) && $params['unit_assigned_to']!='' && $params['statusFilter'] !=8 ){

			if($params['unit_assigned_to'] == 'assignedonly'){
				$where.= ' AND tunits.unit_assigned_to != 0';
			}else{
				$where.= ' AND tunits.unit_assigned_to = '.$params['unit_assigned_to'];
			}
			if(isset($params['task_duedate']) && $params['task_duedate']!='All'){
				$teamtaskparams['TasksUnitsSearch']['task_duedate'] = date("Y-m-d", strtotime($teamtaskparams['TasksUnitsSearch']['task_duedate']));
				if (Yii::$app->db->driverName == 'mysql') {
					$where.=" AND DATE_FORMAT(CONVERT_TZ(CONCAT(insturct.`task_duedate` , ' ', STR_TO_DATE(insturct.`task_timedue` , '%l:%i %p' )),'+00:00','{$timezoneOffset}'), '%Y-%m-%d') = '{$params['task_duedate']}'";
				} else {
					$where.= " AND CAST(switchoffset(todatetimeoffset(Cast((CAST(insturct.task_duedate as varchar)  + ' ' + insturct.task_timedue) as datetime), '+00:00'), '{$timezoneOffset}') as date) = '".$params['task_duedate']."'";
				}
			}
		}
		$status = 0;
		//if(isset($teamtaskparams['TasksUnitsSearch']['unit_status']) && trim(urldecode($teamtaskparams['TasksUnitsSearch']['unit_status']))!= ''){
			
		if(isset($teamtaskparams['TasksUnitsSearch']['unit_status'])) {
			if(is_array($teamtaskparams['TasksUnitsSearch']['unit_status']) && !empty($teamtaskparams['TasksUnitsSearch']['unit_status'])) {
				$params['statusFilter'] = $teamtaskparams['TasksUnitsSearch']['unit_status'];
			} else {
				if(trim(urldecode($teamtaskparams['TasksUnitsSearch']['unit_status']))!= '') {
					$params['statusFilter'] = $teamtaskparams['TasksUnitsSearch']['unit_status'];
				}
				//unset($params['TasksUnitsSearch']['unit_status']);
			}
		}
		if(isset($params['statusFilter']) && $params['statusFilter'] == 8){
			$followup = "";
			if(isset($params['followcat_id']) && $params['followcat_id'] != '' && $params['followcat_id']!= 0 ){
				$followup = "AND tbl_tasks_units_todos.todo_cat_id = {$params['followcat_id']}";
			}
			if(isset($params['source']) && $params['source'] == 'todofollowup' && isset($params['unit_assigned_to']) && $params['unit_assigned_to'] != ''){
		//		$where.= " AND (tunits.id IN (SELECT tbl_tasks_units_todos.tasks_unit_id FROM tbl_tasks_units_todos WHERE tbl_tasks_units_todos.tasks_unit_id = tunits.id AND tbl_tasks_units_todos.complete=0 AND tbl_tasks_units_todos.assigned={$params['unit_assigned_to']} $followup) AND (tunits.unit_status!=4 AND task.task_closed=0))";
			}
			else if(isset($params['unit_assigned_to']) && $params['unit_assigned_to'] != ''){
			//	$where.= " AND (tunits.id IN (SELECT tbl_tasks_units_todos.tasks_unit_id FROM tbl_tasks_units_todos WHERE tbl_tasks_units_todos.tasks_unit_id = tunits.id AND tbl_tasks_units_todos.complete=0 AND tbl_tasks_units_todos.assigned={$params['unit_assigned_to']} $followup) OR (tunits.unit_assigned_to={$params['unit_assigned_to']} AND tunits.unit_status!=4 AND task.task_closed=0))";

				if(isset($params['task_duedate']) && $params['task_duedate']!='All'){
					$teamtaskparams['TasksUnitsSearch']['task_duedate'] = date("Y-m-d", strtotime($teamtaskparams['TasksUnitsSearch']['task_duedate']));

					if (Yii::$app->db->driverName == 'mysql') {
			//			$where.= " AND DATE_FORMAT(CONVERT_TZ(CONCAT( instruct.`task_duedate` , ' ', STR_TO_DATE(instruct.`task_timedue` , '%l:%i %p' )),'+00:00','{$timezoneOffset}'), '%Y-%m-%d') = '{$params['task_duedate']}'";
					} else {
			//			$where.= " AND CAST(switchoffset(todatetimeoffset(Cast((CAST(instruct.task_duedate as varchar)  + ' ' + instruct.task_timedue) as datetime), '+00:00'), '{$timezoneOffset}') as date) = '".$params['task_duedate']."'";
					}
				}

			}
			else{
		//		$where.= " AND tunits.id IN (SELECT tbl_tasks_units_todos.tasks_unit_id FROM tbl_tasks_units_todos WHERE tbl_tasks_units_todos.tasks_unit_id = tunits.id AND tbl_tasks_units_todos.complete=0 $followup)";
			}
		}


		if(isset($params['statusFilter']) && $params['statusFilter'] != ''){
			$status = $params['statusFilter'];
			if($status == 5) {
		//		$where.= " AND tunits.unit_status IN (0,1,2,3) AND task.task_closed = 0";
			}else if($status == 6){
		//		$where.= " AND tunits.unit_status IN (0,1,2,3) AND task.task_closed = 0";
				if (Yii::$app->db->driverName == 'mysql'){
		//			$where.= " AND CONCAT(instruct.task_duedate,' ', STR_TO_DATE(instruct.task_timedue, '%h:%i %p' )) < CASE WHEN task.task_complete_date!='0000-00-00 00:00:00' AND task.task_complete_date!='' THEN task.task_complete_date ELSE '" . date('Y-m-d H:i:s') . "' END";
				} else {
		//			$where.= " AND CAST(CAST(instruc.task_duedate as varchar)+ ' ' +instruct.task_timedue as datetime) < CASE WHEN CAST(task.task_complete_date as varchar)!='0000-00-00 00:00:00' AND CAST(task.task_complete_date as varchar) IS NOT NULL THEN task.task_complete_date ELSE '" . date('Y-m-d H:i:s') . "' END";
				}
				//$query->andWhere($datesql);
			}
			else if($status == 7 || $status == 8 || $status == 9){

			}else{
		//		$where.= ' AND tunits.unit_status = TasksUnitsSearch.$status;
			}
		}else{
			$where.= " AND tunits.unit_status != 4 AND task.task_closed = 0";
		}


		if(isset($teamtaskparams['TasksUnitsSearch']['task_id']) && $teamtaskparams['TasksUnitsSearch']['task_id']!="" && $teamtaskparams['TasksUnitsSearch']['task_id']!="All")
			//$where.= ' AND tunits.task_id IN ('.implode(",",$teamtaskparams['TasksUnitsSearch']['task_id']).')';

		if(isset($teamtaskparams['TasksUnitsSearch']['id']) && $teamtaskparams['TasksUnitsSearch']['id']!="" && $teamtaskparams['TasksUnitsSearch']['id']!="All")
	//		$where.= ' AND tunits.id IN ('.implode(",",$teamtaskparams['TasksUnitsSearch']['id']).')';

		if(isset($teamtaskparams['TasksUnitsSearch']['unit_status']) && $teamtaskparams['TasksUnitsSearch']['unit_status']!="" && $teamtaskparams['TasksUnitsSearch']['unit_status']!="all")
		{
			if(in_array($teamtaskparams['TasksUnitsSearch']['unit_status'],array(0,1,3,4))){
		//		$where.= ' AND tunits.unit_status = '.$teamtaskparams['TasksUnitsSearch']['unit_status'];
			}
		}

		if(isset($teamtaskparams['TasksUnitsSearch']['unit_assigned_to']) && $teamtaskparams['TasksUnitsSearch']['unit_assigned_to']!="" && $teamtaskparams['TasksUnitsSearch']['unit_assigned_to']!="All"){
			//$query->andFilterWhere(['tbl_tasks_units.unit_assigned_to' => $params['TasksUnitsSearch']['unit_assigned_to']]);
			if($teamtaskparams['TasksUnitsSearch']['unit_assigned_to'] == 'assignedonly'){
		//		$where.= ' AND tunits.unit_assigned_to != 0';
			}else if($teamtaskparams['TasksUnitsSearch']['unit_assigned_to'] == 'unassigned'){
		//		$where.= ' AND tunits.unit_assigned_to = 0';
			}else{
		//		$where.= ' AND tunits.unit_assigned_to = '.$teamtaskparams['TasksUnitsSearch']['unit_assigned_to'];
			}
		}

		if(isset($teamtaskparams['TasksUnitsSearch']['workflow_task']) && $teamtaskparams['TasksUnitsSearch']['workflow_task']!="" && $teamtaskparams['TasksUnitsSearch']['workflow_task']!="All"){
			if(is_numeric($teamtaskparams['TasksUnitsSearch']['workflow_task'])){
			//	$where.= ' AND tbl_task_instruct_servicetask.servicetask_id = '.$teamtaskparams['TasksUnitsSearch']['workflow_task'];
			}else{
				$service_loc = explode('_',implode(",",$teamtaskparams['TasksUnitsSearch']['workflow_task']));
			//	$where.= ' AND tbl_task_instruct_servicetask.servicetask_id = '.$service_loc[0].' AND tbl_task_instruct_servicetask.team_loc = '.$service_loc[1];
			 }
		}

		if(isset($teamtaskparams['TasksUnitsSearch']['client_wise']) && $teamtaskparams['TasksUnitsSearch']['client_wise']!="" && $teamtaskparams['TasksUnitsSearch']['client_wise']!="All"){
			$client_data = explode('_',$teamtaskparams['TasksUnitsSearch']['client_wise']);
		//	$where.= ' AND task.client_id = '.$client_data[0].' AND task.client_case_id = '.$client_data[1];
		}

		if(isset($teamtaskparams['TasksUnitsSearch']['task_priority']) && $teamtaskparams['TasksUnitsSearch']['task_priority']!="" && $teamtaskparams['TasksUnitsSearch']['task_priority']!="All"){
		//	$where.= ' AND project.priority like "'.$teamtaskparams['TasksUnitsSearch']['task_priority'].'"';
		}

    if(isset($teamtaskparams['TasksUnitsSearch']['client_id']) && !empty($teamtaskparams['TasksUnitsSearch']['client_id'])){
    //  if($params['field']!="client_id")
      //  $where.= ' AND client.id  IN ('.implode(',',$teamtaskparams['TasksUnitsSearch']['client_id']).')';

      // if($params['field']!='client_id')
       //	$query->andWhere('tbl_client.id IN ('.implode(',',$teamprojectparams['TeamSearch']['client_id']).')');
    }
		if(isset($teamtaskparams['TasksUnitsSearch']['team_priority']) && $teamtaskparams['TasksUnitsSearch']['team_priority']!="" && $teamtaskparams['TasksUnitsSearch']['team_priority']!="All")
		//	$where.= ' AND tpriority.tasks_priority_name like "'.$teamtaskparams['TasksUnitsSearch']['team_priority'].'"';

		if(isset($teamtaskparams['TasksUnitsSearch']['task_duedate']) && $teamtaskparams['TasksUnitsSearch']['task_duedate']!=""){
			$timezoneOffset = (new Options)->getOffsetOfCurrenttimeZone();
			$teamtaskparams['TasksUnitsSearch']['task_duedate'] = date("Y-m-d", strtotime($teamtaskparams['TasksUnitsSearch']['task_duedate']));
			if (Yii::$app->db->driverName == 'mysql') {
	//			$where.=" AND DATE_FORMAT(CONVERT_TZ(CONCAT(instruct.`task_duedate` , ' ', STR_TO_DATE(instruct.`task_timedue` , '%l:%i %p' )),'+00:00','{$timezoneOffset}'), '%Y-%m-%d') = '{$teamtaskparams['TasksUnitsSearch']['task_duedate']}'";
			} else {
		//		$where.=" AND CAST(switchoffset(todatetimeoffset(Cast((CAST(instruct.task_duedate as varchar)  + ' ' + instruct.task_timedue) as datetime), '+00:00'), '{$timezoneOffset}') as date) = '".$teamtaskparams['TasksUnitsSearch']['task_duedate']."'";
			}
		}


		if($params['field']=='task_id'){
			$select= "instruct.task_id";
			if(isset($params['q']) && $params['q']!=""){
				$where .= " AND tunits.task_id like '".$params['q']."%'";
			}
		}
		if($params['field']=='id'){
			$select= "tunits.id";
			if(isset($params['q']) && $params['q']!=""){
				$where .= " AND tunits.id like '".$params['q']."%'";
			}
		}
		if($params['field']=='unit_assigned_to'){
			$select= "tunits.unit_assigned_to";
			if(isset($params['q']) && $params['q']!=""){
				$where .= " AND CONCAT(assign_user.usr_first_name,' ',assign_user.usr_lastname) like '%".$params['q']."%'";
			}

		}
		if($params['field']=='workflow_task'){
			$select= "tunits.servicetask_id";
			if(isset($params['q']) && $params['q']!=""){
				$where .= " AND tunits.servicetask_id IN (SELECT id FROM tbl_servicetask WHERE service_task like '".$params['q']."%') ";
			}
		//	$select= "stask.service_task";
		}
		if($params['field']=='client_wise'){
			$select= "clientcase.id,clientcase.client_id,clientcase.case_name,client.client_name";
		}
		if($params['field']=='client_case_id'){
			$select= "clientcase.id,clientcase.case_name";
      if(isset($teamtaskparams['TasksUnitsSearch']['client_id']) && !empty($teamtaskparams['TasksUnitsSearch']['client_id'])){
          $where.= ' AND client.id  IN ('.implode(',',$teamtaskparams['TasksUnitsSearch']['client_id']).')';
      }
		}
		if($params['field']=='client_id'){
			$select  = "clientcase.client_id,client.client_name";
		}
		if($params['field']=='task_priority'){
			$select= "project.priority";
		}
		if($params['field']=='team_priority'){
			$select= "tpriority.tasks_priority_name";
		}
		if($status == 7) {
			if (Yii::$app->db->driverName == 'mysql') {
				$subquery="Select unit_status From tbl_tasks_units as tu
				INNER JOIN tbl_task_instruct on tbl_task_instruct.id=tu.task_instruct_id
				INNER JOIN tbl_task_instruct_servicetask as service ON service.id = tu.task_instruct_servicetask_id
				Where service.sort_order = A.sort_order - 1 And tbl_task_instruct.task_id = A.task_id
				ORDER BY tu.id DESC LIMIT 1";
			}else{
				$subquery="Select TOP 1 unit_status From tbl_tasks_units as tu
		    INNER JOIN tbl_task_instruct ON tu.task_instruct_id = tbl_task_instruct.id
			INNER JOIN tbl_task_instruct_servicetask as service ON service.id = tu.task_instruct_servicetask_id
		    Where tu.sort_order = A.sort_order - 1 And tbl_task_instruct.task_id = A.task_id ORDER BY tu.id DESC";
			}
			$sqldata = "Select B.* From (Select A.* From (SELECT $select FROM tbl_tasks_units as tunits $join WHERE tunits.team_id=".$params['team_id']." AND tunits.team_loc=".$params['team_loc']." AND tunits.unit_status=0 AND (tunits.sort_order not in(0,1)) AND $where) as A
			Where ($subquery) = 4
			Union All
			SELECT $select FROM tbl_tasks_units as tunits $join WHERE tunits.team_id=".$params['team_id']." AND tunits.team_loc=".$params['team_loc']." AND tunits.unit_status=0 AND tunits.sort_order=1 AND $where) AS B";
		} else if ($status == 9) {
			$where .= " AND tunits.unit_assigned_to=0 AND stask.sampling != 0";
			if (Yii::$app->db->driverName == 'mysql') {
				$sqldata = 'call tbl_tasks_units_sampling_data("'.$select.'","'.$join.'","'.$where.'");';
			} else {
				$sqldata = "tbl_tasks_units_sampling_data '$select','$join','$where'";
			}
		} else {
			$sqldata = "SELECT ".$select." FROM ".$fromtable.$join." WHERE ".$where;
		}
		if($params['field']=='task_id'){
			$sqldata.=' group by instruct.task_id';
    		$dataProvider = ArrayHelper::map(Tasks::find()->where('id IN ('.$sqldata.')')->all(),'id','id');
    	}

    	if($params['field']=='id'){
			$dataProvider = ArrayHelper::map(TasksUnits::find()->where('id IN ('.$sqldata.')')->all(),'id','id');
    	}
    	if($params['field']=='unit_assigned_to'){
			/*
			 * Entire Application  Change for Removed Sys admin User
			 * Code Change 	: 12/6/2016
			 * Change By 	: Nelson Rana
			*/
			$sqldata .= ' AND assign_user.id != 1';
			// Code Ends
			$dataProvider = ArrayHelper::map(User::find()->select(["id","CONCAT(usr_first_name, ' ' ,usr_lastname) as usr_first_name"])->where( 'id in ('.$sqldata.')')->orderby('tbl_user.usr_first_name')->all(),'id','usr_first_name');
    		$dataProvider = array('assignedonly'=>'All Assigned','unassigned'=>'All UnAssigned') + $dataProvider;
    	}

    	if($params['field']=='workflow_task')
    	{
			$sqldata.=' group by tunits.servicetask_id';
    		$service_query = TasksUnits::find()
			->select(["tbl_tasks_units.team_loc","tbl_tasks_units.servicetask_id","tbl_tasks_units.teamservice_id"])
			->innerJoinWith(['teamservice'=>function(\yii\db\Activequery $query)use($params){
				$query->select(['tbl_teamservice.id','tbl_teamservice.service_name']);
			}])
			->innerJoinWith(['servicetask'=>function(\yii\db\Activequery $query)use($params){
				$query->select(['tbl_servicetask.id','tbl_servicetask.service_task']);
			}])
			->innerJoinWith(['teamLoc'=>function(\yii\db\ActiveQuery $query)use($params){
					$query->select(['tbl_teamlocation_master.id','tbl_teamlocation_master.team_location_name']);
			}])->where(['team_id'=>$params['team_id'],'team_loc'=>$params['team_loc']])->andWhere('tbl_tasks_units.servicetask_id in ('.$sqldata.')');
			$service_query->groupBy(["tbl_tasks_units.team_loc","tbl_tasks_units.servicetask_id","tbl_tasks_units.teamservice_id"]);
			if(isset($params['q']) && $params['q']!=""){
				$service_query->andFilterWhere(['like', 'tbl_servicetask.service_task', $params['q']]);
			}
			$ser_ids=[];
			$service_data = $service_query->asArray()->all();
			if(!empty($service_data)){
				foreach($service_data as $service => $data){
					//if(!in_array($data['servicetask_id'],$ser_ids)){
					$dataProvider[$data['servicetask_id'].'_'.$data['team_loc']] = $data['teamservice']['service_name'] .' - ' .$data['servicetask']['service_task'];
					//$ser_ids[$data['servicetask_id']]=$data['servicetask_id'];
					//}
				}
			}
		}
    	if($params['field']=='client_wise'){
			if(isset($params['q']) && $params['q']!=""){
				$sqldata.=" AND CONCAT(client.client_name,'/',clientcase.case_name) like '%".$params['q']."%' ";
			}
			$sqldata.=' group by clientcase.id,clientcase.client_id,clientcase.case_name,client.client_name';
			$client_query=ClientCase::findBySql($sqldata);
			$client_data = $client_query->asArray()->all();
			if(!empty($client_data)){
				foreach($client_data as $client => $data){
					$dataProvider[$data['client_id'].'_'.$data['id']] = $data['client_name'].' - '.$data['case_name'];
				}
			}
		}
		if($params['field'] == 'client_case_id'){
			if(isset($params['q']) && $params['q']!=""){
				$sqldata.=" AND clientcase.case_name like '%".$params['q']."%' ";
			}
			$sqldata.=' group by clientcase.id,clientcase.case_name order by clientcase.case_name';
			$client_query=ClientCase::findBySql($sqldata);
			$client_data = $client_query->asArray()->all();
			if(!empty($client_data)){
				foreach($client_data as $client => $data){
					$dataProvider[$data['id']] = $data['case_name'];
				}
			}
		 }
        if ($params['field'] == 'client_id') {
            if (isset($params['q']) && $params['q'] != "") {
                $sqldata .= " AND client.client_name like '%" . $params['q'] . "%' ";
            }
            $sqldata .= ' group by clientcase.client_id, client.client_name order by client.client_name';
            $client_query = ClientCase::findBySql($sqldata);
            $client_data = $client_query->asArray()->all();
            if (!empty($client_data)) {
                foreach ($client_data as $client => $data) {
                    $dataProvider[$data['client_id']] = $data['client_name'];
                }
            }
        }
        if($params['field']=='task_priority'){
    		if(isset($params['q']) && $params['q']!=""){
    			$sqldata.=" AND priority like '%".$params['q']."%' ";
    		}
    		$sqldata.=' group by project.priority';
    		$dataProvider = ArrayHelper::map(PriorityProject::findBySql($sqldata)->all(),'priority','priority');
    	}
    	if($params['field']=='team_priority'){
			$sqldata.=' AND tpriority.tasks_priority_name IS NOT NULL ';
			if(isset($params['q']) && $params['q']!=""){
    			$sqldata.=" AND tpriority.tasks_priority_name like '%".$params['q']."%'";
    		}
    		$sqldata.=' group by tpriority.tasks_priority_name';
    		$dataProvider = ArrayHelper::map(PriorityTeam::findBySql($sqldata)->all(),'tasks_priority_name','tasks_priority_name');
    	    /*$team_priority = TasksUnits::find()
    	    ->select(['tbl_tasks_units.id','tbl_tasks_units.task_instruct_servicetask_id','tbl_tasks_units.task_id','tbl_tasks_units.unit_assigned_to'])->where(['tbl_tasks_units.team_id'=>$params['team_id'],'tbl_tasks_units.team_loc'=>$params['team_loc']])->innerJoinWith(['tasks'=>function(\yii\db\Activequery $query){
					$query->select(['tbl_tasks.id','tbl_tasks.team_priority'])
					->innerJoinWith(['teamPriority'=>function(\yii\db\ActiveQuery $query){
						$query->select(['tbl_priority_team.id','tbl_priority_team.tasks_priority_name']);
					}]);
			}]);


			if(isset($params['q']) && $params['q']!=""){
				$team_priority->andFilterWhere(['like', 'tasks_priority_name', $params['q']]);
			}
			$team_priority = $team_priority->asArray()->all();
			foreach($team_priority as $priority => $value){
				$dataProvider[$value['tasks']['team_priority']] = $value['tasks']['teamPriority']['tasks_priority_name'];
			}*/
    	}
    	if(isset($params['task_cancel'])){
    		$query->andFilterWhere(['like', 'task_cancel', 1]);
    	}
    	if(isset($params['task_closed'])){
    		$query->andFilterWhere(['like', 'task_closed', 1]);
    	}

    	$this->load($params);

    	/*if (!$this->validate()) {
            return $dataProvider;
        }*/
        //echo "<pre>"; print_r($dataProvider); exit;

		return array('All'=>'All') + $dataProvider;

	}


}
