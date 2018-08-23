<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%tasks_units_todo_transaction_log}}".
 *
 * @property integer $id
 * @property integer $task_id
 * @property string $duration
 * @property integer $tasks_unit_id
 * @property integer $todo_id
 * @property integer $user_assigned
 * @property integer $transaction_type
 * @property integer $current_time
 * @property integer $transaction_by
 * @property string $transaction_date
 * @property string $created
 * @property integer $created_by
 * @property string $modified
 * @property integer $modified_by
 */
class TasksUnitsTodoTransactionLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%tasks_units_todo_transaction_log}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['duration', 'tasks_unit_id', 'todo_id', 'user_assigned', 'transaction_type'], 'required'],
            [['tasks_unit_id', 'todo_id', 'user_assigned', 'transaction_type', 'current_time', 'transaction_by', 'created_by', 'modified_by'], 'integer'],
            [['transaction_date', 'created', 'modified'], 'safe'],
            [['duration'], 'string'],
            /*[['task_id'], 'exist', 'skipOnError' => true, 'targetClass' => Tasks::className(), 'targetAttribute' => ['task_id' => 'id']],*/
            [['tasks_unit_id'], 'exist', 'skipOnError' => true, 'targetClass' => TasksUnits::className(), 'targetAttribute' => ['tasks_unit_id' => 'id']],
            [['todo_id'], 'exist', 'skipOnError' => true, 'targetClass' => TasksUnitsTodos::className(), 'targetAttribute' => ['todo_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'task_id' => 'Task ID',
            'duration' => 'Duration',
            'tasks_unit_id' => 'Tasks Unit ID',
            'todo_id' => 'Todo ID',
            'user_assigned' => 'User Assigned',
            'transaction_type' => 'Transaction Type',
            'current_time' => 'Current Time',
            'transaction_by' => 'Transaction By',
            'transaction_date' => 'Transaction Date',
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
    
    public function getTasksUnitsTodos(){
    	return $this->hasOne(TasksUnitsTodos::className(), ['id' => 'todo_id']);
    }
   
    
    public function generateLog($todo_id,$task_id,$taskunit_id,$unit_assigned_to,$type,$duration){
//        echo $todo_id;die;
    	$last_assigntask = TasksUnitsTodoTransactionLog::find()->innerJoinWith(['tasksUnitsTodos' => function(\yii\db\ActiveQuery $query) use($taskunit_id){
				$query->where(['tbl_tasks_units_todos.tasks_unit_id'=>$taskunit_id]);
			}])->select(['transaction_date','tbl_tasks_units_todo_transaction_log.id'])->where('todo_id=' . $todo_id)->orderBy('tbl_tasks_units_todo_transaction_log.id DESC')->one();
    	if (!empty($last_assigntask)) {
    		$currtime = time();
    		$trans_date = strtotime($last_assigntask->transaction_date);
    		$diff = abs($currtime - $trans_date);
    		$days = intval((floor($diff / 86400)));
    		$hours = intval((floor($diff / 3600)));
    		$hours = $hours % 24;
    		$minutes = intval((floor($diff / 60)));
    		$minutes = $minutes % 60;
    		$lastduration = $days . " days " . $hours . " hours " . $minutes . " min";
                		
    		TasksUnitsTodoTransactionLog::updateAll(['current_time' => $currtime, 'duration' => $lastduration], 'id=' . $last_assigntask->id);
    	}
    	$model = new TasksUnitsTodoTransactionLog();
    	/*$model->task_id = $task_id;
    	$model->tasks_unit_id = $taskunit_id;*/
    	$model->todo_id = $todo_id;
    	$model->user_assigned = $unit_assigned_to;
    	$model->transaction_type = $type; 
    	$model->duration = $duration;
        $model->created = date('Y-m-d H:i:s');
        $model->created_by = Yii::$app->user->identity->id;
        $model->modified = date('Y-m-d H:i:s');
        $model->modified_by = Yii::$app->user->identity->id;
        $model->transaction_by = Yii::$app->user->identity->id;
        $model->transaction_date = date('Y-m-d H:i:s');
        $model->current_time = time();
    	$model->save(false);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTransactionUser(){
    	return $this->hasOne(User::className(), ['id' => 'transaction_by']);
    }
}
