<?php

namespace app\models;

use Yii;
use yii\helpers\HtmlPurifier;
/**
 * This is the model class for table "{{%client_case_summary}}".
 *
 * @property integer $id
 * @property integer $client_case_id
 * @property string $summary
 * @property string $summary_note
 * @property integer $created_by
 * @property string $created
 * @property integer $modified_by
 * @property string $modified
 */
class ClientCaseSummary extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%client_case_summary}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['client_case_id',], 'required'],
            [['client_case_id', 'created_by', 'modified_by'], 'integer'],
            [['summary', 'summary_note'], 'string'],
            [['created', 'modified'], 'safe'],
            [['client_case_id'], 'exist', 'skipOnError' => true, 'targetClass' => ClientCase::className(), 'targetAttribute' => ['client_case_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'client_case_id' => 'Client Case ID',
            'summary' => 'Summary',
            'summary_note' => 'Summary Note',
            'created_by' => 'Created By',
            'created' => 'Created',
            'modified_by' => 'Modified By',
            'modified' => 'Modified',
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
    		$summary=HtmlPurifier::process($this->summary);
    		$this->summary = html_entity_decode(strip_tags($summary));
    		$summary_note=HtmlPurifier::process($this->summary_note);
    		$this->summary_note = html_entity_decode(strip_tags($summary_note));
    		// Place your custom code here
    		return true;
    	} else {
    		return false;
    	}
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getModifiedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'modified_by']);
    }
}
