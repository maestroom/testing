<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%reports_fields}}".
 *
 * @property integer $id
 * @property integer $report_table_id
 * @property string $field_name
 * @property string $field_display_name
 * @property integer $reports_field_type_id
 *
 * @property ReportsTables $reportTable
 * @property ReportsFieldType $reportsFieldType
 */
class ReportsFields extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%reports_fields}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['report_table_id', 'field_name', 'field_display_name', 'reports_field_type_id'], 'required'],
            [['report_table_id', 'reports_field_type_id'], 'integer'],
            [['field_name', 'field_display_name'], 'string'],
            [['report_table_id'], 'exist', 'skipOnError' => true, 'targetClass' => ReportsTables::className(), 'targetAttribute' => ['report_table_id' => 'id']],
            [['reports_field_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => ReportsFieldType::className(), 'targetAttribute' => ['reports_field_type_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'report_table_id' => 'Report Table ID',
            'field_name' => 'Field Name',
            'field_display_name' => 'Field Display Name',
            'reports_field_type_id' => 'Reports Field Type ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReportsTables(){
        return $this->hasOne(ReportsTables::className(), ['id' => 'report_table_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReportsFieldType(){
        return $this->hasOne(ReportsFieldType::className(), ['id' => 'reports_field_type_id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReportsFieldsRelationships(){
        return $this->hasMany(ReportsFieldsRelationships::className(), ['rela_base_field' => 'id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReportsFieldsLookupRelationships(){
        return $this->hasMany(ReportsFieldsRelationships::className(), ['rela_base_field' => 'id'])->Where('rela_type NOT IN (0)');
    }
}
