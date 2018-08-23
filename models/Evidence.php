<?php
namespace app\models;
use Yii;
use app\models\FormBuilderSystem;
/**
 * This is the model class for table "{{%evidence}}".
 *
 * @property integer $id
 * @property integer $checkedin_by
 * @property integer $dup_evid
 * @property integer $org_link
 * @property string $other_evid_num
 * @property string $received_date
 * @property string $received_time
 * @property string $received_from
 * @property string $evd_Internal_no
 * @property integer $evid_type
 * @property integer $cat_id
 * @property string $serial
 * @property string $model
 * @property string $hash
 * @property integer $quantity
 * @property string $cont
 * @property string $evid_desc
 * @property string $evid_label_desc
 * @property integer $contents_total_size
 * @property integer $contents_total_size_comp
 * @property integer $unit
 * @property integer $comp_unit
 * @property string $contents_copied_to
 * @property string $mpw
 * @property string $bbates
 * @property string $ebates
 * @property string $m_vol
 * @property string $ftpun
 * @property string $ftppw
 * @property integer $enctype
 * @property string $encpw
 * @property integer $evid_stored_location
 * @property string $evid_notes
 * @property integer $status
 * @property integer $has_contents
 * @property string $barcode
 * @property string $created
 * @property integer $created_by
 * @property string $modified
 * @property integer $modified_by
 */
class Evidence extends \yii\db\ActiveRecord
{
    public $client_id;
    public $case_id,$client_case_id;
    public $upload_files, $evidence_name,$contentstotalsize_comp,$contentstotalsize_compunit;
    public $evidence_by_case = 0;
    public $evidType = 0;
    public $category=null;
    public $evidenceunitunit_name=null;
    public $evidencecompunit_name=null;
    public $evidcreateduser=null;
  //  public $client_name=null;
//    public $case_name=null;


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%evidence}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {//'contents_total_size', 'contents_total_size_comp', 'unit', 'comp_unit',
		$media_form = FormBuilderSystem::find()->where(['sys_form'=>'media_form','grid_only'=>0,'table_field'=>1])->all();
		$return_array=[];
		$required=[];
		$safe=['contents_total_size','contents_total_size_comp','unit','comp_unit'];
		$virtual=[];
        $static_conditional=['contents_total_size'=>'contents_total_size','contents_total_size_comp'=>'contents_total_size_comp','unit'=>'unit','comp_unit'=>'comp_unit'];
		if(!empty($media_form)){
				foreach($media_form as $mform){
					if($mform->required==1){
                        if(!in_array($mform->sys_field_name, $static_conditional)){
						  $required[$mform->sys_field_name]=$mform->sys_field_name;
                        }
					}else{
						$safe[$mform->sys_field_name]=$mform->sys_field_name;
					}
				}
			$media_form_virtual = FormBuilderSystem::find()->where(['sys_field_name'=>['client_id','client_case_id','upload_files'],'sys_form'=>'media_form'])->all();
			if(!empty($media_form_virtual)){
				foreach($media_form_virtual as $mfromvir){
					if($mfromvir->required==1){
                        $required[$mfromvir->sys_field_name]=$mfromvir->sys_field_name;
                    }
				}
			}

        return [
			[$required, 'required'],
			[$safe, 'safe'],
		];
		}else{
		return [
            [['checkedin_by', 'dup_evid', 'org_link', 'evid_type', 'cat_id', 'quantity',  'enctype', 'evid_stored_location', 'status', 'has_contents', 'created_by', 'modified_by'], 'integer'],
            [['other_evid_num', 'received_time', 'received_from', 'evd_Internal_no', 'serial', 'model', 'hash', 'cont', 'evid_desc', 'evid_label_desc', 'contents_copied_to', 'mpw', 'bbates', 'ebates', 'm_vol', 'ftpun', 'ftppw', 'encpw', 'evid_notes', 'barcode'], 'string'],
            [['received_date', 'created', 'modified'], 'safe'],
            [['received_from','received_date','received_time','evid_type','evid_desc'], 'required'],
            // [['contents_total_size','unit'], 'required','when'=>function($model){ return ($model->contents_total_size_comp == '' && $model->comp_unit == '');}],
        	[['contents_total_size','contents_total_size_comp','unit','comp_unit'],'either','skipOnEmpty' => false, 'skipOnError' => false],
        	// [[],'either','skipOnEmpty' => false, 'skipOnError' => false],
            [['evid_type'], 'exist', 'skipOnError' => true, 'targetClass' => EvidenceType::className(), 'targetAttribute' => ['evid_type' => 'id']],
        ];
        }
    }

    public function either($attribute_name)
    {
    	$field1  = "Total Size";
    	$field2  = "Compressed Size";
    	$field11 = "Total Size Units";
    	$field21 = "Compressed Size Units";
    	if (empty($this->contents_total_size_comp) && empty($this->contents_total_size)) {
    		$this->addError('contents_total_size', Yii::t('user', "either {$field1} AND {$field11} or {$field2} AND {$field21} cannot be blank."));
    		$this->addError('contents_total_size_comp', Yii::t('user', "either {$field1} AND {$field11} or {$field2} AND {$field21} cannot be blank."));
    		//echo "if";
    		return false;
    	}
    	if(!empty($this->contents_total_size_comp) && empty($this->comp_unit)){
    		$this->addError('comp_unit', Yii::t('user', "{$field21} cannot be blank."));
    		//echo "if1";
    		return false;
    	}
    	if(!empty($this->contents_total_size) && empty($this->unit)){
    		$this->addError('unit', Yii::t('user', "{$field11} cannot be blank."));
    		//echo "if2";
    		return false;
    	}
    	return true;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'Media#',
            'checkedin_by' => 'Checkedin By',
            'dup_evid' => 'Dup Evid',
            'org_link' => 'Org Link',
            'other_evid_num' => 'Other Media #',
            'received_date' => 'Date Received',
            'received_time' => 'Time Received',
            'received_from' => 'Received From',
            'evd_Internal_no' => 'Internal Media #',
            'evid_type' => 'Media Type',
            'cat_id' => 'Category',
            'serial' => 'Unique ID/Serial',
            'model' => 'Model',
            'hash' => 'Hash Value',
            'quantity' => 'Quantity',
            'cont' => 'Container #',
            'evid_desc' => 'Media Description',
            'evid_label_desc' => 'Label Description',
            'contents_total_size' => 'Total Size',
            'contents_total_size_comp' => 'Compressed  Size',
            'unit' => 'Total Size Units',
            'comp_unit' => 'Compressed Size Units',
            'contents_copied_to' => 'Total Copied To',
            'mpw' => 'Password',
            'bbates' => 'BegBates',
            'ebates' => 'EndBates',
            'm_vol' => 'Media Volume',
            'ftpun' => 'FTP Username',
            'ftppw' => 'FTP Password',
            'enctype' => 'Encryption Type',
            'encpw' => 'Encryption Password',
            'evid_stored_location' => 'Stored Location',
            'evid_notes' => 'Notes',
            'status' => 'Status',
            'has_contents' => 'Has Contents',
            'barcode' => 'Barcode',
            'created' => 'Created',
            'created_by' => 'Created By',
            'modified' => 'Modified',
            'modified_by' => 'Modified By',
        	'evidence_by_case' => 'Total Evidence Count By Case',
        	'evidType' => 'Evidence Type Count By Case',
        	'client_id' => 'Client',
        	'client_case_id' => 'Case'
        //  'client_name' => 'Client',
        //  'case_name' => 'Case'
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
    	if(!empty($this->contents_total_size) && empty($this->unit)){
            //echo "here";die;
            $this->addError('unit', 'Total Size Units cannot be blank.');
            //$this->addRule(['unit'], 'required');//->validate();
            return false;
        }

    	if (parent::beforeSave($insert)) {

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
    /**
     * @inheritdoc
     */
    public function getUser()
    {
    	return $this->hasOne(User::className(), ['id' => 'created_by']);
    }

    public function getEvidencetype()
    {
        return $this->hasOne(EvidenceType::className(), ['id' => 'evid_type']);
    }
    public function getEvidencecategory()
    {
        return $this->hasOne(EvidenceCategory::className(), ['id' => 'cat_id']);
    }
    public function getEvidenceunit()
    {
        return $this->hasOne(Unit::className(), ['id' => 'unit']);
    }
    public function getEvidencecompunit()
    {
        return $this->hasOne(Unit::className(), ['id' => 'comp_unit'])->alias('evidcomp');
    }
    public function getEvidenceattachments()
    {
        return $this->hasMany(Mydocument::className(), ['reference_id' => 'id'])->andOnCondition(['origination' => 'Media']);
    }
    public function getEvidencestoredloc()
    {
        return $this->hasOne(EvidenceStoredLoc::className(), ['id' => 'evid_stored_location']);
    }
    public function getEvidencecheckedin()
    {
        return $this->hasOne(User::className(), ['id' => 'checkedin_by']);
    }
    public function getEvidencetaskinstruct()
    {
        return $this->hasMany(TaskInstructEvidence::className(), ['evidence_id' => 'id']);
    }
    public function getEvidencecontent()
    {
        return $this->hasMany(EvidenceContents::className(), ['evid_num_id' => 'id']);
    }
    public function getEvidenceclientcase()
    {
        return $this->hasMany(ClientCaseEvidence::className(), ['evid_num_id' => 'id']);
    }
    public function getEvidenceencrypttype(){

        return $this->hasOne(EvidenceEncryptType::className(), ['id' => 'enctype']);
    }
     /**
     * get status image in Evidence Grid
     */
    public function getStatusImage($status) {
        if ($status == 1) {
             $statusImg ='<a href="javascript:void(0);" class="icon-fa" title="Checked In"><em class="fa fa-download text-success" title="Checked In"></em><span class="hide">Checked In</span></a>';
        } else if ($status == 2) {
            $statusImg ='<a href="javascript:void(0);" class="icon-fa" title="Checked Out"><em class="fa fa-upload text-warning" title="Checked Out"></em><span class="hide">Checked Out</span></a>';
            //$statusImg = Html::image(Yii::app()->theme->baseUrl . "/images/evidence_checkout.png", "Check Out", array("title" => "Check Out"));
        } else if ($status == 3) {
             $statusImg ='<a href="javascript:void(0);" class="icon-fa" title="Destroyed"><em class="fa fa-times-circle  text-danger" title="Destroyed"></em><span class="hide">Destroyed</span></a>';
        } else if ($status == 4) {
           $statusImg ='<a href="javascript:void(0);" class="icon-fa" title="Moved"><em class="fa fa-arrow-right text-warning" title="Moved"></em><span class="hide">Moved</span></a>';
        } else if ($status == 5) {
          $statusImg ='<a href="javascript:void(0);" class="icon-fa" title="Returned"><em class="fa fa-arrow-left text-danger" title="Returned"></em><span class="hide">Returned</span></a>';
        } else {
            $statusImg = "Undefined";
        }
        return $statusImg;
    }
    /* search term in evidence description */
    public function getEvidenceDetails($evid_desc) {
        $evidenceList = $this->find()->where("evid_desc like '%:evid_desc%'", [':evid_desc'=>$evid_desc])->orderBy("id ASC")->all();
        return $evidenceList;
    }
    public function getAttachments($id){
		$attachment="";
		$evid_docs = Mydocument::find()->select(['tbl_mydocument.id'])->where(['tbl_mydocument.reference_id'=>(int) $id,'tbl_mydocument.origination'=>'Media'])->all();
		if (!empty($evid_docs)) {
           foreach ($evid_docs as $filename) {
			   if ($attachment == "")
				$attachment ='<a href="javascript:void(0)" onclick="downloadattachment(' . $filename->id . ')" class="icon-fa" title="Attachment"><em class="fa fa-paperclip" title="Attach"></em><span class="screenreader">Download Attachment</span></a>';
			   else
				$attachment .='&nbsp;&nbsp;<a href="javascript:void(0)" onclick="downloadattachment(' . $filename->id . ')" class="icon-fa" title="Attachment"><em class="fa fa-paperclip"  title="Attach"></em><span class="screenreader">Download Attachment</span></a>';
		   }
		}
		return $attachment;
	}

	/**
	 * Created By User
	 * @return Full name
	 */
	public function getCreatedByUser($id)
	{
		$user = User::find()->select(["CONCAT(usr_first_name,' ',usr_lastname) full_name"])->where(['id' => $id])->one();
		return $user->full_name;
	}
}
