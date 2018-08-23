<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%pricing_service_task}}".
 *
 * @property integer $id
 * @property integer $pricing_id
 * @property integer $servicetask_id
 *
 * @property Pricing $pricing
 * @property Servicetask $servicetask
 */
class PricingServiceTask extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%pricing_service_task}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pricing_id', 'servicetask_id'], 'integer'],
            [['pricing_id'], 'exist', 'skipOnError' => true, 'targetClass' => Pricing::className(), 'targetAttribute' => ['pricing_id' => 'id']],
            [['servicetask_id'], 'exist', 'skipOnError' => true, 'targetClass' => Servicetask::className(), 'targetAttribute' => ['servicetask_id' => 'id']],
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
            'servicetask_id' => 'Servicetask ID',
        ];
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
    public function getServicetask()
    {
        return $this->hasOne(Servicetask::className(), ['id' => 'servicetask_id']);
    }
}
