<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;
use app\components\IsataskHeader;
use app\components\IsataskFooter;
use app\models\Settings;
AppAsset::register($this);


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
	<div class="site-index">
      	<div class="body-content">
       	 	<?= $content ?>
    	</div>
  	</div>
</div>
<?= IsataskFooter::widget(); ?>
<div class="loder-main" id="loader" style="display:none;">
 <div class="loder-box">
 	<a title="Loading" href="javascript:void(0);"><span class="screenreader">Loading</span>
		<svg class="uil-spin" preserveAspectRatio="xMidYMid" viewBox="0 0 100 100" xmlns="#" height="55px" width="55px"><rect class="bk" fill="none" height="100" width="100" y="0" x="0"/><g transform="translate(50 50)"><g transform="rotate(0) translate(34 0)"><circle fill="#000" r="8" cy="0" cx="0"><animate repeatCount="indefinite" dur="1s" begin="0s" to="0.1" from="1" attributeName="opacity"/><animateTransform repeatCount="indefinite" dur="1s" begin="0s" to="1" from="1.5" type="scale" attributeName="transform"/></circle></g><g transform="rotate(45) translate(34 0)"><circle fill="#000" r="8" cy="0" cx="0"><animate repeatCount="indefinite" dur="1s" begin="0.12s" to="0.1" from="1" attributeName="opacity"/><animateTransform repeatCount="indefinite" dur="1s" begin="0.12s" to="1" from="1.5" type="scale" attributeName="transform"/></circle></g><g transform="rotate(90) translate(34 0)"><circle fill="#000" r="8" cy="0" cx="0"><animate repeatCount="indefinite" dur="1s" begin="0.25s" to="0.1" from="1" attributeName="opacity"/><animateTransform repeatCount="indefinite" dur="1s" begin="0.25s" to="1" from="1.5" type="scale" attributeName="transform"/></circle></g><g transform="rotate(135) translate(34 0)"><circle fill="#000" r="8" cy="0" cx="0"><animate repeatCount="indefinite" dur="1s" begin="0.37s" to="0.1" from="1" attributeName="opacity"/><animateTransform repeatCount="indefinite" dur="1s" begin="0.37s" to="1" from="1.5" type="scale" attributeName="transform"/></circle></g><g transform="rotate(180) translate(34 0)"><circle fill="#000" r="8" cy="0" cx="0"><animate repeatCount="indefinite" dur="1s" begin="0.5s" to="0.1" from="1" attributeName="opacity"/><animateTransform repeatCount="indefinite" dur="1s" begin="0.5s" to="1" from="1.5" type="scale" attributeName="transform"/></circle></g><g transform="rotate(225) translate(34 0)"><circle fill="#000" r="8" cy="0" cx="0"><animate repeatCount="indefinite" dur="1s" begin="0.62s" to="0.1" from="1" attributeName="opacity"/><animateTransform repeatCount="indefinite" dur="1s" begin="0.62s" to="1" from="1.5" type="scale" attributeName="transform"/></circle></g><g transform="rotate(270) translate(34 0)"><circle fill="#000" r="8" cy="0" cx="0"><animate repeatCount="indefinite" dur="1s" begin="0.75s" to="0.1" from="1" attributeName="opacity"/><animateTransform repeatCount="indefinite" dur="1s" begin="0.75s" to="1" from="1.5" type="scale" attributeName="transform"/></circle></g><g transform="rotate(315) translate(34 0)"><circle fill="#000" r="8" cy="0" cx="0"><animate repeatCount="indefinite" dur="1s" begin="0.87s" to="0.1" from="1" attributeName="opacity"/><animateTransform repeatCount="indefinite" dur="1s" begin="0.87s" to="1" from="1.5" type="scale" attributeName="transform"/></circle></g></g></svg>
		<p>Loading</p>
	</a>
	
 </div>
</div>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
