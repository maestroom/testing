<?php

namespace app\models;

use Yii;
use app\models\Pricing;
use app\models\TaxClassPricing;
use app\models\TaxCode;

/**
 * This is the model class for table "tbl_tax_class".
 *
 * @property integer $id
 * @property string $class_name
 * @property string $tax_class_desc
 * @property integer $pricepoint
 * @property string $created
 * @property integer $created_by
 * @property string $modified
 * @property integer $modified_by
 *
 * @property TaxClassPricing[] $taxClassPricings
 * @property TaxCode[] $taxCodes
 */
class TaxClass extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_tax_class';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['class_name', 'pricepoint'], 'required'],
            [['pricepoint', 'created_by', 'modified_by'], 'integer'],
            [['created', 'modified'], 'safe'],
            [['class_name'], 'string'],
            [['tax_class_desc'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'class_name' => 'Class Name',
            'tax_class_desc' => 'Tax Class Desc',
            'pricepoint' => 'Pricepoint',
            'created' => 'Created',
            'created_by' => 'Created By',
            'modified' => 'Modified',
            'modified_by' => 'Modified By',
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
    	if (parent::beforeSave($insert)) {
    		if($this->isNewRecord){
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
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTaxClassPricings()
    {
        return $this->hasMany(TaxClassPricing::className(), ['tax_class_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTaxCodes()
    {
        return $this->hasMany(TaxCode::className(), ['tax_class_id' => 'id']);
    }
    
    /**
     * Get all select price points to display in grid's title in tax classes
     * @return
     */
     public function getpricepointslink($id,$pricepoint)
     {
		 $query = 'select tp.* from tbl_tax_class as t INNER JOIN tbl_tax_class_pricing as tc ON tc.tax_class_id = t.id
		 INNER JOIN tbl_pricing as tp ON tp.id = tc.price_id WHERE t.id = '.$id;
		 $pricing = \Yii::$app->db->createCommand($query)->queryAll();
		 // price point data
		 $pricePoint_data   = array();
		 if (!empty($pricing)) {
			foreach ($pricing as $pp) {
				if ($pp['pricing_type'] == 1)
					$pricePoint_data[$pp['id']] = "Misc - " . $pp['price_point'];
				else {
					$team_service = Team::findOne($pp['team_id']);
					$pricePoint_data[$pp['id']] = $team_service['team_name'] . " - " . $pp['price_point'];
				}
			}
		}
        asort($pricePoint_data); // sorting
        $pricepoint_val = implode("\n",$pricePoint_data); // convert array to string
        $html = '<span title="'.$pricepoint_val.'">'.$pricepoint.'</span>'; // make a href link
        return $html;
	 }
	 
	 /**
	  * To get Tax Details by pricing_id & client_id  
	  * @param pricing_id (int)
	  * @param client_id (int)
	  */
	 public function getTaxAndClassByPricingClientId($pricing_id, $client_id)
	 {
	 	$taxClassPricing = TaxClassPricing::find()->select(['tax_class_id'])->where(['price_id'=>$pricing_id]);
		
		$taskCodesData = TaxCode::find()
		->select(['tbl_tax_code.id','tax_code','tax_rate'])
		->innerJoinWith([
			'taxCodeClients' => function(\yii\db\ActiveQuery $taxcodeclient) use($client_id)
			{
				$taxcodeclient->where(['client_id'=>$client_id]);
			}
		])
		->where(['in','tax_class_id',$taxClassPricing])
		->all();
		
		$data=array();
		if(!empty($taskCodesData))
		{
			foreach ($taskCodesData as  $taskcode)
			{
				$data[]=array('tax_code_id'=>$taskcode->id, 'code'=>$taskcode->tax_code, 'rate'=>$taskcode->tax_rate);
			}
		}
		return $data;
	 }
}
