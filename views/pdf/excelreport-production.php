<?php use app\models\User;?>
<table cellpadding="0" cellspacing="0" width="100%" border="5">
    <tr>
        <td  style="border: 3px red solid;" align="left">DOCUMENT PRODUCTION LOG</td>
        <td colspan="26">&nbsp;</td>
    </tr>
    <tr>
        <td>Case Name</td>
        <td><?php echo strtoupper(html_entity_decode($case_data->client->client_name . ' - ' . $case_data->case_name)); ?></td>
        <td colspan="24">&nbsp;</td>
    </tr> 
    <tr>
        <td colspan="26">&nbsp;</td>
    </tr>
    <tr>
        <td>Production Type</td>
        <td>Staff Assigned</td>
        <td>Production Date</td>
        <td>Date Received</td>
        <td>Producing Party</td>
        <td>Production Description</td>
        <td>Cover Letter Link</td>
        <td colspan="13">
            <table cellpadding="0" cellspacing="0" width="100%" border="1"><tr>
                <td>Project #</td>
                <td>Media Type</td>
                <td>Media Description</td>
                <td>Media Label</td>
                <td>Media Custodians</td>
                <td>Media Quantity</td>
                <td>Media Size</td>
                <td>Media Size Compressed</td>
                <td>Begin Bates</td>
                <td>End Bates</td>
                <td>Prod Begin Bates</td>
                <td>Prod End Bates</td>
                <td>Prod Date Loaded</td>
            </tr></table>
        </td>
        <td>Production Contains Originals</td>
        <td>Return Production</td>
        <td>Attorney Notes</td>
        <td>Produced in Initial Disclosures</td>
        <td>Produced to Other Agencies</td>
        <td>Access Request</td>
        <td>Misc1</td>
        <td>Misc2</td>
    </tr>
    <?php
  
    $user_model=new User();
    if (count($productionlogdata) > 0) {
        $j = 1;
        foreach ($productionlogdata as $logdata) {
            $tdcount = 1;
            if (isset($logdata->has_media) && ($logdata->has_media)) {
                $tdcount = count($media_ids);
                if (!empty($media_bates_new))
                    $tdcount = count($media_bates_new);
            }
            
            $k=0;
             if (!empty($prod_media_data[$logdata->id])) {
                foreach ($prod_media_data[$logdata->id] as $prod) {
                    if (!empty($prod->prodbates)) {
                        foreach ($prod->prodbates as $m_new) {$k++;}
                    }else{$k++;}
                }
            } 
            if($k==0){$k=1;}
            $k=1;
            ?>
            <tr>
                <td align="left" rowspan="<?php echo $k;?>"><?php
                    if ($logdata->production_type == 1)
                        echo "Incoming".$logdata->id;
                    else
                        echo "Outgoing".$logdata->id;
                    ?>
                </td>
                <td align="left" rowspan="<?php echo $k;?>" ><?php echo $logdata->staff_assigned; ?></td>
                <td align="left" rowspan="<?php echo $k;?>" ><?php echo date('m/d/Y', strtotime($logdata->prod_date)); ?></td>
                <td align="left" rowspan="<?php echo $k;?>" ><?php echo date('m/d/Y', strtotime($logdata->prod_rec_date)); ?></td>
                <td align="left" rowspan="<?php echo $k;?>" ><?php echo $logdata->prod_party; ?></td>
                <td align="left" rowspan="<?php echo $k;?>" ><?php echo $logdata->production_desc ?></td>
                <td align="left" rowspan="<?php echo $k;?>" ><?php echo strip_tags($logdata->cover_let_link); ?></td>
                <td colspan="13"><table>
                        <?php
                        if (isset($logdata->has_media) && ($logdata->has_media)) {
                            $media_ids=array();
                            if (!empty($prod_media_data[$logdata->id])) { ?>
                            <?php $i=0;
                            foreach ($prod_media_data[$logdata->id] as $prod) {
				if (!empty($prod->prodbates)) {
                                foreach ($prod->prodbates as $m_new) { ?>
                                    <?php if($i!=0){ ?>    
                                        <!-- </tr><tr><td colspan="7">&nbsp;</td>-->
                                    <?php } ?>    
                                                <tr>
                                                    <td align="left">
                                                        <?php 
                                                            if (!empty($m_new->task)){ 
                                                                    if($user_model->checkAccess(4.01)){
                                                                      if(isset($task_arr[$m_new->task_id]))
                                                                            echo strip_tags ($task_arr[$m_new->task_id]);
                                                                    } else {
                                                                            echo $m_new->task_id; 
                                                                    }?>

                                                        <?php } ?>
                                                    </td>
                                                    <td align="left"><?php echo $prod->proevidence->evidencetype->evidence_name;?></td>
                                                    <td align="left"><?php echo $prod->proevidence->evid_desc ?></td>
                                                    <td align="left"><?php echo $prod->proevidence->evid_label_desc ?></td>
                                                    <td align="left">
                                                            <?php foreach($prod->proevidence->evidencecontent as $econtents){
                                                                   echo $econtents->evidenceCustodians->cust_lname . ", " . $econtents->evidenceCustodians->cust_fname . " " . $econtents->evidenceCustodians->cust_mi.';';
                                                            } ?>
                                                    </td>
                                                    <td align="left"><?php echo $prod->proevidence->quantity ?></td>
                                                    <td align="left">
                                                        <?php if ($prod->proevidence->contents_total_size != "" && $prod->proevidence->contents_total_size != 0) echo $prod->proevidence->contents_total_size . " " . $prod->proevidence->evidenceunit->unit_name; ?>
                                                    </td>
                                                    <td align="left">
                                                        <?php if ($prod->proevidence->contents_total_size_comp != "" && $prod->proevidence->contents_total_size_comp != 0) echo $prod->proevidence->contents_total_size_comp . " " . $prod->proevidence->evidencecompunit->unit_name; ?>
                                                    </td>
                                                    <td align="left">
							  <?php
                                                            $hasbates = false;
                                                            if (isset($prod->proevidence->bbates) && $prod->proevidence->bbates != "") {
                                                                echo $prod->proevidence->bbates;
                                                            } 
                                                            ?>
                                                     </td>
                                                    <td align="left">
							  <?php
                                                            if (isset($prod->proevidence->ebates) && $prod->proevidence->ebates != "") {
                                                                echo $prod->proevidence->ebates;
                                                            }
                                                            ?>
                                                     </td>
                                                     <td  align="left">
                                                        <?php
                                                           if (isset($m_new->prod_bbates) && $m_new->prod_bbates != "") {
                                                               echo $m_new->prod_bbates;
                                                           } 
                                                           ?>
                                                    </td>
                                                    <td  align="left">
                                                        <?php
                                                          if (isset($m_new->prod_ebates) && $m_new->prod_ebates != "") {
                                                               echo $m_new->prod_ebates;
                                                           }
                                                           ?>
                                                    </td>
                                                    <td align="left">
								<?php
                                                            if (isset($m_new->prod_date_loaded) && date("Y-m-d", strtotime($m_new->prod_date_loaded)) != "1970-01-01") {
                                                                echo date('m/d/Y', strtotime($m_new->prod_date_loaded));
                                                            }
                                                            ?>
                                                     </td>
                                                </tr>         
				<?php
                                    $i++;}
				}
				else{ //echo "<pre>"; print_r($prod); die;?> 
                                    <?php if($i!=0){ ?> 
                                   <!-- </tr><tr><td colspan="7">&nbsp;</td>-->
                                    <?php } ?>
                                          <tr>  
                                                <td align="left">&nbsp; </td>
                                                <td align="left"><?php echo $prod->proevidence->evidencetype->evidence_name;?></td>
                                                <td align="left"><?php echo $prod->proevidence->evid_desc ?></td>
						<td align="left"><?php echo $prod->proevidence->evid_label_desc ?></td>
                                                 <td align="left">
                                                    <?php foreach($prod->proevidence->evidencecontent as $econtents){
                                                                    echo $econtents->evidenceCustodians->cust_lname . ", " . $econtents->evidenceCustodians->cust_fname . " " . $econtents->evidenceCustodians->cust_mi.';';
                                                             } ?>
                                                </td>
                                                <td align="left"><?php echo $prod->proevidence->quantity ?></td>
                                                <td align="left">
                                                    <?php if ($prod->proevidence->contents_total_size != "" && $prod->proevidence->contents_total_size != 0) echo $prod->proevidence->contents_total_size . " " . $prod->proevidence->evidenceunit->unit_name; ?>
                                                </td>
                                                <td align="left">
                                                      <?php if ($prod->proevidence->contents_total_size_comp != "" && $prod->proevidence->contents_total_size_comp != 0) echo $prod->proevidence->contents_total_size_comp . " " . $prod->proevidence->evidencecompunit->unit_name; ?>
                                                </td>	
                                                <td align="left">
						  <?php
                                                        $hasbates = false;
                                                        if (isset($prod->proevidence->bbates) && $prod->proevidence->bbates != "") {
                                                             echo $prod->proevidence->bbates;
                                                            $hasbates = true;
                                                        } 
                                                   ?>
                                                 </td>
                                                 <td align="left">
						  <?php
                                                         if (isset($prod->proevidence->ebates) && $prod->proevidence->ebates != "") {
                                                            echo $prod->proevidence->ebates;
                                                        }
                                                   ?>
                                                 </td>
                                                 <td align="left">&nbsp; </td>
                                                <td align="left">&nbsp; </td>
                                                <td align="left">&nbsp; </td>
                                          </tr>        
			<?php }
                           $i++; } ?>
                             
                    <?php }
                        }else{ ?>
                         <td colspan="13">&nbsp;</td>
                        <?php }?>
                    </table>     
                </td>         
                <td align="left" rowspan="<?php echo $k;?>" ><?php echo $logdata->prod_orig != 0 ? "Yes" : ""; ?></td>
                <td align="left" rowspan="<?php echo $k;?>" ><?php echo $logdata->prod_return != 0 ? "Yes" : ""; ?></td>
                <td align="left" rowspan="<?php echo $k;?>" ><?php echo $logdata->attorney_notes; ?></td>
                <td align="left" rowspan="<?php echo $k;?>" ><?php echo $logdata->prod_disclose; ?></td>
                <td align="left" rowspan="<?php echo $k;?>" ><?php if (date("Y-m-d", strtotime($logdata->prod_agencies)) != '1970-01-01' && date('m-d-Y', strtotime($logdata->prod_agencies))!='11-30--0001') echo date('m-d-Y', strtotime($logdata->prod_agencies)); ?></td>
                <td align="left" rowspan="<?php echo $k;?>" ><?php if (date("Y-m-d", strtotime($logdata->prod_access_req)) != '1970-01-01' && date('m-d-Y', strtotime($logdata->prod_access_req))!='11-30--0001') echo date('m-d-Y', strtotime($logdata->prod_access_req)); ?></td>
                <td align="left" rowspan="<?php echo $k;?>" ><?php if (isset($logdata->prod_misc1)) echo $logdata->prod_misc1; ?></td>
                <td align="left" rowspan="<?php echo $k;?>" ><?php if (isset($logdata->prod_misc1)) echo $logdata->prod_misc2; ?></td>
            </tr>
            <?php
            $j++;
        }
    }
    ?>
</table>