<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%pricing_templates_ids}}".
 *
 * @property integer $id
 * @property integer $template_id
 * @property integer $pricing_id
 */
class PricingTemplatesIds extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%pricing_templates_ids}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['template_id'], 'required'],
            [['template_id', 'pricing_id'], 'integer'],
            [['template_id'], 'exist', 'skipOnError' => true, 'targetClass' => PricingTemplates::className(), 'targetAttribute' => ['template_id' => 'id']],
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
            'template_id' => 'Template ID',
            'pricing_id' => 'Pricing ID',
        ];
    }
    
	/**
     * @return \yii\db\ActiveQuery
     */
	public function getPricingTemplates()
    {
        return $this->hasOne(PricingTemplates::className(), ['id' => 'template_id']);
    }
}
