<?php

namespace app\controllers;
use Yii;
use yii\web\Controller;
use yii\web\Session;
use app\models\ClientCase;
use app\models\Tasks;
use app\models\User;
use app\models\InvoiceFinal;
use yii\data\ArrayDataProvider;
use yii\helpers\Html;

class CaseBudgetController extends Controller
{
	public function beforeAction($action)
	{
		if (Yii::$app->user->isGuest)
			$this->redirect(array('site/login'));
			
		if (!(new User)->checkAccess(4.09) && $action->id == 'index')/* 38 */
			throw new \yii\web\HttpException(404, 'User permissions do not allow entry into this module.');	
			
		
		return parent::beforeAction($action);
	} 
    public function actionIndex($case_id)
    {
    	$case_info = ClientCase::findOne($case_id);
    	if (Yii::$app->request->isAjax &&  Yii::$app->request->post()) {
			$post_data=Yii::$app->request->post('ClientCase');
			//echo "<pre>",print_r($post_data);
			$this->layout = null;
    		$post_data = Yii::$app->request->post('ClientCase');
    		$case_info->budget_value = Html::encode($post_data['budget_value']);
    		$case_info->budget_alert = Html::encode($post_data['budget_alert']);
			//echo "<pre>",print_r($case_info);die;
    		$case_info->save(false);
    		print_r(round(Html::encode($post_data['budget_value'])) . "|" . round(Html::encode($post_data['budget_alert'])));
    		exit;
    	}
    	$this->layout = 'mycase';
    	$caseSpendPerProject = array();
    	$task_data = Tasks::find()->where('client_case_id In (' . $case_id . ')')->select('id')->orderBy('created desc')->all();
    	$total = 0;	$invoiced_total=0;	$pending_total=0;$main_total=0;
    	foreach ($task_data as $tdata) {
    		$invoiced = (new InvoiceFinal)->invoicedBillInvoice($tdata->id);
    		$pending  = (new InvoiceFinal)->pendingBillInvoice($tdata->id);
    		if ($invoiced != 0 || $pending != 0) {
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
    	if(!empty($caseSpendPerProject)){
    		$caseSpendPerProject['total'] = array(
    				'project_id' => 'Spend Totals',
    				'project_name' => '',
    				'invoiced' => $invoiced_total,
    				'pending' => $pending_total,
    				'total_spent'=> $total,
    		);
    	}
    	//echo "<pre>",print_r($caseSpendPerProject),"</pre>";die;
    	$dataProvider = new ArrayDataProvider([
    			'allModels' => $caseSpendPerProject,
    			'pagination' => [
    					'pageSize' => '-1',
    			],
    			'sort' => [
    					'attributes' => ['project_id', 'project_name','invoiced','pending'],
    			],
    	]);    	
    	$client_case_length = (new User)->getTableFieldLimit('tbl_client_case');
    	// get the rows in the currently requested page
        return $this->render('index',['case_id'=>$case_id,'case_info'=>$case_info,'caseSpendPerProject'=>$caseSpendPerProject,'dataProvider'=>$dataProvider, 'total' => $total,'client_case_length' => $client_case_length ]);
    }

}
