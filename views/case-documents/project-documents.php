<?php
use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\Url;
use kartik\grid\datetimepicker;
use yii\web\JsExpression;
use app\models\Options;
/* @var $this yii\web\View */
/* @var $searchModel app\models\search\EvidenceProductionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Evidence Productions';
$this->params['breadcrumbs'][] = $this->title;
//echo "<pre>";print_r($dataProvider);die;

?>
<div class="right-main-container" id="casedocument_container">
	<fieldset class="one-cols-fieldset case-project-fieldset casedocument-fieldset">
       <div class="ui-tabs">
        <ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">
            <li class="ui-state-default ui-corner-top"><a href="<?php echo Url::toRoute(['case-documents/index','case_id'=>$case_id]);?>" title="Case Documents" class="ui-tabs-anchor">Case Documents</a></li>
            <li class=" ui-tabs-active ui-state-active"><a class="ui-tabs-anchor" href="javascript:void(0);" title="Project Documents">Project Documents</a></li>
        </ul>
        <div class="table-responsive" id="projectdoc_container">
        <table class="table table-striped table-hover">
              <tbody>
				  <tr>
					<th class="text-left project_no" title="Project #">Project #</th>
					<th title="Document Name" class="doc_name">Document Name</th>
					<th class="text-left upload_date" title="Uploaded date">Uploaded Date</th>
					<th class="text-left upload_by" title="Uploaded By">Uploaded By</th>
			   </tr>
              <?php if(!empty($dataProvider)){
                  foreach($dataProvider  as $data){ ?>    
                <tr>
                    <td align="left"><?php 
                        if($type == 'IN'){$task_id=$data->taskInstructNotes->task_id;}elseif($type == 'T'){$task_id=$data->tasksUnitsTodos->taskUnit->task_id;}elseif($type == 'C'){$task_id=$data->comments->task_id;}elseif($type == 'TD'){$task_id=$data->tasksUnits->task_id;}else {$task_id=$data->taskInstructServicetask->task_id;} 
                        /* Here change in else part taskInstruct replace with taskinstructservicetask By HNl */
                        echo Html::a($task_id,null,['href'=>Url::toRoute(['case-projects/index','case_id'=>$case_id,'task_id'=>$task_id]),'style'=>'color:#167fac;','title'=>'Project #'.$task_id]);
                    ?></td>
                    <td class="word-break" align="left"><?php echo Html::a($data->fname,null,['href'=>'javascript:void(0);','onclick'=>'downloadattachment('.$data->id.')']); ?></td>
                    <td class="word-break" align="left"><?php echo (new Options)->ConvertOneTzToAnotherTz($data->created, 'UTC', $_SESSION['usrTZ']);?></td>
                    <td class="word-break" align="left"><?php echo $data->user->usr_first_name.' '.$data->user->usr_lastname;;?></td>
                </tr>
              <?php } }else{ ?>
                <tr><td colspan="4">No Document found.</td></tr>
              <?php } ?>
              </tbody>
        </table>      
         </div>
	  </div>
	</fieldset>
<div class="button-set text-right">
    <div class="col-sm-12 pull-right">
            <div class="row">
                <div class="col-sm-4 search-item-set"><input type="hidden" id="projectdoc_type" name="projectdoc_type" value="<?php echo $type;?>"/>
                <label class="sr-only" for="search_projectdoc">Enter Search Term</label>
                <input type="text" id="search_projectdoc" name="seach_doc" class="form-control" placeholder="Enter Search Term" aria-label="Enter Search Term" />
                </div>
				<?= Html::button('Search',['title'=>"Search",'class' => 'btn btn-primary','onclick'=>'search_projectdoc();'])?>
				<?= Html::button('Clear',['title'=>"Search",'class' => 'btn btn-primary','onclick'=>'search_projectdoc("clear");'])?>
           </div>
        </div>      
</div>
    <div id="dialog_probates"></div>
</div>
	
<script>
    function export_log()
    {
        location.href=baseUrl+"pdf/runproductionexcel&case_id=<?php echo $case_id;?>";
    }
    function search_projectdoc(flag)
    {
        showLoader();
        if(flag=='clear')
            var v='';
        else
            var v = $('#search_projectdoc').val();
        
        var case_id= jQuery("#case_id").val();
        var type = $('#projectdoc_type').val();
        $.ajax({
                type: "POST",
                url: baseUrl +'/case-documents/projectdocsearch&case_id='+case_id,
                data: {'term':v,'case_id':case_id,'type':type},
                dataType:'html',
                cache: false,
                success:function(data){
                    $('#projectdoc_container').html(data);
                     if(flag=='clear')
                         $('#search_projectdoc').val('');
                    hideLoader();
                }
                
            });
    }
    </script>
<noscript></noscript>
