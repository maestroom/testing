<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%evidence_custodians_forms}}".
 *
 * @property integer $Id
 * @property string $Form_name
 * @property string $Form_desc
 * @property integer $Publish
 * @property string $Created
 * @property integer $Created_by
 * @property string $Modified
 * @property integer $Modified_by
 */
class EvidenceCustodiansForms extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%evidence_custodians_forms}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
        	[['Form_name'], 'required'],
        	[['Form_name', 'Form_desc'], 'string'],
            [['Publish', 'Created_by', 'Modified_by'], 'integer'],
            [['Created', 'Modified', 'remove'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'Id' => 'ID',
            'Form_name' => 'Form Name',
            'Form_desc' => 'Form Description',
            'Publish' => 'Publish',
        	'remove' => 'Remove',
            'Created' => 'Created',
            'Created_by' => 'Created By',
            'Modified' => 'Modified',
            'Modified_by' => 'Modified By',
        ];
    }
    /**
     * @inheritdoc
     */
    public function beforeSave($insert){
    	if (parent::beforeSave($insert)) {
    		if($this->isNewRecord){
    			$this->remove = 0;
    			$this->Created = date('Y-m-d H:i:s');
    			$this->Created_by =Yii::$app->user->identity->id;
    			$this->Modified =date('Y-m-d H:i:s');
    			$this->Modified_by =Yii::$app->user->identity->id;
    		}else{
    			$this->Modified =date('Y-m-d H:i:s');
    			$this->Modified_by =Yii::$app->user->identity->id;
    		}
    		// Place your custom code here
    		 
    		return true;
    	} else {
    		return false;
    	}
    }
}
