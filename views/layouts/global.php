<?php
/* @var $this \yii\web\View */
/* @var $content string */
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;
use app\assets\SystemAsset;
use app\components\GlobalLeftPanel;
use app\components\IsataskHeader;
use app\components\IsataskFooter;
use app\components\widgets\Alert;

$baseUrl=Yii::$app->request->baseUrl;
$js = <<<JS
var baseUrl = '.$baseUrl.';
JS;

$this->registerJs($js);
AppAsset::register($this);

$controller=Yii::$app->controller->id;
$action=Yii::$app->controller->action->id;
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
<?= Alert::widget() ?>
    <div class="site-index">
      <div class="body-content">
        <div class="row">          
          <div class="col-md-12">
            <div id="page-title" role="heading" class="page-header">
            <?php if($controller=='global-projects' && ($action=='save-filter-grid')){?>
                <em class="fa fa-search-plus text-danger" title="Saved Filters"></em> <span><a title="Saved Filters" class="tag-header-red" href="javascript:void(0);">Saved Filters</a></span>
            <?php }else if($controller=='global-projects' && ($action=='index')){?>
                <em class="fa fa-search-plus text-danger" title="Dynamic Filters"></em> <span><a title="Dynamic Filters" class="tag-header-red" href="javascript:void(0);">Dynamic Filters</a></span>
            <?php } else if($controller=='global-projects' && ($action=='filter-option')){?>
                <em class="fa fa-search-plus text-danger" title="Filter Options"></em> <span><a title="Filter Options" class="tag-header-red" href="javascript:void(0);">Filter Options</a></span>
				<?php } else{ ?>
                <em class="fa fa-search-plus text-danger" title="Global Projects"></em> <span><a title="Global Projects" class="tag-header-red" href="javascript:void(0);">Global Projects</a></span>	
					<?php } ?>
            
            </div>
          </div>
		</div>
		<div class="row">
			  <div class="col-xs-12 col-sm-4 col-md-3 left-side">
	          	<?= GlobalLeftPanel::widget(); ?>
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
