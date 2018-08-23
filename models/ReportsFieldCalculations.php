<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%reports_field_calculations}}".
 *
 * @property integer $id
 * @property string $calculation_field_name
 * @property string $calculation_name
 * @property string $calculation
 * @property string $calculation_type
 * @property string $select_sql
 */
class ReportsFieldCalculations extends \yii\db\ActiveRecord
{
	const SCENARIO_NEXT = 'next';
    /**
     * @inheritdoc
     */
    public $table,$exp,$primary_tables,$function;
    public static function tableName()
    {
        return '{{%reports_field_calculations}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['calculation_field_name','calculation_name','select_sql','calculation_type'], 'required'],
            [['calculation','calculation_type','select_sql'], 'string'],
            [['calculation_name','calculation_field_name'], 'string'],
        ];
    }
     
    
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_NEXT] = ['calculation_field_name','calculation_name', 'calculation_type'];
        return $scenarios;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'calculation_field_name' => 'Calculation Field Name',
            'calculation_name' => 'Cal Display Name',
            'calculation' => 'Calculation Description',
            'calculation_type' => 'Calculation Type',
            'select_sql' => 'SELECT SQL Statement',
            'calculation_primary'=>'This Field'
        ];
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReportsReportTypeFieldCalculation()
    {
        return $this->hasOne(ReportsReportTypeFieldCalculation::className(), ['field_calculation_id'=> 'id']);
    }
}
