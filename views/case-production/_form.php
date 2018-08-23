<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\web\JsExpression;
use kartik\widgets\Typeahead;
use kartik\widgets\TypeaheadBasic;
use kartik\widgets\Select2;
use app\components\IsataskFormFlag;
use app\models\FormBuilderSystem;

/* @var $this yii\web\View */
/* @var $model app\models\EvidenceProduction */
/* @var $form yii\widgets\ActiveForm */
if(empty($staff_assigned_arr)){
	$staff_assigned_arr['']='';
}
if(empty($prod_party_arr)){
	$prod_party_arr['']='';
}
$prod_form = ArrayHelper::map(FormBuilderSystem::find()->select(['sys_field_name','sort_order'])->where(['sys_form'=>'production_form','grid_only'=>0])->orderBy('sort_order')->all(),'sys_field_name','sort_order');
$rules = $model->rules();
$required_elements = [];
foreach($rules as $rule){
    if(isset($rule[1]) && $rule[1]=='required'){
        $required_elements = $rule[0];
    }
}
//echo "<pre>",print_r($required_elements);die;
?>
<div class="evidence-production-form">
<?php $form = ActiveForm::begin(['id' => $model->formName(),'enableAjaxValidation' => false,'enableClientValidation' => true,'options' => ['enctype'=>'multipart/form-data','onsubmit'=>'return true'],]); ?>
<?= IsataskFormFlag::widget(); // change flag ?>
<fieldset class="one-cols-fieldset-report">
    <div class="email-confrigration-table sla-bus-hours" id="list">
    <?php if($model->isNewRecord){$model->production_type=1;} ?>
        <div class="listing-item" data-order=<?= $prod_form['production_type'] ?>>
            <?= 
                $form->field($model, 'production_type',['template' => "<div class='row input-field'><div class='col-md-2'>{label}</div><div class='col-md-8'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->widget(Select2::classname(), [
                    'data' => array(1=>'Incoming',2=>'Outgoing'),
                    'options' => ['prompt' => 'Select Production Type','aria-label'=>'Production Type, ','nolabel'=>true],
                    'pluginOptions' => [
                    'allowClear' => false,
                ],]);
            ?>       
	</div>
	<div class="listing-item" data-order=<?= $prod_form['staff_assigned'] ?>>	
        <?= 
            $form->field($model, 'staff_assigned',['template' => "<div class='row input-field'><div class='col-md-2'>{label}</div><div class='col-md-8'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->widget(TypeaheadBasic::classname(), [
                'data' => $staff_assigned_arr,
                'pluginOptions' => ['highlight' => true],
                'options' => ['placeholder' => 'Filter List','maxlength'=>$evidences_production['staff_assigned'],'aria-label'=>'Staff Assigned'],
            ]); 
        ?>
        </div>
    <?php 
		if(!$model->isNewRecord){
		//	$model->prod_date = (Yii::$app->db->driverName == 'sqlsrv')?'00/00/0000':$model->prod_date;
			if($model->prod_date=="")
				$model->prod_date = '00/00/0000';
		}
    ?>
	<div class="listing-item" data-order=<?=$prod_form['prod_date']?>>
        <?= $form->field($model, 'prod_date',['template' => "<div class='row input-field'><div class='col-md-2'>{label}</div><div class='col-md-8 calender-group'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])
        ->textInput(['id'=>'prod_date','readonly'=>'readonly','shownodate' => 1,'aria-required'=>isset($required_elements['prod_date'])?'true':'false']); ?>            
        </div>
	<div class="listing-item" data-order=<?=$prod_form['prod_rec_date']?>>
        <?= $form->field($model, 'prod_rec_date',['template' => isset($required_elements['prod_rec_date'])?"<div class='row input-field'><div class='col-md-2'>{label}<span class='text-danger'>*</span></div><div class='col-md-8 calender-group'>{input}\n{hint}\n{error}</div></div>":"<div class='row input-field'><div class='col-md-2'>{label}</div><div class='col-md-8 calender-group'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label','label'=>'Date Received']])
        ->textInput(['id'=>'prod_rec_date','readonly'=>'readonly','aria-required'=>isset($required_elements['prod_rec_date'])?'true':'false']); ?>            
        </div>
	<div class="listing-item" data-order=<?=$prod_form['prod_party']?>>
        <?php 
        echo $form->field($model, 'prod_party',['template' => "<div class='row input-field'><div class='col-md-2'>{label}<span class='text-danger'>*</span></div><div class='col-md-8 calender-group'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->widget(TypeaheadBasic::classname(), [
            'data' => $prod_party_arr,
            'pluginOptions' => ['highlight' => true],
            'options' => ['placeholder' => 'Filter List','maxlength'=>$evidences_production['prod_party'],'aria-label'=>'Producing Party'],
        ]);
        ?>
    </div>
	<div class="listing-item" data-order=<?=$prod_form['production_desc']?>>
        <?= $form->field($model, 'production_desc',['template' => "<div class='row input-field'><div class='col-md-2'>{label}</div><div class='col-md-8'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textArea(['rows' => '6','maxlength'=>$evidences_production['production_desc']]); ?>           
    </div>
	<div class="listing-item" data-order=<?=$prod_form['prod_copied_to']?>>
        <?= $form->field($model, 'prod_copied_to',['template' => "<div class='row input-field'><div class='col-md-2'><label class='form_label' for='evidenceproduction-prod_copied_to'>Production Copied To UNC Link</label></div><div class='col-md-8'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textInput(['maxlength'=>$evidences_production['prod_copied_to']]); ?>
    </div>
	<div class="listing-item" data-order=<?=$prod_form['cover_let_link']?>>
        <?= $form->field($model, 'cover_let_link',['template' => "<div class='row input-field'><div class='col-md-2'><label class='form_label' for='evidenceproduction-cover_let_link'>Cover Letter UNC Link</label></div><div class='col-md-8'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textInput(['maxlength'=>$evidences_production['cover_let_link']]); ?>
    </div>
	<div class="listing-item" data-order=<?=$prod_form['upload_files']?>>  
        <?= $form->field($model, 'upload_files[]',['template' => "<div class='row input-field'><div class='col-md-2'><label class='form_label' for='evidenceproduction-upload_files' id='lbl_evidenceproduction-upload_files'>Cover Letter Attachment</label></div><div class='col-md-8'>{input}\n{hint}\n{error}<span><small>Tip: File size cannot exceed 100 MB.</small></span><div id='T7-list'></div></div></div>",'labelOptions'=>['class'=>'form_label']])->fileInput(['multiple' => true,'id'=>'T7','aria-labelledby'=>'lbl_evidenceproduction-upload_files']) ?>
        <?php if (!empty($production_docs)) { ?>
            <div class="form-group field-evidence-cont" >
                 <div class="row input-field">
                     <div class="col-md-2"><label for="evidence-cont" class="form_label">&nbsp;</label></div>
                     <div class="col-md-7">
                     <?php
                    if (!empty($production_docs)) {
                        foreach ($production_docs as $filename) {
                        ?>
                        <div class="MultiFile-label" style="margin-left:7px;">
                            <a href="#Task_attachments_wrap" class="MultiFile-remove" onclick="delete_document('<?php echo $filename->id; ?>', this);"><em title="Remove" class="fa fa-close text-danger"></em></a>
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
        <?php // $form->field($model, 'prod_orig',['template' => "<div class='row input-field'><div class='col-md-3'> </div><div class='col-md-7'>{input}{label}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label','for'=>'evidenceproduction-prod_orig']])->checkbox(array('label'=>'')); ?>
        </div>
	<div class="listing-item" data-order=<?=$prod_form['prod_orig']?>>
        <?= $form->field($model, 'prod_orig',['template' => "<div class='input-field row'><div class='col-md-2 col-case-prd'>{input}<label for='evidenceproduction-prod_orig' class='chkbox-global-design'>Production Contains Originals </label>\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->checkbox(['label' => null]); ?>
        </div>
	<div class="listing-item" data-order=<?=$prod_form['prod_return']?>>
        <?= $form->field($model, 'prod_return',['template' => "<div class='input-field row'><div class='col-md-2 col-case-prd'>{input}<label for='evidenceproduction-prod_return' class='chkbox-global-design'>Return Production </label>\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->checkbox(['label' => null]); ?>
        </div>
	<div class="listing-item" data-order=<?=$prod_form['attorney_notes']?>>
        <?= $form->field($model, 'attorney_notes',['template' => "<div class='row input-field'><div class='col-md-2'>{label}</div><div class='col-md-8'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textArea(['rows' => '6']); ?>           
        </div>
	<div class="listing-item" data-order=<?=$prod_form['prod_disclose']?>>
        <?= $form->field($model, 'prod_disclose',['template' => "<div class='row input-field'><div class='col-md-2'><label for='evidenceproduction-prod_disclose' class='form_label'>Produced in Initial Disclosures</label></div><div class='col-md-8'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textInput(['maxlength'=>$evidences_production['prod_disclose']]); ?>
        </div>
	<div class="listing-item" data-order=<?=$prod_form['prod_agencies']?>>
        <?= $form->field($model, 'prod_agencies',['template' => "<div class='row input-field'><div class='col-md-2'><label for='prod_agencies' class='form_label'>Produced to Other Agencies</label></div><div class='col-md-8 calender-group'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label','label'=>'Produced to Other Agencies Date']])->textInput(['id'=>'prod_agencies','maxlength'=>'10','readonly'=>'readonly']); ?>            
        </div>
	<div class="listing-item" data-order=<?=$prod_form['prod_access_req']?>>
        <?= $form->field($model, 'prod_access_req',['template' => "<div class='row input-field'><div class='col-md-2'>{label}</div><div class='col-md-8 calender-group'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label','label'=>'Access Request Date']])->textInput(['id'=>'prod_access_req','maxlength'=>'10','readonly'=>'readonly']); ?>            
       </div>
	<div class="listing-item" data-order=<?=$prod_form['prod_misc1']?>>
        <?= $form->field($model, 'prod_misc1',['template' => "<div class='row input-field'><div class='col-md-2'>{label}</div><div class='col-md-8'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label','label'=>'Production Misc. 1']])->textInput(['maxlength'=>$evidences_production['prod_misc1']]); ?>
       </div>
	<div class="listing-item" data-order=<?=$prod_form['prod_misc2']?>>
        <?= $form->field($model, 'prod_misc2',['template' => "<div class='row input-field'><div class='col-md-2'>{label}</div><div class='col-md-8'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label','label'=>'Production Misc. 2']])->textInput(['maxlength'=>$evidences_production['prod_misc2']]); ?>
        </div>
	<div class="listing-item" data-order=<?=500?>>
        <div class='row input-field custom-inline-block-width'>
            <div class="col-md-2">Attach Media</div>
            <div class='col-md-2'>
                <fieldset>
                <legend class="sr-only">Attach Media</legend>
                <input aria-setsize="2" aria-posinset="1" type="radio" name="attachMedia" id="attachMedia" value="N"  class="ML5" onclick="$('#evidenceproduction-medialist').hide();" /> <label for="attachMedia"> New</label> 
                <input aria-setsize="2" aria-posinset="2" type="radio" name="attachMedia" id="attachMedia1" value="E" checked="checked" class="ML15" onclick="$('#evidenceproduction-medialist').show();" /> <label for="attachMedia1"> Existing</label>  
                </fieldset>
            </div>     
            <div class='col-md-6'>
                <?= $form->field($model, 'medialist',['template' => "<div class='col-md-13'>{input}\n{hint}\n{error}</div>",'labelOptions'=>['class'=>'form_label']])->widget(Select2::classname(), [
					//'data' => $media_list,
					'options' => ['prompt' => 'Select Attach Media'],
					'pluginOptions' => [
						//'allowClear' => true,
							'ajax' => [
									'url' =>  Url::toRoute(['case-production/search-media', 'case_id' => $case_id]),
									'dataType' => 'json',
									'data' => new JsExpression('function(params) { return {q:params.term}; }')
							],
					],]);?>
            </div> 
            <div class='col-md-2'> 
              <?= Html::button('Attach', ['title' => 'Attach','class' =>  'btn btn-primary','id'=>'btn_attach_media']) ?>
            </div>
        </div>  
        </div>
	<div class="listing-item" data-order=<?=501?>>
       <div class="form-group" >    
        <div class='row input-field'>   
            <div class='col-md-2'></div>
            <div class="col-md-8">
                <table width="100%" border="0" cellspacing="0" cellpadding="0" class="table table-striped">
                    <tbody id="media_attached">
                        <tr>
                            <th class="prod_media_th" scope="col"><a href="javascript:void(0);" title="Media#" class="tag-header-black">Media#</a></th>
                            <th class="prod_media_type_th" scope="col"><a href="javascript:void(0);" title="Media Type" class="tag-header-black">Media Type</a></th>
                            <th class="prod_content_th" scope="col"><a href="javascript:void(0);" title="Content Size" class="tag-header-black">Content Size</a></th>
                            <th class="prod_size_th" scope="col"><a href="javascript:void(0);" title="Content Size Comp" class="tag-header-black">Content Size Comp</a></th>
                            <th class="text-center third-th" scope="col"><a href="javascript:void(0);" title="Actions" class="tag-header-black">Actions</a></th>
                        </tr>  
                        <?php $attachedMedia='';
                        if(!empty($data_exising_media)){
                            foreach($data_exising_media as $prdmedia){?>
                                <tr id="atm_<?php echo $prdmedia->id;?>">
                                    <td class="prod_media_td word-break">
                                    <a onclick="go_toMedia('<?php echo $prdmedia->id;?>');" href="javascript:void(0);"><?php echo $prdmedia->id;?></a>
                                    </td>
                                    <td class="prod_media_td word-break"><?php echo $prdmedia->evidencetype->evidence_name?></td>
                                    <td class="prod_media_td word-break"><?php if(isset($prdmedia->contents_total_size) && $prdmedia->contents_total_size!=0) echo $prdmedia->contents_total_size." ".$prdmedia->evidenceunit->unit_name?></td>
                                    <td class="prod_media_td word-break"><?php if(isset($prdmedia->contents_total_size_comp) && $prdmedia->contents_total_size_comp!=0) echo $prdmedia->contents_total_size_comp." ".$prdmedia->evidencecompunit->unit_name?></td>
                                    <td class="text-center third-td word-break">
                                    <a href="javascript:void(0);" onclick="deleteAttachedMedia('<?php echo $prdmedia->id;?>');" class="icon-fa" title="Delete Content"><em  title="Delete Content" class="fa fa-close"></em></a>
                                    </td>    
                                </tr>  
                        <?php if($attachedMedia == ''){$attachedMedia=$prdmedia->id;}else{$attachedMedia=$attachedMedia.','.$prdmedia->id;}
                           }} ?>
                    </tbody>
                </table>
            </div>
        </div>
        </div>
        <div class="form-group"><div class='row input-field'>
            <?= $form->field($model, 'client_case_id')->hiddenInput()->label(false); ?>
            </div></div>  
     </div>  
        </div>
        
    </fieldset>
            <div class="button-set text-right">
                <input type="hidden" id="deleted_medias" value="" name="deleted_medias">
                <input type="hidden" id="production_deleted_docs" value="" name="production_deleted_docs">
                <input type="hidden" id="prod_id" value="<?php echo $model->id;?>">
                <input type="hidden" id="attachedMedia" name="attachedMedia" value="<?php if ($attachedMedia != 0 && $attachedMedia != "") echo $attachedMedia; ?>">
                <?= Html::button('Cancel', ['class' =>  'btn btn-primary', 'id' => 'caseproduction-cancel', 'title'=>'Cancel']) //'onclick'=>'list_caseproduction();', ?>
                <?= Html::submitButton($model->isNewRecord ? 'Add' : 'Update', ['class' => 'btn btn-primary','title'=>$model->isNewRecord ? 'Add' : 'Update']) ?>
            </div>
    <?php ActiveForm::end(); ?>
</div>
<script>
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
/*rearrange divs based on system from END*/
	/** Edit change Event **/
	$('input').bind('input', function() {
		$('#EvidenceProduction #is_change_form').val('1'); 
		$('#EvidenceProduction #is_change_form_main').val('1'); 
	}); 
	$('textarea').bind('input', function() { 
		$('#EvidenceProduction #is_change_form').val('1'); 
		$('#EvidenceProduction #is_change_form_main').val('1'); 
	}); 
	$('select').on('change', function() {
		$('#EvidenceProduction #is_change_form').val('1'); 
		$('#EvidenceProduction #is_change_form_main').val('1'); 
	});
	$('document').ready(function(){ $('#active_form_name').val('EvidenceProduction'); });
	$('#caseproduction-cancel').click(function(event) {
		var case_id= jQuery("#case_id").val();
		location.href=baseUrl+'/case-production/index&case_id='+case_id;
	});
</script>
