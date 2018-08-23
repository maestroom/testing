<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\HtmlPurifier;


/**
 * This is the model class for table "tbl_email_cron".
 *
 * @property integer $id
 * @property integer $template_id
 * @property string $email_alert
 * @property integer $user_id
 * @property string $template_detail
 * @property string $created
 */
class EmailCron extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_email_cron';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['template_id', 'email_alert', 'template_detail'], 'required'],
            [['template_id', 'user_id'], 'integer'],
            [['template_detail'], 'string'],
            [['created'], 'safe'],
            [['email_alert'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'template_id' => 'Template ID',
            'email_alert' => 'Email Alert',
            'user_id' => 'User ID',
            'template_detail' => 'Template Detail',
            'created' => 'Created'
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if($this->isNewRecord){
                $this->created = date('Y-m-d H:i:s');
                $this->user_id = Yii::$app->user->identity->id;
            }
        }
        return true;
    }

    public static function saveBackgroundEmail($template_id,$email_alert,$data=array()){
        $model = new EmailCron();
        $model->template_id=$template_id;
        $model->email_alert=$email_alert;
        $model->template_detail=json_encode($data);
        $model->save(false);
        return;
    }

    public static function sendQueueEmail(){
        $data=EmailCron::find()->all();
        if(!empty($data)){
            foreach($data as $key=>$val){
                $email_data = json_decode (htmlentities ($val->template_detail,true), true);
                //echo "<pre>",print_r($email_data),"</pre>";die;
                //$email_data=json_decode($val->template_detail,true);
                if(trim($val->email_alert)=='opt_posted_summary_comment') {
                    SettingsEmail::sendSummaryCommentMailBG($val->user_id,$email_data);
                }else if(trim($val->email_alert)=='opt_posted_comment') {
                    SettingsEmail::sendCommentMailBG($val->user_id,$email_data);
                }else {
                    SettingsEmail::sendEmailBG($val->user_id,$val->template_id,$val->email_alert,$email_data);
                }
                EmailCron::deleteAll('id='.$val->id);
            }
        }
		return;
    }
    public static function sendApproachingPastDueEmail(){
        $template_id=25;
        $email_alert='approaching_project_due_date';
        $optionUsers=ArrayHelper::map(Options::find()->where([ $email_alert => 1])->select(['user_id',"(CASE WHEN timezone_id = '' THEN 'UTC' ELSE timezone_id END) as timezone_id "])->all(),'user_id','timezone_id');
        $template_info = SettingsEmail::find()->where(['email_sort'=>$template_id])->one();
        $email_recipient=((isset($template_info->email_custom_recipients) && $template_info->email_custom_recipients!="")?$template_info->email_custom_recipients:$template_info->email_recipients);
        $is_case=false;
        $is_team=false;
        if(in_array(1,explode(",",$email_recipient))){
            $is_case=true;        
        }
        if(in_array(2,explode(",",$email_recipient))){
            $is_team=true;        
        }
        $alreadysenduids=array();
        $sendmail_emailids_by_task=array();
        if(!empty($optionUsers)) {
            foreach($optionUsers as $uid=>$tz) {
                @date_default_timezone_set($tz);
                $_SESSION['usrTZ'] = $tz;
                $email_id=Yii::$app->db->createCommand("SELECT usr_email from tbl_user where id=".$uid)->queryScalar();
                $time = new \DateTime(date('Y-m-d H:i'), new \DateTimeZone($tz));
                $timezoneOffset = $time->format('P');
                $currentdatetime = $time->format('Y-m-d H:i');
                if (Yii::$app->db->driverName == 'mysql') {
                    $data_query_sql = "SELECT getDueDateTimeByUsersTimezone('{$timezoneOffset}',tbl_task_instruct.task_duedate,tbl_task_instruct.task_timedue,'%Y-%m-%d %H:%i')";
                } else {
                    $data_query_sql = "SELECT [dbo].getDueDateTimeByUsersTimezone('{$timezoneOffset}',tbl_task_instruct.task_duedate,tbl_task_instruct.task_timedue,'%Y-%m-%d %H:%i')";
                }
                if($is_case) {
                    $sql="SELECT DISTINCT tbl_client_case.id FROM tbl_client_case 
		INNER JOIN tbl_project_security ON tbl_client_case.id = tbl_project_security.client_case_id 
		WHERE tbl_project_security.client_id!=0 AND client_case_id !=0 AND team_id=0 AND user_id = ".$uid;
                    $task_sql="SELECT tbl_tasks.*, ($data_query_sql) as task_date_time FROM tbl_tasks 
                    INNER JOIN tbl_task_instruct ON tbl_tasks.id = tbl_task_instruct.task_id
                     WHERE ((tbl_tasks.task_status IN (0,1,3) AND tbl_tasks.task_closed =0 AND tbl_tasks.task_cancel =0) 
 AND (tbl_tasks.client_case_id IN ($sql) )) AND (tbl_task_instruct.isactive=1)";
                    $tasks = Yii::$app->db->createCommand($task_sql)->queryAll();
                    if(!empty($tasks)) {
                        foreach($tasks as $task_data){
                            if(strtotime($task_data['task_date_time']) > strtotime($currentdatetime)) {
                                $hourdiff = round((strtotime($task_data['task_date_time']) - strtotime($currentdatetime))/3600);
                                $minutes = round((strtotime($task_data['task_date_time']) - strtotime($currentdatetime)) / 60);
                                if($hourdiff == 24 && $minutes == 1440) 
                                {
                                    $alreadysenduids[$task_data['id']."_".$uid]=$task_data['id']."_".$uid;
                                    $task_data['project_id']=$task_data['id'];
                                    $task_data['case_id']=$task_data['client_case_id'];
                                    SettingsEmail::sendPastDueEmail($uid,$template_id,$email_alert,$task_data);
                                    /*if(isset($sendmail_emailids_by_task[$task_data['id']])) {
                                        if(!in_array($email_id,$sendmail_emailids_by_task[$task_data['id']])) {
                                            $email_ids=SettingsEmail::sendPastDueEmail($uid,$template_id,$email_alert,$task_data);
                                            $sendmail_emailids_by_task[$task_data['id']]=array_merge($sendmail_emailids_by_task[$task_data['id']],$email_ids);
                                        }
                                    }else{
                                        $email_ids=SettingsEmail::sendPastDueEmail($uid,$template_id,$email_alert,$task_data);
                                        $sendmail_emailids_by_task[$task_data['id']]=$email_ids;
                                    }*/
                                }
                            }
                        }
                    }
                }
                if($is_team){
                    $duplicate_task_id=array();
                    $sql_loc="select team_loc from tbl_project_security where user_id = ".$uid." AND team_id !=0 ";
                    $sql="SELECT DISTINCT tbl_team.id FROM tbl_team 
		INNER JOIN tbl_project_security ON tbl_team.id = tbl_project_security.team_id 
		WHERE tbl_project_security.client_id=0 AND client_case_id =0 AND team_id!=0 AND user_id = ".$uid;
                    $task_sql="SELECT tbl_tasks.*, ($data_query_sql) as task_date_time,tbl_tasks_teams.team_id,tbl_tasks_teams.team_loc FROM tbl_tasks 
                    INNER JOIN tbl_task_instruct ON tbl_tasks.id = tbl_task_instruct.task_id
                    INNER JOIN tbl_tasks_teams ON tbl_tasks_teams.task_id=tbl_tasks.id
                     WHERE ((tbl_tasks.task_status IN (0,1,3) AND tbl_tasks.task_closed =0 AND tbl_tasks.task_cancel =0) 
                    AND ( tbl_tasks_teams.team_id IN ($sql) AND tbl_tasks_teams.team_loc IN ($sql_loc) )) AND (tbl_task_instruct.isactive=1)";
                    $tasks = Yii::$app->db->createCommand($task_sql)->queryAll();
                    if(!empty($tasks)) {
                       foreach($tasks as $task_data) {
                           
                            if(!in_array($task_data['id']."_".$uid,$alreadysenduids)) {
                                if(!in_array($task_data['id'],$duplicate_task_id)) {
                                        if(strtotime($task_data['task_date_time']) > strtotime($currentdatetime)) {
                                            $hourdiff = round((strtotime($task_data['task_date_time']) - strtotime($currentdatetime))/3600);
                                            $minutes = round((strtotime($task_data['task_date_time']) - strtotime($currentdatetime)) / 60);
                                            if($hourdiff == 24 && $minutes == 1440) 
                                            {
                                                $alreadysenduids[$task_data['id']."_".$uid]=$task_data['id']."_".$uid;
                                                $task_data['project_id']=$task_data['id'];
                                                SettingsEmail::sendPastDueEmail($uid,$template_id,$email_alert,$task_data);
                                                /*if(isset($sendmail_emailids_by_task[$task_data['id']])) {
                                                    if(!in_array($email_id,$sendmail_emailids_by_task[$task_data['id']])) {
                                                    $email_ids=SettingsEmail::sendPastDueEmail($uid,$template_id,$email_alert,$task_data);
                                                    $sendmail_emailids_by_task[$task_data['id']]=array_merge($sendmail_emailids_by_task[$task_data['id']],$email_ids);
                                                    }
                                                }else{
                                                    $email_ids=SettingsEmail::sendPastDueEmail($uid,$template_id,$email_alert,$task_data);
                                                    $sendmail_emailids_by_task[$task_data['id']]=$email_ids;
                                                }*/
                                            }
                                        }
                                }

                            }
                            $duplicate_task_id[$task_data['id']]=$task_data['id'];
                       }     
                    }
                }
            }
        }
        return;
    }
    public static function sendPastDueEmail(){
        $template_id=10;
        $email_alert='is_sub_past_due';
        $optionUsers=ArrayHelper::map(Options::find()->where([ $email_alert => 1])->select(['user_id',"(CASE WHEN timezone_id = '' THEN 'UTC' ELSE timezone_id END) as timezone_id "])->all(),'user_id','timezone_id');
        $template_info = SettingsEmail::find()->where(['email_sort'=>$template_id])->one();
        $email_recipient=((isset($template_info->email_custom_recipients) && $template_info->email_custom_recipients!="")?$template_info->email_custom_recipients:$template_info->email_recipients);
        $is_case=false;
        $is_team=false;
        if(in_array(1,explode(",",$email_recipient))){
            $is_case=true;        
        }
        if(in_array(2,explode(",",$email_recipient))){
            $is_team=true;        
        }
        $alreadysenduids=array();
        $sendmail_emailids_by_task=array();
        if(!empty($optionUsers)) {
            foreach($optionUsers as $uid=>$tz) {
                $_SESSION['usrTZ'] = $tz;
				$email_id=Yii::$app->db->createCommand("SELECT usr_email from tbl_user where id=".$uid)->queryScalar();
                $time = new \DateTime(date('Y-m-d H:i'), new \DateTimeZone($tz));
                $timezoneOffset = $time->format('P');
                $currentdatetime = $time->format('Y-m-d H:i');
                $currentdatetimewithsec = $time->format('Y-m-d H:i:s');
                $currentdate = $time->format('Y-m-d');
                if (Yii::$app->db->driverName == 'mysql') {
                    $data_query_pastdue = "getDueDateTimeByUsersTimezone('{$timezoneOffset}',tbl_task_instruct.task_duedate,tbl_task_instruct.task_timedue,'%Y-%m-%d %H:%i:%s')";
                } else {
                    $data_query_pastdue = "[dbo].getDueDateTimeByUsersTimezone('{$timezoneOffset}',tbl_task_instruct.task_duedate,tbl_task_instruct.task_timedue,'%Y-%m-%d %H:%i:%s')";
                }
                if (Yii::$app->db->driverName == 'mysql') {
                    $data_query_sql = "SELECT getDueDateTimeByUsersTimezone('{$timezoneOffset}',tbl_task_instruct.task_duedate,tbl_task_instruct.task_timedue,'%Y-%m-%d %H:%i')";
                } else {
                    $data_query_sql = "SELECT [dbo].getDueDateTimeByUsersTimezone('{$timezoneOffset}',tbl_task_instruct.task_duedate,tbl_task_instruct.task_timedue,'%Y-%m-%d %H:%i')";
                }
                $sqlpastdue="SELECT COUNT(*) as totalitems
                FROM tbl_task_instruct
                INNER JOIN tbl_tasks as tasks ON tasks.id = task_id
                INNER JOIN (SELECT tbl_task_instruct.id,tbl_task_instruct.task_id, $data_query_pastdue as task_date_time FROM tbl_task_instruct WHERE isactive=1 AND task_id IS NOT NULL AND task_id<>'') as A ON tbl_task_instruct.id = A.id
                WHERE A.task_id = tbl_tasks.id
                ";
                $todaysdate = (new Options)->ConvertOneTzToAnotherTz(date('Y-m-d H:i:s'), 'UTC', $tz, "YMDHIS");
                if (Yii::$app->db->driverName == 'mysql'){
                    $task_completedate = " CONVERT_TZ(tasks.task_complete_date,'+00:00','".$timezoneOffset."') ";
                    $sqlpastdue.=" AND A.task_date_time < CASE WHEN tasks.task_complete_date!='0000-00-00 00:00:00' AND tasks.task_complete_date!='' AND tasks.task_status = 4 THEN A.task_date_time ELSE '$todaysdate' END";
                }else{
                    $task_completedate = " CAST(switchoffset(todatetimeoffset(tasks.task_complete_date, '+00:00'), '$timezoneOffset') as DATETIME) ";
                    $sqlpastdue.=" AND A.task_date_time < CASE WHEN CAST(tasks.task_complete_date as varchar)!='0000-00-00 00:00:00' AND CAST(tasks.task_complete_date as varchar) IS NOT NULL AND tasks.task_status = 4 THEN A.task_date_time ELSE '$todaysdate' END";
                }
                if($is_case) {
                    $sql="SELECT DISTINCT tbl_client_case.id FROM tbl_client_case 
		INNER JOIN tbl_project_security ON tbl_client_case.id = tbl_project_security.client_case_id 
		WHERE tbl_project_security.client_id!=0 AND client_case_id !=0 AND team_id=0 AND user_id = ".$uid;
                    $task_sql="SELECT tbl_tasks.*, ($sqlpastdue) as is_pastdue, ($data_query_pastdue) as task_date_time FROM tbl_tasks 
                    INNER JOIN tbl_task_instruct ON tbl_tasks.id = tbl_task_instruct.task_id
                     WHERE ((tbl_tasks.task_status IN (0,1,3) AND tbl_tasks.task_closed =0 AND tbl_tasks.task_cancel =0) 
 AND (tbl_tasks.client_case_id IN ($sql) )) AND (tbl_task_instruct.isactive=1) AND tbl_tasks.id NOT IN (SELECT DISTINCT task_id FROM tbl_project_pastdue WHERE user_id=$uid)";
                    $tasks = Yii::$app->db->createCommand($task_sql)->queryAll();
                    if(!empty($tasks)) {
                        foreach($tasks as $task_data) {
                            $duedatetime = new \DateTime(date($task_data['task_date_time']), new \DateTimeZone($tz));
                            $duedate = $duedatetime->format('Y-m-d');
                            if($task_data['is_pastdue'] == 1 && $duedate == $currentdate) {
                                    $alreadysenduids[$task_data['id']."_".$uid]=$task_data['id']."_".$uid;
                                    $task_data['project_id']=$task_data['id'];
                                    $task_data['case_id']=$task_data['client_case_id'];
                                    $email_ids=SettingsEmail::sendPastDueEmail($uid,$template_id,$email_alert,$task_data);
                                    if(!empty($email_ids)) {
                                        $email="'".implode("', '", $email_ids)."'";
                                        $insert_sql="INSERT INTO tbl_project_pastdue(task_id, user_id, created) SELECT ".$task_data['id']." as task_id,id, '".$date."' FROM tbl_user where usr_email IN ($email)";
                                        Yii::$app->db->createCommand($insert_sql)->execute();
                                    }
                                    /*if(isset($sendmail_emailids_by_task[$task_data['id']])) {
                                        if(!in_array($email_id,$sendmail_emailids_by_task[$task_data['id']])) {
                                            $email_ids=SettingsEmail::sendPastDueEmail($uid,$template_id,$email_alert,$task_data);
                                            $date=date('Y-m-d H:i:s');
                                            if(!empty($email_ids)){
                                                $email="'".implode("', '", $email_ids)."'";
                                                $insert_sql="INSERT INTO tbl_project_pastdue(task_id, user_id, created) SELECT ".$task_data['id']." as task_id,id, '".$date."' FROM tbl_user where usr_email IN ($email)";
                                                Yii::$app->db->createCommand($insert_sql)->execute();
                                            }
                                            $sendmail_emailids_by_task[$task_data['id']]=array_merge($sendmail_emailids_by_task[$task_data['id']],$email_ids);
                                        }
                                    }else{
                                        $email_ids=SettingsEmail::sendPastDueEmail($uid,$template_id,$email_alert,$task_data);
                                        $date=date('Y-m-d H:i:s');
                                        if(!empty($email_ids)){
                                            $email="'".implode("', '", $email_ids)."'";
                                            $insert_sql="INSERT INTO tbl_project_pastdue(task_id, user_id, created) SELECT ".$task_data['id']." as task_id,id, '".$date."' FROM tbl_user where usr_email IN ($email)";
                                            Yii::$app->db->createCommand($insert_sql)->execute();
                                        }
                                        $sendmail_emailids_by_task[$task_data['id']]=$email_ids;
                                    }*/
                                
                            }
                        }
                    }
                }
                if($is_team){
                    $duplicate_task_id=array();
                    $sql_loc="select team_loc from tbl_project_security where user_id = ".$uid." AND team_id !=0 ";
                    $sql="SELECT DISTINCT tbl_team.id FROM tbl_team 
		INNER JOIN tbl_project_security ON tbl_team.id = tbl_project_security.team_id 
		WHERE tbl_project_security.client_id=0 AND client_case_id =0 AND team_id!=0 AND user_id = ".$uid;
                    $task_sql="SELECT tbl_tasks.*, ($sqlpastdue) as is_pastdue,($data_query_pastdue) as task_date_time, tbl_tasks_teams.team_id,tbl_tasks_teams.team_loc FROM tbl_tasks 
                    INNER JOIN tbl_task_instruct ON tbl_tasks.id = tbl_task_instruct.task_id
                    INNER JOIN tbl_tasks_teams ON tbl_tasks_teams.task_id=tbl_tasks.id
                     WHERE ((tbl_tasks.task_status IN (0,1,3) AND tbl_tasks.task_closed =0 AND tbl_tasks.task_cancel =0) 
                    AND ( tbl_tasks_teams.team_id IN ($sql) AND tbl_tasks_teams.team_loc IN ($sql_loc) )) AND (tbl_task_instruct.isactive=1) AND tbl_tasks.id NOT IN (SELECT DISTINCT task_id FROM tbl_project_pastdue WHERE user_id=$uid)";
                    $tasks = Yii::$app->db->createCommand($task_sql)->queryAll();
                    if(!empty($tasks)) {
                        foreach($tasks as $task_data) {
                            $duedatetime = new \DateTime(date($task_data['task_date_time']), new \DateTimeZone($tz));
                            $duedate = $duedatetime->format('Y-m-d');
                            if($task_data['is_pastdue'] == 1 && $duedate == $currentdate) {
                            if(!in_array($task_data['id']."_".$uid,$alreadysenduids)) {
                                if(!in_array($task_data['id'],$duplicate_task_id)) {
                                                $alreadysenduids[$task_data['id']."_".$uid]=$task_data['id']."_".$uid;
                                                $task_data['project_id']=$task_data['id'];
                                                $email_ids=SettingsEmail::sendPastDueEmail($uid,$template_id,$email_alert,$task_data);
                                                if(!empty($email_ids)) {
                                                    $email="'".implode("', '", $email_ids)."'";
                                                    $insert_sql="INSERT INTO tbl_project_pastdue(task_id, user_id, created) SELECT ".$task_data['id']." as task_id,id, '".$date."' FROM tbl_user where usr_email IN ($email)";
                                                    Yii::$app->db->createCommand($insert_sql)->execute();
                                                }
                                                /*if(isset($sendmail_emailids_by_task[$task_data['id']])) {
                                                    if(!in_array($email_id,$sendmail_emailids_by_task[$task_data['id']])) {
                                                    $email_ids=SettingsEmail::sendPastDueEmail($uid,$template_id,$email_alert,$task_data);
                                                    $date=date('Y-m-d H:i:s');
                                                    if(!empty($email_ids)){
                                                        $email="'".implode("', '", $email_ids)."'";
                                                        $insert_sql="INSERT INTO tbl_project_pastdue(task_id, user_id, created) SELECT ".$task_data['id']." as task_id,id, '".$date."' FROM tbl_user where usr_email IN ($email)";
                                                        Yii::$app->db->createCommand($insert_sql)->execute();
                                                    }
                                                    $sendmail_emailids_by_task[$task_data['id']]=array_merge($sendmail_emailids_by_task[$task_data['id']],$email_ids);
                                                    }
                                                }else{
                                                    $email_ids=SettingsEmail::sendPastDueEmail($uid,$template_id,$email_alert,$task_data);
                                                    $date=date('Y-m-d H:i:s');
                                                    if(!empty($email_ids)){
                                                       $email="'".implode("', '", $email_ids)."'";
                                                       $insert_sql="INSERT INTO tbl_project_pastdue(task_id, user_id, created) SELECT ".$task_data['id']." as task_id,id, '".$date."' FROM tbl_user where usr_email IN ($email)";
                                                       Yii::$app->db->createCommand($insert_sql)->execute();
                                                    }
                                                    $sendmail_emailids_by_task[$task_data['id']]=$email_ids;
                                                }*/
                                            
                                        }
                                }

                            }
                            $duplicate_task_id[$task_data['id']]=$task_data['id'];
                       }     
                    }
                }
            }
        }
        return;
    }
}