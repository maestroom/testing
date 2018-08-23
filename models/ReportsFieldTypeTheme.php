<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%reports_field_type_theme}}".
 *
 * @property integer $id
 * @property string $field_type_theme
 */
class ReportsFieldTypeTheme extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%reports_field_type_theme}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['field_type_theme'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'field_type_theme' => 'Field Type Theme',
        ];
    }
}
