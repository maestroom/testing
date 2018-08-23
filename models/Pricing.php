<?php

namespace app\models;

use Yii;
use app\models\PricingTemplates;
use app\models\PricingClientsRates;
/**
 * This is the model class for table "{{%pricing}}".
 *
 * @property integer $id
 * @property integer $team_id
 * @property string $pricing_type
 * @property string $price_point
 * @property integer $utbms_code_id
 * @property integer $unit_price_id
 * @property string $pricing_range
 * @property string $description
 * @property string $cust_desc_template
 * @property integer $is_custom
 * @property string $created
 * @property integer $created_by
 * @property string $modified
 * @property integer $modified_by
 * @property integer $accum_cost
 * @property integer $remove
 */
class Pricing extends \yii\db\ActiveRecord
{
	public $pricing_rate = '';
	public $service_task = '';
	public $display_teams = '';
    public $utbmscode = '';
    public $unit_name = '';
    public $pricingteam_name = ''; 
    public $pricing_rates = '';
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%pricing}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['team_id', 'pricing_type', 'price_point', 'unit_price_id', 'is_custom' , 'pricing_rate', 'display_teams_type'], 'required'],
            [['team_id', 'utbms_code_id', 'unit_price_id', 'is_custom', 'created_by', 'modified_by', 'accum_cost', 'remove', 'display_teams_type'], 'integer'],
            [['pricing_type', 'price_point', 'pricing_range', 'description', 'cust_desc_template', 'pricing_rate'], 'string'],
            [['service_task'], 'required','when'=>function($model){ return $model->pricing_type == 0;},'whenClient' => "function (attribute, value) {
				return $('#pricing-pricing_type').val() == 0;
		    }"],
            [['display_teams'],'required','when'=>function($model) {return $model->display_teams_type == 2 && $model->pricing_type == 1; },'whenClient' => "function (attribute, value) {
            	return $('#pricing-pricing_type').val() == 1 && $('input[name=\"Pricing[display_teams_type]\"]:checked').val() == 2;
		    }"],
            [['created', 'modified','display_teams_type', 'display_teams'], 'safe'],
            //[['unit_price_id'], 'exist', 'skipOnError' => true, 'targetClass' => UnitPrice::className(), 'targetAttribute' => ['unit_price_id' => 'id']],
            [['unit_price_id'], 'exist', 'skipOnError' => true, 'targetClass' => Unit::className(), 'targetAttribute' => ['unit_price_id' => 'id']],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'team_id' => 'Team ID',
            'pricing_type' => 'Pricing Type',
        	'display_teams_type' => 'Display Across Teams',
            'price_point' => 'Price Point',
            'utbms_code_id' => 'Utbms Code',
            'unit_price_id' => 'Rate Unit',
            'pricing_range' => 'Pricing Range',
            'description' => 'Description',
            'cust_desc_template' => 'Custom Description',
            'is_custom' => 'Is Custom',
            'accum_cost' => 'Accum Cost',
            'remove' => 'Remove',
        	'created' => 'Created',
            'created_by' => 'Created By',
            'modified' => 'Modified',
            'modified_by' => 'Modified By',
        	'pricing_rate' => 'Rate',
        	'service_task' => 'Service Tasks',
        	'display_teams' => 'Teams'
        ];
    }
    
	/**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
    	if (parent::beforeSave($insert)) {
    		
    		if($this->isNewRecord){
				$this->pricing_range = '';
    			$this->created = date('Y-m-d H:i:s');
    			$this->created_by =Yii::$app->user->identity->id;
    			$this->modified =date('Y-m-d H:i:s');
    			$this->modified_by =Yii::$app->user->identity->id;
    		}else{
    			$this->modified =date('Y-m-d H:i:s');
    			$this->modified_by =Yii::$app->user->identity->id;
    		}
    		// Place your custom code here
    		return true;
    	} else {
    		return false;
    	}
    }
    
    public function chkPricePointExistByClientCaseTeam($clientId,$caseId,$team_id,$service_id,$team_loc=0)
    {
    	$pricing_data = array();
		
    	//$isbillable = " AND tbl_pricing.id IN (SELECT tbl_pricing_service_task.pricing_id FROM tbl_pricing_service_task INNER JOIN tbl_servicetask ON tbl_pricing_service_task.servicetask_id = tbl_servicetask.id WHERE tbl_pricing_service_task.pricing_id=tbl_pricing.id AND tbl_servicetask.billable_item IN (1,2))";
    	
		/*case preffred with location*/
		$clientcasesql = 'SELECT tbl_pricing.id FROM tbl_pricing_templates
		INNER  join  tbl_pricing_templates_ids  on  tbl_pricing_templates_ids.template_id = tbl_pricing_templates.id
		INNER Join tbl_pricing on tbl_pricing.id  = tbl_pricing_templates_ids.pricing_id
		INNER Join tbl_pricing_clientscases on tbl_pricing_clientscases.pricing_id  = tbl_pricing_templates_ids.pricing_id
		INNER  Join tbl_pricing_clientscases_rates on tbl_pricing_clientscases_rates.pricing_clientscases_id = tbl_pricing_clientscases.id
		WHERE template_type=2 and tbl_pricing_clientscases.client_case_id='.$caseId.'  and tbl_pricing.remove = 0 AND tbl_pricing_clientscases_rates.team_loc IN ('.$team_loc.')       
		AND ((1 = CASE WHEN tbl_pricing.pricing_type=1 THEN  
		CASE WHEN ('.$team_id.' IN (SELECT tbl_pricing_display_teams.team_id FROM tbl_pricing_display_teams WHERE tbl_pricing_display_teams.pricing_id=tbl_pricing.id)) OR tbl_pricing.display_teams_type = 1 THEN
     1
    ELSE
     0
    END
   END
   ) OR (
   1 = CASE WHEN '.$service_id.' IN (SELECT  tbl_pricing_service_task.servicetask_id FROM tbl_pricing_service_task WHERE tbl_pricing_service_task.pricing_id=tbl_pricing.id AND tbl_pricing.team_id = '.$team_id.') THEN
     1
   ELSE
     0
   END
  ))';
 
	
     $clientcasenotinsql = "SELECT pricing_id FROM tbl_pricing_clientscases INNER JOIN tbl_pricing_clientscases_rates ON tbl_pricing_clientscases_rates.pricing_clientscases_id = tbl_pricing_clientscases.id WHERE client_case_id=$caseId AND tbl_pricing_templates_ids.pricing_id = tbl_pricing_clientscases.pricing_id AND tbl_pricing_clientscases_rates.team_loc NOT IN (".$team_loc.")";
     $clientcasepricingsql = "SELECT tbl_pricing.id FROM tbl_pricing_templates
  INNER  join  tbl_pricing_templates_ids  on  tbl_pricing_templates_ids.template_id = tbl_pricing_templates.id
     INNER Join tbl_pricing on tbl_pricing.id  = tbl_pricing_templates_ids.pricing_id
     INNER  Join tbl_pricing_rates on tbl_pricing_rates.pricing_id = tbl_pricing.id
     WHERE template_type=2 and tbl_pricing_templates.client_case_id=$caseId  and tbl_pricing.remove = 0       
     AND tbl_pricing_templates_ids.pricing_id NOT IN ($clientcasenotinsql)
     AND ((1 = CASE WHEN tbl_pricing.pricing_type=1 THEN  
    CASE WHEN ($team_id IN (SELECT tbl_pricing_display_teams.team_id FROM tbl_pricing_display_teams WHERE tbl_pricing_display_teams.pricing_id=tbl_pricing.id)) OR tbl_pricing.display_teams_type = 1 THEN
     1
    ELSE
     0
    END
   END
   ) OR (
    1 = CASE WHEN $service_id IN (SELECT  tbl_pricing_service_task.servicetask_id FROM tbl_pricing_service_task WHERE tbl_pricing_service_task.pricing_id=tbl_pricing.id AND tbl_pricing.team_id = $team_id AND tbl_pricing_rates.team_loc IN (".$team_loc.")) THEN
     1
    ELSE
     0
    END
   ))";
     
     $sql="SELECT tbl_pricing.* FROM tbl_pricing  WHERE tbl_pricing.id IN (".$clientcasesql." UNION ALL ".$clientcasepricingsql.")";
     //echo $sql; exit;
     $pricing_data = Yii::$app->db->createCommand($sql)->queryAll();
     //echo "<pre>$sql",print_r($pricing_data),"</pre>";
     $iscasetemplateexist = PricingTemplates::find()->where(['template_type'=>2,'client_case_id'=>$caseId])->count();
     /*client preffred with location*/
     if(empty($pricing_data) && $iscasetemplateexist==0){
      $clientsql = 'SELECT tbl_pricing.id FROM tbl_pricing_templates
   INNER  join  tbl_pricing_templates_ids  on  tbl_pricing_templates_ids.template_id = tbl_pricing_templates.id
      INNER Join tbl_pricing on tbl_pricing.id  = tbl_pricing_templates_ids.pricing_id
      INNER Join tbl_pricing_clients on tbl_pricing_clients.pricing_id  = tbl_pricing_templates_ids.pricing_id
      INNER  Join tbl_pricing_clients_rates on tbl_pricing_clients_rates.pricing_clients_id = tbl_pricing_clients.id
      WHERE template_type=1 and tbl_pricing_clients.client_id='.$clientId.'  and tbl_pricing.remove = 0 AND tbl_pricing_clients_rates.team_loc IN ('.$team_loc.')       
   AND ((1 = CASE WHEN tbl_pricing.pricing_type=1 THEN  
     CASE WHEN ('.$team_id.' IN (SELECT tbl_pricing_display_teams.team_id FROM tbl_pricing_display_teams WHERE tbl_pricing_display_teams.pricing_id=tbl_pricing.id)) OR tbl_pricing.display_teams_type = 1 THEN
      1
     ELSE
      0
     END
    END
    ) OR (
     1 = CASE WHEN '.$service_id.' IN (SELECT  tbl_pricing_service_task.servicetask_id FROM tbl_pricing_service_task WHERE tbl_pricing_service_task.pricing_id=tbl_pricing.id AND tbl_pricing.team_id = '.$team_id.') THEN
      1
     ELSE
      0
     END
    ))';
     
      $clientnotinsql = "SELECT pricing_id FROM tbl_pricing_clients INNER JOIN tbl_pricing_clients_rates ON tbl_pricing_clients_rates.pricing_clients_id = tbl_pricing_clients.id WHERE client_id=$clientId AND tbl_pricing_templates_ids.pricing_id = tbl_pricing_clients.pricing_id AND tbl_pricing_clients_rates.team_loc NOT IN ($team_loc)";
      $clientpricingsql = "SELECT tbl_pricing.id FROM tbl_pricing_templates
   INNER  join  tbl_pricing_templates_ids  on  tbl_pricing_templates_ids.template_id = tbl_pricing_templates.id
      INNER Join tbl_pricing on tbl_pricing.id  = tbl_pricing_templates_ids.pricing_id
      INNER  Join tbl_pricing_rates on tbl_pricing_rates.pricing_id = tbl_pricing.id
      WHERE template_type=1 and tbl_pricing_templates.client_id=$clientId  and tbl_pricing.remove = 0       
      AND tbl_pricing_templates_ids.pricing_id NOT IN ($clientnotinsql)
      AND ((1 = CASE WHEN tbl_pricing.pricing_type=1 THEN  
     CASE WHEN ($team_id IN (SELECT tbl_pricing_display_teams.team_id FROM tbl_pricing_display_teams WHERE tbl_pricing_display_teams.pricing_id=tbl_pricing.id)) OR tbl_pricing.display_teams_type = 1 THEN
      1
     ELSE
      0
     END
    END
    ) OR (
     1 = CASE WHEN $service_id IN (
       SELECT  tbl_pricing_service_task.servicetask_id FROM tbl_pricing_service_task 
       WHERE tbl_pricing_service_task.pricing_id=tbl_pricing.id 
       AND tbl_pricing.team_id = $team_id 
       AND tbl_pricing_rates.team_loc IN ($team_loc)
       ) 
     THEN
      1
     ELSE
      0
     END
    ))";
      
      $sql="SELECT tbl_pricing.* FROM tbl_pricing  WHERE tbl_pricing.id IN (".$clientsql." UNION ALL ".$clientpricingsql.")";
      $pricing_data = Yii::$app->db->createCommand($sql)->queryAll();      
     }
     
     /*team & Shared*/
     
     //$iscasetemplateexist = PricingTemplates::find()->where(['template_type'=>2,'client_case_id'=>$caseId])->count();
     //$isclienttemplateexist = PricingTemplates::find()->where(['template_type'=>1,'client_id'=>$clientId])->count();
     $isclienttemplateexist = Yii::$app->db->createCommand("SELECT COUNT(*) FROM tbl_pricing_templates
INNER JOIN tbl_pricing_templates_ids on tbl_pricing_templates_ids.template_id=tbl_pricing_templates.id
WHERE (template_type=1) AND (client_id=".$clientId.")")->queryScalar();
     
     if(empty($pricing_data) && $iscasetemplateexist == 0 && $isclienttemplateexist == 0){
     //if(empty($pricing_data)){
      $sql = 'SELECT tbl_pricing.id FROM tbl_pricing
      INNER  Join tbl_pricing_rates on tbl_pricing_rates.pricing_id = tbl_pricing.id
   WHERE tbl_pricing.remove = 0       
   AND ((1 = CASE WHEN tbl_pricing.pricing_type=1 THEN  
     CASE WHEN ('.$team_id.' IN (SELECT tbl_pricing_display_teams.team_id FROM tbl_pricing_display_teams WHERE tbl_pricing_display_teams.pricing_id=tbl_pricing.id)) OR tbl_pricing.display_teams_type = 1 THEN
      1
     ELSE
      0
     END
    END
    ) OR (
     1 = CASE WHEN '.$service_id.' IN (SELECT  tbl_pricing_service_task.servicetask_id FROM tbl_pricing_service_task WHERE tbl_pricing_service_task.pricing_id=tbl_pricing.id AND tbl_pricing.team_id = '.$team_id.' AND tbl_pricing_rates.team_loc IN ('.$team_loc.')) THEN
      1
     ELSE
      0
     END
    ))';
      
      $sql="SELECT tbl_pricing.* FROM tbl_pricing  WHERE tbl_pricing.id IN (".$sql.")";
      $pricing_data = Yii::$app->db->createCommand($sql)->queryAll();
     }
     return $pricing_data;
    	
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
	public function getPricingRates()
    {
        return $this->hasMany(PricingRates::className(), ['pricing_id' => 'id']);
    }
    
	/**
     * @return \yii\db\ActiveQuery
     */
	public function getPricingDisplayTeams()
    {
        return $this->hasMany(PricingDisplayTeams::className(), ['pricing_id' => 'id']);
    }
    
	/**
     * @return \yii\db\ActiveQuery
     */
    public function getUnit()
    {
        return $this->hasOne(Unit::className(), ['id' => 'unit_price_id']);
    }
    
	/**
     * @return \yii\db\ActiveQuery
     */
    public function getPricingUtbmsCodes()
    {
        return $this->hasOne(PricingUtbmsCodes::className(), ['id' => 'utbms_code_id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
	public function getPricingServiceTask()
    {
        return $this->hasMany(PricingServiceTask::className(), ['pricing_id' => 'id']);
    }
    
	/**
     * @return \yii\db\ActiveQuery
     */
	public function getPricingTemplatesIds()
    {
        return $this->hasMany(PricingTemplatesIds::className(), ['pricing_id' => 'id']);
    }
    
	/**
     * @return \yii\db\ActiveQuery
     */
	public function getTeam()
    {
        return $this->hasOne(Team::className(), ['id' => 'team_id']);
    }
    
    /**
     *  To get Pricing Rates by Pricing ID with Team Location.
     *  @param pricing_id int
     *  @param unit_price_name string
     *  @param pricing_range string
     *  @param pricing_type int
     */
    public function getPriceRatesByLoc($pricing_id, $unit_price_name, $range="", $pricing_type="")
    {
    	$internalRate="";
		if($pricing_type==1)
		{
            $pricing_rates = PricingRates::find()->where(['pricing_id'=>$pricing_id])->one();
			$internalRate="";
			if($pricing_rates->rate_amount!=0)
			{
				$internalRate="$".number_format($pricing_rates->rate_amount, 2, '.', ',')." ".$unit_price_name." ".$range;
			}
		}
		else 
		{
			$internalRate = '';
			$pricing_rates = PricingRates::find()->select(['tbl_pricing_rates.id','team_loc','rate_type','rate_amount','tier_from','tier_to','tbl_teamlocation_master.team_location_name as teamlocation_name'])->joinWith(['teamlocationMaster'],false)->where(['pricing_id'=>$pricing_id])->all();
			if(!empty($pricing_rates))
			{
				foreach ($pricing_rates as $rate) {
					if($internalRate!=''){
						$internalRate.="<br/>";
					}
					//$internalRate.=$rate->teamlocationMaster->team_location_name." - ";
                    $internalRate.=$rate->teamlocation_name." - ";
                    if($rate->rate_type == 1) {
						if($internalRate!='')
						{
							$internalRate.="$".number_format($rate->rate_amount, 2, '.', ',')." ".$unit_price_name." ".$range;
						}
						else
						{
							$internalRate="$".number_format($rate->rate_amount, 2, '.', ',')." ".$unit_price_name." ".$range;
						}
					} else {
						if($internalRate!='')
						{
							$internalRate.="$".number_format($rate->rate_amount, 2, '.', ',')." ".$unit_price_name;
							$internalRate.=" (".$rate->tier_from." - ".$rate->tier_to.")"; 
						}
						else
						{
							$internalRate="$".number_format($rate->rate_amount, 2, '.', ',')." ".$unit_price_name;
							$internalRate.=" (".$rate->tier_from." - ".$rate->tier_to.")";
						}
					}
				}
			}
		}    
		
    	return $internalRate;
    }
    
	/**
     *  To get Pricing Client Rates by Pricing ID with Team Location.
     *  @param pricing_id int
     *  @param unit_price_name string
     *  @param pricing_range string
     *  @param pricing_type int
     */
    public function getPriceClientRatesByLoc($pricing_id, $unit_price_name, $range="", $pricing_type="", $client_id)
    {
    	$internalRate="";
		/*if($pricing_type==1)
		{
			$pricing_rates = PricingClientsRates::find()->innerJoinWith(['pricingClients'=>function(\yii\db\ActiveQuery $pricingClients) use($pricing_id,$client_id){$pricingClients->where(['pricing_id'=>$pricing_id,'client_id'=>$client_id]);}])->one();
			$internalRate="";
			if($pricing_rates->rate_amount!=0)
			{
				$internalRate="$".number_format($pricing_rates->rate_amount, 2, '.', ',')." ".$unit_price_name." ".$range;
			}
		}
		else 
		{*/
			$internalRate = '';
			$pricing_rates = PricingClientsRates::find()->innerJoinWith(['pricingClients'=>function(\yii\db\ActiveQuery $pricingClients) use($pricing_id,$client_id){$pricingClients->where(['pricing_id'=>$pricing_id,'client_id'=>$client_id]);}])->all();
			//echo "<pre>$pricing_id : ",print_r($pricing_rates),"</pre>";die;
			if(!empty($pricing_rates))
			{
				foreach ($pricing_rates as $rate) {
					if($internalRate!=''){
						$internalRate.="<br/>";
					}
					$internalRate.=$rate->teamlocationMaster->team_location_name." - ";
					if($rate->rate_type == 1) {
						if($internalRate!='')
						{
							$internalRate.="$".number_format($rate->rate_amount, 2, '.', ',')." ".$unit_price_name." ".$range;
						}
						else
						{
							$internalRate="$".number_format($rate->rate_amount, 2, '.', ',')." ".$unit_price_name." ".$range;
						}
					} else {
						if($internalRate!='')
						{
							$internalRate.="$".number_format($rate->rate_amount, 2, '.', ',')." ".$unit_price_name;
							$internalRate.=" (".$rate->tier_from." - ".$rate->tier_to.")"; 
						}
						else
						{
							$internalRate="$".number_format($rate->rate_amount, 2, '.', ',')." ".$unit_price_name;
							$internalRate.=" (".$rate->tier_from." - ".$rate->tier_to.")";
						}
					}
				}
			}
		//}    
		
    	return $internalRate;
    }
    
	/**
     *  To get Pricing ClientCase Rates by Pricing ID with Team Location.
     *  @param pricing_id int
     *  @param unit_price_name string
     *  @param pricing_range string
     *  @param pricing_type int
     */
    public function getPriceClientCaseRatesByLoc($pricing_id, $unit_price_name, $range="", $pricing_type="", $case_id)
    {
    	$internalRate="";
		/*if($pricing_type==1)
		{
			$pricing_rates = PricingClientscasesRates::find()->innerJoinWith(['pricingClientscases'=>function(\yii\db\ActiveQuery $pricingClientscases) use($pricing_id,$case_id){$pricingClientscases->where(['pricing_id'=>$pricing_id,'client_case_id'=>$case_id]);}])->all();
			$internalRate="";
			if($pricing_rates->rate_amount!=0)
			{
				$internalRate="$".number_format($pricing_rates->rate_amount, 2, '.', ',')." ".$unit_price_name." ".$range;
			}
		}
		else 
		{*/
			$internalRate = '';
			$pricing_rates = PricingClientscasesRates::find()->innerJoinWith(['pricingClientscases'=>function(\yii\db\ActiveQuery $pricingClientscases) use($pricing_id,$case_id){$pricingClientscases->where(['pricing_id'=>$pricing_id,'client_case_id'=>$case_id]);}])->all();
			if(!empty($pricing_rates))
			{
				foreach ($pricing_rates as $rate) {
					if($internalRate!=''){
						$internalRate.="<br/>";
					}
					$internalRate.=$rate->teamlocationMaster->team_location_name." - ";
					if($rate->rate_type == 1) {
						if($internalRate!='')
						{
							$internalRate.="$".number_format($rate->rate_amount, 2, '.', ',')." ".$unit_price_name." ".$range;
						}
						else
						{
							$internalRate="$".number_format($rate->rate_amount, 2, '.', ',')." ".$unit_price_name." ".$range;
						}
					} else {
						if($internalRate!='')
						{
							$internalRate.="$".number_format($rate->rate_amount, 2, '.', ',')." ".$unit_price_name;
							$internalRate.=" (".$rate->tier_from." - ".$rate->tier_to.")"; 
						}
						else
						{
							$internalRate="$".number_format($rate->rate_amount, 2, '.', ',')." ".$unit_price_name;
							$internalRate.=" (".$rate->tier_from." - ".$rate->tier_to.")";
						}
					}
				}
			}
		//}    
		
    	return $internalRate;
    }
}
