<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;
/**
 * This is the model class for table "{{%task_instruct_notes}}".
 *
 * @property integer $id
 * @property integer $task_id
 * @property integer $servicetask_id
 * @property string $notes
 * @property string $created
 * @property integer $created_by
 * @property string $modified
 * @property integer $modified_by
 */
class TaskInstructNotes extends \yii\db\ActiveRecord
{
	public $attachment;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%tasks_units_notes}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['task_id', 'servicetask_id'], 'required'],
            [['task_id', 'servicetask_id'], 'integer'],
            [['notes'], 'string'],
            [['created', 'modified'], 'safe'],
            [['servicetask_id'], 'exist', 'skipOnError' => true, 'targetClass' => Servicetask::className(), 'targetAttribute' => ['servicetask_id' => 'id']],
            [['task_id'], 'exist', 'skipOnError' => true, 'targetClass' => Tasks::className(), 'targetAttribute' => ['task_id' => 'id']],
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
            'servicetask_id' => 'Servicetask ID',
            'notes' => 'Notes',
            'created' => 'Created',
            'created_by' => 'Created By',
            'modified' => 'Modified',
            'modified_by' => 'Modified By'
        ];
    }
    
    public function removeTaskInstructNotesAttachmentsByProject($taskId){
    	if(isset($taskId) && $taskId!=""){
    		$sql="select tbl_mydocument.id  FROM tbl_mydocument inner join tbl_tasks_units_notes on tbl_mydocument.reference_id=tbl_tasks_units_notes.id and tbl_mydocument.origination='Instruct N' WHERE tbl_tasks_units_notes.task_id=".$taskId;
    		//$ids=ArrayHelper::map(Mydocument::find()->select('id')->where('id IN ('.$sql.')')->all(),'id','id');
    		//if(!empty($ids)){
	    		/*Remove Attachments*/
	    		MydocumentsBlob::deleteAll('id IN (select doc_id  FROM tbl_mydocument where id IN ('.$sql.'))');
                $deletesql="DELETE tbl_mydocument FROM tbl_mydocument inner join tbl_tasks_units_notes on tbl_mydocument.reference_id=tbl_tasks_units_notes.id and tbl_mydocument.origination='Instruct N' WHERE tbl_tasks_units_notes.task_id=".$taskId;
	    		Yii::$app->db->createCommand($deletesql)->execute();
                //Mydocument::deleteAll('id IN ('.implode(",",$ids).')');
	    		/*Remove Attachments*/
    		//}
    	}
    }

     /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
    	if (parent::beforeSave($insert)) {

            if($this->isNewRecord){
                $this->created = date('Y-m-d H:i:s');
                $this->created_by =Yii::$app->user->identity->id;
                $this->modified =date('Y-m-d H:i:s');
                $this->modified_by =Yii::$app->user->identity->id;
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

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedUser()
    {
    	return $this->hasOne(User::className(), ['id' => 'created_by']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getModifiedUser()
    {
    	return $this->hasOne(User::className(), ['id' => 'modified_by']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInstructionattachments()
    {
    	return $this->hasMany(Mydocument::className(), ['reference_id' => 'id'])->andOnCondition(['origination' => 'Instruct N']);
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
    public function getServicetask()
    {
        return $this->hasOne(Servicetask::className(), ['id' => 'servicetask_id']);
    }
}
