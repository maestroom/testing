<?php
/* @var $this \yii\web\View */
/* @var $content string */
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;
use app\assets\BillingAsset;
use app\components\BillingLeftPanel;
use app\components\IsataskHeader;
use app\components\IsataskFooter;
use app\components\widgets\Alert;

use app\models\InvoiceFinal;
$baseUrl=Yii::$app->request->baseUrl;
$js = <<<JS
var baseUrl = '.$baseUrl.';
JS;
$this->title = 'Billing';
$this->registerJs($js);
AppAsset::register($this);
BillingAsset::register($this);
$controller=Yii::$app->controller->id;
$action=Yii::$app->controller->action->id;
$type = Yii::$app->request->get('type','');
?>
<?php $this->beginPage() ?>

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
<input type="hidden" id='module-url' />
<input type="hidden" id='pajax_container' />
<?= Alert::widget() ?>
    <div class="site-index">
      <div class="body-content">
        <div class="row">
          <div class="col-md-12">
            <div id="page-title" role="heading" class="page-header">
	            <?php
	            	if($controller=='billing-taxes'){
		            	$headertext = '';
		            	if($action=='tax-classes'){$headertext = 'Tax Management - Tax Classes';}
		            	if($action=='tax-codes'){$headertext = 'Tax Management - Tax Codes';}
		        ?>
                <em class="fa fa-pie-chart" title="<?= $headertext; ?>"></em><span tabindex="0" class="top-left-header"><a href="javascript:void(0);" title="<?= $headertext; ?>" class="tag-header-red"><?= $headertext; ?></a></span>
            	<?php } else if($controller=='billing-generate-invoice'){
            			$headertext = '';
            			if($action=='billing-invoice-management' || $action=='display-generate-invoice'){$headertext = 'Generate Invoices';} ?>
            			<?php if($action=='saved-invoice'){$headertext = 'Saved Invoices';} ?>
                <em class="fa fa-pie-chart" title="<?= $headertext; ?>"></em><span tabindex="0" class="top-left-header"><a href="javascript:void(0);" title="<?= $headertext; ?>" class="tag-header-red"><?php echo $headertext; ?></a></span>
				<?php } else if($controller == 'billing-pricelist'){
						$headertext = "";
						if($action == 'internal-team-pricing'){
							$headertext="Internal Team Pricing";
						} else if($action == 'internal-shared-pricing'){
							$headertext="Internal Shared Pricing";
						} else if($action == 'get-preferred-pricing' && $type == 'client'){
							$headertext="Client Preferred Pricing";
						} else if($action == 'get-preferred-pricing' && $type == 'case'){
							$headertext="Case Preferred Pricing";
						}
				?>
                                                <em class="fa fa-money" title="Price List - <?= $headertext; ?>"></em><span tabindex="0" class="top-left-header"><a href="javascript:void(0);" title="Price List - <?= $headertext; ?>" class="tag-header-red">Price List - <?= $headertext; ?></a></span>
				<?php } else if($controller == 'billing-finalized-invoice') {
                                        $headertext = "";
                                        $sum='';
                                        if($action=='finalized-invoices'){
                                                $sum = 0;
                                                $sum = 'Total Revenue: $'.$sum;
                                                $headertext = 'Finalized Invoices';
                                        } else if($action == 'preview-invoice') {
                                                $headertext = 'Finalized Invoice - Preview';
                                        } else if($action == 'edit-invoice'){
                                                $headertext = 'Finalized Invoice - Edit';
                                        }
				?>
                                <em class="fa fa-money" title="<?= $headertext; ?>"></em>
								<span tabindex="0" class="top-left-header">
								<a href="javascript:void(0);" title="<?= $headertext; ?>" class="tag-header-red"><?= $headertext; ?></a>
								</span>
								<span class="pull-right">
								<!--<a href="javascript:void(0);" title="<?= $sum; ?>" class="tag-header-red"><?= $sum; ?></a>-->
								</span>
				<?php } else if($controller == 'billing-closed-invoice') {
                                        $headertext = "";
                                        $sum='';
                                        if($action=='closed-invoices'){
                                                $headertext = 'Closed Invoices';
                                        } else if($action == 'preview-invoice') {
                                                $headertext = 'Closed Invoice - Preview';
                                        }
				?>
                                <em class="fa fa-money" title="<?= $headertext; ?>"></em>
								<span tabindex="0" class="top-left-header">
								<a href="javascript:void(0);" title="<?= $headertext; ?>" class="tag-header-red"><?= $headertext; ?></a>
								</span>
								<span class="pull-right">
								<!--<a href="javascript:void(0);" title="<?= $sum; ?>" class="tag-header-red"><?= $sum; ?></a>-->
								</span>
				<?php } ?>

				<!--<span class="pull-right <?php //if($type!='client'){ ?>hide<?php //} ?>" id="header-right-client"><?php //Html::dropDownList('client_id', [], $clientList, ['class'=>'form-control billing-dropdown-filterlist','onchange'=>"getTemplatesByID(this.value,0,'client');"]); ?></span>
            	<span class="pull-right <?php //if($type!='case'){ ?>hide<?php //} ?>" id="header-right-clientcase"><?php //Html::dropDownList('client_case_id', [], $clientCaseList, ['class'=>'form-control billing-dropdown-filterlist','onchange'=>"getTemplatesByID(0,this.value,'case');"]); ?></span>-->
            </div>
          </div>
		</div>
		<div class="row">
			  <div class="col-xs-12 col-sm-4 col-md-3 left-side">
	          	<?= BillingLeftPanel::widget(); ?>
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
