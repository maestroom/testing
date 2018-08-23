<?php

namespace app\models;


use Yii;
use yii\base\Model;
use app\models\Settings;
use app\models\User;

/**
 * LoginForm is the model behind the login form.
 */
class Report extends Model
{
    public $start_date;
    public $end_date;
    public $datedropdown;
    public $chkclientcases;
    public $checkcases_all;
    public $selectall;
    public $chkprojectstatus;
    public $statusall;
    public $chartgroupcriteria;




    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
          
        ];
    }
    public function attributeLabels()
    {
    	return [
    			
    	];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    
}
