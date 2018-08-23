<?php
use yii\helpers\Html;
use app\models\Comments;
use app\models\Mydocument;
use app\models\Taskinstruct;
?>
<style>
.st {
    line-height: 1.24;
    color: #222222;
}
em {
    font-style: normal;
    font-weight: bold;
}
.s {
    color: #222222;
}
.s {
    max-width: 42em;
    margin-top: -10px;
}
.a, cite, cite a:link, cite a:visited, .cite, .cite:link, #mbEnd cite b, #tads cite b, #tadsb cite b, #ans > i, .bc a:link {
    color: #009933;
    font-style: normal;
}
ol,  li {
    border: 0 none;
    margin: 0;
    padding: 0;
}
ol li {
    list-style: none outside none;
}
#ires
{
 width: 100%;
}
#ires a {
    color: #1122CC;
    cursor: pointer;
}
#ires h3 {
    font-size: medium;
}
#ires h3 a{
    font-size: medium;
    text-decoration:underline;
}
</style>
<div id="ires">
		<ol id="rso" eid="M0EwUYu6OYiPrgfGroBI">
		<?php 
		//echo "<pre>"; print_r($unreadComment); exit;
		if(!empty($unreadComment))
		{
			foreach ($unreadComment as $casearch) {?>
					<?php 
                        if($case_id!=0)
					    $url='index.php?r=summary-comment/index&case_id='.$casearch->case_id.'&comment_id='.$casearch->Id;
                        else
                        $url='index.php?r=summary-comment/index&team_id='.$casearch->team_id.'&team_loc='.$casearch->team_loc.'&comment_id='.$casearch->Id;
					?>
					<li class="g">
						<div sig="szg" class="vsc">
						   <h3 class="r">
							   	<a class="l" href="<?php echo $url?>">
							   		<strong>
							   		Summary Comment
							   		</strong>
							   	</a>
						   </h3>
						   <div class="s">
						   	<div class="f kv">
						   		<cite><?php 
                                   if($case_id!=0){
                                       $case_detail=strtoupper($casearch->clientCase->client->client_name.' - '. $casearch->clientCase->case_name);
                                   }else{
                                       $case_detail=strtoupper($casearch->team->team_name.' - '. $casearch->teamLocation->team_location_name);
                                   }
                                   $green_link=$case_detail;
                                   echo $green_link;?></cite>
						   	</div>
						   	<span class="st">
						   		<?php
								$val=$casearch->comment;
								if(strlen($val)>200) {
									$val = substr($val,0,200)."...";								
								}								
								echo "<strong>".$val."</strong>";
								?>								
						   		<br>
							   		Attachments:<?php 
							   		$attachment_info=Mydocument::find()->where(['reference_id' => $casearch->Id, 'origination' => 'Summary Comment'])->all();
							   		$strattachment = "";                                                                        
							   		foreach($attachment_info as $attachment)
							   		{
										if($strattachment=="")
											$strattachment= Html::a("<em title='".$attachment->fname."' class='fa fa-paperclip'></em>",["media/downloadfiles", "name"=>$attachment->id],["title"=>$attachment->fname]);
										else
											$strattachment.='&nbsp;&nbsp;'.Html::a("<em title='".$attachment->fname."' class='fa fa-paperclip'></em>",["media/downloadfiles", "name"=>$attachment->id],["title"=>$attachment->fname]);
									}	
									echo $strattachment;
									?>
							   	<br>
									Posted By:	<?php echo $casearch->createdUser->usr_first_name." ".$casearch->createdUser->usr_lastname; ?>
						   	</span>
						   </div>
						 </div>
					</li>
					<?php
					 
				 }
		} else {?>
			<li>There are 0 unread comments.</li>
		<?php }?>
		</ol>
	</div>
