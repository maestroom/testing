<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%reports_report_type_sql}}".
 *
 * @property integer $id
 * @property integer $reporttype_id
 * @property string $join_string
 * @property string $base_table
 * @property string $relationship_data
 *
 * @property ReportsReportType $reporttype
 */
class ReportsReportTypeSql extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%reports_report_type_sql}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['reporttype_id', 'base_table'], 'required'],
            [['reporttype_id'], 'integer'],
            [['join_string', 'relationship_data'], 'string'],
            [['reporttype_id'], 'exist', 'skipOnError' => true, 'targetClass' => ReportsReportType::className(), 'targetAttribute' => ['reporttype_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'reporttype_id' => 'Reporttype ID',
            'join_string' => 'Join String',
            'base_table' => 'Base Table',
            'relationship_data' => 'Relationships'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReporttype()
    {
        return $this->hasOne(ReportsReportType::className(), ['id' => 'reporttype_id']);
    }
}
