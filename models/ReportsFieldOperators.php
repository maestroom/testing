<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%reports_field_operators}}".
 *
 * @property integer $id
 * @property string $field_operator
 * @property string $field_operator_use
 */
class ReportsFieldOperators extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%reports_field_operators}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['field_operator','field_operator_use'], 'required'],
            [['field_operator'], 'string'],
            [['field_operator_use'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'field_operator' => 'Field Operator',
            'field_operator_use' => 'Field Operator Use',
        ];
    }
}
