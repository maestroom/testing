<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use kartik\grid\GridView;
use kartik\dynagrid\DynaGrid;
use yii\helpers\ArrayHelper;
use kartik\grid\datetimepicker;
use yii\web\JsExpression; 
/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = 'Case Projects';
$this->params['breadcrumbs'][] = $this->title;
$columns=[
['class' => '\kartik\grid\CheckboxColumn','checkboxOptions'=>array('customInput'=>true),'width' => '5%','headerOptions'=>['title'=>'Select All/None'], 'rowHighlight' => false, 'mergeHeader'=>false,'contentOptions'=>['title'=>'Select Row','class' => 'first-td text-center']],
 					['attribute' => 'instruct_version', 'format' => 'raw', 'filterInputOptions' => ['title' => 'Filter By Version #','class'=>'form-control'], 'label'=>'Version', 'headerOptions'=>['title'=>'Version'], 'contentOptions' => ['class' => 'first-td,version-width text-center'], 'value' =>  function ($model) { return Html::a("V".$model->instruct_version, "javascript:viewTeamInstruction($model->id);", array("class" => "dialog","title"=>"V".$model->instruct_version)); }, 'filterType'=>GridView::FILTER_SELECT2,
		  						'filterWidgetOptions'=>[
		  						'pluginOptions'=>[
		  						'ajax' =>[
		  							'url' => Url::toRoute(['case-projects/ajax-change-filter', 'task_id' => $task_id]),
		  							'dataType' => 'json',
		  							'data' => new JsExpression('function(params) { return {q:params.term,field:"version"}; }')
		  				]]],'filterType'=>$filter_type['instruct_version'],'filterWidgetOptions'=>$filterWidgetOption['instruct_version']],
		  			['attribute' => 'created_by', 'format' => 'raw', 'filterInputOptions' => ['title' => 'Filter By Submitted By #'], 'label'=>'Submitted By', 'headerOptions'=>['title'=>'Submitted By'], 'contentOptions' => ['class' => 'text-center submitedby-width'], 'value' =>  function ($model) { return $model->createdUser->usr_first_name.' '.$model->createdUser->usr_lastname; }, 'filterType'=>GridView::FILTER_SELECT2,
		  				'filterWidgetOptions'=>[
		  						'pluginOptions'=>[
		  						'ajax' =>[
		  								'url' => Url::toRoute(['case-projects/ajax-change-filter', 'task_id' => $task_id]),
		  								'dataType' => 'json',
		  								'data' => new JsExpression('function(params) { return {q:params.term,field:"created_by"}; }')
		  				]]],'filterType'=>$filter_type['created_by'],'filterWidgetOptions'=>$filterWidgetOption['created_by']],
		  			['attribute' => 'created', 'format' => 'raw', 'width' => '30%', 'filterInputOptions' => ['title' => 'Filter By Date Submitted#','class'=>'form-control'], 'label'=>'Submitted Date', 'headerOptions'=>['title'=>'Submitted Date','class'=>'global-datetime-width'], 'format' => 'html', 'filter'=>'<input type="text" class="form-control" name="TaskInstructSearch[created]" value="">', 'contentOptions' => ['class' => 'text-center global-datetime-width'], 'value' =>  function ($model) { return $model->getTaskSubmittedDate($model); }, 'filterType'=>GridView::FILTER_DATE,
		  				'filterWidgetOptions'=>[
		  						'pluginOptions'=>[
		  						'ajax' =>[
		  								'url' => Url::toRoute(['case-projects/ajax-change-filter', 'task_id' => $task_id]),
		  										'dataType' => 'json',
		  										'data' => new JsExpression('function(params) { return {q:params.term,field:"created"}; }')
		  				]]],'filterType'=>$filter_type['created'],'filterWidgetOptions'=>$filterWidgetOption['created']],
		  				//['attribute' => 'project_attachment', 'format' => 'raw', 'filterInputOptions' => ['title' => 'Filter By Project Attachment #'], 'label'=>'Project Attachment', 'headerOptions'=>['title'=>'Project Attachment','class'=>'text-center'], 'contentOptions' => ['class' => 'text-center project-attachedment-width'], 'value' =>  function ($model) { return $model->getAttachments($model->id); }, 'filterType'=>GridView::FILTER_SELECT2],
];
?>
<div class="right-main-container" id="media_container">
    <fieldset class="one-cols-fieldset case-project-fieldset">
        <div class="table-responsive">
			<?php $dynagrid = DynaGrid::begin([
    'columns'=>$columns,
    'storage'=>'db',
    'theme'=>'panel-info',
    'gridOptions'=>[
        'id'=>'changed-projects-case-grid',
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'panel'=>false,
		'layout' => '{items}<div class="kv-panel-pager text-right">{summary}{dynagridSort}{dynagrid}{pager}</div>',
		'responsiveWrap' => false,
		'export'=>false,
		'floatHeader'=>true, 
		'floatHeaderOptions' => ['top' => 'auto'],
		'persistResize'=>false,
		'resizableColumns'=>false,
		'pjax'=>true,
			'pjaxSettings'=>[
					'options'=>['id'=>'changed-projects-case-pajax','enablePushState' => false],
					'neverTimeout'=>true,
					'beforeGrid'=>'',
					'afterGrid'=>'',
			],
			'pager' => [
					'options'=>['class'=>'pagination'], // set clas name used in ui list of pagination
					'prevPageLabel' => 'Previous',  // Set the label for the "previous" page button
					'nextPageLabel' => 'Next',  // Set the label for the "next" page button
					'firstPageLabel'=>'First',  // Set the label for the "first" page button
					'lastPageLabel'=>'Last',  // Set the label for the "last" page button
					'nextPageCssClass'=>'next',  // Set CSS class for the "next" page button
					'prevPageCssClass'=>'prev',  // Set CSS class for the "previous" page button
					'firstPageCssClass'=>'first',  // Set CSS class for the "first" page button
					'lastPageCssClass'=>'last',  // Set CSS class for the "last" page button
					'maxButtonCount'=>5,  // Set maximum number of page buttons that can be displayed
			],
			'responsive'=>true,    
			'floatOverflowContainer'=>true,
			
    ],
    'allowThemeSetting'=>false,
    'allowFilterSetting'=>false,
    'allowPageSetting'=>false,
    'enableMultiSort'=>true,
    'toggleButtonGrid'=>['class'=>'btn btn-info btn-sm'],
    'toggleButtonSort'=>['class'=>'btn btn-sm'],
    'options'=>[
    'id'=>'dynagrid-changed-projects-case',
    ] // a unique identifier is important
]);
if (substr($dynagrid->theme, 0, 6) == 'simple') {
    $dynagrid->gridOptions['panel'] = false;
}
DynaGrid::end();
?>
			<?php /*GridView::widget([
            	'id'=>'changed-projects-grid',
            	'dataProvider' => $dataProvider,
				'filterModel' => $searchModel,
				'layout' => '{items}<div class="kv-panel-pager text-right">{summary}{pager}</div>',
				'summary' => "<div class='summary'>Showing <strong>{begin}-{end}</strong> of <strong id='totalItemCount'>{totalCount}</strong> items</div>",
				'columns' => [
					
		  		],
				'export'=>false,
				'floatHeader'=>true,
				'floatHeaderOptions' => ['top' => 'auto'],
	            'responsive'=>false,
				'responsiveWrap' => false,
				'pjax'=>true,
	            'pjaxSettings'=>[
	                'options'=>['id' => 'changed-projects-pajax','enablePushState' => false],
	                'neverTimeout' => true,
	                'beforeGrid' => '',
	                'afterGrid' => '',
	            ],
			]);*/ ?>
		</div>
	</fieldset>
	<div id="bulkreopen-closed-dialog" class="bulkreopentasks hide">
		<fieldset>
			<div class="custom-inline-block-width">
				<input aria-setsize="2" aria-posinset="1" type="radio" name="bulkreopen" class="bulkreopen" value="alltask" checked="checked" id="rdo_bulkreopen"/><label for="rdo_bulkreopen">All <span id="alltask">0</span> Projects in Grid</label>
				<input aria-setsize="2" aria-posinset="2" type="radio" name="bulkreopen" class="bulkreopen" value="selectedtask" id="rdo_selectedreopen"><label for="rdo_selectedreopen">Selected <span id="selectedtask">0</span> Projects in Grid</label>
			</div>
		</fieldset>
	</div>
    <div class="button-set text-right">
           <?= Html::button('Back', ['title'=>"Back",'class' => 'btn btn-primary','onclick'=>'loadChangeProjects('.$case_id.');']) ?>
           <?= Html::button('Change Project',['onclick'=>'editproject('.$case_id.','.$task_id.');','title'=>"Change Project",'class' => 'btn btn-primary']);?> 
     </div>
</div>
<script>
$('#case_id').val('<?php echo $case_id;?>'); 						
	function viewTeamInstruction(id){
		$.ajax({
	        url: baseUrl + "team-projects/view-instructions&taskinstruction_id="+id,
	        cache: false,
	        dataType: 'html',
	        beforeSend:function(){
	            showLoader();
	        },
	        success: function (data) {
	        	hideLoader();
	            if (data != "") {
	            	if(!$( "#instruction_preview" ).length){
		    			$('body').append("<div id='instruction_preview'></div>");
		    		}
		    	   	$( "#instruction_preview" ).html(data);
						    $( "#instruction_preview" ).dialog({
							      autoOpen: true,
							      resizable: false,
							      width: "80em",
							      height:692,
							      modal: true,
							      
								  show: {
								effect: "fade",
								duration: 500
							      },
							      hide: {
								effect: "fade",
								duration: 500
							      },
								create: function(event, ui) { 
								     $('.ui-dialog-titlebar-close').append('<span class="ui-button-icon-primary ui-icon"></span>');
								     $('.ui-dialog-titlebar-close').attr("title", "Close");
                                                                     $('.ui-dialog-titlebar-close').attr("aria-label", "Close");
								},
								  buttons: [
								{
								    text: "Cancel",
								    "title":"Cancel",
								    "class": 'btn btn-primary',
								    click: function() {
									$( this ).dialog( "close" );
								    }
								},
								{
								    text: "PDF",
								    "title":"PDF",
								    "class": 'btn btn-primary',
								    click: function() {
								    	instruction_pdf(id);
								    }
								}
							    ],
							    close: function() {
								$(this).dialog('destroy').remove();
								// Close code here (incidentally, same as Cancel code)
							    }
							}).parent().find('.ui-dialog-title').html("View Instructions").after("<a href='javascript:toggleinstructions();' id='instruction-view' title='Expand/Collapse All' aria-label='Expand Raw'><span id='showhideall' class='glyphicon glyphicon-plus'></span></a>");
				}
	       }
	    });
	}

	function loadChangeProjects(case_id){
		location.href = baseUrl +'case-projects/index&case_id='+case_id;
	}
        
        function editproject(case_id,task_id){
		location.href = baseUrl +'project/edit&case_id='+case_id+'&task_id='+task_id;
	}
/*dyangird setting*/
$('#dynagrid-<?=$dynagrid->gridOptions['id']?>-modal').on('shown.bs.modal', function () { 
	//var self = this,
	$element = $('input[name="<?=$dynagrid->options['id']?>-dynagrid');
	$form = self.$element.closest('form');
	$form.find('select[data-krajee-select2]').each(function () {
		var $el = $(this), settings = window[$el.attr('data-krajee-select2')] || {};
		if ($el.data('select2')) {
			$el.select2('destroy');
		}
		$.when($el.select2(settings)).done(function () {
			initS2Loading($el.attr('id'), '.select2-container--krajee'); // jshint ignore:line
		});
	});
	$form.find('[data-krajee-sortable]').each(function () {
		var $el = $(this);
		if ($el.data('sortable')) {
			$el.sortable('destroy');
		}
		$el.sortable(window[$el.attr('data-krajee-sortable')]);
	});
});
/*dyangird setting*/
</script>
<noscript></noscript>
