<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%settings_ad_filters}}".
 *
 * @property integer $id
 * @property integer $filter_type
 * @property string $name
 * @property string $filter
 */
class SettingsAdFilters extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%settings_ad_filters}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['filter_type'], 'integer'],
            [['name', 'filter'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'filter_type' => 'Filter Type',
            'name' => 'Name',
            'filter' => 'Filter',
        ];
    }
}
