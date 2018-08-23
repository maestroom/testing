<?php

namespace app\models;

use Yii;
use kotchuprik\sortable\behaviors;
use yii\helpers\ArrayHelper;
use app\models\TeamlocationMaster;
use app\models\TeamserviceLocs;
use app\models\ActivityLog;
use app\models\TeamserviceSla;


/**
 * This is the model class for table "{{%teamservice}}".
 *
 * @property integer $id
 * @property integer $teamid
 * @property string $service_name
 * @property string $service_description
 * @property string $hastasks
 * @property integer $sort_order
 * @property string $created
 * @property integer $created_by
 * @property string $modified
 * @property integer $modified_by
 *
 * @property CaseXteam[] $caseXteams
 * @property Servicetask[] $servicetasks
 * @property TaskInstructServicetask[] $taskInstructServicetasks
 * @property TasksTeams[] $tasksTeams
 * @property Team $team
 * @property TeamserviceLocs[] $teamserviceLocs
 * @property TeamserviceSla[] $teamserviceSlas
 */
class Teamservice extends \yii\db\ActiveRecord
{
	public $team_location;
	public $orderAttribute ='sort_order';
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%teamservice}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['teamid', 'service_name'], 'required'],
        	[['team_location'], 'required','when'=>function($model){ return $model->teamid != 1;}, 'whenClient' => "function (attribute, value) {
		        return $('#teamservice-teamid').val() != 1;
		    }"],
            [['teamid', 'sort_order', 'created_by', 'modified_by','hastasks'], 'integer'],
            [['service_name', 'service_description'], 'string'],
            [['created', 'modified'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'teamid' => 'Teamid',
            'service_name' => 'Service Name',
            'service_description' => 'Service Description',
            'hastasks' => 'Tasks',
            'sort_order' => 'Sort Order',
            'created' => 'Created',
            'created_by' => 'Created By',
            'modified' => 'Modified',
            'modified_by' => 'Modified By',
        ];
    }
    /**
     * @sortable behaviors
     */
    public function behaviors()
    {
    	return [
    			'sortable' => [
    					'class' => \kotchuprik\sortable\behaviors\Sortable::className(),
    					'query' => self::find()->where(['teamid'=>$this->teamid]),
    					'orderAttribute'=>'sort_order',
    			],
    	];
    }
    /**
     * @inheritdoc
     */
    public function beforeInsert()
    {
    		$last = $this->find()->where(['teamid'=>$this->teamid])->orderBy([$this->orderAttribute => SORT_DESC])->limit(1)->one();
    		if ($last === null) {
    			$this->{$this->orderAttribute} = 1;
    		} else {
    			$this->{$this->orderAttribute} = $last->{$this->orderAttribute} + 1;
    		}
    }
    /**
     * @inheritdoc
     */
    public function beforeSave($insert){
    	if (parent::beforeSave($insert)) {
    		
    		if($this->isNewRecord){
    			$this->created = date('Y-m-d H:i:s');
    			$this->created_by =Yii::$app->user->identity->id;
    			$this->modified =date('Y-m-d H:i:s');
    			$this->modified_by =Yii::$app->user->identity->id;
    			$this->hastasks = "N";
    		}else{
    			$this->modified =date('Y-m-d H:i:s');
    			$this->modified_by =Yii::$app->user->identity->id;
    		}
    		// Place your custom code here
    		return true;
    	} else {
    		return false;
    	}
    }
    
    public function logAndLocation($teamservice_id,$teamservice,$mod='add'){
    	$activityLog = new ActivityLog();
    	if($mod == 'add') {
    		$activityLog->generateLog('TeamService','Added', $teamservice_id, $teamservice);
    	} else {
    		TeamserviceLocs::deleteAll('teamservice_id = '.$teamservice_id);
    		$activityLog->generateLog('TeamService','Updated', $teamservice_id, $teamservice);
    	}
    	/* TeamServiceLocationModel */
		if(!empty($this->team_location)){
			foreach($this->team_location as $loc){
    			$teamlocModel=new TeamserviceLocs();
    			$teamlocModel->teamservice_id=$teamservice_id;
    			$teamlocModel->team_loc=$loc;
    			$teamlocModel->save();
    		}
    	} else { 
			$teamlocModel=new TeamserviceLocs();
			$teamlocModel->teamservice_id=$teamservice_id;
			$teamlocModel->team_loc = 0; //IF teamid 1
			$teamlocModel->save();
		}
    	return true;
    }
    
    public function processSlaLogic($post_data,$model){
    	if(!empty($post_data['TeamserviceSla'])){
    		foreach ($post_data['TeamserviceSla'] as $id=>$data){
    			if(is_numeric($id)){ //edit Sla Logic
    				$TeamserviceSlamodel = TeamserviceSla::findOne($id);
    				$TeamserviceSlamodel->teamservice_id=$data['teamservice_id'];
    				$TeamserviceSlamodel->team_loc_id=$data['team_loc_id'];
    				$TeamserviceSlamodel->start_logic=$data['start_logic'];
    				$TeamserviceSlamodel->start_qty=$data['start_qty'];
    				$TeamserviceSlamodel->size_start_unit_id=$data['size_start_unit_id'];
    				$TeamserviceSlamodel->end_logic=$data['end_logic'];
    				$TeamserviceSlamodel->end_qty=$data['end_qty'];
    				$TeamserviceSlamodel->size_end_unit_id=$data['size_end_unit_id'];
					$TeamserviceSlamodel->del_qty=$data['del_qty'];
    				$TeamserviceSlamodel->del_time_unit=$data['del_time_unit'];
    				$TeamserviceSlamodel->project_priority_id=$data['project_priority_id'];
    				$TeamserviceSlamodel->save();
    			}else{ //add Sla Logic
    				$TeamserviceSlamodel = new TeamserviceSla();
    				$TeamserviceSlamodel->teamservice_id=$model->id;
    				$TeamserviceSlamodel->team_loc_id=$data['team_loc_id'];
    				$TeamserviceSlamodel->start_logic=$data['start_logic'];
    				$TeamserviceSlamodel->start_qty=$data['start_qty'];
    				$TeamserviceSlamodel->size_start_unit_id=$data['size_start_unit_id'];
    				$TeamserviceSlamodel->end_logic=$data['end_logic'];
    				$TeamserviceSlamodel->end_qty=$data['end_qty'];
    				$TeamserviceSlamodel->size_end_unit_id=$data['size_end_unit_id'];
                                $TeamserviceSlamodel->del_qty=$data['del_qty'];
    				$TeamserviceSlamodel->del_time_unit=$data['del_time_unit'];
    				$TeamserviceSlamodel->project_priority_id=$data['project_priority_id'];                                
    				$TeamserviceSlamodel->save();
    			}
    		}
    	}
    	if(isset($post_data['deletedLogicId']) && $post_data['deletedLogicId']!=""){
    		$integerIDs = explode(',', $post_data['deletedLogicId']);
    		foreach ($integerIDs as $key => $value) {
    			if (!is_int($key)) {
    				unset($integerIDs[$key]);
    			}
    		}
    		if(!empty($integerIDs)){
    			TeamserviceSla::deleteAll(' id IN ('.implode(",",$integerIDs).')');
    		}
    	}
    	return;
    }
    public static function getPublicServiceTask(){
    	$sql2 = "SELECT tbl_team.team_name, tbl_teamservice.id as teamservice_id, service_name,tbl_servicetask.id as servicetask_id, tbl_servicetask.service_task, tbl_servicetask_team_locs.team_loc, tbl_teamlocation_master.team_location_name
FROM tbl_teamservice
INNER JOIN tbl_team ON tbl_teamservice.teamid = tbl_team.id
INNER JOIN tbl_servicetask ON tbl_teamservice.id = tbl_servicetask.teamservice_id
LEFT JOIN  tbl_servicetask_team_locs ON tbl_servicetask_team_locs.servicetask_id = tbl_servicetask.id
LEFT JOIN  tbl_teamlocation_master ON tbl_teamlocation_master.id = tbl_servicetask_team_locs.team_loc
WHERE (tbl_servicetask.task_hide=0) AND (tbl_servicetask.publish=1)
AND ( tbl_servicetask_team_locs.team_loc in (select team_loc from tbl_teamservice_locs where tbl_teamservice_locs.team_loc=tbl_servicetask_team_locs.team_loc and tbl_teamservice_locs.teamservice_id=tbl_servicetask.teamservice_id) OR tbl_servicetask_team_locs.team_loc=0)    			
ORDER BY tbl_teamservice.service_name,tbl_servicetask.service_task";
				
    	$data =   \Yii::$app->db->createCommand($sql2)->queryAll();
    	$teamlocModel=new Teamservice();
    	return $data;//$teamlocModel->processServiceTask($data);
    }
    
    public function processServiceTask($data = array()){
    	$makeRequiredArr=array();
    	if(!empty($data)){
    		foreach ($data as $servicedata){
    			if($servicedata['team_loc'] == "" || $servicedata['team_loc'] == 0){
    				$makeRequiredArr['teamservice'][$servicedata['teamservice_id']][0]=array('teamservice'=>$servicedata['service_name'],'location'=>0);
    				$makeRequiredArr['servicetask'][$servicedata['teamservice_id']][0][]=$servicedata;
    			}else{
    				$makeRequiredArr['teamservice'][$servicedata['teamservice_id']][$servicedata['team_loc']]=array('teamservice'=>$servicedata['service_name'],'location'=>$servicedata['team_location_name']);
    				$makeRequiredArr['servicetask'][$servicedata['teamservice_id']][$servicedata['team_loc']][]=$servicedata;
    			}
    		}
    	}
    	return $makeRequiredArr;
    } 
   
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCaseXteams()
    {
        return $this->hasMany(CaseXteam::className(), ['teamservice_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getServicetasks()
    {
        return $this->hasMany(Servicetask::className(), ['teamservice_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTaskInstructServicetasks()
    {
        return $this->hasMany(TaskInstructServicetask::className(), ['teamservice_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTasksTeams()
    {
        return $this->hasMany(TasksTeams::className(), ['teamservice_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTeam()
    {
        return $this->hasOne(Team::className(), ['id' => 'teamid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTeamserviceLocs()
    {
        return $this->hasMany(TeamserviceLocs::className(), ['teamservice_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTeamserviceSlas()
    {
        return $this->hasMany(TeamserviceSla::className(), ['teamservice_id' => 'id']);
    }
    
    public function getProjectSecurity()
    {
		return $this->hasMany(ProjectSecurity::className(),['team_id'=>'teamid']);
	}
    
}
