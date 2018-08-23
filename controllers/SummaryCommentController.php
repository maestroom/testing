<?php

namespace app\controllers;

use Yii;
use app\models\SummaryComment;
use app\models\SummaryCommentsRead;
use app\models\Mydocument;
use app\models\SummaryCommentsSharedWith;
use app\models\search\SearchSummaryComment;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

/**
 * SummaryCommentController implements the CRUD actions for SummaryComment model.
 */
class SummaryCommentController extends Controller
{
    public $layout = 'mycase';
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all SummaryComment models.
     * @return mixed
     */
    public function actionIndex()
    {
        $case_id = Yii::$app->request->get('case_id',0);
        $comment_id = Yii::$app->request->get('comment_id',0);
        $team_id = Yii::$app->request->get('team_id',0);
        $team_loc = Yii::$app->request->get('team_loc',0);

        $comment = Yii::$app->request->get('comment','');
        $shraed_teamloc= Yii::$app->request->get('shraed_teamloc',0);
        if($team_id!=0){
            $this->layout = 'myteam';
        }
        $searchModel = new SearchSummaryComment();
        if($comment_id)
        $searchModel->Id=$comment_id;

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        //$model=$dataProvider->getModels();
        //echo count($model);
        //echo "<pre>",print_r($model),"</pre>";die;
        $dropdown_widget=[];
        if($case_id!=0){
            $userId = Yii::$app->user->identity->id; 
            $roleId = Yii::$app->user->identity->role_id;
            $sql_query = "SELECT team.id as team_id,team.team_name,tbl_team_locs.team_loc,master.team_location_name  FROM tbl_team as team LEFT JOIN tbl_team_locs on tbl_team_locs.team_id=team.id LEFT JOIN tbl_teamlocation_master as master ON master.id = tbl_team_locs.team_loc WHERE  team.id != 1 order by team.team_name,master.team_location_name ";
            if($roleId!=0){
                $sql_query = "SELECT security.team_id,security.team_loc,team.team_name,master.team_location_name  FROM tbl_project_security security INNER JOIN tbl_team as team ON team.id = security.team_id INNER JOIN tbl_teamlocation_master as master ON master.id = security.team_loc WHERE security.user_id = ".$userId." AND security.team_id != 0 AND security.team_loc != 0 order by team.team_name,master.team_location_name";
            }
            $params[':user_id'] = $userId;
            $dropdown_data = \Yii::$app->db->createCommand($sql_query)->queryAll();
            if(!empty($dropdown_data)){
                foreach($dropdown_data as $drop => $value){
                    $dropdown_widget[$drop]['id'] = $value['team_id'].'_'.$value['team_loc'];
                    $dropdown_widget[$drop]['team_name'] = $value['team_name'].' - '.$value['team_location_name'];
                }
            }else{
                $dropdown_widget['id'] = 'No Records Found';
            }
        }

        /*read comment*/
        $comment_data=$dataProvider->getModels();
        $SummaryCommentsRead_commentids=ArrayHelper::map(SummaryCommentsRead::find()->select(['comment_id'])->distinct(true)->where(['user_id'=>Yii::$app->user->identity->id])->all(),'comment_id','comment_id');
    	if(!empty($comment_data)) {
    		$comments_rows=array();
    		foreach ($comment_data as $comment) {
                if(!in_array($comment->Id,$SummaryCommentsRead_commentids)){
                    $commentsAttr = array();
                    $commentsAttr['comment_id']=$comment->Id;
                    $commentsAttr['user_id']=Yii::$app->user->identity->id;
                    $comments_rows[] = $commentsAttr;
                }
		    }
            if(!empty($comments_rows)) {
    			$columns = (new SummaryCommentsRead)->attributes();
    			unset($columns[array_search('id',$columns)]);
    			Yii::$app->db->createCommand()->batchInsert(SummaryCommentsRead::tableName(), $columns, $comments_rows)->execute();
    		}
    	}
       // echo "<prE>",print_r($dropdown_widget),"</pre>";die;
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'case_id'=>$case_id,
            'team_id'=>$team_id,
            'team_loc'=>$team_loc,
            'dropdown_widget'=>$dropdown_widget,
            'shraed_teamloc'=>$shraed_teamloc,
            'comment_id'=>$comment_id,
            'comment'=>$comment
        ]);
    }

    /**
     * Displays a single SummaryComment model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new SummaryComment model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $case_id = Yii::$app->request->get('case_id',0);
        $team_id = Yii::$app->request->get('team_id',0);
        $team_loc = Yii::$app->request->get('team_loc',0);
        $model = new SummaryComment();
        if (Yii::$app->request->post()) {

            $post_data=Yii::$app->request->post();
            $model->postComment($post_data,$case_id,$team_id,$team_loc);
           // echo "<pre>",print_r($post_data),"</pre>";die('here');
            if($case_id!=0){
                return $this->redirect(['index','case_id'=>$case_id]);
            }else{
                return $this->redirect(['index','team_id'=>$team_id,'team_loc'=>$team_loc]);
            }
        } else {
            return $this->renderAjax('create', [
                'model' => $model,
                'case_id'=>$case_id,
                'team_id'=>$team_id,
                'team_loc'=>$team_loc
            ]);
        }
    }
    /**
     * Creates a new SummaryComment model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionReply()
    {
        $case_id = Yii::$app->request->get('case_id',0);
        $team_id = Yii::$app->request->get('team_id',0);
        $team_loc = Yii::$app->request->get('team_loc',0);
        $comment_id = Yii::$app->request->get('comment_id',0);
        $model = new SummaryComment();
        if (Yii::$app->request->post()) {
            $post_data=Yii::$app->request->post();
            $model->replyComment($post_data,$comment_id,$case_id,$team_id,$team_loc);
            if($case_id!=0){
                return $this->redirect(['index','case_id'=>$case_id]);
            }else{
                return $this->redirect(['index','team_id'=>$team_id,'team_loc'=>$team_loc]);
            }
        } else {
            return $this->renderAjax('reply', [
                'model' => $model,
                'case_id'=>$case_id,
                'team_id'=>$team_id,
                'team_loc'=>$team_loc,
            ]);
        }
    }

    /**
     * Updates an existing SummaryComment model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $case_id = Yii::$app->request->get('case_id',0);
        $team_id = Yii::$app->request->get('team_id',0);
        $team_loc = Yii::$app->request->get('team_loc',0);
        $comment_id = Yii::$app->request->get('comment_id',0);
        $model = $this->findModel($id);
        if (Yii::$app->request->post()) {
            $post_data=Yii::$app->request->post();
            $model->editComment($post_data,$comment_id,$case_id,$team_id,$team_loc);
            if($case_id!=0){
                return $this->redirect(['index','case_id'=>$case_id]);
            }else{
                return $this->redirect(['index','team_id'=>$team_id,'team_loc'=>$team_loc]);
            }
        } else {
            return $this->renderAjax('update', [
                'model' => $model,
                'case_id'=>$case_id,
                'team_id'=>$team_id,
                'team_loc'=>$team_loc,
            ]);
        }
    }
    /**
     * Deletes an existing SummaryComment model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id){
        $case_id = Yii::$app->request->post('case_id',0);
        $team_id = Yii::$app->request->post('team_id',0);
        $team_loc = Yii::$app->request->post('team_loc',0);
        $comment_id = Yii::$app->request->post('comment_id',0);
        $msg=Yii::$app->request->post('msg');
        return (new SummaryComment())->deleteComment($id,$msg);
    }

    public function actionTeamlocs(){
        $shared_team = Yii::$app->request->get('shared_team');
        $selected_teams=[];
        $selected_teams_locs=[];
        if(isset($shared_team)){
            $selected_teams_locs=explode(',',$shared_team);
            foreach(explode(',',$shared_team) as $team){
                $team_loc=explode("_",$team);
                $selected_teams[$team_loc[0]]=$team_loc[0];
            }
        }
        //$userId = Yii::$app->user->identity->id; 
		//$roleId = Yii::$app->user->identity->role_id;
		$sql_query = "SELECT team.id as team_id,team.team_name,tbl_team_locs.team_loc,master.team_location_name  FROM tbl_team as team LEFT JOIN tbl_team_locs on tbl_team_locs.team_id=team.id LEFT JOIN tbl_teamlocation_master as master ON master.id = tbl_team_locs.team_loc WHERE  team.id != 1 order by team.team_name,master.team_location_name ";
		//if($roleId!=0){
		//	$sql_query = "SELECT security.team_id,security.team_loc,team.team_name,master.team_location_name  FROM tbl_project_security security INNER JOIN tbl_team as team ON team.id = security.team_id INNER JOIN tbl_teamlocation_master as master ON master.id = security.team_loc WHERE security.user_id = ".$userId." AND security.team_id != 0 AND security.team_loc != 0 order by team.team_name,master.team_location_name";
		//}
		$params[':user_id'] = $userId;
		$dropdown_data = \Yii::$app->db->createCommand($sql_query)->queryAll();
		if(!empty($dropdown_data)){
			foreach($dropdown_data as $drop => $value){
				$dropdown_widget[$value['team_id']][$value['team_name']][$value['team_loc']] = $value['team_location_name'];
				//$dropdown_widget[$drop]['team_name'] = $value['team_name'].' - '.$value['team_location_name'];
			}
		}else{
			$dropdown_widget['id'] = 'No Records Found';
		}
        //echo "<pre>",print_r($dropdown_widget),"</pre>";die;
        return $this->renderAjax('teamlocs', [
        	'dropdown_widget' => $dropdown_widget,
        	'selected_teams'=>$selected_teams,
        	'selected_teams_locs'=>$selected_teams_locs,
        ]);
    }
    /**
     * Get Child SummaryComment 
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $case_id,$team_id,$team_loc,comment_id
     * @return mixed
     */
    public function actionGetCommentDetails(){
        $post_data= Yii::$app->request->post(); 
        $case_id  = Yii::$app->request->post('case_id',0);
        $team_id  = Yii::$app->request->post('team_id',0);
        $team_loc = Yii::$app->request->post('team_loc',0);
        $model    = SummaryComment::find()->where(['parent_id'=>$post_data['expandRowKey']])->all();
        return $this->renderAjax('comment_details', [
                'case_id'   => $case_id,
                'team_id'   => $team_id,
                'team_loc'  => $team_loc,
                'model'     => $model 
        ]);

    }

    /**
     * Unread SummaryComment 
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $case_id,$team_id,$team_loc
     * @return mixed
     */
    public function actionGetsummary()
    {
        $case_id  = Yii::$app->request->post('case_id',0);
        $team_id  = Yii::$app->request->post('team_id',0);
        $team_loc = Yii::$app->request->post('team_loc',0);

        $user_id = Yii::$app->user->identity->id;
        $html = "";
        $sql="";
        if($case_id!=0){
            $sql="SELECT comment_id FROM tbl_summary_comments_read inner join tbl_summary_comments on tbl_summary_comments.Id=comment_id AND tbl_summary_comments.case_id=$case_id where parent_id = 0 AND user_id=".$user_id; 
            $unreadComment = SummaryComment::find()->where("id NOT IN ($sql) and case_id=$case_id AND parent_id = 0")->all();
        }else if($team_id!=0){
            $sql="SELECT comment_id FROM tbl_summary_comments_read inner join tbl_summary_comments on tbl_summary_comments.Id=comment_id AND tbl_summary_comments.team_id=$team_id AND tbl_summary_comments.team_loc=$team_loc where parent_id = 0 AND user_id=".$user_id; 
            $unreadComment = SummaryComment::find()->where("parent_id = 0 AND id NOT IN ($sql) and team_id=$team_id AND team_loc=$team_loc")->all();
        }

        return $this->renderAjax('getsummary', [
                'case_id'   => $case_id,
                'team_id'   => $team_id,
                'team_loc'  => $team_loc,
                'unreadComment'     => $unreadComment 
        ]);

    }

    /**
     * Updatecommentstatus To read 
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $case_id,$team_id,$team_loc
     * @return mixed
     */
     public function actionUpdatecommentstatus(){
        $case_id  = Yii::$app->request->post('case_id',0);
        $team_id  = Yii::$app->request->post('team_id',0);
        $team_loc = Yii::$app->request->post('team_loc',0);
        $user_id = Yii::$app->user->identity->id;
         if($caseId!=0){
            $sql="SELECT comment_id FROM tbl_summary_comments_read inner join tbl_summary_comments on tbl_summary_comments.Id=comment_id AND tbl_summary_comments.case_id=$caseId where parent_id = 0 AND user_id=".$user_id; 
            $unreadComment = SummaryComment::find()->where("id NOT IN ($sql) and case_id=$caseId AND parent_id = 0")->all();
        }else{
            $sql="SELECT comment_id FROM tbl_summary_comments_read inner join tbl_summary_comments on tbl_summary_comments.Id=comment_id AND tbl_summary_comments.team_id=$team_id AND tbl_summary_comments.team_loc=$team_loc where parent_id = 0 AND user_id=".$user_id; 
            $unreadComment = SummaryComment::find()->where("parent_id = 0 AND id NOT IN ($sql) and team_id=$team_id AND team_loc=$team_loc")->all();
        }
        if(!empty($unreadComment)){
            foreach($unreadComment as $model){
                $model_commentread = new  SummaryCommentsRead();
                $model_commentread->comment_id = $model->Id;
                $model_commentread->user_id    = Yii::$app->user->identity->id;
                $model_commentread->save();
            }
        }
        return;

     }

    /**
     * Finds the SummaryComment model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return SummaryComment the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = SummaryComment::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
