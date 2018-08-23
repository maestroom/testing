<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%pricing_utbms_codes}}".
 *
 * @property integer $id
 * @property string $code
 * @property string $code_group
 * @property string $code_sort
 * @property string $code_group_name
 * @property string $code_name
 * @property string $code_description
 */
class PricingUtbmsCodes extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%pricing_utbms_codes}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['code', 'code_group', 'code_sort', 'code_group_name', 'code_name', 'code_description'], 'required'],
            [['code'], 'string'],
            [['code_group', 'code_sort'], 'string'],
            [['code_group_name'], 'string'],
            [['code_name'], 'string'],
            [['code_description'], 'string'],
            [['code'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'code' => 'Code',
            'code_group' => 'Code Group',
            'code_sort' => 'Code Sort',
            'code_group_name' => 'Code Group Name',
            'code_name' => 'Code Name',
            'code_description' => 'Code Description',
        ];
    }
}
