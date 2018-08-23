<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%evidence_production_media}}".
 *
 * @property integer $id
 * @property integer $evid_id
 * @property integer $prod_id
 * @property integer $on_hold
 * @property string $created
 * @property integer $created_by
 * @property string $modified
 * @property integer $modified_by
 */
class EvidenceProductionMedia extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%evidence_production_media}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['evid_id', 'prod_id', 'on_hold', 'created', 'created_by', 'modified', 'modified_by'], 'required'],
            [['evid_id', 'prod_id', 'on_hold', 'created_by', 'modified_by'], 'integer'],
            [['created', 'modified'], 'safe'],
            [['evid_id'], 'exist', 'skipOnError' => true, 'targetClass' => Evidence::className(), 'targetAttribute' => ['evid_id' => 'id']],
            [['prod_id'], 'exist', 'skipOnError' => true, 'targetClass' => EvidenceProduction::className(), 'targetAttribute' => ['prod_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'evid_id' => 'Evid ID',
            'prod_id' => 'Prod ID',
            'on_hold' => 'On Hold',
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
    			if(!isset($this->on_hold)){$this->on_hold = 0;}
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
     public function getProdbates()
    {
        return $this->hasMany(EvidenceProductionBates::className(), ['prod_id' => 'prod_id', 'prod_media_id' => 'id'])->onCondition('tbl_evidence_production_bates.prod_id IN (SELECT prod_id FROM tbl_task_instruct_evidence INNER join tbl_task_instruct on tbl_task_instruct.id=tbl_task_instruct_evidence.task_instruct_id where tbl_task_instruct.isactive=1 AND tbl_task_instruct.task_id=tbl_evidence_production_bates.task_id)');
    }
    public function getProevidence()
    {
        return $this->hasOne(Evidence::className(), ['id' => 'evid_id']);
    }
    public function getMediaids($prod_id){
        
        return $todo_cat_list = ArrayHelper::map($this::find()->select(['evid_id','evid_id'])->where(['prod_id'=>$prod_id])->all(),'evid_id','evid_id');
    }
}
