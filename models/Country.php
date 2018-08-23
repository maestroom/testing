<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%country}}".
 *
 * @property integer $id
 * @property string $country_name
 * @property integer $remove
 */
class Country extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%country}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['country_name'], 'required'],
            [['country_name'], 'string'],
            [['remove'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'country_name' => 'Country Name',
            'remove' => 'Remove',
        ];
    }
}
