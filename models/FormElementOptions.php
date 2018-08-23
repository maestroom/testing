<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%form_element_options}}".
 *
 * @property integer $id
 * @property integer $form_builder_id
 * @property string $element_option
 * @property integer $is_default
 * @property integer $sort_order
 *
 * @property FormBuilder $formBuilder
 */
class FormElementOptions extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%form_element_options}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['form_builder_id', 'element_option', 'is_default', 'sort_order'], 'required'],
            [['form_builder_id', 'is_default', 'sort_order','remove'], 'integer'],
            [['element_option'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'form_builder_id' => 'Form Builder ID',
            'element_option' => 'Element Option',
            'is_default' => 'Is Default',
            'sort_order' => 'Sort Order',
            'remove' => 'remove'
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
    	if (parent::beforeSave($insert)) {
    		
    		if(!isset($this->remove) OR (isset($this->remove) AND $this->remove == '')) {
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
    public function getFormBuilder()
    {
        return $this->hasOne(FormBuilder::className(), ['id' => 'form_builder_id']);
    }
}
