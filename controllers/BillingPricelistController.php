<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Session;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\widgets\ActiveForm;
use yii\data\ActiveDataProvider;
use yii\helpers\Json;
use yii\helpers\Html;

use app\models\User;
use app\models\Team;
use app\models\ProjectSecurity;
use app\models\Pricing;
use app\models\search\PricingSearch;
use app\models\Unit;
use app\models\PricingUtbmsCodes;
use app\models\Servicetask;
use app\models\PricingRates;
use app\models\TeamlocationMaster;
use app\models\TeamLocs;
use app\models\PricingServiceTask;
use app\models\PricingDisplayTeams;
use app\models\TasksUnitsBilling;
use app\models\PricingTemplates;
use app\models\PricingTemplatesIds;
use app\models\PricingClients;
use app\models\PricingClientsRates;
use app\models\PricingClientscases;
use app\models\PricingClientscasesRates;
use app\models\ClientCase;
use app\models\Client;
use app\models\Options;
use yii\helpers\Url;
class BillingPricelistController extends \yii\web\Controller
{
	/**
     * @inheritdoc
     */

    public function beforeAction($action)
	{
		if (Yii::$app->user->isGuest)
			$this->redirect(array('site/login'));

		if ((!(new User)->checkAccess(7)) || (!(new User)->checkAccess(7.01)) || (!(new User)->checkAccess(7.02) && $action->id == 'internal-team-pricing') || (!(new User)->checkAccess(7.04) && $action->id == 'internal-shared-pricing') || (!(new User)->checkAccess(7.08) && $action->id == 'get-preferred-pricing' && $_REQUEST['type'] == 'client') || (!(new User)->checkAccess(7.06) && $action->id == 'get-preferred-pricing' && $_REQUEST['type'] == 'case')){
			/* IRT 31 Default landing page */
			$def_land_page = Options::find()->where(['user_id' => Yii::$app->user->identity->id])->one()->default_landing_page;
            if($def_land_page=='') $def_land_page = '';
            if($def_land_page==1) { // Show My Assignments
				if((new User)->checkAccess(1)) {
					$redirect_method[] = 'site/index';
				} else {
					$redirect_method[]=(new User)->checkOtherModuleAccess(1);
				}
			} else if ($def_land_page==2){ // Show Media
				if((new User)->checkAccess(3)) {
					$redirect_method[] = 'media/index';
				} else {
					$redirect_method[]=(new User)->checkOtherModuleAccess(2);
				}
			} else if ($def_land_page==3){ // Show My Teams
				if((new User)->checkAccess(4)) {
					$redirect_method[] = 'mycase/index';
				} else {
					$redirect_method[]=(new User)->checkOtherModuleAccess(3);
				}
			} else if ($def_land_page==4){ // Show My Cases
				if((new User)->checkAccess(5)) {
					$redirect_method[] = 'team/index';
				} else {
					$redirect_method[]=(new User)->checkOtherModuleAccess(4);
				}
			} else if ($def_land_page==5){ // Show Global Projects
				if((new User)->checkAccess(2)) {
					$redirect_method[] = 'global-projects/index';
				} else {
					$redirect_method[]=(new User)->checkOtherModuleAccess(5);
				}
			} else if ($def_land_page==6){ // Show Billing
				if((new User)->checkAccess(7)) {
					$redirect_method[] = 'billing-pricelist/internal-team-pricing';
				} else {
					$redirect_method[]=(new User)->checkOtherModuleAccess(6);
				}
			} else if ($def_land_page==7){ // Show Report
				if((new User)->checkAccess(11)) {
					$redirect_method[] = 'custom-report/index';
				} else {
					$redirect_method[]=(new User)->checkOtherModuleAccess(7);
				}
			} else if ($def_land_page==8){ // Show Administrator
				if((new User)->checkAccess(8)) {
					$redirect_method[] =  'site/administration';
				} else {
					$redirect_method[]=(new User)->checkOtherModuleAccess(8);
				}
			} else {
				if((new User)->checkAccess(1)) {
					return $this->redirect(array(
						'site/index'
					));
				} elseif((new User)->checkAccess(3)) {
					return $this->redirect(array(
						'media/index'
					));
				} elseif((new User)->checkAccess(4)) {
					return $this->redirect(array(
						'mycase/index'
					));
				} elseif((new User)->checkAccess(5)) {
					return $this->redirect(array(
						'team/index'
					));
				} elseif((new User)->checkAccess(2)) {
					return $this->redirect(array(
						'global-projects/index'
					));
				} elseif((new User)->checkAccess(7)) {
					return $this->redirect(array(
						'billing-pricelist/internal-team-pricing'
					));
				} elseif((new User)->checkAccess(75)) {
					return $this->redirect(array(
						'site/reports'
					));
				} elseif((new User)->checkAccess(8)) {
					return $this->redirect(array(
						'site/administration'
					));
				} else{
					return $this->goBack();
				}
			}
			return $this->redirect($redirect_method);
			//throw new \yii\web\HttpException(404, 'User permissions do not allow entry into this module.');
		}


		return parent::beforeAction($action);
	}

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

	/**
     * Filter GridView with Ajax
     * */
    public function actionAjaxFilter(){
	    $searchModel = new PricingSearch();
	    $searchArray = array_merge(['team_id'=>$team_id,'remove'=>0],Yii::$app->request->queryParams);
	    $dataProvider = $searchModel->searchFilter($searchArray);
	    $out['results']=array();
	    foreach ($dataProvider as $key=>$val){
		    $out['results'][] = ['id' => $val, 'text' => $val,'label' => $val];
	    }
	    return json_encode($out);
    }
    /**
     * It will load Team Pricing View.
     * 2nd section will load entire Teams to select.
     * 3rd section will load pricepoint list by selecting Team.
     */
    public function actionInternalTeamPricing()
    {
    	$this->layout = 'billing';
    	$roleId = Yii::$app->user->identity->role_id;
    	$userId = Yii::$app->user->identity->id;
		$role_info = $_SESSION['role'];
    	$roleTypes = explode(",",$role_info->role_type);

    	if ($roleId == 0) {
    		$teamList = Team::find()->select(['id', 'team_name','team_type'])->orderBy('sort_order')->all();
    	} else {
    		$team_data = ProjectSecurity::find()->select('team_id')->where('team_id !=0 AND user_id='.Yii::$app->user->identity->id)->groupBy('team_id');
    		if(in_array(1, $roleTypes)) {
    			$teamList = Team::find()->select(['id', 'team_name','team_type'])->where(['or','id=1',['in','id', $team_data]])->orderBy('sort_order')->all();
    		} else {
    			$teamList = Team::find()->select(['id', 'team_name','team_type'])->where(['in','id', $team_data])->andWhere('id NOT IN (1)')->orderBy('sort_order')->all();
    		}
    	}

    	$searchModel = new PricingSearch();
    	$team_id = 1;
    	if(!empty($teamList)){
    		foreach($teamList as $team){
        		$team_id=$team->id;
        		break;
    		}
    	}

        //$searchArray = array_merge(['PricingSearch'=>['team_id'=>$team_id,'remove'=>0]],Yii::$app->request->queryParams);
        $_REQUEST['PricingSearch']['team_id']=$team_id;
        $_REQUEST['PricingSearch']['pricing_type']=0;
        $_REQUEST['PricingSearch']['remove']=0;
		$_REQUEST['grid_id']='dynagrid-internal-price-point';
        $searchArray = array_merge(Yii::$app->request->getQueryParams(),$_REQUEST);
        $dataProvider = $searchModel->search($searchArray);

        /*IRT 67,68,86,87,258*/
        $filter_type=\app\models\User::getFilterType(['tbl_pricing.price_point','tbl_pricing_rates.pricing_rate','tbl_pricing.description','tbl_pricing.accum_cost','tbl_pricing.utbms_code_id'],['tbl_pricing','tbl_pricing_rates']);
        $config = ['accum_cost'=>['All'=>'All','1'=>'Yes','0'=>'No']];
        $config_widget_options = [
        'pricing_rate'=>[
        'filter_type'=>'range',
        'options' => ['placeholder' => 'Rate (0 - 1000)'],
		'html5Options' => ['min' => 0, 'max' => 1000, 'step'=>1],
		],
        'utbms_code_id'=>['url'=>Url::toRoute(['billing-pricelist/ajax-filter', 'pricing_type' => 0, 'team_id' => $team_id])]

        ];
        $filterWidgetOption=\app\models\User::getFilterWidgetOption($filter_type,Url::toRoute(['billing-pricelist/ajax-filter', 'team_id' => $team_id]),$config,$config_widget_options);
        /*IRT 67,68,86,87,258*/
        if(Yii::$app->request->isAjax)
        	return $this->renderAjax('internal-team-pricing',['filter_type'=>$filter_type,'filterWidgetOption'=>$filterWidgetOption,'teamList' => $teamList, 'searchModel' => $searchModel, 'dataProvider' => $dataProvider, 'team_id' => $team_id]);
        else
        	return $this->render('internal-team-pricing',['filter_type'=>$filter_type,'filterWidgetOption'=>$filterWidgetOption,'teamList' => $teamList, 'searchModel' => $searchModel, 'dataProvider' => $dataProvider, 'team_id' => $team_id]);

    }

	/**
     * Lists all Pricing models by Selecting Team from 2nd section.
     * @return mixed
     */
    public function actionLoadTeamPricing()
    {
		$params= Yii::$app->request->post();
    	$team_id = Yii::$app->request->get('team_id',0);
        $searchModel = new PricingSearch();
        $_REQUEST['PricingSearch']['team_id']=$team_id;
        $_REQUEST['PricingSearch']['remove']=0;
        $searchArray = array_merge(Yii::$app->request->getQueryParams(),$_REQUEST);
        $dataProvider = $searchModel->search($searchArray);
		 /*IRT 67,68,86,87,258*/
        $filter_type=\app\models\User::getFilterType(['tbl_pricing.price_point','tbl_pricing_rates.pricing_rate','tbl_pricing.description','tbl_pricing.accum_cost','tbl_pricing.utbms_code_id'],['tbl_pricing','tbl_pricing_rates']);
        $config = ['accum_cost'=>['All'=>'All','1'=>'Yes','0'=>'No']];
        $config_widget_options = [
        'pricing_rate'=>[
        'filter_type'=>'range',
        'options' => ['placeholder' => 'Rate (0 - 1000)'],
		'html5Options' => ['min' => 0, 'max' => 1000, 'step'=>1],
		],
        'utbms_code_id'=>['url'=>Url::toRoute(['billing-pricelist/ajax-filter', 'pricing_type' => 0, 'team_id' => $team_id])]

        ];
        $filterWidgetOption=\app\models\User::getFilterWidgetOption($filter_type,Url::toRoute(['billing-pricelist/ajax-filter', 'team_id' => $team_id]),$config,$config_widget_options);
        /*IRT 67,68,86,87,258*/
        $result=$this->renderAjax('index-team-pricing', [
			'filter_type'=>$filter_type,
			'filterWidgetOption'=>$filterWidgetOption,
			'team_id' => $team_id,
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
        ]);
        if (array_key_exists('dynagrid-internal-price-point-dynagrid', $params))
        {

            // Remove the redirection headers set in
            // DynaGrid.php line 525: Yii::$app->controller->refresh();
            Yii::$app->getResponse()->setStatusCode(200);
            Yii::$app->getResponse()->getHeaders()->remove('X-Pjax-Url');
            Yii::$app->getResponse()->getHeaders()->remove('X-Redirect');
            Yii::$app->getResponse()->getHeaders()->remove('Location');

            return Json::encode([
            'success' => true,
            ]);
        }
        else
        {
            return $result;
        }
    }

    /**
     * Creates a new Pricing model.
     * If creation is successful, user will lend on List of pricepoints by team_id.
     * @param team_id (int)
     * @return mixed
     */
    public function actionCreate()
    {
    	$team_id = Yii::$app->request->get('team_id');
    	$PricingRates = Yii::$app->request->post('PricingRatesAvail');

        $model = new Pricing();
        $model->pricing_type=0;
     	$listunitType          = ArrayHelper::map(Unit::find()->where(['remove'=>0])->orderBy('unit_name ASC')->select(['id', 'unit_name'])->all(), 'id', 'unit_name');
        $pricingUtmbsCodesData = PricingUtbmsCodes::find()->orderBy('code ASC')->all();
        $pricingUtmbsCodes     = array();
        foreach ($pricingUtmbsCodesData as $key => $val) {
            $pricingUtmbsCodes[$val->code_group . " - " . $val->code_group_name][$val->id] = $val->code . " - " . $val->code_name;
        }
        $serviceList = array();
        if(!empty($PricingRates)) {
        	if(isset($PricingRates['team_loc']) && !empty($PricingRates['team_loc'])) {
	        	$teamLocAr = $PricingRates['team_loc'];
	        	$serviceList = (new Servicetask)->getServicesByTeamandTeamloc($team_id,$teamLocAr);
        	}
        }

        if ($model->load(Yii::$app->request->post())){
        	if($model->save()){
        		$pricing_id = Yii::$app->db->getLastInsertId();
        		/* Start : Stores servicetasks associated with Pricing Point */
        		$rows = array();
        		foreach($model->service_task as $servicetask) {
        			$rows[] = array($pricing_id, $servicetask);
        		}
    			$columns = (new PricingServiceTask)->attributes();
        		unset($columns[array_search('id',$columns)]);
        		Yii::$app->db->createCommand()->batchInsert(PricingServiceTask::tableName(), $columns, $rows)->execute();
        		/* End : Stores servicetasks associated with Pricing Point */

        		/* Start : Stores Pricing Rates */
        		$rows = array();
        		$teamLocAr = $PricingRates['team_loc'];
        		$ratesCount = count($teamLocAr);
        		$i=0;
        		for($i=0;$i<$ratesCount;$i++) {
        			$team_loc = $PricingRates['team_loc'][$i];
        			$rate_type = $PricingRates['rate_type'][$i];
        			$rate_amount = $PricingRates['rate_amount'][$i];
        			$cost_amount = $PricingRates['cost_amount'][$i];
        			$tier_form = $PricingRates['tier_from'][$i];
        			$tier_to = $PricingRates['tier_to'][$i];
        			//$rows[] = array($pricing_id, $team_loc, $rate_type, $rate_amount, $cost_amount, $tier_form, $tier_to);
        			$rows[] = array('pricing_id'=>$pricing_id, 'team_loc'=>$team_loc, 'rate_type'=>$rate_type, 'rate_amount'=>$rate_amount, 'cost_amount'=>$cost_amount, 'tier_from'=>$tier_form, 'tier_to'=>$tier_to);
        		}
    			$columns = (new PricingRates)->attributes();
        		unset($columns[array_search('id',$columns)]);
        		Yii::$app->db->createCommand()->batchInsert(PricingRates::tableName(), $columns, $rows)->execute();
        		/* End : Stores Pricing Rates */
	        	return 'OK';

        	} else {

        		return $this->renderAjax('_form-team-pricing', [
	            	'team_id'=> $team_id,
	                'model' => $model,
	            	'listunitType' => $listunitType,
		            'pricingUtmbsCodes' => $pricingUtmbsCodes,
        			'serviceList' => $serviceList
	            ]);
        	}

        } else {
			$pricing_length = (new User)->getTableFieldLimit('tbl_pricing');
            return $this->renderAjax('create-team-pricing', [
            	'team_id'=> $team_id,
                'model' => $model,
            	'listunitType' => $listunitType,
	            'pricingUtmbsCodes' => $pricingUtmbsCodes,
            	'serviceList' => $serviceList,
            	'pricing_length' => $pricing_length
            ]);

        }
    }

    /**
     * Load Pricing Rate Form
     * @param pricing_id (int)
     * @param type (string) [team/client/case]
     * @param team_id (int)
     * @param id (int) [client_id,case_id]
     * @param data (int)
     * @return mixed
     */
    public function actionLoadPricingRate()
    {
    	$pricing_id = Yii::$app->request->get('pricing_id',0);
    	$team_id = Yii::$app->request->get('team_id',0);
    	$id = Yii::$app->request->get('id',0);
    	$type = Yii::$app->request->get('type','team');
    	$data = Yii::$app->request->post('data','');
    	$teamLocs = TeamLocs::find()->select(['team_loc']);
    	if($team_id != 0)
    		$teamLocs->where(['team_id'=>$team_id]);
    	else
    		$teamLocs->where('remove=0 OR team_id=1');

    	$teamLocation = ArrayHelper::map(TeamlocationMaster::find()->where(['in','id',$teamLocs])->select(['id','team_location_name'])->orderBy(['team_location_name'=>'ASC'])->all(), 'id','team_location_name');
    	$model = new PricingRates;
    	if($pricing_id!='' && $pricing_id!=0){
    		if($type == 'team'){
				$pricingRate = PricingRates::find()->where(['pricing_id'=>$pricing_id])->all();
    		} else {
    			if($type == 'client') {
		    		$pricingRate = PricingClientsRates::find()->innerJoinWith([
		    			'pricingClients' => function(\yii\db\ActiveQuery $query) use($id,$pricing_id){
		    				$query->where(['pricing_id'=>$pricing_id, 'client_id' => $id]);
		    			}
		    		])->all();
	    			if(!empty($pricingRate)){
			    		$data = $this->renderAjax('pricing-rate-list', [
				        	'rateType' => ArrayHelper::map($pricingRate,'rate_type','rate_type'),
				        	'pricingRates' => $pricingRate
						]);
		    		}
		    	}
		    	if($type == 'case') {
		    		$pricingRate = PricingClientscasesRates::find()->innerJoinWith([
		    			'pricingClientscases' => function(\yii\db\ActiveQuery $query) use($id,$pricing_id){
		    				$query->where(['pricing_id'=>$pricing_id, 'client_case_id' => $id]);
		    			}
		    		])->all();
			    	if(!empty($pricingRate)){
			    		$data = $this->renderAjax('pricing-rate-list', [
				        	'rateType' => ArrayHelper::map($pricingRate,'rate_type','rate_type'),
				        	'pricingRates' => $pricingRate
						]);
		    		}
		    	}

    		}
    	}

    	if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())){
    		$validateResult = ActiveForm::validate($model);
    		unset($validateResult['pricingrates-pricing_id']);
    		if(count($validateResult) > 0) {
	    		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
	    		return $validateResult;
    		}
    		return 'OK';
    	}
    	$pricing_rates_length = (new User)->getTableFieldLimit('tbl_pricing_rates');
    	return $this->renderAjax('_form-pricing-rate', [
            'pricing_id'=> $pricing_id,
            'model' => $model,
    		'teamLocation' => $teamLocation,
    		'pricingRate' => $pricingRate,
    		'data' => $data,
    		'pricing_rates_length' => $pricing_rates_length
		]);
    }

    /**
     * Load Teamservices - Servicetask By Team & Locations selected for pricing rates
     * @param team_id int
     */
    public function actionLoadServices()
    {
    	$team_id = Yii::$app->request->get('team_id',0);
    	$data = Yii::$app->request->post('PricingRatesAvail',array());
    	$serviceList = array();
    	if(isset($data['team_loc']) && !empty($data['team_loc'])){
    		$teamLocAr = $data['team_loc'];
    		$serviceList = (new Servicetask)->getServicesByTeamandTeamloc($team_id,$teamLocAr);
    	}

    	return $this->renderAjax('_load-services', [
            'serviceList'=> $serviceList,
		]);
    }

    /**
     * Validates the Pricing Form.
     * If success then return OK else Array of Errors in JSON form.
     */
    public function actionValidatePricing()
    {
    	$model = new Pricing;
    	if ($model->load(Yii::$app->request->post())){
    		$validateResult = ActiveForm::validate($model);
    		unset($validateResult['pricingrates-pricing_id']);
    		if(count($validateResult) > 0) {
	    		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
	    		return $validateResult;
    		}
    	}
    	Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    	return 'OK';
    }

    /**
     * Updates an existing Pricing model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $pricing_id
     * @param integer $team_id
     * @return mixed
     */
    public function actionUpdate()
    {
    	$team_id = Yii::$app->request->get('team_id');
    	$pricing_id = Yii::$app->request->get('pricing_id');

        $model = $this->findModel($pricing_id);

     	$listunitType          = ArrayHelper::map(Unit::find()->where(['remove'=>0])->orderBy('unit_name ASC')->select(['id', 'unit_name'])->all(), 'id', 'unit_name');
        $pricingUtmbsCodesData = PricingUtbmsCodes::find()->orderBy('code ASC')->all();
        $pricingUtmbsCodes     = array();
        foreach ($pricingUtmbsCodesData as $key => $val) {
            $pricingUtmbsCodes[$val->code_group . " - " . $val->code_group_name][$val->id] = $val->code . " - " . $val->code_name;
        }

        if ($model->load(Yii::$app->request->post())){

        	$PricingRates = Yii::$app->request->post('PricingRatesAvail');

        	if($model->save()){
        		/* Start : Stores servicetasks associated with Pricing Point */
        		$servicetask_ids = implode(',',$model->service_task);
        		PricingServiceTask::deleteAll("pricing_id=$pricing_id AND servicetask_id NOT IN ($servicetask_ids)");
        		$servicetasks = ArrayHelper::map(PricingServiceTask::find()->where(['pricing_id'=>$pricing_id])->select('servicetask_id')->all(),'servicetask_id','servicetask_id');
        		$rows = array();
        		$newservicetasks = $model->service_task;
        		foreach($model->service_task as $servicetask) {
        			if(!in_array($servicetask, $servicetasks))
        				$rows[] = array($pricing_id, $servicetask);
        		}
        		if(!empty($rows)){
	    			$columns = (new PricingServiceTask)->attributes();
	        		unset($columns[array_search('id',$columns)]);
	        		Yii::$app->db->createCommand()->batchInsert(PricingServiceTask::tableName(), $columns, $rows)->execute();
        		}
        		/* End : Stores servicetasks associated with Pricing Point */

        		/* Start : Stores Pricing Rates */
        		PricingRates::deleteAll("pricing_id=$pricing_id");
        		$rows = array();
        		$teamLocAr = $PricingRates['team_loc'];
        		$ratesCount = count($teamLocAr);
        		$i=0;
        		for($i=0;$i<$ratesCount;$i++)
        		{
        			$team_loc = $PricingRates['team_loc'][$i];
        			$rate_type = $PricingRates['rate_type'][$i];
        			$rate_amount = $PricingRates['rate_amount'][$i];
        			$cost_amount = $PricingRates['cost_amount'][$i];
        			$tier_form = $PricingRates['tier_from'][$i];
        			$tier_to = $PricingRates['tier_to'][$i];
        			//$rows[] = array($pricing_id, $team_loc, $rate_type, $rate_amount, $cost_amount, $tier_form, $tier_to);
					$rows[] = array('pricing_id'=>$pricing_id, 'team_loc'=>$team_loc, 'rate_type'=>$rate_type, 'rate_amount'=>$rate_amount, 'cost_amount'=>$cost_amount, 'tier_from'=>$tier_form, 'tier_to'=>$tier_to);
        		}
    			$columns = (new PricingRates)->attributes();
        		unset($columns[array_search('id',$columns)]);
        		Yii::$app->db->createCommand()->batchInsert(PricingRates::tableName(), $columns, $rows)->execute();
        		/* End : Stores Pricing Rates */
				return 'OK';

        	} else {

		        $serviceList = array();
		        if(!empty($PricingRates)) {
		        	if(isset($PricingRates['team_loc']) && !empty($PricingRates['team_loc'])) {
			        	$teamLocAr = $PricingRates['team_loc'];
			        	$serviceList = (new Servicetask)->getServicesByTeamandTeamloc($team_id,$teamLocAr);
		        	}
		        }
				$pricing_length = (new User)->getTableFieldLimit('tbl_pricing');
        		return $this->renderAjax('_form-team-pricing', [
	            	'team_id'=> $team_id,
	                'model' => $model,
	            	'listunitType' => $listunitType,
		            'pricingUtmbsCodes' => $pricingUtmbsCodes,
        			'serviceList' => $serviceList,
        			'pricing_type' => 0,
        			'sourceDiv'=>'teampricing_div',
        			'pricing_length' => $pricing_length
	            ]);
        	}

        } else {

	        $serviceList = array();
	        $pricingRates = $model->pricingRates;
	        $model->pricing_rate = $this->renderAjax('pricing-rate-list', [
	        	'rateType' => ArrayHelper::map($pricingRates,'rate_type','rate_type'),
                'pricingRates' => $pricingRates
            ]);
        	$teamLocs = ArrayHelper::map($pricingRates,'team_loc','team_loc');
        	if(isset($teamLocs) && !empty($teamLocs)) {
	        	$serviceList = (new Servicetask)->getServicesByTeamandTeamloc($team_id,$teamLocs);
        	}
        	//echo "<pre>",print_r($serviceList),"</pre>";
	        $model->service_task = ArrayHelper::map($model->pricingServiceTask,'servicetask_id','servicetask_id');
			$pricing_length = (new User)->getTableFieldLimit('tbl_pricing');
            return $this->renderAjax('update-team-pricing', [
            	'team_id'=> $team_id,
                'model' => $model,
            	'listunitType' => $listunitType,
	            'pricingUtmbsCodes' => $pricingUtmbsCodes,
            	'serviceList' => $serviceList,
            	'pricing_length' => $pricing_length
            ]);

        }
    }

    /**
     * Check an existing Pricing model with updated servicetask & Location.
     * If found Billable Item Left for removed Location and Servicetask, then browser will prompt appropriate error.
     * @param integer $pricing_id
     * @param integer $team_id
     * @return mixed
     */
    public function actionChklocandservicetask()
    {
    	$team_id = Yii::$app->request->get('team_id');
    	$pricing_id = Yii::$app->request->get('pricing_id');
    	$post_data = Yii::$app->request->post();

    	$serviectasks = isset($post_data['Pricing']['service_task']) && !empty($post_data['Pricing']['service_task'])?$post_data['Pricing']['service_task']:array();
		$existservicetask = ArrayHelper::map(PricingServiceTask::find()->where(['pricing_id'=>$pricing_id])->select(['servicetask_id'])->all(),'servicetask_id','servicetask_id');
		$variation = array_diff($existservicetask,$serviectasks);

		$invoiceleft = 0;
	    $accumalatedleft = 0;

	    if($post_data['Pricing']['pricing_type'] == 1){
			$teams = isset($post_data['Pricing']['display_teams']) && !empty($post_data['Pricing']['display_teams'])?$post_data['Pricing']['display_teams']:array();
			$existteam = ArrayHelper::map(PricingDisplayTeams::find()->where(['pricing_id'=>$pricing_id])->select(['team_id'])->all(),'team_id','team_id');
			$variation = array_diff($existservicetask,$serviectasks);
			if(!empty($variation)){
			$invoiceleft = TasksUnitsBilling::find()
				->innerJoinWith([
					'tasksUnits' => function(\yii\db\ActiveQuery $taskUnits) use($variation){
						$taskUnits->where(['in','team_id',$variation])->innerJoinWith([
						'taskInstruct'=> function(\yii\db\ActiveQuery $instruct){
										$instruct->where(['isactive'=>1])->innerJoinWith([
										'tasks' => function(\yii\db\ActiveQuery $tasks){
											$tasks->innerJoinWith([
												'clientCase' => function(\yii\db\ActiveQuery $clientCase){
													$clientCase->where(['is_close'=>0]);
												}
											]);
										}]);
									}
						]);
					}
				])
				->where("(invoiced IS NULL OR invoiced = ''	OR invoiced =0)")
				->andWhere(['pricing_id' => $pricing_id])
				->count();
			}
			if ($invoiceleft > 0) {
	        	return "billableitemsleftforteam";
	        }
	        return 'OK';
		}

		if(!empty($variation)){
			$invoiceleft = TasksUnitsBilling::find()
				->innerJoinWith([
					'tasksUnits' => function(\yii\db\ActiveQuery $taskUnits) use($variation){
						$taskUnits->where(['in','servicetask_id',$variation])->innerJoinWith([
						'taskInstruct'=> function(\yii\db\ActiveQuery $instruct){
										$instruct->where(['isactive'=>1])->innerJoinWith([
										'tasks' => function(\yii\db\ActiveQuery $tasks){
											$tasks->innerJoinWith([
												'clientCase' => function(\yii\db\ActiveQuery $clientCase){
													$clientCase->where(['is_close'=>0]);
												}
											]);
										}]);
									}
						]);
					}])
				->where("(invoiced IS NULL OR invoiced = ''	OR invoiced =0)")
				->andWhere(['pricing_id' => $pricing_id])
				->count();

			$accumalatedleft = TasksUnitsBilling::find()
				->innerJoinWith([
					'tasksUnits' => function(\yii\db\ActiveQuery $taskUnits) use($variation){
						$taskUnits->where(['in','servicetask_id',$variation])->innerJoinWith([
						'taskInstruct'=> function(\yii\db\ActiveQuery $instruct){
										$instruct->where(['isactive'=>1])->innerJoinWith([
										'tasks' => function(\yii\db\ActiveQuery $tasks){
											$tasks->innerJoinWith([
												'clientCase' => function(\yii\db\ActiveQuery $clientCase){
													$clientCase->where(['is_close'=>0]);
												}
											]);
										}]);
									}
						]);
					},
					'pricing' => function(\yii\db\ActiveQuery $pricing){
						$pricing->where(['accum_cost'=>1]);
					}
				])
				->where("(invoiced IS NULL OR invoiced = ''	OR invoiced =0)")
				->andWhere(['pricing_id' => $pricing_id])
				->count();

        	if($invoiceleft > 0 && $invoiceleft == $accumalatedleft) {
				return "accumalateditemsleft";
        	} else if ($invoiceleft > 0) {
	        	return "billableitemsleft";
	        }
		}

		$newrate = isset($post_data['PricingRatesAvail']['team_loc']) && !empty($post_data['PricingRatesAvail']['team_loc'])?$post_data['PricingRatesAvail']['team_loc']:array();
		$existrate = ArrayHelper::map(PricingRates::find()->where(['pricing_id'=>$pricing_id])->select(['team_loc'])->all(),'team_loc','team_loc');
		$variationLocRate = array_diff($existrate,$newrate);

		$invoiceleft = 0;
	    $accumalatedleft = 0;
		if(!empty($variationLocRate)){
			$invoiceleft = TasksUnitsBilling::find()
				->innerJoinWith([
					'tasksUnits' => function(\yii\db\ActiveQuery $taskUnits) use($variationLocRate){
						$taskUnits->where(['in','team_loc',$variationLocRate])->innerJoinWith([
						'taskInstruct'=> function(\yii\db\ActiveQuery $instruct){
										$instruct->where(['isactive'=>1])->innerJoinWith([
										'tasks' => function(\yii\db\ActiveQuery $tasks){
											$tasks->innerJoinWith([
												'clientCase' => function(\yii\db\ActiveQuery $clientCase){
													$clientCase->where(['is_close'=>0]);
												}
											]);
										}]);
									}
						]);
					},
				])
				->where("(invoiced IS NULL OR invoiced = ''	OR invoiced =0)")
				->andWhere(['pricing_id' => $pricing_id])
				->count();

			$accumalatedleft = TasksUnitsBilling::find()
				->innerJoinWith([
					'tasksUnits' => function(\yii\db\ActiveQuery $taskUnits) use($variationLocRate){
						$taskUnits->where(['in','team_loc',$variationLocRate])->innerJoinWith([
						'taskInstruct'=> function(\yii\db\ActiveQuery $instruct){
										$instruct->where(['isactive'=>1])->innerJoinWith([
										'tasks' => function(\yii\db\ActiveQuery $tasks){
											$tasks->innerJoinWith([
												'clientCase' => function(\yii\db\ActiveQuery $clientCase){
													$clientCase->where(['is_close'=>0]);
												}
											]);
										}]);
									}
						]);
					},
					'pricing' => function(\yii\db\ActiveQuery $pricing){
						$pricing->where(['accum_cost'=>1]);
					}
				])
				->where("(invoiced IS NULL OR invoiced = ''	OR invoiced =0)")
				->andWhere(['pricing_id' => $pricing_id])
				->count();

			if($invoiceleft > 0 && $invoiceleft == $accumalatedleft) {
				return "accumalateditemsleftforloc";
        	} else if ($invoiceleft > 0) {
	        	return "billableitemsleftforloc";
	        }
		}
    	return 'OK';
    }

	/**
     * Check an existing Pricing model for remaining billing items.
     * If found Billable Item Left for pricepoint, then browser will prompt appropriate error.
     * @param integer $pricing_id
     * @return mixed
     */
    public function actionChkcanremovepricepoint()
    {
    	$pricing_id = Yii::$app->request->post('pricing_id');

		$invoiceleft = 0;
	    $accumalatedleft = 0;

		if(!empty($pricing_id)){

			$invoiceleft = TasksUnitsBilling::find()
				->innerJoinWith([
					'tasksUnits'=>function(\yii\db\ActiveQuery $tasks){
						$tasks->innerJoinWith(['taskInstruct'=>function(\yii\db\ActiveQuery $tasks){
						$tasks->innerJoinWith(['tasks' => function(\yii\db\ActiveQuery $tasks){
						$tasks->innerJoinWith([
							'clientCase' => function(\yii\db\ActiveQuery $clientCase){
								$clientCase->where(['is_close'=>0]);
							}
						]);
					}]);
						}]);
					},
				])
				->where("(invoiced IS NULL OR invoiced = ''	OR invoiced =0)")
				->andWhere(['in','pricing_id',$pricing_id])
				->count();

			$accumalatedleft = TasksUnitsBilling::find()
				->innerJoinWith([
					'tasksUnits'=>function(\yii\db\ActiveQuery $tasks){
							$tasks->innerJoinWith(['taskInstruct'=>function(\yii\db\ActiveQuery $tasks){
								$tasks->innerJoinWith(['tasks' => function(\yii\db\ActiveQuery $tasks){
									$tasks->innerJoinWith([
										'clientCase' => function(\yii\db\ActiveQuery $clientCase){
											$clientCase->where(['is_close'=>0]);
										}
									]);
								}]);
							}
							]);
					},
					'pricing' => function(\yii\db\ActiveQuery $pricing){
						$pricing->where(['accum_cost'=>1]);
					}
				])
				->where("(invoiced IS NULL OR invoiced = ''	OR invoiced =0)")
				->andWhere(['in','pricing_id',$pricing_id])
				->count();

        	if($invoiceleft > 0 && $invoiceleft == $accumalatedleft) {
				return "accumalateditemsleft";
        	} else if ($invoiceleft > 0) {
	        	return "billableitemsleft";
	        }
		}

    	return 'OK';
    }

    /**
     * Deletes an existing Pricing model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $pricing_id
     * @return mixed
     */
    public function actionDeletePricepoint()
    {
    	$pricing_id = Yii::$app->request->post('pricing_id');
    	if(!empty($pricing_id)){
			Pricing::updateAll(['remove'=>1],['in','id',$pricing_id]);
	        return 'OK';
    	}
    	return;
    }

    /**
     * It will load Shared Pricing View.
     * 2nd section will load shared pricepoint from Pricing Model.
     */
    public function actionInternalSharedPricing()
    {
    	$this->layout = 'billing';
    	$searchModel = new PricingSearch();
    	$_REQUEST['PricingSearch']['pricing_type']=1;
        $_REQUEST['PricingSearch']['remove']=0;
		$_REQUEST['grid_id']='shared-price-point-transaction';
        $searchArray = array_merge(Yii::$app->request->getQueryParams(),$_REQUEST);

        $dataProvider = $searchModel->search($searchArray);
		//$data=$dataProvider->getModels();
		//echo "<pre>",print_r($data),"</prE>";die;
        /*IRT 67,68,86,87,258*/
        $filter_type=\app\models\User::getFilterType(['tbl_pricing.price_point','tbl_pricing_rates.pricing_rate','tbl_pricing.description','tbl_pricing.utbms_code_id'],['tbl_pricing','tbl_pricing_rates']);
        $config = ['accum_cost'=>['All'=>'All','1'=>'Yes','0'=>'No']];
        $config_widget_options = [
        'pricing_rate'=>[
            'filter_type'=>'range',
            'options' => ['placeholder' => 'Rate (0 - 1000)'],
		'html5Options' => ['min' => 0, 'max' => 1000, 'step'=>1],
            ],
        ];
        $filterWidgetOption=\app\models\User::getFilterWidgetOption($filter_type,Url::toRoute(['billing-pricelist/ajax-filter', 'pricing_type' => 1]),$config,$config_widget_options);
        /*IRT 67,68,86,87,258*/
        if(Yii::$app->request->isAjax)
       		return $this->renderAjax('internal-shared-pricing',['filter_type'=>$filter_type,'filterWidgetOption'=>$filterWidgetOption,'searchModel' => $searchModel, 'dataProvider' => $dataProvider, 'team_id' => $team_id]);
       	else
       		return $this->render('internal-shared-pricing',['filter_type'=>$filter_type,'filterWidgetOption'=>$filterWidgetOption,'searchModel' => $searchModel, 'dataProvider' => $dataProvider, 'team_id' => $team_id]);
    }

    /**
     * Finds the Pricing model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Pricing the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Pricing::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

	/**
     * Creates a new Pricing model for Shared Pricing.
     * If creation is successful, user will lend on List of Shared pricepoints.
     * @return mixed
     */
    public function actionCreateSharedPricing()
    {
    	$roleId = Yii::$app->user->identity->role_id;
    	$userId = Yii::$app->user->identity->id;
    	$roleTypes = explode(",",Yii::$app->user->identity->role->role_type);
    	$sqlbillable_team_ids = "SELECT tbl_teamservice.teamid FROM tbl_servicetask inner join tbl_teamservice on tbl_teamservice.id=tbl_servicetask.teamservice_id and tbl_servicetask.billable_item IN (1,2) group by tbl_teamservice.teamid";
    	if ($roleId == 0) {
			$teamList = ArrayHelper::map(Team::find()->select(['id', 'team_name','team_type'])->where('id IN ('.$sqlbillable_team_ids.')')->orderBy('sort_order')->all(),'id','team_name');
    	} else {
			$team_data = ProjectSecurity::find()->select('team_id')->where('team_id !=0 AND  team_id IN ('.$sqlbillable_team_ids.') AND user_id='.$userId)->groupBy('team_id');
    		if(in_array(1, $roleTypes)){
    			$teamList = ArrayHelper::map(Team::find()->select(['id', 'team_name','team_type'])->where(['or','id=1',['in','id', $team_data]])->orderBy('sort_order')->all(),'id','team_name');
    		} else {
    			$teamList = ArrayHelper::map(Team::find()->select(['id', 'team_name','team_type'])->where(['in','id', $team_data])->andWhere('id NOT IN (1)')->orderBy('sort_order')->all(),'id','team_name');
    		}
    	}
    	$model = new Pricing();
        $model->pricing_type=1;
     	$listunitType          = ArrayHelper::map(Unit::find()->where(['remove'=>0])->orderBy('unit_name ASC')->select(['id', 'unit_name'])->all(), 'id', 'unit_name');
        $pricingUtmbsCodesData = PricingUtbmsCodes::find()->orderBy('code ASC')->all();
        $pricingUtmbsCodes     = array();
        foreach ($pricingUtmbsCodesData as $key => $val) {
            $pricingUtmbsCodes[$val->code_group . " - " . $val->code_group_name][$val->id] = $val->code . " - " . $val->code_name;
        }
        $params = Yii::$app->request->post('Pricing');
        if ($model->load(Yii::$app->request->post())){
        	if($model->save()){
        		$pricing_id = Yii::$app->db->getLastInsertId();
        		/* Start : Stores Display Teams associated with Pricing Point */
        		if(!empty($model->display_teams)){
	        		$rows = array();
	        		foreach($model->display_teams as $team) {
	        			$rows[] = array($pricing_id, $team);
	        		}
	    			$columns = (new PricingDisplayTeams)->attributes();
	        		unset($columns[array_search('id',$columns)]);
	        		Yii::$app->db->createCommand()->batchInsert(PricingDisplayTeams::tableName(), $columns, $rows)->execute();
        		}
        		/* End : Stores Display Teams associated with Pricing Point */
        		/* Start : Stores Pricing Rates */
        		if($params['pricing_rate']!=''){
        			$rows = array();
        			$rows[] = array('pricing_id'=>$pricing_id, 'team_loc'=>NULL, 'rate_type'=>0, 'rate_amount'=>$params['pricing_rate'], 'cost_amount'=>0, 'tier_from'=>0, 'tier_to'=>0);
	        		//$rows[] = array($pricing_id, NULL, 0, $params['pricing_rate'], 0, 0, 0);
	    			$columns = (new PricingRates)->attributes();
	        		unset($columns[array_search('id',$columns)]);
	        		Yii::$app->db->createCommand()->batchInsert(PricingRates::tableName(), $columns, $rows)->execute();
        		}
        		/* End : Stores Pricing Rates */
	        	return 'OK';
        	} else {
				//echo "<pre>",print_r($model->errors),"</pre>";
				$pricing_length = (new User)->getTableFieldLimit('tbl_pricing');
        		return $this->renderAjax('_form-team-pricing', [
	                'model' => $model,
	            	'listunitType' => $listunitType,
		            'pricingUtmbsCodes' => $pricingUtmbsCodes,
        			'teamList' => $teamList,
        			'sourceDiv'=>'sharedpricing_div',
        			'pricing_length' => $pricing_length
	            ]);
        	}
        } else {
        	//$model->display_teams = array_keys($teamList);
        	$model->display_teams_type = 1;
        	$pricing_length = (new User)->getTableFieldLimit('tbl_pricing');
            return $this->renderAjax('create-shared-pricing', [
                'model' => $model,
            	'listunitType' => $listunitType,
	            'pricingUtmbsCodes' => $pricingUtmbsCodes,
            	'teamList' => $teamList,
            	'pricing_length' => $pricing_length
            ]);
        }
    }

    /**
     * Updates an existing Pricing model for  Shared Pricing.
     * If update is successful, user will lend on List of Shared pricepoints.
     * @param integer $pricing_id
     * @return mixed
     */
    public function actionUpdateSharedPricing()
    {
		/*echo '<pre>';
		print_r(Yii::$app->request->post());
		die;*/
   		$pricing_id = Yii::$app->request->get('pricing_id');

        $model = $this->findModel($pricing_id);

    	$roleId = Yii::$app->user->identity->role_id;
    	$userId = Yii::$app->user->identity->id;
    	$roleTypes = explode(",",Yii::$app->user->identity->role->role_type);

    	if ($roleId == 0) {
    		$teamList = ArrayHelper::map(Team::find()->select(['id', 'team_name','team_type'])->orderBy('sort_order')->all(),'id','team_name');
    	} else {
    		$team_data = ProjectSecurity::find()->select('team_id')->where('team_id !=0 AND user_id='.$userId)->groupBy('team_id');
    		if(in_array(1, $roleTypes)){
    			$teamList = ArrayHelper::map(Team::find()->select(['id', 'team_name','team_type'])->where(['or','id=1',['in','id', $team_data]])->orderBy('sort_order')->all(),'id','team_name');
    		} else {
    			$teamList = ArrayHelper::map(Team::find()->select(['id', 'team_name','team_type'])->where(['in','id', $team_data])->andWhere('id NOT IN (1)')->orderBy('sort_order')->all(),'id','team_name');
    		}
    	}

     	$listunitType          = ArrayHelper::map(Unit::find()->where(['remove'=>0])->orderBy('unit_name ASC')->select(['id', 'unit_name'])->all(), 'id', 'unit_name');
        $pricingUtmbsCodesData = PricingUtbmsCodes::find()->orderBy('code ASC')->all();
        $pricingUtmbsCodes     = array();

        foreach ($pricingUtmbsCodesData as $key => $val) {
            $pricingUtmbsCodes[$val->code_group . " - " . $val->code_group_name][$val->id] = $val->code . " - " . $val->code_name;
        }

        $params = Yii::$app->request->post('Pricing');

        if ($model->load(Yii::$app->request->post())){

        	if($model->save()){

        		/* Start : Stores Display Teams associated with Pricing Point */
        		PricingDisplayTeams::deleteAll("pricing_id=$pricing_id");
        		if(!empty($model->display_teams) && $model->display_teams_type == 2){
	        		$rows = array();
	        		foreach($model->display_teams as $team) {
	        			$rows[] = array($pricing_id, $team);
	        		}
	    			$columns = (new PricingDisplayTeams)->attributes();
	        		unset($columns[array_search('id',$columns)]);
	        		Yii::$app->db->createCommand()->batchInsert(PricingDisplayTeams::tableName(), $columns, $rows)->execute();
        		}
        		/* End : Stores Display Teams associated with Pricing Point */

        		/* Start : Stores Pricing Rates */
        		PricingRates::deleteAll("pricing_id=$pricing_id");
        		if($params['pricing_rate']!=''){
        			$rows = array();
	        		$rows[] = array('pricing_id'=>$pricing_id, 'team_loc'=>NULL, 'rate_type'=>0, 'rate_amount'=>$params['pricing_rate'], 'cost_amount'=>0, 'tier_from'=>0, 'tier_to'=>0);
	    			$columns = (new PricingRates)->attributes();
	        		unset($columns[array_search('id',$columns)]);
	        		Yii::$app->db->createCommand()->batchInsert(PricingRates::tableName(), $columns, $rows)->execute();
        		}
        		/* End : Stores Pricing Rates */
	        	return 'OK';

        	} else {
				//echo "<pre>",print_r($model->errors),"</pre>";
        		return $this->renderAjax('_form-team-pricing', [
	                'model' => $model,
	            	'listunitType' => $listunitType,
		            'pricingUtmbsCodes' => $pricingUtmbsCodes,
        			'teamList' => $teamList,
        			'sourceDiv'=>'sharedpricing_div'
	            ]);
        	}

        } else {
        	//$model->display_teams = array_keys($teamList);
        	//$model->display_teams_type = 1;

	        $rateAmount = ArrayHelper::map($model->pricingRates,'rate_amount','rate_amount');
	        $model->pricing_rate = array_pop($rateAmount);
	        $model->display_teams = ArrayHelper::map($model->pricingDisplayTeams,'team_id','team_id');

			$pricing_length = (new User)->getTableFieldLimit('tbl_pricing');
            return $this->renderAjax('update-shared-pricing', [
                'model' => $model,
            	'listunitType' => $listunitType,
	            'pricingUtmbsCodes' => $pricingUtmbsCodes,
            	'teamList' => $teamList,
            	'pricing_length' => $pricing_length,
            	'sourceDiv'=>'sharedpricing_div'
            ]);

        }
    }
    public function actionClientjsonlist(){
		\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		$params = Yii::$app->request->queryParams;
		$roleId = Yii::$app->user->identity->role_id;
    	$userId = Yii::$app->user->identity->id;
		$client_id = Yii::$app->request->get('client_id',0);
		$page=isset($params['page'])?$params['page']:1;
		$limit=50;
		$mssql="OFFSET 0 ROWS FETCH NEXT $limit ROWS ONLY;";
        $mysql="LIMIT $limit OFFSET 0";
		$offset=( ( $page - 1 ) * $limit );
		$out['items'] = array();
		$out['total_count']=0;
		$out['pagination']['more']=false;
		$q=$params['q'];
		$filterWhere="";
		if(trim($q)!=""){
			$filterWhere=" AND client_name LIKE '%$q%' ";
		}
        if(Yii::$app->db->driverName == 'mysql') {
        	$mysql="LIMIT $limit OFFSET $offset";
            $limit_sql=$mysql;
        } else {
        	$mssql="OFFSET $offset ROWS FETCH NEXT $limit ROWS ONLY;";
            $limit_sql=$mssql;
        }
		if ($roleId != 0) {
			$sql_count="SELECT COUNT(DISTINCT tbl_client.id) FROM tbl_client INNER JOIN tbl_client_case ON tbl_client.id = tbl_client_case.client_id WHERE (tbl_client.id IN (SELECT client_id FROM tbl_project_security WHERE (user_id=$userId) AND (team_id=0))) AND (is_close=0) $filterWhere ";
			$sql="SELECT DISTINCT tbl_client.id, client_name FROM tbl_client INNER JOIN tbl_client_case ON tbl_client.id = tbl_client_case.client_id WHERE (tbl_client.id IN (SELECT client_id FROM tbl_project_security WHERE (user_id=$userId) AND (team_id=0))) AND (is_close=0) $filterWhere ORDER BY client_name $limit_sql";
			$clientList = Yii::$app->db->createCommand($sql)->queryAll();
			$out['total_count'] = Yii::$app->db->createCommand($sql_count)->queryScalar();
			if(!empty($clientList)){
				if($page == 1) {$out['items'][] = ['id' => 0, 'text' => 'Select Client Preferred Pricing'];}
				foreach($clientList as $client){
					$val=Html::decode($client['client_name']);
					$out['items'][] = ['id' => $client['id'], 'text' => $val];
				}
			}
		} else {
			$sql_count="SELECT COUNT(DISTINCT tbl_client.id) FROM tbl_client INNER JOIN tbl_client_case ON tbl_client.id = tbl_client_case.client_id WHERE  is_close=0 $filterWhere ";
			$sql="SELECT DISTINCT tbl_client.id, client_name FROM tbl_client INNER JOIN tbl_client_case ON tbl_client.id = tbl_client_case.client_id WHERE  is_close=0 $filterWhere ORDER BY client_name  $limit_sql";
			$clientList = Yii::$app->db->createCommand($sql)->queryAll();
			$out['total_count'] = Yii::$app->db->createCommand($sql_count)->queryScalar();
			if(!empty($clientList)){
				if($page == 1) {$out['items'][] = ['id' => 0, 'text' => 'Select Client Preferred Pricing'];}
				foreach($clientList as $client){
					$val=Html::decode($client['client_name']);
					$out['items'][] = ['id' => $client['id'], 'text' => $val];
				}
			}
		}
		if($out['total_count'] > 0 && ($page * $limit) < $out['total_count']){
			$out['pagination']['more']=true;
		}
		return $out;
	}
	public function actionClientcasejsonlist(){
		\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		$params = Yii::$app->request->queryParams;
		$roleId = Yii::$app->user->identity->role_id;
    	$userId = Yii::$app->user->identity->id;
		$client_case_id = Yii::$app->request->get('client_case_id',0);
		$page=isset($params['page'])?$params['page']:1;
		$limit=50;
		$mssql="OFFSET 0 ROWS FETCH NEXT $limit ROWS ONLY;";
        $mysql="LIMIT $limit OFFSET 0";
		$offset=( ( $page - 1 ) * $limit );
		$out['items'] = array();
		$out['total_count']=0;
		$out['pagination']['more']=false;
		$q=$params['q'];
		$filterWhere="";
		if(trim($q)!=""){
			$filterWhere=" AND (CONCAT(case_name,' - ', tbl_client.client_name) LIKE '%$q%') ";
		}
        if(Yii::$app->db->driverName == 'mysql') {
        	$mysql="LIMIT $limit OFFSET $offset";
            $limit_sql=$mysql;
        } else {
        	$mssql="OFFSET $offset ROWS FETCH NEXT $limit ROWS ONLY;";
            $limit_sql=$mssql;
        }
		if ($roleId != 0) {
			$sql_count="SELECT count(DISTINCT tbl_client_case.id) FROM tbl_client_case INNER JOIN tbl_client ON tbl_client_case.client_id = tbl_client.id WHERE (tbl_client_case.id IN (SELECT DISTINCT client_case_id FROM tbl_project_security WHERE client_id!=0 AND  client_case_id !=0 AND (user_id=$userId) AND (team_id=0))) AND (is_close=0) $filterWhere ";
			$sql="SELECT DISTINCT tbl_client_case.id, tbl_client.client_name, case_name FROM tbl_client_case INNER JOIN tbl_client ON tbl_client_case.client_id = tbl_client.id WHERE (tbl_client_case.id IN (SELECT DISTINCT client_case_id FROM tbl_project_security WHERE client_id!=0 AND  client_case_id !=0 AND (user_id=$userId) AND (team_id=0))) AND (is_close=0) $filterWhere ORDER BY client_name, case_name $limit_sql";
			$caseList = Yii::$app->db->createCommand($sql)->queryAll();
			$out['total_count'] = Yii::$app->db->createCommand($sql_count)->queryScalar();
			if(!empty($caseList)){
				if($page == 1) {$out['items'][] = ['id' => 0, 'text' => 'Select Case Preferred Pricing'];}
				foreach($caseList as $case){
					$val=Html::decode($case['client_name']." - ".$case['case_name']);
					$out['items'][] = ['id' => $case['id'], 'text' => $val];
				}
			}
		} else {
			$sql_count="SELECT count(DISTINCT tbl_client_case.id) FROM tbl_client_case INNER JOIN tbl_client on tbl_client.id=tbl_client_case.client_id WHERE (is_close=0) $filterWhere ";
			$sql = "SELECT DISTINCT tbl_client_case.id, case_name, client_name FROM tbl_client_case INNER JOIN tbl_client on tbl_client.id=tbl_client_case.client_id WHERE (is_close=0) $filterWhere ORDER BY case_name $limit_sql";
            $caseList = Yii::$app->db->createCommand($sql)->queryAll();
			$out['total_count'] = Yii::$app->db->createCommand($sql_count)->queryScalar();
			if(!empty($caseList)){
				if($page == 1) {$out['items'][] = ['id' => 0, 'text' => 'Select Case Preferred Pricing'];}
				foreach($caseList as $case){
					$val=Html::decode($case['client_name']." - ".$case['case_name']);
					$out['items'][] = ['id' => $case['id'], 'text' => $val];
				}
			}
		}
		if($out['total_count'] > 0 && ($page * $limit) < $out['total_count']){
			$out['pagination']['more']=true;
		}
		return $out;
	}
    /**
     * It will load Preferred Pricing View By Client/Case.
     * 2nd section will Preferred pricepoint by Selection of Client/Case from PricingTemplates Model.
     * @param client_id (int)
     * @param client_case_id (int)
     * @param type (string) client / case
     */
    public function actionGetPreferredPricing()
    {

    	$this->layout = 'billing';
    	$client_id = Yii::$app->request->get('client_id',0);
    	$client_case_id = Yii::$app->request->get('client_case_id',0);
    	$type = Yii::$app->request->get('type','');

    	$roleId = Yii::$app->user->identity->role_id;
    	$userId = Yii::$app->user->identity->id;
    	$roleTypes = explode(",",Yii::$app->user->identity->role->role_type);

    	$clientList = array('0'=>'Select Client Preferred Pricing');
    	$clientCaseList = array('0'=>'Select Case Preferred Pricing');
    	/*DD code*/
    	if($type == 'client'){
			$template = PricingTemplates::find()->where(['client_id' => $client_id,'template_type'=>1])->select(['id'])->one();
    		/*if ($roleId != 0) {
    			$securityclientSQL = ProjectSecurity::find()->select('client_id')->where(['user_id'=>$userId,'team_id'=>0]);
				$clientList += ArrayHelper::map(Client::find()->select(['tbl_client.id','client_name'])->where(['in','tbl_client.id',$securityclientSQL])
				->innerJoinWith(['clientCases' => function(\yii\db\ActiveQuery $query){$query->where(['is_close'=>0]);}],false)->orderBy('client_name')->all(),'id','client_name');
    		} else {
    			$clientList += ArrayHelper::map(Client::find()->select(['tbl_client.id','client_name'])
    			->innerJoinWith(['clientCases' => function(\yii\db\ActiveQuery $query) use ($userId){
					$query->where(['is_close'=>0]);
				}],false)->orderBy('client_name')->all(),'id','client_name');
			}*/
    	}else{
			$client_id = ClientCase::find()->select(['client_id'])->where(['id'=>$client_case_id])->one()->client_id;
    		$template = PricingTemplates::find()->where(['client_case_id' => $client_case_id,'template_type'=>2])->select(['id'])->one();

    		/*if ($roleId != 0) {
    			$securitySQL = ProjectSecurity::find()->select('client_case_id')->where(['user_id'=>$userId,'team_id'=>0]);
				$clientCaseList += ArrayHelper::map(ClientCase::find()->select(['tbl_client_case.id','tbl_client.client_name','case_name','tbl_client_case.client_id'])
				->where(['in','tbl_client_case.id',$securitySQL])
				->innerJoinWith(['client' => function(\yii\db\ActiveQuery $query){$query->select(['client_name','tbl_client.id']);}],false)
				->andWhere(['is_close'=>0])
				->orderBy('client_name,case_name')
				->all(),'id',function($model, $defaultValue) {
					return $model['client_name']. " - ". $model['case_name'];
				});
    		} else {
	    		$clientCaseList += ArrayHelper::map(ClientCase::find()->select(['tbl_client_case.id','tbl_client.client_name','case_name','tbl_client_case.client_id'])
	    		->innerJoinWith(['client' => function(\yii\db\ActiveQuery $query)use ($userId){
					$query->select(['client_name','tbl_client.id']);
				}],false)->where(['is_close'=>0])->orderBy('client_name,case_name')->all(),'id',function($model, $defaultValue) {
					return $model['client_name']. " - ". $model['case_name'];
				});
    		}*/
    	}
		/*DD code*/

    	$template_id = 0;
    	if(!empty($template)){
    		$template_id = $template->id;
    	}
    	$pricing_temp_sql = PricingTemplatesIds::find()
    	->select(['pricing_id'])
    	->where(['template_id'=>$template_id]);

    	$team_data = 1;
    	if ($roleId == 0) {
    		$team_data = 'SELECT tbl_team.id FROM tbl_team';
    	} else {
    		$team_data = "SELECT team_id FROM tbl_project_security WHERE team_id !=0 AND user_id=$userId GROUP BY team_id";
    	}
    	/*$query = Pricing::find()
    	->select(['tbl_pricing.team_id', 'pricing_type'])
    	->joinWith(['team' => function(\yii\db\ActiveQuery $team) use($team_data){
    		$team->select(['tbl_team.id','team_name']);
    	}])
    	->where(['remove'=>0])
    	->andWhere(['in','tbl_pricing.id',$pricing_temp_sql])
    	->groupBy(['tbl_pricing.team_id','pricing_type']);*/
    	$query = Pricing::find()
    	->select(['tbl_pricing.team_id', 'pricing_type','tbl_team.team_name as pricingteam_name'])
    	->joinWith(['team' => function(\yii\db\ActiveQuery $team) use($team_data){
    		$team->select(['tbl_team.id','team_name','tbl_team.sort_order']);
    	}],false)
    	->where(['remove'=>0])
    	->andWhere(['in','tbl_pricing.id',$pricing_temp_sql])
    	->groupBy(['tbl_pricing.team_id','pricing_type', 'tbl_team.sort_order','tbl_team.team_name']);

    	if(in_array(1,$roleTypes)){
    		$query->andWhere("(tbl_team.id IN ($team_data)) OR (tbl_pricing.team_id = 0) OR (tbl_pricing.team_id = 1)");
    	}else{
    		$query->andWhere("(tbl_team.id IN ($team_data)) OR (tbl_pricing.team_id = 0)");
    	}
    	$query->orderBy('pricing_type, tbl_team.sort_order');
    	//echo "<pre>",print_r($query),"</pre>";die;
    	$dataProvider = new ActiveDataProvider([
    		'query' => $query,
    		'pagination' =>['pageSize'=>-1],
    	]);

    	$models = $dataProvider->getModels();
		//echo "<pre>",print_r($models);die;

    	/*if(!empty($clientList)){
				foreach($clientList as $key => $single){
					$clientList[$key] = htmlspecialchars_decode($single);
				}
		}
		if(!empty($clientCaseList)){
				foreach($clientCaseList as $key => $single){
					$clientCaseList[$key] = htmlspecialchars_decode($single);
				}
		}*/

		//echo "<pre>",print_r($clientList);die;
    	if(Yii::$app->request->isAjax)
    		return $this->renderAjax('get-preferred-pricing',['dataProvider' => $dataProvider, 'type'=>$type, 'client_id' => $client_id, 'client_case_id' => $client_case_id, 'models' => $models, 'clientCaseList' => $clientCaseList, 'clientList' => $clientList, 'template_id'=>$template_id]);
    	else
    		return $this->render('get-preferred-pricing',['dataProvider' => $dataProvider, 'type'=>$type, 'client_id' => $client_id, 'client_case_id' => $client_case_id, 'models' => $models, 'clientCaseList' => $clientCaseList, 'clientList' => $clientList, 'template_id'=>$template_id]);

    }

	/**
     * It will load Preferred Pricepoints By Team & Client/Case.
     * 2nd section will Client Preferred pricepoint by Selection of Client/Case & Team from PricingTemplates Model.
     * @param client_id (int)
     * @param client_case_id (int)
     * @param type (string) client/case
     * @param expandRowKey (int) pricing_id (post)
     */
	public function actionGetPricepointByType()
    {
    	$client_id = Yii::$app->request->get('client_id',0);
    	$client_case_id = Yii::$app->request->get('client_case_id',0);
    	$type = Yii::$app->request->get('type',0);
    	if($type == 'client'){
			$template = PricingTemplates::find()->where(['client_id' => $client_id,'template_type'=>1])->select(['id'])->one();
    	} else {
			$template = PricingTemplates::find()->where(['client_id' => $client_id,'client_case_id' => $client_case_id,'template_type'=>2])->select(['id'])->one();
    	}
    	$template_id = 0;
    	if(!empty($template)) {
    		$template_id = $template->id;
    	}
    	$pricing_temp_sql = PricingTemplatesIds::find()
    	->select(['pricing_id'])
    	->where(['template_id'=>$template_id]);

    	$roleId = Yii::$app->user->identity->role_id;
    	$userId = Yii::$app->user->identity->id;
    	$roleTypes = explode(",",Yii::$app->user->identity->role->role_type);
    	$team_data = 1;
    	if ($roleId == 0) {
    		$team_data = 'SELECT tbl_team.id FROM tbl_team';
    	} else {
    		$team_data = "SELECT team_id FROM tbl_project_security WHERE team_id !=0 AND user_id=$userId GROUP BY team_id";
    	}
    	$expandRowKey=Yii::$app->request->post('expandRowInd',0);
    	$querytogetTeam = Pricing::find()
    	->select(['tbl_pricing.team_id', 'pricing_type','tbl_team.sort_order'])
    	->joinWith(['team' => function(\yii\db\ActiveQuery $team) use($team_data){
    		$team->select(['tbl_team.id','team_name']);
    	}])
    	->where(['remove'=>0])
    	->andWhere(['in','tbl_pricing.id',$pricing_temp_sql])
    	->groupBy(['tbl_pricing.team_id','pricing_type','tbl_team.sort_order']);

    	if(in_array(1,$roleTypes)){
    		$querytogetTeam->andWhere("(tbl_team.id IN ($team_data)) OR (tbl_pricing.team_id = 0) OR (tbl_pricing.team_id = 1)");
    	}else{
    		$querytogetTeam->andWhere("(tbl_team.id IN ($team_data)) OR (tbl_pricing.team_id = 0)");
    	}
    	$querytogetTeam->orderBy('pricing_type,tbl_team.sort_order');
    	$dataProviderForTeamId = new ActiveDataProvider([
    		'query' => $querytogetTeam,
    		'pagination' =>['pageSize'=>-1],
    	]);

    	$models = $dataProviderForTeamId->getModels();
    	$team_id = $models[$expandRowKey]['team_id'];

    	$query = Pricing::find()
    	->select(['tbl_pricing.id','tbl_pricing.price_point', 'pricing_range', 'pricing_type','unit_price_id'])
    	->innerJoinWith(['unit'])
    	->where(['team_id'=>$team_id,'tbl_pricing.remove'=>0])
    	->andWhere(['in','tbl_pricing.id',$pricing_temp_sql]);

		$dataProvider = new ActiveDataProvider([
    		'query' => $query,
    		'pagination' =>['pageSize'=>-1],
    	]);

		return $this->renderPartial('get-pricepoint-by-type',['dataProvider' => $dataProvider, 'client_id' => $client_id, 'client_case_id'=>$client_case_id, 'type'=>$type, 'team_id'=>$team_id, 'template_id' => $template_id]);
    }

    /**
     * It will load remaining pricepoints from pricing model.
     * @param client_id (int)
     * @param client_case_id (int)
     * @param type (string) client / case
     */
    public function actionLoadRemainingPricepoints()
    {
    	$client_id = Yii::$app->request->get('client_id',0);
    	$client_case_id = Yii::$app->request->get('client_case_id',0);
    	$type = Yii::$app->request->get('type','client');

		$roleId = Yii::$app->user->identity->role_id;
    	$userId = Yii::$app->user->identity->id;
    	$roleTypes = explode(",",Yii::$app->user->identity->role->role_type);

    	$clientCaseList = array();
		$clientList = array();
		if ($role_id != 0) {
			$securitySQL = ProjectSecurity::find()->select('case_id')->where(['user_id'=>$user_id,'team_id'=>0]);
			$clientCaseList += ArrayHelper::map(ClientCase::find()->innerJoinWith(['pricingTemplates'=>function(\yii\db\ActiveQuery $query){$query->where(['template_type'=>2]);}])->select(['tbl_client_case.id','case_name','tbl_client_case.client_id'])->where(['in','tbl_client_case.id',$securitySQL])->innerJoinWith(['client' => function(\yii\db\ActiveQuery $query){$query->select(['client_name','tbl_client.id']);}])->where(['is_close'=>0])->andWhere("tbl_client_case.id != $client_case_id")->orderBy('client_name,case_name')->all(),'id',function($model, $defaultValue) {
				return $model['client']['client_name']. " - ". $model['case_name'];
			});
			$securityclientSQL = ProjectSecurity::find()->innerJoinWith(['pricingTemplates'=>function(\yii\db\ActiveQuery $query){$query->where(['template_type'=>1]);}])->select('client_id')->where(['user_id'=>$user_id,'team_id'=>0]);
			$clientList += ArrayHelper::map(Client::find()->select(['tbl_client.id','client_name'])->where(['in','tbl_client.id',$securityclientSQL])->andWhere("tbl_client.id != $client_id")->innerJoinWith(['clientCases' => function(\yii\db\ActiveQuery $query){$query->where(['is_close'=>0]);}])->orderBy('client_name')->all(),'id','client_name');
		} else {
			$clientCaseList += ArrayHelper::map(ClientCase::find()->innerJoinWith(['pricingTemplates'=>function(\yii\db\ActiveQuery $query){$query->where(['template_type'=>2]);}])->select(['tbl_client_case.id','case_name','tbl_client_case.client_id'])->innerJoinWith(['client' => function(\yii\db\ActiveQuery $query){$query->select(['client_name','tbl_client.id']);}])->where(['is_close'=>0])->andWhere("tbl_client_case.id != $client_case_id")->orderBy('client_name,case_name')->all(),'id',function($model, $defaultValue) {
				return $model['client']['client_name']. " - ". $model['case_name'];
			});
			$clientList += ArrayHelper::map(Client::find()->innerJoinWith(['pricingTemplates'=>function(\yii\db\ActiveQuery $query){$query->where(['template_type'=>1]);}])->select(['tbl_client.id','client_name'])->andWhere("tbl_client.id != $client_id")->innerJoinWith(['clientCases' => function(\yii\db\ActiveQuery $query){$query->where(['is_close'=>0]);}])->orderBy('client_name')->all(),'id','client_name');
		}

    	$team_data = 1;
    	if ($roleId == 0) {
    		$team_data = 'SELECT tbl_team.id FROM tbl_team';
    	} else {
    		$team_data = "SELECT team_id FROM tbl_project_security WHERE team_id !=0 AND user_id=$userId GROUP BY team_id";
    	}

    	$pricing_temp_sql = PricingTemplatesIds::find()
    	->select(['pricing_id'])
    	->innerJoinWith(['pricingTemplates' => function(\yii\db\ActiveQuery $appQuery) use($client_id,$client_case_id,$type){
    		$appQuery->select(false);
    		if($type == 'client') {
    			$appQuery->where(['client_id' => $client_id,'template_type'=>1]);
    		} else {
    			$appQuery->where(['client_id' => $client_id,'client_case_id' => $client_case_id,'template_type'=>2]);
    		}
    	}]);

    	// echo "<pre>",print_r($pricing_temp_sql); die;
    	$query = Pricing::find()
    	->select(['tbl_pricing.team_id', 'pricing_type','tbl_pricing.id','tbl_pricing.price_point'])
    	->joinWith(['team' => function(\yii\db\ActiveQuery $team) use($team_data){
    		$team->select(['tbl_team.id','team_name','sort_order']);
    	}])
    	->where(['remove'=>0])
    	->andWhere(['not',['in','tbl_pricing.id',$pricing_temp_sql]]);
    	//->orderBy('tbl_team.sort_order');

    	if(in_array(1,$roleTypes)){
    		$query->andWhere("(tbl_team.id IN ($team_data)) OR (tbl_pricing.team_id = 0) OR (tbl_pricing.team_id = 1)");
    	}else{
    		$query->andWhere("(tbl_team.id IN ($team_data)) OR (tbl_pricing.team_id = 0)");
    	}
    	$data = $query->asArray()->orderBy('pricing_type,sort_order, id')->all();
		//	$data = $query->asArray()->orderBy(' id, pricing_type, sort_order')->all();
    	$model = array();
    	$modelTeam = array();
    	if(!empty($data)){
	    	foreach($data as $key => $models){
	    		$modelTeam[$models['team_id']] = $models['team'];
	    		$model[$models['team_id']][$models['id']] = $models;
	    	}
    	}
    	//echo "<pre>",print_r($model),"</pre>"; die;
    	return $this->renderAjax('remaining-preferred-pricing',['model' => $model, 'modelTeam'=>$modelTeam, 'client_id' => $client_id, 'case_id' => $client_case_id, 'clientList' => $clientList, 'clientCaseList' => $clientCaseList, 'type' => $type]);
    }

    /**
     * It will save remaining pricepoints in pricing templates model.
     * @param client_id (int)
     * @param mixed form posted data;
     */
    public function actionAddRemainingClientPricepoint()
    {
		$client_id = Yii::$app->request->get('client_id',0);
    	$dataClone = Yii::$app->request->post('clone_id',0);
    	$pricingtemp = PricingTemplates::find()->where(['template_type'=>1,'client_id'=>$client_id])->select(['id'])->one();
    	if(empty($pricingtemp)){
    		$pricingtemplates = new PricingTemplates();
    		$pricingtemplates->template_type = 1;
    		$pricingtemplates->client_id = $client_id;
    		$pricingtemplates->client_case_id = 0;
    		$pricingtemplates->save(false);
    		$pricingtempid = Yii::$app->db->getLastInsertID();
    	} else {
    		$pricingtempid = $pricingtemp->id;
    	}
    	if($dataClone!=0){

    		$pricingtempids = PricingTemplatesIds::find()->select(['pricing_id'])->where(['template_id'=>$pricingtempid]);
    		if($pricingtempids->count() > 0){
    			$pricingclients = PricingClients::find()->select(['id'])->where(['in','pricing_id',$pricingtempids])->andWhere(['client_id'=>$client_id]);
    			PricingClientsRates::deleteAll(['in', 'pricing_clients_id', $pricingclients]);
    			PricingClients::deleteAll(['and','client_id=:client_id',['in','pricing_id',$pricingtempids]],[':client_id'=>$client_id]);
    			PricingTemplatesIds::deleteAll(['template_id'=>$pricingtempid]);
    		}

    		$pricing_sql = "SELECT $pricingtempid as template_id,tbl_pricing_templates_ids.pricing_id FROM tbl_pricing_templates_ids INNER JOIN tbl_pricing_templates ON tbl_pricing_templates_ids.template_id = tbl_pricing_templates.id WHERE (template_type=1) AND (client_id='$dataClone')";
    		$sql = 'INSERT INTO tbl_pricing_templates_ids(template_id,pricing_id) '.$pricing_sql;
			\Yii::$app->db->createCommand($sql)->execute();

			/*copy rate IRT-620*/
			$sql="INSERT INTO tbl_pricing_clients(pricing_id, client_id, created, created_by, modified, modified_by) SELECT pricing_id, $client_id as client_id, created, created_by, modified, modified_by FROM tbl_pricing_clients where  client_id=$dataClone AND pricing_id IN (SELECT tbl_pricing_templates_ids.pricing_id FROM tbl_pricing_templates_ids INNER JOIN tbl_pricing ON tbl_pricing.id = tbl_pricing_templates_ids.pricing_id INNER JOIN tbl_pricing_templates ON tbl_pricing_templates_ids.template_id = tbl_pricing_templates.id WHERE tbl_pricing.remove=0 AND (template_type=1) AND (client_id=$dataClone))";
			\Yii::$app->db->createCommand($sql)->execute();
			$sql="INSERT INTO tbl_pricing_clients_rates(pricing_clients_id, team_loc, rate_type, rate_amount, cost_amount, tier_from, tier_to)
			SELECT (SELECT tbl_pricing_clients.id FROM tbl_pricing_clients WHERE pricing_id=(select pricing_id FROM tbl_pricing_clients where id=tbl_pricing_clients_rates.pricing_clients_id) and client_id=$client_id) as pricing_clients_id,team_loc, rate_type, rate_amount, cost_amount, tier_from, tier_to FROM tbl_pricing_clients_rates WHERE pricing_clients_id IN (SELECT tbl_pricing_clients.id FROM tbl_pricing_clients WHERE pricing_id in (SELECT tbl_pricing_templates_ids.pricing_id FROM tbl_pricing_templates_ids INNER JOIN tbl_pricing ON tbl_pricing.id = tbl_pricing_templates_ids.pricing_id INNER JOIN tbl_pricing_templates ON tbl_pricing_templates_ids.template_id = tbl_pricing_templates.id WHERE tbl_pricing.remove=0 AND (template_type=1) AND (client_id=$dataClone)) AND (client_id=$dataClone))
			";
			\Yii::$app->db->createCommand($sql)->execute();
			/*copy rate IRT-620*/
		} else {
    		$teams = Yii::$app->request->post('team');
    		$pricepoint = Yii::$app->request->post('pricepoint');
    		foreach($teams as $team){
    			if(isset($pricepoint[$team]) && !empty($pricepoint[$team])){
	    			foreach($pricepoint[$team] as $ppoint){
	    				$pricingTemplatesIds = new PricingTemplatesIds();
		    			$pricingTemplatesIds->template_id = $pricingtempid;
		    			$pricingTemplatesIds->pricing_id = $ppoint;
		    			$pricingTemplatesIds->save(false);
	    			}
    			}
    		}
    	}
    	return "OK";
    }

	/**
     * It will save remaining pricepoints in pricing templates model.
     * @param client_case_id (int)
     * @param mixed form posted data;
     */
    public function actionAddRemainingCasePricepoint()
    {
		$client_id = Yii::$app->request->get('client_id',0);
		$client_case_id = Yii::$app->request->get('client_case_id',0);
    	$dataClone = Yii::$app->request->post('clone_id',0);
    	$pricingtemp = PricingTemplates::find()->where(['template_type'=>2,'client_id'=>$client_id,'client_case_id'=>$client_case_id])->select(['id'])->one();
    	if(empty($pricingtemp)){
    		$pricingtemplates = new PricingTemplates();
    		$pricingtemplates->template_type = 2;
    		$pricingtemplates->client_id = $client_id;
    		$pricingtemplates->client_case_id = $client_case_id;
    		$pricingtemplates->save(false);
    		$pricingtempid = Yii::$app->db->getLastInsertID();
    	} else {
    		$pricingtempid = $pricingtemp->id;
    	}
    	if($dataClone!=0){

    		$pricingtempids = PricingTemplatesIds::find()->select(['pricing_id'])->where(['template_id'=>$pricingtempid]);
    		if($pricingtempids->count() > 0){
    			$pricingclientscases = PricingClientscases::find()->select(['id'])->where(['in','pricing_id',$pricingtempids])->andWhere(['client_id'=>$client_id,'client_case_id'=>$client_case_id]);
    			PricingClientscasesRates::deleteAll(['in', 'pricing_clientscases_id', $pricingclientscases]);
    			PricingClientscases::deleteAll(['and','client_id=:client_id AND client_case_id=:client_case_id',['in','pricing_id',$pricingtempids]],[':client_id'=>$client_id,':client_case_id'=>$client_case_id]);
    			PricingTemplatesIds::deleteAll(['template_id'=>$pricingtempid]);
    		}

    		$pricing_sql = "SELECT $pricingtempid as template_id,tbl_pricing_templates_ids.pricing_id FROM tbl_pricing_templates_ids INNER JOIN tbl_pricing_templates ON tbl_pricing_templates_ids.template_id = tbl_pricing_templates.id WHERE (template_type=2) AND (client_case_id='$dataClone')";
    		$sql = 'INSERT INTO tbl_pricing_templates_ids(template_id,pricing_id) '.$pricing_sql;
			\Yii::$app->db->createCommand($sql)->execute();
			/*copy rate IRT-620*/
			$sql="INSERT INTO tbl_pricing_clientscases(pricing_id, client_case_id, created, created_by, modified, modified_by) SELECT pricing_id, $client_case_id as client_case_id, created, created_by, modified, modified_by FROM tbl_pricing_clientscases where  client_case_id=$dataClone AND pricing_id IN (SELECT tbl_pricing_templates_ids.pricing_id FROM tbl_pricing_templates_ids INNER JOIN tbl_pricing ON tbl_pricing.id = tbl_pricing_templates_ids.pricing_id INNER JOIN tbl_pricing_templates ON tbl_pricing_templates_ids.template_id = tbl_pricing_templates.id WHERE tbl_pricing.remove=0 AND (template_type=2) AND (client_case_id=$dataClone))";
			\Yii::$app->db->createCommand($sql)->execute();
			$sql="INSERT INTO tbl_pricing_clientscases_rates(pricing_clientscases_id, team_loc, rate_type, rate_amount, cost_amount, tier_from, tier_to)
			SELECT (SELECT tbl_pricing_clientscases.id FROM tbl_pricing_clientscases WHERE pricing_id=(select pricing_id FROM tbl_pricing_clientscases where id=tbl_pricing_clientscases_rates.pricing_clientscases_id) and client_case_id=$client_case_id) as pricing_clientscases_id,team_loc, rate_type, rate_amount, cost_amount, tier_from, tier_to FROM tbl_pricing_clientscases_rates WHERE pricing_clientscases_id IN (SELECT tbl_pricing_clientscases.id FROM tbl_pricing_clientscases WHERE pricing_id in (SELECT tbl_pricing_templates_ids.pricing_id FROM tbl_pricing_templates_ids INNER JOIN tbl_pricing ON tbl_pricing.id = tbl_pricing_templates_ids.pricing_id INNER JOIN tbl_pricing_templates ON tbl_pricing_templates_ids.template_id = tbl_pricing_templates.id WHERE tbl_pricing.remove=0 AND (template_type=2) AND (client_case_id=$dataClone)) AND (client_case_id=$dataClone))
			";
			\Yii::$app->db->createCommand($sql)->execute();
			/*copy rate IRT-620*/
    	} else {
    		$teams = Yii::$app->request->post('team');
    		$pricepoint = Yii::$app->request->post('pricepoint');
    		foreach($teams as $team) {
    			if(isset($pricepoint[$team]) && !empty($pricepoint[$team])) {
	    			foreach($pricepoint[$team] as $ppoint) {
	    				$pricingTemplatesIds = new PricingTemplatesIds();
		    			$pricingTemplatesIds->template_id = $pricingtempid;
		    			$pricingTemplatesIds->pricing_id = $ppoint;
		    			$pricingTemplatesIds->save(false);
	    			}
    			}
    		}
    	}
    	return "OK";
    }

    /**
     * It will remove entire template from pricing templates model.
	 * @param template_id (int)
     */
    public function actionRemoveTemplate()
    {
    	$template_id = Yii::$app->request->post('template_id',0);
    	$pricingtemp = PricingTemplates::findOne($template_id);

    	if(!empty($pricingtemp)){
    		$pricingtempid = $pricingtemp->id;
	    	$template_type = $pricingtemp->template_type;
	    	$client_id = $pricingtemp->client_id;
	    	$client_case_id = $pricingtemp->client_case_id;
	    	$pricingtempids = PricingTemplatesIds::find()->select(['pricing_id'])->where(['template_id'=>$pricingtempid]);
	    	if($pricingtempids->count() > 0){
    			if($template_type==1){
	    			$pricingclients = PricingClients::find()->select(['id'])->where(['in','pricing_id',$pricingtempids])->andWhere(['client_id'=>$client_id]);
	    			PricingClientsRates::deleteAll(['in', 'pricing_clients_id', $pricingclients]);
	    			PricingClients::deleteAll(['and','client_id=:client_id',['in','pricing_id',$pricingtempids]],[':client_id'=>$client_id]);
	    		}
	    		if($template_type==2) {
		    		if($pricingtempids->count() > 0){
		    			$pricingclientscases = PricingClientscases::find()->select(['id'])->where(['in','pricing_id',$pricingtempids])->andWhere(['client_case_id'=>$client_case_id]);
		    			PricingClientscasesRates::deleteAll(['in', 'pricing_clientscases_id', $pricingclientscases]);
		    			PricingClientscases::deleteAll(['and','client_case_id=:client_case_id',['in','pricing_id',$pricingtempids]],[':client_case_id'=>$client_case_id]);
		    		}
	    		}
	    		PricingTemplatesIds::deleteAll(['template_id'=>$pricingtempid]);
	    	}
	    	$pricingtemp->delete();
    		return "OK";
    	} else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

	/**
     * It will remove pricepoint from pricing templates model.
	 * @param template_id (int)
	 * @param pricepoint_id (int)
     */

    public function actionRemovePricepointByTemplate()
    {
    	$template_id = Yii::$app->request->post('template_id',0);
    	$pricepoint = Yii::$app->request->post('pricepoint',[]);
    	$pricingtemp = PricingTemplates::findOne($template_id);

    	if(!empty($pricingtemp)){
    		$pricingtempid = $pricingtemp->id;
	    	$template_type = $pricingtemp->template_type;
	    	$client_id = $pricingtemp->client_id;
	    	$client_case_id = $pricingtemp->client_case_id;
	    	$pricingtempids = PricingTemplatesIds::find()->select(['pricing_id'])->where(['and',['template_id'=>$pricingtempid],['in','pricing_id',$pricepoint]]);
	    	if($pricingtempids->count() > 0){
    			if($template_type==1){
	    			$pricingclients = PricingClients::find()->select(['id'])->where(['in','pricing_id',$pricingtempids])->andWhere(['client_id'=>$client_id]);
	    			PricingClientsRates::deleteAll(['in', 'pricing_clients_id', $pricingclients]);
	    			PricingClients::deleteAll(['and','client_id=:client_id',['in','pricing_id',$pricingtempids]],[':client_id'=>$client_id]);
	    		}
	    		if($template_type==2) {
		    		if($pricingtempids->count() > 0){
		    			$pricingclientscases = PricingClientscases::find()->select(['id'])->where(['in','pricing_id',$pricingtempids])->andWhere(['client_case_id'=>$client_case_id]);
		    			PricingClientscasesRates::deleteAll(['in', 'pricing_clientscases_id', $pricingclientscases]);
		    			PricingClientscases::deleteAll(['and','client_case_id=:client_case_id',['in','pricing_id',$pricingtempids]],[':client_case_id'=>$client_case_id]);
		    		}
	    		}
	    		PricingTemplatesIds::deleteAll(['and',['template_id'=>$pricingtempid],['in','pricing_id',$pricepoint]]);
	    	}
    		return "OK";
    	} else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * It will Add client/case preferred Rate by pricepoint.
     * @param pricing_id (int)
     * @param client_id (int)
     * @param client_case_id (int)
     * @param type (string) client/case
     */
    public function actionAddPreferredPricingRate()
    {
    	$pricing_id = Yii::$app->request->get('pricing_id',0);
    	$client_id = Yii::$app->request->get('client_id',0);
    	$client_case_id = Yii::$app->request->get('client_case_id',0);
    	$type = Yii::$app->request->get('type','');
    	$PricingRates = Yii::$app->request->post('PricingRatesAvail');
    	if(!empty($PricingRates)){
    		if($type == 'client'){
	    		/* Start : Stores Pricing Clients Rates */
    			$pricingClients = PricingClients::find()->select(['id'])->where(["pricing_id"=>$pricing_id, 'client_id'=>$client_id])->one();
    			if(empty($pricingClients)){
    				$pricingClients = new PricingClients;
    				$pricingClients->pricing_id = $pricing_id;
	    			$pricingClients->client_id = $client_id;
	    			$pricingClients->save(false);
		    		$pricing_clients_id = Yii::$app->db->getLastInsertID();
		    	} else {
		    		$pricing_clients_id = $pricingClients->id;
		    		PricingClientsRates::deleteAll('pricing_clients_id = :pricing_clients_id',[':pricing_clients_id'=>$pricing_clients_id]);
		    	}

	        	$rows = array();
	        	$teamLocAr = $PricingRates['team_loc'];
	        	$ratesCount = count($teamLocAr);
	        	$i=0;
	        	for($i=0;$i<$ratesCount;$i++) {
	        		$team_loc = $PricingRates['team_loc'][$i];
	        		$rate_type = $PricingRates['rate_type'][$i];
	        		$rate_amount = $PricingRates['rate_amount'][$i];
	        		$cost_amount = $PricingRates['cost_amount'][$i];
	        		$tier_form = $PricingRates['tier_from'][$i];
	        		$tier_to = $PricingRates['tier_to'][$i];
	        		$rows[] = array('pricing_clients_id'=>$pricing_clients_id, 'team_loc'=>$team_loc, 'rate_type'=>$rate_type, 'rate_amount'=>$rate_amount, 'cost_amount'=>$cost_amount, 'tier_from'=>$tier_form, 'tier_to'=>$tier_to);
	        	}
	    		$columns = (new PricingClientsRates)->attributes();
	        	unset($columns[array_search('id',$columns)]);
	        	Yii::$app->db->createCommand()->batchInsert(PricingClientsRates::tableName(), $columns, $rows)->execute();
	        	/* End : Stores Pricing Clients Rates */
    		} else {
    			/* Start : Stores Pricing Clientscases Rates */
    			$pricingClientscases = PricingClientscases::find()->innerJoinWith('clientCase')->select(['tbl_pricing_clientscases.id'])->where(["pricing_id"=>$pricing_id, 'tbl_client_case.client_id'=>$client_id, 'client_case_id'=>$client_case_id])->one();
    			if(empty($pricingClientscases)){
    				$pricingClientscases = new PricingClientscases;
    				$pricingClientscases->pricing_id = $pricing_id;
	    			//$pricingClientscases->client_id = $client_id;
	    			$pricingClientscases->client_case_id = $client_case_id;
	    			$pricingClientscases->save(false);
		    		$pricing_clientscases_id = Yii::$app->db->getLastInsertID();
		    	} else {
		    		$pricing_clientscases_id = $pricingClientscases->id;
		    		PricingClientscasesRates::deleteAll('pricing_clientscases_id = :pricing_clientscases_id',[':pricing_clientscases_id'=>$pricing_clientscases_id]);
		    	}

	        	$rows = array();
	        	$teamLocAr = $PricingRates['team_loc'];
	        	$ratesCount = count($teamLocAr);
	        	$i=0;
	        	for($i=0;$i<$ratesCount;$i++) {
	        		$team_loc = $PricingRates['team_loc'][$i];
	        		$rate_type = $PricingRates['rate_type'][$i];
	        		$rate_amount = $PricingRates['rate_amount'][$i];
	        		$cost_amount = $PricingRates['cost_amount'][$i];
	        		$tier_form = $PricingRates['tier_from'][$i];
	        		$tier_to = $PricingRates['tier_to'][$i];
	        		$rows[] = array('pricing_clientscases_id'=>$pricing_clientscases_id, 'team_loc'=>$team_loc, 'rate_type'=>$rate_type, 'rate_amount'=>$rate_amount, 'cost_amount'=>$cost_amount, 'tier_from'=>$tier_form, 'tier_to'=>$tier_to);
	        	}
	    		$columns = (new PricingClientscasesRates)->attributes();
	        	unset($columns[array_search('id',$columns)]);
	        	Yii::$app->db->createCommand()->batchInsert(PricingClientscasesRates::tableName(), $columns, $rows)->execute();
	        	/* End : Stores Pricing Clients Rates */
    		}
    	} else {
    		if($type == 'client'){
    			$pricingClients = PricingClients::find()->select(['id'])->where(["pricing_id"=>$pricing_id, 'client_id'=>$client_id])->one();
    			if(!empty($pricingClients)){
	    			$pricing_clients_id = $pricingClients->id;
	    			PricingClientsRates::deleteAll('pricing_clients_id = :pricing_clients_id',[':pricing_clients_id'=>$pricing_clients_id]);
	    			$pricingClients->delete();
    			}
    		} else {
    			$pricingClientscases = PricingClientscases::find()->where(["pricing_id"=>$pricing_id, 'client_case_id'=>$client_case_id])->one();
    			if(!empty($pricingClientscases)){
	    			$pricing_clientscases_id = $pricingClientscases->id;
	    			PricingClientscasesRates::deleteAll('pricing_clientscases_id = :pricing_clientscases_id',[':pricing_clientscases_id'=>$pricing_clientscases_id]);
	    			$pricingClientscases->delete();
    			}
    		}
    	}
    	return 'OK';
    }
}
