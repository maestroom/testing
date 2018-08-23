<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%security_feature}}".
 *
 * @property integer $id
 * @property string $feature_sort
 * @property string $security_feature
 * @property string $description
 *
 * @property RoleSecurity[] $roleSecurities
 */
class SecurityFeature extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%security_feature}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['feature_sort'], 'number'],
            [['security_feature', 'description'], 'required'],
            [['security_feature', 'description'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'feature_sort' => 'Feature Sort',
            'security_feature' => 'Security Feature',
            'description' => 'Description',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRoleSecurities()
    {
        return $this->hasMany(RoleSecurity::className(), ['security_feature_id' => 'id']);
    }
    
    /**
     * Get child
     */
    public function getChlid($feature_sort){
    	$childs = $this->find()->asArray()->where("feature_sort like '".intval($feature_sort).".%'")->andWhere("feature_sort!='".$feature_sort."'")->orderBy('feature_sort ASC')->all();
    	return $chlids;
    }
    
    /**
     * Get All Security Features
     */
    public function getSecurityFeatures(){
    	$securityFeatureList = $this->find()->select(['id','feature_sort','security_feature','description'])->where("feature_sort LIKE '%.000000000%'")->orderBy('feature_sort ASC')->all();
    	$main_ids=array(); $child = array();
    	foreach ($securityFeatureList as $data){
    		$bool = ( !is_int($data->feature_sort) ? (ctype_digit($data->feature_sort)) : true );
    		if($bool){
    			$main_ids[$data->id]=$data->id;
    		}
    	}
    	if(!empty($main_ids)){
    		$securityFeatureList = $this->find()->select(['id','feature_sort','security_feature','description'])->where("id In ('.implode(',',$main_ids).')")->orderBy('id ASC')->all();
    	}
    	$tempOptions = [];
    	foreach($securityFeatureList as $feature){
			//$title[$feature->security_feature]['description'] = $feature->description;
            /*IRT-746*/
            $childs[$feature->security_feature][$feature->id] = $this->find()->asArray()->where("feature_sort like '".intval($feature->feature_sort).".%'")->andWhere("feature_sort!='".$feature->feature_sort."'")->orderBy('feature_sort ASC')->all();		
            //and  feature_sort !='10.03'
            if($feature->security_feature == 'Options - Subscribe to Email Alerts'){
                if(!empty($childs[$feature->security_feature][$feature->id])){
                    foreach($childs[$feature->security_feature][$feature->id] as $keyOp => $valueOp){
                        if(isset($valueOp['security_feature']) && $valueOp['security_feature'] != '')
                            $tempOptions[$keyOp] = $valueOp['security_feature'];       
                    }
                }
                array_multisort($tempOptions, SORT_ASC|SORT_NATURAL|SORT_FLAG_CASE, $childs[$feature->security_feature][$feature->id]);
            }
            $childs[$feature->security_feature][$feature->id]['desc'] = $feature->description;                        
    	}         
    	//echo "<pre>"; print_r($childs); exit;
    	return $childs;
    }
}
