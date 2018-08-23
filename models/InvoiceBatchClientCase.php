<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%invoice_batch_client_case}}".
 *
 * @property integer $id
 * @property integer $invoice_batch_id
 * @property integer $client_case_id
 *
 * @property InvoiceBatch $invoiceBatch
 * @property ClientCase $clientCase
 */
class InvoiceBatchClientCase extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%invoice_batch_client_case}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['invoice_batch_id'], 'required'],
            [['invoice_batch_id', 'client_case_id'], 'integer'],
            [['invoice_batch_id'], 'exist', 'skipOnError' => true, 'targetClass' => InvoiceBatch::className(), 'targetAttribute' => ['invoice_batch_id' => 'id']],
            [['client_case_id'], 'exist', 'skipOnError' => true, 'targetClass' => ClientCase::className(), 'targetAttribute' => ['client_case_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'invoice_batch_id' => 'Invoice Batch ID',
            'client_case_id' => 'Client Case ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInvoiceBatch()
    {
        return $this->hasOne(InvoiceBatch::className(), ['id' => 'invoice_batch_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClientCase()
    {
        return $this->hasOne(ClientCase::className(), ['id' => 'client_case_id']);
    }
}
