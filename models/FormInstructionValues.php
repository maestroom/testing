<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%form_instruction_values}}".
 *
 * @property integer $id
 * @property integer $form_builder_id
 * @property integer $task_instruct_id
 * @property string $element_value
 * @property string $element_value_origin
 * @property integer $element_unit
 *
 * @property FormBuilder $formBuilder
 * @property TaskInstruct $taskInstruct
 */
class FormInstructionValues extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%form_instruction_values}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['form_builder_id', 'task_instruct_id', 'element_value'], 'required'],
            [['form_builder_id', 'task_instruct_id', 'element_unit'], 'integer'],
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
            'task_instruct_id' => 'Task Instruct ID',
            'element_value' => 'Element Value',
            'element_value_origin'=>'Element Value Origin',
            'element_unit' => 'Element Unit',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFormBuilder()
    {
        return $this->hasOne(FormBuilder::className(), ['id' => 'form_builder_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTaskInstruct()
    {
        return $this->hasOne(TaskInstruct::className(), ['id' => 'task_instruct_id']);
    }
}
