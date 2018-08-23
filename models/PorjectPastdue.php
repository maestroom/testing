<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;


/**
 * This is the model class for table "{{%project_pastdue}}".
 *
 * @property integer $id
 * @property integer $task_id
 * @property integer $user_id
 * @property string $created
 *
 */
class PorjectPastdue extends \yii\db\ActiveRecord
{
	/**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%project_pastdue}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'task_id', 'user_id'], 'required'],
            [['id', 'task_id', 'user_id'], 'integer'],
            [['created'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'task_id' => 'Project Id',
            'user_id' => 'User Id',
            'created' => 'Created'
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
    	if (parent::beforeSave($insert)) {
    		if($this->isNewRecord){
    			$this->created = date('Y-m-d H:i:s');
    		}
    		return true;
    	} else {
    		return false;
    	}
    }
}
