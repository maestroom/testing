<?php

namespace app\models;

use Yii;
use app\models\CaseCloseType;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * This is the model class for table "{{%client_case}}".
 * @property integer $id
 * @property integer $client_id
 * @property string $case_name
 * @property string $description
 * @property integer $case_type_id
 * @property integer $case_close_id
 * @property string $case_matter_no
 * @property string $internal_ref_no
 * @property string $counsel_name
 * @property integer $is_close
 * @property string $close_reason
 * @property integer $sales_user_id
 * @property string $case_manager
 * @property double $budget_value
 * @property double $budget_alert
 * @property string $created
 * @property integer $created_by
 * @property string $modified
 * @property integer $modified_by
 * @property CaseContacts[] $caseContacts
 * @property CaseXteam[] $caseXteams
 * @property Client $client
 * @property Client $client0
 * @property Client $client1
 * @property CaseType $caseType
 * @property CaseCloseType $caseClose
 * @property ClientCaseCustodians[] $clientCaseCustodians
 * @property ClientCaseEvidence[] $clientCaseEvidences
 * @property ClientCaseSummary[] $clientCaseSummaries
 * @property ClientCaseSummary[] $clientCaseSummaries0
 * @property ClientCaseSummary[] $clientCaseSummaries1
 * @property EvidenceProduction[] $evidenceProductions
 * @property EvidenceProduction[] $evidenceProductions0
 * @property FormCustodianValues[] $formCustodianValues
 * @property InvoiceBatchClientCase[] $invoiceBatchClientCases
 * @property PricingClientscases[] $pricingClientscases
 * @property PricingTemplates[] $pricingTemplates
 * @property Tasks[] $tasks
 */
class ClientCase extends \yii\db\ActiveRecord
{

    public $iscontactexist = 0;
    public $task_count = 0;
    public $client_name;
    public $scenario;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%client_case}}';
    }
//    public function afterFind()
//    {        
//        //if($this->scenario == 'clientcase_list'){
//            if(!empty($this)){
//                foreach($this as $index => $single){
//                    if(isset($this[$index]['name']) && $this[$index]['name'] != ''){
//                        $this[$index]['name'] = htmlspecialchars_decode($single['name']);
//                    }
//                }
//            }
////            echo var_dump($this);            
////            die('nelson');
//        //}        
//        parent::afterFind();
//    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['client_id', 'case_name'], 'required'],
            [['client_id', 'case_type_id', 'case_close_id', 'is_close', 'sales_user_id','created_by', 'modified_by','iscontactexist'], 'integer'],
            [['case_close_id'], 'required','when'=>function($model){ return $model->is_close == 1;},'whenClient' => "function (attribute, value) {
		        return $('#clientcase-is_close').val() == 1;
		    }"],
		    [['created', 'modified'], 'safe'],
            [['case_name', 'counsel_name'], 'string'],
            [['description', 'close_reason'], 'string'],
            [['case_matter_no', 'internal_ref_no'], 'string'],
            [['case_manager'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'client_id' => Yii::t('app', 'Client ID'),
            'case_name' => Yii::t('app', 'Case Name'),
            'description' => Yii::t('app', 'Case Description'),
            'case_type_id' => Yii::t('app', 'Case Type'),
            'case_close_id' => Yii::t('app', 'Case Close Type'),
            'case_matter_no' => Yii::t('app', 'Case Matter No'),
            'internal_ref_no' => Yii::t('app', 'Internal Reference No'),
            'counsel_name' => Yii::t('app', 'Counsel Name'),
            'is_close' => Yii::t('app', 'Case Closed'),
            'close_reason' => Yii::t('app', 'Case Close/Open Notes'),
            'sales_user_id' => Yii::t('app', 'Sales Representative'),
            'case_manager' => Yii::t('app', 'Case Manager'),
            'budget_value' => Yii::t('app', 'Budget Value'),
            'budget_alert' => Yii::t('app', 'Alert Value'),
            'created' => Yii::t('app', 'Created'),
            'created_by' => Yii::t('app', 'Created By'),
            'modified' => Yii::t('app', 'Modified'),
            'modified_by' => Yii::t('app', 'Modified By'),
        	'iscontactexist' => Yii::t('app', 'Is Contact Exist'),
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
    			$this->created_by = Yii::$app->user->identity->id;
    			$this->modified = date('Y-m-d H:i:s');
    			$this->modified_by = Yii::$app->user->identity->id;
    			$this->is_close = 0;
    			$this->budget_value = 0;
    			$this->budget_alert = 0;
    		}else{
    			$this->modified = date('Y-m-d H:i:s');
    			$this->modified_by = Yii::$app->user->identity->id;
    		}
    		// Place your custom code here
    		return true;
    	} else {
    		return false;
    	}
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCaseContacts()
    {
        return $this->hasMany(CaseContacts::className(), ['case_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCaseXteams()
    {
        return $this->hasMany(CaseXteam::className(), ['client_case_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClient()
    {
        return $this->hasOne(Client::className(), ['id' => 'client_id']);
    }

     /**
     * @return \yii\db\ActiveQuery
     */
    public function getCaseType()
    {
        return $this->hasOne(CaseType::className(), ['id' => 'case_type_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCaseClose()
    {
        return $this->hasOne(CaseCloseType::className(), ['id' => 'case_close_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClientCaseCustodians()
    {
        return $this->hasMany(ClientCaseCustodians::className(), ['case_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClientCaseEvidences()
    {
        return $this->hasMany(ClientCaseEvidence::className(), ['client_case_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClientCaseSummaries()
    {
        return $this->hasMany(ClientCaseSummary::className(), ['client_case_id' => 'id']);
    }

     /**
     * @return \yii\db\ActiveQuery
     */
    public function getEvidenceProductions()
    {
        return $this->hasMany(EvidenceProduction::className(), ['case_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFormCustodianValues()
    {
        return $this->hasMany(FormCustodianValues::className(), ['client_case_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInvoiceBatchClientCases()
    {
        return $this->hasMany(InvoiceBatchClientCase::className(), ['client_case_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPricingClientscases()
    {
        return $this->hasMany(PricingClientscases::className(), ['case_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPricingTemplates()
    {
        return $this->hasMany(PricingTemplates::className(), ['client_case_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTasks()
    {
        return $this->hasMany(Tasks::className(), ['client_case_id' => 'id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProjectSecurity()
    {
        return $this->hasMany(ProjectSecurity::className(), ['client_case_id' => 'id']);
    }
	/**
     * @return \yii\db\ActiveQuery
     */
    public function getModifiedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'modified_by']);
    }
    
	/**
     * @return \yii\db\ActiveQuery
     */
    public function getSalesRepo()
    {
        return $this->hasOne(User::className(), ['id' => 'sales_user_id']);
    }
    
	/**
     * @return mixed
     */
    public function checkContactExist()
    {
        return intval($this->iscontactexist);
    }
    
	/**
     * @return mixed
     */
    public function getCaseCloseTypeByID($case_type_id)
    {
        return (new CaseCloseType)->findOne($case_type_id)->close_type;
    }
    
    public function getClientId($caseId){
 		$caseInfo=ClientCase::findOne($caseId);
 		return $caseInfo->client_id;
    }
    
    /* get search cases results based on selected clients in my case landing page */
    public function getCaseSearchResults($term, $caseId, $ismagnified="")
 	{
 		$searchArray=array();
 		$user_id=Yii::$app->user->identity->id;
 		if($term!="comment_search")
 		{
			if(isset($caseId) && $caseId!="")
			{
				//comment table
                static $securityteam_ids = array();
                if (empty($securityteam_ids)) {
                    $securityteam_ids = (new ProjectSecurity)->getUserTeamsArr($user_id);
                    if (empty($securityteam_ids))
                        $securityteam_ids = array(0);
                }
        
                $roleId = Yii::$app->user->identity->role_id;
                $role_types = explode(",", $roleId);
                $comment_data = Comments::find()->select(['tbl_comments.Id','tbl_comments.comment','tbl_comments.task_id','tbl_comments.created_by','tbl_tasks.client_case_id','tbl_client_case.case_name','tbl_client_case.id','usr_first_name' , 'usr_lastname', 'tbl_comments.comment_origination','tbl_comments_read.user_id', 'tbl_comment_teams.team_id','tbl_comment_roles.role_id'])->joinWith(['commentsRead', 'commentRoles', 'commentTeams', 'tasks' => function (\yii\db\ActiveQuery $query) { $query->joinWith(['clientCase', 'createdUser']); }])->where("tbl_comments.comment LIKE :term AND tbl_tasks.client_case_id IN ($caseId)", [':term' => '%'.$term.'%'])->all();
                
				if(!empty($comment_data))
				{
					foreach ($comment_data as $comments)
					{
						$has_access = false;
                        if ($comments->created_by == $user_id) 
                        {
                            $has_access = true;
                        } 
                        else 
                        {
                            if (isset($comments->commentTeams->team_id) && $comments->commentTeams->team_id != "") 
                            {
                                if (in_array($comments->commentTeams->team_id, $securityteam_ids)) 
                                {
                                    $has_access = true;
                                }
                            }

                            if (isset($comments->commentRoles->role_id) && $comments->commentRoles->role_id != "") 
                            {
                                $roles = Role::find()->select(['id'])->andWhere("id = " . $comments->commentRoles->role_id . " AND id > 0")->one();
                                if(isset($roles->id))
                                {
                                    if (in_array($roles->id, $role_types)) 
                                    {
                                        $has_access = true;
                                    }
                                }  
                            }
                        }
						if($has_access){
							$case_detail=strtoupper($comments->tasks->clientCase->client->client_name.' - '. $comments->tasks->clientCase->case_name);
							$green_link=$case_detail." submitted By ".$comments->tasks->createdUser->usr_first_name." ".$comments->tasks->createdUser->usr_lastname;
							$searchArray[]=array('title'=>"Comment - Project #".$comments->task_id,'value'=>$comments->comment,'task_id'=>$comments->task_id,'caseId'=>$comments->tasks->client_case_id,'green_link'=>$green_link,'origination'=>'comment','id'=>$comments->Id);
						}
					}
				}
				
				// tbl_tasks_units_todos
                
                $todo_data = TasksUnitsTodos::find()->select(['tbl_tasks_units_todos.id', 'tbl_tasks_units_todos.todo','tbl_tasks_units_todos.tasks_unit_id','tbl_tasks_units.task_id','tbl_tasks.client_case_id','tbl_client_case.case_name','usr_first_name' , 'usr_lastname'])
                ->joinWith(['taskUnit'=> function (\yii\db\ActiveQuery $query) { 
					$query->joinWith(['tasks' => function (\yii\db\ActiveQuery $query) { 
						$query->joinWith([ 'clientCase', 'createdUser']); 
					}]);
				}])->where("tbl_tasks_units_todos.todo LIKE :term AND tbl_tasks.client_case_id IN ($caseId)", [':term' => '%'.$term.'%'])->all();
                
				if(!empty($todo_data))
				{                                    
                                    foreach ($todo_data as $todo)
                                    {
                                        $case_detail=strtoupper($todo->taskUnit->tasks->clientCase->client->client_name.' - '. $todo->taskUnit->tasks->clientCase->case_name);
                                        $green_link=$case_detail." submitted By ".$todo->createdUser->usr_first_name." ".$todo->createdUser->usr_lastname;
                                        $searchArray[]=array('title'=>"ToDo - Project #".$todo->taskUnit->task_id,'value'=>$todo->todo,'task_id'=>$todo->taskUnit->task_id,'caseId'=>$todo->taskUnit->tasks->client_case_id,'green_link'=>$green_link,'origination'=>'todo','id'=>$todo->id);
                                    }
				}
				
				//instruction table
                $taskinstruction_data = TaskInstruct::find()->select(['tbl_task_instruct.project_name', 'tbl_task_instruct.task_id','tbl_tasks.client_case_id','tbl_client_case.case_name','tbl_client_case.id','usr_first_name' , 'usr_lastname'])->joinWith(['tasks' => function (\yii\db\ActiveQuery $query) { $query->joinWith(['clientCase', 'createdUser']); }])->where("tbl_task_instruct.isactive=1 AND tbl_task_instruct.project_name LIKE :term AND tbl_tasks.client_case_id IN ($caseId)", [':term' => '%'.$term.'%'])->all();
                if(!empty($taskinstruction_data))
				{
					foreach ($taskinstruction_data as $instruction)
					{
						$case_detail = strtoupper($instruction->clientCase->client->client_name.' - '. $instruction->tasks->clientCase->case_name);
						$green_link = $case_detail." submitted By ".$instruction->tasks->createdUser->usr_first_name." ".$instruction->tasks->createdUser->usr_lastname;
                        $val = $instruction->project_name;
						$searchArray[] = array('title'=>"Project Name - Project #".$instruction->task_id,'value'=>$val,'task_id'=>$instruction->task_id,'caseId'=>$instruction->tasks->client_case_id,'green_link'=>$green_link,'origination'=>'instruction_project');
					}
				}         	

				//instruction table task attachments
				$where=1;
				
				//instruction notes table
                $taskinstructionnotes_data = TaskInstructNotes::find()->select(['tbl_tasks_units_notes.notes', 'tbl_tasks_units_notes.task_id','tbl_tasks.client_case_id','tbl_client_case.case_name','tbl_client_case.id','usr_first_name' , 'usr_lastname'])->joinWith(['tasks' => function (\yii\db\ActiveQuery $query) { $query->joinWith(['clientCase', 'createdUser']); }])->where("tbl_tasks_units_notes.notes LIKE :term AND tbl_tasks.client_case_id IN ($caseId)", [':term' => '%'.$term.'%'])->all();
				if(!empty($taskinstructionnotes_data))
				{
					foreach ($taskinstructionnotes_data as $notes)
					{
						//echo "<pre>"; print_r($notes); exit;
						$case_detail=strtoupper($notes->tasks->clientCase->client->client_name.' - '. $notes->tasks->clientCase->case_name);
						$green_link=$case_detail." submitted By ".$notes->tasks->createdUser->usr_first_name." ".$notes->tasks->createdUser->usr_lastname;
						$val=$notes->notes;
						$searchArray[]=array('title'=>"Project Name - Project #".$notes->task_id,'value'=>$val,'task_id'=>$notes->task_id,'caseId'=>$notes->tasks->client_case_id,'green_link'=>$green_link,'origination'=>'instruction_notes_project');
					}
				}         	
				
				//evidence detail
                
                $evid_data = TaskInstruct::find()->select(['tbl_task_instruct.id', 'tbl_task_instruct.instruct_version', 'tbl_task_instruct.task_id','tbl_tasks.client_case_id','tbl_client_case.case_name','tbl_client_case.id','usr_first_name' , 'usr_lastname'])->innerJoinWith(['taskInstructEvidences' => function (\yii\db\ActiveQuery $query) { $query->joinWith('evidence'); }, 'tasks' => function (\yii\db\ActiveQuery $query) { $query->joinWith(['clientCase', 'createdUser']); }])->where("tbl_task_instruct.isactive=1 AND tbl_evidence.evid_desc LIKE :term AND tbl_tasks.client_case_id IN ($caseId)", [':term' => '%'.$term.'%'])->all();
                
			    if(!empty($evid_data))
				{
					foreach ($evid_data as $evid)
					{
						$val=$evid->taskInstructEvidences->evidence->evid_desc;//"Media#".$evid_data['id']." and Media Description ".
						$case_detail=strtoupper($evid->clientCase->client->client_name.' - '. $evid->tasks->clientCase->case_name);
						$green_link=$case_detail." submitted By ".$evid->tasks->createdUser->usr_first_name." ".$evid->tasks->createdUser->usr_lastname;
                        $searchArray[]=array('title'=>"Instruction - Project #".$evid->task_id,'version'=>"V".$evid->instruct_version,'value'=>$val,'task_id'=>$evid->task_id,'caseId'=>$evid->tasks->client_case_id,'green_link'=>$green_link,'origination'=>'instruction_evid');
                    }
                }
				
                $evidcont_data = TaskInstruct::find()->select(['tbl_task_instruct.id', 'tbl_task_instruct.instruct_version', 'tbl_task_instruct.task_id','tbl_tasks.client_case_id','tbl_client_case.case_name','tbl_client_case.id','usr_first_name' , 'usr_lastname'])->innerJoinWith(['taskInstructEvidences' => function (\yii\db\ActiveQuery $query) { $query->joinWith(['evidenceContents' => function (\yii\db\ActiveQuery $query) { $query->innerJoinWith('evidenceCustodians');}]); }, 'tasks' => function (\yii\db\ActiveQuery $query) { $query->joinWith(['clientCase', 'createdUser']); }])->where("tbl_task_instruct.isactive=1 AND (tbl_evidence_custodians.cust_fname LIKE :term OR tbl_evidence_custodians.cust_lname LIKE :term1) AND tbl_tasks.client_case_id IN ($caseId)", [':term' => '%'.$term.'%',':term1' => '%'.$term.'%'])->all();
                
                if(!empty($evidcont_data))
                {
                    foreach ($evidcont_data as $evidcont)
                    {
                        $case_detail=strtoupper($evidcont->clientCase->client->client_name.' - '. $evidcont->clientCase->case_name);
                        $green_link=$case_detail." submitted By ".$evidcont->createdUser->usr_first_name." ".$evidcont->createdUser->usr_lastname;
                        $val=$evidcont->taskInstructEvidences->evidenceContents->evidenceCustodians->cust_lname." ".$evidcont->taskInstructEvidences->evidenceContents->evidenceCustodians->cust_fname;
                        $searchArray[]=array('title'=>"Instruction - Project #".$evidcont->task_id,'version'=>"V".$evidcont->instruct_version,'value'=>$val,'task_id'=>$evidcont->task_id,'caseId'=>$evidcont->tasks->client_case_id,'green_link'=>$green_link,'origination'=>'instruction_evid');
                    }
                }
				
				
				//task from search
                
                $instruction_data = TaskInstruct::find()->select(['tbl_task_instruct.id', 'tbl_task_instruct.instruct_version', 'tbl_task_instruct.task_id','tbl_tasks.client_case_id','tbl_client_case.case_name','tbl_client_case.id','usr_first_name' , 'usr_lastname'])
                ->innerJoinWith(['formInstructionValues' => function (\yii\db\ActiveQuery $query) { $query->joinWith('formBuilder'); }, 'tasks' => function (\yii\db\ActiveQuery $query) { $query->joinWith(['clientCase', 'createdUser']); }])->where("tbl_task_instruct.isactive=1 AND tbl_tasks.client_case_id IN ($caseId)")->all();
                
                foreach($instruction_data as $instruction) { 
                    $element_types = array('checkbox','radio','dropdown');
                    
                    foreach($instruction->formInstructionValues as $val) {
                        $myval = "";
                        if(in_array($val->formBuilder->element_type, $element_types))
                        {
                            //need to search in element options table
                            $element_options_data = FormElementOptions::find()->select(['tbl_form_element_options.id', 'tbl_form_element_options.element_option'])->where(["form_builder_id" => $val->formBuilder->id])->all();
                            
                            foreach($element_options_data as $option)
                            {
                                if(stristr(strtolower($option->element_option),strtolower($term)))
                                {
                                    if($myval=="")
                                        $myval=$option->element_option;
                                    else 
                                        $myval.="<br>".$option->element_option;	
                                }
                            } 
                            
                        }
                        else
                        {
                            if(stristr($val->formBuilder->element_label,$term))
                            {
                                if($myval=="")
                                $myval=$val->formBuilder->element_label;
                                else 
                                $myval.="<br>".$val->formBuilder->element_label;	
                            }
                            else
                            {
                                if(stristr(strtolower($val->element_value),strtolower($term)))
                                {
                                    if($myval=="")
                                        $myval=$val->element_value;
                                    else 
                                        $myval.="<br>".$val->element_value;	
                                }
                            }
                        }
                        
                        if($myval!="")
                        {
                            $case_Id=$instruction->tasks->client_case_id;
                            $case_detail=strtoupper($instruction->clientCase->client->client_name.' - '. $instruction->tasks->clientCase->case_name);
                            $green_link=$case_detail." submitted By ".$instruction->tasks->createdUser->usr_first_name." ".$instruction->tasks->createdUser->usr_lastname;
                            $task="Label Name";
                            if(in_array($case_Id,explode(",",$caseId)))
                                $searchArray[]=array('instruction_id' => $instruction->id, 'title'=>"Instruction - Project #".$instruction->task_id,'version'=>"V".$instruction->instruct_version,'value'=>$myval,'task_id'=>$instruction->task_id,'caseId'=>$instruction->tasks->client_case_id,'green_link'=>$green_link,'origination'=>'instruction_task_details','task'=>$task);
                        }
                    }
                }
            }
		}
		else
		{
			static $securityteam_ids = array();
                if (empty($securityteam_ids)) {
                    //$securityteam_ids = (new ProjectSecurity)->getUserTeamsArr($user_id);
                    if (empty($securityteam_ids))
                        $securityteam_ids = array(0);
                }
        
                $roleId = Yii::$app->user->identity->role_id;
                $user_id = Yii::$app->user->identity->id;
                $role_types = explode(",", $roleId);
               // echo "<pre>temids: ";print_r($securityteam_ids);
               // echo "<pre>roletypes: ";print_r($role_types);
               $comment_sql="SELECT tbl_comments.Id, tbl_comments.comment, tbl_comments.task_id, tbl_comments.created_by, tbl_tasks.client_case_id, tbl_client.client_name, tbl_client_case.case_name, tbl_client_case.id, usr_first_name, usr_lastname, tbl_comments.comment_origination, (select COUNT(comment_id) from tbl_comments_read where tbl_comments_read.comment_id = tbl_comments.Id and tbl_comments_read.user_id=$user_id) as readcount 
               FROM tbl_comments 
               INNER JOIN tbl_tasks ON tbl_comments.task_id = tbl_tasks.id 
               INNER JOIN tbl_client_case ON tbl_tasks.client_case_id = tbl_client_case.id
               INNER JOIN tbl_client ON tbl_client_case.client_id = tbl_client.id 
               INNER JOIN tbl_user createdUser ON tbl_tasks.created_by = createdUser.id 
               WHERE tbl_tasks.client_case_id IN ($caseId) AND tbl_tasks.task_closed = 0 AND tbl_tasks.task_cancel = 0 AND tbl_client_case.is_close=0";
               //echo $comment_sql;die;
               $comment_data = Yii::$app->db->createCommand($comment_sql)->queryAll();
               //echo "<pre>",print_r($comment_data),"</pre>";die;
               //$comment_data = Comments::find()->select(['tbl_comments.Id','tbl_comments.comment','tbl_comments.task_id','tbl_comments.created_by','tbl_tasks.client_case_id','tbl_client_case.case_name','tbl_client_case.id','usr_first_name' , 'usr_lastname', 'tbl_comments.comment_origination','(select COUNT(comment_id) from tbl_comments_read where tbl_comments_read.comment_id = tbl_comments.Id and tbl_comments_read.user_id='.$user_id.') as readcount'])
               //->innerJoinWith(['tasks' => function (\yii\db\ActiveQuery $query) { $query->innerJoinWith(['clientCase','createdUser']);}])
               //->where("tbl_tasks.client_case_i IN (:caseId) AND tbl_tasks.task_closed = 0 AND tbl_tasks.task_cancel = 0 AND tbl_client_case.is_close=0", [':caseId'=>$caseId])
               //->all();
                
                $notread = 0;
				foreach($comment_data as $comments){
                    if($comments['readcount'] == 0){
						$case_detail=strtoupper($comments['client_name'].' - '. $comments['case_name']);
                        $green_link=$case_detail." submitted By ".$comments['usr_first_name']." ".$comments['usr_lastname'];
                        $searchArray[]=array('title'=>"Project #".$comments['task_id'],'value'=>$comments['comment'],'task_id'=>$comments['task_id'],'caseId'=>$comments['client_case_id'],'green_link'=>$green_link,'origination'=>'comment','id'=>$comments['Id']);
					}	
				}
                /*if(!empty($comment_data))
				{
					foreach ($comment_data as $comments)
					{
						$has_access = false;
                        if ($comments->created_by == $user_id) 
                        {
                            $has_access = true;
                        } 
                        else 
                        {
                            if (!empty($comments->commentTeams)) 
                            {
                                foreach($comments->commentTeams as $team)
                                {
                                    if (in_array($team->team_id, $securityteam_ids)) 
                                    {
                                        $has_access = true;
                                        break;
                                    }
                                }
                            }

                            if (!empty($comments->commentRoles)) 
                            {
                                foreach($comments->commentRoles as $role)
                                {
                                    $roles = Role::find()->select(['id'])->andWhere("id = " . $role->role_id . " AND id > 0")->one();
                                    if(isset($roles->id))
                                    {
                                        if (in_array($roles->id, $role_types)) 
                                        {
                                            $has_access = true;
                                            break;
                                        }
                                    }
                                }  
                            }
                        }
						if($has_access){
                            
                            $is_read = "N";
                        if($user_id == $comments->created_by) 
                        { 
                            $is_read = "Y";
                        } 
                        else 
                        { 
                            if (!empty($comments->commentsRead))
                            {
                                foreach($comments->commentsRead as $val)
                                {
                                    if ($val->user_id != "") {
                                        $already_readuser = $val->user_id;
                                        
                                            if($user_id==$already_readuser) {
                                                $is_read = "Y";
                                                break;
                                            } else {
                                                $is_read = "N";
                                            }
                                        
                                    } 
                                }
                            } else {
                                    $is_read = "N";
                            }	
                        }
                            if($is_read=="N")
                            {
                                $case_detail=strtoupper($comments->tasks->clientCase->client->client_name.' - '. $comments->tasks->clientCase->case_name);
                                $green_link=$case_detail." submitted By ".$comments->tasks->createdUser->usr_first_name." ".$comments->tasks->createdUser->usr_lastname;
                                $searchArray[]=array('title'=>"Project #".$comments->task_id,'value'=>$comments->comment,'task_id'=>$comments->task_id,'caseId'=>$comments->tasks->client_case_id,'green_link'=>$green_link,'origination'=>'comment','id'=>$comments->Id);
                            }
						}
					}
                    
				}*/
		}		
	
		//echo "<pre>",print_r($searchArray),"</pre>";die;
		if($ismagnified == 'unread_cnt')
			return count($searchArray);
		else
			return $searchArray; 	
		
	}
    /*IRT-434 
        Get only Cleints     
     */
    public function getCleintsDetaills($clients,$addedClientsCases){
        if(!empty($clients)){           
            $notCondition = $clientCondition = '1=1';
            if(!in_array('All',$clients)){
                $clientCondition = ['tbl_client.id'=>$clients];                  
            }
            /*if(!empty($addedClientsCases)){      
                array_walk($addedClientsCases, function (&$value, $key) {
                    $value=" SELECT $value as id ";
                });
                $invaluetable = implode(' UNION ALL ',$addedClientsCases);

                $notCondition = ['NOT IN',"concat(tbl_client_case.client_id,',',tbl_client_case.id)",$invaluetable];             
            }
            $clientList = ClientCase::find()->select(['tbl_client_case.client_id','tbl_client_case.id as client_case_id','tbl_client.client_name','tbl_client_case.case_name'])->From('tbl_client_case')->joinWith(['client'])->where($clientCondition)->andWhere($notCondition)->distinct()->orderBy('tbl_client.client_name, tbl_client_case.case_name')->asArray()->all();*/
            $clients = ClientCase::find()->select(['tbl_client_case.client_id','tbl_client_case.id as client_case_id','tbl_client.client_name','tbl_client_case.case_name'])
            ->joinWith(['client'])
            ->where($clientCondition)
            //->andWhere($notCondition)
            ->distinct()
            ->orderBy('tbl_client.client_name, tbl_client_case.case_name');

            if(!empty($addedClientsCases)){             
                if (Yii::$app->db->driverName == 'mysql') {
                    $notCondition = ['NOT IN',"concat(tbl_client_case.client_id,',',tbl_client_case.id)",$addedClientsCases];             
                    $clients->andWhere($notCondition);
                } else {   
                    array_walk($addedClientsCases, function (&$value, $key) {
                        $value=" SELECT '$value' as id ";
                    });
                    $invaluetable = implode(' UNION ALL ',$addedClientsCases);
                    $clients->innerJoin("($invaluetable) as A", "concat(tbl_client_case.client_id,',',tbl_client_case.id) = A.id");
                }
            }
            
            $clientList = $clients->asArray()->all();
        }
        return $clientList;        
    }
    /*
    *    IRT-434     
     * return only those clients who are already added
     */
    public function clientsFilterdProjectSecurity($userID){
        $notClients = [];
        $allClientWithCases_SQL = "SELECT tbl_client_case.client_id,client_name,count(tbl_client_case.client_id) AS total_clients FROM tbl_client Left JOIN tbl_client_case  ON tbl_client_case.client_id = tbl_client.id group BY tbl_client_case.client_id,client_name";         
        $Q_result = Yii::$app->db->createCommand($allClientWithCases_SQL)->queryAll();
        $allClients = ArrayHelper::map($Q_result,'client_id','client_name');
        $allClientWithCases = ArrayHelper::map($Q_result,'client_id','total_clients');
        
        $SQLonlysecured = "SELECT client_id,COUNT(client_id) AS total FROM tbl_project_security WHERE user_id = $userID and client_id <> 0 GROUP BY client_id";
        $allprojectclients = ArrayHelper::map(Yii::$app->db->createCommand($SQLonlysecured)->queryAll(),'client_id','total');        
        
        if(!empty($allprojectclients) && !empty($allClientWithCases)){            
            foreach($allClientWithCases as $client_id => $clientCaseTotal){
                if(isset($allprojectclients[$client_id]) && $allprojectclients[$client_id] != ''){
                    if($allprojectclients[$client_id] == $clientCaseTotal ){
                        if(isset($allClients[$client_id]) && $allClients[$client_id] != '')
                            unset($allClients[$client_id]);
                    }                        
                }                
            }                
        }       
//        $clientList= ArrayHelper::map(Client::find()->select(['id','client_name'])->andWhere(['not in','id',$notClients])->orderBy('client_name ASC')->asArray()->all(),'id','client_name');        
        return $allClients;
    }
    public static function getClientCaseData(){
        if(!isset($_SESSION['HeaderClientCaseData'])){
            $data=array();
            $_SESSION['HeaderClientCaseData'];
            $roleId = Yii::$app->user->identity->role_id;
			$userId = Yii::$app->user->identity->id; 
			if ($roleId != 0) {
				//$case_data = ProjectSecurity::find()->select('client_case_id')->where('client_id!=0 AND user_id='.$userId.' AND team_id=0');
				$caseList_sql = "SELECT tbl_client_case.id, case_name AS name, tbl_client.client_name 
 FROM tbl_client_case 
 INNER JOIN tbl_client on tbl_client.id=tbl_client_case.client_id
 WHERE (tbl_client_case.id IN (SELECT client_case_id FROM tbl_project_security WHERE client_id!=0 AND  client_case_id !=0  AND user_id=$userId AND team_id=0)) AND (is_close=0) ORDER BY case_name";
                $caseList = Yii::$app->db->createCommand($caseList_sql)->queryAll();
                if(!empty($caseList)){
                    foreach($caseList as $case){
                        $data[$case['id']]=Html::decode($case['client_name']." - ".$case['name']);
                    }
                }
                $_SESSION['HeaderClientCaseData']=$data;
                //ClientCase::find()->select(['id', 'case_name as name'])->where(['in', 'i', $case_data])->andWhere(['is_close'=>0])->orderBy('case_name')->asArray()->all();
			} else {
				$caseList_sql = "SELECT tbl_client_case.id, case_name AS name, client_name FROM tbl_client_case 
INNER JOIN tbl_client on tbl_client.id=tbl_client_case.client_id
WHERE (is_close=0) ORDER BY case_name";
                $caseList = Yii::$app->db->createCommand($caseList_sql)->queryAll();
                if(!empty($caseList)){
                    foreach($caseList as $case){
                        $data[$case['id']]=Html::decode($case['client_name']." - ".$case['name']);
                    }
                }
                $_SESSION['HeaderClientCaseData']=$data;
                //ClientCase::find()->select(['id', 'case_name as name'])->where([ 'client_i' => $client_id])->andWhere(['is_close'=>0])->orderBy('case_name')->asArray()->all();
			}
        }
        return $_SESSION['HeaderClientCaseData'];
    }
}
