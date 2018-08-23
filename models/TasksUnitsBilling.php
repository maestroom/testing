<?php

namespace app\models;

use Yii;
use yii\db\Query;

use \app\models\Options;

/**
 * This is the model class for table "{{%tasks_units_billing}}".
 *
 * @property integer $id
 * @property integer $task_id
 * @property integer $tasks_unit_id
 * @property integer $pricing_id
 * @property integer $evid_num_id
 * @property double $quantity
 * @property string $billing_desc
 * @property string $invoiced
 * @property string $temp_rate
 * @property double $temp_discount
 * @property string $temp_discount_reason
 * @property string $internal_ref_no_id
 * @property string $created
 * @property integer $created_by
 * @property string $modified
 * @property integer $modified_by
 */
class TasksUnitsBilling extends \yii\db\ActiveRecord
{
	public $nonbillableitem;
	public $service_name;
	public $teamservice_id;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%tasks_units_billing}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tasks_unit_id', 'pricing_id', 'quantity'], 'required'],
            [['tasks_unit_id', 'pricing_id', 'evid_num_id', 'created_by', 'modified_by'], 'integer'],
            [['quantity', 'temp_discount'], 'number'],
            [['billing_desc', 'temp_rate', 'temp_discount_reason', 'internal_ref_no_id'], 'string'],
            [['created', 'modified'], 'safe'],
            [['pricing_id'], 'exist', 'skipOnError' => true, 'targetClass' => Pricing::className(), 'targetAttribute' => ['pricing_id' => 'id']],
            /*[['task_id'], 'exist', 'skipOnError' => true, 'targetClass' => Tasks::className(), 'targetAttribute' => ['task_id' => 'id']],*/
            [['tasks_unit_id'], 'exist', 'skipOnError' => true, 'targetClass' => TasksUnits::className(), 'targetAttribute' => ['tasks_unit_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'task_id' => 'Task ID',
            'tasks_unit_id' => 'Tasks Unit ID',
            'pricing_id' => 'Pricing ID',
            'evid_num_id' => 'Evid Num ID',
            'quantity' => 'Quantity',
            'billing_desc' => 'Billing Desc',
            'invoiced' => 'Invoiced',
            'temp_rate' => 'Temp Rate',
            'temp_discount' => 'Temp Discount',
            'temp_discount_reason' => 'Temp Discount Reason',
            'internal_ref_no_id' => 'Internal Ref No ID',
            'created' => 'Created',
            'created_by' => 'Created By',
            'modified' => 'Modified',
            'modified_by' => 'Modified By',
        ];
    }
    /**
     * @inheritdoc
     */
    public function beforeSave($insert){
    	if (parent::beforeSave($insert)) {
    		if($this->isNewRecord){
    			if(!isset($this->created)){
    			$this->created = date('Y-m-d H:i:s');
    			}
    			$this->created_by =Yii::$app->user->identity->id;
    			$this->modified =date('Y-m-d H:i:s');
    			$this->modified_by =Yii::$app->user->identity->id;
    		}else{
    			$this->modified =date('Y-m-d H:i:s');
    			$this->modified_by =Yii::$app->user->identity->id;
    		}
    		return true;
    	} else {
    		return false;
    	}
    }
        
    /**
     * Get rate type for billing item.
     * @clientid ,$caseid,$pricingid,$quantity,$ppname
     */
    public function checkpricingforrate2($clientid,$caseid,$pricingid,$quantity,$ppname="")
    {
    	$ppdat=array();
    	$priceclientcasedata=PricingClientscases::find()->where(['pricing_id'=>$pricingid,'client_id'=>$clientid,'client_case_id'=>$caseid])->one();
    	$ratedata=array();
    	$rate2=false;
    	if(!empty($priceclientcasedata->pricingClientscasesRates)){
    			foreach ($priceclientcasedata->pricingClientscasesRates as $ratedata){
    				if($ratedata->rate_type==2)
    					$rate2=true;
    			}
    	}else{
    		$ratedata=array();
    		$priceclientdata=PricingClients::find()->where(['pricing_id'=>$pricingid,'client_id'=>$clientid])->one();
    		if(!empty($priceclientdata->pricingClientsRates)){
    				foreach ($priceclientdata->pricingClientsRates as $ratedata){
    					if($ratedata->rate_type==2)
    						$rate2=true;;
    				}
    		}else {
    			$ratedata=array();
    			$pricedata=Pricing::findOne($pricingid);
    			if(!empty($pricedata->pricingRates)){
    					foreach ($pricedata->pricingRates as $ratedata){
    						if($ratedata->rate_type==2)
    							$rate2=true;;
    					}
    			}
    				
    		}
    	}
    	return $rate2;
    }
    /**
     * Get rate for billing item.
     * @clientid ,$caseid,$pricingid,$quantity,$ppname,$team_loc
     */
    public function checkpricingforrate($clientid,$caseid,$pricingid,$quantity,$ppname="",$team_loc=0)
    {
		
		static $rates;

    	if($ppname != ""){
    		$pp=Pricing::find()->where(['price_point'=>$ppname])->one();
    		$pricingid=$pp->id;
    	}
		if(isset($rates[$clientid.'|'.$caseid.'|'.$pricingid.'|'.$team_loc])){
			return $rates[$clientid.'|'.$caseid.'|'.$pricingid.'|'.$team_loc];
		}else{
			if(isset($rates['ClientscasesRates'][$pricingid][$caseid])){
				return $rates['ClientscasesRates'][$pricingid][$caseid];
			}
		}
    	//$priceclientcasedata=PricingClientscases::find()->where(['pricing_id'=>$pricingid,'client_case_id'=>$caseid])->one();
		$priceclientcasedata=PricingClientscasesRates::find()->joinWith(['pricingClientscases'],false)->where(['pricing_id'=>$pricingid,'client_case_id'=>$caseid])->all();
    	$rate='';
    	//echo "<pre>$pricingid = $clientid - $caseid";
    	/*if($pricingid == 29 && $clientid == 1 && $caseid ==1 ){
    		echo "<pre>$pricingid = $clientid - $caseid",print_r($priceclientcasedata),"</pre>";
    		die;
    	}*/
    	/*if(!empty($priceclientcasedata->pricingClientscasesRates)){*/
		if(!empty($priceclientcasedata)) {
			//echo $clientid,',',$caseid,',',$pricingid,',',$quantity,',',$ppname,',',$team_loc;
			//die('pccr');
    		$clientdate = array();
    		$clientraterange = array();
    		
    		//echo "<pre>",print_r($priceclientcasedata->pricingClientscasesRates),"</pre>";
    		//new rate location logic//
    		if(!empty($priceclientcasedata)) {
    			foreach($priceclientcasedata as $key=>$val) {

					/*if(!empty($priceclientcasedata->pricingClientscasesRates)) {
    			foreach($priceclientcasedata->pricingClientscasesRates as $key=>$val) {*/

    				if($val->rate_type==2) {//type 2 tier
    					if($val->team_loc==$team_loc) {
    						$clientdate['rate_amount'][] = $val->rate_amount;
    						$clientraterange[] = array(
    								'rate_amount' => $val->rate_amount,
    								'tier_from' => $val->tier_from,
    								'tier_to' => $val->tier_to,
    						);
    					}
    				}
    				if($val->rate_type==1) {//type 1 tier 
    					if($val->team_loc==$team_loc)
    					{
    						$rate=$val->rate_amount;
    						break;
    					}
    				}
    			}
    			if($rate==''){
    				if(!empty($clientraterange)){
    					$rateArray = array();
    					foreach($clientraterange as $cckey => $ccval){
    						if($quantity >= $ccval['tier_from'] && $quantity <= $ccval['tier_to']){
    							$rate = $ccval['rate_amount'];
    						}
    						$rateArray[$ccval['tier_to']] = $ccval['rate_amount'];
    					}
    					if($rate==''){
    						//$rate = $ccval['rate_amount'];
    						if($quantity > 0)
    							$rate = $rateArray[max(array_keys($rateArray))];
    						else
    							$rate = $rateArray[min(array_keys($rateArray))];
    					}
    				}
					$rates['ClientscasesRates'][$pricingid][$caseid]=$rate;
    			}
    		}
    	}
    	
    	if(!is_numeric($rate)){
			if(isset($rates['ClientsRates'][$pricingid][$clientid])){
				return $rates['ClientsRates'][$pricingid][$clientid];
			}
    		//$priceclientdata=PricingClients::find()->where(['pricing_id'=>$pricingid,'client_id'=>$clientid])->one();
    		$priceclientdata=PricingClientsRates::find()->joinWith(['pricingClients'],false)->where(['pricing_id'=>$pricingid,'client_id'=>$clientid])->all();
    		if(!empty($priceclientdata)){
				//echo $clientid,',',$caseid,',',$pricingid,',',$quantity,',',$ppname,',',$team_loc;
				//die('pcr');
    			$clientdate = array();
    			$clientraterange = array();
    			
    			//new rate location logic//
    			if(!empty($priceclientdata)) {
	    			foreach($priceclientdata as $key=>$val) {

					/*if(!empty($priceclientdata->pricingClientsRates)) {
	    			foreach($priceclientdata->pricingClientsRates as $key=>$val) {*/	
	    
		    			if($val->rate_type==2){//type 2 tier
			    			if($val->team_loc==$team_loc){
			    				$clientdate['rate_amount'][] = $val->rate_amount;
			    				$clientraterange[] = array(
			    					'rate_amount' => $val->rate_amount,
			    					'tier_from' => $val->tier_from,
			    					'tier_to' => $val->tier_to,
			    				);
			    			}
		    			}
		    			if($val->rate_type==1){//type 1 tier
		    				if($val->team_loc==$team_loc){
								$rate=$val->rate_amount;
		    					break;
		    				}
						}
	    			}
					if($rate==''){
    					if(!empty($clientraterange)){
							$rateArray = array();
    						foreach($clientraterange as $cckey => $ccval){
    							if($quantity >= $ccval['tier_from'] && $quantity <= $ccval['tier_to']){
    								$rate = $ccval['rate_amount'];
    							}
    							$rateArray[$ccval['tier_to']] = $ccval['rate_amount'];
							}
	    					if($rate==''){
	    						if($quantity > 0)
			    					$rate = $rateArray[max(array_keys($rateArray))];
			    				else
			    					$rate = $rateArray[min(array_keys($rateArray))];
	    					}
    					}
						$rates['ClientsRates'][$pricingid][$clientid]=$rate;
    				}
    			}
    		
    		}
    	}
    	//echo $rate." jjj   ==";
    	if(!is_numeric($rate)){
			if(isset($rates['Rates'][$pricingid])){
				return $rates['Rates'][$pricingid];
			}
			//$pricedata=Pricing::findOne($pricingid);
			$pricedata=PricingRates::find()->where(['pricing_id'=>$pricingid])->all();
    		$clientdate = array();
    		$clientraterange = array();
    		
    		//new rate location logic//
    		if(!empty($pricedata)){
				//echo $clientid,',',$caseid,',',$pricingid,',',$quantity,',',$ppname,',',$team_loc;
				//die('pr');
    			foreach($pricedata as $key=>$val) {
        			if($val->rate_type==2){//type 2 tier
	    				if($val->team_loc==$team_loc){
		    				$clientdate['rate_amount'][] = $val->rate_amount;
		    				$clientraterange[] = array(
		    				'rate_amount' => $val->rate_amount,
		    				'tier_from' => $val->tier_from,
		    				'tier_to' => $val->tier_to,
		    				);
		    			}
    				}
    				if($val->rate_type==1){//type 1 tier
	    				if($val->team_loc==$team_loc){
	    					$rate=$val->rate_amount;
	    					break;
	    				}
    				}
    				
    				if($val->rate_type==0){//type 0 shared rate logic
    					$rate=$val->rate_amount;
    					break;
    				}
    			}
    			//echo "<pre>$clientid : $caseid = $pricingid - QTY = $quantity",print_r($clientraterange);
    			if($rate==''){
    				if(!empty($clientraterange)){
    					$rateArray = array();
    					foreach($clientraterange as $cckey => $ccval){
    						if($quantity >= $ccval['tier_from'] && $quantity <= $ccval['tier_to']){
    							$rate = $ccval['rate_amount'];
    						}
    						$rateArray[$ccval['tier_to']] = $ccval['rate_amount'];
    					}
						if($rate==''){
	    					if($quantity > 0)
	    						$rate = $rateArray[max(array_keys($rateArray))];
	    					else
	    						$rate = $rateArray[min(array_keys($rateArray))];
    					}
    				}
					$rates['Rates'][$pricingid]=$rate;
    			}
    
    		}
    	
	    }
		$rates[$clientid.'|'.$caseid.'|'.$pricingid.'|'.$team_loc]=$rate==''?0:$rate;
    	return $rate==''?0:$rate;
	}
	public function getTaskUnitBillingClienCaseData($clientcase_id=0, $team_id=0, $from_date, $end_date,$clientSqlAll=''){
		if($from_date != ''){
			$dateformAr = explode("/",$from_date);
			$from_date = $dateformAr[2]."-".$dateformAr[0]."-".$dateformAr[1]; 
		}
		
		if($end_date != ''){
			$dateformAr = explode("/",$end_date);
			$end_date = $dateformAr[2]."-".$dateformAr[0]."-".$dateformAr[1];
		}
		$timezoneOffset = (new Options)->getOffsetOfCurrenttimeZone();
		$clientcasesql = '';
		if($clientcase_id!=0) {
			$clientcasesql = " AND tasks.client_case_id IN ($clientcase_id) ";
		}
		if($clientSqlAll != '') {
			$clientRawSqlAll = " AND tasks.client_case_id IN ($clientSqlAll) ";
		}
		$teamsql = '';
		if($team_id!=0) {
			$teamsql = " AND taskunit.team_id IN ($team_id) ";
		}
		
		if (Yii::$app->db->driverName == 'mysql') {
			$sqldate = "DATE_FORMAT(CONVERT_TZ(unitbilling.created,'+00:00','{$timezoneOffset}'), '%Y-%m-%d') >= '{$from_date}' AND DATE_FORMAT( CONVERT_TZ(unitbilling.created,'+00:00','{$timezoneOffset}'), '%Y-%m-%d') <= '{$end_date}'";
    	} else {
			$sqldate = "CAST(switchoffset(todatetimeoffset(Cast(unitbilling.created as datetime), '+00:00'), '{$timezoneOffset}') as date) >= '{$from_date}' AND CAST(switchoffset(todatetimeoffset(Cast(unitbilling.created as datetime), '+00:00'), '{$timezoneOffset}') as date) <= '{$end_date}'";
    	}
		$sql = "SELECT * FROM (
			SELECT clientcase.client_id, client.client_name, tasks.client_case_id, clientcase.case_name
			FROM tbl_invoice_final as invoicefinal
			INNER JOIN tbl_invoice_final_billing as finalbilling ON finalbilling.invoice_final_id = invoicefinal.id
			INNER JOIN tbl_tasks_units_billing as unitbilling ON unitbilling.id = finalbilling.billing_unit_id
			INNER JOIN tbl_pricing as pricing ON unitbilling.pricing_id = pricing.id
			INNER JOIN tbl_tasks_units as taskunit ON unitbilling.tasks_unit_id = taskunit.id
			INNER JOIN tbl_tasks as tasks ON tasks.id = taskunit.task_id
			INNER JOIN tbl_client_case as clientcase ON tasks.client_case_id = clientcase.id
			INNER JOIN tbl_client as client ON clientcase.client_id = client.id
			INNER JOIN tbl_servicetask as servicetask ON servicetask.id = taskunit.servicetask_id
			WHERE pricing.accum_cost = 1 AND unitbilling.invoiced = 1 AND servicetask.billable_item IN (1,2) AND clientcase.is_close = 0 $clientcasesql $teamsql $clientRawSqlAll
			UNION ALL
			SELECT clientcase.client_id, client.client_name, tasks.client_case_id, clientcase.case_name
			FROM tbl_tasks_units_billing as unitbilling
			INNER JOIN tbl_tasks_units as taskunit ON unitbilling.tasks_unit_id = taskunit.id
			INNER JOIN tbl_tasks as tasks ON tasks.id = taskunit.task_id
			INNER JOIN tbl_client_case as clientcase ON tasks.client_case_id = clientcase.id
			INNER JOIN tbl_client as client ON clientcase.client_id = client.id
			INNER JOIN tbl_servicetask as servicetask ON servicetask.id = taskunit.servicetask_id
			WHERE $sqldate AND (unitbilling.invoiced IS NULL OR unitbilling.invoiced = 0 OR unitbilling.invoiced = 2) AND (servicetask.billable_item IN (1,2)) AND clientcase.is_close = 0 $clientcasesql $teamsql $clientRawSqlAll
		) as billableunits
		GROUP BY billableunits.client_id, billableunits.client_name, billableunits.client_case_id, billableunits.case_name
		ORDER BY  billableunits.client_name ASC, billableunits.case_name ASC";
		//echo $sql;die;
		$connection = \Yii::$app->db;
		$model = $connection->createCommand($sql);
		return $model->queryAll();
	}
	public function getTaskUnitBillingItemizedData($client_case_id,$team_id=0, $from_date, $end_date){
		if($from_date != ''){
			$dateformAr = explode("/",$from_date);
			$from_date = $dateformAr[2]."-".$dateformAr[0]."-".$dateformAr[1]; 
		}
		
		if($end_date != ''){
			$dateformAr = explode("/",$end_date);
			$end_date = $dateformAr[2]."-".$dateformAr[0]."-".$dateformAr[1];
		}
		
		$timezoneOffset = (new Options)->getOffsetOfCurrenttimeZone();
		$clientcasesql = '';
		if($client_id!=0){
			$clientcasesql = " AND tasks.client_case_id IN ($client_case_id)";
		}
		$teamsql = '';
		if($team_id!=0){
			$teamsql = " AND taskunit.team_id IN ($team_id) ";
		}
		
		if (Yii::$app->db->driverName == 'mysql') {
			$sqldate = "DATE_FORMAT(CONVERT_TZ(unitbilling.created,'+00:00','{$timezoneOffset}'), '%Y-%m-%d') >= '{$from_date}' AND DATE_FORMAT( CONVERT_TZ(unitbilling.created,'+00:00','{$timezoneOffset}'), '%Y-%m-%d') <= '{$end_date}'";
    	} else {
			$sqldate = "CAST(switchoffset(todatetimeoffset(Cast(unitbilling.created as datetime), '+00:00'), '{$timezoneOffset}') as date) >= '{$from_date}' AND CAST(switchoffset(todatetimeoffset(Cast(unitbilling.created as datetime), '+00:00'), '{$timezoneOffset}') as date) <= '{$end_date}'";
    	}
		$sqlistiered = "(SELECT CASE WHEN EXISTS(
				SELECT caserates.id FROM tbl_pricing_clientscases_rates as caserates 
				INNER JOIN tbl_pricing_clientscases as pricingcases ON caserates.pricing_clientscases_id = pricingcases.id WHERE pricingcases.pricing_id = pricing.id  AND pricingcases.client_case_id = tasks.client_case_id AND caserates.rate_type=2
			) THEN 1 ELSE CASE WHEN EXISTS(SELECT clientsrates.id FROM tbl_pricing_clients_rates as clientsrates INNER JOIN tbl_pricing_clients as pricingclients ON clientsrates.pricing_clients_id = pricingclients.id WHERE pricingclients.pricing_id = pricing.id AND pricingclients.client_id = clientcase.client_id AND clientsrates.rate_type=2) THEN 1 ELSE CASE WHEN EXISTS(SELECT rates.id FROM tbl_pricing_rates as rates WHERE rates.pricing_id = pricing.id AND rates.rate_type=2) THEN 1 ELSE 0 END END END)";
		
    	//$sqldescription = "(SELECT CASE WHEN EXISTS(SELECT id FROM tbl_tasks_units_billing as taskunitbilling WHERE taskunitbilling.billing_desc IS NOT NULL AND taskunitbilling.billing_desc <> '' AND unitbilling.id = taskunitbilling.id) THEN unitbilling.billing_desc ELSE pricing.description END)";
    	$sqldescription = "unitbilling.billing_desc";

		$sql = "SELECT * FROM (
			SELECT unitbilling.id as unitbilling_id, unitbilling.tasks_unit_id, 'Accumulated' as created, invoicefinal.id as invoicefinal_id, clientcase.client_id, client.client_name, tasks.client_case_id, clientcase.case_name, pricing.id as pricing_id, pricing.price_point, pricing.unit_price_id, unitprice.unit_name, 
			pricing.accum_cost, tasks.id as task_id, taskunit.team_loc, teamloc.team_location_name, pricing.description as description, 
			unitbilling.invoiced, unitbilling.quantity, unitbilling.temp_rate, unitbilling.temp_discount, unitbilling.temp_discount_reason, unitbilling.internal_ref_no_id, $sqlistiered as istieredrate
			FROM tbl_invoice_final as invoicefinal
			INNER JOIN tbl_invoice_final_billing as finalbilling ON finalbilling.invoice_final_id = invoicefinal.id
			INNER JOIN tbl_tasks_units_billing as unitbilling ON unitbilling.id = finalbilling.billing_unit_id
			INNER JOIN tbl_pricing as pricing ON unitbilling.pricing_id = pricing.id
			INNER JOIN tbl_unit as unitprice ON unitprice.id = pricing.unit_price_id
			INNER JOIN tbl_tasks_units as taskunit ON unitbilling.tasks_unit_id = taskunit.id
			INNER JOIN tbl_teamlocation_master as teamloc ON teamloc.id = taskunit.team_loc
			INNER JOIN tbl_tasks as tasks ON tasks.id = taskunit.task_id
			INNER JOIN tbl_client_case as clientcase ON tasks.client_case_id = clientcase.id
			INNER JOIN tbl_client as client ON clientcase.client_id = client.id
			INNER JOIN tbl_servicetask as servicetask ON servicetask.id = taskunit.servicetask_id
			WHERE pricing.accum_cost = 1 AND unitbilling.invoiced = 1 AND servicetask.billable_item IN (1,2) AND clientcase.is_close = 0 $clientcasesql $teamsql 
			UNION ALL
			SELECT unitbilling.id as unitbilling_id, unitbilling.tasks_unit_id, CAST(unitbilling.created as CHAR) as created, 0 as invoicefinal_id, clientcase.client_id, client.client_name, tasks.client_case_id, clientcase.case_name, pricing.id as pricing_id, pricing.price_point, pricing.unit_price_id, unitprice.unit_name, 
			pricing.accum_cost, tasks.id as task_id, taskunit.team_loc, teamloc.team_location_name, $sqldescription as description, 
			unitbilling.invoiced, unitbilling.quantity, unitbilling.temp_rate, unitbilling.temp_discount, unitbilling.temp_discount_reason, unitbilling.internal_ref_no_id, $sqlistiered as istieredrate
			FROM tbl_tasks_units_billing as unitbilling
			INNER JOIN tbl_pricing as pricing ON unitbilling.pricing_id = pricing.id
			INNER JOIN tbl_unit as unitprice ON unitprice.id = pricing.unit_price_id
			INNER JOIN tbl_tasks_units as taskunit ON unitbilling.tasks_unit_id = taskunit.id
			INNER JOIN tbl_teamlocation_master as teamloc ON teamloc.id = taskunit.team_loc
			INNER JOIN tbl_tasks as tasks ON tasks.id = taskunit.task_id
			INNER JOIN tbl_client_case as clientcase ON tasks.client_case_id = clientcase.id
			INNER JOIN tbl_client as client ON clientcase.client_id = client.id
			INNER JOIN tbl_servicetask as servicetask ON servicetask.id = taskunit.servicetask_id
			WHERE $sqldate AND (unitbilling.invoiced IS NULL OR unitbilling.invoiced = 0 OR unitbilling.invoiced = 2) AND (servicetask.billable_item IN (1,2)) AND clientcase.is_close = 0 $clientcasesql $teamsql 
		) as billableunits
		ORDER BY  billableunits.client_name ASC, billableunits.case_name ASC, billableunits.accum_cost DESC, billableunits.created DESC";
		
		$connection = \Yii::$app->db;
		$model = $connection->createCommand($sql);
		return $model->queryAll();
	}
	public function getTaskUnitBillingConsolidatedData($client_id,$team_id=0, $from_date, $end_date){
		if($from_date != ''){
			$dateformAr = explode("/",$from_date);
			$from_date = $dateformAr[2]."-".$dateformAr[0]."-".$dateformAr[1]; 
		}
		
		if($end_date != ''){
			$dateformAr = explode("/",$end_date);
			$end_date = $dateformAr[2]."-".$dateformAr[0]."-".$dateformAr[1];
		}
		
		$timezoneOffset = (new Options)->getOffsetOfCurrenttimeZone();
		$clientcasesql = '';
		if($client_id!=0){
			$clientcasesql = " AND tasks.client_case_id IN (SELECT id FROM tbl_client_case WHERE is_close=0 and client_id=$client_id)";
		}
		$teamsql = '';
		if($team_id!=0){
			$teamsql = " AND taskunit.team_id IN ($team_id) ";
		}
		
		if (Yii::$app->db->driverName == 'mysql') {
			$sqldate = "DATE_FORMAT(CONVERT_TZ(unitbilling.created,'+00:00','{$timezoneOffset}'), '%Y-%m-%d') >= '{$from_date}' AND DATE_FORMAT( CONVERT_TZ(unitbilling.created,'+00:00','{$timezoneOffset}'), '%Y-%m-%d') <= '{$end_date}'";
    	} else {
			$sqldate = "CAST(switchoffset(todatetimeoffset(Cast(unitbilling.created as datetime), '+00:00'), '{$timezoneOffset}') as date) >= '{$from_date}' AND CAST(switchoffset(todatetimeoffset(Cast(unitbilling.created as datetime), '+00:00'), '{$timezoneOffset}') as date) <= '{$end_date}'";
    	}
		$sqlistiered = "(SELECT CASE WHEN EXISTS(
				SELECT caserates.id FROM tbl_pricing_clientscases_rates as caserates 
				INNER JOIN tbl_pricing_clientscases as pricingcases ON caserates.pricing_clientscases_id = pricingcases.id WHERE pricingcases.pricing_id = pricing.id  AND pricingcases.client_case_id = tasks.client_case_id AND caserates.rate_type=2
			) THEN 1 ELSE CASE WHEN EXISTS(SELECT clientsrates.id FROM tbl_pricing_clients_rates as clientsrates INNER JOIN tbl_pricing_clients as pricingclients ON clientsrates.pricing_clients_id = pricingclients.id WHERE pricingclients.pricing_id = pricing.id AND pricingclients.client_id = clientcase.client_id AND clientsrates.rate_type=2) THEN 1 ELSE CASE WHEN EXISTS(SELECT rates.id FROM tbl_pricing_rates as rates WHERE rates.pricing_id = pricing.id AND rates.rate_type=2) THEN 1 ELSE 0 END END END)";
		
    	//$sqldescription = "(SELECT CASE WHEN EXISTS(SELECT id FROM tbl_tasks_units_billing as taskunitbilling WHERE taskunitbilling.billing_desc IS NOT NULL AND taskunitbilling.billing_desc <> '' AND unitbilling.id = taskunitbilling.id) THEN unitbilling.billing_desc ELSE pricing.description END)";
    	$sqldescription = "unitbilling.billing_desc";

		$sql = "SELECT * FROM (
			SELECT unitbilling.id as unitbilling_id, unitbilling.tasks_unit_id, 'Accumulated' as created, invoicefinal.id as invoicefinal_id, clientcase.client_id, client.client_name, tasks.client_case_id, clientcase.case_name, pricing.id as pricing_id, pricing.price_point, pricing.unit_price_id, unitprice.unit_name, 
			pricing.accum_cost, tasks.id as task_id, taskunit.team_loc, teamloc.team_location_name, pricing.description as description, 
			unitbilling.invoiced, unitbilling.quantity, unitbilling.temp_rate, unitbilling.temp_discount, unitbilling.temp_discount_reason, unitbilling.internal_ref_no_id, $sqlistiered as istieredrate
			FROM tbl_invoice_final as invoicefinal
			INNER JOIN tbl_invoice_final_billing as finalbilling ON finalbilling.invoice_final_id = invoicefinal.id
			INNER JOIN tbl_tasks_units_billing as unitbilling ON unitbilling.id = finalbilling.billing_unit_id
			INNER JOIN tbl_pricing as pricing ON unitbilling.pricing_id = pricing.id
			INNER JOIN tbl_unit as unitprice ON unitprice.id = pricing.unit_price_id
			INNER JOIN tbl_tasks_units as taskunit ON unitbilling.tasks_unit_id = taskunit.id
			INNER JOIN tbl_teamlocation_master as teamloc ON teamloc.id = taskunit.team_loc
			INNER JOIN tbl_tasks as tasks ON tasks.id = taskunit.task_id
			INNER JOIN tbl_client_case as clientcase ON tasks.client_case_id = clientcase.id
			INNER JOIN tbl_client as client ON clientcase.client_id = client.id
			INNER JOIN tbl_servicetask as servicetask ON servicetask.id = taskunit.servicetask_id
			WHERE pricing.accum_cost = 1 AND unitbilling.invoiced = 1 AND servicetask.billable_item IN (1,2) AND clientcase.is_close = 0 $clientcasesql $teamsql 
			UNION ALL
			SELECT unitbilling.id as unitbilling_id, unitbilling.tasks_unit_id, CAST(unitbilling.created as CHAR) as created, 0 as invoicefinal_id, clientcase.client_id, client.client_name, tasks.client_case_id, clientcase.case_name, pricing.id as pricing_id, pricing.price_point, pricing.unit_price_id, unitprice.unit_name, 
			pricing.accum_cost, tasks.id as task_id, taskunit.team_loc, teamloc.team_location_name, $sqldescription as description, 
			unitbilling.invoiced, unitbilling.quantity, unitbilling.temp_rate, unitbilling.temp_discount, unitbilling.temp_discount_reason, unitbilling.internal_ref_no_id, $sqlistiered as istieredrate
			FROM tbl_tasks_units_billing as unitbilling
			INNER JOIN tbl_pricing as pricing ON unitbilling.pricing_id = pricing.id
			INNER JOIN tbl_unit as unitprice ON unitprice.id = pricing.unit_price_id
			INNER JOIN tbl_tasks_units as taskunit ON unitbilling.tasks_unit_id = taskunit.id
			INNER JOIN tbl_teamlocation_master as teamloc ON teamloc.id = taskunit.team_loc
			INNER JOIN tbl_tasks as tasks ON tasks.id = taskunit.task_id
			INNER JOIN tbl_client_case as clientcase ON tasks.client_case_id = clientcase.id
			INNER JOIN tbl_client as client ON clientcase.client_id = client.id
			INNER JOIN tbl_servicetask as servicetask ON servicetask.id = taskunit.servicetask_id
			WHERE $sqldate AND (unitbilling.invoiced IS NULL OR unitbilling.invoiced = 0 OR unitbilling.invoiced = 2) AND (servicetask.billable_item IN (1,2)) AND clientcase.is_close = 0 $clientcasesql $teamsql 
		) as billableunits
		ORDER BY  billableunits.client_name ASC, billableunits.case_name ASC, billableunits.accum_cost DESC, billableunits.created DESC";
		
		$connection = \Yii::$app->db;
		$model = $connection->createCommand($sql);
		return $model->queryAll();
		
	}
	
	public function getTaskUnitBillingData($clientcase_id=0, $team_id=0, $from_date, $end_date,$clientSqlAll=''){
		//echo $clientcase_id," == = ",$team_id;
		if($from_date != ''){
			$dateformAr = explode("/",$from_date);
			$from_date = $dateformAr[2]."-".$dateformAr[0]."-".$dateformAr[1]; 
		}
		
		if($end_date != ''){
			$dateformAr = explode("/",$end_date);
			$end_date = $dateformAr[2]."-".$dateformAr[0]."-".$dateformAr[1];
		}
		
		$timezoneOffset = (new Options)->getOffsetOfCurrenttimeZone();
		$clientcasesql = '';
		if($clientcase_id!=0){
			$clientcasesql = " AND tasks.client_case_id IN ($clientcase_id) ";
		}
		if($clientSqlAll != ''){
			$clientRawSqlAll = " AND tasks.client_case_id IN ($clientSqlAll) ";
		}
		$teamsql = '';
		if($team_id!=0){
			$teamsql = " AND taskunit.team_id IN ($team_id) ";
		}
		
		if (Yii::$app->db->driverName == 'mysql') {
			$sqldate = "DATE_FORMAT(CONVERT_TZ(unitbilling.created,'+00:00','{$timezoneOffset}'), '%Y-%m-%d') >= '{$from_date}' AND DATE_FORMAT( CONVERT_TZ(unitbilling.created,'+00:00','{$timezoneOffset}'), '%Y-%m-%d') <= '{$end_date}'";
    	} else {
			$sqldate = "CAST(switchoffset(todatetimeoffset(Cast(unitbilling.created as datetime), '+00:00'), '{$timezoneOffset}') as date) >= '{$from_date}' AND CAST(switchoffset(todatetimeoffset(Cast(unitbilling.created as datetime), '+00:00'), '{$timezoneOffset}') as date) <= '{$end_date}'";
    	}
    	
    	$sqlistiered = "(SELECT CASE WHEN EXISTS(
				SELECT caserates.id FROM tbl_pricing_clientscases_rates as caserates 
				INNER JOIN tbl_pricing_clientscases as pricingcases ON caserates.pricing_clientscases_id = pricingcases.id WHERE pricingcases.pricing_id = pricing.id  AND pricingcases.client_case_id = tasks.client_case_id AND caserates.rate_type=2
			) THEN 1 ELSE CASE WHEN EXISTS(SELECT clientsrates.id FROM tbl_pricing_clients_rates as clientsrates INNER JOIN tbl_pricing_clients as pricingclients ON clientsrates.pricing_clients_id = pricingclients.id WHERE pricingclients.pricing_id = pricing.id AND pricingclients.client_id = clientcase.client_id AND clientsrates.rate_type=2) THEN 1 ELSE CASE WHEN EXISTS(SELECT rates.id FROM tbl_pricing_rates as rates WHERE rates.pricing_id = pricing.id AND rates.rate_type=2) THEN 1 ELSE 0 END END END)";
		
    	//$sqldescription = "(SELECT CASE WHEN EXISTS(SELECT id FROM tbl_tasks_units_billing as taskunitbilling WHERE taskunitbilling.billing_desc IS NOT NULL AND taskunitbilling.billing_desc <> '' AND unitbilling.id = taskunitbilling.id) THEN unitbilling.billing_desc ELSE pricing.description END)";
    	$sqldescription = "unitbilling.billing_desc";
    	
		/*$sql = "SELECT * FROM (
			SELECT unitbilling.id as unitbilling_id, unitbilling.tasks_unit_id, 'Accumulated' as created, invoicefinal.id as invoicefinal_id, clientcase.client_id, client.client_name, tasks.client_case_id, clientcase.case_name, pricing.id as pricing_id, pricing.price_point, pricing.unit_price_id, unitprice.unit_price_name, 
			pricing.accum_cost, tasks.id as task_id, taskunit.team_loc, teamloc.team_location_name, pricing.description as description, 
			unitbilling.invoiced, unitbilling.quantity, unitbilling.temp_rate, unitbilling.temp_discount, unitbilling.temp_discount_reason, unitbilling.internal_ref_no_id, $sqlistiered as istieredrate
			FROM tbl_invoice_final as invoicefinal
			INNER JOIN tbl_invoice_final_billing as finalbilling ON finalbilling.invoice_final_id = invoicefinal.id
			INNER JOIN tbl_tasks_units_billing as unitbilling ON unitbilling.id = finalbilling.billing_unit_id
			INNER JOIN tbl_pricing as pricing ON unitbilling.pricing_id = pricing.id
			INNER JOIN tbl_unit_price as unitprice ON unitprice.id = pricing.unit_price_id
			INNER JOIN tbl_tasks_units as taskunit ON unitbilling.tasks_unit_id = taskunit.id
			INNER JOIN tbl_teamlocation_master as teamloc ON teamloc.id = taskunit.team_loc
			INNER JOIN tbl_tasks as tasks ON tasks.id = taskunit.task_id
			INNER JOIN tbl_client_case as clientcase ON tasks.client_case_id = clientcase.id
			INNER JOIN tbl_client as client ON clientcase.client_id = client.id
			INNER JOIN tbl_servicetask as servicetask ON servicetask.id = taskunit.servicetask_id
			WHERE pricing.accum_cost = 1 AND unitbilling.invoiced = 1 AND servicetask.billable_item IN (1,2) AND clientcase.is_close = 0 $clientcasesql $teamsql $clientRawSqlAll
			UNION ALL
			SELECT unitbilling.id as unitbilling_id, unitbilling.tasks_unit_id, CAST(unitbilling.created as CHAR) as created, 0 as invoicefinal_id, clientcase.client_id, client.client_name, tasks.client_case_id, clientcase.case_name, pricing.id as pricing_id, pricing.price_point, pricing.unit_price_id, unitprice.unit_price_name, 
			pricing.accum_cost, tasks.id as task_id, taskunit.team_loc, teamloc.team_location_name, $sqldescription as description, 
			unitbilling.invoiced, unitbilling.quantity, unitbilling.temp_rate, unitbilling.temp_discount, unitbilling.temp_discount_reason, unitbilling.internal_ref_no_id, $sqlistiered as istieredrate
			FROM tbl_tasks_units_billing as unitbilling
			INNER JOIN tbl_pricing as pricing ON unitbilling.pricing_id = pricing.id
			INNER JOIN tbl_unit_price as unitprice ON unitprice.id = pricing.unit_price_id
			INNER JOIN tbl_tasks_units as taskunit ON unitbilling.tasks_unit_id = taskunit.id
			INNER JOIN tbl_teamlocation_master as teamloc ON teamloc.id = taskunit.team_loc
			INNER JOIN tbl_tasks as tasks ON tasks.id = taskunit.task_id
			INNER JOIN tbl_client_case as clientcase ON tasks.client_case_id = clientcase.id
			INNER JOIN tbl_client as client ON clientcase.client_id = client.id
			INNER JOIN tbl_servicetask as servicetask ON servicetask.id = taskunit.servicetask_id
			WHERE $sqldate AND (unitbilling.invoiced IS NULL OR unitbilling.invoiced = 0 OR unitbilling.invoiced = 2) AND (servicetask.billable_item IN (1,2)) AND clientcase.is_close = 0 $clientcasesql $teamsql $clientRawSqlAll
		) as billableunits
		ORDER BY  billableunits.client_name ASC, billableunits.case_name ASC, billableunits.accum_cost DESC, billableunits.created DESC";
		*/
		$sql = "SELECT * FROM (
			SELECT unitbilling.id as unitbilling_id, unitbilling.tasks_unit_id, 'Accumulated' as created, invoicefinal.id as invoicefinal_id, clientcase.client_id, client.client_name, tasks.client_case_id, clientcase.case_name, pricing.id as pricing_id, pricing.price_point, pricing.unit_price_id, unitprice.unit_name, 
			pricing.accum_cost, tasks.id as task_id, taskunit.team_loc, teamloc.team_location_name, pricing.description as description, 
			unitbilling.invoiced, unitbilling.quantity, unitbilling.temp_rate, unitbilling.temp_discount, unitbilling.temp_discount_reason, unitbilling.internal_ref_no_id, $sqlistiered as istieredrate
			FROM tbl_invoice_final as invoicefinal
			INNER JOIN tbl_invoice_final_billing as finalbilling ON finalbilling.invoice_final_id = invoicefinal.id
			INNER JOIN tbl_tasks_units_billing as unitbilling ON unitbilling.id = finalbilling.billing_unit_id
			INNER JOIN tbl_pricing as pricing ON unitbilling.pricing_id = pricing.id
			INNER JOIN tbl_unit as unitprice ON unitprice.id = pricing.unit_price_id
			INNER JOIN tbl_tasks_units as taskunit ON unitbilling.tasks_unit_id = taskunit.id
			INNER JOIN tbl_teamlocation_master as teamloc ON teamloc.id = taskunit.team_loc
			INNER JOIN tbl_tasks as tasks ON tasks.id = taskunit.task_id
			INNER JOIN tbl_client_case as clientcase ON tasks.client_case_id = clientcase.id
			INNER JOIN tbl_client as client ON clientcase.client_id = client.id
			INNER JOIN tbl_servicetask as servicetask ON servicetask.id = taskunit.servicetask_id
			WHERE pricing.accum_cost = 1 AND unitbilling.invoiced = 1 AND servicetask.billable_item IN (1,2) AND clientcase.is_close = 0 $clientcasesql $teamsql $clientRawSqlAll
			UNION ALL
			SELECT unitbilling.id as unitbilling_id, unitbilling.tasks_unit_id, CAST(unitbilling.created as CHAR) as created, 0 as invoicefinal_id, clientcase.client_id, client.client_name, tasks.client_case_id, clientcase.case_name, pricing.id as pricing_id, pricing.price_point, pricing.unit_price_id, unitprice.unit_name, 
			pricing.accum_cost, tasks.id as task_id, taskunit.team_loc, teamloc.team_location_name, $sqldescription as description, 
			unitbilling.invoiced, unitbilling.quantity, unitbilling.temp_rate, unitbilling.temp_discount, unitbilling.temp_discount_reason, unitbilling.internal_ref_no_id, $sqlistiered as istieredrate
			FROM tbl_tasks_units_billing as unitbilling
			INNER JOIN tbl_pricing as pricing ON unitbilling.pricing_id = pricing.id
			INNER JOIN tbl_unit as unitprice ON unitprice.id = pricing.unit_price_id
			INNER JOIN tbl_tasks_units as taskunit ON unitbilling.tasks_unit_id = taskunit.id
			INNER JOIN tbl_teamlocation_master as teamloc ON teamloc.id = taskunit.team_loc
			INNER JOIN tbl_tasks as tasks ON tasks.id = taskunit.task_id
			INNER JOIN tbl_client_case as clientcase ON tasks.client_case_id = clientcase.id
			INNER JOIN tbl_client as client ON clientcase.client_id = client.id
			INNER JOIN tbl_servicetask as servicetask ON servicetask.id = taskunit.servicetask_id
			WHERE $sqldate AND (unitbilling.invoiced IS NULL OR unitbilling.invoiced = 0 OR unitbilling.invoiced = 2) AND (servicetask.billable_item IN (1,2)) AND clientcase.is_close = 0 $clientcasesql $teamsql $clientRawSqlAll
		) as billableunits
		ORDER BY  billableunits.client_name ASC, billableunits.case_name ASC, billableunits.accum_cost DESC, billableunits.created DESC";
		
		$connection = \Yii::$app->db;
		$model = $connection->createCommand($sql);
		return $model->queryAll();
	}
	
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedUser(){
    	return $this->hasOne(User::className(), ['id' => 'created_by'])->alias('user');
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPricing(){
    	return $this->hasOne(Pricing::className(), ['id' => 'pricing_id']);
    }
    
	/**
     * @return \yii\db\ActiveQuery
     */
    public function getTasks(){
    	return $this->hasOne(Tasks::className(), ['id' => 'task_id'])->alias('tasks');
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTasksUnits()
    {
    	return $this->hasOne(TasksUnits::className(), ['id' => 'tasks_unit_id']);
    }
}
