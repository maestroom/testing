<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%case_type}}".
 *
 * @property integer $id
 * @property string $case_type_name
 * @property integer $remove
 *
 * @property ClientCase[] $clientCases
 */
class CaseType extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%case_type}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['case_type_name'], 'required'],
            [['remove'], 'integer'],
            [['case_type_name'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'case_type_name' => Yii::t('app', 'Case Type'),
            'remove' => Yii::t('app', 'Remove'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClientCases()
    {
        return $this->hasMany(ClientCase::className(), ['case_type_id' => 'id']);
    }
}
