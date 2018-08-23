<?php

namespace app\models;

use Yii;
use app\models\ReportsFieldType;

/**
 * This is the model class for table "{{%reports_report_type_fields}}".
 *
 * @property integer $id
 * @property integer $reporttype_id
 * @property string $table_name
 * @property string $table_display_name
 * @property string $field_name
 * @property string $field_display_name
 * @property integer $is_field_associated
 */
class ReportsReportTypeFields extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%reports_report_type_fields}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['report_type_id', 'reports_fields_id', 'reports_fields_relationships_id','is_grp'], 'integer'],
             [['report_condition'], 'string'],
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
            'reports_fields_id' => 'Reports Fields ID',
            'reports_fields_relationships_id' => 'Fields Relationships ID',
            'report_condition'=>'Fields Condition',
            'is_grp'=>'Is Group'
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function getReportsField(){
    	return $this->hasOne(ReportsFields::className(), ['id' => 'reports_fields_id']);	
    }
}
