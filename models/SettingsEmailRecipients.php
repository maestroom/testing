<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%settings_email_recipients}}".
 *
 * @property integer $id
 * @property string $email_recipients
 * @property integer $rep_ids
 */
class SettingsEmailRecipients extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%settings_email_recipients}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['email_recipients'], 'string'],
            [['rep_ids'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'email_recipients' => 'Email Recipients',
            'rep_ids' => 'Rep Ids',
        ];
    }
}
