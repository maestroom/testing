<?php

namespace app\models;

use Yii;
use yii\helpers\Html;

/**
 * This is the model class for table "{{%evidence_production}}".
 *
 * @property integer $id
 * @property integer $client_id
 * @property integer $client_case_id
 * @property string $staff_assigned
 * @property string $prod_date
 * @property string $prod_rec_date
 * @property string $prod_party
 * @property string $production_desc
 * @property integer $production_type
 * @property string $cover_let_link
 * @property integer $prod_orig
 * @property integer $prod_return
 * @property string $attorney_notes
 * @property string $prod_disclose
 * @property string $prod_agencies
 * @property string $prod_access_req
 * @property integer $has_media
 * @property integer $has_hold
 * @property integer $has_projects
 * @property string $prod_misc1
 * @property string $prod_misc2
 * @property string $created
 * @property integer $created_by
 * @property string $modified
 * @property integer $modified_by
 */
class EvidenceProduction extends \yii\db\ActiveRecord
{
	public $production_party_count=0;
    public $upload_files,$medialist;
    public $has_attachment;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%evidence_production}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
		$production_form = FormBuilderSystem::find()->where(['sys_form'=>'production_form','grid_only'=>0,'table_field'=>1])->all();
        $return_array=[];
		$required=[];
		$safe=[];
		$virtual=[];
		if(!empty($production_form)){
				foreach($production_form as $prod_form){
					if($prod_form->required==1){
						$required[$prod_form->sys_field_name]=$prod_form->sys_field_name;	
					}else{
						$safe[$prod_form->sys_field_name]=$prod_form->sys_field_name;		
					}
				}
		return [
			[$required, 'required'],
			[$safe, 'safe'],
		];
		}else{
			return [
				[['client_case_id', /*'prod_date',*/ 'prod_rec_date', 'prod_party'], 'required'],
				[['client_case_id', 'production_type', 'prod_orig', 'prod_return', 'has_media', 'has_hold', 'has_projects', 'created_by', 'modified_by'], 'integer'],
				[['prod_date', 'prod_rec_date', 'prod_agencies', 'prod_access_req', 'created', 'modified','prod_copied_to'], 'safe'],
				[['attorney_notes'], 'string'],
				[['staff_assigned'], 'string'],
				[['prod_party', 'cover_let_link', 'prod_disclose', 'prod_misc1', 'prod_misc2'], 'string'],
				[['production_desc'], 'string'],
				/*['prod_date', 'required', 'when' => function ($model) {
					return Yii::$app->db->driverName != 'sqlsrv';
				}],*/
				/*[['client_id'], 'exist', 'skipOnError' => true, 'targetClass' => Client::className(), 'targetAttribute' => ['client_id' => 'id']],*/
				[['client_case_id'], 'exist', 'skipOnError' => true, 'targetClass' => ClientCase::className(), 'targetAttribute' => ['client_case_id' => 'id']],
			];
		}
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'Producttion#',
            'client_id' => 'Client ID',
            'client_case_id' => 'Client Case ID',
            'staff_assigned' => 'Staff Assigned',
            'prod_date' => 'Production Date',
            'prod_rec_date' => 'Date Received',
            'prod_party' => 'Producing Party',
            'production_desc' => 'Production Description',
            'production_type' => 'Production Type',
            'cover_let_link' => 'Cover Letter Link',
            'prod_orig' => 'Production Orig',
            'prod_return' => 'Production Return',
            'attorney_notes' => 'Attorney Notes',
            'prod_disclose' => 'Production Disclose',
            'prod_agencies' => 'Production to Other Agencies',
            'prod_access_req' => 'Access Request',
            'has_media' => 'Has Media',
            'has_hold' => 'Has Hold',
            'has_projects' => 'Has Projects',
            'prod_misc1' => 'Prod Misc1',
            'prod_misc2' => 'Prod Misc2',
            'created' => 'Created',
            'created_by' => 'Created By',
            'modified' => 'Modified',
            'modified_by' => 'Modified By',
        	'production_party_count' => 'Party wise Production Count',
            'prod_copied_to'=>'Production Copied To UNC Link'
        ];
    }
     /**
     * get Production type image in Case production Grid
     */
    public function getProdTypeImage($prodtype) {
        if ($prodtype == 1) {
             $statusImg ='<a href="javascript:void(0);" class="icon-fa" title="Incoming Production"><em class="fa fa-download text-success"  title="Incoming Production"></em></a>';
        } 
	else{
          $statusImg ='<a href="javascript:void(0);" class="icon-fa" title="Outgoing Production"><em class="fa fa-upload text-warning" title="Outgoing Production"></em></a>';
        } 
        return $statusImg;
    }
	  /**
	 * get Tick mark image for media exist in Case production Grid
	 */
    function getStatus($status,$field,$id=-1) {
        $statusImg = '';
        if ($field == 'has_media' && $status == 1) {
            $statusImg = '<a href="javascript:void(0);" class="icon-fa" title="Has Media"><em class="fa fa-check text-danger" title="Has Media"></em><span class="screenreader">Media</span></a>';
        } else if ($field == 'has_hold' && $status == 1) {
            $statusImg = '<a href="javascript:void(0);" class="icon-fa" title="On Hold"><em class="fa fa-check text-danger" title="On Hold"></em><span class="screenreader">Media</span></a>';
        } else if ($field == 'has_projects' && $status == 1) {
           // TaskInstructEvidence::find()->joinWith('taskInstruct')->where('prod_id=' . $id . ' AND isactiv=1')->count();
            //if (TaskInstructEvidence::find()->joinWith('taskInstruct')->where('prod_id=' . $id . ' AND isactive=1')->count()) {
                //EvidenceProduction::updateAll(['has_projects' => 1], 'id = ' . $id);
                $statusImg = '<a href="javascript:void(0);" class="icon-fa" title="Has Projects"><em class="fa fa-check text-danger" title="Has Projects"></em><span class="screenreader">Projects</span></a>';
           // } else {
             //   EvidenceProduction::updateAll(['has_projects' => 0], 'id = ' . $id);
            //}
        } else if ($field == 'prod_orig' && $status == 1) {
            $statusImg = '<a href="javascript:void(0);" class="icon-fa" title="Prod Contains Orig"><em class="fa fa-check text-danger" title="Production"></em><span class="screenreader">Production</span></a>';
        } else if ($field == 'prod_return' && $status == 1) {
            $statusImg = '<a href="javascript:void(0);" class="icon-fa" title="Prod Return"><em class="fa fa-check text-danger"  title="Production"></em><span class="screenreader">Production</span></a>';
        } else if ($field == 'prod_agencies' && !in_array(date('Y', strtotime($status)), array('1970', '-0001'))) {
            $statusImg = date('m/d/Y', strtotime($status));
            //(new Options)->ConvertOneTzToAnotherTz($status, 'UTC', $_SESSION['usrTZ'], 'date');
            // $statusImg =date('m/d/Y', strtotime($status));
        } else if ($field == 'prod_access_req' && !in_array(date('Y', strtotime($status)), array('1970', '-0001'))) {
            //$statusImg = (new Options)->ConvertOneTzToAnotherTz($status, 'UTC', $_SESSION['usrTZ'], 'date');
             $statusImg =date('m/d/Y', strtotime($status));
        }
        return $statusImg;
    }
    public function getProductionattachments()
    {
        return $this->hasMany(Mydocument::className(), ['reference_id' => 'id'])->andOnCondition(['origination' => 'Production']);
    }
    public function getAttachments($id,$cntattachment="")
    {
        $attachment='';
        if($cntattachment!="" && $cntattachment > 0) {
            $attach=Mydocument::find()->where(['reference_id'=>$id,'origination' => 'Production'])->all();
            if(!empty($attach)) {
                foreach($attach as $at) {
                    if ($attachment == "")
                        $attachment ='<a href="javascript:void(0)" onclick="downloadattachment(' . $at->id . ')" class="icon-fa" title="Attachment"><em class="fa fa-paperclip text-danger" title="Attachment"></em><span class="screenreader">Download Attachment</span></a>';
                    else
                        $attachment .='&nbsp;&nbsp;<a href="javascript:void(0)" onclick="downloadattachment(' . $at->id . ')" class="icon-fa" title="Attachment"><em class="fa fa-paperclip text-danger" title="Attachment"></em><span class="screenreader">Download Attachment</span></a>';
                }
            }
        }
		return $attachment;
    }
    public function getProjectsLink($tasks_id) {
                $user_model = new User();
				$roleId = Yii::$app->user->identity->role_id;
                if ($tasks_id != "" && $tasks_id != 0) {
					$currentUser = Role::findOne(Yii::$app->user->identity->role_id);
                    $current_user_roles = explode(",", $currentUser->role_type);
                    $task_info = Tasks::findOne($tasks_id);
                    if (in_array(1, $current_user_roles) || $roleId == 0) {
                        if ($user_model->checkAccess(4) && $user_model->checkAccess(4.01)) {
                            if($task_info->task_cancel == 1)
                            {
                                if($user_model->checkAccess(4.0811)){
                                    $url = "index.php?r=case-projects/load-canceled-projects&case_id=" . $task_info->client_case_id . "&task_id=" . $task_info->id;
                                    $tasks_ids[$tasks_id] = Html::a($task_info->id, $url, array("class" => "num_a","title"=>"Project #".$task_info->id));
                                }
                                else
                                    $tasks_ids[$tasks_id] = $tasks_id;
                            }
                            else if($task_info->task_closed == 1)
                            {
                                if($user_model->checkAccess(4.081)){
                                    $url = "index.php?r=case-projects/load-closed-projects&case_id=" . $task_info->client_case_id . "&task_id=" . $task_info->id;
                                    $tasks_ids[$tasks_id] = Html::a($task_info->id, $url, array("class" => "num_a","title"=>"Project #".$task_info->id));
                                }
                                else
                                    $tasks_ids[$tasks_id] = $tasks_id;
                            }
                            else
                            {
                                $url = "index.php?r=case-projects/index&case_id=" . $task_info->client_case_id . "&task_id=" . $task_info->id;
                                $tasks_ids[$tasks_id] = Html::a($task_info->id, $url, array("title"=>"Project #".$task_info->id));
                            }
                        }
                        else {
                    		$tasks_ids[$tasks_id] = $task_info->id;
                    	}
                    } else {
                        $team_data = json_decode($task_info->team_loc);
                        $te_amid = 0;
                        $team_loc = 0;
                        $te_amid = "";
                        $team_loc = "";
                        foreach ($team_data as $tda) {
                            if (User::checkTeamAccess($tda->team_id, $tda->team_loc)) 
                            {
                                $te_amid = $tda->team_id;
                                $team_loc = $tda->team_loc;
                                break;
                            }
                        }
                        if($task_info->task_cancel == 1)
                        	$tasks_ids[$est_data->tasks_id] = $task_info->id;
                        else{
							$url = "index.php?r=team/loadmyTask&teamId=" . $te_amid . "&team_loc=" . $team_loc . "&taskId=" . $task_info->id;
							$tasks_ids[$est_data->tasks_id] = Html::a($task_info->id, $url, array("title"=>"Project #".$task_info->id));
                        	//$tasks_ids[$est_data->tasks_id] = "<a href='" . Yii::app()->baseUrl . "/index.php?r=team/loadmyTask&teamId=" . $te_amid . "&team_loc=" . $team_loc . "&taskId=" . $task_info->id . "'>" . $task_info->id . "</a>";
                        }	
                    }
                }
            return implode(", ", $tasks_ids);
    }
    
     /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
    	if (parent::beforeSave($insert)) {
    		if(isset($this->prod_date) && $this->prod_date==0){$this->prod_date = NULL;}
    		if($this->isNewRecord){
    			if(!isset($this->has_hold)){$this->has_hold = 0;}
    			if(!isset($this->has_media)){$this->has_media = 0;}
    			if(!isset($this->has_projects)){$this->has_projects = 0;}
    			$this->created = date('Y-m-d H:i:s');
    			$this->created_by = Yii::$app->user->identity->id;
    			$this->modified = date('Y-m-d H:i:s');
    			$this->modified_by = Yii::$app->user->identity->id;
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
    
    public function getProductionmedia() 
    {
        return $this->hasMany(EvidenceProductionMedia::className(), ['prod_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClientCase()
    {
        return $this->hasOne(ClientCase::className(), ['id' => 'client_case_id']);
    }
    
    /** IRT 270 **/
    public function getProdDate($prod_date)
    {
        if (Yii::$app->db->driverName == 'sqlsrv')
        {
            if($prod_date != '' && $prod_date != '0000-00-00')
                return $new_prod_date = date('m/d/Y',strtotime($prod_date));
            else
                return $new_prod_date = '00/00/0000';
        } 
        else
        {
            if($prod_date != '' && $prod_date != '0000-00-00')
                return $new_prod_date = date('m/d/Y',strtotime($prod_date));
            else
                return $new_prod_date = '00/00/0000';
        }        
    }
}
