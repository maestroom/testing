<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%case_close_type}}".
 *
 * @property integer $id
 * @property string $close_type
 * @property integer $remove
 *
 * @property ClientCase[] $clientCases
 */
class CaseCloseType extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%case_close_type}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['close_type',], 'required'],
            [['remove'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'close_type' => Yii::t('app', 'Close Type'),
            'remove' => Yii::t('app', 'Remove'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClientCases()
    {
        return $this->hasMany(ClientCase::className(), ['case_close_id' => 'id']);
    }
}
