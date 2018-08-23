<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%pricing_clientscases_rates}}".
 *
 * @property integer $id
 * @property integer $pricing_clientscases_id
 * @property integer $team_loc
 * @property integer $rate_type
 * @property double $rate_amount
 * @property double $cost_amount
 * @property double $tier_from
 * @property double $tier_to
 *
 * @property PricingClientscases $pricingClientscases
 */
class PricingClientscasesRates extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%pricing_clientscases_rates}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pricing_clientscases_id'], 'required'],
            [['pricing_clientscases_id', 'team_loc', 'rate_type'], 'integer'],
            [['rate_amount', 'cost_amount', 'tier_from', 'tier_to'], 'double'],
            [['pricing_clientscases_id'], 'exist', 'skipOnError' => true, 'targetClass' => PricingClientscases::className(), 'targetAttribute' => ['pricing_clientscases_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'pricing_clientscases_id' => 'Pricing Clientscases ID',
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
    public function getPricingClientscases()
    {
        return $this->hasOne(PricingClientscases::className(), ['id' => 'pricing_clientscases_id']);
    }
    
	/**
     * @return \yii\db\ActiveQuery
     */
	public function getTeamlocationMaster()
    {
        return $this->hasOne(TeamlocationMaster::className(), ['id' => 'team_loc']);
    }
}
