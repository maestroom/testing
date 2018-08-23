<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%reports_field_calculation_table}}".
 *
 * @property integer $id
 * @property integer $field_cal_id
 * @property integer $table_id
 *
 * @property ReportsFieldCalculations $fieldCal
 * @property ReportsTables $table
 */
class ReportsFieldCalculationTable extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%reports_field_calculation_table}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['field_cal_id', 'table_id'], 'required'],
            [['field_cal_id', 'table_id'], 'integer'],
            [['field_cal_id'], 'exist', 'skipOnError' => true, 'targetClass' => ReportsFieldCalculations::className(), 'targetAttribute' => ['field_cal_id' => 'id']],
            [['table_id'], 'exist', 'skipOnError' => true, 'targetClass' => ReportsTables::className(), 'targetAttribute' => ['table_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'field_cal_id' => 'Field Cal ID',
            'table_id' => 'Table ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFieldCal()
    {
        return $this->hasOne(ReportsFieldCalculations::className(), ['id' => 'field_cal_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTable()
    {
        return $this->hasOne(ReportsTables::className(), ['id' => 'table_id']);
    }
}
