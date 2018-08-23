<?php
namespace app\models;
use Yii;
use yii\helpers\Html;
use yii\data\SqlDataProvider;
use yii\helpers\ArrayHelper;
use app\models\User;
use app\models\Mydocument;
/**
 * This is the model class for table "{{%task_instruct_servicetask}}".
 *
 * @property integer $id
 * @property integer $task_instruct_id
 * @property integer $task_id
 * @property integer $team_id
 * @property integer $teamservice_id
 * @property integer $servicetask_id
 * @property double $est_time
 * @property integer $team_loc
 * @property integer $sort_order
 *
 * @property Servicetask $servicetask
 * @property Team $team
 * @property Teamservice $teamservice
 * @property TeamlocationMaster $teamLoc
 * @property TaskInstruct $taskInstruct
 * @property TasksUnits[] $tasksUnits
 * @property TasksUnitsData[] $tasksUnitsDatas
 */
class TaskInstructServicetask extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
     

    public static function tableName()
    {
        return '{{%task_instruct_servicetask}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['task_instruct_id', 'task_id', 'team_id', 'teamservice_id', 'servicetask_id',  'team_loc'], 'required'],
            [['task_instruct_id', 'task_id', 'team_id', 'teamservice_id', 'servicetask_id', 'team_loc', 'sort_order'], 'integer'],
            [['est_time'], 'number'],
            [['servicetask_id'], 'exist', 'skipOnError' => true, 'targetClass' => Servicetask::className(), 'targetAttribute' => ['servicetask_id' => 'id']],
            [['team_id'], 'exist', 'skipOnError' => true, 'targetClass' => Team::className(), 'targetAttribute' => ['team_id' => 'id']],
            [['teamservice_id'], 'exist', 'skipOnError' => true, 'targetClass' => Teamservice::className(), 'targetAttribute' => ['teamservice_id' => 'id']],
            [['team_loc'], 'exist', 'skipOnError' => true, 'targetClass' => TeamlocationMaster::className(), 'targetAttribute' => ['team_loc' => 'id']],
            [['task_instruct_id'], 'exist', 'skipOnError' => true, 'targetClass' => TaskInstruct::className(), 'targetAttribute' => ['task_instruct_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'task_instruct_id' => 'Task Instruct ID',
            'task_id' => 'Task ID',
            'team_id' => 'Team ID',
            'teamservice_id' => 'Teamservice ID',
            'servicetask_id' => 'Servicetask ID',
            'est_time' => 'Est Time',
            'team_loc' => 'Team Loc',
            'sort_order' => 'Sort Order',
        ];
    }
	/**
     * Method to check service task has access or not
     * */
    public function checkDeniedServiceFlag($task_id,$case_id=0,$team_id=0,$team_loc=0,$model,$belongtocurr_team){
    	$roleId = Yii::$app->user->identity->role_id;
    	if($roleId == 0){
			return true;
		}
    	if($model['sort_order']==0){
    		return true;
    	}
    	if($case_id!=0){
    		if (!in_array($model['servicetask_id'], $belongtocurr_team)){
    			return false;
    		}
    		$hasAccessLocation=ProjectSecurity::find()->where('user_id='.Yii::$app->user->identity->id.' AND team_id!=0 AND team_id='.$model['teamId'].' AND team_loc='.$model['team_loc'])->select(['team_id','team_loc'])->count();
    		if (isset($model['team_loc']) && $model['team_loc']!=0 && $model['team_loc']!="" && $model['teamId'] != 1 && $hasAccessLocation == 0) {
    			return false;
    		}
    	}else{
    		if (!empty($belongtocurr_team) && !in_array($model['servicetask_id'], $belongtocurr_team)) {
    			return false;
    		}
    		$hasAccessLocation=ProjectSecurity::find()->where('user_id='.Yii::$app->user->identity->id.' AND team_id!=0 AND team_id='.$model['teamId'].' AND team_loc='.$model['team_loc'])->select(['team_id','team_loc'])->count();
    		if (isset($model['team_loc']) && $model['team_loc']!=0 && $model['team_loc']!="" && $model['teamId'] != 1 && $hasAccessLocation == 0) {
    			return false;
    		}
    	}
    	return true;
    }
    /**
     * Method to check service task has access or not
     * */
    public function checkDeniedService($task_id,$case_id=0,$team_id=0,$team_loc=0,$model,$belongtocurr_team){
    	$roleId = Yii::$app->user->identity->role_id;
    	if($roleId == 0){
			return;
		}
    	if($model['sort_order']==0){
    		return;
    	}
    	if($case_id!=0) {
    		if (!in_array($model['servicetask_id'], $belongtocurr_team)){
    			return ['style'=>'background:none repeat scroll 0 0 #FFFFD4;','class'=>'denied'];
    		}
    		$hasAccessLocation=ProjectSecurity::find()->where('user_id='.Yii::$app->user->identity->id.' AND team_id!=0 AND team_id='.$model['teamId'].' AND team_loc='.$model['team_loc'])->select(['team_id','team_loc'])->count();
    		if (isset($model['team_loc']) && $model['team_loc']!=0 && $model['team_loc']!="" && $model['teamId'] != 1 && $hasAccessLocation == 0) {
    			return ['style'=>'background:none repeat scroll 0 0 #FFFFD4;','class'=>'denied'];
    		}
    	} else {
    		if (!empty($belongtocurr_team) && !in_array($model['servicetask_id'], $belongtocurr_team)) {
    			return ['style'=>'background:none repeat scroll 0 0 #FFFFD4;','class'=>'denied'];
    		}
    		$hasAccessLocation=ProjectSecurity::find()->where('user_id='.Yii::$app->user->identity->id.' AND team_id!=0 AND team_id='.$model['teamId'].' AND team_loc='.$model['team_loc'])->select(['team_id','team_loc'])->count();
    		if (isset($model['team_loc']) && $model['team_loc']!=0 && $model['team_loc']!="" && $model['teamId'] != 1 && $hasAccessLocation == 0) {
    			return ['style'=>'background:none repeat scroll 0 0 #FFFFD4;','class'=>'denied'];
    		}
    	}
    	return;
    }
	/**
	* Method to get data of track project media section in team and case wise
	*/
	public function getTrackProjectDataWithMedia($task_id,$case_id=0,$team_id=0,$options=array()){
		/*$track_data=array();
    	$track_data['media']=$this->checkHasMedia($task_id,$case_id,$team_id,$options);
    	return $track_data;*/
    	$userId = Yii::$app->user->identity->id;
    	$where  = "";
    	if(isset($options['option'])&&$options['option']!=""){
    		$options['option'] = str_replace("#","",$options['option']);
    	}
    	/*Filter*/
    	if(isset($options['option']) && trim($options['option']) == 'Team'){
    		if($team_id==0){
    			$where= " AND tbl_tasks_units.team_id IN (1)";
    		}else{
    			if(isset($options['team_loc']) && $options['team_loc']!=0){
    				$where= " AND tbl_tasks_units.team_id IN (".$team_id.") AND tbl_tasks_units.team_loc=".$options['team_loc'];
    			}else{
    				$where= " AND tbl_tasks_units.team_id IN (".$team_id.")";
    			}
    		}
    	} 
    	if(isset($options['option']) && $options['option'] == 'My'){
    		if($team_id==0){
    			$where= " AND tbl_tasks_units.unit_assigned_to=" . $userId;
    		}else{
    			if(isset($options['team_loc']) && $options['team_loc']!=0){
    				$where= " AND tbl_tasks_units.team_id IN (".$team_id.") AND tbl_tasks_units.team_loc=".$options['team_loc']." AND tbl_tasks_units.unit_assigned_to=" . $userId;;
    			}else{
    				$where= " AND tbl_tasks_units.team_id IN (".$team_id.") AND tbl_tasks_units.unit_assigned_to=" . $userId;
    			}
    		}
    	}
    	if(isset($options['tasks_unit_id']) && is_numeric($options['tasks_unit_id']) && $options['tasks_unit_id'] >0){
    		if($where == "")
    			$where  = " AND tbl_tasks_units.id=" .$options['tasks_unit_id'];
    		else
    			$where .= " AND tbl_tasks_units.id=" .$options['tasks_unit_id'];
    	}
    	if(isset($options['taskunit']) && is_numeric($options['taskunit']) && $options['taskunit'] >0){
    		if($where == "")
    			$where  = " AND tbl_tasks_units.id=" .$options['taskunit'];
    		else
    			$where .= " AND tbl_tasks_units.id=" .$options['taskunit'];
    	}
    	
    	if(isset($options['servicetask_id']) && is_numeric($options['servicetask_id']) && $options['servicetask_id'] >0){
    		if($where == "")
    			$where  = " AND tbl_tasks_units.servicetask_id=" .$options['servicetask_id'];
    		else
    			$where .= " AND tbl_tasks_units.servicetask_id=" .$options['servicetask_id'];
    	}
    	if (isset($options['todofilter']) && $options['todofilter'] == 'me') {
    		if($where == "")
    			$where  = " AND tbl_tasks_units.id IN (SELECT tbl_tasks_units_todos.tasks_unit_id from  tbl_tasks_units_todos WHERE tbl_tasks_units_todos.assigned=" .$userId.")";
    		else
    			$where .= " AND tbl_tasks_units.id IN (SELECT tbl_tasks_units_todos.tasks_unit_id from  tbl_tasks_units_todos WHERE tbl_tasks_units_todos.assigned=" .$userId.")";
    		
    	}
    	if (isset($options['todofilter']) && $options['todofilter'] == 'other') {
    		if($where == "")
    			$where  = " AND tbl_tasks_units.id IN (SELECT tbl_tasks_units_todos.tasks_unit_id from  tbl_tasks_units_todos WHERE tbl_tasks_units_todos.assigned!=" .$userId." AND tbl_tasks_units_todos.assigned!=0)"; 
    					
    		else
    			$where .= " AND tbl_tasks_units.id IN (SELECT tbl_tasks_units_todos.tasks_unit_id from  tbl_tasks_units_todos WHERE tbl_tasks_units_todos.assigned!=" .$userId." AND tbl_tasks_units_todos.assigned!=0)";
    	}
    	if (isset($options['todofilter']) && $options['todofilter'] == 'unassign') {
    		if($where == "")
    			$where  = " AND tbl_tasks_units.id IN (SELECT tbl_tasks_units_todos.tasks_unit_id from  tbl_tasks_units_todos WHERE tbl_tasks_units_todos.assigned=0)";
    		else
    			$where .= " AND tbl_tasks_units.id IN (SELECT tbl_tasks_units_todos.tasks_unit_id from  tbl_tasks_units_todos WHERE tbl_tasks_units_todos.assigned=0)";
    	}
    	
    	if (isset($options['status']) && is_numeric($options['status'])) { //me filter home chart
    		if($where == "")
    			$where  = " AND tbl_tasks_units.unit_status=" .$options['status'];
    		else
    			$where .= " AND tbl_tasks_units.unit_status=" .$options['status'];
    	}
    	if (isset($options['assign']) && $options['assign'] == "other") { //other filter home chart
    		if($where == "")
    			$where  = " AND tbl_tasks_units.unit_assigned_to!=" .$userId;
    		else
    			$where .= " AND tbl_tasks_units.unit_assigned_to!=" .$userId;
    	}
    	if (isset($options['assign']) && $options['assign'] == "me") { //other filter home chart
    		if($where == "")
    			$where  = " AND tbl_tasks_units.unit_assigned_to=" .$userId;
    		else
    			$where .= " AND tbl_tasks_units.unit_assigned_to=" .$userId;
    	}
    	if (isset($options['services']) && $options['services']!="") {
    		//$pendingTask_services = explode(",", $options['services']);
    		if($where == "")
    			$where  = " AND tbl_tasks_units.servicetask_id IN (" .$options['services'].")";
    		else
    			$where .= " AND tbl_tasks_units.servicetask_id IN (" .$options['services'].")";
    	}
    	/*Filter*/
    	
    	$sql="
			SELECT teamservice_id, servicetask_id, team_location_name, team_loc, sort_order as sort_order, service_name, service_task,assignuser,taskunit_id,teamId,instruction_notes,unit_assigned_to,unit_status,hastodo,hasbilling,hasteamloc FROM 
			(
				(select count(DISTINCT  tbl_task_instruct_evidence.evidence_id) as teamservice_id, (select count(tbl_task_instruct_evidence.evidence_contents_id) as mediacontent_count  
				FROM tbl_task_instruct_evidence 
				inner join tbl_task_instruct on tbl_task_instruct.id =tbl_task_instruct_evidence.task_instruct_id AND tbl_task_instruct.isactive=1
				where tbl_task_instruct.task_id=$task_id AND tbl_task_instruct_evidence.evidence_contents_id  > 0) as servicetask_id, null as team_location_name,0 as team_loc,0 as sort_order, null as service_name, null as service_task , null as assignuser,0 as taskunit_id, 0 as teamId, 0 as instruction_notes,0 as unit_assigned_to,null as unit_status,0 as hastodo,0 as hasbilling,0 as hasteamloc   FROM tbl_task_instruct_evidence 
				inner join tbl_task_instruct on tbl_task_instruct.id =tbl_task_instruct_evidence.task_instruct_id AND tbl_task_instruct.isactive=1
				where tbl_task_instruct.task_id=$task_id)
			union all
				(SELECT tbl_tasks_units.teamservice_id, tbl_tasks_units.servicetask_id, tbl_teamlocation_master.team_location_name, tbl_tasks_units.team_loc, tbl_tasks_units.sort_order as sort_order, tbl_teamservice.service_name, tbl_servicetask.service_task,CONCAT(tbl_user.usr_first_name ,' ', tbl_user.usr_lastname) as assignuser,tbl_tasks_units.id as taskunit_id,tbl_teamservice.teamid as teamId, (SELECT COUNT(*) FROM tbl_tasks_units_notes WHERE servicetask_id =tbl_tasks_units.servicetask_id AND task_id =$task_id) as instruction_notes,tbl_tasks_units.unit_assigned_to,tbl_tasks_units.unit_status,(select count(*) from tbl_tasks_units_todos WHERE tbl_tasks_units_todos.tasks_unit_id=tbl_tasks_units.id) as hastodo,(select COUNT(*) from tbl_tasks_units_billing where tbl_tasks_units_billing.tasks_unit_id=tbl_tasks_units.id) as hasbilling,(SELECT count(team_loc) FROM tbl_servicetask_team_locs INNER JOIN tbl_teamlocation_master on tbl_teamlocation_master.id=tbl_servicetask_team_locs.team_loc WHERE servicetask_id =tbl_tasks_units.servicetask_id AND team_loc NOT IN (tbl_tasks_units.team_loc) AND tbl_teamlocation_master.remove=0 ) as hasteamloc  
				FROM tbl_tasks_units 
				left join tbl_teamlocation_master on tbl_teamlocation_master.id = tbl_tasks_units.team_loc 
				left join tbl_user on tbl_user.id = tbl_tasks_units.unit_assigned_to 
				inner join tbl_servicetask on tbl_servicetask.id=tbl_tasks_units.servicetask_id 
				inner join tbl_teamservice on tbl_teamservice.id=tbl_tasks_units.teamservice_id 
				inner join tbl_task_instruct on tbl_task_instruct.id =tbl_tasks_units.task_instruct_id AND tbl_task_instruct.isactive=1 
				where tbl_task_instruct.task_id=$task_id $where)
			) as test 
			WHERE teamservice_id > 0 
			order by  sort_order";
			
		/*$sql="
			SELECT teamservice_id, servicetask_id, team_location_name, team_loc, sort_order as sort_order, service_name, service_task,assignuser,taskunit_id,teamId,instruction_notes,unit_assigned_to,unit_status FROM 
			(
				(select count(DISTINCT tbl_task_instruct_evidence.evidence_id) as teamservice_id, 
	(select count(tbl_task_instruct_evidence.evidence_contents_id) as mediacontent_count FROM tbl_task_instruct_evidence 
	inner join tbl_tasks_units ON tbl_tasks_units.task_instruct_id=tbl_task_instruct_evidence.task_instruct_id where tbl_tasks_units.task_id=$task_id AND tbl_task_instruct_evidence.evidence_contents_id > 0) as servicetask_id, 
	null as team_location_name,
	0 as team_loc,
	0 as sort_order, 
	null as service_name, 
	null as service_task , 
	null as assignuser,
	0 as taskunit_id, 
	0 as teamId, 
	0 as instruction_notes,
	0 as unit_assigned_to,
	null as unit_status 
	FROM tbl_task_instruct_evidence 
	inner join tbl_tasks_units ON tbl_tasks_units.task_instruct_id=tbl_task_instruct_evidence.task_instruct_id
	where tbl_tasks_units.task_id=$task_id)
			union all
				(SELECT tbl_tasks_units.teamservice_id, tbl_tasks_units.servicetask_id, 
				tbl_teamlocation_master.team_location_name as team_location_name,
				tbl_tasks_units.team_loc,
				tbl_tasks_units.sort_order as sort_order,
				tbl_teamservice.service_name as service_name,
				tbl_servicetask.service_task as service_task, 
				CONCAT(tbl_user.usr_first_name ,' ', tbl_user.usr_lastname) as assignuser,
				tbl_tasks_units.id as taskunit_id,
				tbl_tasks_units.team_id as teamId, 
				tbl_tasks_units_notes.id as instruction_notes,
				tbl_tasks_units.unit_assigned_to, tbl_tasks_units.unit_status   
				FROM tbl_tasks_units 
				INNER JOIN tbl_teamlocation_master on tbl_teamlocation_master.id=tbl_tasks_units.team_loc
				INNER JOIN tbl_teamservice on tbl_teamservice.id=tbl_tasks_units.teamservice_id
				INNER JOIN tbl_servicetask on tbl_servicetask.id=tbl_tasks_units.servicetask_id
				LEFT JOIN tbl_user on tbl_user.id=tbl_tasks_units.unit_assigned_to
				LEFT JOIN tbl_tasks_units_notes on tbl_tasks_units_notes.servicetask_id =tbl_tasks_units.servicetask_id AND tbl_tasks_units_notes.task_id=464
				where tbl_tasks_units.task_id=$task_id $where)
			) as test 
			WHERE teamservice_id > 0 
			order by  sort_order";	
	$sql="
			SELECT tbl_tasks_units.teamservice_id, tbl_tasks_units.servicetask_id, 
				tbl_teamlocation_master.team_location_name as team_location_name,
				tbl_tasks_units.team_loc,
				tbl_tasks_units.sort_order as sort_order,
				tbl_teamservice.service_name as service_name,
				tbl_servicetask.service_task as service_task, 
				CONCAT(tbl_user.usr_first_name ,' ', tbl_user.usr_lastname) as assignuser,
				tbl_tasks_units.id as taskunit_id,
				tbl_tasks_units.team_id as teamId, 
				tbl_tasks_units_notes.id as instruction_notes,
				tbl_tasks_units.unit_assigned_to, tbl_tasks_units.unit_status   
				FROM tbl_tasks_units 
				INNER JOIN tbl_teamlocation_master on tbl_teamlocation_master.id=tbl_tasks_units.team_loc
				INNER JOIN tbl_teamservice on tbl_teamservice.id=tbl_tasks_units.teamservice_id
				INNER JOIN tbl_servicetask on tbl_servicetask.id=tbl_tasks_units.servicetask_id
				LEFT JOIN tbl_user on tbl_user.id=tbl_tasks_units.unit_assigned_to
				LEFT JOIN tbl_tasks_units_notes on tbl_tasks_units_notes.servicetask_id =tbl_tasks_units.servicetask_id AND tbl_tasks_units_notes.task_id=464
				where tbl_tasks_units.task_id=$task_id $where order by  tbl_tasks_units.sort_order";		
*/
		//echo $sql;die;	
		$dataProvider = new SqlDataProvider([
    			'sql' => $sql,
    			//'params' => [':task_id' => $task_id],
			'pagination' => [
			    'pageSize' => '-1',
			]
    	]);
    	
    	return $dataProvider;
	}
	/**
	 * Method to get data of tarck project Media section team and case wise
	 * */
	public function getTrackProjectMediaData($task_id,$case_id=0,$team_id=0,$options=array()){
		$userId = Yii::$app->user->identity->id;
    	$where  = "";
    	if(isset($options['option'])&&$options['option']!=""){
    		$options['option'] = str_replace("#","",$options['option']);
    	}
    	/*Filter*/
    	if(isset($options['option']) && trim($options['option']) == 'Team'){
    		if($team_id==0){
    			$where= " AND tbl_tasks_units.team_id IN (1)";
    		}else{
    			if(isset($options['team_loc']) && $options['team_loc']!=0){
    				$where= " AND tbl_tasks_units.team_id IN (".$team_id.") AND tbl_tasks_units.team_loc=".$options['team_loc'];
    			}else{
    				$where= " AND tbl_tasks_units.team_id IN (".$team_id.")";
    			}
    		}
    	} 
    	if(isset($options['option']) && $options['option'] == 'My'){
    		if($team_id==0){
    			$where= " AND tbl_tasks_units.unit_assigned_to=" . $userId;
    		}else{
    			if(isset($options['team_loc']) && $options['team_loc']!=0){
    				$where= " AND tbl_tasks_units.team_id IN (".$team_id.") AND tbl_tasks_units.team_loc=".$options['team_loc']." AND tbl_tasks_units.unit_assigned_to=" . $userId;;
    			}else{
    				$where= " AND tbl_tasks_units.team_id IN (".$team_id.") AND tbl_tasks_units.unit_assigned_to=" . $userId;
    			}
    		}
    	}
    	if(isset($options['tasks_unit_id']) && is_numeric($options['tasks_unit_id']) && $options['tasks_unit_id'] >0){
    		if($where == "")
    			$where  = " AND tbl_tasks_units.id=" .$options['tasks_unit_id'];
    		else
    			$where .= " AND tbl_tasks_units.id=" .$options['tasks_unit_id'];
    	}
    	if(isset($options['taskunit']) && is_numeric($options['taskunit']) && $options['taskunit'] >0){
    		if($where == "")
    			$where  = " AND tbl_tasks_units.id=" .$options['taskunit'];
    		else
    			$where .= " AND tbl_tasks_units.id=" .$options['taskunit'];
    	}
    	
    	if(isset($options['servicetask_id']) && is_numeric($options['servicetask_id']) && $options['servicetask_id'] >0){
    		if($where == "")
    			$where  = " AND tbl_tasks_units.servicetask_id=" .$options['servicetask_id'];
    		else
    			$where .= " AND tbl_tasks_units.servicetask_id=" .$options['servicetask_id'];
    	}
    	if (isset($options['todofilter']) && $options['todofilter'] == 'me') {
    		if($where == "")
    			$where  = " AND tbl_tasks_units.id IN (SELECT tbl_tasks_units_todos.tasks_unit_id from  tbl_tasks_units_todos WHERE tbl_tasks_units_todos.assigned=" .$userId.")";
    		else
    			$where .= " AND tbl_tasks_units.id IN (SELECT tbl_tasks_units_todos.tasks_unit_id from  tbl_tasks_units_todos WHERE tbl_tasks_units_todos.assigned=" .$userId.")";
    		
    	}
    	if (isset($options['todofilter']) && $options['todofilter'] == 'other') {
    		if($where == "")
    			$where  = " AND tbl_tasks_units.id IN (SELECT tbl_tasks_units_todos.tasks_unit_id from  tbl_tasks_units_todos WHERE tbl_tasks_units_todos.assigned!=" .$userId." AND tbl_tasks_units_todos.assigned!=0)"; 
    					
    		else
    			$where .= " AND tbl_tasks_units.id IN (SELECT tbl_tasks_units_todos.tasks_unit_id from  tbl_tasks_units_todos WHERE tbl_tasks_units_todos.assigned!=" .$userId." AND tbl_tasks_units_todos.assigned!=0)";
    	}
    	if (isset($options['todofilter']) && $options['todofilter'] == 'unassign') {
    		if($where == "")
    			$where  = " AND tbl_tasks_units.id IN (SELECT tbl_tasks_units_todos.tasks_unit_id from  tbl_tasks_units_todos WHERE tbl_tasks_units_todos.assigned=0)";
    		else
    			$where .= " AND tbl_tasks_units.id IN (SELECT tbl_tasks_units_todos.tasks_unit_id from  tbl_tasks_units_todos WHERE tbl_tasks_units_todos.assigned=0)";
    	}
    	
    	if (isset($options['status']) && is_numeric($options['status'])) { //me filter home chart
    		if($where == "")
    			$where  = " AND tbl_tasks_units.unit_status=" .$options['status'];
    		else
    			$where .= " AND tbl_tasks_units.unit_status=" .$options['status'];
    	}
    	if (isset($options['assign']) && $options['assign'] == "other") { //other filter home chart
    		if($where == "")
    			$where  = " AND tbl_tasks_units.unit_assigned_to!=" .$userId;
    		else
    			$where .= " AND tbl_tasks_units.unit_assigned_to!=" .$userId;
    	}
    	if (isset($options['assign']) && $options['assign'] == "me") { //other filter home chart
    		if($where == "")
    			$where  = " AND tbl_tasks_units.unit_assigned_to=" .$userId;
    		else
    			$where .= " AND tbl_tasks_units.unit_assigned_to=" .$userId;
    	}
    	if (isset($options['services']) && $options['services']!="") {
    		//$pendingTask_services = explode(",", $options['services']);
    		if($where == "")
    			$where  = " AND tbl_tasks_units.servicetask_id IN (" .$options['services'].")";
    		else
    			$where .= " AND tbl_tasks_units.servicetask_id IN (" .$options['services'].")";
    	}
    	/*Filter*/
		$sql="select count(DISTINCT tbl_task_instruct_evidence.evidence_id) as teamservice_id, 
	(select count(tbl_task_instruct_evidence.evidence_contents_id) as mediacontent_count FROM tbl_task_instruct_evidence 
	inner join tbl_tasks_units ON tbl_tasks_units.task_instruct_id=tbl_task_instruct_evidence.task_instruct_id where tbl_tasks_units.task_id=$task_id AND tbl_task_instruct_evidence.evidence_contents_id > 0) as servicetask_id, 
	null as team_location_name,
	0 as team_loc,
	0 as sort_order, 
	null as service_name, 
	null as service_task , 
	null as assignuser,
	0 as taskunit_id, 
	0 as teamId, 
	0 as instruction_notes,
	0 as unit_assigned_to,
	null as unit_status 
	FROM tbl_task_instruct_evidence 
	inner join tbl_tasks_units ON tbl_tasks_units.task_instruct_id=tbl_task_instruct_evidence.task_instruct_id
	where tbl_tasks_units.task_id=$task_id";
	$dataProvider = new SqlDataProvider([
    			'sql' => $sql,
    			//'params' => [':task_id' => $task_id],
			'pagination' => [
			    'pageSize' => '-1',
			]
    	]);
    	
    	return $dataProvider;
	}
	/**
	 * Method to get data of tarck project section team and case wise
	 * */
    public function getTrackProjectData($task_id,$case_id=0,$team_id=0,$options=array()){
    	/*$track_data=array();
    	$track_data['media']=$this->checkHasMedia($task_id,$case_id,$team_id,$options);
    	return $track_data;*/
    	$userId = Yii::$app->user->identity->id;
    	$where  = "";
    	if(isset($options['option'])&&$options['option']!=""){
    		$options['option'] = str_replace("#","",$options['option']);
    	}
    	/*Filter*/
    	if(isset($options['option']) && trim($options['option']) == 'Team'){
    		if($team_id==0){
    			$where= " AND tbl_tasks_units.team_id IN (1)";
    		}else{
    			if(isset($options['team_loc']) && $options['team_loc']!=0){
    				$where= " AND tbl_tasks_units.team_id IN (".$team_id.") AND tbl_tasks_units.team_loc=".$options['team_loc'];
    			}else{
    				$where= " AND tbl_tasks_units.team_id IN (".$team_id.")";
    			}
    		}
    	} 
    	if(isset($options['option']) && $options['option'] == 'My'){
    		if($team_id==0){
    			$where= " AND tbl_tasks_units.unit_assigned_to=" . $userId;
    		}else{
    			if(isset($options['team_loc']) && $options['team_loc']!=0){
    				$where= " AND tbl_tasks_units.team_id IN (".$team_id.") AND tbl_tasks_units.team_loc=".$options['team_loc']." AND tbl_tasks_units.unit_assigned_to=" . $userId;;
    			}else{
    				$where= " AND tbl_tasks_units.team_id IN (".$team_id.") AND tbl_tasks_units.unit_assigned_to=" . $userId;
    			}
    		}
    	}
    	if(isset($options['tasks_unit_id']) && is_numeric($options['tasks_unit_id']) && $options['tasks_unit_id'] >0){
    		if($where == "")
    			$where  = " AND tbl_tasks_units.id=" .$options['tasks_unit_id'];
    		else
    			$where .= " AND tbl_tasks_units.id=" .$options['tasks_unit_id'];
    	}
    	if(isset($options['taskunit']) && is_numeric($options['taskunit']) && $options['taskunit'] >0){
    		if($where == "")
    			$where  = " AND tbl_tasks_units.id=" .$options['taskunit'];
    		else
    			$where .= " AND tbl_tasks_units.id=" .$options['taskunit'];
    	}
    	
    	if(isset($options['servicetask_id']) && is_numeric($options['servicetask_id']) && $options['servicetask_id'] >0){
    		if($where == "")
    			$where  = " AND tbl_tasks_units.servicetask_id=" .$options['servicetask_id'];
    		else
    			$where .= " AND tbl_tasks_units.servicetask_id=" .$options['servicetask_id'];
    	}
    	if (isset($options['todofilter']) && $options['todofilter'] == 'me') {
    		if($where == "")
    			$where  = " AND tbl_tasks_units.id IN (SELECT tbl_tasks_units_todos.tasks_unit_id from  tbl_tasks_units_todos WHERE tbl_tasks_units_todos.assigned=" .$userId.")";
    		else
    			$where .= " AND tbl_tasks_units.id IN (SELECT tbl_tasks_units_todos.tasks_unit_id from  tbl_tasks_units_todos WHERE tbl_tasks_units_todos.assigned=" .$userId.")";
    		
    	}
    	if (isset($options['todofilter']) && $options['todofilter'] == 'other') {
    		if($where == "")
    			$where  = " AND tbl_tasks_units.id IN (SELECT tbl_tasks_units_todos.tasks_unit_id from  tbl_tasks_units_todos WHERE tbl_tasks_units_todos.assigned!=" .$userId." AND tbl_tasks_units_todos.assigned!=0)"; 
    					
    		else
    			$where .= " AND tbl_tasks_units.id IN (SELECT tbl_tasks_units_todos.tasks_unit_id from  tbl_tasks_units_todos WHERE tbl_tasks_units_todos.assigned!=" .$userId." AND tbl_tasks_units_todos.assigned!=0)";
    	}
    	if (isset($options['todofilter']) && $options['todofilter'] == 'unassign') {
    		if($where == "")
    			$where  = " AND tbl_tasks_units.id IN (SELECT tbl_tasks_units_todos.tasks_unit_id from  tbl_tasks_units_todos WHERE tbl_tasks_units_todos.assigned=0)";
    		else
    			$where .= " AND tbl_tasks_units.id IN (SELECT tbl_tasks_units_todos.tasks_unit_id from  tbl_tasks_units_todos WHERE tbl_tasks_units_todos.assigned=0)";
    	}
    	
    	if (isset($options['status']) && is_numeric($options['status'])) { //me filter home chart
    		if($where == "")
    			$where  = " AND tbl_tasks_units.unit_status=" .$options['status'];
    		else
    			$where .= " AND tbl_tasks_units.unit_status=" .$options['status'];
    	}
    	if (isset($options['assign']) && $options['assign'] == "other") { //other filter home chart
    		if($where == "")
    			$where  = " AND tbl_tasks_units.unit_assigned_to!=" .$userId;
    		else
    			$where .= " AND tbl_tasks_units.unit_assigned_to!=" .$userId;
    	}
    	if (isset($options['assign']) && $options['assign'] == "me") { //other filter home chart
    		if($where == "")
    			$where  = " AND tbl_tasks_units.unit_assigned_to=" .$userId;
    		else
    			$where .= " AND tbl_tasks_units.unit_assigned_to=" .$userId;
    	}
    	if (isset($options['services']) && $options['services']!="") {
    		//$pendingTask_services = explode(",", $options['services']);
    		if($where == "")
    			$where  = " AND tbl_tasks_units.servicetask_id IN (" .$options['services'].")";
    		else
    			$where .= " AND tbl_tasks_units.servicetask_id IN (" .$options['services'].")";
    	}
    	/*Filter*/
    	
    	/*$sql="
			SELECT teamservice_id, servicetask_id, team_location_name, team_loc, sort_order as sort_order, service_name, service_task,assignuser,taskunit_id,teamId,instruction_notes,unit_assigned_to,unit_status,hastodo,hasbilling,hasteamloc FROM 
			(
				(select count(DISTINCT  tbl_task_instruct_evidence.evidence_id) as teamservice_id, (select count(tbl_task_instruct_evidence.evidence_contents_id) as mediacontent_count  
				FROM tbl_task_instruct_evidence 
				inner join tbl_task_instruct on tbl_task_instruct.id =tbl_task_instruct_evidence.task_instruct_id AND tbl_task_instruct.isactive=1
				where tbl_task_instruct.task_id=$task_id AND tbl_task_instruct_evidence.evidence_contents_id  > 0) as servicetask_id, null as team_location_name,0 as team_loc,0 as sort_order, null as service_name, null as service_task , null as assignuser,0 as taskunit_id, 0 as teamId, 0 as instruction_notes,0 as unit_assigned_to,null as unit_status,0 as hastodo,0 as hasbilling,0 as hasteamloc   FROM tbl_task_instruct_evidence 
				inner join tbl_task_instruct on tbl_task_instruct.id =tbl_task_instruct_evidence.task_instruct_id AND tbl_task_instruct.isactive=1
				where tbl_task_instruct.task_id=$task_id)
			union all
				(SELECT tbl_tasks_units.teamservice_id, tbl_tasks_units.servicetask_id, tbl_teamlocation_master.team_location_name, tbl_tasks_units.team_loc, tbl_tasks_units.sort_order as sort_order, tbl_teamservice.service_name, tbl_servicetask.service_task,CONCAT(tbl_user.usr_first_name ,' ', tbl_user.usr_lastname) as assignuser,tbl_tasks_units.id as taskunit_id,tbl_teamservice.teamid as teamId, (SELECT COUNT(*) FROM tbl_tasks_units_notes WHERE servicetask_id =tbl_tasks_units.servicetask_id AND task_id =$task_id) as instruction_notes,tbl_tasks_units.unit_assigned_to,tbl_tasks_units.unit_status,(select count(*) from tbl_tasks_units_todos WHERE tbl_tasks_units_todos.tasks_unit_id=tbl_tasks_units.id) as hastodo,(select COUNT(*) from tbl_tasks_units_billing where tbl_tasks_units_billing.tasks_unit_id=tbl_tasks_units.id) as hasbilling,(SELECT count(team_loc) FROM tbl_servicetask_team_locs INNER JOIN tbl_teamlocation_master on tbl_teamlocation_master.id=tbl_servicetask_team_locs.team_loc WHERE servicetask_id =tbl_tasks_units.servicetask_id AND team_loc NOT IN (tbl_tasks_units.team_loc) AND tbl_teamlocation_master.remove=0 ) as hasteamloc  
				FROM tbl_tasks_units 
				left join tbl_teamlocation_master on tbl_teamlocation_master.id = tbl_tasks_units.team_loc 
				left join tbl_user on tbl_user.id = tbl_tasks_units.unit_assigned_to 
				inner join tbl_servicetask on tbl_servicetask.id=tbl_tasks_units.servicetask_id 
				inner join tbl_teamservice on tbl_teamservice.id=tbl_tasks_units.teamservice_id 
				inner join tbl_task_instruct on tbl_task_instruct.id =tbl_tasks_units.task_instruct_id AND tbl_task_instruct.isactive=1 
				where tbl_task_instruct.task_id=$task_id $where)
			) as test 
			WHERE teamservice_id > 0 
			order by  sort_order";
		*/	
		/*$sql="
			SELECT teamservice_id, servicetask_id, team_location_name, team_loc, sort_order as sort_order, service_name, service_task,assignuser,taskunit_id,teamId,instruction_notes,unit_assigned_to,unit_status FROM 
			(
				(select count(DISTINCT tbl_task_instruct_evidence.evidence_id) as teamservice_id, 
	(select count(tbl_task_instruct_evidence.evidence_contents_id) as mediacontent_count FROM tbl_task_instruct_evidence 
	inner join tbl_tasks_units ON tbl_tasks_units.task_instruct_id=tbl_task_instruct_evidence.task_instruct_id where tbl_tasks_units.task_id=$task_id AND tbl_task_instruct_evidence.evidence_contents_id > 0) as servicetask_id, 
	null as team_location_name,
	0 as team_loc,
	0 as sort_order, 
	null as service_name, 
	null as service_task , 
	null as assignuser,
	0 as taskunit_id, 
	0 as teamId, 
	0 as instruction_notes,
	0 as unit_assigned_to,
	null as unit_status 
	FROM tbl_task_instruct_evidence 
	inner join tbl_tasks_units ON tbl_tasks_units.task_instruct_id=tbl_task_instruct_evidence.task_instruct_id
	where tbl_tasks_units.task_id=$task_id)
			union all
				(SELECT tbl_tasks_units.teamservice_id, tbl_tasks_units.servicetask_id, 
				tbl_teamlocation_master.team_location_name as team_location_name,
				tbl_tasks_units.team_loc,
				tbl_tasks_units.sort_order as sort_order,
				tbl_teamservice.service_name as service_name,
				tbl_servicetask.service_task as service_task, 
				CONCAT(tbl_user.usr_first_name ,' ', tbl_user.usr_lastname) as assignuser,
				tbl_tasks_units.id as taskunit_id,
				tbl_tasks_units.team_id as teamId, 
				tbl_tasks_units_notes.id as instruction_notes,
				tbl_tasks_units.unit_assigned_to, tbl_tasks_units.unit_status   
				FROM tbl_tasks_units 
				INNER JOIN tbl_teamlocation_master on tbl_teamlocation_master.id=tbl_tasks_units.team_loc
				INNER JOIN tbl_teamservice on tbl_teamservice.id=tbl_tasks_units.teamservice_id
				INNER JOIN tbl_servicetask on tbl_servicetask.id=tbl_tasks_units.servicetask_id
				LEFT JOIN tbl_user on tbl_user.id=tbl_tasks_units.unit_assigned_to
				LEFT JOIN tbl_tasks_units_notes on tbl_tasks_units_notes.servicetask_id =tbl_tasks_units.servicetask_id AND tbl_tasks_units_notes.task_id=464
				where tbl_tasks_units.task_id=$task_id $where)
			) as test 
			WHERE teamservice_id > 0 
			order by  sort_order";*/	
	$sql="
			SELECT tbl_tasks_units.teamservice_id, tbl_tasks_units.servicetask_id, 
				tbl_teamlocation_master.team_location_name as team_location_name,
				tbl_tasks_units.team_loc,
				tbl_tasks_units.sort_order as sort_order,
				tbl_teamservice.service_name as service_name,
				tbl_servicetask.service_task as service_task, 
				CONCAT(tbl_user.usr_first_name ,' ', tbl_user.usr_lastname) as assignuser,
				tbl_tasks_units.id as taskunit_id,
				tbl_tasks_units.team_id as teamId, 
				tbl_tasks_units_notes.id as instruction_notes,
				tbl_tasks_units.unit_assigned_to, tbl_tasks_units.unit_status,
				(
						SELECT count(team_loc) 
						FROM 
						tbl_servicetask_team_locs 
						INNER JOIN tbl_teamlocation_master on tbl_teamlocation_master.id=tbl_servicetask_team_locs.team_loc 
						WHERE servicetask_id =tbl_tasks_units.servicetask_id 
						AND team_loc NOT IN (tbl_tasks_units.team_loc) AND tbl_teamlocation_master.remove=0 ) as hasteamloc   
				FROM tbl_tasks_units 
				INNER JOIN tbl_teamlocation_master on tbl_teamlocation_master.id=tbl_tasks_units.team_loc
				INNER JOIN tbl_teamservice on tbl_teamservice.id=tbl_tasks_units.teamservice_id
				INNER JOIN tbl_servicetask on tbl_servicetask.id=tbl_tasks_units.servicetask_id
				LEFT JOIN tbl_user on tbl_user.id=tbl_tasks_units.unit_assigned_to
				LEFT JOIN tbl_tasks_units_notes on tbl_tasks_units_notes.servicetask_id =tbl_tasks_units.servicetask_id AND tbl_tasks_units_notes.task_id=464
				where tbl_tasks_units.task_id=$task_id $where order by  tbl_tasks_units.sort_order";		

		//echo $sql;die;	
		$dataProvider = new SqlDataProvider([
    			'sql' => $sql,
    			//'params' => [':task_id' => $task_id],
			'pagination' => [
			    'pageSize' => '-1',
			]
    	]);
    	
    	return $dataProvider;
    }
    
    /**
     * Method to get data of project instruction
     * */
    public function getTrackProjectDataByInstructionId($taskinstruction_id){
    	$userId = Yii::$app->user->identity->id;
    	$sql="SELECT teamservice_id, servicetask_id, team_location_name, team_loc, sort_order as sort_order, service_name, service_task,assignuser,taskunit_id,teamId FROM (
    	(select count(DISTINCT  tbl_task_instruct_evidence.evidence_id) as teamservice_id, (select count(tbl_task_instruct_evidence.evidence_contents_id) as mediacontent_count  FROM tbl_task_instruct_evidence
    	inner join tbl_task_instruct on tbl_task_instruct.id =tbl_task_instruct_evidence.task_instruct_id AND tbl_task_instruct.isactive=1
    	where tbl_task_instruct_evidence.task_instruct_id=$taskinstruction_id AND tbl_task_instruct_evidence.evidence_contents_id  > 0) as servicetask_id, null as team_location_name,0 as team_loc,0 as sort_order, null as service_name, null as service_task , null as assignuser,0 as taskunit_id, 0 as teamId  FROM tbl_task_instruct_evidence
    	inner join tbl_task_instruct on tbl_task_instruct.id =tbl_task_instruct_evidence.task_instruct_id AND tbl_task_instruct.isactive=1
    	where tbl_task_instruct_evidence.task_instruct_id=$taskinstruction_id)
    	union all
    	(SELECT tbl_task_instruct_servicetask.teamservice_id, tbl_task_instruct_servicetask.servicetask_id, tbl_teamlocation_master.team_location_name, tbl_task_instruct_servicetask.team_loc, tbl_task_instruct_servicetask.sort_order as sort_order, tbl_teamservice.service_name, tbl_servicetask.service_task,CONCAT(tbl_user.usr_first_name ,' ', tbl_user.usr_lastname) as assignuser,tbl_tasks_units.id as taskunit_id,tbl_teamservice.teamid as teamId
    	FROM tbl_task_instruct_servicetask
    	left  join tbl_teamlocation_master on tbl_teamlocation_master.id = tbl_task_instruct_servicetask.team_loc
    	left  join tbl_tasks_units on tbl_tasks_units.task_instruct_servicetask_id = tbl_task_instruct_servicetask.id
    	left  join tbl_user on tbl_user.id = tbl_tasks_units.unit_assigned_to
    	inner join tbl_servicetask on tbl_servicetask.id=tbl_task_instruct_servicetask.servicetask_id
    	inner join tbl_teamservice on tbl_teamservice.id=tbl_task_instruct_servicetask.teamservice_id
    	inner join tbl_task_instruct on tbl_task_instruct.id =tbl_task_instruct_servicetask.task_instruct_id
    	where tbl_task_instruct_servicetask.task_instruct_id=$taskinstruction_id)) as test WHERE teamservice_id > 0 order by  sort_order";
    	$dataProvider = new SqlDataProvider([
    			'sql' => $sql,
    			//'params' => [':taskinstruction_id' => $taskinstruction_id],
    			'pagination' => [
    					'pageSize' => '-1',
    			]
    	]);
    	return $dataProvider;
    }
    /**
     * Method to get services by task_id 
     * */
    public function getServicesByTaksid($task_id){
    	$sql="SELECT tbl_task_instruct_servicetask.teamservice_id, tbl_task_instruct_servicetask.servicetask_id, tbl_teamlocation_master.team_location_name, tbl_task_instruct_servicetask.team_loc, tbl_task_instruct_servicetask.sort_order as sort_order, tbl_teamservice.service_name, tbl_servicetask.service_task,CONCAT(tbl_user.usr_first_name ,' ', tbl_user.usr_lastname) as assignuser,tbl_tasks_units.id as taskunit_id,tbl_teamservice.teamid as teamId
			FROM tbl_task_instruct_servicetask 
			left  join tbl_teamlocation_master on tbl_teamlocation_master.id = tbl_task_instruct_servicetask.team_loc
			left  join tbl_tasks_units on tbl_tasks_units.task_instruct_servicetask_id = tbl_task_instruct_servicetask.id
			left  join tbl_user on tbl_user.id = tbl_tasks_units.unit_assigned_to
			inner join tbl_servicetask on tbl_servicetask.id=tbl_task_instruct_servicetask.servicetask_id
			inner join tbl_teamservice on tbl_teamservice.id=tbl_task_instruct_servicetask.teamservice_id
			inner join tbl_task_instruct on tbl_task_instruct.id =tbl_task_instruct_servicetask.task_instruct_id AND tbl_task_instruct.isactive=1
			where tbl_task_instruct_servicetask.task_id=:task_id";
	    	$dataProvider = new SqlDataProvider([
	    			'sql' => $sql,
	    			'params' => [':task_id' => $task_id],
	    	]);
	    	return $models = $dataProvider->getModels();
    } 
    /**
     * Get Media used in project by instruction  
     * */
    public function processTrackMedia($servietask_id,$sort_order,$task_id,$case_id,$team_id,$taskunit_id,$instruction_id,$options){
    	$media=array();
    	$instruct_data = TaskInstruct :: find()->select(['id','mediadisplay_by','instruct_version'])->where('id='.$instruction_id)->one();
    	if($sort_order == 0 ){
    		if($instruct_data->mediadisplay_by == 1){ //M
    			$sql="SELECT tbl_task_instruct_evidence.evidence_id FROM tbl_task_instruct_evidence LEFT JOIN tbl_task_instruct TaskInstruct ON tbl_task_instruct_evidence.task_instruct_id = TaskInstruct.id WHERE TaskInstruct.isactive=1 AND tbl_task_instruct_evidence.prod_id = 0 AND TaskInstruct.task_id=".$task_id;
    		}if($instruct_data->mediadisplay_by == 2){ //PM
    			$sql="SELECT tbl_task_instruct_evidence.evidence_id FROM tbl_task_instruct_evidence LEFT JOIN tbl_task_instruct TaskInstruct ON tbl_task_instruct_evidence.task_instruct_id = TaskInstruct.id WHERE TaskInstruct.isactive=1 AND tbl_task_instruct_evidence.prod_id != 0 AND TaskInstruct.task_id=".$task_id;
    		}
    		$sql_content = "select tbl_task_instruct_evidence.evidence_contents_id FROM tbl_task_instruct_evidence
    		inner join tbl_task_instruct on tbl_task_instruct.id =tbl_task_instruct_evidence.task_instruct_id AND tbl_task_instruct.isactive=1
    		where tbl_task_instruct.task_id=$task_id AND tbl_task_instruct_evidence.evidence_contents_id  > 0";
    		$media['media'] = Evidence::find()->select(['id','evid_type','evid_desc','quantity','contents_total_size','unit','contents_total_size_comp','comp_unit'])->where('id IN ('.$sql.')')->all();
    		$media['media_content'] =  EvidenceContents::find()->select(['id','evid_num_id','cust_id','data_size','data_copied_to'])->where('id IN ('.$sql_content.')')->all();
    	}
    	return $media;
    }
    /**
     * Get Media used in project by instruction
     * */
    public function processTrackInstruction($servietask_id,$sort_order,$task_id,$case_id,$team_id,$taskunit_id,$instruction_id,$options){
    	$task_instructions=array();
    	$instruct_data = TaskInstruct :: find()->select(['id','mediadisplay_by','instruct_version'])->where('id='.$instruction_id)->one();
    	if($sort_order != 0 ){
    		/*Instruction*/
    		$forminstrval = FormInstructionValues::find()->select(['form_builder_id','element_value','element_unit'])->where(['task_instruct_id'=>$instruct_data->id])->all();
			$formValues = array();
			$unitValues = array();
			if(!empty($forminstrval)){
				foreach($forminstrval as $instrval){
					$formValues[$instrval['form_builder_id']] = $instrval['element_value'];
					if($instrval['element_unit']!=0){
						$instrval['element_unit'] = Unit::findOne($instrval['element_unit'])->unit_name;
					} else {
						$instrval['element_unit'] = '';
					}
					$unitValues[$instrval['form_builder_id']] = $instrval['element_unit'];
					
				}
			}
    		//$sql="SELECT task_instruct_servicetask_id FROM tbl_tasks_units WHERE id=".$taskunit_id;
			$sql="SELECT id FROM tbl_task_instruct_servicetask WHERE servicetask_id=$servietask_id AND task_instruct_id=$instruction_id and task_id=".$task_id;
    		$task_instructions['task_instructions']['instructServicetask'] = $this::find()->where('id IN ('.$sql.')')->one();
    		$task_instructions['task_instructions']['formbuilder_data'] = (new FormBuilder)->getFromData($servietask_id,1,'ASC');
    		$task_instructions['task_instructions']['formValues']['active']=ArrayHelper::map(FormInstructionValues::find()->select(['form_builder_id','element_value'])->where(['task_instruct_id'=>$instruct_data->id])->all(),'form_builder_id','element_value');
    		$task_instructions['task_instructions']['unitValues']['active']=$unitValues;
    		
    		if($instruct_data->instruct_version > 1){
				
				$forminstrval = FormInstructionValues::find()->select(['form_builder_id','element_value','element_unit'])->where(['task_instruct_id'=>$instruct_lastversion_data->id])->all();
				$formValues = array();
				$unitValues = array();
				if(!empty($forminstrval)){
					foreach($forminstrval as $instrval){
						$formValues[$instrval['form_builder_id']] = $instrval['element_value'];
						if($instrval['element_unit']!=0){
							$instrval['element_unit'] = Unit::find()->select(['unit_name'])->where(['id'=>$instrval['element_unit']])->one()->unit_name;
						} else {
							$instrval['element_unit'] = '';
						}
						$unitValues[$instrval['form_builder_id']] = $instrval['element_unit'];
					}
				}
				
    			$instruct_lastversion_data = TaskInstruct :: find()->select(['id','mediadisplay_by','instruct_version'])->where('instruct_version ='.($instruct_data->instruct_version - 1).' AND task_id='.$task_id)->one();
    			$task_instructions['task_instructions']['formValues']['lastversion']=ArrayHelper::map(FormInstructionValues::find()->select(['form_builder_id','element_value'])->where(['task_instruct_id'=>$instruct_lastversion_data->id])->all(),'form_builder_id','element_value');
    			$task_instructions['task_instructions']['unitValues']['lastversion']=$unitValues;
    		}
    		
    		
    		
    		$Instrcution_data = TaskInstructNotes::find()->joinWith(['modifiedUser'],false)->where('servicetask_id ='.$servietask_id.' AND task_id ='.$task_id)->select(['tbl_tasks_units_notes.notes','tbl_tasks_units_notes.id','tbl_tasks_units_notes.modified_by','tbl_tasks_units_notes.modified'])->one();
			
    		if(!empty($Instrcution_data)){
    			$task_instructions['task_instructions']['notes'] = $Instrcution_data->notes;
    			$task_instructions['task_instructions']['attachments'] = $Instrcution_data->instructionattachments;
    			//$actLog_case = (new ActivityLog)->find()->where("activity_module_id =".$Instrcution_data->id)->orderBy('id DESC')->one();
    			//if (isset($actLog_case->id) && $actLog_case->id != 0) {
    			$task_instructions['task_instructions']['user'] = $Instrcution_data->modifiedUser->usr_first_name . " " . $Instrcution_data->modifiedUser->usr_lastname . " " . (new Options)->ConvertOneTzToAnotherTz($Instrcution_data->modified, 'UTC', $_SESSION['usrTZ']);
    			//}
    		}
    	}
    	return $task_instructions;
    }
    public function processTrackData($servietask_id,$sort_order,$task_id,$case_id,$team_id,$taskunit_id,$options,$type=0){
    	$trackData = array();
    	$instruct_data = TaskInstruct::find()->select(['id','mediadisplay_by','instruct_version'])->where('isactive = 1 AND task_id='.$task_id)->one();
    	if($sort_order == 0 ){
    		if($instruct_data->mediadisplay_by == 1){ //M
                    $sql="SELECT tbl_task_instruct_evidence.evidence_id FROM tbl_task_instruct_evidence LEFT JOIN tbl_task_instruct TaskInstruct ON tbl_task_instruct_evidence.task_instruct_id = TaskInstruct.id WHERE TaskInstruct.isactive=1 AND tbl_task_instruct_evidence.prod_id = 0 AND TaskInstruct.task_id=".$task_id;
    		}if($instruct_data->mediadisplay_by == 2){ //PM
                    $sql="SELECT tbl_task_instruct_evidence.evidence_id FROM tbl_task_instruct_evidence LEFT JOIN tbl_task_instruct TaskInstruct ON tbl_task_instruct_evidence.task_instruct_id = TaskInstruct.id WHERE TaskInstruct.isactive=1 AND tbl_task_instruct_evidence.prod_id != 0 AND TaskInstruct.task_id=".$task_id;
    		}
    		$sql_content = "select tbl_task_instruct_evidence.evidence_contents_id FROM tbl_task_instruct_evidence 
			inner join tbl_task_instruct on tbl_task_instruct.id =tbl_task_instruct_evidence.task_instruct_id AND tbl_task_instruct.isactive=1
			where tbl_task_instruct.task_id=$task_id AND tbl_task_instruct_evidence.evidence_contents_id  > 0";
    		$trackData['media'] = Evidence::find()->select(['id','evid_type','evid_desc','quantity','contents_total_size','unit','contents_total_size_comp','comp_unit'])->where('id IN ('.$sql.')')->all();
    		$trackData['media_content'] =  EvidenceContents::find()->select(['id','evid_num_id','cust_id','data_size','data_copied_to'])->where('id IN ('.$sql_content.')')->all();
    	}else{
    		/*Instruction*/
    		$trackData['task_instructions']['formbuilder_data'] = (new FormBuilder)->getFromData($servietask_id,1,'ASC');
    		
    		$forminstrval = FormInstructionValues::find()->select(['form_builder_id','element_value','element_unit'])->where(['task_instruct_id'=>$instruct_data->id])->all();
                $formValues = array();
                $unitValues = array();
                if(!empty($forminstrval)){
                    foreach($forminstrval as $instrval){
                        $formValues[$instrval['form_builder_id']] = $instrval['element_value'];
                        if($instrval['element_unit']!=0){
                                $instrval['element_unit'] = Unit::findOne($instrval['element_unit'])->unit_name;
                        } else {
                                $instrval['element_unit'] = '';
                        }
                        $unitValues[$instrval['form_builder_id']] = $instrval['element_unit'];
                    }
                }
    		
    		$trackData['task_instructions']['formValues']['active']=$formValues;
    		$trackData['task_instructions']['unitValues']['active']=$unitValues;
    		$trackData['task_instructions']['instruct'] =$instruct_data;
    		$sql="SELECT task_instruct_servicetask_id FROM tbl_tasks_units WHERE id=".$taskunit_id;
    		$trackData['task_instructions']['instructServicetask'] = $this::find()->where('id IN ('.$sql.')')->one();
    		if($instruct_data->instruct_version > 1){
    			$instruct_lastversion_data = TaskInstruct :: find()->select(['id','mediadisplay_by','instruct_version'])->where('instruct_version ='.($instruct_data->instruct_version - 1).' AND task_id='.$task_id)->one();
    			
    			$forminstrval = FormInstructionValues::find()->select(['form_builder_id','element_value','element_unit'])->where(['task_instruct_id'=>$instruct_lastversion_data->id])->all();
				$formValues = array();
				$unitValues = array();
				if(!empty($forminstrval)){
					foreach($forminstrval as $instrval){
						$formValues[$instrval['form_builder_id']] = $instrval['element_value'];
						if($instrval['element_unit']!=0){
							$instrval['element_unit'] = Unit::find()->select(['unit_name'])->where(['id'=>$instrval['element_unit']])->one()->unit_name;
						} else {
							$instrval['element_unit'] = '';
						}
						$unitValues[$instrval['form_builder_id']] = $instrval['element_unit'];
					}
				}
    			
    			$trackData['task_instructions']['formValues']['lastversion']=$formValues;
    			$trackData['task_instructions']['unitValues']['lastversion']=$unitValues;
    		}
    		$Instrcution_data = TaskInstructNotes::find()->joinWith(['modifiedUser'],false)->where('servicetask_id ='.$servietask_id.' AND task_id ='.$task_id)->select(['tbl_tasks_units_notes.notes','tbl_tasks_units_notes.id','tbl_tasks_units_notes.modified_by','tbl_tasks_units_notes.modified'])->one();
    		if(!empty($Instrcution_data)){
	    		$trackData['task_instructions']['notes'] = $Instrcution_data->notes;
	    		$trackData['task_instructions']['attachments'] = $Instrcution_data->instructionattachments;
	    		//$actLog_case = (new ActivityLog)->find()->where("activity_module_id ='".$Instrcution_data->id."'")->orderBy('id DESC')->one();
	    		//if (isset($actLog_case->id) && $actLog_case->id != 0) {
	    			$trackData['task_instructions']['user'] = $Instrcution_data->modifiedUser->usr_first_name . " " . $Instrcution_data->modifiedUser->usr_lastname;
	    			$trackData['task_instructions']['user_date'] =(new Options)->ConvertOneTzToAnotherTz($Instrcution_data->modified, 'UTC', $_SESSION['usrTZ']);
	    		//}
    		}
			//echo "<pre>",print_r($trackData),"</pre>";die;
    		
    		/* Todo */
    		if(($team_id!=0 && (new User)->checkAccess(5.05)) || ($case_id!=0 && (new User)->checkAccess(4.06))){
    				/* echo $taskunit_id;
    				echo "<br>";
    				echo $task_id; */
    				$trackData['tododata'] = TasksUnitsTodos::find()->select(['tbl_tasks_units_todos.id','tbl_tasks_units_todos.complete','tbl_tasks_units_todos.todo','tbl_tasks_units_todos.todo_cat_id','tbl_tasks_units_todos.assigned','tbl_tasks_units_todos.created_by','tbl_tasks_units_todos.modified',"concat(tbl_user.usr_first_name,' ',tbl_user.usr_lastname) as assigned_user"])->join('inner join','tbl_tasks_units','tbl_tasks_units.id=tbl_tasks_units_todos.tasks_unit_id')->join('left join','tbl_user','tbl_user.id=tbl_tasks_units_todos.assigned')->where(['tbl_tasks_units.task_id' => $task_id,'tasks_unit_id'=>$taskunit_id])->orderBy('tbl_tasks_units_todos.id desc')->all();
    				$trackData['todo_cat_list'] = ArrayHelper::map(Todocats::find()->select(['id',"concat(todo_cat,' - ',todo_desc) as cat_desc"])->where('remove=0')->all(),'id','cat_desc');
    		}
    		
    		/*billing*/
    		$service_info =Servicetask::findOne($servietask_id);
			if(isset($type) && $type=='team')
				$security_feature_id = '5.06';
			else
				$security_feature_id = '4.07';
    		if (($service_info->billable_item == 1 || $service_info->billable_item == 2 ) && (new User)->checkAccess($security_feature_id)) {	/* 84 */
    			$trackData['billing'] = TasksUnitsBilling::find()->joinWith('tasksUnits')->where(['tbl_tasks_units.task_id' => $task_id,'tasks_unit_id'=>$taskunit_id])->all();
    		}
    		$trackData['tasksUnitDatas'] = TasksUnitsData::find()->joinWith('tasksUnits')->where(['tbl_tasks_units.task_id' => $task_id,'tasks_unit_id'=>$taskunit_id])->all();
			$trackData['attachments'] = MyDocument::find()->where(['reference_id' => $taskunit_id,'origination'=>'Data Statistics'])->joinWith('user')->joinWith(['mydocumentsBlobs'])->all();
		}
    	return $trackData;
    }

	public function processTrackDataInstruction($servietask_id,$sort_order,$task_id,$case_id,$team_id,$taskunit_id,$options,$type=0){
    	$trackData = array();
    	$instruct_data = TaskInstruct::find()->select(['id','mediadisplay_by','instruct_version'])->where('isactive = 1 AND task_id='.$task_id)->one();
    	if($sort_order == 0 ){
    		if($instruct_data->mediadisplay_by == 1){ //M
                    $sql="SELECT tbl_task_instruct_evidence.evidence_id FROM tbl_task_instruct_evidence LEFT JOIN tbl_task_instruct TaskInstruct ON tbl_task_instruct_evidence.task_instruct_id = TaskInstruct.id WHERE TaskInstruct.isactive=1 AND tbl_task_instruct_evidence.prod_id = 0 AND TaskInstruct.task_id=".$task_id;
    		}if($instruct_data->mediadisplay_by == 2){ //PM
                    $sql="SELECT tbl_task_instruct_evidence.evidence_id FROM tbl_task_instruct_evidence LEFT JOIN tbl_task_instruct TaskInstruct ON tbl_task_instruct_evidence.task_instruct_id = TaskInstruct.id WHERE TaskInstruct.isactive=1 AND tbl_task_instruct_evidence.prod_id != 0 AND TaskInstruct.task_id=".$task_id;
    		}
    		$sql_content = "select tbl_task_instruct_evidence.evidence_contents_id FROM tbl_task_instruct_evidence 
			inner join tbl_task_instruct on tbl_task_instruct.id =tbl_task_instruct_evidence.task_instruct_id AND tbl_task_instruct.isactive=1
			where tbl_task_instruct.task_id=$task_id AND tbl_task_instruct_evidence.evidence_contents_id  > 0";
    		$trackData['media'] = Evidence::find()->select(['id','evid_type','evid_desc','quantity','contents_total_size','unit','contents_total_size_comp','comp_unit'])->where('id IN ('.$sql.')')->all();
    		$trackData['media_content'] =  EvidenceContents::find()->select(['id','evid_num_id','cust_id','data_size','data_copied_to'])->where('id IN ('.$sql_content.')')->all();
    	}else{
    		/*Instruction*/
    		$trackData['task_instructions']['formbuilder_data'] = (new FormBuilder)->getFromData($servietask_id,1,'ASC');
    		$forminstrval = FormInstructionValues::find()->select(['form_builder_id','element_value','element_unit'])->where(['task_instruct_id'=>$instruct_data->id])->all();
                $formValues = array();
                $unitValues = array();
                if(!empty($forminstrval)){
                    foreach($forminstrval as $instrval){
                        $formValues[$instrval['form_builder_id']] = $instrval['element_value'];
                        if($instrval['element_unit']!=0){
                                $instrval['element_unit'] = Unit::findOne($instrval['element_unit'])->unit_name;
                        } else {
                                $instrval['element_unit'] = '';
                        }
                        $unitValues[$instrval['form_builder_id']] = $instrval['element_unit'];
                    }
                }
    		$trackData['task_instructions']['formValues']['active']=$formValues;
    		$trackData['task_instructions']['unitValues']['active']=$unitValues;
    		$trackData['task_instructions']['instruct'] =$instruct_data;
    		$sql="SELECT task_instruct_servicetask_id FROM tbl_tasks_units WHERE id=".$taskunit_id;
    		$trackData['task_instructions']['instructServicetask'] = $this::find()->where('id IN ('.$sql.')')->one();
			if($instruct_data->instruct_version > 1){
    			$instruct_lastversion_data = TaskInstruct :: find()->select(['id','mediadisplay_by','instruct_version'])->where('instruct_version ='.($instruct_data->instruct_version - 1).' AND task_id='.$task_id)->one();
    			$forminstrval = FormInstructionValues::find()->select(['form_builder_id','element_value','element_unit'])->where(['task_instruct_id'=>$instruct_lastversion_data->id])->all();
				$formValues = array();
				$unitValues = array();
				if(!empty($forminstrval)){
					foreach($forminstrval as $instrval){
						$formValues[$instrval['form_builder_id']] = $instrval['element_value'];
						if($instrval['element_unit']!=0){
							$instrval['element_unit'] = Unit::find()->select(['unit_name'])->where(['id'=>$instrval['element_unit']])->one()->unit_name;
						} else {
							$instrval['element_unit'] = '';
						}
						$unitValues[$instrval['form_builder_id']] = $instrval['element_unit'];
					}
				}	
    			$trackData['task_instructions']['formValues']['lastversion']=$formValues;
    			$trackData['task_instructions']['unitValues']['lastversion']=$unitValues;
    		}
    		$Instrcution_data = TaskInstructNotes::find()->joinWith(['modifiedUser'],false)->where('servicetask_id ='.$servietask_id.' AND task_id ='.$task_id)->select(['tbl_tasks_units_notes.notes','tbl_tasks_units_notes.id','tbl_tasks_units_notes.modified_by','tbl_tasks_units_notes.modified'])->one();
    		if(!empty($Instrcution_data)){
	    		$trackData['task_instructions']['notes'] = $Instrcution_data->notes;
	    		$trackData['task_instructions']['attachments'] = $Instrcution_data->instructionattachments;
	    		$trackData['task_instructions']['user'] = $Instrcution_data->modifiedUser->usr_first_name . " " . $Instrcution_data->modifiedUser->usr_lastname;
	    		$trackData['task_instructions']['user_date'] =(new Options)->ConvertOneTzToAnotherTz($Instrcution_data->modified, 'UTC', $_SESSION['usrTZ']);
	    	}
			$trackData['attachments'] = MyDocument::find()->where(['reference_id' => $taskunit_id,'origination'=>'Data Statistics'])->joinWith('user')->joinWith(['mydocumentsBlobs'])->all();
		}
    	return $trackData;
    }
    
    
    public function processTrackDatapopup($servietask_id,$sort_order,$task_id,$case_id,$team_id,$taskunit_id,$options){
    	$trackData = array();
    	$instruct_data = TaskInstruct :: find()->select(['id','mediadisplay_by','instruct_version'])->where('isactive = 1 AND task_id='.$task_id)->one();
    	if($sort_order == 0 ){
    		if($instruct_data->mediadisplay_by == 1){ //M
    			$sql="SELECT tbl_task_instruct_evidence.evidence_id FROM tbl_task_instruct_evidence LEFT JOIN tbl_task_instruct TaskInstruct ON tbl_task_instruct_evidence.task_instruct_id = TaskInstruct.id WHERE TaskInstruct.isactive=1 AND tbl_task_instruct_evidence.prod_id = 0 AND TaskInstruct.task_id=".$task_id;
    		}if($instruct_data->mediadisplay_by == 2){ //PM
    			$sql="SELECT tbl_task_instruct_evidence.evidence_id FROM tbl_task_instruct_evidence LEFT JOIN tbl_task_instruct TaskInstruct ON tbl_task_instruct_evidence.task_instruct_id = TaskInstruct.id WHERE TaskInstruct.isactive=1 AND tbl_task_instruct_evidence.prod_id != 0 AND TaskInstruct.task_id=".$task_id;
    		}
    		$sql_content = "select tbl_task_instruct_evidence.evidence_contents_id FROM tbl_task_instruct_evidence 
			inner join tbl_task_instruct on tbl_task_instruct.id =tbl_task_instruct_evidence.task_instruct_id AND tbl_task_instruct.isactive=1
			where tbl_task_instruct_evidence.task_id=$task_id AND tbl_task_instruct_evidence.evidence_contents_id  > 0";
    		$trackData['media'] = Evidence::find()->select(['id','evid_type','evid_desc','quantity','contents_total_size','unit','contents_total_size_comp','comp_unit'])->where('id IN ('.$sql.')')->all();
    		$trackData['media_content'] =  EvidenceContents::find()->select(['id','evid_num_id','cust_id','data_size','data_copied_to'])->where('id IN ('.$sql_content.')')->all();
    	}else{
    		/*Instruction*/
    		$trackData['task_instructions']['formbuilder_data'] = (new FormBuilder)->getFromData($servietask_id,1,'ASC');
    		//$trackData['task_instructions']['formValues']['active']=ArrayHelper::map(FormInstructionValues::find()->select(['form_builder_id','element_value'])->where(['task_instruct_id'=>$instruct_data->id])->all(),'form_builder_id','element_value');
    		
    		$forminstrval = FormInstructionValues::find()->select(['form_builder_id','element_value','element_unit'])->where(['task_instruct_id'=>$instruct_data->id])->all();
			$formValues = array();
			$unitValues = array();
			if(!empty($forminstrval)){
				foreach($forminstrval as $instrval){
					$formValues[$instrval['form_builder_id']] = $instrval['element_value'];
					if($instrval['element_unit']!=0){
						$instrval['element_unit'] = Unit::findOne($instrval['element_unit'])->unit_name;
					} else {
						$instrval['element_unit'] = '';
					}
					$unitValues[$instrval['form_builder_id']] = $instrval['element_unit'];
					
				}
			}
    		
    		$trackData['task_instructions']['formValues']['active']=$formValues;
    		$trackData['task_instructions']['unitValues']['active']=$unitValues;
    		
    		$trackData['task_instructions']['instruct'] =$instruct_data;
    		$sql="SELECT task_instruct_servicetask_id FROM tbl_tasks_units WHERE id=".$taskunit_id;
    		$trackData['task_instructions']['instructServicetask'] = $this::find()->where('id IN ('.$sql.')')->one(); 
    		if($instruct_data->instruct_version > 1){
    			$instruct_lastversion_data = TaskInstruct :: find()->select(['id','mediadisplay_by','instruct_version'])->where('instruct_version ='.($instruct_data->instruct_version - 1).' AND task_id='.$task_id)->one();
					//$trackData['task_instructions']['formValues']['lastversion']=ArrayHelper::map(FormInstructionValues::find()->select(['form_builder_id','element_value'])->where(['task_instruct_id'=>$instruct_lastversion_data->id])->all(),'form_builder_id','element_value');
				$forminstrval = FormInstructionValues::find()->select(['form_builder_id','element_value','element_unit'])->where(['task_instruct_id'=>$instruct_lastversion_data->id])->all();
				$formValues = array();
				$unitValues = array();
				if(!empty($forminstrval)){
					foreach($forminstrval as $instrval){
						$formValues[$instrval['form_builder_id']] = $instrval['element_value'];
						if($instrval['element_unit']!=0){
							$instrval['element_unit'] = Unit::find()->select(['unit_name'])->where(['id'=>$instrval['element_unit']])->one()->unit_name;
						} else {
							$instrval['element_unit'] = '';
						}
						$unitValues[$instrval['form_builder_id']] = $instrval['element_unit'];
					}
				}
    			
    			$trackData['task_instructions']['formValues']['lastversion']=$formValues;
    			$trackData['task_instructions']['unitValues']['lastversion']=$unitValues;	
    		}
    		$Instrcution_data = TaskInstructNotes::find()->joinWith(['modifiedUser'],false)->where('servicetask_id ='.$servietask_id.' AND task_id ='.$task_id)->select(['tbl_tasks_units_notes.notes','tbl_tasks_units_notes.id','tbl_tasks_units_notes.modified_by','tbl_tasks_units_notes.modified'])->one();
			if(!empty($Instrcution_data)){
	    		$trackData['task_instructions']['notes'] = $Instrcution_data->notes;
	    		$trackData['task_instructions']['attachments'] = $Instrcution_data->instructionattachments;
	    		//$actLog_case = (new ActivityLog)->find()->where("activity_module_id =".$Instrcution_data->id)->orderBy('id DESC')->one();
	    		//if (isset($actLog_case->id) && $actLog_case->id != 0) {
	    			$trackData['task_instructions']['user'] = $Instrcution_data->modifiedUser->usr_first_name . " " . $Instrcution_data->modifiedUser->usr_lastname;
	    			$trackData['task_instructions']['user_date'] =(new Options)->ConvertOneTzToAnotherTz($Instrcution_data->modified, 'UTC', $_SESSION['usrTZ']);
	    		//}
    		}
    		
    		
    	}
    	return $trackData;
    }

    
    
    
    public function getProjectMedias($task_id){
    	$trackData=array();
    	$instruct_data = TaskInstruct :: find()->select(['id','mediadisplay_by','instruct_version'])->where('isactive = 1 AND task_id='.$task_id)->one();
    	if($instruct_data->mediadisplay_by == 1) { //M
    		$sql="SELECT tbl_task_instruct_evidence.evidence_id FROM tbl_task_instruct_evidence LEFT JOIN tbl_task_instruct TaskInstruct ON tbl_task_instruct_evidence.task_instruct_id = TaskInstruct.id WHERE TaskInstruct.isactive=1 AND tbl_task_instruct_evidence.prod_id = 0 AND TaskInstruct.task_id=".$task_id;
    	} if($instruct_data->mediadisplay_by == 2) { //PM
    		$sql="SELECT tbl_task_instruct_evidence.evidence_id FROM tbl_task_instruct_evidence LEFT JOIN tbl_task_instruct TaskInstruct ON tbl_task_instruct_evidence.task_instruct_id = TaskInstruct.id WHERE TaskInstruct.isactive=1 AND tbl_task_instruct_evidence.prod_id != 0 AND TaskInstruct.task_id=".$task_id;
    	}
    	$sql_content = "select tbl_task_instruct_evidence.evidence_contents_id FROM tbl_task_instruct_evidence
		    	inner join tbl_task_instruct on tbl_task_instruct.id =tbl_task_instruct_evidence.task_instruct_id AND tbl_task_instruct.isactive=1
		    	where tbl_task_instruct.task_id=$task_id AND tbl_task_instruct_evidence.evidence_contents_id  > 0";
    	
    	$trackData['media'] = Evidence::find()->select(['id','evid_type','evid_desc','quantity','contents_total_size','unit','contents_total_size_comp','comp_unit'])->where('id IN ('.$sql.')')->all();
    	$trackData['media_content'] = EvidenceContents::find()->select(['id','evid_num_id','cust_id','data_size','data_copied_to'])->where('id IN ('.$sql_content.')')->all();
    	if(!empty($trackData['media'])) {
    		foreach ($trackData['media'] as $media) {
			$prod_id_sql="SELECT prod_id FROM tbl_task_instruct_evidence INNER join tbl_task_instruct on tbl_task_instruct.id=tbl_task_instruct_evidence.task_instruct_id where tbl_task_instruct.isactive=1 and tbl_task_instruct.task_id=".$task_id;
    			$trackData['evidprodbates'][$media->id] = EvidenceProductionBates::find()->where('prod_media_id IN (SELECT id FROM tbl_evidence_production_media WHERE evid_id  = '.$media->id.' AND prod_id IN ('.$prod_id_sql.')) AND task_id = '.$task_id)->one();
    		}
    	}
    	return $trackData;
    }
    
    public function getSearchCaseMedias($case_id,$limit="",$offset="",$attach_media="",$attach_media_content="",$search_media_id="",$search_media_type=""){
    	$trackData=array();
    	$client_case_sql="SELECT evid_num_id FROM tbl_client_case_evidence WHERE client_case_id =".$case_id." GROUP BY tbl_client_case_evidence.evid_num_id ";
    	$where=""; $where1="";
    	if(isset($attach_media_content) && trim($attach_media_content)!=""){
    		$where1.=" AND tbl_evidence_contents.id NOT IN (".$attach_media_content.")";
    	}
    	if($search_media_id!=""){
    		$client_case_sql="SELECT evid_num_id FROM tbl_client_case_evidence WHERE client_case_id =".$case_id." AND evid_num_id = ".$search_media_id."   GROUP BY tbl_client_case_evidence.evid_num_id";
    	}
    	if($search_media_type!=""){
    		$where  .=" AND evid_type IN (select id from tbl_evidence_type where evidence_name = '".$search_media_type."')";
    		$where1	.=" AND tbl_evidence.evid_type IN(select id from  tbl_evidence_type where evidence_name = '".$search_media_type."')";	
    	}
    	if($limit!="" && ($offset!=""||$offset==0)){
    		$trackData['media_content'] =  EvidenceContents::find()->joinWith(['evidence'])->select(['tbl_evidence_contents.id','evid_num_id','cust_id','data_size','data_copied_to','tbl_evidence_contents.unit','data_type'])->where('evid_num_id IN ('.$client_case_sql.') '.$where1)->orderBy('tbl_evidence_contents.created DESC')->offset($offset)->limit($limit)->all();
    	}else{
    		$trackData['media_content'] =  EvidenceContents::find()->joinWith(['evidence'])->select(['tbl_evidence_contents.id','evid_num_id','cust_id','data_size','data_copied_to','tbl_evidence_contents.unit','data_type'])->where('evid_num_id IN ('.$client_case_sql.') '.$where1)->orderBy('tbl_evidence_contents.created DESC')->all();
    	}
    	
    	if(isset($attach_media) && trim($attach_media)!=""){
    		$attach_media_arr=explode(",",$attach_media);
    		if(!empty($trackData['media_content'])){
    			foreach ($trackData['media_content'] as $media_content){
    				if(($key = array_search($media_content->evid_num_id, $attach_media_arr)) !== false) {
    					unset($attach_media_arr[$key]);
    				}
    			}
    		}
    		if(!empty($attach_media_arr)){
    			$where.=" AND id NOT IN (".implode(",",$attach_media_arr).")";
    		}
    	}
    	if($limit!="" && ($offset!=""||$offset==0)){
    		$trackData['media'] = Evidence::find()->select(['id','evid_type','evid_desc','quantity','contents_total_size','unit','contents_total_size_comp','comp_unit'])->where('id IN ('.$client_case_sql.') '.$where)->andWhere('status NOT IN(3,5)')->orderBy('created DESC')->offset($offset)->limit($limit)->all();
    	}else{
    		$trackData['media'] = Evidence::find()->select(['id','evid_type','evid_desc','quantity','contents_total_size','unit','contents_total_size_comp','comp_unit'])->where('id IN ('.$client_case_sql.') '.$where)->andWhere('status NOT IN(3,5)')->orderBy('created DESC')->all();
    	}
    	if(!empty($trackData['media'])){
    		foreach ($trackData['media'] as $media){
    			$trackData['evidprodbates'][$media->id] = EvidenceProductionBates::find()->where('prod_media_id='.$media->id)->one();
    		}
    	}
    	return $trackData;
    }
    public function getCaseMedias($case_id,$limit="",$offset="",$attach_media="",$attach_media_content=""){
    	$trackData=array();
    	$client_case_sql="SELECT evid_num_id FROM tbl_client_case_evidence WHERE client_case_id =".$case_id." GROUP BY tbl_client_case_evidence.evid_num_id";
    	$where="";
    	$where1="";
    	if(isset($attach_media_content) && trim($attach_media_content)!=""){
    		$where1=" AND id NOT IN (".$attach_media_content.")";
    	}
    	if($limit!="" && ($offset!=""||$offset==0)){
    		$trackData['media_content'] =  EvidenceContents::find()->select(['id','evid_num_id','cust_id','data_size','data_copied_to','unit','data_type'])->where('evid_num_id IN ('.$client_case_sql.') '.$where1)->orderBy('created DESC')->offset($offset)->limit($limit)->all();
    	}else{
    		$trackData['media_content'] =  EvidenceContents::find()->select(['id','evid_num_id','cust_id','data_size','data_copied_to','unit','data_type'])->where('evid_num_id IN ('.$client_case_sql.') '.$where1)->orderBy('created DESC')->all();
    	}
    	
    	if(isset($attach_media) && trim($attach_media)!=""){
    		$attach_media_arr=explode(",",$attach_media);
    		if(!empty($trackData['media_content'])){
    			foreach ($trackData['media_content'] as $media_content){
    				if(($key = array_search($media_content->evid_num_id, $attach_media_arr)) !== false) {
					    unset($attach_media_arr[$key]);
					}
    			}
    		}
    		if(!empty($attach_media_arr)){
    			$where=" AND id NOT IN (".implode(",",$attach_media_arr).")";
    		}
    	}
    	if($limit!="" && ($offset!=""||$offset==0)){
    		$trackData['media'] = Evidence::find()->select(['id','evid_type','evid_desc','quantity','contents_total_size','unit','contents_total_size_comp','comp_unit'])->where('id IN ('.$client_case_sql.') '.$where)->andWhere('status NOT IN(3,5)')->orderBy('created DESC')->offset($offset)->limit($limit)->all();
    	}else{
    		$trackData['media'] = Evidence::find()->select(['id','evid_type','evid_desc','quantity','contents_total_size','unit','contents_total_size_comp','comp_unit'])->where('id IN ('.$client_case_sql.') '.$where)->andWhere('status NOT IN(3,5)')->orderBy('created DESC')->all();
    	}
    	if(!empty($trackData['media'])){
    		foreach ($trackData['media'] as $media){
    			$trackData['evidprodbates'][$media->id] = EvidenceProductionBates::find()->where('prod_media_id='.$media->id)->one();
    		}
    	}
    	return $trackData;
    }
    
    
    public function getSelectedMedias($case_id,$attach_media,$attach_media_content){
    	$trackData=array();
    	$media_content_id="0";
    	$where1="";
    	if(isset($attach_media_content) && trim($attach_media_content)!=""){
    		$where1=" AND id IN (".$attach_media_content.")";
    	}
    	$client_case_sql="SELECT evid_num_id FROM tbl_client_case_evidence WHERE client_case_id =".$case_id." GROUP BY tbl_client_case_evidence.evid_num_id ";
    	$trackData['media'] = Evidence::find()->select(['id','evid_type','evid_desc','quantity','contents_total_size','unit','contents_total_size_comp','comp_unit'])->where('id IN ('.$attach_media.')')->orderBy('created DESC')->all();
    	$trackData['media_content'] =  EvidenceContents::find()->select(['id','evid_num_id','cust_id','data_size','data_copied_to','unit','data_type'])->where('evid_num_id IN ('.$attach_media.') '.$where1 )->orderBy('created DESC')->all();
    	if(!empty($trackData['media'])){
    		foreach ($trackData['media'] as $media){
    			$trackData['evidprodbates'][$media->id] = EvidenceProductionBates::find()->where('prod_media_id='.$media->id)->one();
    		}
    	}
    	return $trackData;
    }
    
    /**
     * Method to check is media use in Project Or Not 
     * */
    public function checkHasMedia($task_id,$case_id,$team_id,$options){
    	$sql="SELECT tbl_task_instruct_evidence.evidence_id FROM tbl_task_instruct_evidence LEFT JOIN tbl_task_instruct TaskInstruct ON tbl_task_instruct_evidence.task_instruct_id = TaskInstruct.id WHERE TaskInstruct.isactive=1 AND tbl_task_instruct_evidence.task_id=".$task_id;
    	return Evidence::find()->select(['id','evid_type','evid_desc','quantity','contents_total_size','unit','contents_total_size_comp','comp_unit'])->where('id IN ('.$sql.')')->all();
    }
    
    /**
     * Show Column action based on condition in Track Grid
     **/
    public function getColumn($model,$task_id,$case_id,$team_id,$team_loc,$columnName='')
    {
    	if(!isset($_SESSION[$model['teamId'].'_team_name'])){
				$_SESSION[$model['teamId'].'_team_name'] = serialize(Team::find()->select('team_name')->where('id = '.$model['teamId'])->one()->team_name);
		}
		$team_name = unserialize($_SESSION[$model['teamId'].'_team_name']);

		if(!isset($_SESSION[$model['teamId'].'_'.$model['team_loc'].'_check_access'])){
				$_SESSION[$model['teamId'].'_'.$model['team_loc'].'_check_access'] =serialize((new ProjectSecurity)->checkTeamAccess($model['teamId'],$model['team_loc']));
		}
    	$checkAccess = unserialize($_SESSION[$model['teamId'].'_'.$model['team_loc'].'_check_access']);
		//$modelTaksInstruction->checkDeniedService($task_id,$case_id,$team_id,$team_loc,$model,$belongtocurr_team);
    	if($columnName=='todo'){
    		//$totaltodo = TasksUnitsTodos::find()->select(['tbl_tasks_units_todos.id'])->where(['task_id' => $task_id,'tasks_unit_id'=>$model["taskunit_id"]])->count();
			$taskunit_id=$model['taskunit_id'];
			$totaltodo=$outstaingtodo=$completedtodo=0;
			if($model['hastodo'] > 0) {
    		/*$sql="SELECT COUNT(*) as todocnt FROM tbl_tasks_units_todos INNER JOIN tbl_tasks_units ON tbl_tasks_units_todos.tasks_unit_id = tbl_tasks_units.id WHERE (tbl_tasks_units_todos.tasks_unit_id=$taskunit_id) AND (tbl_tasks_units.task_id=$task_id)
			UNION ALL 
			SELECT COUNT(*) as todocnt FROM tbl_tasks_units_todos INNER JOIN tbl_tasks_units ON tbl_tasks_units_todos.tasks_unit_id = tbl_tasks_units.id WHERE (tasks_unit_id=$taskunit_id) AND (tbl_tasks_units.task_id=$task_id) AND (complete=0) 
			UNION ALL 
			SELECT COUNT(*) as todocnt FROM tbl_tasks_units_todos INNER JOIN tbl_tasks_units ON tbl_tasks_units_todos.tasks_unit_id = tbl_tasks_units.id WHERE (tasks_unit_id=$taskunit_id) AND (tbl_tasks_units.task_id=$task_id) AND (complete=1)";
			$data=Yii::$app->db->createCommand($sql)->queryAll();
			//echo "<pre>",print_r($data[0]['todocnt']),"</prE>";die;
    		$totaltodo = $data[0]['todocnt'];
			//TasksUnitsTodos::find()->select(['tbl_tasks_units_todos.id'])->innerJoinWith('taskUnit')->where(['tbl_tasks_units_todos.tasks_unit_id'=>$model["taskunit_id"],'tbl_tasks_units.task_id'=>$task_id])->count();
    		
    		$outstaingtodo = $data[1]['todocnt'];
			//TasksUnitsTodos::find()->select(['tbl_tasks_units_todos.id'])->innerJoinWith('taskUnit')->where(['tasks_unit_id'=>$model["taskunit_id"],'tbl_tasks_units.task_i'=>$task_id,'complete'=>0])->count();
				
    		$completedtodo = $data[2]['todocnt'];
			//TasksUnitsTodos::find()->select(['tbl_tasks_units_todos.id'])->innerJoinWith('taskUnit')->where(['tasks_unit_id'=>$model["taskunit_id"],'tbl_tasks_units.task_id'=>$task_id,'complete'=>1])->count();
			*/}	
    		if($outstaingtodo > 0){
				if(((new User)->checkAccess(4.06) && $case_id != 0) || ((new User)->checkAccess(5.05) && $team_id != 0 && $team_loc != 0)){
					return Html::a('<em class="fa fa-bell text-danger" title="Add Todo"></em><span class="sr-only">Add Todo</span>',null,['title'=>'outstanding todos = '.$outstaingtodo,'href'=>'javascript:AddTodo('.$model["servicetask_id"].','.$task_id.','.$model["team_loc"].','.$model["taskunit_id"].');','class'=>'track-icon']);
				}
    		}
    		if($totaltodo == $completedtodo && $completedtodo > 0) {
				if(((new User)->checkAccess(4.06) && $case_id != 0) || ((new User)->checkAccess(5.05) && $team_id != 0 && $team_loc != 0)){
					return Html::a('<em class="fa fa-bell text-danger" title="Add Todo"></em><span class="sr-only">Add Todo</span>',null,['title'=>'Completed todos = '.$completedtodo,'href'=>'javascript:AddTodo('.$model["servicetask_id"].','.$task_id.','.$model["team_loc"].','.$model["taskunit_id"].');','class'=>'track-icon']);
				}
    		}
    		if( ($team_id!=0 && $team_loc!=0 && (new User)->checkAccess(5.05)) || ($case_id!=0 && (new User)->checkAccess(4.06)) ) {
				if(((new User)->checkAccess(4.06) && $case_id != 0) || ((new User)->checkAccess(5.05) && $team_id != 0 && $team_loc != 0)){
    				return Html::a('<em class="fa fa-bell"  title="Add Todo"></em><span class="sr-only">Add Todo</span>',null,['href'=>'javascript:AddTodo('.$model["servicetask_id"].','.$task_id.','.$model["team_loc"].','.$model["taskunit_id"].');','title'=>'Add ToDo','class'=>'track-icon']);
    			}	
    		}
    		return;
    	}
    	if($columnName=='task_status') {
    		if(!isset($_SESSION[$task_id.'_task_info'])) {
				$_SESSION[$task_id.'_task_info'] = serialize(Tasks::findOne($task_id));
			}
			$task_info = unserialize($_SESSION[$task_id.'_task_info']);
			$status_arr =array(1=>"Start",2=>"Pause",3=>"Hold",4=>"Complete");
    		$status_arr_class =array(0=>"text-primary",1=>"text-success",2=>"text-info",3=>"text-gray",4=>"text-dark");
    		$unit_status = "";
    		$task_status_a="";
			/*if(!isset($_SESSION[$model['taskunit_id'].'_taskunit'])) {
				$_SESSION[$model['taskunit_id'].'_taskunit'] = serialize(TasksUnits::findOne($model['taskunit_id']));
			}*/
    		//$taskunit = $taskunit = TasksUnits::findOne($model['taskunit_id']);
			$taskunitunit_status=$model['unit_status'];
			//unserialize($_SESSION[$model['taskunit_id'].'_taskunit']);
    		$has_hold_access=false;
    		$has_pause_access=false;
    		if(((new User)->checkAccess(4.0501) && $case_id != 0) || ((new User)->checkAccess(5.0401) && $team_id != 0 && $team_loc != 0)){
				$has_pause_access=true;
			}
			if(((new User)->checkAccess(4.0502) && $case_id != 0) || ((new User)->checkAccess(5.0402) && $team_id != 0 && $team_loc != 0)){
				$has_hold_access=true;
			}
    		if ($taskunitunit_status == 0) {
    			if ($task_info->task_status == 3) {
    				$unit_status= 3;
    				$task_status_a = Html::a("<em title='On Hold' class='fa fa-clock-o text-gray'></em><span class='sr-only'>On Hold</span>",null, ['href'=>'javascript:void(0);',"title" => "On Hold",'class'=>"track-icon",'data-toggle'=>"dropdown"]);
					
    			} else {
    				$unit_status =0;
    				$task_status_a = Html::a("<em title='Not Started' class='fa fa-clock-o text-primary'></em><span class='sr-only'>Not Started</span>",null, ['href'=>'javascript:void(0);',"title" => "Not Started",'class'=>"track-icon",'data-toggle'=>"dropdown"]);
    			}
    		} else if ($taskunitunit_status == 1) {
    			$unit_status =1;
    			$task_status_a = Html::a("<em title='Started' class='fa fa-clock-o text-success'></em><span class='sr-only'>Started</span>",null, ['href'=>'javascript:void(0);',"title" => "Started",'class'=>"track-icon",'data-toggle'=>"dropdown"]);
    		} else if ($taskunitunit_status == 2) {
    			$unit_status =2;
    			$task_status_a = Html::a("<em title='On Pause' class='fa fa-clock-o text-info'></em><span class='sr-only'>On Pause</span>",null, ['href'=>'javascript:void(0);',"title" => "On Pause",'class'=>"track-icon",'data-toggle'=>"dropdown"]);
			} else if ($taskunitunit_status == 3) {
    			$unit_status =3;
    			$task_status_a = Html::a("<em title='On Hold' class='fa fa-clock-o text-gray'></em><span class='sr-only'>On Hold</span>",null, ['href'=>'javascript:void(0);',"title" => "On Hold",'class'=>"track-icon",'data-toggle'=>"dropdown"]);
			} else if ($taskunitunit_status == 4) {
    			$unit_status =4;
    			$task_status_a = Html::a("<em title='Completed' class='fa fa-clock-o text-dark'></em><span class='sr-only'>Completed</span>",null, ['href'=>'javascript:void(0);',"title" => "Completed",'class'=>"track-icon",'data-toggle'=>"dropdown"]);
    		}
    		
    		if(((new User)->checkAccess(4.05) && $case_id != 0) || ((new User)->checkAccess(5.04) && $team_id != 0 && $team_loc != 0)){
				$return_data ='<div class="dropdown">'.$task_status_a.'<ul class="dropdown-menu">';
				foreach ($status_arr as $staus=>$name){ if($staus == $unit_status){ continue;}
					if($unit_status == 0){ if(!in_array($staus,array(1,3))) {continue;} }
					if($unit_status == 1){ if(!in_array($staus,array(2,3,4))) {continue;} }
					if($unit_status == 2){ if(!in_array($staus,array(1))) {continue;} }
					if($unit_status == 3){ if(!in_array($staus,array(1,4))) {continue;} }
					if($unit_status == 4){ if(!in_array($staus,array(1,3))) {continue;} }
					if($has_pause_access==false && $staus==3){
						continue;
					}
					if($has_hold_access==false && $staus==2){
						continue;
					}								
					$onclick = "javascript:changeStaus(".$model['servicetask_id'].",".$task_id.",".$model['taskunit_id'].",".$staus.");";
					if(!$checkAccess) {
						$onclick = "javascript:alert('This action is available only to $team_name Team Members.');";
					}
					$return_data .='<li>'.Html::a("<em title='".$name." Task' class='fa fa-clock-o ".$status_arr_class[$staus]."'></em> ".$name,null, ['href'=>$onclick,"title" => $name.' Task','class'=>'track-icon']).'</li>';
				}
				$return_data .='</ul></div>';
    		}else{
				$return_data = $task_status_a;
			}
    		return $return_data;
    	}
		if($columnName=='unassign'){
    		/*$taskunit = TasksUnits::findOne($model['taskunit_id']);*/
			/*if(!isset($_SESSION[$model['taskunit_id'].'_taskunit'])) {
				$_SESSION[$model['taskunit_id'].'_taskunit'] = serialize(TasksUnits::findOne($model['taskunit_id']));
			}
    		$taskunit = unserialize($_SESSION[$model['taskunit_id'].'_taskunit']);*/
			$unit_assigned_to=$model['unit_assigned_to'];
    		if($unit_assigned_to!=0){
    			$onclick = "javascript:UnassignTask(".$model['servicetask_id'].",".$task_id.",".$model['team_loc'].",".$model['taskunit_id'].");";
    			$title   = "Transition Task";
    			$class   = "text-danger";
    		}if($unit_assigned_to==0){
    			$onclick = "javascript:AssignTask(".$model['servicetask_id'].",".$task_id.",".$case_id.",".$model['teamId'].",".$model['team_loc'].",".$model['taskunit_id'].");";
    			$title   = "Assign Task";
    			$class   = "text-primary";
    		}
    		if(!$checkAccess){
    			$onclick = "javascript:alert('This action is available only to $team_name Team Members.');";
    		}
    		/*echo 'HNL'.(new User)->checkAccess(5.031);
    		exit;*/
    		if(((new User)->checkAccess(4.04) && $case_id != 0 && $unit_assigned_to==0) || ((new User)->checkAccess(5.03) && $team_id != 0 && $team_loc != 0  && $unit_assigned_to==0)){
				return Html::a('<em class="fa fa-thumb-tack '.$class.'" title="'.$title.'"></em><span class="sr-only">'.$title.'</span>',null,['href'=>$onclick,'title'=>$title,'class'=>'track-icon']);
			} else if(((new User)->checkAccess(4.041) && $case_id != 0 && $unit_assigned_to!=0) || ((new User)->checkAccess(5.031) && $team_id != 0 && $team_loc != 0  && $unit_assigned_to!=0)){
				//return Html::a('<em class="fa fa-thumb-tack '.$class.'"></em>',null,['href'=>$onclick,'title'=>$title,'class'=>'track-icon']);
				return "";
			}else{
				if($unit_assigned_to==0)
					return '<em class="fa fa-thumb-tack text-gray" title="Unassign"></em>';
			}
			
    	}
    	if($columnName=='assign'){
			/* Additional Track Icon task By HNL */
			//$taskunit = TasksUnits::findOne($model['taskunit_id']);
			/*if(!isset($_SESSION[$model['taskunit_id'].'_taskunit'])) {
				$_SESSION[$model['taskunit_id'].'_taskunit'] = serialize();
			}*/
    		//unserialize($_SESSION[$model['taskunit_id'].'_taskunit']);
			$unit_assigned_to=$model['unit_assigned_to'];
			if($unit_assigned_to!=0){
    			$onclick_assign = "javascript:TransitTask(".$task_id.",".$case_id.",".$model['teamId'].",".$model['team_loc'].",".$model['servicetask_id'].",".$model['taskunit_id'].",'all');";
    			
    			//"javascript:UnassignTask(".$model['servicetask_id'].",".$task_id.",".$model['team_loc'].",".$model['taskunit_id'].");";
    			$title   = "Transition Task";
    			$class   = "text-danger";
    		
    		/*}if($taskunit->unit_assigned_to==0){
    			$onclick_assign = "javascript:AssignTask(".$model['servicetask_id'].",".$task_id.",".$case_id.",".$model['teamId'].",".$model['team_loc'].",".$model['taskunit_id'].");";
    			$title   = "Assign Task";
    			$class   = "text-primary";
    		}*/
    		if(!$checkAccess){
    			$onclick_assign = "javascript:alert('This action is available only to $team_name Team Members.');";
    		}
    		$assign_change = "";
			if($case_id != 0 && !(new User)->checkAccess(4.041)){
				$onclick_assign = "javascript:TransitTask(".$task_id.",".$case_id.",".$model['teamId'].",".$model['team_loc'].",".$model['servicetask_id'].",".$model['taskunit_id'].",'assign');";
			}if($team_id != 0 && $team_loc != 0 && !(new User)->checkAccess(5.031)){
				$onclick_assign = "javascript:TransitTask(".$task_id.",".$case_id.",".$model['teamId'].",".$model['team_loc'].",".$model['servicetask_id'].",".$model['taskunit_id'].",'assign');";
			}
			if(!(new User)->checkAccess(4.04) && $case_id != 0) {
				$onclick_assign = "javascript:TransitTask(".$task_id.",".$case_id.",".$model['teamId'].",".$model['team_loc'].",".$model['servicetask_id'].",".$model['taskunit_id'].",'unassign');";
			}
			if(!(new User)->checkAccess(5.03) && $team_id != 0 && $team_loc != 0){
				$onclick_assign = "javascript:TransitTask(".$task_id.",".$case_id.",".$model['teamId'].",".$model['team_loc'].",".$model['servicetask_id'].",".$model['taskunit_id'].",'unassign');";
			}
    		if(((new User)->checkAccess(4.04) && $case_id != 0) || ((new User)->checkAccess(5.03) && $team_id != 0 && $team_loc != 0)){
				$assign_change = Html::a('<em class="fa fa-thumb-tack '.$class.'" title="'.$title.'"></em><span class="sr-only">'.$title.'</span>',null,['href'=>$onclick_assign,'title'=>$title,'class'=>'track-icon']);
			}else if(((new User)->checkAccess(4.041) && $case_id != 0 && $unit_assigned_to!=0) || ((new User)->checkAccess(5.031) && $team_id != 0 && $team_loc != 0  && $unit_assigned_to!=0)){
				$assign_change = Html::a('<em class="fa fa-thumb-tack '.$class.'" title="'.$title.'"></em><span class="sr-only">'.$title.'</span>',null,['href'=>$onclick_assign,'title'=>$title,'class'=>'track-icon']);
			}
			
			/* End here */
    		$userAssigned=$model['assignuser'];
    		$onclick = "javascript:TransitTask(".$task_id.",".$case_id.",".$model['teamId'].",".$model['team_loc'].",".$model['servicetask_id'].",".$model['taskunit_id'].");";
    		$hover_assign_to = "";
			/*if($assign_change != ''){
				$transaction_log = TasksUnitsTransactionLog::find()->where("tasks_unit_id = {$model['taskunit_id']} AND transaction_type IN (5,6)");
				if($transaction_log->count() > 0){
				$transactionlog_data=$transaction_log->orderBy('id desc')->one();
					if($transactionlog_data->transaction_type == 5)
						$hover_assign_to = (new Options)->ConvertOneTzToAnotherTz($transactionlog_data->transaction_date, 'UTC', $_SESSION['usrTZ'])." Assign From ".$transactionlog_data->transactionUser->usr_first_name." ".$transactionlog_data->transactionUser->usr_lastname;
					else
						$hover_assign_to = (new Options)->ConvertOneTzToAnotherTz($transactionlog_data->transaction_date, 'UTC', $_SESSION['usrTZ'])." Transition From ".$transactionlog_data->transactionUser->usr_first_name." ".$transactionlog_data->transactionUser->usr_lastname;
				}
			}*/
    		if(!$checkAccess){
    			$onclick = "javascript:alert('This action is available only to $team_name Team Members.');";
    			$userAssigned="User";
    			//if (((new User)->checkAccess(4.0611) && $case_id != 0) || ((new User)->checkAccess(5.0611) && $team_id != 0)) { 
				//$userAssigned=$model['assignuser'];
				//}
    			
    		}
    		if($case_id != 0){
	    		if((new User)->checkAccess(4.04) || (new User)->checkAccess(4.041)){
					return $assign_change." <span title='{$hover_assign_to}'>".$userAssigned."</span>";
					//Html::a('<span title="Transition Task">'.$userAssigned.'</span>',null,['href'=>$onclick,'class'=>	'track-icon']);
				}
				else{
					return " <span title='{$hover_assign_to}'>".$userAssigned."</span>";;
				}
    		}else if(($team_id != 0 && $team_loc != 0)){
    			if((new User)->checkAccess(5.03) || (new User)->checkAccess(5.031)){
    				return $assign_change." <span title='{$hover_assign_to}'>".$userAssigned."</span>";
    				//Html::a('<span title="Transition Task">'.$userAssigned.'</span>',null,['href'=>$onclick,'class'=>'track-icon']);
    			}
    			else{
    				return " <span title='{$hover_assign_to}'>".$userAssigned."</span>";;
    			}
    		}
    		else{
					return " <span title='{$hover_assign_to}'>".$userAssigned."</span>";;
			}
			}
    	}
    	if($columnName == 'transferTask'){
    		$onclick = "javascript:TransferTask(".$task_id.",".$case_id.",".$model['teamId'].",".$model['team_loc'].",".$model['servicetask_id'].",".$model['taskunit_id'].");";
    		if(!$checkAccess){
    			$onclick = "javascript:alert('This action is available only to $team_name Team Members.');";
    		}
    		if(((new User)->checkAccess(4.051) && $case_id !=0) || ((new User)->checkAccess(5.041) && $team_id != 0 && $team_loc != 0)){
				//$sql="SELECT team_loc FROM tbl_servicetask_team_locs WHERE servicetask_id =".$model['servicetask_id']." AND team_loc NOT IN (". $model['team_loc'] .") ";
    			//$teamLocation=ArrayHelper::map(TeamlocationMaster::find()->select(['id','team_location_name'])->orderBy('id')->where('remove=0 and id IN ('.$sql.')')->all(), 'id','team_location_name');
				//if(!empty($teamLocation)){
				if($model['hasteamloc'] > 0) {	
					return Html::a('<em class="fa fa-map-marker text-primary" title="Transfer Task Location"></em><span class="sr-only">Transfer Task Location</span>',null,['href'=>$onclick,'title'=>'Transfer Task Location','class'=>'track-icon']);
				} else {
					return '<em class="fa fa-map-marker text-gray"  title="Transfer Task Location"></em>';
				}
			}
    	}
    	if($columnName == 'billing'){    
			$taskunit_id = $model["taskunit_id"];
			$outstaingbiiling=$invoicebiiling=$dataUnit=0;
			if($model['unit_assigned_to'] > 0 && $model['hasbilling'] > 0) {
			/*	
			$sql="SELECT COUNT(*) as billingcnt FROM tbl_tasks_units_billing INNER JOIN tbl_tasks_units ON tbl_tasks_units_billing.tasks_unit_id = tbl_tasks_units.id WHERE ((tasks_unit_id=$taskunit_id) AND (tbl_tasks_units.task_id=$task_id)) AND (invoiced = '' OR invoiced IS NULL OR invoiced = 2)
			UNION ALL
			SELECT COUNT(*) as billingcnt FROM tbl_tasks_units_billing INNER JOIN tbl_tasks_units ON tbl_tasks_units_billing.tasks_unit_id = tbl_tasks_units.id WHERE (tasks_unit_id=$taskunit_id) AND (tbl_tasks_units.task_id=$task_id) AND (invoiced=1)
			UNION ALL
			SELECT COUNT(*) as billingcnt FROM (SELECT form_builder_id FROM tbl_tasks_units_data INNER JOIN tbl_tasks_units ON tbl_tasks_units_data.tasks_unit_id = tbl_tasks_units.id WHERE (tasks_unit_id=$taskunit_id) AND (tbl_tasks_units.task_id=$task_id) GROUP BY form_builder_id) c
			";

			$billing_data=Yii::$app->db->createCommand($sql)->queryAll();

			///echo "<pre>",print_r($billing_data);die;


    		$outstaingbiiling = $billing_data[0]['billingcnt'];
			//TasksUnitsBilling::find()->select(['id'])->innerJoinWith('tasksUnits')->where(['tasks_unit_id'=>$model["taskunit_id"],'tbl_tasks_units.task_id'=>$task_id])->andWhere("invoiced = '' OR invoiced IS NULL OR invoiced = 2")->count();                
    		
    		$invoicebiiling = $billing_data[1]['billingcnt'];
			//TasksUnitsBilling::find()->select(['id'])->innerJoinWith('tasksUnits')->where(['tasks_unit_id'=>$model["taskunit_id"],'tbl_tasks_units.task_id'=>$task_id,'invoiced'=>1])->count();
    		
    		$dataUnit = $billing_data[2]['billingcnt'];
			//TasksUnitsData::find()->select(['form_builder_id'])->innerJoinWith('tasksUnits')->where(['tasks_unit_i'=>$model["taskunit_id"],'tbl_tasks_units.task_id'=>$task_id])->groupBy('form_builder_id')->count();
			*/}

			
    		
    		$onclick = "javascript:AddBilling(".$task_id.",".$case_id.",".$model['teamId'].",".$model['team_loc'].",".$model['servicetask_id'].",".$model['taskunit_id'].");";
    		
    		if(!$checkAccess){
    			$onclick = "javascript:alert('This action is available only to $team_name Team Members.');";
    		}
    		$class = "fa fa-star text-primary";
    		$title = "Enter Task Details";
    		if($dataUnit > 0 || $invoicebiiling > 0) {
    			// BY HNL $class="fa fa-star text-dark";
    			$class="fa fa-star text-danger";
    			$title = "Task Outcome Item = ".$dataUnit; 
    		} else if($outstaingbiiling > 0) {
    			$class="fa fa-star text-danger";
    			$title = "Billable Item = ".$outstaingbiiling;
    		}
			/*else{
				$dataUnit_attachments = MyDocument::find()->where(['reference_id' => $model["taskunit_id"],'origination'=>'Data Statistics'])->joinWith('user')->joinWith(['mydocumentsBlobs'])->count();
				if($dataUnit_attachments > 0){
					$class="fa fa-star text-danger";
    				$title = "Task Outcome Item Attachments = ".$dataUnit_attachments; 
				}
			}*/
    		
    		return Html::a('<em class="'.$class.'" title="'.$title.'"></em><span class="sr-only">'.$title.'</span>',null,['href'=>$onclick,'title'=>$title,'class'=>'track-icon']);
    		
    	}
    	
    }
    /*
     * get Active project's Workflow
     * */
    public function getProjectWorkflow($project_id){
    	if(is_array($project_id)){
    		$project_id=implode(",",$project_id);
    	}
    	$workflow_servicedata=array();
    	$sql="SELECT tbl_teamservice.teamid as teamId, tbl_teamservice.id AS teamservice_id, service_name, tbl_servicetask.id AS servicetask_id, tbl_servicetask.service_task, tbl_task_instruct_servicetask.team_loc, tbl_teamlocation_master.team_location_name
		FROM tbl_task_instruct_servicetask
		INNER JOIN tbl_task_instruct ON tbl_task_instruct.id = tbl_task_instruct_servicetask.task_instruct_id
		INNER JOIN tbl_teamservice ON tbl_teamservice.id = tbl_task_instruct_servicetask.teamservice_id
		INNER JOIN tbl_servicetask ON tbl_servicetask.id = tbl_task_instruct_servicetask.servicetask_id
		INNER JOIN tbl_teamlocation_master ON tbl_teamlocation_master.id = tbl_task_instruct_servicetask.team_loc
		WHERE tbl_task_instruct_servicetask.task_id IN (".$project_id.") AND tbl_task_instruct.isactive =1 ORDER BY tbl_task_instruct_servicetask.sort_order";
    	$servicesdata = Yii::$app->db->createCommand($sql)->queryAll();
    	foreach ($servicesdata as $k=>$mydata){
    		if($mydata['teamId']==1){
                    $mydata['team_loc']=0;
    		}
    		$exculdeservice=CaseXteam::find()->where(['client_case_id'=>$case_id,'teamid'=>$mydata['teamId'],'team_loc'=>$mydata['team_loc'],'teamservice_id'=>$mydata['teamservice_id']])->innerJoinWith('teamservice')->select(['id'])->count();
    		if($exculdeservice==0){
    			$workflow_servicedata[]=$mydata;
    		}
    	}
    	return $workflow_servicedata;
    }

	public function changeAttchment($task_id,$instruction_id){
	$prev_instruction_id="SELECT id FROM tbl_task_instruct WHERE task_id=$task_id and instruct_version=((select instruct_version from tbl_task_instruct WHERE id=$instruction_id and task_id=$task_id)-1)";
	$sql="SELECT servicetask_id,fname FROM (
		(SELECT fname,tbl_task_instruct_servicetask.servicetask_id FROM tbl_mydocument INNER JOIN tbl_task_instruct_servicetask on tbl_task_instruct_servicetask.id=tbl_mydocument.reference_id
		where origination='instruct' and tbl_task_instruct_servicetask.task_instruct_id=$instruction_id and tbl_task_instruct_servicetask.task_id=$task_id)
		UNION ALL 
		(SELECT fname,tbl_task_instruct_servicetask.servicetask_id FROM tbl_mydocument INNER JOIN tbl_task_instruct_servicetask on tbl_task_instruct_servicetask.id=tbl_mydocument.reference_id
		where origination='instruct' and tbl_task_instruct_servicetask.task_instruct_id IN ($prev_instruction_id) and tbl_task_instruct_servicetask.task_id=$task_id)
		) as a GROUP by servicetask_id,fname HAVING count(fname)=1";
		return ArrayHelper::map(Yii::$app->db->createCommand($sql)->queryAll(),'servicetask_id','servicetask_id');	
	}
    
    /**
     * Method to check is media use in Project Or Not
     * */
    public function showTrackExpandDetail(){
    	
    }
    
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getServicetask()
    {
        return $this->hasOne(Servicetask::className(), ['id' => 'servicetask_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTeam()
    {
        return $this->hasOne(Team::className(), ['id' => 'team_id']);
    }
    
    public function getTasks()
    {
        return $this->hasOne(Tasks::className(), ['id' => 'task_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTeamservice()
    {
        return $this->hasOne(Teamservice::className(), ['id' => 'teamservice_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTeamLoc()
    {
        return $this->hasOne(TeamlocationMaster::className(), ['id' => 'team_loc']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTaskInstruct()
    {
        return $this->hasOne(TaskInstruct::className(), ['id' => 'task_instruct_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTasksUnits()
    {
        return $this->hasMany(TasksUnits::className(), ['task_instruct_servicetask_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTasksUnitsDatas()
    {
        return $this->hasMany(TasksUnitsData::className(), ['task_instruct_servicetask_id' => 'id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInstructionAttachments()
    {
    	return $this->hasMany(Mydocument::className(), ['reference_id' => 'id'])->andOnCondition(['origination' => 'instruct']);
    }
    
    public function getTaskInstructServicetaskSla()
    {
     return $this->hasMany(TaskInstructServicetaskSla::className(), ['task_instruct_servicetask_id' => 'id']);
    }
}
