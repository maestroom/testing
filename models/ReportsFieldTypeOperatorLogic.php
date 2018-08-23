<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%reports_field_type_operator_logic}}".
 *
 * @property integer $id
 * @property integer $fieldtype_id
 * @property integer $fieldoperator_id
 */
class ReportsFieldTypeOperatorLogic extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%reports_field_type_operator_logic}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['fieldtype_id','fieldoperator_id'], 'required'],
            [['fieldtype_id', 'fieldoperator_id'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'fieldtype_id' => 'Fieldtype ID',
            'fieldoperator_id' => 'Fieldoperator ID',
        ];
    }
     /**
     * @return \yii\db\ActiveQuery
     */
    public function getReportsFieldType()
    {
        return $this->hasMany(ReportsFieldType::className(), ['id' => 'fieldtype_id']);
    }
}
