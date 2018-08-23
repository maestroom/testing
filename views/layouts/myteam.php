<?php
/* @var $this \yii\web\View */
/* @var $content string */
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;
use app\components\MyTeamLeftPanel;
use app\components\IsataskHeader;
use app\components\IsataskFooter;
use app\components\widgets\Alert;
use app\models\Team;
use app\models\Tasks;
use app\models\Client;
use app\models\ClientCase;
use app\models\TeamlocationMaster;
use kartik\widgets\Select2;

$this->registerJsFile(Yii::$app->request->baseUrl.'/js/team.js');

$baseUrl=Yii::$app->request->baseUrl;
$js = <<<JS
var baseUrl = '.$baseUrl.';
JS;

$this->registerJs($js);
AppAsset::register($this);

$controller=Yii::$app->controller->id;
$action=Yii::$app->controller->action->id;
$team_id = Yii::$app->request->get('team_id');
$team_loc = Yii::$app->request->get('team_loc');
$task_id = Yii::$app->request->get('taskid');



if(in_array($action,array('post-comment')) || ($controller=='track' && $action=='index') || ($controller=='team-projects' && $action=='instrution')) {
//echo "here";die;
$team_info=Team::findOne($team_id);
$teamloc_info=TeamLocationMaster::findOne($team_loc);
}
if($controller=='track' && in_array($action,array('index'))){
	$clientCase = Yii::$app->db->createCommand("Select case_name,client_name from tbl_client_case inner join tbl_tasks on tbl_tasks.client_case_id=tbl_client_case.id  inner join tbl_client on tbl_client.id=tbl_client_case.client_id  WHERE tbl_tasks.id=$task_id")->queryOne();
//echo "<pre>",print_r($clientCase),"</pre>";die;
//$task_info = Tasks::findOne($task_id);
//$client_case_info=$task_info->clientCase;
//$client_info = Client::findOne($client_case_info->client_id);
 //= ClientCase::findOne($task_info->client_case_id);
}
$this->title = 'My Teams';

$team_loc_data=Team::getTeamLocData();
//echo "<pre>",print_r($team_loc_data);die;
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
            	if($controller=='team-overview' && in_array($action,array('total-projects','total-media-projects','total-media-unit-size','media-by-custodian','production-by-type','production-producing-parties','taskassignments','taskdistribute','followupdistribute','taskassigncompleted','assignbyprojectsize'))){
	            	$headertext = '';
	            	if($action=='total-projects'){$headertext = 'Total Projects Reports';}
	            	if($action=='total-media-projects'){$headertext = 'Total Media Reports';}
	            	if($action=='total-media-unit-size'){$headertext = 'Total Media Unit Size Reports';}
	            	if($action=='media-by-custodian'){$headertext = 'Media By Custodian Reports';}
	            	if($action=='production-by-type'){$headertext = 'Total Productions Reports';}
	            	if($action=='production-producing-parties'){$headertext = 'Productions Producing Parties Reports';}
                    if($action == 'taskassignments' || $action == 'taskdistribute' || $action == 'followupdistribute' || $action=='taskassigncompleted' || $action=='assignbyprojectsize'){
							$headertext='Team Overview';
					}
            ?>
                <em class="fa fa-pie-chart" title="<?= $headertext; ?>"></em><span><a href="javascript:void(0);" title="<?= $headertext; ?>" class="tag-header-red"><?= $headertext; ?></a></span>
            <?php 
	            } else if($controller=='summary-comment' && $action=='index') { 
	            	$headertext = 'Team Overview';
	        ?>
                <em class="fa fa-pie-chart" title="<?= $headertext; ?>"></em><span><a href="javascript:void(0);" title="<?= $headertext; ?>" class="tag-header-red"><?= $headertext; ?></a></span>
             <?php 
	            }else if($controller=='team-projects' && in_array($action,array('index','load-cancelled-projects','load-closed-projects'))) { 
	            	$headertext = 'Team Projects';
	            	if($action=='load-cancelled-projects'){$headertext = 'Cancelled Projects';}
	            	if($action=='load-closed-projects'){$headertext = 'Closed Projects';}
	        ?>
                <em class="fa fa-pie-chart" title="<?= $headertext; ?>"></em><span><a href="javascript:void(0);" title="<?= $headertext; ?>" class="tag-header-red"><?= $headertext; ?></a></span>
             <?php 
	            } else if($controller=='track' && in_array($action,array('index'))){
					$headertext = 'Track Project'; ?>
                <em class="fa fa-pie-chart" title="<?= $headertext; ?>"></em><span><a href="javascript:void(0);" title="<?= $headertext.' ('.$clientCase['client_name'].' - '.$clientCase['case_name'].')'; ?>" class="tag-header-red"><?= $headertext.' ('.$clientCase['client_name'].' - '.$clientCase['case_name'].')'; ?></a></span>
				<?php }
					else if($controller=='team-tasks' && $action=='index') { 
	            	$headertext = 'Team Tasks'; ?>
                <em class="fa fa-pie-chart" title="<?= $headertext; ?>"></em><span><a href="javascript:void(0);" title="<?= $headertext; ?>" class="tag-header-red"><?= $headertext; ?></a></span>
	            	
			<?php } 
	            
	            else if($controller=='team-projects' && $action=='instrution') { 
	            	$headertext = 'Project Instructions (Project: #'.Yii::$app->request->get('task_id').')';
	        ?>
                <em class="fa fa-wrench" title="<?= $headertext; ?>"></em><span><a href="javascript:void(0);" title="<?= $headertext; ?>" class="tag-header-red"><?= $headertext; ?></a></span>
             <?php 
                } else if($controller=='team-documents') {
            		$headertext = 'Team Documents';
            		?> 
            		 <em class="fa fa-tasks" title="<?= $headertext; ?>"></em>
                         <span><a href="javascript:void(0);" title="<?= $headertext; ?>" class="tag-header-red"><?= $headertext; ?></a></span>   
            <?php 
	            }else if($controller=='case-production') { 
	            	$headertext = 'Case Production';
	            	if($action=='index'){$headertext = 'Case Production list';}
	            	//if($action=='load-closed-projects'){$headertext = 'Closed Projects';}
	        ?>
                <em class="fa fa-pie-chart" title="<?= $headertext; ?>"></em><span><a href="javascript:void(0);" title="<?= $headertext; ?>" class="tag-header-red"><?= $headertext; ?></a></span> 
            	<?php } else if($controller=='team-projects' && $action="post-comment") {
            		$headertext = 'Project Comments (Project: #'.Yii::$app->request->get('task_id').')';
            		?> 
                <em class="fa fa-comments" title="<?= $headertext; ?>"></em><span><a href="javascript:void(0);" title="<?= $headertext; ?>" class="tag-header-red"><?= $headertext; ?></a></span>   
            <?php 
	            }  else {
			?>
                <em class="fa fa-wrench" title="<?= $headertext; ?>"></em><span><a href="javascript:void(0);" title="My Teams" class="tag-header-red">My Teams</a></span>
            <?php 
	            } 
			?>
                <span class="pull-right ddclientcaseteamloc_main" id="header-right">
				<?php if(in_array($action,array('post-comment')) || ($controller=='track' && $action=='index') || ($controller=='team-projects' && $action=='instrution')) {?>
				<a href="javascript:void(0);" title="<?= $team_info->team_name." - ".$teamloc_info->team_location_name; ?>" class="tag-header-red"><?= $team_info->team_name." - ".$teamloc_info->team_location_name; ?></a>
				<?php 
				}else{
						 echo Select2::widget([
							 	'id'=>'team_loc_id',
								'name' => 'team_loc_id',
								'data' => $team_loc_data,
								'value'=>$team_id.'_'.$team_loc,
								'pluginOptions' => [
									'allowClear' => false
								],
								'pluginEvents' => [
								'change' => "function() {
									var team_team_loc=this.value;
									var res = team_team_loc.split('_');
									var team_id=res[0];
									var team_loc=res[1];
									showLoader();
									var current_url=window.location.href;
									newUrl = current_url.replace(/(team_id=)[^\&]+/, '$1' + team_id);
									newUrl = newUrl.replace(/(team_loc=)[^\&]+/, '$1' + team_loc);
									window.location.href=newUrl;
								}",
								]
							]);
					}	?>
				<!--<a href="javascript:void(0);" title="<?= $team_info->team_name." - ".$teamloc_info->team_location_name; ?>" class="tag-header-red"><?= $team_info->team_name." - ".$teamloc_info->team_location_name; ?></a>-->
				
				</span>
            </div>
            <input type="hidden" value="<?= $team_id; ?>" id="team_id">
            <input type="hidden" value="<?= $team_loc; ?>" id="team_loc">
          </div>
		</div>
		<div class="row">
			  <div class="col-xs-12 col-sm-4 col-md-3 left-side">
	          	<?= MyTeamLeftPanel::widget(); ?>
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
