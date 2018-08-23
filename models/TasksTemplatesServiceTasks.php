<?php

namespace app\models;
use kotchuprik\sortable\behaviors;

use Yii;

/**
 * This is the model class for table "{{%tasks_templates_service_tasks}}".
 *
 * @property integer $id
 * @property integer $task_template_id
 * @property integer $service_task
 * @property integer $team_loc
 * @property integer $sort_order
 *
 * @property Servicetask $serviceTask
 * @property TasksTemplates $taskTemplate
 * @property TeamlocationMaster $teamLoc
 */
class TasksTemplatesServiceTasks extends \yii\db\ActiveRecord
{
	public $orderAttribute='sort_order';
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%tasks_templates_service_tasks}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['task_template_id', 'service_task', 'team_loc'], 'required'],
            [['task_template_id', 'service_task', 'team_loc', 'sort_order'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'task_template_id' => 'Task Template ID',
            'service_task' => 'Service Task',
            'team_loc' => 'Team Loc',
            'sort_order' => 'Sort Order',
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
    					'query' => self::find(),
    					'orderAttribute'=>'sort_order',
    			],
    	];
    }
    /**
     * @inheritdoc
     */
    public function beforeInsert()
    {
    	$last = $this->find()->orderBy([$this->orderAttribute => SORT_DESC])->limit(1)->one();
    	if ($last === null) {
    		$this->{$this->orderAttribute} = 1;
    	} else {
    		$this->{$this->orderAttribute} = $last->{$this->orderAttribute} + 1;
    	}
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getServiceTask()
    {
        return $this->hasOne(Servicetask::className(), ['id' => 'service_task']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTaskTemplate()
    {
        return $this->hasOne(TasksTemplates::className(), ['id' => 'task_template_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTeamLoc()
    {
        return $this->hasOne(TeamlocationMaster::className(), ['id' => 'team_loc']);
    }
}
