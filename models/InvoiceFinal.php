<?php

namespace app\models;

use Yii;
use app\models\Client;
use app\models\ClientCase;
use app\models\Tasks;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%invoice_final}}".
 *
 * @property integer $id
 * @property integer $client_id
 * @property integer $display_by
 * @property double $total
 * @property integer $has_accum_cost
 * @property integer $created_by
 * @property string $created_date
 * @property integer $modified_by
 * @property string $modified_date
 * @property integer $closed_by
 * @property string $closed_date
 */
class InvoiceFinal extends \yii\db\ActiveRecord
{
    public $client_name = '';
    public $case_name = '';
    public $usr_first_name = '';
    public $usr_lastname = '';
    public $totalinvoiceamt = 0;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%invoice_final}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['client_id', 'contact_id', 'display_by', 'has_accum_cost'], 'integer'],
            [['display_by', 'has_accum_cost'], 'required'],
          //  [['total'], 'number'],
            [['created_by', 'created_date', 'modified_by', 'modified_date', 'client_name', 'case_name', 'totalinvoiceamt', 'closed_by', 'closed_date', 'usr_first_name', 'usr_lastname'], 'safe'],
            [['client_id'], 'exist', 'skipOnError' => true, 'targetClass' => Client::className(), 'targetAttribute' => ['client_id' => 'id']],
            [['client_case_id'], 'exist', 'skipOnError' => true, 'targetClass' => ClientCase::className(), 'targetAttribute' => ['client_case_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'client_id' => 'Client ID',
        	'client_case_id' => 'Client Case ID',
            'display_by' => 'Display By',
          //  'total' => 'Total',
            'has_accum_cost' => 'Has Accum Cost',
            'created_by' => 'Created By',
            'created_date' => 'Created Date',
            'modified_by' => 'Modified By',
            'modified_date' => 'Modified Date',
        	'totalinvoiceamt'=> 'Total',
          'closed_date' => 'Closed Date',
          'closed_by' => 'Closed By'
        ];
    }

	/**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
    	if (parent::beforeSave($insert)) {
    		if($this->isNewRecord) {
    			$this->created_date = date('Y-m-d H:i:s');
    			$this->created_by =Yii::$app->user->identity->id;
    			$this->client_case_id = isset($this->client_case_id)?$this->client_case_id:0;
    			$this->modified_date =date('Y-m-d H:i:s');
    			$this->modified_by =Yii::$app->user->identity->id;
    		} else {
    			$this->modified_date =date('Y-m-d H:i:s');
    			$this->modified_by =Yii::$app->user->identity->id;
    		}
    		// Place your custom code here
    		return true;
    	} else {
    		return false;
    	}
    }

	/**
     * @return \yii\db\ActiveQuery
     */
    public function getInvoiceFinalBilling()
    {
        return $this->hasMany(InvoiceFinalBilling::className(), ['invoice_final_id' => 'id']);
    }

	/**
     * @return \yii\db\ActiveQuery
     */
    public function getTotalInvoiceAmount($invoiceid)
    {
        $connection = \Yii::$app->db;
        $tax = "(CASE WHEN (SELECT sum( rate ) AS totaltax FROM tbl_invoice_final_taxes WHERE tbl_invoice_final_taxes.invoice_final_billing_id = tbl_invoice_final_billing.id) IS NULL THEN 0 ELSE (SELECT sum( rate ) AS totaltax FROM tbl_invoice_final_taxes WHERE tbl_invoice_final_taxes.invoice_final_billing_id = tbl_invoice_final_billing.id) END)";
        $subtotal = "(tbl_invoice_final_billing.final_rate * tbl_tasks_units_billing.quantity ) ";
		$sql = "SELECT sum( totaltaxprice.subtotaltax ) AS subtotaltax FROM (
			SELECT (($subtotal) + (($subtotal * $tax)/100)) AS subtotaltax
			FROM tbl_invoice_final_billing
			INNER JOIN tbl_tasks_units_billing ON tbl_tasks_units_billing.id = tbl_invoice_final_billing.billing_unit_id
			WHERE invoice_final_id=$invoiceid AND tbl_tasks_units_billing.invoiced != 2
		) AS totaltaxprice";
		$model = $connection->createCommand($sql);
		return number_format($model->queryScalar(),2);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSumTotalInvoiceAmount()
    {
        $connection = \Yii::$app->db;
        $tax = "(CASE WHEN (SELECT sum( rate ) AS totaltax FROM tbl_invoice_final_taxes WHERE tbl_invoice_final_taxes.invoice_final_billing_id = tbl_invoice_final_billing.id) IS NULL THEN 0 ELSE (SELECT sum( rate ) AS totaltax FROM tbl_invoice_final_taxes WHERE tbl_invoice_final_taxes.invoice_final_billing_id = tbl_invoice_final_billing.id) END)";
        $subtotal = "(tbl_invoice_final_billing.final_rate * tbl_tasks_units_billing.quantity ) ";
		$sql = "SELECT sum( totaltaxprice.subtotaltax ) AS subtotaltax FROM (
			SELECT (($subtotal) + (($subtotal * $tax)/100)) AS subtotaltax
			FROM tbl_invoice_final_billing
			INNER JOIN tbl_tasks_units_billing ON tbl_tasks_units_billing.id = tbl_invoice_final_billing.billing_unit_id
			WHERE tbl_tasks_units_billing.invoiced != 2
		) AS totaltaxprice";
		$model = $connection->createCommand($sql);
		return number_format($model->queryScalar(),2);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClientCaseDetails($id, $display_by)
    {
        $sql = "SELECT tc.client_name, tcc.case_name FROM tbl_invoice_final as tf
            INNER JOIN tbl_invoice_final_billing as tb ON tf.id = tb.invoice_final_id
            INNER JOIN tbl_tasks_units_billing as tu ON tu.id = tb.billing_unit_id
            INNER JOIN tbl_tasks_units as taskunit ON taskunit.id = tu.tasks_unit_id
            INNER JOIN tbl_tasks as t ON t.id = taskunit.task_id
            INNER JOIN tbl_client_case as tcc ON tcc.id = t.client_case_id
            INNER JOIN tbl_client as tc ON tc.id = tcc.client_id
             WHERE tf.id =  ".$id." GROUP BY tc.client_name,tcc.case_name";
        $query = TasksUnitsBilling::findBySql($sql)->asArray()->all();
        $result = ($display_by==1)?$query[0]['client_name'].' - '.$query[0]['case_name']:$query[0]['client_name'];
        $html='';
        $html = '<a href="index.php?r=billing-finalized-invoice/preview-invoice&invoice_id='.$id.'&flag=preview" title="Preview '.$result.' Invoice">'.$result.'</a>';
        return $html;
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCaseDetails($id, $display_by)
    {
        $sql = "SELECT tc.client_name, tcc.case_name FROM tbl_invoice_final as tf
            INNER JOIN tbl_invoice_final_billing as tb ON tf.id = tb.invoice_final_id
            INNER JOIN tbl_tasks_units_billing as tu ON tu.id = tb.billing_unit_id
            INNER JOIN tbl_tasks_units as taskunit ON taskunit.id = tu.tasks_unit_id
            INNER JOIN tbl_tasks as t ON t.id = taskunit.task_id
            INNER JOIN tbl_client_case as tcc ON tcc.id = t.client_case_id
            INNER JOIN tbl_client as tc ON tc.id = tcc.client_id
             WHERE tf.id =  ".$id." GROUP BY tc.client_name,tcc.case_name";
        $query = TasksUnitsBilling::findBySql($sql)->asArray()->all();
        $result = ($display_by==1)?$query[0]['case_name']:'';
        $html='';
        $html = '<a href="index.php?r=billing-finalized-invoice/preview-invoice&invoice_id='.$id.'&flag=preview" title="Preview '.$result.' Invoice">'.$result.'</a>';
        return $html;
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClientDetails($id, $display_by)
    {
        $sql = "SELECT tc.client_name, tcc.case_name FROM tbl_invoice_final as tf
            INNER JOIN tbl_invoice_final_billing as tb ON tf.id = tb.invoice_final_id
            INNER JOIN tbl_tasks_units_billing as tu ON tu.id = tb.billing_unit_id
            INNER JOIN tbl_tasks_units as taskunit ON taskunit.id = tu.tasks_unit_id
            INNER JOIN tbl_tasks as t ON t.id = taskunit.task_id
            INNER JOIN tbl_client_case as tcc ON tcc.id = t.client_case_id
            INNER JOIN tbl_client as tc ON tc.id = tcc.client_id
             WHERE tf.id =  ".$id." GROUP BY tc.client_name,tcc.case_name";
        $query = TasksUnitsBilling::findBySql($sql)->asArray()->all();
        $result = $query[0]['client_name'];
        $html='';
        $html = '<a href="index.php?r=billing-finalized-invoice/preview-invoice&invoice_id='.$id.'&flag=preview" title="Preview '.$result.' Invoice">'.$result.'</a>';
        return $html;
    }


    /**
     * pendingBillInvoice is used to get total invoiced & pending amount Case wise.
     * @task_id = tbl_tasks.id
     * @type = pending[to retrieve total of pending amount] OR invoice[to retrieve total of invoiced amount]
     */
    public function invoicedBillInvoice($task_id){
    	$mainTotal=0;
    	$sql="SELECT tbl_tasks_units_billing.* FROM tbl_tasks_units_billing
			   INNER JOIN tbl_tasks_units ON tbl_tasks_units.id = tbl_tasks_units_billing.tasks_unit_id
			   INNER JOIN tbl_task_instruct_servicetask ON tbl_task_instruct_servicetask.id = tbl_tasks_units.task_instruct_servicetask_id
    		   INNER JOIN tbl_tasks ON tbl_tasks.id=tbl_task_instruct_servicetask.task_id
    		   INNER JOIN tbl_client_case ON tbl_client_case.id = tbl_tasks.client_case_id
    		   INNER JOIN tbl_client ON tbl_client.id = tbl_client_case.client_id
    		   INNER JOIN tbl_servicetask ON tbl_servicetask.id = tbl_task_instruct_servicetask.servicetask_id AND (tbl_servicetask.billable_item = 1 OR tbl_servicetask.billable_item = 2)  AND tbl_client_case.is_close = 0
    		  WHERE";
    	$order = " ORDER BY tbl_client.client_name asc,tbl_client_case.case_name asc";
    	$where = " (tbl_tasks.id IN ($task_id)) AND (tbl_tasks_units_billing.invoiced = '1')";

    	$mainSql = $sql.$where.$order;
    	$taskunitbillingdata = TasksUnitsBilling::findBySql($mainSql)->all();

    	if(!empty($taskunitbillingdata)){
    		foreach($taskunitbillingdata as $taskey1 => $taskval1){
				$billing_final_invoiced = InvoiceFinalBilling::find()->where(['billing_unit_id'=>$taskval1->id])->all();
    			if(!empty($billing_final_invoiced)){
    				foreach ($billing_final_invoiced as $billingfinalinvoiced){
						$mainTotal = $mainTotal + ($billingfinalinvoiced->final_rate * $taskval1->quantity);
    				}
    			}
    		}
    	}
    	return $mainTotal;
    }

    /**
     * pendingBillInvoice is used to get total invoiced & pending amount Case wise.
     * @task_id = tbl_tasks.id
     * @type = pending[to retrieve total of pending amount] OR invoice[to retrieve total of invoiced amount]
     */
    public function pendingBillInvoice($task_id)
    {
    	$mainTotal=0;
    	$sql=" SELECT tbl_tasks_units_billing.* FROM tbl_tasks_units_billing
			   INNER JOIN tbl_tasks_units ON tbl_tasks_units.id = tbl_tasks_units_billing.tasks_unit_id
			   INNER JOIN tbl_task_instruct ON tbl_task_instruct.id = tbl_tasks_units.task_instruct_id
			   INNER JOIN tbl_task_instruct_servicetask ON tbl_task_instruct_servicetask.id = tbl_tasks_units.task_instruct_servicetask_id
    		   INNER JOIN tbl_tasks ON tbl_tasks.id=tbl_task_instruct_servicetask.task_id
    		   INNER JOIN tbl_client_case ON tbl_client_case.id = tbl_tasks.client_case_id
    		   INNER JOIN tbl_client ON tbl_client.id = tbl_client_case.client_id
    		   INNER JOIN tbl_servicetask ON tbl_servicetask.id = tbl_task_instruct_servicetask.servicetask_id AND (tbl_servicetask.billable_item = 1 OR tbl_servicetask.billable_item = 2) AND tbl_client_case.is_close = 0
    		  WHERE";
    	$where = " (tbl_tasks.id IN ($task_id)) AND (tbl_tasks_units_billing.invoiced IS NULL OR tbl_tasks_units_billing.invoiced = '')";
    	$order = " ORDER BY tbl_client.client_name asc,tbl_client_case.case_name asc";
    	$mainSql = $sql.$where.$order;
        $taskunitbillingdata = TasksUnitsBilling::findBySql($mainSql)->all();
    	//echo "<pre>"; print_r($taskunitbillingdata); exit;
    	$taskunitbillingdata1 = array();
    	$pprate2=array();
    	$casearr=array();
    	if(!empty($taskunitbillingdata)){
            foreach($taskunitbillingdata as $taskey1 => $taskval1){
    		/*if((new TasksUnitsBilling)->checkpricingforrate2($taskval1->tasks->client_id,$taskval1->tasks->client_case_id,$taskval1->pricing_id,$taskval1->quantity))
    			$pprate2[$taskval1->pricing_id] = $taskval1->pricing_id;*/
    		$istemprate=0;
    		if(isset($taskval1->temp_rate) && $taskval1->temp_rate!='') {
    			$istemprate=1;
    			$rate = $taskval1->temp_rate;
    			if($taskval1->temp_discount!='') {
					$frate = $rate - ($rate*$taskval1->temp_discount/100);
					$finalrate = $rate;
    			} else {
				 	$frate = $rate;
					$finalrate = $rate;
    			}
    		}else{
    			$rate = (new TasksUnitsBilling)->checkpricingforrate($taskval1->tasksUnits->taskInstruct->tasks->clientCase->client_id,$taskval1->tasksUnits->taskInstruct->tasks->client_case_id,$taskval1->pricing_id,$taskval1->quantity,"",$taskval1->tasksUnits->team_loc);
    			if($taskval1->temp_discount!='') {
    				$frate = $rate - ($rate*$taskval1->temp_discount/100);
    				$finalrate = $rate;
    			} else {
    				$frate = $rate;
    				$finalrate = $rate;
    			}
    		}

    		$taskunitbillingdata1[$taskval1->tasksUnits->taskInstruct->tasks->clientCase->client_id][$taskval1->tasksUnits->taskInstruct->tasks->client_case_id][] = array(
				'client_case_id' => $taskval1->tasksUnits->taskInstruct->tasks->client_case_id,
				'servicetask_id' => $taskval1->tasksUnits->taskInstructServicetask,
				'price_point' => $taskval1->pricing->price_point,
				'price_pointid'=> $taskval1->pricing_id,
				'project_id' => $taskval1->tasksUnits->task_id,
				'id' => $taskval1->id,
				'loc'=> $taskval1->tasksUnits->team_loc,
				'unit_price_id' => $taskval1->pricing->unit_price_id,
				'tasks_unit_id' => $taskval1->tasks_unit_id,
				'created'=>$taskval1->created,
				'invoiced'=>$taskval1->invoiced,
				'desc'=>$taskval1->billing_desc,
				'unit_id'=>$taskval1->quantity,
				'rate'=>$frate,
				'istemprate'=>$istemprate,
				'subtotal'=>$frate * $taskval1->quantity,
				'setdelete'=>$setdelete,
				'discount'=>$taskval1->temp_discount,
				'discount_reason'=>$taskval1->temp_discount_reason,
				'internal_ref_no_id'=>$taskval1->internal_ref_no_id
			);
		}
	}

    /* Start : If Range PP then rate will be on the bases of total of all same unit along with same pricepoint of same location */
        $pricepointLocwisetotal = array();
        $revisedArray=array();
        if (!empty($taskunitbillingdata1)) {
        	foreach ($taskunitbillingdata1 as $keysclient => $values){
        		foreach ($values as $keyscase => $values1){
	        		foreach ($values1 as $k => $value){
		        		if(is_numeric($k))
		        		{
		        			if($value['invoiced'] != 2 && $value['istemprate']!=1)
		        			$pricepointLocwisetotal[$keysclient][$keyscase][$value['price_pointid']][$value['loc']][$value['unit_price_id']]['unit_total'] += $value['unit_id'];
	        			}
        			}
        		}
        	}
        	//echo "<pre>",print_r($pricepointLocwisetotal),"<pre>";
        	foreach ($taskunitbillingdata1 as $keysclient => $values){
        		foreach ($values as $keyscase => $values1){
	        		foreach ($values1 as $k => $value){
		        		if(is_numeric($k))
		        		{
		        			if(isset($pricepointLocwisetotal[$keysclient][$keyscase][$value['price_pointid']][$value['loc']][$value['unit_price_id']]['unit_total']))
		        			{
			        			if ($value['istemprate'] != 1) {
			        				$quantity = $pricepointLocwisetotal[$keysclient][$keyscase][$value['price_pointid']][$value['loc']][$value['unit_price_id']]['unit_total'];
					                $rate = (new TasksUnitsBilling)->checkpricingforrate($keysclient, $keyscase, $value['price_pointid'], $quantity ,"",$value['loc']);
					                if ($value['discount'] != '') {
					                    $frate     = $rate - ($rate * $value['discount'] / 100);
					                    $finalrate = $rate;
					                } else {
					                    $frate     = $rate;
					                    $finalrate = $rate;
					                }

					                $taskunitbillingdata1[$keysclient][$keyscase][$k]['rate'] = $frate;
		        					$taskunitbillingdata1[$keysclient][$keyscase][$k]['subtotal'] = $frate * $value['unit_id'];
					            }
		        			}
	        			}
	        		}
        		}
        	}
        }
      //  echo "<pre>",print_r($taskunitbillingdata1),"</pre>";die;
	  //  echo "<pre>",print_r($taskunitbillingdata1),"</pre>";die;
    	/* End : If Range PP then rate will be on the bases of total of all same unit along with same pricepoint of same location */
    	foreach ($taskunitbillingdata1 as $key => $val)
    	{
            $total          = 0;
            $cnt            = 0;
            $setdeletetotal = 0;
            $taskdiscount   = 0;
            foreach ($val as $ckey => $cval) {
            	foreach ($cval as $kkey => $kval) {
                	if($kval['invoiced']!=2){
	                   	$total += $kval['subtotal'];
                	}
	            }
            }
            $mainTotal =$mainTotal +$total;
        }
    	return $mainTotal;
    }

    /**
     * pendingBillInvoiceByCase is used to get total invoiced & pending amount Case wise.
     * @caseId = tbl_client_case.id
     * @type = pending[to retrieve total of pending amount] / invoice[to retrieve total of invoiced amount]
     */
	public function pendingBillInvoiceByCase($caseId,$type="pending")
	{
        $mainTotal=0;
		$list_task="SELECT id FROM tbl_tasks WHERE client_case_id=".$caseId;
		if($type == "pending" && !empty($list_task)){
            $mainTotal=$this->pendingBillInvoice($list_task);
		}
		if($type == "invoice" && !empty($list_task)){
			$mainTotal=$this->invoicedBillInvoice($list_task);
		}
		return $mainTotal;
	}

	/*
	 * Sum of Pending
	 * */
    public function totalspendbudget($caseId){
		$task_data= Tasks::find()->where('client_case_id In (' . $caseId . ')')->select('id')->orderBy('created desc')->all();
		$total = 0;	$invoiced_total=0;	$pending_total=0;$main_total=0;
		foreach ($task_data as $tdata) {
			$invoiced = (new InvoiceFinal)->invoicedBillInvoice($tdata->id);
			$pending  = (new InvoiceFinal)->pendingBillInvoice($tdata->id);
			if ($invoiced != 0 || ($pending != 0 && !empty($pending))) {
    			$task_ids[$tdata->id] = $tdata->id;
    			$caseSpendPerProject[] = array(
    				'project_id' => $tdata->id,
    				'project_name' => $tdata->activeTaskInstruct->project_name,
    				'invoiced' => $invoiced,
    				'pending' => $pending,
    				'total_spent'=> $pending+$invoiced,
    			);
    			$invoiced_total+=$invoiced;
    			$pending_total+=$pending;
    			$total = $total + ($invoiced + $pending);
    		}
    	}
    	return $total;
	}
    /* get task due date and time according to user's timezone settings */
    public function getFinalInvoicedate($dt)
    {
        return (new Options)->ConvertOneTzToAnotherTz($dt, 'UTC', $_SESSION['usrTZ'],'MDY');
    }
    /* get closed invoice date and time according to user's timezone settings */
    public function getClosedInvoicedate($dt)
    {
        return (new Options)->ConvertOneTzToAnotherTz($dt, 'UTC', $_SESSION['usrTZ'],'MDY');
    }
    /**
     * get user full name
     */
     public function get_user_fullname($closed_by)
     {
  		 $query = ArrayHelper::map(self::find()->select(['tbl_invoice_final.closed_by'])->with(['closedUser' => function(\yii\db\ActiveQuery $query) use ($closed_by){
  			 $query->select(['tbl_user.id','tbl_user.usr_first_name','tbl_user.usr_lastname']);
  			 $query->where(['tbl_user.id' => $closed_by]);
  		 }])->all(),'closed_by',function($model, $defaultValue) {
         return $model['closedUser']['usr_first_name'].' '.$model['closedUser']['usr_lastname'];
  		 });
  		 return $query;
	   }

     /* get existing invoices for client case */
     public function getExistingInvoicesForClientCase($client_id, $client_case_id, $display_type)
     {
       if($display_type=='Itemized') {
		$sql = "SELECT id, created_date FROM tbl_invoice_final WHERE client_id = $client_id AND client_case_id = $client_case_id AND display_by = 1 AND is_closed=0 ORDER BY id DESC";
       }
       else {
        $sql = "SELECT id, created_date FROM tbl_invoice_final WHERE client_id = $client_id AND client_case_id = 0 AND display_by = 2 AND is_closed=0 ORDER BY id DESC";
      }

      
      $invoice_data = InvoiceFinal::findBySql($sql)->all();

  		 return $invoice_data;
	 }
	 /* get invoices to merge for client case */
     public function getInvoicestomergeForClientCase($client_id, $client_case_id, $display_type, $invoice_id)
     {
       if($display_type==1) {
		$sql = "SELECT id, created_date FROM tbl_invoice_final WHERE client_id = $client_id AND client_case_id = $client_case_id AND display_by = 1 AND is_closed=0 AND id != $invoice_id ORDER BY id DESC";
       }
       else {
        $sql = "SELECT id, created_date FROM tbl_invoice_final WHERE client_id = $client_id AND client_case_id = 0 AND display_by = 2 AND is_closed=0 AND id != $invoice_id ORDER BY id DESC";
      }

      
      $invoice_data = InvoiceFinal::findBySql($sql)->all();

  		 return $invoice_data;
	 }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClosedUser()
    {
    	return $this->hasOne(User::className(), ['id' => 'closed_by']);
    }
    /**
     * find Accumulate Added
     */
	public function findIfAccumAdded($criteria, $dataarray){
		$index = array();
		if(!empty($dataarray)){
			foreach($dataarray as $key => $value){
				if($criteria['client_id'] == $value['client_id'] && $criteria['client_case_id'] == $value['client_case_id'] && $criteria['team_loc'] == $value['team_loc'] && $criteria['pricing_id'] == $value['pricing_id'] && $criteria['unit_price_id'] == $value['unit_price_id']){
					$index['key'] = $key;
					$index['quantity'] = $value['quantity'];
					$index['pricing_id'] = $value['pricing_id'];
					break;
				}
			}
		}
		return $index;
	}

	/**
     * find if in same client case - with same pricepoint - with same location - with same pricing unit - with same pricing rate exist.
     * If success return array index
     */
	public function findIfSamePPAdded($criteria, $dataarray){
		$index = array();
		if(!empty($dataarray)){
			foreach($dataarray as $key => $value){
				//echo "<br/>".$criteria['client_id']." = ".$value['client_id']." & ".$criteria['client_case_id']." = ".$value['client_case_id']." & ".$criteria['team_loc'] ." == ". $value['team_loc']." && ".$criteria['pricing_id']." == ".$value['pricing_id']." && ".$criteria['unit_price_id']." == ".$value['unit_price_id']." && ".$criteria['final_rate']." == ".$value['final_rate'];
				if($criteria['client_id'] == $value['client_id'] && $criteria['client_case_id'] == $value['client_case_id'] && $criteria['team_loc'] == $value['team_loc'] && $criteria['pricing_id'] == $value['pricing_id'] && $criteria['unit_price_id'] == $value['unit_price_id'] && $criteria['final_rate'] == $value['final_rate']){
					$index['key'] = $key;
					$index['quantity'] = $value['quantity'];
					$index['pricing_id'] = $value['pricing_id'];
					break;
				}
			}
		}
		return $index;
	}

	/**
	 * Chk is accumulated delete
	 */
	public function chkIsAccumulatedDetele($invicebilling_id, $invoice_id)
	{
		$sql = 'SELECT * FROM tbl_invoice_final_billing WHERE billing_unit_id = '.$invicebilling_id.' AND invoice_final_id NOT IN('.$invoice_id.')';
		$dataProvider = \Yii::$app->db->createCommand($sql)->queryAll();

		if(!empty($dataProvider))
			return false;
		else
			return true;
	}

	/**
	 * Wordwrap function for PDF
	 * @return
	 */
	public function smart_wordwrap($string, $width = 75, $break = "\n") {
		// split on problem words over the line length
		$pattern = sprintf('/([^ ]{%d,})/', $width);
		$output = '';
		$words = preg_split($pattern, $string, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
		foreach ($words as $word) {
			if (false !== strpos($word, ' ')) {
				// normal behaviour, rebuild the string
				$output .= $word;
			} else {
				// work out how many characters would be on the current line
				$wrapped = explode($break, wordwrap($output, $width, $break));
				$count = $width - (strlen(end($wrapped)) % $width);
				// fill the current line and add a break
				$output .= substr($word, 0, $count) . $break;
				// wrap any remaining characters from the problem word
				$output .= wordwrap(substr($word, $count), $width, $break, true);
			}
		}
		// wrap the final output
		return wordwrap($output, $width, $break);
	}

	/**
     * @return \yii\db\ActiveQuery
     */
    public function getClient()
    {
        return $this->hasOne(Client::className(), ['id' => 'client_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClientCase()
    {
        return $this->hasOne(ClientCase::className(), ['id' => 'client_case_id']);
    }
}
