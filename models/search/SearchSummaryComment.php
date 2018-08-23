<?php

namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\SummaryComment;

/**
 * SearchSummaryComment represents the model behind the search form about `app\models\SummaryComment`.
 */
class SearchSummaryComment extends SummaryComment
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['Id', 'parent_id', 'case_id', 'team_id', 'team_loc', 'created_by', 'modified_by'], 'integer'],
            [['comment', 'created', 'modified'], 'safe'],
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
        //echo "<pre>",print_r($params);die;
        $query = SummaryComment::find()->where('parent_id = 0');
        $user_id = Yii::$app->user->identity->id;
        if(isset($params['shraed_teamloc']) && $params['shraed_teamloc']!=""){
            $exp_shraed_teamloc=explode("_",$params['shraed_teamloc']);
            $team_id=$exp_shraed_teamloc[0];
            $team_loc=$exp_shraed_teamloc[1];
            $sql="SELECT comment_id FROM tbl_summary_comments_shared_with where team_id=$team_id and team_loc=$team_loc";
            $query->andWhere('id IN ('.$sql.')');
        }
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' =>['pageSize'=>'-1'],
        ]);
        if(isset($params['case_id']) && $params['case_id']!=0){
            $this->case_id=$params['case_id'];
            if(isset($params['comment'])){
                $sql="SELECT comment_id FROM tbl_summary_comments_read inner join tbl_summary_comments on tbl_summary_comments.Id=comment_id AND tbl_summary_comments.case_id=".$params['case_id']." where user_id=".$user_id; 
                $query->andWhere("id NOT IN ($sql)");
            }
        }
        if(isset($params['team_id']) && $params['team_id']!=0){
            $team_id=$params['team_id'];
            $team_loc=$params['team_loc'];
            if(isset($params['comment'])){
                $sql="SELECT comment_id FROM tbl_summary_comments_read inner join tbl_summary_comments on tbl_summary_comments.Id=comment_id AND tbl_summary_comments.team_id=".$params['team_id']." AND tbl_summary_comments.team_loc=".$params['team_loc']." where user_id=".$user_id; 
                $query->andWhere("id NOT IN ($sql)");
                $query->andWhere('(team_id='.$params['team_id'].' AND team_loc='.$params['team_loc'].') ');
            }else{
            $sql="SELECT comment_id FROM tbl_summary_comments_shared_with where team_id=$team_id and team_loc=$team_loc";
            $query->andWhere('(id IN ('.$sql.') OR (team_id='.$params['team_id'].' AND team_loc='.$params['team_loc'].') )');
            }
        }
        $this->load($params);
        if (!$this->validate()) {
            return $dataProvider;
        }
        // grid filtering conditions
        $query->andFilterWhere([
            'Id' => $this->Id,
            'parent_id' => $this->parent_id,
            'case_id' => $this->case_id,
            'team_id' => $this->team_id,
            'team_loc' => $this->team_loc,
            'created' => $this->created,
            'created_by' => $this->created_by,
            'modified' => $this->modified,
            'modified_by' => $this->modified_by,
        ]);

        $query->andFilterWhere(['like', 'comment', $this->comment]);

        return $dataProvider;
    }
}
