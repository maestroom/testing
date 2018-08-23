<?php

namespace app\models;

use Yii;
use app\models\FormElementOptions;
use app\models\Servicetask;
use app\models\FormCustodianValues;
use yii\helpers\ArrayHelper;
use yii\helpers\HtmlPurifier;

/**
 * This is the model class for table "{{%form_builder}}".
 *
 * @property integer $id
 * @property integer $formref_id
 * @property integer $form_type
 * @property string $element_id
 * @property string $element_type
 * @property integer $element_order
 * @property string $element_text_value
 * @property integer $element_required
 * @property integer $element_no_load_prev
 * @property integer $element_optionchk
 * @property integer $element_qareportuse
 * @property string $element_label
 * @property string $element_description
 * @property integer $element_sync_prod
 * @property integer $element_field_type
 * @property integer $remove
 *
 * @property FormCustodianValues[] $formCustodianValues
 * @property FormElementOptions[] $formElementOptions
 * @property FormInstructionValues[] $formInstructionValues
 * @property TasksUnitsData[] $tasksUnitsDatas
 */
class FormBuilder extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%form_builder}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['formref_id', 'form_type', 'element_id', 'element_type', 'element_order'], 'required'],
            [['formref_id', 'form_type', 'element_order','element_view', 'element_required', 'element_no_load_prev', 'element_qareportuse', 'element_sync_prod', 'remove', 'default_unit'], 'integer'],
            [['element_id', 'element_type', 'element_text_value', 'element_label', 'element_description', 'default_answer'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'formref_id' => 'Formref ID',
            'form_type' => 'Form Type',
            'element_id' => 'Element ID',
            'element_type' => 'Element Type',
            'element_order' => 'Element Order',
            'element_text_value' => 'Element Text Value',
            'element_required' => 'Element Required',
            'element_no_load_prev' => 'Element No Load Prev',
       //     'element_optionchk' => 'Element Optionchk',
            'element_qareportuse' => 'Element Qareportuse',
            'element_label' => 'Element Label',
            'element_description' => 'Element Description',
            'element_sync_prod' => 'Element Sync Prod',
			'element_view'   =>'Element View',
            //'element_field_type' => 'Element Field Type',
            'remove' => 'Remove',
            'default_answer' => 'Default Answer',
            'default_unit' => 'Default Unit'
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
		if (parent::beforeSave($insert)) {
			if(!isset($this->default_answer))
				$this->default_answer = NULL;
			if(isset($this->default_answer))
				$this->default_answer = html_entity_decode($this->default_answer);
			if(!isset($this->default_unit) || $this->default_unit == '')
				$this->default_unit = 0;
			if(!isset($this->element_view))
				$this->element_view = 0;
		
			return true;
    	} else {
    		return false;
    	}
	}
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFormCustodianValues()
    {
        return $this->hasMany(FormCustodianValues::className(), ['form_builder_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFormElementOptions()
    {
        return $this->hasMany(FormElementOptions::className(), ['form_builder_id' => 'id'])->andOnCondition(['tbl_form_element_options.remove'=>0])->orderBy('tbl_form_element_options.sort_order');
        //->andOnCondition(['tbl_form_element_options.remove'=>0])
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getformElementOptionsForAll()
    {
        return $this->hasMany(FormElementOptions::className(), ['form_builder_id' => 'id'])->orderBy('sort_order');
        //->andOnCondition(['tbl_form_element_options.remove'=>0])
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFormInstructionValues()
    {
        return $this->hasMany(FormInstructionValues::className(), ['form_builder_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTasksUnitsDatas()
    {
        return $this->hasMany(TasksUnitsData::className(), ['form_builder_id' => 'id']);
    }
    public function dbToJsonFormat($data = array(),$form_mode){
    	$sync_prod = Yii::$app->params['sync_prod'];
    	$properties = array();
   		if(!empty($data)){
    		foreach ($data as $fdata){                    
    			$properties[$fdata['element_id']]['form_builder_id']=$fdata['id'];
    			$properties[$fdata['element_id']]['id']=$fdata['element_id'];
    			$properties[$fdata['element_id']]['type']=$fdata['element_type'];
    			$properties[$fdata['element_id']]['label']=$fdata['element_label'];
    			$properties[$fdata['element_id']]['value']='';
    			$properties[$fdata['element_id']]['values']='';
    			$formelement_option=array();
    			$formelement_remove_status=array();
    			if(in_array($fdata['element_type'],array('dropdown','checkbox','radio'))){
    				$ele_values='';
                                //echo '<pre>';print_r($fdata);die;
//                                $form_mode
//                                if($form_mode == 'front'){                                    
//                                    $newfeedData = $fdata['formElementOptionsForAll'];
//                                }else{ // for system
//                                    $newfeedData = $fdata['formElementOptions'];                                    
//                                }
    				foreach ($fdata['formElementOptions'] as $options){
                                    if($options['is_default'] == 1){
                                            $formelement_option[] = $options['element_option'];
                                    }
                                    $formelement_remove_status[] = $options['remove'];
                                    if($ele_values == ''){
                                        $ele_values=$options['element_option'];
                                        $ele_values_option_ids=$options['id'];                                            
                                    }else{
                                        $ele_values=$ele_values.';'.$options['element_option'];
                                        $ele_values_option_ids=$ele_values_option_ids.';'.$options['id'];                                            
                                    } 
    				}
    				if($ele_values!=''){
    					$properties[$fdata['element_id']]['values']=$ele_values;
    					$properties[$fdata['element_id']]['values_option_ids']=$ele_values_option_ids;
    				}
    			}
                        $properties[$fdata['element_id']]['form_type']=$fdata['form_type'];
    			$properties[$fdata['element_id']]['optionchk'] = implode(',',$formelement_option);
    			$properties[$fdata['element_id']]['optionrmv'] = implode(',',$formelement_remove_status);                        
    			$properties[$fdata['element_id']]['description']=$fdata['element_description'];
				$properties[$fdata['element_id']]['required']=$fdata['element_required'];
				$properties[$fdata['element_id']]['element_view']=$fdata['element_view'];
    			$properties[$fdata['element_id']]['no_load_prev']=$fdata['element_no_load_prev'];
    			$properties[$fdata['element_id']]['order']=$fdata['element_order'];
    			$properties[$fdata['element_id']]['text_val']=$fdata['element_text_value'];
    			$properties[$fdata['element_id']]['sync_prod']=$fdata['element_sync_prod'];
                        $properties[$fdata['element_id']]['element_view']=$fdata['element_view'];
                        //$properties[$fdata['element_id']]['field_type']=$fdata['element_field_type'];
    			$properties[$fdata['element_id']]['qareportuse']=$fdata['element_qareportuse'];
    			$properties[$fdata['element_id']]['remove']=$fdata['remove'];
    			$properties[$fdata['element_id']]['default_answer']=isset($fdata['default_answer'])?$fdata['default_answer']:'';
    			$properties[$fdata['element_id']]['default_unit']=isset($fdata['default_unit'])?$fdata['default_unit']:0;
    		}
    	}               
    	return $properties;
    }
    public function getFromData($id,$type = 0,$order = 'DESC',$formbuilder = 'formbuilder',$cust_id = 0,$form_mode = 'front'){
		if($form_mode == 'copyelement')
			$data = $this->checkFormExitsById($id,$order);
		else 
		   	$data = $this->checkFormExitsByRefId($id,$type,$order,$formbuilder,$cust_id,$form_mode);

    //    echo "<pre>",print_r($data),"</pre>"; die();
    	$properties = $this->dbToJsonFormat($data,$form_mode);
    	return $properties;
    }
    public function deleteFromData($id,$type){ 
    	//FormElementOptions::deleteAll('form_builder_id IN (SELECT id  FROM tbl_form_builder WHERE formref_id='.$id.' and form_type='.$type.')');
    	//$this::deleteAll('formref_id='.$id.' and form_type='.$type.'');
    	$this::updateAll(['remove'=>1],'formref_id='.$id.' and form_type='.$type);
    	if($type == '2'){
			Servicetask::updateAll(['data_hasform'=>0,'data_publish'=>0],'id='.$id);
		}else{
			Servicetask::updateAll(['publish'=>0,'hasform'=>0],'id='.$id);
		}
    	$data = $this::find()->where('formref_id='.$id.' AND form_type='.$type.' AND remove=1')->count();
    	if(!$data){
    		$servicetaskModel = ServiceTask::findOne($id);
    		if($type == 1){
    			$servicetaskModel->hasform=0;
    			$servicetaskModel->publish=0;
    		}
    		if($type == 2){
    			$servicetaskModel->data_hasform=0;
    			$servicetaskModel->data_publish=0;
    		}
    		$servicetaskModel->save(false);
    	}
    	return; 
    }
    public function ProcessFormData($post,$model='')
    {
    	if(isset($model) && $model!=''){
    		unset($post[$model]);
    	}
    	foreach ($post as $key=>$val){
    		if(is_array($post[$key])){
    			$i=1;
    			foreach ($post[$key] as $k=>$v){
    				$v['order']=$i;
    				if($v['type']=="text"){
    					if(isset($v[$k]))
    						$v['text_val']=($v[$k]);
    					else
    						$v['text_val']=($v['text_val']);
    				}
    				$new[$key][$k]=$v;
					$i++;
    			}
    		}else{
    			$new[$key]=$val;
    		}
    	}
    	$post=$new;
    	/*code for sort order*/
    	if(isset($post['sort_order']) && $post['sort_order']!=""){
            $j=1;
            foreach (explode(",",$post['sort_order']) as $ele){
                if(isset($post['properties'][$ele]['order'])){
                    $post['properties'][$ele]['order']=$j;
                    $j++;
                }
            }
    	}
    	return $post;
    }
    public function addFormOptions($data = array(),$form_builder_id,$optionchk_name = '',$existOptions=''){
	//	echo "<pre>";print_r($data);die;
    	$sort_ord=1;
    	//echo "<pre>"; print_r($optionchk_name); exit;
    	$isOptionExist = FormElementOptions::find()->where(['form_builder_id' => $form_builder_id,'remove'=> '0'])->count();
    	if($isOptionExist > 0){
            //For Option exist
			$optionchk_explode = explode(',',$optionchk_name);
			foreach ($data as $key => $option){
				$option=HtmlPurifier::process(htmlentities($option));
				
				$existElement = FormElementOptions::find()->where(['form_builder_id' => $form_builder_id, 'element_option'=>$option,'remove'=>'0'])->one();
				if(!empty($existElement)){
					$existElement->sort_order=$sort_ord;
					if(isset($optionchk_name) && $optionchk_name!=''){
						// &&  in_array($option,$optionchk_explode)){
						foreach($optionchk_explode as $optexp){	
							if(html_entity_decode($option)==html_entity_decode($optexp)){	
								$existElement->is_default=1;
								break;
							}
						}
					}
					$existElement->save();
				} else {
					$FormElementOptions = new FormElementOptions();
					unset($FormElementOptions->id);
					$FormElementOptions->form_builder_id=$form_builder_id;
					$FormElementOptions->element_option=$option;
					$FormElementOptions->is_default=0; 
					if(isset($optionchk_name) && $optionchk_name!=''){// &&  in_array($option,$optionchk_explode)){
						foreach($optionchk_explode as $optexp){	
							if(html_entity_decode($option)==html_entity_decode($optexp)){
								$FormElementOptions->is_default=1;
								break;
							}
						}
					}
					$FormElementOptions->sort_order=$sort_ord;
					$FormElementOptions->isNewRecord = true;
					$FormElementOptions->save();
				}
				$sort_ord++;
			}
		} else {
                    //for  Add new element option
                    $optionchk_explode = explode(',',$optionchk_name);
                    foreach ($data as $option){
						$option=HtmlPurifier::process(htmlentities($option));
                        $FormElementOptions = new FormElementOptions();
                        unset($FormElementOptions->id);
                        $FormElementOptions->form_builder_id=$form_builder_id;
                        $FormElementOptions->element_option=$option;
                        $FormElementOptions->is_default=0; 
                        if(isset($optionchk_name) && $optionchk_name!=''){// &&  in_array($option,$optionchk_explode)){
							foreach($optionchk_explode as $optexp){	
								if(html_entity_decode($option)==html_entity_decode($optexp)){
									$FormElementOptions->is_default=1;
									break;
								}
							}
                        }
                        $FormElementOptions->sort_order=$sort_ord;
                        $FormElementOptions->isNewRecord = true;                        
                        $FormElementOptions->save();
                        $sort_ord++;
                    }

		}
    	return;
    }
    public function saveFormData($form_data,$last_id,$type,$form_mode = 'front'){
   //     echo "<pre>",print_r($form_data); die();
    	$sync_prod = Yii::$app->params['sync_prod'];
        $data = $this->checkFormExitsExcludeRemovedByRefId($last_id,$type);
    	if(!empty($form_data['properties'])) {
    		$data = $this->checkFormExitsByRefId($last_id,$type,'DESC','formbuilder',0,'getOnlyActiveElements');
    		//echo "<pre>$last_id - ",print_r($data),print_r($form_data),"</pre>";die;
    		if(!empty($data)){ //edit from builder
    			$update_ele = array();
    			$add_ele    = array();
    			$delete_ele = array();
    			$properties = $this->dbToJsonFormat($data,$form_mode);
    			$update_ele = array_intersect(array_keys($properties), array_keys($form_data['properties']));
    			/*if(count(array_keys($properties)) > count(array_keys($form_data['properties']))){
    				$delete_ele = array_diff(array_keys($properties),array_keys($form_data['properties']));
    			}else{*/
    				$add_ele = array_diff(array_keys($form_data['properties']),array_keys($properties));
					$delete_ele = array_diff(array_keys($properties),array_keys($form_data['properties']));
    			/*}*/
				
    			
				//echo "<pre>",print_r($add_ele),print_r($delete_ele),print_r(array_keys($properties)),print_r(array_keys($form_data['properties'])),"</pre>";die;
				//echo "<pre>"; print_r($form_data['properties']);die;
                foreach ($form_data['properties'] as $ele_id=>$ele_data){                                                        
    				if(in_array($ele_id,$update_ele)){ //Update Records in update mode
						if($type == 3) {
							$ele_data['element_view']=0;
						}
    					$attr=array(
    						'formref_id'=>$last_id,
    						'form_type'=>$type,
    						'element_id'=>$ele_id,
    						'element_order'=>$ele_data['order'],
    						'element_text_value'=>HtmlPurifier::process($ele_data['text_val']),
    						'element_required'=>$ele_data['required'],
    						'element_no_load_prev'=>$ele_data['no_load_prev'],
    						'element_qareportuse'=>$ele_data['qareportuse'],
    						'element_label'=>HtmlPurifier::process($ele_data['label']),
                            'element_view'=>$ele_data['element_view'],
    						'element_description'=>(isset($ele_data['description'])?HtmlPurifier::process($ele_data['description']):NULL),
    						'element_sync_prod'=>(isset($ele_data['sync_prod'])?array_search($ele_data['sync_prod'],$sync_prod):0),
    						// 'element_field_type'=> ($ele_data['field_type']=='number' || $ele_data['field_type']==1)?1:0,			
    						'default_answer'=> isset($ele_data['default_answer'])?HtmlPurifier::process($ele_data['default_answer']):'',			
    						'default_unit'=> isset($ele_data['default_unit'])?$ele_data['default_unit']:0,			
    					);
    					
    					
    					if(isset($properties[$ele_id]['form_builder_id']) && $properties[$ele_id]['form_builder_id']!=0) {
    						$form_builder_last_id = $properties[$ele_id]['form_builder_id'];
    						$this::updateAll($attr, 'id = '.$form_builder_last_id);
							if(in_array($ele_data['type'],array('dropdown','checkbox','radio'))) {
    							if(isset($ele_data['values']) && $ele_data['values']!='') {
		    						/* FormElementOptions::deleteAll('form_builder_id = '.$form_builder_last_id); */
		    						$element_options=explode(";",$ele_data['values']);
		    						$tocheck = str_replace(';',"','",$ele_data['values']);
		    						FormElementOptions::updateAll(['remove'=>1],"element_option NOT IN ('{$tocheck}') AND form_builder_id={$form_builder_last_id}");
                                    $this->addFormOptions($element_options,$form_builder_last_id,(isset($ele_data['optionchk'])?$ele_data['optionchk']:''));
    							}
	    					}
    					}
    				}
    				if(in_array($ele_id,$add_ele)) { //Add New Records in update mode
                     	unset($this->id);
    					$this->formref_id = $last_id;
    					$this->form_type = $type;
    					$this->element_id = $ele_id;
    					$this->element_type = $ele_data['type'];
    					$this->element_order = $ele_data['order'];
    					$this->element_text_value = HtmlPurifier::process($ele_data['text_val']);
    					$this->element_required = $ele_data['required'];
    					$this->element_no_load_prev = $ele_data['no_load_prev'];
    				//	$this->element_optionchk = $ele_data['optionchk'];
    					$this->element_qareportuse = $ele_data['qareportuse'];
    					$this->element_label = HtmlPurifier::process($ele_data['label']);
                        $this->element_view= $ele_data['element_view'];
    					$this->element_description = (isset($ele_data['description'])?HtmlPurifier::process($ele_data['description']):NULL);
    					$this->element_sync_prod = isset($ele_data['sync_prod']) && $ele_data['sync_prod']!='' && array_search($ele_data['sync_prod'],$sync_prod)!=''?array_search($ele_data['sync_prod'],$sync_prod):0;
    					/*$this->element_field_type = 0;
    					if(isset($ele_data['field_type'])){
    						$this->element_field_type = ($ele_data['field_type']=='number' || $ele_data['field_type']==1)?1:0;
    					}*/
    					$this->default_answer = isset($ele_data['default_answer'])?HtmlPurifier::process($ele_data['default_answer']):'';
    					$this->default_unit = isset($ele_data['default_unit'])?$ele_data['default_unit']:0;
    					$this->isNewRecord = true;
                              //          echo '<pre>';
                              //          print_r($this);die;
						$this->save();
    					/*if(!$this->save()){
							echo "<pre>",print_r($this->getErrors()),"</pre>";
						}*/
//                                        if($ele_data['label'] == 'txtremovbox' && $ele_data['type'] == 'textbox'){
//                                            echo '<pre>';print_r($ele_data);print_r($update_ele);echo $ele_id;print_r($add_ele);die;
//                                        }
                                       // echo '<pre>',print_r($ele_data);die;
    					/*make elements options entry into db*/
    					if(in_array($ele_data['type'],array('dropdown','checkbox','radio'))) {
    						if(isset($ele_data['values']) && $ele_data['values']!='') {
	    						$form_builder_last_id = Yii::$app->db->getLastInsertId();
	    						$element_options=explode(";",$ele_data['values']);
	    						$this->addFormOptions($element_options,$form_builder_last_id,(isset($ele_data['optionchk'])?$ele_data['optionchk']:''));
    						}
    					}
    				}
    			}
    			
    			//die();
    			if(!empty($delete_ele)) { //Delete Removed Element from DB
    				foreach ($delete_ele as $delete_element_id) {
						$form_builder_delete_id = $properties[$delete_element_id]['form_builder_id'];
						if(isset($form_builder_delete_id) && $form_builder_delete_id!=0) {
							$this::updateAll(['remove'=>1,'element_required'=>1],'id = '.$form_builder_delete_id);
    						if(in_array($properties[$delete_element_id]['type'],array('dropdown','checkbox','radio'))) {
								//$this::deleteAll('id ='.$form_builder_last_id);
								//FormElementOptions::deleteAll('form_builder_id = '.$form_builder_last_id);
								FormElementOptions::updateAll(['remove'=>1],'form_builder_id = '.$form_builder_delete_id);
							}
						}
    				}
    			}
    		} else {
				//Add New Records in Add Mode                        
    			foreach ($form_data['properties'] as $ele_id=>$ele_data) {
	    			
	    			unset($this->id);
	    			
	    			$this->formref_id = $last_id;
			    	$this->form_type = $type;
			    	$this->element_id = $ele_id;
			    	$this->element_type = $ele_data['type'];
			    	$this->element_order = $ele_data['order'];
			    	$this->element_text_value = HtmlPurifier::process($ele_data['text_val']);
			    	$this->element_required = $ele_data['required'];
			    	$this->element_no_load_prev = $ele_data['no_load_prev'];
			    //	$this->element_optionchk = $ele_data['optionchk'];
			    	$this->element_qareportuse = $ele_data['qareportuse'];
			    	$this->element_label = HtmlPurifier::process($ele_data['label']);
                    $this->element_view = $ele_data['element_view'];
			    	$this->element_description = (isset($ele_data['description'])?HtmlPurifier::process($ele_data['description']):NULL);
			    	$this->element_sync_prod = isset($ele_data['sync_prod']) && $ele_data['sync_prod']!='' && array_search($ele_data['sync_prod'],$sync_prod)!=''?array_search($ele_data['sync_prod'],$sync_prod):0;
			    	/*$this->element_field_type = 0;
			    	if(isset($ele_data['field_type'])){
			    		$this->element_field_type = ($ele_data['field_type']=='number' || $ele_data['field_type']==1)?1:0;
			    	} */
			    	$this->default_answer = (isset($ele_data['default_answer'])?HtmlPurifier::process($ele_data['default_answer']):NULL);
			    	$this->default_unit = isset($ele_data['default_unit'])?$ele_data['default_unit']:0;
			    	$this->remove = 0;
			    	$this->isNewRecord = true;
			    	$this->save();
			    	
			    	//echo Yii::$app->db->getLastInsertId()."sss";die;
			    	/*if(!$this->save()){
						echo "<pre>",print_r($this->getErrors()),"</pre>";
					}*/
			    	/*make elements options entry into db*/
			    	if(in_array($ele_data['type'],array('dropdown','checkbox','radio'))) {
			    		if(isset($ele_data['values']) && $ele_data['values']!='') {
				    		$form_builder_last_id = Yii::$app->db->getLastInsertId();
				    		$element_options=explode(";",$ele_data['values']);
				    		$this->addFormOptions($element_options,$form_builder_last_id,(isset($ele_data['optionchk'])?$ele_data['optionchk']:''));
			    		}
			    	}
			    }
    		}
        }else{
            if(!empty($data)){                
                foreach($data as $single){
                    $form_builder_id = $single['id'];
                    $this::updateAll(['remove'=>1,'element_required'=>1],'id ='.$form_builder_id);
                    FormElementOptions::updateAll(['remove'=>1],'form_builder_id = '.$form_builder_id);                    
                }
            }            
        }
    	return;
    }
    public function checkFormExitsByRefId($id,$type,$order = 'DESC',$flag='formbuilder',$cust_id = 0,$form_mode = 'front'){
        $relation_name  = 'formElementOptions';
        if(Yii::$app->db->driverName=='mysql')
            $sqlstart       = "$relation_name.remove = 1 AND $relation_name.id IN (";
        else
            $sqlstart       = "$relation_name.remove = 1 AND  cast($relation_name.id as nvarchar(255)) IN (";
        $sqlinput       = "($relation_name.remove=0)";
        $sqlend         = ")";
        
        if($cust_id != '0' && $type == 3){
            $inner_sql = "cust_id = {$cust_id} AND";
        }else if($cust_id != '0' && $type == 1){
            $inner_sql = "task_instruct_id = {$cust_id} AND ";            
        } else if($cust_id != '0' && $type == 2){
            $inner_sql = "tasks_unit_id = {$cust_id} AND ";            
        }else{
            $inner_sql = '';
        }
        if($type == 3) {
			//if (Yii::$app->db->driverName == 'mysql'){
            	$sql1 = "SELECT tbl_form_custodian_values.element_value FROM tbl_form_custodian_values INNER JOIN tbl_form_builder as inner_tbl_form_builder ON inner_tbl_form_builder.id = tbl_form_custodian_values.form_builder_id WHERE {$inner_sql} (element_value IN (SELECT inner_tbl_form_element_options.id FROM tbl_form_element_options as inner_tbl_form_element_options WHERE inner_tbl_form_element_options.form_builder_id IN (SELECT inner_tbl_form_builder.id FROM tbl_form_builder as inner_tbl_form_builder WHERE inner_tbl_form_builder.formref_id = {$id} AND inner_tbl_form_builder.form_type = {$type})) AND tbl_form_custodian_values.form_builder_id IN (SELECT inner_tbl_form_builder.id FROM tbl_form_builder as inner_tbl_form_builder WHERE inner_tbl_form_builder.formref_id = {$id} AND inner_tbl_form_builder.form_type = $type))";            
			/*}else{
				$sql1 = "SELECT tbl_form_custodian_values.element_value FROM tbl_form_custodian_values INNER JOIN tbl_form_builder as inner_tbl_form_builder ON inner_tbl_form_builder.id = tbl_form_custodian_values.form_builder_id WHERE {$inner_sql} (element_value IN (SELECT CAST(inner_tbl_form_element_options.id AS VARCHAR) FROM tbl_form_element_options as inner_tbl_form_element_options WHERE inner_tbl_form_element_options.form_builder_id IN (SELECT inner_tbl_form_builder.id FROM tbl_form_builder as inner_tbl_form_builder WHERE inner_tbl_form_builder.formref_id = {$id} AND inner_tbl_form_builder.form_type = {$type})) AND tbl_form_custodian_values.form_builder_id IN (SELECT inner_tbl_form_builder.id FROM tbl_form_builder as inner_tbl_form_builder WHERE inner_tbl_form_builder.formref_id = {$id} AND inner_tbl_form_builder.form_type = $type))";            
			}*/
        } else if($type == 1) {             
			//if (Yii::$app->db->driverName == 'mysql'){
            	$sql1 = "SELECT tbl_form_instruction_values.element_value FROM tbl_form_instruction_values INNER JOIN tbl_form_builder as inner_tbl_form_builder ON inner_tbl_form_builder.id = tbl_form_instruction_values.form_builder_id WHERE {$inner_sql} (element_value IN (SELECT inner_tbl_form_element_options.id FROM tbl_form_element_options as inner_tbl_form_element_options WHERE inner_tbl_form_element_options.form_builder_id IN (SELECT inner_tbl_form_builder.id FROM tbl_form_builder as inner_tbl_form_builder WHERE inner_tbl_form_builder.formref_id = {$id} AND inner_tbl_form_builder.form_type = {$type})) AND tbl_form_instruction_values.form_builder_id IN (SELECT inner_tbl_form_builder.id FROM tbl_form_builder as inner_tbl_form_builder WHERE inner_tbl_form_builder.formref_id = {$id} AND inner_tbl_form_builder.form_type = $type))";
			/*}else{
				$sql1 = "SELECT tbl_form_instruction_values.element_value FROM tbl_form_instruction_values INNER JOIN tbl_form_builder as inner_tbl_form_builder ON inner_tbl_form_builder.id = tbl_form_instruction_values.form_builder_id WHERE {$inner_sql} (element_value IN (SELECT  CAST(inner_tbl_form_element_options.id AS VARCHAR) FROM tbl_form_element_options as inner_tbl_form_element_options WHERE inner_tbl_form_element_options.form_builder_id IN (SELECT inner_tbl_form_builder.id FROM tbl_form_builder as inner_tbl_form_builder WHERE inner_tbl_form_builder.formref_id = {$id} AND inner_tbl_form_builder.form_type = {$type})) AND tbl_form_instruction_values.form_builder_id IN (SELECT inner_tbl_form_builder.id FROM tbl_form_builder as inner_tbl_form_builder WHERE inner_tbl_form_builder.formref_id = {$id} AND inner_tbl_form_builder.form_type = $type))";
			}*/
        } else if($type == 2) {
			//if (Yii::$app->db->driverName == 'mysql'){
            	$sql1 = "SELECT tbl_tasks_units_data.element_value FROM tbl_tasks_units_data INNER JOIN tbl_form_builder as inner_tbl_form_builder ON inner_tbl_form_builder.id = tbl_tasks_units_data.form_builder_id  WHERE {$inner_sql} (element_value IN (SELECT inner_tbl_form_element_options.id FROM tbl_form_element_options as inner_tbl_form_element_options WHERE inner_tbl_form_element_options.form_builder_id IN (SELECT inner_tbl_form_builder.id FROM tbl_form_builder as inner_tbl_form_builder WHERE inner_tbl_form_builder.formref_id = {$id} AND inner_tbl_form_builder.form_type = {$type})) AND tbl_tasks_units_data.form_builder_id IN (SELECT inner_tbl_form_builder.id FROM tbl_form_builder as inner_tbl_form_builder WHERE inner_tbl_form_builder.formref_id = {$id} AND inner_tbl_form_builder.form_type = $type))";
			/*}else{
				$sql1 = "SELECT tbl_tasks_units_data.element_value FROM tbl_tasks_units_data INNER JOIN tbl_form_builder as inner_tbl_form_builder ON inner_tbl_form_builder.id = tbl_tasks_units_data.form_builder_id  WHERE {$inner_sql} (element_value IN (SELECT CAST(inner_tbl_form_element_options.id AS VARCHAR) FROM tbl_form_element_options as inner_tbl_form_element_options WHERE inner_tbl_form_element_options.form_builder_id IN (SELECT inner_tbl_form_builder.id FROM tbl_form_builder as inner_tbl_form_builder WHERE inner_tbl_form_builder.formref_id = {$id} AND inner_tbl_form_builder.form_type = {$type})) AND tbl_tasks_units_data.form_builder_id IN (SELECT inner_tbl_form_builder.id FROM tbl_form_builder as inner_tbl_form_builder WHERE inner_tbl_form_builder.formref_id = {$id} AND inner_tbl_form_builder.form_type = $type))";
			}*/
        } else {
            $sql1 = '';
        }

        if($flag == 'formvalues' && $inner_sql != ''){
            $sql = $sqlstart.$sql1.$sqlend;
        } 
        
        if($sql != ''){
            // $subsql = "CASE WHEN element_type IN ('checkbox','radio','dropdown') THEN ".$sqlinput.' OR '.$sql." ELSE 1=1 END";
            $subsql = " ((element_type IN ('checkbox','radio','dropdown') AND (".$sqlinput." OR ".$sql.")) OR (element_type NOT IN ('checkbox','radio','dropdown') AND 1=1))";
        } else {
            // $subsql = "CASE WHEN element_type IN ('checkbox','radio','dropdown') THEN ".$sqlinput." ELSE 1=1 END";
            $subsql = " ((element_type IN ('checkbox','radio','dropdown') AND ".$sqlinput.") OR (element_type NOT IN ('checkbox','radio','dropdown') AND 1=1))";
	}	
        
        $isactiverecords = '';
        if($form_mode == 'getOnlyActiveElements')
            $isactiverecords = ' AND tbl_form_builder.remove=0';
        
        $query = "SELECT tbl_form_builder.*, $relation_name.id as option_id, $relation_name.element_option as option_element_option, $relation_name.is_default as option_is_default, $relation_name.form_builder_id as form_builder_id, $relation_name.remove as option_remove FROM tbl_form_builder 
        LEFT JOIN tbl_form_element_options as $relation_name ON tbl_form_builder.id = $relation_name.form_builder_id AND ($subsql)
        WHERE ((formref_id='$id') AND (form_type=$type)) $isactiverecords ORDER BY element_order $order, sort_order";    
	//echo $query;die;
	$result_data = Yii::$app->db->createCommand($query)->queryAll();
        
        $data = array();
        if($result_data){
            foreach($result_data as $key=>$result){
                if(!isset($data[$result['id']]))
                    $data[$result['id']] = $result;
                
                if($result['option_id']!=''){
                    $data[$result['id']][$relation_name][$result['option_id']]['id'] = $result['option_id'];
                    $data[$result['id']][$relation_name][$result['option_id']]['element_option'] = $result['option_element_option'];
                    $data[$result['id']][$relation_name][$result['option_id']]['is_default'] = $result['option_is_default'];
                    $data[$result['id']][$relation_name][$result['option_id']]['form_builder_id'] = $result['option_form_builder_id'];
                    $data[$result['id']][$relation_name][$result['option_id']]['remove'] = $result['option_remove'];
                    $data[$result['id']][$relation_name] = array_values($data[$result['id']][$relation_name]);
                } else {
                    $data[$result['id']][$relation_name] = array();
                }
            }
        }
        $data = array_values($data);
        //echo "<pre>",print_r($data);die;
        return $data;
		
    }
    
    public function checkFormExitsById($id, $order = 'DESC'){

        $relation_name  = 'formElementOptions';
        $subsql = " ((element_type IN ('checkbox','radio','dropdown') AND $relation_name.remove=0) OR (element_type NOT IN ('checkbox','radio','dropdown') AND 1=1))";
        $query = "SELECT tbl_form_builder.*, $relation_name.id as option_id, $relation_name.element_option as option_element_option, $relation_name.is_default as option_is_default, $relation_name.form_builder_id as form_builder_id, $relation_name.remove as option_remove FROM tbl_form_builder LEFT JOIN tbl_form_element_options as $relation_name ON tbl_form_builder.id = $relation_name.form_builder_id AND ($subsql) WHERE tbl_form_builder.id IN ($id) ORDER BY element_order $order, sort_order";        
        $result_data = Yii::$app->db->createCommand($query)->queryAll();
        
        $data = array();
        if($result_data) {
            foreach($result_data as $key=>$result) {
                if(!isset($data[$result['id']]))
                    $data[$result['id']] = $result;
                
                if($result['option_id']!='') {
                    $data[$result['id']][$relation_name][$result['option_id']]['id'] = $result['option_id'];
                    $data[$result['id']][$relation_name][$result['option_id']]['element_option'] = $result['option_element_option'];
                    $data[$result['id']][$relation_name][$result['option_id']]['is_default'] = $result['option_is_default'];
                    $data[$result['id']][$relation_name][$result['option_id']]['form_builder_id'] = $result['option_form_builder_id'];
                    $data[$result['id']][$relation_name][$result['option_id']]['remove'] = $result['option_remove'];
                    $data[$result['id']][$relation_name] = array_values($data[$result['id']][$relation_name]);
                } else {
                    $data[$result['id']][$relation_name] = array();
                }
            }
        }
        $data = array_values($data);
        return $data;
    }
    
    public function checkFormExitsExcludeRemovedByRefId($id,$type,$order = 'DESC'){
    	$data = $this::find()->joinWith([
    	'formElementOptions' => function (\yii\db\ActiveQuery $query) {
        	$query->orderBy(['sort_order'=>SORT_ASC]);
        	$query->select(['id','element_option','is_default','form_builder_id']);
        	//$query->where(['tbl_form_element_options.remove'=>0]);
    	}
		])->where(['formref_id'=>$id,'form_type'=>$type,'tbl_form_builder.remove'=>0])->orderBy('element_order '.$order)->asArray()->all();
		return $data;
    }
    public function saveCustInterviewFrom($post_data,$cust_id,$form_id){
    	$model=array();
		//echo "<pre>",print_r($post_data),"</pre>";die;
		$custFrom = $this->getFromData($form_id,3,'DESC','formbuilder',$cust_id,'system');
    	if(!empty($post_data['properties'])){
    		foreach ($post_data['properties'] as $ele_id=>$ele_data){
    			$value="";
    			if(isset($post_data[$ele_id])){
    				if($ele_data['type'] == 'dropdown' || $ele_data['type'] == 'radio' || $ele_data['type'] == 'checkbox' ){
    					/*$values = explode(";",$custFrom[$ele_id]['values']);
    					$values_option_ids =explode(";",$custFrom[$ele_id]['values_option_ids']);
    					if($custFrom[$ele_id]['type'] == 'dropdown' && !empty($values)){
    						$values = array_combine(range(1, count($values)), $values);
    						if(!empty($values_option_ids)){
    							$values_option_ids = array_combine(range(1, count($values_option_ids)), $values_option_ids);
    						}
    					}*/
    					if (is_array($post_data[$ele_id])){
    						foreach ($post_data[$ele_id] as $value){
    							//$value=(isset($values_option_ids[$index])?$values_option_ids[$index]:"");
    							if(isset($value) && $value!="" && $value!=0){
									//echo $value;
									//echo $this->getSelectedElementOption($value);die;
	    							$model[]=array('form_builder_id'=>$custFrom[$ele_id]['form_builder_id'],
	    									'cust_id'=>$cust_id,
	    									'element_value'=>HtmlPurifier::process($value),
											'element_unit'=>0,
											'element_value_origin'=>$this->getSelectedElementOption($value)
	    							);
    							}
    						}
    					}else{
    						//$value=(isset($values_option_ids[$post_data[$ele_id]])?$values_option_ids[$post_data[$ele_id]]:"");
    						$value = $post_data[$ele_id];
    						if(isset($value) && $value!="" && $value!=0){
	    						$model[]=array('form_builder_id'=>$custFrom[$ele_id]['form_builder_id'],
	    								'cust_id'=>$cust_id,
	    								'element_value'=>HtmlPurifier::process($value),
										'element_unit'=>0,
										'element_value_origin'=>$this->getSelectedElementOption($value)
	    						);
    						}
    					}
    				}else{
    					$value=$post_data[$ele_id];
    					if(isset($value) && $value!=""){
	    					$model[]=array('form_builder_id'=>$custFrom[$ele_id]['form_builder_id'],
	    							'cust_id'=>$cust_id,
	    							'element_value'=>HtmlPurifier::process(htmlentities($value)),
									'element_unit'=>((isset($ele_data['unit_id'])&&$ele_data['unit_id']!="")?$ele_data['unit_id']:0),
									'element_value_origin'=>HtmlPurifier::process(htmlentities($value)),
	    					);
    					}
    				}
    			}
    			
    		}
    	}
    	//echo "<pre>",print_r($model),"</pre>";
    	if(!empty($model)){                
    		FormCustodianValues::deleteAll('cust_id='.$cust_id);
    		$columns = (new FormCustodianValues)->attributes();
    		unset($columns[array_search('id',$columns)]);
			//print_r($columns);die;
    		Yii::$app->db->createCommand()->batchInsert(FormCustodianValues::tableName(), $columns, $model)->execute();
    	}
    	return;
    }
    
    
    public function saveInstructionFrom($post_data,$instruction_id,$flag='')
    {
    	$model=array();
    	if(!empty($post_data['properties'])){
    		foreach ($post_data['properties'] as $servicetask => $dataval){
                foreach ($dataval as $ele_id=>$ele_data){
    				$value="";
                    if(isset($post_data[$servicetask][$ele_id])){
						$ref_id = isset($ele_data['formref_id'])?$ele_data['formref_id']:0;
						$frombuilder_id=$this->getIdByElementIdAndReferenceId($ele_id, $ref_id, 0);
						if($ele_data['type'] == 'dropdown' || $ele_data['type'] == 'radio' || $ele_data['type'] == 'checkbox' ){
							if (is_array($post_data[$servicetask][$ele_id])) {
								foreach ($post_data[$servicetask][$ele_id] as $index) {
									$value = $index;
									// $value=(isset($values_option_ids[$index])?$values_option_ids[$index]:"");
									if($flag=='change' && $frombuilder_id != null) {
										$model[]=array('form_builder_id'=>$frombuilder_id,
											'task_instruct_id'=>$instruction_id,
											'element_value'=>HtmlPurifier::process($value),
											'element_unit'=>0,
											'element_value_origin'=>$this->getSelectedElementOption($value)
										);
									}else{
										if(isset($value) && $value!="" && $value!=0 && $frombuilder_id != null){
											$model[]=array('form_builder_id'=>$frombuilder_id,
												'task_instruct_id'=>$instruction_id,
												'element_value'=>HtmlPurifier::process($value),
												'element_unit'=>0,
												'element_value_origin'=>$this->getSelectedElementOption($value)
											);
										}
									}
								}
							}else{
								//$value=(isset($values_option_ids[$post_data[$ele_id]])?$values_option_ids[$post_data[$ele_id]]:"");
								$value = $post_data[$servicetask][$ele_id];
								if($flag=='change' && $frombuilder_id != null){
									$model[]=array('form_builder_id'=>$frombuilder_id,
										'task_instruct_id'=>$instruction_id,
										'element_value'=>HtmlPurifier::process($value),
										'element_unit'=>0,
										'element_value_origin'=>$this->getSelectedElementOption($value)
									);
								}else{
									if(isset($value) && $value!="" && $value!=0 && $frombuilder_id != null){
										$model[]=array('form_builder_id'=>$frombuilder_id,
											'task_instruct_id'=>$instruction_id,
											'element_value'=>HtmlPurifier::process($value),
											'element_unit'=>0,
											'element_value_origin'=>$this->getSelectedElementOption($value)
										);
									}
								}
							}
						}else{
							$value=htmlentities($post_data[$servicetask][$ele_id]);
							if($flag=='change' && $frombuilder_id != null){
								$model[]=array('form_builder_id'=>$frombuilder_id,
									'task_instruct_id'=>$instruction_id,
									'element_value'=>HtmlPurifier::process($value),
									'element_unit'=>(isset($ele_data['unit_id']) && trim($ele_data['unit_id'])!="")?$ele_data['unit_id']:0,
									'element_value_origin'=>HtmlPurifier::process($value)
								);
							}else{
								if(isset($value) && $value!="" && $frombuilder_id != null){
									$model[]=array('form_builder_id'=>$frombuilder_id,
										'task_instruct_id'=>$instruction_id,
										'element_value'=>HtmlPurifier::process($value),
										'element_unit'=>(isset($ele_data['unit_id']) && trim($ele_data['unit_id'])!="")?$ele_data['unit_id']:0,
										'element_value_origin'=>HtmlPurifier::process($value)
									);
								}
							}
						}
					}
				}
    		}
    	}
        //echo "<pre>",print_r($model),"</pre>";
    	if(!empty($model)){
    		FormInstructionValues::deleteAll('task_instruct_id='.$instruction_id);
    		$columns = (new FormInstructionValues)->attributes();
    		unset($columns[array_search('id',$columns)]);
    		Yii::$app->db->createCommand()->batchInsert(FormInstructionValues::tableName(), $columns, $model)->execute();
    	}
    	return;
    }
    
    
    
    
    /**
     * Get Selected Options Value for values table
     * $id = cust_id or servicetask_id
     * $type= 1 = Instrcution Form, 2 = Data Form, 3 =Cust Form
     * return Selected Options Array or empty array 
     * */
    public function getSelectedOption($id,$form_builder_id,$type){
    	$data = array();
    	if(isset($type) && $type==3){
    		$formCustValues=FormCustodianValues::find()->select('element_value')->where(['form_builder_id'=>$form_builder_id,'cust_id'=>$id])->all();
    		if(!empty($formCustValues)){
    			foreach ($formCustValues as $form_cust_values)$data[] =$form_cust_values->element_value;
    		}
    	}
    	
    	if(isset($type) && $type==2){
    		$formCustValues=TasksUnitsData::find()->joinWith(['formElementOptions'])->select('element_value')->where(['tbl_tasks_units_data.form_builder_id'=>$form_builder_id,'modified'=>$id])->all(); // here id will be used as date
    		if(!empty($formCustValues)){
    			foreach ($formCustValues as $form_cust_values)$data[] = $form_cust_values->formElementOptions->element_option;
    		}
    	}
    	
    	if(isset($type) && $type==1){
    		$formCustValues=FormInstructionValues::find()->select('element_value')->where(['form_builder_id'=>$form_builder_id,'task_instruct_id'=>$id])->all();
    		if(!empty($formCustValues)){
    			foreach ($formCustValues as $form_cust_values)$data[] =$form_cust_values->element_value;
    		}
    	}
    	return $data;
    }
    /**
     *  Get Selected Options value for numeric options 
     * */
     public function getSelectedElementOption($id){
		$data= array();
		$form_data_value = FormElementOptions::find()->where(['id'=>$id])->one()->element_option;
		return $form_data_value;
	 
	 }
	 
	 /**
     *  Get Selected Options value for numeric options 
     * */
     public function getDefaultElementOption($id){
		$data= array();
		$form_data_value = ArrayHelper::map(FormElementOptions::find()->select('id')->where(['form_builder_id'=>$id, 'is_default' => 1, 'remove' => 0])->all(),'id','id');
		return $form_data_value;
	 
	 }
	 
    /**
     * Get Selected Options Value text for values table
     * $id = cust_id or servicetask_id
     * $type= 1 = Instrcution Form, 2 = Data Form, 3 =Cust Form
     * return Selected Options Array or empty array
     * */
    public function getSelectedOptionText($id,$form_builder_id,$type){
    	$data = array();
    	if(isset($type) && $type==3){
    		$sql="SELECT element_value FROM tbl_form_custodian_values WHERE (form_builder_id=$form_builder_id) AND (cust_id=$id)";
    		$data=ArrayHelper::map(FormElementOptions::find()->select('element_option')->where('id IN ('.$sql.')')->all(),'element_option','element_option');
    	}
    	if(isset($type) && $type==2){
    		$sql="SELECT element_value FROM tbl_form_instruction_values WHERE (form_builder_id=$form_builder_id) AND (task_instruct_id=$id)";
    		$data=ArrayHelper::map(FormElementOptions::find()->select('element_option')->where('id IN ('.$sql.')')->all(),'element_option','element_option');
    	}
    	return $data;
    }
    
    public function getIdByElementId($ele_id){
    	return $this::find()->where(['element_id'=>$ele_id])->select('id')->one()->id;
    }   
    
    public function getIdByElementIdAndReferenceId($ele_id, $formref_id, $remove = 0){
		return $this::find()->where(['element_id'=>$ele_id, 'formref_id' => $formref_id, 'remove' => $remove])->select('id')->one()->id;
	}
}
