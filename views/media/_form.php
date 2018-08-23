<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use kartik\widgets\TimePicker;
use kartik\widgets\Typeahead;
use kartik\widgets\TypeaheadBasic;
use kartik\widgets\Select2;
use kartik\widgets\DepDrop;
use app\components\IsataskFormFlag;
use app\models\FormBuilderSystem;
use yii\web\JsExpression;
//echo '<pre>';print_r($model->attributeLabels());die;

////use kartik\widgets\FileInput;
//use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Evidence */
/* @var $form yii\widgets\ActiveForm */

$timings = Yii::$app->params['timing_arr']; 
$new_timings = array();
$modelLabels = $model->attributeLabels();
foreach ($timings as $k => $v) {
    $new_timings[$v] = $v;
}
$media_form = ArrayHelper::map(FormBuilderSystem::find()->select(['sys_field_name','sort_order'])->where(['sys_form'=>'media_form','grid_only'=>0])->orderBy('sort_order')->all(),'sys_field_name','sort_order');
?>
<style type="text/css">
.help-block{
    color: #c52d2e;
}
</style>
<?php //$form = ActiveForm::begin(['action'=> $model->isNewRecord ?Url::to(['media/create']):Url::to(['media/update-evidence-process']),'id' => $model->formName(),'enableAjaxValidation' => false,'enableClientValidation' => true,'options' => ['enctype'=>'multipart/form-data','onsubmit'=>'return validatemedia();'],]); ?>
<?php $form = ActiveForm::begin(['action'=> $model->isNewRecord ?Url::to(['media/create']):Url::to(['media/update-evidence-process']),'id' => $model->formName(),'enableAjaxValidation' => false,'enableClientValidation' => true,'options' => ['enctype'=>'multipart/form-data','onsubmit'=>'return validatemedia();']]); ?>
<?= IsataskFormFlag::widget(); // change flag ?>
<fieldset class="one-cols-fieldset-report">
    <div class="email-confrigration-table sla-bus-hours" id="list">
	<?php 
				/*echo TypeaheadBasic::widget([
					'model' => $model, 
					'attribute' => 'client_id',
					'options' => ['placeholder' => 'Filter as you type ...'],
					'pluginOptions' => ['highlight'=>true],
					'pluginEvents' => [
						"typeahead:initialized " => "function() { console.log('typeahead:initialized'); }",
						"typeahead:selected" => "function() { console.log(this); }",
						"typeahead:autocompleted" => "function() { console.log('typeahead:autocompleted'); }",
					],
					
					'data' => $clientList,
					
				]);
				
				echo $form->field($model, 'case_id',['template' => "<div class='col-md-12'>{input}\n{hint}\n{error}</div>",'labelOptions'=>['class'=>'form_label']])->widget(Typeahead::classname(), [
					'options' => ['placeholder' => 'Filter as you type ...'],
					'pluginOptions' => ['highlight'=>true],
					'dataset' => [
						[
								'limit' => 10,
							 'remote' => [
								'url' => Url::to(['case/load-caseautocomplete-by-client']) . '&client_id=1&term=%QUERY',
								'wildcard' => '%QUERY'
							]
						]
					]
				]);*/
	?>
    <?php       
		/*
		 * Change to special chars Client List
		 */       
		 if(!empty($clientList)){
			 foreach($clientList as $key => $list_single)  {
				 $clientList[$key] = html_entity_decode($list_single);
			}
		}
		?>
		<div class="listing-item" data-order=<?= $media_form['client_id'] ?>>
            <?php if(!$model->isNewRecord){
                    if(isset($client_id) && $client_id!=0){
                      $model->client_id=$client_id;
                    }
             }?>
			<?= 
				$form->field($model, 'client_id', ['template' => "<div class='row input-field'><div class='col-md-2'>{label}</div><div class='col-md-8'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->widget(Select2::classname(), [
					'data' => $clientList,
	        		'options' => ['prompt' => 'Select Client', 'id' => 'evidence-client_id', 'class' => 'form-control ', 'title' => 'Select Client','placeholder'=>'Select Client','nolabel'=>true,'aria-label'=>'Select Client'],
				])->label('Client'); 
			?>
		</div>
            <?php //echo '<pre>',print_r($model);
                   /*echo Select2::widget([
                    'model' => $model,
//                    'name'=> 'Evidence[client_id]',
                    'attribute' => 'client_id',
                    'data' => $clientList,
                    'options' => ['prompt' => 'Select Client', 'id' => 'client_id', 'class' => 'form-control '.$required_client_id, 'title' => 'Select Client','placeholder'=>'Select Client'],
                    'pluginOptions' => [
                      'allowClear' => true
                    ],
                    ]);*/ 
			?>
        <div class="listing-item" data-order=<?= $media_form['client_case_id'] ?>>
        <?php  
		   echo $form->field($model, 'client_case_id',['template' => "<div class='row input-field'><div class='col-md-2'>{label}</div><div class='col-md-8'>{input}\n{hint}\n{error}</div><div class='col-md-2'> 
              <button type='button' id='btn_add_case' class='btn btn-primary' title='Add' onclick='addevidcase();'>Add</button><div style='color: #c52d2e;' id='evidence-client_case_id_btnadd'></div></div></div>",'labelOptions'=>['class'=>'form_label']])->widget(DepDrop::classname(),[
			'type' => 2,
			'name' => 'case_name',
			'options' => ['title' => 'Select Case','id' => 'evidence-client_case_id', 'class' => 'form-control ','nolabel'=>true],
				'pluginOptions' => [
				  //'allowClear' => true,
				  'depends'=>['evidence-client_id'],
				  'prompt' => false,
                                  //'initialize' => true,
				  'placeholder' => 'Select Case',
				  'url' => Url::toRoute(['media/getcasesbyclient'])
				]
        ]); ?>
     <div class="form-group" >    
        <div class='row input-field'>   
            <div class='col-md-2'>Selected Client / Cases</div>
            <div class="col-md-8">
                <table width="100%" border="0" cellspacing="0" cellpadding="0" class="table table-striped">
                    <tbody id="evid_case_list">
                        <tr>
                            <th class="media-client-th"><a href="javascript:void(0);" title="Client" class="tag-header-black">Client</a></th>
                            <th class="media-case-th"><a href="javascript:void(0);" title="Case" class="tag-header-black">Case</a> <input type="hidden" id="evidence-case_id" name="Evidence[case_id]" value="<?php if(!empty($evidence_case_id)){echo implode(",",$evidence_case_id);} ?>"/></th>
                            <th class="third-th text-center"><a href="javascript:void(0);" title="Actions" class="tag-header-black">Actions</a></th>
                        </tr>  
                        <?php 
							if (!empty($CC_data)) { 
								foreach ($CC_data as $cl_key=>$selcase){
									foreach ($selcase['case_ids'] as $key=>$cases){   
							?>
							<tr id="sel_case_<?php echo $key;?>" class="client_case_media_list">
								<td class="media-client-td"><?php echo $selcase['client_name'];?></td>
								<td class="media-case-td"><?php echo $cases;?></td>
                                                                <td class="third-td text-center"><a href="javascript:void(0);" onclick=delete_evidcase("<?php echo $key.'|'.$cl_key;?>","<?php echo $key;?>","<?php echo $model->id ?>") class="icon-fa" title="Delete"><em title="Delete" class="fa fa-close"></em><span class="screenreader">Delete <?=$cases?></span></a></td>
							</tr> 
                        <?php } 
						} 
                    } ?> 
                    </tbody> 
                </table>    
            </div>
        </div> 
    </div> 
    </div>
    <?php //echo $form->field($model, 'client_id',['template' => "<div class='row input-field'><div class='col-md-2'>Select Client</div><div class='col-md-8'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->dropDownList($clientList,['prompt'=>'Select Client']);?>
    <?php //echo $form->field($model, 'case_id',['template' => "<div class='row input-field'><div class='col-md-2'>Select Case</div><div class='col-md-8' id='case_by_client'>{input}\n{hint}\n{error}</div></div>",'l<strong></strong>abelOptions'=>['class'=>'form_label']])->dropDownList($case_arr,['prompt'=>'Select Case']);?>
    <div class="listing-item" data-order=<?=$media_form['barcode']?>>
    <?= $form->field($model, 'barcode',['template' => "<div class='row input-field'><div class='col-md-2'>{label}</div><div class='col-md-8'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textInput(['maxlength'=>$evidences_length["barcode"]]); ?>        
    </div>
    <div class="listing-item" data-order=<?=$media_form['received_from']?>>
    <?= $form->field($model, 'received_from',['template' => "<div class='row input-field'><div class='col-md-2'>{label}</div><div class='col-md-8'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textInput(['maxlength'=>$evidences_length["received_from"],]); ?>        
   </div>
    <div class="listing-item" data-order=<?=$media_form['received_date']?>>
    <?php // $form->field($model, 'received_date',['template' => "<div class='row input-field'><div class='col-md-2'>{label}</div><div class='col-md-8 calender-group'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textInput(['id'=>'evidence-received_date','maxlength'=>'10','readonly'=>'readonly']); ?>
    <?= $form->field($model, 'received_date',['template' => "<div class='row input-field'><div class='col-md-2'>{label}</div><div class='col-md-8 calender-group'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textInput(['id'=>'evidence-received_date','maxlength'=>'10','readonly'=>'readonly']); ?>        
  	</div>
  	<div class="listing-item" data-order=<?=$media_form['received_time']?>>        
    <?= $form->field($model, 'received_time',['template' => "<div class='row input-field'><div class='col-md-2'>{label}</div><div class='col-md-8'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])
    ->widget(TimePicker::classname(), [
    		'readonly' => true,
    		'pluginOptions' => [
    			'minuteStep' => 15,
    			'showMeridian' => true,
            ],
            'addonOptions' => [
                'asButton' => true,
                'buttonOptions' => ['class' => 'btn','title'=>'Select Time']
            ]
    	]);
	   	/* ->widget(Select2::classname(), [
	    	'data' => $new_timings,
	    	'options' => ['prompt' => 'Select Received Time', 'id' => 'evidence-received_time','aria-label'=>$modelLabels['received_time'],'nolabel'=>true],
	    'pluginOptions' => [
	        'allowClear' => true
	    ],]);*/ ?>   
    </div>
    <div class="listing-item" data-order=<?=$media_form['evid_type']?>>
    <?= $form->field($model, 'evid_type',['template' => "<div class='row input-field'><div class='col-md-2'>{label}</div><div class='col-md-8'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label','id'=>'lbl-evid_type']])->widget(Select2::classname(), [
    'data' => $listEvidenceType,
    'options' => ['prompt' => 'Select Media Type', 'id' => 'evidence-evid_type','aria-label'=>'Media Type, ','nolabel'=>true],
    /*'pluginOptions' => [
        'allowClear' => true
    ],*/]);?>   
    </div>
    <div class="listing-item" data-order=<?=$media_form['cat_id']?>>
    <?= $form->field($model, 'cat_id',['template' => "<div class='row input-field'><div class='col-md-2'>{label}</div><div class='col-md-8'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->widget(Select2::classname(), [
    'data' => $listEvidenceCategory,
    'options' => ['prompt' => 'Select Category', 'id' => 'evidence-cat_id','aria-label'=>'Category,','nolabel'=>true],
   /* 'pluginOptions' => [
        'allowClear' => true
    ],*/]);?>
    </div>
    <div class="listing-item" data-order=<?=$media_form['quantity']?>>           
    <?php  if($model->isNewRecord || $model->quantity == ''){		
		$model->quantity = 1;
	} ?>
    <?= $form->field($model, 'quantity',['template' => "<div class='row input-field'><div class='col-md-2'>{label}</div><div class='col-md-8'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textInput(['maxlength'=>$evidences_length["quantity"]]); ?>           
    </div>
    <div class="listing-item" data-order=<?=$media_form['evid_desc']?>>    
    <?= $form->field($model, 'evid_desc',['template' => "<div class='row input-field'><div class='col-md-2'>{label}</div><div class='col-md-8'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textArea(['rows' => '6']); ?>           
    </div>
    <div class="listing-item" data-order=<?=$media_form['serial']?>>
    <?= $form->field($model, 'serial',['template' => "<div class='row input-field'><div class='col-md-2'>{label}</div><div class='col-md-8'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textInput(['maxlength'=>$evidences_length["serial"]]); ?>           
    </div>
    <div class="listing-item" data-order=<?=$media_form['model']?>>
    <?= $form->field($model, 'model',['template' => "<div class='row input-field'><div class='col-md-2'>{label}</div><div class='col-md-8'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textInput(['maxlength'=>$evidences_length["model"]]); ?>               
    </div>
    <div class="listing-item" data-order=<?=$media_form['evid_label_desc']?>>
    <?= $form->field($model, 'evid_label_desc',['template' => "<div class='row input-field'><div class='col-md-2'>{label}</div><div class='col-md-8'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textArea(['rows' => '6']); ?>           
    </div>
    <div class="listing-item" data-order=<?=$media_form['contents_total_size']?>>
    <?= $form->field($model, 'contents_total_size',['template' => "<div class='row input-field'><div class='col-md-2'>{label}<span class='require-asterisk'>*</span></div><div class='col-md-8'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textInput(['class' => 'form-control numeric-field-qu integer','aria-required'=>'true','maxlength'=>$evidences_length["contents_total_size"]]); ?>               
    </div>
    <div class="listing-item" data-order=<?=$media_form['unit']?>>
		<?= $form->field($model, 'unit',['template' => "<div class='row input-field'><div class='col-md-2'>{label}<span class='require-asterisk'>*</span></div><div class='col-md-8'>{input}\n{hint}\n{error}</div><div class='col-md-1' id='media-or'>OR</div></div>",'labelOptions'=>['class'=>'form_label']])->widget(Select2::classname(), [
			'data' => $listUnit,
			'options' => ['prompt' => 'Select Total Size Units', 'id' => 'evidence-unit','aria-label'=>'Total Size Units,','nolabel'=>true,'aria-required'=>'true'],
			/* 'pluginOptions' => [
				'allowClear' => true
			], */
		]); ?>      
	</div>
    <div class="listing-item" data-order=<?=$media_form['contents_total_size_comp']?>>
    <?= $form->field($model, 'contents_total_size_comp',['template' => "<div class='row input-field'><div class='col-md-2'>{label}<span class='require-asterisk'>*</span></div><div class='col-md-8'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textInput(['class' => 'form-control numeric-field-qu integer','aria-required'=>'true','maxlength'=>$evidences_length["contents_total_size_comp"]]); ?>               
   </div>
    <div class="listing-item" data-order=<?=$media_form['comp_unit']?>>
    <?= $form->field($model, 'comp_unit',['template' => "<div class='row input-field'><div class='col-md-2'>{label}<span class='require-asterisk'>*</span></div><div class='col-md-8'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])
    ->widget(Select2::classname(), [
    'data' => $listUnit,
    'options' => ['prompt' => 'Select Total Compressed Size Units', 'id' => 'evidence-comp_unit','aria-label'=>'Compressed Size Units,','nolabel'=>true,'aria-required'=>'true'],
    /*'pluginOptions' => [
        'allowClear' => true
    ],*/ ]); ?>                   
   </div>
    <div class="listing-item" data-order=<?=$media_form['contents_copied_to']?>>
    <?= $form->field($model, 'contents_copied_to',['template' => "<div class='row input-field'><div class='col-md-2'>{label}</div><div class='col-md-8'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textInput(['maxlength'=>$evidences_length["contents_copied_to"]]); ?>           
    </div>
    <div class="listing-item" data-order=<?=$media_form['mpw']?>>
    <?= $form->field($model, 'mpw',['template' => "<div class='row input-field'><div class='col-md-2'>{label}</div><div class='col-md-8'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textInput(['maxlength'=>$evidences_length["mpw"]]); ?>           
    </div>
    <div class="listing-item" data-order=<?=$media_form['ftppw']?>>
    
    <?= $form->field($model, 'ftppw',['template' => "<div class='row input-field'><div class='col-md-2'>{label}</div><div class='col-md-8'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textInput(['maxlength'=>$evidences_length["ftppw"]]); ?>               
    </div>
    <div class="listing-item"data-order=<?=$media_form['ftpun']?>>
    <?= $form->field($model, 'ftpun',['template' => "<div class='row input-field'><div class='col-md-2'>{label}</div><div class='col-md-8'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textInput(['maxlength'=>$evidences_length["ftpun"]]); ?>               
   </div>
    <div class="listing-item" data-order=<?=$media_form['enctype']?>>
    <?= $form->field($model, 'enctype',['template' => "<div class='row input-field'><div class='col-md-2'>{label}</div><div class='col-md-8'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->widget(Select2::classname(), [
    'data' => $listEvidenceEncrypt,
    'options' => ['prompt' => 'Select Encryption Type', 'id' => 'evidence-enctype','aria-label'=>'Encryption Type,','nolabel'=>true],
    /*'pluginOptions' => [
        'allowClear' => true
    ],*/]);?>                       
   </div>
    <div class="listing-item" data-order=<?=$media_form['encpw']?>>
    <?= $form->field($model, 'encpw',['template' => "<div class='row input-field'><div class='col-md-2'>{label}</div><div class='col-md-8'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textInput(['maxlength'=>$evidences_length["encpw"]]); ?>               
    </div>
    <div class="listing-item" data-order=<?=$media_form['hash']?>>
	<?= $form->field($model, 'hash',['template' => "<div class='row input-field'><div class='col-md-2'>{label}</div><div class='col-md-8'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textInput(['maxlength'=>$evidences_length["hash"]]); ?>               	
	</div>
    <div class="listing-item" data-order=<?=$media_form['evd_Internal_no']?>>
    <?= $form->field($model, 'evd_Internal_no',['template' => "<div class='row input-field'><div class='col-md-2'>{label}</div><div class='col-md-8'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textInput(['maxlength'=>$evidences_length["evd_Internal_no"]]); ?>               
    </div>
    <div class="listing-item" data-order=<?=$media_form['other_evid_num']?>>
    <?= $form->field($model, 'other_evid_num',['template' => "<div class='row input-field'><div class='col-md-2'>{label}</div><div class='col-md-8'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textInput(['maxlength'=>$evidences_length["other_evid_num"]]); ?>               
    </div>
    <div class="listing-item" data-order=<?=$media_form['dup_evid']?>>
    <div class='row input-field'>
        <div class='col-md-2' id="duplicate_media">Duplicate Media?</div>
		<div class='col-md-1'>
			<?= $form->field($model, 'dup_evid',['template' => "<div class='col-md-12'>{input}<label for='evidence-dup_evid'><span class='sr-only'>Duplicate Media?</span></label>\n{hint}\n{error}</div>",'labelOptions'=>['class'=>'form_label']])->checkbox(array('label'=>null,'aria-labelledby'=>'duplicate_media')); ?>
		</div>
		<div class='col-md-7'>
			<?php 
            echo $form->field($model, 'org_link',['template' => "<div class='row'><div class='col-md-12'>{input}<label for='evidence-org_link' class='screenreader'>Evidence link</label>\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->widget(Select2::classname(), [
                'pluginOptions'=>[
					'minimumInputLength' => 1,
					'allowClear'=>false,
   					'ajax' => [
                           'url' => Url::to(['media/bring-media-list']),
							'type' => 'GET',
		  					'dataType' => 'json',
		  					'data' => new JsExpression('function(params) { return {term:params.term}; }'),
                    ]
				],
                'options' => [
                        'prompt' => 'Filter as you type ...', 
                        'nolabel'=>true,
                    ],
                 ]);

			/*echo $form->field($model, 'org_link',['template' => "<div class='row'><div class='col-md-12'>{input}<label for='evidence-org_link' class='screenreader'>Evidence link</label>\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->widget(Typeahead::classname(), [
					'options' => ['placeholder' => 'Filter as you type ...', 'title' => 'Evidence link','nolabel'=>true],
					'pluginOptions' => ['highlight'=>true],
					'dataset' => [
						[
							 'limit' => 10,
							 'remote' => [
								'url' => Url::to(['media/bring-media-list']) . '&term=%QUERY',
                                                                'wildcard' => '%QUERY', 
//                                                     'replace' => new JsExpression('function(url, uriEncodedQuery){ var caseData = $("input[name=\"Evidence[case_id]\"]").val(); return url+"&clientCases="+caseData+"&term="+uriEncodedQuery}'),
							],
//                                                    'replace' => new JsExpression("function(url, uriEncodedQuery) {
//                                                                    val = $('input[name=\"Evidence[case_id]\"]').val();
//                                                                    if (!val) 
//                                                                        return url;
//                                                                    else
//                                                                        return url + '&client_case_id=' + encodeURIComponent(val)
//                                                                }"),
						]
					]
				]);*/
				?>
		</div>
    </div>
    </div>
    <div class="listing-item" data-order=<?=$media_form['bbates']?>>
    <?= $form->field($model, 'bbates',['template' => "<div class='row input-field'><div class='col-md-2'>{label}</div><div class='col-md-8'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textInput(['maxlength'=>$evidences_length["bbates"]]); ?>                   
    </div>
    <div class="listing-item" data-order=<?=$media_form['ebates']?>>
    <?= $form->field($model, 'ebates',['template' => "<div class='row input-field'><div class='col-md-2'>{label}</div><div class='col-md-8'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textInput(['maxlength'=>$evidences_length["ebates"]]); ?>                   
    </div>
    <div class="listing-item" data-order=<?=$media_form['m_vol']?>>
    <?= $form->field($model, 'm_vol',['template' => "<div class='row input-field'><div class='col-md-2'>{label}</div><div class='col-md-8'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textInput(['maxlength'=>$evidences_length["m_vol"]]); ?>               
    </div>
    <div class="listing-item" data-order=<?=$media_form['evid_notes']?>>
    <?= $form->field($model, 'evid_notes',['template' => "<div class='row input-field'><div class='col-md-2'>{label}</div><div class='col-md-8'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textArea(['rows' => '6']); ?>                   
    </div>
    <div class="listing-item" data-order=<?=$media_form['evid_stored_location']?>>
    <?= $form->field($model, 'evid_stored_location',['template' => "<div class='row input-field'><div class='col-md-2'>{label}</div><div class='col-md-8'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->widget(Select2::classname(), [
    
    'data' => $listEvidenceLoc,
    'options' => ['prompt' => 'Select Stored Location Type', 'id' => 'evidence-evid_stored_location','aria-label'=>'Stored Location Type','nolabel'=>true],
    /*'pluginOptions' => [
        'allowClear' => true
    ],*/]);?>        
    </div>
    <div class="listing-item" data-order=<?=$media_form['cont']?>>
    <?= $form->field($model, 'cont',['template' => "<div class='row input-field'><div class='col-md-2'>{label}</div><div class='col-md-8'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textInput(['maxlength'=>$evidences_length["cont"]]); ?>
   </div>
    <div class="listing-item" data-order=<?=$media_form['upload_files']?>>
    <?= $form->field($model, 'upload_files[]',['template' => "<div class='row input-field'><div class='col-md-2'><label class='form_label' for='Attachment'>Attachment</label></div><div class='col-md-8'>{input}\n{hint}\n{error}<span><small>Tip: File size cannot exceed 100 MB.</small></span><div id='T7-list'></div></div></div>",'labelOptions'=>['class'=>'form_label']])->fileInput(['multiple' => true,"id"=>"Attachment","class"=>$required_upload_files]) ?>
    
    
    <!--<div class="form-group field-evidence-cont" >
		<div class="row input-field">
			<div class="col-md-3"><label for="evidence-cont" class="form_label">&nbsp;</label></div>
			<div class="col-md-7" id="T7-lists"></div>
	</div>
	</div>-->
   <?php if (!empty($evid_docs)) { ?>
   <div class="form-group field-evidence-cont" >
	<div class="row input-field">
            <div class="col-md-3"><label for="evidence-cont" class="form_label">&nbsp;</label></div>
            <div class="col-md-7">
            <?php
           if (!empty($evid_docs)) {
               foreach ($evid_docs as $filename) {
               ?>
               <div class="MultiFile-label" style="margin-left:7px;">
                   <a href="javascript:void(0);" class="MultiFile-remove" onclick="remove_image('<?php echo $filename->id; ?>', this,'<?=$filename->fname;?>');">x</a>
                   <span title="File selected: " class="MultiFile-title">
                       <?php echo $filename->fname; ?>
                   </span>
               </div>
               <?php
                   } 
               }
               ?>  
             </div> 
        </div> 
    </div> 
    <?php } ?>   
    </div>
	<div class="listing-item" data-order=<?=500?>>
	<div class="form-group field-evidence-cont">
		<div class="row input-field">
			<div class="col-md-2"><!--<label for="evidence-cont" class="form_label">&nbsp;</label>--> &nbsp;</div>
			<div class="col-md-8">
				<?php $display="display:none";if(!empty($model->case_id)){ $display="display:block"; }?>
				<?= Html::button('Add Contents', ['title' => 'Add Contents','class' =>  'btn btn-primary','onclick'=>'openaddevidcontent();','id'=>'btn_add_content','style'=>$display]) ?>
			</div>
		</div>
	</div>
        <div class="row">
            <div class="form-group">
              <div class="col-md-2">&nbsp;</div>
               <div class="col-md-8">
                   <div class="text_field_bx">
                       <div  class="table-responsive" style="<?php echo $display;?>">
                           <table width="100%" border="0" cellspacing="0" cellpadding="0" class="table table-striped">
                               <tbody id="evid_content_list">
                                    <th class="media-custodian-th"><a href="javascript:void(0);" title="Custodian" class="tag-header-black">Custodian</a></th>
                                    <th class="media-data-type"><a href="javascript:void(0);" title="Data Type" class="tag-header-black">Data Type</a></th>
                                    <th class="media-data-size"><a href="javascript:void(0);" title="Data Size" class="tag-header-black">Data Size</a></th>
                                    <th class="media-data-path"><a href="javascript:void(0);" title="Data Path" class="tag-header-black">Data Path</a>
                                   <input type="hidden" name="editEvidContentId" id="editEvidContentId" value=""/> 
                                   <input  type="hidden" name="tmp_evid_num_id" id="tmp_evid_num_id" value="0"/>
                                   </th>
                                   <th class="text-center third-th"><a href="javascript:void(0);" title="Actions" class="tag-header-black">Actions</a></th>
                               
                              <?php if(!empty($evidencecontents_data))
                                    {
                                        foreach($evidencecontents_data as $content){ //echo "<pre>";print_r($content->evidenceContentUnit->unit_name); ?>
                                            <tr id="row_evid_content_<?php echo $content['id']; ?>">
                                                <td class="media-custodian-td word-break">
                                                    <?php echo $custo_name = $content->evidenceCustodians->cust_lname.", ".$content->evidenceCustodians->cust_fname." ".$content->evidenceCustodians->cust_mi; ?>
                                                </td>
                                                <td class="media-data-type-td word-break"><?php echo $content->datatype->data_type;?></td>
                                                <td class="media-data-size-td word-break"><?php echo $content->data_size.' '.$content->evidenceContentUnit->unit_name;?></td>
                                                <td class="media-data-path-td word-break"><?php echo $content->data_copied_to;?>
												<?php 
													echo "<input type='hidden' name='EvidenceContent[{$content->id}][id]' value='{$content->id}'/>"; 
													echo "<input type='hidden' name='EvidenceContent[{$content->id}][cust_id]' value='{$content->cust_id}'/>"; 
													echo "<input type='hidden' name='EvidenceContent[{$content->id}][data_type]' value='{$content->data_type}'/>"; 
													echo "<input type='hidden' name='EvidenceContent[{$content->id}][data_size]' value='{$content->data_size}'/>"; 
													echo "<input type='hidden' name='EvidenceContent[{$content->id}][unit]' value='{$content->unit}'/>"; 
													echo "<input type='hidden' name='EvidenceContent[{$content->id}][data_copied_to]' value='{$content->data_copied_to}'/>"; 
												?>
                                                </td> 
                                                <td class="text-center third-td word-break">
                                                     <a href="javascript:void(0)" onclick="evidencecontentaction('edit','<?php echo $content->id;?>');" class="icon-fa" title="Edit"><em  title="Edit" class="fa fa-pencil"></em></a>
                                                     <a href="javascript:RemoveHoliday(0)" onclick="evidencecontentaction('delete','<?php echo $content->id;?>','<?=$custo_name?>');" class="icon-fa" title="Delete"><em  title="Delete" class="fa fa-close"></em></a>
                                                </td>
                                            </tr>
                                    <?php } } ?>
                               </tbody>
                           </table>
                       </div>
                   </div>
                </div>
            </div>
        </div>
         <div class="text_field_bx" id="evid_custodian_list" style="display:block;height:1px;"></div>
    </div>     
    <input type="hidden" name="Evidence[deleted_img]" id="Evidence_deleted_img" />
    <div class="listing-item" data-order=<?=501?>>
    <?= $form->field($model, 'id',['template'=>'{input}'])->hiddenInput()->label(false); ?>
    </div>
</fieldset>
<div class="button-set text-right">
    <?= Html::button('Cancel', ['class' =>  'btn btn-primary media-action-btn', 'id' => 'cancel-media-btn', 'title'=>'Cancel']) ?>
	<?= Html::submitButton($model->isNewRecord ? 'Add' : 'Update', ['id' => 'media-btn-save', 'class' =>'btn btn-primary media-action-btn', 'title'=>$model->isNewRecord ? 'Add' : 'Update']) ?>
</div>
    <?php ActiveForm::end(); ?>
<script>

  	
$('#cancel-media-btn').click(function(event){
	location.href = "index.php?r=media/index";
//	$('#Evidence #is_change_form_main').val('1'); 
});	
/*rearrange divs based on system from Start*/
var $people = $('#list'),
	$peopleli = $people.children('.listing-item');

$peopleli.sort(function(a,b){
	var an = parseInt(a.getAttribute('data-order')),
		bn = parseInt(b.getAttribute('data-order'));

	if(an > bn) {
		return 1;
	}
	if(an < bn) {
		return -1;
	}
	return 0;
});

$peopleli.detach().appendTo($people);
/*rearrange divs based on system from End*/
$(document).ready(function () 
{
	$('#Evidence #is_change_form_main').val('0'); // refresh value reset to flag 0
	$('input[name="Evidence[quantity]"]').on("blur", function (e) {
		var value = $(this).val();
		if (value == "" || value == 0) {
			$(this).val(1);
		}
		var quantity = $(this).val();
		var evidtype = $('#evidence-evid_type').val();
		if (quantity > 0 && evidtype != "") {
			setTotalSizeAndUnit(quantity, evidtype);
		}
	});
});
	
$('#evidence-evid_type').on('change',function()
{
	var evidence_type = $('#evidence-evid_type').val();
	var quantity = $('#evidence-quantity').val();
	setTotalSizeAndUnit(quantity,evidence_type);
});
	
function setTotalSizeAndUnit(quantity, evidence_type) 
{
	var url = baseUrl + "media/gettotalsizebyevidencetype/";
	$.ajax({
            type: "post",
            url: url,
            async:true,
            data: { "evidence_id": evidence_type,"quantity": quantity},
			success: function (data) {
				if (data != 0) {
					$('#evidence-contents_total_size').val(parseFloat(data));
					unit = data.replace(/[0-9]./g, '').replace(" ", "");
					$("#evidence-unit > option").each(function () {
						if (this.text == unit) {
							var val = $(this).val();
							$('#evidence-unit').val(val).change(); 
							return false;
						}
					});
				} else {
					$('#evidence-contents_total_size').val('');
					$('#evidence-unit').val('').change();
				}
            },
		});
}


    function validatemedia()
    {
        $("#evidence-client_case_id_btnadd").html('');
        $('div.help-blocks').remove();
        $('#evidence-contents_total_size').parent().find('.help-block').html(null);
        $('#evidence-unit').parent().find('.help-block').html(null);
        $('#evidence-contents_total_size_comp').parent().find('.help-block').html(null);
        $('#evidence-comp_unit').parent().find('.help-block').html(null);
        $('div.has-errors').removeClass('has-errors');
        var is_false=true;
        if($('#evidence-contents_total_size').val() == '' && $('#evidence-unit').val() == '' && $('#evidence-contents_total_size_comp').val() == '' && $('#evidence-comp_unit').val() == ''){
            $('#evidence-contents_total_size').parent().find(".help-block").html('Total Size cannot be blank.');
            $('#evidence-contents_total_size').parent().parent().parent().addClass('has-errors');
            $('#evidence-unit').parent().find(".help-block").html('Total Size Units cannot be blank.');
            $('.field-evidence-unit').addClass('has-errors');
            is_false= false;
        }
        else if($('#evidence-contents_total_size').val()!='')
        {
            if($('#evidence-unit').val() == '')
            {
               $('#evidence-unit').parent().find(".help-block").html('Total Size Units cannot be blank.');
               $('#evidence-unit').parent().parent().parent().addClass('has-errors');
               is_false= false;
            }
        }
        else if($('#evidence-unit').val()!='')
        {
            if($('#evidence-contents_total_size').val() == '')
            {
               $('#evidence-contents_total_size').parent().find(".help-block").html('Total Size cannot be blank.');
               $('#evidence-contents_total_size').parent().parent().parent().addClass('has-errors');
               is_false= false;
            }
        }
        else if($('#evidence-contents_total_size_comp').val() != '')
        {
            if($('#evidence-comp_unit').val() == '')
            {
               $('#evidence-comp_unit').parent().find(".help-block").html('Compressed Size Units cannot be blank.');
               $('#evidence-comp_unit').parent().parent().parent().addClass('has-errors');
               is_false= false;
            }
        }
        else if($('#evidence-comp_unit').val() != '')
        {
            if($('#evidence-contents_total_size_comp').val() == '')
            {
               $('#evidence-contents_total_size_comp').parent().find(".help-block").html('Compressed Size cannot be blank.');
               $('#evidence-contents_total_size_comp').parent().parent().parent().addClass('has-errors');
               is_false= false;
            }
        }
        if($('#evidence-contents_total_size').val()!=""){
            if($('#evidence-unit').val()==''){
            $('#evidence-unit').parent().find(".help-block").html('Total Size Units cannot be blank.');
               $('#evidence-unit').parent().parent().parent().addClass('has-errors');
               is_false= false;
           }
        }
        if($('#evidence-unit').val()!=""){
            if($('#evidence-contents_total_size').val()==''){
             $('#evidence-contents_total_size').parent().find(".help-block").html('Total Size cannot be blank.');
               $('#evidence-contents_total_size').parent().parent().parent().addClass('has-errors');
               is_false= false;
           }
        }

        if($('#evidence-contents_total_size_comp').val()!=""){
            if($('#evidence-comp_unit').val()==''){
            $('#evidence-comp_unit').parent().find(".help-block").html('Compressed Size Units cannot be blank.');
               $('#evidence-comp_unit').parent().parent().parent().addClass('has-errors');
               is_false= false;
           }
        }
        if($('#evidence-comp_unit').val()!=""){
            if($('#evidence-contents_total_size_comp').val()==''){
             $('#evidence-contents_total_size_comp').parent().find(".help-block").html('Compressed Size cannot be blank.');
               $('#evidence-contents_total_size_comp').parent().parent().parent().addClass('has-errors');
               is_false= false;
           }
        }
        if($('.field-evidence-client_id').hasClass('required')){
            if ($('#evid_case_list').find('tr.client_case_media_list').length == 0){
                if($('#evidence-client_id').val()!="" && $('#evidence-client_case_id').val()!=""){
                    $("#evidence-client_case_id_btnadd").html('It is required to click on add button.');
                }
                is_false= false;
            }
        }else{
            if($('#evidence-client_id').val()!="" && $('#evidence-client_case_id').val()!=""){
                if ($('#evid_case_list').find('tr.client_case_media_list').length == 0){
                    $("#evidence-client_case_id_btnadd").html('It is required to click on add button.');
                    is_false= false;
                }
            }
        }
        return is_false;
    }
    
  
	/** Edit change Event **/
	$('input').bind('input', function(){
		$('#Evidence #is_change_form').val('1'); 
		$('#Evidence #is_change_form_main').val('1');
	}); 
	$('textarea').bind('input', function(){ 
		$('#Evidence #is_change_form').val('1'); 
		$('#Evidence #is_change_form_main').val('1'); 
	}); 
	var cnt = 0;
	$('select').on('change', function() {
		if(cnt > 0){
			$('#Evidence #is_change_form').val('1');
			$('#Evidence #is_change_form_main').val('1'); 
		}
		cnt++;
	});
        $('#evidence-contents_total_size').focusout(function(e){ if($(this).val() != '') { $('#select2-evidence-unit-container .select2-selection__sronly_evidence-unit').html('Required');} else { $('#select2-evidence-unit-container .select2-selection__sronly_evidence-unit').html('');} });
        $('#evidence-contents_total_size_comp').focusout(function(e){ if($(this).val() != '') { $('#select2-evidence-comp_unit-container .select2-selection__sronly_evidence-comp_unit').html('Required');} else { $('#select2-evidence-comp_unit-container .select2-selection__sronly_evidence-comp_unit').html('');} });
</script>
<noscript></noscript>
