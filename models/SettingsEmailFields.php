<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%settings_email_fields}}".
 *
 * @property integer $id
 * @property string $origination
 * @property string $display_name
 * @property string $field_value
 * @property string $preview_display
 */
class SettingsEmailFields extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public $display;
    public static function tableName()
    {
        return '{{%settings_email_fields}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['origination', 'display_name', 'field_value', 'preview_display'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'origination' => 'Origination',
            'display_name' => 'Display Name',
            'field_value' => 'Field Value',
            'preview_display' => 'Preview Display',
        ];
    }
}
