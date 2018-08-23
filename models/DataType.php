<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%data_type}}".
 *
 * @property integer $id
 * @property string $data_type
 * @property integer $remove
 *
 * @property EvidenceContents[] $evidenceContents
 */
class DataType extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%data_type}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['data_type'], 'required'],
            [['data_type'], 'string'],
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
            'data_type' => Yii::t('app', 'Data Type'),
            'remove' => Yii::t('app', 'Remove'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEvidenceContents()
    {
        return $this->hasMany(EvidenceContents::className(), ['data_type' => 'id']);
    }
}
