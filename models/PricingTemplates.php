<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%pricing_templates}}".
 *
 * @property integer $id
 * @property integer $template_type
 * @property integer $client_id
 * @property integer $client_case_id
 * @property string $created
 * @property integer $created_by
 * @property string $modified
 * @property integer $modified_by
 */
class PricingTemplates extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%pricing_templates}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['template_type', 'client_id', 'client_case_id'], 'required'],
            [['template_type', 'client_id', 'client_case_id'], 'integer'],
            [['created', 'modified'], 'safe'],
            [['client_id'], 'exist', 'skipOnError' => true, 'targetClass' => Client::className(), 'targetAttribute' => ['client_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'template_type' => 'Template Type',
            'client_id' => 'Client ID',
            'client_case_id' => 'Client Case ID',
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
     * @return \yii\db\ActiveQuery
     */
	public function getPricingTemplatesIds()
    {
        return $this->hasMany(PricingTemplatesIds::className(), ['template_id' => 'id']);
    }
}
