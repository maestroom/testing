<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;
use app\models\ServicetaskTeamLocs;
use app\models\Teamservice;
use app\models\ActivityLog;

/**
 * This is the model class for table "{{%servicetask}}".
 *
 * @property integer $id
 * @property integer $teamservice_id
 * @property string $service_task
 * @property string $description
 * @property integer $task_hide
 * @property integer $publish
 * @property integer $billable_item
 * @property integer $sampling
 * @property string $hasform
 * @property string $haspricing
 * @property integer $service_order
 * @property integer $force_entry
 * @property string $created
 * @property integer $created_by
 * @property string $modified
 * @property integer $modified_by
 *
 * @property PricingServiceTask[] $pricingServiceTasks
 * @property Team $team
 * @property Teamservice $teamservice
 * @property ServicetaskTeamLocs[] $servicetaskTeamLocs
 * @property TaskInstructNotes[] $taskInstructNotes
 * @property TaskInstructServicetask[] $taskInstructServicetasks
 * @property TasksTemplatesServiceTasks[] $tasksTemplatesServiceTasks
 */
class Servicetask extends \yii\db\ActiveRecord
{
	public $team_location;
	public $orderAttribute ='service_order';
	public $servicetasks = '';
	public $teamId;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%servicetask}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['teamservice_id', 'task_hide', 'publish', 'billable_item', 'sampling', 'service_order', 'force_entry', 'created_by', 'modified_by','hasform', 'haspricing'], 'integer'],
        	[['team_location'], 'required','when'=>function($model){ return $model->teamId != 1;},'whenClient' => "function (attribute, value) {
		        return $('#servicetask-teamId').val() != 1;
		    }"],
            [['teamservice_id', 'service_task'], 'required'],
            [['service_task', 'description'], 'string'],
            [['created', 'modified','servicetasks'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'teamservice_id' => 'Teamservice ID',
            'service_task' => 'Service Task',
            'description' => 'Description',
            'task_hide' => 'Task Hide',
            'publish' => 'Publish',
            'billable_item' => 'Billable Item',
            'sampling' => 'Sampling',
            'hasform' => 'Hasform',
            'haspricing' => 'Haspricing',
            'service_order' => 'Service Order',
            'force_entry' => 'Force Entry',
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
    					'query' => self::find()->where(['teamservice_id'=>$this->teamservice_id]),
    					'orderAttribute'=>'service_order',
    			],
    	];
    }
    /**
     * @inheritdoc
     */
    public function beforeInsert()
    {
    	$last = $this->find()->where(['teamservice_id'=>$this->teamservice_id])->orderBy([$this->orderAttribute => SORT_DESC])->limit(1)->one();
    	if ($last === null) {
    		$this->{$this->orderAttribute} = 1;
    	} else {
    		$this->{$this->orderAttribute} = $last->{$this->orderAttribute} + 1;
    	}
    }
    /**
     * @inheritdoc
     */
    public function afterFind()
    {
		$this->teamId=$this->teamservice->teamid;
		return true;
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
    			$this->publish = 0;
    			$this->task_hide = 0;
    			$this->hasform = 0;
    			$this->haspricing = 0;
    		//	$this->datahasform = 0;
    		}else{
    			$this->modified =date('Y-m-d H:i:s');
    			$this->modified_by =Yii::$app->user->identity->id;
    		}
    		return true;
    	} else {
    		return false;
    	}
    }
	public function logAndLocation($servicetask_id,$servicetask,$mod='add'){
    	$activityLog = new ActivityLog();
    	if($mod == 'add'){
    		$activityLog->generateLog('ServiceTask','Added', $servicetask_id, $servicetask);
    	}else{
    		ServicetaskTeamLocs::deleteAll('servicetask_id = '.$servicetask_id);
    		$activityLog->generateLog('ServiceTask','Updated', $servicetask_id, $servicetask);
    	}
    	if($mod!='delete'){
			if(!empty($this->team_location)){
				foreach ($this->team_location as $loc){
					$teamlocModel=new ServicetaskTeamLocs();
					$teamlocModel->servicetask_id=$servicetask_id;
					$teamlocModel->team_loc=$loc;
					$teamlocModel->save();
				}
			}else if($this->teamId == 1){
					$teamlocModel=new ServicetaskTeamLocs();
					$teamlocModel->servicetask_id=$servicetask_id;
					$teamlocModel->team_loc=0;
					$teamlocModel->save();
			}
		}
    	$data=$this::findOne($servicetask_id);
    	if(isset($data->teamservice_id) && $data->teamservice_id > 0){
    		Teamservice::updateAll(['hastasks' => '1'], ['id'=>$data->teamservice_id]);
    	}
    	return true;
    }
    public function log($servicetask_id,$servicetask,$mod){
    	$data=$this::findOne($servicetask_id);
    	$teamservice_id = $data->teamservice_id;
    	$activityLog = new ActivityLog();
    	$activityLog->generateLog('ServiceTask','Deleted', $servicetask_id, $servicetask);
    	$alldata_count=$this::find()->where(['teamservice_id'=>$teamservice_id])->count();
    	if(!$alldata_count){
    		Teamservice::updateAll(['hastasks' => 'N'], ['id'=>$teamservice_id]);
    	}
    	
    }
    
    /**
     * Check is Service task is used in project,Billing or in Workflow
     * */
    public function CheckIsServiceUsed($id){
    	$response="N";
    	$data = $this::findOne($id);
    	if(!empty($data->tasksTemplatesServiceTasks)){
    		$response="You cannot remove this Task because it is used in a Workflow Template.";
    	}
    	/* if(!empty($data->pricingServiceTasks)){
    		$response="Service is used in Billing pricing.";
    	} */
    	/* if(!empty($data->taskInstructServicetasks)){
    		$response="Service is used in Project.";
    	} */
    	return $response;
    }
    
    /**
     * Check is service task is added in team service in workflow
     */
    public function CheckIsServiceTaskUsed($id){
    	$response="N";
    	$data = $this::find()->where('teamservice_id='.$id)->one();
    	if(!empty($data->teamservice_id)){
    		$response="Team Service have service task in workflow template.";
    	}
    	return $response;
    }
    
    /**
     * Get Belongto current team to login users
     */
    public function getBelongto($user_id){
    	$sql="teamid IN (SELECT team_id FROM tbl_project_security WHERE user_id =".$user_id." AND team_id !=0 )";
    	return ArrayHelper::map(Servicetask::find()->joinWith('teamservice')->select('tbl_servicetask.id')->where($sql)->all(),'id','id');
    }
    
    /**
     * Get Belongto current team loc to login users
     */
    public function getBelongtoLoc($user_id){
    	$service_locs =array();
    	$sql="team_loc IN (SELECT team_loc FROM tbl_project_security WHERE user_id =".$user_id." AND team_id !=0 )";
    	$data = ServicetaskTeamLocs::find()->select(['servicetask_id','team_loc'])->where($sql)->all();
    	if(!empty($data)){
    			foreach ($data  as $serlocs){
    				$service_locs[$serlocs->servicetask_id][$serlocs->team_loc]=$serlocs->team_loc;
    			}
    	}
    	return $service_locs;//,'servicetask_id','team_loc');
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPricingServiceTasks()
    {
        return $this->hasMany(PricingServiceTask::className(), ['servicetask_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    /*public function getTeam()
    {
        return $this->hasOne(Team::className(), ['id' => 'teamId']);
    }*/

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
    public function getServicetaskTeamLocs()
    {
        return $this->hasMany(ServicetaskTeamLocs::className(), ['servicetask_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTaskInstructNotes()
    {
        return $this->hasMany(TaskInstructNotes::className(), ['servicetask_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTaskInstructServicetasks()
    {
        return $this->hasMany(TaskInstructServicetask::className(), ['servicetask_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTasksTemplatesServiceTasks()
    {
        return $this->hasMany(TasksTemplatesServiceTasks::className(), ['service_task' => 'id']);
    }
    
    /**
     * get Services by Team & TeamLoc
     * @param team_id int
     * @param team_loc array 
     */
    
    public function getServicesByTeamandTeamloc($team_id, $teamLocAr)
    {
    	$servicetasks = $this->find()
	    	->joinWith([
	    		'servicetaskTeamLocs'=>function(\yii\db\ActiveQuery $query) use($teamLocAr){
	    			$query->where(['in','team_loc',$teamLocAr]);
	    		},
	    		'teamservice'=>function(\yii\db\ActiveQuery $query) {
	    			$query->select(['tbl_teamservice.id','tbl_teamservice.service_name']);
	    		}
	    	])
	    	->where(['tbl_teamservice.teamid' => $team_id, 'task_hide' => 0])
	    	->andWhere('billable_item IN (1,2)')
	    	->select(['tbl_servicetask.id','service_task','tbl_servicetask.teamservice_id'])
	    	->asArray()->all();
	    $servicetaskList = array();
	    if(!empty($servicetasks)) {	
	    	foreach($servicetasks as $service){
	    		$servicetaskList[$service['id']] = $service['teamservice']['service_name']." - ".$service['service_task'];
	    	}
    	}
	    return $servicetaskList; 
    } 
}
