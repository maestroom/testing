<?php
/**
 * View : manageclient
 * @abstract This view file is created for manage Client Section for This application
 * @package views
 * @subpackage component
 * @author Jayant (mali1545@gmail.com)
 * @copyright �  2011-2012 Inovitech, LLC, All Rights Reserved.
 * @todo Ensure all comment block and methods are complete
 * @version 1.0.0 Initial release
 *
 */

// --------------------------------------------------------------------------------------
// PRODUCTION CODE STARTS HERE
// --------------------------------------------------------------------------------------
?>
<tr class="" id="atm_<?php echo $data->id?>">
    
    <td class="word-break">
        <a onclick="go_toMedia('<?php echo $data->id;?>');" href="javascript:void(0);"><?php echo $data->id;?></a>
    </td>
    <td class="word-break"><?php echo $data->evidencetype->evidence_name?></td>
    <td class="word-break"><?php if(isset($data->contents_total_size) && $data->contents_total_size!=0) echo $data->contents_total_size." ".$data->evidenceunit->unit_name?></td>
    <td class="word-break"><?php if(isset($data->contents_total_size_comp) && $data->contents_total_size_comp!=0) echo $data->contents_total_size_comp." ".$data->evidencecompunit->unit_name?></td>
    <td class="word-break" align="center">
        <a href="javascript:void(0);" onclick="deleteAttachedMedia('<?php echo $data->id;?>');" class="icon-fa" title="Delete Content"><em title="Delete Content" class="fa fa-close"></em></a>
    </td>
</tr>
