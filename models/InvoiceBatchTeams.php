<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%invoice_batch_teams}}".
 *
 * @property integer $id
 * @property integer $invoice_batch_id
 * @property integer $team_id
 *
 * @property InvoiceBatch $invoiceBatch
 * @property Team $team
 */
class InvoiceBatchTeams extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%invoice_batch_teams}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['invoice_batch_id'], 'required'],
            [['invoice_batch_id', 'team_id'], 'integer'],
            [['invoice_batch_id'], 'exist', 'skipOnError' => true, 'targetClass' => InvoiceBatch::className(), 'targetAttribute' => ['invoice_batch_id' => 'id']],
            [['team_id'], 'exist', 'skipOnError' => true, 'targetClass' => Team::className(), 'targetAttribute' => ['team_id' => 'id']],
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
            'team_id' => 'Team ID',
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
    public function getTeam()
    {
        return $this->hasOne(Team::className(), ['id' => 'team_id']);
    }
}
