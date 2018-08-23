<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%reports_calculation_function_table}}".
 *
 * @property integer $id
 * @property integer $function_id
 * @property integer $table_id
 *
 * @property ReportsCalculationFunction $function
 * @property ReportsTables $table
 */
class ReportsCalculationFunctionTable extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%reports_calculation_function_table}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['function_id', 'table_id'], 'required'],
            [['function_id', 'table_id'], 'integer'],
            [['function_id'], 'exist', 'skipOnError' => true, 'targetClass' => ReportsCalculationFunction::className(), 'targetAttribute' => ['function_id' => 'id']],
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
            'function_id' => 'Function ID',
            'table_id' => 'Table ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFunction()
    {
        return $this->hasOne(ReportsCalculationFunction::className(), ['id' => 'function_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTable()
    {
        return $this->hasOne(ReportsTables::className(), ['id' => 'table_id']);
    }
}
