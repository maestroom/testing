<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Media */

$this->title = 'Perform Barcode Transaction';
$this->params['breadcrumbs'][] = ['label' => 'Evidences', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
    <div class="sub-heading" class="two-cols-fieldset"><?= Html::encode($this->title) ?></div>
    <div id="form_div"  class="two-cols-fieldset">
        <?= $this->render('_formcheckinoutbarcode', [
		    'model' => $model,
		    'listUser' => $listUser,
		    'listEvidenceLoc' => $listEvidenceLoc,
		    'listEvidenceTo'=>$listEvidenceTo
		]) ?>
    </div>

<script type="text/javascript">
	 $('#move_to').hide();
	 $('#spn_Apply_Transaction_to_both').hide();
	 $('#trans_to').hide();
	$('#evidencetransaction-trans_type').change(function(){
		var str_val=$(this).val();
		if(str_val==4)
			$('#move_to').show();
		else
			$('#move_to').hide();
			
		if(str_val==3)
			$('#spn_Apply_Transaction_to_both').show();
		else
			$('#spn_Apply_Transaction_to_both').hide();
			
		if(str_val==2 || str_val==5)
			$('#trans_to').show();
		else
			$('#trans_to').hide();		
	});
</script>
<noscript></noscript>
