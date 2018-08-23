<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_reports_report_type_field_calculation".
 *
 * @property integer $id
 * @property integer $report_type_id
 * @property integer $field_calculation_id
 *
 * @property ReportsFieldType $reportType
 * @property ReportsFieldCalculations $fieldCalculation
 */
class ReportsReportTypeFieldCalculation extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_reports_report_type_field_calculation';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['report_type_id', 'field_calculation_id'], 'required'],
            [['report_type_id', 'field_calculation_id'], 'integer'],
          //  [['report_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => ReportsFieldType::className(), 'targetAttribute' => ['report_type_id' => 'id']],
           // [['field_calculation_id'], 'exist', 'skipOnError' => true, 'targetClass' => ReportsFieldCalculations::className(), 'targetAttribute' => ['field_calculation_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'report_type_id' => 'Report Type ID',
            'field_calculation_id' => 'Field Calculation ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReportType()
    {
        return $this->hasOne(ReportsFieldType::className(), ['id' => 'report_type_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReportsFieldCalculations()
    {
        return $this->hasOne(ReportsFieldCalculations::className(), ['id' => 'field_calculation_id']);
    }
}
