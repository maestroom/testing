<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%task_instruct_evidence}}".
 *
 * @property integer $id
 * @property integer $task_instruct_id
 * @property integer $task_id
 * @property integer $evidence_id
 * @property integer $prod_id
 * @property integer $evidence_contents_id
 */
class TaskInstructEvidence extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%task_instruct_evidence}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['task_instruct_id'], 'required'],
            [['task_instruct_id', 'evidence_id', 'prod_id', 'evidence_contents_id'], 'integer'],
            [['task_instruct_id'], 'exist', 'skipOnError' => true, 'targetClass' => TaskInstruct::className(), 'targetAttribute' => ['task_instruct_id' => 'id']],
            [['evidence_id'], 'exist', 'skipOnError' => true, 'targetClass' => Evidence::className(), 'targetAttribute' => ['evidence_id' => 'id']],
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
            'evidence_id' => 'Evidence ID',
            'prod_id' => 'Prod ID',
            'evidence_contents_id' => 'Evidence Contents ID',
        ];
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTaskInstruct()
    {
    	return $this->hasMany(TaskInstruct::className(), ['id' => 'task_instruct_id']);
    }
   /**
     * @return \yii\db\ActiveQuery
     */
    public function getEvidence()
    {
    	return $this->hasOne(Evidence::className(), ['id' => 'evidence_id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEvidenceContents()
    {
    	return $this->hasOne(EvidenceContents::className(), ['id' => 'evidence_contents_id']);
    }
    public function getEvidenceProduction(){
		return $this->hasOne(EvidenceProduction::className(), ['id' => 'prod_id']);
	}
}
