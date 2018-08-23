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

class BillingClosedInvoiceController extends \yii\web\Controller
{

	public function beforeAction($action)
	{
		if (Yii::$app->user->isGuest)
			$this->redirect(array('site/login'));

		if (!(new User)->checkAccess(7) || !(new User)->checkAccess(7.19)){
			throw new \yii\web\HttpException(404, 'User permissions do not allow entry into this module.');
		}
		return parent::beforeAction($action);
	}


		/* Display Closed Invoices */
		public function actionClosedInvoices()
    {
			$this->layout = 'billing';
        $searchModel = new InvoiceFinalSearch();
        $params	=	Yii::$app->request->queryParams;
				$params['grid_id']='dynagrid-closed-invoiced';
				$params['is_closed'] = 1;
        Yii::$app->request->queryParams +=$params;
        //echo '<pre>',print_r($params),'</pre>';die;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

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
				if(isset($params['InvoiceFinalSearch']['closed_by']) && !empty($params['InvoiceFinalSearch']['closed_by'])){
            $closedby_selected =(new User)->getSelectedGridClosedby($params['InvoiceFinalSearch']['closed_by']);
            if($closedby_selected == 'ALL'){
                unset($params['InvoiceFinalSearch']['closed_by']);
                $closedby_selected = array();
            }
        }
        /*IRT 96,398 Code Code Ends */
        $filter_type=\app\models\User::getFilterType(['tbl_invoice_final.id','tbl_invoice_final.client_id','tbl_invoice_final.created_date', 'totalinvoiceamt','tbl_invoice_final.client_case_id','tbl_invoice_final.closed_date','tbl_invoice_final.closed_by'],['tbl_invoice_final']);
        $config = [];
        $config_widget_options = [
			'totalinvoiceamt'=>[
				'filter_type'=>'range',
				'options' => ['placeholder' => 'Rate (0 - 1000)'],
				'html5Options' => ['min' => 0, 'max' => 1000, 'step'=>1]
			],
			'client_case_id'=>['initValueText' => $client_case_selected],'client_id'=>['initValueText' => $clients_selected],'closed_by'=>['initValueText' => $closedby_selected]
        ];
        $filterWidgetOption=\app\models\User::getFilterWidgetOption($filter_type,Url::toRoute(['billing-closed-invoice/ajax-closed-invoice-filter']),$config,$config_widget_options);
        $sum=0;//$sum=(new InvoiceFinal)->getSumTotalInvoiceAmount();
        /*IRT 67,68,86,87,258*/
		if(Yii::$app->request->isAjax)
			return $this->renderAjax('closed_invoiced',['sum'=>$sum,'filter_type'=>$filter_type,'filterWidgetOption'=>$filterWidgetOption,'dataProvider' => $dataProvider, 'searchModel' => $searchModel]);
		else
        	return $this->render('closed_invoiced',['sum'=>$sum,'filter_type'=>$filter_type,'filterWidgetOption'=>$filterWidgetOption,'dataProvider' => $dataProvider, 'searchModel' => $searchModel]);
    }

		/**
		 * Filter Ajax Closed Invoice
		 */
		public function actionAjaxClosedInvoiceFilter()
		{
				$searchModel = new InvoiceFinalSearch();
				$params = Yii::$app->request->queryParams;
				$params['is_closed'] = 1;
				$dataProvider = $searchModel->searchFilter($params);
				$out['results']=array();
		foreach ($dataProvider as $key=>$val){
			if($params['field'] == 'client_id' || $params['field'] == 'client_case_id' || $params['field'] == 'closed_by')
				$id_val = $key;
			else
				$id_val = $val;
			$out['results'][] = ['id' => $id_val, 'text' => $val, 'label' => $val];
		}
				return json_encode($out);
		}


	/**
	 * ReOpen Closed invoice from grid
	 * @return
	 */
	public function actionReopenfinalinvoices()
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
				$postdata['data_mode'] = 'bulk_reopen_invoices';
				$postdata['is_closed'] = 1;
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

						$invoice->is_closed = 0;
						$invoice->closed_date = date("Y-m-d H:i:s");
						$invoice->closed_by = Yii::$app->user->identity->id;
						$invoice->save(false);
						$activity_name = $invoice->id;
						(new ActivityLog)->generateLog('Finalized Invoice', 'ReopenedInvoice', $invoice->id, $activity_name);
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
	   * Billing Finalized Invoice
	   * @return
	   */
	   public function actionExportInvoice()
	   {
		   // export finalized invoice
		   return $this->renderAjax('_export-closed-invoice');
	   }
}
