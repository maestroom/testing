<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%reports_calculation_sp_table}}".
 *
 * @property integer $id
 * @property integer $sp_id
 * @property integer $table_id
 *
 * @property ReportsCalculationSp $sp
 * @property ReportsTables $table
 */
class ReportsCalculationSpTable extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%reports_calculation_sp_table}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sp_id', 'table_id'], 'required'],
            [['sp_id', 'table_id'], 'integer'],
            [['sp_id'], 'exist', 'skipOnError' => true, 'targetClass' => ReportsCalculationSp::className(), 'targetAttribute' => ['sp_id' => 'id']],
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
            'sp_id' => 'Sp ID',
            'table_id' => 'Table ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSp()
    {
        return $this->hasOne(ReportsCalculationSp::className(), ['id' => 'sp_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTable()
    {
        return $this->hasOne(ReportsTables::className(), ['id' => 'table_id']);
    }
}
