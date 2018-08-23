<?php
/* @var $this yii\web\View */
/* @var $searchModel app\models\Mydocument */
/* @var $dataProvider yii\data\ActiveDataProvider */
use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\Url;
use kartik\grid\datetimepicker;
use yii\web\JsExpression;
use app\models\MyDocument;
use yii\widgets\ActiveForm;

$this->registerJsFile(Yii::$app->request->baseUrl.'/js/jstree.min.js');
$this->registerCssFile(Yii::$app->request->baseUrl.'/css/jstree/default/style.min.css');
$this->registerJsFile(Yii::$app->request->baseUrl.'/js/jquery.MultiFile.js');

$this->title = 'Team Documents';
$this->params['breadcrumbs'][] = $this->title;

$js = <<<JS
// get the form id and set the event
$(function() {
 $('#T7').MultiFile({ 
        list: '#T7-list',
        STRING: {
            remove: '<em class="fa fa-close text-danger" title="Remove"></em>'
         },
		maxsize:102400
 });	
});
JS;
$this->registerJs($js);
?>

<div class="right-main-container" id="caseproduction_container">
    <div class="sub-heading"><a href="javascript:void(0);" class="tag-header-black" title="Team Documents">Team Documents</a></div>
    <fieldset class="one-cols-fieldset case-project-fieldset caseproduction-fieldset">
        
        <div id="document-tabs" class="ui-tabs one-cols-fieldset">
                <div id="tabs-teamdoc">
                    <div id="dialog_uploadfile" title="Upload File" class="hide">
                        <div id="con">
                            <!--<div>&nbsp;</div>
                            <div>&nbsp;</div>-->
                            <?php $form = ActiveForm::begin(['action'=> Url::toRoute(['team-documents/uploadfiles','team_id'=>$team_id,'team_loc'=>$team_loc]),'id' => 'frm_uploadfile','enableAjaxValidation' => false,'enableClientValidation' => true,'options' => ['enctype'=>'multipart/form-data'],]); ?>
                            <div class='row input-field'>
								<div class='col-md-2'>
									<label class='form_label' for=''>Attachment</label>
								</div>	
								<div class='col-md-9'>
									<input type="file" id="T7" name="Team[upload_files][]"/>      
									<span><small>Tip: File size cannot exceed 100 MB.</small></span>          
									<!--<div class="hint">(File Size cannot exceed 100MB)</div>-->
									<div id="T7-list"></div>
                                </div>
                             </div>   
                                
                            <?php ActiveForm::end(); ?>
                        </div>
                    </div>
                    <div id="dialog_permission"></div>
                    <div class="table-responsive">
                        <div id="container" role="main">
                            <div id="tree" class="demo">
                                <ul>
                                    <?php echo $data['mydoc_str'];?>
                                </ul>
                            </div>
                            <input type="hidden" name="selected_li" id="selected_li" value="0">
                            <input type="hidden" name="copy_selected" id="copy_selected" value="0">
                            <input type="hidden" name="cut_selected" id="cut_selected" value="0">
                        </div>
                     </div>
                </div>
                <div id="tabs-projectdoc"></div>
        </div>        
    </fieldset>
    <div class="button-set text-right">
			<span class="text-danger col-sm-4 padding-top_seven" id="myteam-teamdoc"></span>
            <div class="col-sm-8 pull-right">
            <div class="row">
            <div class="col-sm-7 search-item-set"><input type="text" id="search_doc" title="Enter Search Term" name="seach_doc" class="form-control" placeholder="Enter Search Term" onfocus="$('#myteam-teamdoc').html(null);" /></div>
        <?= Html::button('Search',['title'=>"Search",'class' => 'btn btn-primary','onclick'=>'search_doc();'])?>
        <?= Html::button('Clear',['title'=>"Clear",'class' => 'btn btn-primary','onclick'=>'search_doc("clear");'])?>
        </div>
            </div>
    </div>
    <div id="dialog_probates"></div>
</div>
<script>
function decodeHtml(html) {
    var txt = document.createElement("textarea");
    txt.innerHTML = html;
    return txt.value;
}
var file_name_max_length = "<?php echo $mydocument_length['fname'];?>";	
$(function () {
        $('#tree').jstree({
                        'core' : {
                                'check_callback' : true,
                                'themes' : {
                                        'responsive' : false
                                }
                        },
                        'force_text' : true,
                        'plugins' : ['state','wholerow','search']
                })
                .on('delete_node.jstree', function (e, data) {
                   // console.log(data.node);
                        var team_id= jQuery("#team_id").val();
                        var team_loc= jQuery("#team_loc").val();
                        var node_text=data.node.text;
                        $.ajax({
                            type: "POST",
                            url: baseUrl+"team-documents/deletefolder",
                            beforeSend:function(){
                                showLoader();
                            },
                            data: {'name':data.node.text,'team_id':team_id,'team_loc':team_loc,'selected_node':data.node.id},
                            dataType:'html',
                            cache: false,
                            success:function(data){
                              //  alert(data);
                              //  alert(node_text);
                                //alert("The "+decodeHtml(data)+" has been deleted.");
                               // $('#tree').jstree(true).refresh();
                                setTimeout(function(){
                                        list_teamdocument();
                                    },300);
                            }
                        });
                    })
                .on('create_node.jstree', function (e, data) { })
                .on('search.jstree',function (e, data){
						 if (data.nodes.length == 0){
							$('#myteam-teamdoc').html('No Documents found.');
						 }else{
							$('#myteam-teamdoc').html(null);
						 }
					 })
                .on('ready.jstree', function(e, data) {
                    $('#tree').jstree('open_all');
                    var node_selected='<?php echo $node_id;?>';    
                    $("#tree").jstree("deselect_all");
                    $("#tree").jstree("select_node",node_selected ).trigger("select_node.jstree");
                  });
        
     /*  $( "#document-tabs" ).tabs({
            beforeActivate: function (event, ui) {
    	    if(ui.newPanel.attr('id') == 'tabs-projectdoc'){
    	    	location.href=baseUrl+'case-documents/projectdoc';
        	}
             },
        });*/
       
        var to = false;
    
});
</script>
<noscript></noscript>
<script>
$("#create_folder").on('click',function () {
    var ref = $('#tree').jstree(true);
    var selected_node = ref.get_selected();
    //alert(selected_node);return false;
    if(selected_node==0)
    {
        alert('Please select the File/Folder to perform this action.');
        return false;
    }
    var team_id= jQuery("#team_id").val();
    var team_loc= jQuery("#team_loc").val();
    if(selected_node=='root'){
    	sel = ref.get_selected();
        if(!sel.length) { return false; }
        sel = sel[0];
        sel = ref.create_node(sel, {"type":"folder"});
        if(sel) {
            ref.edit(sel,'New folder',function(node,status){
               // console.log(node.text);console.log(node.id);
                
                $.ajax({
                    type: "POST",
                    url: baseUrl+"team-documents/createfolder",
                    beforeSend:function(){
                        showLoader();
                    },
                    data: {'name':node.text,'team_id':team_id,'team_loc':team_loc,'selected_node':selected_node},
                    dataType:'html',
                    cache: false,
                    success:function(data){
                      //  alert('The folder, "'+decodeHtml(node.text)+'" is created.');
                       // $('#tree').jstree(true).refresh();
                        setTimeout(function(){
                                list_teamdocument(data);
                            },300);
                    }
                });

            },file_name_max_length);
        }
    }else{
    $.ajax({
        type: "POST",
        url: baseUrl+"case-documents/chkfilefolder",
        data: {'selected_node':selected_node},
        dataType:'html',
        cache: false,
        success:function(data){
            data=data.replace(/\s/g, '');
            if(data=='folder')
            {
                sel = ref.get_selected();
                if(!sel.length) { return false; }
                sel = sel[0];
                sel = ref.create_node(sel, {"type":"folder"});
                if(sel) {
                    ref.edit(sel,'New folder',function(node,status){
                        //console.log(node.text);console.log(node);return false;
                        var case_id= jQuery("#case_id").val();
                        $.ajax({
                            type: "POST",
                            url: baseUrl+"team-documents/createfolder",
                            beforeSend:function(){
                                showLoader();
                            },
                            data: {'name':node.text,'team_id':team_id,'team_loc':team_loc,'selected_node':selected_node},
                            dataType:'html',
                            cache: false,
                            success:function(data){
                               // alert('The folder, "'+decodeHtml(node.text)+'" is created.');
                               // $('#tree').jstree(true).refresh();
                                setTimeout(function(){
                                        list_teamdocument(data);
                                    },300);
                            }
                        });

                    },file_name_max_length);
                }
                
            }
            else
                alert('A Folder cannot be created within a File.');
        }
    });
    }
});
$("#rename_folder").click(function () 
{
    var ref = $('#tree').jstree(true);
    var selected_node = ref.get_selected();
    var selected_text = ref.get_text(selected_node);
    if(selected_node=='root')
        return false;
    if(selected_node==0)
    {
        alert('Please select the File/Folder to perform this action.');
        return false;
    }
    $.ajax({
        type: "POST",
        url: baseUrl+"case-documents/chkfilefolder",
        data: {'selected_node':selected_node},
        dataType:'html',
        cache: false,
        success:function(data){
            data=data.replace(/\s/g, '');
            if(data=='folder')
            {
                sel = ref.get_selected();
                console.log(selected_text);
                sel = sel[0];
                
                ref.edit(sel,selected_text,function(node,status){
                        
                        var case_id= jQuery("#case_id").val();
                        $.ajax({
                            type: "POST",
                            url: baseUrl+"team-documents/renamefolder",
                            beforeSend:function() {
                                showLoader();
                            },
                            data: {'name':node.text,'case_id':case_id,'selected_node':selected_node},
                            dataType:'html',
                            cache: false,
                            success:function(data){
                                //alert('The folder, "'+decodeHtml(selected_text)+'"  has been renamed to "'+ decodeHtml(node.text)+'".');
                               // $('#tree').jstree(true).refresh();
                                setTimeout(function(){
                                        list_teamdocument(selected_node);
                                    },300);
                            }
                        });

                    },file_name_max_length);
            }else{alert('A File cannot be renamed.');}
        }
    });
    return false;
});
$("#delete_folder").click(function () 
{
    var ref = $('#tree').jstree(true);
    var selected_node = ref.get_selected();
    var selected_text = ref.get_text(selected_node);
    if(selected_node=='root')
        return false;
    if(selected_node==0)
    {
        alert('Please select the File/Folder to perform this action.');
        return false;
    }
    $.ajax({
        type: "POST",
        url: baseUrl+"case-documents/chkfilefolder",
        data: {'selected_node':selected_node},
        dataType:'html',
        cache: false,
        success:function(data){
            data=data.replace(/\s/g, '');
                if(data=='folder')
                {
                    if(confirm('Are you sure you want to Delete, "'+decodeHtml(selected_text)+'" folder?  All subsequent folders and files will also be Deleted.'))
                    {
                        $("#tree").jstree("remove");
                        sel = ref.get_selected();
                        sel = sel[0];
                        ref.delete_node(sel,status,'delete_node.jstree');
                    }
                }
                else{
                    if(confirm('Are you sure you want to Delete "'+decodeHtml(selected_text)+'"?'))
                    {
                         $("#tree").jstree("remove");
                         sel = ref.get_selected();
                            sel = sel[0];
                            ref.delete_node(sel,status,'delete_node.jstree');
                     }
                }
        }
    });
});
$("#upload_file").click(function () 
{
    var ref = $('#tree').jstree(true);
    var selected_node = ref.get_selected();
    var selected_text = ref.get_text(selected_node);
    if(selected_node==0)
    {
        alert('Please select the File/Folder to perform this action.');
        return false;
    }
    var $otherDialogContainer = $('#dialog_uploadfile');
    $otherDialogContainer.dialog({
        autoOpen: true,
        resizable: false,
        height: 250,
        title: 'Upload File',
        width: "50em",
        height:302,
        modal: true,
        closeText: "hide",
        open: function () {
            $('#dialog_uploadfile').removeClass('hide');
            hideLoader();
             $('.btn-primary').blur();
       },
        create: function(event, ui) { 
            
             $('.ui-dialog-titlebar-close').append('<span class="ui-button-icon-primary ui-icon"></span>');
             $('.ui-dialog-titlebar-close').attr("title", "Close");
             $('.ui-dialog-titlebar-close').attr("aria-label", "Close");
        },
        buttons: {
            Cancel:{
                        text: 'Cancel',
                        "title":"Cancel",
                        "class": 'btn btn-primary',
                        'aria-label': "Cancel",
                        click:function () {
                                $otherDialogContainer.dialog("close");
                                $.each($('.ui-dialog'), function (i, e) {
                                 $otherDialogContainer.dialog("close");
                            });
                        }
                    },
            Upload :{
                        text: 'Upload',
                        "title":"Upload",
                        "class": 'btn btn-primary',
                        'aria-label': "Upload",
                        click:function () {
                       		showLoader();
		                    var input = $("<input>").attr("type", "hidden").attr("name", "selected_node").val(selected_node);
                            $('#frm_uploadfile').append($(input));
                            $('#frm_uploadfile').submit();
                           // $("#upload_file").focus();
                        }
                    }
             }
    });
});
$('#copy_folder').click(function () 
{
    var ref = $('#tree').jstree(true);
    var selected_node = ref.get_selected();
    var selected_text = ref.get_text(selected_node);
    if(selected_node=='root')
    {
        return false;
    }
    if(selected_node==0)
    {
            alert('Please select the File/Folder to perform this action.');
            return false;
    }
    var type=$('#'+selected_node).attr('data-type');
    var children = ref.get_children_dom(selected_node);
    var children1 = [];
    children.each(function () {
            children1.push($(this).attr("id"));
    });
    //console.log(children1);return false;
    $('#copy_selected').val(selected_node);
    $('#copy_selected_childs').val(children1);
    $('#cut_selected').val(0);
    if(type == 0)
        alert('The file, "'+decodeHtml(selected_text)+'" has been copied.');
    else
        alert('The folder, "'+decodeHtml(selected_text)+'" has been copied.');
});
$('#paste_folder').click(function () 
{
    var ref = $('#tree').jstree(true);
    var selected_node = ref.get_selected();
    var selected_text = ref.get_text(selected_node);
    
    if(selected_node==0)
    {
            alert('Please select the File/Folder to perform this action.');
            return false;
    }
    var copy_selected=$('#copy_selected').val();
    var cut_selected=$('#cut_selected').val();
    var type='copy';
    if(cut_selected != 0)
        var type='cut';
    
    if(type=='cut'  && cut_selected  == 0){ return false;}
    if(type=='copy' && copy_selected == 0){ return false;}
    var team_id= jQuery("#team_id").val();
    var team_loc= jQuery("#team_loc").val();
    $.ajax({
        type: "POST",
        url: baseUrl+"team-documents/pastefolder",
        beforeSend:function(){
		    showLoader();
		},
        data: {'team_id':team_id,'team_loc':team_loc,'selected_node':selected_node,'copy_selected':copy_selected,'cut_selected':cut_selected,'type':type},
        dataType:'html',
        cache: false,
        success:function(data){
            setTimeout(function(){list_teamdocument(selected_node);},300);
        }
    });
});
$('#cut_folder').click(function () 
{
    var ref = $('#tree').jstree(true);
    var selected_node = ref.get_selected();
    var selected_text = ref.get_text(selected_node);
    if(selected_node=='root')
    {
        return false;
    }
    if(selected_node==0)
    {
            alert('Please select the File/Folder to perform this action.');
            return false;
    }
    /*var children = ref.get_children_dom(selected_node);
    var children1 = [];
    children.each(function () {
            children1.push($(this).attr("id"));
    });*/
    //console.log(children1);return false;
    var type=$('#'+selected_node).attr('data-type');
    $('#cut_selected').val(selected_node);
    $('#copy_selected').val(0);
    //$('#copy_selected_childs').val(children1);
    if(type == 0)
        alert('The file, "'+decodeHtml(selected_text)+'" has been cut.');
    else
        alert('The folder, "'+decodeHtml(selected_text)+'" has been cut.');
});
$("#permission_folder").click(function () 
{
    var ref = $('#tree').jstree(true);
    var selected_node = ref.get_selected();
    var selected_text = ref.get_text(selected_node);
    if(selected_node=='root')
    {
        return false;
    }
    if(selected_node==0)
    {
            alert('Please select the File/Folder to perform this action.');
            return false;
    }
    var type=$('#'+selected_node).attr('data-type');
    if(type == 0)
        title="Edit Permissions of '"+decodeHtml(selected_text)+"' File";
    else
        title="Edit Permissions of '"+decodeHtml(selected_text)+"' Folder";
    Url = baseUrl + "case-documents/getpermission&selected_node="+selected_node;
    var $otherDialogContainer = $('#dialog_permission');
    $otherDialogContainer.dialog({
        autoOpen: true,
        resizable: false,
        height: 200,
        title: title,
        width: 650,
        modal: true,
        closeText: "hide",
        open: function () {
            $('#dialog_permission').load(Url, function() { $('.btn-primary').blur(); $('input').customInput();});
            hideLoader();
       },
        create: function(event, ui) { 
             $('.ui-dialog-titlebar-close').append('<span class="ui-button-icon-primary ui-icon"></span>');
             $('.ui-dialog-titlebar-close').attr("title", "Close");
             $('.ui-dialog-titlebar-close').attr("aria-label", "Close");
        },
        buttons: {
            Cancel:{
                        text: 'Cancel',
                        "title":"Cancel",
                        "class": 'btn btn-primary',
                        'aria-label': "Cancel",
                        click:function () {
                                $otherDialogContainer.dialog("close");
                                $.each($('.ui-dialog'), function (i, e) {
                                 $otherDialogContainer.dialog("close");
                            });
                        }
                    },
            Edit :{
                        text: 'Update',
                        "title":"Update",
                        "class": 'btn btn-primary',
                        'aria-label': "Edit",
                        click:function () {
                            changeper(selected_node,"team-documents");
                            //var input = $("<input>").attr("type", "hidden").attr("name", "selected_node").val(selected_node);
                            //$('#frm_uploadfile').append($(input));
                            //$('#frm_uploadfile').submit();
                           // $("#upload_file").focus();
                        }
                    }
             }
    });
    return false;
});
function changeper(id,controller)
{
    var per=$('input[name=public_private]:checked').val();//$('#public_private').val();
    
    var caseId='<?php echo $caseId;?>';
    if(per!="")
    {
        $.ajax({
                 type: "POST",
                 url: baseUrl+controller+"/chkusertochangepermission",
                data: {'selected_node':id},
                dataType:'html',
                cache: false,
                success:function(data){
                    if(data=="Denied")
                    {
                            alert("This action is available only to the Folder/File Creater.");
                            $('#doc_permission').html(""); 
                            $('#dialog_permission').dialog("close");
                            return false;
                    }
                    else
                    {	
                        var team_id= jQuery("#team_id").val();
                        var team_loc= jQuery("#team_loc").val();
                        $.ajax({
                                type: "POST",
                                url: baseUrl+controller+"/changepermission",
                                data: {'selected_node':id,'per':per,'team_id':team_id,'team_loc':team_loc},
                                dataType:'html',
                                cache: false,
                                success:function(data){
                                            if(per==0)
                                                    name="Public";
                                            else if(per==1)
                                                    name="Private";
                                            
                                            //alert("The "+decodeHtml(data)+" is now "+ name+".");
                                             $('#dialog_permission').dialog("close");
                                             list_teamdocument(id);
                                }
                                });
                    }
                }
            });
    }
}

function search_doc(flag){
	$('#myteam-teamdoc').html(null);
    if(flag=='clear')
    {
      var v ='';  
      $('#search_doc').val('')
    }
    else
      var v = $('#search_doc').val();
        
    $('#tree').jstree(true).search(v);
}
function liclicked(obj)
{
    $('#selected_li').val(obj);
    $('#doc_permission').html('');
}
</script>
<noscript></noscript>