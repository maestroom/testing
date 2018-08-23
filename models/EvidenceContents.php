<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%evidence_contents}}".
 *
 * @property integer $id
 * @property integer $evid_num_id
 * @property integer $evid_cont_id
 * @property integer $cust_id
 * @property integer $data_type
 * @property integer $data_size
 * @property integer $unit
 * @property string $data_copied_to
 * @property string $created
 * @property integer $created_by
 * @property string $modified
 * @property integer $modified_by
 */
class EvidenceContents extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%evidence_contents}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['cust_id'], 'required'],
            [['evid_num_id', 'cust_id', 'data_type', 'data_size', 'unit', 'created_by', 'modified_by'], 'integer'],
            [['data_copied_to'], 'string'],
            [['created', 'modified'], 'safe'],
            /*
            [['data_type'], 'exist', 'skipOnError' => true, 'targetClass' => DataType::className(), 'targetAttribute' => ['data_type' => 'id']],
            [['cust_id'], 'exist', 'skipOnError' => true, 'targetClass' => EvidenceCustodians::className(), 'targetAttribute' => ['cust_id' => 'cust_id']],
            [['evid_num_id'], 'exist', 'skipOnError' => true, 'targetClass' => Evidence::className(), 'targetAttribute' => ['evid_num_id' => 'id']],
            [['unit'], 'exist', 'skipOnError' => true, 'targetClass' => Unit::className(), 'targetAttribute' => ['unit' => 'id']],
            
            */
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'evid_num_id' => 'Evid Num ID',
            'evid_cont_id' => 'Evid Cont ID',
        //    'cust_id' => 'Cust ID',
            'cust_id' => 'Select Custodian',
            'data_type' => 'Data Type',
            'data_size' => 'Data Size',
            'unit' => 'Unit',
            'data_copied_to' => 'Data Copied To',
            'created' => 'Created',
            'created_by' => 'Created By',
            'modified' => 'Modified',
            'modified_by' => 'Modified By',
        ];
    }
    public function beforeSave($insert)
    {
    	if (parent::beforeSave($insert)) {
    		
    		if($this->isNewRecord){
                       // $this->has_contents = 'N';
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
    public function getEvidenceCustodians(){
    	return $this->hasOne(EvidenceCustodians::className(), ['cust_id' => 'cust_id']);
    }
    public function getEvidenceContentUnit(){
    	return $this->hasOne(Unit::className(), ['id' => 'unit']);
    }
    public function getDatatype(){
    	return $this->hasOne(DataType::className(), ['id' => 'data_type']);
    }
    public function getDataunit(){
    	return $this->hasOne(Unit::className(), ['id'=>'unit']);
    }
    public function getCountEvidenceContentByCid($cust_id)
    {
    	return $this::find()->joinWith('evidenceCustodians')->where('tbl_evidence_custodians.cust_id='.$cust_id)->count();
    }
    public function getEvidence()
    {
    	return $this->hasOne(Evidence::className(), ['id' => 'evid_num_id']);
    }
  
}
