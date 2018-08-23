<?php
use yii\helpers\Html;
/* @var $this yii\web\View */
$this->title = 'Case Summary';
$this->params['breadcrumbs'][] = $this->title;
?>
<?php if(isset($flag) && $flag=='pdf') {?>
<div style="padding:0px 3px 8px;">
<small><?= (new app\models\Options)->ConvertOneTzToAnotherTz(date('Y-m-d H:i:s'), 'UTC', $_SESSION['usrTZ']);?></small>
<h2>Case Summary <?=Html::encode($caseModel->client->client_name);?> - <?=Html::encode($caseModel->case_name);?></h2>
<span style="float:right">Last Updated on: <?= (new app\models\Options)->ConvertOneTzToAnotherTz($model->modified, 'UTC', $_SESSION['usrTZ']);?> by <?=ucwords($model->modifiedBy->usr_first_name." ".$model->modifiedBy->usr_lastname)?></span>
<table width="100%">
  <tbody>
		<tr>
			<td class="text-left" valign="top" width="20%">Summary: </td>
			<td class="text-left" valign="top" width="80%"><?=nl2br($model->summary)?> </td>
		</tr>
        <tr>
			<td colspan="2">&nbsp;</td>
		</tr>
        <tr>	
            <td class="text-left" valign="top" width="20%">Summary Note: </td>
			<td class="text-left" valign="top" width="80%"><?=nl2br($model->summary_note)?> </td>
		</tr>
  </tbody>
  </table>
  </div>
<?php }else{?>
<div class="right-main-container">			
	<div class="sub-heading"><a href="javascript:void(0);" class="tag-header-black" title="Case Summary">Case Summary</a>
        <?php if (!$model->isNewRecord) {?>
        <span style="float:right">Last Updated on: <?= (new app\models\Options)->ConvertOneTzToAnotherTz($model->modified, 'UTC', $_SESSION['usrTZ']);?> by <?=ucwords($model->modifiedBy->usr_first_name." ".$model->modifiedBy->usr_lastname)?></span>
        <?php }?>

    </div>
	<fieldset class="one-cols-fieldset">
		<div class="administration-form">
            <div class="col-sm-12 panel-body">
            <?php if (!$model->isNewRecord) {?>
                <div class="form-group">
                    <div class="row input-field">
                        <div class="col-md-2">
                            <label class="form_label lineheight-zero" for="taskinstruct-requestor">Summary: </label>
                        </div>
                        <div class="col-md-10 theme-section-outline minHeight50" >
                            <?=nl2br($model->summary)?>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="row input-field">
                        <div class="col-md-2">
                            <label class="form_label lineheight-zero" for="taskinstruct-requestor">Summary Note: </label>
                        </div>
                        <div class="col-md-10 theme-section-outline minHeight200" >
                            <?=nl2br($model->summary_note)?>
                        </div>
                    </div>
                </div>
            <?php }?>  
            </div>
	    </div>
	</fieldset>
	<div class="button-set text-right">
    <?php if ($model->isNewRecord) {?>
        <?php if ((new app\models\User)->checkAccess(4.122)){  ?>
	        <?= Html::button('Add', ['title'=>"Add",'class' => 'btn btn-primary','id'=>'addsummary']) ?>
        <?php }?>
    <?php } else{?>
        <?php if ((new app\models\User)->checkAccess(4.123)){  ?>
            <?= Html::button('PDF', ['title'=>"PDF Export",'class' => 'btn btn-primary','id'=>'exportpdfsummery']) ?>
        <?php }?>    
        <?php if ((new app\models\User)->checkAccess(4.122)){  ?>
	        <?= Html::button('Edit', ['title'=>"Edit",'class' => 'btn btn-primary','id'=>'editsummary']) ?> 
        <?php }?>
    
    <?php }?>
    
	</div>
</div>
<script>
jQuery(document).ready(function(){
    jQuery("#exportpdfsummery").on('click',function(event){
        var case_id = '<?=$case_id?>';
        location.href = baseUrl +'pdf/case-summary&case_id='+case_id;
    });
    jQuery("#addsummary , #editsummary").on('click',function(event){

        var case_id = '<?=$case_id?>';
		if(!$('#caseSummary').length){
			$('body').append("<div id='caseSummary'></div>");
		}
     	var $custodianDialogContainer = $('#caseSummary');
     	 $custodianDialogContainer.dialog({
         	title:"<?php if ($model->isNewRecord) {?>Add<?php }else{?>Edit<?php }?> Case Summary",
             autoOpen: false,
             resizable: false,
             height:456,
             width:"50em",
             modal: true,
             buttons: {
            	 'Cancel': {
                         text: 'Cancel',
                         "title":"Cancel",
                         "class": 'btn btn-primary',
                         'aria-label': "Cancel Case Summary",
                         click:  function (event) {
							 trigger = 'Cancel';
							 $custodianDialogContainer.dialog('close');
					     }
                },
                 "Save":{
                             text: "Save",
                             "title": "Save",
                             "class": 'btn btn-primary',
                             'aria-label': "text: <?php if ($model->isNewRecord) {?>Add<?php }else{?>Edit<?php }?>, Summary",
                             click: function () {
								 trigger = 'Add';
                            	 var form = $("#caseSummary #ClientCaseSummary");
                            	 jQuery.ajax({
                             		url : form.attr('action'),
                             		data:form.serialize(),
                             		type: 'post',
                             		beforeSend:function (data) {showLoader();},
                             		success: function (data) {
                             			hideLoader();
                             			if(data=='OK'){
                             				location.href = baseUrl +'case/case-summary&case_id='+case_id;
                             			}else{
                             				$("#caseSummary").html(data);
                             			}
                             		}
                               	});
                             }
                 }
             },
             close: function(event) {
				$custodianDialogContainer.dialog('destroy').remove();
			 }
         });
         
         /**
          * Case summary form create 
          */
     	 jQuery.ajax({
    		url: baseUrl +'/case/clientcase-summary',
    		data:{client_case_id: case_id},
    		type: 'get',
    		beforeSend:function (data) {showLoader();},
    		success: function (data) {
    			hideLoader();
    			$custodianDialogContainer.html(data);
    			$custodianDialogContainer.dialog("open");
    		}
      	 });
    });
});
</script>
<noscript>
</noscript>
<?php }?>