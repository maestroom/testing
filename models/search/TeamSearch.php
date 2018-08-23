<?php

namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use app\models\Team;
use app\models\Options;
use app\models\Tasks;
use app\models\TaskInstruct;
use app\models\Settings;
use app\models\TasksUnits;
use app\models\PriorityProject;
use app\models\PriorityTeam;
use app\models\CommentsRead;
use app\models\CommentRoles;
use app\models\CommentTeams;
use app\models\ClientCase;
use app\models\Role;
/**
 * TeamSearch represents the model behind the search form about `app\models\Team`.
 */
class TeamSearch extends Team
{
	public $team_priority,$task_id,$team_status;
	public $teamorderpriority;
	public $porder=NULL; // task prority order
	public $pname=NULL; // task prority name
	public $ispastdue=NULL; // task prority name
	public $task_duetime=NULL;
	public $client_name=NULL;
	public $clientcase_name=NULL;
	public $tasks_priority_name=NULL;
    /**
     * @inheritdoc
     */
    public function rules(){
        return [

            [['team_type','team_status','priority','sort_order', 'created_by', 'modified_by','ispastdue'], 'integer'],
            [['id','task_status' ,'team_name', 'team_priority', 'team_description', 'created', 'modified','priority','task_duedate','client_wise','client_case_id','client_id','per_complete', 'team_per_complete','task_id','team_status','task_duetime','porder','pname','client_name','clientcase_name','tasks_priority_name'], 'safe'],
        ];
    }
    public function attributes(){
        // add related fields to searchable attributes
        return array_merge(parent::attributes(), ['task_duedate', 'priority', 'project_name','task_status','client_wise', 'per_complete', 'team_per_complete','team_priority_order', 'teamorderpriority','ispastdue','task_duetime','porder','pname','client_name','clientcase_name','tasks_priority_name']);
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
    public function search($params)
    {
		$userId = Yii::$app->user->identity->id;
		$roleId = Yii::$app->user->identity->role_id;
		$where = "select id from tbl_team where id != 1";
		$query = Team::find()->select(['tbl_team.id','tbl_team.team_name'])->where('tbl_team.id !=1');
		if($roleId != 0) {
			$query = Team::find()->select(['tbl_team.id','tbl_team.team_name']);
			$where = "select team_id from tbl_project_security where user_id = ".$userId." AND team_id !=0 group by team_id";
			$query->where('tbl_team.id IN ('.$where.')');
		}

		$query->join('INNER JOIN', 'tbl_tasks_teams',
				'tbl_tasks_teams.team_id =tbl_team.id');
		$query->join('INNER JOIN', 'tbl_tasks',
				'tbl_tasks.id =tbl_tasks_teams.task_id');
		$query->join('INNER JOIN', 'tbl_client_case',
				'tbl_client_case.id =tbl_tasks.client_case_id');
		$query->andWhere('tbl_tasks.task_closed=0 AND tbl_tasks.task_cancel=0 AND tbl_client_case.is_close=0
AND tbl_tasks.task_status != 4');

       $query->distinct();
	   $query->orderBy('tbl_team.team_name');


        $dataProvider = new ActiveDataProvider([
            'query' => $query,
			'pagination' => false,
            //'pagination' =>['pageSize'=>25],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'team_type' => $this->team_type,
            'team_status' => $this->team_status,
            'sort_order' => $this->sort_order,
            'created' => $this->created,
            'created_by' => $this->created_by,
            'modified' => $this->modified,
            'modified_by' => $this->modified_by,
        ]);

        $query->andFilterWhere(['like', 'team_name', $this->team_name])
            ->andFilterWhere(['like', 'team_description', $this->team_description]);

        return $dataProvider;
    }

    public function loadproject($params)
    {
		//echo "<pre>",print_r($params),"</pre>";die;
    	$roleId = Yii::$app->user->identity->role_id;
		$userId = Yii::$app->user->identity->id;
		$timezoneOffset = (new Options)->getOffsetOfCurrenttimeZone();
		if (Yii::$app->db->driverName == 'mysql') {
			$data_query_pastdue = "getDueDateTimeByUsersTimezone('{$timezoneOffset}',tbl_task_instruct.task_duedate,tbl_task_instruct.task_timedue,'%Y-%m-%d %H:%i:%s')";
		}else{
			$data_query_pastdue = "[dbo].getDueDateTimeByUsersTimezone('{$timezoneOffset}',tbl_task_instruct.task_duedate,tbl_task_instruct.task_timedue,'%Y-%m-%d %H:%i:%s')";
		}

        $sqlpastdue="SELECT COUNT(*) as totalitems
    	FROM tbl_task_instruct
    	INNER JOIN tbl_tasks as tasks ON tasks.id = task_id
		WHERE tbl_task_instruct.task_id = tbl_tasks.id
    	AND isactive=1";
		/*INNER JOIN (SELECT tbl_task_instruct.id, $data_query_pastdue as task_date_time FROM tbl_task_instruct WHERE isactive=1 AND task_id IS NOT NULL AND task_id<>'') as A ON tbl_task_instruct.id = A.id*/
		$todaysdate = (new Options)->ConvertOneTzToAnotherTz(date('Y-m-d H:i:s'), 'UTC', $_SESSION['usrTZ'], "YMDHIS");
    	if (Yii::$app->db->driverName == 'mysql'){
			$task_completedate = " CONVERT_TZ(tasks.task_complete_date,'+00:00','".$timezoneOffset."') ";
			$sqlpastdue.=" AND A.task_date_time < CASE WHEN tasks.task_complete_date!='0000-00-00 00:00:00' AND tasks.task_complete_date!='' AND tasks.task_status = 4 THEN '$task_duedatetime' ELSE '$todaysdate' END";
        }else{
			$task_completedate = " CAST(switchoffset(todatetimeoffset(tasks.task_complete_date, '+00:00'), '$timezoneOffset') as DATETIME) ";
        	$sqlpastdue.=" AND A.task_date_time < CASE WHEN CAST(tasks.task_complete_date as varchar)!='0000-00-00 00:00:00' AND CAST(tasks.task_complete_date as varchar) IS NOT NULL AND tasks.task_status = 4 THEN '$task_duedatetime' ELSE '$todaysdate' END";
        }

		$askPercentageC = (new \app\models\Tasks())->getTaskPercentageCompleteByTaskid('tbl_tasks.id');
		$TeamTaskPercentageC = (new \app\models\Tasks())->getTeamTaskPercentageCompleteByTaskid('tbl_tasks.id', $params['team_id'], $params['team_loc']);
		//$role_type = explode(',',Role::findOne($roleId)->role_type);
		$role_info = $_SESSION['role'];
        //$role_type = explode(',',Role::findOne($roleId)->role_type);
		$role_type = explode(',',$role_info->role_type);

		if (Yii::$app->db->driverName == 'mysql') {
			//$data_query = "DATE_FORMAT(CONVERT_TZ(CONCAT(tbl_task_instruct.`task_duedate` , ' ', STR_TO_DATE(tbl_task_instruct.`task_timedue` , '%l:%i %p' )),'+00:00','{$timezoneOffset}'), '%Y-%m-%d %H:%i')";
			// $askPercentageC = 'getTaskPercentageCompleteByTaskid';
			$data_query = "getDueDateTimeByUsersTimezone('{$timezoneOffset}',tbl_task_instruct.task_duedate,tbl_task_instruct.task_timedue,'%Y-%m-%d %H:%i:%s')";
		} else {
			// $askPercentageC = 'dbo.getTaskPercentageCompleteByTaskid';
			//$data_query = "CAST(switchoffset(todatetimeoffset(Cast((CAST(tbl_task_instruct.task_duedate as varchar)  + ' ' + tbl_task_instruct.task_timedue) as datetime), '+00:00'), '{$timezoneOffset}') as datetime)";
			//$data_query = "[dbo].getDueDateTimeByUsersTimezone('{$timezoneOffset}',tbl_task_instruct.task_duedate,tbl_task_instruct.task_timedue,'%Y-%m-%d %H:%i:%s')";
			$data_query = "(SELECT duedatetime FROM [dbo].getDueDateTimeByUsersTimezoneTV('{$timezoneOffset}',tbl_task_instruct.task_duedate,tbl_task_instruct.task_timedue,'%Y-%m-%d %H:%i:%s'))";
		}

		/*$sql = "(SELECT CASE WHEN (team_loc_prority IS NULL OR team_loc_prority=0) THEN ((SELECT count( * )FROM tbl_priority_team) +1) ELSE team.priority_order END AS final_priority
		FROM tbl_tasks_teams AS task_team
		LEFT JOIN tbl_priority_team as team ON task_team.team_loc_prority = team.id
		WHERE task_team.task_id = tbl_tasks.id AND task_team.team_id=".$params['team_id']." AND task_team.team_loc=".$params['team_loc'].")";
		// $sql=(SELECT CASE WHEN (team_priority IS NULL OR team_priority =0) THEN ((SELECT count( * ) FROM tbl_priority_team) +1) ELSE team.priority_order END AS final_priority FROM tbl_tasks AS task LEFT JOIN tbl_priority_team as team ON task.team_priority = team.id where task.id = tbl_tasks.id)*/

		$sqlteampname=" SELECT tbl_priority_team.tasks_priority_name FROM tbl_priority_team WHERE id IN (SELECT tbl_tasks_teams.team_loc_prority
FROM tbl_tasks_teams INNER JOIN tbl_priority_team_loc ON tbl_priority_team_loc.priority_team_id = tbl_tasks_teams.team_loc_prority
INNER JOIN tbl_priority_team ON tbl_priority_team.id = tbl_priority_team_loc.priority_team_id
WHERE tbl_tasks_teams.task_id=tbl_tasks.id AND tbl_tasks_teams.team_id='".$params['team_id']."'
AND tbl_tasks_teams.team_loc='".$params['team_loc']."')";


		$query = Tasks::find()
			->select(['tbl_client.client_name as client_name','tbl_client_case.client_id','tbl_tasks.client_case_id','tbl_tasks.id', /*'tbl_tasks.team_priority'*/ 'tbl_tasks_teams.team_loc_prority', 'tbl_tasks.task_status','tbl_tasks.task_closed','tbl_tasks.task_cancel','tbl_tasks.task_cancel_reason','tbl_client_case.case_name as clientcase_name','tbl_task_instruct.project_name as project_name','tbl_task_instruct.task_duedate','tbl_task_instruct.task_timedue as task_duetime','tbl_task_instruct.task_priority','tbl_priority_project.priority_order as porder', 'tbl_priority_project.priority as pname', '(CASE WHEN (tbl_tasks_teams.team_loc_prority IS NULL OR team_loc_prority=0) THEN ((SELECT MAX(tbl_priority_team_loc.priority_order) FROM tbl_priority_team_loc WHERE tbl_priority_team_loc.team_id='.$params['team_id'].' and tbl_priority_team_loc.team_loc_id='.$params['team_loc'].') + 1) ELSE tbl_priority_team_loc.priority_order END) as teamorderpriority', $askPercentageC.' as per_complete', $TeamTaskPercentageC.' as team_per_complete', 'A.task_date_time','('.$sqlpastdue.') as ispastdue','('.$sqlteampname.') as tasks_priority_name'])//,"$data_query as task_date_time"])
			->joinWith(['clientCase'=>function (\yii\db\ActiveQuery $q) use($roleId, $userId, $role_type) {
			$q->joinWith(['client'],false);
			/* if($roleId!=0 && $role_type[0]!=2) {
				$q->joinWith(['projectSecurity' => function(\yii\db\ActiveQuery $q) use ($userId) {
					$q->where([ 'tbl_project_security.user_id' => $userId ]);
				}]);
			} */
			}],false)->where([ 'tbl_client_case.is_close' => 0])
			//  ->joinWith('client')
				->joinWith(['tasksTeams' => function (\yii\db\ActiveQuery $query) use ($params, $roleId, $role_type, $userId) {
					if($roleId!=0 && $role_type[0]!=1) { // role type is Case Manager
						$query->joinWith(['projectSecurity' => function(\yii\db\ActiveQuery $query) use ($userId) {
							$query->where([ 'tbl_project_security.user_id' => $userId ]);
						}],false);
					}
					$query->where('tbl_tasks_teams.team_id='. $params['team_id'] .' AND tbl_tasks_teams.team_loc='. $params['team_loc']);
					$query->joinWith(['teamPriority' => function(\yii\db\ActiveQuery $query) use($params) {
					/* $query->joinWith(['priorityTeamLoc' => function(\yii\db\ActiveQuery $query) use($params) {
						$query->where(['tbl_priority_team_loc.team_id' => $params['team_id'], 'tbl_priority_team_loc.team_loc_id' => $params['team_loc']]);
					}]); */
					$query->join('LEFT JOIN', 'tbl_priority_team_loc', 'tbl_priority_team_loc.priority_team_id =tbl_priority_team.id AND tbl_priority_team_loc.team_id ='.$params['team_id'].' AND tbl_priority_team_loc.team_loc_id ='.$params['team_loc']);
					}],false);
				}],false)
				->joinWith([
					'taskInstruct' => function (\yii\db\ActiveQuery $query) use ($params, $data_query) {
						$query->innerJoin("(SELECT tbl_task_instruct.id, $data_query as task_date_time FROM tbl_task_instruct WHERE isactive=1 AND task_id IS NOT NULL AND task_id <> '') as A", 'tbl_task_instruct.id = A.id');
						$query->where(['isactive'=>1])->joinWith('taskPriority');
						//->innerJoinWith(['taskInstructServicetasksWithoutOrder'=>function(\yii\db\ActiveQuery $query) use($params) { $query->where(['tbl_task_instruct_servicetask.team_id'=>$params['team_id'],'tbl_task_instruct_servicetask.team_loc'=>$params['team_loc']]); }]);
				}
			],false);
			//->joinWith('teamPriority');
			//->joinWith('teamPriority');

        if(isset($params['active']) && $params['active']="active" && $params['TeamSearch']['task_status']=="")
        	$query->andWhere('tbl_tasks.task_status IN (0,1,3)');
			if(isset($params['task_id'])) {
    			$query->andWhere('tbl_tasks.id = '.$params['task_id']);
    	}

		if(isset($params['comment']) && $params['comment']="comment") {
			$task_ids = (new Tasks)->getUnreadCommentsTeam($params['team_id'],$params['team_loc'], "task_ids");
			if (empty($task_ids)) {
			   $task_ids[0] = 0;
			}
			if (!empty($task_ids)) {
			   $query->andWhere('tbl_tasks.id IN ('.implode(",",$task_ids).')');
			}
		}

		if(isset($params['task_cancel']) && $params['task_cancel']=1)
			$query->andWhere(['task_cancel'=>1]);
		else
			$query->andWhere(['task_cancel'=>0]);


    	if(isset($params['task_closed']) && $params['task_closed']=1)
			$query->andWhere(['task_closed'=>1]);
		else
 			$query->andWhere(['task_closed'=>0]);

 		$drivername = Yii::$app->db->driverName;
		$currdate=time();
    	if(isset($params['due'])) {
    		if($params['due']=='past') {
    			if ($drivername == 'mysql') {
					$query->andWhere('tbl_task_instruct.task_duedate < "' . date('Y-m-d H:i:s', time()) . '"');
    			} else {
    				$query->andWhere("Cast((CAST(tbl_task_instruct.task_duedate as varchar)  + ' ' + tbl_task_instruct.task_timedue) as datetime) < '" . date('Y-m-d H:i:s', time()) . "'");
    			}
    			$query->andWhere('tbl_tasks.task_status !=4');
    		}
    		if($params['due']=='notpastdue') {
    			$query->andWhere('tbl_tasks.task_closed = 0 AND tbl_tasks.task_cancel = 0');
    		}
    	}

		if(isset($params['status']) && is_numeric($params['status'])) {
			$query->andWhere('tbl_tasks.task_status = '.$params['status']);
		}

 		$query->distinct();
 		$defaultSort="";
 		if($params['sort']=="") {
		//	$query->andWhere($sql_query);
			$settings_info = Settings::find()->where("field = 'project_sort'")->one();
			$fieldvalue = $settings_info->fieldvalue;
			/*IRT 26*/
			$optionsproject_sort_display=Options::find()->select('project_sort_display')->where(['user_id'=>Yii::$app->user->identity->id])->andWhere('project_sort_display IS NOT NULL')->one()->project_sort_display;

			if(isset($optionsproject_sort_display) && in_array($optionsproject_sort_display, array(0,1,2,3))) {
				$fieldvalue = $optionsproject_sort_display;
			}
			/*IRT 26*/
			if ($fieldvalue == '0') {
				// $query->orderBy("tbl_priority_project.priority_order ASC,tbl_task_instruct.task_duedate DESC,tbl_task_instruct.task_timedue DESC");
				$defaultSort=['priority'=>SORT_ASC,'task_duedate'=>SORT_DESC,'instruct_task_timedue'=>SORT_DESC];
			} else if ($fieldvalue == '1') {
				// $query->orderBy("tbl_task_instruct.task_duedate DESC,tbl_task_instruct.task_timedue DESC");
				$defaultSort=['task_duedate'=>SORT_DESC,'instruct_task_timedue'=>SORT_DESC];
			} else if ($fieldvalue == '2') {
				// $query->orderBy("tbl_tasks.id DESC");
				$defaultSort=['task_id'=>SORT_DESC];
			} else if($fieldvalue == '3') {
				//$query->orderBy("team_priority ASC, priority ASC,tbl_task_instruct.task_duedate DESC,tbl_task_instruct.task_timedue DESC");
				$defaultSort=['team_priority' => SORT_ASC, 'priority' => SORT_ASC, 'task_duedate' => SORT_DESC]; //, // 'team_priority' => SORT_ASC
			}
        }

        //echo "<pre>",print_r($defaultSort),"</pre>";
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' =>['pageSize'=>'25'],
        ]);

        $dataProvider->sort->enableMultiSort = true;
        //$data = $dataProvider->getModels();
        //echo "<pre>",print_r($data),"</pre>"; die;

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
		//	echo "<pre>",print_r($defaultSort),"</pre>";
			$dataProvider->sort->defaultOrder=$defaultSort;
		}


        $dataProvider->sort->attributes['task_id'] = [
            'asc' => ['tbl_tasks.id' => SORT_ASC],
            'desc' => ['tbl_tasks.id' => SORT_DESC],
        ];

        /*$dataProvider->sort->attributes['instruct_task_duedate'] = [
            'asc' => ['tbl_task_instruct.task_duedate' => SORT_ASC],
            'desc' => ['tbl_task_instruct.task_duedate' => SORT_DESC],
        ];
		$dataProvider->sort->attributes['instruct_task_timedue'] = [
            'asc' => ['tbl_task_instruct.task_timedue' => SORT_ASC],
            'desc' => ['tbl_task_instruct.task_timedue' => SORT_DESC],
        ];*/

        $dataProvider->sort->attributes['task_status'] = [
            'asc' => ['task_status' => SORT_ASC],
            'desc' => ['task_status' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['task_duedate'] = [
			'asc' => ['A.task_date_time' => SORT_ASC],
			'desc' => ['A.task_date_time' => SORT_DESC],
		];
        $dataProvider->sort->attributes['priority'] = [
            'asc' => ['tbl_priority_project.priority_order' => SORT_ASC],
            'desc' => ['tbl_priority_project.priority_order' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['team_priority'] = [
            'asc' => ['teamorderpriority' => SORT_ASC],
            'desc' => ['teamorderpriority' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['project_name'] = [
            'asc' => ['project_name' => SORT_ASC],
            'desc' => ['project_name' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['client_id'] = [
            'asc' => ['client_id' => SORT_ASC],
            'desc' => ['client_id' => SORT_DESC],
        ];
		$dataProvider->sort->attributes['client_id'] = [
            'asc' => ['tbl_client.client_name' => SORT_ASC],
            'desc' => ['tbl_client.client_name' => SORT_DESC],
        ];
		$dataProvider->sort->attributes['client_case_id'] = [
            'asc' => ['tbl_client_case.case_name' => SORT_ASC],
            'desc' => ['tbl_client_case.case_name' => SORT_DESC],
        ];

       /* multiselect */
        if ($params['TeamSearch']['id'] != null && is_array($params['TeamSearch']['id'])) {
			if(!empty($params['TeamSearch']['id'])){
				foreach($params['TeamSearch']['id'] as $k=>$v){
					if($v=='All'){ // || strpos($v,",") !== false
						unset($params['TeamSearch']['id']);
					}
				}
			}
			$query->andFilterWhere(['tbl_tasks.id' => $params['TeamSearch']['id']]);
		}else{
			$query->andFilterWhere(['tbl_tasks.id' => $params['TeamSearch']['id']]);
		}

		 if ($params['TeamSearch']['task_id'] != null && is_array($params['TeamSearch']['task_id'])) {
			if(!empty($params['TeamSearch']['task_id'])){
				foreach($params['TeamSearch']['task_id'] as $k=>$v){
					if($v=='All'){ // || strpos($v,",") !== false
						unset($params['TeamSearch']['task_id']);
					}
				}
			}
		}

		if ($params['TeamSearch']['task_status'] != null && is_array($params['TeamSearch']['task_status'])) {
			if(!empty($params['TeamSearch']['task_status'])){
				foreach($params['TeamSearch']['task_status'] as $k=>$v){
					if($v=='All'){ // || strpos($v,",") !== false
						unset($params['TeamSearch']['task_status']);
					}
				}
			}
		}
		if ($params['TeamSearch']['priority'] != null && is_array($params['TeamSearch']['priority'])) {
			if(!empty($params['TeamSearch']['priority'])) {
				foreach($params['TeamSearch']['priority'] as $k=>$v) {
					if($v=='All') { // || strpos($v,",") !== false
						unset($params['TeamSearch']['priority']);
					}
				}
			}
			$query->andFilterWhere(["or like","priority",$params['TeamSearch']['priority']]);
		}
		if ($params['TeamSearch']['team_priority'] != null && is_array($params['TeamSearch']['team_priority'])) {
			if(!empty($params['TeamSearch']['team_priority'])){
				foreach($params['TeamSearch']['team_priority'] as $k=>$v){
					if($v=='All'){ // || strpos($v,",") !== false
						unset($params['TeamSearch']['team_priority']);
					}
				}
			}
			$query->andFilterWhere(["or like","tbl_priority_team.tasks_priority_name",$params['TeamSearch']['team_priority']]);
		}
		if ($params['TeamSearch']['project_name'] != null && is_array($params['TeamSearch']['project_name'])) {
			$project_namequery = "";
			$is_unset=false;
			if(!empty($params['TeamSearch']['project_name'])) {
				foreach($params['TeamSearch']['project_name'] as $k=>$v) {
					if($v=='(not set)'){
						$params['TeamSearch']['project_name'][$k]='';
					}/* else if(strpos($v,",") !== false) {
						unset($params['TeamSearch']['project_name']);
						$is_unset=true; break;
					} */
					if($project_namequery == ""){
						$project_namequery = "project_name='".$params['TeamSearch']['project_name'][$k]."'";
						if($params['TeamSearch']['project_name'][$k]==''){$params['TeamSearch']['project_name'][$k]='(not set)';}
					} else {
						$project_namequery .= " OR project_name='".$params['TeamSearch']['project_name'][$k]."'";
						if($params['TeamSearch']['project_name'][$k]==''){$params['TeamSearch']['project_name'][$k]='(not set)';}
					}
				}
			}
			if($is_unset==false){
				$query->andWhere("(".$project_namequery.")");
			}
			$this->project_name=$params['TeamSearch']['project_name'];
		} else{
			$query->andFilterWhere(['or like', 'project_name', $params['TeamSearch']['project_name']]);
			$this->project_name=$params['TeamSearch']['project_name'];
		}

		if(isset($params['TeamSearch']['per_complete'])){
                    $params['TeamSearch']['per_complete'] = str_replace( array('%','!','@','#','$','&','*','(',')','-','_','+','=','{','}','[',']',',','<','>','.','/','?',':',';','"','\''), '', $params['TeamSearch']['per_complete']);
                    if($params['TeamSearch']['per_complete']!="")
                        $query->andWhere(["ROUND(".(new \app\models\Tasks())->getTaskPercentageCompleteByTaskid('tbl_tasks.id').",0)"=>$params['TeamSearch']['per_complete']]);
		}

		if(isset($params['TeamSearch']['team_per_complete'])){
                    $params['TeamSearch']['team_per_complete'] = str_replace( array('%','!','@','#','$','&','*','(',')','-','_','+','=','{','}','[',']',',','<','>','.','/','?',':',';','"','\''), '', $params['TeamSearch']['team_per_complete']);
               	    if($params['TeamSearch']['team_per_complete']!="")
                        $query->andWhere(["ROUND(".(new \app\models\Tasks())->getTeamTaskPercentageCompleteByTaskid('tbl_tasks.id','tbl_tasks_teams.team_id','tbl_tasks_teams.team_loc').",0)"=>$params['TeamSearch']['team_per_complete']]);
		}

		if ($params['TeamSearch']['client_id'] != null && is_array ($params['TeamSearch']['client_id'])) {
				if(!empty($params['TeamSearch']['client_id'])) {
				foreach($params['TeamSearch']['client_id'] as $k=>$v) {
					if($v == 'All'){ // || strpos($v,",") !== false
						unset($params['TeamSearch']['client_id']); break;
					}
				}
			}
		}
		if ($params['TeamSearch']['client_case_id'] != null && is_array ($params['TeamSearch']['client_case_id'])) {
			if(!empty($params['TeamSearch']['client_case_id'])){
				foreach($params['TeamSearch']['client_case_id'] as $k=>$v){
					if($v=='All'){ // || strpos($v,",") !== false
						unset($params['TeamSearch']['client_case_id']);break;
					}
				}
			}
		}


		/*multiselect*/
        $this->load($params);
		 if(isset($params['TeamSearch']['task_id']) && $params['TeamSearch']['task_id']!="" && $params['TeamSearch']['task_id']!="All"){
		   $query->andFilterWhere(['tbl_tasks.id' => $params['TeamSearch']['task_id']]);
		}
        $drivername = Yii::$app->db->driverName;
        //if(isset($params['TeamSearch']['id']) && $params['TeamSearch']['id']!="" && $params['TeamSearch']['id']!="All")

        //if(isset($params['TeamSearch']['priority']) && $params['TeamSearch']['priority']!="" && $params['TeamSearch']['priority']!="All")
    	//	$query->andFilterWhere(['like', 'priority', $params['TeamSearch']['priority']]);
    	if(isset($params['TeamSearch']['task_status']) && $params['TeamSearch']['task_status']!="" && $params['TeamSearch']['task_status']!="All") {
    		$this->task_status=$params['TeamSearch']['task_status'];
    		if(in_array(5,$params['TeamSearch']['task_status'])) {
				$query->andWhere('tbl_tasks.task_status IN (0,1,3)');
			} else if(in_array(6,$params['TeamSearch']['task_status'])) {
				$query->andWhere('tbl_tasks.task_status IN (0,1,3)');
				if ($drivername == 'mysql') {
					$query->andWhere('tbl_task_instruct.task_duedate < "' . date('Y-m-d H:i:s', time()) . '"');
    			} else {
    				$query->andWhere("Cast((CAST(tbl_task_instruct.task_duedate as varchar)  + ' ' + tbl_task_instruct.task_timedue) as datetime) < '" . date('Y-m-d H:i:s', time()) . "'");
    			}
    			$query->andWhere('tbl_tasks.task_status !=4');
			}else{
				if(!empty($params['TeamSearch']['task_status'])) {
					foreach($params['TeamSearch']['task_status'] as $k=>$v) {
						if($v=='All') { //  || strpos($v,",") !== false
							unset($params['TeamSearch']['task_status']);
						}
					}
				}

				$tStatus = array();
				foreach($params['TeamSearch']['task_status'] as $k => $v1) {
					$tStatus[$k] = $v1;
				}

				if (in_array('7', $tStatus)) {
					$teamId = $params['team_id'];
					$query->joinWith(['comments' => function(\yii\db\ActiveQuery $query) use($userId,$teamId,$roleId) {
    					$query->select(['tbl_comments.id','tbl_comments.task_id']);
        				$query->where(['NOT IN','tbl_comments.id', CommentsRead::find()->select(	['comment_id'])->where(['user_id' => $userId]) ]);
        				$query->joinWith(['commentTeams','commentRoles']);
        				$query->andWhere('tbl_comment_teams.team_id='.$teamId.' OR tbl_comment_roles.role_id='.$roleId);
        				$query->andWhere('tbl_comments.created_by!='.$userId);
        			}]);
				}

				if(!array_search('7',$tStatus))	$tStatus = array_diff($tStatus, array('7'));

				$query->andFilterWhere(['or like', 'task_status', $tStatus]);
			}
		}

		/* if(isset($params['TeamSearch']['client_wise']) && $params['TeamSearch']['client_wise']!="" && $params['TeamSearch']['client_wise']!="All"){
    		$query->andFilterWhere(['or like', "CONCAT(tbl_client.client_name,' - ',tbl_client_case.case_name)", $params['TeamSearch']['client_wise'],false]);
    	} */
    	if(isset($params['TeamSearch']['client_case_id']) && !empty($params['TeamSearch']['client_case_id'])){
        	$clientcasevid_sql = "SELECT id FROM tbl_tasks WHERE client_case_id IN (".implode(',',$params['TeamSearch']['client_case_id']).") Group By id";
        	$query->andWhere('tbl_tasks.id IN ('.$clientcasevid_sql.')');
        }
        if(isset($params['TeamSearch']['client_id']) && !empty($params['TeamSearch']['client_id'])){
        	 $clientcasevid_sql = "SELECT tbl_tasks.id FROM tbl_tasks inner join tbl_client_case on tbl_client_case.id = tbl_tasks.client_case_id WHERE tbl_client_case.client_id IN (".implode(',',$params['TeamSearch']['client_id']).") Group By tbl_tasks.id";
        	 $query->andWhere('tbl_tasks.id IN ('.$clientcasevid_sql.')');
        }

		/*if(isset($params['TeamSearch']['project_name']) && $params['TeamSearch']['project_name'] == 'blank'){
			$query->andWhere(['project_name' => '']);
		}*/

		/*if(isset($params['TeamSearch']['project_name']) && $params['TeamSearch']['project_name']!="blank" && $params['TeamSearch']['project_name']!="All"){
            $query->andFilterWhere(['like', 'project_name', $params['TeamSearch']['project_name']]);

		}*/

         /*if(isset($params['TeamSearch']['task_duedate']) && $params['TeamSearch']['task_duedate']!=""){
     		$task_duedate = date("Y-m-d", strtotime($params['TeamSearch']['task_duedate']));
    		if (Yii::$app->db->driverName == 'mysql') {
    			$datesql ="DATE_FORMAT(CONVERT_TZ(CONCAT( tbl_task_instruct.`task_duedate` , ' ', STR_TO_DATE(tbl_task_instruct.`task_timedue` , '%l:%i %p' )),'+00:00','{$timezoneOffset}'), '%Y-%m-%d') = '{$task_duedate}'";
    		} else {
    			$datesql = "CAST(switchoffset(todatetimeoffset(Cast((CAST(tbl_task_instruct.task_duedate as varchar)  + ' ' + tbl_task_instruct.task_timedue) as datetime), '+00:00'), '{$timezoneOffset}') as date) = '".$task_duedate."'";
    		}
    		$this->task_duedate=$params['TeamSearch']['task_duedate'];
    		$query->andWhere($datesql);
    		//echo $params['TeamSearch']['task_duedate'] = date("m/d/Y", strtotime($params['TeamSearch']['task_duedate']));
    	}*/
    	/*if(isset($params['TeamSearch']['task_duedate']) && $params['TeamSearch']['task_duedate']!=""){
			$task_duedate=explode("-",$params['TeamSearch']['task_duedate']);
			$task_duedate_start=explode("/",trim($task_duedate[0]));
			$task_duedate_end=explode("/",trim($task_duedate[1]));
			$task_duedate_s=$task_duedate_start[2]."-".$task_duedate_start[0]."-".$task_duedate_start[1];
			$task_duedate_e=$task_duedate_end[2]."-".$task_duedate_end[0]."-".$task_duedate_end[1];
			if (Yii::$app->db->driverName == 'mysql') {
        		$where_date_query = "DATE_FORMAT( CONVERT_TZ(CONCAT( tbl_task_instruct.`task_duedate` , ' ', STR_TO_DATE(tbl_task_instruct.`task_timedue` , '%l:%i %p' )),'+00:00','{$timezoneOffset}'), '%Y-%m-%d')";
        	} else {
        		$where_date_query = "CAST(switchoffset(todatetimeoffset(Cast((CAST(tbl_task_instruct.task_duedate as varchar)  + ' ' + tbl_task_instruct.task_timedue) as datetime), '+00:00'), '{$timezoneOffset}') as date) ";
    		}
    		$query->andWhere(" $where_date_query >= '$task_duedate_s' AND $where_date_query  <= '$task_duedate_e' ");
        }*/
		if(isset($params['TeamSearch']['task_duedate']) && $params['TeamSearch']['task_duedate']!=""){
			$task_duedate=explode("-",$params['TeamSearch']['task_duedate']);
			$task_duedate_start = explode("/", trim($task_duedate[0]));
            $task_duedate_end = explode("/", trim($task_duedate[1]));
            $task_duedate_s = $task_duedate_start[2] . "-" . $task_duedate_start[0] . "-" . $task_duedate_start[1].' 00:00:00';
            $task_duedate_e = $task_duedate_end[2] . "-" . $task_duedate_end[0] . "-" . $task_duedate_end[1].' 23:59:59';
           // $task_duedate_s=trim($task_duedate[0]).' 00:00:00';
			//	$task_duedate_e=trim($task_duedate[1]).' 23:59:59';

			$query->andWhere(" A.task_date_time >= '$task_duedate_s' AND A.task_date_time  <= '$task_duedate_e' ");
			/*
			$task_duedate_s=trim($task_duedate[0]).' 00:00:00';
			$task_duedate_e=trim($task_duedate[1]).' 23:59:59';

			$query->andWhere(" A.task_date_time >= '$task_duedate_s' AND A.task_date_time  <= '$task_duedate_e' ");
			*/
        }

        if (!$this->validate()) {
            return $dataProvider;
        }
        if ($params['TeamSearch']['project_name'] != null && is_array($params['TeamSearch']['project_name'])) {
			if(!empty($params['TeamSearch']['project_name'])){
				foreach($params['TeamSearch']['project_name'] as $k=>$v){
					if($v==''){
						$params['TeamSearch']['project_name'][$k]='(not set)';
					}
				}
			}
			$this->project_name=$params['TeamSearch']['project_name'];
		}else if ($params['TeamSearch']['project_name'] != null){
			$this->project_name=$params['TeamSearch']['project_name'];
		}
	    return $dataProvider;
	}
	public function searchLoadProjectFilter($params){
		$roleId = Yii::$app->user->identity->role_id;
		$userId = Yii::$app->user->identity->id;
		$teamprojectparams=$params['params'];
		$timezoneOffset = (new Options)->getOffsetOfCurrenttimeZone();

		if (Yii::$app->db->driverName == 'mysql') {
			$data_query_pastdue = "getDueDateTimeByUsersTimezone('{$timezoneOffset}',tbl_task_instruct.task_duedate,tbl_task_instruct.task_timedue,'%Y-%m-%d %H:%i:%s')";
		}else{
			$data_query_pastdue = "[dbo].getDueDateTimeByUsersTimezone('{$timezoneOffset}',tbl_task_instruct.task_duedate,tbl_task_instruct.task_timedue,'%Y-%m-%d %H:%i:%s')";
		}

        $sqlpastdue="SELECT COUNT(*) as totalitems
    	FROM tbl_task_instruct
    	INNER JOIN tbl_tasks as tasks ON tasks.id = task_id
		WHERE tbl_task_instruct.task_id = tbl_tasks.id
    	AND isactive=1";
		/*INNER JOIN (SELECT tbl_task_instruct.id, $data_query_pastdue as task_date_time FROM tbl_task_instruct WHERE isactive=1 AND task_id IS NOT NULL AND task_id<>'') as A ON tbl_task_instruct.id = A.id*/
		$todaysdate = (new Options)->ConvertOneTzToAnotherTz(date('Y-m-d H:i:s'), 'UTC', $_SESSION['usrTZ'], "YMDHIS");
    	if (Yii::$app->db->driverName == 'mysql'){
			$task_completedate = " CONVERT_TZ(tasks.task_complete_date,'+00:00','".$timezoneOffset."') ";
			$sqlpastdue.=" AND A.task_date_time < CASE WHEN tasks.task_complete_date!='0000-00-00 00:00:00' AND tasks.task_complete_date!='' AND tasks.task_status = 4 THEN '$task_duedatetime' ELSE '$todaysdate' END";
        }else{
			$task_completedate = " CAST(switchoffset(todatetimeoffset(tasks.task_complete_date, '+00:00'), '$timezoneOffset') as DATETIME) ";
        	$sqlpastdue.=" AND A.task_date_time < CASE WHEN CAST(tasks.task_complete_date as varchar)!='0000-00-00 00:00:00' AND CAST(tasks.task_complete_date as varchar) IS NOT NULL AND tasks.task_status = 4 THEN '$task_duedatetime' ELSE '$todaysdate' END";
        }

		$askPercentageC = (new \app\models\Tasks())->getTaskPercentageCompleteByTaskid('tbl_tasks.id');
		$TeamTaskPercentageC = (new \app\models\Tasks())->getTeamTaskPercentageCompleteByTaskid('tbl_tasks.id', $params['team_id'], $params['team_loc']);
		$role_info = $_SESSION['role'];
        $role_type = explode(',',$role_info->role_type);

		if (Yii::$app->db->driverName == 'mysql') {
			$data_query = "getDueDateTimeByUsersTimezone('{$timezoneOffset}',tbl_task_instruct.task_duedate,tbl_task_instruct.task_timedue,'%Y-%m-%d %H:%i:%s')";
		} else {
			//$data_query = "[dbo].getDueDateTimeByUsersTimezone('{$timezoneOffset}',tbl_task_instruct.task_duedate,tbl_task_instruct.task_timedue,'%Y-%m-%d %H:%i:%s')";
			$data_query = "(SELECT duedatetime FROM [dbo].getDueDateTimeByUsersTimezoneTV('{$timezoneOffset}',tbl_task_instruct.task_duedate,tbl_task_instruct.task_timedue,'%Y-%m-%d %H:%i:%s'))";
		}

		$sqlteampname=" SELECT tbl_priority_team.tasks_priority_name FROM tbl_priority_team WHERE id IN (SELECT tbl_tasks_teams.team_loc_prority
FROM tbl_tasks_teams INNER JOIN tbl_priority_team_loc ON tbl_priority_team_loc.priority_team_id = tbl_tasks_teams.team_loc_prority
INNER JOIN tbl_priority_team ON tbl_priority_team.id = tbl_priority_team_loc.priority_team_id
WHERE tbl_tasks_teams.task_id=tbl_tasks.id AND tbl_tasks_teams.team_id='".$params['team_id']."'
AND tbl_tasks_teams.team_loc='".$params['team_loc']."')";


		$query = Tasks::find()
			->select(['tbl_client.client_name as client_name','tbl_client_case.client_id','tbl_tasks.client_case_id','tbl_tasks.id', 'tbl_tasks_teams.team_loc_prority', 'tbl_tasks.task_status','tbl_tasks.task_closed','tbl_tasks.task_cancel','tbl_tasks.task_cancel_reason','tbl_client_case.case_name as clientcase_name','tbl_task_instruct.project_name as project_name','tbl_task_instruct.task_duedate','tbl_task_instruct.task_timedue as task_duetime','tbl_task_instruct.task_priority','tbl_priority_project.priority_order as porder', 'tbl_priority_project.priority as pname', '(CASE WHEN (tbl_tasks_teams.team_loc_prority IS NULL OR team_loc_prority=0) THEN ((SELECT MAX(tbl_priority_team_loc.priority_order) FROM tbl_priority_team_loc WHERE tbl_priority_team_loc.team_id='.$params['team_id'].' and tbl_priority_team_loc.team_loc_id='.$params['team_loc'].') + 1) ELSE tbl_priority_team_loc.priority_order END) as teamorderpriority', $askPercentageC.' as per_complete', $TeamTaskPercentageC.' as team_per_complete', 'A.task_date_time','('.$sqlpastdue.') as ispastdue','('.$sqlteampname.') as tasks_priority_name'])
			->joinWith(['clientCase'=>function (\yii\db\ActiveQuery $q) use($roleId, $userId, $role_type) {
			$q->joinWith(['client'],false);
			}],false)->where([ 'tbl_client_case.is_close' => 0])
				->joinWith(['tasksTeams' => function (\yii\db\ActiveQuery $query) use ($params, $roleId, $role_type, $userId) {
					if($roleId!=0 && $role_type[0]!=1) { // role type is Case Manager
						$query->joinWith(['projectSecurity' => function(\yii\db\ActiveQuery $query) use ($userId) {
							$query->where([ 'tbl_project_security.user_id' => $userId ]);
						}],false);
					}
					$query->where('tbl_tasks_teams.team_id='. $params['team_id'] .' AND tbl_tasks_teams.team_loc='. $params['team_loc']);
					$query->joinWith(['teamPriority' => function(\yii\db\ActiveQuery $query) use($params) {
					$query->join('LEFT JOIN', 'tbl_priority_team_loc', 'tbl_priority_team_loc.priority_team_id =tbl_priority_team.id AND tbl_priority_team_loc.team_id ='.$params['team_id'].' AND tbl_priority_team_loc.team_loc_id ='.$params['team_loc']);
					}],false);
				}],false)
				->joinWith([
					'taskInstruct' => function (\yii\db\ActiveQuery $query) use ($params, $data_query) {
						$query->innerJoin("(SELECT tbl_task_instruct.id, $data_query as task_date_time FROM tbl_task_instruct WHERE isactive=1 AND task_id IS NOT NULL AND task_id <> '') as A", 'tbl_task_instruct.id = A.id');
						$query->where(['isactive'=>1])->joinWith('taskPriority');
				}
			],false);

        if(isset($teamprojectparams['active']) && $teamprojectparams['active']="active" && $teamprojectparams['TeamSearch']['task_status']=="")
        	$query->andWhere('tbl_tasks.task_status IN (0,1,3)');
			if(isset($params['task_id'])) {
    			$query->andWhere('tbl_tasks.id = '.$params['task_id']);
    	}

		if(isset($params['comment']) && $params['comment']="comment") {
			$task_ids = (new Tasks)->getUnreadCommentsTeam($params['team_id'],$params['team_loc'], "task_ids");
			if (empty($task_ids)) {
			   $task_ids[0] = 0;
			}
			if (!empty($task_ids)) {
			   $query->andWhere('tbl_tasks.id IN ('.implode(",",$task_ids).')');
			}
		}

		if(isset($params['task_cancel']) && $params['task_cancel']=1)
			$query->andWhere(['task_cancel'=>1]);
		else
			$query->andWhere(['task_cancel'=>0]);


    	if(isset($params['task_closed']) && $params['task_closed']=1)
			$query->andWhere(['task_closed'=>1]);
		else
 			$query->andWhere(['task_closed'=>0]);

 		$drivername = Yii::$app->db->driverName;
		$currdate=time();
    	if(isset($params['due'])) {
    		if($params['due']=='past') {
    			if ($drivername == 'mysql') {
					$query->andWhere('tbl_task_instruct.task_duedate < "' . date('Y-m-d H:i:s', time()) . '"');
    			} else {
    				$query->andWhere("Cast((CAST(tbl_task_instruct.task_duedate as varchar)  + ' ' + tbl_task_instruct.task_timedue) as datetime) < '" . date('Y-m-d H:i:s', time()) . "'");
    			}
    			$query->andWhere('tbl_tasks.task_status !=4');
    		}
    		if($params['due']=='notpastdue') {
    			$query->andWhere('tbl_tasks.task_closed = 0 AND tbl_tasks.task_cancel = 0');
    		}
    	}

		if(isset($params['status']) && is_numeric($params['status'])) {
			$query->andWhere('tbl_tasks.task_status = '.$params['status']);
		}

 		$query->distinct();
 		$defaultSort="";
 		if($params['sort']=="") {
			$settings_info = Settings::find()->where("field = 'project_sort'")->one();
			$fieldvalue = $settings_info->fieldvalue;
			/*IRT 26*/
			$optionsproject_sort_display=Options::find()->select('project_sort_display')->where(['user_id'=>Yii::$app->user->identity->id])->andWhere('project_sort_display IS NOT NULL')->one()->project_sort_display;

			if(isset($optionsproject_sort_display) && in_array($optionsproject_sort_display, array(0,1,2,3))) {
				$fieldvalue = $optionsproject_sort_display;
			}
			/*IRT 26*/
			if ($fieldvalue == '0') {
				$defaultSort=['priority'=>SORT_ASC,'task_duedate'=>SORT_DESC,'instruct_task_timedue'=>SORT_DESC];
			} else if ($fieldvalue == '1') {
				$defaultSort=['task_duedate'=>SORT_DESC,'instruct_task_timedue'=>SORT_DESC];
			} else if ($fieldvalue == '2') {
				$defaultSort=['task_id'=>SORT_DESC];
			} else if($fieldvalue == '3') {
				$defaultSort=['team_priority' => SORT_ASC, 'priority' => SORT_ASC, 'task_duedate' => SORT_DESC];
			}
        }

       /* multiselect */
        if ($teamprojectparams['TeamSearch']['id'] != null && is_array($teamprojectparams['TeamSearch']['id'])) {
			if(!empty($teamprojectparams['TeamSearch']['id'])){
				foreach($teamprojectparams['TeamSearch']['id'] as $k=>$v){
					if($v=='All'){ // || strpos($v,",") !== false
						unset($teamprojectparams['TeamSearch']['id']);
					}
				}
			}
	//		$query->andFilterWhere(['tbl_tasks.id' => $teamprojectparams['TeamSearch']['id']]);
		}else{
	//		$query->andFilterWhere(['tbl_tasks.id' => $teamprojectparams['TeamSearch']['id']]);
		}

		 if ($teamprojectparams['TeamSearch']['task_id'] != null && is_array($teamprojectparams['TeamSearch']['task_id'])) {
			if(!empty($teamprojectparams['TeamSearch']['task_id'])){
				foreach($teamprojectparams['TeamSearch']['task_id'] as $k=>$v){
					if($v=='All'){ // || strpos($v,",") !== false
						unset($teamprojectparams['TeamSearch']['task_id']);
					}
				}
			}
		}

		if ($teamprojectparams['TeamSearch']['task_status'] != null && is_array($teamprojectparams['TeamSearch']['task_status'])) {
			if(!empty($teamprojectparams['TeamSearch']['task_status'])){
				foreach($teamprojectparams['TeamSearch']['task_status'] as $k=>$v){
					if($v=='All'){ // || strpos($v,",") !== false
						unset($teamprojectparams['TeamSearch']['task_status']);
					}
				}
			}
		}
		if ($teamprojectparams['TeamSearch']['priority'] != null && is_array($teamprojectparams['TeamSearch']['priority'])) {
			if(!empty($teamprojectparams['TeamSearch']['priority'])) {
				foreach($teamprojectparams['TeamSearch']['priority'] as $k=>$v) {
					if($v=='All') { // || strpos($v,",") !== false
						unset($teamprojectparams['TeamSearch']['priority']);
					}
				}
			}
		//	$query->andFilterWhere(["or like","priority",$teamprojectparams['TeamSearch']['priority']]);
		}
		if ($teamprojectparams['TeamSearch']['team_priority'] != null && is_array($teamprojectparams['TeamSearch']['team_priority'])) {
			if(!empty($teamprojectparams['TeamSearch']['team_priority'])){
				foreach($teamprojectparams['TeamSearch']['team_priority'] as $k=>$v){
					if($v=='All'){ // || strpos($v,",") !== false
						unset($teamprojectparams['TeamSearch']['team_priority']);
					}
				}
			}
	//		$query->andFilterWhere(["or like","tbl_priority_team.tasks_priority_name",$teamprojectparams['TeamSearch']['team_priority']]);
		}
		if ($teamprojectparams['TeamSearch']['project_name'] != null && is_array($teamprojectparams['TeamSearch']['project_name'])) {
			$project_namequery = "";
			$is_unset=false;
			if(!empty($teamprojectparams['TeamSearch']['project_name'])) {
				foreach($teamprojectparams['TeamSearch']['project_name'] as $k=>$v) {
					if($v=='(not set)'){
						$teamprojectparams['TeamSearch']['project_name'][$k]='';
					}
					if($project_namequery == ""){
						$project_namequery = "project_name='".$teamprojectparams['TeamSearch']['project_name'][$k]."'";
						if($teamprojectparams['TeamSearch']['project_name'][$k]==''){$teamprojectparams['TeamSearch']['project_name'][$k]='(not set)';}
					} else {
						$project_namequery .= " OR project_name='".$teamprojectparams['TeamSearch']['project_name'][$k]."'";
						if($teamprojectparams['TeamSearch']['project_name'][$k]==''){$teamprojectparams['TeamSearch']['project_name'][$k]='(not set)';}
					}
				}
			}
			if($is_unset==false){
			//	$query->andWhere("(".$project_namequery.")");
			}
			$this->project_name=$teamprojectparams['TeamSearch']['project_name'];
		}

		if(isset($teamprojectparams['TeamSearch']['per_complete'])){
                    $teamprojectparams['TeamSearch']['per_complete'] = str_replace( array('%','!','@','#','$','&','*','(',')','-','_','+','=','{','}','[',']',',','<','>','.','/','?',':',';','"','\''), '', $teamprojectparams['TeamSearch']['per_complete']);
                  //  if($teamprojectparams['TeamSearch']['per_complete']!="")
                  //      $query->andWhere(["ROUND(".(new \app\models\Tasks())->getTaskPercentageCompleteByTaskid('tbl_tasks.id').",0)"=>$teamprojectparams['TeamSearch']['per_complete']]);
		}

		if(isset($teamprojectparams['TeamSearch']['team_per_complete'])){
                    $teamprojectparams['TeamSearch']['team_per_complete'] = str_replace( array('%','!','@','#','$','&','*','(',')','-','_','+','=','{','}','[',']',',','<','>','.','/','?',':',';','"','\''), '', $teamprojectparams['TeamSearch']['team_per_complete']);
               	  //  if($teamprojectparams['TeamSearch']['team_per_complete']!="")
                  //      $query->andWhere(["ROUND(".(new \app\models\Tasks())->getTeamTaskPercentageCompleteByTaskid('tbl_tasks.id','tbl_tasks_teams.team_id','tbl_tasks_teams.team_loc').",0)"=>$teamprojectparams['TeamSearch']['team_per_complete']]);
		}

		if ($teamprojectparams['TeamSearch']['client_id'] != null && is_array ($teamprojectparams['TeamSearch']['client_id'])) {
				if(!empty($teamprojectparams['TeamSearch']['client_id'])) {
				foreach($teamprojectparams['TeamSearch']['client_id'] as $k=>$v) {
					if($v == 'All'){ // || strpos($v,",") !== false
						unset($teamprojectparams['TeamSearch']['client_id']); break;
					}
				}

			}
		}
		if ($teamprojectparams['TeamSearch']['client_case_id'] != null && is_array ($teamprojectparams['TeamSearch']['client_case_id'])) {
			if(!empty($teamprojectparams['TeamSearch']['client_case_id'])){
				foreach($teamprojectparams['TeamSearch']['client_case_id'] as $k=>$v){
					if($v=='All'){ // || strpos($v,",") !== false
						unset($teamprojectparams['TeamSearch']['client_case_id']);break;
					}
				}

			}
		}


		/*multiselect*/
        $this->load($params);
		 if(isset($teamprojectparams['TeamSearch']['task_id']) && $teamprojectparams['TeamSearch']['task_id']!="" && $teamprojectparams['TeamSearch']['task_id']!="All"){
		//   $query->andFilterWhere(['tbl_tasks.id' => $teamprojectparams['TeamSearch']['task_id']]);
		}
        $drivername = Yii::$app->db->driverName;
        if(isset($teamprojectparams['TeamSearch']['task_status']) && $teamprojectparams['TeamSearch']['task_status']!="" && $teamprojectparams['TeamSearch']['task_status']!="All") {
    		$this->task_status=$teamprojectparams['TeamSearch']['task_status'];
    		if(in_array(5,$teamprojectparams['TeamSearch']['task_status'])) {
			//	$query->andWhere('tbl_tasks.task_status IN (0,1,3)');
			} else if(in_array(6,$teamprojectparams['TeamSearch']['task_status'])) {
			//	$query->andWhere('tbl_tasks.task_status IN (0,1,3)');
				if ($drivername == 'mysql') {
			//		$query->andWhere('tbl_task_instruct.task_duedate < "' . date('Y-m-d H:i:s', time()) . '"');
    			} else {
    		//		$query->andWhere("Cast((CAST(tbl_task_instruct.task_duedate as varchar)  + ' ' + tbl_task_instruct.task_timedue) as datetime) < '" . date('Y-m-d H:i:s', time()) . "'");
    			}
    		//	$query->andWhere('tbl_tasks.task_status !=4');
			}else{
				if(!empty($teamprojectparams['TeamSearch']['task_status'])) {
					foreach($teamprojectparams['TeamSearch']['task_status'] as $k=>$v) {
						if($v=='All') { //  || strpos($v,",") !== false
							unset($teamprojectparams['TeamSearch']['task_status']);
						}
					}
				}

				$tStatus = array();
				foreach($teamprojectparams['TeamSearch']['task_status'] as $k => $v1) {
					$tStatus[$k] = $v1;
				}

			/*	if (in_array('7', $tStatus)) {
					$teamId = $params['team_id'];
					$query->joinWith(['comments' => function(\yii\db\ActiveQuery $query) use($userId,$teamId,$roleId) {
    					$query->select(['tbl_comments.id','tbl_comments.task_id']);
        				$query->where(['NOT IN','tbl_comments.id', CommentsRead::find()->select(	['comment_id'])->where(['user_id' => $userId]) ]);
        				$query->joinWith(['commentTeams','commentRoles']);
        				$query->andWhere('tbl_comment_teams.team_id='.$teamId.' OR tbl_comment_roles.role_id='.$roleId);
        				$query->andWhere('tbl_comments.created_by!='.$userId);
        			}]);
				}

				if(!array_search('7',$tStatus))	$tStatus = array_diff($tStatus, array('7'));

				$query->andFilterWhere(['or like', 'task_status', $tStatus]);*/
			}
		}
		if(isset($teamprojectparams['TeamSearch']['client_case_id']) && !empty($teamprojectparams['TeamSearch']['client_case_id'])){
        	$clientcasevid_sql = "SELECT id FROM tbl_tasks WHERE client_case_id IN (".implode(',',$teamprojectparams['TeamSearch']['client_case_id']).") Group By id";
      //  	$query->andWhere('tbl_tasks.id IN ('.$clientcasevid_sql.')');
					//$query->andWhere('tbl_client_case.id IN ('.implode(',',$teamprojectparams['TeamSearch']['client_case_id']).')');
        }
        if(isset($teamprojectparams['TeamSearch']['client_id']) && !empty($teamprojectparams['TeamSearch']['client_id'])){
					if($params['field']!="client_id")
					 	$clientcasevid_sql = "SELECT tbl_tasks.id FROM tbl_tasks inner join tbl_client_case on tbl_client_case.id = tbl_tasks.client_case_id WHERE tbl_client_case.client_id IN (".implode(',',$teamprojectparams['TeamSearch']['client_id']).") Group By tbl_tasks.id";
					else
						$clientcasevid_sql = "SELECT tbl_tasks.id FROM tbl_tasks inner join tbl_client_case on tbl_client_case.id = tbl_tasks.client_case_id Group By tbl_tasks.id";

      //  	 $query->andWhere('tbl_tasks.id IN ('.$clientcasevid_sql.')');
					// if($params['field']!='client_id')
					 //	$query->andWhere('tbl_client.id IN ('.implode(',',$teamprojectparams['TeamSearch']['client_id']).')');
        }
		if(isset($teamprojectparams['TeamSearch']['task_duedate']) && $teamprojectparams['TeamSearch']['task_duedate']!=""){
			$task_duedate=explode("-",$teamprojectparams['TeamSearch']['task_duedate']);
			$task_duedate_start = explode("/", trim($task_duedate[0]));
            $task_duedate_end = explode("/", trim($task_duedate[1]));
            $task_duedate_s = $task_duedate_start[2] . "-" . $task_duedate_start[0] . "-" . $task_duedate_start[1].' 00:00:00';
            $task_duedate_e = $task_duedate_end[2] . "-" . $task_duedate_end[0] . "-" . $task_duedate_end[1].' 23:59:59';
    //    	$query->andWhere(" A.task_date_time >= '$task_duedate_s' AND A.task_date_time  <= '$task_duedate_e' ");
		}
		if($params['field']=='id') {
			$query->select('tbl_tasks.id');
    		if(isset($params['q']) && $params['q']!="") {
                $query->andFilterWhere(['like', 'tbl_tasks.id', $params['q'].'%',false]);
    		}
    		$query->orderBy('tbl_tasks.id');
    		$dataProvider = ArrayHelper::map($query->all(),'id','id');
    	}
    	if($params['field']=='priority' || $params['field'] == 'task_priority') {
    		$query->select('tbl_task_instruct.task_priority');
    		if(isset($params['q']) && $params['q']!="") {
                $query->andFilterWhere(['like', 'priority', $params['q']]);
            }
            //$query->distinct('tbl_priority_project.priority');
    		//$dataProvider = ArrayHelper::map($query->all(),'id','taskInstruct.taskPriority.priority'); /*->orderBy('priority_order')*/
            $dataProvider = ArrayHelper::map(PriorityProject::find()->select(['priority'])->where(['in','id',$query])->groupBy('priority')->all(),'priority','priority');
    	}
    	if($params['field']=='team_priority'){
			//echo "<pre>",print_r($params),"</pre>";die;
			$sql="SELECT team_loc_prority FROM  tbl_tasks_teams WHERE team_id=".$params['team_id']." AND team_loc=".$params['team_loc'];
			/*$query->select('tbl_tasks.team_priority');
			if(isset($params['q']) && $params['q']!=""){
                $query->andFilterWhere(['like', 'tasks_priority_name', $params['q']]);
            }*/
            $myqurey=PriorityTeam::find()->where("id in ($sql)");
            if(isset($params['q']) && $params['q']!="") {
				$myqurey->andFilterWhere(['like', 'tasks_priority_name', $params['q']]);
			}
            $dataProvider = ArrayHelper::map($myqurey->select(['tasks_priority_name'])->groupBy('tasks_priority_name')->all(),'tasks_priority_name','tasks_priority_name');
		}

    	if($params['field']=='project_name'){
    		$query->select('tbl_tasks.id');
    		if(isset($params['q']) && $params['q']!=""){
                $query->andFilterWhere(['like', 'project_name', $params['q'].'%',false]);
            }
    		//$dataProvider = ArrayHelper::map($query->groupBy('tbl_task_instruct.project_name')->all(),'id','taskInstruct.project_name');
            //$query->groupBy('tbl_task_instruct.project_name')->all()
            $dataProvider = ArrayHelper::map(TaskInstruct::find()->select(['project_name'])->where(['in','tbl_task_instruct.task_id',$query])->innerJoinWith(['taskInstructServicetasksWithoutOrder'=>function(\yii\db\ActiveQuery $query) use($params) { $query->where(['tbl_task_instruct_servicetask.team_id' => $params['team_id'],'tbl_task_instruct_servicetask.team_loc' => $params['team_loc']]); }],false)->andWhere('isactive=1')->groupBy('project_name')->all(), 'project_name', 'project_name');
         }
         if($params['field'] == 'client_case_id') {
					 	if(isset($teamprojectparams['TeamSearch']['client_id']) && !empty($teamprojectparams['TeamSearch']['client_id'])){
					 			$clientcasevid_sql = "SELECT tbl_tasks.id FROM tbl_tasks inner join tbl_client_case on tbl_client_case.id = tbl_tasks.client_case_id WHERE tbl_client_case.client_id IN (".implode(',',$teamprojectparams['TeamSearch']['client_id']).") Group By tbl_tasks.id";

		   	 				$query->andWhere('tbl_tasks.id IN ('.$clientcasevid_sql.')');
			 			}
			$query->select('tbl_tasks.client_case_id');
    		if(isset($params['q']) && $params['q']!="") {
    			$query->andFilterWhere(['like', "tbl_client_case.case_name", $params['q']]);
    		}
    		$data = ClientCase::find()->select(['tbl_client_case.id','tbl_client_case.case_name'])->where(['tbl_client_case.id'=>$query])->orderBy(['tbl_client_case.case_name'=> SORT_ASC])->joinWith('client',false)->asArray()->all();
    		foreach($data as $d => $value) {
				$dataProvider[$value['id']] = html_entity_decode($value['case_name']);
			}
			$dataProvider = array('All'=>'All') + $dataProvider;
			return $dataProvider;
		 }
         if($params['field'] == 'client_id') {
			$query->select('tbl_client_case.client_id');
    		if(isset($params['q']) && $params['q']!="") {
    			$query->andFilterWhere(['like', "tbl_client.client_name", $params['q']]);
    		}
    		$data = ClientCase::find()->select(['tbl_client.id','tbl_client.client_name'])->where(['tbl_client_case.client_id'=>$query])->orderBy(['tbl_client.client_name'=> SORT_ASC])->joinWith('client',false)->asArray()->all();

    		foreach($data as $d => $value) {
                    $dataProvider[$value['id']] = html_entity_decode($value['client_name']);
                }
                $dataProvider = array('All'=>'All') + $dataProvider;
                return $dataProvider;
		}

    	if(isset($params['task_cancel'])){
    		$query->andFilterWhere(['like', 'task_cancel', 1]);
    	}
    	if(isset($params['task_closed'])){
    		$query->andFilterWhere(['like', 'task_closed', 1]);
    	}

    	return array_merge(array(''=>'All'), $dataProvider);
	}
	public function searchFilter($params){
		$roleId = Yii::$app->user->identity->role_id; // Role Id
		$userId = Yii::$app->user->identity->id;

		$dataProvider = array();

		$timezoneOffset = (new Options)->getOffsetOfCurrenttimeZone();
		$query = Tasks::find()
    			->select(['tbl_tasks.id',/*'tbl_tasks.team_priority'*/'tbl_tasks_teams.team_loc_prority as team_priority','tbl_tasks.task_status','tbl_tasks.task_closed','tbl_tasks.task_cancel','tbl_tasks.task_cancel_reason','tbl_client_case.case_name','tbl_task_instruct.project_name','tbl_task_instruct.task_duedate','tbl_task_instruct.task_timedue','tbl_task_instruct.task_priority','tbl_priority_project.priority_order', 'tbl_priority_project.priority', 'tbl_priority_team_loc.priority_order as teamorder','(SELECT CASE WHEN (team_priority IS NULL OR team_priority =0) THEN ((SELECT count( * )FROM tbl_priority_team) +1) ELSE team.priority_order END AS final_priority FROM tbl_tasks AS task LEFT JOIN tbl_priority_team as team ON tasksTeams.team_priority = team.id where task.id = tbl_tasks.id) as team_priority_order'])
    			//->joinWith('clientCase')
    			->joinWith(['clientCase' => function(\yii\db\ActiveQuery $query) use($roleId,$userId){
					if($roleId!=0){
						$query->joinWith(['projectSecurity' => function(\yii\db\ActiveQuery $query) use ($userId){
							$query->where(['tbl_project_security.user_id' => $userId]);
						}]);
					}
				}])
    			->where(['tbl_client_case.is_close'=>0])
                ->joinWith([
                	'taskInstruct' => function (\yii\db\ActiveQuery $query) use ($params){
                		$query->where(['isactive'=>1])->joinWith('taskPriority')->innerJoinWith(['taskInstructServicetasksWithoutOrder'=>function(\yii\db\ActiveQuery $query) use($params) { $query->where(['tbl_task_instruct_servicetask.team_id'=>$params['team_id'],'tbl_task_instruct_servicetask.team_loc'=>$params['team_loc']]); }]);
					}
				],false)
                ->joinWith(['teamPriority' => function(\yii\db\ActiveQuery $query){
					$query->joinWith('priorityTeamLoc');
				}]);

        if(isset($params['active']) && $params['active']="active" && $teamprojectparams['TeamSearch']['task_status']=="")
			$query->andWhere('tbl_tasks.task_status IN (0,1,3)');

		if(isset($params['task_id'])){
    		$query->andWhere('tbl_tasks.id = '.$params['task_id']);
    	}

		if(isset($params['comment']) && $params['comment']="comment"){
			$task_ids = (new Tasks)->getUnreadCommentsTeam($params['team_id'],$params['team_loc'], "task_ids");
			if (empty($task_ids)) {
			   $task_ids[0] = 0;
			}
			if (!empty($task_ids)) {
			   $query->andWhere('tbl_tasks.id IN ('.implode(",",$task_ids).')');
			}
		}

		if(isset($params['task_cancel']) && $params['task_cancel']=1)
			$query->andWhere(['task_cancel'=>1]);
		else
			$query->andWhere(['task_cancel'=>0]);


    	if(isset($params['task_closed']) && $params['task_closed']=1)
			$query->andWhere(['task_closed'=>1]);
		else
 			$query->andWhere(['task_closed'=>0]);
 		$drivername = Yii::$app->db->driverName;
		$currdate=time();
    	if(isset($params['due'])){
    		if($params['due']=='past'){
    			if ($drivername == 'mysql') {
					$query->andWhere('tbl_task_instruct.task_duedate < "' . date('Y-m-d H:i:s', time()) . '"');
    			} else {
    				$query->andWhere("Cast((CAST(tbl_task_instruct.task_duedate as varchar)  + ' ' + tbl_task_instruct.task_timedue) as datetime) < '" . date('Y-m-d H:i:s', time()) . "'");
    			}
    			$query->andWhere('tbl_tasks.task_status !=4');
    		}
    		if($params['due']=='notpastdue'){
    			$query->andWhere('tbl_tasks.task_closed = 0 AND tbl_tasks.task_cancel = 0');
    		}
    	}

		if(isset($params['status']) && is_numeric($params['status'])){
			$query->andWhere('tbl_tasks.task_status = '.$params['status']);
		}

 		$query->distinct();

		if($params['field']=='id') {
			$query->select('tbl_tasks.id');
    		if(isset($params['q']) && $params['q']!="") {
                $query->andFilterWhere(['like', 'tbl_tasks.id', $params['q'].'%',false]);
    		}
    		$query->orderBy('tbl_tasks.id');
    		$dataProvider = ArrayHelper::map($query->all(),'id','id');
    	}
    	if($params['field']=='priority' || $params['field'] == 'task_priority') {
    		$query->select('tbl_task_instruct.task_priority');
    		if(isset($params['q']) && $params['q']!="") {
                $query->andFilterWhere(['like', 'priority', $params['q']]);
            }
            //$query->distinct('tbl_priority_project.priority');
    		//$dataProvider = ArrayHelper::map($query->all(),'id','taskInstruct.taskPriority.priority'); /*->orderBy('priority_order')*/
            $dataProvider = ArrayHelper::map(PriorityProject::find()->select(['priority'])->where(['in','id',$query])->groupBy('priority')->all(),'priority','priority');
    	}
    	if($params['field']=='team_priority'){
			//echo "<pre>",print_r($params),"</pre>";die;
			$sql="SELECT team_loc_prority FROM  tbl_tasks_teams WHERE team_id=".$params['team_id']." AND team_loc=".$params['team_loc'];
			/*$query->select('tbl_tasks.team_priority');
			if(isset($params['q']) && $params['q']!=""){
                $query->andFilterWhere(['like', 'tasks_priority_name', $params['q']]);
            }*/
            $myqurey=PriorityTeam::find()->where("id in ($sql)");
            if(isset($params['q']) && $params['q']!="") {
				$myqurey->andFilterWhere(['like', 'tasks_priority_name', $params['q']]);
			}
            $dataProvider = ArrayHelper::map($myqurey->select(['tasks_priority_name'])->groupBy('tasks_priority_name')->all(),'tasks_priority_name','tasks_priority_name');
		}

    	if($params['field']=='project_name'){
    		$query->select('tbl_tasks.id');
    		if(isset($params['q']) && $params['q']!=""){
                $query->andFilterWhere(['like', 'project_name', $params['q'].'%',false]);
            }
    		//$dataProvider = ArrayHelper::map($query->groupBy('tbl_task_instruct.project_name')->all(),'id','taskInstruct.project_name');
            //$query->groupBy('tbl_task_instruct.project_name')->all()
            $dataProvider = ArrayHelper::map(TaskInstruct::find()->select(['project_name'])->where(['in','tbl_task_instruct.task_id',$query])->innerJoinWith(['taskInstructServicetasksWithoutOrder'=>function(\yii\db\ActiveQuery $query) use($params) { $query->where(['tbl_task_instruct_servicetask.team_id' => $params['team_id'],'tbl_task_instruct_servicetask.team_loc' => $params['team_loc']]); }],false)->andWhere('isactive=1')->groupBy('project_name')->all(), 'project_name', 'project_name');
         }
         if($params['field'] == 'client_case_id') {
			$query->select('tbl_tasks.client_case_id');
    		if(isset($params['q']) && $params['q']!="") {
    			$query->andFilterWhere(['like', "tbl_client_case.case_name", $params['q']]);
    		}
    		$data = ClientCase::find()->select(['tbl_client_case.id','tbl_client_case.case_name'])->where(['tbl_client_case.id'=>$query])->orderBy(['tbl_client_case.case_name'=> SORT_ASC])->joinWith('client',false)->asArray()->all();
    		foreach($data as $d => $value) {
				$dataProvider[$value['id']] = html_entity_decode($value['case_name']);
			}
			$dataProvider = array('All'=>'All') + $dataProvider;
			return $dataProvider;
		 }
         if($params['field'] == 'client_id') {
			$query->select('tbl_client_case.client_id');
    		if(isset($params['q']) && $params['q']!="") {
    			$query->andFilterWhere(['like', "tbl_client.client_name", $params['q']]);
    		}
    		$data = ClientCase::find()->select(['tbl_client.id','tbl_client.client_name'])->where(['tbl_client_case.client_id'=>$query])->orderBy(['tbl_client.client_name'=> SORT_ASC])->joinWith('client',false)->asArray()->all();

    		foreach($data as $d => $value) {
                    $dataProvider[$value['id']] = html_entity_decode($value['client_name']);
                }
                $dataProvider = array('All'=>'All') + $dataProvider;
                return $dataProvider;
		}

    	if(isset($params['task_cancel'])){
    		$query->andFilterWhere(['like', 'task_cancel', 1]);
    	}
    	if(isset($params['task_closed'])){
    		$query->andFilterWhere(['like', 'task_closed', 1]);
    	}

    	return array_merge(array(''=>'All'), $dataProvider);
	}

	public function loadteamtasks($params){
		$dataProvider = array();

		$query = TasksUnits::find()->select(['tbl_tasks_units.id','tbl_tasks_units.task_instruct_servicetask_id','tbl_tasks_units.task_id','tbl_tasks_units.unit_assigned_to'])
		->innerJoinWith(['taskInstructServicetask'=>function(\yii\db\Activequery $query)use($params){
			$query->where(['tbl_task_instruct_servicetask.team_id'=>$params['team_id'],'tbl_task_instruct_servicetask.team_loc'=>$params['team_loc']])
			->innerJoinWith(['taskInstruct'=>function(\yii\db\Activequery $query){
					$query->where(['tbl_task_instruct.isactive'=>1])
					->joinWith('taskPriority');
				 }])
			->innerJoinWith('servicetask')->innerJoinWith('teamLoc'); }])
		->innerJoinWith(['tasks'=>function(\yii\db\Activequery $query){
				$query->where(['tbl_tasks.task_closed'=>0,'tbl_tasks.task_cancel'=>0])
				->joinwith(['clientCase'=>function(\yii\db\Activequery $query){
						$query->where(['tbl_client_case.is_close'=>0]);
					}])
				->joinwith('client')
				->joinwith('teamPriority');
			}])
		->joinWith('assignedUser');

		if(isset($params['task_id'])){
    		$query->andWhere('tbl_tasks.id = '.$params['task_id']);
    	}


		$dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' =>['pageSize'=>'25'],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        return $dataProvider;

		/*$query = "SELECT * FROM `tbl_tasks_units` as units INNER JOIN tbl_task_instruct_servicetask as service ON units.task_instruct_servicetask_id = service.id INNER JOIN tbl_task_instruct as instruct ON service.task_instruct_id = instruct.id INNER JOIN tbl_tasks as task ON units.task_id = task.id LEFT JOIN tbl_client as client ON task.client_id = client.id LEFT JOIN tbl_client_case as clientCase ON task.client_case_id=clientCase.id
		WHERE instruct.isactive = 1 AND service.team_id = 2 AND service.team_loc = 4 AND task.task_closed=0 AND task.task_cancel=0 AND clientCase.is_close=0";

		$dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' =>['pageSize'=>'25'],
        ]);*/
	}
}
