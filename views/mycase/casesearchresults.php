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
		//echo "<pre>"; print_r($search_results); exit;
		if(!empty($search_results))
		{
			foreach ($search_results as $casearch) {?>
					<?php 
					if($casearch['origination']=='display_all_comment') 
					{ 
						$url='index.php?r=case-projects/post-comment&task_id='.$casearch['task_id'].'&case_id='.$casearch['caseId'].'&comment_id='.$casearch['id'];
					?>
					<li class="g">
						<div sig="szg" class="vsc">
						   <h3 class="r">
							   	<a class="l" href="<?php echo $url?>">
							   		<strong>
							   		<?php
							   		echo $casearch['title']; 
							   		?>
							   		</strong>
							   	</a>
						   </h3>
						   <div class="s">
						   	<div class="f kv">
						   		<cite><?php echo $casearch['green_link'];?></cite>
						   	</div>
						   	<span class="st">
						   		<?php
								$val=$casearch['value'];
								if(strlen($val)>200) {
									$val = substr($casearch['value'],0,200)."...";								
								}								
								echo "<strong>".$val."</strong>";
								?>								
						   		<br>
							   		Attachments:<?php 
							   		$attachment_info=Mydocument::find()->where(['reference_id' => $casearch['id'], 'origination' => 'Comment'])->all();
							   		$strattachment = "";                                                                        
							   		foreach($attachment_info as $attachment)
							   		{
										if($strattachment=="")
											$strattachment= Html::a("<em title='".$attachment->fname."' class='fa fa-paperclip'></em>",["media/downloadfiles", "name"=>$attachment->id],["title"=>$attachment->fname]);
										else
											$strattachment.='&nbsp;&nbsp;'.Html::a("<em title='".$attachment->fname."' class='fa fa-paperclip'></em>",["media/downloadfiles", "name"=>$attachment->id],["title"=>$attachment->fname]);
									}	
									echo $strattachment;
									
							   		//$comment_info=Comments::model()->findByPk($casearch['id']);
							   		//echo  Comments::model()->getAttachment($comment_info->attachments,$comment_info->Id);
							   		?>
							   	<br>
									Posted By:	<?php echo $casearch['posted_by']; ?>
						   	</span>
						   </div>
						 </div>
					</li>
					<?php
					} 
					else if($casearch['origination']=='comment') 
					{
					$url='index.php?r=case-projects/post-comment&task_id='.$casearch['task_id'].'&case_id='.$casearch['caseId'].'&comment_id='.$casearch['id'];
					?>
					<li class="g">
						<div sig="szg" class="vsc">
						   <h3 class="r">
							   	<a class="l" href="<?php echo $url?>">
							   		<strong>
							   		<?php
								   	echo $casearch['title']; 
							   		?>
							   		</strong>
							   	</a>
						   </h3>
						   <div class="s">
						   	<div class="f kv">
						   		<cite><?php echo $casearch['green_link'];?></cite>
						   	</div>
						   	<span class="st">
						   		<?php 
						   		$val=$casearch['value'];
						   		if($term!="comment_search")
						   		{
									if(stristr($val,$term))
									{
										if(strlen($val)>200)
										{
											if(strpos($val,$term)==0)
												echo substr(str_replace($term,"<em>".$term."</em>",$val),(strpos($val,$term)),200)."...";
											else if((strpos($val,$term)+strlen($term))==strlen($val))
												echo "...".substr(str_replace($term,"<em>".$term."</em>",$val),200,(strpos($val,$term)));
											else
												echo "...".substr(str_replace($term,"<em>".$term."</em>",$val),(strpos($val,$term)-2),200).'...';
										}
										else
											echo str_replace($term,"<em>".$term."</em>",$val);
									} else if(isset($casearch['ismagnified']) && $casearch['ismagnified'] == 1) {
										echo $casearch['value'];
									}
								}
								else
								{
									echo $casearch['value'];
								}
								?>
						   		<br>
							   		Attachments:<?php 
							   		$attachment_info=Mydocument::find()->where(['reference_id' => $casearch['id'], 'origination' => 'Comment'])->all();
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
						   	</span>
						   </div>
						 </div>
					</li>
					<?php
					}
					else if($casearch['origination']=='todo') 
					{
					$url='index.php?r=track/index&taskid='.$casearch['task_id'].'&case_id='.$casearch['caseId'].'';
					?>
					<li class="g">
						<div sig="szg" class="vsc">
						   <h3 class="r">
							   	<a class="l" href="<?php echo $url?>">
							   		<strong>
							   		<?php
								   	echo $casearch['title']; 
							   		?>
							   		</strong>
							   	</a>
						   </h3>
						   <div class="s">
						   	<div class="f kv">
						   		<cite><?php echo $casearch['green_link'];?></cite>
						   	</div>
						   	<span class="st">
						   		<?php 
						   		$val=$casearch['value'];						   		
						   		if(stristr($val,$term))
						   		{
						   			if(strlen($val)>200)
						   			{
						   				if(strpos($val,$term)==0)
						   					echo substr(str_replace($term,"<em>".$term."</em>",$val),(strpos($val,$term)),200)."...";
						   				else if((strpos($val,$term)+strlen($term))==strlen($val))
						   					echo "...".substr(str_replace($term,"<em>".$term."</em>",$val),200,(strpos($val,$term)));
						   				else
						   					echo "...".substr(str_replace($term,"<em>".$term."</em>",$val),(strpos($val,$term)-2),200).'...';
						   			}
						   			else
						   				echo str_replace($term,"<em>".$term."</em>",$val);
								}
								?>
						   		<br>
							   		Attachments:<?php //                                                                        
							   		$attachment_info=Mydocument::find()->where(['reference_id' => $casearch['id'], 'origination' => 'Todo'])->all();
							   		$strattachment = "";
							   		foreach($attachment_info as $attachment)
							   		{
										if($strattachment=="")
											$strattachment= Html::a("<em title='".$attachment->fname."' class='fa fa-paperclip'></em>",["media/downloadfiles", "name"=>$attachment->id],["title"=>$attachment->fname]);
										else
											$strattachment.='&nbsp;&nbsp;'.Html::a("<em title='".$attachment->fname."' class='fa fa-paperclip'></em>",["media/downloadfiles", "name"=>$attachment->id],["title"=>$attachment->fname]);
									}	
									echo $strattachment;
									
							   		//$todo_info=TasksUnitsTodos::model()->findByPk($casearch['id']);
							   		//echo  TasksUnitsTodos::model()->getAttachment($todo_info->attach,$todo_info->id);							   		
							   		?>
						   	</span>
						   </div>
						 </div>
					</li>
					<?php 
					}
					else if($casearch['origination']=='instruction_project') 
					{
						$url='index.php?r=case-projects/index&case_id='.$casearch['caseId'].'&todotaskids='.$casearch['task_id'];
					?>
						<li class="g">
							<div sig="szg" class="vsc">
							   <h3 class="r">
								   	<a class="l" href="<?php echo $url?>">
								   		<strong>
								   		<?php 
								   		echo $casearch['title'];
								   		?>
								   		</strong>
								   	</a>
							   </h3>
							   <div class="s">
							   	<div class="f kv">
							   		<cite><?php echo $casearch['green_link'];?></cite>
							   	</div>
							   	<span class="st">
							   		<?php echo str_replace($term,"<em>".$term."</em>",$casearch['value']);?>
							   		
							   	</span>
							   </div>
							 </div>
						</li>
					<?php 
					}
					else if($casearch['origination']=='instruction_notes_project') 
					{
						$url='index.php?r=case-projects/index&case_id='.$casearch['caseId'].'&todotaskids='.$casearch['task_id'];
					?>
						<li class="g">
							<div sig="szg" class="vsc">
							   <h3 class="r">
								   	<a class="l" href="<?php echo $url?>">
								   		<strong>
								   		<?php 
								   		echo $casearch['title'];
								   		?>
								   		</strong>
								   	</a>
							   </h3>
							   <div class="s">
							   	<div class="f kv">
							   		<cite><?php echo $casearch['green_link'];?></cite>
							   	</div>
							   	<span class="st">
							   		<?php echo str_replace($term,"<em>".$term."</em>",$casearch['value']);?>
							   		
							   	</span>
							   </div>
							 </div>
						</li>
					<?php 
					}
					else if($casearch['origination']=='instruction_attachments')
					{
						$url='index.php?r=case-projects/index&case_id='.$casearch['caseId'].'&todotaskids='.$casearch['task_id'];
					?>
						<li class="g">
							<div sig="szg" class="vsc">
							   <h3 class="r">
								   	<a class="l" href="<?php echo $url?>">
								   		<strong><?php 
								   		echo $casearch['title'];
								   		?>
								   		</strong>
								   	</a>
							   </h3>
							   <div class="s">
							   	<div class="f kv">
							   		<cite><?php echo $casearch['green_link'];//$url?></cite>
							   	</div>
							   	<span class="st">
							   		<?php echo str_replace($term,"<em>".$term."</em>",$casearch['value']);?>
							   		<br>
							   		<span style="font-size: 13px;">Version: </span><?php
									$taskinstruct_info=Taskinstruct::find()->where(['task_id'=>$casearch['task_id'],'isactive'=>'1'])->one();
							   		echo "<a href='javascript:viewInstruction(".$taskinstruct_info->id.");' class='dialog'>".$casearch['version']."</a>";?>
							   		<span style="font-size: 13px;">Attachments: </span><?php 
							   		
							   		$attachment_info=Mydocument::find()->where(['reference_id' => $taskinstruct_info->id, 'origination' => 'instruct'])->all();
							   		$strattachment = "";
							   		foreach($attachment_info as $attachment)
							   		{
										if($strattachment=="")
											$strattachment= Html::a("<em title='".$attachment->fname."' class='fa fa-paperclip'></em>",["media/downloadfiles", "name"=>$attachment->id],["title"=>$attachment->fname]);
										else
											$strattachment.='&nbsp;&nbsp;'.Html::a("<em title='".$attachment->fname."' class='fa fa-paperclip'></em>",["media/downloadfiles", "name"=>$attachment->id],["title"=>$attachment->fname]);
									}	
									echo $strattachment;
									
							   		//echo Taskindividual::model()->getAttachment($taskinstruct_info->task_attachments,$taskinstruct_info->id);
							   		?>
							   	</span>
							   </div>
							 </div>
						</li>
					<?php 
					}
					else if($casearch['origination']=='instruction_notes_attachments')
					{
						$url='index.php?r=case-projects/index&case_id='.$casearch['caseId'].'&todotaskids='.$casearch['task_id'];
					?>
						<li class="g">
							<div sig="szg" class="vsc">
							   <h3 class="r">
								   	<a class="l" href="<?php echo $url?>">
								   		<strong><?php 
								   		echo $casearch['title'];
								   		?>
								   		</strong>
								   	</a>
							   </h3>
							   <div class="s">
							   	<div class="f kv">
							   		<cite><?php echo $casearch['green_link'];//$url?></cite>
							   	</div>
							   	<span class="st">
							   		<?php echo str_replace($term,"<em>".$term."</em>",$casearch['value']);?>
							   		<br>
							   		<span style="font-size: 13px;">Version: </span><?php
									$taskinstruct_info=Taskinstruct::find()->where(['task_id'=>$casearch['task_id'],'isactive'=>'1'])->one();
							   		echo "<a href='javascript:viewInstruction(".$taskinstruct_info->id.");' class='dialog'>".$casearch['version']."</a>";?>
							   		<span style="font-size: 13px;">Attachments: </span><?php 
							   		$attachment_info=Mydocument::find()->where(['reference_id' => $taskinstruct_info->id, 'origination' => 'Instruct N'])->all();
							   		$strattachment = "";
							   		foreach($attachment_info as $attachment)
							   		{
										if($strattachment=="")
											$strattachment= Html::a("<em title='".$attachment->fname."' class='fa fa-paperclip'></em>",["media/downloadfiles", "name"=>$attachment->id],["title"=>$attachment->fname]);
										else
											$strattachment.='&nbsp;&nbsp;'.Html::a("<em title='".$attachment->fname."' class='fa fa-paperclip'></em>",["media/downloadfiles", "name"=>$attachment->id],["title"=>$attachment->fname]);
									}	
									echo $strattachment;
									
							   		//echo InstrcutionNotes::model()->getAttachment($taskinstruct_info->attachment,$taskinstruct_info->id);
							   		?>
							   	</span>
							   </div>
							 </div>
						</li>
					<?php
					}
					else if($casearch['origination']=='instruction_task_details')
					{
						$url='index.php?r=case-projects/index&case_id='.$casearch['caseId'].'&todotaskids='.$casearch['task_id'];
					?>
							<li class="g">
							<div sig="szg" class="vsc">
							   <h3 class="r">
								   	<a class="l" href="<?php echo $url?>">
								   		<strong><?php 
								   		echo $casearch['title'];
								   		?>
								   		</strong>
								   	</a>
							   </h3>
							   <div class="s">
							   	<div class="f kv">
							   		<cite><?php echo $casearch['green_link'];//$url?></cite>
							   	</div>
							   	<span class="st">
							   		<?php echo str_replace($term,"<em>".$term."</em>",$casearch['value']);?>
							   		<br>
							   		<span style="font-size: 13px;">Version: </span><?php
							   		echo "<a href='javascript:viewInstruction(".$casearch['instruction_id'].");' class='dialog'>".$casearch['version']."</a>";?>
							   	</span>
							   </div>
							 </div>
						</li>
					<?php 
					}
					else if($casearch['origination']=='instruction_evid')
					{
						$url='index.php?r=evidence/index';
					?>
						<li class="g">
							<div sig="szg" class="vsc">
							   <h3 class="r">
								   	<a class="l" href="<?php echo $url?>">
								   		<strong><?php 
								   		echo $casearch['title'];
								   		?>
								   		</strong>
								   	</a>
							   </h3>
							   <div class="s">
							   	<div class="f kv">
							   		<cite><?php echo $casearch['green_link'];//$url?></cite>
							   	</div>
							   	<span class="st">
							   		<?php echo str_replace($term,"<em>".$term."</em>",$casearch['value']);?>
							   		<br />
							   		<span style="font-size: 13px;">Version: </span>
							   		<?php
									$taskinstruct_info=Taskinstruct::find()->where(['task_id'=>$casearch['task_id'],'isactive'=>'1'])->one();
							   		echo "<a href='javascript:viewInstruction(".$taskinstruct_info->id.");' class='dialog'>".$casearch['version']."</a>";
							   		?>
							   		
							   	</span>
							   </div>
							 </div>
						</li>
					<?php }?>
			<?php }
		}
		else {?>
			<?php
			if($term!='comment_search') {
			?>
			<div class="med">
				<a href="javascript:void(0);"></a>
				<p style="padding-top:.33em">Your search, "<strong><?php echo $term?></strong>" did not match any IS-A-TASK records for the selected Cases.</p>
					<p style="margin-top:1em">Suggestions:</p>
					<ul style="margin:0 0 2em;margin-left:1.3em">
						<li>Make sure all words are spelled correctly.</li>
						<li>Try different keywords.</li>
						<li>Try more general keywords.</li>
						<li>Try fewer keywords.</li>
					</ul>
			</div>
			<?php
			} else {
			?>
			<div class="med">
				<p style="padding-top:.33em">There are 0 unread comments.</p>
				<a href="javascript:void(0);"></a>
			</div>			
			<?php
			}
			?>
		<?php }?>
		</ol>
	</div>
