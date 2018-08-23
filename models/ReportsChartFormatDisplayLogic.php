<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%reports_chart_format_display_logic}}".
 *
 * @property integer $id
 * @property integer $chartformat_id
 * @property integer $chartformat_displayby_id
 */
class ReportsChartFormatDisplayLogic extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%reports_chart_format_display_logic}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['chartformat_id', 'chartformat_displayby_id'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'chartformat_id' => 'Chartformat ID',
            'chartformat_displayby_id' => 'Chartformat Displayby ID',
        ];
    }
}
