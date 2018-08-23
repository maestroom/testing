<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;


/**
 * This is the model class for table "{{%tax_code}}".
 *
 * @property integer $id
 * @property integer $tax_class_id
 * @property string $tax_code
 * @property string $tax_code_desc
 * @property integer $tax_rate
 * @property string $created
 * @property integer $client
 * @property integer $created_by
 * @property string $modified
 * @property integer $modified_by
 *
 * @property TaxClass $taxClass
 * @property TaxCodeClients[] $taxCodeClients
 */
class TaxCode extends \yii\db\ActiveRecord
{
	public $class_name = '';
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%tax_code}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tax_class_id', 'tax_code', 'tax_rate', 'client'], 'required'],
            [['tax_class_id', 'tax_rate', 'client', 'created_by', 'modified_by'], 'integer'],
            [['created', 'modified','class_name','tax_code_desc'], 'safe'],
            [['tax_code'], 'string'],
            [['tax_code_desc'], 'string'],
            [['tax_class_id'], 'exist', 'skipOnError' => true, 'targetClass' => TaxClass::className(), 'targetAttribute' => ['tax_class_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'tax_class_id' => 'Tax Class ID',
            'tax_code' => 'Tax Code',
            'tax_code_desc' => 'Tax Code Desc',
            'tax_rate' => 'Code Rate',
            'created' => 'Created',
            'client' => 'Client',
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
    public function getTaxClass()
    {
        return $this->hasOne(TaxClass::className(), ['id' => 'tax_class_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTaxCodeClients()
    {
        return $this->hasMany(TaxCodeClients::className(), ['tax_code_id' => 'id']);
    }
    
    /**
     * Get client lists for tax code grid view
     * @return 
     */
     public function getClientListsLink($id, $clientId){
		$query = 'SELECT tc.client_name FROM tbl_tax_code as t INNER JOIN tbl_tax_code_clients as tcc ON t.id = tcc.tax_code_id
		INNER JOIN tbl_client as tc ON tc.id = tcc.client_id WHERE t.id = '.$id;
		$clients = \Yii::$app->db->createCommand($query)->queryAll();
		$arr_client_list = ArrayHelper::map($clients, 'client_name', 'client_name');
		$client_name = implode("\n",$arr_client_list);
		return $html = '<span title="'.$client_name.'">'.$clientId.'</span>';
	 }
}
