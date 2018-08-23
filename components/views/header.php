<?php
/* @var $this \yii\web\View */
/* @var $content string */
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\web\Session;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;
use app\models\Role;
use app\models\User;
use app\models\Options;
use app\models\Settings;
use app\assets\TimeoutDialogAsset;
\app\assets\TimeoutDialogAsset::register($this);

$logo= Settings::getLogo();
//echo "<pre>",print_r($logo);die;
$logo_name=Settings::getLogoName();
$controller=Yii::$app->controller->id;
$action=Yii::$app->controller->action->id;
$resuest = Yii::$app->request->get();
$isCase=false;
if(isset($resuest['case_id']) && $controller=='track')
{
	$isCase=true;
}
if(isset($resuest['case_id']) && $controller=='case' && $action='case-summary')
{
	$isCase=true;
}
if(isset($resuest['case_id']) && $controller=='summary-comment')
{
	$isCase=true;
}
$isTeam=false;
if(isset($resuest['team_id']) && $controller=='track')
{
	$isTeam=true;
}
if(isset($resuest['team_id']) && $controller=='summary-comment')
{
	$isTeam=true;
}


$userId = Yii::$app->user->identity->id;
$roleId = Yii::$app->user->identity->role_id;
//echo "<pre>",print_r($_SESSION),print_r(Yii::$app->user->identity);die;
$roleInfo = $_SESSION['role'];
//Role::find()->select(['role_type'])->where('id = '.$roleId)->one();
$User_Role = explode(',', $roleInfo->role_type);
$has_access_4=0;
$has_access_5=0;

$session = new Session;
$session->open();
if(!isset($session['has_access_4'])){
	$session['has_access_4']=(new User)->checkAccess(4);
}
if(!isset($session['has_access_5'])){
	$session['has_access_5']=(new User)->checkAccess(5);
}
$has_access_4=$session['has_access_4'];
$has_access_5=$session['has_access_5'];

/*session timeout with confrim dialog*/
$option = $session['options']->session_timeout;
if(isset($session['options'][0]->session_timeout)){
$option = $session['options'][0]->session_timeout;
}
//Options::find()->where('user_id ='.Yii::$app->user->identity->id)->one()->session_timeout;
$timeout=259200;

if(!Yii::$app->user->isGuest && Yii::$app->user->authTimeout != null){
	$settings_info = Settings::getSessionTimeout();
	if(isset($settings_info->fieldvalue) && ($settings_info->fieldvalue=='default' || $settings_info->fieldvalue==1)){
		if(isset($option) && ($option != 0 || $option != "")){
			$timeout=$option;
			if($timeout=="never")
				$timeout=259200;
		}
	} else {
		$timeout=$settings_info->fieldvalue;
	}
	$options = [
			'dialogTitle'=> 'Your Session is about to expire!',
			'dialogText'=> 'Do you want to stay signed in?',
			'logoutButton'=> 'Sign Out',
	        'keepAliveButton'=> 'Keep Alive',
	        //'keep_alive_url' =>  Yii::$app->urlManager->createUrl('/site/keepAlive'),
	        'redirectUrl'=> Yii::$app->urlManager->createUrl('/site/logout'),
	        'idleTimeLimit'=> $timeout,
            'dialogDisplayLimit'=> 60,
            'sessionKeepAliveTimer' => false,
            'dialogTimeRemaining'=> "You will be logged out in <span id='countdownDisplay' class='countdown-holder'>60</span> seconds",
        ];
        $this->registerJs('$(document).idleTimeout(' . yii\helpers\Json::encode($options) . ');', \yii\web\View::POS_READY, 'timeout');

}

		?>

<header>
     <a href="#page-title" class="skip-navigation sr-only sr-only-focusable">Skip Navigation</a>
	 <div class="navbar-header">
            <?php if($logo != ''){ ?>
                <a class="navbar-brand" href="<?=Yii::$app->homeUrl; ?>" title='<?php echo $logo_name?>'><?= Html::img("data:image/jpeg;base64,". base64_encode($logo),['title'=>$logo_name,'alt'=>$logo_name,'style'=>'height:37px']);?></a>
            <?php } else { ?>
                <a class="navbar-brand" href="<?=Yii::$app->homeUrl; ?>" title='<?php echo $logo_name?>'><?= Html::img('@web/images/logo_login.png',['title'=>$logo_name,'alt'=>$logo_name,'style'=>'height:37px']);?></a>
            <?php } ?>
 	 </div>
	 <div class="container">
            <nav class="navbar" role="navigation" aria-label="Main Navigation">
		<div id="isatask-nav-collapse" class="collapse navbar-collapse pull-right">
		  <ul id="main-top-side-bar" class="navbar-nav navbar-right nav">
			<li><span>Welcome, <?php
            	if((isset(Yii::$app->user->identity->usr_first_name) && Yii::$app->user->identity->usr_first_name!="") && (isset(Yii::$app->user->identity->usr_lastname) && Yii::$app->user->identity->usr_lastname!=""))
                    echo  ucwords(Yii::$app->user->identity->usr_first_name . " " . Yii::$app->user->identity->usr_lastname);
            	else
                    echo ucwords(Yii::$app->user->identity->usr_username);

            	if(Yii::$app->user->identity->usr_type==3){ echo  " (Active Directory)";}
            	 ?></span></li>
			<li class="<?php if($controller=='user' && $action=='options'){ echo 'active';}?>"><?= Html::a('<em class="fa fa-cogs" title="Options"></em> Options','@web/index.php?r=user/options',['title'=>'Options']) ?></li>
			<?php if ((new User)->checkAccess(12.01)) { ?>
			<li class="<?php if($controller=='site' && $action=='todaysactivity'){ echo 'active';}?>"><?= Html::a('<em class="fa fa-line-chart" title="Today\'s Activity"></em> Today\'s Activity','@web/index.php?r=site/todaysactivity',['title'=>'Today\'s Activity']) ?></li>
			<?php } ?>
			<?php
				$current_tabs="case";
				if ((in_array(1, $User_Role) && in_array(2, $User_Role)) || ($roleId == '0')){
							$current_tabs="case";
                        }
                        else if (in_array(1, $User_Role) || ($roleId == '0')) {
                        	$current_tabs="case";
                        }
                        else if (in_array(2, $User_Role) || ($roleId == '0')) {
                        	$current_tabs="team";
                        }
			?>
			<li class="<?php if($controller=='user' && $action=='events'){ echo 'active';}?>"><?= Html::a('<em class="fa fa-calendar" title="My Events"></em> My Events','@web/index.php?r=user/events&current_tabs='.$current_tabs,['title'=>'My Events Calendar']) ?></li>
			<?php if ((new User)->checkAccess(9))
                {  ?>
			<!--<li><?= Html::a('<em class="fa fa-question-circle"></em> Help','@web/help/index.php',['title'=>'Help']) ?></li>-->
			<?php }?>
			<li><?= Html::a('<em class="fa fa-sign-out" title="Logout"></em> Logout','javascript:void(0);',['title'=>'Logout','onclick'=>'$.fn.idleTimeout().logout();']) ?></li>
		  </ul>
		</div>
		<div id="isatask-top-collapse" class="collapse navbar-collapse">
		  <ul id="w11" class="nav nav-justified" role="menu">
			<?php if ((new User)->checkAccess(1)) { ?>
				<li <?php if($controller=='site' && $action=="index"){?>class="active"<?php }?>  role="menuitem" aria-grabbed="false" draggable="true"><?= Html::a('My Assignments','@web/index.php?r=site/index',['title'=>'My Assignments','onclick'=>'ActiveMainNavigation(this);']) ?></li>
			<?php } ?>
            <?php if ((new User)->checkAccess(3) && (new User)->checkAccess(3.009)) {?><li <?php if($controller=='media' && $action=="index"){?>class="active"<?php }?> role="menuitem" aria-grabbed="false" draggable="true"><?= Html::a('Sources','@web/index.php?r=media/index',['title'=>'Sources']) ?></li><?php }?>

			<?php if ((new User)->checkAccess(4)){ if ($has_access_4 && ($roleId == '0' || in_array(1,$User_Role))) { ?>
			<li <?php if($controller=='mycase' || $controller=='case-custodians' || $controller=='case-projects' || $controller=='project' || $controller == 'case-production' || $controller == 'case-overview' || $controller == 'case-budget' || $controller == 'case-documents' || $isCase){?>class="active"<?php }?> role="menuitem" aria-grabbed="false" draggable="true"><?= Html::a('My Cases','@web/index.php?r=mycase/index',['title'=>'My Cases']) ?></li>
			<?php } }?>

			<?php if ((new User)->checkAccess(5)){ if ($has_access_5 && ($roleId == '0' || in_array(2,$User_Role))) { ?>
				<li <?php if($controller=='team' || $controller == 'team-projects' || $controller == 'team-tasks' || $controller == 'team-documents' || $controller == 'team-overview' || $isTeam){?>class="active"<?php }?> role="menuitem" aria-grabbed="false" draggable="true"><?= Html::a('My Teams','@web/index.php?r=team/index',['title'=>'My Teams']) ?>
				</li><?php } } ?>
			<?php if ((new User)->checkAccess(2)) {?><li <?php if($controller=='global-projects'){?>class="active"<?php }?> role="menuitem" aria-grabbed="false" draggable="true"><?= Html::a('Global Projects','@web/index.php?r=global-projects/index',['title'=>'Global Projects']) ?></li><?php }?>
			<?php if ((new User)->checkAccess(7) && (((new User)->checkAccess(7.01) && ((new User)->checkAccess(7.02) || (new User)->checkAccess(7.04) || (new User)->checkAccess(7.06) || (new User)->checkAccess(7.08))) || (new User)->checkAccess(7.10) || (new User)->checkAccess(7.12) || (new User)->checkAccess(7.15) || (new User)->checkAccess(7.19))){ ?>
				<li <?php if($controller=='billing-pricelist' || $controller=="billing-taxes" || $controller=="billing-generate-invoice" || $controller=="billing-finalized-invoice" || $controller=="billing-closed-invoice"){?>class="active"<?php }?> role="menuitem" aria-grabbed="false" draggable="true">
				<?php if ((new User)->checkAccess(7.01) && ((new User)->checkAccess(7.02) || (new User)->checkAccess(7.04) || (new User)->checkAccess(7.06) || (new User)->checkAccess(7.08))){
						if ((new User)->checkAccess(7.02)){  ?>
							<?= Html::a('Billing','@web/index.php?r=billing-pricelist/internal-team-pricing',['title'=>'Billing']) ?>
					<?php } else if((new User)->checkAccess(7.04)){ ?>
							<?= Html::a('Billing','@web/index.php?r=billing-pricelist/internal-shared-pricing',['title'=>'Billing']) ?>
					<?php } else if((new User)->checkAccess(7.08)){ ?>
						<?= Html::a('Billing','@web/index.php?r=billing-pricelist/get-preferred-pricing&type=client',['title'=>'Billing']) ?>
					<?php } else if((new User)->checkAccess(7.06)){ ?>
							<?= Html::a('Billing','@web/index.php?r=billing-pricelist/get-preferred-pricing&type=case',['title'=>'Billing']) ?>
					<?php }
					} else if((new User)->checkAccess(7.10)){ ?>
						<?= Html::a('Billing','@web/index.php?r=billing-taxes/tax-classes',['title'=>'Billing']) ?>
					<?php } else if((new User)->checkAccess(7.12)){ ?>
						<?= Html::a('Billing','@web/index.php?r=billing-generate-invoice/billing-invoice-management',['title'=>'Billing']) ?>
					<?php } else if((new User)->checkAccess(7.15)){ ?>
						<?= Html::a('Billing','@web/index.php?r=billing-finalized-invoice/finalized-invoices',['title'=>'Billing']) ?>
					<?php } else if((new User)->checkAccess(7.19)){ ?>
						<?= Html::a('Billing','@web/index.php?r=billing-closed-invoice/closed-invoices',['title'=>'Billing']) ?>	
					<?php } } ?>
				</li>
				<?php if ((new User)->checkAccess(11)) {
					$report_url = "javascript:void(0);";
						if((new User)->checkAccess(11.1)) {
							$report_url = '@web/index.php?r=custom-report/index';
						}
						elseif((new User)->checkAccess(11.2)) {
							$report_url = '@web/index.php?r=saved-report/index';
						}
					}?>
			<?php if ((new User)->checkAccess(11)) {?><li <?php if(($controller=='custom-report' || $controller=='saved-report') && ($action=='index' || $action=='edit-savereport' || $action=='run-savereport' )){?>class="active"<?php }?> role="menuitem" aria-grabbed="false" draggable="true"><?= Html::a('Reports',$report_url,['title'=>'Reports']) ?></li><?php }?>
			<?php if ((new User)->checkAccess(8)){?><li <?php if($controller=='site' && $action=="administration"){?>class="active"<?php }?> role="menuitem" aria-grabbed="false" draggable="true"><?= Html::a('Administration','@web/index.php?r=site/administration',['title'=>'Administration']) ?></li><?php }?>
		  </ul>
		</div>
		</nav>
	  </div>
</header>
<script>
var baseUrl = '<?php echo Yii::$app->request->baseUrl;?>/index.php?r=';
var AdminFormBuilderbaseUrl = '<?php echo Yii::$app->request->baseUrl;?>';
</script>
<noscript></noscript>
