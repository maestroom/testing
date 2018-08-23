<?php
namespace app\controllers;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use app\models\ProjectSecurity;
use app\models\Options;
use app\models\TasksUnitsBilling;
use app\models\InvoiceFinal;
use app\models\InvoiceFinalBilling;
use app\models\Client;
use app\models\ClientCase;
use app\models\User;
use app\models\search\InvoiceFinalSearch;
use app\models\ClientContacts;
use app\models\CaseContacts;
use app\models\TeamlocationMaster;
use app\models\ActivityLog;

class BillingFinalizedInvoiceController extends \yii\web\Controller
{

	public function beforeAction($action)
	{
		if (Yii::$app->user->isGuest)
			$this->redirect(array('site/login'));

		if (!(new User)->checkAccess(7) || !(new User)->checkAccess(7.15)){
			throw new \yii\web\HttpException(404, 'User permissions do not allow entry into this module.');
		}
		return parent::beforeAction($action);
	}


    public function actionFinalizedInvoices()
    {
		$this->layout = 'billing';
        $searchModel = new InvoiceFinalSearch();
        $params	=	Yii::$app->request->queryParams;
				$params['is_closed'] = 0;
		$params['grid_id']='dynagrid-final-invoiced';
        Yii::$app->request->queryParams +=$params;
        //echo '<pre>',print_r($params),'</pre>';die;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		//echo "<pre>",print_r($dataProvider->getModels()),"</pre>";
		//die;

        /*IRT 67,68,86,87,258*/
        /*IRT 96,398 Code Starts*/
        if(isset($params['InvoiceFinalSearch']['client_case_id']) && !empty($params['InvoiceFinalSearch']['client_case_id'])) {
            $client_case_selected =(new User)->getSelectedGridCases($params['InvoiceFinalSearch']['client_case_id']);
            if($client_case_selected == 'ALL') {
                unset($params['InvoiceFinalSearch']['client_case_id']);
                $client_case_selected = array();
            }
        }
        if(isset($params['InvoiceFinalSearch']['client_id']) && !empty($params['InvoiceFinalSearch']['client_id'])){
            $clients_selected =(new User)->getSelectedGridClients($params['InvoiceFinalSearch']['client_id']);
            if($clients_selected == 'ALL'){
                unset($params['InvoiceFinalSearch']['client_id']);
                $clients_selected = array();
            }
        }
        /*IRT 96,398 Code Code Ends */
        $filter_type=\app\models\User::getFilterType(['tbl_invoice_final.id','tbl_invoice_final.client_id','tbl_invoice_final.created_date', 'totalinvoiceamt','tbl_invoice_final.client_case_id'],['tbl_invoice_final']);
        $config = [];
        $config_widget_options = [
			'totalinvoiceamt'=>[
				'filter_type'=>'range',
				'options' => ['placeholder' => 'Rate (0 - 1000)'],
				'html5Options' => ['min' => 0, 'max' => 1000, 'step'=>1]
			],
			'client_case_id'=>['initValueText' => $client_case_selected],'client_id'=>['initValueText' => $clients_selected]
        ];
        $filterWidgetOption=\app\models\User::getFilterWidgetOption($filter_type,Url::toRoute(['billing-finalized-invoice/ajax-finalized-invoice-filter']),$config,$config_widget_options);
        $sum=0;//$sum=(new InvoiceFinal)->getSumTotalInvoiceAmount();
        /*IRT 67,68,86,87,258*/
		if(Yii::$app->request->isAjax)
			return $this->renderAjax('final_invoiced',['sum'=>$sum,'filter_type'=>$filter_type,'filterWidgetOption'=>$filterWidgetOption,'dataProvider' => $dataProvider, 'searchModel' => $searchModel]);
		else
        	return $this->render('final_invoiced',['sum'=>$sum,'filter_type'=>$filter_type,'filterWidgetOption'=>$filterWidgetOption,'dataProvider' => $dataProvider, 'searchModel' => $searchModel]);
    }

    /**
     * Filter Ajax Invoice
     */
    public function actionAjaxFinalizedInvoiceFilter()
    {
        $searchModel = new InvoiceFinalSearch();
        $params = Yii::$app->request->queryParams;
				$params['is_closed'] = 0;
        $dataProvider = $searchModel->searchFilter($params);
        $out['results']=array();
		foreach ($dataProvider as $key=>$val){
			if($params['field'] == 'client_id' || $params['field'] == 'client_case_id')
				$id_val = $key;
			else
				$id_val = $val;
			$out['results'][] = ['id' => $id_val, 'text' => $val, 'label' => $val];
		}
        return json_encode($out);
    }

/**
		* To get existing invoices to merge for client and case
		* @param client_id
		* @param client_case_id
		* @param display_type (itemized or consolidated)
	*/
	public function actionGetInvoicesToMerge()
	{
		$client_id = Yii::$app->request->post('client_id');
		$client_case_id = Yii::$app->request->post('client_case_id');
		$display_type = Yii::$app->request->post('display_type');
		$invoice_id = Yii::$app->request->post('invoice_id');

		$existing_invoices = (new InvoiceFinal)->getInvoicestomergeForClientCase($client_id, $client_case_id, $display_type, $invoice_id);
		$invoice_data = array();
		foreach($existing_invoices as $val)
		{
			$invoice_data[$val['id']] = "Invoice #".$val['id']." ".(new Options)->ConvertOneTzToAnotherTz($val['created_date'], 'UTC', $_SESSION['usrTZ'],'MDY');
		}
		$html = "";
		if(count($invoice_data) > 0)
		{
				
					
			$stroptions = "";
			foreach($invoice_data as $key => $val)
			{
					if($stroptions!="")
						$stroptions = $stroptions.'<input type="checkbox" name="merge_invoice" id="chk_'.$key.'" value="'.$key.'" /><label for="chk_'.$key.'">'.$val.'</label>';
					else
						$stroptions = '<input type="checkbox" name="merge_invoice" id="chk_'.$key.'" value="'.$key.'" /><label for="chk_'.$key.'">'.$val.'</label>';

			}
			$html = $stroptions;
				
		}
		else
		{
			$html = '<span style="display:block; margin-top: 15px;">No invoices found to merge.</span>';	
		}
		$resp_arr = array();
		$resp_arr['invoice_count'] = count($invoice_data);
		$resp_arr['html_text'] = $html;		
		echo json_encode($resp_arr);
		die;
	}

    /**
     * Before Delete final invoice checked invoice present in billing
     * @return mixed
     */
    public function actionChkhasaccuinvoice()
    {
        $invoiceid = Yii::$app->request->post('invoiceid');

        $msg       = "done";
        $ids       = explode(",", $invoiceid);
        $count     = InvoiceFinal::find()->count();
        if ($count != count($ids)) {
            foreach ($ids as $id) {
               $invoice_data = InvoiceFinal::findOne((int)$id);
               $client_id = $invoice_data->client_id;
               if($client_id!=''){
				   $data = InvoiceFinal::find()->where('id >'.($id).' AND client_id='.$client_id)->one();
			       if (isset($data->id) && $data->has_accum_cost == 1){
	                   //$msg = "-Selected Invoice cannot be deleted because there are more recent invoices that contain same billing items.  Delete all newer invoices prior to deleting selected Invoice.";
	                    $msg =  "At least one of the Selected Invoices cannot be deleted because there are newer Invoices that contain the same Accumulated Billing Items.  Therefore, please delete the newer Invoices prior to deleting the Selected Invocies to proceed.";
	                   break;
	               }
               }
            }
        }
        echo $msg;
        die;
    }

    /**
     * Delete Final invoice from grid
     * @return
     */
    public function actionDeletefinalinvoice()
    {
		$invoiceid = Yii::$app->request->post('invoice_id');
		$sql = "SELECT tfb.*,tft.* FROM tbl_invoice_final as tf
			LEFT JOIN tbl_invoice_final_billing as tfb ON tfb.invoice_final_id = tf.id
			LEFT JOIN tbl_invoice_final_taxes as tft ON tft.invoice_final_billing_id = tfb.id
			WHERE tf.id IN (".$invoiceid.")";
		$dataProvider = \Yii::$app->db->createCommand($sql)->queryAll();

		foreach($dataProvider as $key => $val){
			if($val['billing_unit_id']!=''){
				$billing_model = new InvoiceFinal;
				if($billing_model->chkIsAccumulatedDetele($val['billing_unit_id'], $invoiceid))
				{
					if(isset($val['billing_unit_id']) && $val['billing_unit_id'] >0){
						$model_billable = TasksUnitsBilling::findOne($val['billing_unit_id']);
						if(isset($model_billable->id) && $model_billable->id > 0){
							if($model_billable->invoiced != 2)
							{
								$model_billable->invoiced 		  = NULL;
							}
							$model_billable->temp_rate            = NULL;
							$model_billable->temp_discount        = NULL;
							$model_billable->temp_discount_reason = NULL;
							$model_billable->save(false);
						}
					}
				}
			}
		}

		$delete1 = 'DELETE FROM tbl_invoice_final_taxes WHERE invoice_final_billing_id IN (SELECT id FROM tbl_invoice_final_billing WHERE invoice_final_id IN ('.$invoiceid.'))';
		$delete2 = 'DELETE FROM tbl_invoice_final_billing WHERE invoice_final_id IN ('.$invoiceid.')';
		$delete3 = 'DELETE FROM tbl_invoice_final WHERE id IN ('.$invoiceid.')';
		\Yii::$app->db->createCommand($delete1)->execute();
		\Yii::$app->db->createCommand($delete2)->execute();
		\Yii::$app->db->createCommand($delete3)->execute();
		echo "done";
		die();
	}

	/**
	 * Close Final invoice from grid
	 * @return
	 */
	public function actionClosefinalinvoices()
	{
		$invoiceids = Yii::$app->request->post('invoiceIds');

		/*$sql_close = 'UPDATE tbl_invoice_final SET is_closed = 1 WHERE id IN ('.$invoiceid.')';
		\Yii::$app->db->createCommand($sql_close)->execute();
		echo "done";
		die();*/
		$type = Yii::$app->request->post('type', 'selected');
		$error = "";
		$error2 = "";
		$invoicesdata = "";
		if ($type == 'selected') {
				$finalinvoices = InvoiceFinal::find()->where('id IN (' . $invoiceids . ')')->all();
		} else if ($type == 'all') {
				$postdata = Yii::$app->request->post();
				$postdata['data_mode'] = 'bulk_close_invoices';
				$postdata['is_closed']=0;
				$searchModel = new InvoiceFinalSearch();
				$dataProvider = $searchModel->search($postdata);
				if (!empty($dataProvider)) {
						$newData = [];
						foreach ($dataProvider as $single) {
								$newData[] = $single['id'];
						}
						$finalinvoices = InvoiceFinal::find()->where('id IN (' . implode(',', $newData) . ')')->all();
				}
		}
		$invoice_ids=[];
		if (!empty($finalinvoices)) {
				foreach ($finalinvoices as $invoice) {

						$invoice->is_closed = 1;
						$invoice->closed_date = date("Y-m-d H:i:s");
						$invoice->closed_by = Yii::$app->user->identity->id;
						$invoice->save(false);
						$activity_name = $invoice->id;
						(new ActivityLog)->generateLog('Finalized Invoice', 'ClosedInvoice', $invoice->id, $activity_name);
						$duration = "0 days 0 hours 0 min";

						$final = 'OK';
						//}
						//	}
				}
		}
		echo json_encode(array("error" => $error, 'finalresult' => $final));
		die;
	}

	/**
	 * Merge Final invoice from grid
	 * @return
	 */
	public function actionMergefinalinvoices()
	{
		$maininvoiceid = Yii::$app->request->post('maininvoiceid');
		$mergeinvoiceIds = explode(",",Yii::$app->request->post('mergeinvoiceIds'));
		$error = "";
		if (!empty($mergeinvoiceIds)) {
			foreach ($mergeinvoiceIds as $merge_invoiceid) {
				$invoicebillingdata = InvoiceFinalBilling::find()->where('invoice_final_id = ' . $merge_invoiceid)->all();
				if (!empty($invoicebillingdata)) {
					foreach ($invoicebillingdata as $billing_invoice) {
						$billing_invoice->invoice_final_id = $maininvoiceid;
						$billing_invoice->save(false);
					}
					$delete_sql  = "DELETE FROM tbl_invoice_final WHERE id = ".$merge_invoiceid;
					\Yii::$app->db->createCommand($delete_sql)->execute();
				}
			}
		}
		$final = 'OK';
		echo json_encode(array("error" => $error, 'finalresult' => $final));
		die;
	}
	/**
	 * Finalized preview billing
	 * @param invoiced_id (int)
	 */
	 public function actionPreviewInvoice()
	 {
		$this->layout = 'billing';

		$invoicedId = Yii::$app->request->get('invoice_id');
		$invoice = InvoiceFinal::find()->where(['id'=>$invoicedId])->asArray()->one();
		$invoice['created_date'] = (new Options)->ConvertOneTzToAnotherTz($invoice['created_date'],'UTC',$_SESSION['usrTZ'],'date');
		$preview = InvoiceFinalBilling::find()->joinWith([
			'invoiceFinal',
			'invoiceFinalTaxes',
			'billingUnit' => function(\yii\db\ActiveQuery $query){
				//$query->where('invoiced != 2');
				$query->where("invoiced = '' OR invoiced IS NULL OR invoiced != 2 OR invoiced = 1");
				$query->joinWith([
					'tasksUnits'=>function(\yii\db\ActiveQuery $query){
						$query->select(['tbl_tasks_units.id','task_id']);
						$query->joinWith(['tasks'=>function(\yii\db\ActiveQuery $query){
							$query->select(['tbl_tasks.id','tbl_tasks.client_case_id']);
							$query->joinWith([
								'clientCase'=>function(\yii\db\ActiveQuery $query){
									$query->select(['case_name','case_matter_no','counsel_name','sales_user_id','tbl_client_case.id','client_id']);
									$query->joinWith([
										'salesRepo'=>function(\yii\db\ActiveQuery $query){
											$query->select(['tbl_user.id','usr_first_name','usr_lastname']);
										},
										'client'=>function(\yii\db\ActiveQuery $query){
											$query->select(['client_name','tbl_client.id']);
										}
									]);
								},
							]);
						}]);
					},
					'pricing'=>function(\yii\db\ActiveQuery $query){
							$query->joinWith(['unit','pricingUtbmsCodes']);
					}
				]);
			}
		])->where(['invoice_final_id'=>$invoicedId])->asArray()->all();
		//echo "<pre>",print_r($preview),"</pre>";die;
		$taskunitbillingdata1 = array();
		$summarydata = array();
		$taxcodes = array();
		$taxcodewiseAr = array();
		if(!empty($preview)){
			$dataArray = array();
			$invoiceArray = array();
			foreach($preview as $taskval1){
				$invoiceArray['invoice_final_id'] = $taskval1['invoice_final_id'];
				$invoiceArray['invoice_created'] = (new Options)->ConvertOneTzToAnotherTz($taskval1['invoiceFinal']['created_date'],'UTC',$_SESSION['usrTZ'],'date');
				$invoiceArray['billing_unit_id'] = $taskval1['billing_unit_id'];
				$invoiceArray['team_loc'] = $taskval1['team_loc'];
				$invoiceArray['final_rate'] = number_format($taskval1['final_rate'],2,'.','');
				$invoiceArray['discount'] = $taskval1['discount'];
				$invoiceArray['discount_reason'] = $taskval1['discount_reason'];
				$invoiceArray['internal_ref_no_id'] = $taskval1['internal_ref_no_id'];
				$invoiceArray['task_id'] = $taskval1['billingUnit']['tasksUnits']['task_id'];
				$invoiceArray['pricing_id'] = $taskval1['billingUnit']['pricing_id'];
				$invoiceArray['quantity'] = number_format(round($taskval1['billingUnit']['quantity'],2),2,'.','');
				$invoiceArray['billing_desc'] = $taskval1['billingUnit']['billing_desc'];
				$invoiceArray['client_id'] = $taskval1['billingUnit']['tasksUnits']['tasks']['clientCase']['client_id'];
				$invoiceArray['client_name'] = $taskval1['billingUnit']['tasksUnits']['tasks']['clientCase']['client']['client_name'];
				$invoiceArray['client_case_id'] = $taskval1['billingUnit']['tasksUnits']['tasks']['client_case_id'];
				$invoiceArray['case_name'] = $taskval1['billingUnit']['tasksUnits']['tasks']['clientCase']['case_name'];
				$invoiceArray['case_matter_no'] = $taskval1['billingUnit']['tasksUnits']['tasks']['clientCase']['case_matter_no'];
				$invoiceArray['counsel_name'] = $taskval1['billingUnit']['tasksUnits']['tasks']['clientCase']['counsel_name'];
				$invoiceArray['sales_user_id'] = $taskval1['billingUnit']['tasksUnits']['tasks']['clientCase']['sales_user_id'];
				$invoiceArray['sales_user_name'] = !empty($taskval1['billingUnit']['tasksUnits']['tasks']['clientCase']['salesRepo'])?$taskval1['billingUnit']['tasksUnits']['tasks']['clientCase']['salesRepo']['usr_first_name']." ".$taskval1['billingUnit']['tasksUnits']['tasks']['clientCase']['salesRepo']['usr_lastname']:'';
				$invoiceArray['price_point'] = $taskval1['billingUnit']['pricing']['price_point'];
				$invoiceArray['utbms_code'] = !empty($taskval1['billingUnit']['pricing']['pricingUtbmsCodes'])?$taskval1['billingUnit']['pricing']['pricingUtbmsCodes']['code']:'';
				$invoiceArray['unit_price_id'] = $taskval1['billingUnit']['pricing']['unit_price_id'];
				$invoiceArray['unit_name'] = $taskval1['billingUnit']['pricing']['unit']['unit_name'];
				$invoiceArray['pricing_description'] = $taskval1['billingUnit']['pricing']['description'];
				$invoiceArray['pricing_cust_desc_template'] = $taskval1['billingUnit']['pricing']['cust_desc_template'];
				$invoiceArray['pricing_is_custom'] = $taskval1['billingUnit']['pricing']['is_custom'];
				$invoiceArray['unit_created'] = (new Options)->ConvertOneTzToAnotherTz($taskval1['billingUnit']['created'],'UTC',$_SESSION['usrTZ'],'date');
				$invoiceArray['invoiceFinalTaxes'] = $taskval1['invoiceFinalTaxes'];
				$cases[$invoiceArray['client_case_id']] = $invoiceArray['client_case_id'];
				$summarydata[$invoiceArray['client_case_id']][] = $invoiceArray;
				if(!empty($dataArray)){
            		$index = (new InvoiceFinal)->findIfSamePPAdded($invoiceArray, $dataArray);
            		if(!empty($index)){
						$dataArray[$index['key']]['billing_unit_id'] .= ",".$invoiceArray['billing_unit_id'];
						$dataArray[$index['key']]['quantity'] += $invoiceArray['quantity'];
            		} else {
            			$dataArray[] = $invoiceArray;
            		}
            	} else {
            		$dataArray[] = $invoiceArray;
            	}
			}
			//echo "<pre>",print_r($dataArray),"</pre>Done<br/>";	die;
			foreach($dataArray as $taskval1){
				$taskunitbillingdata1[$taskval1['client_case_id']][] = $taskval1;
			}
			/* Start : If Range PP then rate will be on the bases of total of all same unit along with same pricepoint of same location */
	        if (!empty($taskunitbillingdata1)) {
	        	foreach ($taskunitbillingdata1 as $keysclientcase => $values){
	        		foreach ($values as $k => $value){
	        			$clientcase = explode("||",$keysclientcase);
	        			$clientdata = explode("=",$clientcase[0]);
	        			$casedata = explode("=",$clientcase[1]);
	        			$keysclient = $clientdata[0];
	        			$keyscase = $casedata[0];

	        			$rate = number_format($value['final_rate'],2,'.','');
	        			$subtotal = $rate * number_format(round($value['quantity'],2),2,'.','');
						$taskunitbillingdata1[$keysclientcase][$k]['rate'] = $rate;
						$taskunitbillingdata1[$keysclientcase][$k]['subtotal'] = $subtotal;
						//$taskunitbillingdata1[$keysclientcase]['subtotal'] += $subtotal;
						if(!empty($value['invoiceFinalTaxes'])){
							foreach($value['invoiceFinalTaxes'] as $tax){
								$taxcodewise = ($tax['rate']/100)*$subtotal;
								$taxcodes[$tax['code']] = number_format($tax['rate'],2,'.','');
								if(isset($taxcodewiseAr[$tax['code']]))
								$taxcodewiseAr[$tax['code']] += $taxcodewise;
								else
								$taxcodewiseAr[$tax['code']] = $taxcodewise;
							}
						}
					}
	        	}
	        }

	        /* End : If Range PP then rate will be on the bases of total of all same unit along with same pricepoint of same location */
		}

		$clientData = Client::find()->where(['id'=>$invoice['client_id']])->asArray()->one();

		/* $clientcaseData = ClientCase::find()->joinWith([
			'salesRepo'=>function(\yii\db\ActiveQuery $query){
				$query->select(['tbl_user.id','usr_first_name','usr_lastname']);
			}
		])->where(['tbl_client_case.id' => $invoice['client_case_id']])->asArray()->one(); */

	 	$clientcaseData = array();
		if(!empty($cases)) {
			foreach($cases as $case) {
				$clientcaseData[$case] = ClientCase::find()->joinWith([
					'salesRepo'=>function(\yii\db\ActiveQuery $query) {
						$query->select(['tbl_user.id','usr_first_name','usr_lastname']);
					}
				])->where(['tbl_client_case.id'=>$case])->asArray()->one();
			}
		}

		$contactData = ClientContacts::find()->where(['id'=>$invoice['contact_id']])->asArray()->one();
		$teamLocation = ArrayHelper::map(TeamlocationMaster::find()->select(['id','team_location_name'])->orderBy('team_location_name ASC')->where('remove=0 OR id=0')->all(),'id','team_location_name');
		if(Yii::$app->request->isAjax)
			return $this->renderAjax('preview_invoice',['invoice'=>$invoice,'taskunitbillingdata1'=>$taskunitbillingdata1, 'taxcodes' => $taxcodes, 'taxcodewiseAr'=>$taxcodewiseAr, 'summarydata'=>$summarydata, 'display_by' => $preview->display_by, 'clientData' => $clientData, 'clientcaseData' => $clientcaseData, 'contactData' => $contactData, 'teamLocation'=>$teamLocation]);
		else
			return $this->render('preview_invoice',['invoice'=>$invoice,'taskunitbillingdata1'=>$taskunitbillingdata1, 'taxcodes' => $taxcodes, 'taxcodewiseAr'=>$taxcodewiseAr, 'summarydata'=>$summarydata, 'display_by' => $preview->display_by, 'clientData' => $clientData, 'clientcaseData' => $clientcaseData, 'contactData' => $contactData, 'teamLocation'=>$teamLocation]);
	 }

	/**
	 * Edit Finalized Invoice
	 * @param invoiced_id (int)
	 */
	 public function actionEditInvoice()
	 {
		$this->layout = 'billing';

		$invoicedId = Yii::$app->request->get('invoice_id');
		$flag = Yii::$app->request->get('flag','finalized-grid');
		$invoice = InvoiceFinal::find()->where(['id'=>$invoicedId])->one();

		$invoice->created_date = (new Options)->ConvertOneTzToAnotherTz($invoice->created_date,'UTC',$_SESSION['usrTZ'],'date');
		$preview = InvoiceFinalBilling::find()->joinWith([
			'invoiceFinal',
			'invoiceFinalTaxes',
			'billingUnit' => function(\yii\db\ActiveQuery $query){
				$query->where('invoiced != 2');
				$query->joinWith([
					'tasksUnits'=>function(\yii\db\ActiveQuery $query){
						$query->select(['tbl_tasks_units.id','task_id']);
						$query->joinWith(['tasks'=>function(\yii\db\ActiveQuery $query){
						$query->select(['tbl_tasks.id','tbl_tasks.client_case_id']);
						$query->joinWith([
							'clientCase'=>function(\yii\db\ActiveQuery $query){
								$query->select(['case_name','case_matter_no','counsel_name','sales_user_id','tbl_client_case.id','client_id']);
								$query->joinWith([
									'salesRepo'=>function(\yii\db\ActiveQuery $query){
										$query->select(['usr_first_name','usr_lastname']);
									},
									'client'=>function(\yii\db\ActiveQuery $query){
										$query->select(['client_name','tbl_client.id']);
									}
								]);
							},
						]);
						}]);
					},
					'pricing'=>function(\yii\db\ActiveQuery $query){
						$query->joinWith(['unit','pricingUtbmsCodes']);
					}
				]);
			}
		])->where(['invoice_final_id'=>$invoicedId])->orderBy('tbl_client.client_name ASC, tbl_client_case.case_name ASC')->asArray()->all();
		$summarydata = array();
		$taxcodes = array();
		$taxcodewiseAr = array();
		$cases = array();
		if(!empty($preview)){
			$invoiceArray = array();
			foreach($preview as $taskval1){
				$invoiceArray['invoice_final_id'] = $taskval1['invoice_final_id'];
				$invoiceArray['invoice_created'] = (new Options)->ConvertOneTzToAnotherTz($taskval1['invoiceFinal']['created_date'],'UTC',$_SESSION['usrTZ'],'date');
				$invoiceArray['billing_unit_id'] = $taskval1['billing_unit_id'];
				$invoiceArray['team_loc'] = $taskval1['team_loc'];
				$invoiceArray['final_rate'] = number_format($taskval1['final_rate'],2,'.','');
				$invoiceArray['discount'] = $taskval1['discount'];
				$invoiceArray['discount_reason'] = $taskval1['discount_reason'];
				$invoiceArray['internal_ref_no_id'] = $taskval1['internal_ref_no_id'];
				$invoiceArray['task_id'] = $taskval1['billingUnit']['tasksUnits']['task_id'];
				$invoiceArray['pricing_id'] = $taskval1['billingUnit']['pricing_id'];
				$invoiceArray['quantity'] = round($taskval1['billingUnit']['quantity'],2);
				$invoiceArray['billing_desc'] = $taskval1['billingUnit']['billing_desc'];
				$invoiceArray['client_id'] = $taskval1['billingUnit']['tasksUnits']['tasks']['clientCase']['client_id'];
				$invoiceArray['client_name'] = $taskval1['billingUnit']['tasksUnits']['tasks']['clientCase']['client']['client_name'];
				$invoiceArray['client_case_id'] = $taskval1['billingUnit']['tasksUnits']['tasks']['client_case_id'];
				$invoiceArray['case_name'] = $taskval1['billingUnit']['tasksUnits']['tasks']['clientCase']['case_name'];
				$invoiceArray['case_matter_no'] = $taskval1['billingUnit']['tasksUnits']['tasks']['clientCase']['case_matter_no'];
				$invoiceArray['counsel_name'] = $taskval1['billingUnit']['tasksUnits']['tasks']['clientCase']['counsel_name'];
				$invoiceArray['sales_user_id'] = $taskval1['billingUnit']['tasksUnits']['tasks']['clientCase']['sales_user_id'];
				$invoiceArray['sales_user_name'] = !empty($taskval1['billingUnit']['tasksUnits']['tasks']['clientCase']['salesRepo'])?$taskval1['billingUnit']['tasksUnits']['tasks']['clientCase']['salesRepo']['usr_first_name']." ".$taskval1['billingUnit']['tasksUnits']['tasks']['clientCase']['salesRepo']['usr_lastname']:'';
				$invoiceArray['price_point'] = $taskval1['billingUnit']['pricing']['price_point'];
				$invoiceArray['utbms_code'] = !empty($taskval1['billingUnit']['pricing']['pricingUtbmsCodes'])?$taskval1['billingUnit']['pricing']['pricingUtbmsCodes']['code']:'';
				$invoiceArray['invoiced'] = $taskval1['billingUnit']['invoiced'];
				//$invoiceArray['istieredrate'] = istieredrate;
				$invoiceArray['unit_price_id'] = $taskval1['billingUnit']['pricing']['unit_price_id'];
				$invoiceArray['unit_name'] = $taskval1['billingUnit']['pricing']['unit']['unit_name'];
				$invoiceArray['pricing_description'] = $taskval1['billingUnit']['pricing']['description'];
				$invoiceArray['pricing_cust_desc_template'] = $taskval1['billingUnit']['pricing']['cust_desc_template'];
				$invoiceArray['pricing_is_custom'] = $taskval1['billingUnit']['pricing']['is_custom'];
				$invoiceArray['subtotalamount'] = round($invoiceArray['quantity'],2) * round($invoiceArray['final_rate'],2);
				$invoiceArray['unit_created'] = (new Options)->ConvertOneTzToAnotherTz($taskval1['billingUnit']['created'],'UTC',$_SESSION['usrTZ'],'date');
				$invoiceArray['invoiceFinalTaxes'] = $taskval1['invoiceFinalTaxes'];
				$summarydata[$invoiceArray['client_case_id']][] = $invoiceArray;
				$cases[$invoiceArray['client_case_id']] = $invoiceArray['client_case_id'];
				if(!empty($invoiceArray['invoiceFinalTaxes'])){
					foreach($invoiceArray['invoiceFinalTaxes'] as $tax){
						$taxcodewise = ($tax['rate']/100)*$invoiceArray['subtotalamount'];
						$taxcodes[$tax['code']] = number_format($tax['rate'],2,'.','');
						if(isset($taxcodewiseAr[$tax['code']]))
						$taxcodewiseAr[$tax['code']] += $taxcodewise;
						else
						$taxcodewiseAr[$tax['code']] = $taxcodewise;
					}
				}
			}
		}
		//echo "<pre>",print_r($summarydata),"</pre>Done<br/>";die;
		$clientData = Client::find()->where(['id'=>$invoice->client_id])->asArray()->one();
		$clientcaseData = array();
		if(!empty($cases)){
                    foreach($cases as $case){
                        $clientcaseData[$case] = ClientCase::find()->joinWith([
                            'salesRepo'=>function(\yii\db\ActiveQuery $query){
                                   $query->select(['tbl_user.id','usr_first_name','usr_lastname']);
                            }
                        ])->where(['tbl_client_case.id'=>$case])->asArray()->one();
                    }
		}
		$contactList = array();
		if($invoice->display_by == 1){
                    $contactList = ArrayHelper::map(CaseContacts::find()->joinWith(['clientContacts'=>function(\yii\db\ActiveQuery $query){$query->where(['tbl_client_contacts.contact_type'=>'Billing']);}])->select(['tbl_case_contacts.client_contacts_id'])->where(['tbl_case_contacts.client_case_id'=>$invoice->client_case_id])->all(),'client_contacts_id',function($model, $defaultValue) {
		        return $model['clientContacts']['fname']." ".$model['clientContacts']['lname'];
		    });
		} else {
                    $contactList = ArrayHelper::map(ClientContacts::find()->where(['contact_type'=>'Billing','client_id'=>$invoice->client_id])->select(['id','fname','lname'])->all(),'id',function($model, $defaultValue) {
		        return $model['fname']." ".$model['lname'];
		    });
		}

		$contactData = ClientContacts::find()->where(['id'=>$invoice->contact_id])->asArray()->one();
		$teamLocation = ArrayHelper::map(TeamlocationMaster::find()->select(['id','team_location_name'])->orderBy('team_location_name ASC')->where('remove=0 OR id=0')->all(),'id','team_location_name');

		if(Yii::$app->request->isAjax)
			return $this->renderAjax('edit_invoice',['invoice' => $invoice, 'summarydata' => $summarydata, 'taxcodes' => $taxcodes, 'taxcodewiseAr' => $taxcodewiseAr, 'clientData' => $clientData, 'clientcaseData' => $clientcaseData, 'contactList'=>$contactList, 'contactData' => $contactData, 'teamLocation'=>$teamLocation, 'flag'=>$flag]);
		else
			return $this->render('edit_invoice',['invoice' => $invoice, 'summarydata' => $summarydata, 'taxcodes' => $taxcodes, 'taxcodewiseAr' => $taxcodewiseAr, 'clientData' => $clientData, 'clientcaseData' => $clientcaseData, 'contactList'=>$contactList, 'contactData' => $contactData, 'teamLocation'=>$teamLocation, 'flag'=>$flag]);
	 }

	 /**
	  * change biller contact details from Edit invoice dropdown
	  * @return details
	  */
	  public function actionGetBillerContactDetails(){
                $contact_id = Yii::$app->request->post('contact_id');
                if($contactList = ClientContacts::findOne($contact_id)){
                    return $this->renderAjax('_biller-contact-details',['contactList' => $contactList]);
                }
	  }

	  /**
	   * Billing Finalized Invoice
	   * @return
	   */
	   public function actionExportInvoice()
	   {
		   // export finalized invoice
		   return $this->renderAjax('_export-finalized-invoice');
	   }

   /**
    * Billing Invoice Update Functionality
    * @return
    */
	public function actionUpdateInvoice()
	{
		$quantity = Yii::$app->request->post('quantity');
		$billing_desc = Yii::$app->request->post('billing_desc');
		$final_units = Yii::$app->request->post('final_units');
    	$display_type = Yii::$app->request->post('display_type');
		$invoice_id = Yii::$app->request->post('invoice_id');
    	$flag = Yii::$app->request->post('flag');
		$data = Yii::$app->request->post();

    	if(!empty($final_units)){
    		$pricepointLocwisetotal = array();
    		$invoiceArray = array();
    		foreach($final_units as $case_id => $unit) {
    			$qtychanged = 0;
    			/** Start **/
    			if(isset($billing_desc[$case_id]) && !empty($billing_desc[$case_id])){ // check pricing description available (billing_desc)
					// Task unit billing update
					foreach($billing_desc[$case_id] as $key => $val) {
						$units = json_decode($unit[$key],true);
						$invoiceBillingunit = TasksUnitsBilling::findOne($key);
						$changed=0;

						if($invoiceBillingunit->billing_desc != $val){
							$invoiceBillingunit->billing_desc = $val;
							$changed=1;
						}

						if($invoiceBillingunit->quantity != $quantity[$case_id][$key]){
							$invoiceBillingunit->quantity = $quantity[$case_id][$key];
							$qtychanged = 1;
							$changed=1;
						}

						//if($qtychanged == 1){
						$units['quantity'] = $quantity[$case_id][$key];
						//}
						$invoiceArray[$case_id][] = $units;
						$pricepointLocwisetotal[$case_id][$units['pricing_id']][$units['team_loc']][$units['unit_price_id']]['unit_total'] += $quantity[$case_id][$key];

						if($changed == 1){
							$invoiceBillingunit->save(false);
						}
					}
				}
				/** End **/
    		}
    		$invoiceFinal = InvoiceFinal::findOne($invoice_id);
    		$ischanged = 0;
    		//echo "<pre>",print_r($pricepointLocwisetotal),print_r($invoiceArray),"</pre>";
			//die;

    		if(!empty($invoiceArray)){
    			$total = 0;
    			//echo "<pre>",print_r($invoiceArray),"</pre>";
    			foreach($invoiceArray as $key => $finalizedunit){
    				foreach($finalizedunit as $key1 => $invoicedata){
	    				if(isset($pricepointLocwisetotal[$key][$invoicedata['pricing_id']][$invoicedata['team_loc']][$invoicedata['unit_price_id']]['unit_total'])){

	    					$quantity = $pricepointLocwisetotal[$key][$invoicedata['pricing_id']][$invoicedata['team_loc']][$invoicedata['unit_price_id']]['unit_total'];
			                $invoiceBillingunit = TasksUnitsBilling::findOne($invoicedata['billing_unit_id']);
							$rate = (new TasksUnitsBilling)->checkpricingforrate($invoicedata['client_id'], $key, $invoicedata['pricing_id'], $quantity ,"",$invoicedata['team_loc']);
							if(isset($invoiceBillingunit->temp_rate) && $invoiceBillingunit->temp_rate!="") {
								$rate = $invoiceBillingunit->temp_rate;
							}


			                if ($invoicedata['discount'] != '')
								$rate = $rate - ($rate * $invoicedata['discount'] / 100);

			                $finalrate = $invoicedata['quantity'] * $rate;

			                $invoiceBillingModel = InvoiceFinalBilling::find()->where(['invoice_final_id'=>$invoice_id,'billing_unit_id'=>$invoicedata['billing_unit_id']])->one();

							$invoiceBillingModel->final_rate = $rate;
							$invoiceBillingModel->save(false); // Save Invoice Final Billing Data

							$total += $finalrate;
						//	$invoiceFinal->total = $total;

							$ischanged = 1;
	    				}
    				}
    			}
    		}

    		$createddate = (new Options)->ConvertOneTzToAnotherTz($invoiceFinal->created_date,'UTC',$_SESSION['usrTZ'],'YMD');
    		if($createddate != $data['created_date']){
    			//$his = (new Options)->ConvertOneTzToAnotherTz(date('h:i:s'),'UTC',$_SESSION['usrTZ'],'HIS');
    			$dt = explode("/",$data['created_date']);
    			$data['created_date'] = $dt[2].'-'.$dt[0].'-'.$dt[1];
    			$datetime = (new Options)->ConvertOneTzToAnotherTz($data['created_date'],$_SESSION['usrTZ'],'UTC','YMDHIS');
    			$invoiceFinal->created_date = $datetime;
    			$ischanged = 1;
    		}
    		if($invoiceFinal->contact_id != $data['InvoiceFinal']['contact_id']){
    			$invoiceFinal->contact_id = $data['InvoiceFinal']['contact_id'];
    			$ischanged = 1;
    		}
    		//echo "<pre>",print_r($invoiceFinal->attributes),"</pre>";
    		$ischanged==1?$invoiceFinal->save(false):'';
    		if($flag == 'preview'){
    			$url = Yii::$app->urlManager->createUrl(["billing-finalized-invoice/preview-invoice",'invoice_id'=>$invoice_id,'flag'=>'preview']);
    			return 'preview';
				//return $this->redirect(Url::to($url),302);
    		} else {
    			$url = Yii::$app->urlManager->createUrl(['billing-finalized-invoice/finalized-invoices','flag'=>'finalized-grid']);
    			return 'finalinvoice';
				//return $this->redirect(Url::to($url),302);
    		}
    	}
	}
}
