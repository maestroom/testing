<?php

namespace app\models;

use Yii;
/**
 * This is the model class for table "{{%options}}".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $is_sub_new_task
 * @property integer $is_sub_com_task
 * @property integer $is_sub_new_production
 * @property integer $is_sub_past_due
 * @property integer $opt_posted_comment
 * @property integer $is_sub_self_assign
 * @property integer $is_new_todo_post
 * @property integer $is_completed_todos
 * @property integer $is_todos_assign_to_me
 * @property integer $is_servicetask_transists
 * @property integer $is_cancel
 * @property integer $is_uncanceled
 * @property integer $is_unassign
 * @property string $session_timeout
 * @property string $timezone_id
 * @property integer $changed_instructions
 * @property integer $pending_tasks
 * @property integer $approaching_case_budget_spend
 * @property integer $reached_case_budget_spend
 * @property integer $changed_casedetail
 * @property integer $is_reopen_project
 * @property integer $is_sub_com_service
 * @property integer $project_sort_display
 * @property string $set_loc
 *
 * @property User $user
 */
class Options extends \yii\db\ActiveRecord
{
	public $old_password,$usr_pass,$confirm_password;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%options}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'is_sub_new_task', 'is_sub_com_task', 'is_sub_past_due', 'opt_posted_comment', 'is_sub_self_assign', 'is_new_todo_post', 'is_completed_todos', 'is_todos_assign_to_me', 'is_servicetask_transists', 'is_cancel', 'is_uncanceled', 'is_unassign', 'changed_instructions', 'pending_tasks', 'approaching_case_budget_spend','approaching_project_due_date', 'reached_case_budget_spend', 'changed_casedetail'], 'required'],
        	[['confirm_password'],'required','when'=>function($model){ return $model->usr_pass != '';},'whenClient' => "function (attribute, value) {
		        return $('#options-usr_pass').val() != '';
		    }"],
		    [['confirm_password'], 'compare', 'compareAttribute'=>'usr_pass','message'=>'"Confirm Password" must match your "New Password".'],
            [['user_id', 'default_landing_page', 'project_sort_display', 'is_sub_new_task', 'is_sub_com_task', 'is_sub_new_production', 'is_sub_past_due', 'opt_posted_comment','opt_posted_summary_comment', 'is_sub_self_assign', 'is_new_todo_post', 'is_completed_todos', 'is_todos_assign_to_me', 'is_servicetask_transists', 'is_cancel', 'is_uncanceled', 'is_unassign', 'changed_instructions', 'pending_tasks', 'approaching_case_budget_spend', 'reached_case_budget_spend', 'changed_casedetail', 'is_reopen_project', 'is_sub_com_service','is_sub_new_media','is_sub_production_posted'], 'integer'],
            [['session_timeout', 'timezone_id', 'timezone_offset','set_loc'], 'string']
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
            'is_sub_new_task' => 'New Project Posted',
            'is_sub_com_task' => 'Completed Project',
            'is_sub_new_production' => 'New Project Production Posted',
            'is_sub_past_due' => 'Past Due Project',
            'opt_posted_comment' => 'New Comments Posted',
            'opt_posted_summary_comment' => 'New Summary Comments Posted',
            'is_sub_self_assign' => 'Task Assigned to Me',
            'is_new_todo_post' => 'New ToDo Posted',
            'is_completed_todos' => 'Completed ToDo',
            'is_todos_assign_to_me' => 'ToDo Assigned to Me',
            'is_servicetask_transists' => 'Task Transferred Location',
            'is_cancel' => 'Canceled Project',
            'is_uncanceled' => 'UnCanceled Project',
            'is_unassign' => 'Task UnAssigned from Me',
            'session_timeout' => 'Session Timeout',
            'timezone_id' => 'Timezone ID',
            'changed_instructions' => 'Changed Project Posted',
            'pending_tasks' => 'My Pending Tasks',
            'approaching_case_budget_spend' => 'Approaching Case Budget Spend',
            'approaching_project_due_date'=>'Approaching Project Due Date',
            'reached_case_budget_spend' => 'Reached Case Budget Spend',
            'changed_casedetail' => 'Changed Case Management Details',
            'is_reopen_project' => 'ReOpened Project',
            'is_sub_com_service' => 'Completed Service',
            'is_sub_new_media' => 'New Media Received',
            'usr_pass' =>'New Password',
            'project_sort_display'=>'Set Project Sort Display',
            'default_landing_page' => 'Set Default Landing Module',
            'timezone_offset' => 'Timezone Offset',
            'is_sub_production_posted'=> 'New Production Posted',
            'set_loc'=>'Set Location'
        ];
    }

    public function beforeSave($insert)
    {
    	if (parent::beforeSave($insert)) {

			$this->is_sub_new_task = ((isset($this->is_sub_new_task) && $this->is_sub_new_task == 'on' && !is_numeric($this->is_sub_new_task)) || (isset($this->is_sub_new_task) && $this->is_sub_new_task==1))?1:0;

			$this->is_sub_com_task = ((isset($this->is_sub_com_task) && $this->is_sub_com_task == 'on' && !is_numeric($this->is_sub_com_task)) || (isset($this->is_sub_com_task) && $this->is_sub_com_task==1))?1:0;
			$this->is_sub_new_production = ((isset($this->is_sub_new_production) && $this->is_sub_new_production == 'on' && !is_numeric($this->is_sub_new_production)) || (isset($this->is_sub_new_production) && $this->is_sub_new_production==1))?1:0;
			$this->is_sub_past_due = ((isset($this->is_sub_past_due) && $this->is_sub_past_due == 'on' && !is_numeric($this->is_sub_past_due)) || (isset($this->is_sub_past_due) && $this->is_sub_past_due==1))?1:0;
			$this->opt_posted_comment = ((isset($this->opt_posted_comment) && $this->opt_posted_comment == 'on' && !is_numeric($this->opt_posted_comment)) || (isset($this->opt_posted_comment) && $this->opt_posted_comment==1))?1:0;
            $this->opt_posted_summary_comment = ((isset($this->opt_posted_summary_comment) && $this->opt_posted_summary_comment == 'on' && !is_numeric($this->opt_posted_summary_comment)) || (isset($this->opt_posted_summary_comment) && $this->opt_posted_summary_comment==1))?1:0;
			$this->is_sub_self_assign = ((isset($this->is_sub_self_assign) && $this->is_sub_self_assign == 'on' && !is_numeric($this->is_sub_self_assign)) || (isset($this->is_sub_self_assign) && $this->is_sub_self_assign==1))?1:0;
			$this->is_new_todo_post = ((isset($this->is_new_todo_post) && $this->is_new_todo_post == 'on' && !is_numeric($this->is_new_todo_post)) || (isset($this->is_new_todo_post) && $this->is_new_todo_post==1))?1:0;
			$this->is_completed_todos = ((isset($this->is_completed_todos) && $this->is_completed_todos == 'on' && !is_numeric($this->is_completed_todos)) || (isset($this->is_completed_todos) && $this->is_completed_todos==1))?1:0;
		    $this->is_todos_assign_to_me = ((isset($this->is_todos_assign_to_me) && $this->is_todos_assign_to_me == 'on' && !is_numeric($this->is_todos_assign_to_me)) || (isset($this->is_todos_assign_to_me) && $this->is_todos_assign_to_me==1))?1:0;
			$this->is_servicetask_transists = ((isset($this->is_servicetask_transists) && $this->is_servicetask_transists == 'on' && !is_numeric($this->is_servicetask_transists)) || (isset($this->is_servicetask_transists) && $this->is_servicetask_transists==1))?1:0;
			$this->is_cancel = ((isset($this->is_cancel) && $this->is_cancel == 'on' && !is_numeric($this->is_cancel)) || (isset($this->is_cancel) && $this->is_cancel==1))?1:0;
			$this->is_uncanceled = ((isset($this->is_uncanceled) && $this->is_uncanceled == 'on' && !is_numeric($this->is_uncanceled)) || (isset($this->is_uncanceled) && $this->is_uncanceled==1))?1:0;
			$this->is_unassign = ((isset($this->is_unassign) && $this->is_unassign == 'on' && !is_numeric($this->is_unassign)) || (isset($this->is_unassign) && $this->is_unassign==1))?1:0;
			$this->changed_instructions = ((isset($this->changed_instructions) && $this->changed_instructions == 'on' && !is_numeric($this->changed_instructions)) || (isset($this->changed_instructions) && $this->changed_instructions==1))?1:0;
			$this->pending_tasks = ((isset($this->pending_tasks) && $this->pending_tasks == 'on' && !is_numeric($this->pending_tasks)) || (isset($this->pending_tasks) && $this->pending_tasks==1))?1:0;
			$this->approaching_case_budget_spend = ((isset($this->approaching_case_budget_spend) && $this->approaching_case_budget_spend == 'on' && !is_numeric($this->approaching_case_budget_spend)) || (isset($this->approaching_case_budget_spend) && $this->approaching_case_budget_spend==1))?1:0;
            $this->approaching_project_due_date = ((isset($this->approaching_project_due_date) && $this->approaching_project_due_date == 'on' && !is_numeric($this->approaching_project_due_date)) || (isset($this->approaching_project_due_date) && $this->approaching_project_due_date==1))?1:0;
			$this->reached_case_budget_spend = ((isset($this->reached_case_budget_spend) && $this->reached_case_budget_spend == 'on' && !is_numeric($this->reached_case_budget_spend)) || (isset($this->reached_case_budget_spend) && $this->reached_case_budget_spend==1))?1:0;
			$this->changed_casedetail = ((isset($this->changed_casedetail) && $this->changed_casedetail == 'on' && !is_numeric($this->changed_casedetail)) || (isset($this->changed_casedetail) && $this->changed_casedetail==1))?1:0;
			$this->is_reopen_project = ((isset($this->is_reopen_project) && $this->is_reopen_project == 'on' && !is_numeric($this->is_reopen_project)) || (isset($this->is_reopen_project) && $this->is_reopen_project==1))?1:0;
			$this->is_sub_com_service = ((isset($this->is_sub_com_service) && $this->is_sub_com_service == 'on' && !is_numeric($this->is_sub_com_service)) || (isset($this->is_sub_com_service) && $this->is_sub_com_service==1))?1:0;
			$this->is_sub_new_media = ((isset($this->is_sub_new_media) && $this->is_sub_new_media == 'on' && !is_numeric($this->is_sub_new_media)) || (isset($this->is_sub_new_media) && $this->is_sub_new_media==1))?1:0;
			$this->is_sub_production_posted = ((isset($this->is_sub_production_posted) && $this->is_sub_production_posted == 'on' && !is_numeric($this->is_sub_production_posted)) || (isset($this->is_sub_production_posted) && $this->is_sub_production_posted==1))?1:0;
            //$this->timezone_offset = $this->getOffsetOfTimeZone($this->timezone_id);
            if(isset($this->timezone_id) && $this->timezone_id!=""){
				$this->timezone_offset = $this->getOffsetOfTimeZone($this->timezone_id);
			}else{
				$this->timezone_id='America/New_York';
				$this->timezone_offset = $this->getOffsetOfTimeZone($this->timezone_id);
			}
		//	if($this->is_sub_self_assign=='on')$this->is_sub_self_assign=1;
		//	if($this->is_new_todo_post=='on')$this->is_new_todo_post=1;
		//	if($this->is_completed_todos=='on')$this->is_completed_todos=1;
		//	if($this->is_todos_assign_to_me=='on')$this->is_todos_assign_to_me=1;
		//	if($this->is_servicetask_transists=='on')$this->is_servicetask_transists=1;
		//	if($this->is_cancel=='on')$this->is_cancel=1;
		//	if($this->is_uncanceled=='on')$this->is_uncanceled=1;
		//	if($this->is_unassign=='on')$this->is_unassign=1;
		//	if($this->changed_instructions=='on')$this->changed_instructions=1;
		//	if($this->pending_tasks=='on')$this->pending_tasks=1;
		//	if($this->approaching_case_budget_spend=='on')$this->approaching_case_budget_spend=1;
		//	if($this->reached_case_budget_spend=='on')$this->reached_case_budget_spend=1;
		//	if($this->changed_casedetail=='on')$this->changed_casedetail=1;
		//	if($this->is_reopen_project=='on')$this->is_reopen_project=1;
		//	if($this->is_sub_com_service=='on')$this->is_sub_com_service=1;
		//	if($this->is_sub_new_media=='on')$this->is_sub_new_media=1;
		}
		return true;
    }
    /**
     * Get List of Supportted TimeZone
     */
	public function getTimezone() {
        $abbr = \DateTimeZone::listAbbreviations();
        $timezones_dont_want_to_display = array('CET' => 'CET', 'EET' => 'EET', 'EST' => 'EST', 'GB' => 'GB', 'GMT' => 'GMT', 'HST' => 'HST', 'MET' => 'MET', 'MST' => 'MST', 'NZ' => 'NZ', 'PRC' => 'PRC', 'ROC' => 'ROC', 'ROK' => 'ROK', 'UCT' => 'UCT', 'UTC' => 'UTC', 'WET' => 'WET');
        $options = array();
        //$optionsAr = array();
        foreach ($abbr as $section => $zones) {
            foreach ($zones as $zone) {
                if (!$zone['timezone_id']) {
                    continue;
                }
                if (isset($options[$zone['timezone_id']])) {
                    continue;
                }
								if($zone['timezone_id']=='Canada/East-Saskatchewan')
									continue;
                //if (strpos($zone['timezone_id'], 'GMT') !== false) {
                // ignore the plain GMT zones
                //continue;
                //}
                /*$offset = round($zone['offset'], 2) / 3600;
                $hours = floor($offset);
                $minutes = ($offset - $hours) * 60;
                $minutes = $minutes == 0 ? '00' : $minutes;
                // ignore the weird ones
                if (!in_array($minutes, array('00', '15', '30', '45'))) {
                    continue;
                }
                $sign = substr($hours, 0, 1) == '-' ? '-' : '+';
                $hours = abs($hours);*/

                $offset = $this->getOffsetOfTimeZone($zone['timezone_id']);
                $options[$zone['timezone_id']] = str_replace('_', ' ', $zone['timezone_id']) . " ($offset)";
                //$optionsAr[$zone['timezone_id']] = "UPDATE tbl_options SET timezone_offset = '$offset' WHERE timezone_id = '{$zone['timezone_id']}';";//$offset;
            }
        }
        //asort($optionsAr);
        asort($options);
        foreach ($options as $key => $val) {
            if (array_key_exists($key, $timezones_dont_want_to_display)) {
                unset($options[$key]);
                //unset($optionsAr[$key]);
            }
        }
        //echo "<pre>",implode('<br/>',$optionsAr);die;
        return $options;
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /* Convert one timezone to specified timezone */
    public function ConvertOneTzToAnotherTz($time, $currentTimezone, $timezoneRequired, $option = "")
    {
        $system_timezone = date_default_timezone_get();
        $local_timezone = $currentTimezone;
        date_default_timezone_set($local_timezone);
        $local = date("Y-m-d h:i:s A");

        date_default_timezone_set("GMT");
        $gmt = date("Y-m-d h:i:s A");

        $require_timezone = $timezoneRequired;
        date_default_timezone_set($require_timezone);
        $required = date("Y-m-d h:i:s A");

        date_default_timezone_set($system_timezone);

        $diff1 = (strtotime($gmt) - strtotime($local));
        $diff2 = (strtotime($required) - strtotime($gmt));


        $time = preg_replace('~\x{00a0}~u', ' ', $time);

        $date = new \DateTime($time);

        $date->modify("+$diff1 seconds");
        $date->modify("+$diff2 seconds");
        if ($option == "date")
            $timestamp = $date->format("m/d/Y");
        else if ($option == "time")
            $timestamp = $date->format("h:i A");
        else if ($option == "time2")
            $timestamp = $date->format("h:i");
        else if ($option == "comment")
            $timestamp = $date->format("M d, Y h:i A");
        else if ($option == "YMDHIS")
            $timestamp = $date->format("Y-m-d H:i:s");
        else if ($option == "YMDHIA")
            $timestamp = $date->format("Y-m-d h:i A");
        else if ($option == "HIS")
            $timestamp = $date->format("H:i:s");
        else if ($option == "HI")
            $timestamp = $date->format("H:i");
        else if ($option == "YMD")
            $timestamp = $date->format("Y-m-d");
        else if ($option == "MDY")
            $timestamp = $date->format("m/d/Y");
        else if ($option == "MDYHIS")
        	$timestamp = $date->format("m/d/Y H:i:s");
        else if ($option == "MDY_d")
            $timestamp = $date->format("m-d-Y");
        else if ($option == "DMY")
            $timestamp = $date->format("d m Y");
        else if ($option == "longdate")
            $timestamp = $date->format("j M Y");
        else if ($option == "requestdate")
            $timestamp = $date->format("n/d/Y");
        else if ($option == "turntime")
            $timestamp = $date->format("m/d/Y h:i A");
        else
            $timestamp = $date->format("m/d/Y h:i A");

        return $timestamp;
    }

    public function getOffsetOfCurrenttimeZone(){
    	$data = $_SESSION['options'];
        //$this::find()->where(["user_id" => Yii::$app->user->identity->id])->one();
    	//echo '<pre>';print_r($data);die;
    	if (!isset($_SESSION['usrTZ']) || $_SESSION['usrTZ'] == "")
			$_SESSION['usrTZ'] = $data->timezone_id;

        if($_SESSION['usrTZ'] == "")
        	$_SESSION['usrTZ'] = 'America/New_York';

        $time = new \DateTime(date('Y-m-d H:i:s'), new \DateTimeZone($_SESSION['usrTZ']));
        return $timezoneOffset = $time->format('P');
    }
    public function getOffsetOfTimeZone($TZ = 'UTC'){
    	//$data = $this::find(array('condition' => "user_id='" .Yii::$app->user->identity->id. "'"));
    	$time = new \DateTime(date('Y-m-d H:i:s'), new \DateTimeZone($TZ));
        return $timezoneOffset = $time->format('P');
    }
}
