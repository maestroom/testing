<?php

namespace app\models;

use Yii;
use app\models\SavedReportsFields;
use app\models\ReportsFieldOperators;

/**
 * This is the model class for table "tbl_saved_reports_fields_filter".
 *
 * @property integer $id
 * @property integer $saved_report_field_id
 * @property integer $report_field_operator_id
 * @property integer $format_total_type
 * @property integer $format_display_type
 * @property integer $format_display_decimal
 * @property integer $sort_type
 * @property integer $sort_order
 * @property string  $format_display_separator
 * @property string  $format_display_symbol
 * @property string  $value1
 * @property string  $value2
 *
 * @property SavedReportsFields $savedReportField
 * @property ReportsFieldOperators $reportFieldOperator
 */
class ReportsUserSavedFieldsLogic extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_reports_user_saved_fields_logic';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['saved_report_field_id'], 'required'],
            [['saved_report_field_id', 'report_field_operator_id','format_total_type','format_display_type','format_display_decimal','sort_type','sort_order'], 'integer'],
            [['value1', 'value2','format_display_separator','format_display_symbol'], 'string'],
			[['saved_report_field_id'], 'exist', 'skipOnError' => true, 'targetClass' => ReportsUserSavedFields::className(), 'targetAttribute' => ['saved_report_field_id' => 'id']],
			//[['report_field_operator_id'], 'exist', 'skipOnError' => true, 'targetClass' => ReportsFieldOperators::className(), 'targetAttribute' => ['report_field_operator_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'saved_report_field_id' => 'Saved Report Field ID',
            'report_field_operator_id' => 'Report Field Operator ID',
            'value1' => 'Value1',
            'value2' => 'Value2',
            'format_total_type'=>'Format Total Type',
            'format_display_type'=>'Format Type',
            'format_display_decimal'=>'Format Decimal',
            'sort_type'=>'Sort Type',
            'sort_order'=>'Sort  Order',
            'format_display_separator'=>'Format Separator',
            'format_display_symbol'=>'Format Symbol'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReportsUserSavedFields()
    {
        return $this->hasOne(ReportsUserSavedFields::className(), ['id' => 'saved_report_field_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReportFieldOperator()
    {
        return $this->hasOne(ReportsFieldOperators::className(), ['id' => 'report_field_operator_id'])->andWhere('report_field_operator_id!=0');
    }
}
