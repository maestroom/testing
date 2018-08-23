<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%reports_calculation_sp}}".
 *
 * @property integer $id
 * @property string $sp_name
 * @property string $sp_desc
 * @property string $mysql_sp_code
 * @property string $mssql_sp_code
 * @property string $sp_params
 *
 * @property ReportsCalculationSpTable[] $reportsCalculationSpTables
 */
class ReportsCalculationSp extends \yii\db\ActiveRecord
{
	public $related_table,$primary_tables;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%reports_calculation_sp}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sp_name', 'sp_desc'], 'required'],
            [['sp_desc', 'mysql_sp_code', 'mssql_sp_code', 'sp_params'], 'string'],
//            [['sp_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'sp_name' => 'Sp Name',
            'sp_desc' => 'Sp Desc',
            'mysql_sp_code' => 'Mysql Sp Code',
            'mssql_sp_code' => 'Mssql Sp Code',
            'sp_params' => 'Sp Definition',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReportsCalculationSpTables()
    {
        return $this->hasMany(ReportsCalculationSpTable::className(), ['sp_id' => 'id']);
    }
}
