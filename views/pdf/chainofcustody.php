<?php use app\models\Options;
//echo "<pre>";print_r($evidtrans);die;
$transType =array('','Check in','Check out','Destroy','Move','Return');
 ?>

<div id="form_builder_panel" style="overflow-x:hidden !important;">
  <?php $j=0; 
   ?>
  <?php if($j != 0){ ?>
  <div style='page-break-after:always;'>&nbsp;</div>
  <?php } ?>
  <div style="background:#c52d2e; padding:7px 10px; font-size:12px; font-weight:bold; color:#FFF; margin:0px 0px 5px; font-family:Arial;">Media Chain of Custody Form</div>
  <div style="font-family:Arial;">
    <div style="background:#e9e7e8; color:#333; font-size:11px; margin:0px; padding:7px 10px; position:relative;">Media Details</div>
    <table cellpadding="0" cellspacing="0" style="width:100%; margin:5px 7px;">
      <tr>
        <td style="width:150px; border:none 0px; font-family:Arial; font-size:10px; padding:3px 5px;" valign="top"><strong>Client / Case Name (s)</strong></td>
        <td style="border:none 0px; font-family:Arial; font-size:10px; padding:3px 5px;"><?php 
        if(!empty($clientCaseEvidences)) {
        foreach($clientCaseEvidences as $clientcase){
        if(isset($clientcase['case_name'])){
          $exploded_cases=explode(",",$clientcase['case_name']);
          if(!empty($exploded_cases)){
            foreach($exploded_cases as $case_name){
              echo $clientcase['client_name'] ." - ".$case_name."<br>";
            }
          }
        }
        
         $j++;}} ?></td>
      </tr>
      <tr>
        <td style="width:150px; border:none 0px; font-family:Arial; font-size:10px; padding:3px 5px;"><strong>Media #</strong></td>
        <td style="border:none 0px; font-family:Arial; font-size:10px; padding:3px 5px;"><?php echo $evidtrans[0]->evidence->id;?></td>
      </tr>
      <tr>
        <td style="width:150px; border:none 0px; font-family:Arial; font-size:10px; padding:3px 5px;"><strong>Media Description</strong></td>
        <td style="border:none 0px; font-family:Arial; font-size:10px; padding:3px 5px;"><?php echo $evidtrans[0]->evidence->evid_desc;?></td>
      </tr>
      <tr>
        <td style="width:150px; border:none 0px; font-family:Arial; font-size:10px; padding:3px 5px;"><strong>Received From</strong></td>
        <td style="border:none 0px; font-family:Arial; font-size:10px; padding:3px 5px;"><?php echo $evidtrans[0]->evidence->received_from;?></td>
      </tr>
    </table>
  </div>
  
  
  
<div style="font-family:Arial;">
  <div style="background:#e9e7e8; color:#333; font-size:11px; margin:0px 0px 5px; padding:7px 10px; position:relative;">Chain of Custody Details</div>  
    <table cellpadding="0" cellspacing="0" style="width:100%; margin:0px;">
      <thead style="background:#e9e7e8;">
        <tr>
          <th align="left" style="font-size:10px; font-family:Arial; padding:7px 15px; border:none 0px; background:#e9e7e8; width:100px;"><strong>Trans Type</strong></th>
          <th align="left" style="font-size:10px; font-family:Arial; padding:7px 15px; border:none 0px; background:#e9e7e8; width:140px;"><strong>Trans Date</strong></th>
          <th align="left" style="font-size:10px; font-family:Arial; padding:7px 15px; border:none 0px; background:#e9e7e8; width:100px;"><strong>Trans By</strong></th>
          <th align="left" style="font-size:10px; font-family:Arial; padding:7px 15px; border:none 0px; background:#e9e7e8; width:150px;"><strong>Trans Requested By</strong></th>
          <th align="left" style="font-size:10px; font-family:Arial; padding:7px 15px; border:none 0px; background:#e9e7e8; width:90px;"><strong>Moved To</strong></th>
          <th align="left" style="font-size:10px; font-family:Arial; padding:7px 15px; border:none 0px; background:#e9e7e8; width:90px;"><strong>Trans To</strong></th>
          <th align="left" style="font-size:10px; font-family:Arial; padding:7px 15px; border:none 0px; background:#e9e7e8; width:120px;"><strong>Reason for Transaction</strong></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($evidtrans as $tran){?>
        <tr>
          <td style="font-size:10px; font-family:Arial; padding:5px 15px; border:none 0px;"><?php echo $transType[$tran->trans_type];?></td>
          <td style="font-size:10px; font-family:Arial; padding:5px 15px; border:none 0px;"><?php echo (new Options)->ConvertOneTzToAnotherTz($tran->trans_date, 'UTC', $_SESSION['usrTZ']);?></td>
          <td style="font-size:10px; font-family:Arial; padding:5px 15px; border:none 0px;"><?php echo $tran->transby->usr_first_name.' '.$tran->transby->usr_lastname;;?></td>
          <td style="font-size:10px; font-family:Arial; padding:5px 15px; border:none 0px;"><?php echo $tran->transRequstedby->usr_first_name.' '.$tran->transRequstedby->usr_lastname;?></td>
          <td style="font-size:10px; font-family:Arial; padding:5px 15px; border:none 0px;"><?php echo $tran->storedLoc->stored_loc;?></td>
          <td style="font-size:10px; font-family:Arial; padding:5px 15px; border:none 0px;"><?php echo $tran->evidenceTo->to_name;?></td>
          <td style="font-size:10px; font-family:Arial; padding:5px 15px; border:none 0px;"><?php echo $tran->trans_reason;?></td>
        </tr>
        <?php }?>
      </tbody>
    </table>
  </div>  
</div>