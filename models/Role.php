<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%role}}".
 *
 * @property integer $id
 * @property string $role_name
 * @property string $role_description
 * @property string $role_type
 * @property integer $role_cases
 * @property integer $role_team
 * @property string $created
 * @property integer $created_by
 * @property string $modified
 * @property integer $modified_by
 *
 * @property CommentRoles[] $commentRoles
 * @property RoleSecurity[] $roleSecurities
 * @property User[] $users
 */
class Role extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%role}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
    	//, 'role_type'
        return [
            [['role_name','role_type'], 'required'],
            [['role_name', 'role_description', 'role_type'], 'string'],
         //   [['role_cases', 'role_team', 'created_by', 'modified_by'], 'integer'],
        		[['created_by', 'modified_by'], 'integer'],
            [['created', 'modified'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'role_name' => 'Role Name',
            'role_description' => 'Role Description',
            'role_type' => 'Role Type',
//             'role_cases' => 'Role Cases',
//             'role_team' => 'Role Team',
            'created' => 'Created',
            'created_by' => 'Created By',
            'modified' => 'Modified',
            'modified_by' => 'Modified By',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCommentRoles()
    {
        return $this->hasMany(CommentRoles::className(), ['role_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRoleSecurities()
    {
        return $this->hasMany(RoleSecurity::className(), ['role_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(User::className(), ['role_id' => 'id']);
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
    		//	$this->role_cases=0;
    		//	$this->role_team=0;
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
     * @role Details
     */
    /**
     * @check role is available
     */
    public function checkRoleUsed($role_id){
    	$count_role = User::find()->where('role_id='.$role_id)->count();
    	return $count_role;
    }
}
