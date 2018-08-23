<?php 
use kartik\widgets\Select2; 
kartik\select2\Select2Asset::register($this);
kartik\select2\ThemeKrajeeAsset::register($this);

$unitConversionSize = Yii::$app->params['unit_conversion_size'];
?>

<div class="sub-heading pos-relative"><a href="javascript:void(0);" title="Edit <?= $type ?> Conversions" class="tag-header-black">Edit <?= $type ?> Conversions</a></div>
<div class="sub-heading pos-relative"><a href="javascript:void(0);" title="Add Units to Convert to <?= $unitConversionSize[$type] ?>" class="pull-left tag-header-black">Add Units to Convert to <?= $unitConversionSize[$type] ?></a><a href="javascript:void(0);" class="pull-right" onclick="addMoreUnits()" aria-label="add More Units" title="Add More Units"><em title="Add More Units" class="fa fa-plus"></em></a></div>
<?php //echo "<pre>",print_r($unitConversionSize),"</pre>"; die(); ?>
<fieldset class="one-cols-fieldset units-table-grid">
        <legend class="sr-only">Add Units to Convert to <?= $unitConversionSize[$type] ?></legend>    
	<form id="frmUnitsList" method="post" autocomplete="off">
            	<table id="UnitsList" class="table table-striped">
			<thead>
                            <tr>
                                <th width="20%">Unit</th>
                                <th width="20%">Unit Conversion Size</th>
                                <th width="30%">Unit Conversion Size Name</th>
                                <th width="23%">Unit Convert Reports</th>
								<th width="2%">Action</th>
                            </tr>
			</thead>
			<tbody>
				<?php 
				if($unitMaster) { $i=1;
                                        $total = count($unitMaster);
					foreach($unitMaster as $unit) {
                                            $uniqID = uniqid();
				?>
                            <tr>
                                <td width="20%"><label for="<?= $uniqID ?>"><span class="sr-only">Unit Conversion Size,Unit</span><span><?= $unit->unit->unit_name ?></span></label><input type="hidden" name="UnitMaster[unit_id][]" value="<?= $unit->unit_id ?>"/></td>
                                <td width="25%"><input id="<?= $uniqID ?>" type="text" class="form-control" name="UnitMaster[unit_size][]" value="<?= $unit->unit_size ?>" /></td>
                                <td width="30%"><?= $unitConversionSize[$type] ?></td>
                                <td width="20%">
                                    <input type="radio" aria-setsize="<?= $total ?>" aria-posinset="<?= $i ?>" id="unit_convert_report-<?= $uniqID ?>" name='unit_convert_report' <?php if ($unit->unit_convert_report == 1) { ?> checked="checked" <?php } ?> value="<?= $unit->unit_id ?>" >
                                    <label for="unit_convert_report-<?= $uniqID ?>" class=""><span class="sr-only">Unit Convert Reports,Unit,<?= $unit->unit->unit_name ?></span></label>
								</td>
								<td width="2%">
									<?php if($unit->default_unit==0){?>
										<a class="icon-set" href="javascript:DeleteUnitMaster(<?php echo $unit->id?>);" title="Delete" aria-label="Remove"><em style="font-size:14px;" class="fa fa-close text-primary"></em></a>
									<?php }?>
								</td>
                            </tr>
				<?php $i++; }
				}
				?>
			</tbody>
		</table>
	</form>
	<div style="display:none;">
		<?php echo Select2::widget([
			'name' => 'UnitMaster[unit_id][]',
			'attribute' => 'units',
			'data' => array(),
			'options' => ['prompt' => 'Select Unit','title' => 'Select Unit', 'class' => 'form-control','id'=>'units','nolabel'=>true],
		]) ?>
	</div>
</fieldset>	
<div class="button-set text-right">
	<button class="btn btn-primary" title="Cancel" onclick="SelectUnitConversion('<?= $type ?>');">Cancel</button>
	<button class="btn btn-primary" title="Update" onclick="UpdateUnitMaster();">Update</button>
</div>
<script>
$('input').customInput();
function addMoreUnits(){	
	var unit_type = '<?= $type ?>';
	$.ajax({
		url:baseUrl +'/site/add-more-units-view&typeconversion='+unit_type,
		type:'get',
		beforeSend:function(){showLoader();},
		success:function(response){
			$('table#UnitsList tbody').append(response);
			$('table#UnitsList tbody tr:last').find('select').select2({
				theme: "krajee"
			}).focus();

		},
		complete:function(){hideLoader();$('input').customInput();}
	});
}
function UpdateUnitMaster(){
	var unit_type = '<?= $type ?>';
	$.ajax({
		url:baseUrl +'/site/upadte-units-master&typeconversion='+unit_type,
		type:'post',
		data:$('#frmUnitsList').serialize(),
		beforeSend:function(){showLoader();},
		success:function(response){
			if(response == 'OK'){
				SelectUnitConversion(unit_type);
			} else {
				alert('something went wrong, please try again');
			}
		},
		complete:function(){hideLoader();}
	});
}
function DeleteUnitMaster(id){
	var unit_type = '<?= $type ?>';
	if(confirm("Are You Sure you want to delete this record?")){
		$.ajax({
			url:baseUrl +'/site/delete-units-master',
			type:'post',
			data:{'id':id},
			beforeSend:function(){
				showLoader();
			},
			success:function(response){
				if(response == 'OK'){
					SelectUnitConversion(unit_type);
				} else {
					alert('something went wrong, please try again');
				}
			},
			complete:function(){hideLoader();}
		});
	}
}
</script>
<noscript></noscript>
