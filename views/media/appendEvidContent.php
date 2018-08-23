<?php
/**
 * View: Index
 * @abstract This view is define for Evidence Index Page
 * @package views
 * @subpackage evidence
 * @author Jayant (mali1545@gmail.com)
 * @copyright � 2011-2012 Inovitech, LLC, All Rights Reserved.
 * @todo Ensure all comment block and methods are complete
 * @version 1.0.0 Initial release
 *
 */
// --------------------------------------------------------------------------------------
// PRODUCTION CODE STARTS HERE
// --------------------------------------------------------------------------------------
?>
 <?php if($data['type'] != 'edit'){ ?> <tr id="row_evid_content_<?php echo $data['temp_evid_id']; ?>"> <?php } ?>
                       
                        <?php
                        if(empty($evid_custdata)){ 
                            ?>
                       <td class="word-break">
                           <div id="custodians_name_<?php echo $data['EvidenceContents']['cust_id']; ?>"></div>
                        <script>
                            $(document).ready(function(){
                                    if($('body div.wrap').find('#media_container').find('#Evidence #evid_custodian_list div#<?php echo $data['EvidenceContents']['cust_id'];?> .cust_fullname').length){
                                        $('div#custodians_name_<?php echo $data['EvidenceContents']['cust_id']; ?>').html($('body div.wrap').find('#media_container').find('#Evidence #evid_custodian_list div#<?php echo $data['EvidenceContents']['cust_id'];?> .cust_fullname').val());
                                    }
                                    else{ 
                                        if($('body div.wrap').find('#admin_main_container').find('#Evidence #evid_custodian_list div#<?php echo $data['EvidenceContents']['cust_id'];?> .cust_fullname').length){
                                             $('div#custodians_name_<?php echo $data['EvidenceContents']['cust_id']; ?>').html($('body div.wrap').find('#admin_main_container').find('#Evidence #evid_custodian_list div#<?php echo $data['EvidenceContents']['cust_id'];?> .cust_fullname').val());   
                                        }else{
                                            $('div#custodians_name_<?php echo $data['EvidenceContents']['cust_id']; ?>').html($('body div.wrapper').find('#evid_custodian_list div#<?php echo $data['EvidenceContents']['cust_id'];?> .cust_fullname').val());
                                        }
                                    }
                            });
                        </script>
<noscript></noscript>
                       </td>
                        <?php 
                        }else { ?>
                            <td class="word-break">
                            <?php 
                                $custo_name = '';
                                if($data['evid_id'] != 0)
									echo "<a href='javascript:void(0);' id='evid-custodian-{$evid_custdata->cust_id}' onclick='editEvidCustodian({$evid_custdata->cust_id});'>".$evid_custdata->cust_lname.", ".$evid_custdata->cust_fname." ".$evid_custdata->cust_mi."</a>";
                                else
									echo $custo_name = $evid_custdata->cust_lname.", ".$evid_custdata->cust_fname." ".$evid_custdata->cust_mi;           
                            ?>          
                            </td>   
                     <?php  } ?>
                    <td class="word-break"><?php echo $datatype->data_type;?></td>
                    <td class="word-break"><?php echo $data['EvidenceContents']['data_size']." ".$unit->unit_name;?></td>
                    <td class="word-break"><?php echo $data['EvidenceContents']['data_copied_to'];?>
                    <?php 
						echo "<input type='hidden' name='EvidenceContent[{$data['temp_evid_id']}][cust_id]' value='{$data['EvidenceContents']['cust_id']}'/>"; 
						echo "<input type='hidden' name='EvidenceContent[{$data['temp_evid_id']}][data_type]' value='{$data['EvidenceContents']['data_type']}'/>"; 
						echo "<input type='hidden' name='EvidenceContent[{$data['temp_evid_id']}][data_size]' value='{$data['EvidenceContents']['data_size']}'/>"; 
						echo "<input type='hidden' name='EvidenceContent[{$data['temp_evid_id']}][unit]' value='{$data['EvidenceContents']['unit']}'/>"; 
						echo "<input type='hidden' name='EvidenceContent[{$data['temp_evid_id']}][data_copied_to]' value='{$data['EvidenceContents']['data_copied_to']}'/>"; 
                    ?>
                    </td>
                    <td class="text-center third-td word-break">
                           <a href="javascript:void(0)" onclick="evidencecontentaction('edit','<?php echo $data['temp_evid_id'];?>');" class="icon-fa" title="Edit"><em title="Edit" class="fa fa-pencil"></em></a>
                           <a href="javascript:RemoveHoliday(0)" onclick="evidencecontentaction('delete','<?php echo $data['temp_evid_id'];?>','<?=$custo_name?>');" class="icon-fa" title="Delete"><em title="Delete" class="fa fa-close"></em></a>
                    </td>
<?php if($data['type'] != 'edit'){ ?>     </tr>          <?php } ?>
