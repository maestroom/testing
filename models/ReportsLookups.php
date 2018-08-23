<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * This is the model class for table "{{%reports_lookups}}".
 *
 * @property integer $id
 * @property string $lookup_name
 * @property integer $type
 * @property string $filter_table
 * @property string $filter_field
 * @property string $lookup_table
 * @property string $lookup_field
 *
 * @property ReportsLookupValues[] $reportsLookupValues
 */
class ReportsLookups extends Model
{
 public $id;
 public $lookup_name;
 public $type;
 public $filter_table;
 public $filter_field;
 public $lookup_table;
 public $lookup_field;
 public $lookup_field_separator;
 public $lookup_field_separator2;

    /**
     * @inheritdoc
     */
    /*public static function tableName()
    {
        return '{{%reports_lookups}}';
    }*/

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['lookup_name', 'type', 'filter_table', 'filter_field'], 'required'],
            [['type'], 'integer'],
            [['lookup_name', 'filter_table', 'filter_field', 'lookup_table','lookup_field_separator'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'lookup_name' => 'Lookup Name',
            'type' => 'Type',
            'filter_table' => 'Filter Table',
            'filter_field' => 'Filter Field',
            'lookup_table' => 'Lookup Table',
            'lookup_field' => 'Lookup Field',
            'lookup_field_separator'=>'Lookup Field Separator'
        ];
    }

    /*
     * @return \yii\db\ActiveQuery
     
    public function getReportsLookupValues()
    {
        //return $this->hasMany(ReportsLookupValues::className(), ['reports_lookup_id' => 'id']);
    }*/
}
