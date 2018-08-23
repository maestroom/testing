<?php
use yii\helpers\Html;
$controller=Yii::$app->controller->id;
$action=Yii::$app->controller->action->id;
use app\models\User;
use app\assets\ManageUserAccessAsset;
ManageUserAccessAsset::register($this);

$by_media_filter='';
$params=Yii::$app->request->queryParams;
if(isset($params['client_case_id'])){
	$by_media_filter='on';
}
?>
 <div class="acordian-main">
     <div id="accordion-container">
			  <?php if((new User)->checkAccess(3.009)){ ?>
	<h3 title="Media Inventory">Media Inventory</h3>
            <div>
		<div class="acordian-div">
		  <ul class="sidebar-acordian mediamoduleList">
			<li><a href="<?php echo 'index.php?r=media/index'; ?>" title="Display Media node" class="mediaModules <?= ($action=='index')?'active':''; ?>">Display Media	</a></li>
			<!-- 605 -->
			<li  id="media_filter_li" class="custom-full-width clearfix"> <input type="checkbox" name='media_filter' id="media_filter" onclick='if (this.checked) {
                                            $(".media_filters").show();
                                        } else {
                                            $(".media_filters").hide();
                                        }' <?php if ($by_media_filter == 'on') echo 'checked'; ?> class="by_media_filter"> <label for="media_filter" class="media_filter" aria-label="media_filter">Filter Media By Client/Case &nbsp;&nbsp;</label>
										<a class='pull-left info_a' href="javascript:void(0)" title="Only 100 items or less can be checked per filter request."><em class="fa fa-info-circle text-primary" aria-hidden="true" title="Only 100 items or less can be checked per filter request."></em></a>
										<a class='pull-right info_a_search' href="javascript:void(0)" title="Filter Media By Client/Case"><em class='fa fa-search-plus fa-2x text-primary global-dynamic-search' onclick='javascript:filterMediaGrid()' title='Filter Media By Client/Case' style='cursor:pointer'><span style='display:none'>Filter Media By Client/Case</span></em></a>
										<div class="clearfix"></div>
                                                <fieldset><legend class="sr-only">Filter Media</legend>
                                    <ul id="medias_filter" class='media_filters filter_hide_all' style='display:<?php if ($by_media_filter == 'on') echo '';
                            else echo 'none' ?>;'>
                                        <li>
                                            <em class="fa fa-spinner fa-pulse fa-2x" title="Loading..."></em>
                                            <span class="sr-only">Loading...</span>
                                        </li>
                                  </ul></fieldset>
			</li>
			<!-- 605 -->


                        <!--  <li><a id='edit_media' title ="Edit Media" href="javascript:void(0);" onclick="editmedia();" class="mediaModules <?=$active ?>">Edit Media</a></li>-->
                        <!--  <li><a id='delete_media' title ="Delete Media" href="javascript:void(0);" onclick="deletemedia();" class="mediaModules <?=$active ?>">Delete Media</a></li>-->
                        <?php if((new User)->checkAccess(3.05)){ ?>
                        <li class="checkoutin_media_li"><a id='checkoutin_media' title ="Check Out/In" href="javascript:void(0);" onclick="check_out_in();" class="mediaModules <?=$active ?>">Check Out/In</a></li>
                        <?php } ?>
                        <?php if((new User)->checkAccess(3.06)){ ?>
                        <li class="chainofcustody_media_li"><a id='chainofcustody_media' title ="Chain Of Custody" href="javascript:void(0);" onclick="chain_of_custody('single');" class="mediaModules <?=$active ?>">Chain Of Custody</a></li>
                        <?php } ?>
												<?php if((new User)->checkAccess(3.061)){ ?>
														<li><a href="javascript:void(0);" onclick="check_outin_barcode();" class="mediaModules <?=$active ?>" title ="Barcode Check Out/In"> Barcode Check Out/In</a></li>
												<?php } ?>
		  </ul>
		</div>
            </div>
         <?php /*if((new User)->checkAccess(3.05)){ ?>
        <h3 title="Barcode Activity">Barcode Activity</h3>
            <div>
			<div class="acordian-div">
				<ul class="sidebar-acordian">
                        <li><a href="javascript:void(0);" onclick="check_outin_barcode();" class="barcodeModules<?=$active ?>" title ="Bulk Check Out/In"> Check Out/In (Bulk)</a></li>
				</ul>
		</div>
            </div>
         <?php }*/ ?>
	<?php } ?>
     </div>
 </div>
 <form id="dynamicfilter_gird" autocomplete="off" style=''>
	 <input type="hidden" name="client_case_id" id="client_case_id" value=""/>
</form>
<script type="text/javascript">
var accordionOptions = {
		 heightStyle: 'fill',clearStyle: true,autoHeight: false
	    };
		var accordionOptions = {
	 heightStyle: 'fill',clearStyle: true,autoHeight: false, icons: { "header": "fa fa-caret-right pull-right", "activeHeader": "fa fa-caret-down pull-right" }
,create: function( event, ui ) {

//$("#accordion-container h3 span").removeClass('ui-accordion-header-icon');
$("#accordion-container h3 span").removeClass('ui-icon');

}};
$("#accordion-container" ).accordion(accordionOptions);
jQuery(document).ready(function($) {
	$('input').customInput();
	$("#accordion-container h3").bind("click", function() {
	   var str = $('#page-title span').text();
           //alert($(this).text());

	   if($(this).text() == 'Barcode Activity'){
            //$(".sysModules").removeClass('active');
	   }
	});
 	$(window).resize(function(){
 	 	// update accordion height
 		$('#accordion-container').accordion("refresh");
		 $( "#accordion-container" ).accordion( "destroy" );
		$("#accordion-container" ).accordion(accordionOptions);
 	});
	  $('body').on('change', '#media_filter', function () {
        if ($("#medias_filter li").length == 1) {
            $.ajax({
                type: "GET",
                url: baseUrl + "media/get-client-case",
                dataType: 'html',
                cache: false,
                success: function (data) {
                    if (data != "") {
                        $("#medias_filter").html(data);
                    }
                }
            });
        }
		if ($("#medias_filter li").length > 1) {
			if(this.checked == false){
				 if($("#tree-media_client_case").length > 0) {
					$("#tree-media_client_case").dynatree("getRoot").visit(function(node){
						node.select(false);
					});
					return false;
				}
			}
		}
    });
<?php if(isset($params['client_case_id'])){ ?>
  $.ajax({
                type: "GET",
                url: baseUrl + "media/get-client-case",
				data:{ids:'<?php echo $params['client_case_id'];?>'},
                dataType: 'html',
                cache: false,
                success: function (data) {
                    if (data != "") {
                        $("#medias_filter").html(data);
                    }
                }
            });
 <?php }?>
 });
 function filterMediaGrid(){
	var client_case_ids="";
	var has_zero_andmore="N";
	if($("#tree-media_client_case").length > 0) {
	$("#tree-media_client_case").dynatree("getRoot").visit(function(node) {
		selKeys = $.map(node.tree.getSelectedNodes(), function(node){
			if(node.childList===null)
				return node.data.key.toString();
		});
		if(node.isSelected() && node.data.isFolder==false) {
			if(client_case_ids==''){
				client_case_ids=node.data.key;
			}
			else
			{
				client_case_ids=client_case_ids+','+node.data.key;
				if(node.data.key==0){
					has_zero_andmore="Y";
				}
			}
		}
	});
	}
	if(client_case_ids!=''){
		if(has_zero_andmore=="Y"){
			alert('Please select either client case or By No Associated Client/Cases.');
		}else{
			var res = client_case_ids.split(",");
			if(res.length > 100){
				alert('Max 100 Cases allow to select, you have selected '+res.length+' cases');
			}else{
				//$('#client_case_id').val(client_case_ids);
				if($('#media-grid-filters').find('#client_case_id')){
					$('#media-grid-filters').find('#client_case_id').val(client_case_ids);
				}else{
					$('#media-grid-filters td:last-child').append('<input type="hidden" name="client_case_id" id="client_case_id" value="'+client_case_ids+'"/>');
				}
				 $.pjax.reload({container: '#dynagrid-media-pjax', replace: false, url:baseUrl + "media/index&client_case_id="+client_case_ids});
				//$.pjax.reload('#dynagrid-media-pjax', $.pjax.defaults);
				//location.href=baseUrl + "media/index&client_case_id="+client_case_ids;
			}
		}
	}else{
		alert('Please select at least one client case');
	}
 }

</script>
<noscript></noscript>
