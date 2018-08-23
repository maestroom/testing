<?php

namespace app\models;

use Yii;
use kotchuprik\sortable\behaviors;

/**
 * This is the model class for table "{{%priority_project}}".
 *
 * @property integer $id
 * @property string $priority
 * @property integer $priority_order
 * @property integer $remove
 *
 * @property TaskInstruct[] $taskInstructs
 */
class PriorityProject extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%priority_project}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['priority','project_priority_order'], 'required'],
            [['priority'], 'string'],
            [['priority_order', 'remove'], 'integer'],
            ['project_priority_order','number','numberPattern' => '/^\s*[-+]?[0-9]*[.,]?[0-9]+([eE][-+]?[0-9]+)?\s*$/']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'priority' => 'Priority',
            'priority_order' => 'Priority Order',
            'project_priority_order' => 'Project Priority Order',
            'remove' => 'Remove',
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
    					'query' => self::find()->where(['remove'=>0]),
    					'orderAttribute'=>'priority_order',
    			],
    	];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTaskInstructs()
    {
        return $this->hasMany(TaskInstruct::className(), ['task_priority' => 'id']);
    }
}
