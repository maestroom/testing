<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_pricing_rates".
 *
 * @property integer $id
 * @property integer $pricing_id
 * @property integer $team_loc
 * @property integer $rate_type
 * @property double $rate_amount
 * @property double $cost_amount
 * @property double $tier_from
 * @property double $tier_to
 */
class PricingRates extends \yii\db\ActiveRecord
{
    public $teamlocation_name=""; 
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%pricing_rates}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pricing_id','rate_amount', 'team_loc'], 'required'],
            [['pricing_id', 'rate_type'], 'integer'],
            [['rate_amount', 'cost_amount', 'tier_from', 'tier_to'], 'double'],
            [['tier_from','tier_to'], 'required','when'=>function($model){ return $model->rate_type == 2;},'whenClient' => "function (attribute, value) {
				return $('input[name=\"PricingRates[rate_type]\"]:checked').val() == 2;
		    }"],
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
            'team_loc' => 'Location(s)',
            'rate_type' => 'Rate Type',
            'rate_amount' => 'Bill Rate',
            'cost_amount' => 'Cost Rate',
            'tier_from' => 'From',
            'tier_to' => 'To',
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
	public function getTeamlocationMaster()
    {
        return $this->hasOne(TeamlocationMaster::className(), ['id' => 'team_loc']);
    }
}
