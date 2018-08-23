<?php

namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\InvoiceFinal;
use yii\helpers\ArrayHelper;
use app\models\Options;

/**
 * InvoiceFinalSearch represents the model behind the search form about app\models\InvoiceFinal.
 */
class InvoiceFinalSearch extends InvoiceFinal
{
	public $client_case_id;
    public $client_name = '';
    public $case_name = '';
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'client_id', 'display_by', 'has_accum_cost', 'created_by', 'modified_by','closed_by'], 'integer'],
            //[['total'], 'number'],
            [['created_date', 'modified_date','totalinvoiceamt','client_case_id','client_name','case_name','closed_date', 'usr_first_name'], 'safe'],
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
        $timezoneOffset = (new Options)->getOffsetOfCurrenttimeZone();
		if (Yii::$app->db->driverName == 'mysql'){
			$function = 'getTotalFinalizedInvoiceAmount(tbl_invoice_final.id)';
		} else {
			$function = 'dbo.getTotalFinalizedInvoiceAmount(tbl_invoice_final.id)';
		}
		/*$case_sql="SELECT tcc.case_name FROM tbl_invoice_final as tf
            INNER JOIN tbl_invoice_final_billing as tb ON tf.id = tb.invoice_final_id
            INNER JOIN tbl_tasks_units_billing as tu ON tu.id = tb.billing_unit_id
            INNER JOIN tbl_tasks_units as taskunit ON taskunit.id = tu.tasks_unit_id
            INNER JOIN tbl_tasks as t ON t.id = taskunit.task_id
            INNER JOIN tbl_client_case as tcc ON tcc.id = t.client_case_id
            INNER JOIN tbl_client as tc ON tc.id = tcc.client_id
             WHERE tf.id =  tbl_invoice_final.id GROUP BY tcc.case_name";
        $client_sql="SELECT tc.client_name FROM tbl_invoice_final as tf
            INNER JOIN tbl_invoice_final_billing as tb ON tf.id = tb.invoice_final_id
            INNER JOIN tbl_tasks_units_billing as tu ON tu.id = tb.billing_unit_id
            INNER JOIN tbl_tasks_units as taskunit ON taskunit.id = tu.tasks_unit_id
            INNER JOIN tbl_tasks as t ON t.id = taskunit.task_id
            INNER JOIN tbl_client_case as tcc ON tcc.id = t.client_case_id
            INNER JOIN tbl_client as tc ON tc.id = tcc.client_id
             WHERE tf.id = tbl_invoice_final.id GROUP BY tc.client_name";*/

        $query = InvoiceFinal::find()->select(['tbl_invoice_final.id','tbl_invoice_final.client_id','client_case_id','display_by','has_accum_cost','tbl_invoice_final.created_by','tbl_invoice_final.modified_by','created_date','modified_date','tbl_client_case.case_name as case_name','tbl_client.client_name as client_name',$function.' as totalinvoiceamt', 'tbl_invoice_final.closed_date', 'tbl_invoice_final.closed_by']);
        $query->joinWith(['client','clientCase','closedUser'],false);
        // add conditions that should always apply here
        //$params['InvoiceFinalSearch']['total'] = (isset($params['InvoiceFinalSearch']['total']) && $params['InvoiceFinalSearch']['total']!='')?$params['InvoiceFinalSearch']['total']:"";

        $params['InvoiceFinalSearch']['totalinvoiceamt'] = (isset($params['InvoiceFinalSearch']['totalinvoiceamt']) && $params['InvoiceFinalSearch']['totalinvoiceamt']!='')?$params['InvoiceFinalSearch']['totalinvoiceamt']:"";

        $params['InvoiceFinalSearch']['id'] = (isset($params['InvoiceFinalSearch']['id']) && $params['InvoiceFinalSearch']['id']!='')?$params['InvoiceFinalSearch']['id']:"";
        $params['InvoiceFinalSearch']['created_date'] = (isset($params['InvoiceFinalSearch']['created_date']) && $params['InvoiceFinalSearch']['created_date']!='')?$params['InvoiceFinalSearch']['created_date']:"";
        $params['InvoiceFinalSearch']['client_id'] = (isset($params['InvoiceFinalSearch']['client_id']) && $params['InvoiceFinalSearch']['client_id']!='')?$params['InvoiceFinalSearch']['client_id']:"";
        //$defaultSort="";
        $defaultSort=['id'=>SORT_DESC];
			//$query->orderBy('tbl_invoice_final.id DESC');
        /*if(isset($params['sort'])){
        	if($params['sort']=='created_date'){
        		$defaultSort=['created_date'=>SORT_DESC];
        		//$query->orderBy('created_date DESC');

        	}else if($params['sort'] == 'client_id' || $params['sort'] == '-client_id'){
				$query->select(['tbl_invoice_final.*','stc.client_name as cc_name','stcc.case_name',"CONCAT(stc.client_name,' - ',stcc.case_name) as client_case_name", $function.' as totalinvoiceamt','('.$case_sql.') as case_name','('.$client_sql.') as client_name']);
				$query->join('INNER JOIN','tbl_invoice_final_billing as stb','tbl_invoice_final.id = stb.invoice_final_id');
				$query->join('INNER JOIN','tbl_tasks_units_billing as stu','stu.id = stb.billing_unit_id');
				$query->join('INNER JOIN','tbl_tasks_units as staskunit','staskunit.id = stu.tasks_unit_id');
				$query->join('INNER JOIN','tbl_tasks as st','st.id = staskunit.task_id');
				$query->join('INNER JOIN','tbl_client_case as stcc','stcc.id = st.client_case_id');
				$query->join('INNER JOIN','tbl_client as stc','stc.id = stcc.client_id');
				if($params['sort'] == 'client_id'){
					$defaultSort=['stc_client_name'=>SORT_DESC];
					//$query->orderBy("stc.client_name DESC");
				}else{
					//$query->orderBy("stc.client_name ASC");
					$defaultSort=['stc_client_name'=>SORT_ASC];
				}
			}else if($params['sort'] == 'client_case_id' || $params['sort'] == '-client_case_id'){
				$query->select(['tbl_invoice_final.*','stc.client_name as cc_name','stcc.case_name',"CONCAT(stc.client_name,' - ',stcc.case_name) as client_case_name", $function.' as totalinvoiceamt','('.$case_sql.') as case_name','('.$client_sql.') as client_name']);
				$query->join('INNER JOIN','tbl_invoice_final_billing as stb','tbl_invoice_final.id = stb.invoice_final_id');
				$query->join('INNER JOIN','tbl_tasks_units_billing as stu','stu.id = stb.billing_unit_id');
				$query->join('INNER JOIN','tbl_tasks_units as staskunit','staskunit.id = stu.tasks_unit_id');
				$query->join('INNER JOIN','tbl_tasks as st','st.id = staskunit.task_id');
				$query->join('INNER JOIN','tbl_client_case as stcc','stcc.id = st.client_case_id');
				$query->join('INNER JOIN','tbl_client as stc','stc.id = stcc.client_id');
				if($params['sort'] == 'client_case_id'){
					$defaultSort=['null_client_case_id'=>SORT_DESC,'stcc_case_name'=>SORT_DESC];
					//$query->orderBy("ISNULL(tbl_invoice_final.client_case_id) DESC, stcc.case_name DESC");
				}else{
					//$query->orderBy("ISNULL(tbl_invoice_final.client_case_id) ASC, stcc.case_name ASC");
					$defaultSort=['null_client_case_id'=>SORT_ASC,'stcc_case_name'=>SORT_ASC];
				}
			}
        }else{
			$defaultSort=['id'=>SORT_DESC];
			//$query->orderBy('tbl_invoice_final.id DESC');
		}*/
        $userId = Yii::$app->user->identity->id;
		$roleId = Yii::$app->user->identity->role_id;
		$where = "tbl_invoice_final.is_closed = ".$params['is_closed'];
		
		if($roleId!=0){
			  $clientCaseSql = "SELECT DISTINCT tps.client_case_id  FROM tbl_project_security tps LEFT JOIN tbl_client_case ON tps.client_case_id = tbl_client_case.id LEFT JOIN tbl_client ON tps.client_id = tbl_client.id WHERE tps.user_id = $userId  AND tps.team_id=0";
			  $clientsSql = "SELECT DISTINCT tpc.client_id  FROM tbl_project_security tpc LEFT JOIN tbl_client_case ON tpc.client_case_id = tbl_client_case.id LEFT JOIN tbl_client ON tpc.client_id = tbl_client.id WHERE tpc.user_id = $userId  AND tpc.team_id=0";
			  $query->orWhere("tbl_invoice_final.client_case_id IN ($clientCaseSql)");
			  $query->orWhere("tbl_invoice_final.client_id IN ($clientsSql) AND tbl_invoice_final.client_case_id = 0");

				$where .= " AND (tbl_invoice_final.client_case_id IN ($clientCaseSql) OR (tbl_invoice_final.client_id IN ($clientsSql) AND tbl_invoice_final.client_case_id = 0))";

		}
		$query->andWhere(['tbl_invoice_final.is_closed' => $params['is_closed']]);
		$dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
				'pageSize' => 50
            ],
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
        $dataProvider->sort->attributes['client_id'] = [
			'asc' => ['tbl_client.client_name' => SORT_ASC],
			'desc' => ['tbl_client.client_name' => SORT_DESC],
		];
        $dataProvider->sort->attributes['client_case_id'] = [
			'asc' => ['tbl_client_case.case_name' => SORT_ASC],
			'desc' => ['tbl_client_case.case_name' => SORT_DESC],
		];
		$dataProvider->sort->attributes['stc_client_name'] = [
			'asc' => ['stc.client_name' => SORT_ASC],
			'desc' => ['stc.client_name' => SORT_DESC],
		];
		$dataProvider->sort->attributes['stcc_case_name'] = [
			'asc' => ['stcc.case_name' => SORT_ASC],
			'desc' => ['stcc.case_name' => SORT_DESC],
		];
		$dataProvider->sort->attributes['null_client_case_id'] = [
			'asc' => ['ISNULL(tbl_invoice_final.client_case_id)' => SORT_ASC],
			'desc' => ['ISNULL(tbl_invoice_final.client_case_id)' => SORT_DESC],
		];
		$dataProvider->sort->attributes['created_date'] = [
			'asc' => ['tbl_invoice_final.created_date' => SORT_ASC],
			'desc' => ['tbl_invoice_final.created_date' => SORT_DESC],
		];
        $dataProvider->sort->attributes['id'] = [
			'asc' => ['tbl_invoice_final.id' => SORT_ASC],
			'desc' => ['tbl_invoice_final.id' => SORT_DESC],
		];

		$dataProvider->sort->attributes['totalinvoiceamt'] = [
			'asc' => [$function => SORT_ASC],
			'desc' => [$function => SORT_DESC],
		];

      /*  if($params['sort'] == 'client_id' || $params['sort'] == '-client_id'){
			/*$dataProvider->sort->attributes['client_id'] = [
    			'asc' => ["client_case_name" => SORT_ASC],
    			'desc' => ["client_case_name" => SORT_DESC],
			];*/
		/*}
        /*$dataProvider->sort->attributes['client_id'] = [
            'asc' => ['tbl_client.client_name' => SORT_ASC],
            'desc' => ['tbl_client.client_name' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['client_case_id'] = [
            'asc' => ['tbl_client_case.case_name' => SORT_ASC],
            'desc' => ['tbl_client_case.case_name' => SORT_DESC],
        ];*/

		/*multiselect*/
        if ($params['InvoiceFinalSearch']['id'] != null && is_array($params['InvoiceFinalSearch']['id'])) {
			if(!empty($params['InvoiceFinalSearch']['id'])){
				foreach($params['InvoiceFinalSearch']['id'] as $k=>$v){
					if($v=='ALL'){ //  || strpos($v,",") !== false
						unset($params['InvoiceFinalSearch']['id']);
					}
				}
			}
		}
		if ($params['InvoiceFinalSearch']['client_id'] != null && is_array($params['InvoiceFinalSearch']['client_id'])) {
			if(!empty($params['InvoiceFinalSearch']['client_id'])){
				foreach($params['InvoiceFinalSearch']['client_id'] as $k=>$v){
					if($v=='ALL'){ //  || strpos($v,",") !== false
						unset($params['InvoiceFinalSearch']['client_id']);
					}
				}
			}
		}
		if ($params['InvoiceFinalSearch']['client_case_id'] != null && is_array($params['InvoiceFinalSearch']['client_case_id'])) {
                    if(!empty($params['InvoiceFinalSearch']['client_case_id'])){
                        foreach($params['InvoiceFinalSearch']['client_case_id'] as $k=>$v){
                            if($v=='ALL'){ //  || strpos($v,",") !== false
                                unset($params['InvoiceFinalSearch']['client_case_id']);
                            }
                        }
                    }
		}
		if ($params['InvoiceFinalSearch']['closed_date'] != null && is_array($params['InvoiceFinalSearch']['closed_date'])) {
                    if(!empty($params['InvoiceFinalSearch']['closed_date'])){
                        foreach($params['InvoiceFinalSearch']['closed_date'] as $k=>$v){
                            if($v=='ALL'){ //  || strpos($v,",") !== false
                                unset($params['InvoiceFinalSearch']['closed_date']);
                            }
                        }
                    }
		}
		if ($params['InvoiceFinalSearch']['closed_by'] != null && is_array($params['InvoiceFinalSearch']['closed_by'])) {
                    if(!empty($params['InvoiceFinalSearch']['closed_by'])){
                        foreach($params['InvoiceFinalSearch']['closed_by'] as $k=>$v){
                            if($v=='ALL'){ //  || strpos($v,",") !== false
                                unset($params['InvoiceFinalSearch']['closed_by']);
                            }
                        }
                    }
		}

		if(isset($params['InvoiceFinalSearch']['totalinvoiceamt'])){
                    $params['InvoiceFinalSearch']['totalinvoiceamt'] = str_replace( array('%','!','@','#','$','&','*','(',')','-','_','+','=','{','}','[',']',',','<','>','/','?',':',';','"','\''), '', $params['InvoiceFinalSearch']['totalinvoiceamt']);
			$query->andFilterWhere([$function => $params['InvoiceFinalSearch']['totalinvoiceamt']]);
			//$where.= " AND ".$function." = ".$params['InvoiceFinalSearch']['totalinvoiceamt'];
		}

		/*multiselect*/
        $this->load($params);

//        if (!$this->validate()) {
//            // uncomment the following line if you do not want to return any records when validation fails
//            // $query->where('0=1');
//            return $dataProvider;
//        }

        // grid filtering conditions
        $query->andFilterWhere([
   //         'id' => $this->id,
   //         'client_id' => $this->client_id,
            'display_by' => $this->display_by,
   //         'total' => $this->total,
            'has_accum_cost' => $this->has_accum_cost,
            'created_by' => $this->created_by,
   //         'created_date' => $this->created_date,
            'modified_by' => $this->modified_by,
            'modified_date' => $this->modified_date
        ]);
//				$where.=" AND display_by = ".$this->display_by." AND has_accum_cost = ".$this->has_accum_cost." AND created_by = ".$this->created_by." AND modified_by = ".$this->modified_by." AND modified_date = ".$this->modified_date;
        $UTCtimezoneOffset = (new Options)->getOffsetOfTimeZone();
        $UserSettimezoneOffset = (new Options)->getOffsetOfTimeZone($_SESSION['usrTZ']);

      /*  if(isset($params['InvoiceFinalSearch']['total']) && $params['InvoiceFinalSearch']['total']!='' && $params['InvoiceFinalSearch']['total']!='ALL'){
            $total_val = explode(" ",$params['InvoiceFinalSearch']['total']);
            $query->andFilterWhere(['like','total',$total_val[1]]);
						$where.=" AND total LIKE  $total_val[1]";
        }*/

        if(isset($params['InvoiceFinalSearch']['id']) && $params['InvoiceFinalSearch']['id']!='' && $params['InvoiceFinalSearch']['id']!='ALL'){
            $query->andFilterWhere(['tbl_invoice_final.id' => $params['InvoiceFinalSearch']['id']]);
						$where.=" AND tbl_invoice_final.id IN (".implode(",",$params['InvoiceFinalSearch']['id']).")";
        }
				if(isset($params['InvoiceFinalSearch']['closed_by']) && $params['InvoiceFinalSearch']['closed_by']!='' && $params['InvoiceFinalSearch']['closed_by']!='ALL'){
            $query->andWhere("tbl_invoice_final.closed_by IN (".implode(',',$params['InvoiceFinalSearch']['closed_by']).")");
						$where.=" AND tbl_invoice_final.closed_by IN (".implode(",",$params['InvoiceFinalSearch']['closed_by']).")";
        }
        /*if((isset($params['InvoiceFinalSearch']['client_id']) && $params['InvoiceFinalSearch']['client_id']!='') || (isset($params['InvoiceFinalSearch']['client_case_id']) && $params['InvoiceFinalSearch']['client_case_id']!='')){
			$query->select(['DISTINCT(tbl_invoice_final.id)','tbl_invoice_final.*', "CONCAT(tc.client_name,' - ',tcc.case_name) as client_case_name",$function.' as totalinvoiceamt','('.$case_sql.') as case_name','('.$client_sql.') as client_name']);
			$query->join('INNER JOIN','tbl_invoice_final_billing as tb','tbl_invoice_final.id = tb.invoice_final_id');
            $query->join('INNER JOIN','tbl_tasks_units_billing as tu','tu.id = tb.billing_unit_id');
            $query->join('INNER JOIN','tbl_tasks_units as taskunit','taskunit.id = tu.tasks_unit_id');
            $query->join('INNER JOIN','tbl_tasks as t','t.id = taskunit.task_id');
            $query->join('INNER JOIN','tbl_client_case as tcc','tcc.id = t.client_case_id');
            $query->join('INNER JOIN','tbl_client as tc','tc.id = tcc.client_id');
		}*/

        if(isset($params['InvoiceFinalSearch']['client_id']) && $params['InvoiceFinalSearch']['client_id']!='' && $params['InvoiceFinalSearch']['client_id']!='ALL')
        {
            $query->andWhere("tbl_invoice_final.client_id IN (".implode(',',$params['InvoiceFinalSearch']['client_id']).")");
						$where .= " AND tbl_invoice_final.client_id IN (".implode(',',$params['InvoiceFinalSearch']['client_id']).")";
        }
         if(isset($params['InvoiceFinalSearch']['client_case_id']) && $params['InvoiceFinalSearch']['client_case_id']!='' )
        {
            $query->andWhere("tbl_invoice_final.client_case_id IN (".implode(',',$params['InvoiceFinalSearch']['client_case_id']).")");
						$where .= " AND tbl_invoice_final.client_case_id IN (".implode(',',$params['InvoiceFinalSearch']['client_case_id']).")";
        }
        /*if(isset($params['InvoiceFinalSearch']['created_date']) && $params['InvoiceFinalSearch']['created_date']!=''){
            $start_date=date('Y-m-d',strtotime($params['InvoiceFinalSearch']['created_date']));
            if (DB_TYPE == 'sqlsrv'){
                $datesql = "Cast(switchoffset(todatetimeoffset(Cast(tbl_invoice_final.created_date as datetime), '+00:00'), '{$UserSettimezoneOffset}') as date)";
            } else {
                $datesql = "DATE_FORMAT(CONVERT_TZ(tbl_invoice_final.created_date,'{$UTCtimezoneOffset}','{$UserSettimezoneOffset}'),'%Y-%m-%d')";
            }
            //echo $datesql;die;
            $query->andFilterWhere(["$datesql" => $start_date]);
        }*/
        if(isset($params['InvoiceFinalSearch']['created_date']) && $params['InvoiceFinalSearch']['created_date']!=""){
			$created_duedate=explode("-",$params['InvoiceFinalSearch']['created_date']);
			$created_duedate_start=explode("/",trim($created_duedate[0]));
			$created_duedate_end=explode("/",trim($created_duedate[1]));
			$created_duedate_duedate_s=$created_duedate_start[2]."-".$created_duedate_start[0]."-".$created_duedate_start[1];
			$created_duedate_duedate_e=$created_duedate_end[2]."-".$created_duedate_end[0]."-".$created_duedate_end[1];
			if (Yii::$app->db->driverName == 'mysql'){
        		$where_date_query ="DATE_FORMAT( CONVERT_TZ(tbl_invoice_final.created_date,'+00:00','{$timezoneOffset}'), '%Y-%m-%d')";
        	}else{
        		$where_date_query = "CAST(switchoffset(todatetimeoffset(Cast(tbl_invoice_final.created_date as datetime), '+00:00'), '{$timezoneOffset}') as date) ";
    		}
    		$query->andWhere(" $where_date_query >= '$created_duedate_duedate_s' AND $where_date_query  <= '$created_duedate_duedate_e' ");
				$where .= " AND ($where_date_query >= '$created_duedate_duedate_s' AND $where_date_query <= '$created_duedate_duedate_e')";
        }

				if(isset($params['InvoiceFinalSearch']['closed_date']) && $params['InvoiceFinalSearch']['closed_date']!=""){
					$closed_date=explode("-",$params['InvoiceFinalSearch']['closed_date']);
					$closed_date_start=explode("/",trim($closed_date[0]));
					$closed_date_end=explode("/",trim($closed_date[1]));
					$closed_date_startdate=$closed_date_start[2]."-".$closed_date_start[0]."-".$closed_date_start[1];
					$closed_date_enddate=$closed_date_end[2]."-".$closed_date_end[0]."-".$closed_date_end[1];
					if (Yii::$app->db->driverName == 'mysql'){
								$where_date_query ="DATE_FORMAT( CONVERT_TZ(tbl_invoice_final.closed_date,'+00:00','{$timezoneOffset}'), '%Y-%m-%d')";
							}else{
								$where_date_query = "CAST(switchoffset(todatetimeoffset(Cast(tbl_invoice_final.closed_date as datetime), '+00:00'), '{$timezoneOffset}') as date) ";
						}
						$query->andWhere(" $where_date_query >= '$closed_date_startdate' AND $where_date_query  <= '$closed_date_enddate' ");
						$where .= " AND ($where_date_query >= '$closed_date_startdate' AND $where_date_query <= '$closed_date_enddate')";
				}

				if (isset($params['type']) && $params['type'] == 'all' && ($params['data_mode'] == 'bulk_close_invoices' || $params['data_mode'] == 'bulk_reopen_invoices') && isset($params['data_mode'])) {
	               $rawSqlQuery = "SELECT tbl_invoice_final.id,tbl_invoice_final.client_id,client_case_id,display_by,has_accum_cost,tbl_invoice_final.created_by,tbl_invoice_final.modified_by,created_date,modified_date,tbl_client_case.case_name as case_name,tbl_client.client_name as client_name,$function as totalinvoiceamt FROM tbl_invoice_final LEFT JOIN tbl_client ON tbl_client.id = tbl_invoice_final.client_id LEFT JOIN tbl_client_case ON tbl_client_case.id = tbl_invoice_final.client_case_id WHERE $where";

	                $allResults = Yii::$app->db->createCommand($rawSqlQuery)->queryAll();
	                return $allResults;
	            }
       /* echo '<pre>';
        print_r($params);die;*/

        return $dataProvider;
    }

    /**
     * Creates data provider instance with searchFilter query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function searchFilter($params)
    {
        $query = InvoiceFinal::find()->orderBy('id DESC');

        // add conditions that should always apply here
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $this->load($params);
         $userId = Yii::$app->user->identity->id;
		$roleId = Yii::$app->user->identity->role_id;

		if($roleId!=0){
			  $clientCaseSql = "SELECT DISTINCT tps.client_case_id  FROM tbl_project_security tps LEFT JOIN tbl_client_case ON tps.client_case_id = tbl_client_case.id LEFT JOIN tbl_client ON tps.client_id = tbl_client.id WHERE tps.user_id = $userId  AND tps.team_id=0";
			  $clientsSql = "SELECT DISTINCT tpc.client_id  FROM tbl_project_security tpc LEFT JOIN tbl_client_case ON tpc.client_case_id = tbl_client_case.id LEFT JOIN tbl_client ON tpc.client_id = tbl_client.id WHERE tpc.user_id = $userId  AND tpc.team_id=0";
			  $query->orWhere("tbl_invoice_final.client_case_id IN ($clientCaseSql)");
			  $query->orWhere("tbl_invoice_final.client_id IN ($clientsSql) AND tbl_invoice_final.client_case_id = 0");
		}
    /*    if($params['field']=='total'){
            if(isset($params['q']) && $params['q']!='' && $params['q']!="All"){
                $query->andFilterWhere(['total' => $params['q']]);
            }
						$query->andWhere("tbl_invoice_final.is_closed = ".$params['is_closed']);
            $dataProvider = ArrayHelper::map($query->all(),function($model, $defaultValue){
                return '$ '.$model->total;
            },function($model, $defaultValue){
                return '$ '.$model->total;
            });
        }*/

        if($params['field']=='id'){
            $query->select('id');
    		if(isset($params['q']) && $params['q']!=""){
    			$query->where(['like','id', $params['q'].'%',false]);
					$query->andWhere("tbl_invoice_final.is_closed = ".$params['is_closed']);
    			$query->orderBy('id');
    			$dataProvider = ArrayHelper::map($query->all(),'id','id');
    		}else{
    			$dataProvider = ArrayHelper::map($query->all(),'id','id'); //JoinWith Error
    		}
            /*if(isset($params['q']) && $params['q']!='' && $params['q']!="All"){
                $query->andFilterWhere(['tbl_invoice_final.id' => $params['q']]);
            }
            $dataProvider = ArrayHelper::map($query->all(),'id','id');*/
        }

        if($params['field']=='client_id')
        {
		    $query->select(['tbl_invoice_final.*','tc.client_name','tcc.case_name']);
            $query->join('INNER JOIN','tbl_invoice_final_billing as tb','tbl_invoice_final.id = tb.invoice_final_id');
            $query->join('INNER JOIN','tbl_tasks_units_billing as tu','tu.id = tb.billing_unit_id');
            $query->join('INNER JOIN','tbl_tasks_units as taskunit','taskunit.id = tu.tasks_unit_id');
            $query->join('INNER JOIN','tbl_tasks as t','t.id = taskunit.task_id');
            $query->join('INNER JOIN','tbl_client_case as tcc','tcc.id = t.client_case_id');
            $query->join('INNER JOIN','tbl_client as tc','tc.id = tcc.client_id');
            $query->orderBy('tc.client_name');
            if(isset($params['q']) && $params['q']!='' && $params['q']!="All"){
			    $query->andFilterWhere(['like',"tc.client_name", $params['q']]);
			}
			$query->andWhere("tbl_invoice_final.is_closed = ".$params['is_closed']);
            $dataProvider = ArrayHelper::map($query->all(),function($model, $defaultValue){
                return html_entity_decode($model->client_id);
            },function($model, $defaultValue){
                return html_entity_decode($model->client_name);
            });
        }
        if($params['field']=='client_case_id')
        {
		    $query->select(['tbl_invoice_final.*','tc.client_name','tcc.case_name']);
            $query->join('INNER JOIN','tbl_invoice_final_billing as tb','tbl_invoice_final.id = tb.invoice_final_id');
            $query->join('INNER JOIN','tbl_tasks_units_billing as tu','tu.id = tb.billing_unit_id');
            $query->join('INNER JOIN','tbl_tasks_units as taskunit','taskunit.id = tu.tasks_unit_id');
            $query->join('INNER JOIN','tbl_tasks as t','t.id = taskunit.task_id');
            $query->join('INNER JOIN','tbl_client_case as tcc','tcc.id = t.client_case_id');
            $query->join('INNER JOIN','tbl_client as tc','tc.id = tcc.client_id');
            $query->orderBy('tcc.case_name');
            if(isset($params['q']) && $params['q']!='' && $params['q']!="All"){
			    $query->andFilterWhere(['like',"tcc.case_name", $params['q']]);
			}
						$query->andWhere("tbl_invoice_final.is_closed = ".$params['is_closed']);
            $dataProvider = ArrayHelper::map($query->all(),function($model, $defaultValue){
                return html_entity_decode($model->client_case_id);
            },function($model, $defaultValue){
                return html_entity_decode($model->case_name);
            });
            unset($dataProvider[0]);
        }

				if($params['field']=='closed_by')
        {
		    	$query->select(['tbl_invoice_final.*','tu.usr_first_name','tu.usr_lastname']);
            $query->join('INNER JOIN','tbl_user as tu','tu.id = tbl_invoice_final.closed_by');
            $query->orderBy('tu.usr_first_name');
            if(isset($params['q']) && $params['q']!='' && $params['q']!="All"){
			    		$query->andFilterWhere(['like',"tu.usr_first_name", $params['q']]);
							$query->andFilterWhere(['like',"tu.usr_lastname", $params['q']]);
						}
					$query->andWhere("tbl_invoice_final.is_closed = ".$params['is_closed']);
          $dataProvider = ArrayHelper::map($query->all(),function($model, $defaultValue){
                return html_entity_decode($model->closed_by);
            },function($model, $defaultValue){
                return html_entity_decode($model->usr_first_name." ".$model->usr_lastname);
            });
        }

        return array('ALL' => 'ALL') + $dataProvider;
    }
}
