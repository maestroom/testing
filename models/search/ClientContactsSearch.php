<?php

namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use app\models\ClientContacts;

/**
 * ClientContactsSearch represents the model behind the search form about `app\models\ClientContacts`.
 */
class ClientContactsSearch extends ClientContacts
{
	public $fullname,$address;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['client_id', 'country_id', 'created_by', 'modified_by'], 'integer'],
            [['contact_type', 'fullname', 'lname', 'fname', 'mi', 'title', 'phone_o', 'phone_m', 'email', 'add_1', 'add_2', 'city', 'state', 'zip', 'notes'], 'string'],
            [['created', 'modified','fullname'], 'safe']
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
    public function search($params)
    {       
        $query = ClientContacts::find()->where(['client_id' => $params['client_id']]); //->orderBy(['id'=>SORT_ASC]);
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        	'pagination'=>['pageSize'=>25],
        	'sort'=> ['defaultOrder' => ['id'=>SORT_DESC]]
        ]);
        
        $dataProvider->sort->attributes['fullname'] = [	
            'asc' => ['tbl_client_contacts.lname' => SORT_ASC,'tbl_client_contacts.fname' => SORT_ASC,'tbl_client_contacts.mi' => SORT_ASC],
            'desc' => ['tbl_client_contacts.lname' => SORT_DESC,'tbl_client_contacts.fname' => SORT_DESC,'tbl_client_contacts.mi' => SORT_DESC],
        ];
        
        $this->load($params);
//        Commented Below code to multiselect values	
//        if (!$this->validate()) {
//         // uncomment the following line if you do not want to return any records when validation fails
//            $query->where('0=1');
//            return $dataProvider;
//        }
	
        $query->andFilterWhere(['id' => $this->id]);        
        if(isset($params['ClientContactsSearch']['contact_type']) && is_array($params['ClientContactsSearch']['contact_type'])){
            if(!empty($params['ClientContactsSearch']['contact_type'])){               
                foreach($params['ClientContactsSearch']['contact_type'] as $key => $single_ctype){
                        if($key == 0)
                            $ctype_sql = "contact_type like '%$single_ctype%' ";
                        else
                            $ctype_sql .= "OR contact_type like '%$single_ctype%'";                
                    }
                     $query->andWhere($ctype_sql);
                }            
//            	$query->andFilterWhere(['like', 'contact_type', $this->contact_type]);           
        }
        if(isset($params['ClientContactsSearch']['fullname']) && is_array($params['ClientContactsSearch']['fullname'])){
            if(!empty($params['ClientContactsSearch']['fullname'])){            
                foreach($params['ClientContactsSearch']['fullname'] as $key => $single_fullname){
                        if($key == 0)
                            $fullname_sql = "CONCAT(lname, ', ', fname, ' ', mi) like '%$single_fullname%' ";
                        else
                            $fullname_sql .= "OR CONCAT(lname, ', ', fname, ' ', mi) like '%$single_fullname%'";                
                    }
                     $query->andWhere($fullname_sql);
//        	$fullname = $params['ClientContactsSearch']['fullname'];
//        	$query->andWhere("CONCAT(lname, ', ', fname, ' ', mi) like '%$fullname%'");
            }
        }else{
			$fullname = $params['ClientContactsSearch']['fullname'];	
			$fullname_sql = "CONCAT(lname, ', ', fname, ' ', mi) like '%$fullname%' ";
			$query->andWhere($fullname_sql);
		}
        $query->andFilterWhere(['like', 'title', $this->title]);
        $query->andFilterWhere(['like', 'phone_o', $this->phone_o]);
        $query->andFilterWhere(['like', 'phone_m', $this->phone_m]);
        $query->andFilterWhere(['like', 'email', $this->email]);
        
    	if(isset($params['ClientContactsSearch']['add_1']) && $params['ClientContactsSearch']['add_1']!=""  && $params['ClientContactsSearch']['add_1']!="All"){
    		$query->join('LEFT JOIN','tbl_country','tbl_country.id = tbl_client_contacts.country_id');
    		if (Yii::$app->db->driverName == 'mysql'){
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
    		
    		$query->andFilterWhere(['or like',$sql,$params['ClientContactsSearch']['add_1']]);
    		
    		/*$query->andFilterWhere(["like","CONCAT(tbl_client_contacts.add_1,', ',tbl_client_contacts.add_2,' , ',tbl_client_contacts.city,', ',tbl_country.country_name,', ',tbl_client_contacts.zip)",$params['ClientContactsSearch']['add_1']]);*/
    		
        	/*$query->andFilterWhere([
        			'or',
        			['like', 'tbl_client_contacts.add_1', $params['ClientContactsSearch']['add_1']],
        			['like', 'tbl_client_contacts.add_2', $params['ClientContactsSearch']['add_1']],
        			['like', 'tbl_client_contacts.city', $params['ClientContactsSearch']['add_1']],
        			['like', 'tbl_client_contacts.zip', $params['ClientContactsSearch']['add_1']],
        			['like', 'tbl_country.country_name', $params['ClientContactsSearch']['add_1']],
        	]);*/
        }
        if(isset($params['ClientContactsSearch']['notes']) && is_array($params['ClientContactsSearch']['notes'])){
			if(!empty($params['ClientContactsSearch']['notes'])){
				foreach($params['ClientContactsSearch']['notes'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($params['ClientContactsSearch']['notes']);break;
					}
				}
			}
			$query->andFilterWhere(['or like', 'notes', $this->notes]);
        }else{
			$query->andFilterWhere(['or like', 'notes', $params['ClientContactsSearch']['notes']]);
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
    	$query = ClientContacts::find()->where(['client_id' => $params['client_id']]); //->orderBy(['id'=>SORT_ASC]);
    
    	$dataProvider = new ActiveDataProvider([
    			'query' => $query,
    			'pagination'=>['pageSize'=>25],
    			'sort'=> ['defaultOrder' => ['id'=>SORT_DESC]]
    	]);
    
    	$this->load($params);
    	if($params['field']=='contact_type'){
    		$query->select('contact_type');
    		if(isset($params['q']) && $params['q']!=""){
    			$query->andFilterWhere(['like', 'contact_type', $params['q']]);
    		}
    		$dataProvider = ArrayHelper::map($query->all(),'contact_type','contact_type');
    	}
    	if($params['field']=='fullname'){
    		//$query->select("CONCAT(lname, ', ', fname, ' ', mi) as fullname");
    		$sql="SELECT CONCAT(lname, ', ', fname, ' ', mi) as fullname FROM tbl_client_contacts WHERE client_id=".$params['client_id'];
    		if(isset($params['q']) && $params['q']!=""){
    			$sql.=" AND fullname like '%".$params['q']."%'";
    			//$query->andFilterWhere(['like', "CONCAT(lname, ', ', fname, ' ', mi)", $params['q']]);
    		}
    		$dataProvider = ArrayHelper::map(ClientContacts::findBySql($sql)->all(),'fullname','fullname');
    	
    	}
    	if($params['field']=='add_1'){
    		if (Yii::$app->db->driverName == 'mysql'){
	    		$sql="SELECT * FROM (SELECT CONCAT_WS(', ',if(add_1!='',add_1,NULL),if(add_2!='',add_2,NULL),if(city!='',city,NULL),if(tbl_country.country_name!='',tbl_country.country_name,NULL),if(zip!='',zip,NULL)) AS address FROM tbl_client_contacts LEFT JOIN  tbl_country ON tbl_country.id = tbl_client_contacts.country_id WHERE client_id=".$params['client_id'].") as a WHERE address!=''";
	    	}else{
    			$sql = "SELECT  * FROM (SELECT STUFF(
							CASE WHEN LEN(add_1) > 0 THEN COALESCE(', ' + add_1, '') ELSE '' END +
							CASE WHEN LEN(add_2) > 0 THEN COALESCE(', ' + add_2, '') ELSE '' END +
							CASE WHEN LEN(city) > 0 THEN COALESCE(', ' + city, '') ELSE '' END+
							CASE WHEN LEN(tbl_country.country_name) > 0 THEN COALESCE(', ' + tbl_country.country_name, '') ELSE '' END+
							CASE WHEN LEN(zip) > 0 THEN COALESCE(', ' + zip, '') ELSE '' END,
						1, 2, '') AS address
						FROM tbl_client_contacts LEFT JOIN tbl_country ON tbl_country.id = tbl_client_contacts.country_id WHERE client_id=".$params['client_id'].") as a WHERE address IS NOT NULL";
    		}
    		if(isset($params['q']) && $params['q']!="") {
    			$sql.=" AND address like '%".$params['q']."%'";
    			// $query->andFilterWhere(['like', "CONCAT(lname, ', ', fname, ' ', mi)", $params['q']]);
    		}
    		//echo $sql;
    		$dataProvider = ArrayHelper::map(ClientContacts::findBySql($sql)->all(), 'address', 'address');
    	}
    	if($params['field']=='notes') {
    		$query->select('notes');
    		if(isset($params['q']) && $params['q']!="") {
    			$query->andFilterWhere(['like', 'notes', $params['q']]);
    		}
    		// andWhere('notes!=NULL')->
    		$dataProvider = ArrayHelper::map($query->all(),'notes','notes');
    	}
    	// print_r($dataProvider);die;
    	$data=array_merge(array(''=>'All'), $dataProvider);
    	// print_r($data);die;
    	return $data;
    }
}
