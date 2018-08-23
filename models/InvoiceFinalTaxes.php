<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%invoice_final_taxes}}".
 *
 * @property integer $id
 * @property integer $invoice_final_billing_id
 * @property integer $tax_code_id
 * @property string $code
 * @property double $rate
 *
 * @property InvoiceFinalBilling $invoiceFinalBilling
 */
class InvoiceFinalTaxes extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%invoice_final_taxes}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['invoice_final_billing_id', 'tax_code_id', 'rate'], 'required'],
            [['invoice_final_billing_id', 'tax_code_id'], 'integer'],
            [['rate'], 'number'],
            [['code'], 'string'],
            [['invoice_final_billing_id'], 'exist', 'skipOnError' => true, 'targetClass' => InvoiceFinalBilling::className(), 'targetAttribute' => ['invoice_final_billing_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'invoice_final_billing_id' => 'Invoice Final Billing ID',
            'tax_code_id' => 'Tax Code ID',
            'code' => 'Code',
            'rate' => 'Rate',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInvoiceFinalBilling()
    {
        return $this->hasOne(InvoiceFinalBilling::className(), ['id' => 'invoice_final_billing_id']);
    }
}
