<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%reports_chart_format}}".
 *
 * @property integer $id
 * @property string $chart_format
 * @property string $chart_axis
 */
class ReportsChartFormat extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%reports_chart_format}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['chart_format'], 'required'],
            [['chart_format', 'chart_axis'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'chart_format' => 'Chart Format',
            'chart_axis' => 'Chart Axis',
        ];
    }
}
