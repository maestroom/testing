<?php

namespace app\models;

use Yii;
use app\models\Options;
/**
 * This is the model class for table "{{%tasks_units_transaction_log}}".
 *
 * @property integer $id
 * @property string $duration
 * @property integer $tasks_unit_id
 * @property integer $user_assigned
 * @property integer $transaction_type
 * @property integer $current_time
 * @property integer $transaction_by
 * @property string $transaction_date
 * @property string $team_assigned
 * @property string $created
 * @property integer $created_by
 * @property string $modified
 * @property integer $modified_by
 */
class TasksUnitsTransactionLog extends \yii\db\ActiveRecord
{
	public $transactionUser = '';
	
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%tasks_units_transaction_log}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['duration',  'user_assigned', 'transaction_type', 'current_time', 'transaction_by', 'transaction_date', 'created', 'created_by', 'modified', 'modified_by'], 'required'],
            [[ 'user_assigned', 'transaction_type', 'current_time', 'transaction_by', 'created_by', 'modified_by'], 'integer'],
            [['transaction_date', 'created', 'modified'], 'safe'],
            [['duration'], 'string'],
            [['team_assigned'], 'string'],
            /*[['task_id'], 'exist', 'skipOnError' => true, 'targetClass' => Tasks::className(), 'targetAttribute' => ['task_id' => 'id']],*/
            /*[['tasks_unit_id'], 'exist', 'skipOnError' => true, 'targetClass' => TasksUnits::className(), 'targetAttribute' => ['tasks_unit_id' => 'id']],*/
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'duration' => 'Duration',
            'tasks_unit_id' => 'Tasks Unit ID',
            'user_assigned' => 'User Assigned',
            'transaction_type' => 'Transaction Type',
            'current_time' => 'Current Time',
            'transaction_by' => 'Transaction By',
            'transaction_date' => 'Transaction Date',
            'team_assigned' => 'Team Assigned',
            'created' => 'Created',
            'created_by' => 'Created By',
            'modified' => 'Modified',
            'modified_by' => 'Modified By',
        ];
    }
    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
    	if (parent::beforeSave($insert)) {
    
    		if($this->isNewRecord){
    			$this->created = date('Y-m-d H:i:s');
    			$this->created_by = Yii::$app->user->identity->id;
    			$this->modified = date('Y-m-d H:i:s');
    			$this->modified_by = Yii::$app->user->identity->id;
    			$this->transaction_by = Yii::$app->user->identity->id;
    			$this->transaction_date = date('Y-m-d H:i:s');
    			$this->current_time = time();
    		}else{
    			$this->modified = date('Y-m-d H:i:s');
    			$this->modified_by = Yii::$app->user->identity->id;
    		}
    		// Place your custom code here
    		return true;
    	} else {
    		return false;
    	}
    }
   /**
    * generate  task unit activity Log
    * */
    public function generateLog($task_id,$taskunit_id,$unit_assigned_to,$type,$duration){
    	/* Unit Transaction log */
    	$last_assigntask=TasksUnitsTransactionLog::find()->joinWith('tasksUnits')->where(['tbl_tasks_units.id'=>$taskunit_id])->orderBy('id DESC')->one();
    	if(isset($last_assigntask->id) && $last_assigntask->id > 0){
	    	$currtime=time();
	    	$trans_date=strtotime($last_assigntask->transaction_date);
	    	$diff=abs($currtime-$trans_date);
	    	$days = intval((floor($diff / 86400)));
	    	$hours = intval((floor($diff / 3600)));
	    	$hours = $hours % 24;
	    	$minutes = intval((floor($diff / 60)));
	    	$minutes = $minutes % 60;
	    	
	    	$duration=$days." days ".$hours." hours ".$minutes." min";
	    	TasksUnitsTransactionLog::updateAll(['current_time'=>$currtime,'duration'=>$duration],'id='.$last_assigntask->id);
    	}
    	$model = new TasksUnitsTransactionLog();
    	//$model->task_id=$task_id;
    	$model->tasks_unit_id=$taskunit_id;
    	$model->transaction_type=$type;
    	$model->user_assigned = $unit_assigned_to;
    	$model->duration="0 days 0 hours 0 min";
        $model->created = date('Y-m-d H:i:s');
        $model->created_by = Yii::$app->user->identity->id;
        $model->modified = date('Y-m-d H:i:s');
        $model->modified_by = Yii::$app->user->identity->id;
        $model->transaction_by = Yii::$app->user->identity->id;
        $model->transaction_date = date('Y-m-d H:i:s');
        $model->current_time = time();
    	$model->save(false);
    }
    
	public function getStartedDateTimeByUnit($unit_id)
	{
		$data=$this->find()->where(['tasks_unit_id'=>$unit_id,'transaction_type'=>1])->orderBy('id')->select(['transaction_date'])->one();
		if(isset($data->transaction_date) && $data->transaction_date!="" && $data->transaction_date!="0000-00-00 00:00:00")
			return (new Options)->ConvertOneTzToAnotherTz($data->transaction_date,"UTC",$_SESSION["usrTZ"]);
		else 
			return "";
	}
	
	public function getTasksUnits(){
    	return $this->hasOne(TasksUnits::className(), ['id' => 'tasks_unit_id']);
    }
     /**
     * @return \yii\db\ActiveQuery
     */
    public function getTransactionUser()
    {
        return $this->hasOne(User::className(), ['id' => 'transaction_by']);
    }
}
