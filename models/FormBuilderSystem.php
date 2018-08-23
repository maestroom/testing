<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%form_builder_system}}".
 *
 * @property integer $id
 * @property string $sys_form
 * @property string $sys_display_name
 * @property integer $grid_only
 * @property string $sys_field_name
 * @property integer $required
 * @property integer $grid_type
 * @property integer $sort_order
 * @property integer $must_required
 * @property integer $table_field
 */
class FormBuilderSystem extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%form_builder_system}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sys_form', 'sys_display_name', 'sys_field_name', 'sort_order'], 'required'],
            [['required', 'grid_type', 'sort_order','must_required','grid_only','table_field'], 'integer'],
            [['sys_form', 'sys_display_name', 'sys_field_name'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'sys_form' => 'Sys Form',
            'sys_display_name' => 'Sys Display Name',
            'sys_field_name' => 'Sys Field Name',
            'required' => 'Required',
            'grid_type' => 'Grid Type',
            'sort_order' => 'Sort Order',
            'must_required'=>'Must Required',
            'grid_only'=>'Is Grid Only',
            'table_field'=>'Table Field'
        ];
    }
}
