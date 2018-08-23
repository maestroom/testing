<?php
use app\models\Options;
$params = Yii::$app->params['task_status'];
?>
<div class="table-responsive">
	<?= $this->render('preview-report-table', ['selected_field_keys'=>$selected_field_keys,'format'=>$format,'change_ids'=>$change_ids,'report_data'=>$report_data, 'column_data' => $column_data,'reportTypeFields'=>$reportTypeFields,'column_display_data' => $column_display_data,'column_data_alias' => $column_data_alias]);  ?>
</div>
