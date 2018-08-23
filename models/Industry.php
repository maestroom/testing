<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_industry".
 *
 * @property integer $id
 * @property string $industry_name
 * @property integer $remove
 *
 * @property Client[] $clients
 */
class Industry extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_industry';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['industry_name'], 'required'],
            [['remove'], 'integer'],
            [['industry_name'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'industry_name' => Yii::t('app', 'Industry Name'),
            'remove' => Yii::t('app', 'Remove'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClients()
    {
        return $this->hasMany(Client::className(), ['industry_id' => 'id']);
    }
}
