<?php 
use app\models\FormBuilder;
use app\models\InvoiceFinal;
?>

<div id="form_builder_panel" class="ViewCustodianForm" style="color:#333; font-family:Arial;">
  <form method="post" action="#" class="fancy" id="formb" style="margin:0px;" autocomplete="off">
    <?php 
					$i=1;
					if(!empty($custodiants['cust_data'])){
					$custodian_count = count($custodiants['cust_data']);
					foreach($custodiants['cust_data'] as $cust) { 
						
				?>
    <div style="background:#c52d2e; padding:7px 10px; font-size:12px; font-weight:bold; color:#FFF; margin:0px 0px 5px; font-family:Arial;">Custodian Interview Form</div>
    <div style="overflow:hidden; float:left; width:100%; padding:0px 0px 5px;">
      <div style="background:#e9e7e8; color:#333; font-size:11px; margin:0px 0px 5px; padding:7px 10px; position:relative;">Custodian Details </div>
	  
      <div style="float:left; width:100%; font-size:10px;">
        <div style="float:left; width:45%; padding:2px 0px 2px 10px;"><strong>Client</strong></div>
        <div style="float:left; padding:2px 0px;"><?php echo $client_data->client->client_name;?></div>
      </div>
      <div style="float:left; width:100%; font-size:10px;">
        <div style="float:left; width:45%; padding:2px 0px 2px 10px;"><strong>Case</strong></div>
        <div style="float:left; padding:2px 0px;"><?php echo $client_data->case_name;?></div>
      </div>
      <div style="float:left; width:100%; font-size:10px;">
        <div style="float:left; width:45%; padding:2px 0px 2px 10px;"><strong>Custodian</strong></div>
        <div style="float:left; padding:2px 0px;"><?php echo $cust->cust_fname." ".$cust->cust_mi." ".$cust->cust_lname;?></div>
      </div>
      <div style="float:left; width:100%; font-size:10px;">
        <div style="float:left; width:45%; padding:2px 0px 2px 10px;"><strong>Title</strong></div>
        <div style="float:left; padding:2px 0px;"><?php echo $cust->title;?></div>
      </div>
      <div style="float:left; width:100%; font-size:10px;">
        <div style="float:left; width:45%; padding:2px 0px 2px 10px;"><strong>Department</strong></div>
        <div style="float:left; padding:2px 0px;"><?php echo $cust->dept;?></div>
      </div>
    </div>
    <div style="clear:both; float:left; width:100%;">
      <div style="background:#e9e7e8; color:#333; font-size:11px; margin:5px 0px 0px; padding:7px 10px; position:relative;">Interview Questions </div>
      <div style="margin:0px; padding:5px 8px; font-size:10px; font-family:Arial;">
        <table style="border:none; width:100%;">
          <?php 
			if(!empty($custodiants['formbuilder_data'][$cust->cust_id])){
				foreach ($custodiants['formbuilder_data'][$cust->cust_id] as $ele_id=>$frm_data){
					if(($frm_data['remove'] == 1 && $custodiants['formValues'][$cust->cust_id][$frm_data['form_builder_id']] != '') ||  $frm_data['remove'] == 0){ 
			?>
          <tr>
            <td style="width:45%; padding:2px 0px; font-family:Arial;"><strong style="color:#333; font-size:10px;"> <?php echo $frm_data['label'] != ""?$frm_data['label']:'&nbsp;'; ?> </strong></div></td>
            <td style="padding:2px 0px; font-family:Arial;"><div class="value" style="color:#333; font-size:10px;">
                <?php 
												if($frm_data['type'] == 'dropdown' || $frm_data['type'] == 'radio' || $frm_data['type'] == 'checkbox' ){
													$value=(new FormBuilder)->getSelectedOptionText($cust->cust_id,$frm_data['form_builder_id'],3);
													echo (new InvoiceFinal)->smart_wordwrap(implode(", ",$value), 20);
												}else if($frm_data['type']=='textarea'){
													echo (new InvoiceFinal)->smart_wordwrap(nl2br($custodiants['formValues'][$cust->cust_id][$frm_data['form_builder_id']]),20); 
												}else if($frm_data['type'] == 'datetime'){
													echo str_replace('-','/',$custodiants['formValues'][$cust->cust_id][$frm_data['form_builder_id']]);
												}else if($frm_data['type'] == 'number'){
													echo $custodiants['formValues'][$cust->cust_id][$frm_data['form_builder_id']].' '.$custodiants['unitValues'][$cust->cust_id][$frm_data['form_builder_id']];
												}else{
													echo (new InvoiceFinal)->smart_wordwrap($custodiants['formValues'][$cust->cust_id][$frm_data['form_builder_id']],20);
												}
												?>
              </div></td>
          </tr>
          <?php }}}?>
        </table>
      </div>
    </div>
    <?php 
					if($custodian_count > $i)
						echo "<pagebreak />";
					
					$i++;	
					} }
				?>
  </form>
</div>
