<?php if(!empty($filter_data)){  foreach ($filter_data as $id=>$name) {
	?>
<li style="width:100%">
		<div class="pull-left" style="width: 90%">
			<a href="javascript:void(0);" title="<?php if($name['title'] != ""){ echo $name['title']; }?>" onclick="showSavefilter(<?=$name['id']?>)"  class="allsavedfilter " id="savefilter_<?=$name['id']?>"><?=html_entity_decode($name['filter_name'])?></a>
		</div>
		<div class="pull-right" style="width: 10%">
                    <a href="javascript:void(0);" onclick="deletesavefilter(<?=$name['id']?>)" class="close-acd" title="Delete" aria-label="Delete" tabindex="0"><em class="fa fa-remove text-primary" title="Delete"></em><span class="sr-only">Delete</span></a>
		</div>
	</li>
<?php }}else{?>No Record Found...<?php }?>
<!--<style>
    a.close-acd:hover {border: 2px solid #000 !important;}
</style>-->
