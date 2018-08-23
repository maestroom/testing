<?php

namespace app\models;

use Yii;
use app\components\EMailer;

/**
 * This is the model class for table "{{%emailsettings}}".
 *
 * @property integer $setting_id
 * @property string $emailtype
 * @property string $from_name
 * @property string $from_email
 * @property string $email_host
 * @property string $port
 * @property string $username
 * @property string $password
 * @property string $security
 * @property string $sendmailvia
 */
class Emailsettings extends \yii\db\ActiveRecord
{
	public $auth;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%emailsettings}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['from_name', 'from_email', 'email_host', 'port', 'security', 'sendmailvia'], 'required'],
        	['from_email','email'],
        	['port','number'],
            [['emailtype', 'from_name', 'from_email', 'email_host', 'port', 'username', 'password', 'security', 'sendmailvia'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'setting_id' => 'Setting ID',
            'emailtype' => 'Emailtype',
            'from_name' => 'From Name',
            'from_email' => 'From Email',
            'email_host' => 'Email Host',
            'port' => 'Port',
            'username' => 'Username',
            'password' => 'Password',
            'security' => 'Security',
            'sendmailvia' => 'Send Email via',
            'auth'=>'Authentication'
        ];
    }
    
    public function testsendemail($request) {
    	$mailer = new EMailer();
        $mailer->CharSet = 'UTF-8';
        
        $data = array();

        if (!empty($request)) {
            $data = $request;
            $host = $data['email_host'];
            $port = $data['port'];
            $smtp_from = $data['from_email'];
            $smtp_fromname = $data['from_name'];
			$mailer->IsMail();
            if (isset($data['sendmailvia']) && trim($data['sendmailvia']) == 'smtp'){
                $mailer->IsSMTP();
			}
            if (isset($request['security']) && $request['security'] != 'none')
                $mailer->SMTPSecure = $request['security'];       // sets the prefix to the servier

            $mailer->Host = $host;      // sets GMAIL as the SMTP server
            $mailer->Port = $port;      // set the SMTP port for the GMAIL server

            $mailer->From = $smtp_from;
            $mailer->FromName = $smtp_fromname;
            if ($data['emailtype'] != 'internal') {
                $mailer->SMTPAuth = true;                // enable SMTP authentication
                $smtp_username = $data['username'];
                $smtp_password = $data['password'];
                $mailer->Username = $smtp_username;    // GMAIL username
                $mailer->Password = $smtp_password;   // GMAIL pw
            }
            return $mailer;
        } else {
           return false;
        }
        
    }
    
	public static function sendemail() 
	{
    	$mailer = new EMailer();
        $mailer->CharSet = 'UTF-8';
       
        $data = array();

        $request = self::find()->one();
        if (!empty($request)) {
            $data = $request;
            $host = $data['email_host'];
            $port = $data['port'];
            $smtp_from = $data['from_email'];
            $smtp_fromname = $data['from_name'];

            if (isset($request['sendmailvia']) && $request['sendmailvia'] == 'smtp')
                $mailer->IsSMTP();
            else if (isset($request['sendmailvia']) && $request['sendmailvia'] == 'mail')
                $mailer->IsMail();
            else
                $mailer->IsMail();

            if (isset($request['security']) && $request['security'] != 'none')
                $mailer->SMTPSecure = $request['security'];       // sets the prefix to the servier

				$mailer->Host = $host;      // sets GMAIL as the SMTP server
				$mailer->Port = $port;      // set the SMTP port for the GMAIL server

				$mailer->From = $smtp_from;
				$mailer->FromName = $smtp_fromname;
				if ($data['emailtype'] != 'internal') {
					$mailer->SMTPAuth = true;                // enable SMTP authentication
					$smtp_username = $data['username'];
					$smtp_password = $data['password'];
					$mailer->Username = $smtp_username;    // GMAIL username
					$mailer->Password = $smtp_password;   // GMAIL pw
				}
			return $mailer;
        } else {
			return false;
          
        }
        
    }
}
