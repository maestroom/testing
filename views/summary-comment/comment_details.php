<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\User;
use app\models\Comments;
use app\models\Options;
/*Second level loop*/?>
<div id="comments" class="comments left case_88">
<?php if(!empty($model)){ ?>
<ul class="list-unstyled">
<?php foreach ($model as $dachilds){
$child_char = substr(strip_tags($dachilds->comment),0,100);
?>
<li>
<div class="comment_box col-xs-offset-1">
<div class="col-sm-9">
    <div class="name h6">
        <strong><?= $dachilds->createdUser->usr_first_name." ".$dachilds->createdUser->usr_lastname;?></strong>
        <?php if($dachilds->parent_id!=0){?>
        
            <em class="fa fa-share" aria-hidden="true" title="Comment username"></em>
            <span style="color:gray"><?= $dachilds->parent->createdUser->usr_first_name." ".$dachilds->parent->createdUser->usr_lastname;?></span>
        <?php }?>
    </div>
    <div class="date">
        <div id="defualt_<?=$dachilds->Id?>" class="defualt"><small><?=Html::decode($dachilds->comment)?></small></div>
    </div>
</div>
<div class="col-sm-3">
<div class="type  text-right">
        <div class="h6 white-space"><?php  
            $attachment="";
            if (!empty($dachilds->attachments)) {
                foreach ($dachilds->attachments as  $at) {
                    if ($attachment == "")
                        $attachment ='<a href="javascript:void(0)" onclick="downloadattachment(' . $at->id . ')" class="icon-fa" title="Attachment"><em title="Attachment" class="fa fa-paperclip"></em><span class="screenreader">Download Attachment</span></a>';
                    else
                        $attachment .='&nbsp;&nbsp;<a href="javascript:void(0)" onclick="downloadattachment(' . $at->id . ')" class="icon-fa" title="Attachment"><em title="Attachment" class="fa fa-paperclip"></em><span class="screenreader">Download Attachment</span></a>';
                }
            }echo "&nbsp;".$attachment; 
            echo "<strong>".(new Options)->ConvertOneTzToAnotherTz($da->created,'UTC',$_SESSION['usrTZ'])."</strong>";
            ?>
        </div>
    </div>
    <div class="text-right"><em> </em></div>
</div>
<div class="col-sm-12">
<div class="action text-right h6" >
    <?php if($case_id!=0){?>
        <?php if ((new User)->checkAccess(4.0805)) { ?>
        <a href="javascript:void(0)" onclick="EditSummaryComment(<?=$dachilds->Id?>,<?=$team_id?>,<?=$team_loc?>,<?=$case_id?>,'case');" title="Edit" class=" ">Edit</a>&nbsp;&nbsp;
        <?php } ?>
        <a href="javascript:void(0)" onclick="<?php if ((new User)->checkAccess(4.0806)) {?>DeleteSummaryComment(<?php echo $dachilds->Id?>,'reply','case','<?php echo $child_char; ?>');<?php } else {?>alert('You do not have access to delete this item. Please contact your administrator if your access needs to be updated.');<?php }?>" title="Delete" class="">Delete</a>&nbsp;&nbsp;
    <?php } else{?>
        <?php if ((new User)->checkAccess(5.075)) { ?>
        <a href="javascript:void(0)" onclick="EditSummaryComment(<?=$dachilds->Id?>,<?=$team_id?>,<?=$team_loc?>,<?=$case_id?>,'team');" title="Edit" class=" ">Edit</a>&nbsp;&nbsp;
        <?php } ?>
        <a href="javascript:void(0)" onclick="<?php if ((new User)->checkAccess(5.076)) {?>DeleteSummaryComment(<?php echo $dachilds->Id?>,'reply','team','<?php echo $child_char; ?>');<?php } else {?>alert('You do not have access to delete this item. Please contact your administrator if your access needs to be updated.');<?php }?>" title="Delete" class="">Delete</a>&nbsp;&nbsp;
    <?php }?>
</div>
</div>
</div>

</li>
<?php }?>
</ul>
<?php } else{ /*Second level loop*/?>
<div class="comment_box col-xs-offset-1"><br>Reply not  found...</div>
<?php }?>
</div>
