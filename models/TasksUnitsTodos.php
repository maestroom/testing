<?php

namespace app\models;

use Yii;
use app\models\ActivityLog;
use app\models\TasksUnitsTodoTransactionLog;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%tasks_units_todos}}".
 *
 * @property integer $id
 * @property integer $task_id
 * @property integer $tasks_unit_id
 * @property integer $todo_cat_id
 * @property string $todo
 * @property integer $assigned
 * @property integer $complete
 * @property string $created
 * @property integer $created_by
 * @property string $modified
 * @property integer $modified_by
 */
class TasksUnitsTodos extends \yii\db\ActiveRecord
{
	public $attachment,$assigned_user;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%tasks_units_todos}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
			[['todo'],'required','message'=>'ToDo cannot be blank.'],
            [[ 'tasks_unit_id'], 'required'],
            [[ 'tasks_unit_id', 'todo_cat_id', 'assigned', 'complete', 'created_by', 'modified_by'], 'integer'],
            [['created', 'modified'], 'safe'],
            [['todo'], 'string'],
            [['tasks_unit_id'], 'exist', 'skipOnError' => true, 'targetClass' => TasksUnits::className(), 'targetAttribute' => ['tasks_unit_id' => 'id']],
            /*[['task_id'], 'exist', 'skipOnError' => true, 'targetClass' => Tasks::className(), 'targetAttribute' => ['task_id' => 'id']],*/
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
            'tasks_unit_id' => 'Tasks Unit ID',
            'todo_cat_id' => 'Follow-up Category',
            'todo' => 'Todo',
            'assigned' => 'Assigned',
            'complete' => 'Complete',
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
    			$this->complete = 0;
    		}else{
    			$this->modified = date('Y-m-d H:i:s');
    			$this->modified_by = Yii::$app->user->identity->id;
    		}
    		if(!isset($this->todo_cat_id) || $this->todo_cat_id=="" || $this->todo_cat_id == null)
    			$this->todo_cat_id =0;
    		
    		// Place your custom code here
    		return true;
    	} else {
    		return false;
    	}
    }
    public function todoTransition($task_id,$taskunit_id){
    	$duration = "0 days 0 hours 0 min";
    	$list_notcompeltedtodos=TasksUnitsTodos::find()->where('tasks_unit_id=' . $taskunit_id . ' AND complete=0')->select('id')->all();
    	if(!empty($list_notcompeltedtodos)){
    		foreach ($list_notcompeltedtodos as $todo){
    			$model = TasksUnitsTodos::findOne($todo->id);
    			$model->assigned = 0;
    			$model->save(false);
    			(new ActivityLog)->generateLog('ToDo', 'Transition', $todo->id, $task_id);
    			(new TasksUnitsTodoTransactionLog)->generateLog($todo->id,$task_id,$taskunit_id,0,11,$duration);//Todo Transition
    		}
    	}
    	return;
    }
    public function removeTodoAttachmentsByProject($taskId){
    	if(isset($taskId) && $taskId!=""){
    		$sql="select tbl_mydocument.id  FROM tbl_mydocument inner join tbl_tasks_units_todos on tbl_mydocument.reference_id=tbl_tasks_units_todos.id and tbl_mydocument.origination='Todo' INNER JOIN tbl_tasks_units on tbl_tasks_units.id=tbl_tasks_units_todos.tasks_unit_id where tbl_tasks_units.task_id=".$taskId;
    		//$ids=ArrayHelper::map(Mydocument::find()->select('id')->where('id IN ('.$sql.')')->all(),'id','id');
    		//if(!empty($ids)){
    		/*Remove Attachments*/
    		MydocumentsBlob::deleteAll('id IN (select doc_id  FROM tbl_mydocument where id IN ('.$sql.'))');
    		$deletesql="DELETE tbl_mydocument FROM tbl_mydocument inner join tbl_tasks_units_todos on tbl_mydocument.reference_id=tbl_tasks_units_todos.id and tbl_mydocument.origination='Todo' INNER JOIN tbl_tasks_units on tbl_tasks_units.id=tbl_tasks_units_todos.tasks_unit_id where tbl_tasks_units.task_id=".$taskId;
            Yii::$app->db->createCommand($deletesql)->execute();
            //Mydocument::deleteAll('id IN ('.implode(",",$ids).')');
    		/*Remove Attachments*/
    		//}
    	}
    }
    
    public function getTodoattachments()
    {
    	return $this->hasMany(Mydocument::className(), ['reference_id' => 'id'])->andOnCondition(['origination' => 'Todo']);
    }
    public function getSomething(){
		return $this->hasMany(TasksUnitsTodoTransactionLog::className(), ['todo_id' => 'id'])->andOnCondition('(transaction_type=9 OR transaction_type=8 OR transaction_type=7 OR transaction_type=13)');
	}
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTasks()
    {
        return $this->hasOne(Tasks::className(), ['id' => 'task_id']);
    }
    
     /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedUser(){
    	return $this->hasOne(User::className(), ['id' => 'created_by']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAssignedUser(){
    	return $this->hasOne(User::className(), ['id' => 'assigned']);
    }
    public function getTaskUnit(){
		return $this->hasOne(TasksUnits::className(), ['id' => 'tasks_unit_id']);
	}
	
	public function getTodoCats(){
		return $this->hasOne(Todocats::className(), ['id' => 'todo_cat_id']);
	}
}
