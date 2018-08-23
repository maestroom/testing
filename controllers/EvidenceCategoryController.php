<?php

namespace app\controllers;

use Yii;
use app\models\EvidenceCategory;
use app\models\search\EvidenceCategorySearch;
use app\models\User;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
/**
 * EvidenceCategoryController implements the CRUD actions for EvidenceCategory model.
 */
class EvidenceCategoryController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['get'],
                ],
            ],
        ];
    }

    /**
     * Lists all EvidenceCategory models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new EvidenceCategorySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		/*IRT 67,68,86,87,258*/
		/*IRT 96,398 */
		$filter_type=\app\models\User::getFilterType(['tbl_evidence_category.id','tbl_evidence_category.category'],['tbl_evidence_category']);
		$config = [];       
		$config_widget_options = [];		
		$filterWidgetOption=\app\models\User::getFilterWidgetOption($filter_type,Url::toRoute(['evidence-category/ajax-filter']),$config,$config_widget_options);
		/*IRT 67,68,86,87,258*/

        return $this->renderAjax('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'filter_type'=>$filter_type,
			'filterWidgetOption'=>$filterWidgetOption
        ]);
    }

	/**
	 * Filter GridView with Ajax
	 * */
	public function actionAjaxFilter(){
		$params = Yii::$app->request->queryParams;				
		$modelseach = EvidenceCategory::find()->select(['id','category'])->where(['remove'=>0]);
		if(isset($params['q']) && $params['q']!='')
			$modelseach->andWhere("category like '%".$params['q']."%'");
			
		$dataProvider = ArrayHelper::map($modelseach->all(),'id','category');
		$dataProvider = array_merge([''=>'All'],$dataProvider);
		$out['results']=array();		
		foreach ($dataProvider as $key=>$val){
			$val1 = $val;
			$val2 = $val;
			
			if($val == ''){
				$val1 = '(not set)';
				$val='(not set)';
				$val2='(not set)';
			}							
			
			$out['results'][] = ['id' => $val1, 'text' => $val,'label' => $val2];
		}
		//print_r($out);die;
		return json_encode($out);
	}

    /**
     * Displays a single EvidenceCategory model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new EvidenceCategory model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new EvidenceCategory();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return 'OK'; 
            //$this->redirect(['view', 'id' => $model->id]);
        } else {
			$evidence_category_length = (new User)->getTableFieldLimit('tbl_evidence_category');
            return $this->renderAjax('create', [
                'model' => $model,
                'evidence_category_length' => $evidence_category_length
            ]);
        }
    }

    /**
     * Updates an existing EvidenceCategory model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return 'OK'; 
            //$this->redirect(['view', 'id' => $model->id]);
        } else {
			$evidence_category_length = (new User)->getTableFieldLimit('tbl_evidence_category');
            return $this->renderAjax('update', [
                'model' => $model,
                'evidence_category_length'=>$evidence_category_length
            ]);
        }
    }

    /**
     * Deletes an existing EvidenceCategory model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
	public function actionDelete($id){
     	$model = $this->findModel($id);
     	$model->remove = 1;
     	if ($model->save()) {
     		return 'OK';
     	}
     	exit;
    }
    /**
     * Deletes an selected existing CaseCloseType model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    
    public function actionDeleteselected() {
    	if (isset($_POST['keylist'])) {
    		$keys = implode(",",$_POST['keylist']);
    		EvidenceCategory::updateAll(['remove' => 1], 'id IN ('.$keys.') AND remove = 0' );
    		return 'OK';
    	}
    }

    /**
     * Finds the EvidenceCategory model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return EvidenceCategory the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = EvidenceCategory::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
