<?php
namespace app\controllers;

use Yii;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use app\models\ProjectSecurity;
use app\models\search\InvoiceBatchSearch;
use app\models\Options;
use app\models\TasksUnitsBilling;
use app\models\InvoiceBatch;
use app\models\InvoiceBatchClientCase;
use app\models\InvoiceBatchTeams;
use app\models\InvoiceFinal;
use app\models\InvoiceFinalBilling;
use app\models\InvoiceFinalTaxes;
use app\models\CaseContacts;
use app\models\ClientContacts;
use yii\data\ArrayDataProvider;
use app\models\TaxClass;
use app\models\User;
use app\models\Team;

class BillingGenerateInvoiceController extends \yii\web\Controller
{
	/**
	 * Billing invoice management Selection Criteria Form
	 * @return
	 */
	public function beforeAction($action)
	{
		if (Yii::$app->user->isGuest)
			$this->redirect(array('site/login'));

		if (!(new User)->checkAccess(7))
			throw new \yii\web\HttpException(404, 'User permissions do not allow entry into this module.');

		return parent::beforeAction($action);
	}

    public function actionBillingInvoiceManagement()
    {
    	$this->layout = 'billing';
        $filter_data = Yii::$app->request->post('filter_data');
        $batchId = Yii::$app->request->get('batchId');
        if(!empty($filter_data))
            $filter_data = json_decode($filter_data,true);

        if($batchId!='' && $batchId!=0)
        {
            $filter_data=array();
            $saved_invoice = InvoiceBatch::find()->select(['iv.id','iv.datefrom','iv.dateto','iv.display_by as chkinvoiced','iv.display_invoice as chkclientcases'])
                ->from('tbl_invoice_batch As iv')->with(['invoiceBatchClientCases' => function(\yii\db\ActiveQuery $query){
                    $query->select(['tbl_invoice_batch_client_case.client_case_id','tbl_invoice_batch_client_case.invoice_batch_id']);
                },'invoiceBatchTeams' => function(\yii\db\ActiveQuery $query){
                    $query->select(['tbl_invoice_batch_teams.team_id','tbl_invoice_batch_teams.invoice_batch_id']);
            }])->where('iv.id='.$batchId)->asArray()->One();
            //echo "<pre>",print_r($saved_invoice),"</pre>";die;
            /** Client Case Invoice **/
            $str = array();
            foreach($saved_invoice['invoiceBatchClientCases'] as $val){
                $str[] = $val['client_case_id'];
            }

            /** Teams Invoice **/
            $str_team = array();
            foreach($saved_invoice['invoiceBatchTeams'] as $val){
                $str_team[] = $val['team_id'];
            }

            /** Filter data **/
            $filter_data['start_date'] = $saved_invoice['datefrom'];
            $filter_data['end_date'] = $saved_invoice['dateto'];
            $filter_data['clientcases'] = $str;
            $filter_data['chkinvoiced'] = $saved_invoice['chkinvoiced'];
            $filter_data['teams'] = $str_team;
            $filter_data['chkteams'] = !empty($str_team)?'teams':"";
            $filter_data['chkclientcases'] = $saved_invoice['chkclientcases'];
            /** End Filter **/
        }

    	//$Casedata1 = (new ProjectSecurity)->getCaseSecurityData();

    	$userId = Yii::$app->user->identity->id;
    	$roleId = Yii::$app->user->identity->role_id;
    	$client_data_case = array();

    	/*foreach ($Casedata1 as $ccase) {
			if(isset($ccase['client_case_id']) && $ccase['client_case_id']!="" && $ccase['client_case_id']!=0 && $ccase['is_close']==0) {
				$client_data_case[$ccase['client_case_id']] = $ccase['client_name'] . '-' . $ccase['case_name'];
			}
        }*/
    	//echo "<pre>",print_r($Casedata1);die;
    	// echo "<pre>",print_r($Casedata1),print_r($client_data_case),"</pre>";die;
    	/* $teams = ArrayHelper::map(ProjectSecurity::find()->select(['team_id','team_name'])->from('tbl_project_security as pc')
        ->join('INNER JOIN','tbl_team as tm','tm.id=pc.team_id')->where('pc.team_id!=1 AND pc.team_id!=0 AND pc.user_id='.$userId)
        ->groupBy(['pc.team_id','tm.team_name'])->orderBy('tm.sort_order')->all(),'team_id','team_name'); */
		if($roleId==0) {
			$teams = ArrayHelper::map(Team::find()->select(['id','team_name'])->where('id!=1')->all(),'id','team_name');
		} else {
			if(Yii::$app->db->driverName=='mysql') {
					$teams = ArrayHelper::map(ProjectSecurity::find()->select(['team_id','team_name'])->from('tbl_project_security as pc')
					->join('INNER JOIN','tbl_team as tm','tm.id=pc.team_id')->where('pc.team_id!=1 AND pc.team_id!=0 AND pc.user_id='.$userId)
					->groupBy(['pc.team_id','tm.team_name'])->orderBy('tm.sort_order')->all(),'team_id','team_name');
			} else {
					$teams = ArrayHelper::map(ProjectSecurity::find()->select(['team_id','team_name'])->from('tbl_project_security as pc')
					->join('INNER JOIN','tbl_team as tm','tm.id=pc.team_id')->where('pc.team_id!=1 AND pc.team_id!=0 AND pc.user_id='.$userId)
					->groupBy(['pc.team_id','tm.team_name','tm.sort_order'])->orderBy('tm.sort_order')->all(),'team_id','team_name');
			}
		}
        if(Yii::$app->request->isAjax)
			return $this->renderAjax('invoicemanagement', ['client_data_case' => $client_data_case, 'teams' => $teams, 'filter_data' => $filter_data]);
		else
        	return $this->render('invoicemanagement', ['client_data_case' => $client_data_case, 'teams' => $teams, 'filter_data' => $filter_data]);
    }
	public function actionGetClientcasedata() {
		//$page=Yii::$app->request->post('page',1);
		//$Casedata1 = (new ProjectSecurity)->getCaseSecurityDataNew($page);
		/*$client_data_case=array();
		if(!empty($Casedata1)) {
			foreach ($Casedata1 as $ccase) {
				if(isset($ccase['client_case_id']) && $ccase['client_case_id']!="" && $ccase['client_case_id']!=0 && $ccase['is_close']==0) {
					$client_data_case[$ccase['client_case_id']] = $ccase['client_name'] . '-' . $ccase['case_name'];
				}
			}
		}*/
		$data = Yii::$app->request->post();
		$projectsecurity=[];
		if(isset($data['selectedclientcasedata']) && $data['selectedclientcasedata']!=""){
			$projectsecurity=explode(",",$data['selectedclientcasedata']);
		}
		//echo "<pre>",print_r($projectsecurity),"</pre>";die;
		$clientList=(new ProjectSecurity)->getCaseSecurityData();
		//echo "<pre>",print_r($clientListAr),"</pre>";die;
		$clientListAr=[];
        foreach($clientList as $client_data){
            $clientListAr[$client_data['client_name']][$client_data['client_id']][$client_data['id']]=$client_data['case_name'];
        }
		$clientList = [];
		$selectedCases = [];
		foreach($clientListAr as $client_name => $clientCases){
			$client = [];
			foreach($clientCases as $client_id => $cases){
				$client['title'] = $client_name;
				$client['isFolder'] = true;
				$client['key'] = $client_id;
				$case = [];
				foreach($cases as $case_id => $case_name){
					$case['title'] = $case_name;
					$case['key'] = $case_id;
					if(in_array($case['key'],$projectsecurity)){
						$case['select'] = true;
						$selectedCases[] = $case['key'];
					} else {
						$case['select'] = false;
					}

					$client['children'][] = $case;
				}
				if(!empty($client['children']))
					$clientList[] = $client;
			}
		}
		return $this->renderAjax('show_client_case', ['selectedCases'=>$selectedCases,'clientList'=>$clientList,'client_data_case' => $client_data_case]);
	}

    /**
     * Saved Invoice Grid Page
     * @return type
     */
    public function actionSavedInvoice()
    {
    	$this->layout = 'billing';
    	$searchModel = new InvoiceBatchSearch();
		$params['grid_id']='dynagrid-saved-invoice';
        Yii::$app->request->queryParams +=$params;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		//$models=$dataProvider->getModels();

		//echo "<pre>",print_r($models),"</pre>";
		//die;

        /*IRT 67,68,86,87,258*/
        $filter_type=\app\models\User::getFilterType(['tbl_invoice_batch.id','tbl_invoice_batch.datefrom','tbl_invoice_batch.display_invoice','tbl_invoice_batch.display_by','tbl_invoice_batch.created','tbl_invoice_batch.created_by','tbl_invoice_batch.modified','tbl_invoice_batch.modified_by'],['tbl_invoice_batch']);
        $config = [];
        $config_widget_options = ['id'=>['field_alais'=>'batch']];
        $filterWidgetOption=\app\models\User::getFilterWidgetOption($filter_type,Url::toRoute(['billing-generate-invoice/ajax-generate-invoice-filter']),$config,$config_widget_options);
        /*IRT 67,68,86,87,258*/
		if(Yii::$app->request->isAjax)
			return $this->renderAjax('savedinvoice', array('dataProvider' => $dataProvider,'searchModel' => $searchModel,'filter_type'=>$filter_type,'filterWidgetOption'=>$filterWidgetOption), false, true);
		else
        	return $this->render('savedinvoice', array('dataProvider' => $dataProvider,'searchModel' => $searchModel,'filter_type'=>$filter_type,'filterWidgetOption'=>$filterWidgetOption), false, true);
    }

    /**
     * Saved invoice details
     * @return
     */
    public function actionAddInvoiceDetails()
    {
        $model = new InvoiceBatch();
        $data = (array)json_decode(Yii::$app->request->post('filter_data'));
		if(!empty($data)){
			//echo "<pre>",print_r($data),"</pre>";die;
			$model->datefrom = date('Y-m-d',strtotime($data['start_date']));
            $model->dateto = date('Y-m-d',strtotime($data['end_date']));
            $model->display_invoice = $data['chkclientcases'];
            $model->display_by = $data['chkinvoiced'];
            if($model->save()){
				/* Saved Client Case */
                if(!empty($data['clientcases'])){
                    foreach($data['clientcases'] as $clientcase){
                        $clientmodel = new InvoiceBatchClientCase();
                        $clientmodel->invoice_batch_id = $model->id;
                        $clientmodel->client_case_id = $clientcase;
                        $clientmodel->save();
                    }
                }
				/* End Client Case */
                /* Saved Teams */
                if(!empty($data['teams'])){
                    foreach($data['teams'] as $teams){
                        $clientmodel = new InvoiceBatchTeams();
                        $clientmodel->invoice_batch_id = $model->id;
                        $clientmodel->team_id = $teams;
                        $clientmodel->save();
                    }
                }
                /* End Saved Teams */
                echo "OK";
            } else {
			    /* End */
                echo "FAIL";
            }
        }
        die();
    }

    /**
     * Ajax generate Invoice Filter
     * @return mixed
     */
    public function actionAjaxGenerateInvoiceFilter()
    {
		$searchModel = new InvoiceBatchSearch();
    	$dataProvider = $searchModel->searchFilter(Yii::$app->request->queryParams);
		$out['results']=array();
		foreach ($dataProvider as $key=>$val){
			$out['results'][] = ['id' => $val, 'text' => $val, 'label' => $val];
		}
		return json_encode($out);
    }

    /*
     * Delete saved invoice
     * @return mixed
     */
     public function actionDeletegenerateinvoice()
     {
		$batchId = Yii::$app->request->get('batchId');
		$delete1 = "DELETE  FROM tbl_invoice_batch_client_case WHERE invoice_batch_id IN (".$batchId.")";
		$delete2 = "DELETE  FROM tbl_invoice_batch_teams  WHERE invoice_batch_id IN (".$batchId.")";
		$delete  = "DELETE FROM tbl_invoice_batch WHERE id IN (".$batchId.")";
		\Yii::$app->db->createCommand($delete1)->execute();
		\Yii::$app->db->createCommand($delete2)->execute();
        \Yii::$app->db->createCommand($delete)->execute();
		die();
     }

    /**
     * It will load invoiced/non-invoiced units to generate invoices.
     * @param filter params (mixed)
     */
    public function actionDisplayGenerateInvoice()
    {
		//die('here');
        $client_id = '';
        $client_case_id = '';
        $flag = Yii::$app->request->post('flag','');
        $filterdata = Yii::$app->request->post();
        if($flag=='reload')
			$filterdata = json_decode(Yii::$app->request->post('filter_data'),true);

        $params = Yii::$app->params['display_invoice_view'];
    	$view = isset($params[$filterdata['chkinvoiced']])?$params[$filterdata['chkinvoiced']]:'Itemized';
		$clientcasedetails = array();
		$client_ids=array();
		$client_case_ids=array();
    	if(!empty($filterdata)) {
			$filteredTeams = isset($filterdata['teams']) && !empty($filterdata['teams'])?implode(',',$filterdata['teams']):0;
			$filteredClientcases = 0;
			if(isset($filterdata['clientcases']) &&  !is_array($filterdata['clientcases'])) {
				$filterdata['clientcases']=json_decode($filterdata['clientcases'],true);
			}
			if(isset($filterdata['clientcases']) &&  is_array($filterdata['clientcases'])) {
				if(isset($filterdata['clientcases']) && !empty($filterdata['clientcases']))
					$filteredClientcases = implode(',',$filterdata['clientcases']);
			}
			$clientSqlAll = "";
			if(isset($filterdata['chkclientcases']) && $filterdata['chkclientcases'] == 'ALL'){
				$userId = Yii::$app->user->identity->id;
				$roleId = Yii::$app->user->identity->role_id;
				if($roleId!=0){
					 $clientSqlAll = "SELECT DISTINCT t.client_case_id FROM tbl_project_security t LEFT JOIN tbl_client_case ON t.client_case_id = tbl_client_case.id LEFT JOIN tbl_client ON t.client_id = tbl_client.id WHERE t.user_id = $userId  AND t.team_id=0";
				}
			}
			$taskunitbillingdata = (new TasksUnitsBilling)->getTaskUnitBillingClienCaseData($filteredClientcases, $filteredTeams, $filterdata['start_date'],$filterdata['end_date'],$clientSqlAll);
    		if(!empty($taskunitbillingdata)){
				foreach($taskunitbillingdata as $key=>$value){
					// for itemized view
					$keyscase   = $value['client_case_id'];
					$keysclient = $value['client_id'];
					$client_ids[$value['client_id']]=$value['client_id'];
					$client_case_ids[$value['client_case_id']]=$value['client_case_id'];
							if($view=='Itemized'){
								// clientcasedetails

								$clientcasedetails[$keyscase]['client_id'] = $value['client_id'];
								$clientcasedetails[$keyscase]['client_name'] = $value['client_name'];
								$clientcasedetails[$keyscase]['client_case_id'] = $value['client_case_id'];
								$clientcasedetails[$keyscase]['case_name'] = $value['case_name'];
								// non billable
								//if($value['invoiced']!=2)
								//	$clientcasedetails[$keyscase]['total'] += $rate * $value['quantity'];

								//$clientcasedetails[$keyscase]['data'][$k] = $value;
								//$clientcasedetails[$keyscase]['data'][$k]['rate'] = $rate;
								//$clientcasedetails[$keyscase]['data'][$k]['subtotal'] = $rate * $value['quantity'];
							} else {
								// clientcasedetails
								$clientcasedetails[$keysclient]['client_id'] = $value['client_id'];
								$clientcasedetails[$keysclient]['client_name'] = $value['client_name'];
								// non billable
								//if($value['invoiced']!=2)
								//	$clientcasedetails[$keysclient]['total'] += $rate * $value['quantity'];

								$clientcasedetails[$keysclient][$keyscase]['client_case_id'] = $value['client_case_id'];
								$clientcasedetails[$keysclient][$keyscase]['case_name'] = $value['case_name'];
								//$clientcasedetails[$keysclient][$keyscase]['data'][$k] = $value;
								//$clientcasedetails[$keysclient][$keyscase]['data'][$k]['rate'] = $rate;
								//$clientcasedetails[$keysclient][$keyscase]['data'][$k]['subtotal'] = $rate * $value['quantity'];
							}
				}
			}
			/*$taskunitbillingdata = (new TasksUnitsBilling)->getTaskUnitBillingData($filteredClientcases, $filteredTeams, $filterdata['start_date'],$filterdata['end_date'],$clientSqlAll);
    		// echo "<pre>",print_r($taskunitbillingdata),"</pre>";die;
    		$taskunitbillingdata1 = array();

		    if(!empty($taskunitbillingdata)){
            	$invoiceAr = array();
            	$dataArray = array();
            	$dataArrayAccum = array();
            	foreach($taskunitbillingdata as $key=>$taskval1){
            		if($taskval1['created'] == 'Accumulated'){
            			if(!empty($dataArrayAccum)){
							$index = (new InvoiceFinal)->findIfAccumAdded($taskval1, $dataArrayAccum);
    	        			if(!empty($index)){
    	        				$unitexist = explode(",",$dataArrayAccum[$index['key']]['unitbilling_id']);
    	        				if(!in_array($taskval1['unitbilling_id'], $unitexist)){
									$dataArrayAccum[$index['key']]['unitbilling_id'] .= ",".$taskval1['unitbilling_id'];
									$dataArrayAccum[$index['key']]['invoicefinal_id'] .= ",".$taskval1['invoicefinal_id'];
									$dataArrayAccum[$index['key']]['quantity'] += $taskval1['quantity'];
    	        				}
    	        			} else {
    	        				$dataArrayAccum[] = $taskval1;
    	        			}
            			} else {
            				$dataArrayAccum[] = $taskval1;
            			}
            			$invoiceAr[$taskval1['unitbilling_id']] = $taskval1['unitbilling_id'];
            		} else {
            			$dataArray[] = $taskval1;
            		}
            	}

            	$dataArray = array_merge($dataArrayAccum, $dataArray);
//            	echo "<pre>",print_r($dataArray),"</pre>";die;

	    		foreach($dataArray as $key=>$taskval1){
					$taskunitbillingdata1[$taskval1['client_id'].'='.$taskval1['client_name'].'||'.$taskval1['client_case_id'].'='.$taskval1['case_name']][] = $taskval1;
				}

	    		/* Start : If Range PP then rate will be on the bases of total of all same unit along with same pricepoint of same location */
		       /* ///////$pricepointLocwisetotal = array();
		        $revisedArray=array();
		        if (!empty($taskunitbillingdata1)) {
		        	foreach ($taskunitbillingdata1 as $keysclientcase => $values){
	        			foreach ($values as $k => $value){
		        			if($value['invoiced'] != 2 && $value['temp_rate']=='')
		        				$pricepointLocwisetotal[$keysclientcase][$value['pricing_id']][$value['team_loc']][$value['unit_price_id']]['unit_total'] += $value['quantity'];
	        			}
		        	}

		        	foreach ($taskunitbillingdata1 as $keysclientcase => $values){
		        		foreach ($values as $k => $value){
		        			$clientcase = explode("||",$keysclientcase);
		        			$clientdata = explode("=",$clientcase[0]);
		        			$casedata = explode("=",$clientcase[1]);
		        			$keysclient = $clientdata[0];
		        			$keyscase = $casedata[0];

		        			if ($value['temp_rate']=='') {
		        				$quantity = $value['invoiced']==2?$value['quantity']:$pricepointLocwisetotal[$keysclientcase][$value['pricing_id']][$value['team_loc']][$value['unit_price_id']]['unit_total'];
				                $rate = (new TasksUnitsBilling)->checkpricingforrate($keysclient, $keyscase, $value['pricing_id'], $quantity ,"",$value['team_loc']);
				            } else {
				           		$rate = $value['temp_rate'];
				            }

							if ($value['temp_discount'] != '')
								$rate = $rate - ($rate * $value['temp_discount'] / 100);

							$taskunitbillingdata1[$keysclientcase][$k]['rate'] = $rate;
							$taskunitbillingdata1[$keysclientcase][$k]['subtotal'] = $rate * $value['quantity'];

							// for itemized view
							if($view=='Itemized'){
								// clientcasedetails
								$clientcasedetails[$keyscase]['client_id'] = $value['client_id'];
								$clientcasedetails[$keyscase]['client_name'] = $value['client_name'];
								$clientcasedetails[$keyscase]['client_case_id'] = $value['client_case_id'];
								$clientcasedetails[$keyscase]['case_name'] = $value['case_name'];
								// non billable
								if($value['invoiced']!=2)
									$clientcasedetails[$keyscase]['total'] += $rate * $value['quantity'];

								$clientcasedetails[$keyscase]['data'][$k] = $value;
								$clientcasedetails[$keyscase]['data'][$k]['rate'] = $rate;
								$clientcasedetails[$keyscase]['data'][$k]['subtotal'] = $rate * $value['quantity'];
							} else {
								// clientcasedetails
								$clientcasedetails[$keysclient]['client_id'] = $value['client_id'];
								$clientcasedetails[$keysclient]['client_name'] = $value['client_name'];
								// non billable
								if($value['invoiced']!=2)
									$clientcasedetails[$keysclient]['total'] += $rate * $value['quantity'];

								$clientcasedetails[$keysclient][$keyscase]['client_case_id'] = $value['client_case_id'];
								$clientcasedetails[$keysclient][$keyscase]['case_name'] = $value['case_name'];
								$clientcasedetails[$keysclient][$keyscase]['data'][$k] = $value;
								$clientcasedetails[$keysclient][$keyscase]['data'][$k]['rate'] = $rate;
								$clientcasedetails[$keysclient][$keyscase]['data'][$k]['subtotal'] = $rate * $value['quantity'];
							}
						}
		        	}
		        }
		        /* End : If Range PP then rate will be on the bases of total of all same unit along with same pricepoint of same location */
           /*////// }*/
           // echo "<pre>",print_r($filterdata),print_r($dataArray),print_r($taskunitbillingdata1);die();
        }

		// Array Dataprovider for clientcase details
        $clientcase_provider = new ArrayDataProvider([
			'allModels' => $clientcasedetails,
			'pagination' => [
				'pageSize' => -1,
			],
		]);

		if($flag=='reload'){
			if($view == 'Consolidated')
				return $this->renderAjax('display-consolidated-invoice',['client_ids'=>$client_ids,'client_case_ids'=>$client_case_ids,'data' => $filterdata, 'clientcaseprovider' => $clientcase_provider, 'billingdata' => $taskunitbillingdata1 ,'view'=>$view]);
			else
				return $this->renderAjax('display-itemized-invoice',['client_ids'=>$client_ids,'client_case_ids'=>$client_case_ids,'data' => $filterdata,'clientcaseprovider' => $clientcase_provider, 'billingdata' => $taskunitbillingdata1 ,'view'=>$view]);
		} else {
			$this->layout = 'billing';
			if($view == 'Consolidated')
				return $this->renderAjax('display-consolidated-invoice',['client_ids'=>$client_ids,'client_case_ids'=>$client_case_ids,'data' => $filterdata,'clientcaseprovider' => $clientcase_provider, 'billingdata' => $taskunitbillingdata1 ,'view'=>$view]);
			else
				return $this->renderAjax('display-itemized-invoice',['client_ids'=>$client_ids,'client_case_ids'=>$client_case_ids,'data' => $filterdata, 'clientcaseprovider' => $clientcase_provider, 'billingdata' => $taskunitbillingdata1 ,'view'=>$view]);
		}
    }

	public function actionBillingItemizedInvoice(){
		$post_data=Yii::$app->request->post();
		$client_case_id = $post_data['expandRowKey'];
		$filterdata = $post_data['filterdata'];
		$flag = Yii::$app->request->post('flag','');
        if($flag=='reload')
			$filterdata = json_decode(Yii::$app->request->post('filter_data'),true);

		$filteredTeams = isset($filterdata['teams']) && !empty($filterdata['teams'])?implode(',',$filterdata['teams']):0;


		$taskunitbillingdata = (new TasksUnitsBilling)->getTaskUnitBillingItemizedData($client_case_id, $filteredTeams, $filterdata['start_date'],$filterdata['end_date']);
		// echo "<pre>",print_r($taskunitbillingdata),"</pre>";die;
		$taskunitbillingdata1 = array();
		if(!empty($taskunitbillingdata)) {
            	$invoiceAr = array();
            	$dataArray = array();
            	$dataArrayAccum = array();
            	foreach($taskunitbillingdata as $key=>$taskval1){
            		if($taskval1['created'] == 'Accumulated'){
            			if(!empty($dataArrayAccum)){
							$index = (new InvoiceFinal)->findIfAccumAdded($taskval1, $dataArrayAccum);
    	        			if(!empty($index)){
    	        				$unitexist = explode(",",$dataArrayAccum[$index['key']]['unitbilling_id']);
    	        				if(!in_array($taskval1['unitbilling_id'], $unitexist)){
									$dataArrayAccum[$index['key']]['unitbilling_id'] .= ",".$taskval1['unitbilling_id'];
									$dataArrayAccum[$index['key']]['invoicefinal_id'] .= ",".$taskval1['invoicefinal_id'];
									$dataArrayAccum[$index['key']]['quantity'] += round($taskval1['quantity'],2);
    	        				}
    	        			} else {
    	        				$dataArrayAccum[] = $taskval1;
    	        			}
            			} else {
            				$dataArrayAccum[] = $taskval1;
            			}
            			$invoiceAr[$taskval1['unitbilling_id']] = $taskval1['unitbilling_id'];
            		} else {
            			$dataArray[] = $taskval1;
            		}
            	}

            	$dataArray = array_merge($dataArrayAccum, $dataArray);
//            	echo "<pre>",print_r($dataArray),"</pre>";die;

	    		foreach($dataArray as $key=>$taskval1){
					$taskunitbillingdata1[$taskval1['client_id'].'='.$taskval1['client_name'].'||'.$taskval1['client_case_id'].'='.$taskval1['case_name']][] = $taskval1;
				}

	    		/* Start : If Range PP then rate will be on the bases of total of all same unit along with same pricepoint of same location */
		        $pricepointLocwisetotal = array();
		        $revisedArray=array();
		        if (!empty($taskunitbillingdata1)) {
		        	foreach ($taskunitbillingdata1 as $keysclientcase => $values){
	        			foreach ($values as $k => $value){
		        			if($value['invoiced'] != 2 && $value['temp_rate']=='')
		        				$pricepointLocwisetotal[$keysclientcase][$value['pricing_id']][$value['team_loc']][$value['unit_price_id']]['unit_total'] += round($value['quantity'],2);
	        			}
		        	}
//		        	echo "<pre>",print_r($pricepointLocwisetotal),"</pre>";die;
		        	foreach ($taskunitbillingdata1 as $keysclientcase => $values){
		        		foreach ($values as $k => $value){
		        			$clientcase = explode("||",$keysclientcase);
		        			$clientdata = explode("=",$clientcase[0]);
		        			$casedata = explode("=",$clientcase[1]);
		        			$keysclient = $clientdata[0];
		        			$keyscase = $casedata[0];

		        			if ($value['temp_rate']=='') {
		        				$quantity = $value['invoiced']==2?round($value['quantity'],2):$pricepointLocwisetotal[$keysclientcase][$value['pricing_id']][$value['team_loc']][$value['unit_price_id']]['unit_total'];
				                $rate = (new TasksUnitsBilling)->checkpricingforrate($keysclient, $keyscase, $value['pricing_id'], $quantity ,"",$value['team_loc']);
				            } else {
				           		$rate = $value['temp_rate'];
				            }

							if ($value['temp_discount'] != '')
								$rate = $rate - ($rate * $value['temp_discount'] / 100);

							$taskunitbillingdata1[$keysclientcase][$k]['rate'] = $rate;
							$taskunitbillingdata1[$keysclientcase][$k]['subtotal'] = $rate * round($value['quantity'],2);

							// for itemized view
							/*if($view=='Itemized'){*/
								// clientcasedetails
								$clientcasedetails[$keyscase]['client_id'] = $value['client_id'];
								$clientcasedetails[$keyscase]['client_name'] = $value['client_name'];
								$clientcasedetails[$keyscase]['client_case_id'] = $value['client_case_id'];
								$clientcasedetails[$keyscase]['case_name'] = $value['case_name'];
								// non billable
								if($value['invoiced']!=2)
									$clientcasedetails[$keyscase]['total'] += $rate * round($value['quantity'],2);

								$clientcasedetails[$keyscase]['data'][$k] = $value;
								$clientcasedetails[$keyscase]['data'][$k]['rate'] = $rate;
								$clientcasedetails[$keyscase]['data'][$k]['subtotal'] = $rate * round($value['quantity'],2);
							/*} else {
								// clientcasedetails
								$clientcasedetails[$keysclient]['client_id'] = $value['client_id'];
								$clientcasedetails[$keysclient]['client_name'] = $value['client_name'];
								// non billable
								if($value['invoiced']!=2)
									$clientcasedetails[$keysclient]['total'] += $rate * $value['quantity'];

								$clientcasedetails[$keysclient][$keyscase]['client_case_id'] = $value['client_case_id'];
								$clientcasedetails[$keysclient][$keyscase]['case_name'] = $value['case_name'];
								$clientcasedetails[$keysclient][$keyscase]['data'][$k] = $value;
								$clientcasedetails[$keysclient][$keyscase]['data'][$k]['rate'] = $rate;
								$clientcasedetails[$keysclient][$keyscase]['data'][$k]['subtotal'] = $rate * $value['quantity'];
							//}*/
						}
		        	}
		        }
		        /* End : If Range PP then rate will be on the bases of total of all same unit along with same pricepoint of same location */
            }
		return $this->renderAjax('_billing-itemized-invoice',['client_case_id'=>$client_case_id,'data' => $clientcasedetails[$client_case_id]]);
	}

	/**
	*
	*
	*/
    public function actionBillingConsolidatedInvoice(){
		$post_data=Yii::$app->request->post();
		$client_id = $post_data['expandRowKey'];
		$filterdata = $post_data['filterdata'];
		$flag = Yii::$app->request->post('flag','');
        if($flag=='reload')
			$filterdata = json_decode(Yii::$app->request->post('filter_data'),true);

		$filteredTeams = isset($filterdata['teams']) && !empty($filterdata['teams'])?implode(',',$filterdata['teams']):0;


		$taskunitbillingdata = (new TasksUnitsBilling)->getTaskUnitBillingConsolidatedData($client_id, $filteredTeams, $filterdata['start_date'],$filterdata['end_date']);
		// echo "<pre>",print_r($taskunitbillingdata),"</pre>";die;
		$taskunitbillingdata1 = array();
		if(!empty($taskunitbillingdata)) {
            	$invoiceAr = array();
            	$dataArray = array();
            	$dataArrayAccum = array();
            	foreach($taskunitbillingdata as $key=>$taskval1){
            		if($taskval1['created'] == 'Accumulated'){
            			if(!empty($dataArrayAccum)){
							$index = (new InvoiceFinal)->findIfAccumAdded($taskval1, $dataArrayAccum);
    	        			if(!empty($index)){
    	        				$unitexist = explode(",",$dataArrayAccum[$index['key']]['unitbilling_id']);
    	        				if(!in_array($taskval1['unitbilling_id'], $unitexist)){
									$dataArrayAccum[$index['key']]['unitbilling_id'] .= ",".$taskval1['unitbilling_id'];
									$dataArrayAccum[$index['key']]['invoicefinal_id'] .= ",".$taskval1['invoicefinal_id'];
									$dataArrayAccum[$index['key']]['quantity'] += round($taskval1['quantity'],2);
    	        				}
    	        			} else {
    	        				$dataArrayAccum[] = $taskval1;
    	        			}
            			} else {
            				$dataArrayAccum[] = $taskval1;
            			}
            			$invoiceAr[$taskval1['unitbilling_id']] = $taskval1['unitbilling_id'];
            		} else {
            			$dataArray[] = $taskval1;
            		}
            	}

            	$dataArray = array_merge($dataArrayAccum, $dataArray);
//            	echo "<pre>",print_r($dataArray),"</pre>";die;

	    		foreach($dataArray as $key=>$taskval1){
					$taskunitbillingdata1[$taskval1['client_id'].'='.$taskval1['client_name'].'||'.$taskval1['client_case_id'].'='.$taskval1['case_name']][] = $taskval1;
				}

	    		/* Start : If Range PP then rate will be on the bases of total of all same unit along with same pricepoint of same location */
		        $pricepointLocwisetotal = array();
		        $revisedArray=array();
		        if (!empty($taskunitbillingdata1)) {
		        	foreach ($taskunitbillingdata1 as $keysclientcase => $values){
	        			foreach ($values as $k => $value){
		        			if($value['invoiced'] != 2 && $value['temp_rate']=='')
		        				$pricepointLocwisetotal[$keysclientcase][$value['pricing_id']][$value['team_loc']][$value['unit_price_id']]['unit_total'] += round($value['quantity'],2);
	        			}
		        	}
//		        	echo "<pre>",print_r($pricepointLocwisetotal),"</pre>";die;
		        	foreach ($taskunitbillingdata1 as $keysclientcase => $values){
		        		foreach ($values as $k => $value){
		        			$clientcase = explode("||",$keysclientcase);
		        			$clientdata = explode("=",$clientcase[0]);
		        			$casedata = explode("=",$clientcase[1]);
		        			$keysclient = $clientdata[0];
		        			$keyscase = $casedata[0];

		        			if ($value['temp_rate']=='') {
		        				$quantity = $value['invoiced']==2?round($value['quantity'],2):$pricepointLocwisetotal[$keysclientcase][$value['pricing_id']][$value['team_loc']][$value['unit_price_id']]['unit_total'];
				                $rate = (new TasksUnitsBilling)->checkpricingforrate($keysclient, $keyscase, $value['pricing_id'], $quantity ,"",$value['team_loc']);
				            } else {
				           		$rate = $value['temp_rate'];
				            }

							if ($value['temp_discount'] != '')
								$rate = $rate - ($rate * $value['temp_discount'] / 100);

							$taskunitbillingdata1[$keysclientcase][$k]['rate'] = $rate;
							$taskunitbillingdata1[$keysclientcase][$k]['subtotal'] = $rate * round($value['quantity'],2);

							// for itemized view
							/*if($view=='Itemized'){
								// clientcasedetails
								$clientcasedetails[$keyscase]['client_id'] = $value['client_id'];
								$clientcasedetails[$keyscase]['client_name'] = $value['client_name'];
								$clientcasedetails[$keyscase]['client_case_id'] = $value['client_case_id'];
								$clientcasedetails[$keyscase]['case_name'] = $value['case_name'];
								// non billable
								if($value['invoiced']!=2)
									$clientcasedetails[$keyscase]['total'] += $rate * $value['quantity'];

								$clientcasedetails[$keyscase]['data'][$k] = $value;
								$clientcasedetails[$keyscase]['data'][$k]['rate'] = $rate;
								$clientcasedetails[$keyscase]['data'][$k]['subtotal'] = $rate * $value['quantity'];
							//} else {*/
								// clientcasedetails
								$clientcasedetails[$keysclient]['client_id'] = $value['client_id'];
								$clientcasedetails[$keysclient]['client_name'] = $value['client_name'];
								// non billable
								if($value['invoiced']!=2)
									$clientcasedetails[$keysclient]['total'] += $rate * round($value['quantity'],2);

								$clientcasedetails[$keysclient][$keyscase]['client_case_id'] = $value['client_case_id'];
								$clientcasedetails[$keysclient][$keyscase]['case_name'] = $value['case_name'];
								$clientcasedetails[$keysclient][$keyscase]['data'][$k] = $value;
								$clientcasedetails[$keysclient][$keyscase]['data'][$k]['rate'] = $rate;
								$clientcasedetails[$keysclient][$keyscase]['data'][$k]['subtotal'] = $rate * round($value['quantity'],2);
							//}
						}
		        	}
		        }
		        /* End : If Range PP then rate will be on the bases of total of all same unit along with same pricepoint of same location */
            }
		//echo "<pre>",print_r($clientcasedetails);die;
		return $this->renderAjax('_billing-consolidated-invoice',['client_id'=>$client_id,'data' => $clientcasedetails[$client_id]]);
	}
	/**
		* To get existing invoices for client and case
		* @param client_id
		* @param client_case_id
		* @param display_type (itemized or consolidated)
	*/
	public function actionGetExistingInvoices()
	{
		$client_id = Yii::$app->request->post('client_id');
		$client_case_id = Yii::$app->request->post('client_case_id');
		$display_type = Yii::$app->request->post('display_type');

		$existing_invoices = (new InvoiceFinal)->getExistingInvoicesForClientCase($client_id, $client_case_id, $display_type);
		$invoice_data = array();
		foreach($existing_invoices as $val)
		{
			$invoice_data[$val['id']] = "Invoice #".$val['id']." ".(new Options)->ConvertOneTzToAnotherTz($val['created_date'], 'UTC', $_SESSION['usrTZ'],'MDY');
		}
		$html = "";
		if(count($invoice_data) > 0)
		{
				if(count($invoice_data) == 1)
				{
					foreach($invoice_data as $key => $val)
					{
							$html = '<span style="padding-left: 30px;color: #666;">'.$val.'</span><input type="hidden" id="selected_invoice" name="selected_invoice" value="'.$key.'" />';	
					}
				}
				else
				{
					
					$stroptions = "";
					foreach($invoice_data as $key => $val)
					{
							if($stroptions!="")
								$stroptions = $stroptions.'<option value="'.$key.'">'.$val.'</option>';
							else
								$stroptions = '<option value="'.$key.'">'.$val.'</option>';

					}
					$html = $stroptions;
				}
		}
		$resp_arr = array();
		$resp_arr['invoice_count'] = count($invoice_data);
		$resp_arr['html_text'] = $html;		
		echo json_encode($resp_arr);
		die;
	}
    /**
     * To finalize an invoice for selected units.
     * @param selcted value = json_encode(['client_id'=>3, 'client_case_id'=>1, 'display_by'=>1/2, 'has_accum_cost'=>1/0, 'billing_unit_id' => 3,'invoice_id'=>'5,3,4,6', 'team_loc' => 6, 'final_rate' => 125.00, 'discount' => 0.00, 'discount_reason' => '', 'internal_ref_no_id' => '','isbillable'=>1/0])
     * @param selected checkboxes named with "final_units"
     */
    public function actionFinalizeInvoice()
    {
    	$final_units = Yii::$app->request->post('final_units');
    	$display_type = Yii::$app->request->post('display_type');
			$finalize_type = Yii::$app->request->post('finalize_type');
			$invoice_id = Yii::$app->request->post('existing_invoice_id');
    	//echo "<pre>",print_r($final_units),print_r($display_type),"</pre>";die;
    	if(!empty($final_units)) {
    		$invoicefinal = array();
    		$invoicefinalbilling = array();
    		$invoicefinaltax = array();
    		$clientAr = array();
    		$i = 0;
    		$pricepointLocwisetotal = array();
    		$invoiceAr = array();
    		foreach($final_units as $unit) {

				$finalunits = json_decode($unit,true);

    			if($finalunits['isnonbillable']==0){
	    			// Itemized
	    			if($display_type == 'Itemized') {
	    				/* Start : Array for invoice_final_billing table */
		    			if($finalunits['has_accum_cost'] == 1) {
		    				$billing_unit_id = explode(",",$finalunits['billing_unit_id']);
		    				foreach($billing_unit_id as $unit_id) {

		    					$quantity = TasksUnitsBilling::findOne($unit_id)->quantity;
		    					$invoicefinalbilling[$finalunits['client_case_id']][$i]['billing_unit_id'] = $unit_id;
		    					$invoicefinalbilling[$finalunits['client_case_id']][$i]['client_id'] = $finalunits['client_id'];
		    					$invoicefinalbilling[$finalunits['client_case_id']][$i]['pricing_id'] = $finalunits['pricing_id'];
		    					$invoicefinalbilling[$finalunits['client_case_id']][$i]['unit_price_id'] = $finalunits['unit_price_id'];
		    					$invoicefinalbilling[$finalunits['client_case_id']][$i]['quantity'] = round($quantity,2);
		    					$invoicefinalbilling[$finalunits['client_case_id']][$i]['team_loc'] = $finalunits['team_loc'];
		    					$invoicefinalbilling[$finalunits['client_case_id']][$i]['final_rate'] = $finalunits['final_rate'];
		    					$invoicefinalbilling[$finalunits['client_case_id']][$i]['discount'] = $finalunits['discount'];
		    					$invoicefinalbilling[$finalunits['client_case_id']][$i]['discount_reason'] = $finalunits['discount_reason'];
		    					$invoicefinalbilling[$finalunits['client_case_id']][$i]['internal_ref_no_id'] = $finalunits['internal_ref_no_id'];

		    					/* Start : Array for invoice_final_billing_tax table */
		    					$unittax = (new TaxClass)->getTaxAndClassByPricingClientId($finalunits['pricing_id'],$finalunits['client_id']);
		    					if(!empty($unittax)) {
		    						$invoicefinaltax[$finalunits['client_case_id']][$unit_id] = $unittax;
		    					}
		    					/* End : Array for invoice_final_billing_tax table */
		    					$i++;
		    				}
		    			} else {

		    				$invoicefinalbilling[$finalunits['client_case_id']][$i]['billing_unit_id'] = $finalunits['billing_unit_id'];
		    				$invoicefinalbilling[$finalunits['client_case_id']][$i]['client_id'] = $finalunits['client_id'];
		    				$invoicefinalbilling[$finalunits['client_case_id']][$i]['pricing_id'] = $finalunits['pricing_id'];
		    				$invoicefinalbilling[$finalunits['client_case_id']][$i]['unit_price_id'] = $finalunits['unit_price_id'];
		    				$invoicefinalbilling[$finalunits['client_case_id']][$i]['quantity'] = round($finalunits['quantity'],2);
	    					$invoicefinalbilling[$finalunits['client_case_id']][$i]['team_loc'] = $finalunits['team_loc'];
	    					$invoicefinalbilling[$finalunits['client_case_id']][$i]['final_rate'] = $finalunits['final_rate'];
	    					$invoicefinalbilling[$finalunits['client_case_id']][$i]['temp_rate'] = $finalunits['temp_rate'];
	    					$invoicefinalbilling[$finalunits['client_case_id']][$i]['discount'] = $finalunits['discount'];
	    					$invoicefinalbilling[$finalunits['client_case_id']][$i]['discount_reason'] = $finalunits['discount_reason'];
	    					$invoicefinalbilling[$finalunits['client_case_id']][$i]['internal_ref_no_id'] = $finalunits['internal_ref_no_id'];
							// echo $i."<pre>",print_r($invoicefinalbilling),"</pre>";
	    					/* Start : Array for invoice_final_billing_tax table */
		    				$unittax = (new TaxClass)->getTaxAndClassByPricingClientId($finalunits['pricing_id'],$finalunits['client_id']);
	    					if(!empty($unittax)) {
	    						$invoicefinaltax[$finalunits['client_case_id']][$finalunits['billing_unit_id']] = $unittax;
	    					}
		    				/* End : Array for invoice_final_billing_tax table */
	    					$i++;
		    			}
		    			/* End : Array for invoice_final_billing table */
		    			// echo "<pre>",print_r($invoicefinalbilling),"</pre>";
		    			/* Start : Array for invoice_final table */
		    			$invoicefinal[$finalunits['client_case_id']]['client_id']=$finalunits['client_id'];
		    			$invoicefinal[$finalunits['client_case_id']]['client_case_id']=$finalunits['client_case_id'];
		    			$caseContact = CaseContacts::find()->joinWith(['clientContacts'=>function(\yii\db\ActiveQuery $query){$query->where(['tbl_client_contacts.contact_type'=>'Billing']);}])->select(['tbl_case_contacts.client_contacts_id'])->where([ 'tbl_case_contacts.client_case_id'=>$finalunits['client_case_id']])->one();
		    			if(!empty($caseContact)){
							$invoicefinal[$finalunits['client_case_id']]['contact_id']=$caseContact->client_contacts_id;
						}else{
							$invoicefinal[$finalunits['client_case_id']]['contact_id']= 0 ;
						}
	    				$invoicefinal[$finalunits['client_case_id']]['display_by']=1;

	    				if(!isset($invoicefinal[$finalunits['client_id']]['has_accum_cost']) || (isset($invoicefinal[$finalunits['client_id']]['has_accum_cost']) && $invoicefinal[$finalunits['client_id']]['has_accum_cost']==0))
	    					$invoicefinal[$finalunits['client_case_id']]['has_accum_cost'] = $finalunits['has_accum_cost'];
	    				/* End : Array for invoice_final table */
	    			}

					// Consolidated
	    			if($display_type == 'Consolidated') {
	    				/* Start : Array for invoice_final_billing table */
		    			if($finalunits['has_accum_cost'] == 1) {
		    				$billing_unit_id = explode(",", $finalunits['billing_unit_id']);
		    				foreach($billing_unit_id as $unit_id) {
		    					$quantity = TasksUnitsBilling::findOne($unit_id)->quantity;
		    					$invoicefinalbilling[$finalunits['client_id']][$i]['billing_unit_id'] = $unit_id;
		    					$invoicefinalbilling[$finalunits['client_id']][$i]['client_id'] = $finalunits['client_id'];
		    					$invoicefinalbilling[$finalunits['client_id']][$i]['pricing_id'] = $finalunits['pricing_id'];
		    					$invoicefinalbilling[$finalunits['client_id']][$i]['unit_price_id'] = $finalunits['unit_price_id'];
		    					$invoicefinalbilling[$finalunits['client_id']][$i]['quantity'] = round($quantity,2);
		    					$invoicefinalbilling[$finalunits['client_id']][$i]['team_loc'] = $finalunits['team_loc'];
		    					$invoicefinalbilling[$finalunits['client_id']][$i]['final_rate'] = $finalunits['final_rate'];
		    					$invoicefinalbilling[$finalunits['client_id']][$i]['discount'] = $finalunits['discount'];
		    					$invoicefinalbilling[$finalunits['client_id']][$i]['discount_reason'] = $finalunits['discount_reason'];
		    					$invoicefinalbilling[$finalunits['client_id']][$i]['internal_ref_no_id'] = $finalunits['internal_ref_no_id'];

		    					/* Start : Array for invoice_final_billing_tax table */
		    					$unittax = (new TaxClass)->getTaxAndClassByPricingClientId($finalunits['pricing_id'],$finalunits['client_id']);
		    					if(!empty($unittax)) {
		    						$invoicefinaltax[$finalunits['client_id']][$unit_id] = $unittax;
		    					}
		    					/* End : Array for invoice_final_billing_tax table */
		    					$i++;
		    				}
		    			} else {
		    				$invoicefinalbilling[$finalunits['client_id']][$i]['billing_unit_id'] = $finalunits['billing_unit_id'];
		    				$invoicefinalbilling[$finalunits['client_id']][$i]['client_id'] = $finalunits['client_id'];
		    				$invoicefinalbilling[$finalunits['client_id']][$i]['pricing_id'] = $finalunits['pricing_id'];
		    				$invoicefinalbilling[$finalunits['client_id']][$i]['unit_price_id'] = $finalunits['unit_price_id'];
		    				$invoicefinalbilling[$finalunits['client_id']][$i]['quantity'] = round($finalunits['quantity'],2);
	    					$invoicefinalbilling[$finalunits['client_id']][$i]['team_loc'] = $finalunits['team_loc'];
	    					$invoicefinalbilling[$finalunits['client_id']][$i]['final_rate'] = $finalunits['final_rate'];
	    					$invoicefinalbilling[$finalunits['client_id']][$i]['temp_rate'] = $finalunits['temp_rate'];
	    					$invoicefinalbilling[$finalunits['client_id']][$i]['discount'] = $finalunits['discount'];
	    					$invoicefinalbilling[$finalunits['client_id']][$i]['discount_reason'] = $finalunits['discount_reason'];
	    					$invoicefinalbilling[$finalunits['client_id']][$i]['internal_ref_no_id'] = $finalunits['internal_ref_no_id'];

	    					/* Start : Array for invoice_final_billing_tax table */
		    				$unittax = (new TaxClass)->getTaxAndClassByPricingClientId($finalunits['pricing_id'],$finalunits['client_id']);
	    					if(!empty($unittax)) {
	    						$invoicefinaltax[$finalunits['client_id']][$finalunits['billing_unit_id']] = $unittax;
	    					}
		    				/* End : Array for invoice_final_billing_tax table */
	    					$i++;
		    			}
		    			/* End : Array for invoice_final_billing table */

		    			/* Start : Array for invoice_final table */
		    			$invoicefinal[$finalunits['client_id']]['client_id']=$finalunits['client_id'];
		    			$clientContact = ClientContacts::find()->select(['id'])->where(['client_id'=>$finalunits['client_id'],'contact_type'=>'Billing'])->one();
		    			if(!empty($clientContact)){
							$invoicefinal[$finalunits['client_id']]['contact_id']=$clientContact->id;
						}else{
							$invoicefinal[$finalunits['client_id']]['contact_id']=0;
						}
	    				$invoicefinal[$finalunits['client_id']]['display_by']=2;
	    			//	if(isset($finalunits['isnonbillable']) && $finalunits['isnonbillable']!=1)
							//$invoicefinal[$finalunits['client_case_id']]['total']+=$finalunits['subtotal'];
	    				if(!isset($invoicefinal[$finalunits['client_id']]['has_accum_cost']) || (isset($invoicefinal[$finalunits['client_id']]['has_accum_cost']) && $invoicefinal[$finalunits['client_id']]['has_accum_cost']==0))
	    					$invoicefinal[$finalunits['client_id']]['has_accum_cost'] = $finalunits['has_accum_cost'];
	    				/* End : Array for invoice_final table */
	    			}
	    			//$invoiceAr[$finalunits['client_case_id']] = $finalunits;
    				$pricepointLocwisetotal[$finalunits['client_id']][$finalunits['pricing_id']][$finalunits['team_loc']][$finalunits['unit_price_id']]['unit_total'] += round($finalunits['quantity'],2);
    			}
    		}
    		//echo "<pre>",print_r($pricepointLocwisetotal),print_r($finalunits),"</pre>";
    		/* Start : Get pricing rate as per tiered range */
    		if(!empty($invoicefinalbilling)){
    			foreach($invoicefinalbilling as $key => $finalizedunit){
    				foreach($finalizedunit as $key1 => $invoicedata){

						$quantity = $pricepointLocwisetotal[$key][$invoicedata['pricing_id']][$invoicedata['team_loc']][$invoicedata['unit_price_id']]['unit_total'];
		                $rate = (new TasksUnitsBilling)->checkpricingforrate($invoicedata['client_id'], $key, $invoicedata['pricing_id'], round($quantity,2) ,"",$invoicedata['team_loc']);

		                $rate = $invoicedata['temp_rate']!=''?$invoicedata['temp_rate']:$rate;

		                if ($invoicedata['discount'] != '')
							$rate = $rate - ($rate * $invoicedata['discount'] / 100);

		                $finalrate = round($invoicedata['quantity'],2) * $rate;

		                $total += $finalrate;

		                $invoicefinalbilling[$key][$key1]['final_rate'] = $rate;

    				}
    		//		$invoicefinal[$key]['total'] = $total;
    			}
    		}
    		/* End : Get pricing rate as per tiered range */

			// echo "<pre>",print_r($invoicefinal),print_r($invoicefinalbilling),print_r($invoicefinaltax),"</pre>";die;
			$transaction = Yii::$app->db->beginTransaction();

			try{
			//	echo "<pre>",print_r($invoicefinal),"</pre>";die;
				if(!empty($invoicefinal)) {
					foreach ($invoicefinal as $key => $invoicedata) {
						if($key == ""){continue;}
						$InvoiceFinalData = array("InvoiceFinal"=>$invoicedata);
						if($finalize_type==0)
						{
							$invoiceModel = new InvoiceFinal;
							$invoiceModel->load($InvoiceFinalData);
							$invoiceModel->save(false); // Save Invoice Final Data
							$invoice_final_id = Yii::$app->db->getLastInsertId();
						}
						else {
							$invoice_final_id = $invoice_id;
						}
						if(isset($invoicefinalbilling[$key])) {
							foreach($invoicefinalbilling[$key] as $billingdata) {
								$billingdata['invoice_final_id'] = $invoice_final_id;
								$InvoiceFinalBillingData = array("InvoiceFinalBilling"=>$billingdata);
								$invoiceBillingModel = new InvoiceFinalBilling;
								$invoiceBillingModel->load($InvoiceFinalBillingData);
								$invoiceBillingModel->save(false); // Save Invoice Final Billing Data
								$invoice_final_billing_id = Yii::$app->db->getLastInsertId();

								if(isset($invoicefinaltax[$key][$billingdata['billing_unit_id']])) {
									foreach($invoicefinaltax[$key][$billingdata['billing_unit_id']] as $taxdata) {
										$taxdata['invoice_final_billing_id'] = $invoice_final_billing_id;
										$InvoiceFinalTaxesData = array("InvoiceFinalTaxes"=>$taxdata);
										$invoiceTaxesModel = new InvoiceFinalTaxes;
										$invoiceTaxesModel->load($InvoiceFinalTaxesData);
										$invoiceTaxesModel->save(false); // Save Invoice Final Taxes Data
									}
								}
								$unit_data=TasksUnitsBilling::findOne($billingdata['billing_unit_id'])->invoiced;
//                                                                echo '<pre>';
//                                                                print_r($unit_data);
//                                                                echo '=>'.$billingdata['billing_unit_id'];die;
								if($unit_data != 2) {
                                                                    // status changed to invoiced = 1 for non billable item
                                                                    TasksUnitsBilling::updateAll(['invoiced' => 1] ,'id='.$billingdata['billing_unit_id']);
                                                                    //continue;
                                                                }
							}
						}
					}
				}
				$transaction->commit();

			} catch (Exception $e){
				$transaction->rollBack();
			}
    	}
    	return $this->redirect(Url::to(['billing-finalized-invoice/finalized-invoices']),302);
    }

    /**
	 * Edit Invoice form of Generated invoice
	 * @params final_units
	 * @return mixed
	 */
    public function actionEditInvoice(){
		$view_type = Yii::$app->request->post('display_type');
		$final_value = json_decode(Yii::$app->request->post('final_units'),true);
		return $this->renderAjax('edit-invoice',['data' => $final_value, 'display_type' => $view_type]);
	}

	/**
	 * Update Invoice of Generated invoice (itemized/consolidated)
	 * @return mixed
	 */
	public function actionUpdateInvoice(){
		$id = Yii::$app->request->post('billing_unit_id');

		// check rate is available or not
		$taskBilling = $this->find_taskunitbilling($id);
		if(Yii::$app->request->post('pre_rate')!=Yii::$app->request->post('temp_rate'))
			$taskBilling->temp_rate	= Yii::$app->request->post('temp_rate');

		$desc = Yii::$app->request->post('desc');
		if(isset($desc) && trim($desc)!=""){
			$taskBilling->billing_desc =$desc;
		}
		$taskBilling->quantity = Yii::$app->request->post('quantity');
		$taskBilling->invoiced = Yii::$app->request->post('isnonbillable');
		if($taskBilling->save(false)){ // update the task unit billing rate,desc,quantity
			echo "OK";
		}
	}

	/**
	 * Edit Form for Discount Invoice in dialogue box
	 * @params final_units
	 * @return mixed
	 */
	public function actionAddDiscountInvoice(){
		$view_type = Yii::$app->request->post('display_type');
		$final_value = json_decode(Yii::$app->request->post('final_units'),true);
		return $this->renderAjax('add-discount-invoice',['data' => $final_value, 'display_type' => $view_type]);
	}

	/**
	 * Update Discount in Generated invoice
	 * @return mixed
	 */
	 public function actionUpdateDiscount(){
		$id = Yii::$app->request->post('billing_unit_id');
		$taskBilling = $this->find_taskunitbilling($id);
		$taskBilling->temp_discount	= Yii::$app->request->post('temp_discount');
		$taskBilling->temp_discount_reason = Yii::$app->request->post('temp_discount_reason');
		if($taskBilling->save(false)){ // update the task unit billing discount
			echo "OK";
		}
	 }

	 /**
	  * get details of Task unit Billing
	  * @return mixed
	  */
	  public function find_taskunitbilling($id){
		 if (($model = TasksUnitsBilling::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
	 }
}
