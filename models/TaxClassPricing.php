<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_tax_class_pricing".
 *
 * @property integer $id
 * @property integer $tax_class_id
 * @property integer $price_id
 *
 * @property TaxClass $taxClass
 * @property Pricing $price
 */
class TaxClassPricing extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_tax_class_pricing';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tax_class_id', 'price_id'], 'required'],
            [['tax_class_id', 'price_id'], 'integer'],
            [['tax_class_id'], 'exist', 'skipOnError' => true, 'targetClass' => TaxClass::className(), 'targetAttribute' => ['tax_class_id' => 'id']],
            [['price_id'], 'exist', 'skipOnError' => true, 'targetClass' => Pricing::className(), 'targetAttribute' => ['price_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'tax_class_id' => 'Tax Class ID',
            'price_id' => 'Price ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTaxClass()
    {
        return $this->hasOne(TaxClass::className(), ['id' => 'tax_class_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPrice()
    {
        return $this->hasOne(Pricing::className(), ['id' => 'price_id']);
    }
}
