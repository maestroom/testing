<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%reports_chart_format_display_by}}".
 *
 * @property integer $id
 * @property string $chart_display_by
 */
class ReportsChartFormatDisplayBy extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%reports_chart_format_display_by}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
             [['chart_display_by'], 'required'],
            [['chart_display_by'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'chart_display_by' => 'Chart Display By',
        ];
    }
}
