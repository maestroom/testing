<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%setting_email_template_fields}}".
 *
 * @property integer $id
 * @property integer $template_id
 * @property integer $field_id
 * @property string $display
 * @property integer $is_default
 *
 * @property SettingsEmail $template
 * @property SettingsEmailFields $field
 */
class SettingEmailTemplateFields extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%setting_email_template_fields}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['template_id', 'field_id', 'display'], 'required'],
            [['template_id', 'field_id', 'is_default'], 'integer'],
            [['display_name'], 'string', 'max' => 255],
            [['template_id'], 'exist', 'skipOnError' => true, 'targetClass' => SettingsEmail::className(), 'targetAttribute' => ['template_id' => 'id']],
            [['field_id'], 'exist', 'skipOnError' => true, 'targetClass' => SettingsEmailFields::className(), 'targetAttribute' => ['field_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'template_id' => 'Template ID',
            'field_id' => 'Field ID',
            'display_name' => 'Display',
            'is_default' => 'Is Default',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTemplate()
    {
        return $this->hasOne(SettingsEmail::className(), ['id' => 'template_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getField()
    {
        return $this->hasOne(SettingsEmailFields::className(), ['id' => 'field_id']);
    }
}
