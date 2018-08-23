<?php
use yii\helpers\Html;
use yii\helpers\Url;
/*$userclick = 'javascript:UserDetails('.$model->id.');';
$ids = 'user_'. $i;

if($from == 'useraccess'){ */
	$userclick = "javascript:UserDetailsAccess(".$model->id.",'".$model->role['role_type']."');";
	$ids = 'user_access_'. $i;
//}
$nameUsers = '';
if($model->usr_lastname == '' && $model->usr_first_name == '')
    $nameUsers = Html::encode($model->usr_username);
else 
    $nameUsers = Html::encode($model->usr_first_name).' '.Html::encode($model->usr_lastname);
?>
<li class="userlist" id="userlist_<?=$model->id?>">
	<a href="javascript:void(0);" onclick="<?= $userclick ?>" title="<?=$nameUsers?>">
		<em title="<?=$nameUsers?>" class="fa fa-user<?php if($model->status !=1){?>-times<?php }?>  <?php if($model->usr_type == '3') {?> text-primary <?php } else if($model->usr_type == '1') {?> text-gray <?php } else{?> text-danger<?php }?>"></em> 
		<?=$nameUsers?>                
	</a>
	<?php if($role_id != '' && $role_id != 0){ ?>
	<input type="checkbox" class="userschkall" name="users[]" id="<?= $ids ?>" value="<?= $model->id?>" />	
        <label for="<?= $ids ?>" class="usersChk" ><span class="sr-only"><?=$nameUsers?></span></label>
	<?php } ?>
</li>

