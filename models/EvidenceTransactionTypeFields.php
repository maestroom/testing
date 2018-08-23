<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%evidence_transaction_type_fields}}".
 *
 * @property integer $id
 * @property string $field_type_theme
 */
class EvidenceTransactionTypeFields extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%evidence_transaction_type_fields}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['form_builder_system_id'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'form_builder_system_id' => 'Form Builder System Id',
            'transaction_type_id' => 'Transaction Type Id',
        ];
    }
}
