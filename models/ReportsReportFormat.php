<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%reports_report_format}}".
 *
 * @property integer $id
 * @property string $report_format
 */
class ReportsReportFormat extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%reports_report_format}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['report_format'], 'required'],
            [['report_format'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'report_format' => 'Report Format',
        ];
    }
}
