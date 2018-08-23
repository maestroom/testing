<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%form_custodian_values}}".
 *
 * @property integer $id
 * @property integer $form_builder_id
 * @property integer $client_case_id
 * @property integer $custodian_id
 * @property string $element_value
 * @property string $element_value_origin
 * @property integer $element_unit
 *
 * @property ClientCase $clientCase
 * @property EvidenceCustodians $custodian
 * @property FormBuilder $formBuilder
 */
class FormCustodianValues extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%form_custodian_values}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['form_builder_id', 'client_case_id', 'custodian_id', 'element_value'], 'required'],
            [['form_builder_id', 'client_case_id', 'custodian_id', 'element_unit'], 'integer'],
            [['element_value','element_value_origin'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'form_builder_id' => 'Form Builder ID',
            'client_case_id' => 'Client Case ID',
            'custodian_id' => 'Custodian ID',
            'element_value' => 'Element Value',
            'element_value_origin'=>'Element Value Origin',
            'element_unit' => 'Element Unit',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClientCase()
    {
        return $this->hasOne(ClientCase::className(), ['id' => 'client_case_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustodian()
    {
        return $this->hasOne(EvidenceCustodians::className(), ['cust_id' => 'custodian_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFormBuilder()
    {
        return $this->hasOne(FormBuilder::className(), ['id' => 'form_builder_id']);
    }
}
