<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use kartik\widgets\Typeahead;
use kartik\widgets\TimePicker;
use kartik\widgets\TypeaheadBasic;
use kartik\widgets\Select2;
use kartik\widgets\DepDrop;
use app\components\IsataskFormFlag;
use yii\web\JsExpression;
//use kartik\widgets\FileInput;
//use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Evidence */
/* @var $form yii\widgets\ActiveForm */

$timings = Yii::$app->params['timing_arr'];
$new_timings = array();
foreach ($timings as $k => $v) {
    $new_timings[$v] = $v;
}
//echo "<pre>";print_r($CC_data);die;
?>

<?php //$form = ActiveForm::begin(['action'=> $model->isNewRecord ?Url::to(['media/create']):Url::to(['media/update-evidence-process']),'id' => $model->formName(),'enableAjaxValidation' => false,'enableClientValidation' => true,'options' => ['enctype'=>'multipart/form-data','onsubmit'=>'return validatemedia();'],]); ?>
<?php $form = ActiveForm::begin(['action'=> $model->isNewRecord ?Url::to(['media/create']):Url::to(['media/update-evidence-process']),'id' => $model->formName(),'enableAjaxValidation' => false,'enableClientValidation' => true,'options' => ['enctype'=>'multipart/form-data']]); ?>
<?= IsataskFormFlag::widget(); // change flag ?>
<fieldset class="one-cols-fieldset">
    <div class="email-confrigration-table sla-bus-hours">
    <input type="hidden" id="evidence-case_id" name="Evidence[case_id]" value="<?php echo $params['case_id'].'|'.$params['client_id'];?>"/>
    <?= $form->field($model, 'barcode',['template' => "<div class='row input-field'><div class='col-md-2'>{label}</div><div class='col-md-8'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textInput(); ?>
    <?= $form->field($model, 'received_from',['template' => "<div class='row input-field'><div class='col-md-2'>{label}</div><div class='col-md-8'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textInput(['value'=>$params['prod_party']]); ?>
    <?= $form->field($model, 'received_date',['template' => "<div class='row input-field'><div class='col-md-2'>{label}</div><div class='col-md-8 calender-group'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textInput(['id'=>'evidence-received_date','maxlength'=>'10','readonly'=>'readonly','value'=>$params['prod_rec_date']]); ?>

    <?= $form->field($model, 'received_time',['template' => "<div class='row input-field'><div class='col-md-2'>{label}</div><div class='col-md-8'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])
    	->widget(TimePicker::classname(), [
    		'readonly' => true,
    		'pluginOptions' => [
    			'minuteStep' => 15,
                        'defaultTime' => 'current',
    			'showMeridian' => true,
    		]
    	]);
    /*->widget(Select2::classname(), [
    'data' => $new_timings,
    'options' => ['prompt' => 'Select Received Time', 'id' => 'evidence-received_time'],
     ]);*/ ?>

    <?= $form->field($model, 'evid_type',['template' => "<div class='row input-field'><div class='col-md-2'>{label}</div><div class='col-md-8'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->widget(Select2::classname(), [
    'data' => $listEvidenceType,
    'options' => ['prompt' => 'Select Media Type', 'id' => 'evidence-evid_type'],
     ]);?>

     <?= $form->field($model, 'cat_id',['template' => "<div class='row input-field'><div class='col-md-2'>{label}</div><div class='col-md-8'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->widget(Select2::classname(), [
    'data' => $listEvidenceCategory,
    'options' => ['prompt' => 'Select Category', 'id' => 'evidence-cat_id'],
     ]);?>
	<?php $model->isNewRecord ? $model->quantity = 1 : $model->quantity = $model->quantity;  ?>
  <?php  if($model->isNewRecord || $model->quantity == ''){
		$model->quantity = 1;
	} ?>
    <?= $form->field($model, 'quantity',['template' => "<div class='row input-field'><div class='col-md-2'>{label}</div><div class='col-md-8'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textInput(); ?>

    <?= $form->field($model, 'evid_desc',['template' => "<div class='row input-field'><div class='col-md-2'>{label}</div><div class='col-md-8'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textArea(['rows' => '6']); ?>

    <?= $form->field($model, 'serial',['template' => "<div class='row input-field'><div class='col-md-2'>{label}</div><div class='col-md-8'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textInput(); ?>
    <?= $form->field($model, 'model',['template' => "<div class='row input-field'><div class='col-md-2'>{label}</div><div class='col-md-8'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textInput(); ?>
    <?= $form->field($model, 'evid_label_desc',['template' => "<div class='row input-field'><div class='col-md-2'>{label}</div><div class='col-md-8'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textArea(['rows' => '6']); ?>
    <?= $form->field($model, 'contents_total_size',['template' => "<div class='row input-field'><div class='col-md-2'>{label}<span class='require-asterisk'>*</span></div><div class='col-md-8'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textInput(['aria-required'=>'true']); ?>

    <?= $form->field($model, 'unit',['template' => "<div class='row input-field'><div class='col-md-2'>{label}<span class='require-asterisk'>*</span></div><div class='col-md-8'>{input}\n{hint}\n{error}</div><div class='col-md-1' id='media-or'>OR</div></div>",'labelOptions'=>['class'=>'form_label']])->widget(Select2::classname(), [
    'data' => $listUnit,
    'options' => ['prompt' => 'Select Unit', 'id' => 'evidence-unit','aria-required'=>'true'],
    ]);?>

    <?= $form->field($model, 'contents_total_size_comp',['template' => "<div class='row input-field'><div class='col-md-2'>{label}<span class='require-asterisk'>*</span></div><div class='col-md-8'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textInput(['aria-required'=>'true']); ?>
    <?= $form->field($model, 'comp_unit',['template' => "<div class='row input-field'><div class='col-md-2'>{label}<span class='require-asterisk'>*</span></div><div class='col-md-8'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->widget(Select2::classname(), [
    'data' => $listUnit,
    'options' => ['prompt' => 'Select Unit', 'id' => 'evidence-comp_unit','aria-required'=>'true'],
    ]);?>
    <?= $form->field($model, 'contents_copied_to',['template' => "<div class='row input-field'><div class='col-md-2'>{label}</div><div class='col-md-8'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textInput(); ?>
    <?= $form->field($model, 'mpw',['template' => "<div class='row input-field'><div class='col-md-2'>{label}</div><div class='col-md-8'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textInput(); ?>
    <?= $form->field($model, 'ftppw',['template' => "<div class='row input-field'><div class='col-md-2'>{label}</div><div class='col-md-8'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textInput(); ?>
    <?= $form->field($model, 'ftpun',['template' => "<div class='row input-field'><div class='col-md-2'>{label}</div><div class='col-md-8'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textInput(); ?>

    <?= $form->field($model, 'enctype',['template' => "<div class='row input-field'><div class='col-md-2'>{label}</div><div class='col-md-8'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->widget(Select2::classname(), [
    'data' => $listEvidenceEncrypt,
    'options' => ['prompt' => 'Select Encryption Type', 'id' => 'evidence-enctype'],
    ]);?>

    <?= $form->field($model, 'encpw',['template' => "<div class='row input-field'><div class='col-md-2'>{label}</div><div class='col-md-8'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textInput(); ?>

	<?= $form->field($model, 'hash',['template' => "<div class='row input-field'><div class='col-md-2'>{label}</div><div class='col-md-8'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textInput(); ?>
    <?= $form->field($model, 'evd_Internal_no',['template' => "<div class='row input-field'><div class='col-md-2'>{label}</div><div class='col-md-8'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textInput(); ?>
    <?= $form->field($model, 'other_evid_num',['template' => "<div class='row input-field'><div class='col-md-2'>{label}</div><div class='col-md-8'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textInput(); ?>

    <div class='row input-field'>
		<div class='col-md-2' id="duplicate_media">Is Duplicate Media ?</div>
		<div class='col-md-1'>
			<?php echo $form->field($model, 'dup_evid',['template' => "<div class='col-md-12'><label for='evidence-dup_evid'/>{input}\n{hint}\n{error}</div>",'labelOptions'=>['class'=>'form_label']])->checkbox(array('label'=>null,'aria-labelledby'=>'duplicate_media')); ?>
		</div>
		<div class='col-md-7'>
			<?php
			echo $form->field($model, 'org_link',['template' => "<div class='row'><div class='col-md-12'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->label('')->widget(Select2::classname(), [
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
            /*->widget(Typeahead::classname(), [
				'options' => ['placeholder' => 'Filter as you type ...'],
				'pluginOptions' => ['highlight'=>true],
				'dataset' => [
					[
						'limit' => 10,
						'remote' => [
							'url' => Url::to(['media/bring-media-list']) . '&term=%QUERY',
							'wildcard' => '%QUERY'
						]
					]
				]
			]);*/
			?>
		</div>
    </div>
    <?= $form->field($model, 'bbates',['template' => "<div class='row input-field'><div class='col-md-2'>{label}</div><div class='col-md-8'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textInput(); ?>
    <?= $form->field($model, 'ebates',['template' => "<div class='row input-field'><div class='col-md-2'>{label}</div><div class='col-md-8'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textInput(); ?>
    <?= $form->field($model, 'm_vol',['template' => "<div class='row input-field'><div class='col-md-2'>{label}</div><div class='col-md-8'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textInput(); ?>
    <?= $form->field($model, 'evid_notes',['template' => "<div class='row input-field'><div class='col-md-2'>{label}</div><div class='col-md-8'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textArea(['rows' => '6']); ?>
    <?= $form->field($model, 'evid_stored_location',['template' => "<div class='row input-field'><div class='col-md-2'>{label}</div><div class='col-md-8'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])
    ->widget(Select2::classname(), [
		'data' => $listEvidenceLoc,
		'options' => ['prompt' => 'Select Stored Location', 'id' => 'evidence-evid_stored_location'],
     ]);?>
    <?= $form->field($model, 'cont',['template' => "<div class='row input-field'><div class='col-md-2'>{label}</div><div class='col-md-8'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textInput(); ?>
    <?= $form->field($model, 'upload_files[]',['template' => "<div class='row input-field'><div class='col-md-2' id='lbl_media_attachments'>Attachments </div><div class='col-md-8'>{input}\n{hint}\n{error}<div id='T71-list'></div></div></div>",'labelOptions'=>['class'=>'form_label']])->fileInput(['multiple' => true,"id"=>"T71",'aria-labelledby'=>'lbl_media_attachments']) ?>
    <!--<div class="form-group field-evidence-cont" >
		<div class="row input-field">
			<div class="col-md-3"><label for="evidence-cont" class="form_label">&nbsp;</label></div>
			<div class="<co></co>l-md-7" id="T7-lists"></div>
	    </div>
	</div>-->
   <?php if (!empty($evid_docs)) { ?>
   <div class="form-group field-evidence-cont">
	<div class="row input-field">
            <div class="col-md-3"><label for="evidence-cont" class="form_label">&nbsp;</label></div>
            <div class="col-md-7">
            <?php
           if (!empty($evid_docs)) {
               foreach ($evid_docs as $filename) {
               ?>
               <div class="MultiFile-label" style="margin-left:7px;">
                   <a href="#Task_attachments_wrap" class="MultiFile-remove" onclick="remove_image('<?php echo $filename->id; ?>', this);">x</a>
                   <span title="File selected: " class="MultiFile-title">
                       <?php echo $filename->fname;?>
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
	<div class="form-group field-evidence-cont">
            <div class="row input-field">
                <div class="col-md-2"><label for="evidence-cont" class="form_label">&nbsp;</label></div>
                <div class="col-md-8">
                   <?= Html::button('Add Contents', ['title' => 'Add Contents','class' =>  'btn btn-primary','onclick'=>'openaddevidcontent();','id'=>'btn_add_content']) ?>
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
                                    <th>Custodian</th>
                                    <th>Data Type</th>
                                    <th>Data Size</th>
                                    <th>Data Path
										<input type="hidden" name="editEvidContentId" id="editEvidContentId" value=""/>
										<input  type="hidden" name="tmp_evid_num_id" id="tmp_evid_num_id" value="0"/>
                                    </th>
                                    <th>Actions</th>

                              <?php if(!empty($evidencecontents_data))
                                    {
                                        foreach($evidencecontents_data as $content){ //echo "<pre>";print_r($content->evidenceContentUnit->unit_name); ?>
                                            <tr id="row_evid_content_<?php echo $content['id']; ?>">
                                                <td class="word-break" align="center">
                                                     <a href="javascript:void(0)" onclick="evidencecontentaction('edit','<?php echo $content->id;?>');" class="icon-fa" title="Edit Content"><em title="Edit Content" class="fa fa-pencil"></em></a>
                                                     <a href="javascript:RemoveHoliday(0)" onclick="evidencecontentaction('delete','<?php echo $content->id;?>');" class="icon-fa" title="Delete Content"><em title="Delete Content" class="fa fa-times"></em></a>
                                                </td>
                                                <td class="word-break">
                                                    <?php echo $content->evidenceCustodians->cust_lname.", ".$content->evidenceCustodians->cust_fname." ".$content->evidenceCustodians->cust_mi; ?>
                                                </td>
                                                <td class="word-break"><?php echo $content->data_type;?></td>
                                                <td class="word-break"><?php echo $content->data_size.' '.$content->evidenceContentUnit->unit_name;?></td>
                                                <td class="word-break"><?php echo $content->data_copied_to;?>
                                                    <?php
														echo "<input type='hidden' name='EvidenceContent[{$content->id}][id]' value='{$content->id}'/>";
														echo "<input type='hidden' name='EvidenceContent[{$content->id}][cust_id]' value='{$content->cust_id}'/>";
														echo "<input type='hidden' name='EvidenceContent[{$content->id}][data_type]' value='{$content->data_type}'/>";
														echo "<input type='hidden' name='EvidenceContent[{$content->id}][data_size]' value='{$content->data_size}'/>";
														echo "<input type='hidden' name='EvidenceContent[{$content->id}][unit]' value='{$content->unit}'/>";
														echo "<input type='hidden' name='EvidenceContent[{$content->id}][data_copied_to]' value='{$content->data_copied_to}'/>";
                                                    ?>
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
</fieldset>
<div class="button-set text-right">
        <input type="hidden" name="Evidence[deleted_img]" id="Evidence_deleted_img" />
        <input type="hidden" id="Evidence_flag" value="" name="Evidence_flag" />
        <?= $form->field($model, 'id')->hiddenInput()->label(false); ?>
        <?= Html::button('Cancel', ['class' =>  'btn btn-primary', 'id' => 'cancel-evidence-production-form','title'=>'Cancel']) //, 'onclick'=>'$("#production_form").show();$("#evidence_form").hide();'?>
        <?php Html::submitButton('Attach Another', ['class' =>'btn btn-primary','title'=>'Attach','name'=>'attach_another','value'=>'attach_another']) ?>
        <?php Html::submitButton('Attach', ['class' =>'btn btn-primary','title'=>'Attach','name'=>'attach','value'=>'attach']) ?>
        <?= Html::button('Attach', ['class' =>'btn btn-primary','title'=>'Attach','name'=>'attach','value'=>'attach','onclick'=>'$("#Evidence_flag").val(1);$("#Evidence").submit();']) ?>
        <?= Html::button('Attach Another', ['class' =>'btn btn-primary','title'=>'Attach Another','name'=>'attach','value'=>'attach','onclick'=>'$("#Evidence_flag").val(2);$("#Evidence").submit();']) ?>
</div>
    <?php ActiveForm::end(); ?>
<script>
$('input').bind("input", function(){
	$('#Evidence #is_change_form').val('1');
	$('#Evidence #is_change_form_main').val('1');
});
$('textarea').bind('input', function(){
	$('#Evidence #is_change_form').val('1');
	$('#Evidence #is_change_form_main').val('1');
});
$('select').on('change', function(e) {
	$('#Evidence #is_change_form').val('1');
	$('#Evidence #is_change_form_main').val('1');
});
$('document').ready(function(){ $('#active_form_name').val('Evidence'); });
$('#cancel-evidence-production-form').click(function(event){
	var chk = checkformstatus(event);
	if(chk == true) {
		$("#production_form").show();
		$("#evidence_form").hide();
	}
});

$(document).ready(function (){
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

$('#evidence-evid_type').on('change',function(){
	var evidence_type = $('#evidence-evid_type').val();
	var quantity = $('#evidence-quantity').val();
	setTotalSizeAndUnit(quantity,evidence_type);
});

function setTotalSizeAndUnit(quantity, evidence_type) {
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
					$('#evidence-unit').val('');
				}
            },
		});
	}

    function validatemedia()
    {

        $('div.help-blocks').remove();
        $('div.has-errors').removeClass('has-errors');
        if($('#evidence-contents_total_size').val() == '' && $('#evidence-unit').val() == '' && $('#evidence-contents_total_size_comp').val() == '' && $('#evidence-comp_unit').val() == ''){
            $('#evidence-contents_total_size').parent().append('<div class="help-blocks"> Total Size cannot be blank.</div>');
            $('#evidence-contents_total_size').parent().parent().parent().addClass('has-errors');
            $('#evidence-unit').parent().append('<div class="help-blocks"> Total Size Units cannot be blank.</div>');
            $('#evidence-unit').parent().parent().parent().addClass('has-errors');
            return false;
        }
        else
        {
             if($('#evidence-contents_total_size').val()!='')
             {
                 if($('#evidence-unit').val() == '')
                 {
                    $('#evidence-unit').parent().append('<div class="help-blocks"> Total Size Units cannot be blank.</div>');
                    $('#evidence-unit').parent().parent().parent().addClass('has-errors');
                    return false;
                 }
             }
             if($('#evidence-contents_total_size').val()=='')
             {
                 if($('#evidence-unit').val() != '')
                 {
                    $('#evidence-contents_total_size').parent().append('<div class="help-blocks"> Total Size cannot be blank.</div>');
                    $('#evidence-contents_total_size').parent().parent().parent().addClass('has-errors');
                    return false;
                 }
             }
             if($('#evidence-contents_total_size_comp').val()!='')
             {
                 if($('#evidence-comp_unit').val() == '')
                 {
                    $('#evidence-comp_unit').parent().append('<div class="help-blocks"> Compressed Size Units cannot be blank.</div>');
                    $('#evidence-comp_unit').parent().parent().parent().addClass('has-errors');
                    return false;
                 }
             }
             if($('#evidence-contents_total_size_comp').val()=='')
             {
                 if($('#evidence-comp_unit').val() != '')
                 {
                    $('#evidence-contents_total_size_comp').parent().append('<div class="help-blocks"> Compressed Size cannot be blank.</div>');
                    $('#evidence-contents_total_size_comp').parent().parent().parent().addClass('has-errors');
                    return false;
                 }
             }
        }
        showLoader();
        return true;
    }
    </script>
<noscript></noscript>
