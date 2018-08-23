<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_saved_reports_fields".
 *
 * @property integer $id
 * @property integer $saved_report_id
 * @property integer $report_type_field_id
 * @property integer $field_calculation_id
 * @property integer $column_sort_order
 * @property integer $field_group_type
 *
 * @property SavedReports $savedReport
 * @property SavedReportsFields $reportTypeField
 * @property SavedReportsFields[] $savedReportsFields
 * @property ReportsFieldCalculations $fieldCalculation
 * @property ReportsChartFormatDisplayBy $chartDisplayType
 * @property SavedReportsFieldsFilter[] $savedReportsFieldsFilters
 */
class ReportsUserSavedFields extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_reports_user_saved_fields';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['saved_report_id', 'report_type_field_id',  'field_calculation_id'], 'required'],
            [['saved_report_id', 'report_type_field_id',  'field_calculation_id'], 'integer'],
            [['saved_report_id'], 'exist', 'skipOnError' => true, 'targetClass' => ReportsUserSaved::className(), 'targetAttribute' => ['saved_report_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'saved_report_id' => 'Saved Report ID',
            'report_type_field_id' => 'Report Type Field ID',
            'field_calculation_id' => 'Field Calculation ID',
            //'chart_display_type_id' => 'Chart Display Type ID',
            //'chart_axis' => '1=X, 2=Y',
            'column_sort_order' => 'Column Sort Order',
            
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReportsUserSaved()
    {
        return $this->hasOne(ReportsUserSaved::className(), ['id' => 'saved_report_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReportsReportTypeFields()
    {
        return $this->hasOne(ReportsReportTypeFields::className(), ['id' => 'report_type_field_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFieldCalculation()
    {
        return $this->hasOne(ReportsFieldCalculations::className(), ['id' => 'field_calculation_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     
    public function getChartDisplayType()
    {
        return $this->hasOne(ReportsChartFormatDisplayBy::className(), ['id' => 'chart_display_type_id']);
    }*/

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReportsUserSavedFieldsLogic()
    {
        return $this->hasMany(ReportsUserSavedFieldsLogic::className(), ['saved_report_field_id' => 'id']);
    }
}
