<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%user_log}}".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $login
 * @property string $logout
 * @property string $ses_duration
 */
class UserLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_log}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['user_id'], 'integer'],
            [['login', 'logout'], 'safe'],
            [['ses_duration'], 'string'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'login' => 'Login',
            'logout' => 'Logout',
            'ses_duration' => 'Ses Duration',
        ];
    }
    public function dateDiff($start,$end=false)
	{
		$return = array();
		 
		try {
			$start = new \DateTime($start);
			$end = new \DateTime($end);
			$form = $start->diff($end);
		} catch (Exception $e){
			return $e->getMessage();
		}
		 
		$display = array('y'=>'year',
				'm'=>'month',
				'd'=>'day',
				'h'=>'hour',
				'i'=>'minute',
				's'=>'second');
		foreach($display as $key => $value){
			if($form->$key > 0){
				$return[] = $form->$key.' '.($form->$key > 1 ? $value.'s' : $value);
			}
		}
		 
		return implode($return, ', ');
	}

}
