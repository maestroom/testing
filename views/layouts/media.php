<?php
/* @var $this \yii\web\View */
/* @var $content string */
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;
use app\components\MediaLeftPanel;
use app\components\IsataskHeader;
use app\components\IsataskFooter;
use app\components\widgets\Alert;
use app\models\ClientCase;
use kartik\widgets\Select2;
use yii\widgets\ActiveForm;
use yii\web\JsExpression;
$baseUrl=Yii::$app->request->baseUrl;
$userId = Yii::$app->user->identity->id;
$roleId = Yii::$app->user->identity->role_id;
$js = <<<JS
var baseUrl = '.$baseUrl.';
JS;

$this->registerJs($js);
AppAsset::register($this);
$model = new ClientCase();
/*
 $securitySql="SELECT client_case_id FROM tbl_project_security WHERE user_id=".$userId." and client_id!=0 and client_case_id!=0 group by client_case_id";
 
 $clientDropdownData = ClientCase::find()->joinWith('client')->select(['tbl_client_case.id','client_id','case_name','client_name']);
 if($roleId!=0){
	 $clientDropdownData->where('tbl_client_case.id IN ('.$securitySql.')');
 }
 $clientDropdownData = $clientDropdownData->orderBy('tbl_client.client_name')->all();
*/

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
              <div id="page-title" role="heading" class="page-header default-header"><em title="Media Inventory" class="fa fa-file-image-o"></em> <span><a href="javascript:void(0);" title="Media Inventory" class="tag-header-red">Media Inventory </a></span>
           	<div class="col-sm-3 pull-right">
            <span  id="header-right"><?php 
				/*
				$client_dropdowndata[0] = 'No Associated Cases';
				foreach ($clientDropdownData as $ddata){
                                        $client_dropdowndata[$ddata->id] = html_entity_decode($ddata->client->client_name. ' - '.$ddata->case_name);				
                                } 
				echo "<pre>",print_r($client_dropdowndata),"</pre>";
				$form = ActiveForm::begin(['id' => $model->formName(), 'enableAjaxValidation' => false, 'enableClientValidation' => true]); 				
				echo $form->field($model, 'client_id')->widget(Select2::classname(), [
							'data' => $client_dropdowndata,
							'options' => ['prompt' => 'Filter Client - Case Media', 'aria-hidden' => 'true', 'id' => 'media-inventory-filter', 'title' => 'Filter by Client - Case', 'class' => 'form-control billing-dropdown-filterlist'],
							'pluginOptions' => [
								'allowClear' => true
							]])->label(false); // ->label('',['for'=>'clientcase-client_id','class'=>NULL])
				ActiveForm::end();
				,'onchange'=>'fillterMedia(this.value);'
				*/
             ?></span></div>
            </div>
          </div>
		</div>
		<div class="row">
			  <div class="col-xs-12 col-sm-4 col-md-3 left-side">
	          	<?= MediaLeftPanel::widget(); ?>
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
 	<a title="Loading" href="javascript:void(0);"><span class="screenreader">Loading</span><svg class="uil-spin" preserveAspectRatio="xMidYMid" viewBox="0 0 100 100" xmlns="#" height="55px" width="55px"><rect class="bk" fill="none" height="100" width="100" y="0" x="0"/><g transform="translate(50 50)"><g transform="rotate(0) translate(34 0)"><circle fill="#000" r="8" cy="0" cx="0"><animate repeatCount="indefinite" dur="1s" begin="0s" to="0.1" from="1" attributeName="opacity"/><animateTransform repeatCount="indefinite" dur="1s" begin="0s" to="1" from="1.5" type="scale" attributeName="transform"/></circle></g><g transform="rotate(45) translate(34 0)"><circle fill="#000" r="8" cy="0" cx="0"><animate repeatCount="indefinite" dur="1s" begin="0.12s" to="0.1" from="1" attributeName="opacity"/><animateTransform repeatCount="indefinite" dur="1s" begin="0.12s" to="1" from="1.5" type="scale" attributeName="transform"/></circle></g><g transform="rotate(90) translate(34 0)"><circle fill="#000" r="8" cy="0" cx="0"><animate repeatCount="indefinite" dur="1s" begin="0.25s" to="0.1" from="1" attributeName="opacity"/><animateTransform repeatCount="indefinite" dur="1s" begin="0.25s" to="1" from="1.5" type="scale" attributeName="transform"/></circle></g><g transform="rotate(135) translate(34 0)"><circle fill="#000" r="8" cy="0" cx="0"><animate repeatCount="indefinite" dur="1s" begin="0.37s" to="0.1" from="1" attributeName="opacity"/><animateTransform repeatCount="indefinite" dur="1s" begin="0.37s" to="1" from="1.5" type="scale" attributeName="transform"/></circle></g><g transform="rotate(180) translate(34 0)"><circle fill="#000" r="8" cy="0" cx="0"><animate repeatCount="indefinite" dur="1s" begin="0.5s" to="0.1" from="1" attributeName="opacity"/><animateTransform repeatCount="indefinite" dur="1s" begin="0.5s" to="1" from="1.5" type="scale" attributeName="transform"/></circle></g><g transform="rotate(225) translate(34 0)"><circle fill="#000" r="8" cy="0" cx="0"><animate repeatCount="indefinite" dur="1s" begin="0.62s" to="0.1" from="1" attributeName="opacity"/><animateTransform repeatCount="indefinite" dur="1s" begin="0.62s" to="1" from="1.5" type="scale" attributeName="transform"/></circle></g><g transform="rotate(270) translate(34 0)"><circle fill="#000" r="8" cy="0" cx="0"><animate repeatCount="indefinite" dur="1s" begin="0.75s" to="0.1" from="1" attributeName="opacity"/><animateTransform repeatCount="indefinite" dur="1s" begin="0.75s" to="1" from="1.5" type="scale" attributeName="transform"/></circle></g><g transform="rotate(315) translate(34 0)"><circle fill="#000" r="8" cy="0" cx="0"><animate repeatCount="indefinite" dur="1s" begin="0.87s" to="0.1" from="1" attributeName="opacity"/><animateTransform repeatCount="indefinite" dur="1s" begin="0.87s" to="1" from="1.5" type="scale" attributeName="transform"/></circle></g></g></svg>
            <span class="hide">Loading</span></a>
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
