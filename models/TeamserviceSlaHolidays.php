<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%teamservice_sla_holidays}}".
 *
 * @property integer $id
 * @property string $holidaydate
 * @property string $holiday
 * @property string $created
 * @property integer $created_by
 * @property string $modified
 * @property integer $modified_by
 */
class TeamserviceSlaHolidays extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%teamservice_sla_holidays}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['holidaydate', 'holiday'], 'string'],
            [['created', 'modified'], 'safe'],
            [['created_by', 'modified_by'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'holidaydate' => 'Holidaydate',
            'holiday' => 'Holiday',
            'created' => 'Created',
            'created_by' => 'Created By',
            'modified' => 'Modified',
            'modified_by' => 'Modified By',
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function beforeSave($insert){
    	if (parent::beforeSave($insert)) {
    		if($this->isNewRecord){
    			$this->created = date('Y-m-d H:i:s');
    			$this->created_by =Yii::$app->user->identity->id;
    			$this->modified =date('Y-m-d H:i:s');
    			$this->modified_by =Yii::$app->user->identity->id;
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
}
