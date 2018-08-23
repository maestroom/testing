<?php
\app\assets\CustomInputAsset::register($this);
/**
 * View : _loadaddTeam
 * @abstract This view file is created to load a Add Team Section for This application
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
		<div class="row">
		<?php 
				$public_chk="checked='checked'";
				$private_chk="";
				if($data->is_private==1)
				{
				$public_chk="";
				$private_chk="checked='checked'";
				}
		?>
                    <fieldset>
                        <legend class="sr-only">Permissions</legend>
                        <div class="col-sm-12">
                            <div class="custom-inline-block-width">
                                <input aria-setsize="2" aria-posinset="1" type="radio" name='public_private' id="public" value="0" <?php echo $public_chk; ?>> 
                                <label for="public">Public</label>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="custom-inline-block-width">
                                <input aria-setsize="2" aria-posinset="2" type="radio" name='public_private' id="private" value="1"  <?php echo $private_chk; ?>>
                                <label for="private">Private</label>
                            </div>
                        </div>
                    </fieldset>
		</div>
		<div class="left">
		<?php
			//echo CHtml::button('Edit',array('class'=>'button_small','id'=>'edit_per','onclick'=>'changeper('.$id.',"case")'));
		?>
	   </div>
	   <div class="left">
		<?php
			//echo CHtml::button('Cancel',array('class'=>'button_small','id'=>'','onclick'=>'$("#doc_permission").html("");'));
		?>
	   </div>