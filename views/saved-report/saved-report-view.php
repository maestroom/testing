<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use kartik\grid\GridView;
use yii\web\JsExpression;
use app\models\Options;

/* @var $this yii\web\View */
/* @var $model app\models\ReportsUserSaved */
$this->title = "Report Saved User View";
$this->params['breadcrumbs'][] = $this->title;
?>
<table class="table table-striped">
	<thead>
		<tr>
			<?php $column_names = array(); foreach($column_display_data as $key => $column){ $fieldexplode = explode(".",$column_data[$key]); if($fieldexplode[0]=='Calc'){ $column_names[$key] = str_replace(" ","_",strtolower($fieldexplode[1])); } else { $column_names[$key] = $fieldexplode[1]; }?>
				<th><?php echo $column; ?></th>
			<?php } ?>
		</tr>
	</thead>
	<tbody>
<?php 
if(!empty($report_data)){
	foreach($report_data as $k => $data){ 
?>
<tr>
	<?php 
	if(!empty($column_names)){
		foreach($column_names as $col_type => $column){ 
	?>
		<td>
		<?php 
			if(isset($reportTypeFields[$col_type]) && $reportTypeFields[$col_type] == 'DATETIME'){
				echo $data[$column] != '0000-00-00 00:00:00' && $data[$column] != NULL?(new Options)->ConvertOneTzToAnotherTz($data[$column], 'UTC', $_SESSION['usrTZ']):''; 
			} else {
				if(isset($data[$column]) && $data[$column]!=''){
					echo htmlspecialchars($data[$column]);
				}
			}
		?>
		</td>
	<?php 
		} 
	} 
	?>
</tr>
<?php 
	} 
} else {
?>
<tr>
	<td colspan="<?= count($column_names); ?>" align="center">No Records Found</td>
</tr>	
<?php	
}
?>
</tbody>
</table>

