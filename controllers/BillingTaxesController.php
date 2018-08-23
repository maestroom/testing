<?php
namespace app\controllers;

use Yii;
use yii\widgets\ActiveForm;
use app\models\TaxClass;
use app\models\search\TaxClassSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use app\models\TaxClassPricing;
use app\models\Pricing;
use app\models\Team;
use app\models\Client;
use app\models\search\TaxCodeSearch;
use app\models\TaxCode;
use app\models\User;
use app\models\TaxCodeClients;

/**
 * BillingTaxesController implements the CRUD actions for TaxClass model.
 */
class BillingTaxesController extends Controller
{
    /**
     * @inheritdoc
     */
     
    public function beforeAction($action)
	{
		if (Yii::$app->user->isGuest)
			$this->redirect(array('site/login'));
			
		if (!(new User)->checkAccess(7))
			throw new \yii\web\HttpException(404, 'User permissions do not allow entry into this module.');	
			
	
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
     * Lists all TaxClass models.
     * @return mixed
     */
    public function actionTaxClasses()
    {
    	$this->layout = 'billing';
        $searchModel = new TaxClassSearch();
        $params['grid_id']='dynagrid-tax-classes';
        Yii::$app->request->queryParams +=$params;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
        $params = Yii::$app->request->queryParams;
        $tax_class_desc = array();
        $tax_class_desc['tax_class_desc'] = $params['TaxClassSearch']['tax_class_desc'];
        if($params['TaxClassSearch']['tax_class_desc'] == 'blank')
			$tax_class_desc['tax_class_desc'] = '';
			
		/*IRT 67,68,86,87,258*/
        $filter_type=\app\models\User::getFilterType(['tbl_tax_class.class_name','tbl_pricing.price_point'],['tbl_pricing','tbl_tax_class']);
        $config = [];
        $config_widget_options = [];
        $filterWidgetOption=\app\models\User::getFilterWidgetOption($filter_type,Url::toRoute(['billing-taxes/ajax-tax-class-filter']),$config,$config_widget_options);
        /*IRT 67,68,86,87,258*/	
        if(Yii::$app->request->isAjax) 
            return $this->renderAjax('tax-management', ['searchModel' => $searchModel,'dataProvider' => $dataProvider,'tax_class_desc' => $tax_class_desc,'filter_type'=>$filter_type,'filterWidgetOption'=>$filterWidgetOption,]);
        else
            return $this->render('tax-management', ['searchModel' => $searchModel,'dataProvider' => $dataProvider,'tax_class_desc' => $tax_class_desc,'filter_type'=>$filter_type,'filterWidgetOption'=>$filterWidgetOption,]);    

    }
    
    /**
     * Lists all TaxClass models.
     * @return mixed
     */
    public function actionTaxCodes()
    {
    	$this->layout = 'billing';
    	$searchModel = new TaxCodeSearch();
        $params['grid_id']='dynagrid-tax-codes';
        Yii::$app->request->queryParams +=$params;
    	$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
    	
    	$params = Yii::$app->request->queryParams;
        $tax_code_desc = array();
        $tax_code_desc['tax_code_desc'] = $params['TaxCodeSearch']['tax_code_desc'];
        if($params['TaxCodeSearch']['tax_code_desc'] == 'blank')
			$tax_code_desc['tax_code_desc'] = '';
		
		/*IRT 67,68,86,87,258*/
        $filter_type=\app\models\User::getFilterType(['tbl_tax_code.tax_code','tbl_tax_code.tax_class_id','tbl_tax_code.tax_rate','tbl_tax_code.client'],['tbl_tax_code']);
        $config = [];
        $config_widget_options = ['tax_class_id'=>['field_alais'=>'class_name']];
        $filterWidgetOption=\app\models\User::getFilterWidgetOption($filter_type,Url::toRoute(['billing-taxes/ajax-tax-code-filter']),$config,$config_widget_options);
        /*IRT 67,68,86,87,258*/	
        if(Yii::$app->request->isAjax) 
    	    return $this->renderAjax('tax-code-management', ['searchModel' => $searchModel,'dataProvider' => $dataProvider,'tax_code_desc' => $tax_code_desc,'filter_type'=>$filter_type,'filterWidgetOption'=>$filterWidgetOption]);
        else
            return $this->render('tax-code-management', ['searchModel' => $searchModel,'dataProvider' => $dataProvider,'tax_code_desc' => $tax_code_desc,'filter_type'=>$filter_type,'filterWidgetOption'=>$filterWidgetOption]);    
    }
    
    /**
     * Ajax Tax Class Filter
     * @mixed
     */
    public function actionAjaxTaxClassFilter()
    {
    	$searchModel = new TaxClassSearch();
        $dataProvider = $searchModel->searchFilter(Yii::$app->request->queryParams);
    	$out['results']=array();
	    foreach ($dataProvider as $key=>$val){
			$val1 = $val;
			if($val == '')
				$val1 = 'blank';
	        $out['results'][] = ['id' => $val1, 'text' => $val, 'label' => $val1];
	    }
	    return json_encode($out);
    }
    
    /**
     * Ajax Tax Code Filter
     * @mixed
     */
    public function actionAjaxTaxCodeFilter()
    {
    	$searchModel = new TaxCodeSearch();
    	$dataProvider = $searchModel->searchFilter(Yii::$app->request->queryParams);
    	$out['results']=array();
    	foreach ($dataProvider as $key=>$val){
			$val1 = $val;
			if($val == '')
				$val1 = 'blank';
    		$out['results'][] = ['id' => $val1, 'text' => $val, 'label' => $val1];
    	}
    	return json_encode($out);
    }
    
    /**
     * get clients list for tax code grid 
     */
     public function actionGetTaxcodeClientsLists(){
		$id = Yii::$app->request->get('id');			
		// where condition
		$where='';
		if($id!='' && $id!=0){
			$where.=' WHERE id NOT IN (SELECT client_id FROM tbl_tax_code_clients WHERE tax_code_id ='.$id.')';
		}
		$taxcodeclients = "SELECT id,client_name from tbl_client as tc".$where;
		$lists = \Yii::$app->db->createCommand($taxcodeclients)->queryAll();
		return $this->renderAjax('taxcode-client-lists', [
    		'model' => $model,
    		'taxcodeclients' => $lists,
    	]);
	 }
    
    /**
     *  get price point list for Add/Edit Tax classes
     */ 
     public function actionGetPricePointLists()
     {
		$model = new TaxClass();
		$id = Yii::$app->request->get('id'); // get id
		
		// Tax Class Pricing 
			$where='';
			if($id!=''){
				$where = 'tbl_tax_class_pricing.tax_class_id='.$id;
			}
			$tblclasspricing = TaxClassPricing::find()->select(['tbl_tax_class_pricing.price_id'])->where($where);
			$taxclassbilling = Pricing::find()->select(['tbl_pricing.id','tbl_pricing.pricing_type','tbl_pricing.team_id','tbl_pricing.price_point'])->joinWith(['team' => function(\Yii\db\ActiveQuery $query){
				$query->select(['tbl_team.id','tbl_team.team_name']);
			}])->where('remove=0')->AndWhere(['NOT IN','tbl_pricing.id',$tblclasspricing])->asArray()->all();
		// pricing
		
		$lists = array();
		foreach($taxclassbilling as $val) {
			if($val['team']['team_name']=='')
				$lists[0]['Sharing'][$val['id']] = $val['price_point'];
			else
				$lists[$val['team']['id']][$val['team']['team_name']][$val['id']] = $val['price_point'];
		}
		
	    return $this->renderAjax('price-point-lists', [
    		'model' => $model,
    		'pricePoint_data' => $lists,
    	]);
	 }
	 
    /**
     * Creates a new TaxCode model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionAddTaxCodes(){
    	$model = new TaxCode();
    	if ($model->load(Yii::$app->request->post())) {
            $clients = Yii::$app->request->post('clients');
    		$model->client = count($clients);
            //$clients = Yii::$app->request->post('clients');
    		if(!empty($clients)){
                if($model->save()) {
                    foreach($clients as $val){
        				$mymodel = new TaxCodeClients();
        				$mymodel->tax_code_id = $model->id;
        				$mymodel->client_id = $val;
        				$mymodel->save(false);
        			}
                    echo "OK";
                } else {
                    Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                    return ActiveForm::validate($model);    
                }
            } else {
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                $validate=ActiveForm::validate($model);
                if(!empty($validate)){
                    return $validate;//ActiveForm::validate($model);     
                } else {
                    return "FAIL";
                }
            }
    		die();
    	} else {
			$tax_classes = ArrayHelper::map(TaxClass::find()->select(['id','class_name'])->all(),'id','class_name');
			$tax_code_length = (new User)->getTableFieldLimit('tbl_tax_code'); 	
    		return $this->renderAjax('add-tax-code', [
    			'model' => $model,
    			'tax_classes'=>$tax_classes,
    			'tax_code_length' =>$tax_code_length
    		]);
    	}
    }

    /**
     * Creates a new TaxClass model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionAddTaxClasses()
    {
        $model = new TaxClass();
        if ($model->load(Yii::$app->request->post())) {
			$pricing = Yii::$app->request->post('pricepointlist');
            $model->pricepoint = count($pricing);
            if(!empty($pricing)){
            	if($model->save(false)){
            		foreach($pricing as $val){
            			$mymodel = new TaxClassPricing();
            			$mymodel->tax_class_id = $model->id;
            			$mymodel->price_id = $val;
            			$mymodel->save(false);
            		}
            		echo "OK";
            	} else {
            		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                    return ActiveForm::validate($model);  
            	}
            } else {
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                 $validate=ActiveForm::validate($model);
                if(!empty($validate)){
                    return $validate;//ActiveForm::validate($model);       
                } else {
                    return "FAIL";
                }
            }
        	die();
        } else {
			$tax_class_length = (new User)->getTableFieldLimit('tbl_tax_class'); 						
		    return $this->renderAjax('add-tax-class', [
                'model' => $model,
                'tax_class_length' =>$tax_class_length
            ]);
        }
    }
    
    /**
     * Updates an existing TaxCode model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdateTaxCodes($id)
    {
    	$model = TaxCode::findOne($id);
    	if ($model->load(Yii::$app->request->post())) {
    		$model->client = count(Yii::$app->request->post('clients'));
            $clients = Yii::$app->request->post('clients');
            if(!empty($clients)){
                $delete = TaxCodeClients::deleteAll('tax_code_id='.$id);
        		if($model->save()){
        			$clients = Yii::$app->request->post('clients');
        			foreach($clients as $val){
        				$mymodel = new TaxCodeClients();
        				$mymodel->tax_code_id = $model->id;
        				$mymodel->client_id = $val;
        				$mymodel->save();
        			}
        			echo "OK";
         		} else {
         			Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                    return ActiveForm::validate($model);    
         		}
            } else {
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                 $validate=ActiveForm::validate($model);
                if(!empty($validate)){
                    return $validate;//ActiveForm::validate($model);       
                } else {
                    return "FAIL";
                }
            }
     		die();
    	} else {
    		$query = "SELECT tc.id,tc.client_name FROM tbl_client as tc LEFT JOIN tbl_tax_code_clients as tcc ON tc.id = tcc.client_id WHERE tcc.tax_code_id = ".$id;
    		$taxcodeclients = \Yii::$app->db->createCommand($query)->queryAll();
    		$tax_classes = ArrayHelper::map(TaxClass::find()->select(['id','class_name'])->all(),'id','class_name');
    		$tax_code_length = (new User)->getTableFieldLimit('tbl_tax_code'); 	
    		return $this->renderAjax('update-tax-code', [
    			'model' => $model,
    			'tax_classes' => $tax_classes,
    			'taxcodeclients' => $taxcodeclients,
    			'tax_code_length' =>$tax_code_length
    		]);
    	}
    }

    /**
     * Updates an existing TaxClass model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdateTaxClasses($id)
    {
    	$model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post())) {
			$delete = TaxClassPricing::deleteAll('tax_class_id='.$id);
        	$model->pricepoint = count(Yii::$app->request->post('pricepointlist'));
            $pricing = Yii::$app->request->post('pricepointlist');
            if(!empty($pricing)){
                if($model->save()){
            		foreach($pricing as $val){
            			$mymodel = new TaxClassPricing();
            			$mymodel->tax_class_id = $model->id;
            			$mymodel->price_id = $val;
            			$mymodel->save();
            		}
            		echo "OK";
            	} else {
            		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                    return ActiveForm::validate($model);  
            	}
            } else {
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                $validate=ActiveForm::validate($model);
                if(!empty($validate)){
                    return $validate;//ActiveForm::validate($model);      
                } else {
                    return "FAIL";
                }
            }
            die();
        } else {
			// get class pricing point
        	$tblclasspricing = TaxClassPricing::find()->select(['tbl_tax_class_pricing.price_id'])->where('tbl_tax_class_pricing.tax_class_id='.$id);
			$taxclassbilling = Pricing::find()->select(['tbl_pricing.id','tbl_pricing.pricing_type','tbl_pricing.team_id','tbl_pricing.price_point'])->joinWith(['team' => function(\Yii\db\ActiveQuery $query){
				$query->select(['tbl_team.id','tbl_team.team_name']);
			}])->where('remove=0')->AndWhere(['IN','tbl_pricing.id',$tblclasspricing])->asArray()->all();
        	
        	// price point data
        	$pricePoint_data = array();
			foreach($taxclassbilling as $val){
				if($val['team']['team_name']=='')
					$pricePoint_data[$val['team']['id']]['Sharing'][$val['id']] = $val['price_point'];
				else
					$pricePoint_data[$val['team']['id']][$val['team']['team_name']][$val['id']] = $val['price_point'];
			}
			// end price point
			$tax_class_length = (new User)->getTableFieldLimit('tbl_tax_class'); 		
		    return $this->renderAjax('update-tax-classes', [
                'model' => $model,
            	'pricePoint_data' => $pricePoint_data,
            	'taxclassesprice' => $taxclassesprice,
            	'tax_class_length' => $tax_class_length
            ]);
        }
    }
    

    /**
     * Deletes an existing TaxClass model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDeleteTaxClasses()
    {
    	$text_class_ids = Yii::$app->request->post('keys');    	
    	$avail = TaxCode::find()->where('tax_class_id IN ('.$text_class_ids.')')->one();    	
    	$response = [];
    	if(!empty($avail)){
			$response['status'] = 'fail';
			$response['tax_code'] = $avail->tax_code;			
    	}else { 
			$response['status'] = 'success';
			$delete1 = \Yii::$app->db->createCommand("DELETE FROM tbl_tax_class_pricing  WHERE tax_class_id IN ($text_class_ids)")->execute();
			$delete2 = \Yii::$app->db->createCommand("DELETE FROM tbl_tax_class WHERE id IN ($text_class_ids)")->execute();
		}
		echo json_encode($response);
        die();
    }
    
    /**
     * Deletes an existing TaxCode model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDeleteTaxCodes()
    {
    	$id = Yii::$app->request->post('keys','0'); 
        $sql=" SELECT count(*) FROM tbl_invoice_final_taxes WHERE tbl_invoice_final_taxes.tax_code_id IN ($id)";		
		$count=Yii::$app->db->createCommand($sql)->queryScalar();
		if($count > 0){
			echo "FINALINVOICE";die;
		}
    	$delete = \Yii::$app->db->createCommand("DELETE FROM tbl_tax_code_clients WHERE tax_code_id IN ($id)")->execute();
    	$delete = \Yii::$app->db->createCommand("DELETE FROM tbl_tax_code WHERE id IN ($id)")->execute();
    	die();
    }

    /**
     * Finds the TaxClass model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TaxClass the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TaxClass::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
