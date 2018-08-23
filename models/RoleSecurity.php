<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%role_security}}".
 *
 * @property integer $id
 * @property integer $role_id
 * @property integer $security_feature_id
 * @property integer $security_force
 * @property integer $user_id
 *
 * @property Role $role
 * @property SecurityFeature $securityFeature
 */
class RoleSecurity extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%role_security}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['role_id', 'security_feature_id'], 'required'],
            [['role_id', 'security_feature_id', 'user_id'], 'integer'],
        	[['security_force'],'safe']	
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'role_id' => 'Role ID',
            'security_feature_id' => 'Security Feature ID',
            'security_force' => 'Security Force',
            'user_id' => 'User ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRole()
    {
        return $this->hasOne(Role::className(), ['id' => 'role_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSecurityFeature()
    {
        return $this->hasOne(SecurityFeature::className(), ['id' => 'security_feature_id']);
    }
    
    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
    	if (parent::beforeSave($insert)) {
    		if($this->isNewRecord){
    			if(!isset($this->user_id))
    				$this->user_id=0;
    			if(!isset($this->security_force))
    				$this->security_force=0;
    			if(!isset($this->role_id))
    				$this->role_id=0;
    		}
    		return true;
    	} else {
    		return false;
    	}
    }
    
    /**
     * Delete Role Security By RoleID 
     */
    public function deleteSecurityRole($roleId){
    	foreach ($this->find()->where('role_id='.$roleId)->all() as $role) {
    		$role->delete();
    	}
    	return true;
    }
    
    /**
     * Delete Role Security by UserID
     */
    public function DeleteRolesecurityAll($userId){
    	foreach ($this->find()->where('user_id='.$userId)->all() as $user) {
    		$user->delete();
    	}
    	return true;
    }
}
