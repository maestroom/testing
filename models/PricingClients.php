<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%pricing_clients}}".
 *
 * @property integer $id
 * @property integer $pricing_id
 * @property integer $client_id
 * @property string $created
 * @property integer $created_by
 * @property string $modified
 * @property integer $modified_by
 *
 * @property Client $client
 * @property Pricing $pricing
 * @property PricingClientsRates[] $pricingClientsRates
 */
class PricingClients extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%pricing_clients}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pricing_id', 'client_id'], 'required'],
            [['pricing_id', 'client_id', 'created_by', 'modified_by'], 'integer'],
            [['created', 'modified'], 'safe'],
            [['client_id'], 'exist', 'skipOnError' => true, 'targetClass' => Client::className(), 'targetAttribute' => ['client_id' => 'id']],
            [['pricing_id'], 'exist', 'skipOnError' => true, 'targetClass' => Pricing::className(), 'targetAttribute' => ['pricing_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'pricing_id' => 'Pricing ID',
            'client_id' => 'Client ID',
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

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClient()
    {
        return $this->hasOne(Client::className(), ['id' => 'client_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPricing()
    {
        return $this->hasOne(Pricing::className(), ['id' => 'pricing_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPricingClientsRates()
    {
        return $this->hasMany(PricingClientsRates::className(), ['pricing_clients_id' => 'id']);
    }
}
