<?php
namespace app\models;
use Yii;
/**
 * This is the model class for table "{{%reports_calculation_function}}".
 *
 * @property integer $id
 * @property string $function_name
 * @property string $function_desc
 * @property string $mysql_function_code
 * @property string $mssql_function_code
 * @property string $function_params
 *
 * @property ReportsCalculationFunctionTable[] $reportsCalculationFunctionTables
 */
class ReportsCalculationFunction extends \yii\db\ActiveRecord
{
	const SCENARIO_NEXT = 'next';
    public $related_table,$primary_tables;
	
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%reports_calculation_function}}';
    }

	public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_NEXT] = ['function_name','function_display_name', 'function_desc'];
        return $scenarios;
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['function_name','function_display_name', 'function_desc','mysql_function_code','mssql_function_code','function_params'], 'required'],
            [['function_desc', 'mysql_function_code', 'mssql_function_code', 'function_params'], 'string'],
            [['function_name','function_display_name'], 'string'],
            /*['mysql_function_code', 'required', 'when' => function($model) {               
                return Yii::$app->db->driverName == 'mysql';                 
            }, 'whenClient' => "function (attribute, value) {
                return '<?php echo Yii::$app->db->driverName;?>' == 'mysql';
            }"],
            ['mssql_function_code', 'required', 'when' => function($model) {               
                return Yii::$app->db->driverName != 'mysql';                 
            }, 'whenClient' => "function (attribute, value) {
                return '<?php echo Yii::$app->db->driverName;?>' != 'mysql';
            }"],*/
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'function_name' => 'Function Name',
            'function_display_name'=>'Function Display Name',
            'function_desc' => 'Function Desc',
            'mysql_function_code' => 'Mysql Function Code',
            'mssql_function_code' => 'Mssql Function Code',
            'function_params' => 'Function Definition',
        ];
    }

	
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReportsCalculationFunctionTables()
    {
        return $this->hasMany(ReportsCalculationFunctionTable::className(), ['function_id' => 'id']);
    }
}
