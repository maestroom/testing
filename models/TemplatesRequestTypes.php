<?php

namespace app\models;

use app\models\ProjectRequestType;
use Yii;

/**
 * This is the model class for table "{{%templates_request_types}}".
 *
 * @property integer $id
 * @property integer $task_template_id
 * @property integer $project_request_type_id
 */
class TemplatesRequestTypes extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%templates_request_types}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [           
            [['task_template_id','project_request_type_id'], 'integer']
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
            'project_request_type_id' => 'Project Request Type Id',
        ];
    }
    public function getProjectRequestType(){
        return $this->hasOne(ProjectRequestType::className(), ['id'=>'project_request_type_id']);
    }
}
