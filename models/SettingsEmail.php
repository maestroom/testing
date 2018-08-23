<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\db\Query;
use app\models\Options;
use app\models\User;
use app\models\Servicetask;
use app\models\ProjectSecurity;
use app\models\TasksUnitsTodos;
use app\models\Tasks;
use app\models\Unit;
use app\models\TaskInstruct;
use app\models\InvoiceFinal;
use app\models\FormInstructionValues;
use app\models\EvidenceProductionBates;
use app\models\TasksUnitsData;
use app\models\FormBuilder;

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
 * @property string $is_instruction_form_field
 * @property string $is_data_form_field
 */
class SettingsEmail extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%settings_email}}';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['email_name', 'email_header', 'email_subject', 'email_body', 'email_recipients', 'email_custom_subject', 'email_custom_body', 'email_custom_recipients', 'email_caserole', 'email_teamservice', 'bcc_email_recipients'], 'string'],
            [['email_sort'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
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
            'is_instruction_form_field' => 'Is Instruction Form Field',
            'is_data_form_field' => 'Is Data Form Field'
        ];
    }

    /**
     * sendEmail function used to send Comment  mail only due to special logic in comment
     * @template_id = email_sort
     * @email_alert = field from tbl_options
     * @data = used to replace with template field.
     */
    public function sendSummaryCommentMail($data = array(), $template_id = 24, $email_alert = 'opt_posted_summary_comment') {
        // echo "<pre>"; print_r($data); exit;
        $notinemail_ids = array();
        if (isset($data['notinEmail_ids']) && !empty($data['notinEmail_ids'])) { //code to banned same email id
            $notinemail_ids = $data['notinEmail_ids'];
        }
        $email_idsss = array();
        $optionUsers = array(0);
        $optionData = Options::find()->select('user_id')->where($email_alert . '=1')->all();

        if (!empty($optionData)) { //chk first system has at least one user who subscribe to particular alert type
            $team_id = 0;
            $user_info = Yii::$app->user->identity;
            $data['user'] = $user_info->usr_first_name . " " . $user_info->usr_lastname;
            $service_info = array();
            if (isset($data['comment_id'])) {
                $comment_info = SummaryComment::findOne($data['comment_id']);
                $data['comment'] = Html::encode($comment_info->comment);
                $data['comment_posted_by'] = $comment_info->createdUser->usr_first_name . " " . $comment_info->createdUser->usr_lastname;
            }

            foreach ($optionData as $optData) {
                $optionUsers[$optData->user_id] = $optData->user_id;
            }

            if (isset($template_id) && $template_id != 0) {
                $template_info = self::find()->where(['email_sort' => $template_id])->one();
                $final_users = array();
                $case_users = array();
                $team_users = array();
                $email_recipient = ((isset($template_info->email_custom_recipients) && $template_info->email_custom_recipients != "") ? $template_info->email_custom_recipients : $template_info->email_recipients);
                if (in_array(1, explode(",", $email_recipient))) { // All Case members project was submitted under
                    if (isset($data['case_id']) && $data['case_id'] != 0) {
                        $options = array();
                        $options['case'] = "ProjectWasSubmittedTo";
                        $options['case_id'] = $data['case_id'];
                        $case_users = self::getFinalMailReceipents($email_alert, $options, $optionUsers);
                    }
                }
                if (in_array(2, explode(",", $email_recipient))) { // All My Team members project was submitted to
                    if (isset($data['team_id']) && $data['team_id'] != 0) {
                        $options = array();
                        $options['team'] = "ProjectWasSubmittedTo";
                        $options['team_id'] = $data['team_id'];
                        $options['team_loc'] = $data['team_loc'];
                        $team_users = self::getFinalMailReceipents($email_alert, $options, $optionUsers);
                    }
                }
                $final_users = array_merge($case_users, $team_users);

                if (!empty($final_users)) {
                    $email_ids = array();
                    /* $option_data=Options::find()->where($email_alert.'=1 AND user_id IN ('.implode(",",$final_users).')')->all();

                      if(!empty($option_data))
                      {
                      foreach ($option_data as $opdata){
                      if(isset($opdata->user->usr_email) && $opdata->user->usr_email!="")
                      $email_ids[$opdata->user->usr_email]=$opdata->user->usr_email;
                      }
                      } */
                    if (!empty($final_users)) {
                        foreach ($final_users as $email => $fullname) {
                            $email_ids[$email] = $email;
                            if ($data['user_id'] == "") {
                                $data['user_id'] = $fullname;
                            } else {
                                $data['user_id'] .= ", " . $fullname;
                            }
                        }
                    }
                    if (isset($data['case_id']) && $data['case_id'] != 0) {
                        $caseinfo = ClientCase::findOne($data['case_id']);
                        $data['case_name'] = $caseinfo->case_name;
                        if (!isset($data['client_id'])) {
                            $data['client_name'] = $caseinfo->client->client_name;
                        }
                        $data['team'] = '(No Information Available)';
                    }
                    if (isset($data['team_id']) && $data['team_id'] != 0) {
                        $teaminfo = Team::findOne($data['team_id']);
                        $data['team'] = $teaminfo->team_name;
                        $data['case_name'] = $data['client_name'] = '(No Information Available)';
                    }
                    if (isset($data['team_loc'])) {
                        $teamlocinfo = TeamlocationMaster::findOne($data['team_loc']);
                        $data['team_location'] = $teamlocinfo->team_location_name;
                    }
                    if (isset($email_ids) && !empty($email_ids)) {
                        $mailer = Emailsettings::sendemail();
                        // if object mailer found then following code executes
                        if (!empty($mailer)) {
                            $get_all_fields = ArrayHelper::map(SettingsEmailFields::find()->select('display_name')->all(), 'display_name', 'display_name');
                            foreach ($email_ids as $email) {
                                if (!in_array($email, $notinemail_ids)) { // code to banned same email id
                                    $mailer->AddAddress(trim($email));
                                    $email_id_array[$email] = trim($email);
                                }
                            }
                            if (!empty($template_info->bcc_email_recipients)) {
                                $bccEmails = explode(';', $template_info->bcc_email_recipients);
                                foreach ($bccEmails as $singleEmail) {
                                    $mailer->AddBCC(trim($singleEmail));
                                }
                            }
                            $main_body = ((isset($template_info->email_custom_body) && $template_info->email_custom_body != "") ? nl2br(html_entity_decode($template_info->email_custom_body)) : nl2br(html_entity_decode($template_info->email_body)));
                            $main_subject = ((isset($template_info->email_custom_body) && $template_info->email_custom_subject != "") ? $template_info->email_custom_subject : $template_info->email_subject);
                            foreach ($get_all_fields as $field) {
                                $d_field = str_replace(array("[", "]"), "", $field);
                                if (isset($data[$d_field]) && $data[$d_field] != '') {
                                    $dataContent = $data[$d_field];
                                } else {
                                    $dataContent = '(No Information Available)';
                                }
                                if (strpos($main_body, $field)) {
                                    $main_body = str_replace($field, nl2br(html_entity_decode($dataContent)), $main_body);
                                }
                                if (strpos($main_subject, $field)) {
                                    $main_subject = str_replace($field, nl2br(html_entity_decode($dataContent)), $main_subject);
                                }
                            }
                            if (isset($data['team'])) {
                                $main_body = str_replace("[team]", nl2br(html_entity_decode($data['team'])), $main_body);
                            }
                            if (isset($data['case_name'])) {
                                $main_body = str_replace("[case_name]", nl2br(html_entity_decode($data['case_name'])), $main_body);
                            }
                            if (isset($data['case_name'])) {
                                $main_body = str_replace("[case_name]", nl2br(html_entity_decode($data['case_name'])), $main_body);
                            }
                            $body_header = $template_info->email_header;
                            $html = \Yii::$app->view->renderFile('@app/views/layouts/email/emailtemplate.php', ['subject' => $main_subject, 'body' => $main_body, 'body_header' => $body_header], null);
                            //echo "<pre>"; print_r($data);
                            //print_r($mailer);
                            //echo $html;die;
                            $mailer->Subject = $main_subject;
                            $mailer->IsHTML(true);
                            $mailer->Body = $html;
                            if ($_SERVER['HTTP_HOST'] != '192.168.0.129' && $_SERVER['HTTP_HOST'] != 'localhost') {
                                $mailer->Send();
                            }
                        }
                        /** End Send * */
                    }
                    /* Sending Email to Recipients as per system settings */
                }
            }
        }
    }

    /**
     * sendEmail function used to send Comment  mail only due to special logic in comment
     * @template_id = email_sort
     * @email_alert = field from tbl_options
     * @data = used to replace with template field.
     */
    public function sendCommentMail($data = array(), $template_id = 12, $email_alert = 'opt_posted_comment') {
        //   echo "<pre>"; print_r($data); exit;
        $notinemail_ids = array();
        if (isset($data['notinEmail_ids']) && !empty($data['notinEmail_ids'])) { //code to banned same email id
            $notinemail_ids = $data['notinEmail_ids'];
        }
        $email_idsss = array();
        $optionUsers = array(0);
        $optionData = Options::find()->select('user_id')->where($email_alert . '=1')->all();
        //echo "<pre>",print_r($optionData),"</pre>";die;
        if (!empty($optionData)) { //chk first system has at least one user who subscribe to particular alert type
            $team_id = 0;
            $user_info = Yii::$app->user->identity;
            $data['user'] = $user_info->usr_first_name . " " . $user_info->usr_lastname;
            $service_info = array();
            if (isset($data['service_id'])) {
                $service_info = Servicetask::findOne($data['service_id']);
                $data['todo_submitted_by_services'] = $service_info->teamservice->service_name . " - " . $service_info->service_task;
            }
            if (isset($data['servicetask_id'])) {
                $teamLocation = ArrayHelper::map(TeamlocationMaster::find()->select(['id', 'team_location_name'])->where('remove=0')->all(), 'id', 'team_location_name');
                $servicetask_data = Servicetask::findOne($data['servicetask_id']);
                $team_id = $servicetask_data->teamId;
                $data['team_service_task'] = $servicetask_data->service_task;
                $data['previous_team_location'] = $teamLocation[$data['previous_tl']];
                $data['team_location'] = $teamLocation[$data['tl']];
            }
            if (isset($data['todo_id'])) {
                $tododata = TasksUnitsTodos::findOne($data['todo_id']);
                $data['todo'] = Html::decode($tododata->todo);
                $data['todo_submitted_by'] = $tododata->createdUser->usr_first_name . " " . $tododata->createdUser->usr_lastname;
            }
            if (isset($data['comment_id'])) {
                $comment_info = Comments::findOne($data['comment_id']);
                $data['comment'] = Html::encode($comment_info->comment);
                $data['comment_posted_by'] = $comment_info->createdUser->usr_first_name . " " . $comment_info->createdUser->usr_lastname;
            }

            foreach ($optionData as $optData) {
                $optionUsers[$optData->user_id] = $optData->user_id;
            }

            if (isset($template_id) && $template_id != 0) {
                $template_info = self::find()->where(['email_sort' => $template_id])->one();
                $final_users = array();
                $case_users = array();
                $team_users = array();
                $team_users_assigned = array();
                $team_users_task_assigned = array();
                $team_users_todo_assigned = array();
                $team_users_task_transferred_to = array();

                /* get Email Recipients */
                if (isset($data['parent_id']))
                    $data['comment_id'] = $data['parent_id'];

                $final_users = $this->getAllCaseMembersANDTeamMember($data['comment_id'], implode(",", $optionUsers));
                /* get Email Recipients */

                if (!empty($final_users)) {
                    $email_ids = array();
                    $option_data = Options::find()->where($email_alert . '=1 AND user_id IN (' . implode(",", $final_users) . ')')->all();

                    if (!empty($option_data)) {
                        foreach ($option_data as $opdata) {
                            if (isset($opdata->user->usr_email) && $opdata->user->usr_email != "")
                                $email_ids[$opdata->user->usr_email] = $opdata->user->usr_email;
                        }
                    }
                    // echo "<pre>",print_r($email_ids),"</pre>"; die();
                    if (isset($data['case_id'])) {
                        $caseinfo = ClientCase::findOne($data['case_id']);
                        $data['case_name'] = $caseinfo->case_name;
                        $data['case_modified_by'] = $caseinfo->modifiedBy->usr_first_name . " " . $caseinfo->modifiedBy->usr_lastname;
                        $data['sales_represenative'] = $caseinfo->salesRepo->usr_first_name . " " . $caseinfo->salesRepo->usr_lastname;
                        $data['case_total_budget'] = "$" . number_format(($caseinfo->budget_value), 2, '.', ',');
                        //$invoiced=InvoiceFinal::model()->pendingBillInvoiceByCase($data['case_id'],"invoice");
                        //$pending=InvoiceFinal::model()->pendingBillInvoiceByCase($data['case_id']);
                        $case_total_spend = 0; //($invoiced+$pending);
                        $data['case_total_spend'] = "$" . number_format($case_total_spend, 2, '.', ',');
                        if (!isset($data['client_id'])) {
                            $data['client_name'] = $caseinfo->client->client_name;
                        }
                    }

                    if (!isset($_SESSION['usrTZ'])) {
                        $tzoptions_data = Options::find()->where(['user_id' => Yii::$app->user->identity->id])->one();
                        if (isset($tzoptions_data['timezone_id']) && $tzoptions_data['timezone_id'] != "") {
                            $_SESSION['usrTZ'] = $tzoptions_data['timezone_id'];
                        }
                    }
                    if (isset($data['project_id'])) {
                        $status_arr = array(0 => 'Not Started', 1 => 'Started', 3 => 'Hold', 4 => 'Complete');
                        $task_info = Tasks::findOne($data['project_id']);
                        // with(array('createdUser'=>array('select'=>array('usr_first_name','usr_lastname'))))->findByPk($data['project_id']);
                        $taskinstruct_info = TaskInstruct::find()->joinWith('taskPriority')->where(['task_id' => $data['project_id'], 'isactive' => 1])->select(['tbl_task_instruct.task_id', 'tbl_task_instruct.project_name', 'tbl_task_instruct.task_duedate', 'tbl_task_instruct.task_timedue', 'tbl_task_instruct.created', 'tbl_task_instruct.instruct_version', 'tbl_task_instruct.id', 'tbl_task_instruct.task_priority', 'tbl_priority_project.priority'])->one();
                        // model()->with(array('priorityproject'=>array('select'=>array('priority'))))->find(array('condition'=>"tasks_id={$data['project_id']} AND isactive='1'",'select'=>array('project_name','task_duedate','task_timedue','created','instruct_version','id')));
                        if (!isset($data['case_id'])) {
                            $caseinfo = ClientCase::findOne($task_info->client_case_id);
                            // ClientCase::model()->with(array('modifiedBy'=>array('select'=>array('usr_first_name','usr_lastname')),'SalesRepo'=>array('select'=>array('usr_first_name','usr_lastname')),'client'=>array('select'=>array('client_name'))))->find(array('select'=>array('case_name','budget_value'),'condition'=>'t.id='.$task_info->client_case_id));
                            // ByPk($task_info->client_case_id);
                            $data['case_name'] = $caseinfo->case_name;
                            $data['case_modified_by'] = $caseinfo->modifiedBy->usr_first_name . " " . $caseinfo->modifiedBy->usr_lastname;
                            $data['sales_represenative'] = $caseinfo->salesRepo->usr_first_name . " " . $caseinfo->salesRepo->usr_lastname;
                            $data['case_total_budget'] = "$" . number_format(($caseinfo->budget_value), 2, '.', ',');
                            // $invoiced=InvoiceFinal::model()->pendingBillInvoiceByCase($task_info->client_case_id,"invoice");
                            // $pending=InvoiceFinal::model()->pendingBillInvoiceByCase($task_info->client_case_id);
                            $case_total_spend = 0; //($invoiced+$pending);
                            $data['case_total_spend'] = "$" . number_format($case_total_spend, 2, '.', ',');
                            if (!isset($data['client_id'])) {
                                $data['client_name'] = $caseinfo->client->client_name;
                            }
                        }

                        $data['project_#'] = $data['project_id'];
                        $data['project_name'] = $taskinstruct_info->project_name;
                        if (isset($data['todo_id']) && $data['todo_id'] != 0) {
                            $data['task_assigned_to'] = $tododata->assignedUser->usr_first_name . " " . $tododata->assignedUser->usr_lastname;
                        } else if (isset($data['task_unit_id']) && $data['task_unit_id'] != 0) {
                            $tasksunitsdata = TasksUnits::findOne($data['task_unit_id']);
                            $data['task_assigned_to'] = $tasksunitsdata->assignedUser->usr_first_name . " " . $tasksunitsdata->assignedUser->usr_lastname;
                        } else {
                            $userList = (new SettingsEmail)->getAssignedUserByTaskID($data['project_id']);
                            $data['task_assigned_to'] = implode(";", $userList); // this function should be in Tasks Model
                        }

                        $data['project_submitted_by'] = $task_info->createdUser->usr_first_name . " " . $task_info->createdUser->usr_lastname;
                        $serviceList = (new SettingsEmail)->getService($data['project_id']); // this function should be in Tasks Model
                        $teamserviceList = (new SettingsEmail)->getTeamServicesByProjectID($data['project_id']); // this function should be in Tasks Model
                        if (isset($data['teamservice_id']) && $data['teamservice_id'] != "") {
                            $data['completed_service'] = (new SettingsEmail)->getCompletedService($data['project_id'], $data['teamservice_id']);
                            if (is_array($data['completed_service'])) {
                                $data['completed_service'] = implode(', ', $data['completed_service']);
                            }
                        }

                        $data['service_name'] = implode("; ", $serviceList);
                        $data['team_services'] = implode("; ", $teamserviceList);
                        if (isset($data['task_unit_id']) && $data['task_unit_id'] != 0) {
                            $tasksunitsdata = TasksUnits::findOne($data['task_unit_id']);
                            $data['team_service_task'] = $tasksunitsdata->taskInstructServicetask->servicetask->service_task;
                        }
                        $data['due_date'] = $data['project_due_date'] = (new Options)->ConvertOneTzToAnotherTz($taskinstruct_info->task_duedate . " " . date('H:i:s', strtotime($taskinstruct_info->task_timedue)), "UTC", $_SESSION["usrTZ"], "MDY");
                        $data['project_due_time'] = (new Options)->ConvertOneTzToAnotherTz($taskinstruct_info->task_duedate . " " . date('H:i:s', strtotime($taskinstruct_info->task_timedue)), "UTC", $_SESSION["usrTZ"], "HIS");
                        $data['project_priority'] = $taskinstruct_info->taskPriority->priority;
                        $data['project_status'] = $status_arr[$task_info->task_status];
                        $data['project_cancel_reason'] = html_entity_decode($task_info->task_cancel_reason);
                        $data['project_complete_%'] = (new Tasks)->getTaskPercentageCompleted($data['project_id'], "case", 0, array(), "NUM");
                        $data['project_start_date'] = (new Options)->ConvertOneTzToAnotherTz($taskinstruct_info->created, "UTC", $_SESSION["usrTZ"], "MDY");
                        $data['instruction_version'] = "V " . $taskinstruct_info->instruct_version;
                        if (isset($data['instrid'])) {
                            $data['instruction_changes'] = '';
                            if ($taskinstruct_info->instruct_version > 1)
                                $data['instruction_changes'] = $taskinstruct_info->project_name . ' ' . (new SettingsEmail)->getChangedTaskInstruction($data['project_id']);
                        }
                    }

                    if (isset($data['case_changes']) && !empty($data['case_changes'])) {
                        $new_changes = "";
                        foreach ($data['case_changes'] as $key => $val)
                            $new_changes .= " Case " . ucwords(str_replace("_", " ", $key)) . " - " . $val . " :";

                        $data['case_changes'] = $new_changes;
                    }

                    /* if(isset($data['task_unit_id']) && $data['task_unit_id']!=0)
                      {
                      $data['team_service_task'] = TasksUnits::model()->getService($data['task_unit_id']);
                      } */
                    /* Sending Email to Recipients as per system settings */

                    if (isset($email_ids) && !empty($email_ids)) {
                        $mailer = Emailsettings::sendemail();

                        //	echo "<pre>",print_r($mailer),"</pre>";	die();
                        // if object mailer found then following code executes
                        if (!empty($mailer)) {
                            $get_all_fields = ArrayHelper::map(SettingsEmailFields::find()->select('display_name')->all(), 'display_name', 'display_name');
                            foreach ($email_ids as $email) {
                                if (!in_array($email, $notinemail_ids)) { // code to banned same email id
                                    $mailer->AddAddress(trim($email));
                                    $email_id_array[$email] = trim($email);
                                }
                            }
                            if (!empty($template_info->bcc_email_recipients)) {
                                $bccEmails = explode(';', $template_info->bcc_email_recipients);
                                foreach ($bccEmails as $singleEmail) {
                                    if ($singleEmail != " ")
                                        $mailer->AddBCC(trim($singleEmail));
                                }
                            }
                            $main_body = ((isset($template_info->email_custom_body) && $template_info->email_custom_body != "") ? nl2br(html_entity_decode($template_info->email_custom_body)) : nl2br(html_entity_decode($template_info->email_body)));
                            $main_subject = ((isset($template_info->email_custom_body) && $template_info->email_custom_subject != "") ? $template_info->email_custom_subject : $template_info->email_subject);
                            foreach ($get_all_fields as $field) {
                                $d_field = str_replace(array("[", "]"), "", $field);
                                if (strpos($main_body, $field) !== false) {
                                    $main_body = str_replace($field, nl2br(html_entity_decode($data[$d_field])), $main_body);
                                }
                                if (strpos($main_subject, $field) !== false) {
                                    $main_subject = str_replace($field, nl2br(html_entity_decode($data[$d_field])), $main_subject);
                                }
                            }
                            $main_subject = str_replace("[]", "[ No Information Available ]", $main_subject);
                            $main_body = str_replace("[]", "[ No Information Available ]", $main_body);
                            $body_header = $template_info->email_header;
                            $html = \Yii::$app->view->renderFile('@app/views/layouts/email/emailtemplate.php', ['subject' => $main_subject, 'body' => $main_body, 'body_header' => $body_header], null);
                            //echo "<pre>"; print_r($data);
                            //echo $html;die;
                            $mailer->Subject = $main_subject;
                            $mailer->IsHTML(true);
                            $mailer->Body = $html;
                            if ($_SERVER['HTTP_HOST'] != '192.168.0.129' && $_SERVER['HTTP_HOST'] != 'localhost') {
                                $mailer->Send();
                            }
                        }
                        /** End Send * */
                    }
                    /* Sending Email to Recipients as per system settings */
                }
            }
        }
    }

    /**
     * sendEmail function used to send mail
     * @template_id = email_sort
     * @email_alert = field from tbl_options
     * @data = used to replace with template field.
     */
    public static function sendEmail($template_id, $email_alert, $data = array(), $email_alert_val = 1) {
        /** teamplate info * */
        $email_id_array = array();
        $notinemail_ids = array();
        if (isset($data['notinEmail_ids']) && !empty($data['notinEmail_ids'])) { //code to banned same email id
            $notinemail_ids = $data['notinEmail_ids'];
        }
        $optionUsers = ArrayHelper::map(Options::find()->where([$email_alert => $email_alert_val])->select(['user_id'])->all(), 'user_id', 'user_id');
        $unitdatafield = array();
        if (!empty($optionUsers)) {
            //chk first system has at least one user who subscribe to particular alert type
            $data['user'] = Yii::$app->user->identity->usr_first_name . " " . Yii::$app->user->identity->usr_lastname;
            if (isset($data['service_id'])) {
                $service_info = Servicetask::findOne($data['service_id']);
                $data['todo_submitted_by_services'] = $service_info->teamservice->service_name . " - " . $service_info->service_task;
            }
            if (isset($data['servicetask_id'])) {
                $teamLocation = ArrayHelper::map(TeamlocationMaster::find()->select(['id', 'team_location_name'])->where('remove=0')->all(), 'id', 'team_location_name');
                $servicetask_data = Servicetask::findOne($data['servicetask_id']);
                $team_id = $servicetask_data->teamId;
                $data['team_service_task'] = $servicetask_data->service_task;
                $data['previous_team_location'] = $teamLocation[$data['previous_tl']];
                $data['team_location'] = $teamLocation[$data['tl']];
            }
            if (isset($data['todo_id'])) {
                $tododata = TasksUnitsTodos::findOne($data['todo_id']);
                $data['todo'] = Html::decode($tododata->todo);
                $data['todo_submitted_by'] = $tododata->createdUser->usr_first_name . " " . $tododata->createdUser->usr_lastname;
            }
            if (isset($template_id) && $template_id != 0) {
                $template_info = self::find()->where(['email_sort' => $template_id])->one();
                $final_users = array();
                $case_users = array();
                $caserole_users = array();
                $team_users = array();
                $team_users_assigned = array();
                $team_users_task_assigned = array();
                $team_users_todo_assigned = array();
                $team_users_task_transferred_to = array();
                $pending_task_assigned_user = array();

                $email_recipient = ((isset($template_info->email_custom_recipients) && $template_info->email_custom_recipients != "") ? $template_info->email_custom_recipients : $template_info->email_recipients);

                /* get Email Recipients */
                if (in_array(1, explode(",", $email_recipient))) { // All Case members project was submitted under
                    if (isset($data['case_id']) && $data['case_id'] != 0) {
                        $options = array();
                        $options['case'] = "ProjectWasSubmittedTo";
                        $options['case_id'] = $data['case_id'];
                        $case_users = self::getFinalMailReceipents($email_alert, $options, $optionUsers);
                    }
                }

                if (in_array(2, explode(",", $email_recipient))) { // All My Team members project was submitted to
                    if (isset($data['project_id']) && $data['project_id'] != 0) {
                        $options = array();
                        $options['team'] = "ProjectWasSubmittedTo";
                        $options['task_id'] = $data['project_id'];
                        $team_users = self::getFinalMailReceipents($email_alert, $options, $optionUsers);
                        // getAllMyTeamMembersProjectSubmittedTo($data['project_id'],implode(",",$optionUsers));
                    }
                }
                if (in_array(3, explode(",", $email_recipient))) { // Only Team members project was assigned to
                    if (isset($data['project_id']) && $data['project_id'] != 0) {
                        $options = array();
                        $options['assignOnly'] = 'OnlyTeamMembersProjectAssignedTo';
                        $options['task_id'] = $data['project_id'];
                        $team_users_assigned = self::getFinalMailReceipents($email_alert, $options, $optionUsers);
                        //$this->getOnlyTeamMembersProjectAssignedTo($data['project_id']);
                    }
                }
                if (in_array(4, explode(",", $email_recipient))) { // Only to user that has been assigned/transitioned task
                    if (isset($data['task_unit_id']) && $data['task_unit_id'] != 0) {
                        $options = array();
                        $options['assignOnly'] = 'OnlyUserAssignedTransitionedTOTask';
                        $options['task_unit_id'] = $data['task_unit_id'];
                        $team_users_task_assigned = self::getFinalMailReceipents($email_alert, $options, $optionUsers);
                        //$team_users_task_assigned=$this->getOnlyUserAssignedTransitionedTOTask($data['task_unit_id']);
                    }
                }

                if (in_array(5, explode(",", $email_recipient))) { // Only to user that has been assigned/transitioned todo
                    if (isset($data['todo_id']) && $data['todo_id'] != 0) {
                        $options = array();
                        $options['assignOnly'] = 'OnlyUserAssignedTransitionedTOToDo';
                        $options['todo_id'] = $data['todo_id'];
                        $team_users_todo_assigned = self::getFinalMailReceipents($email_alert, $options, $optionUsers);
                        // $team_users_todo_assigned=$this->getOnlyUserAssignedTransitionedTOToDo();
                    }
                }

                if (in_array(6, explode(",", $email_recipient))) { // Only to user that has been assigned/transitioned task that appears directly below a task that was newly flagged the complete status
                    if (isset($data['unit_assigned_to']) && $data['unit_assigned_to'] != 0 && $data['unit_assigned_to'] != '') {
                        if (!empty($optionUsers))
                            $pending_task_assigned_user = ArrayHelper::map(User::find()->select(["usr_email", "CONCAT(usr_first_name,' ',usr_lastname) as full_name"])->where('id IN (' . $data['unit_assigned_to'] . ') AND id IN(' . implode(',', $optionUsers) . ')')->all(), 'usr_email', 'full_name');
                    }
                }
                if (in_array(7, explode(",", $email_recipient))) { // All Team members task was transferred to
                    if (isset($data['servicetask_id']) && $data['servicetask_id'] != 0) {
                        $options = array();
                        $options['teamTask'] = 'AllTeamMembersTaskTransferredTo';
                        if (isset($data['team_id']) && $data['team_id'] != "") {
                            $options['team_id'] = $data['team_id'];
                        } else {
                            if (isset($data['servicetask_id']) && $data['servicetask_id'] != "") {
                                $service_info = Servicetask::findOne($data['servicetask_id']);
                                if (isset($service_info->teamId) && $service_info->teamId > 0)
                                    $options['team_id'] = $service_info->teamId;
                            }
                        }
                        if (isset($options['team_id']) && $options['team_id'] != "")
                            $team_users_task_transferred_to = self::getFinalMailReceipents($email_alert, $options, $optionUsers);
                        // $team_users_task_transferred_to=$this->getAllTeamMembersTaskTransferredTo($team_id,implode(",",$optionUsers));
                    }
                }

                // Special case role access & email recipient
                if (in_array(8, explode(",", $email_recipient))) {
                    if ($template_info->email_caserole != "") {
                        if (in_array(1, explode(",", $email_recipient))) {
                            $case_users_ids = self::getAllCaseMembersProjectubmittedUnder($data['case_id'], implode(",", $optionUsers));
                            $casepermittedusers = array_intersect($case_users_ids, $optionUsers);
                            if (!empty($casepermittedusers)) {
                                $caserole_users = self::getAllCaseRoleUsers($template_info->email_caserole, implode(",", $optionUsers));
                            }
                        } else {
                            $caserole_users = self::getAllCaseRoleUsers($template_info->email_caserole, implode(",", $optionUsers));
                        }
                    }
                }

                $final_users = array_merge($case_users, $team_users, $team_users_assigned, $team_users_task_assigned, $team_users_todo_assigned, $caserole_users, $pending_task_assigned_user);


                /* get Email Recipients */
                if (!empty($final_users)) {
                    $email_ids = array();
                    $data['user_id'] = "";
                    if (!empty($final_users)) {
                        foreach ($final_users as $email => $fullname) {
                            $email_ids[$email] = $email;
                            if ($data['user_id'] == "") {
                                $data['user_id'] = $fullname;
                            } else {
                                $data['user_id'] .= ", " . $fullname;
                            }
                        }
                    }

                    /* prod_id */
                    if (isset($data['prod_id']) && $data['prod_id'] != 0) {
                        $data['production_#'] = $data['prod_id'];
                        $prod_data = EvidenceProduction::findOne($data['prod_id']);
                        $data['producing_party'] = $prod_data->prod_party;
                        $data['staff_assigned'] = $prod_data->staff_assigned;
                        $data['production_date'] = $prod_data->getProdDate($prod_data->prod_date);
                        $data['production_received_date'] = isset($prod_data->prod_rec_date) ? date("m/d/Y", strtotime($prod_data->prod_rec_date)) : "";
                        $data['production_copied_to'] = isset($prod_data->prod_copied_to) ? htmlspecialchars($prod_data->prod_copied_to) : "";
                        $data['production_cover_letter_link'] = isset($prod_data->cover_let_link) ? htmlspecialchars($prod_data->cover_let_link) : "";
                        $data['production_type'] = (isset($prod_data->production_type) && production_type == 1) ? "Incoming" : "Outgoing";
                        $data['production_description'] = isset($prod_data->production_desc) ? $prod_data->production_desc : "";
                        if (isset($prod_data->has_media) && ($prod_data->has_media)) {
                            $media_ids = (new EvidenceProductionMedia)->getMediaids($prod_data->id);
                            if (!empty($media_ids))
                                $data['production_media'] = implode(",", $media_ids);
                        }
                        else {
                            $data['production_media'] = "";
                        }
                    }


                    if (isset($data['case_id'])) {
                        $caseinfo = ClientCase::findOne($data['case_id']);
                        $data['case_name'] = $caseinfo->case_name;
                        $data['case_modified_by'] = $caseinfo->modifiedBy->usr_first_name . " " . $caseinfo->modifiedBy->usr_lastname;
                        $data['sales_represenative'] = $caseinfo->salesRepo->usr_first_name . " " . $caseinfo->salesRepo->usr_lastname;
                        $data['case_total_budget'] = "$" . number_format(($caseinfo->budget_value), 2, '.', ',');
                        /* $invoiced= (new InvoiceFinal)->pendingBillInvoiceByCase($data['case_id'],"invoice");
                          $pending= (new InvoiceFinal)->pendingBillInvoiceByCase($data['case_id']);
                          $case_total_spend=$invoiced+$pending; */
                        $case_total_spend = (new InvoiceFinal)->totalspendbudget($data['case_id']);
                        $data['case_total_spend'] = "$" . number_format($case_total_spend, 2, '.', ',');
                        if (!isset($data['client_id'])) {
                            $data['client_name'] = $caseinfo->client->client_name;
                        }
                    }
                    if (isset($data['evidence'])) {
                        /** Evidence * */
                        $data['client_name'] = $data['client_name'];
                        $data['case_name'] = $data['case_name'];
                        $data['case_total_budget'] = $data['case_total_budget'];
                        $data['sales_represenative'] = $data['sales_represenative'];
                        $data['media_id'] = $data['evid_clientcase']['ClientCaseEvidence']['evid_num_id'];
                        $data['media_checkedinby_user'] = $data['user'];
                        $data['user'] = $data['user_id'];
                        $data['media_other_id'] = $data['evidence']['other_evid_num'];
                        $data['media_recieved_date'] = $data['evidence']['received_date'];
                        $data['media_recieved_time'] = $data['evidence']['received_time'];
                        $data['media_received_from'] = $data['evidence']['received_from'];
                        $data['media_internal_id'] = $data['evidence']['evd_Internal_no'];
                        $evidtype = EvidenceType::findOne($data['evidence']['evid_type']);
                        $data['media_type'] = $evidtype->evidence_name;
                        $evidCat = EvidenceCategory::findOne($data['evidence']['cat_id']);
                        $data['media_category'] = $evidCat->category;
                        $data['media_serial_num'] = $data['evidence']['serial'];
                        $data['media_quantity'] = $data['evidence']['quantity'];
                        $data['media_description'] = $data['evidence']['evid_desc'];
                        $data['media_label_description'] = $data['evidence']['evid_label_desc'];
                        $data['media_total_size'] = $data['evidence']['contents_total_size'];
                        $data['media_total_size_compressed'] = $data['evidence']['contents_total_size_comp'];
                        $contentUnit = Unit::findOne($data['evidence']['unit']);
                        $data['media_total_size_unit'] = $contentUnit->unit_name;
                        $contentCompUnit = Unit::findOne($data['evidence']['comp_unit']);
                        $data['media_total_size_compressed_unit'] = $contentCompUnit->unit_name;
                        $data['media_contents_copied_to'] = $data['evidence']['contents_copied_to'];
                        $data['media_begbates'] = $data['evidence']['bbates'];
                        $data['media_endbates'] = $data['evidence']['ebates'];
                        $data['media_volume_#'] = $data['evidence']['m_vol'];
                        $data['media_notes'] = $data['evidence']['evid_notes'];
                        $data['media_barcode_id'] = $data['evidence']['barcode'];
                        if (isset($data['newcontents']) && !empty($data['newcontents'])) {
                            $contentDetail = "<table border='1' style='border:1px solid black!important;'><thead><tr><th style='text-align:left;width:250px;'>Custodian</th><th style='text-align:left;width:300px;'>Data Type</th><th style='text-align:left;'>Data Size</th></tr></thead><tbody>";
                            foreach ($data['newcontents'] as $content) {
                                $custodian = EvidenceCustodians::findOne($content['cust_id']); // Evidence
                                $custodianname = $custodian->cust_fname . " " . $custodian->cust_lname; // cust fname and cust lname
                                $dtype = DataType::findOne($content['data_type']); // get data_type field name
                                $contentUnit = Unit::findOne($content['unit']); // unit find
                                $datasize = $content['data_size'] . " " . $contentUnit->unit_name;
                                $contentDetail .= "<tr><td style='width:250px;'>{$custodianname}</td><td style='border-left:1px solid black!important;width:300px;'>{$dtype->data_type}</td><td style='border-left:1px solid black!important;'>{$datasize}</td></tr>";
                            }
                            $contentDetail .= "</tbody></table>";
                            $data['media_content'] = $contentDetail;
                        }
                    }
                    /** End New Media Add * */
                    if (!isset($_SESSION['usrTZ'])) {
                        $tzoptions_data = Options::find()->where(['user_id' => Yii::$app->user->identity->id])->one();
                        if (isset($tzoptions_data['timezone_id']) && $tzoptions_data['timezone_id'] != "") {
                            $_SESSION['usrTZ'] = $tzoptions_data['timezone_id'];
                        }
                    }

                    /* project id */
                    if (isset($data['project_id'])) {
                        if (isset($data['Production']) && is_array($data['Production'])) {
                            foreach ($data['Production'] as $prod_id => $production) {
                                $prod_data = EvidenceProduction::findOne($prod_id);
                                if (isset($prod_data)) {
                                    if (isset($data['production_#'])) {
                                        $data['production_#'] = $data['production_#'] . ', ' . $prod_id;
                                    } else {
                                        $data['production_#'] = $prod_id;
                                    }
                                    if (isset($data['producing_party'])) {
                                        $data['producing_party'] = $data['producing_party'] . ', ' . $prod_data->prod_party;
                                    } else {
                                        $data['producing_party'] = $prod_data->prod_party;
                                    }

                                    if (isset($data['production_received_date'])) {
                                        $prd = isset($prod_data->prod_rec_date) ? date("m/d/Y", strtotime($prod_data->prod_rec_date)) : "";
                                        $data['production_received_date'] = $data['production_received_date'] . ', ' . $prd;
                                    } else {
                                        $data['production_received_date'] = isset($prod_data->prod_rec_date) ? date("m/d/Y", strtotime($prod_data->prod_rec_date)) : "";
                                    }
                                    if (isset($data['production_date'])) {
                                        $pd = isset($prod_data->prod_date) ? date("m/d/Y", strtotime($prod_data->prod_date)) : "";
                                        $data['production_date'] = $data['production_date'] . ', ' . $pd;
                                    } else {
                                        $data['production_date'] = isset($prod_data->prod_date) ? date("m/d/Y", strtotime($prod_data->prod_date)) : "";
                                    }
                                    if (isset($data['production_copied_to'])) {
                                        $pct = isset($prod_data->prod_copied_to) ? htmlspecialchars($prod_data->prod_copied_to) : "";
                                        $data['production_copied_to'] = $data['production_copied_to'] . ', ' . $pct;
                                    } else {
                                        $data['production_copied_to'] = isset($prod_data->prod_copied_to) ? htmlspecialchars($prod_data->prod_copied_to) : "";
                                    }
                                    if (isset($data['production_copied_to'])) {
                                        $pcll = isset($prod_data->cover_let_link) ? htmlspecialchars($prod_data->cover_let_link) : "";
                                        $data['production_cover_letter_link'] = $data['production_cover_letter_link'] . ', ' . $pcll;
                                    } else {
                                        $data['production_cover_letter_link'] = isset($prod_data->cover_let_link) ? htmlspecialchars($prod_data->cover_let_link) : "";
                                    }
                                    if (isset($data['production_copied_to'])) {
                                        $pt = (isset($prod_data->production_type) && production_type == 1) ? "Incoming" : "Outgoing";
                                        $data['production_type'] = $data['production_type'] . ', ' . $pt;
                                    } else {
                                        $data['production_type'] = (isset($prod_data->production_type) && production_type == 1) ? "Incoming" : "Outgoing";
                                    }
                                    if (isset($data['production_copied_to'])) {
                                        $pd = isset($prod_data->production_desc) ? $prod_data->production_desc : "";
                                        $data['production_description'] = $data['production_description'] . ', ' . $pd;
                                    } else {
                                        $data['production_description'] = isset($prod_data->production_desc) ? $prod_data->production_desc : "";
                                    }
                                    $pm = '';
                                    if (isset($prod_data->has_media) && ($prod_data->has_media)) {
                                        $media_ids = (new EvidenceProductionMedia)->getMediaids($prod_data->id);
                                        if (!empty($media_ids))
                                            if (isset($pm) && $pm != '') {
                                                $pm = $pm . ', ' . implode(",", $media_ids);
                                            } else {
                                                $pm = implode(",", $media_ids);
                                            }
                                    }
                                    if (isset($pm) && $pm != '') {
                                        $data['production_media'] = $pm;
                                    }
                                }
                            }
                        }
                        $status_arr = array(0 => 'Not Started', 1 => 'Started', 3 => 'Hold', 4 => 'Complete');
                        $task_info = Tasks::findOne($data['project_id']);
                        $taskinstruct_info = TaskInstruct::find()->joinWith('taskPriority')->where(['task_id' => $data['project_id'], 'isactive' => 1])->select(['tbl_task_instruct.project_name', 'tbl_task_instruct.task_duedate', 'tbl_task_instruct.task_timedue', 'tbl_task_instruct.created', 'tbl_task_instruct.instruct_version', 'tbl_task_instruct.id', 'tbl_task_instruct.task_priority', 'tbl_priority_project.priority'])->one();
                        if (!isset($data['case_id'])) {
                            $caseinfo = ClientCase::findOne($task_info->client_case_id);
                            $data['case_name'] = $caseinfo->case_name;
                            $data['case_modified_by'] = $caseinfo->modifiedBy->usr_first_name . " " . $caseinfo->modifiedBy->usr_lastname;
                            $data['sales_represenative'] = $caseinfo->salesRepo->usr_first_name . " " . $caseinfo->salesRepo->usr_lastname;
                            $data['case_total_budget'] = "$" . number_format(($caseinfo->budget_value), 2, '.', ',');
                            $case_total_spend = 0;
                            $data['case_total_spend'] = "$" . number_format($case_total_spend, 2, '.', ',');
                            if (!isset($data['client_id'])) {
                                $data['client_name'] = $caseinfo->client->client_name;
                            }
                        }
                        $data['project_#'] = $data['project_id'];
                        $data['project_name'] = ($taskinstruct_info->project_name != "" && isset($taskinstruct_info->project_name)) ? $taskinstruct_info->project_name : "(No Information Available)";
                        if (isset($data['todo_id']) && $data['todo_id'] != 0) {
                            $data['task_assigned_to'] = $tododata->assignedUser->usr_first_name . " " . $tododata->assignedUser->usr_lastname;
                        } else if (isset($data['task_unit_id']) && $data['task_unit_id'] != 0) {
                            $tasksunitsdata = TasksUnits::findOne($data['task_unit_id']);
                            $data['task_assigned_to'] = $tasksunitsdata->assignedUser->usr_first_name . " " . $tasksunitsdata->assignedUser->usr_lastname;
                        } else {
                            $userList = (new SettingsEmail)->getAssignedUserByTaskID($data['project_id']);
                            $data['task_assigned_to'] = implode(";", $userList); // this function should be in Tasks Model
                        }
                        $data['project_submitted_by'] = $task_info->createdUser->usr_first_name . " " . $task_info->createdUser->usr_lastname;

                        $teamserviceList = (new SettingsEmail)->getTeamServicesByProjectID($data['project_id']);
                        $serviceList = (new SettingsEmail)->getService($data['project_id']); // this function should be in Tasks Model

                        if (isset($data['teamservice_id']) && $data['teamservice_id'] != "") {
                            $data['completed_service'] = (new SettingsEmail)->getCompletedService($data['project_id'], $data['teamservice_id']);
                            if (is_array($data['completed_service'])) {
                                $data['completed_service'] = implode(', ', $data['completed_service']);
                            }
                        }
                        $data['service_name'] = implode("; ", $serviceList);
                        $data['team_services'] = implode("; ", $teamserviceList);
                        if (isset($data['task_unit_id']) && $data['task_unit_id'] != 0) {
                            $tasksunitsdata = TasksUnits::findOne($data['task_unit_id']);
                            $data['team_service_task'] = $tasksunitsdata->taskInstructServicetask->servicetask->service_task;
                        }
                        $taskduedatetime = explode(" ", $taskinstruct_info->task_date_time);
                        $data['due_date'] = $data['project_due_date'] = $taskduedatetime[0];
                        $data['project_due_time'] = $taskduedatetime[1] . ' ' . $taskduedatetime[2];
                        $data['project_priority'] = $taskinstruct_info->taskPriority->priority;
                        $data['project_status'] = $status_arr[$task_info->task_status];
                        $data['project_cancel_reason'] = html_entity_decode($task_info->task_cancel_reason);
                        $data['project_complete_%'] = (new Tasks)->getTaskPercentageCompleted($data['project_id'], "case", $data['case_id'], 0, 0, "NUM");
                        $data['project_start_date'] = (new Options)->ConvertOneTzToAnotherTz($taskinstruct_info->created, "UTC", $_SESSION["usrTZ"], "MDY");
                        $data['instruction_version'] = "V " . $taskinstruct_info->instruct_version;
                        if (isset($data['instrid'])) {
                            $data['instruction_changes'] = '';
                            if ($taskinstruct_info->instruct_version > 1)
                                $data['instruction_changes'] = $taskinstruct_info->project_name . ' ' . (new SettingsEmail)->getChangedTaskInstruction($data['project_id']);
                        }
                        $prodbates = (new EvidenceProductionBates)->getProdBatesValue($data['project_id']);
                        $data['prod_bbates'] = $prodbates['bbates'];
                        $data['prod_ebates'] = $prodbates['ebates'];
                        $data['prod_vol'] = $prodbates['vol'];

                        /* if($template_id == 5 || $template_id == 6)
                          {
                          /*$unitsdata = Unit::find()->all();
                          $unit = array();
                          $unitIdName= array();
                          foreach($unitsdata as $unitdata)
                          {
                          $unit[$unitdata->unit_name] = $unitdata->est_size;
                          $unitIdName[$unitdata->id] = $unitdata->unit_name;
                          }
                          $teamsre = "";
                          if($template_id != 5 && isset($data['teamservice_id']) && $data['teamservice_id'] != "")
                          {
                          $teamsre = " AND tbl_tasks_units.teamservice_id = {$data['teamservice_id']}";
                          } */
                        /* $data_sql = "Select task_data.task_instruct_servicetask_id,servicetask.teamservice_id,task_data.element_value,tbl_form_builder.element_id,teamserviceId.service_name,task_data.element_unit,tbl_unit.unit_name,tbl_form_builder.element_label from tbl_tasks_units_data as task_data
                          INNER JOIN tbl_task_instruct_servicetask as servicetask ON servicetask.id = task_data.task_instruct_servicetask_id
                          LEFT JOIN tbl_teamservice as teamserviceId ON servicetask.teamservice_id=teamserviceId.id
                          INNER JOIN tbl_unit ON tbl_unit.id = task_data.element_unit
                          INNER JOIN tbl_form_builder ON task_data.form_builder_id = tbl_form_builder.id
                          where task_data.task_id = ".$data['project_id']." AND tbl_form_builder.element_field_type = 1".$teamsre; */
                        /* $data_sql = "Select tbl_tasks_units.task_instruct_servicetask_id,tbl_tasks_units.teamservice_id,task_data.element_value,tbl_form_builder.element_id,teamserviceId.service_name,task_data.element_unit,tbl_unit.unit_name,tbl_form_builder.element_label from tbl_tasks_units_data as task_data
                          INNER JOIN tbl_tasks_units ON tbl_tasks_units.id = task_data.tasks_unit_id
                          LEFT JOIN tbl_teamservice as teamserviceId ON tbl_tasks_units.teamservice_id=teamserviceId.id
                          INNER JOIN tbl_unit ON tbl_unit.id = task_data.element_unit
                          INNER JOIN tbl_form_builder ON task_data.form_builder_id = tbl_form_builder.id
                          where tbl_tasks_units.task_id = ".$data['project_id']." AND tbl_form_builder.element_field_type = 1".$teamsre;
                          $unitdataArr = Yii::$app->db->createCommand($data_sql)->queryAll();

                          $tasksbasedunis = array(); */
                        /* if(!empty($unitdataArr)) {
                          $unitdatas = array();
                          foreach ($unitdataArr as $tasukey1 => $taskunitval) {
                          $unitcount = 0;
                          if($taskunitval['element_unit'] != "") {
                          $units = "";
                          $unit_name = $unitIdName[$taskunitval['element_unit']];
                          $est_size = $unit[$unit_name];
                          if($est_size > 0 && $unit_name != 'GB') {
                          $unitsAr['GB'] = 'GB';
                          $units = 'GB';
                          $kb = $est_size;
                          $total_kbs = $taskunitval['element_value']; // get qty value in kb
                          if($unit_name != 'KB')
                          $total_kbs = $kb * $taskunitval['element_value']; // get qty value in kb
                          $total_bytes = $total_kbs * 1024; // get total values in bytes to convert it to max unit
                          $unitcount = number_format($total_bytes / 1073741824, 2, '.','');
                          } else {
                          $unitsAr[$unit_name]=$unit_name;
                          $units = $unit_name;
                          $unitcount = $taskunitval['element_value'];
                          }
                          if($template_id == 6){
                          if(isset($unitdatas[$taskunitval['element_label']][$taskunitval['service_name']][$units])){
                          $unitdatas[$taskunitval['element_label']][$taskunitval['service_name']][$units] += (float)$unitcount;
                          }else{
                          $unitdatas[$taskunitval['element_label']][$taskunitval['service_name']][$units] = (float)$unitcount;
                          }
                          }
                          if($template_id == 5){
                          if(isset($unitdatas[$taskunitval['element_label']][$units])){
                          $unitdatas[$taskunitval['element_label']][$units] += (float)$unitcount;
                          }else{
                          $unitdatas[$taskunitval['element_label']][$units] = (float)$unitcount;
                          }
                          }
                          }
                          }
                          }
                          if(!empty($unitdatas)){
                          if($template_id == 5){
                          foreach($unitdatas as $key => $unitdataval) {
                          foreach($unitdataval as $key1 => $unitdataval1) {
                          if(isset($data[$key]))
                          $data[$key] .= ", ".$unitdataval1." ".$key1;
                          else
                          $data[$key] = $key." ".$unitdataval1." ".$key1;
                          }
                          $unitdatafield[$key] = $data[$key];
                          }
                          }
                          if($template_id == 6){
                          foreach($unitdatas as $key => $unitdataval) {
                          foreach($unitdataval as $key1 => $unitdataval1) {
                          $unitst = "";
                          foreach($unitdataval1 as $t=>$v){
                          if($unitst == "")
                          $unitst = $v." ".$t;
                          else
                          $unitst .= ", ". $v." ".$t;
                          }
                          if(isset($data[$key]))
                          $data[$key] .= " AND ".$key." ".$unitst;
                          else
                          $data[$key] = $key." ".$unitst;
                          }
                          $unitdatafield[$key] = $data[$key];
                          }
                          }
                          }
                          }
                          }
                          } */
                        /* if(!empty($unitdatas)){
                          if($template_id == 5){
                          foreach($unitdatas as $key => $unitdataval) {
                          foreach($unitdataval as $key1 => $unitdataval1) {
                          if(isset($data[$key]))
                          $data[$key] .= ", ".$unitdataval1." ".$key1;
                          else
                          $data[$key] = $key." ".$unitdataval1." ".$key1;
                          }
                          $unitdatafield[$key] = $data[$key];
                          }
                          }
                          if($template_id == 6){
                          foreach($unitdatas as $key => $unitdataval) {
                          foreach($unitdataval as $key1 => $unitdataval1) {
                          $unitst = "";
                          foreach($unitdataval1 as $t=>$v){
                          if($unitst == "")
                          $unitst = $v." ".$t;
                          else
                          $unitst .= ", ". $v." ".$t;
                          }
                          if(isset($data[$key]))
                          $data[$key] .= " AND ".$key." ".$unitst;
                          else
                          $data[$key] = $key." ".$unitst;
                          }
                          $unitdatafield[$key] = $data[$key];
                          }
                          }
                          } */
                        /* } */
                    }

                    if (isset($data['case_changes']) && !empty($data['case_changes'])) {
                        $new_changes = "";
                        foreach ($data['case_changes'] as $key => $val)
                            $new_changes .= " Case " . ucwords(str_replace("_", " ", $key)) . " - " . $val;

                        $data['case_changes'] = $new_changes;
                    }

                    /* Sending Email to Recipients as per system settings */
                    if (isset($email_ids) && !empty($email_ids)) {
                        $mailer = Emailsettings::sendemail();
                        if ($mailer === false) {
                            return;
                        }
                        $get_template_fields = ArrayHelper::map(SettingEmailTemplateFields::find()->select('field_id')->where(['template_id' => $template_info->id])->all(), 'field_id', 'field_id');
                        $get_template_field_ids = implode(",", $get_template_fields);
                        $get_all_fields = ArrayHelper::map(SettingsEmailFields::find()->select(['display_name'])->where('id IN (' . $get_template_field_ids . ')')->all(), 'display_name', 'display_name');
                        //echo "<pre>",print_r($get_all_fields);die;
                        $get_all_fields['[due_date]'] = '[due_date]';
                        $get_all_fields['[producing_party]'] = '[producing_party]';
                        $get_all_fields['[service_name]'] = '[service_name]';
                        if (!empty($unitdatafield)) {
                            foreach ($unitdatafield as $fields => $unitdata) {
                                $get_all_fields["[" . $fields . "]"] = "[" . $fields . "]";
                            }
                        }
                        foreach ($email_ids as $email) {
                            if (!in_array($email, $notinemail_ids)) { // code to banned same email id
                                if ($email != '' && !is_numeric($email)) {
                                    $mailer->AddAddress(trim($email));
                                    $email_id_array[$email] = $email;
                                }
                            }
                        }
                        if (!empty($template_info->bcc_email_recipients)) {
                            $bccEmails = explode(';', $template_info->bcc_email_recipients);
                            foreach ($bccEmails as $singleEmail) {
                                $BCCmail = trim($singleEmail);
                                if ($BCCmail != "") {
                                    $mailer->AddBCC($BCCmail);
                                    $email_id_array[$BCCmail] = $BCCmail;
                                }
                            }
                        }
                        $main_body = ((isset($template_info->email_custom_body) && $template_info->email_custom_body != "") ? nl2br(html_entity_decode($template_info->email_custom_body)) : nl2br(html_entity_decode($template_info->email_body)));
                        $main_subject = ((isset($template_info->email_custom_body) && $template_info->email_custom_subject != "") ? $template_info->email_custom_subject : $template_info->email_subject);

                        /** IRT 446 Field Values * */
                        if (isset($data['project_id'])) {
                            /** Instruction Form Fields IRT 446 * */
                            $result = TaskInstruct::find()->select(['tbl_task_instruct.id'])
                                            ->joinWith(['formInstructionValues' => function(\yii\db\ActiveQuery $query) {
                                                    $query->select(['tbl_form_instruction_values.id', 'tbl_form_instruction_values.element_value', 'tbl_form_instruction_values.element_unit', 'tbl_form_instruction_values.task_instruct_id', 'tbl_form_instruction_values.form_builder_id']);
                                                    $query->joinWith(['formBuilder' => function(\yii\db\ActiveQuery $query) {
                                                            $query->select(['tbl_form_builder.*']);
                                                            $query->joinWith('formElementOptions');
                                                        }]);
                                                }])->where(['tbl_task_instruct.task_id' => $data['project_id'], 'tbl_task_instruct.isactive' => 1])->asArray()->all();
                            foreach ($result as $key => $value) {
                                foreach ($value['formInstructionValues'] as $innerKey => $vals) {
                                    $label = 'No Label';
                                    if ($vals['formBuilder']['element_label'] != '') {
                                        $label = ' ( ' . $vals['formBuilder']['element_label'] . ' ) ';
                                    }
                                    /* Instruction form field IRT 446 */
                                    if ($vals['formBuilder']['form_type'] == 1 && $vals['formBuilder']['element_view'] == 1) {
                                        if ($vals['formBuilder']['element_type'] == 'checkbox' || $vals['formBuilder']['element_type'] == 'radio' || $vals['formBuilder']['element_type'] == 'dropdown') {
                                            foreach ($vals['formBuilder']['formElementOptions'] as $keys => $element_val) {
                                                if ($vals['element_value'] == $element_val['id'])
                                                    $option_val[$vals['formBuilder']['id']][] = $element_val['element_option'];
                                            }
                                            $option_result[$vals['formBuilder']['id']] = implode(" , ", $option_val[$vals['formBuilder']['id']]) . $label;
                                        }
                                        else if ($vals['formBuilder']['element_type'] == 'datetime') {
                                            $option_result[$vals['formBuilder']['id']] = $vals['element_value'] . $label;
                                        } else if ($vals['formBuilder']['element_type'] == 'number') {
                                            $unit_number = Unit::find()->where(['id' => $vals['element_unit']])->one();
                                            $option_result[$vals['formBuilder']['id']] = $vals['element_value'] . ' ' . $unit_number->unit_name . $label;
                                        } else {
                                            $option_result[$vals['formBuilder']['id']] = $vals['element_value'] . $label;
                                        }
                                    }
                                }
                            }

                            /** Task outcome fields IRT 446 * */
                            /* $dataresult = TaskInstruct::find()->select(['tbl_task_instruct.id'])
                              ->joinWith(['tasksUnits' => function(\yii\db\ActiveQuery $query){
                              $query->joinWith(['tasksUnitsData' => function(\yii\db\ActiveQuery $query){
                              $query->select(['tbl_tasks_units_data.id','tbl_tasks_units_data.element_value','tbl_tasks_units_data.element_unit' , 'tbl_tasks_units_data.tasks_unit_id','tbl_tasks_units_data.form_builder_id']);
                              $query->joinWith(['formBuilder' => function(\yii\db\ActiveQuery $query) {
                              $query->select(['tbl_form_builder.*']);
                              $query->joinWith('formElementOptions');
                              }]);
                              }]);
                              }])->where(['tbl_task_instruct.task_id' => $data['project_id'], 'tbl_task_instruct.isactive' => 1])->asArray()->all();
                              $option_val = array();
                              foreach($dataresult as $key => $value) {
                              foreach($value['tasksUnits'] as $innerKey => $values) {
                              foreach($values['tasksUnitsData'] as $taskInnerKey => $vals) {
                              $label='No Label';
                              if($vals['formBuilder']['element_label']!=''){
                              $label = ' ( '.$vals['formBuilder']['element_label'].' ) ';
                              }
                              if($vals['formBuilder']['form_type']==2 && $vals['formBuilder']['element_view']==1){
                              if($vals['formBuilder']['element_type'] == 'checkbox' || $vals['formBuilder']['element_type'] == 'radio' || $vals['formBuilder']['element_type'] == 'dropdown') {
                              foreach($vals['formBuilder']['formElementOptions'] as $keys => $element_val) {
                              if($vals['element_value']==$element_val['id'])
                              $option_val[$vals['formBuilder']['id']][] = $element_val['element_option'];
                              }
                              $option_result_data[$vals['formBuilder']['id']] = implode(" , ",$option_val[$vals['formBuilder']['id']]).$label;
                              } else if($vals['formBuilder']['element_type'] == 'datetime') {
                              $option_result_data[$vals['formBuilder']['id']] = $vals['element_value'].$label;
                              } else if($vals['formBuilder']['element_type'] == 'number') {
                              $unit_number = Unit::find()->where(['id' => $vals['element_unit']])->one();
                              $option_result_data[$vals['formBuilder']['id']] = $vals['element_value']. ' '. $unit_number->unit_name .$label;
                              } else {
                              $option_result_data[$vals['formBuilder']['id']] = $vals['element_value'].$label;
                              }
                              }
                              }
                              }
                              } */
                            //not in email query

                            $dataresult = TasksUnitsData::find()->joinWith('tasksUnits')->where('tbl_tasks_units_data.form_builder_id NOT IN (SELECT id FROM tbl_form_builder WHERE element_view=0 and form_type=2) AND  tbl_tasks_units.task_id=' . $data['project_id'])->all();
                            $formbuilder_id = array();
                            foreach ($dataresult as $tasksUnitDatas) {
                                $label = 'No Label';
                                if ($tasksUnitDatas->formBuilder->element_label != '') {
                                    $label = ' ( ' . $tasksUnitDatas->formBuilder->element_label . ' ) ';
                                }
                                if (in_array($tasksUnitDatas->formBuilder->element_type, array('checkbox'))) {

                                    $i = 0;
                                    $time_fbid = $tasksUnitDatas->created . '_' . $tasksUnitDatas->form_builder_id;
                                    if (in_array($time_fbid, $formbuilder_id)) {
                                        continue;
                                    }
                                    $formbuilder_id[$time_fbid] = $time_fbid;
                                    $value_array = array();
                                    $value_array = (new FormBuilder)->getSelectedOption($tasksUnitDatas->modified, $tasksUnitDatas->form_builder_id, 2);
                                    $option_result_data[] = implode(", ", $value_array) . $label;
                                } else if (in_array($tasksUnitDatas->formBuilder->element_type, array('dropdown', 'radio'))) {
                                    $option_result_data[] = (new FormBuilder)->getSelectedElementOption($tasksUnitDatas->element_value) . $label;
                                } else {
                                    $unit_name = "";
                                    if ($tasksUnitDatas->element_unit > 0) {
                                        $unit_name = " " . $tasksUnitDatas->unit->unit_name;
                                    }
                                    $option_result_data[] = $tasksUnitDatas->element_value . $unit_name . $label;
                                }
                            }

                            if (empty($option_result)) {
                                $option_result[] = 'No Information Available';
                            }
                            if (empty($option_result_data)) {
                                $option_result_data[] = 'No Information Available';
                            }

                            /* Instruction Form Field IRT 446 */
                            if ($template_info->is_instruction_form_field == 1) {
                                $required_value = '[project_instruction_form_fields]';
                                $option_result_view = implode("<br>", $option_result);
                                if (strpos($main_body, $required_value))
                                    $main_body = str_replace($required_value, nl2br(html_entity_decode($option_result_view)), $main_body);
                                if (strpos($main_subject, $vals['element_value']))
                                    $main_subject = str_replace($required_value, nl2br(html_entity_decode($option_result_view)), $main_subject);
                            }

                            /* Data Form Field IRT 446 */
                            if ($template_info->is_data_form_field == 1) {
                                $required_value = '[task_outcome_form_fields]';
                                $option_result_data_view = implode("<br>", $option_result_data);
                                if (strpos($main_body, $required_value))
                                    $main_body = str_replace($required_value, nl2br(html_entity_decode($option_result_data_view)), $main_body);
                                if (strpos($main_subject, $vals['element_value']))
                                    $main_subject = str_replace($required_value, nl2br(html_entity_decode($option_result_data_view)), $main_subject);
                            }
                        }

                        foreach ($get_all_fields as $field) {
                            $d_field = str_replace(array("[", "]"), "", $field);
                            if (strpos($main_body, $field)) {
                                $main_body = str_replace($field, nl2br(html_entity_decode($data[$d_field])), $main_body);
                            }
                            if (strpos($main_subject, $field)) {
                                $main_subject = str_replace($field, nl2br(html_entity_decode($data[$d_field])), $main_subject);
                            }
                        }
                        $main_subject = str_replace("[]", "[ No Information Available ]", $main_subject);
                        $main_body = str_replace("[]", "[ No Information Available ]", $main_body);
                        $body_header = $template_info->email_header;
                        $html = \Yii::$app->view->renderFile('@app/views/layouts/email/emailtemplate.php', ['subject' => $main_subject, 'body' => $main_body, 'body_header' => $body_header], null);
                        $mailer->Subject = html_entity_decode($main_subject);
                        $mailer->IsHTML(true);
                        $mailer->Body = $html;
                        //     echo "<pre>",$html,"</pre>"; die();
                        if ($_SERVER['HTTP_HOST'] != '192.168.0.129' && $_SERVER['HTTP_HOST'] != 'localhost') {
                            $mailer->Send();
                        }
                    }
                    /* Sending Email to Recipients as per system settings */
                }
            }
        }
        return $email_id_array;
    }

    /**
     * get All case role users
     * @return mixed
     */
    public static function getAllCaseRoleUsers($roles, $sub_users) {
        $users = array();
        if (isset($roles) && $roles != "") {
            $optionusers = '';
            if ($sub_users != '')
            //	$optionusers = " AND id IN (".$sub_users.")";
            //$users=CHtml::listData(User::model()->findAll(array('condition'=>"role_id IN (".$roles.")$optionusers",'select'=>array('id'),'group'=>'id')), 'id', 'id');
                $users = ArrayHelper::map(User::find()->where("role_id IN (" . $roles . ") AND id IN (" . $sub_users . ")")->select(['id', 'usr_email', "CONCAT(usr_first_name,' ',usr_lastname) as full_name"])->all(), 'usr_email', 'full_name');
        }
        return $users;
    }

    /**
     * @param $options['task_unit_id'] =  1,2,3,4 string
     * @param $options['case_id'] =  1 int
     * @param $options['task_id'] =  1 int
     */
    public static function getFinalMailReceipents($email_alert, $options = array(), $optionUsers = array()) {
        $sqlOption = "select user_id from tbl_options where $email_alert=1";
        $where = "tbl_project_security.user_id IN($sqlOption)";
        $join = "";
        if (isset($options['team']) && $options['team'] == 'ProjectWasSubmittedTo') {
            if (isset($options['task_id']) && $options['task_id'] != 0) {
                $where = " tbl_tasks_teams.task_id =" . $options['task_id'];
                if ($join == "") {
                    $join = "left join tbl_tasks_teams on tbl_tasks_teams.team_id = tbl_project_security.team_id and tbl_tasks_teams.team_loc = tbl_project_security.team_loc";
                }
            }
            if (isset($options['team_id']) && $options['team_id'] != 0) {
                $where = " tbl_project_security.team_id =" . $options['team_id'] . " AND team_loc = " . $options['team_loc'];
            }
        }
        if (isset($options['case']) && $options['case'] == 'ProjectWasSubmittedTo') {
            $where = " tbl_project_security.client_case_id=" . $options['case_id'];
        }
        if (isset($options['assignOnly']) && $options['assignOnly'] == 'OnlyTeamMembersProjectAssignedTo') {
            $where = " tbl_tasks_units.task_id =" . $options['task_id'];
            $join = "inner join tbl_tasks_units on tbl_tasks_units.unit_assigned_to = tbl_project_security.user_id";
        }
        if (isset($options['assignOnly']) && $options['assignOnly'] == 'OnlyUserAssignedTransitionedTOTask') {
            $where = " tbl_tasks_units.id IN (" . $options['task_unit_id'] . ")";
            $join = "inner join tbl_tasks_units on tbl_tasks_units.unit_assigned_to = tbl_project_security.user_id";
        }
        if (isset($options['assignOnly']) && $options['assignOnly'] == 'OnlyUserAssignedTransitionedTOToDo') {
            $where = " tbl_tasks_units_todos.id IN (" . $options['todo_id'] . ")";
            $join = "inner join tbl_tasks_units_todos on tbl_tasks_units_todos.assigned =tbl_project_security.user_id";
        }
        if (isset($options['teamTask']) && $options['teamTask'] == 'AllTeamMembersTaskTransferredTo') {
            $where = " tbl_project_security.team_id IN (" . $options['team_id'] . ")";
        }
        if (empty($optionUsers)) {
            $not_in_sql = '';
        } else {
            $not_in_sql = ' AND id IN (' . implode(',', $optionUsers) . ')';
        }
        $sql = "select user_id from tbl_project_security $join  WHERE $where";
        $result = ArrayHelper::map(User::find()->select(["usr_email", "CONCAT(usr_first_name,' ',usr_lastname) as full_name"])->where('id IN (' . $sql . ')' . $not_in_sql)->all(), 'usr_email', 'full_name');
        return $result;
    }

    public static function getAllCaseMembersProjectubmittedUnder($case_id, $sub_users) {
        $getCaseUser = array();
        $CasesSecurity_model = ProjectSecurity::find()->select(['tbl_project_security.user_id'])
                ->innerJoin('tbl_user', 'tbl_user.id = tbl_project_security.user_id')
                ->where("tbl_project_security.client_case_id=$case_id AND tbl_user.id!=0 AND tbl_project_security.team_id=0 AND tbl_project_security.user_id IN (" . $sub_users . ")")
                ->groupBy('tbl_project_security.user_id')
                ->all();

        if (!empty($CasesSecurity_model)) {
            foreach ($CasesSecurity_model as $CS_model) {
                $getCaseUser[$CS_model->user_id] = $CS_model->user_id;
            }
        }

        return $getCaseUser;
    }

    /**
     * To get Assigned User's List By Task ID
     * @param int task_id = Primary key of task
     */
    public function getAssignedUserByTaskID($task_id) {
        $sql = "SELECT unit_assigned_to FROM tbl_tasks_units
		INNER JOIN tbl_task_instruct ON tbl_tasks_units.task_instruct_id = tbl_task_instruct.id AND isactive=1
		INNER JOIN tbl_tasks ON tbl_tasks.id = tbl_task_instruct.task_id
		WHERE tbl_task_instruct.task_id=$task_id";
        $usersList = ArrayHelper::map(User::find()->select(['id', "CONCAT(usr_first_name,' ',usr_lastname) as full_name"])->where("id IN ($sql)")->all(), 'id', 'full_name');
        //echo $sql;exit;
        return $usersList;
    }

    /**
     * To get Services List By Task ID
     * @param int task_id = Primary key of task
     */
    public function getService($task_id) {
        $sql = "SELECT servicetask_id FROM tbl_task_instruct_servicetask INNER JOIN tbl_tasks ON tbl_tasks.id = tbl_task_instruct_servicetask.task_id INNER JOIN tbl_task_instruct ON tbl_task_instruct_servicetask.task_instruct_id = tbl_task_instruct.id AND isactive=1 WHERE tbl_task_instruct_servicetask.task_id=$task_id";
        $servicetaskList = ArrayHelper::map(Servicetask::find()->select(['id', "service_task"])->where("id IN ($sql)")->all(), 'id', 'service_task');
        return $servicetaskList;
    }

    /* To get Team services list by Project ID for displaying services in New project posted email alert. */

    public function getTeamServicesByProjectID($task_id) {
        $sql = "SELECT DISTINCT tbl_task_instruct_servicetask.teamservice_id FROM tbl_task_instruct_servicetask INNER JOIN tbl_task_instruct ON tbl_task_instruct_servicetask.task_instruct_id = tbl_task_instruct.id AND isactive=1 WHERE tbl_task_instruct_servicetask.task_id=$task_id";
        $serviceList = ArrayHelper::map(Teamservice::find()->select(['id', "service_name"])->where("id IN ($sql)")->all(), 'id', 'service_name');
        return $serviceList;
    }

    public function getCompletedService($task_id, $teamservice_id) {
        $sql = "SELECT tbl_task_instruct_servicetask.servicetask_id FROM tbl_task_instruct_servicetask INNER JOIN tbl_tasks_units ON tbl_tasks_units.task_instruct_servicetask_id =tbl_task_instruct_servicetask.id INNER JOIN tbl_tasks ON tbl_tasks.id = tbl_task_instruct_servicetask.task_id INNER JOIN tbl_task_instruct ON tbl_task_instruct_servicetask.task_instruct_id = tbl_task_instruct.id AND isactive=1 WHERE tbl_task_instruct_servicetask.task_id=$task_id AND tbl_tasks_units.unit_status = 4 AND tbl_task_instruct_servicetask.teamservice_id = " . $teamservice_id;
        $servicetaskList = ArrayHelper::map(Servicetask::find()->select(['id', "service_task"])->where("id IN ($sql)")->all(), 'id', 'service_task');
        return $servicetaskList;
    }

    /**
     * To get Changed Instruction Data using Active TaskInstruction Object.
     * @param object TaskInstruct of active Instruction
     */
    public function getChangedTaskInstruction($task_id) {
        $html = '';
        $sql = "SELECT DISTINCT form_builder_id FROM (SELECT form_builder_id, element_value
		FROM (
			(SELECT tbl_form_builder.id as form_builder_id, tbl_form_builder.element_id, tbl_form_builder.element_label, tbl_form_instruction_values.element_value FROM tbl_form_builder
			INNER JOIN tbl_task_instruct_servicetask ON tbl_task_instruct_servicetask.servicetask_id = tbl_form_builder.formref_id AND tbl_task_instruct_servicetask.task_id=$task_id
			INNER JOIN tbl_task_instruct ON tbl_task_instruct.id = tbl_task_instruct_servicetask.task_instruct_id AND tbl_task_instruct.isactive=1
			INNER JOIN tbl_form_instruction_values ON tbl_form_instruction_values.form_builder_id = tbl_form_builder.id AND tbl_task_instruct.id = tbl_form_instruction_values.task_instruct_id
			WHERE tbl_form_builder.form_type=1)
			UNION ALL
			(SELECT tbl_form_builder.id as form_builder_id, tbl_form_builder.element_id, tbl_form_builder.element_label, tbl_form_instruction_values.element_value FROM tbl_form_builder
			INNER JOIN tbl_task_instruct_servicetask ON tbl_task_instruct_servicetask.servicetask_id = tbl_form_builder.formref_id AND tbl_task_instruct_servicetask.task_id=$task_id
			INNER JOIN tbl_task_instruct ON tbl_task_instruct.id = tbl_task_instruct_servicetask.task_instruct_id AND tbl_task_instruct.instruct_version = (SELECT instruct_version FROM tbl_task_instruct WHERE  tbl_task_instruct.task_id=$task_id AND tbl_task_instruct.isactive=1)-1
			INNER JOIN tbl_form_instruction_values ON tbl_form_instruction_values.form_builder_id = tbl_form_builder.id AND tbl_task_instruct.id = tbl_form_instruction_values.task_instruct_id
			WHERE tbl_form_builder.form_type=1)
		) as InstructionDiff
		GROUP BY form_builder_id, element_value
		HAVING count(*) = 1) as ElementDiff";

        $formElementArray = array();
        //$query = (new Query)->select(['tbl_form_builder.id','tbl_form_builder.element_type','tbl_form_builder.element_label', 'tbl_form_instruction_values.element_value', "(CASE WHEN ISNUMERIC(tbl_form_instruction_values.element_value) = 1 THEN (SELECT element_option FROM tbl_form_element_options WHERE id = tbl_form_instruction_values.element_value) ELSE '' END) as option_value"])
        $query = (new Query)->select(['tbl_form_builder.id', 'tbl_form_builder.element_type', 'tbl_form_builder.element_label', 'tbl_form_instruction_values.element_value', "(CASE WHEN EXISTS(SELECT * FROM tbl_form_builder WHERE tbl_form_builder.id=tbl_form_instruction_values.form_builder_id AND tbl_form_builder.element_type IN ('checkbox','radio','dropdown')) THEN (SELECT element_option FROM tbl_form_element_options WHERE id = tbl_form_instruction_values.element_value) ELSE '' END) as option_value"])
                ->from('tbl_form_builder')
                ->join('INNER JOIN', 'tbl_task_instruct_servicetask', 'tbl_task_instruct_servicetask.servicetask_id = tbl_form_builder.formref_id')
                ->join('INNER JOIN', 'tbl_task_instruct', 'tbl_task_instruct.id = tbl_task_instruct_servicetask.task_instruct_id AND tbl_task_instruct.isactive=1')
                ->join('INNER JOIN', 'tbl_form_instruction_values', 'tbl_form_instruction_values.task_instruct_id = tbl_task_instruct.id AND tbl_form_instruction_values.form_builder_id = tbl_form_builder.id')
                ->where('tbl_form_builder.id IN (' . $sql . ')')
                ->andWhere(['tbl_task_instruct_servicetask.task_id' => $task_id]);
        $formBuilder = $query->createCommand()->queryAll();

        if (!empty($formBuilder)) {
            foreach ($formBuilder as $element) {
                $class = 'text-warning';
                if ($element['element_type'] == 'dropdown' || $element['element_type'] == 'radio' || $element['element_type'] == 'checkbox') {
                    $final_value = $element['option_value'];
                } else if ($type == 'textarea') {
                    $final_value = nl2br($element['element_value']);
                } else {
                    $final_value = $element['element_value'];
                }

                $html .= '<tr class="row ' . $class . '">';

                if (!in_array($element['id'], $formElementArray)) {
                    $html .= '<td class="label_td">' . $element['element_label'];
                    if ($element['element_label'] != '') {
                        $html .= ':';
                    }
                    $html .= '</td>';
                } else {
                    $html .= '<td class="label_td">&nbsp;</td>';
                }
                $html .= '<td class="value_td">' . nl2br($final_value) . '</td>';
                $html .= '</tr>';
                $formElementArray[$element['id']] = $element['id'];
            }
        }

        if ($html != "")
            $html = "<table>" . $html . "</table>";

        return $html;
    }

    public function getAllCaseMembersANDTeamMember($comment_id, $sub_users) {
        $comment_info = Comments::findOne($comment_id);
        $recipients = array();
        $role_recipients = $team_recipients = array();
        $sub_users = explode(",", $sub_users);
        if (isset($comment_info->commentRoles) && $comment_info->commentRoles != "") {
            /* $sql = "SELECT tbl_comment_roles_users.user_id FROM tbl_comment_roles
              LEFT JOIN tbl_comment_roles_users ON tbl_comment_roles_users.tbl_comment_role_id = tbl_comment_roles.id
              WHERE tbl_comment_roles.comment_id=".$comment_id; */
            $sql = "SELECT tbl_project_security.user_id FROM tbl_comment_roles
                        LEFT JOIN tbl_comment_roles_users ON tbl_comment_roles_users.tbl_comment_role_id = tbl_comment_roles.id
                        LEFT JOIN tbl_comments ON tbl_comments.id = tbl_comment_roles.comment_id
                        LEFT JOIN tbl_tasks ON tbl_tasks.id = tbl_comments.task_id
                        INNER JOIN tbl_project_security ON tbl_project_security.client_case_id = tbl_tasks.client_case_id
                        WHERE tbl_comment_roles.comment_id = " . $comment_id . " GROUP BY tbl_project_security.user_id";
            $caseRole_users = ArrayHelper::map(\Yii::$app->db->CreateCommand($sql)->queryAll(), 'user_id', 'user_id');
            $role_recipients = array_intersect($caseRole_users, $sub_users);
            //$caseRole_users = User::find()->select('id')->where("role_id IN (".$sql.") AND id IN (".$sub_users.")")->all();
            /* $sql="SELECT role_id FROM tbl_comment_roles WHERE comment_id=".$comment_id;
              $caseRole_users=User::find()->select('id')->where("role_id IN (".$sql.") AND id IN (".$sub_users.")")->all(); */
            /* if(!empty($caseRole_users)){
              foreach ($caseRole_users as $cruser) {
              $recipients[$cruser->id]=$cruser->id;
              }
              } */
            //    echo "<pre>",print_r($caseRole_users),"</pre>"; die();
        }
        if (isset($comment_info->commentTeams) && $comment_info->commentTeams != "") {
            $sql = "SELECT tbl_comment_teams_users.user_id FROM tbl_comment_teams
			LEFT JOIN tbl_comment_teams_users ON tbl_comment_teams_users.tbl_comment_team_id = tbl_comment_teams.id
			WHERE tbl_comment_teams.comment_id=" . $comment_id;
            $caseTeam_users = ArrayHelper::map(\Yii::$app->db->CreateCommand($sql)->queryAll(), 'user_id', 'user_id');
            $team_recipients = array_intersect($caseTeam_users, $sub_users);
            /* $sql = "SELECT team_id FROM tbl_comment_teams WHERE comment_id=".$comment_id;
              $data = ProjectSecurity::find()->select('user_id')->where("team_id IN (".$sql.") AND user_id IN (".$sub_users.") AND team_id!=0")->groupBy('user_id')->all();
              if (!empty($data)) {
              foreach ($data as $d) {
              $recipients[$d->user_id] = $d->user_id;
              }
              } */
        }
        $recipients = array_unique(array_merge($role_recipients, $team_recipients));

        return $recipients;
    }

    /**
     * sendEmail function used to send Comment  mail only due to special logic in comment
     * @template_id = email_sort
     * @email_alert = field from tbl_options
     * @data = used to replace with template field.
     */
    public static function sendSummaryCommentMailBG($user_id, $data = array(), $template_id = 24, $email_alert = 'opt_posted_summary_comment') {
        $login_user = User::findOne($user_id);
        $notinemail_ids = array();
        if (isset($data['notinEmail_ids']) && !empty($data['notinEmail_ids'])) { //code to banned same email id
            $notinemail_ids = $data['notinEmail_ids'];
        }
        $email_idsss = array();
        $optionUsers = array(0);
        $optionData = Options::find()->select('user_id')->where($email_alert . '=1')->all();
        //echo "<pre>",print_r($optionData);die;
        if (!empty($optionData)) { //chk first system has at least one user who subscribe to particular alert type
            $team_id = 0;
            $user_info = $login_user;
            $data['user'] = $user_info->usr_first_name . " " . $user_info->usr_lastname;

            $service_info = array();
            if (isset($data['comment_id'])) {
                $comment_info = SummaryComment::findOne($data['comment_id']);
                $data['comment'] = Html::encode($comment_info->comment);
                $data['comment_posted_by'] = $comment_info->createdUser->usr_first_name . " " . $comment_info->createdUser->usr_lastname;
            }
            foreach ($optionData as $optData) {
                $optionUsers[$optData->user_id] = $optData->user_id;
            }

            if (isset($template_id) && $template_id != 0) {
                $template_info = self::find()->where(['email_sort' => $template_id])->one();
                $final_users = array();
                $case_users = array();
                $team_users = array();
                $email_recipient = ((isset($template_info->email_custom_recipients) && $template_info->email_custom_recipients != "") ? $template_info->email_custom_recipients : $template_info->email_recipients);
                if (in_array(1, explode(",", $email_recipient))) { // All Case members project was submitted under
                    if (isset($data['case_id']) && $data['case_id'] != 0) {
                        $options = array();
                        $options['case'] = "ProjectWasSubmittedTo";
                        $options['case_id'] = $data['case_id'];
                        $case_users = self::getFinalMailReceipents($email_alert, $options, $optionUsers);
                    }
                }
                if (in_array(2, explode(",", $email_recipient))) { // All My Team members project was submitted to
                    if (isset($data['team_id']) && $data['team_id'] != 0) {
                        $options = array();
                        $options['team'] = "ProjectWasSubmittedTo";
                        $options['team_id'] = $data['team_id'];
                        $options['team_loc'] = $data['team_loc'];
                        $team_users = self::getFinalMailReceipents($email_alert, $options, $optionUsers);
                    }
                }
                $final_users = array_merge($case_users, $team_users);
                if (!empty($final_users)) {
                    $email_ids = array();
                    if (!empty($final_users)) {
                        foreach ($final_users as $email => $fullname) {
                            $email_ids[$email] = $email;
                            if ($data['user_id'] == "") {
                                $data['user_id'] = $fullname;
                            } else {
                                $data['user_id'] .= ", " . $fullname;
                            }
                        }
                    }
                    if (isset($data['case_id']) && $data['case_id'] != 0) {
                        $caseinfo = ClientCase::findOne($data['case_id']);
                        $data['case_name'] = $caseinfo->case_name;
                        if (!isset($data['client_id'])) {
                            $data['client_name'] = $caseinfo->client->client_name;
                        }
                        $data['team'] = '(No Information Available)';
                    }
                    if (isset($data['team_id']) && $data['team_id'] != 0) {
                        $teaminfo = Team::findOne($data['team_id']);
                        $data['team'] = $teaminfo->team_name;
                        $data['case_name'] = $data['client_name'] = '(No Information Available)';
                    }
                    if (isset($data['team_loc'])) {
                        $teamlocinfo = TeamlocationMaster::findOne($data['team_loc']);
                        $data['team_location'] = $teamlocinfo->team_location_name;
                    }
                    if (isset($email_ids) && !empty($email_ids)) {
                        $mailer = Emailsettings::sendemail();
                        // if object mailer found then following code executes
                        if (!empty($mailer)) {
                            $get_all_fields = ArrayHelper::map(SettingsEmailFields::find()->select('display_name')->all(), 'display_name', 'display_name');
                            foreach ($email_ids as $email) {
                                if (!in_array($email, $notinemail_ids)) { // code to banned same email id
                                    $mailer->AddAddress(trim($email));
                                    $email_id_array[$email] = trim($email);
                                }
                            }
                            if (!empty($template_info->bcc_email_recipients)) {
                                $bccEmails = explode(';', $template_info->bcc_email_recipients);
                                foreach ($bccEmails as $singleEmail) {
                                    $mailer->AddBCC(trim($singleEmail));
                                }
                            }
                            $main_body = ((isset($template_info->email_custom_body) && $template_info->email_custom_body != "") ? nl2br(html_entity_decode($template_info->email_custom_body)) : nl2br(html_entity_decode($template_info->email_body)));
                            $main_subject = ((isset($template_info->email_custom_body) && $template_info->email_custom_subject != "") ? $template_info->email_custom_subject : $template_info->email_subject);
                            //echo "<pre>"; print_r($get_all_fields); exit;
                            foreach ($get_all_fields as $field) {
                                $d_field = str_replace(array("[", "]"), "", $field);
                                if (isset($data[$d_field]) && $data[$d_field] != '') {
                                    if (strtolower($d_field) != 'comment') {
                                        $data[$d_field] = htmlentities($data[$d_field], ENT_COMPAT, 'UTF-8');
                                    }
                                    $dataContent = $data[$d_field];
                                } else {
                                    $dataContent = '(No Information Available)';
                                }
                                if (strpos($main_body, $field)) {
                                    $main_body = str_replace($field, nl2br(html_entity_decode($dataContent)), $main_body);
                                }
                                if (strpos($main_subject, $field)) {
                                    $main_subject = str_replace($field, nl2br(html_entity_decode($dataContent)), $main_subject);
                                }
                            }
                            if (isset($data['team'])) {
                                $data['team'] = htmlentities($data['team'], ENT_COMPAT, 'UTF-8');
                                $main_body = str_replace("[team]", nl2br(html_entity_decode($data['team'])), $main_body);
                            }
                            if (isset($data['case_name'])) {
                                $data['case_name'] = htmlentities($data['case_name'], ENT_COMPAT, 'UTF-8');
                                $main_body = str_replace("[case_name]", nl2br(html_entity_decode($data['case_name'])), $main_body);
                            }
                            if (isset($data['case_name'])) {
                                $data['case_name'] = htmlentities($data['case_name'], ENT_COMPAT, 'UTF-8');
                                $main_body = str_replace("[case_name]", nl2br(html_entity_decode($data['case_name'])), $main_body);
                            }
                            $body_header = $template_info->email_header;
                            $html = \Yii::$app->view->renderFile('@app/views/layouts/email/emailtemplate.php', ['subject' => $main_subject, 'body' => $main_body, 'body_header' => $body_header], null);
                            //echo "<pre>"; print_r($data);
                            //print_r($mailer);
                            //echo $html;die;
                            $mailer->Subject = $main_subject;
                            $mailer->IsHTML(true);
                            $mailer->Body = $html;
                            if ($_SERVER['HTTP_HOST'] != '192.168.0.129' && $_SERVER['HTTP_HOST'] != 'localhost') {
                                $mailer->Send();
                            }
                        }
                        //die('dasfdsg');
                        /** End Send * */
                    }
                    /* Sending Email to Recipients as per system settings */
                }
            }
        }
    }

    /**
     * sendEmail function used to send Comment  mail only due to special logic in comment
     * @template_id = email_sort
     * @email_alert = field from tbl_options
     * @data = used to replace with template field.
     */
    public static function sendCommentMailBG($user_id, $data = array(), $template_id = 12, $email_alert = 'opt_posted_comment') {
        if (!isset($data['comment_email_users'])) {
            return false;
        }
        $login_user = User::findOne($user_id);
        $notinemail_ids = array();
        if (isset($data['notinEmail_ids']) && !empty($data['notinEmail_ids'])) { //code to banned same email id
            $notinemail_ids = $data['notinEmail_ids'];
        }
        $email_idsss = array();
        $optionUsers = array(0);
        $optionData = Options::find()->select('user_id')->where($email_alert . '=1')->all();
        if (!empty($optionData)) { //chk first system has at least one user who subscribe to particular alert type
            $team_id = 0;
            $user_info = $login_user;
            $data['user'] = $user_info->usr_first_name . " " . $user_info->usr_lastname;
            $service_info = array();
            if (isset($data['service_id'])) {
                $service_info = Servicetask::findOne($data['service_id']);
                $data['todo_submitted_by_services'] = $service_info->teamservice->service_name . " - " . $service_info->service_task;
            }
            if (isset($data['servicetask_id'])) {
                $teamLocation = ArrayHelper::map(TeamlocationMaster::find()->select(['id', 'team_location_name'])->where('remove=0')->all(), 'id', 'team_location_name');
                $servicetask_data = Servicetask::findOne($data['servicetask_id']);
                $team_id = $servicetask_data->teamId;
                $data['team_service_task'] = $servicetask_data->service_task;
                $data['previous_team_location'] = $teamLocation[$data['previous_tl']];
                $data['team_location'] = $teamLocation[$data['tl']];
            }
            if (isset($data['todo_id'])) {
                $tododata = TasksUnitsTodos::findOne($data['todo_id']);
                $data['todo'] = Html::decode($tododata->todo);
                $data['todo_submitted_by'] = $tododata->createdUser->usr_first_name . " " . $tododata->createdUser->usr_lastname;
            }
            if (isset($data['comment_id'])) {
                $comment_info = Comments::findOne($data['comment_id']);
                $data['comment'] = Html::encode($comment_info->comment);
                $data['comment_posted_by'] = $comment_info->createdUser->usr_first_name . " " . $comment_info->createdUser->usr_lastname;
            }
            $comment_email_users = explode(",", $data['comment_email_users']);
            if (is_array($comment_email_users) && !empty($comment_email_users)) {
                foreach ($optionData as $optData) {
                    if (in_array($optData->user_id, $comment_email_users)) {
                        $optionUsers[$optData->user_id] = $optData->user_id;
                    }
                }
            }

            if (isset($template_id) && $template_id != 0) {
                $template_info = self::find()->where(['email_sort' => $template_id])->one();
                $final_users = array();
                $case_users = array();
                $team_users = array();
                $team_users_assigned = array();
                $team_users_task_assigned = array();
                $team_users_todo_assigned = array();
                $team_users_task_transferred_to = array();

                /* get Email Recipients */
                if (isset($data['parent_id']) && $data['parent_id'] != "") {
                    $data['comment_id'] = $data['parent_id'];
                }

                $final_users = (new SettingsEmail)->getAllCaseMembersANDTeamMember($data['comment_id'], implode(",", $optionUsers));
                /* get Email Recipients */
                if (!empty($final_users)) {
                    $email_ids = array();
                    $option_data = Options::find()->where($email_alert . '=1 AND user_id IN (' . implode(",", $final_users) . ')')->all();

                    if (!empty($option_data)) {
                        foreach ($option_data as $opdata) {
                            if (isset($opdata->user->usr_email) && $opdata->user->usr_email != "")
                                $email_ids[$opdata->user->usr_email] = $opdata->user->usr_email;
                        }
                    }
                    // echo "<pre>",print_r($email_ids),"</pre>"; die();
                    if (isset($data['case_id'])) {
                        $caseinfo = ClientCase::findOne($data['case_id']);
                        $data['case_name'] = $caseinfo->case_name;
                        $data['case_modified_by'] = $caseinfo->modifiedBy->usr_first_name . " " . $caseinfo->modifiedBy->usr_lastname;
                        $data['sales_represenative'] = $caseinfo->salesRepo->usr_first_name . " " . $caseinfo->salesRepo->usr_lastname;
                        $data['case_total_budget'] = "$" . number_format(($caseinfo->budget_value), 2, '.', ',');
                        //$invoiced=InvoiceFinal::model()->pendingBillInvoiceByCase($data['case_id'],"invoice");
                        //$pending=InvoiceFinal::model()->pendingBillInvoiceByCase($data['case_id']);
                        $case_total_spend = 0; //($invoiced+$pending);
                        $data['case_total_spend'] = "$" . number_format($case_total_spend, 2, '.', ',');
                        if (!isset($data['client_id'])) {
                            $data['client_name'] = $caseinfo->client->client_name;
                        }
                    }

                    if (!isset($_SESSION['usrTZ'])) {
                        $tzoptions_data = Options::find()->where(['user_id' => $user_id])->one();
                        if (isset($tzoptions_data['timezone_id']) && $tzoptions_data['timezone_id'] != "") {
                            $_SESSION['usrTZ'] = $tzoptions_data['timezone_id'];
                        } else {
                            $_SESSION['usrTZ'] = 'America/New_York';
                        }
                    }



                    if (isset($data['project_id'])) {
                        $status_arr = array(0 => 'Not Started', 1 => 'Started', 3 => 'Hold', 4 => 'Complete');
                        $task_info = Tasks::findOne($data['project_id']);
                        // with(array('createdUser'=>array('select'=>array('usr_first_name','usr_lastname'))))->findByPk($data['project_id']);
                        $taskinstruct_info = TaskInstruct::find()->joinWith('taskPriority')->where(['task_id' => $data['project_id'], 'isactive' => 1])->select(['tbl_task_instruct.task_id', 'tbl_task_instruct.project_name', 'tbl_task_instruct.task_duedate', 'tbl_task_instruct.task_timedue', 'tbl_task_instruct.created', 'tbl_task_instruct.instruct_version', 'tbl_task_instruct.id', 'tbl_task_instruct.task_priority', 'tbl_priority_project.priority'])->one();
                        // model()->with(array('priorityproject'=>array('select'=>array('priority'))))->find(array('condition'=>"tasks_id={$data['project_id']} AND isactive='1'",'select'=>array('project_name','task_duedate','task_timedue','created','instruct_version','id')));
                        if (!isset($data['case_id'])) {
                            $caseinfo = ClientCase::findOne($task_info->client_case_id);
                            // ClientCase::model()->with(array('modifiedBy'=>array('select'=>array('usr_first_name','usr_lastname')),'SalesRepo'=>array('select'=>array('usr_first_name','usr_lastname')),'client'=>array('select'=>array('client_name'))))->find(array('select'=>array('case_name','budget_value'),'condition'=>'t.id='.$task_info->client_case_id));
                            // ByPk($task_info->client_case_id);
                            $data['case_name'] = $caseinfo->case_name;
                            $data['case_modified_by'] = $caseinfo->modifiedBy->usr_first_name . " " . $caseinfo->modifiedBy->usr_lastname;
                            $data['sales_represenative'] = $caseinfo->salesRepo->usr_first_name . " " . $caseinfo->salesRepo->usr_lastname;
                            $data['case_total_budget'] = "$" . number_format(($caseinfo->budget_value), 2, '.', ',');
                            // $invoiced=InvoiceFinal::model()->pendingBillInvoiceByCase($task_info->client_case_id,"invoice");
                            // $pending=InvoiceFinal::model()->pendingBillInvoiceByCase($task_info->client_case_id);
                            $case_total_spend = 0; //($invoiced+$pending);
                            $data['case_total_spend'] = "$" . number_format($case_total_spend, 2, '.', ',');
                            if (!isset($data['client_id'])) {
                                $data['client_name'] = $caseinfo->client->client_name;
                            }
                        }

                        $data['project_#'] = $data['project_id'];
                        $data['project_name'] = $taskinstruct_info->project_name;
                        if (isset($data['todo_id']) && $data['todo_id'] != 0) {
                            $data['task_assigned_to'] = $tododata->assignedUser->usr_first_name . " " . $tododata->assignedUser->usr_lastname;
                        } else if (isset($data['task_unit_id']) && $data['task_unit_id'] != 0) {
                            $tasksunitsdata = TasksUnits::findOne($data['task_unit_id']);
                            $data['task_assigned_to'] = $tasksunitsdata->assignedUser->usr_first_name . " " . $tasksunitsdata->assignedUser->usr_lastname;
                        } else {
                            $userList = (new SettingsEmail)->getAssignedUserByTaskID($data['project_id']);
                            $data['task_assigned_to'] = implode(";", $userList); // this function should be in Tasks Model
                        }
                        $data['project_submitted_by'] = $task_info->createdUser->usr_first_name . " " . $task_info->createdUser->usr_lastname;
                        $serviceList = (new SettingsEmail)->getService($data['project_id']); // this function should be in Tasks Model
                        $teamserviceList = (new SettingsEmail)->getTeamServicesByProjectID($data['project_id']); // this function should be in Tasks Model
                        if (isset($data['teamservice_id']) && $data['teamservice_id'] != "") {
                            $data['completed_service'] = (new SettingsEmail)->getCompletedService($data['project_id'], $data['teamservice_id']);
                            if (is_array($data['completed_service'])) {
                                $data['completed_service'] = implode(', ', $data['completed_service']);
                            }
                        }

                        $data['service_name'] = implode("; ", $serviceList);
                        $data['team_services'] = implode("; ", $teamserviceList);
                        if (isset($data['task_unit_id']) && $data['task_unit_id'] != 0) {
                            $tasksunitsdata = TasksUnits::findOne($data['task_unit_id']);
                            $data['team_service_task'] = $tasksunitsdata->taskInstructServicetask->servicetask->service_task;
                        }
                        $data['due_date'] = $data['project_due_date'] = (new Options)->ConvertOneTzToAnotherTz($taskinstruct_info->task_duedate . " " . date('H:i:s', strtotime($taskinstruct_info->task_timedue)), "UTC", $_SESSION["usrTZ"], "MDY");
                        $data['project_due_time'] = (new Options)->ConvertOneTzToAnotherTz($taskinstruct_info->task_duedate . " " . date('H:i:s', strtotime($taskinstruct_info->task_timedue)), "UTC", $_SESSION["usrTZ"], "HIS");
                        $data['project_priority'] = $taskinstruct_info->taskPriority->priority;
                        $data['project_status'] = $status_arr[$task_info->task_status];
                        $data['project_cancel_reason'] = html_entity_decode($task_info->task_cancel_reason);
                        $data['project_complete_%'] = (new Tasks)->getTaskPercentageCompleted($data['project_id'], "case", 0, array(), "NUM");
                        $data['project_start_date'] = (new Options)->ConvertOneTzToAnotherTz($taskinstruct_info->created, "UTC", $_SESSION["usrTZ"], "MDY");
                        $data['instruction_version'] = "V " . $taskinstruct_info->instruct_version;
                        if (isset($data['instrid'])) {
                            $data['instruction_changes'] = '';
                            if ($taskinstruct_info->instruct_version > 1)
                                $data['instruction_changes'] = $taskinstruct_info->project_name . ' ' . (new SettingsEmail)->getChangedTaskInstruction($data['project_id']);
                        }
                    }

                    if (isset($data['case_changes']) && !empty($data['case_changes'])) {
                        $new_changes = "";
                        foreach ($data['case_changes'] as $key => $val)
                            $new_changes .= " Case " . ucwords(str_replace("_", " ", $key)) . " - " . $val . " :";

                        $data['case_changes'] = $new_changes;
                    }

                    /* if(isset($data['task_unit_id']) && $data['task_unit_id']!=0)
                      {
                      $data['team_service_task'] = TasksUnits::model()->getService($data['task_unit_id']);
                      } */
                    /* Sending Email to Recipients as per system settings */

                    if (isset($email_ids) && !empty($email_ids)) {
                        $mailer = Emailsettings::sendemail();

                        //	echo "<pre>",print_r($mailer),"</pre>";	die();
                        // if object mailer found then following code executes
                        if (!empty($mailer)) {
                            $get_all_fields = ArrayHelper::map(SettingsEmailFields::find()->select('display_name')->all(), 'display_name', 'display_name');
                            foreach ($email_ids as $email) {
                                if (!in_array($email, $notinemail_ids)) { // code to banned same email id
                                    $mailer->AddAddress(trim($email));
                                    $email_id_array[$email] = trim($email);
                                }
                            }
                            if (!empty($template_info->bcc_email_recipients)) {
                                $bccEmails = explode(';', $template_info->bcc_email_recipients);
                                foreach ($bccEmails as $singleEmail) {
                                    if ($singleEmail != " ")
                                        $mailer->AddBCC(trim($singleEmail));
                                }
                            }
                            $main_body = ((isset($template_info->email_custom_body) && $template_info->email_custom_body != "") ? nl2br(html_entity_decode($template_info->email_custom_body)) : nl2br(html_entity_decode($template_info->email_body)));
                            $main_subject = ((isset($template_info->email_custom_body) && $template_info->email_custom_subject != "") ? $template_info->email_custom_subject : $template_info->email_subject);
                            foreach ($get_all_fields as $field) {
                                $d_field = str_replace(array("[", "]"), "", $field);
                                if (strtolower($d_field) != 'comment') {
                                    $data[$d_field] = htmlentities($data[$d_field], ENT_COMPAT, 'UTF-8');
                                }
                                if (strpos($main_body, $field) !== false) {
                                    $main_body = str_replace($field, nl2br(html_entity_decode($data[$d_field])), $main_body);
                                }
                                if (strpos($main_subject, $field) !== false) {
                                    $main_subject = str_replace($field, nl2br(html_entity_decode($data[$d_field])), $main_subject);
                                }
                            }
                            $main_subject = str_replace("[]", "[ No Information Available ]", $main_subject);
                            $main_body = str_replace("[]", "[ No Information Available ]", $main_body);
                            $body_header = $template_info->email_header;
                            $html = \Yii::$app->view->renderFile('@app/views/layouts/email/emailtemplate.php', ['subject' => $main_subject, 'body' => $main_body, 'body_header' => $body_header], null);
                            //echo "<pre>"; print_r($data);
                            //echo $html;die;
                            $mailer->Subject = $main_subject;
                            $mailer->IsHTML(true);
                            $mailer->Body = $html;
                            if ($_SERVER['HTTP_HOST'] != '192.168.0.129' && $_SERVER['HTTP_HOST'] != 'localhost') {
                                $mailer->Send();
                            }
                        }
                        /** End Send * */
                    }
                    /* Sending Email to Recipients as per system settings */
                }
            }
        }
    }

    /**
     * sendEmail function used to send mail
     * @template_id = email_sort
     * @email_alert = field from tbl_options
     * @data = used to replace with template field.
     */
    public static function sendEmailBG($user_id, $template_id, $email_alert, $data = array(), $email_alert_val = 1) {

        $login_user = User::findOne($user_id);
        /** teamplate info * */
        $email_id_array = array();
        $notinemail_ids = array();
        if (isset($data['notinEmail_ids']) && !empty($data['notinEmail_ids'])) { //code to banned same email id
            $notinemail_ids = $data['notinEmail_ids'];
        }
        $optionUsers = ArrayHelper::map(Options::find()->where([$email_alert => $email_alert_val])->select(['user_id'])->all(), 'user_id', 'user_id');
        $unitdatafield = array();
        if (!empty($optionUsers)) {
            //chk first system has at least one user who subscribe to particular alert type
            $data['user'] = $login_user->usr_first_name . " " . $login_user->usr_lastname;
            if (isset($data['service_id'])) {
                $service_info = Servicetask::findOne($data['service_id']);
                $data['todo_submitted_by_services'] = $service_info->teamservice->service_name . " - " . $service_info->service_task;
            }
            if (isset($data['servicetask_id'])) {
                $teamLocation = ArrayHelper::map(TeamlocationMaster::find()->select(['id', 'team_location_name'])->where('remove=0')->all(), 'id', 'team_location_name');
                $servicetask_data = Servicetask::findOne($data['servicetask_id']);
                $team_id = $servicetask_data->teamId;
                $data['team_service_task'] = $servicetask_data->service_task;
                $data['previous_team_location'] = $teamLocation[$data['previous_tl']];
                $data['team_location'] = $teamLocation[$data['tl']];
            }
            if (isset($data['todo_id'])) {
                $tododata = TasksUnitsTodos::findOne($data['todo_id']);
                $data['todo'] = Html::decode($tododata->todo);
                $data['todo_submitted_by'] = $tododata->createdUser->usr_first_name . " " . $tododata->createdUser->usr_lastname;
            }
            if (isset($template_id) && $template_id != 0) {

                //die('sm');

                $template_info = self::find()->where(['email_sort' => $template_id])->one();
                $final_users = array();
                $case_users = array();
                $caserole_users = array();
                $team_users = array();
                $team_users_assigned = array();
                $team_users_task_assigned = array();
                $team_users_todo_assigned = array();
                $team_users_task_transferred_to = array();
                $pending_task_assigned_user = array();
                $unassinedusers = array();




                $email_recipient = ((isset($template_info->email_custom_recipients) && $template_info->email_custom_recipients != "") ? $template_info->email_custom_recipients : $template_info->email_recipients);


                /* get Email Recipients */
                if (in_array(1, explode(",", $email_recipient))) { // All Case members project was submitted under
                    if (isset($data['case_id']) && $data['case_id'] != 0) {
                        $options = array();
                        $options['case'] = "ProjectWasSubmittedTo";
                        $options['case_id'] = $data['case_id'];
                        $case_users = self::getFinalMailReceipents($email_alert, $options, $optionUsers);
                    }
                }


                if (in_array(2, explode(",", $email_recipient))) { // All My Team members project was submitted to
                    if (isset($data['project_id']) && $data['project_id'] != 0) {
                        $options = array();
                        $options['team'] = "ProjectWasSubmittedTo";
                        $options['task_id'] = $data['project_id'];
                        $team_users = self::getFinalMailReceipents($email_alert, $options, $optionUsers);
                    }
                }
                if (in_array(3, explode(",", $email_recipient))) { // Only Team members project was assigned to
                    if (isset($data['project_id']) && $data['project_id'] != 0) {
                        $options = array();
                        $options['assignOnly'] = 'OnlyTeamMembersProjectAssignedTo';
                        $options['task_id'] = $data['project_id'];
                        $team_users_assigned = self::getFinalMailReceipents($email_alert, $options, $optionUsers);
                    }
                }
                if (in_array(4, explode(",", $email_recipient))) { // Only to user that has been assigned/transitioned task
                    if (isset($data['task_unit_id']) && $data['task_unit_id'] != 0) {
                        $options = array();
                        $options['assignOnly'] = 'OnlyUserAssignedTransitionedTOTask';
                        $options['task_unit_id'] = $data['task_unit_id'];
                        $team_users_task_assigned = self::getFinalMailReceipents($email_alert, $options, $optionUsers);
                    }
                }

                if (in_array(5, explode(",", $email_recipient))) { // Only to user that has been assigned/transitioned todo
                    if (isset($data['todo_id']) && $data['todo_id'] != 0) {
                        $options = array();
                        $options['assignOnly'] = 'OnlyUserAssignedTransitionedTOToDo';
                        $options['todo_id'] = $data['todo_id'];
                        $team_users_todo_assigned = self::getFinalMailReceipents($email_alert, $options, $optionUsers);
                    }
                }

                if (in_array(6, explode(",", $email_recipient))) { // Only to user that has been assigned/transitioned task that appears directly below a task that was newly flagged the complete status
                    if (isset($data['unit_assigned_to']) && $data['unit_assigned_to'] != 0 && $data['unit_assigned_to'] != '') {
                        if (!empty($optionUsers))
                            $pending_task_assigned_user = ArrayHelper::map(User::find()->select(["usr_email", "CONCAT(usr_first_name,' ',usr_lastname) as full_name"])->where('id IN (' . $data['unit_assigned_to'] . ') AND id IN(' . implode(',', $optionUsers) . ')')->all(), 'usr_email', 'full_name');
                    }
                }
                if (in_array(7, explode(",", $email_recipient))) { // All Team members task was transferred to
                    if (isset($data['servicetask_id']) && $data['servicetask_id'] != 0) {
                        $options = array();
                        $options['teamTask'] = 'AllTeamMembersTaskTransferredTo';
                        if (isset($data['team_id']) && $data['team_id'] != "") {
                            $options['team_id'] = $data['team_id'];
                        } else {
                            if (isset($data['servicetask_id']) && $data['servicetask_id'] != "") {
                                $service_info = Servicetask::findOne($data['servicetask_id']);
                                if (isset($service_info->teamId) && $service_info->teamId > 0)
                                    $options['team_id'] = $service_info->teamId;
                            }
                        }
                        if (isset($options['team_id']) && $options['team_id'] != "")
                            $team_users_task_transferred_to = self::getFinalMailReceipents($email_alert, $options, $optionUsers);
                    }
                }

                // Special case role access & email recipient
                if (in_array(8, explode(",", $email_recipient))) {
                    if ($template_info->email_caserole != "") {
                        if (in_array(1, explode(",", $email_recipient))) {
                            $case_users_ids = self::getAllCaseMembersProjectubmittedUnder($data['case_id'], implode(",", $optionUsers));
                            $casepermittedusers = array_intersect($case_users_ids, $optionUsers);
                            if (!empty($casepermittedusers)) {
                                $caserole_users = self::getAllCaseRoleUsers($template_info->email_caserole, implode(",", $optionUsers));
                            }
                        } else {
                            $caserole_users = self::getAllCaseRoleUsers($template_info->email_caserole, implode(",", $optionUsers));
                        }
                    }
                }
                if ($template_id == 14 && isset($data['current_assigned']) && $data['current_assigned'] != 0 && $data['current_assigned'] != "") { //unassgined from me
                    $unassinedusers = ArrayHelper::map(User::find()->where("id IN (" . $data['current_assigned'] . ")")->select(['id', 'usr_email', "CONCAT(usr_first_name,' ',usr_lastname) as full_name"])->all(), 'usr_email', 'full_name');
                }
                $final_users = array_merge($case_users, $team_users, $team_users_assigned, $team_users_task_assigned, $team_users_todo_assigned, $caserole_users, $pending_task_assigned_user, $unassinedusers);

                /* get Email Recipients */
                if (!empty($final_users)) {
                    $email_ids = array();
                    $data['user_id'] = "";
                    if (!empty($final_users)) {
                        foreach ($final_users as $email => $fullname) {
                            $email_ids[$email] = $email;
                            if ($data['user_id'] == "") {
                                $data['user_id'] = $fullname;
                            } else {
                                $data['user_id'] .= ", " . $fullname;
                            }
                        }
                    }

                    /* prod_id */
                    if (isset($data['prod_id']) && $data['prod_id'] != 0) {
                        $data['production_#'] = $data['prod_id'];
                        $prod_data = EvidenceProduction::findOne($data['prod_id']);
                        $data['producing_party'] = (isset($prod_data->prod_party) && $prod_data->prod_party != "") ? htmlspecialchars($prod_data->prod_party) : '(No Information Available)';
                        $data['staff_assigned'] = (isset($prod_data->staff_assigned) && $prod_data->staff_assigned != "") ? htmlspecialchars($prod_data->staff_assigned) : '(No Information Available)';
                        $data['prod_disclose'] = (isset($prod_data->prod_disclose) && $prod_data->prod_disclose != "") ? htmlspecialchars($prod_data->prod_disclose) : '(No Information Available)';
                        if (isset($prod_data->prod_date) && $prod_data->prod_date != "0000-00-00")
                            $pd = date("m/d/Y", strtotime($prod_data->prod_date));
                        else
                            $pd = "00/00/0000";

                        $data['production_date'] = $pd;
                        //$data['production_date'] = (new EvidenceProduction)->getProdDate($prod_data->prod_date);
                        $data['production_received_date'] = isset($prod_data->prod_rec_date) ? date("m/d/Y", strtotime($prod_data->prod_rec_date)) : "";
                        $data['production_copied_to'] = (isset($prod_data->prod_copied_to) && $prod_data->prod_copied_to != "") ? htmlspecialchars($prod_data->prod_copied_to) : "(No Information Available)";
                        $data['production_cover_letter_link'] = (isset($prod_data->cover_let_link) && $prod_data->cover_let_link != "") ? htmlspecialchars($prod_data->cover_let_link) : "(No Information Available)";
                        $data['production_type'] = (isset($prod_data->production_type) && $prod_data->production_type == 1) ? "Incoming" : "Outgoing";
                        $data['production_description'] = (isset($prod_data->production_desc) && $prod_data->production_desc != "") ? htmlspecialchars($prod_data->production_desc) : "(No Information Available)";
                        $data['prod_access_req'] = (isset($prod_data->prod_access_req) && $prod_data->prod_access_req != "") ? date("m/d/Y", strtotime($prod_data->prod_access_req)) : "(No Information Available)";
                        $data['production_copied_to_location'] = (isset($prod_data->prod_copied_to) && $prod_data->prod_copied_to != "") ? htmlspecialchars($prod_data->prod_copied_to) : "(No Information Available)";
                        $data['prod_misc1'] = (isset($prod_data->prod_misc1) && $prod_data->prod_misc1 != "") ? htmlspecialchars($prod_data->prod_misc1) : "(No Information Available)";
                        $data['prod_misc2'] = (isset($prod_data->prod_misc2) && $prod_data->prod_misc2 != "") ? htmlspecialchars($prod_data->prod_misc2) : "(No Information Available)";
                        $data['production_media_on_hold'] = (isset($prod_data->has_hold) && $prod_data->has_hold == 0) ? "No" : "Yes";

                        if (isset($prod_data->has_media) && ($prod_data->has_media)) {
                            $media_ids = (new EvidenceProductionMedia)->getMediaids($prod_data->id);
                            if (!empty($media_ids))
                                $data['production_media'] = implode(",", $media_ids);
                        } else {
                            $data['production_media'] = "";
                        }
                    }



                    if (isset($data['case_id'])) {
                        $caseinfo = ClientCase::findOne($data['case_id']);
                        $data['case_name'] = $caseinfo->case_name;
                        $data['case_modified_by'] = $caseinfo->modifiedBy->usr_first_name . " " . $caseinfo->modifiedBy->usr_lastname;
                        $data['sales_represenative'] = $caseinfo->salesRepo->usr_first_name . " " . $caseinfo->salesRepo->usr_lastname;
                        $data['case_total_budget'] = "$" . number_format(($caseinfo->budget_value), 2, '.', ',');
                        $case_total_spend = (new InvoiceFinal)->totalspendbudget($data['case_id']);
                        $data['case_total_spend'] = "$" . number_format($case_total_spend, 2, '.', ',');
                        if (!isset($data['client_id'])) {
                            $data['client_name'] = $caseinfo->client->client_name;
                        }
                    }
                    if (isset($data['evidence'])) {
                        /** Evidence * */
                        $data['client_name'] = $data['client_name'];
                        $data['case_name'] = $data['case_name'];
                        $data['case_total_budget'] = $data['case_total_budget'];
                        $data['sales_represenative'] = $data['sales_represenative'];
                        $data['media_id'] = $data['evid_clientcase']['ClientCaseEvidence']['evid_num_id'];
                        $data['media_checkedinby_user'] = $data['user'];
                        $data['user'] = $data['user_id'];
                        $data['media_other_id'] = $data['evidence']['other_evid_num'];
                        $data['media_recieved_date'] = $data['evidence']['received_date'];
                        $data['media_recieved_time'] = $data['evidence']['received_time'];
                        $data['media_received_from'] = $data['evidence']['received_from'];
                        $data['media_internal_id'] = $data['evidence']['evd_Internal_no'];
                        $evidtype = EvidenceType::findOne($data['evidence']['evid_type']);
                        $data['media_type'] = $evidtype->evidence_name;
                        $evidCat = EvidenceCategory::findOne($data['evidence']['cat_id']);
                        $data['media_category'] = $evidCat->category;
                        $data['media_serial_num'] = $data['evidence']['serial'];
                        $data['media_quantity'] = $data['evidence']['quantity'];
                        $data['media_description'] = $data['evidence']['evid_desc'];
                        $data['media_label_description'] = $data['evidence']['evid_label_desc'];
                        $data['media_total_size'] = $data['evidence']['contents_total_size'];
                        $data['media_total_size_compressed'] = $data['evidence']['contents_total_size_comp'];
                        $contentUnit = Unit::findOne($data['evidence']['unit']);
                        $data['media_total_size_unit'] = $contentUnit->unit_name;
                        $contentCompUnit = Unit::findOne($data['evidence']['comp_unit']);
                        $data['media_total_size_compressed_unit'] = $contentCompUnit->unit_name;
                        $data['media_contents_copied_to'] = $data['evidence']['contents_copied_to'];
                        $data['media_begbates'] = $data['evidence']['bbates'];
                        $data['media_endbates'] = $data['evidence']['ebates'];
                        $data['media_volume_#'] = $data['evidence']['m_vol'];
                        $data['media_notes'] = $data['evidence']['evid_notes'];
                        $data['media_barcode_id'] = $data['evidence']['barcode'];
                        if (isset($data['newcontents']) && !empty($data['newcontents'])) {
                            $contentDetail = "<table border='1' style='border:1px solid black!important;'><thead><tr><th style='text-align:left;width:250px;'>Custodian</th><th style='text-align:left;width:300px;'>Data Type</th><th style='text-align:left;'>Data Size</th></tr></thead><tbody>";
                            foreach ($data['newcontents'] as $content) {
                                $custodian = EvidenceCustodians::findOne($content['cust_id']); // Evidence
                                $custodianname = $custodian->cust_fname . " " . $custodian->cust_lname; // cust fname and cust lname
                                $dtype = DataType::findOne($content['data_type']); // get data_type field name
                                $contentUnit = Unit::findOne($content['unit']); // unit find
                                $datasize = $content['data_size'] . " " . $contentUnit->unit_name;
                                $contentDetail .= "<tr><td style='width:250px;'>{$custodianname}</td><td style='border-left:1px solid black!important;width:300px;'>{$dtype->data_type}</td><td style='border-left:1px solid black!important;'>{$datasize}</td></tr>";
                            }
                            $contentDetail .= "</tbody></table>";
                            $data['media_content'] = $contentDetail;
                        }
                    }
                    /** End New Media Add * */
                    if (!isset($_SESSION['usrTZ'])) {
                        $tzoptions_data = Options::find()->where(['user_id' => $user_id])->one();
                        if (isset($tzoptions_data['timezone_id']) && $tzoptions_data['timezone_id'] != "") {
                            $_SESSION['usrTZ'] = $tzoptions_data['timezone_id'];
                        } else {
                            $_SESSION['usrTZ'] = 'America/New_York';
                        }
                    }

                    /* project id */
                    if (isset($data['project_id'])) {
                        if (isset($data['Production']) && is_array($data['Production'])) {
                            
                        } else {
                            if (isset($data['prod_id']) && $data['prod_id'] != 0) {
                                
                            } else {
                                $taskInstructevid_sql = "SELECT DISTINCT prod_id FROM tbl_task_instruct_evidence INNER JOIN tbl_task_instruct on tbl_task_instruct.id=tbl_task_instruct_evidence.task_instruct_id Where tbl_task_instruct.task_id=" . $data['project_id'] . " and tbl_task_instruct.isactive=1 ";
                                $prod_id = Yii::$app->db->createCommand($taskInstructevid_sql)->queryAll();
                                $Production = array();
                                if (!empty($prod_id)) {
                                    foreach ($prod_id as $prod_value) {
                                        $Production[$prod_value['prod_id']] = $prod_value['prod_id'];
                                    }
                                }
                                if (!empty($Production)) {
                                    $data['Production'] = $Production;
                                }
                            }
                        }

                        if (isset($data['Production']) && is_array($data['Production'])) {
                            foreach ($data['Production'] as $prod_id => $production) {
                                $prod_data = EvidenceProduction::findOne($prod_id);
                                if (isset($prod_data)) {
                                    if (isset($data['production_#'])) {
                                        $data['production_#'] = $data['production_#'] . ', ' . $prod_id;
                                    } else {
                                        $data['production_#'] = $prod_id;
                                    }
                                    if (isset($data['producing_party']) && $data['producing_party'] != "") {
                                        $data['producing_party'] = $data['producing_party'] . ', ' . htmlspecialchars($prod_data->prod_party);
                                    } else {
                                        $data['producing_party'] = htmlspecialchars($prod_data->prod_party);
                                    }

                                    if (isset($data['production_received_date'])) {
                                        $prd = isset($prod_data->prod_rec_date) ? date("m/d/Y", strtotime($prod_data->prod_rec_date)) : "00/00/0000";
                                        $data['production_received_date'] = $data['production_received_date'] . ', ' . $prd;
                                    } else {
                                        $data['production_received_date'] = isset($prod_data->prod_rec_date) ? date("m/d/Y", strtotime($prod_data->prod_rec_date)) : "00/00/000";
                                    }
                                    if (isset($data['production_date'])) {
                                        if (isset($prod_data->prod_date) && $prod_data->prod_date != "0000-00-00")
                                            $pd = date("m/d/Y", strtotime($prod_data->prod_date));
                                        else
                                            $pd = "00/00/0000";

                                        $data['production_date'] = $data['production_date'] . ', ' . $pd;
                                    } else {
                                        if (isset($prod_data->prod_date) && $prod_data->prod_date != "0000-00-00")
                                            $pd = date("m/d/Y", strtotime($prod_data->prod_date));
                                        else
                                            $pd = "00/00/0000";

                                        $data['production_date'] = $pd;
                                    }
                                    if (isset($data['production_copied_to']) && $data['production_copied_to'] != "") {
                                        $pct = isset($prod_data->prod_copied_to) ? htmlspecialchars($prod_data->prod_copied_to) : "";
                                        if ($pct != "") {
                                            $data['production_copied_to'] = $data['production_copied_to'] . ', ' . $pct;
                                        }
                                    } else {
                                        $data['production_copied_to'] = isset($prod_data->prod_copied_to) ? htmlspecialchars($prod_data->prod_copied_to) : "";
                                    }
                                    if (isset($data['production_cover_letter_link']) && $data['production_cover_letter_link'] != "") {
                                        $pcll = isset($prod_data->cover_let_link) ? htmlspecialchars($prod_data->cover_let_link) : "";
                                        if ($pcll != "") {
                                            $data['production_cover_letter_link'] = $data['production_cover_letter_link'] . ', ' . $pcll;
                                        }
                                    } else {
                                        $data['production_cover_letter_link'] = isset($prod_data->cover_let_link) ? htmlspecialchars($prod_data->cover_let_link) : "";
                                    }
                                    if (isset($data['production_type'])) {
                                        $pt = (isset($prod_data->production_type) && $prod_data->production_type == 1) ? "Incoming" : "Outgoing";
                                        $data['production_type'] = $data['production_type'] . ', ' . $pt;
                                    } else {
                                        $data['production_type'] = (isset($prod_data->production_type) && $prod_data->production_type == 1) ? "Incoming" : "Outgoing";
                                    }
                                    if (isset($data['production_description']) && $data['production_description'] != "") {
                                        $pd = (isset($prod_data->production_desc) && trim($prod_data->production_desc) != "") ? htmlspecialchars($prod_data->production_desc) : "";
                                        if ($pd != "") {
                                            $data['production_description'] = $data['production_description'] . ', ' . $pd;
                                        }
                                    } else {
                                        $data['production_description'] = (isset($prod_data->production_desc) && trim($prod_data->production_desc) != "") ? htmlspecialchars($prod_data->production_desc) : "";
                                    }
                                    if (isset($data['staff_assigned']) && $data['staff_assigned'] != "") {
                                        $pstaff_assigned = isset($prod_data->staff_assigned) ? htmlspecialchars($prod_data->staff_assigned) : "";
                                        if ($pstaff_assigned != "") {
                                            $data['staff_assigned'] = $data['staff_assigned'] . ', ' . $pstaff_assigned;
                                        }
                                    } else {
                                        $data['staff_assigned'] = isset($prod_data->staff_assigned) ? htmlspecialchars($prod_data->staff_assigned) : "";
                                    }

                                    if (isset($data['prod_disclose']) && $data['prod_disclose'] != "") {
                                        $pprod_disclose = isset($prod_data->prod_disclose) ? $prod_data->prod_disclose : "";
                                        if ($pprod_disclose != "") {
                                            $data['prod_disclose'] = $data['prod_disclose'] . ', ' . $pprod_disclose;
                                        }
                                    } else {
                                        $data['prod_disclose'] = isset($prod_data->prod_disclose) ? $prod_data->prod_disclose : "";
                                    }
                                    if (isset($data['prod_access_req']) && $data['prod_access_req'] != "") {
                                        $pprod_access_req = isset($prod_data->prod_access_req) ? date("m/d/Y", strtotime($prod_data->prod_access_req)) : "";
                                        if ($pprod_access_req != "") {
                                            $data['prod_access_req'] = $data['prod_access_req'] . ', ' . $pprod_access_req;
                                        }
                                    } else {
                                        $data['prod_access_req'] = isset($prod_data->prod_access_req) ? date("m/d/Y", strtotime($prod_data->prod_access_req)) : "";
                                    }
                                    if (isset($data['production_copied_to_location']) && $data['production_copied_to_location'] != "") {
                                        $pctl = isset($prod_data->prod_copied_to) ? htmlspecialchars($prod_data->prod_copied_to) : "";
                                        if ($pctl != "") {
                                            $data['production_copied_to_location'] = $data['production_copied_to_location'] . ', ' . $pctl;
                                        }
                                    } else {
                                        $data['production_copied_to_location'] = isset($prod_data->prod_copied_to) ? htmlspecialchars($prod_data->prod_copied_to) : "";
                                    }


                                    if (isset($data['prod_misc1']) && $data['prod_misc1'] != "") {
                                        $pcmi1 = isset($prod_data->prod_misc1) ? htmlspecialchars($prod_data->prod_misc1) : "";
                                        if ($pcmi1 != "") {
                                            $data['prod_misc1'] = $data['prod_misc1'] . ', ' . $pcmi1;
                                        }
                                    } else {
                                        $data['prod_misc1'] = isset($prod_data->prod_misc1) ? htmlspecialchars($prod_data->prod_misc1) : "";
                                    }

                                    if (isset($data['prod_misc2']) && $data['prod_misc2'] != "") {
                                        $pcmi2 = isset($prod_data->prod_misc2) ? htmlspecialchars($prod_data->prod_misc2) : "";
                                        if ($pcmi2 != "") {
                                            $data['prod_misc2'] = $data['prod_misc2'] . ', ' . $pcmi2;
                                        }
                                    } else {
                                        $data['prod_misc2'] = isset($prod_data->prod_misc2) ? htmlspecialchars($prod_data->prod_misc2) : "";
                                    }

                                    if (isset($data['production_media_on_hold']) && $data['production_media_on_hold'] != "") {
                                        $pchold = (isset($prod_data->has_hold) && $prod_data->has_hold == 0) ? "No" : "Yes";
                                        if ($pchold != "") {
                                            $data['production_media_on_hold'] = $data['production_media_on_hold'] . ', ' . $pchold;
                                        }
                                    } else {
                                        $data['production_media_on_hold'] = (isset($prod_data->has_hold) && $prod_data->has_hold == 0) ? "No" : "Yes";
                                    }




                                    $pm = '';
                                    if (isset($prod_data->has_media) && ($prod_data->has_media)) {
                                        $media_ids = (new EvidenceProductionMedia)->getMediaids($prod_data->id);
                                        if (!empty($media_ids))
                                            if (isset($pm) && $pm != '') {
                                                $pm = $pm . ', ' . implode(",", $media_ids);
                                            } else {
                                                $pm = implode(",", $media_ids);
                                            }
                                    }
                                    if (isset($pm) && $pm != '') {
                                        $data['production_media'] = $pm;
                                    }
                                }
                            }
                            if (trim($data['producing_party']) == "") {
                                $data['producing_party'] = '(No Information Available)';
                            }
                            if (trim($data['production_copied_to']) == "") {
                                $data['production_copied_to'] = '(No Information Available)';
                            }
                            if (trim($data['production_copied_to']) == "") {
                                $data['production_copied_to'] = '(No Information Available)';
                            }
                            if (trim($data['production_cover_letter_link']) == "") {
                                $data['production_cover_letter_link'] = '(No Information Available)';
                            }
                            if (trim($data['production_description']) == "") {
                                $data['production_description'] = '(No Information Available)';
                            }
                            if (trim($data['staff_assigned']) == "") {
                                $data['staff_assigned'] = '(No Information Available)';
                            }
                            if (trim($data['prod_disclose']) == "") {
                                $data['prod_disclose'] = '(No Information Available)';
                            }
                            if (trim($data['prod_disclose']) == "") {
                                $data['prod_disclose'] = '(No Information Available)';
                            }
                            if (trim($data['prod_access_req']) == "") {
                                $data['prod_access_req'] = '(No Information Available)';
                            }
                            if (trim($data['production_copied_to_location']) == "") {
                                $data['production_copied_to_location'] = '(No Information Available)';
                            }
                        }

                        $status_arr = array(0 => 'Not Started', 1 => 'Started', 3 => 'Hold', 4 => 'Complete');
                        $task_info = Tasks::findOne($data['project_id']);
                        $taskinstruct_info = TaskInstruct::find()->joinWith('taskPriority')->where(['task_id' => $data['project_id'], 'isactive' => 1])->select(['tbl_task_instruct.project_name', 'tbl_task_instruct.task_duedate', 'tbl_task_instruct.task_timedue', 'tbl_task_instruct.created', 'tbl_task_instruct.instruct_version', 'tbl_task_instruct.id', 'tbl_task_instruct.task_priority', 'tbl_priority_project.priority'])->one();
                        if (!isset($data['case_id'])) {
                            $caseinfo = ClientCase::findOne($task_info->client_case_id);
                            $data['case_name'] = $caseinfo->case_name;
                            $data['case_modified_by'] = $caseinfo->modifiedBy->usr_first_name . " " . $caseinfo->modifiedBy->usr_lastname;
                            $data['sales_represenative'] = $caseinfo->salesRepo->usr_first_name . " " . $caseinfo->salesRepo->usr_lastname;
                            $data['case_total_budget'] = "$" . number_format(($caseinfo->budget_value), 2, '.', ',');
                            $case_total_spend = 0;
                            $data['case_total_spend'] = "$" . number_format($case_total_spend, 2, '.', ',');
                            if (!isset($data['client_id'])) {
                                $data['client_name'] = $caseinfo->client->client_name;
                            }
                        }
                        $data['project_#'] = $data['project_id'];
                        $data['project_name'] = ($taskinstruct_info->project_name != "" && isset($taskinstruct_info->project_name)) ? $taskinstruct_info->project_name : "(No Information Available)";
                        if (isset($data['todo_id']) && $data['todo_id'] != 0) {
                            $data['task_assigned_to'] = $tododata->assignedUser->usr_first_name . " " . $tododata->assignedUser->usr_lastname;
                        } else if (isset($data['task_unit_id']) && $data['task_unit_id'] != 0) {
                            $tasksunitsdata = TasksUnits::findOne($data['task_unit_id']);
                            $data['task_assigned_to'] = $tasksunitsdata->assignedUser->usr_first_name . " " . $tasksunitsdata->assignedUser->usr_lastname;
                        } else {
                            $userList = (new SettingsEmail)->getAssignedUserByTaskID($data['project_id']);
                            $data['task_assigned_to'] = implode(";", $userList); // this function should be in Tasks Model
                        }
                        $data['project_submitted_by'] = $task_info->createdUser->usr_first_name . " " . $task_info->createdUser->usr_lastname;
                        $serviceList = (new SettingsEmail)->getService($data['project_id']); // this function should be in Tasks Model
                        $teamserviceList = (new SettingsEmail)->getTeamServicesByProjectID($data['project_id']); // this function should be in Tasks Model
                        if (isset($data['teamservice_id']) && $data['teamservice_id'] != "") {
                            $data['completed_service'] = (new SettingsEmail)->getCompletedService($data['project_id'], $data['teamservice_id']);
                            if (is_array($data['completed_service'])) {
                                $data['completed_service'] = implode(', ', $data['completed_service']);
                            }
                        }
                        $data['service_name'] = implode("; ", $serviceList);
                        $data['team_services'] = implode("; ", $teamserviceList);
                        if (isset($data['task_unit_id']) && $data['task_unit_id'] != 0) {
                            $tasksunitsdata = TasksUnits::findOne($data['task_unit_id']);
                            $data['team_service_task'] = $tasksunitsdata->taskInstructServicetask->servicetask->service_task;
                        }
                        $taskduedatetime = explode(" ", $taskinstruct_info->task_date_time);
                        $data['due_date'] = $data['project_due_date'] = $taskduedatetime[0];
                        $data['project_due_time'] = $taskduedatetime[1] . ' ' . $taskduedatetime[2];
                        $data['project_priority'] = $taskinstruct_info->taskPriority->priority;
                        $data['project_status'] = $status_arr[$task_info->task_status];
                        $data['project_cancel_reason'] = html_entity_decode($task_info->task_cancel_reason);
                        $data['project_complete_%'] = (new Tasks)->getTaskPercentageCompleted($data['project_id'], "case", $data['case_id'], 0, 0, "NUM");
                        $data['project_start_date'] = (new Options)->ConvertOneTzToAnotherTz($taskinstruct_info->created, "UTC", $_SESSION["usrTZ"], "MDY");
                        $data['instruction_version'] = "V " . $taskinstruct_info->instruct_version;
                        if (isset($data['instrid'])) {
                            $data['instruction_changes'] = '';
                            if ($taskinstruct_info->instruct_version > 1)
                                $data['instruction_changes'] = $taskinstruct_info->project_name . ' ' . (new SettingsEmail)->getChangedTaskInstruction($data['project_id']);
                        }
                        $prodbates = (new EvidenceProductionBates)->getProdBatesValue($data['project_id']);
                        $data['prod_bbates'] = $prodbates['bbates'];
                        $data['prod_ebates'] = $prodbates['ebates'];
                        $data['prod_vol'] = $prodbates['vol'];
                    }

                    if (isset($data['case_changes']) && !empty($data['case_changes'])) {
                        $new_changes = "";
                        foreach ($data['case_changes'] as $key => $val)
                            $new_changes .= " Case " . ucwords(str_replace("_", " ", $key)) . " - " . $val;

                        $data['case_changes'] = $new_changes;
                    }

                    /* Sending Email to Recipients as per system settings */
                    if (isset($email_ids) && !empty($email_ids)) {

                        $mailer = Emailsettings::sendemail();
                        if ($mailer === false) {
                            return;
                        }
                        $get_template_fields = ArrayHelper::map(SettingEmailTemplateFields::find()->select('field_id')->where(['template_id' => $template_info->id])->all(), 'field_id', 'field_id');
                        $get_template_field_ids = implode(",", $get_template_fields);
                        $get_all_fields = ArrayHelper::map(SettingsEmailFields::find()->select(['display_name'])->where('id IN (' . $get_template_field_ids . ')')->all(), 'display_name', 'display_name');
                        $get_all_fields['[due_date]'] = '[due_date]';
                        $get_all_fields['[producing_party]'] = '[producing_party]';
                        $get_all_fields['[service_name]'] = '[service_name]';
                        $get_all_fields['production_copied_to'] = '[production_copied_to]';
                        if (!empty($unitdatafield)) {
                            foreach ($unitdatafield as $fields => $unitdata) {
                                $get_all_fields["[" . $fields . "]"] = "[" . $fields . "]";
                            }
                        }
                        foreach ($email_ids as $email) {
                            if (!in_array($email, $notinemail_ids)) { // code to banned same email id
                                if ($email != '' && !is_numeric($email)) {
                                    $mailer->AddAddress(trim($email));
                                    $email_id_array[$email] = $email;
                                }
                            }
                        }
                        if (!empty($template_info->bcc_email_recipients)) {
                            $bccEmails = explode(';', $template_info->bcc_email_recipients);
                            foreach ($bccEmails as $singleEmail) {
                                $BCCmail = trim($singleEmail);
                                if ($BCCmail != "") {
                                    $mailer->AddBCC($BCCmail);
                                    $email_id_array[$BCCmail] = $BCCmail;
                                }
                            }
                        }
                        $main_body = ((isset($template_info->email_custom_body) && $template_info->email_custom_body != "") ? nl2br(html_entity_decode($template_info->email_custom_body)) : nl2br(html_entity_decode($template_info->email_body)));
                        $main_subject = ((isset($template_info->email_custom_body) && $template_info->email_custom_subject != "") ? $template_info->email_custom_subject : $template_info->email_subject);

                        

                        /** IRT 446 Field Values * */
                        if (isset($data['project_id'])) {

                            /** Instruction Form Fields IRT 446 * */
                            $result = TaskInstruct::find()->select(['tbl_task_instruct.id'])
                                            ->joinWith(['formInstructionValues' => function(\yii\db\ActiveQuery $query) {
                                                    $query->select(['tbl_form_instruction_values.id', 'tbl_form_instruction_values.element_value', 'tbl_form_instruction_values.element_unit', 'tbl_form_instruction_values.task_instruct_id', 'tbl_form_instruction_values.form_builder_id']);
                                                    $query->joinWith(['formBuilder' => function(\yii\db\ActiveQuery $query) {
                                                            $query->select(['tbl_form_builder.*']);
                                                            $query->joinWith('formElementOptions');
                                                        }]);
                                                }])->where(['tbl_task_instruct.task_id' => $data['project_id'], 'tbl_task_instruct.isactive' => 1])->asArray()->all();
                            foreach ($result as $key => $value) {
                                //echo "<pre>",print_r($value['formInstructionValues']);die;
                                foreach ($value['formInstructionValues'] as $innerKey => $vals) {
                                    $label = 'No Label';
                                    if ($vals['formBuilder']['element_label'] != '') {
                                        $label = '' . htmlspecialchars($vals['formBuilder']['element_label']) . '';
                                    }
                                    /* Instruction form field IRT 446 */
                                    if ($vals['formBuilder']['form_type'] == 1 && $vals['formBuilder']['element_view'] == 1) {
                                        if ($vals['formBuilder']['element_type'] == 'checkbox' || $vals['formBuilder']['element_type'] == 'radio' || $vals['formBuilder']['element_type'] == 'dropdown') {
                                            foreach ($vals['formBuilder']['formElementOptions'] as $keys => $element_val) {
                                                if ($vals['element_value'] == $element_val['id'])
                                                    $option_val[$vals['formBuilder']['id']][] = htmlspecialchars($element_val['element_option']);
                                            }
                                            $option_result[$vals['formBuilder']['id']] = $label . ': ' . implode(" , ", (array) $option_val[$vals['formBuilder']['id']]);
                                        }
                                        else if ($vals['formBuilder']['element_type'] == 'datetime') {
                                            $option_result[$vals['formBuilder']['id']] = $label . ': ' . htmlspecialchars($vals['element_value']);
                                        } else if ($vals['formBuilder']['element_type'] == 'number') {
                                            $unit_number = Unit::find()->where(['id' => $vals['element_unit']])->one();
                                            $option_result[$vals['formBuilder']['id']] = $label . ': ' . htmlspecialchars($vals['element_value']) . ' ' . $unit_number->unit_name;
                                        } else {
                                            $option_result[$vals['formBuilder']['id']] = $label . ': ' . htmlspecialchars($vals['element_value']);
                                        }
                                    }
                                }
                            }

                            /** Task outcome fields IRT 446 * */
                            /* $dataresult = TaskInstruct::find()->select(['tbl_task_instruct.id'])
                              ->joinWith(['tasksUnits' => function(\yii\db\ActiveQuery $query){
                              $query->joinWith(['tasksUnitsData' => function(\yii\db\ActiveQuery $query){
                              $query->select(['tbl_tasks_units_data.id','tbl_tasks_units_data.element_value','tbl_tasks_units_data.element_unit' , 'tbl_tasks_units_data.tasks_unit_id','tbl_tasks_units_data.form_builder_id']);
                              $query->joinWith(['formBuilder' => function(\yii\db\ActiveQuery $query) {
                              $query->select(['tbl_form_builder.*']);
                              $query->joinWith('formElementOptions');
                              }]);
                              }]);
                              }])->where(['tbl_task_instruct.task_id' => $data['project_id'], 'tbl_task_instruct.isactive' => 1])->asArray()->all();
                              $option_val = array();
                              foreach($dataresult as $key => $value) {
                              foreach($value['tasksUnits'] as $innerKey => $values) {
                              foreach($values['tasksUnitsData'] as $taskInnerKey => $vals) {
                              $label='No Label';
                              if($vals['formBuilder']['element_label']!=''){
                              $label = ' ( '.$vals['formBuilder']['element_label'].' ) ';
                              }
                              if($vals['formBuilder']['form_type']==2 && $vals['formBuilder']['element_view']==1){
                              if($vals['formBuilder']['element_type'] == 'checkbox' || $vals['formBuilder']['element_type'] == 'radio' || $vals['formBuilder']['element_type'] == 'dropdown') {
                              foreach($vals['formBuilder']['formElementOptions'] as $keys => $element_val) {
                              if($vals['element_value']==$element_val['id'])
                              $option_val[$vals['formBuilder']['id']][] = $element_val['element_option'];
                              }
                              $option_result_data[$vals['formBuilder']['id']] = implode(" , ",$option_val[$vals['formBuilder']['id']]).$label;
                              } else if($vals['formBuilder']['element_type'] == 'datetime') {
                              $option_result_data[$vals['formBuilder']['id']] = $vals['element_value'].$label;
                              } else if($vals['formBuilder']['element_type'] == 'number') {
                              $unit_number = Unit::find()->where(['id' => $vals['element_unit']])->one();
                              $option_result_data[$vals['formBuilder']['id']] = $vals['element_value']. ' '. $unit_number->unit_name .$label;
                              } else {
                              $option_result_data[$vals['formBuilder']['id']] = $vals['element_value'].$label;
                              }
                              }
                              }
                              }
                              } */
                            //$dataresult = TasksUnitsData::find()->joinWith('tasksUnits')->where(['tbl_tasks_units.task_id' => $data['project_id']])->all();
                            $dataresult = TasksUnitsData::find()->joinWith('tasksUnits')->where('tbl_tasks_units_data.form_builder_id NOT IN (SELECT id FROM tbl_form_builder WHERE element_view=0 and form_type=2) AND  tbl_tasks_units.task_id=' . $data['project_id'])->all();
                            $formbuilder_id = array();
                            foreach ($dataresult as $tasksUnitDatas) {
                                $label = 'No Label';
                                if ($tasksUnitDatas->formBuilder->element_label != '') {
                                    $label = '' . htmlspecialchars($tasksUnitDatas->formBuilder->element_label) . '';
                                }
                                if (in_array($tasksUnitDatas->formBuilder->element_type, array('checkbox'))) {
                                    $i = 0;
                                    $time_fbid = $tasksUnitDatas->created . '_' . $tasksUnitDatas->form_builder_id;
                                    if (in_array($time_fbid, $formbuilder_id)) {
                                        continue;
                                    }
                                    $formbuilder_id[$time_fbid] = $time_fbid;
                                    $option_val = $value_array = array();

                                    $value_array = (new FormBuilder)->getSelectedOption($tasksUnitDatas->modified, $tasksUnitDatas->form_builder_id, 2);

                                    foreach ($value_array as $keys => $element_val) {
                                        $option_val[$keys] = htmlspecialchars($element_val);
                                    }
                                    $option_result_data[] = $label . ': ' . implode(", ", (array) $option_val);
                                } else if (in_array($tasksUnitDatas->formBuilder->element_type, array('dropdown', 'radio'))) {

                                    $option_result_data[] = $label . ': ' . htmlspecialchars((new FormBuilder)->getSelectedElementOption($tasksUnitDatas->element_value));
                                } else {
                                    $unit_name = "";
                                    if ($tasksUnitDatas->element_unit > 0) {
                                        $unit_name = " " . $tasksUnitDatas->unit->unit_name;
                                    }
                                    $option_result_data[] = $label . ': ' . htmlspecialchars($tasksUnitDatas->element_value) . $unit_name;
                                }
                            }

                            if (empty($option_result)) {
                                $option_result[] = 'No Information Available';
                            }
                            if (empty($option_result_data)) {
                                $option_result_data[] = 'No Information Available';
                            }

                            /* Instruction Form Field IRT 446 */
                            if ($template_info->is_instruction_form_field == 1) {
                                $required_value = '[project_instruction_form_fields]';
                                $option_result_view = implode("<br>", $option_result);
                                if (strpos($main_body, $required_value))
                                    $main_body = str_replace($required_value, nl2br(html_entity_decode($option_result_view)), $main_body);
                                if (strpos($main_subject, $vals['element_value']))
                                    $main_subject = str_replace($required_value, nl2br(html_entity_decode($option_result_view)), $main_subject);
                            }

                            /* Data Form Field IRT 446 */
                            if ($template_info->is_data_form_field == 1) {
                                $required_value = '[task_outcome_form_fields]';
                                $option_result_data_view = implode("<br>", $option_result_data);
                                if (strpos($main_body, $required_value))
                                    $main_body = str_replace($required_value, nl2br(html_entity_decode($option_result_data_view)), $main_body);
                                if (strpos($main_subject, $vals['element_value']))
                                    $main_subject = str_replace($required_value, nl2br(html_entity_decode($option_result_data_view)), $main_subject);
                            }
                        }
                        
                        
                        
                        //echo "<pre>",print_r($data),"</pre>";//die;
                        //echo "<pre>",print_r($main_body),"</pre>";//die;
                        foreach ($get_all_fields as $field) {
                            $d_field = str_replace(array("[", "]"), "", $field);
                            //$d_field = $field;
                            $data[$d_field] = htmlentities($data[$d_field], ENT_COMPAT, 'UTF-8');
                            
                            if(nl2br(html_entity_decode($data[$d_field]))!="")
                                $field_to_replace = $field;
                            else
                                $field_to_replace = $d_field;
                            
                            if (strpos($main_body, $field)) {
                                $main_body = str_replace($field_to_replace, nl2br(html_entity_decode($data[$d_field])), $main_body);
                            }
                            if (strpos($main_subject, $field)) {
                                $main_subject = str_replace($field_to_replace, nl2br(html_entity_decode($data[$d_field])), $main_subject);                                
                            }
                            
                        }
                        
                        
                        $main_subject = str_replace("[]", "[ No Information Available ]", $main_subject);
                        $main_body = str_replace("[]", "[ No Information Available ]", $main_body);
                        $body_header = $template_info->email_header;
                        $html = \Yii::$app->view->renderFile('@app/views/layouts/email/emailtemplate.php', ['subject' => $main_subject, 'body' => $main_body, 'body_header' => $body_header], null);
                        $mailer->Subject = html_entity_decode($main_subject);
                        $mailer->IsHTML(true);
                        $mailer->Body = $html;
                        //echo "<pre> HTML : <br>",$html,"<br> Subject <br>".html_entity_decode($main_subject)."</pre>"; die();
                        if ($_SERVER['HTTP_HOST'] != '192.168.0.129' && $_SERVER['HTTP_HOST'] != 'localhost') {
                            $mailer->Send();
                        }
                    }
                    /* Sending Email to Recipients as per system settings */
                }
            }
        }
        return $email_id_array;
    }

    /**
     * sendEmail function used to send mail
     * @template_id = email_sort
     * @email_alert = field from tbl_options
     * @data = used to replace with template field.
     */
    public static function sendPastDueEmail($user_id, $template_id, $email_alert, $data = array(), $email_alert_val = 1) {
        $login_user = User::findOne($user_id);
        $data['user'] = $login_user->usr_first_name . " " . $login_user->usr_lastname;
        $fullname = $login_user->usr_first_name . " " . $login_user->usr_lastname;
        $notinemail_ids = array();
        /** teamplate info * */
        $email_id_array = array();
        $unitdatafield = array();
        if (isset($data['servicetask_id'])) {
            $teamLocation = ArrayHelper::map(TeamlocationMaster::find()->select(['id', 'team_location_name'])->where('remove=0')->all(), 'id', 'team_location_name');
            $servicetask_data = Servicetask::findOne($data['servicetask_id']);
            $team_id = $servicetask_data->teamId;
            $data['team_service_task'] = $servicetask_data->service_task;
            $data['previous_team_location'] = $teamLocation[$data['previous_tl']];
            $data['team_location'] = $teamLocation[$data['tl']];
        }
        if (isset($template_id) && $template_id != 0) {
            $template_info = self::find()->where(['email_sort' => $template_id])->one();
            $final_users = array();
            $email_recipient = ((isset($template_info->email_custom_recipients) && $template_info->email_custom_recipients != "") ? $template_info->email_custom_recipients : $template_info->email_recipients);
            $case_users = array();
            $team_users = array();
            if ($template_id == 25 || $template_id == 10) {
                $final_users[$login_user->usr_email] = $fullname;
            } else {
                if (in_array(1, explode(",", $email_recipient))) { // All Case members project was submitted under
                    if (isset($data['case_id']) && $data['case_id'] != 0) {
                        $options = array();
                        $options['case'] = "ProjectWasSubmittedTo";
                        $options['case_id'] = $data['case_id'];
                        $case_users = self::getFinalMailReceipents($email_alert, $options, $optionUsers);
                    }
                }

                if (in_array(2, explode(",", $email_recipient))) { // All My Team members project was submitted to
                    if (isset($data['project_id']) && $data['project_id'] != 0) {
                        $options = array();
                        $options['team'] = "ProjectWasSubmittedTo";
                        $options['task_id'] = $data['project_id'];
                        $team_users = self::getFinalMailReceipents($email_alert, $options, $optionUsers);
                    }
                }
                $final_users = array_merge($case_users, $team_users);
            }
            /* get Email Recipients */
            if (!empty($final_users)) {
                $email_ids = array();
                $data['user_id'] = "";
                if (!empty($final_users)) {
                    foreach ($final_users as $email => $fullname) {
                        $email_ids[$email] = $email;
                        if ($data['user_id'] == "") {
                            $data['user_id'] = $fullname;
                        } else {
                            $data['user_id'] .= ", " . $fullname;
                        }
                    }
                }
                if (isset($data['case_id'])) {
                    $caseinfo = ClientCase::findOne($data['case_id']);
                    $data['case_name'] = $caseinfo->case_name;
                    $data['case_modified_by'] = $caseinfo->modifiedBy->usr_first_name . " " . $caseinfo->modifiedBy->usr_lastname;
                    $data['sales_represenative'] = $caseinfo->salesRepo->usr_first_name . " " . $caseinfo->salesRepo->usr_lastname;
                    $data['case_total_budget'] = "$" . number_format(($caseinfo->budget_value), 2, '.', ',');
                    $case_total_spend = (new InvoiceFinal)->totalspendbudget($data['case_id']);
                    $data['case_total_spend'] = "$" . number_format($case_total_spend, 2, '.', ',');
                    if (!isset($data['client_id'])) {
                        $data['client_name'] = $caseinfo->client->client_name;
                    }
                }
                /* project id */
                if (isset($data['project_id'])) {
                    $status_arr = array(0 => 'Not Started', 1 => 'Started', 3 => 'Hold', 4 => 'Complete');
                    $task_info = Tasks::findOne($data['project_id']);
                    $taskinstruct_info = TaskInstruct::find()->joinWith('taskPriority')->where(['task_id' => $data['project_id'], 'isactive' => 1])->select(['tbl_task_instruct.project_name', 'tbl_task_instruct.task_duedate', 'tbl_task_instruct.task_timedue', 'tbl_task_instruct.created', 'tbl_task_instruct.instruct_version', 'tbl_task_instruct.id', 'tbl_task_instruct.task_priority', 'tbl_priority_project.priority'])->one();
                    if (!isset($data['case_id'])) {
                        $caseinfo = ClientCase::findOne($task_info->client_case_id);
                        $data['case_name'] = $caseinfo->case_name;
                        $data['case_modified_by'] = $caseinfo->modifiedBy->usr_first_name . " " . $caseinfo->modifiedBy->usr_lastname;
                        $data['sales_represenative'] = $caseinfo->salesRepo->usr_first_name . " " . $caseinfo->salesRepo->usr_lastname;
                        $data['case_total_budget'] = "$" . number_format(($caseinfo->budget_value), 2, '.', ',');
                        $case_total_spend = 0;
                        $data['case_total_spend'] = "$" . number_format($case_total_spend, 2, '.', ',');
                        if (!isset($data['client_id'])) {
                            $data['client_name'] = $caseinfo->client->client_name;
                        }
                    }
                    $data['project_#'] = $data['project_id'];
                    $data['project_name'] = ($taskinstruct_info->project_name != "" && isset($taskinstruct_info->project_name)) ? $taskinstruct_info->project_name : "(No Information Available)";
                    if (isset($data['todo_id']) && $data['todo_id'] != 0) {
                        $data['task_assigned_to'] = $tododata->assignedUser->usr_first_name . " " . $tododata->assignedUser->usr_lastname;
                    } else if (isset($data['task_unit_id']) && $data['task_unit_id'] != 0) {
                        $tasksunitsdata = TasksUnits::findOne($data['task_unit_id']);
                        $data['task_assigned_to'] = $tasksunitsdata->assignedUser->usr_first_name . " " . $tasksunitsdata->assignedUser->usr_lastname;
                    } else {
                        $userList = (new SettingsEmail)->getAssignedUserByTaskID($data['project_id']);
                        $data['task_assigned_to'] = implode(";", $userList); // this function should be in Tasks Model
                    }
                    $data['project_submitted_by'] = $task_info->createdUser->usr_first_name . " " . $task_info->createdUser->usr_lastname;
                    $serviceList = (new SettingsEmail)->getService($data['project_id']); // this function should be in Tasks Model
                    $teamserviceList = (new SettingsEmail)->getTeamServicesByProjectID($data['project_id']); // this function should be in Tasks Model
                    if (isset($data['teamservice_id']) && $data['teamservice_id'] != "") {
                        $data['completed_service'] = (new SettingsEmail)->getCompletedService($data['project_id'], $data['teamservice_id']);
                        if (is_array($data['completed_service'])) {
                            $data['completed_service'] = implode(', ', $data['completed_service']);
                        }
                    }
                    $data['service_name'] = implode("; ", $serviceList);
                    $data['team_services'] = implode("; ", $teamserviceList);
                    if (isset($data['task_unit_id']) && $data['task_unit_id'] != 0) {
                        $tasksunitsdata = TasksUnits::findOne($data['task_unit_id']);
                        $data['team_service_task'] = $tasksunitsdata->taskInstructServicetask->servicetask->service_task;
                    }
                    $taskduedatetime = explode(" ", $taskinstruct_info->task_date_time);
                    $data['due_date'] = $data['project_due_date'] = $taskduedatetime[0];
                    $data['project_due_time'] = $taskduedatetime[1] . ' ' . $taskduedatetime[2];
                    $data['project_priority'] = $taskinstruct_info->taskPriority->priority;
                    $data['project_status'] = $status_arr[$task_info->task_status];
                    $data['project_cancel_reason'] = html_entity_decode($task_info->task_cancel_reason);
                    $data['project_complete_%'] = (new Tasks)->getTaskPercentageCompleted($data['project_id'], "case", $data['case_id'], 0, 0, "NUM");
                    $data['project_start_date'] = (new Options)->ConvertOneTzToAnotherTz($taskinstruct_info->created, "UTC", $_SESSION["usrTZ"], "MDY");
                    $data['instruction_version'] = "V " . $taskinstruct_info->instruct_version;
                    if (isset($data['instrid'])) {
                        $data['instruction_changes'] = '';
                        if ($taskinstruct_info->instruct_version > 1)
                            $data['instruction_changes'] = $taskinstruct_info->project_name . ' ' . (new SettingsEmail)->getChangedTaskInstruction($data['project_id']);
                    }
                }

                if (isset($data['case_changes']) && !empty($data['case_changes'])) {
                    $new_changes = "";
                    foreach ($data['case_changes'] as $key => $val)
                        $new_changes .= " Case " . ucwords(str_replace("_", " ", $key)) . " - " . $val;

                    $data['case_changes'] = $new_changes;
                }
                //echo "<prE> here",print_r($email_ids),"</pre>";die;
                /* Sending Email to Recipients as per system settings */
                if (isset($email_ids) && !empty($email_ids)) {

                    $mailer = Emailsettings::sendemail();
                    if ($mailer === false) {
                        return;
                    }
                    $get_template_fields = ArrayHelper::map(SettingEmailTemplateFields::find()->select('field_id')->where(['template_id' => $template_info->id])->all(), 'field_id', 'field_id');
                    //echo "<prE> here",print_r($get_template_fields),"</pre>";die;
                    $get_template_field_ids = implode(",", $get_template_fields);
                    $get_all_fields = ArrayHelper::map(SettingsEmailFields::find()->select(['display_name'])->where('id IN (' . $get_template_field_ids . ')')->all(), 'display_name', 'display_name');
                    $get_all_fields['[due_date]'] = '[due_date]';
                    $get_all_fields['[producing_party]'] = '[producing_party]';
                    $get_all_fields['[service_name]'] = '[service_name]';
                    if (!empty($unitdatafield)) {
                        foreach ($unitdatafield as $fields => $unitdata) {
                            $get_all_fields["[" . $fields . "]"] = "[" . $fields . "]";
                        }
                    }
                    foreach ($email_ids as $email) {
                        if (!in_array($email, $notinemail_ids)) { // code to banned same email id
                            if ($email != '' && !is_numeric($email)) {
                                $mailer->AddAddress(trim($email));
                                $email_id_array[$email] = $email;
                            }
                        }
                    }
                    if (!empty($template_info->bcc_email_recipients)) {
                        $bccEmails = explode(';', $template_info->bcc_email_recipients);
                        foreach ($bccEmails as $singleEmail) {
                            $BCCmail = trim($singleEmail);
                            if ($BCCmail != "") {
                                $mailer->AddBCC($BCCmail);
                                $email_id_array[$BCCmail] = $BCCmail;
                            }
                        }
                    }
                    $main_body = ((isset($template_info->email_custom_body) && $template_info->email_custom_body != "") ? nl2br(html_entity_decode($template_info->email_custom_body)) : nl2br(html_entity_decode($template_info->email_body)));
                    $main_subject = ((isset($template_info->email_custom_body) && $template_info->email_custom_subject != "") ? $template_info->email_custom_subject : $template_info->email_subject);



                    foreach ($get_all_fields as $field) {
                        $d_field = str_replace(array("[", "]"), "", $field);
                        if (strpos($main_body, $field)) {
                            $main_body = str_replace($field, nl2br(html_entity_decode($data[$d_field])), $main_body);
                        }
                        if (strpos($main_subject, $field)) {
                            $main_subject = str_replace($field, nl2br(html_entity_decode($data[$d_field])), $main_subject);
                        }
                    }
                    $main_subject = str_replace("[]", "[ No Information Available ]", $main_subject);
                    $main_body = str_replace("[]", "[ No Information Available ]", $main_body);
                    $body_header = $template_info->email_header;
                    $html = \Yii::$app->view->renderFile('@app/views/layouts/email/emailtemplate.php', ['subject' => $main_subject, 'body' => $main_body, 'body_header' => $body_header], null);
                    $mailer->Subject = html_entity_decode($main_subject);
                    $mailer->IsHTML(true);
                    $mailer->Body = $html;
                    //echo "<pre>",$html,"</pre>",print_r($mailer); die();
                    if ($_SERVER['HTTP_HOST'] != '192.168.0.128' && $_SERVER['HTTP_HOST'] != 'localhost') {
                        $mailer->Send();
                    }
                }
                /* Sending Email to Recipients as per system settings */
            }
        }

        return $email_id_array;
    }

}
