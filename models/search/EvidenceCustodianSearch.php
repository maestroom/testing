<?php

namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\EvidenceCustodians;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%evidence_custodians}}".
 *
 * @property integer $cust_id
 * @property string $title
 * @property string $dept
 * @property string $cust_fname
 * @property string $cust_lname
 * @property string $cust_email
 * @property string $cust_mi
 * @property string $created
 * @property integer $created_by
 * @property string $modified
 * @property integer $modified_by
 */
class EvidenceCustodianSearch extends EvidenceCustodians
{
	/**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'dept', 'cust_fname', 'cust_lname', 'cust_email', 'cust_mi','media','project','form' ], 'string'],
            [['created', 'modified'], 'safe'],
            [['created_by', 'modified_by'], 'integer'],
        ];
    }
    /**
     * @inheritdoc
     */
    public function scenarios()
    {
    	// bypass scenarios() implementation in the parent class
    	return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params,$case_id)
    {

    	$query = EvidenceCustodians::find()->joinWith(['clientCaseCustodians clientcasecust'],false)->joinWith(['clientCaseEvidence clientcaseevid'],false)->where('(clientcasecust.cust_id !=0 AND  clientcasecust.client_case_id=' . $case_id.') OR (clientcaseevid.cust_id !=0 AND clientcaseevid.client_case_id=' . $case_id.')');
    	$query->distinct=true;
    	$defaultSort="";
    	if(!isset($params['sort'])){
    		$defaultSort=['custodians_id'=>SORT_ASC];
    		//$query->orderBy('tbl_evidence_custodians.cust_id');
    	}
    	$dataProvider = new ActiveDataProvider([
    			'query' => $query,
    			'pagination'=>['pageSize'=>25]
    	]);
		$dataProvider->sort->enableMultiSort=true;
		/*IRT-67*/
		if(isset($params['grid_id']) && $params['grid_id']!=""){
			$grid_id=$params['grid_id'].'_'.Yii::$app->user->identity->id;
			$sql="SELECT * FROM tbl_dynagrid_dtl WHERE id IN (SELECT sort_id FROM tbl_dynagrid WHERE id='$grid_id')";
			$sort_data=Yii::$app->db->createCommand($sql)->queryOne();
			if(!empty($sort_data)){
					$defaultSort=json_decode($sort_data['data'],true);
			}
		}
		/*IRT-67*/
        if(!isset($params['sort']) && $defaultSort!=""){
			$dataProvider->sort->defaultOrder=$defaultSort;
		}
    	$dataProvider->sort->attributes['custodians_id'] = [
            'asc' => ['tbl_evidence_custodians.cust_id' => SORT_ASC],
            'desc' => ['tbl_evidence_custodians.cust_id' => SORT_DESC],
        ];
    	/* if (!$this->validate()) {
    		// uncomment the following line if you do not want to return any records when validation fails
    		// $query->where('0=1');
    		return $dataProvider;
    	} */

    	if ($params['EvidenceCustodianSearch']['cust_fname'] != null && is_array($params['EvidenceCustodianSearch']['cust_fname'])) {
			if(!empty($params['EvidenceCustodianSearch']['cust_fname'])){
				foreach($params['EvidenceCustodianSearch']['cust_fname'] as $k=>$v){
					if($v=='All'){
						unset($params['EvidenceCustodianSearch']['cust_fname']);break;
					}
				}
			}
			//$query->andFilterWhere(['or like',"CONCAT(cust_lname, ', ' , cust_fname,' ',cust_mi)", $params['EvidenceCustodianSearch']['cust_lname']]);
			$query->andFilterWhere(['or like',"cust_fname", $params['EvidenceCustodianSearch']['cust_fname']]);
		}else{
			$query->andFilterWhere(['or like',"cust_fname", $params['EvidenceCustodianSearch']['cust_fname']]);
		}
    	if ($params['EvidenceCustodianSearch']['cust_lname'] != null && is_array($params['EvidenceCustodianSearch']['cust_lname'])) {
			if(!empty($params['EvidenceCustodianSearch']['cust_lname'])){
				foreach($params['EvidenceCustodianSearch']['cust_lname'] as $k=>$v){
					if($v=='All'){
						unset($params['EvidenceCustodianSearch']['cust_lname']);break;
					}
				}
			}
			//$query->andFilterWhere(['or like',"CONCAT(cust_lname, ', ' , cust_fname,' ',cust_mi)", $params['EvidenceCustodianSearch']['cust_lname']]);
			$query->andFilterWhere(['or like',"cust_lname", $params['EvidenceCustodianSearch']['cust_lname']]);
		}else{
			$query->andFilterWhere(['or like',"cust_lname", $params['EvidenceCustodianSearch']['cust_lname']]);
		}

		if ($params['EvidenceCustodianSearch']['cust_email'] != null && is_array($params['EvidenceCustodianSearch']['cust_email'])) {
			if(!empty($params['EvidenceCustodianSearch']['cust_email'])) {
				foreach($params['EvidenceCustodianSearch']['cust_email'] as $k=>$v) {
					if($v=='All') {
						unset($params['EvidenceCustodianSearch']['cust_email']);break;
					}
				}
			}
			//$query->andFilterWhere(['or like',"CONCAT(cust_lname, ', ' , cust_fname,' ',cust_mi)", $params['EvidenceCustodianSearch']['cust_lname']]);
			$query->andFilterWhere(['or like',"cust_email", $params['EvidenceCustodianSearch']['cust_email']]);
		}else{
			$query->andFilterWhere(['or like',"cust_email", $params['EvidenceCustodianSearch']['cust_email']]);
		}

    	if ($params['EvidenceCustodianSearch']['title'] != null && is_array($params['EvidenceCustodianSearch']['title'])) {
			$title_data=$params['EvidenceCustodianSearch']['title'];
			if(!empty($params['EvidenceCustodianSearch']['title'])){
				foreach($params['EvidenceCustodianSearch']['title'] as $k=>$v){
					if($v=='(not set)') {
						$params['EvidenceCustodianSearch']['title'][$k] = '';
					}
          if($v=='All'){
            unset($params['EvidenceCustodianSearch']['title']);break;
          }
          /* else if(strpos($v,",") !== false) {
						unset($params['EvidenceCustodianSearch']['title'][$k]);
					}*/
				}
			}
			$query->andFilterWhere(['in',"title", $params['EvidenceCustodianSearch']['title']]);
		//	$params['EvidenceCustodianSearch']['title'] = $title_data;
		}else{
			$query->andFilterWhere(['like',"title", $params['EvidenceCustodianSearch']['title']]);
		}

    	if ($params['EvidenceCustodianSearch']['dept'] != null && is_array($params['EvidenceCustodianSearch']['dept'])) {
			$dept_data=$params['EvidenceCustodianSearch']['dept'];
			if(!empty($params['EvidenceCustodianSearch']['dept'])){
				foreach($params['EvidenceCustodianSearch']['dept'] as $k=>$v){
					if($v=='(not set)') {
						$params['EvidenceCustodianSearch']['dept'][$k] = '';
					}
          if($v=='All'){
            unset($params['EvidenceCustodianSearch']['dept']);break;
          }
          /* else if(strpos($v,",") !== false) {
						unset($params['EvidenceCustodianSearch']['dept'][$k]);
					} */
				}
			}
			$query->andFilterWhere(['in',"dept", $params['EvidenceCustodianSearch']['dept']]);
		//	$params['EvidenceCustodianSearch']['dept'] = $dept_data;
		}else{
			$query->andFilterWhere(['like',"dept", $params['EvidenceCustodianSearch']['dept']]);
		}

		if ($params['EvidenceCustodianSearch']['cust_mi'] != null && is_array($params['EvidenceCustodianSearch']['cust_mi'])) {
			$dept_data=$params['EvidenceCustodianSearch']['cust_mi'];
			if(!empty($params['EvidenceCustodianSearch']['cust_mi'])){
				foreach($params['EvidenceCustodianSearch']['cust_mi'] as $k=>$v){
					if($v=='(not set)') {
						$params['EvidenceCustodianSearch']['cust_mi'][$k] = '';
					}
          if($v=='All'){
            unset($params['EvidenceCustodianSearch']['cust_mi']);break;
          }
          /* else if(strpos($v,",") !== false) {
						unset($params['EvidenceCustodianSearch']['dept'][$k]);
					} */
				}
			}
			$query->andFilterWhere(['in',"cust_mi", $params['EvidenceCustodianSearch']['cust_mi']]);
		//	$params['EvidenceCustodianSearch']['dept'] = $dept_data;
		}else{
			$query->andFilterWhere(['like',"cust_mi", $params['EvidenceCustodianSearch']['cust_mi']]);
		}

    	if(isset($params['EvidenceCustodianSearch']['media']) && $params['EvidenceCustodianSearch']['media']!=""){
			if(!empty($params['EvidenceCustodianSearch']['media'])){
				$querysql = '';
				foreach($params['EvidenceCustodianSearch']['media'] as $k=>$v){
					if($v=='All'){
						unset($params['EvidenceCustodianSearch']['media']);
						$querysql='';
						break;
					} else if($v=='Y') {
						if($querysql!='')
							$querysql .= ' OR tbl_evidence_custodians.cust_id IN (SELECT cust_id FROM tbl_evidence_contents WHERE cust_id=tbl_evidence_custodians.cust_id GROUP BY cust_id)';
						else
							$querysql = 'tbl_evidence_custodians.cust_id IN (SELECT cust_id FROM tbl_evidence_contents WHERE cust_id=tbl_evidence_custodians.cust_id GROUP BY cust_id)';
					} else if($v == 'N') {
						if($querysql!='')
							$querysql .= ' OR tbl_evidence_custodians.cust_id NOT IN (SELECT cust_id FROM tbl_evidence_contents WHERE cust_id=tbl_evidence_custodians.cust_id GROUP BY cust_id)';
						else
							$querysql = 'tbl_evidence_custodians.cust_id NOT IN (SELECT cust_id FROM tbl_evidence_contents WHERE cust_id=tbl_evidence_custodians.cust_id GROUP BY cust_id)';
					}
				}
				if($querysql!='')
					$query->andWhere($querysql);
			}
			//$query->andFilterWhere(['or in',"tbl_evidence_custodians.cust_id", $params['EvidenceCustodianSearch']['dept']]);

    		/*if($params['EvidenceCustodianSearch']['media']=="Y"){
    			$query->andWhere("tbl_evidence_custodians.cust_id IN (SELECT cust_id FROM tbl_evidence_contents WHERE cust_id=tbl_evidence_custodians.cust_id GROUP BY cust_id)");
    		}else if($params['EvidenceCustodianSearch']['media']=="N"){
    			$query->andWhere("tbl_evidence_custodians.cust_id NOT IN (SELECT cust_id FROM tbl_evidence_contents WHERE cust_id=tbl_evidence_custodians.cust_id GROUP BY cust_id)");
    		}*/
    	}

    	/*if($params['EvidenceCustodianSearch']['title'] == 'blank'){
			$query->andWhere(['title' => '']);
		}

		if($params['EvidenceCustodianSearch']['dept'] == 'blank'){
			$query->andWhere(['dept' => '']);
		}*/

    	if(isset($params['EvidenceCustodianSearch']['project']) && $params['EvidenceCustodianSearch']['project']!="") {
    		$sql="SELECT tbl_evidence_contents.cust_id
	    	FROM tbl_task_instruct_evidence
	    	INNER JOIN tbl_task_instruct ON tbl_task_instruct_evidence.task_instruct_id = tbl_task_instruct.id
	    	INNER JOIN tbl_tasks ON tbl_tasks.id = tbl_task_instruct.task_id
	    	INNER JOIN tbl_evidence_contents ON tbl_evidence_contents.id = evidence_contents_id
	    	WHERE tbl_tasks.client_case_id = $case_id
	    	AND tbl_tasks.task_status
	    	IN ( 0, 1, 3 )
	    	AND tbl_tasks.task_closed =0
	    	AND tbl_tasks.task_cancel =0
	    	AND tbl_task_instruct.isactive =1";

	    	if(!empty($params['EvidenceCustodianSearch']['project'])){
				$querysql = '';
				foreach($params['EvidenceCustodianSearch']['project'] as $k=>$v){
					if($v=='All'){
						unset($params['EvidenceCustodianSearch']['project']);
						$querysql='';
						break;
					} else if($v=='Y') {
						if($querysql!='')
							$querysql .= " OR tbl_evidence_custodians.cust_id IN ($sql)";
						else
							$querysql = " tbl_evidence_custodians.cust_id IN ($sql)";
					} else if($v == 'N') {
						if($querysql!='')
							$querysql .= " OR tbl_evidence_custodians.cust_id NOT IN ($sql)";
						else
							$querysql = "tbl_evidence_custodians.cust_id NOT IN ($sql)";
					}
				}
				if($querysql!='')
					$query->andWhere($querysql);
			}

	    	/*if($params['EvidenceCustodianSearch']['project']=="Y"){
    			$query->andWhere("tbl_evidence_custodians.cust_id IN ($sql)");
    		}else if($params['EvidenceCustodianSearch']['project']=="N"){
    			$query->andWhere("tbl_evidence_custodians.cust_id NOT IN ($sql)");
    		}*/
    	}
    	if(isset($params['EvidenceCustodianSearch']['form']) && $params['EvidenceCustodianSearch']['form']!=""){
    		$sql = "SELECT tbl_form_custodian_values.cust_id
					FROM tbl_form_custodian_values
					INNER JOIN tbl_client_case_custodians ON tbl_client_case_custodians.cust_id = tbl_form_custodian_values.cust_id
					WHERE tbl_client_case_custodians.client_case_id=$case_id
					GROUP BY tbl_form_custodian_values.cust_id
					UNION ALL
					SELECT tbl_form_custodian_values.cust_id
					FROM tbl_form_custodian_values
					INNER JOIN tbl_client_case_evidence ON tbl_client_case_evidence.cust_id = tbl_form_custodian_values.cust_id
					WHERE tbl_client_case_evidence.client_case_id =$case_id
					GROUP BY tbl_form_custodian_values.cust_id";

			if(!empty($params['EvidenceCustodianSearch']['form'])){
				$querysql = '';
				foreach($params['EvidenceCustodianSearch']['form'] as $k=>$v){
					if($v=='All'){
						unset($params['EvidenceCustodianSearch']['form']);
						$querysql='';
						break;
					} else if($v=='Y') {
						if($querysql!='')
							$querysql .= " OR tbl_evidence_custodians.cust_id IN ($sql)";
						else
							$querysql = " tbl_evidence_custodians.cust_id IN ($sql)";
					} else if($v == 'N') {
						if($querysql!='')
							$querysql .= " OR tbl_evidence_custodians.cust_id NOT IN ($sql)";
						else
							$querysql = "tbl_evidence_custodians.cust_id NOT IN ($sql)";
					}
				}
				if($querysql!='')
					$query->andWhere($querysql);
			}

    		/*if($params['EvidenceCustodianSearch']['form']=="Y"){
    			$query->andWhere("tbl_evidence_custodians.cust_id IN ($sql)");
    		}else if($params['EvidenceCustodianSearch']['form']=="N"){
    			$query->andWhere("tbl_evidence_custodians.cust_id NOT IN ($sql)");
    		}*/
    	}
    	$this->load($params);
    	return $dataProvider;
    }

 	public function searchFilter($params,$case_id)
    {
    	//echo "<pre>",print_r($case_id),"</pre>";die;
    	$dataProvider = array();
		$query = EvidenceCustodians::find()->joinWith('clientCaseCustodians clientcasecust')->joinWith('clientCaseEvidence clientcaseevid')->where('clientcasecust.cust_id !=0 AND  clientcasecust.client_case_id=' . $case_id.' OR clientcaseevid.cust_id !=0 AND clientcaseevid.client_case_id=' . $case_id);
    	if($params['field']=='cust_lname') {
    		$query->select(['tbl_evidence_custodians.cust_id','cust_lname',"CONCAT(cust_lname, ', ' , cust_fname,' ',cust_mi) AS full_name"]);
    		if(isset($params['q']) && $params['q']!="") {
	    		$query->andFilterWhere([
					'or',
					//['like', 'cust_fname', $params['q']],
					['like', 'cust_lname', $params['q']],
					//['like', 'cust_mi', $params['q']]
	    		]);
    		}
    		$dataProvider = ArrayHelper::map($query->all(),'cust_lname','cust_lname');
    	}
    	if($params['field']=='cust_fname') {
    		$query->select(['tbl_evidence_custodians.cust_id','cust_fname',"CONCAT(cust_lname, ', ' , cust_fname,' ',cust_mi) AS full_name"]);
    		if(isset($params['q']) && $params['q']!="") {
	    		$query->andFilterWhere([
					'or',
					//['like', 'cust_fname', $params['q']],
					['like', 'cust_fname', $params['q']],
					//['like', 'cust_mi', $params['q']]
	    		]);
    		}
    		$dataProvider = ArrayHelper::map($query->all(),'cust_fname','cust_fname');
    	}
		if($params['field']=='cust_email') {
    		$query->select(['tbl_evidence_custodians.cust_id','cust_email']);
    		$query->andWhere("cust_email IS NOT NULL AND cust_email!='' ");
    		if(isset($params['q']) && $params['q']!="") {
	    		$query->andFilterWhere([
					'or',
					//['like', 'cust_fname', $params['q']],
					['like', 'cust_email', $params['q']],
					//['like', 'cust_mi', $params['q']]
	    		]);
    		}
    		$dataProvider = ArrayHelper::map($query->all(),'cust_email','cust_email');
    	}
    	if($params['field']=='title'){
    		$query->select(['tbl_evidence_custodians.cust_id',"title"]);
    		$query->groupby(['tbl_evidence_custodians.cust_id','title']);
    		$query->andWhere("title IS NOT NULL AND title!='' ");
    		$query->orderby('title');
    		if(isset($params['q']) && $params['q']!=""){$query->andFilterWhere(['like', 'title', $params['q']]);}
    		$dataProvider = ArrayHelper::map($query->all(),'title','title');
    	}
    	if($params['field']=='dept' ){
    		$query->select(['tbl_evidence_custodians.cust_id',"dept"]);
    		$query->groupby(['tbl_evidence_custodians.cust_id', "dept"]);
    		$query->andWhere("dept IS NOT NULL AND dept!='' ");
    		$query->orderby('dept');
    		if(isset($params['q']) && $params['q']!=""){$query->andFilterWhere(['like', 'dept', $params['q']]);}
    		$dataProvider = ArrayHelper::map($query->all(),'dept','dept');
    	}
    	return array_merge(array(''=>'All'), $dataProvider);
    }
    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function searchWithoutFilter($params,$case_id)
    {
		$query = EvidenceCustodians::find()->joinWith('clientCaseCustodians clientcasecust')->joinWith('clientCaseEvidence clientcaseevid')->where('clientcasecust.cust_id !=0 AND  clientcasecust.client_case_id=' . $case_id.' OR clientcaseevid.cust_id !=0 AND clientcaseevid.client_case_id=' . $case_id);
    	$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'pagination'=>['pageSize'=>25]
    	]);

    	$this->load($params);

    	if (!$this->validate()) {
    		// uncomment the following line if you do not want to return any records when validation fails
    		// $query->where('0=1');
    		return $dataProvider;
    	}
    	return $dataProvider;
    }

}
