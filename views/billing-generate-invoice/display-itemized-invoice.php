<?php
// yii
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use app\models\options;
use app\models\User;
use kartik\grid\GridView;
use kartik\widgets\Select2;
?>
<div class="right-main-container" id="media_container">
		<fieldset class="two-cols-fieldset full-cols-table">
			<?php $form = ActiveForm::begin(['id' => 'add-finalized-invoice','action' => '@web/index.php?r=billing-generate-invoice/finalize-invoice']); ?>
                    <input type="hidden" name="display_type" id="display_type" value="Itemized" />
										<input type="hidden" name="finalize_type" id="finalize_type" value="0" />
										<input type="hidden" name="existing_invoice_id" id="existing_invoice_id" value="0" />

				<?= GridView::widget([
					'id'=>'display-generated-invoice',
					'dataProvider' => $clientcaseprovider,
					'layout' => '{items}',
					'columns' => [
						 /*['class' => '\kartik\grid\ExpandRowColumn', 'detail' => function($model){return $this->render('_billing-itemized-invoice',['data' => $model]);},
							'headerOptions'=>['title'=>'Expand/Collapse All','class'=>'first-th','id'=>'display_itemized_invoice_expand','scope'=>'col'], 'contentOptions'=>['title'=>'Expand/Collapse Row','class' => 'first-td','headers'=>'display_itemized_invoice_expand'], 'expandIcon' => '<a href="#" aria-label="expand Raw"><span class="glyphicon glyphicon-plus"></span></a>', 'collapseIcon' => '<a href="#" aria-label="Collapse Raw"><span class="glyphicon glyphicon-minus"></span></a>', 'expandTitle' => 'Expand', 'collapseTitle' => 'Collapse', 'expandAllTitle' => 'Expand All', 'collapseAllTitle' => 'Collapse All', 'mergeHeader'=>false, 'value' => function ($model) { return 1; }],
							[
								'class' => '\kartik\grid\CheckboxColumn',
								'headerOptions'=>['title'=>'Select All/None','class'=>'first-th','id'=>'display_itemized_invoice_checkbox','scope'=>'col'],
								'rowHighlight' => false,
								'mergeHeader'=>false,
								'contentOptions' => ['class' => 'first-td text-center','headers'=>'display_itemized_invoice_checkbox'],
								'checkboxOptions' => ['customInput'=>true,'aria-label'=>'Select Row', 'class'=>'checkall_inner']
							], */
						['class' => '\kartik\grid\ExpandRowColumn', 'width' => '3%', 'detailUrl' => Url::to(['billing-generate-invoice/billing-itemized-invoice']),'extraData'=>['filterdata'=>$data],
						'headerOptions'=>['title'=>'Expand/Collapse All', 'class'=>'first-td','id'=>'display_invoice_expand','scope'=>'col'],'contentOptions'=>['title'=>'Expand/Collapse Row','class' => 'first-td','headers'=>'display_invoice_expand'], 'expandIcon' => '<a href="#" aria-label="expand Raw"><span class="glyphicon glyphicon-plus"></span></a>', 'collapseIcon' => '<a href="#" aria-label="Collapse Raw"><span class="glyphicon glyphicon-minus"></span></a>', 'expandTitle' => 'Expand', 'collapseTitle' => 'Collapse', 'expandAllTitle' => 'Expand All', 'collapseAllTitle' => 'Collapse All', 'mergeHeader'=>false, 'value' => function ($model) { return 1; }],
						['attribute' => 'client_name', 'width'=>'77.8%','label' => 'Itemized Invoices', 'format' => 'raw', 'headerOptions'=>['title'=>'Itemized Invoices', 'class'=>'billing-generate-header','id'=>'display_itemized_invoice_itemized_invoices','scope'=>'col'],'contentOptions'=>['headers'=>'display_itemized_invoice_itemized_invoices'], 'value' => function($model) { return '<a href="javascript:void(0);" title="'.$model['client_name'].' - '.$model['case_name'].'" class="tag-header-black"><strong>'.$model['client_name'].' - '.$model['case_name'].'</strong></a>';}],
						//['attribute' => '', 'format' => 'raw', 'value' => function($model) { return '<strong>'.'$'.number_format($model['total'],2).'</strong>';},'contentOptions'=>['class'=>'consolidated-price','headers'=>'display_itemized_invoice_itemized_price'],'headerOptions'=>['id'=>'display_itemized_invoice_itemized_price']],
					],
					'export'=>false,
					'floatHeader'=>true,
					'pjax'=>true,
                    'showPageSummary'=>false,
					'responsive'=>false,
                                        'responsiveWrap' => false,
                                        'persistResize'=>false,
                                        'resizableColumns'=>false,
                                        'floatOverflowContainer'=>true,
					'floatHeaderOptions' => ['top' => 'auto'],
					'pjaxSettings'=>[
							'options'=>['id'=>'display-generated-invoice-pajax','enablePushState' => false],
							'neverTimeout'=>true,
							'beforeGrid'=>'',
							'afterGrid'=>'',
					],
				]); ?>
			<?php ActiveForm::end(); ?>
        </fieldset>
        <?php $form = ActiveForm::begin(['id' => 'display-generate-invoice-form','action' => '@web/index.php?r=billing-generate-invoice/billing-invoice-management']); ?>
			<div class="generate-invoice">
				<input type="hidden" name="filter_data" id="filter_data" value='<?php echo json_encode($data); ?>' />
			</div>
			<div class="button-set text-right">
				<button onclick="previousinvoicebtn();" title="Previous" class="btn btn-primary" id="previousrequestinvoice" type="button">Previous</button>
				<button onclick="savedinvoicebackbtn();" title="Save Invoice" class="btn btn-primary" id="backrequestinvoiced" type="button">Save Invoice</button>
			</div>

		<?php ActiveForm::end(); ?>
		<div id="finalizeinvoice-dialog" class="bulkreopeninvoices hide">
				<fieldset>
								 <legend class="sr-only">Finalize Invoice</legend>
					<div class="custom-inline-block-width">
						<input type="radio" aria-setsize="2" aria-posinset="1" name="finalizeinvoice" class="bulkreopen" value="newinvoice" checked="checked" id="rdo_newinvoice" /><label for="rdo_newinvoice" >Create New Invoice</label>
						<?php if((new User)->checkAccess(7.13)){?>
						<input type="radio" aria-setsize="2" aria-posinset="2" name="finalizeinvoice" class="bulkreopen" value="existinginvoice" id="rdo_existinginvoice"/><label for="rdo_existinginvoice" >Add to Existing Invoice</label>
						<div id="existinginvoices" style="display:none">
						<?php
						echo Select2::widget([
										'name'=>'selinvoices',
										'options' => ['id'=>'selinvoices','placeholder' => 'Select Invoice', 'title' => 'Select Invoice', 'class' => 'form-control', 'style' => 'width:200px;', 'nolabel'=>true,'aria-label'=>'Select Invoice'],
										'theme' => Select2::THEME_KRAJEE,
										'pluginOptions' => [
											'allowClear' => true
										]
									]);
								?>	
						</div>
						<?php }?>
					</div>
				</fieldset>
			</div>
        <div class="administration-rt-cols pull-right" id="admin_right"></div>
</div>
<script type="text/javascript">
$('#module-url').val('<?=$fullUrl ?>');

/**
 * Select All Outer Checkbox
 */
function checkallinvoice(loop){
	if($('.itemized_outer_'+loop).is(':checked')){
		$('.final_units_'+loop).prop('checked',true);
		$('.final_label_units_'+loop).prop('checked',true);
		$('.final_label_units_'+loop).addClass('checked');
	}
	else {
		$('.final_units_'+loop).prop('checked',false);
		$('.final_label_units_'+loop).prop('checked',false);
		$('.final_label_units_'+loop).removeClass('checked');
	}
}

/**
 * myheader span link
 */
$(".myheader span").click(function () {
    $header = $(this).parent();
    $content = $header.next();
    $content.slideToggle(500, function () {
        $header.text(function () {
			//change text based on condition
			//return $content.is(":visible") ? "Collapse" : "Expand";
        });
    });
});

/**
 * Header span
 */
$('.myheader').on('click',function(){
	if($(this).hasClass('myheader-selected-tab')){
		$(this).removeClass('myheader-selected-tab');
	}else{
		$(this).addClass('myheader-selected-tab');
	}
});

/**
 * Check all inner
 */
$('.checkall_inner').change(function(){
	var key = $(this).val();
	if($('.checkall_inner').is(':checked')){
		$('.innercheckbox_'+key).prop('checked',true);
		$('.innercheckbox_'+key).addClass('checked');
	}else{
		$('.innercheckbox_'+key).prop('checked',false);
		$('.innercheckbox_'+key).removeClass('checked');
	}
});
</script>
<noscript></noscript>
