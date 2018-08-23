<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%pricing_clients_rates}}".
 *
 * @property integer $id
 * @property integer $pricing_clients_id
 * @property integer $team_loc
 * @property integer $rate_type
 * @property double $rate_amount
 * @property double $cost_amount
 * @property double $tier_from
 * @property double $tier_to
 *
 * @property PricingClients $pricingClients
 */
class PricingClientsRates extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%pricing_clients_rates}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pricing_clients_id'], 'required'],
            [['pricing_clients_id', 'team_loc', 'rate_type'], 'integer'],
            [['rate_amount', 'cost_amount', 'tier_from', 'tier_to'], 'double'],
            [['pricing_clients_id'], 'exist', 'skipOnError' => true, 'targetClass' => PricingClients::className(), 'targetAttribute' => ['pricing_clients_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'pricing_clients_id' => 'Pricing Clients ID',
            'team_loc' => 'Team Loc',
            'rate_type' => 'Rate Type',
            'rate_amount' => 'Rate Amount',
            'cost_amount' => 'Cost Amount',
            'tier_from' => 'Tier From',
            'tier_to' => 'Tier To',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPricingClients()
    {
        return $this->hasOne(PricingClients::className(), ['id' => 'pricing_clients_id']);
    }
    
	/**
     * @return \yii\db\ActiveQuery
     */
	public function getTeamlocationMaster()
    {
        return $this->hasOne(TeamlocationMaster::className(), ['id' => 'team_loc']);
    }
}
