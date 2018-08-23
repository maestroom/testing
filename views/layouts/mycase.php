<?php
/* @var $this \yii\web\View */
/* @var $content string */
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;
use app\components\MycaseLeftPanel;
use app\components\IsataskHeader;
use app\components\IsataskFooter;
use app\components\widgets\Alert;
use app\models\ClientCase;
use kartik\widgets\Select2;
$baseUrl=Yii::$app->request->baseUrl;
$js = <<<JS
var baseUrl = '.$baseUrl.';
JS;
$this->registerJs($js);
AppAsset::register($this);
$controller=Yii::$app->controller->id;
$action=Yii::$app->controller->action->id;
$case_id = Yii::$app->request->get('case_id');
$client_case_name="";
if(in_array($action,array('post-comment')) || ($controller=='track' && $action=='index') || ($controller=='project' && $action=='edit') || ($controller=='case-projects' && $action=='change-project') ) {
	$clientCase = Yii::$app->db->createCommand("Select case_name,client_name from tbl_client_case inner join tbl_client on tbl_client.id=tbl_client_case.client_id  WHERE tbl_client_case.id=$case_id")->queryOne();
	//echo "<pre>",print_r($clientCase),"</pre>";
	//ClientCase::findOne($case_id);
}else{
$case_data=ClientCase::findOne($case_id);
$client_case_name=$case_data->client->client_name." - ".$case_data->case_name;
//$client_case_data = ClientCase::getClientCaseData();
}
$resuest = Yii::$app->request->get();

$this->title = 'My Cases';
if(isset($resuest['team_id']) && $controller=='summary-comment')
{
	$this->title = 'My Teams';
}
// script to parse the results into the format expected by Select2
$resultsJs = <<< JS
function (data, params) {
    params.page = params.page || 1;
    var more = (params.page * 50) < data.total_count;
    return {
        results: data.items,
        pagination : {
          more: more
        }   
    };
}
JS;
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="shortcut icon" href="<?php echo Yii::$app->request->baseUrl; ?>/images/app_company_logo_small.png" type="image/png" />
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
    <noscript></noscript>
</head>
<body>
<?php $this->beginBody() ?>
<?= IsataskHeader::widget(); ?>
<div class="wrap">
<?= Alert::widget() ?>
    <div class="site-index">
      <div class="body-content">
        <div class="row">          
          <div class="col-md-12">
            <div id="page-title" role="heading" class="page-header">
            <?php 
            	if($controller=='case-overview'){
	            	$headertext = 'Case Overview';
	            	/*if($action=='total-projects'){$headertext = 'Total Projects Report';}
	            	if($action=='total-media-projects'){$headertext = 'Total Media Report';}
	            	if($action=='total-media-unit-size'){$headertext = 'Total Media Unit Size Report';}
	            	if($action=='media-by-custodian'){$headertext = 'Media By Custodian Report';}
	            	if($action=='production-by-type'){$headertext = 'Total Productions Report';}
	            	if($action=='production-producing-parties'){$headertext = 'Productions Producing Parties Report';}*/
            ?>
                <em class="fa fa-pie-chart" title="<?= $headertext; ?>"></em><span><a href="javascript:void(0);" title="<?= $headertext; ?>" class="tag-header-red"><?= $headertext; ?></a></span>
            <?php 
	            } else if($controller=='case-projects' && in_array($action,array('index','change-project','load-canceled-projects','load-closed-projects','load-saved-projects'))) { 
	            	$headertext = 'Case Projects';
	            	$iconclass = 'fa-arrow-right text-danger';
	            	$textalign1 = 'text-right';
	            	$textalign2 = 'text-left';
	            	if($action=='change-project'){
	            		$headertext = 'Change Project: #'.Yii::$app->request->get('task_id');;
	            		$iconclass = 'fa-save text-danger';
	            		$textalign1 = 'text-left';
	            		$textalign2 = 'text-right';
	            	}
	            	if($action=='load-canceled-projects'){
	            		$headertext = 'Canceled Projects';
	            		$iconclass = 'fa-times-circle text-danger';
	            		$textalign1 = 'text-left';
	            		$textalign2 = 'text-right';
	            	}
	            	if($action=='load-closed-projects'){$headertext = 'Closed Projects';$iconclass = 'fa-minus-circle text-theme-blue';$textalign1 = 'text-left';$textalign2 = 'text-right';}
	            	if($action=='load-saved-projects'){$headertext = 'Saved Projects';$iconclass = 'fa-save text-theme-blue';$textalign1 = 'text-left';$textalign2 = 'text-right';}
	        ?>
            	<!--<em class="fa <?php //$iconclass ?>"></em>-->
            	<span class="icon-stack" title="<?= $headertext; ?>">
				   <em class="fa <?= $iconclass ?> icon-stack-2x <?= $textalign1; ?>"></em>
				   <em class="fa fa-file-o icon-stack-1x <?= $textalign2; ?>"></em>
				</span>
                <span><a href="javascript:void(0);" title="<?= $headertext; ?>" class="tag-header-red"><?= $headertext; ?></a></span>
             <?php 
	            } else if($controller=='case-production') { 
	            	$headertext = 'Case Production';
	            	if($action=='index'){$headertext = 'Case Productions';}
	        ?>
                <em class="fa fa-pie-chart" title="<?= $headertext; ?>"></em><span><a href="javascript:void(0);" title="<?= $headertext; ?>" class="tag-header-red"><?= $headertext; ?></a></span> 
            	<?php }
				else if($controller=='project' && $action=='add') { 
	            	$headertext = 'New Project';
	        	?>
                <em class="fa fa-tasks" title="<?= $headertext; ?>"></em><span><a href="javascript:void(0);" title="<?= $headertext; ?>" class="tag-header-red"><?= $headertext; ?></a></span> 
            	<?php }
				else if($controller=='project' && $action=='saved') { 
	            	$headertext = 'Edit Saved Instruction';
	        	?>
	        	<span class="icon-stack" title="<?= $headertext; ?>">
				   <em class="fa fa-save icon-stack-2x text-left"></em>
				   <em class="fa fa-file-o icon-stack-1x text-right"></em>
				</span>
                <span><a href="javascript:void(0);" title="<?= $headertext; ?>" class="tag-header-red"><?= $headertext; ?></a></span> 
            	<?php }
				else if($controller=='project' && $action=='edit') { 
	            	$headertext = 'Change Project: #'.Yii::$app->request->get('task_id');;
            	?>
	        	<span class="icon-stack" title="<?= $headertext; ?>">
				   <em class="fa fa-save text-danger icon-stack-2x text-left"></em>
				   <em class="fa fa-file-o icon-stack-1x text-right"></em>
				</span>
                <span><a href="javascript:void(0);" title="<?= $headertext; ?>" class="tag-header-red"><?= $headertext; ?></a></span> 
            	<?php } else if($controller=='case-custodians') { 
	            	$headertext = 'Case Custodians';
	            	if($action=='index'){$headertext = 'Case Custodians';}
	        	?>
                <em class="fa fa-file-o" title="<?= $headertext; ?>"></em><span><a href="javascript:void(0);" title="<?= $headertext; ?>" class="tag-header-red"><?= $headertext; ?></a></span> 
            	<?php } else if($controller=='case-projects' && $action=='post-comment') {
            		$headertext = 'Project Comments (Project: #'.Yii::$app->request->get('task_id').')';
            		?> 
                <em class="fa fa-comments" title="<?= $headertext; ?>"></em><span><?= $headertext; ?></span>   
            		
            <?php 
	            } else if($controller=='case-budget' && $action=='index') {
            		$headertext = 'Case Budget';
            		?> 
            		 <em class="fa fa-bank" title="<?= $headertext; ?>"></em>
                         <span><a href="javascript:void(0);" title="<?= $headertext; ?>" class="tag-header-red"><?= $headertext; ?></a></span>   
            <?php 
	            } else if(($controller=='case' && $action=='case-summary') || ($controller=='summary-comment' && $action=='index')) {
            		$headertext = 'Case Summary';
            		?> 
            		 <em class="fa fa-pencil" title="<?= $headertext; ?>"></em>
                         <span><a href="javascript:void(0);" title="<?= $headertext; ?>" class="tag-header-red"><?= $headertext; ?></a></span>   
            <?php 
	            } else if($controller=='track' && $action=='index') {
            		$headertext = Html::a('Track Project', "javascript:viewInstruction(".Yii::$app->request->get('taskid').");", array("class" => "dialog tag-header-red","style"=>"","title"=>"Project #".Yii::$app->request->get('taskid')));
            		?> 
            		 <em class="fa fa-briefcase" title="<?= strip_tags($headertext); ?>"></em>
                         <span><?= $headertext; ?></span>   
            <?php 
	            } else if($controller=='case-projects' && $action=='est-report') {
            		$headertext = 'Case Projects';
            		?> 
            		 <em class="fa fa-tasks" title="<?= $headertext; ?>"></em>
                         <span><a href="javascript:void(0);" title="<?= $headertext; ?>" class="tag-header-red"><?= $headertext; ?></a></span>   
            <?php 
	            } else if($controller=='project' && $action=='create') {
            		$headertext = 'New Project';
            		?> 
            		 <em class="fa fa-tasks" title="<?= $headertext; ?>"></em>
                         <span><a href="javascript:void(0);" title="<?= $headertext; ?>" class="tag-header-red"><?= $headertext; ?></a></span>   
            <?php 
	            } else if($controller=='case-documents') {
            		$headertext = 'Case Documents';
            		?> 
            		 <em class="fa fa-tasks" title="<?= $headertext; ?>"></em>
                         <span><a href="javascript:void(0);" title="<?= $headertext; ?>" class="tag-header-red"><?= $headertext; ?></a></span>   
            <?php 
	            } else {
			?>
                         <em class="fa fa-wrench" title="<?= $headertext; ?>"></em><span><a href="javascript:void(0);" title="<?= $headertext; ?>" class="tag-header-red">My Cases</a></span>
            <?php 
	            } 
			?>
                        <span class="pull-right ddclientcaseteamloc_main" id="header-right">
						<?php 
						if(in_array($action,array('post-comment')) || ($controller=='track' && $action=='index') || ($controller=='project' && $action=='edit') || ($controller=='case-projects' && $action=='change-project') ) {?>
						<a href="javascript:void(0);" title="<?= $clientCase['client_name']." - ".$clientCase['case_name']; ?>" class="tag-header-red"><?= $clientCase['client_name']." - ".$clientCase['case_name']; ?></a>
						<?php 
							}else{
						/*
						
						*/		
						 echo Select2::widget([
							 	'id'=>'client_case_id',
								'name' => 'client_case_id',
								'initValueText' => $client_case_name,
								//'data' => $client_case_data,
								'value'=>$case_id,
								'pluginOptions' => [
									'allowClear' => false,
									'ajax' => [
										'url' => Url::to(['mycase/clientcasejsonlist']),
										'dataType' => 'json',
										'delay' => 250,
										'data' => new JsExpression('function(params) { return {q:params.term, page: params.page}; }'),
										'processResults' => new JsExpression($resultsJs),
										'cache' => true
										
									],
								],
								'pluginEvents' => [
								'change' => "function() {
									showLoader();
									var current_url=window.location.href;
									newUrl = current_url.replace(/(case_id=)[^\&]+/, '$1' + this.value);
									window.location.href=newUrl;
									
								}",
								]
							]);
							}
						?>
						</span>
            </div>
            <input type="hidden" value="<?= $case_id; ?>" id="case_id">
          </div>
		</div>
		<div class="row">
                  <div class="col-xs-12 col-sm-4 col-md-3 left-side">
                    <?= MycaseLeftPanel::widget(); ?>
	          </div>
                    <div id="admin_main_container" class="col-xs-12 col-sm-8 col-md-9 right-side">
                        <?= $content ?>
                    </div>
		 </div>
      </div>
    </div>
</div>
<?= IsataskFooter::widget(); ?>
<div class="loder-main" id="loader" style="display:none;">
 <div class="loder-box">
 	<a title="Loading" href="javascript:void(0);"><span class="screenreader">Loading</span><svg class="uil-spin" preserveAspectRatio="xMidYMid" viewBox="0 0 100 100" xmlns="#" height="55px" width="55px"><rect class="bk" fill="none" height="100" width="100" y="0" x="0"/><g transform="translate(50 50)"><g transform="rotate(0) translate(34 0)"><circle fill="#000" r="8" cy="0" cx="0"><animate repeatCount="indefinite" dur="1s" begin="0s" to="0.1" from="1" attributeName="opacity"/><animateTransform repeatCount="indefinite" dur="1s" begin="0s" to="1" from="1.5" type="scale" attributeName="transform"/></circle></g><g transform="rotate(45) translate(34 0)"><circle fill="#000" r="8" cy="0" cx="0"><animate repeatCount="indefinite" dur="1s" begin="0.12s" to="0.1" from="1" attributeName="opacity"/><animateTransform repeatCount="indefinite" dur="1s" begin="0.12s" to="1" from="1.5" type="scale" attributeName="transform"/></circle></g><g transform="rotate(90) translate(34 0)"><circle fill="#000" r="8" cy="0" cx="0"><animate repeatCount="indefinite" dur="1s" begin="0.25s" to="0.1" from="1" attributeName="opacity"/><animateTransform repeatCount="indefinite" dur="1s" begin="0.25s" to="1" from="1.5" type="scale" attributeName="transform"/></circle></g><g transform="rotate(135) translate(34 0)"><circle fill="#000" r="8" cy="0" cx="0"><animate repeatCount="indefinite" dur="1s" begin="0.37s" to="0.1" from="1" attributeName="opacity"/><animateTransform repeatCount="indefinite" dur="1s" begin="0.37s" to="1" from="1.5" type="scale" attributeName="transform"/></circle></g><g transform="rotate(180) translate(34 0)"><circle fill="#000" r="8" cy="0" cx="0"><animate repeatCount="indefinite" dur="1s" begin="0.5s" to="0.1" from="1" attributeName="opacity"/><animateTransform repeatCount="indefinite" dur="1s" begin="0.5s" to="1" from="1.5" type="scale" attributeName="transform"/></circle></g><g transform="rotate(225) translate(34 0)"><circle fill="#000" r="8" cy="0" cx="0"><animate repeatCount="indefinite" dur="1s" begin="0.62s" to="0.1" from="1" attributeName="opacity"/><animateTransform repeatCount="indefinite" dur="1s" begin="0.62s" to="1" from="1.5" type="scale" attributeName="transform"/></circle></g><g transform="rotate(270) translate(34 0)"><circle fill="#000" r="8" cy="0" cx="0"><animate repeatCount="indefinite" dur="1s" begin="0.75s" to="0.1" from="1" attributeName="opacity"/><animateTransform repeatCount="indefinite" dur="1s" begin="0.75s" to="1" from="1.5" type="scale" attributeName="transform"/></circle></g><g transform="rotate(315) translate(34 0)"><circle fill="#000" r="8" cy="0" cx="0"><animate repeatCount="indefinite" dur="1s" begin="0.87s" to="0.1" from="1" attributeName="opacity"/><animateTransform repeatCount="indefinite" dur="1s" begin="0.87s" to="1" from="1.5" type="scale" attributeName="transform"/></circle></g></g></svg></a>
	<p>Loading</p>
 </div>
</div>
 <script type="text/javascript">
$(function() {
	if($('input[type="checkbox"]').length > 0 || $('input[type="radio"]').length > 0){
	  $('input').customInput();
    }
});
</script>
<noscript></noscript>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
