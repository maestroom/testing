<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%client_case_custodians}}".
 *
 * @property integer $id
 * @property integer $client_id
 * @property integer $client_case_id
 * @property integer $cust_id
 */
class ClientCaseCustodians extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%client_case_custodians}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['client_case_id', 'cust_id'], 'required'],
            [['client_case_id', 'cust_id'], 'integer'],
            [['client_case_id'], 'exist', 'skipOnError' => true, 'targetClass' => ClientCase::className(), 'targetAttribute' => ['client_case_id' => 'id']],
            /*[['client_id'], 'exist', 'skipOnError' => true, 'targetClass' => Client::className(), 'targetAttribute' => ['client_id' => 'id']],*/
            [['cust_id'], 'exist', 'skipOnError' => true, 'targetClass' => EvidenceCustodians::className(), 'targetAttribute' => ['cust_id' => 'cust_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'client_id' => 'Client ID',
            'client_case_id' => 'Client Case ID',
            'cust_id' => 'Cust ID',
        ];
    }
}
