<?php
namespace app\components;

use yii\base\Widget;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;
use yii\helpers\Json;
use app\assets\DataTableAsset;


class DatatableWidget extends Widget{
	/* public $columnDefs;
	public $pagingType;
	public $dom;
	public $order;
	public $sImgDirPath; */
	
	public $id;
	public $tableOptions = [];
	protected $_options = [];
	
	public function init(){
		parent::init();
		// add your logic here
		DataTableAsset::register($this->getView());
		$this->initColumns();
	}
	public function run()
	{
		$id = isset($this->id) ? $this->id : $this->getId();
		echo Html::beginTag('table', ArrayHelper::merge(['id' => $id], $this->tableOptions));
		echo Html::beginTag('thead');
		echo Html::endTag('thead');
		echo Html::beginTag('tbody');
		echo Html::endTag('tbody');
		echo Html::endTag('table');
		$this->getView()->registerJs('jQuery("#' . $id . '").DataTable(' . Json::encode($this->getParams()) . ');');
	}
	public function getId($autoGenerate = true){
		
		return uniqid();
	}
	protected function getParams()
	{
		return $this->_options;
	}
	protected function initColumns()
    {
        if (isset($this->_options['columns'])) {
            foreach ($this->_options['columns'] as $key => $value) {
                if (is_string($value)) {
                    $this->_options['columns'][$key] = ['data' => $value, 'title' => Inflector::camel2words($value)];
                }
                if (isset($value['type'])) {
                    if ($value['type'] == 'link') {
                        $value['class'] = LinkColumn::className();
                    }
                }
                if (isset($value['class'])) {
                    $column = \Yii::createObject($value);
                    $this->_options['columns'][$key] = $column;
                }
            }
        }
    }

    public function __set($name, $value)
    {
        return $this->_options[$name] = $value;
    }

    public function __get($name)
    {
        return isset($this->_options[$name]) ? $this->_options[$name] : null;
    }	
}