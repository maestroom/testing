<?php

namespace app\models;

use Yii;
use app\models\TemplatesRequestTypes;
use yii\helpers\ArrayHelper;


/**
 * This is the model class for table "{{%project_request_type}}".
 *
 * @property integer $id
 * @property string $request_type
 * @property integer $remove
 */
class ProjectRequestType extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%project_request_type}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['request_type'], 'required'],
            [['request_type'], 'string'],
            [['remove'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'request_type' => 'Request Type',
            'remove' => 'Remove',
        ];
    }
    /*
     * Get status  if all roles are selected or not.
     * */
     public function getRequestRolesStatus($request_type_id){
		 $total_roles = Role::find()->where('id!=0')->select(['id','role_name']);
		 $req_total_roles = ArrayHelper::map(Role::find()->where('id!=0 AND id IN (SELECT role_id from tbl_project_request_type_roles where project_request_type_id = '.$request_type_id.')')->select(['id','role_name'])->all(),'id','role_name');
		 if($total_roles->count() == count($req_total_roles)){
			return 'eye';
		 }else{                      
                     $returnVal[0] = 'eye-slash';                                                              
                     if(count($req_total_roles) == 0){
                         $returnVal[1] = 'The Field values are visible to No User Role.';
                     }else{
                         $returnVal[1] = 'The Field values are visible only to the following User Roles: '.implode(', ',$req_total_roles);
                     }
                    return $returnVal;
		 }
	 }
	  /**
     * @return \yii\db\ActiveQuery
     */
    public function getTemplatesRequestTypes()
    {
        return $this->hasMany(TemplatesRequestTypes::className(), ['project_request_type_id' => 'id']);
    }
}
