<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%invoice_final_billing}}".
 *
 * @property integer $id
 * @property integer $invoice_final_id
 * @property integer $billing_unit_id
 * @property integer $team_loc
 * @property double $final_rate
 * @property double $discount
 * @property string $discount_reason
 * @property string $internal_ref_no_id
 *
 * @property InvoiceFinal $invoiceFinal
 * @property TasksUnitsBilling $billingUnit
 * @property InvoiceFinalTaxes[] $invoiceFinalTaxes
 */
class InvoiceFinalBilling extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%invoice_final_billing}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['invoice_final_id', 'billing_unit_id', 'team_loc', 'final_rate'], 'required'],
            [['invoice_final_id', 'billing_unit_id', 'team_loc'], 'integer'],
            [['final_rate', 'discount'], 'number'],
//            [['discount_reason', 'internal_ref_no_id'], 'string', 'max' => 255],
            [['invoice_final_id'], 'exist', 'skipOnError' => true, 'targetClass' => InvoiceFinal::className(), 'targetAttribute' => ['invoice_final_id' => 'id']],
            [['billing_unit_id'], 'exist', 'skipOnError' => true, 'targetClass' => TasksUnitsBilling::className(), 'targetAttribute' => ['billing_unit_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'invoice_final_id' => 'Invoice Final ID',
            'billing_unit_id' => 'Billing Unit ID',
            'team_loc' => 'Team Loc',
            'final_rate' => 'Final Rate',
            'discount' => 'Discount',
            'discount_reason' => 'Discount Reason',
            'internal_ref_no_id' => 'Internal Ref No ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInvoiceFinal()
    {
        return $this->hasOne(InvoiceFinal::className(), ['id' => 'invoice_final_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBillingUnit()
    {
        return $this->hasOne(TasksUnitsBilling::className(), ['id' => 'billing_unit_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInvoiceFinalTaxes()
    {
        return $this->hasMany(InvoiceFinalTaxes::className(), ['invoice_final_billing_id' => 'id']);
    }
}
