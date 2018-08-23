<?php

/* @var $this yii\web\View */

use yii\helpers\Html;
use app\models\User;

$this->title = 'Administration';
$this->params['breadcrumbs'][] = $this->title;
//\app\assets\AppAsset::register($this);
?>
<script>
	<?php if ((new User)->checkAccess(8.023)) {?>
	commonAjax(baseUrl +'/system/projectsort','admin_main_container');
	jQuery('[data-module="project_sort"]').addClass("active");
	setTitle('fa-wrench','<a href="javascript:void(0);" title="System Management - Project Sort Display" class="tag-header-red">System Management - Project Sort Display</a>');
	<?php } ?>
</script>
<noscript></noscript>
