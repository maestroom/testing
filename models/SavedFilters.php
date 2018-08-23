<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%saved_filters}}".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $filter_name
 * @property integer $filter_type
 * @property string $filter_attributes
 */
class SavedFilters extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%saved_filters}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'filter_name', 'filter_attributes'], 'required'],
            [['user_id', 'filter_type'], 'integer'],
            [['filter_name'], 'string', 'max' => 150],
            [['filter_attributes'], 'string', 'max' => 8000],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'filter_name' => 'Filter Name',
            'filter_type' => 'Filter Type',
            'filter_attributes' => 'Filter Attributes',
        ];
    }
}
