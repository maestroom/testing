<?php
namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

use app\models\Tasks;
use app\models\Settings;
use app\models\Options;
use app\models\ClientCase;
use app\models\Role;
use app\models\CommentsRead;
use app\models\CommentRoles;
use app\models\CommentTeams;
use app\models\PriorityProject;
use app\models\TaskInstruct;
/**
 * EvidenceSearch represents the model behind the search form about `app\models\Evidence`.
 */
class TaskSearch extends Tasks
{

	public $client_id;
	public $project_name = NULL;
	public $porder=NULL; // task prority order
	public $pname=NULL; // task prority name
	public $ispastdue=NULL; // task prority name
	public $task_duedate=NULL;
	public $task_duetime=NULL;
	public $client_name=NULL;
	public $clientcase_name=NULL;
	public $task_date_time=NULL;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'client_case_id', 'task_status', 'task_duedate','task_duetime', 'priority', 'project_name','porder','pname', 'task_closed', 'task_cancel', 'created', 'created_by', 'modified', 'modified_by','client_name','clientcase_name','task_date_time'], 'safe'],
            [['sales_user_id', 'task_closed', 'task_cancel', 'team_priority', 'created_by', 'modified_by'], 'integer'],
            [['task_complete_date', 'created', 'modified','per_complete','client_id'], 'safe'],
            [['task_cancel_reason'], 'string'],
        ];
    }

    public function attributes()
    {
        // add related fields to searchable attributes
        //return array_merge(parent::attributes(), ['task_duedate', 'priority', 'project_name', 'per_complete']);
		return array_merge(parent::attributes(), ['task_duedate','task_duetime', 'priority', 'project_name', 'per_complete', 'task_date_time','porder','pname','client_name','clientcase_name']);
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function searchGlobalProject($params)
    {
        /* IRT 75 get RoleType */
        $roleId = Yii::$app->user->identity->role_id; // Role Id
        $userId = Yii::$app->user->identity->id;
		$role_info = $_SESSION['role'];
        //$role_type = explode(',',Role::findOne($roleId)->role_type);
		$role_type = explode(',',$role_info->role_type);
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
		/*INNER JOIN (SELECT tbl_task_instruct.id FROM tbl_task_instruct WHERE isactive=1 AND task_id IS NOT NULL AND task_id<>'') as A ON tbl_task_instruct.id = A.id*/
		$todaysdate = (new Options)->ConvertOneTzToAnotherTz(date('Y-m-d H:i:s'), 'UTC', $_SESSION['usrTZ'], "YMDHIS");
    	if (Yii::$app->db->driverName == 'mysql'){
			$task_completedate = " CONVERT_TZ(tasks.task_complete_date,'+00:00','".$timezoneOffset."') ";
			$sqlpastdue.=" AND A.task_date_time < CASE WHEN tasks.task_complete_date!='0000-00-00 00:00:00' AND tasks.task_complete_date!='' AND tasks.task_status = 4 THEN '$task_duedatetime' ELSE '$todaysdate' END";
        }else{
			$task_completedate = " CAST(switchoffset(todatetimeoffset(tasks.task_complete_date, '+00:00'), '$timezoneOffset') as DATETIME) ";
        	$sqlpastdue.=" AND A.task_date_time < CASE WHEN CAST(tasks.task_complete_date as varchar)!='0000-00-00 00:00:00' AND CAST(tasks.task_complete_date as varchar) IS NOT NULL AND tasks.task_status = 4 THEN '$task_duedatetime' ELSE '$todaysdate' END";
        }
        /*if (Yii::$app->db->driverName == 'mysql') {
                $data_query = "DATE_FORMAT( CONVERT_TZ(CONCAT(tbl_task_instruct.`task_duedate` , ' ', STR_TO_DATE(tbl_task_instruct.`task_timedue` , '%l:%i %p' )),'+00:00','{$timezoneOffset}'), '%Y-%m-%d %H:%i')";
        } else {
                $data_query = "CAST(switchoffset(todatetimeoffset(Cast((CAST(tbl_task_instruct.task_duedate as varchar)  + ' ' + tbl_task_instruct.task_timedue) as datetime), '+00:00'), '{$timezoneOffset}') as datetime)";
        }*/

        if (Yii::$app->db->driverName == 'mysql') {
                $data_query = "getDueDateTimeByUsersTimezone('{$timezoneOffset}',tbl_task_instruct.task_duedate,tbl_task_instruct.task_timedue,'%Y-%m-%d %H:%i:%s')";
        } else {
                $data_query = "[dbo].getDueDateTimeByUsersTimezone('{$timezoneOffset}',tbl_task_instruct.task_duedate,tbl_task_instruct.task_timedue,'%Y-%m-%d %H:%i:%s')";
        }

    	/**
    	 * IRT 169 Remove priority_order
		 *,'A.task_date_time as task_date_time'
		 *,'('.$sqlpastdue.') as ispastdue'
    	 */

    	$query = Tasks::find()
			->select(['tbl_tasks.id','client_name as client_name','case_name as clientcase_name',"CONCAT(client_name,' ',case_name) as client_case_name",'tbl_tasks.created','tbl_tasks.client_case_id','tbl_tasks.task_status','tbl_tasks.task_closed','tbl_tasks.task_cancel','tbl_tasks.task_cancel_reason','tbl_client_case.case_name','tbl_task_instruct.project_name as project_name','tbl_task_instruct.task_duedate as task_duedate','tbl_task_instruct.task_timedue as task_duetime','tbl_task_instruct.task_priority','tbl_priority_project.priority_order as porder', 'tbl_priority_project.priority as pname'])

			->joinWith(['clientCase'=> function (\yii\db\ActiveQuery $query) use ($userId, $roleId, $role_type){
				$query->joinWith(['client'],false);
				if($roleId!=0 && $role_type[0]!=2) { // role type is Case Manager && $role_type[0]!=2
					$query->joinWith(['projectSecurity' => function(\yii\db\ActiveQuery $query) use ($userId) {
						$query->where(['tbl_project_security.user_id' => $userId]);
					}]);
				}
			}],false)->joinWith(['taskInstruct' => function (\yii\db\ActiveQuery $query) use ($params, $role_type, $data_query){

				//$query->innerJoin("(SELECT tbl_task_instruct.id, $data_query as task_date_time FROM tbl_task_instruct WHERE isactive=1 AND task_id IS NOT NULL AND task_id<>'') as A", 'tbl_task_instruct.id = A.id');

				$query->where(['isactive'=>1])->joinWith('taskPriority');
				/* IRT 75 Show only casemanager team location records */
				if($role_type[0] == 1 && $role_type[1] != 2){
					$query->joinWith(['tasksUnits' => function(\yii\db\ActiveQuery $query) use ($params){
						$query->select(['tbl_tasks_units.task_instruct_id']);
						$query->where('tbl_tasks_units.team_id=1 AND tbl_tasks_units.team_loc=0');
					}]);
				}
			}],false) // FOR Join With Pre Load Issue
			->joinWith(['teamPriority'],false);
			if($role_type[0]!=1) { // role type team manager
				$query->joinWith(['tasksTeams' => function(\yii\db\ActiveQuery $query) use ($roleId, $userId){
					if($roleId!=0) {
						$query->joinWith(['projectSecurity' => function(\yii\db\ActiveQuery $query) use ($userId) {
							$query->where(['tbl_project_security.user_id' => $userId]);
						}]);
					}
				}]);
			}

        $query->distinct=true; // dISTINCT
    	$settings_info = Settings::find()->select(['fieldvalue'])->where(['field' => 'project_sort'])->one();
		$fieldvalue = $settings_info->fieldvalue;
    	$defaultSort="";
    	if(isset($params['sort'])) {
		} else {
			/*IRT 26*/
			//$option_data=$_SESSION['options'];
			$optionsproject_sort_display=Options::find()->select('project_sort_display')->where(['user_id'=>Yii::$app->user->identity->id])->one()->project_sort_display;
			//$optionsproject_sort_display=$option_data->project_sort_display;
			if(in_array($optionsproject_sort_display, array(0,1,2,3)) && $optionsproject_sort_display != NULL) {
				$fieldvalue = $optionsproject_sort_display;
			}
			/*IRT 26*/
	    	if ($fieldvalue == '' || $fieldvalue == '0') {
	    		//$query->orderBy('tbl_priority_project.priority_order ASC,tbl_task_instruct.task_duedate DESC');
	    		$defaultSort=['priority'=>SORT_ASC,'task_duedate'=>SORT_DESC];
	    	} else if ($fieldvalue == '1') {
	    		//$query->orderBy('tbl_task_instruct.task_duedate DESC');
	    		$defaultSort=['task_duedate'=>SORT_DESC];
	    	} else if ($fieldvalue == '2') {
	    		//$query->orderBy('tbl_tasks.id DESC');
	    		$defaultSort=['id'=>SORT_DESC];
	    	} else if ($fieldvalue == '3') {
				/**
				 * IRT 169 Remove priority_order
				 */
	    		//$query->orderBy('tbl_priority_team.priority_order asc, tbl_priority_project.priority_order asc,tbl_task_instruct.task_duedate desc');
	    		$defaultSort=['priority'=>SORT_ASC,'task_duedate'=>SORT_DESC]; // 'priority_team'=>SORT_ASC,
	    	} else {
	    		//$query->orderBy('tbl_tasks.created desc');
	    		$defaultSort=['created'=>SORT_DESC];
	    	}
    	}

    	$dataProvider = new ActiveDataProvider([
    			'query' => $query,
    			'pagination' =>['pageSize'=>25],
    	]);
    	$dataProvider->sort->enableMultiSort=true;
    	//echo "<pre>",print_r($defaultSort),"</pre>";die;
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
			$dataProvider->sort->defaultOrder=$defaultSort;
		}
		/**
    	 * IRT 169 Remove priority_order
    	 */
    	/*$dataProvider->sort->attributes['priority_team'] = [
            'asc' => ['tbl_priority_team.priority_order' => SORT_ASC],
            'desc' => ['tbl_priority_team.priority_order' => SORT_DESC],
        ];*/
        $dataProvider->sort->attributes['created'] = [
            'asc' => ['tbl_tasks.created' => SORT_ASC],
            'desc' => ['tbl_tasks.created' => SORT_DESC],
        ];

    	$dataProvider->sort->attributes['id'] = [
    			'asc' => ['tbl_tasks.id' => SORT_ASC],
    			'desc' => ['tbl_tasks.id' => SORT_DESC],
    	];
    	$dataProvider->sort->attributes['task_status'] = [
    			'asc' => ['task_status' => SORT_ASC],
    			'desc' => ['task_status' => SORT_DESC],
    	];
    	$dataProvider->sort->attributes['task_duedate'] = [
    			//'asc' => ['A.task_date_time' => SORT_ASC],
    			//'desc' => ['A.task_date_time' => SORT_DESC],
				'asc' => ['tbl_task_instruct.task_duedate' => SORT_ASC],
    			'desc' => ['tbl_task_instruct.task_duedate' => SORT_DESC],
    	];
    	$dataProvider->sort->attributes['priority'] = [
            'asc' => ['tbl_priority_project.priority_order' => SORT_ASC],
            'desc' => ['tbl_priority_project.priority_order' => SORT_DESC],
        ];
    	$dataProvider->sort->attributes['project_name'] = [
    			'asc' => ['project_name' => SORT_ASC],
    			'desc' => ['project_name' => SORT_DESC],
    	];
    	$dataProvider->sort->attributes['client_case_id'] = [
    			'asc' => ["client_case_name" => SORT_ASC],
    			'desc' => ["client_case_name" => SORT_DESC],
    	];
    	$dataProvider->sort->attributes['client_id'] = [
    			'asc' => ["tbl_client.client_name" => SORT_ASC],
    			'desc' => ["tbl_client.client_name" => SORT_DESC],
    	];
    	/*multiselect*/
        if ($params['TaskSearch']['id'] != null && is_array($params['TaskSearch']['id'])) {
			if(!empty($params['TaskSearch']['id'])){
				foreach($params['TaskSearch']['id'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($params['TaskSearch']['id']);
					}
				}
			}
		}
		if ($params['TaskSearch']['task_status'] != null && is_array($params['TaskSearch']['task_status'])) {
			if(!empty($params['TaskSearch']['task_status'])){
				foreach($params['TaskSearch']['task_status'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($params['TaskSearch']['task_status']);
					}
				}
			}
		}
		if ($params['TaskSearch']['client_case_id'] != null && is_array($params['TaskSearch']['client_case_id'])) {
			if(!empty($params['TaskSearch']['client_case_id'])){
				foreach($params['TaskSearch']['client_case_id'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($params['TaskSearch']['client_case_id']);
					}
				}
			}
		}
		if ($params['TaskSearch']['priority'] != null && is_array($params['TaskSearch']['priority'])) {
			if(!empty($params['TaskSearch']['priority'])){
				foreach($params['TaskSearch']['priority'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($params['TaskSearch']['priority']);
					}
				}
			}
		}
		if ($params['TaskSearch']['project_name'] != null && is_array($params['TaskSearch']['project_name'])) {
			if(!empty($params['TaskSearch']['project_name'])){
				foreach($params['TaskSearch']['project_name'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($params['TaskSearch']['project_name']);
					}
				}
			}
		}
		if ($params['TaskSearch']['client_id'] != null && is_array($params['TaskSearch']['client_id'])) {
			if(!empty($params['TaskSearch']['client_id'])){
				foreach($params['TaskSearch']['client_id'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($params['TaskSearch']['client_id']);
					}
				}
			}
		}
		/*multiselect*/

		$this->load($params);
		if(isset($params['TaskSearch']['id']) && $params['TaskSearch']['id']!="" && $params['TaskSearch']['id']!="All")
    		$query->andFilterWhere(['tbl_tasks.id' => $params['TaskSearch']['id']]);

    	if(isset($params['TaskSearch']['task_status']) && $params['TaskSearch']['task_status']!="" && $params['TaskSearch']['task_status']!="All")
    		$query->andFilterWhere(['or like', 'task_status', $params['TaskSearch']['task_status']]);


		if(isset($params['TaskSearch']['task_duedate']) && $params['TaskSearch']['task_duedate']!=""){
			$task_duedate = explode("-", $params['TaskSearch']['task_duedate']);
            $task_duedate_start = explode("/", trim($task_duedate[0]));
            $task_duedate_end = explode("/", trim($task_duedate[1]));
            $task_duedate_s = $task_duedate_start[2] . "-" . $task_duedate_start[0] . "-" . $task_duedate_start[1].' 00:00:00';
            $task_duedate_e = $task_duedate_end[2] . "-" . $task_duedate_end[0] . "-" . $task_duedate_end[1].' 23:59:59';
          	$query->andWhere(" $data_query >= '$task_duedate_s' AND $data_query  <= '$task_duedate_e' ");
        }


    	if(isset($params['TaskSearch']['priority']) && $params['TaskSearch']['priority']!="" && $params['TaskSearch']['priority']!="All"){
			$query->andFilterWhere(['or like', 'priority', $params['TaskSearch']['priority'],false]);
		}
		if(isset($params['TaskSearch']['client_case_id']) && $params['TaskSearch']['client_case_id']!="" && $params['TaskSearch']['client_case_id']!="All"){
    		$query->andWhere('tbl_client_case.id IN ('.implode(',',$params['TaskSearch']['client_case_id']).')');
    	}
		if(isset($params['TaskSearch']['client_id']) && $params['TaskSearch']['client_id']!="" && $params['TaskSearch']['client_id']!="All"){
    		$query->andWhere('tbl_client_case.client_id IN ('.implode(',',$params['TaskSearch']['client_id']).')');
    	}
    	if(isset($params['TaskSearch']['task_cancel_reason']) && $params['TaskSearch']['task_cancel_reason']!="" && $params['TaskSearch']['task_cancel_reason']!="All"){
    		$query->andFilterWhere(['like', 'task_cancel_reason', $params['TaskSearch']['task_cancel_reason'],false]);
    	}

    	if(isset($params['TaskSearch']['project_name']) && $params['TaskSearch']['project_name']!="All"){
			if(is_array($params['TaskSearch']['project_name'])){
			$project_namequery = "";
			$is_unset=false;
			if(!empty($params['TaskSearch']['project_name'])) {
				foreach($params['TaskSearch']['project_name'] as $k=>$v) {
					if($v=='(not set)'){
						$params['TaskSearch']['project_name'][$k]='';
					} /*else if(strpos($v,",") !== false) {
						unset($params['TaskSearch']['project_name']);
						$is_unset=true; break;
					}*/
					if($project_namequery == "") {
						$project_namequery = "project_name='".$params['TaskSearch']['project_name'][$k]."'";
						if($params['TaskSearch']['project_name'][$k]==''){$params['TaskSearch']['project_name'][$k]='(not set)';}
					} else {
						$project_namequery .= " OR project_name='".$params['TaskSearch']['project_name'][$k]."'";
						if($params['TaskSearch']['project_name'][$k]==''){$params['TaskSearch']['project_name'][$k]='(not set)';}
					}
				}

				if($is_unset==false){
					$query->andWhere("(".$project_namequery.")");
				}
				//$query->andFilterWhere(['or like', 'project_name' , $params['TeamSearch']['project_name']]);
				$this->project_name=$params['TaskSearch']['project_name'];
			}
			}else{
				$query->andFilterWhere(['like', 'project_name', $params['TaskSearch']['project_name']]);
				$this->project_name=$params['TaskSearch']['project_name'];
			}
    	}
    	//echo "<pre>",print_r($params),"</pre>";die;
    	/* Dynamic Filter Start */
    	//if(isset($params['by_cleint'])){
		if(isset($params['clientCases'])){ // && is_array($params['clientCases'])){
			$clientCases=json_decode(str_replace("'", '"',$params['clientCases']),true);
			if(!empty($clientCases)){
				foreach($clientCases as $ccval){
					//print_r($ccval);
					$implode_cleintcases = explode(",",$ccval);
					$params['cleints'][$implode_cleintcases[0]]=$implode_cleintcases[0];
					$params['cleintscase'][$implode_cleintcases[1]]=$implode_cleintcases[1];
				}
			}

			//$implode_clientcase = '('.implode("),(",$params['clientCases']).')';
			//$query->andWhere('(tbl_client_case.client_i,tbl_client_case.id) IN ('.$implode_clientcase.')');
		}
        if(isset($params['cleints']) && is_array($params['cleints'])){
                $implode_cleints = implode(",",$params['cleints']);
        }else{
                $implode_cleints = $params['cleints'];
        }
        if($params['cleintscase'] && is_array($params['cleintscase'])){
                $implode_clientcase = implode(",",$params['cleintscase']);
        } else {
                $implode_clientcase = $params['cleintscase'];
        }
        if(!empty($params['cleints'])) {
                $query->andWhere('tbl_client_case.client_id IN ('.$implode_cleints.')');
        } if(!empty($params['cleintscase'])) {
                $query->andWhere('tbl_client_case.id IN ('.$implode_clientcase.')');
        }

    	//}
    	//if(isset($params['by_team'])){

			if(isset($params['taskStat'])) {
				$taskstats=json_decode(str_replace("'", '"',$params['taskStat']),true);
				if(!empty($taskstats)) {
					$params['taskstatuss']=$taskstats;
				}
				//print_r($params['taskstatuss']);die;
			}
			if(isset($params['todoStat'])) {
				$todostats=json_decode(str_replace("'", '"',$params['todoStat']),true);
				if(!empty($todostats)) {
					$params['todotatus']=$todostats;
				}
				//print_r($params['taskstatuss']);die;
			}
			if(isset($params['teamLocs'])) {
				$teamlocs=json_decode(str_replace("'", '"',$params['teamLocs']),true);
				if(!empty($teamlocs)) {
					foreach($teamlocs as $tlval) {
						$implode_teanloc = explode(",",$tlval);
						$params['teams'][$implode_teanloc[0]]=$implode_teanloc[0];
						$params['teamloc'][$implode_teanloc[1]]=$implode_teanloc[1];
					}
				}
			}

			if(!empty($params['teams'])){
				if(is_array($params['teams'])){
					$implode_team = implode(",",$params['teams']);
				} else {
					$implode_team = $params['teams'];
				}
				$query->joinWith('tasksTeams');
				$query->andWhere('tbl_tasks_teams.team_id IN ('.$implode_team.')');
    		}

    		/**
    		 * Changed for (IRT 75) Date: 13-feb-2017
    		 * Add Teamlocation for search teamloc wise in By team(Global Project)
    		 */
    		if(!empty($params['teamloc'])) {
				if(is_array($params['teamloc'])) {
					$implode_team_loc = implode(",", $params['teamloc']);
				} else {
					$implode_team_loc = $params['teamloc'];
				}
				$query->joinWith('tasksTeams');
				$query->andWhere('tbl_tasks_teams.team_loc IN ('.$implode_team_loc.')');
    		}
    	//}
    	//if(isset($params['by_teammanager'])){
			if(!empty($params['teammanagers'])) {
				if(is_array($params['teammanagers'])){
					$implode_teammanager = implode(',', $params["teammanagers"]);
				} else {
					//$implode_teammanager = $params['teammanagers'];
					$teammanagers=json_decode(str_replace("'", '"',$params['teammanagers']),true);
					if(is_array($teammanagers)){
						$params['teammanagers']=$teammanagers;
						$implode_teammanager = implode(',', $params["teammanagers"]);
					}else{
						$implode_teammanager = $params['teammanagers'];
					}

				}
				$sql="SELECT task_id FROM tbl_tasks_units WHERE unit_assigned_to IN (".$implode_teammanager.")";
				$query->andWhere('tbl_tasks.id IN ('.$sql.')');
    		}
    	//}
    	//echo "<pre>"; print_r($params); exit;
    	//if(isset($params['by_casecreatedmanager'])){
			if(!empty($params['casecreatedmanagers'])){
				if(is_array($params['casecreatedmanagers'])){
					$implode_casecreatedmanagers = implode(',',$params["casecreatedmanagers"]);
				}else{
					//$implode_casecreatedmanagers = $params['casecreatedmanagers'];
					$casecreatedmanagers=json_decode(str_replace("'", '"',$params['casecreatedmanagers']),true);
					if(is_array($casecreatedmanagers)){
						$params['casecreatedmanagers']=$casecreatedmanagers;
						$implode_casecreatedmanagers = implode(',', $params["casecreatedmanagers"]);
					}else{
						$implode_casecreatedmanagers = $params['casecreatedmanagers'];
					}
				}
				$query->andWhere('tbl_client_case.created_by  IN ('.$implode_casecreatedmanagers.')');
    		}
    	//}
    	//if(isset($params['by_casemanager'])){
			if(!empty($params['casemanagers'])){
				if(is_array($params['casemanagers'])){
					$implode_casemanagers = implode(',',$params["casemanagers"]);
				} else {
					$casemanagers=json_decode(str_replace("'", '"',$params['casemanagers']),true);
					if(is_array($casemanagers)){
						$params['casemanagers']=$casemanagers;
						$implode_casemanagers = implode(',', $params["casemanagers"]);
					}else{
						$implode_casemanagers = $params['casemanagers'];
					}
					//$implode_casemanagers = $params['casemanagers'];
				}
				$query->andWhere('tbl_tasks.created_by IN ('.$implode_casemanagers.')');
    		}
    	//}
    	//if(isset($params['by_taskpriority'])){
			if(!empty($params['taskpriority'])){
				if(is_array($params['taskpriority'])){
					//$implode_taskpriority = implode(',',$params["taskpriority"]);
//                                    echo '<pre>';print_r($params['taskpriority']);
                                    $prioritySqlCondition = '';
					foreach($params['taskpriority'] as $priority){
                                            if($prioritySqlCondition  != ''){
                                                $prioritySqlCondition .= " OR priority LIKE '".$priority."' ";
//                                                echo $priority.'||||asd';
//						$query->andFilterWhere(['like', 'priority', $priority,false]);
                                            }else{
                                                $prioritySqlCondition = "priority LIKE '".$priority."'";
                                            }
//						$query->orWhere('priority like "'.$priority.'"');
					}
                                        if($prioritySqlCondition != '')
                                            $query->andWhere($prioritySqlCondition);
				}else{
					$taskpriority=json_decode(str_replace("'", '"',$params['taskpriority']),true);
					if(is_array($taskpriority)){
						$params['taskpriority']=$taskpriority;
						$prioritySqlCondition = '';
						foreach($params['taskpriority'] as $priority) {
                        	if($prioritySqlCondition  != '') {
                            	$prioritySqlCondition .= " OR priority LIKE '".$priority."' ";
					        } else {
                            	$prioritySqlCondition = "priority LIKE '".$priority."'";
                            }
						}
                        if($prioritySqlCondition != '')
                        	$query->andWhere($prioritySqlCondition);
					}else{
						//$implode_taskpriority = $params['taskpriority'];
						$query->andFilterWhere(['like', 'priority', $params['taskpriority'],false]);
					}
				}
				//$query->andWhere('tbl_task_instruct.task_priority IN ('.$implode_taskpriority.')');
			}
    	//}
    	$is_cancel=0;
    	$is_closed=0;

    	//if(isset($params['by_taskstatus'])){
			if(!empty($params['taskstatuss'])){
				if(!is_array($params['taskstatuss'])){
					$params['taskstatuss'] = explode(',',$params['taskstatuss']);
				}
				if(in_array(6,$params['taskstatuss'])){ /* past due */
					if (Yii::$app->db->driverName == 'mysql') {
						$query->andWhere("DATE_FORMAT( CONCAT( tbl_task_instruct.`task_duedate` , ' ', STR_TO_DATE( tbl_task_instruct.`task_timedue` , '%l:%i %p' ) ) , '%Y-%m-%d %T' ) < '" . date('Y-m-d h:i:s') . "'");
					} else {
						$query->andWhere("CAST(switchoffset(todatetimeoffset(Cast((CAST(tbl_task_instruct.task_duedate as varchar)  + ' ' + tbl_task_instruct.task_timedue) as datetime), '+00:00'), '+00:00') as datetime) < '" . date('Y-m-d h:i:s') . "'");
					}
				}
				if(in_array(7,$params['taskstatuss'])){ /* due today */
					if(Yii::$app->db->driverName == 'mysql') {
						// $query->andWhere("DATE_FORMAT( CONCAT( tbl_task_instruct.`task_duedate` , ' ', STR_TO_DATE( tbl_task_instruct.`task_timedue` , '%l:%i %p' ) ) , '%Y-%m-%d %T' ) = '" . date('Y-m-d h:i:s') . "'");
						$query->andWhere("DATE_FORMAT( CONCAT( tbl_task_instruct.`task_duedate` , ' ', STR_TO_DATE( tbl_task_instruct.`task_timedue` , '%l:%i %p' ) ) , '%Y-%m-%d' ) = '" . date('Y-m-d') . "'");
					} else {
						$query->andWhere("CAST(switchoffset(todatetimeoffset(Cast((CAST(tbl_task_instruct.task_duedate as varchar)  + ' ' + tbl_task_instruct.task_timedue) as datetime), '+00:00'), '+00:00') as date) = '" . date('Y-m-d') . "'");
					}
				}
				if(in_array(8,$params['taskstatuss'])){ /* closed */
					$is_closed=1;
				}
				if(in_array(9,$params['taskstatuss'])){ /* cancel */
					$is_cancel=1;
				}
				if(in_array(0,$params['taskstatuss']) || in_array(1,$params['taskstatuss']) || in_array(3,$params['taskstatuss']) || in_array(4,$params['taskstatuss'])){ /*Task Status*/
					$query->andWhere("task_status IN (".implode(',',$params['taskstatuss']).")");
				}
    		}
    	//}

    	//if(isset($params['by_submitted_date'])){
    		if((isset($params['previous_submitted_date']) && $params['previous_submitted_date']!="") || (isset($params['previous']) && $params['previous']!=""))
    		{
				if(strpos($params['previous_submitted_date'],'-') !== false) {
					//echo "<pre>",print_r($params['previous_submitted_date']);die;
					$task_duedate = explode("-", $params['previous_submitted_date']);
					$task_duedate_start = explode("/", trim($task_duedate[0]));
					$task_duedate_end = explode("/", trim($task_duedate[1]));
					$previous_start_date = $task_duedate_start[2] . "-" . $task_duedate_start[0] . "-" . $task_duedate_start[1];
					$previous_end_date = $task_duedate_end[2] . "-" . $task_duedate_end[0] . "-" . $task_duedate_end[1];
				} else {
					if ($params['previous_submitted_date'] == 'T' || ($params['previous'] == 'T')) { //Today
						$previous_start_date = date('Y-m-d');
						$previous_end_date = date('Y-m-d');
					} else if ($params['previous_submitted_date'] == 'Y' || $params['previous'] == 'Y') { //Yesterday
						$previous_start_date = date('Y-m-d', strtotime('-1 days'));
						$previous_end_date = date('Y-m-d', strtotime('-1 days'));
					} else if ($params['previous_submitted_date'] == 'W' || $params['previous'] == 'W') { //Last Week
						$previous_start_date = date('Y-m-d', strtotime('-7 days'));
						$previous_end_date = date('Y-m-d');
					} else if ($params['previous_submitted_date'] == 'M' || $params['previous'] == 'M' ) { //Last Month
						$previous_start_date = date('Y-m-d', strtotime('-1 months'));
						$previous_end_date = date('Y-m-d');
					}
				}

	    		// Query To created
	    		if (Yii::$app->db->driverName == 'mysql') {
					$query->andWhere("DATE_FORMAT( tbl_tasks.created, '%Y-%m-%d') BETWEEN '" . $previous_start_date . "' AND '" . $previous_end_date . "'");
				} else {
	    			$query->andWhere("Cast(switchoffset(todatetimeoffset(Cast(tbl_tasks.created as datetime), '+00:00'), '{$timezoneOffset}') as date) BETWEEN '".$previous_start_date."' AND '".$previous_end_date."'");
	    		}
	    	}
	    //}
	    //if(isset($params['by_due_date'])){
	    	if(isset($params['previous_due_date']) && $params['previous_due_date']!=""){
				if(strpos($params['previous_due_date'],'-') !== false) {
					$task_duedate = explode("-", $params['previous_due_date']);
					$task_duedate_start = explode("/", trim($task_duedate[0]));
					$task_duedate_end = explode("/", trim($task_duedate[1]));
					$task_duedate_s = $task_duedate_start[2] . "-" . $task_duedate_start[0] . "-" . $task_duedate_start[1].' 00:00:00';
					$task_duedate_e = $task_duedate_end[2] . "-" . $task_duedate_end[0] . "-" . $task_duedate_end[1].' 23:59:59';
					$query->andWhere(" $data_query >= '$task_duedate_s' AND $data_query  <= '$task_duedate_e' ");
				}else{
					if ($params['previous_due_date'] == 'T') { // Today
						$previous_start_date = date('Y-m-d').' 00:00:00';
						$previous_end_date = date('Y-m-d').' 23:59:59';
					} else if ($params['previous_due_date'] == 'Y') { // Yesterday
						$previous_start_date = date('Y-m-d', strtotime('-1 days')).' 00:00:00';;
						$previous_end_date = date('Y-m-d', strtotime('-1 days')).' 23:59:59';;
					} else if ($params['previous_due_date'] == 'W') { // Last Week
						$previous_start_date = date('Y-m-d', strtotime('-7 days')).' 00:00:00';;
						$previous_end_date = date('Y-m-d').' 23:59:59';;
					} else if ($params['previous_due_date'] == 'M') { // Last Month
						$previous_start_date = date('Y-m-d', strtotime('-1 months')).' 00:00:00';;
						$previous_end_date = date('Y-m-d').' 23:59:59';;
					}
					$query->andWhere(" $data_query >= '$previous_start_date' AND $data_query  <= '$previous_end_date' ");
				}
	    	}

	    //}
	   // if(isset($params['by_completed_date'])){
	    	if((isset($params['previous_completed_date']) && $params['previous_completed_date']!="") || (isset($params['previous_completed']) && $params['previous_completed'] != "")){
				if(strpos($params['previous_completed_date'],'-') !== false) {
					$task_duedate = explode("-", $params['previous_completed_date']);
					$task_duedate_start = explode("/", trim($task_duedate[0]));
					$task_duedate_end = explode("/", trim($task_duedate[1]));
					$previous_start_date = $task_duedate_start[2] . "-" . $task_duedate_start[0] . "-" . $task_duedate_start[1];
					$previous_end_date = $task_duedate_end[2] . "-" . $task_duedate_end[0] . "-" . $task_duedate_end[1];
				}else{
					if ($params['previous_completed_date'] == 'T' || $params['previous_completed'] == 'T') {//Today
						$previous_start_date = date('Y-m-d');
						$previous_end_date = date('Y-m-d');
					} else if ($params['previous_completed_date'] == 'Y'  || $params['previous_completed'] == 'Y') {//Yesterday
						$previous_start_date = date('Y-m-d', strtotime('-1 days'));
						$previous_end_date = date('Y-m-d', strtotime('-1 days'));
					} else if ($params['previous_completed_date'] == 'W'  || $params['previous_completed'] == 'W') {//Last Week
						$previous_start_date = date('Y-m-d', strtotime('-7 days'));
						$previous_end_date = date('Y-m-d');
					} else if ($params['previous_completed_date'] == 'M'  || $params['previous_completed'] == 'M') {//Last Month
						$previous_start_date = date('Y-m-d', strtotime('-1 months'));
						$previous_end_date = date('Y-m-d');
					}
				}
	    		if (Yii::$app->db->driverName == 'mysql') {
	    			$query->andWhere("DATE_FORMAT( tbl_tasks.task_complete_date, '%Y-%m-%d') BETWEEN '" . $previous_start_date . "' AND '" . $previous_end_date . "'");
	    		}else {
	    			$query->andWhere("Cast(switchoffset(todatetimeoffset(Cast(tbl_tasks.task_complete_date as datetime), '+00:00'), '{$timezoneOffset}') as date) BETWEEN '".$previous_start_date."' AND '".$previous_end_date."'");
	    		}
	    		$query->andWhere("tbl_tasks.task_status = 4");
	    	}

	    	/*IRT  78 */
			if(isset($params['todotatus']) && !empty($params['todotatus'])){
				$sql="select l.todo_id from tbl_tasks_units_todo_transaction_log l inner join ( select todo_id, max(transaction_date) as latest from tbl_tasks_units_todo_transaction_log group by todo_id) r on l.transaction_date = r.latest and l.todo_id = r.todo_id WHERE l.transaction_type IN (".implode(',',$params['todotatus']).")";
				$todostatussql="SELECT tbl_tasks_units.task_id FROM tbl_tasks_units INNER JOIN tbl_tasks_units_todos TasksUnitsTodos ON (TasksUnitsTodos.tasks_unit_id = tbl_tasks_units.id) WHERE  TasksUnitsTodos.id IN ($sql)";
				$query->andWhere("tbl_tasks.id IN ($todostatussql)");
			}
			/*IRT  78 */
			/*IRT  74 */
			if(isset($params['requestor']) && !empty($params['requestor'])){
				if(is_array($params['requestor'])){
					$query->andFilterWhere(['or like', 'tbl_task_instruct.requestor', $params['requestor'],false]);
				}else{
					$requestor=json_decode(str_replace("'", '"',$params['requestor']),true);
					if(is_array($requestor)){
						$params['requestor']=$requestor;
						$query->andFilterWhere(['or like', 'tbl_task_instruct.requestor', $params['requestor'],false]);
					}else{
						$query->andFilterWhere(['or like', 'tbl_task_instruct.requestor', $params['requestor'],false]);
					}
				}
			}
			/*IRT  74 */
	   // }
    	/*Dynamic Filter Start*/
    	$query->andWhere('tbl_tasks.task_closed = '.$is_closed);
    	$query->andWhere('tbl_tasks.task_cancel = '.$is_cancel);
        $query->andWhere(['tbl_client_case.is_close' => 0]);
//        echo '<pre>',print_r($query);die;
    	if (!$this->validate()) {
    		return $dataProvider;
    	}

    	return $dataProvider;
    }
    public function searchFilterGlobalProject($params)
    {
		$roleId = Yii::$app->user->identity->role_id; // Role Id
		$userId = Yii::$app->user->identity->id;
		$dataProvider = array();
    	$timezoneOffset = (new Options)->getOffsetOfCurrenttimeZone();
		//echo "<pre>",print_r($params),"</pre>";die;
		$globalprojectleftparams=$params['params'];
		$timezoneOffset = (new Options)->getOffsetOfCurrenttimeZone();
        if (Yii::$app->db->driverName == 'mysql') {
                $data_query = "getDueDateTimeByUsersTimezone('{$timezoneOffset}',tbl_task_instruct.task_duedate,tbl_task_instruct.task_timedue,'%m/%d/%Y %h:%i %p')";
        } else {
                $data_query = "[dbo].getDueDateTimeByUsersTimezone('{$timezoneOffset}',tbl_task_instruct.task_duedate,tbl_task_instruct.task_timedue,'%m/%d/%Y %h:%i %p')";
        }
    	/**
    	 * IRT 169 Remove priority_order
    	 */
    	$query = Tasks::find()
    	    	->select(['tbl_tasks.id','tbl_tasks.client_case_id','tbl_tasks.task_status','tbl_tasks.task_closed','tbl_tasks.task_cancel','tbl_tasks.task_cancel_reason','tbl_client_case.case_name','tbl_task_instruct.project_name','tbl_task_instruct.task_duedate','tbl_task_instruct.task_priority','tbl_priority_project.priority_order', 'tbl_priority_project.priority', /*'tbl_priority_team.priority_order as teamorder',*/ 'tbl_client_case.case_name'])
    	->joinWith(['clientCase'=> function (\yii\db\ActiveQuery $query) use($roleId, $userId){
			$query->joinWith(['client'],false);
			if($roleId!=0) {
				$query->joinWith(['projectSecurity' => function(\yii\db\ActiveQuery $query) use ($userId){
					$query->where(['tbl_project_security.user_id' => $userId]);
				}]);
			}
		}],false)
    	->joinWith(['taskInstruct' => function (\yii\db\ActiveQuery $query) use ($params,$data_query){
				//$query->innerJoin("(SELECT tbl_task_instruct.id, $data_query as task_date_time FROM tbl_task_instruct WHERE isactive=1 AND task_id IS NOT NULL AND task_id<>'') as A", 'tbl_task_instruct.id = A.id');
				$query->where(['isactive'=>1])->joinWith('taskPriority');
    	}],false)
    	->joinWith(['teamPriority'],false);
    	$query->where(['tbl_client_case.is_close'=>0]);
		$query->distinct=true; // dISTINCT
    	/*Dynamic Filter Start*/
		if(isset($globalprojectleftparams['clientCases'])) {
			$clientCases=json_decode(str_replace("'", '"',$globalprojectleftparams['clientCases']),true);
			if(!empty($clientCases)) {
				foreach($clientCases as $ccval) {
					$implode_cleintcases = explode(",",$ccval);
					$globalprojectleftparams['cleints'][$implode_cleintcases[0]]=$implode_cleintcases[0];
					$globalprojectleftparams['cleintscase'][$implode_cleintcases[1]]=$implode_cleintcases[1];
				}
			}
		}
    	if(isset($globalprojectleftparams['cleints']) && is_array($globalprojectleftparams['cleints'])){
                $implode_cleints = implode(",",$globalprojectleftparams['cleints']);
        }else{
                $implode_cleints = $globalprojectleftparams['cleints'];
        }
        if($globalprojectleftparams['cleintscase'] && is_array($globalprojectleftparams['cleintscase'])){
                $implode_clientcase = implode(",",$globalprojectleftparams['cleintscase']);
        } else {
                $implode_clientcase = $globalprojectleftparams['cleintscase'];
        }
        if(!empty($globalprojectleftparams['cleints'])) {
                $query->andWhere('tbl_client_case.client_id IN ('.$implode_cleints.')');
        } if(!empty($globalprojectleftparams['cleintscase'])) {
                $query->andWhere('tbl_client_case.id IN ('.$implode_clientcase.')');
        }
		if(isset($globalprojectleftparams['taskStat'])) {
			$taskstats=json_decode(str_replace("'", '"',$globalprojectleftparams['taskStat']),true);
			if(!empty($taskstats)) {
				$globalprojectleftparams['taskstatuss']=$taskstats;
			}
		}
		if(isset($globalprojectleftparams['todoStat'])) {
			$todostats=json_decode(str_replace("'", '"',$globalprojectleftparams['todoStat']),true);
			if(!empty($todostats)) {
				$globalprojectleftparams['todotatus']=$todostats;
			}
		}
		if(isset($globalprojectleftparams['teamLocs'])) {
			$teamlocs=json_decode(str_replace("'", '"',$globalprojectleftparams['teamLocs']),true);
			if(!empty($teamlocs)) {
				foreach($teamlocs as $tlval) {
					$implode_teanloc = explode(",",$tlval);
					$globalprojectleftparams['teams'][$implode_teanloc[0]]=$implode_teanloc[0];
					$globalprojectleftparams['teamloc'][$implode_teanloc[1]]=$implode_teanloc[1];
				}
			}
		}
    	if(!empty($globalprojectleftparams['teams'])) {
			if(is_array($globalprojectleftparams['teams'])) {
				$implode_team = implode(",",$globalprojectleftparams['teams']);
			} else {
				$implode_team = $globalprojectleftparams['teams'];
			}
			$query->joinWith('tasksTeams');
			$query->andWhere('tbl_tasks_teams.team_id IN ('.$implode_team.')');
		}
		if(!empty($globalprojectleftparams['teamloc'])) {
			if(is_array($globalprojectleftparams['teamloc'])) {
				$implode_team_loc = implode(",", $globalprojectleftparams['teamloc']);
			} else {
				$implode_team_loc = $globalprojectleftparams['teamloc'];
			}
			$query->joinWith('tasksTeams');
			$query->andWhere('tbl_tasks_teams.team_loc IN ('.$implode_team_loc.')');
		}
    	if(!empty($globalprojectleftparams['teammanagers'])) {
			if(is_array($globalprojectleftparams['teammanagers'])) {
				$implode_teammanager = implode(',', $globalprojectleftparams["teammanagers"]);
			} else {
				$teammanagers=json_decode(str_replace("'", '"',$globalprojectleftparams['teammanagers']),true);
				if(is_array($teammanagers)) {
					$globalprojectleftparams['teammanagers']=$teammanagers;
					$implode_teammanager = implode(',', $globalprojectleftparams["teammanagers"]);
				} else {
					$implode_teammanager = $globalprojectleftparams['teammanagers'];
				}
			}
			$sql="SELECT task_id FROM tbl_tasks_units WHERE unit_assigned_to IN (".$implode_teammanager.")";
			$query->andWhere('tbl_tasks.id IN ('.$sql.')');
		}
    	if(!empty($globalprojectleftparams['casecreatedmanagers'])) {
				if(is_array($globalprojectleftparams['casecreatedmanagers'])) {
					$implode_casecreatedmanagers = implode(',',$globalprojectleftparams["casecreatedmanagers"]);
				} else {
					$casecreatedmanagers=json_decode(str_replace("'", '"',$globalprojectleftparams['casecreatedmanagers']),true);
					if(is_array($casecreatedmanagers)){
						$params['casecreatedmanagers']=$casecreatedmanagers;
						$implode_casecreatedmanagers = implode(',', $globalprojectleftparams["casecreatedmanagers"]);
					}else{
						$implode_casecreatedmanagers = $globalprojectleftparams['casecreatedmanagers'];
					}
				}
				$query->andWhere('tbl_client_case.created_by  IN ('.$implode_casecreatedmanagers.')');
    	}
    	if(!empty($globalprojectleftparams['casemanagers'])){
				if(is_array($globalprojectleftparams['casemanagers'])){
					$implode_casemanagers = implode(',',$globalprojectleftparams["casemanagers"]);
				} else {
					$casemanagers=json_decode(str_replace("'", '"',$globalprojectleftparams['casemanagers']),true);
					if(is_array($casemanagers)){
						$globalprojectleftparams['casemanagers']=$casemanagers;
						$implode_casemanagers = implode(',', $globalprojectleftparams["casemanagers"]);
					}else{
						$implode_casemanagers = $globalprojectleftparams['casemanagers'];
					}
				}
				$query->andWhere('tbl_tasks.created_by IN ('.$implode_casemanagers.')');
    	}
		if(isset($globalprojectleftparams['requestor']) && !empty($globalprojectleftparams['requestor'])) {
			if(is_array($globalprojectleftparams['requestor'])) {
				$query->andFilterWhere(['or like', 'tbl_task_instruct.requestor', $globalprojectleftparams['requestor'],false]);
			} else {
				$requestor=json_decode(str_replace("'", '"',$globalprojectleftparams['requestor']),true);
				if(is_array($requestor)) {
					$globalprojectleftparams['requestor']=$requestor;
					$query->andFilterWhere(['or like', 'tbl_task_instruct.requestor', $globalprojectleftparams['requestor'],false]);
				} else {
					$query->andFilterWhere(['or like', 'tbl_task_instruct.requestor', $globalprojectleftparams['requestor'],false]);
				}
			}
		}
    	if(!empty($globalprojectleftparams['taskpriority'])) {
					if(is_array($globalprojectleftparams['taskpriority'])) {
                     	$prioritySqlCondition = '';
						foreach($globalprojectleftparams['taskpriority'] as $priority) {
							if($prioritySqlCondition  != '') {
								$prioritySqlCondition .= " OR priority LIKE '".$priority."' ";
							} else {
								$prioritySqlCondition = "priority LIKE '".$priority."'";
							}
						}
                    	if($prioritySqlCondition != '')
	                    	$query->andWhere($prioritySqlCondition);
				   } else {
				   		$taskpriority=json_decode(str_replace("'", '"',$globalprojectleftparams['taskpriority']),true);
				   		if(is_array($taskpriority)) {
				   			$globalprojectleftparams['taskpriority']=$taskpriority;
				   			$prioritySqlCondition = '';
				   			foreach($globalprojectleftparams['taskpriority'] as $priority) {
                        		if($prioritySqlCondition  != '') {
                            		$prioritySqlCondition .= " OR priority LIKE '".$priority."' ";
				   	        	} else {
                            		$prioritySqlCondition = "priority LIKE '".$priority."'";
                            	}
				   		 	}
                        	if($prioritySqlCondition != '')
                        		$query->andWhere($prioritySqlCondition);

						} else {
							$query->andFilterWhere(['like', 'priority', $globalprojectleftparams['taskpriority'],false]);
						}
				   }
			}
			/*multiselect*/
        if ($globalprojectleftparams['TaskSearch']['id'] != null && is_array($globalprojectleftparams['TaskSearch']['id'])) {
			if(!empty($globalprojectleftparams['TaskSearch']['id'])){
				foreach($globalprojectleftparams['TaskSearch']['id'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($globalprojectleftparams['TaskSearch']['id']);
					}
				}
			}
		}
		if ($globalprojectleftparams['TaskSearch']['task_status'] != null && is_array($globalprojectleftparams['TaskSearch']['task_status'])) {
			if(!empty($globalprojectleftparams['TaskSearch']['task_status'])){
				foreach($globalprojectleftparams['TaskSearch']['task_status'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($globalprojectleftparams['TaskSearch']['task_status']);
					}
				}
			}
		}
		if ($globalprojectleftparams['TaskSearch']['client_case_id'] != null && is_array($globalprojectleftparams['TaskSearch']['client_case_id'])) {
			if(!empty($globalprojectleftparams['TaskSearch']['client_case_id'])){
				foreach($globalprojectleftparams['TaskSearch']['client_case_id'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($globalprojectleftparams['TaskSearch']['client_case_id']);
					}
				}
			}
		}
		if ($globalprojectleftparams['TaskSearch']['priority'] != null && is_array($globalprojectleftparams['TaskSearch']['priority'])) {
			if(!empty($globalprojectleftparams['TaskSearch']['priority'])){
				foreach($globalprojectleftparams['TaskSearch']['priority'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($globalprojectleftparams['TaskSearch']['priority']);
					}
				}
			}
		}
		if ($globalprojectleftparams['TaskSearch']['project_name'] != null && is_array($globalprojectleftparams['TaskSearch']['project_name'])) {
			if(!empty($globalprojectleftparams['TaskSearch']['project_name'])){
				foreach($globalprojectleftparams['TaskSearch']['project_name'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($globalprojectleftparams['TaskSearch']['project_name']);
					}
				}
			}
		}
		if ($globalprojectleftparams['TaskSearch']['client_id'] != null && is_array($globalprojectleftparams['TaskSearch']['client_id'])) {
			if(!empty($globalprojectleftparams['TaskSearch']['client_id'])){
				foreach($globalprojectleftparams['TaskSearch']['client_id'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($globalprojectleftparams['TaskSearch']['client_id']);
					}
				}
			}
		}
		/*multiselect*/


		/*if(isset($globalprojectleftparams['TaskSearch']['id']) && $globalprojectleftparams['TaskSearch']['id']!="" && $globalprojectleftparams['TaskSearch']['id']!="All")
    		$query->andFilterWhere(['tbl_tasks.id' => $globalprojectleftparams['TaskSearch']['id']]);

    	if(isset($globalprojectleftparams['TaskSearch']['task_status']) && $globalprojectleftparams['TaskSearch']['task_status']!="" && $globalprojectleftparams['TaskSearch']['task_status']!="All")
    		$query->andFilterWhere(['or like', 'task_status', $globalprojectleftparams['TaskSearch']['task_status']]);


		if(isset($globalprojectleftparams['TaskSearch']['task_duedate']) && $globalprojectleftparams['TaskSearch']['task_duedate']!=""){
			$task_duedate = explode("-", $globalprojectleftparams['TaskSearch']['task_duedate']);
            $task_duedate_start = explode("/", trim($task_duedate[0]));
            $task_duedate_end = explode("/", trim($task_duedate[1]));
            $task_duedate_s = $task_duedate_start[2] . "-" . $task_duedate_start[0] . "-" . $task_duedate_start[1].' 00:00:00';
            $task_duedate_e = $task_duedate_end[2] . "-" . $task_duedate_end[0] . "-" . $task_duedate_end[1].' 23:59:59';
          	$query->andWhere(" $data_query >= '$task_duedate_s' AND $data_query  <= '$task_duedate_e' ");
        }


    	if(isset($globalprojectleftparams['TaskSearch']['priority']) && $globalprojectleftparams['TaskSearch']['priority']!="" && $globalprojectleftparams['TaskSearch']['priority']!="All"){
			$query->andFilterWhere(['or like', 'priority', $globalprojectleftparams['TaskSearch']['priority'],false]);
		}
		if(isset($globalprojectleftparams['TaskSearch']['client_case_id']) && $globalprojectleftparams['TaskSearch']['client_case_id']!="" && $globalprojectleftparams['TaskSearch']['client_case_id']!="All"){
    		$query->andWhere('tbl_client_case.id IN ('.implode(',',$globalprojectleftparams['TaskSearch']['client_case_id']).')');
    	}
		if(isset($globalprojectleftparams['TaskSearch']['client_id']) && $globalprojectleftparams['TaskSearch']['client_id']!="" && $globalprojectleftparams['TaskSearch']['client_id']!="All"){
    		$query->andWhere('tbl_client_case.client_id IN ('.implode(',',$globalprojectleftparams['TaskSearch']['client_id']).')');
    	}
    	if(isset($globalprojectleftparams['TaskSearch']['task_cancel_reason']) && $globalprojectleftparams['TaskSearch']['task_cancel_reason']!="" && $globalprojectleftparams['TaskSearch']['task_cancel_reason']!="All"){
    		$query->andFilterWhere(['like', 'task_cancel_reason', $globalprojectleftparams['TaskSearch']['task_cancel_reason'],false]);
    	}

    	if(isset($globalprojectleftparams['TaskSearch']['project_name']) && $globalprojectleftparams['TaskSearch']['project_name']!="All"){
			$project_namequery = "";
			$is_unset=false;
			if(!empty($globalprojectleftparams['TaskSearch']['project_name'])) {
				foreach($globalprojectleftparams['TaskSearch']['project_name'] as $k=>$v) {
					if($v=='(not set)'){
						$globalprojectleftparams['TaskSearch']['project_name'][$k]='';
					}
					if($project_namequery == "") {
						$project_namequery = "project_name='".$globalprojectleftparams['TaskSearch']['project_name'][$k]."'";
						if($globalprojectleftparams['TaskSearch']['project_name'][$k]==''){$globalprojectleftparams['TaskSearch']['project_name'][$k]='(not set)';}
					} else {
						$project_namequery .= " OR project_name='".$globalprojectleftparams['TaskSearch']['project_name'][$k]."'";
						if($globalprojectleftparams['TaskSearch']['project_name'][$k]==''){$globalprojectleftparamsparams['TaskSearch']['project_name'][$k]='(not set)';}
					}
				}

				if($is_unset==false){
					$query->andWhere("(".$project_namequery.")");
				}
				//$query->andFilterWhere(['or like', 'project_name' , $params['TeamSearch']['project_name']]);
				$this->project_name=$globalprojectleftparams['TaskSearch']['project_name'];
			}
    	}*/
    	$is_cancel=0;
    	$is_closed=0;
    	if(!empty($globalprojectleftparams['taskstatuss'])) {
				if(!is_array($globalprojectleftparams['taskstatuss'])) {
					$globalprojectleftparams['taskstatuss'] = explode(',',$globalprojectleftparams['taskstatuss']);
				}
				if(in_array(6,$globalprojectleftparams['taskstatuss'])) {
				// past due
					if (Yii::$app->db->driverName == 'mysql') {
						$query->andWhere("DATE_FORMAT( CONCAT( tbl_task_instruct.`task_duedate` , ' ', STR_TO_DATE( tbl_task_instruct.`task_timedue` , '%l:%i %p' ) ) , '%Y-%m-%d %T' ) < '" . date('Y-m-d h:i:s') . "'");
					} else {
						$query->andWhere("CAST(switchoffset(todatetimeoffset(Cast((CAST(tbl_task_instruct.task_duedate as varchar)  + ' ' + tbl_task_instruct.task_timedue) as datetime), '+00:00'), '+00:00') as datetime) < '" . date('Y-m-d h:i:s') . "'");
					}
				}
				if(in_array(7,$globalprojectleftparams['taskstatuss'])) {
				// due today
					if(Yii::$app->db->driverName == 'mysql') {
						// $query->andWhere("DATE_FORMAT( CONCAT( tbl_task_instruct.`task_duedate` , ' ', STR_TO_DATE( tbl_task_instruct.`task_timedue` , '%l:%i %p' ) ) , '%Y-%m-%d %T' ) = '" . date('Y-m-d h:i:s') . "'");
						$query->andWhere("DATE_FORMAT( CONCAT( tbl_task_instruct.`task_duedate` , ' ', STR_TO_DATE( tbl_task_instruct.`task_timedue` , '%l:%i %p' ) ) , '%Y-%m-%d' ) = '" . date('Y-m-d') . "'");
					} else {
						$query->andWhere("CAST(switchoffset(todatetimeoffset(Cast((CAST(tbl_task_instruct.task_duedate as varchar)  + ' ' + tbl_task_instruct.task_timedue) as datetime), '+00:00'), '+00:00') as date) = '" . date('Y-m-d') . "'");
					}
				}
				if(in_array(8,$globalprojectleftparams['taskstatuss'])) {
				// closed
					$is_closed=1;
				}
				if(in_array(9,$globalprojectleftparams['taskstatuss'])) {
				// cancel
					$is_cancel=1;
				}
				if(in_array(0,$globalprojectleftparams['taskstatuss']) || in_array(1,$globalprojectleftparams['taskstatuss']) || in_array(3,$globalprojectleftparams['taskstatuss']) || in_array(4,$globalprojectleftparams['taskstatuss'])) {
				//Task Status
					$query->andWhere("task_status IN (".implode(',',$globalprojectleftparams['taskstatuss']).")");
				}
    		}


		if(isset($globalprojectleftparams['todotatus']) && !empty($globalprojectleftparams['todotatus'])) {
				$sql="select l.todo_id from tbl_tasks_units_todo_transaction_log l inner join ( select todo_id, max(transaction_date) as latest from tbl_tasks_units_todo_transaction_log group by todo_id) r on l.transaction_date = r.latest and l.todo_id = r.todo_id WHERE l.transaction_type IN (".implode(',',$globalprojectleftparams['todotatus']).")";
				$todostatussql="SELECT tbl_tasks_units.task_id FROM tbl_tasks_units INNER JOIN tbl_tasks_units_todos TasksUnitsTodos ON (TasksUnitsTodos.tasks_unit_id = tbl_tasks_units.id) WHERE  TasksUnitsTodos.id IN ($sql)";
				$query->andWhere("tbl_tasks.id IN ($todostatussql)");
		}
		if(isset($globalprojectleftparams['by_submitted_date'])) {
    		if(isset($globalprojectleftparams['previous_submitted_date']) && $globalprojectleftparams['previous_submitted_date']!="") {
    			if(strpos($globalprojectleftparams['previous_submitted_date'],'-') !== false) {
					$task_duedate = explode("-", $globalprojectleftparams['previous_submitted_date']);
					$task_duedate_start = explode("/", trim($task_duedate[0]));
					$task_duedate_end = explode("/", trim($task_duedate[1]));
					$previous_start_date = $task_duedate_start[2] . "-" . $task_duedate_start[0] . "-" . $task_duedate_start[1];
					$previous_end_date = $task_duedate_end[2] . "-" . $task_duedate_end[0] . "-" . $task_duedate_end[1];
				} else {
					if ($globalprojectleftparams['previous_submitted_date'] == 'T' || ($globalprojectleftparams['previous'] == 'T')) { //Today
						$previous_start_date = date('Y-m-d');
						$previous_end_date = date('Y-m-d');
					} else if ($globalprojectleftparams['previous_submitted_date'] == 'Y' || $globalprojectleftparams['previous'] == 'Y') { //Yesterday
						$previous_start_date = date('Y-m-d', strtotime('-1 days'));
						$previous_end_date = date('Y-m-d', strtotime('-1 days'));
					} else if ($globalprojectleftparams['previous_submitted_date'] == 'W' || $globalprojectleftparams['previous'] == 'W') { //Last Week
						$previous_start_date = date('Y-m-d', strtotime('-7 days'));
						$previous_end_date = date('Y-m-d');
					} else if ($globalprojectleftparams['previous_submitted_date'] == 'M' || $globalprojectleftparams['previous'] == 'M' ) { //Last Month
						$previous_start_date = date('Y-m-d', strtotime('-1 months'));
						$previous_end_date = date('Y-m-d');
					}
				}

	    		// Query To created
	    		if (Yii::$app->db->driverName == 'mysql') {
					$query->andWhere("DATE_FORMAT( tbl_tasks.created, '%Y-%m-%d') BETWEEN '" . $previous_start_date . "' AND '" . $previous_end_date . "'");
				} else {
	    			$query->andWhere("Cast(switchoffset(todatetimeoffset(Cast(tbl_tasks.created as datetime), '+00:00'), '{$timezoneOffset}') as date) BETWEEN '".$previous_start_date."' AND '".$previous_end_date."'");
	    		}
    		}
    	}
    	if(isset($globalprojectleftparams['previous_due_date']) && $globalprojectleftparams['previous_due_date']!=""){
				if(strpos($globalprojectleftparams['previous_due_date'],'-') !== false) {
					$task_duedate = explode("-", $globalprojectleftparams['previous_due_date']);
					$task_duedate_start = explode("/", trim($task_duedate[0]));
					$task_duedate_end = explode("/", trim($task_duedate[1]));
					$task_duedate_s = $task_duedate_start[2] . "-" . $task_duedate_start[0] . "-" . $task_duedate_start[1].' 00:00:00';
					$task_duedate_e = $task_duedate_end[2] . "-" . $task_duedate_end[0] . "-" . $task_duedate_end[1].' 23:59:59';
					$query->andWhere(" $data_query >= '$task_duedate_s' AND $data_query  <= '$task_duedate_e' ");
				}else{
					if ($globalprojectleftparams['previous_due_date'] == 'T') { // Today
						$previous_start_date = date('Y-m-d').' 00:00:00';
						$previous_end_date = date('Y-m-d').' 23:59:59';
					} else if ($globalprojectleftparams['previous_due_date'] == 'Y') { // Yesterday
						$previous_start_date = date('Y-m-d', strtotime('-1 days')).' 00:00:00';;
						$previous_end_date = date('Y-m-d', strtotime('-1 days')).' 23:59:59';;
					} else if ($globalprojectleftparams['previous_due_date'] == 'W') { // Last Week
						$previous_start_date = date('Y-m-d', strtotime('-7 days')).' 00:00:00';;
						$previous_end_date = date('Y-m-d').' 23:59:59';;
					} else if ($globalprojectleftparams['previous_due_date'] == 'M') { // Last Month
						$previous_start_date = date('Y-m-d', strtotime('-1 months')).' 00:00:00';;
						$previous_end_date = date('Y-m-d').' 23:59:59';;
					}
					$query->andWhere(" $data_query >= '$previous_start_date' AND $data_query  <= '$previous_end_date' ");
				}

	    	}
	    	if((isset($globalprojectleftparams['previous_completed_date']) && $globalprojectleftparams['previous_completed_date']!="") || (isset($globalprojectleftparams['previous_completed']) && $globalprojectleftparams['previous_completed'] != "")){
	    		if(strpos($globalprojectleftparams['previous_completed_date'],'-') !== false) {
					$task_duedate = explode("-", $globalprojectleftparams['previous_completed_date']);
					$task_duedate_start = explode("/", trim($task_duedate[0]));
					$task_duedate_end = explode("/", trim($task_duedate[1]));
					$previous_start_date = $task_duedate_start[2] . "-" . $task_duedate_start[0] . "-" . $task_duedate_start[1];
					$previous_end_date = $task_duedate_end[2] . "-" . $task_duedate_end[0] . "-" . $task_duedate_end[1];
				} else {
					if ($globalprojectleftparams['previous_completed_date'] == 'T' || $globalprojectleftparams['previous_completed'] == 'T') {//Today
						$previous_start_date = date('Y-m-d');
						$previous_end_date = date('Y-m-d');
					} else if ($globalprojectleftparams['previous_completed_date'] == 'Y'  || $globalprojectleftparams['previous_completed'] == 'Y') {//Yesterday
						$previous_start_date = date('Y-m-d', strtotime('-1 days'));
						$previous_end_date = date('Y-m-d', strtotime('-1 days'));
					} else if ($globalprojectleftparams['previous_completed_date'] == 'W'  || $globalprojectleftparams['previous_completed'] == 'W') {//Last Week
						$previous_start_date = date('Y-m-d', strtotime('-7 days'));
						$previous_end_date = date('Y-m-d');
					} else if ($globalprojectleftparams['previous_completed_date'] == 'M'  || $globalprojectleftparams['previous_completed'] == 'M') {//Last Month
						$previous_start_date = date('Y-m-d', strtotime('-1 months'));
						$previous_end_date = date('Y-m-d');
					}
				}
	    		if (Yii::$app->db->driverName == 'mysql') {
	    			$query->andWhere("DATE_FORMAT( tbl_tasks.task_complete_date, '%Y-%m-%d') BETWEEN '" . $previous_start_date . "' AND '" . $previous_end_date . "'");
	    		}else {
	    			$query->andWhere("Cast(switchoffset(todatetimeoffset(Cast(tbl_tasks.task_complete_date as datetime), '+00:00'), '{$timezoneOffset}') as date) BETWEEN '".$previous_start_date."' AND '".$previous_end_date."'");
	    		}
	    		$query->andWhere("tbl_tasks.task_status = 4");
	    	}
	    /*Dynamic Filter Start*/

    	$query->andWhere('tbl_tasks.task_closed = '.$is_closed);
    	$query->andWhere('tbl_tasks.task_cancel = '.$is_cancel);


    	if(isset($params['task_cancel'])){
    		$query->andFilterWhere(['like', 'task_cancel', 1]);
    	}

    	if($params['field']=='id'){
    		$query->select('tbl_tasks.id');
    		if(isset($params['q']) && $params['q']!=""){
    			$query->andWhere(['like','tbl_tasks.id', $params['q'].'%',false]);
    			$query->orderBy('tbl_tasks.id');
    			$dataProvider = ArrayHelper::map($query->all(),'id','id');
    		}else{
    			$dataProvider = ArrayHelper::map($query->all(),'id','id'); //JoinWith Error
    		}
    	}
    	if($params['field']=='priority'){
    		$query->select('tbl_task_instruct.task_priority');
    		if(isset($params['q']) && $params['q']!=""){
    			$query->andFilterWhere(['like', 'priority', $params['q']]);
    		}
    		$query->groupBy('tbl_task_instruct.task_priority');
    		$dataProvider = ArrayHelper::map(PriorityProject::find()->select(['priority'])->where(['id'=>$query])->orderBy('priority')->groupBy('priority')->all(),'priority','priority');
    	}
    	if($params['field']=='project_name' ){
    		$query->select('tbl_task_instruct.id');
    		if(isset($params['q']) && $params['q']!=""){
    			$query->andFilterWhere(['like', 'project_name', $params['q'].'%',false]);
    		}
    		$dataProvider = ArrayHelper::map(TaskInstruct::find()->select(['id','project_name'])->where(['id'=>$query])->orderBy('project_name')->all(),'id','project_name');
    	}
    	if($params['field']=='client_case_id'){
				if(isset($globalprojectleftparams['TaskSearch']['client_id']) && $globalprojectleftparams['TaskSearch']['client_id']!="" && $globalprojectleftparams['TaskSearch']['client_id']!="All"){
		    		$query->andWhere('tbl_client_case.client_id IN ('.implode(',',$globalprojectleftparams['TaskSearch']['client_id']).')');
		    	}
    		$query->select('tbl_tasks.client_case_id');
    		if(isset($params['q']) && $params['q']!=""){
    			$query->andFilterWhere(['like', "tbl_client_case.case_name", $params['q']]);
    		}
    		$data = ClientCase::find()->select(['tbl_client_case.id','tbl_client_case.case_name'])->where(['tbl_client_case.id'=>$query])->orderBy('tbl_client_case.case_name ASC')->joinWith('client',false)->asArray()->all();

    		foreach($data as $d => $value){
				$dataProvider[$value['id']] = html_entity_decode($value['case_name']);
			}
			return array('All'=>'All') +  $dataProvider;
    	}
    	if($params['field']=='task_cancel_reason' ){
    		$query->select('task_cancel_reason');
    		if(isset($params['q']) && $params['q']!=""){
    			$query->andFilterWhere(['like', 'task_cancel_reason', $params['q']]);
    		}
    		$dataProvider = ArrayHelper::map($query->groupBy('task_cancel_reason')->all(),'task_cancel_reason','task_cancel_reason');
    	}
    	if($params['field']=='client_id'){
            $query->select('tbl_client.id');
            if(isset($params['q']) && $params['q']!=""){
                    $query->andFilterWhere(['like', "tbl_client.client_name", $params['q']]);
            }
            $data = ClientCase::find()->select(['tbl_client.id','tbl_client.client_name'])
                    ->where(['tbl_client_case.client_id'=>$query])
                    ->orderBy('tbl_client.client_name ASC')->joinWith('client',false)->asArray()->all();
            foreach($data as $d => $value){
                            $dataProvider[$value['id']] = html_entity_decode($value['client_name']);
                    }
            return array('All'=>'All') +  $dataProvider;
    	}
    	return array(''=>'All') +  $dataProvider;
    }
    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function searchLoadPrevious($params)
    {
    	$settings_info = Settings::find()->where(['field'=>'project_sort'])->one();
    	$timezoneOffset = (new Options)->getOffsetOfCurrenttimeZone();
    	$case_id = $params['case_id'];
    	/**
    	 * IRT 169 Remove priority_order
    	 */
    	$query = Tasks::find()
    	->select(['tbl_tasks.id','tbl_tasks.client_case_id','tbl_tasks.task_status','tbl_tasks.task_closed','tbl_tasks.task_cancel','tbl_tasks.task_cancel_reason','tbl_client_case.case_name','tbl_task_instruct.project_name','tbl_task_instruct.task_duedate','tbl_task_instruct.task_priority','tbl_priority_project.priority_order', 'tbl_priority_project.priority' /*, 'tbl_priority_team.priority_order as teamorder'*/])
    	->joinWith('clientCase')->where(['tbl_client_case.is_close'=>0, 'tbl_tasks.client_case_id' => $params['case_id']])
    	->joinWith([
    			'taskInstruct' => function (\yii\db\ActiveQuery $query) use ($params,$case_id){
    				$query->where(['isactive'=>1])->joinWith('taskPriority');
    				if(isset($params['unassignproject'])){
    					$query->innerJoinWith(['tasksUnits' => function (\yii\db\ActiveQuery $query) use ($case_id) {

    						//$query->where("tbl_tasks_unit.unit_assigned_to!=0 AND tbl_tasks_units.unit_assigned_to!='' AND tbl_tasks_units.unit_assigned_to IS NOT NULL");
    						$query->where("tbl_task3s_units.task_id IN ( SELECT tbl_tasks.id FROM tbl_tasks INNER JOIN tbl_tasks_units ON tbl_tasks.id =tbl_tasks_units.task_id INNER JOIN tbl_task_instruct ON tbl_task_instruct.task_id = tbl_tasks.id WHERE tbl_tasks.client_case_id = $case_id AND tbl_tasks_units.task_id NOT IN (SELECT task.id FROM tbl_tasks as task INNER JOIN tbl_tasks_units as tu ON tu.task_id = task.id INNER JOIN tbl_task_instruct as ti ON ti.task_id = task.id WHERE client_case_id=$case_id AND task.task_status IN (0,1,3) AND task.task_closed = 0 AND task.task_cancel = 0 AND ti.isactive = 1 AND unit_assigned_to!=0 AND unit_assigned_to!='' AND unit_assigned_to IS NOT NULL GROUP BY task.id) AND tbl_tasks.task_status IN (0,1,3) AND tbl_tasks.task_closed = 0 AND tbl_tasks.task_cancel = 0 AND tbl_task_instruct.isactive = 1 GROUP BY tbl_tasks.id)");

    					}])
    					->groupBy('tbl_tasks_units.task_instruct_id');
    				}
    			}
    	],false)
    	->joinWith('teamPriority');

    	if(isset($params['active']) && $params['active']="active" && $params['TaskSearch']['task_status']=="")
    		$query->andWhere('tbl_tasks.task_status IN (0,1,3)');

    	if(isset($params['task_id'])){
    		$query->andWhere('tbl_tasks.id = '.$params['task_id']);
    	}

    	if(isset($params['todotaskids'])){
    		$query->andWhere('tbl_tasks.id IN ('.$params['todotaskids'].')');
    	}

    	if(isset($params['comment']) && $params['comment']="comment"){
    		$task_ids = (new Tasks)->getUnreadComments($params['case_id'], "task_ids");
    		if (empty($task_ids)) {
    			$task_ids[0] = 0;
    		}
    		if (!empty($task_ids)) {
    			$query->andWhere('tbl_tasks.id IN ('.implode(",",$task_ids).')');
    		}
    	}

    	$currdate=time();
    	if(isset($params['due'])){
    		if($params['due']=='past'){
    			if (DB_TYPE == 'sqlsrv') {
    				$query->andWhere("Cast((CAST(tbl_task_instruct.task_duedate as varchar)  + ' ' + tbl_task_instruct.task_timedue) as datetime) < '" . date('Y-m-d H:i:s', time()) . "'");
    			} else {
    				$query->andWhere('tbl_task_instruct.task_duedate < "' . date('Y-m-d H:i:s', time()) . '"');
    			}
    			$query->andWhere('tbl_tasks.task_status !=4');
    		}
    		if($params['due']=='notpastdue'){
    			$query->andWhere('tbl_tasks.task_closed = 0 AND tbl_tasks.task_cancel = 0');
    		}
    	}

    	if(isset($params['TaskSearch']['task_cancel']) && $params['TaskSearch']['task_cancel']=1)
    		$query->andWhere(['task_cancel'=>1]);
    	else
    		$query->andWhere(['task_cancel'=>0]);


    	if(isset($params['TaskSearch']['task_closed']) && $params['TaskSearch']['task_closed']=1)
    		$query->andWhere(['task_closed'=>1]);
    	else
    		$query->andWhere(['task_closed'=>0]);


    	$query->orderBy('tbl_tasks.id DESC');
    	$dataProvider = new ActiveDataProvider([
    			'query' => $query,
    			'pagination' =>['pageSize'=>25],
    	]);

    	$dataProvider->sort->attributes['task_status'] = [
    			'asc' => ['task_status' => SORT_ASC],
    			'desc' => ['task_status' => SORT_DESC],
    	];
    	$dataProvider->sort->attributes['task_duedate'] = [
    			'asc' => ['task_duedate' => SORT_ASC,'task_timedue' => SORT_ASC],
    			'desc' => ['task_duedate' => SORT_DESC,'task_timedue' => SORT_DESC],
    	];
    	$dataProvider->sort->attributes['priority'] = [
    			'asc' => ['tbl_priority_project.priority_order' => SORT_ASC],
    			'desc' => ['tbl_priority_project.priority_order' => SORT_DESC],
    	];
    	$dataProvider->sort->attributes['project_name'] = [
    			'asc' => ['project_name' => SORT_ASC],
    			'desc' => ['project_name' => SORT_DESC],
    	];

    	$this->load($params);

    	if(isset($params['TaskSearch']['id']) && $params['TaskSearch']['id']!="" && $params['TaskSearch']['id']!="All")
    		$query->andFilterWhere(['tbl_tasks.id' => $params['TaskSearch']['id']]);
    	if(isset($params['TaskSearch']['task_status']) && $params['TaskSearch']['task_status']!="" && $params['TaskSearch']['task_status']!="All")
    		$query->andFilterWhere(['like', 'task_status', $params['TaskSearch']['task_status']]);
    	if(isset($params['TaskSearch']['task_duedate']) && $params['TaskSearch']['task_duedate']!=""){
    		//$query->andFilterWhere(['task_duedate' => $params['TaskSearch']['task_duedate']]);
    		if (Yii::$app->db->driverName == 'mysql'){
    			$datesql ="DATE_FORMAT( CONVERT_TZ(CONCAT( tbl_task_instruct.`task_duedate` , ' ', STR_TO_DATE(tbl_task_instruct.`task_timedue` , '%l:%i %p' )),'+00:00','{$timezoneOffset}'), '%Y-%m-%d') = '".date("Y-m-d",strtotime($params['TaskSearch']['task_duedate']))."'";
    		}else{
    			$datesql = "CAST(switchoffset(todatetimeoffset(Cast((CAST(tbl_task_instruct.task_duedate as varchar)  + ' ' + tbl_task_instruct.task_timedue) as datetime), '+00:00'), '{$timezoneOffset}') as date) = '".date("Y-m-d",strtotime($params['TaskSearch']['task_duedate']))."'";
    		}
    		$query->andWhere($datesql);
    	}
    	if(isset($params['TaskSearch']['priority']) && $params['TaskSearch']['priority']!="" && $params['TaskSearch']['priority']!="All")
    		$query->andWhere('priority = "'.$params['TaskSearch']['priority'].'"');
    	if(isset($params['TaskSearch']['task_cancel_reason']) && $params['TaskSearch']['task_cancel_reason']!="" && $params['TaskSearch']['task_cancel_reason']!="All")
    		$query->andFilterWhere(['like', 'task_cancel_reason', $params['TaskSearch']['task_cancel_reason']]);
    	if(isset($params['TaskSearch']['project_name']) && $params['TaskSearch']['project_name']!="" && $params['TaskSearch']['project_name']!="All"){
    		$query->andFilterWhere(['like', 'project_name', $params['TaskSearch']['project_name']]);
			$this->project_name=$params['TaskSearch']['project_name'];
		}

    	if (!$this->validate()) {
    		return $dataProvider;
    	}

    	return $dataProvider;
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
		$settings_info = Settings::find()->where(['field'=>'project_sort'])->one();
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
    	$case_id = $params['case_id'];
    	if (Yii::$app->db->driverName == 'mysql') {
			//$data_query = "DATE_FORMAT(CONVERT_TZ(CONCAT(tbl_task_instruct.`task_duedate` , ' ', STR_TO_DATE(tbl_task_instruct.`task_timedue` , '%l:%i %p' )),'+00:00','{$timezoneOffset}'), '%Y-%m-%d %H:%i')";
			$data_query = "getDueDateTimeByUsersTimezone('{$timezoneOffset}',tbl_task_instruct.task_duedate,tbl_task_instruct.task_timedue,'%Y-%m-%d %H:%i:%s')";
		}else{
			//$data_query = "CAST(switchoffset(todatetimeoffset(Cast((CAST(tbl_task_instruct.task_duedate as varchar)  + ' ' + tbl_task_instruct.task_timedue) as datetime), '+00:00'), '{$timezoneOffset}') as datetime)";
			//$data_query = "[dbo].getDueDateTimeByUsersTimezone('{$timezoneOffset}',tbl_task_instruct.task_duedate,tbl_task_instruct.task_timedue,'%Y-%m-%d %H:%i:%s')";
			$data_query = "(SELECT duedatetime FROM [dbo].getDueDateTimeByUsersTimezoneTV('{$timezoneOffset}',tbl_task_instruct.task_duedate,tbl_task_instruct.task_timedue,'%Y-%m-%d %H:%i:%s'))";
		}
		/*if(Yii::$app->db->driverName == 'mysql'){
			$askPercentageC = 'getTaskPercentageCompleteByTaskid';
		}else{
			$askPercentageC = 'dbo.getTaskPercentageCompleteByTaskid';
		}*/
		$askPercentageC = (new \app\models\Tasks())->getTaskPercentageCompleteByTaskid('tbl_tasks.id');
		$query = Tasks::find()
    			->select(['tbl_tasks.id','tbl_tasks.client_case_id','tbl_tasks.task_status','tbl_tasks.task_closed','tbl_tasks.task_cancel','tbl_tasks.task_cancel_reason','tbl_client_case.case_name','tbl_task_instruct.project_name as project_name','tbl_task_instruct.task_duedate as task_duedate','tbl_task_instruct.task_timedue as task_duetime','tbl_task_instruct.task_priority','tbl_priority_project.priority_order as porder', 'tbl_priority_project.priority as pname', $askPercentageC.' as per_complete','A.task_date_time','('.$sqlpastdue.') as ispastdue','A.task_date_time as task_date_time'])//,"$data_query as task_date_time"])
    			->joinWith(['clientCase'],false)->where(['tbl_client_case.is_close'=>0, 'tbl_tasks.client_case_id' => $params['case_id']])
                ->joinWith([
                	'taskInstruct' => function (\yii\db\ActiveQuery $query) use ($params,$case_id,$data_query) {
						$query->innerJoin("(SELECT tbl_task_instruct.id, $data_query as task_date_time FROM tbl_task_instruct WHERE isactive=1 AND task_id IS NOT NULL AND task_id<>'') as A", 'tbl_task_instruct.id = A.id');
                		$query->where(['isactive'=>1])->joinWith(['taskPriority'],false);
                    	/*if(isset($params['unassignproject'])) {
                       		$query->innerJoinWith(['tasksUnits' => function (\yii\db\ActiveQuery $query) use ($case_id) {
                       			$query->where("tbl_tasks_units.task_id IN ( SELECT DISTINCT tbl_tasks.id FROM tbl_tasks INNER JOIN tbl_tasks_units ON tbl_tasks.id =tbl_tasks_units.task_id INNER JOIN tbl_task_instruct ON tbl_task_instruct.task_id = tbl_tasks.id WHERE tbl_tasks.client_case_id = $case_id AND tbl_tasks_units.task_id NOT IN (SELECT task.id FROM tbl_tasks as task INNER JOIN tbl_tasks_units as tu ON tu.task_id = task.id INNER JOIN tbl_task_instruct as ti ON ti.task_id = task.id WHERE task.client_case_id=$case_id AND task.task_status IN (0,1,3) AND task.task_closed = 0 AND task.task_cancel = 0 AND ti.isactive = 1 AND unit_assigned_to!=0 AND unit_assigned_to!='' AND unit_assigned_to IS NOT NULL GROUP BY task.id) AND tbl_tasks.task_status IN (0,1,3) AND tbl_tasks.task_closed = 0 AND tbl_tasks.task_cancel = 0 AND tbl_task_instruct.isactive = 1 GROUP BY tbl_tasks.id)");
                       		}]);
                       	}*/
					}
				],false)->joinWith(['teamPriority'],false);
		if(isset($params['unassignproject'])) {
			$sqlunassignedproject="SELECT DISTINCT tbl_tasks.id FROM tbl_tasks
				INNER JOIN tbl_task_instruct ON tbl_task_instruct.task_id = tbl_tasks.id AND tbl_task_instruct.isactive=1
				INNER JOIN tbl_tasks_units ON tbl_task_instruct.id =tbl_tasks_units.task_instruct_id
				WHERE tbl_tasks.client_case_id = $case_id AND tbl_task_instruct.task_id NOT IN (
					SELECT task.id FROM tbl_tasks as task
					INNER JOIN tbl_task_instruct as ti ON ti.task_id = task.id AND ti.isactive=1
					INNER JOIN tbl_tasks_units as tu ON ti.id = tu.task_instruct_id
					WHERE tbl_tasks.client_case_id=$case_id AND task.task_status IN (0,1,3) AND task.task_closed = 0 AND task.task_cancel = 0 AND ti.isactive = 1 AND unit_assigned_to!=0 AND unit_assigned_to!='' AND unit_assigned_to IS NOT NULL
					GROUP BY task.id)  AND tbl_tasks.task_status IN (0,1,3) AND tbl_tasks.task_closed = 0 AND tbl_tasks.task_cancel = 0 AND tbl_task_instruct.isactive = 1
					GROUP BY tbl_tasks.id";
			$query->andWhere("tbl_tasks.id IN ($sqlunassignedproject)");
		}
		if(isset($params['active']) && $params['active']="active" && $params['TaskSearch']['task_status'] == "") {
			$query->andWhere('tbl_tasks.task_status IN (0,1,3)');
		}
        if(isset($params['task_id'])) {
    		$query->andWhere('tbl_tasks.id = '.$params['task_id']);
    	}
        if(isset($params['todotaskids'])) {
    		$query->andWhere('tbl_tasks.id IN ('.$params['todotaskids'].')');
    	}
        if(isset($params['comment']) && $params['comment']="comment") {
            $task_ids = (new Tasks)->getUnreadComments($params['case_id'], "task_ids");
            if (empty($task_ids)) {
    		   $task_ids[0] = 0;
    		}
    		if (!empty($task_ids)) {
               $query->andWhere('tbl_tasks.id IN ('.implode(",",$task_ids).')');
            }
    	}
        $currdate=time();
    	if(isset($params['due'])){
    		if($params['due']=='past'){
    			if (DB_TYPE == 'sqlsrv') {
    				$query->andWhere("Cast((CAST(tbl_task_instruct.task_duedate as varchar)  + ' ' + tbl_task_instruct.task_timedue) as datetime) < '" . date('Y-m-d H:i:s', time()) . "'");
    			} else {
    				$query->andWhere("tbl_task_instruct.task_duedate < '" . date('Y-m-d H:i:s', time()) . "'");
    			}
    			$query->andWhere('tbl_tasks.task_status !=4');
    		}
    		if($params['due']=='notpastdue') {
    			$query->andWhere('tbl_tasks.task_closed = 0 AND tbl_tasks.task_cancel = 0');
    		}
    	}

        if(isset($params['TaskSearch']['task_cancel']) && $params['TaskSearch']['task_cancel']=1)
			$query->andWhere(['task_cancel'=>1]);
		else
			$query->andWhere(['task_cancel'=>0]);

		if(isset($params['TaskSearch']['per_complete']) && $params['TaskSearch']['per_complete']!=null && $params['TaskSearch']['per_complete']!=0){
                        $params['TaskSearch']['per_complete'] = str_replace( array('%','!','@','#','$','&','*','(',')','-','_','+','=','{','}','[',']',',','<','>','.','/','?',':',';','"','\''), '', $params['TaskSearch']['per_complete']);
			$query->andWhere(["ROUND(".(new \app\models\Tasks())->getTaskPercentageCompleteByTaskid('tbl_tasks.id').",0)"=>$params['TaskSearch']['per_complete']]);
		}

		if(isset($params['TaskSearch']['task_closed']) && $params['TaskSearch']['task_closed']=1){
			$query->andWhere(['task_closed'=>1]);
		} else {
 			$query->andWhere(['task_closed'=>0]);
 		}

        $settings_info = Settings::find()->select(['fieldvalue'])->where(['field' => 'project_sort'])->one();
		$fieldvalue=$settings_info->fieldvalue;

 		$defaultSort="";
        if(isset($params['sort'])) {
        	if($params['sort']=='LoadPrevious') {
        		$defaultSort=['id'=>SORT_DESC];
        	}
        }else{
			/*IRT 26*/
			$optionsproject_sort_display=Options::find()->select('project_sort_display')->where(['user_id'=>Yii::$app->user->identity->id])->andWhere('project_sort_display IS NOT NULL')->one()->project_sort_display;

			if(isset($optionsproject_sort_display) && in_array($optionsproject_sort_display, array(0,1,2,3))) {
				//echo $optionsproject_sort_display."here";die;
				$fieldvalue =$optionsproject_sort_display;
			}
			//echo $fieldvalue;die;
			/* IRT 26 */
			if ($fieldvalue == '' || $fieldvalue == '0') {
				//echo "here";die;
	    		$defaultSort=['priority'=>SORT_ASC,'task_duedate'=>SORT_DESC];
	    	} else if ($fieldvalue == '1') {
	    		$defaultSort=['task_duedate'=>SORT_DESC];
	    	} else if ($fieldvalue == '2') {
	    		$defaultSort=['id'=>SORT_DESC];
	    	} else if ($fieldvalue == '3') {
	            $defaultSort=['priority'=>SORT_ASC,'task_duedate'=> SORT_DESC];
	    	} else {
	    		$defaultSort=['id'=>SORT_ASC];
	    	}
	    }
        //echo "<pre>",print_r($defaultSort);die;
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' =>['pageSize'=>25],
        ]);
        //echo "<pre>",print_r($defaultSort);die;
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
			$dataProvider->sort->defaultOrder=$defaultSort;
		}
        if(isset($params['sort']) && $params['sort'] =='id,-task_id'){
            //Skip if already ID
        }else{
        $dataProvider->sort->attributes['id'] = [
                        'asc' => ['tbl_tasks.id' => SORT_ASC],
                        'desc' => ['tbl_tasks.id' => SORT_DESC],
                ];
        }
       /** IRT 169 Changes **/
       // $dataProvider->sort->attributes['priority_team'] = [
       //     'asc' => ['tbl_priority_team.priority_order' => SORT_ASC],
       //     'desc' => ['tbl_priority_team.priority_order' => SORT_DESC],
       // ];
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
        $dataProvider->sort->attributes['project_name'] = [
            'asc' => ['project_name' => SORT_ASC],
            'desc' => ['project_name' => SORT_DESC],
        ];
        // echo "<pre>",print_r($dataProvider),"</pre>";die;
        /* multiselect */

        if ($params['TaskSearch']['id'] != null && is_array($params['TaskSearch']['id'])) {
			if(!empty($params['TaskSearch']['id'])){
				foreach($params['TaskSearch']['id'] as $k=>$v){
					if($v=='All'){ // || strpos($v,",") !== false
						unset($params['TaskSearch']['id']);
					}
				}
			}
		}
		if ($params['TaskSearch']['task_status'] != null && is_array($params['TaskSearch']['task_status'])) {
			if(!empty($params['TaskSearch']['task_status'])){
				foreach($params['TaskSearch']['task_status'] as $k=>$v){
					if($v=='All'){ // || strpos($v,",") !== false
						unset($params['TaskSearch']['task_status']);
					}
				}
			}
		}
		if ($params['TaskSearch']['priority'] != null && is_array($params['TaskSearch']['priority'])) {
			if(!empty($params['TaskSearch']['priority'])){
				foreach($params['TaskSearch']['priority'] as $k=>$v){
					if($v=='All'){ // || strpos($v,",") !== false
						unset($params['TaskSearch']['priority']);
					}
				}
			}
			$query->andFilterWhere(["priority"=>$params['TaskSearch']['priority']]);
		}
		if ($params['TaskSearch']['project_name'] != null && is_array($params['TaskSearch']['project_name'])) {
			if(!empty($params['TaskSearch']['project_name'])){
				foreach($params['TaskSearch']['project_name'] as $k=>$v){
					if($v=='All'){ // || strpos($v,",") !== false
						unset($params['TaskSearch']['project_name']);
					}
				}
			}
			$query->andFilterWhere(['or like', 'project_name' , $params['TaskSearch']['project_name']]);
		}else{
			$query->andFilterWhere(['like', 'project_name' , $params['TaskSearch']['project_name']]);
			$this->project_name=$params['TaskSearch']['project_name'];
		}
		/*multiselect*/

        $this->load($params);

        if(isset($params['TaskSearch']['id']) && $params['TaskSearch']['id']!="" && $params['TaskSearch']['id']!="All"){
		   $query->andFilterWhere(['tbl_tasks.id' => $params['TaskSearch']['id']]);
		}

		/* Start IRT 374 */
        //if(isset($params['TaskSearch']['task_status']) && $params['TaskSearch']['task_status']!="" && $params['TaskSearch']['task_status']!="All"){
		if ($params['TaskSearch']['task_status'] != null && is_array($params['TaskSearch']['task_status'])) {
        	if(!empty($params['TaskSearch']['task_status'])){
        		foreach($params['TaskSearch']['task_status'] as $k=>$v){
        			if($v=='All'){ //  || strpos($v,",") !== false
        				unset($params['TaskSearch']['task_status']);
        			}
        		}
        	}
        	$tStatus = array();
        	foreach($params['TaskSearch']['task_status'] as $k => $v1){
        		if($v1 >= 2) $tStatus[$k] = ++$v1;
        		else
				$tStatus[$k] = $v1;
        	}
        	if (in_array('5', $tStatus)) {
				$query->joinWith(['comments' => function(\yii\db\ActiveQuery $query) use($userId) {
        			$query->select(['tbl_comments.id','tbl_comments.task_id']);
        			$query->where(['NOT IN','tbl_comments.id', CommentsRead::find()->select(['comment_id'])->where(['user_id' => $userId]) ]);
        			$query->andWhere(['tbl_comments.comment_origination' => 1]);
        		}]);
        	}
        	if(!array_search('5',$tStatus))	$tStatus = array_diff($tStatus, array('5'));
        		$query->andFilterWhere(['or like', 'task_status', $tStatus]);
        }
        /* End IRT 374 */

        if(isset($params['TaskSearch']['task_duedate']) && $params['TaskSearch']['task_duedate']!=""){
			$task_duedate = explode("-", $params['TaskSearch']['task_duedate']);
            $task_duedate_start = explode("/", trim($task_duedate[0]));
            $task_duedate_end = explode("/", trim($task_duedate[1]));
            $task_duedate_s = $task_duedate_start[2] . "-" . $task_duedate_start[0] . "-" . $task_duedate_start[1].' 00:00:00';
            $task_duedate_e = $task_duedate_end[2] . "-" . $task_duedate_end[0] . "-" . $task_duedate_end[1].' 23:59:59';
           // $task_duedate_s=trim($task_duedate[0]).' 00:00:00';
			//	$task_duedate_e=trim($task_duedate[1]).' 23:59:59';

			$query->andWhere(" A.task_date_time >= '$task_duedate_s' AND A.task_date_time  <= '$task_duedate_e' ");
        }
        //if(isset($params['TaskSearch']['priority']) && $params['TaskSearch']['priority']!="" && $params['TaskSearch']['priority']!="All")
          //  $query->andWhere("priority = '".$params['TaskSearch']['priority']."'");
		if(isset($params['TaskSearch']['task_cancel_reason']) && $params['TaskSearch']['task_cancel_reason']!="" && $params['TaskSearch']['task_cancel_reason']!="All")
            $query->andFilterWhere(['like', 'task_cancel_reason', $params['TaskSearch']['task_cancel_reason']]);
		//if(isset($params['TaskSearch']['project_name']) && $params['TaskSearch']['project_name']!="" && $params['TaskSearch']['project_name']!="All")
          //  $query->andFilterWhere(['like', 'project_name', $params['TaskSearch']['project_name']]);

		//$this->load($params);
        if (!$this->validate()) {
            return $dataProvider;
        }
		
		//echo "<pre>",print_r($dataProvider->getModels()),"</pre>";die;
        return $dataProvider;
    }

    public function searchFilter($params,$case_id)
    {
    	$dataProvider = array();
    	$newParams=$params['params'];

    	/**
    	 * IRT 169 Remove priority_order
    	 */
    	$query = Tasks::find()
    			->select(['tbl_tasks.id','tbl_tasks.client_case_id','tbl_tasks.task_status','tbl_tasks.task_closed','tbl_tasks.task_cancel','tbl_tasks.task_cancel_reason','tbl_client_case.case_name','tbl_task_instruct.project_name','tbl_task_instruct.task_duedate','tbl_task_instruct.task_priority','tbl_priority_project.priority_order', 'tbl_priority_project.priority'/*, 'tbl_priority_team.priority_order as teamorder'*/])
    			->joinWith('clientCase')->where(['tbl_client_case.is_close'=>0, 'tbl_tasks.client_case_id' => $params['case_id']])
                ->joinWith([
                	'taskInstruct' => function (\yii\db\ActiveQuery $query) use ($params,$case_id){
                		$query->where(['isactive'=>1])->joinWith('taskPriority');
                    	if(isset($params['unassignproject'])){
                       		$query->innerJoinWith(['tasksUnits' => function (\yii\db\ActiveQuery $query) use ($case_id) {

                       			//$query->where("tbl_tasks_unit.unit_assigned_to!=0 AND tbl_tasks_units.unit_assigned_to!='' AND tbl_tasks_units.unit_assigned_to IS NOT NULL");
                       			$query->where("tbl_tasks_units.task_id IN (SELECT tbl_tasks.id FROM tbl_tasks INNER JOIN tbl_tasks_units ON tbl_tasks.id =tbl_tasks_units.task_id INNER JOIN tbl_task_instruct ON tbl_task_instruct.task_id = tbl_tasks.id WHERE tbl_tasks.client_case_id = $case_id AND tbl_tasks_units.task_id NOT IN (SELECT task.id FROM tbl_tasks as task INNER JOIN tbl_tasks_units as tu ON tu.task_id = task.id INNER JOIN tbl_task_instruct as ti ON ti.task_id = task.id WHERE client_case_id=$case_id AND task.task_status IN (0,1,3) AND task.task_closed = 0 AND task.task_cancel = 0 AND ti.isactive = 1 AND unit_assigned_to!=0 AND unit_assigned_to!='' AND unit_assigned_to IS NOT NULL GROUP BY task.id) AND tbl_tasks.task_status IN (0,1,3) AND tbl_tasks.task_closed = 0 AND tbl_tasks.task_cancel = 0 AND tbl_task_instruct.isactive = 1 GROUP BY tbl_tasks.id)");

                       		}])
                       		->groupBy('tbl_tasks_units.task_instruct_id');
                    	}
					}
				],false)
                ->joinWith('teamPriority');

		if(isset($newParams['active']) && $newParams['active']="active")
			$query->andWhere('tbl_tasks.task_status IN (0,1,3)');

        if(isset($newParams['task_id'])){
    		$query->andWhere('tbl_tasks.id = '.$newParams['task_id']);
    	}

        if(isset($newParams['todotaskids'])){
    		$query->andWhere('tbl_tasks.id IN ('.$newParams['todotaskids'].')');
    	}

    	if(isset($newParams['comment']) && $newParams['comment']="comment"){
            $task_ids = (new Tasks)->getUnreadComments($params['case_id'], "task_ids");
            if (empty($task_ids)) {
    		   $task_ids[0] = 0;
    		}
    		if (!empty($task_ids)) {
               $query->andWhere('tbl_tasks.id IN ('.implode(",",$task_ids).')');
            }
    	}

        $currdate=time();
    	if(isset($newParams['due'])){
    		if($newParams['due']=='past'){
    			if (DB_TYPE == 'sqlsrv') {
    				$query->andWhere("Cast((CAST(tbl_task_instruct.task_duedate as varchar)  + ' ' + tbl_task_instruct.task_timedue) as datetime) < '" . date('Y-m-d H:i:s', time()) . "'");
    			} else {
    				$query->andWhere("tbl_task_instruct.task_duedate < '" . date('Y-m-d H:i:s', time()) . "'");
    			}
    			$query->andWhere('tbl_tasks.task_status !=4');
    		}
    		if($newParams['due']=='notpastdue'){
    			$query->andWhere('tbl_tasks.task_closed = 0 AND tbl_tasks.task_cancel = 0');
    		}
    	}

        if(isset($params['TaskSearch']['task_cancel']) && $params['TaskSearch']['task_cancel']==1)
			$query->andWhere(['task_cancel'=>1]);
        else if(isset($params['task_cancel']) && $params['task_cancel']==1)
        	$query->andWhere(['task_cancel'=>1]);
		else
			$query->andWhere(['task_cancel'=>0]);


    	if(isset($params['TaskSearch']['task_closed']) && $params['TaskSearch']['task_closed']==1)
			$query->andWhere(['task_closed'=>1]);
    	else if(isset($params['task_closed']) && $params['task_closed']==1)
    		$query->andWhere(['task_closed'=>1]);
		else
 			$query->andWhere(['task_closed'=>0]);

    	if($params['field']=='id'){
    		if(isset($params['q']) && $params['q']!=""){
                $query->andFilterWhere(['like', 'tbl_tasks.id', $params['q'].'%',false]);
    		}$query->orderBy('id');
    		$dataProvider = ArrayHelper::map($query->all(),'id','id');
    	}
    	if($params['field']=='priority' || $params['field']=='task_priority'){
    		$query->select('tbl_task_instruct.task_priority');
    		if(isset($params['q']) && $params['q']!=""){
    			$query->andFilterWhere(['like', 'priority', $params['q']]);
    		}
    		$query->groupBy('tbl_task_instruct.task_priority');
    		$dataProvider = ArrayHelper::map(PriorityProject::find()->select(['priority'])->andWhere(['id'=>$query])->orderBy('priority')->groupBy('priority')->all(),'priority','priority');
    	}
    	if($params['field']=='project_name' ){
    		$query->select('tbl_tasks.id');
    		if(isset($params['q']) && $params['q']!=""){
                $query->andFilterWhere(['like', 'project_name', $params['q'].'%',false]);
            }
            $query->andWhere("tbl_task_instruct.project_name !='' AND tbl_task_instruct.project_name IS NOT NULL");
    		$dataProvider = ArrayHelper::map(TaskInstruct::find()->select('project_name')->where(['in','task_id',$query])->andWhere('isactive=1')->all(),'project_name','project_name');
            //$dataProvider = ArrayHelper::map(T$query->all(),'project_name','project_name');
    	}
    	if($params['field']=='task_cancel_reason' ){$query->select('task_cancel_reason');
    		if(isset($params['q']) && $params['q']!=""){
    			$query->andFilterWhere(['like', 'task_cancel_reason', $params['q']]);
    		}
    		$dataProvider = ArrayHelper::map($query->groupBy('task_cancel_reason')->all(),'task_cancel_reason','task_cancel_reason');
    	}


    	return array_merge(array(''=>'All'), $dataProvider);
    }
}
