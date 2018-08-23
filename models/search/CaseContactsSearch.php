<?php

namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use app\models\ClientContacts;
use app\models\CaseContacts;

/**
 * ClientContactsSearch represents the model behind the search form about `app\models\ClientContacts`.
 */
class CaseContactsSearch extends CaseContacts
{
	public $fullname,$contact_type,$lname,$add_1,$notes,$clientContacts;

    /**
     * @inheritdoc
     */
    public function rules()
    {
       return [
            [['client_id', 'client_case_id', 'client_contacts_id', 'created', 'created_by', 'modified', 'modified_by'], 'required'],
            [['client_id', 'client_case_id', 'client_contacts_id', 'created_by', 'modified_by'], 'integer'],
            [['created', 'modified','clientContacts'], 'safe'],
        ];
    }
    
    public function attributes()
    {
        // add related fields to searchable attributes
        return array_merge(parent::attributes(), ['fname','lname','mi', 'contact_type', 'add_1','notes']);
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
    public function search($params)
    {
		$query = CaseContacts::find()->select(['tbl_case_contacts.id','tbl_case_contacts.client_case_id','tbl_case_contacts.client_contacts_id'])->where(['client_case_id'=> $params['client_case_id']])->joinWith(['clientContacts' => function (\yii\db\ActiveQuery $query) { $query->joinWith('country'); }]); 
    	//$demo = CaseContacts::find()->select('*')->where(['client_case_id'=> $params['client_case_id']])->joinWith('clientContacts')->all(); 
    	
    	if(!isset($params['sort'])){
			$query->orderBy(['tbl_case_contacts.id'=>SORT_ASC]);
		}
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        	'pagination'=>['pageSize'=>8],
        	//'sort'=> ['defaultOrder' => ['tbl_case_contacts.id'=>SORT_DESC]]
        	//'sort'=> ['defaultOrder' => ['tbl_case_contacts.id'=>SORT_DESC]]
        ]);
        $dataProvider->sort->attributes['notes']=[
			'asc'  => ['tbl_client_contacts.notes' => SORT_ASC],
            'desc' => ['tbl_client_contacts.notes' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['contact_type']=[
			'asc'  => ['tbl_client_contacts.contact_type' => SORT_ASC],
            'desc' => ['tbl_client_contacts.contact_type' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['lname']=[
			'asc' => ['tbl_client_contacts.lname' => SORT_ASC,'tbl_client_contacts.fname' => SORT_ASC,'tbl_client_contacts.mi' => SORT_ASC],
            'desc' => ['tbl_client_contacts.lname' => SORT_DESC,'tbl_client_contacts.fname' => SORT_DESC,'tbl_client_contacts.mi' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['add_1']=[
			'asc'  => ['tbl_client_contacts.add_1' => SORT_ASC,'tbl_client_contacts.add_2' => SORT_ASC,'tbl_client_contacts.city' => SORT_ASC,'tbl_client_contacts.state' => SORT_ASC,'tbl_country.country_name' => SORT_ASC],
            'desc' => ['tbl_client_contacts.add_1' => SORT_DESC,'tbl_client_contacts.add_2' => SORT_DESC,'tbl_client_contacts.city' => SORT_DESC,'tbl_client_contacts.state' => SORT_DESC,'tbl_country.country_name' => SORT_DESC],
        ];
        
        $this->load($params);
        
        $query->andFilterWhere(['id' => $this->id]);
        /*if(isset($params['CaseContactsSearch']['contact_type']) && $params['CaseContactsSearch']['contact_type']!="" && $params['CaseContactsSearch']['contact_type']!="All"){
        	$query->andFilterWhere(['tbl_client_contacts.contact_type' => $params['CaseContactsSearch']['contact_type']]);
        }*/
        if ($params['CaseContactsSearch']['contact_type'] != null && is_array($params['CaseContactsSearch']['contact_type'])) {

			if(!empty($params['CaseContactsSearch']['contact_type'])){
				foreach($params['CaseContactsSearch']['contact_type'] as $k=>$v){
					if($v=='All'){ // || strpos($v,",") !== false
						unset($params['CaseContactsSearch']['contact_type']);
					}
				}
			}
			$query->andFilterWhere(["or like", "contact_type", $params['CaseContactsSearch']['contact_type']]);
			$this->contact_type = $params['CaseContactsSearch']['contact_type'];
		}
		
       /* 
            if(isset($params['CaseContactsSearch']['lname']) && $params['CaseContactsSearch']['lname']!="" && $params['CaseContactsSearch']['lname']!="All"){
    			//print_r($params['CaseContactsSearch']['lname']); exit;
            	$query->andFilterWhere(["like", "CONCAT(tbl_client_contacts.lname,', ',tbl_client_contacts.fname,' ',tbl_client_contacts.mi)", $params['CaseContactsSearch']['lname']]);
            }
        */
        if ($params['CaseContactsSearch']['lname'] != null && is_array($params['CaseContactsSearch']['lname'])) {
			if(!empty($params['CaseContactsSearch']['lname'])){
				foreach($params['CaseContactsSearch']['lname'] as $k=>$v){
					if($v=='All'){
						unset($params['CaseContactsSearch']['lname']);
					}
				}
			}
		    $query->andFilterWhere(["or like", "CONCAT(lname, ', ', fname, ' ', mi)", $params['CaseContactsSearch']['lname']]);
			$this->lname = $params['CaseContactsSearch']['lname'];
		}else{
			$query->andFilterWhere(["or like", "CONCAT(lname, ', ', fname, ' ', mi)", $params['CaseContactsSearch']['lname']]);
			$this->lname = $params['CaseContactsSearch']['lname'];
		}
        
        if(isset($params['CaseContactsSearch']['title']) && $params['CaseContactsSearch']['title']!=""){
        	$query->andFilterWhere(['tbl_client_contacts.title' => $params['CaseContactsSearch']['title']]);
        }
        if(isset($params['CaseContactsSearch']['phone_o']) && $params['CaseContactsSearch']['phone_o']!=""){
        	$query->andFilterWhere(['tbl_client_contacts.phone_o' => $params['CaseContactsSearch']['phone_o']]);
        }
        if(isset($params['CaseContactsSearch']['phone_m']) && $params['CaseContactsSearch']['phone_m']!=""){
        	$query->andFilterWhere(['tbl_client_contacts.phone_m' => $params['CaseContactsSearch']['phone_m']]);
        }
        if(isset($params['CaseContactsSearch']['email']) && $params['CaseContactsSearch']['email']!=""){
        	$query->andFilterWhere(['tbl_client_contacts.email' => $params['CaseContactsSearch']['email']]);
        }
        
        if(isset($params['CaseContactsSearch']['add_1']) && $params['CaseContactsSearch']['add_1']!="" && $params['CaseContactsSearch']['add_1']!="All") {
        	$query->join('LEFT JOIN','tbl_country as tc','tc.id = tbl_client_contacts.country_id');
        	if (Yii::$app->db->driverName == 'mysql') {
	    		$sql="(SELECT CONCAT_WS(', ',if(add_1!='',add_1,NULL),if(add_2!='',add_2,NULL),if(city!='',city,NULL),if(tbl_country.country_name!='',tbl_country.country_name,NULL),if(zip!='',zip,NULL)))";
	    	} else {
    			$sql="(SELECT STUFF(
					CASE WHEN LEN(add_1) > 0 THEN COALESCE(', ' + add_1, '') ELSE '' END +
					CASE WHEN LEN(add_2) > 0 THEN COALESCE(', ' + add_2, '') ELSE '' END +
					CASE WHEN LEN(city) > 0 THEN COALESCE(', ' + city, '') ELSE '' END+
					CASE WHEN LEN(tbl_country.country_name) > 0 THEN COALESCE(', ' + tbl_country.country_name, '') ELSE '' END+
					CASE WHEN LEN(zip) > 0 THEN COALESCE(', ' + zip, '') ELSE '' END,
				1, 2, ''))";
    		}
    		$query->andFilterWhere(['or like', $sql, $params['CaseContactsSearch']['add_1']]);
    		$this->add_1 =  $params['CaseContactsSearch']['add_1'];
    	}
        /*if(isset($params['CaseContactsSearch']['notes']) && $params['CaseContactsSearch']['notes']!="" && $params['CaseContactsSearch']['notes']!="All"){
        	$query->andFilterWhere(['tbl_client_contacts.notes' => $params['CaseContactsSearch']['notes']]);
        }*/
        
        if ($params['CaseContactsSearch']['notes'] != null && is_array($params['CaseContactsSearch']['notes'])) {
			if(!empty($params['CaseContactsSearch']['notes'])){
				foreach($params['CaseContactsSearch']['notes'] as $k=>$v){
					if($v=='All'){ // || strpos($v,",") !== false
						unset($params['CaseContactsSearch']['notes']);
					}
				}
			}
			$query->andFilterWhere(["or like", "notes", $params['CaseContactsSearch']['notes']]);
			$this->notes = $params['CaseContactsSearch']['notes'];
		}else{
			$query->andFilterWhere(["or like", "notes", $params['CaseContactsSearch']['notes']]);
			$this->notes = $params['CaseContactsSearch']['notes'];
		}
        
        return $dataProvider;
    }
    
    
    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function searchFilter($params)
    {
    	$query = CaseContacts::find()->select('*')->joinWith('clientContacts',false)->where(['client_case_id'=> $params['case_id']]);
    	/* if(!isset($params['sort'])){
    		$query->orderBy(['tbl_case_contacts.id'=>SORT_ASC]);
    	} */

		if($params['field']=='contact_type'){
    		$query->select('contact_type');
    		if(isset($params['q']) && $params['q']!=""){
    			$query->andFilterWhere(['like', 'contact_type', $params['q']]);
    		}
    		$dataProvider = ArrayHelper::map($query->all(),'contact_type','contact_type');
    	}
    	if($params['field']=='lname'){
			$sql="SELECT CONCAT(lname, ', ', fname, ' ', mi) as fullname FROM tbl_case_contacts LEFT JOIN tbl_client_contacts ON tbl_case_contacts.client_contacts_id = tbl_client_contacts.id WHERE client_case_id=".$params['case_id']." ORDER BY tbl_case_contacts.id";
    		if(isset($params['q']) && $params['q']!="") {
    			$sql.=" AND fullname like '%".$params['q']."%'";
    		}
    		$dataProvider = ArrayHelper::map(ClientContacts::findBySql($sql)->all(), 'fullname','fullname');
    	}
    	
    	if($params['field']=='add_1'){
			if (Yii::$app->db->driverName == 'mysql'){
    			$sql="SELECT * FROM (SELECT CONCAT_WS(', ',if(add_1!='',add_1,NULL),if(add_2!='',add_2,NULL),if(city!='',city,NULL),if(tbl_country.country_name!='',tbl_country.country_name,NULL),if(zip!='',zip,NULL)) AS address FROM tbl_case_contacts LEFT JOIN tbl_client_contacts ON tbl_case_contacts.client_contacts_id = tbl_client_contacts.id LEFT JOIN  tbl_country ON tbl_country.id = tbl_client_contacts.country_id WHERE client_case_id=".$params['case_id'].") as a WHERE address!=''";
    		}else{
    			$sql="SELECT  * FROM (SELECT STUFF(
						CASE WHEN LEN(add_1) > 0 THEN COALESCE(', ' + add_1, '') ELSE '' END +
						CASE WHEN LEN(add_2) > 0 THEN COALESCE(', ' + add_2, '') ELSE '' END +
						CASE WHEN LEN(city) > 0 THEN COALESCE(', ' + city, '') ELSE '' END+
						CASE WHEN LEN(tbl_country.country_name) > 0 THEN COALESCE(', ' + tbl_country.country_name, '') ELSE '' END+
						CASE WHEN LEN(zip) > 0 THEN COALESCE(', ' + zip, '') ELSE '' END,
					1, 2, '') AS address
					  FROM tbl_case_contacts LEFT JOIN tbl_client_contacts ON tbl_case_contacts.client_contacts_id = tbl_client_contacts.id LEFT JOIN  tbl_country ON tbl_country.id = tbl_client_contacts.country_id WHERE client_case_id=".$params['case_id'].") as a WHERE address IS NOT NULL";
    		}
    		if(isset($params['q']) && $params['q']!=""){
    			$sql.=" AND address like '%".$params['q']."%'";
    			// $query->andFilterWhere(['like', "CONCAT(lname, ', ', fname, ' ', mi)", $params['q']]);
    		}
    		$dataProvider = ArrayHelper::map(ClientContacts::findBySql($sql)->all(),'address','address');
    	}
    	
    	if($params['field']=='notes'){
    		$query->select('notes');
    		if(isset($params['q']) && $params['q']!=""){
    			$query->andFilterWhere(['like', 'notes', $params['q']]);
    		}
    		$dataProvider = ArrayHelper::map($query->all(),'notes','notes'); //->andWhere('notes!=""')
    	}
    	
    	$this->load($params);
    	$data=array_merge(array(''=>'All'), $dataProvider);
    	return $data;
    }
}
