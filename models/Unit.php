<?php

namespace app\models;

use Yii;
use kotchuprik\sortable\behaviors;
/**
 * This is the model class for table "{{%unit}}".
 *
 * @property integer $id
 * @property string $unit_name
 * @property integer $remove
 * @property double $est_size
 *
 * @property EvidenceContents[] $evidenceContents
 */
class Unit extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%unit}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['unit_name'], 'required'],
            [['unit_name'], 'string'],
            [['unit_name'], 'unique','message'=>'You cannot add duplicate Units.','targetAttribute' => 'unit_name', 'filter' => ['remove' => 0]],
            [['remove'], 'integer'],
            [['default_unit','sort_order','is_hidden'], 'safe'],
        ];
    }

	/**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'unit_name' => Yii::t('app', 'Unit Name'),
            'remove' => Yii::t('app', 'Remove'),
            'default_unit' => Yii::t('app', 'Default Unit'),
            'sort_order' => Yii::t('app', 'Sort Order'),
            'is_hidden'=> Yii::t('app','Is Hidden?'),
        ];
    }

	/**
     * @sortable behaviors
     */
    public function behaviors()
    {
    	return [
			'sortable' => [
				'class' => \kotchuprik\sortable\behaviors\Sortable::className(),
				'query' => self::find()->where(['remove'=>0]),
				'orderAttribute'=>'sort_order',
			],
    	];
    }

	/**
     * @inheritdoc
     */
    public function beforeInsert()
    {
		$last = $this->find()->where(['remove'=>0])->orderBy([$this->orderAttribute => SORT_DESC])->limit(1)->one();
		if ($last === null) {
			$this->{$this->orderAttribute} = 1;
		} else {
			$this->{$this->orderAttribute} = $last->{$this->orderAttribute} + 1;
		}
    }

	/**
     * @inheritdoc
     */
    public function beforeSave($insert){
    	if (parent::beforeSave($insert)) {
    		
    		if($this->isNewRecord){
    			if(!isset($this->is_hidden) || $this->is_hidden=='')
					$this->is_hidden = 0;
				if(!isset($this->default_unit) || $this->default_unit=='')
					$this->default_unit = 0;	
				if(!isset($this->remove) || $this->remove=='')
					$this->remove = 0;
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
    public function getEvidenceContents()
    {
        return $this->hasMany(EvidenceContents::className(), ['unit' => 'id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUnitMasters()
    {
        return $this->hasOne(UnitMaster::className(), ['unit_id' => 'id']);
    }
    
    /*
     * Set the Unit Size in Highest Format Units
     * */
    public function formatSizeUnits($total_kbs, $estunittype=0, $isarray = 'no')
    {
		$unit = Unit::find()->joinWith('unitMasters');
		if($estunittype!='' && $estunittype!=0){
			$unit->where(['unit_type'=>$estunittype]);
		}
		$units = $unit->orderBy('unit_size DESC')->all();
		$response = 0;
		if($isarray == 'yes')
			$response = [];
		foreach($units as $unit){
			if(!empty($unit->unitMasters)){
				if($total_kbs >= $unit->unitMasters->unit_size){
					if($isarray == 'yes'){
						$response['size'] = number_format($total_kbs / $unit->unitMasters->unit_size, 0, "", "");
						$response['unit'] = $unit->unit_name;
						$response['unit_id'] = $unit->id;
					} else {
						$response=number_format($total_kbs / $unit->unitMasters->unit_size, 0, "", "").' '.$unit->unit_name;	
					}
					break;
				}
			}
		}
		return $response;
		
    	/*if ($bytes >= 1152921504606840000)
    	{
    		$bytes = number_format($bytes / 1152921504606840000, 0,"","") . ' EB';
    	}
    	else if ($bytes >= 1125899906842620)
    	{
    		$bytes = number_format($bytes / 1125899906842620, 0,"","") . ' PB';
    	}
    	else if ($bytes >= 1099511627776)
    	{
    		$bytes = number_format($bytes / 1099511627776, 0,"","") . ' TB';
    	}
        else if ($bytes >= 1073741824)
        {
            $bytes = number_format($bytes / 1073741824, 0,"","") . ' GB';
        }
        elseif ($bytes >= 1048576)
        {
            $bytes = number_format($bytes / 1048576, 0,"","") . ' MB';
        }
        elseif ($bytes >= 1024)
        {
            $bytes = number_format($bytes / 1024, 0,"","") . ' KB';
        }
        elseif ($bytes > 1)
        {
            $bytes = $bytes . ' bytes';
        }
        elseif ($bytes == 1)
        {
            $bytes = $bytes . ' byte';
        }
        else
        {
            $bytes = '0 bytes';
        }

        return $bytes;*/
	}
}
