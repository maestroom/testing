<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%reports_calculation_function_params}}".
 *
 * @property integer $id
 * @property integer $function_id
 * @property string $params
 * @property string $type
 *
 * @property ReportsCalculationFunction $function
 */
class ReportsCalculationFunctionParams extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%reports_calculation_function_params}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['function_id','report_fields_id'], 'required'],
            [['function_id','report_fields_id'], 'integer'],
            [['function_id'], 'exist', 'skipOnError' => true, 'targetClass' => ReportsCalculationFunction::className(), 'targetAttribute' => ['function_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'function_id' => 'Function ID',
            'report_fields_id'=>'Report Fields ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFunction()
    {
        return $this->hasOne(ReportsCalculationFunction::className(), ['id' => 'function_id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReportFields()
    {
        return $this->hasOne(ReportsFields::className(), ['id' => 'report_fields_id']);
    }
}
