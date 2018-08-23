<?php

namespace app\models;

use Yii;
use app\models\EvidenceContents;

/**
 * This is the model class for table "{{%evidence_custodians}}".
 *
 * @property integer $cust_id
 * @property string $title
 * @property string $dept
 * @property string $cust_fname
 * @property string $cust_lname
 * @property string $cust_email
 * @property string $cust_mi
 * @property string $created
 * @property integer $created_by
 * @property string $modified
 * @property integer $modified_by
 */
class EvidenceCustodians extends \yii\db\ActiveRecord
{
	public $media,$project,$form,$full_name; 
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%evidence_custodians}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
		$cust_form = FormBuilderSystem::find()->where(['sys_form'=>'custodian_form','grid_only'=>0,'table_field'=>1])->all();
        $return_array=[];
		$required=[];
		$safe=[];
		$virtual=[];
		if(!empty($cust_form)){
				foreach($cust_form as $custform){
					if($custform->required==1){
						$required[$custform->sys_field_name]=$custform->sys_field_name;	
					}else{
						$safe[$custform->sys_field_name]=$custform->sys_field_name;		
					}
				}
		return [
			[$required, 'required'],
			[$safe, 'safe'],
		];
		}else{
			return [
				[['title', 'dept', 'cust_fname', 'cust_lname', 'cust_mi','cust_email'], 'string'],
				[['cust_fname','cust_lname'], 'required'],
				[['cust_email'], 'email'],
				[['created', 'modified', 'media', 'project', 'form','cust_email'], 'safe'],
				[['created_by', 'modified_by'], 'integer'],
			];
		}
    }
	
    public function getCustName(){
    	if(isset($this->cust_mi) && $this->cust_mi!="")
    		return $this->cust_lname.", ".$this->cust_fname." ".$this->cust_mi;
    	else
    		return $this->cust_lname.", ".$this->cust_fname;
    }
    
    /*function to check cust is used in media or not*/
    public function isMedia($custId,$flag ='') {
    	$clientMedia = (new EvidenceContents)->getCountEvidenceContentByCid($custId);
    	$media = "";
    	$is_associated = 0;
    	if ($clientMedia > 0) {
    		$media = "<a href='javascript:void(0);' title='Associated to Media'><em class='fa fa-check text-danger' title='Associated to Media'></em><span class='screenreader'>Associated to Media</span></a>";
    		$is_associated = 1;
    	}
    	$media .= '<input type="hidden" name="is_Associated" class="is_Associated" value=' . $is_associated . '>';
    	if($flag == 'status'){
    		return $is_associated;
    	}
    	return $media;
    }
    /*
     * get all content cust used in
     * */
    public function getCountEvidenceNumIdByCid($cust_id,$case_id=0) 
    {
    	if($case_id !=0)
    		return (new EvidenceContents)->find()->join('INNER JOIN','tbl_client_case_evidence','tbl_client_case_evidence.evid_num_id=tbl_evidence_contents.evid_num_id AND tbl_client_case_evidence.client_case_id='.$case_id)->where('tbl_evidence_contents.cust_id='.$cust_id)->select('tbl_evidence_contents.evid_num_id')->groupBy('tbl_evidence_contents.evid_num_id')->all();
    	else 
    		return (new EvidenceContents)->find()->where('tbl_evidence_contents.cust_id='.$cust_id)->select('tbl_evidence_contents.evid_num_id')->groupBy('tbl_evidence_contents.evid_num_id')->all();
    }
    /*
     * get all Tasks cust is used in
     * */
    public function getTotalCaseActiveTasks($case_id,$custId){
    	$sql="SELECT tbl_tasks.id
    	FROM tbl_task_instruct_evidence
    	INNER JOIN tbl_task_instruct ON tbl_task_instruct.id = task_instruct_id
    	INNER JOIN tbl_tasks ON tbl_tasks.id = tbl_task_instruct.task_id
    	INNER JOIN tbl_evidence_contents ON tbl_evidence_contents.id = evidence_contents_id
    	WHERE tbl_tasks.client_case_id = :case_id
    	AND tbl_task_instruct.isactive =1
    	AND tbl_evidence_contents.cust_id = :cust_id group by tbl_tasks.id ";
    	return  \Yii::$app->db->createCommand($sql, [ ':case_id' => $case_id, ':cust_id' => $custId])->queryAll();
    }
    
    /*function to check cust is used in project or not*/
    public function isProjects($case_id, $custId, $flag='') {
		//echo $case_id, "|", $custId,"<br/>";
		$is_task_condition = ' AND tbl_tasks.task_status IN ( 0, 1, 3, 4)';
		if($flag != "show")
			$is_task_condition = ' AND tbl_tasks.task_status IN ( 0, 1, 3) AND tbl_tasks.task_closed = 0 AND tbl_tasks.task_cancel = 0';	
		
		$sql="SELECT COUNT( * ) as cnt_project
			FROM tbl_task_instruct_evidence
			INNER JOIN tbl_task_instruct ON tbl_task_instruct.id = task_instruct_id
			INNER JOIN tbl_tasks ON tbl_tasks.id = tbl_task_instruct.task_id
			INNER JOIN tbl_evidence_contents ON tbl_evidence_contents.id = evidence_contents_id
			WHERE tbl_tasks.client_case_id = :case_id
			$is_task_condition
			AND tbl_task_instruct.isactive =1
			AND tbl_evidence_contents.cust_id = :cust_id";
    	$data = \Yii::$app->db->createCommand($sql, [ ':case_id' => $case_id, ':cust_id' => $custId])->queryOne();
    	
    	$is_associated = 0;
    	if ($data['cnt_project']) {
    		$is_associated = 1;
    		$project_link = "<a href='javascript:void(0);' title='Associated to Project'><em class='fa fa-check text-danger' title='Associated to Project'></em><span class='screenreader'>Associated to Project</span></a>";
    	}
    	$project_link .= '<input type="hidden" name="is_Associated" class="is_Associated" value=' . $is_associated . '>';
    	if($flag == 'status'){
    		return $is_associated;
    	}
    	return $project_link;
    
    }
    /*function to check cust has from or not*/
    public function isForm($cust,$flag = '',$case_id=0) {
    	$isForm = "";
    	$is_associated = 0;
    	$query = $this->find()->joinWith('formCustodianValues formCustVal');
    	
    	if($case_id != 0 ){
    		$query->join('LEFT JOIN', 'tbl_client_case_custodians', 'tbl_client_case_custodians.cust_id = formCustVal.cust_id AND tbl_client_case_custodians.client_case_id='.$case_id);
    		$query->join('LEFT JOIN', 'tbl_client_case_evidence', 'tbl_client_case_evidence.cust_id = formCustVal.cust_id AND tbl_client_case_evidence.client_case_id='.$case_id);
    		
    	}
    	$query->where(['formCustVal.cust_id'=>$cust->cust_id]);
    	$data = $query->count();
    	//echo $data; exit;
    	if ($data > 0) {
    		$is_associated = 1;
    		$isForm = "<a href='javascript:void(0);' title='Associated to Form'><em class='fa fa-check text-danger' title='Associated to Form'></em><span class='screenreader'>Associated to Form</span></a>";
    	} 
    	$isForm .= '<input type="hidden" name="is_Associated" class="is_Associated" value=' . $is_associated . '>';
    	if($flag == 'status'){
    		return $is_associated;
    	}
    	return $isForm;
    }
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'cust_id' => 'Cust ID',
            'title' => 'Title',
            'dept' => 'Department',
            'cust_fname' => 'First Name',
            'cust_lname' => 'Last Name',
            'cust_mi' => 'MI',
			'cust_email'=>'Email',
            'created' => 'Created',
            'created_by' => 'Created By',
            'modified' => 'Modified',
            'modified_by' => 'Modified By',
        ];
    }
    public function getClientCaseEvidence()
    {
        return $this->hasMany(ClientCaseEvidence::className(), ['cust_id' => 'cust_id']);
    }
    public function getClientCaseCustodians()
    {
        return $this->hasMany(ClientCaseCustodians::className(), ['cust_id' => 'cust_id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFormCustodianValues()
    {
    	return $this->hasMany(FormCustodianValues::className(), ['cust_id'=>'cust_id']);
    }
    public function beforeSave($insert){
    	if (parent::beforeSave($insert)){
    		if($this->isNewRecord){
                // $this->has_contents = 'N';
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
    /*filter from data itself*/
    public function createFilter($dataProvider){
    	$filter_array=array();
    	$filter_array['custodiant']['']='';
    	$filter_array['title']['']='';
    	$filter_array['dept']['']='';
    	foreach ($dataProvider->models as $model) {
    		$filter_array['custodiant'][$model->cust_id] = $model->cust_lname." ".$model->cust_fname." , ".$model->cust_mi;
    		$filter_array['title'][$model->cust_id] = $model->title;
    		$filter_array['dept'][$model->cust_id] = $model->dept;
    	}
    	return $filter_array;
    }
}
