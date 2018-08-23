<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use kartik\grid\GridView;
use kartik\grid\datetimepicker;
use yii\web\JsExpression;
use app\models\User;
use app\models\TaskInstructEvidence;
use app\models\Tasks;
/* @var $this yii\web\View */
/* @var $searchModel app\models\search\EvidenceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$user_model=new User();
?>
<div class="table-responsive">
    
    <table class="table table-striped table-hover">
      <thead>
       <tr>
			<th id="detail_chkbox" class="detail_chkbox" scope="col">&nbsp;</th>
			<th id="detail_media" class="detail_media" scope="col"><a href="javascript:void(0);" class="tag-header-black tag-header-cursor-default" title="Media #">Media #</a></th>
			<th id="detail_hold" class="detail_media" scope="col"><a href="javascript:void(0);" class="tag-header-black tag-header-cursor-default" title="Hold">Hold</a></th>
			<th id="detail_desc" class="detail_desc text-left" scope="col"><a href="javascript:void(0);" class="tag-header-black tag-header-cursor-default" title="Media Description">Media Description</a></th>
			<th id="detail_qty" class="detail_qty" scope="col"><a href="javascript:void(0);" class="tag-header-black tag-header-cursor-default" title="Quantity">Quantity</a></th>
			<th id="detail_size" class="detail_size text-left" scope="col"><a href="javascript:void(0);" class="tag-header-black tag-header-cursor-default" title="Media Size">Media Size</a></th>
			<th id="detail_size_comp" class="detail_size text-left" scope="col"><a href="javascript:void(0);" class="tag-header-black tag-header-cursor-default" title="Media Size Comp">Media Size Comp</a></th>
			<th id="detail_softbates" class="detail_softbates text-left" scope="col"><a href="javascript:void(0);" class="tag-header-black tag-header-cursor-default" title="Soft Bates">Soft Bates</a></th>
			<th id="detail_custodian" class="detail_custodian text-left" scope="col"><a href="javascript:void(0);" class="tag-header-black tag-header-cursor-default" title="Media Custodians">Media Custodians</a></th>
			<th id="detail_prno" class="detail_prno text-left" scope="col"><a href="javascript:void(0);" class="tag-header-black tag-header-cursor-default" title="Project #">Project #</a></th>
			<th id="detail_prodbates" class="detail_prodbates text-left" scope="col"><a href="javascript:void(0);" class="tag-header-black tag-header-cursor-default" title="Production Bates">Production Bates</a></th>
			<th id="detail_vol" class="detail_vol text-left" scope="col"><a href="javascript:void(0);" class="tag-header-black tag-header-cursor-default" title="Production Volume">Production Volume</a></th>
			<th id="detail_proddate" class="detail_proddate text-left" scope="col"><a href="javascript:void(0);" class="tag-header-black tag-header-cursor-default" title="Prod Date Loaded">Prod Date Loaded</a></th>
			<th id="detail_blank" scope="col" colspan="5">&nbsp;</th>
	    </tr>
      </thead>
      <tbody>
	  <?php
	      if (!empty($prod_data)) {
			$i=0;
            foreach ($prod_data as $prod) {
				//echo "<pre>"; print_r($prod_data); exit;
				if (!empty($prod->prodbates)) {
				   // echo "<pre>"; print_r($prod->prodbates); exit;                
                    foreach($prod->prodbates as $m_new){ //echo "<pre>"; print_r($m_new); die; ?>
						<tr>
                            <td headers="detail_chkbox" align="center" class="detail_chkbox">
								<input type="checkbox" tabindex="<?php echo $m_new->id ?>" data-id="<?php echo $prod->id ?>"  id="media_row_<?php echo $m_new->id ?>_<?php echo $m_new->prod_id ?>" rel="<?php echo $prod->prod_id ?>" value="<?php echo $prod->evid_id ?>" class="media_datas" name="media[]" aria-label="Select Media #<?php echo $prod->evid_id ?>" >
                                <label for="media_row_<?php echo $m_new->id ?>_<?php echo $m_new->prod_id ?>">&nbsp;</label>
							  </td>
							  <td headers="detail_media" align="center" class="detail_media"><?php
								  if ($user_model->checkAccess(3)) { ?>
                                    <a href="javascript:go_toMedia('<?php echo $prod->evid_id ?>');" style="margin-left:5px;" title="Media #<?php echo $prod->evid_id; ?>"><?php echo $prod->evid_id; ?></a>                            	
								<?php } else {	  
								   echo $prod->evid_id; } ?>
							   </td>
							  <td headers="detail_hold" align="center" class="detail_hold">
								<?php if (isset($prod->on_hold) && $prod->on_hold) { 
										echo '<a href="javascript:void(0); class="icon-fa" title="On Hold"><em title="On Hold" class="fa fa-check text-danger"></em></a>';                                         
									} 
                                 ?>
							  </td>
							  <td headers="detail_desc" class="detail_desc" align="left"><?php echo $prod->proevidence->evid_desc ?></td>
							  <td headers="detail_qty" class="detail_qty" align="center"><?php echo $prod->proevidence->quantity ?></td>
							  <td headers="detail_size" class="detail_size" align="left">
								<?php if ($prod->proevidence->contents_total_size != "" && $prod->proevidence->contents_total_size != 0) echo $prod->proevidence->contents_total_size . " " . $prod->proevidence->evidenceunit->unit_name; ?>
							  </td>
							  <td headers="detail_size_comp" class="detail_size" align="left">
								<?php if ($prod->proevidence->contents_total_size_comp != "" && $prod->proevidence->contents_total_size_comp != 0) echo $prod->proevidence->contents_total_size_comp . " " . $prod->proevidence->evidencecompunit->unit_name; ?>
							  </td>
							  <td headers="detail_softbates" class="detail_softbates" align="left">
							  <?php
                                    $hasbates = false;
                                    if (isset($prod->proevidence->bbates) && $prod->proevidence->bbates != "") {
                                        echo $prod->proevidence->bbates;
                                        $hasbates = true;
                                    } if ($hasbates == true && isset($prod->proevidence->ebates) && $prod->proevidence->ebates != "") {
                                        echo " - " . $prod->proevidence->ebates;
                                    } else if (isset($prod->proevidence->ebates) && $prod->proevidence->ebates != "") {
                                        echo $prod->proevidence->ebates;
                                    }
                                    ?>
                             </td>  
                              <td headers="detail_custodian" class="detail_custodian">
								 <?php 
								 $xyz = array();
								 $h_count = 1; 
									foreach($prod->proevidence->evidencecontent as $econtents){
										if($h_count == 1){
											 $econtents->evidenceCustodians->cust_lname . ", " . $econtents->evidenceCustodians->cust_fname . " " . $econtents->evidenceCustodians->cust_mi;
										}else{
											 $econtents->evidenceCustodians->cust_lname . ", " . $econtents->evidenceCustodians->cust_fname . " " . $econtents->evidenceCustodians->cust_mi.'; ';										}
											 $h_count++; 
										 $xyz[] = $econtents->evidenceCustodians->cust_lname . ", " . $econtents->evidenceCustodians->cust_fname . " " . $econtents->evidenceCustodians->cust_mi;
									 } echo implode('; ',$xyz);  ?>
							 </td>   
                              <td headers="detail_prno" class="detail_prno"><?php
									if (!empty($m_new->task)){ 
										if($user_model->checkAccess(4.01)){
										  	if(isset($task_arr[$m_new->task_id]))
												echo $task_arr[$m_new->task_id];
										} else {
											echo $m_new->task_id; 
										}?>
									<input type="hidden" id="evid_prod_beats_<?php echo $prod->evid_id ?>_<?php echo $m_new->id ?>" value="<?php echo $m_new->task_id; ?>"> 
									 <?php } ?>
                             </td>    
                              <td headers="detail_prodbates" class="detail_prodbates" align="left">
                                 <?php
                                    $haspbates = false;
                                    if (isset($m_new->prod_bbates) && $m_new->prod_bbates != "") {
                                        echo $m_new->prod_bbates;
                                        $haspbates = true;
                                    } if ($haspbates && isset($m_new->prod_ebates) && $m_new->prod_ebates != "") {
                                        echo " - " . $m_new->prod_ebates;
                                    } else if (isset($m_new->prod_ebates) && $m_new->prod_ebates != "") {
                                        echo " - " . $m_new->prod_ebates;
                                    }
                                    ?>
                             </td>
							<td headers="detail_vol" class="detail_vol" align="left"><?php echo $m_new->prod_vol; ?></td>
							<td headers="detail_proddate" class="detail_proddate" align="left">
								<?php
									if (isset($m_new->prod_date_loaded) && date("Y-m-d", strtotime($m_new->prod_date_loaded)) != "1970-01-01") {
										echo date('m/d/Y', strtotime($m_new->prod_date_loaded));
									}
								?>
							</td>
							<td headers="detail_blank" align="left" colspan="5">&nbsp; </td>
					</tr>
				<?php
					}
				}
				else{ //echo "<pre>"; print_r($prod); die; 
				//echo "<pre>",print_r($prod->prodbates->attributes),"</pre>";
					?> 
					<tr>
						  <td  headers="detail_chkbox" class="detail_chkbox" align="center">
								<input type="checkbox" tabindex=0 data-id="<?php echo $prod->id ?>"  id="media_row_<?php echo $prod->evid_id ?>_<?php echo $prod->id ?>" rel="<?php echo $prod->prod_id; ?>" value="<?php echo $prod->evid_id ?>" class="media_datas" name="media[]" aria-label="Select Media #<?php echo $prod->evid_id ?>">
                                                                <label for="media_row_<?php echo $prod->evid_id ?>_<?php echo $prod->id ?>"><span class="sr-only">Select Media #<?php echo $prod->evid_id ?></span></label>
						  </td>
						  <td headers="detail_media" align="center" class="detail_media">
							<?php
								  if ($user_model->checkAccess(3)) { ?>
										<a title ="Media #<?php echo $prod->evid_id; ?>" href="javascript:go_toMedia('<?php echo $prod->evid_id ?>');" style="margin-left:5px;"><?php echo $prod->evid_id; ?></a>                            	
								<?php }else{	  
								   echo $prod->evid_id; }?>
						  </td>
						  <td  headers="detail_hold" class="detail_hold"align="center">
							<?php if (isset($prod->on_hold) && $prod->on_hold) { 
									echo '<a href="javascript:void(0); class="icon-fa" title="On Hold"><em class="fa fa-check text-danger"></em></a>';                                         
								} 
							?>
						  </td>
						  <td headers="detail_desc" class="detail_desc" align="left"><?php echo $prod->proevidence->evid_desc ?></td>
						  <td headers="detail_qty" class="detail_qty" align="center"><?php echo $prod->proevidence->quantity ?></td>
						  <td headers="detail_size" class="detail_size" align="left">
							<?php if ($prod->proevidence->contents_total_size != "" && $prod->proevidence->contents_total_size != 0) echo $prod->proevidence->contents_total_size . " " . $prod->proevidence->evidenceunit->unit_name; ?>
						  </td>
						  <td headers="detail_size_comp" class="detail_size" align="left">
							<?php if ($prod->proevidence->contents_total_size_comp != "" && $prod->proevidence->contents_total_size_comp != 0) echo $prod->proevidence->contents_total_size_comp . " " . $prod->proevidence->evidencecompunit->unit_name; ?>
						  </td>		
						  <td headers="detail_softbates" class="detail_softbates" align="left">
							  <?php
                                    $hasbates = false;
                                    if (isset($prod->proevidence->bbates) && $prod->proevidence->bbates != "") {
                                         echo $prod->proevidence->bbates;
                                        $hasbates = true;
                                    } if ($hasbates == true && isset($prod->proevidence->ebates) && $prod->proevidence->ebates != "") {
                                        echo " - " . $prod->proevidence->ebates;
                                    } else if (isset($prod->proevidence->ebates) && $prod->proevidence->ebates != "") {
                                        echo $prod->proevidence->ebates;
                                    }
                                    ?>
                             </td>  
                          <td headers="detail_custodian" class="detail_custodian">
								<?php 
								$xyz1 = array();
								$h_cou = 1; 
								  foreach($prod->proevidence->evidencecontent as $econtents){
									  if($h_cou == 1){
										$econtents->evidenceCustodians->cust_lname . ", " . $econtents->evidenceCustodians->cust_fname . " " . $econtents->evidenceCustodians->cust_mi;
									  }else{
										$econtents->evidenceCustodians->cust_lname . ", " . $econtents->evidenceCustodians->cust_fname . " " . $econtents->evidenceCustodians->cust_mi.'; ';
									  } $h_cou++;
									  $xyz1[] = $econtents->evidenceCustodians->cust_lname . ", " . $econtents->evidenceCustodians->cust_fname . " " . $econtents->evidenceCustodians->cust_mi;
									 } echo implode('; ',$xyz1); ?>
                             </td>    
                          <td headers="detail_prno" class="detail_prno">
                             <?php $project_ids=TaskInstructEvidence::find()->where('evidence_id = '.$prod->evid_id.' AND prod_id = '.$prod->prod_id)->all();
                             		if(!empty($project_ids)){
                             			foreach ($project_ids as $project_id){
                             				if($user_model->checkAccess(4.01)){
                             					$task_info=Tasks::findOne($project_id->taskInstruct->task_id);
                             					if($task_info->task_cancel){
                             						echo Html::a($project_id->taskInstruct->task_id,null,['title'=>'Project #'.$project_id->taskInstruct->task_id,"href"=>Url::toRoute(['case-projects/load-canceled-projects', 'case_id' => $task_info->client_case_id, 'task_id' => $project_id->taskInstruct->task_id])]);
                             					}
                             					else if($task_info->task_closed){
                             						echo Html::a($project_id->taskInstruct->task_id,null,['title'=>'Project #'.$project_id->taskInstruct->task_id,"href"=>Url::toRoute(['case-projects/load-closed-projects', 'case_id' => $task_info->client_case_id, 'task_id' => $project_id->taskInstruct->task_id])]);
                             					}else{
                             						echo Html::a($project_id->taskInstruct->task_id,null,['title'=>'Project #'.$project_id->taskInstruct->task_id,"href"=>Url::toRoute(['case-projects/index', 'case_id' => $task_info->client_case_id, 'task_id' => $project_id->taskInstruct->task_id])]);
                             					}
                             				}else{
                             					echo $project_id->taskInstruct->task_id;
                             				}?>
                             			<?php }
                             		} ?>
                             <input type="hidden" id="evid_prod_beats_<?php echo $prod->evid_id ?>_0" value=""> 
                             </td> 
							<td headers="detail_prodbates" align="left">&nbsp; </td>
							<td headers="detail_vol" align="left">&nbsp; </td>
							<td headers="detail_proddate" align="left">&nbsp; </td>
							<td headers="detail_blank" align="left" colspan="5">&nbsp; </td>
					</tr>			
			<?php }
			}
	   } else {
            echo "<tr><td colspan='12' headers=''>No Records found...</td></tr>";
        } ?>
      </tbody>
    </table>
</div>
<script> $('input').customInput();</script>
<noscript></noscript>
