<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "{{%product_updates}}".
 *
 * @property integer $id
 * @property string $version
 * @property string $date
 * @property integer $is_updated
 * @property integer $order
 */
class ProductUpdates extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%product_updates}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['version', 'date', 'is_updated', 'order'], 'required'],
            [['version'], 'string'],
            [['date'], 'safe'],
            [['is_updated', 'order'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'version' => 'Version',
            'date' => 'Date',
            'is_updated' => 'Installed',
            'order' => 'Order',
        ];
    }
    public static function setCustomVersion() {
        $_SESSION['product_custom_version'] = ProductUpdates::find()->select(['version'])->orderBy("id desc")->one()->version;
		//Settings::find()->select(['fieldvalue'])->where("field = 'modulepage_logo'")->one()->fieldvalue;
    }
	public static function getCustomVersion() {
        if (!isset($_SESSION['product_custom_version'])) {
            self::setCustomVersion();
        }
        return $_SESSION['product_custom_version'];
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
        $query = ProductUpdates::find()->where(['is_updated'=>1])->orderBy(['id'=>SORT_DESC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'version' => $this->version,
        ]);

        $query->andFilterWhere(['like', 'version', $this->version]);

        return $dataProvider;
    }
}
