<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%unit_price}}".
 *
 * @property integer $id
 * @property string $unit_price_name
 * @property integer $remove
 *
 * @property Pricing $pricing
 * @property TaxClassPricing[] $taxClassPricings
 */
class UnitPrice extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%unit_price}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['unit_price_name'], 'required'],
            [['unit_price_name'], 'string'],
            [['remove'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'unit_price_name' => 'Task Price Units Name',
            'remove' => 'Remove',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPricing()
    {
        return $this->hasOne(Pricing::className(), ['id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTaxClassPricings()
    {
        return $this->hasMany(TaxClassPricing::className(), ['price_id' => 'id']);
    }
}
