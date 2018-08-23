$.extend($.ui.dialog.prototype.options, {
	create: function(event, ui) { 
		 $('.ui-dialog-titlebar-close').append('<span class="ui-button-icon-primary ui-icon"></span>');
                $('.ui-dialog-titlebar-close').attr("title", "Close");
                $('.ui-dialog-titlebar-close').attr("aria-label", "Close");
	} 
});
jQuery(document).ready(function(){

$( "#prod_date,#prod_rec_date,#prod_access_req,#prod_agencies,#taskinstruct-task_duedate,#duedate,#start_date,#end_date,#start_submitted_date,#end_submitted_date,#start_due_date,#end_due_date,#start_completed_date,#end_completed_date").click(function(){
            $(this).next('span').find('a').trigger('click');

    });
    

date_prod_date = datePickerController.createDatePicker({
        formElements: { "prod_date": "%m/%d/%Y"},
        rangeLow:"19700313",
        callbackFunctions: {
			"datereturned" : [changeflag,function(){
                /*IRT-604var today = datePickerController.getSelectedDate("prod_date");
                var newdate = new Date();
                newdate.setDate(today.getDate());
                datePickerController.setRangeHigh("prod_rec_date", newdate);
                if(jQuery.trim(jQuery("#prod_date").val())=='00/00/0000'){
                    datePickerController.setEnabledDates("prod_rec_date", '*');
                }*/
            }],
		}
    });
var date_prod_rec_date = datePickerController.createDatePicker({	                     
        formElements: { "prod_rec_date": "%m/%d/%Y"},
        rangeLow:"19700313",
        callbackFunctions: {
			"datereturned" : [changeflag,function(){
                /*IRT-604var today = datePickerController.getSelectedDate("prod_rec_date");
                var newdate = new Date();
                newdate.setDate(today.getDate());
                datePickerController.setRangeLow("prod_date", newdate);
                if(jQuery.trim(jQuery("#prod_rec_date").val())=='00/00/0000'){
                    datePickerController.setEnabledDates("prod_date", '*');
                }*/
            }],
		}
    });  
    /*IRT-604*/  
    /*$(document).on('#chknodate','change,click',function(){
        alert(this.checked);
        if(this.checked==true){
            if(jQuery.trim(jQuery("#prod_date").val())=='00/00/0000'){
                   datePickerController.setEnabledDates("prod_rec_date", '*');
            }
        }
    });*/
    /*IRT-604*/  
 datePickerController.createDatePicker({	                     
        formElements: { "prod_access_req": "%m/%d/%Y"},
        callbackFunctions: {
			"datereturned" : [changeflag],
		}
    });   
 datePickerController.createDatePicker({	                     
        formElements: { "prod_agencies": "%m/%d/%Y"},
        callbackFunctions: {
			"datereturned" : [changeflag],
		}
    });  
    
    
    $('#btn_attach_media').click(function () {
            var att_type = $('#attachMedia:checked').val();
            var case_id=$('#evidenceproduction-client_case_id').val();
            var client_id=$('#evidenceproduction-client_id').val();
            var prod_party=$('#evidenceproduction-prod_party').val();
            var prod_rec_date=$('#prod_rec_date').val();
            
            //alert(prod_rec_date);
            if (att_type == 'N') //Attach new Media 
            {
                showLoader();
                var url = baseUrl + "case-production/attach-new-media";
                $.ajax({
                        type: "post",
                        url: url,
                        data: {"case_id": case_id,"client_id": client_id,"prod_party":prod_party,"prod_rec_date":prod_rec_date},
                        dataType: 'html',
                        success: function (data) {
                            hideLoader();
                            $('#production_form').hide();
                            $('#evidence_form').show();
                            $('#evidence_form').html(data);
                            $('#Evidence').ajaxForm({
                                beforeSubmit:function(arr, $form, options) { return validatemedia();},
                                success: submitmedia,
                            });
                        }
                    });
                //senddata = $('#addProduction').attr('action', url);
                //$('#addProduction').submit();
            }
            else
            {
                media_id = $('#evidenceproduction-medialist').val();
                if (media_id != "" || media_id != 0) {
                    attachmedia(media_id,'attach');
                } else {
                    alert("Please select a New or an Existing Media record to perform this action.");
                    return false;
                }
                /*end of with out popup code, DD one*/
            }

        });
});
function attachmedia(media_id,flag_jump)
{
    
    var str = $("#attachedMedia").val();
    var arr_media = str.split(',');
    var flag=true;
    $.each(arr_media, function(index,value) {
        if(value == media_id)
            flag=false;
    });
    if(flag == false)
    {
        alert('#'+media_id+' can only be attached one time.');
        return false;
    }
    var url = baseUrl + "case-production/attach-existing-media";
    $.ajax({
        type: "post",
        url: url,
        data: {"id": media_id},
        dataType: 'html',
        success: function (data) {
            if (data != "") {
                var atm = $("#attachedMedia").val();
                if (atm == '')
                    atm = media_id;
                else
                    atm = atm + ',' + media_id;
                $('#media_attached').append(data);
                $("#attachedMedia").val(atm);
            }
            if(flag_jump == 'attach_another')
                $('#btn_attach_media').trigger('click');
            else
            {
                $("#production_form").show();$("#evidence_form").hide();
            }
        },
        complete: function (){
			hideLoader();
		}	
    });
    
    
}
function submitmedia(responseText, statusText)
{
    media_arr=responseText.split('|');
    if(media_arr[1] == 'attach_another')
        attachmedia(responseText,'attach_another');
    else
        attachmedia(responseText,'attach');
}
/* Start : Case Production Management */

/* Start : Used to load case production list */
function list_caseproduction(){
	var case_id= jQuery("#case_id").val();
	location.href=baseUrl +'/case-production/index&case_id='+case_id;
	//	commonAjax(baseUrl +'/case-production/index&case_id='+case_id,'admin_main_container');
}

/* End : Used to load production list */
function addproduction(){
	var case_id= jQuery("#case_id").val();
    location.href=baseUrl +'/case-production/create&case_id='+case_id;
	//commonAjax(baseUrl +'/case-production/index&case_id='+case_id,'admin_main_container');
}
/* Start : Used to load case production list */
function UpdateProduction(id){
    var case_id= jQuery("#case_id").val();
    location.href=baseUrl +'/case-production/update&id='+id+'&case_id='+case_id;
}
/* End : Used to load production list */
function deleteAttachedMedia(mid) {
    //    if (confirm('You are detaching the selected Media from this production and any associated Production bates information, are you sure?')) {
            var prod_id = $("#prod_id").val();
            var list = $("#attachedMedia").val();
            var case_id = $("#caseId").val();
            var client_id = $("#client_id").val();
            if(prod_id != 0){
	            var url = baseUrl + "case-production/removemediaproduction/";
	            $.ajax({
	                type: "get",
	                url: url,
                        data: {"mid": mid,"prod_id": prod_id},
	                dataType: 'html',
	                success: function (data) {
	                    if (data == 'allow') {
	                        var newamt = removeValue(list, mid, ',');
	                        $("#attachedMedia").val(newamt);
	                        $('#atm_' + mid).remove();
                                if ($('#deleted_medias').val() != '') {
                                    $('#deleted_medias').val($('#deleted_medias').val() + "," + mid);
                                } else {
                                    $('#deleted_medias').val(mid);
                                }
                            $('#is_change_form_main').val('1');  
	                    }
	                    else {
	                        alert('#'+mid+' cannot be Deleted as it is associated with a Project.');
	                        return false;
	                    }
	                }
	            });
            } else {
            	var newamt = removeValue(list, mid, ',');
                $("#attachedMedia").val(newamt);
                $('#atm_' + mid).remove();
                $('#is_change_form_main').val('1');  
            }
      //  }
    }
function delete_document(name, obj)
{
    $(obj).parent().html('');
    if ($('#production_deleted_docs').val() != '') {
        $('#production_deleted_docs').val($('#production_deleted_docs').val() + "," + name);
    } else {
        $('#production_deleted_docs').val(name);
    }
}
function removeValue(list, value, separator) {
    separator = separator || ",";
    var values = list.split(separator);
    for (var i = 0; i < values.length; i++) {
        if (values[i] == value) {
            values.splice(i, 1);
            return values.join(separator);
        }
    }
    return list;
}
/* Delete Case Production code starts */
function deleteCaseProduction(production_id) {
    
    if(!production_id)
        var production_id = $('#caseproduction-grid').yiiGridView('getSelectedRows');
    
    var case_id= jQuery("#case_id").val();
    if(production_id != '') {
        //if (confirm("Are you sure you want to delete Production #"+production_id+" ?")) 
        {
            var chkUrl = baseUrl + "case-production/chk-task-exist-in-prodbates&record=" + production_id;
			
			$.ajax({
                type: "get",
                url: chkUrl,
                cache: false,
                success: function (mydata) {
					 if (mydata == 'Notallow') {
					    alert("#"+production_id+" cannot be Deleted as it is associated to 1+ Project.");
                        return false;
                    } else {
						if (mydata == 'allow'){
							var Url = baseUrl + "case-production/delete-production&id=" + production_id + "&caseId=" + case_id;
							$.ajax({
								type: "get",
								url: Url,
								cache: false,
								success: function (data) {
									
									location.reload();
								}
							});
						}else{
							alert(mydata);
						}

                    }
                }
            });
        }
    }else{alert("Please select a record to perform this action.");}

}
/* Delete Case Production code ends */

/* Edit Case Media Production code starts */
function editCaseMediaProductionDetail() {
    var case_id= jQuery("#case_id").val();
    var production_id = $('#caseproduction-grid').yiiGridView('getSelectedRows');
    var sel_row = '';
    var prod_id = '';
    var bates_id = '';
    var batesm_id = '';
    $('.media_datas:checkbox').each(function () {
        if (this.checked) {
            if (sel_row == '')
                sel_row = this.value;
            else
                sel_row += ',' + this.value;

            prod_id = $(this).attr('rel');
            bates_id = $(this).attr('data-id');
            batesm_id = $(this).attr('tabindex');
        }
    });
    if (sel_row != '') {
        var sel_rowArray = sel_row.split(",");
        var sel_rowArrayLenght = sel_rowArray.length;
        if (sel_rowArrayLenght > 1) {            
            alert("Please select a single record to perform this action.");return false;
        }
    } else {
        console.log('in sel_row else');
         alert("This action does not apply to a Production record.  Please select a Media record, within a Production, to perform this action.");
         return false;
    }
    if ($('#evid_prod_beats_' + sel_row + '_' + batesm_id).val() == '') {        
        alert("This action applies to Media records associated to a Project.  Please select a Media record, associated to a Project, to perform this action.");
        return false;
    }
        Url = baseUrl + "case-production/edit-prodcution-bates&case_id="+case_id+"&bates_id=" + batesm_id;
        if(!$( "#edit-case-media-production-detail" ).length){
            $('body').append("<div id='edit-case-media-production-detail'></div>");
        }
        $('#edit-case-media-production-detail').dialog({
            autoOpen: true,
            resizable: false,
            height:456,
            title: 'Update Media Production Bates',
            width:"50em",
            modal: true,
            closeText: "hide",
            open: function () {
				$('.btn').blur();
                $('#edit-case-media-production-detail').load(Url, function() {});
                hideLoader();
           },
            create: function(event, ui) { 
                 $('.ui-dialog-titlebar-close').append('<span class="ui-button-icon-primary ui-icon"></span>');
                 $('.ui-dialog-titlebar-close').attr("title", "Close");
                 $('.ui-dialog-titlebar-close').attr("aria-label", "Close");
            },
            close: function(){
				
				$(this).dialog('destroy').remove();
				
			},
			beforeClose: function(event){
				if(event.keyCode==27) trigger = 'esc';
				if(trigger != 'Update') checkformstatus(event);
			},
            buttons: {
                Cancel:{
                            text: 'Cancel',
                            "title":"Cancel",
                            "class": 'btn btn-primary',
                            'aria-label': "Cancel",
                            click:function () {
								trigger = 'Cancel';
								$(this).dialog("close");
						    }
                        },
                Update :{
                            text: 'Update',
                            "title":"Update",
                            "class": 'btn btn-primary',
                            'aria-label': "Update",
                            click:function () {
									trigger = 'Update';
                                    url = $("#EvidenceProductionBates").attr('action');
                                    var addCasemediadata = $("#EvidenceProductionBates").serialize();
                                    $.ajax({
                                        type: "post",
                                        url: url,
                                        data: addCasemediadata, //{'YII_CSRF_TOKEN':$("#token").val(),"id":media_id},
                                        dataType: 'html',
                                        success: function (data) {
                                            location.reload();
                                          //  dialog.dialog("close");
                                          //  table.ajax.reload(null, false);
                                        }
                                    });
                            }
                        }
                 },
                 
        });
        return false;
    }
/* Edit Case Media Production code ends */

function CaseProductionMediaHold() {
    var case_id= jQuery("#case_id").val();
    var sel_row = '';
    var prod_id = '';
    $('.media_datas:checkbox').each(function () {
        if (this.checked) {
            if (sel_row == '')
                sel_row = this.value;
            else
                sel_row += ',' + this.value;
            if (prod_id == '')
                prod_id = $(this).attr('rel');
            else
                prod_id += ',' + $(this).attr('rel');
        }
    });
    if (sel_row != '') {
        var sel_rowArray = sel_row.toString().split(",");
        var prod_rowArray = prod_id.toString().split(",");
        var prod_rowArrayLenght = prod_rowArray.length;
        var sel_rowArrayLenght = sel_rowArray.length;
    } else {
        alert("This action does not apply to a Production record.  Please select a Media record, within a Production, to perform this action.");return false;
    }
    
        var url = baseUrl + "case-production/makehold-caseproduction";
        $.ajax({
            type: "POST",
            url: url,
            data: { 'caseId': case_id, 'record': sel_row, 'prod_id': prod_id},
            cache: false,
            success: function (data) {
                location.reload();
            }
        });
}
function AddProductionAttorneyNotes() {
    var production_id = $('#caseproduction-grid').yiiGridView('getSelectedRows');
    if(production_id != '') {
        showLoader();
        Url = baseUrl + "case-production/addproduction-attorney&record=" + production_id;

        if(!$( "#add-production-attorney-notes" ).length){
            $('body').append("<div id='add-production-attorney-notes'></div>");
        }
        
        $('#add-production-attorney-notes').dialog({
            autoOpen: true,
            resizable: false,
            height: 692,
            title: 'Add Attorney Notes',
            width: '80em',
            modal: true,
            closeText: "hide",
            open: function () {
                $('#add-production-attorney-notes').load(Url, function() {$('#evidenceproduction-attorney_notes').focus();});
                
                hideLoader();
           },
            create: function(event, ui) { 
                 $('.ui-dialog-titlebar-close').append('<span class="ui-button-icon-primary ui-icon"></span>');
                 $('.ui-dialog-titlebar-close').attr("title", "Close");
                 $('.ui-dialog-titlebar-close').attr("aria-label", "Close");
            },
            close: function(){
				$(this).dialog('destroy').remove();
			},
            beforeClose: function(event){
				if(event.keyCode==27) trigger = 'esc';
				if(trigger!='Update') checkformstatus(event);
			},
            buttons: {
                Cancel:{
                            text: 'Cancel',
                            "title":"Cancel",
                            "class": 'btn btn-primary',
                            'aria-label': "Cancel",
                            click:function () {
								trigger = 'Cancel';
								$( this ).dialog( "close" );
                            }
                        },
                Update :{
                            text: 'Update',
                            "title":"Update",
                            "class": 'btn btn-primary',
                            'aria-label': "Update",
                            click:function () {
								trigger = 'Update';
								var frmproductiondata = $("#EvidenceProduction").serialize();
								$.ajax({
									type: "post",
									url: Url,
									data: frmproductiondata,
									dataType: 'html',
									success: function (data) {
									   if($.trim(data) == 'OK')
                                            location.reload();
                                       else
                                           $('#add-production-attorney-notes').html(data);      
									}
								});
                            }
                        }
                 }
        });
        
    }else{alert("Please select a record to perform this action.");}
}

/* Start : Used to load case document list */
function list_casedocument(node){
    var case_id= jQuery("#case_id").val();
    if(!node){node=0;}
    location.href=baseUrl +'/case-documents/index&case_id='+case_id+'&node_id='+node;
}
function ProductionShortcut()
{
    var case_id= jQuery("#case_id").val();
    location.href=baseUrl +'/case-production/caseproductionshortcut&case_id='+case_id;
}
/* Start : Used to load case production list */
function list_projectdocument(type){
	var case_id= jQuery("#case_id").val();
        location.href=baseUrl +'/case-documents/projectdoc&case_id='+case_id+'&type='+type;
}
