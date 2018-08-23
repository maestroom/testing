<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%mydocument}}".
 *
 * @property integer $id
 * @property integer $p_id
 * @property integer $reference_id
 * @property integer $team_loc
 * @property string $fname
 * @property string $origination
 * @property integer $u_id
 * @property string $is_private
 * @property string $type
 * @property integer $doc_id
 * @property integer $doc_size
 * @property string $doc_type
 * @property string $created
 * @property integer $created_by
 * @property string $modified
 * @property integer $modified_by
 *
 * @property MydocumentsBlob[] $mydocumentsBlobs
 */
class Mydocument extends \yii\db\ActiveRecord
{
    
     /*use \kartik\tree\models\TreeTrait {
        isDisabled as parentIsDisabled; // note the alias
    }*/
    /**
     * @inheritdoc
     */
    public $mydoc=array();
    public $mydoc_str=array();
    
    public static function tableName()
    {
        return '{{%mydocument}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['p_id', 'team_loc', 'origination', 'u_id', 'created', 'created_by', 'modified', 'modified_by'], 'required'],
            [['p_id', 'reference_id', 'team_loc', 'u_id', 'doc_id', 'doc_size', 'created_by', 'modified_by'], 'integer'],
            [['fname', 'origination', 'is_private', 'type', 'doc_type'], 'string'],
            [['created', 'modified'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'p_id' => 'P ID',
            'reference_id' => 'Reference ID',
            'team_loc' => 'Team Loc',
            'fname' => 'Fname',
            'origination' => 'Origination',
            'u_id' => 'U ID',
            'is_private' => 'Is Private',
            'type' => 'Type',
            'doc_id' => 'Doc ID',
            'doc_size' => 'Doc Size',
            'doc_type' => 'Doc Type',
            'created' => 'Created',
            'created_by' => 'Created By',
            'modified' => 'Modified',
            'modified_by' => 'Modified By',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMydocumentsBlobs()
    {
        return $this->hasOne(MydocumentsBlob::className(), ['id'=>'doc_id']);
    }
    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
    	if (parent::beforeSave($insert)) {
    		
    		if($this->isNewRecord){
				if(!isset($this->doc_id))
					$this->doc_id=0;
					
                       // $this->has_contents = 'N';
                        //$this->is_private = 0;
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
     * Function use to remove attchement deleted by user from system
     * */
    public function removeAttachments($remove_attachments = "")
    {
    	if(isset($remove_attachments) && $remove_attachments!=""){
    		/* Remove Attachments */
    		MydocumentsBlob::deleteAll('id IN (select doc_id  FROM tbl_mydocument where id IN ('.$remove_attachments.'))');
    		Mydocument::deleteAll('id IN ('.$remove_attachments.')');
    		/* Remove Attachments */
    	}
    	return;
    }
    /**
     * Function use to save attchement added by user to system 
     * */
    public function Savemydocs($module_name,$field_name,$doc_arr,$remove_attachments = "")
    {
       if(isset($remove_attachments) && $remove_attachments!=""){
	    /*Remove Attachments*/
	    MydocumentsBlob::deleteAll('id IN (select doc_id  FROM tbl_mydocument where id IN ('.$remove_attachments.'))');
	    Mydocument::deleteAll('id IN ('.$remove_attachments.')');
	    /*Remove Attachments*/
       }
       $file_arr=array(); 
       if (!empty($_FILES[$module_name]['name'][$field_name])) {
                if (!empty($_FILES[$module_name]['tmp_name'][$field_name])) {
                    $attach_files = $_FILES[$module_name]['name'][$field_name];
                    $currtime = time();
                    $i = 0;
                    foreach ($_FILES[$module_name]['name'][$field_name] as $attachments) {
                        //$new_file_name = preg_replace('/[^A-Za-z0-9_]/', '-', $attachments);
                        $file_name = $attachments; //$_FILES['Comments']['name']['attachments'][$i];
                        $name = explode('.', $file_name);
                        $arr_set = explode("/", $file_name);
                        $arr_filename = explode(".", $arr_set[count($arr_set) - 1]);
                        $fileext = $arr_filename[count($arr_filename) - 1];
                        $new_file_name = preg_replace('/[^A-Za-z0-9_]/', '-', $name[0]) . "." . $fileext;
                        $mime = $_FILES[$module_name]['type'][$field_name][$i];
                        $data = utf8_encode(file_get_contents($_FILES[$module_name]['tmp_name'][$field_name][$i]));
                        $size = intval($_FILES[$module_name]['size'][$field_name][$i]);
                        $MydocumentsBlob_model = new MydocumentsBlob();
                        $MydocumentsBlob_model->doc = $data;
                        $MydocumentsBlob_model->save(false);
                        $blob_doc_id = $MydocumentsBlob_model->getPrimaryKey();
                        $docmodel = new Mydocument();
                        
                        $docmodel->p_id = $doc_arr['p_id'];
                        $docmodel->reference_id = $doc_arr['reference_id'];
                        $docmodel->team_loc = $doc_arr['team_loc'];
                        $docmodel->fname = $new_file_name;
                        $docmodel->origination = $doc_arr['origination'];
                        $docmodel->u_id = Yii::$app->user->identity->id;
                        $docmodel->is_private = ((isset($doc_arr['is_private']) && $doc_arr['is_private']!="" && $doc_arr['is_private']!=NULL)?$doc_arr['is_private']:0);
                        $docmodel->type = $doc_arr['type'];
                        $docmodel->doc_id = $blob_doc_id;
                        $docmodel->doc_size = $size;
                        $docmodel->doc_type = $mime;
                        
                        $docmodel->save(false);
                        array_push($file_arr, $docmodel->getPrimaryKey());
                        $i++;
                    }
                }
            }
        return $file_arr;    
    }
    
    /**
     * Function use to save Instruction attchement added by user to system
     * */
    public function SaveInstructionmydocs($module_name,$field_name,$servicetask_id,$doc_arr,$remove_attachments = "")
    {
    	if(isset($remove_attachments) && $remove_attachments!=""){
    		/*Remove Attachments*/
    		MydocumentsBlob::deleteAll('id IN (select doc_id  FROM tbl_mydocument where id IN ('.$remove_attachments.'))');
    		Mydocument::deleteAll('id IN ('.$remove_attachments.')');
    		/*Remove Attachments*/
    	}
    	$file_arr=array();
    	if (!empty($_FILES[$module_name]['name'][$field_name][$servicetask_id])) {
    		if (!empty($_FILES[$module_name]['tmp_name'][$field_name][$servicetask_id])) {
    			$attach_files = $_FILES[$module_name]['name'][$field_name][$servicetask_id];
    			$currtime = time();
    			$i = 0;
    			foreach ($_FILES[$module_name]['name'][$field_name][$servicetask_id] as $attachments) {
    				if(isset($_FILES[$module_name]['tmp_name'][$field_name][$servicetask_id][$i]) && $_FILES[$module_name]['tmp_name'][$field_name][$servicetask_id][$i]!=""){
	    				//$new_file_name = preg_replace('/[^A-Za-z0-9_]/', '-', $attachments);
	    				$file_name = $attachments; //$_FILES['Comments']['name']['attachments'][$i];
	    				$name = explode('.', $file_name);
	    				$arr_set = explode("/", $file_name);
	    				$arr_filename = explode(".", $arr_set[count($arr_set) - 1]);
	    				$fileext = $arr_filename[count($arr_filename) - 1];
	    				$new_file_name = preg_replace('/[^A-Za-z0-9_]/', '-', $name[0]) . "." . $fileext;
	    				$mime = $_FILES[$module_name]['type'][$field_name][$servicetask_id][$i];
	    				$data = utf8_encode(file_get_contents($_FILES[$module_name]['tmp_name'][$field_name][$servicetask_id][$i]));
	    				$size = intval($_FILES[$module_name]['size'][$field_name][$servicetask_id][$i]);
	    				$MydocumentsBlob_model = new MydocumentsBlob();
	    				$MydocumentsBlob_model->doc = $data;
	    				$MydocumentsBlob_model->save(false);
	    				$blob_doc_id = $MydocumentsBlob_model->getPrimaryKey();
	    				$docmodel = new Mydocument();
	    
	    				$docmodel->p_id = $doc_arr['p_id'];
	    				$docmodel->reference_id = $doc_arr['reference_id'];
	    				$docmodel->team_loc = $doc_arr['team_loc'];
	    				$docmodel->fname = $new_file_name;
	    				$docmodel->origination = $doc_arr['origination'];
	    				$docmodel->u_id = Yii::$app->user->identity->id;
	    				$docmodel->is_private = ((isset($doc_arr['is_private']) && $doc_arr['is_private']!="" && $doc_arr['is_private']!=NULL)?$doc_arr['is_private']:0);
	    				$docmodel->type = $doc_arr['type'];
	    				$docmodel->doc_id = $blob_doc_id;
	    				$docmodel->doc_size = $size;
	    				$docmodel->doc_type = $mime;
	    
	    				$docmodel->save(false);
	    				array_push($file_arr, $docmodel->getPrimaryKey());
	    				$i++;
    				}
    			}
    		}
    	}
    	return $file_arr;
    }
    
    public function fecthDataRec($id,$origination,$p_id,$teamLoc)
    {
    		$userID = Yii::$app->user->identity->id;
            $this->mydoc=array();
            if($origination=="Case")
            {
                    $caseInfo=ClientCase::findOne($id);
                    $root_title=strtoupper($caseInfo->client->client_name." - ". $caseInfo->case_name);
                    $this->mydoc_str='<li id=root ><a href=# id=0 onclick=liclicked("root") >'.(html_entity_decode($root_title)).'</a>';
                    $this->mydoc_str='<li id=root title="'.$root_title.'" >'.$root_title.'';
                    $data=$this->find()->select(['id','is_private','created_by','p_id','fname','type'])->where(['origination'=>'Case','reference_id'=>$id,'p_id'=>$p_id])->orderBy(['type'=>SORT_ASC,'fname'=>SORT_ASC])->all();//.' AND created_by='.$userID
                    
                    if(!empty($data)){
                            foreach ($data as $da)
                            {
                                    $is_private="";
                                    $add="";
                                    $ondbclk="";
                                    $isfile="";
                                    if($da->type==0)
                                    {
                                            //$add='<ins class="jstree-file">&nbsp;</ins>';
                                            $ondbclk='downloadattachment('.$da->id.')';
                                            $isfile='data-jstree=\'{"icon":"glyphicon glyphicon-file"}\'';
                                            if($da->is_private==1)
                                            {
                                                $is_private=1;                            
                                                $isfile='data-jstree=\'{"icon":"jstree-lockfile"}\'';
                                            }
                                    }
                                    else
                                    {
                                        if($da->is_private==1)
                                        {
                                            $is_private=1;
                                            $isfile='data-jstree=\'{"icon":"jstree-lockicon"}\'';
                                        }
                                    }
                                    $this->mydoc[$da->id]=$da->id;
                                    if($is_private!="") {
                                                    if($da->created_by==$userID) {
                                                            $fname=html_entity_decode($da->fname, ENT_QUOTES);
                                                            $fname=str_replace("'","&#39;",$fname);
                                                            $this->mydoc_str.='<ul><li '.$isfile.' id='.$da->id.' data-pid='.$da->p_id.' onclick=liclicked('.$da->id.') ondblclick="'.$ondbclk.'" data-type="'.$da->type.'" title=\''.$fname.'\'>'.$add.(html_entity_decode($da->fname));
                                                            $this->getrecursive($da->id,$da->p_id,$id);
                                                            $this->mydoc_str.='</li></ul>';
                                                    }
                                    } else  {
                                            //echo html_entity_decode($da->fname, ENT_QUOTES);die;
                                            $fname=html_entity_decode($da->fname, ENT_QUOTES);
                                            $fname=str_replace("'","&#39;",$fname);
                                            $this->mydoc_str.='<ul><li '.$isfile.'  id='.$da->id.' id='.$da->id.' data-pid='.$da->p_id.' onclick=liclicked('.$da->id.') ondblclick="'.$ondbclk.'" data-type="'.$da->type.'" title=\''.$fname.'\' >'.$add.''.html_entity_decode($da->fname, ENT_QUOTES).'';
                                            $this->getrecursive($da->id,$da->p_id,$id);
                                            $this->mydoc_str.='</li></ul>';
                                    }

                            }
                    }
            }
            else if($origination=="Team")
            {
                    $teamInfo=Team::findOne($id);
                    if($teamLoc!=1)
                    {
                            $teamLocation=TeamlocationMaster::findOne($teamLoc);
                            $root_title=strtoupper($teamInfo->team_name)." - ".strtoupper($teamLocation->team_location_name);
                    }else{
                            $root_title=strtoupper($teamInfo->team_name);
                    }



                    $this->mydoc_str='<li id=root ><a href=# id=0 onclick=liclicked("root")>'.$root_title.'</a>';
                    $this->mydoc_str='<li id=root title="'.$root_title.'" >'.(html_entity_decode($root_title)).'';
                    $data=$this::find()->select(['id','is_private','created_by','p_id','fname','team_loc','type'])->where(["origination"=>'Team',"reference_id"=>$id,"p_id"=>$p_id,"team_loc"=>$teamLoc])->orderBy(['type'=>'asc','fname'=>'asc'])->all();
                   // echo '<pre>'; print_r(($data));die;

                    if(!empty($data)){
                            foreach ($data as $da)
                            {
                                    $add="";
                                    $is_private="";
                                    $ondbclk="";
                                    $isfile="";
                                    if($da->type==0)
                                    {
                                            $ondbclk='ondblclick="downloadattachment('.$da->id.')"';
                                            $isfile='data-jstree=\'{"icon":"glyphicon glyphicon-file"}\'';
                                            if($da->is_private==1)
                                            {
                                                $is_private=1;
                                                $isfile='data-jstree=\'{"icon":"jstree-lockfile"}\'';
                                            }
                                            
                                    }
                                    else
                                    {
                                        if($da->is_private==1)
                                        {
                                            $is_private=1;
                                            $isfile='data-jstree=\'{"icon":"jstree-lockicon"}\'';
                                        }
                                    }
                                    //$is_private='<span class="jstree-private img-col">&nbsp;</span>'; //$is_private='<span class="jstree-private">&nbsp;</span>';

                                    $this->mydoc[$da->id]=$da->id;
                                    if($is_private!="")
                                    {
                                                    if($da->created_by==$userID)
                                                    {
                                                        $fname=html_entity_decode($da->fname, ENT_QUOTES);
                                                        $fname=str_replace("'","&#39;",$fname);
                                                        $this->mydoc_str.='<ul><li '.$isfile.'  id='.$da->id.' id='.$da->id.' data-pid='.$da->p_id.' onclick=liclicked('.$da->id.')  data-type="'.$da->type.'" '.$ondbclk.'  title=\''.$fname.'\'>'.$add.(html_entity_decode($da->fname));
                                                        $this->getrecursivedata($da->id,$da->p_id,$id);
                                                        $this->mydoc_str.='</li></ul>';
                                                            
                                                    }
                                    }
                                    else
                                    {
                                        $fname=html_entity_decode($da->fname, ENT_QUOTES);
                                        $fname=str_replace("'","&#39;",$fname);
                                        $this->mydoc_str.='<ul><li '.$isfile.'   id='.$da->id.' data-pid='.$da->p_id.' onclick=liclicked('.$da->id.')  data-type="'.$da->type.'" '.$ondbclk.'  title=\''.$fname.'\'>'.$add.(html_entity_decode($da->fname));
                                        $this->getrecursivedata($da->id,$da->p_id,$id);
                                        $this->mydoc_str.='</li></ul>';
                                    }


                            }

                    }
            }

            $this->mydoc_str.='</li>';
            return array('mydoc'=>$this->mydoc,'mydoc_str'=>$this->mydoc_str);
    }
    public function getrecursive($id,$pid,$case_id,$next=false)
	{
        
			$userID=Yii::$app->user->identity->id;
			$first_data=$this->findOne($id);
			//$data1=$this->find()->select('id','is_private','created_by','p_id','fname','type')->where(["origination='Case' AND case_id=".$case_id." AND p_id=".$id,'order'=>'type asc ,fname asc'));//.' AND created_by='.$userID
                        $data1=$this->find()->select(['id','is_private','created_by','p_id','fname','type'])->where(['origination'=>'Case','reference_id'=>$case_id,'p_id'=>$id])->orderBy(['type'=>SORT_ASC,'fname'=>SORT_ASC])->all();//.' AND created_by='.$userID
			//echo $case_id.$id;print_r($data1);die;
                        if(!empty($data1))
				{
					$this->mydoc_str.='<ul>';
					foreach ($data1 as $da)
					{
						$add="";
						$ondbclk="";
						$is_private="";
                                                $isfile="";
						//if(is_file($da->path))
						if($da->type==0)//"D for document"
						{
							$add='<ins class="jstree-file">&nbsp;</ins>';
							$ondbclk='ondblclick="downloadattachment('.$da->id.')"';
                                                        $isfile='data-jstree=\'{"icon":"glyphicon glyphicon-file"}\'';
                                                        if($da->is_private==1)
                                                        {
                                                            $is_private=1;
                                                            $isfile='data-jstree=\'{"icon":"jstree-lockfile"}\'';
                                                        }
						}
                                                else
                                                {
                                                    if($da->is_private==1)
                                                    {
                                                        $is_private=1;
                                                        $isfile='data-jstree=\'{"icon":"jstree-lockicon"}\'';
                                                    }
                                                }                                           /*else
                                                {
                                                    if($da->is_private==1)
                                                    {
                                                        $isfile='data-jstree=\'{"icon":"//jstree.com/tree.png"}\'';
                                                        //$is_private='irshadkhan&nbsp;<span class="glyphicon glyphicon-lock"></span>';
                                                    }
                                                }    */
                                                   //$is_private='irshad<span data-jstree=\'{"icon":"glyphicon glyphicon-file"}\'></span>';
						//$is_private='<a href="javascript:RemoveHoliday(0) class="icon-fa" title="Checked In"><em class="fa fa-download text-success"></em></a>test1<em class="fa fa-lock" aria-hidden="true"></em>'; //<span class="jstree-private">&nbsp;</span>';
						
						$this->mydoc[$da->id]=$da->id;
						if($is_private!="")
						{
							if($da->created_by==$userID)
							{
							
							//$this->mydoc_str.='<li class=jstree-leaf   id='.$da->id.'><a href=#  id='.$da->p_id.' onclick=liclicked('.$da->id.') ondblclick='.$ondbclk.'>'.$add.$da->fname.'</a>'.$is_private;
                            $fname=html_entity_decode($da->fname, ENT_QUOTES);
                            $fname=str_replace("'","&#39;",$fname);
                            $this->mydoc_str.='<li '.$isfile.'  id='.$da->id.' data-pid='.$da->p_id.' onclick=liclicked('.$da->id.')  data-type="'.$da->type.'" '.$ondbclk.'  title=\''.$fname.'\'>'.$add.(html_entity_decode($da->fname));
							$this->getrecursive($da->id,$da->p_id,$case_id,true);
							$this->mydoc_str.='</li>';
							}
						}
						else
						{
						
						//$this->mydoc_str.='<li class=jstree-leaf   id='.$da->id.'><a href=#  id='.$da->p_id.' onclick=liclicked('.$da->id.') ondblclick='.$ondbclk.'>'.$add.$da->fname.'</a>';
                        $fname=html_entity_decode($da->fname, ENT_QUOTES);
                        $fname=str_replace("'","&#39;",$fname);
                        $this->mydoc_str.='<li '.$isfile.'  id='.$da->id.' data-pid='.$da->p_id.' onclick=liclicked('.$da->id.') data-type="'.$da->type.'" '.$ondbclk.' title=\''.$fname.'\'>'.$add.(html_entity_decode($da->fname));
						$this->getrecursive($da->id,$da->p_id,$case_id,true);
						$this->mydoc_str.='</li>';
						}
					}
					$this->mydoc_str.='</ul>';
				}
			return;
	}
    public function getrecursivedata($id,$pid,$team_id,$next=false)
	{
            $userID=Yii::$app->user->identity->id;
            $first_data=$this->findOne($id);
            $data1=$this::find()->select(['id','is_private','created_by','p_id','fname','team_loc','type'])->where(["origination"=>'Team',"reference_id"=>$team_id,"p_id"=>$id])->orderBy(['type'=>'asc','fname'=>'asc'])->all();
                if(!empty($data1))
                {
                    $this->mydoc_str.='<ul>';
                    foreach ($data1 as $da)
                    {
                        $add="";
                        $ondbclk="";
                        $is_private="";
                        $isfile="";
                        if($da->type==0)//"D for document"
                        {
                                $add='<ins class="jstree-file">&nbsp;</ins>';
                                $ondbclk='ondblclick="downloadattachment('.$da->id.')"';
                                $isfile='data-jstree=\'{"icon":"glyphicon glyphicon-file"}\'';
                                if($da->is_private==1)
                                {
                                    $is_private=1;
                                    $isfile='data-jstree=\'{"icon":"jstree-lockfile"}\'';
                                }
                        }
                        else
                        {
                            if($da->is_private==1)
                            {
                                $is_private=1;
                                $isfile='data-jstree=\'{"icon":"jstree-lockicon"}\'';
                            }
                        } 
                        //if($da->is_private==1)
                        //$is_private='<span class="jstree-private">&nbsp;</span>';

                        $this->mydoc[$da->id]=$da->id;
                        if($is_private!="") 
                        {
                                if($da->created_by==$userID)
                                {
                                    $fname=html_entity_decode($da->fname, ENT_QUOTES);
                                    $fname=str_replace("'","&#39;",$fname);
                                    $this->mydoc_str.='<li '.$isfile.'  id='.$da->id.' data-pid='.$da->p_id.' onclick=liclicked('.$da->id.')  data-type="'.$da->type.'" '.$ondbclk.' title=\''.$fname.'\'>'.$add.(html_entity_decode($da->fname));
                                    $this->getrecursivedata($da->id,$da->p_id,$team_id,true);
                                    $this->mydoc_str.='</li>';
                                }
                        }else{
                            $fname=html_entity_decode($da->fname, ENT_QUOTES);
                            $fname=str_replace("'","&#39;",$fname);
                            $this->mydoc_str.='<li '.$isfile.'  id='.$da->id.' data-pid='.$da->p_id.' onclick=liclicked('.$da->id.') data-type="'.$da->type.'"  '.$ondbclk.' title=\''.$fname.'\'>'.$add.(html_entity_decode($da->fname));
                            $this->getrecursivedata($da->id,$da->p_id,$team_id,true);
                            $this->mydoc_str.='</li>';
                        }
                    }
                    $this->mydoc_str.='</ul>';
                }
            return;
	}    
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'created_by']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTaskInstruct()
    {
        return $this->hasOne(TaskInstruct::className(), ['id'=>'reference_id']);
    }
    
    public function getTaskInstructServicetask()
    {
        return $this->hasOne(TaskInstructServicetask::className(), ['id'=>'reference_id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTaskInstructNotes()
    {
        return $this->hasOne(TaskInstructNotes::className(), ['id'=>'reference_id']);
    }
     /**
     * @return \yii\db\ActiveQuery
     */
    public function getTasksUnitsTodos()
    {
        return $this->hasOne(TasksUnitsTodos::className(), ['id'=>'reference_id']);
    }
     /**
     * @return \yii\db\ActiveQuery
     */
    public function getComments()
    {
        return $this->hasOne(Comments::className(), ['Id'=>'reference_id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTasksUnits() 
    {
    	return $this->hasOne(TasksUnits::className(), ['id'=>'reference_id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClientCase() 
    {
    	return $this->hasOne(ClientCase::className(), ['id'=>'reference_id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTeam() 
    {
    	return $this->hasOne(Team::className(), ['id'=>'reference_id']);
    }
}
