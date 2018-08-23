<!DOCTYPE html>
<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\LoginAsset;
use app\models\Settings;
use app\components\IsataskFooter;
LoginAsset::register($this);

$logo = Settings::find()->where("field = 'loginpage_logo'")->one()->fieldimage;

$logo_name = Settings::find()->where("field = 'custom_logo_name'")->one()->fieldvalue;
if($logo_name == ""){
    $logo_name=Yii::$app->name;
}
?>
<?php $this->beginPage() ?>

<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="<?php echo Yii::$app->request->baseUrl; ?>/images/app_company_logo_small.png" type="image/png" />
    <?= Html::csrfMetaTags() ?>
    <title>Login</title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>
<div class="wrap login-details">
       <div class="container">
       <div class="row ">
	   <!--<h1>Login</h1>
<p>This is Login page for ISATASK</p>-->
		   <?php if($logo != ''){ ?>
       		<header class=""><div class="navbar-brand"><a href="#" title="<?php echo $logo_name?>"><?php echo Html::img("data:image/jpeg;base64,". base64_encode( utf8_decode($logo) ),['title'=>$logo_name,'alt'=>$logo_name,'style'=>'height:64px'])?></a></div></header>
			<?php } else { ?>
			<header class=""><div class="navbar-brand"><a href="#" title="<?php echo $logo_name?>"><?php echo Html::img('@web/images/logo_login.png',['title'=>$logo_name,'alt'=>$logo_name,'style'=>'height:64px'])?></a></div></header>
			<?php } ?>
        	<?= $content ?>
        </div>
    </div>
</div>

<?= IsataskFooter::widget(); ?>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
