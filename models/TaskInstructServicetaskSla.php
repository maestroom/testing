<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%task_instruct_servicetask_sla}}".
 *
 * @property integer $id
 * @property integer $task_instruct_servicetask_id
 * @property integer $teamservice_sla_id
 *
 * @property TeamserviceSla $teamserviceSla
 * @property TaskInstructServicetask $taskInstructServicetask
 */
class TaskInstructServicetaskSla extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%task_instruct_servicetask_sla}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['task_instruct_servicetask_id', 'teamservice_sla_id'], 'required'],
            [['task_instruct_servicetask_id', 'teamservice_sla_id'], 'integer'],
            [['teamservice_sla_id'], 'exist', 'skipOnError' => true, 'targetClass' => TeamserviceSla::className(), 'targetAttribute' => ['teamservice_sla_id' => 'id']],
            [['task_instruct_servicetask_id'], 'exist', 'skipOnError' => true, 'targetClass' => TaskInstructServicetask::className(), 'targetAttribute' => ['task_instruct_servicetask_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'task_instruct_servicetask_id' => 'Task Instruct Servicetask ID',
            'teamservice_sla_id' => 'Teamservice Sla ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTeamserviceSla()
    {
        return $this->hasOne(TeamserviceSla::className(), ['id' => 'teamservice_sla_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTaskInstructServicetask()
    {
        return $this->hasOne(TaskInstructServicetask::className(), ['id' => 'task_instruct_servicetask_id']);
    }
}
