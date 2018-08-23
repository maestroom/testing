<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%evidence_production_bates}}".
 *
 * @property integer $id
 * @property integer $prod_id
 * @property integer $prod_media_id
 * @property integer $task_id
 * @property string $prod_bbates
 * @property string $prod_ebates
 * @property string $prod_vol
 * @property string $prod_date_loaded
 * @property string $created
 * @property integer $created_by
 * @property string $modified
 * @property integer $modified_by
 */
class EvidenceProductionBates extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%evidence_production_bates}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['prod_id', 'prod_media_id', 'task_id'], 'required'],
            [['prod_id', 'prod_media_id', 'task_id', 'created_by', 'modified_by'], 'integer'],
            [['prod_date_loaded', 'created', 'created_by', 'modified', 'modified_by'], 'safe'],
            [['prod_bbates', 'prod_ebates', 'prod_vol'], 'string'],
            [['prod_id'], 'exist', 'skipOnError' => true, 'targetClass' => EvidenceProduction::className(), 'targetAttribute' => ['prod_id' => 'id']],
            [['prod_media_id'], 'exist', 'skipOnError' => true, 'targetClass' => EvidenceProductionMedia::className(), 'targetAttribute' => ['prod_media_id' => 'id']],
            [['task_id'], 'exist', 'skipOnError' => true, 'targetClass' => Tasks::className(), 'targetAttribute' => ['task_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'prod_id' => 'Prod ID',
            'prod_media_id' => 'Prod Media ID',
            'task_id' => 'Task ID',
            'prod_bbates' => 'Production Beg Bates',
            'prod_ebates' => 'Production End Bates',
            'prod_vol' => 'Production Volume',
            'prod_date_loaded' => 'Production Date Loaded',
            'created' => 'Created',
            'created_by' => 'Created By',
            'modified' => 'Modified',
            'modified_by' => 'Modified By',
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
    			$this->created_by = Yii::$app->user->identity->id;
    			$this->modified = date('Y-m-d H:i:s');
    			$this->modified_by = Yii::$app->user->identity->id;
    		}else{
    			$this->modified = date('Y-m-d H:i:s');
    			$this->modified_by = Yii::$app->user->identity->id;
    		}
    		// Place your custom code here
    		return true;
    	} else {
    		return false;
    	}
    }
    
    /**
     * To get Production Bates by Project ID  
     * @param task_id int [51]
     */
	public function getProdBatesValue($task_id) 
	{
		$getallprodbates = array('bbates'=>0,'ebates'=>0,'vol'=>0);
		$getallprodbatesdata = $this->find()->where(['tasks_id ='.$task_id])->select(['prod_bbates','prod_ebates','prod_vol']);
		foreach($getallprodbatesdata as $data) {
			$getallprodbates['bbates'] =  $data->prod_bbates;
			$getallprodbates['ebates'] = $data->prod_ebates;
			$getallprodbates['vol'] = $data->prod_vol;
		}
		return $getallprodbates;
	}
	public function getTask()
    {
        return $this->hasOne(Tasks::className(), ['id' => 'task_id']);
    }
}
