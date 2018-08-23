<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%tax_code_clients}}".
 *
 * @property integer $id
 * @property integer $tax_code_id
 * @property integer $client_id
 *
 * @property TaxCode $taxCode
 * @property Client $client
 */
class TaxCodeClients extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%tax_code_clients}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['client_id'], 'required'],
            [['tax_code_id', 'client_id'], 'integer'],
            [['tax_code_id'], 'exist', 'skipOnError' => true, 'targetClass' => TaxCode::className(), 'targetAttribute' => ['tax_code_id' => 'id']],
            [['client_id'], 'exist', 'skipOnError' => true, 'targetClass' => Client::className(), 'targetAttribute' => ['client_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'tax_code_id' => 'Tax Code ID',
            'client_id' => 'Clients',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTaxCode()
    {
        return $this->hasOne(TaxCode::className(), ['id' => 'tax_code_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClient()
    {
        return $this->hasOne(Client::className(), ['id' => 'client_id']);
    }
}
