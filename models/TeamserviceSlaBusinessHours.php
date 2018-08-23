<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%teamservice_sla_business_hours}}".
 *
 * @property integer $id
 * @property string $start_time
 * @property string $end_time
 * @property integer $workinghours
 * @property string $workingdays
 * @property string $created
 * @property integer $created_by
 * @property string $modified
 * @property integer $modified_by
 */
class TeamserviceSlaBusinessHours extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%teamservice_sla_business_hours}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['start_time', 'end_time', 'workingdays'], 'string'],
            [[ 'created_by', 'modified_by'], 'integer'],
            [['workinghours'], 'double'],
            [['start_time','end_time','workingdays'], 'required'],
            [['end_time'], 'isValidateBusinessHours'],
            [['created', 'modified'], 'safe']
        ];
    }

    public function isValidateBusinessHours($attribute)
    {
        $validate = date("Y-m-d ".$this->start_time.":00") > date("Y-m-d ".$this->end_time.":00") ? false : true;
        if(!$validate)
            $this->addError($attribute, 'End Time should be greater than Start Time.');
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'start_time' => 'Start Time',
            'end_time' => 'End Time',
            'workinghours' => 'Workinghours',
            'workingdays' => 'Workingdays',
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
