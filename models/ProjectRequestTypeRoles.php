<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%project_request_type}}".
 *
 * @property integer $id
 * @property string $request_type
 * @property integer $remove
 */
class ProjectRequestTypeRoles extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%project_request_type_roles}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [           
            [['project_request_type_id','role_id'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'project_request_type_id' => 'Project Request Type ID',
            'role_id' => 'Role Id',
        ];
    }
}
