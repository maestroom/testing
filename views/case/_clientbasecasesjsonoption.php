<?php
/* @var $this yii\web\View */
use yii\helpers\Html;

?>
<?php if($client_id != 0) { ?>
    <?php 
    if(!empty($caseList)){
        foreach ($caseList as $case){
    ?>
        <option value="<?php echo $case->id.'|'.$case->client_id; ?>" data-id="<?php echo $case->id;?>"  ><?=Html::encode($case->case_name); ?></option>
    <?php 
   } 
 }
 ?>
<?php }?>