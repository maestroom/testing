<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%settings_email}}".
 *
 * @property integer $id
 * @property string $email_name
 * @property string $email_header
 * @property string $email_subject
 * @property string $email_body
 * @property string $email_recipients
 * @property string $email_custom_subject
 * @property string $email_custom_body
 * @property string $email_custom_recipients
 * @property integer $email_sort
 * @property string $email_caserole
 * @property string $email_teamservice
 */
class SettingsEmailTemplate extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%settings_email}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['email_name', 'email_header', 'email_subject', 'email_body', 'email_recipients', 'email_custom_subject', 'email_custom_body', 'email_custom_recipients', 'email_caserole', 'email_teamservice'], 'string'],
            [['email_sort'], 'integer'],
            ['bcc_email_recipients','checkAllEmails']
        ];
    }
    
    /**
     * Check Email Validatoin 
     */
    public function checkAllEmails($attribute,$params)
    {
        if($this->$attribute != ''){
            $emails = explode(';',$this->$attribute);
            $pattern = "/^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*(\.[a-zA-Z]{2,})$/";
            if(!empty($emails)){
            	foreach($emails as $single) {
            		$single = trim($single);
            		if($single!="") {
            			if(!preg_match($pattern, $single)) {
                		    $this->addError($attribute, 'One of the Email Addresses ('.$single.') is invalid.');
                    	    break;
                    	}
                	}
                }
            }
        }
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'email_name' => 'Email Name',
            'email_header' => 'Email Header',
            'email_subject' => 'Email Subject',
            'email_body' => 'Email Body',
            'email_recipients' => 'Email Recipients',
            'email_custom_subject' => 'Email Custom Subject',
            'email_custom_body' => 'Email Custom Body',
            'email_custom_recipients' => 'Email Custom Recipients',
            'email_sort' => 'Email Sort',
            'email_caserole' => 'Email Caserole',
            'email_teamservice' => 'Email Teamservice',
            'bcc_email_recipients' => 'Email To: BCC Recipients'
        ];
    }
}
