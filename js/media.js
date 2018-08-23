$.extend($.ui.dialog.prototype.options, {
	create: function(event, ui) { 
		 $('.ui-dialog-titlebar-close').append('<span class="ui-button-icon-primary ui-icon"></span>');
                $('.ui-dialog-titlebar-close').attr("title", "Close");
                $('.ui-dialog-titlebar-close').attr("aria-label", "Close");
	} 
});

jQuery(document).ready(function(){
    $('#evidence-org_link').on("change", function () {
		$.ajax({
	       url: baseUrl +'/media/bring-media-list',
	       type: 'get',
	       beforeSend:function (data) {showLoader();},
	       success: function (data) {
	    	   hideLoader();
	    	   $('#media_container').html(data);
	       } 
		});
	});
});

/* Start : Client Management */
/* Start : Used to load clients list */
function loadClient(){
	commonAjax(baseUrl +'/client/index','admin_main_container');
}
/* End : Used to load clients list */

/* Start : Used to load Add new media Form */
function addMedia(){
	jQuery.ajax({
	       url: baseUrl +'/media/create',
	       type: 'get',
	       beforeSend:function (data) {showLoader();},
	       success: function (data) {
	    	   hideLoader();
	    	   jQuery('#media_container').html(data);
	       } 
	});
}
/* End : Used to load Add new media Form */
/* Start : Display All medias */
function displayAllMedias(){
    location.href = baseUrl +'/media/index';
}
/* End : Display All medias */
/* Start : Used to load Add new media content Form */
function openaddevidcontent()
{
    var url = baseUrl + "media/add-evidence-content/";
    //var case_ids = $('#evidence-case_id').val();
    var case_ids=[];
    var client_ids=[];
    /*$("#evidence-case_id option:selected" ).each(function() {
        if($( this ).data('id'))
           case_ids.push($( this ).data('id'));
       else
       {
           var strval=$(this ).val();
           var arr_val = strval.split('|');
           case_ids.push(arr_val[0]);
       }
    });*/
    var strcase=$("#evidence-case_id").val();
    var arr_case = strcase.split(',');
    $.each(arr_case, function(index,value) {
           var arr_val = value.split('|');
           case_ids.push(arr_val[0]);
           client_ids.push(arr_val[1]);
    });
   // var client_id = $('#client_id').val();
    var client_id = client_ids;
    
    var edit_evid_id = $('.SectionBottom #Evidence_id').val();
	var edit_evid_id ='0';
    var dt = new Date();
    var temp_evid_id = dt.getHours() + "" + dt.getMinutes() + "" + dt.getSeconds();
    if (case_ids != "") {
        if($('#evidContentFrm2').length==0) {
			$('body').append('<div id="evidContentFrm2"></div>');
        }
        
    	showLoader();
        $.ajax({
            type: "post",
            url: url,
           // async:true,
             async: false,
            data: { "case_id": case_ids, "client_id": client_id, "temp_evid_id": temp_evid_id, "evid_id": edit_evid_id},
            dataType: 'html',
            success: function (response) {
                $('#evidContentFrm2').html('').html(response);
                /* Start : to set temp custodians into DD */
                //console.log($('.custodians_list').attr('id'));
                $('.custodians_list').each(function () {
                    var tmp_cust_id = $(this).attr('id');
                    var cust_lname = $(this).find('input[class="cust_lname"]').val();
                    var cust_fname = $(this).find('input[class="cust_fname"]').val();
                    var cust_mi = $(this).find('input[class="cust_mi"]').val();
                    //console.log(tmp_cust_id + " " + cust_lname + ", " + cust_fname +" "+ cust_mi);
                    $('#evidencecontents-cust_id').append("<option value='" + tmp_cust_id + "'>" + cust_lname + ", " + cust_fname + " " + cust_mi + "</option>");
                    
                });
                // $('#evid_content_list').show();
                $('.table-responsive').show();
                
                /* End : to set temp custodians into DD */
            },
            complete: function () 
            {
                var $otherDialogContainer = $('#evidContentFrm2');
                $('#EvidenceContents').ajaxForm({
                    success: SubmitSuccesfulcustodianForm,
                    complete: function(response){
	                	if($('#EvidenceContents').find('#form-action-type').val() == 'Add-another'){
							clearallContent();   
	                	    $($otherDialogContainer).dialog("close");
	                		openaddevidcontent();
	                	}else{
							clearallContent();   
							$($otherDialogContainer).dialog("close");
						}
                	}	 
                });
                
                $otherDialogContainer.dialog({
                    autoOpen: true,
                    resizable: false,
                    height: 456,
                    title: 'Add Media Contents',
                    width: '50em',
                    modal: true,
                    //closeText: "hide",
                    open: function () {
					    $(".ui-dialog").removeAttr("tabindex");
                       // $('.ui-dialog-buttonpane').find('button:contains("Add Another")').attr('class', 'button');
                        hideLoader();
                    },
                    create: function(event, ui) {
						//if($('.ui-dialog-titlebar-close').html() != '<span class="ui-button-icon-primary ui-icon"></span>'){
							$('#evidContentFrm2').prev().find('.ui-dialog-titlebar-close').append('<span class="ui-button-icon-primary ui-icon"></span>');
							$('#evidContentFrm2').prev().find('.ui-dialog-titlebar-close').attr("title", "Close");
							$('#evidContentFrm2').prev().find('.ui-dialog-titlebar-close').attr("aria-label", "Close");
						//}
                        // $otherDialogContainer.dialog( "option", "closeText", "hidess" );
                    },
                    buttons: {
                        "Cancel": {
							text: 'Cancel',
							"title":"Cancel",
							"class": 'btn btn-primary',
							'aria-label': "Cancel New Media Content",
							click:function (event) {
								$otherDialogContainer.dialog("close");
								$.each($('.ui-dialog'), function (i, e) {
									$otherDialogContainer.dialog("close");
								});
							}
					    },
                        "Add Another": {
                             text: 'Add Another',
                             click: function(event){
								$('#Evidence #is_change_form').val('1');
								$('#Evidence #is_change_form_main').val('1');
								$('#EvidenceContents').find('#form-action-type').val('Add-another');
								AppendAddAnother($otherDialogContainer);
							 },
                             'class': 'btn btn-primary',
                             'title': 'Add Another',
							 'aria-label': 'Add Another New Media Content',
                        },
                        "Add": {
							 text: 'Add',
							 "title":"Add",
							 "class": 'btn btn-primary',
							 'aria-label': "Add New Media Contentssssss",
							 click:function () {
								$('#Evidence #is_change_form').val('1');
								$('#Evidence #is_change_form_main').val('1');
                        		$('#EvidenceContents').find('#form-action-type').val('Add');
								validateContent($otherDialogContainer);
							 }
						},
                    },
                    close: function(event) {
						$(this).dialog('destroy').remove();
					}
                });
            }
        });
    }
}

function validateContent(obj)
{
    var url = baseUrl + "media/contentvalidate/";
    $.ajax({
       type: "post",
       url: url,
       async:true,
       data: $('#EvidenceContents').serialize(),
       success: function (response) {
           if(response.length == 0){
                $("#EvidenceContents").submit();
           }
           else{
			for (var key in response) {
				$("#"+key).next().next().html(response[key]);
				$("#"+key).parent().parent().parent().addClass('has-error');
			}
           }
        }
    });
}
function AppendAddAnother(obj) {
	var form = $('#EvidenceContents');
    var url = baseUrl + "media/contentvalidate/";
    $.ajax({
       type: "post",
       url: url,
       async:true,
       data: form.serialize(),
       success: function (response) {
           if(response.length == 0){
        	   form.submit();
        	  // openaddevidcontent();
                //clearallContent();
           }
           else{
            for (var key in response) {
                         $("#"+key).next().html(response[key]);
                         $("#"+key).parent().parent().parent().addClass('has-error');
                     }
           }
        }
    });
    
}
function evidencecontentaction(action, id, custodian_name)
{
    if (action == 'delete') {
        if (confirm("Are you sure you want to Delete "+custodian_name+"?")) {
            showLoader();
            var evid_id = $('#evid_content_list #tmp_evid_num_id').val();
            if (evid_id != 0) {
                $.ajax({
                    //type: "POST",
                    url: httpPath + "evidence/deleteEvidContent/",
                    data: {'YII_CSRF_TOKEN': $("#token").val(), 'records': id, 'evid_num_id': evid_id},
                    dataType: 'html',
                    cache: false,
                    success: function (data) {
                        $('#evid_content_list #row_evid_content_' + id).remove();
                       hideLoader();
                    }
                });
            }
            if(evid_id == 0){
            	hideLoader();
            }
            $('#evid_content_list #row_evid_content_' + id).remove();
            $('#Evidence #is_change_form_main').val('1');
        }
    } else {
    	showLoader();
        var cust_id = $('#evid_content_list input[name="EvidenceContent[' + id + '][cust_id]"]').val();
        var data_type = $('#evid_content_list input[name="EvidenceContent[' + id + '][data_type]"]').val();
        var data_size = $('#evid_content_list input[name="EvidenceContent[' + id + '][data_size]"]').val();
        var unit = $('#evid_content_list input[name="EvidenceContent[' + id + '][unit]"]').val();
        var data_copied_to = $('#evid_content_list input[name="EvidenceContent[' + id + '][data_copied_to]"]').val();
        /*if ($('#client_id').length) {
            var client_id = $('#client_id').val();
        } else {
            var client_id = $('#client_id').val();
        }
         var case_ids=[];
        $("#evidence-case_id option:selected" ).each(function() {
            case_ids.push($( this ).data('id'));
        });*/
        
         var case_ids=[]
        var client_ids=[];
        var strcase=$("#evidence-case_id").val();
        var arr_case = strcase.split(',');
        $.each(arr_case, function(index,value) {
               var arr_val = value.split('|');
               case_ids.push(arr_val[0]);
               client_ids.push(arr_val[1]);
        });
        var client_id = client_ids;
        $('#evid_content_list #editEvidContentId').val(id);
        var edit_evid_id = $('.SectionBottom #Evidence_id').val();
        
        if($('#evidContentFrm2').length==0) {
            $('body').append('<div id="evidContentFrm2"></div>');
        }
        $.ajax({
            type: "POST",
            async:true,
            url : baseUrl + "media/edit-evidence-content/",
            data: {'client_id': client_id, 'case_id': case_ids, 'cust_id': cust_id, 'data_type': data_type, 'data_size': data_size, 'unit': unit, 'data_copied_to': data_copied_to, 'temp_evid_id': id, "evid_id": edit_evid_id, 'edit_content_id': id,'type':'edit'},
            dataType: 'html',
            cache: false,
            success: function (data) {
                $('#evidContentFrm2').html(data);
                // Start : to set temp custodians into DD 
                $('.custodians_list').each(function () {
                    var tmp_cust_id = $(this).attr('id');
                    var cust_lname = $(this).find('input[class="cust_lname"]').val();
                    var cust_fname = $(this).find('input[class="cust_fname"]').val();
                    var cust_mi = $(this).find('input[class="cust_mi"]').val();
                    $('#evidencecontents-cust_id').append("<option value='" + tmp_cust_id + "'>" + cust_lname + ", " + cust_fname + " " + cust_mi + "</option>");
                });
                $('#evidencecontents-cust_id').val(cust_id);
                // End : to set temp custodians into DD 
                $('#EvidenceContents').ajaxForm({
                    success: SubmitEditEvidenceForm,
                });
            },
            complete: function () {
                var $otherDialogContainer = $('#evidContentFrm2');

                $otherDialogContainer.dialog({
                    autoOpen: true,
                    resizable: false,
                    height: 350,
                    title: 'Edit Media Contents',
                    width: 800,
                    modal: true,
                    open:function(){
                		hideLoader();
                	},
                    create: function(event, ui) { 
						 $('.ui-dialog-titlebar-close').append('<span class="ui-button-icon-primary ui-icon"></span>');
                         $('.ui-dialog-titlebar-close').attr("title", "Close");
                         $('.ui-dialog-titlebar-close').attr("aria-label", "Close");
                    },    
                    buttons: {
                        "Cancel": {
							"class": 'btn btn-primary',
							"text": "Cancel",
							"title": "Cancel",
							'aria-label': "Cancel",
							click: function(event){
								$otherDialogContainer.dialog("close");
								$.each($('.ui-dialog'), function (i, e) {
									$otherDialogContainer.dialog("close");
								});
							}
                        },
                        "Update": {
							"class": 'btn btn-primary',
							"text": "Update",
							"title": "Update",
							'aria-label': "Update Media Content",
							click: function(){
								validateContent($otherDialogContainer);
								$otherDialogContainer.dialog("close");
							}
                        },
                    },
                    close: function(event) {
						$otherDialogContainer.dialog("close");
					} 
                });
            }
        });
    }
}


function SubmitEditEvidenceForm(responseText, statusText) {
    if (responseText != "no") {
        var evid_id = $('#evid_content_list #editEvidContentId').val();
        if (evid_id != "") {
                $('#evid_content_list #row_evid_content_' + evid_id).html(responseText);
        }
        $('#evid_content_list #editEvidContentId').val('');
    }
    else {
     //   alert("Opps. Something Wrong...");
    }
}
function clearallContent() {
    $('#evidencecontents-cust_id').val('');
    $('#evidencecontents-data_type').val('');
    $('#evidencecontents-data_size').val('');
    $('#evidencecontents-unit').val('');
    $('#evidencecontents-data_copied_to').val('');
}

 /*$('#EvidenceContents').ajaxForm({
        success: SubmitSuccesfulcustodianForm,
    });*/
    function SubmitSuccesfulcustodianForm(responseText, statusText) {
        //alert(responseText);
        if (responseText != "no") {
            $('#evid_content_list').append(responseText);

        }
        else {
          //  alert("Opps. Something Wrong...");
        }
    }
/* End : Used to load Add new media content Form */

/* Start : Used to load Add new custodian Form */
function openaddcust()
{
		/* $('#add-case-custodian-form').ajaxForm({
			   success: SubmitSuccesful,
		 }); */
		var $custodianDialogContainer = $('#addevidcust');
         
        $custodianDialogContainer.dialog({
            autoOpen: false,
            resizable: false,
            height: 456,
            width: '50em',
            modal: true,
            create: function(event, ui) { 
			//if($('.ui-dialog-titlebar-close').html() != '<span class="ui-button-icon-primary ui-icon"></span>'){	
				$('#addevidcust').prev().find('.ui-dialog-titlebar-close').append('<span class="ui-button-icon-primary ui-icon"></span>');
				$('#addevidcust').prev().find('.ui-dialog-titlebar-close').attr("title", "Close");
				$('#addevidcust').prev().find('.ui-dialog-titlebar-close').attr("aria-label", "Close");
			//}
            },
            buttons: {
                'Cancel': {
                        text: 'Cancel',
                        "title": 'Cancel',
                        "id" : "cancel-btn",
                        "class": 'btn btn-primary',
                        'aria-label': "Cancel New Custodian Data",
							click:  function (event) {
								$custodianDialogContainer.dialog("close");
								$.each($('.ui-dialog'), function (i, e) {
									$custodianDialogContainer.dialog("close");
								});
							}
                        },
                "Add":  {
                            text: 'Add',
                            "title": 'Add',
                            "class": 'btn btn-primary',
                            "id" : "add-btn",
                            'aria-label': "Add New Custodian Data",
                            click: function () {
							    validate($custodianDialogContainer);
							}
					},
		    },
		   close: function(event){
				$custodianDialogContainer.dialog("close");
			} 
        });
		$custodianDialogContainer.dialog("open");
 }
 
 function validate(obj)
    {
	     var url = baseUrl + "media/custodianvalidate/";
	     $("#EvidenceCustodians").find(":hidden").remove();
	     $.ajax({
            type: "post",
            url: url,
            async:true,
            data: $('#EvidenceCustodians').serialize(),
            success: function (response) {
                if(response.length == 0){
                    var cust_fname = $('#evidencecustodians-cust_fname').val();
                    var cust_lname = $('#evidencecustodians-cust_lname').val();
                    var cust_mi = $('#evidencecustodians-cust_mi').val();
                    var cust_title = $('#evidencecustodians-title').val();
                    var cust_dept = $('#evidencecustodians-dept').val();
                    var cust_email = $('#evidencecustodians-cust_email').val();
                    var dt = new Date();
                    var tmp_cust_id = dt.getHours() + "" + dt.getMinutes() + "" + dt.getSeconds();
				    var custodian_values = "<div class='custodians_list' id='" + tmp_cust_id + "'>";
				    custodian_values += "<input type='hidden' class='cust_fname' name='EvidenceCustodian[" + tmp_cust_id + "][cust_fname]' value='" + cust_fname + "'/>";
                    custodian_values += "<input type='hidden' class='cust_lname' name='EvidenceCustodian[" + tmp_cust_id + "][cust_lname]' value='" + cust_lname + "'/>";
                    custodian_values += "<input type='hidden' class='cust_mi' name='EvidenceCustodian[" + tmp_cust_id + "][cust_mi]' value='" + cust_mi + "'/>";
                    custodian_values += "<input type='hidden' class='cust_email' name='EvidenceCustodian[" + tmp_cust_id + "][cust_email]' value='" + cust_email + "'/>";
                    custodian_values += "<input type='hidden' class='cust_title' name='EvidenceCustodian[" + tmp_cust_id + "][title]' value='" + cust_title + "'/>";
                    custodian_values += "<input type='hidden' class='cust_dept' name='EvidenceCustodian[" + tmp_cust_id + "][dept]' value='" + cust_dept + "'/>";
                    custodian_values += "<input type='hidden' class='cust_fullname' value='" + cust_lname + ", " + cust_fname + " " + cust_mi + "'/>";
                    custodian_values += "</div>";
                    
                    if ($('body div.wrap').find('#media_container').find('#Evidence #evid_custodian_list').length) {
                        $('body div.wrap').find('#media_container').find('#Evidence #evid_custodian_list').append(custodian_values);
                    }
                    else {
                        
                        if ($('body div.wrap').find('#admin_main_container').find('#Evidence #evid_custodian_list').length) {
                            $('body div.wrap').find('#admin_main_container').find('#Evidence #evid_custodian_list').append(custodian_values);
                        }else{
                            $('body div.wrap').find('#media_container').find('#Evidence #evid_custodian_list').append(custodian_values);
                        }
                    }
                    
                    $('#case_by_client #evidencecontents-cust_id').append("<option value='" + tmp_cust_id + "'>" + cust_lname + ", " + cust_fname + " " + cust_mi + "</option>");
                    $("#case_by_client #evidencecontents-cust_id").val(tmp_cust_id).trigger("change");
                    $('#case_by_client #evidencecontents-cust_id').val(tmp_cust_id);
                    clearall();
                    $('#Evidence #is_change_form').val('1'); // change flag to 1
					$('#Evidence #is_change_form_main').val('1'); // change flag to 1
					$(obj).dialog("close");
                }else{
                        for (var key in response) {
                            $("#"+key).next().html(response[key]);
                            $("#"+key).parent().parent().parent().addClass('has-error');
                        }
                }
            }
         });
    }
    function clearall()
    {
       $('#evidencecustodians-cust_fname').val('');
       $('#evidencecustodians-cust_lname').val('');
       $('#evidencecustodians-cust_mi').val('');
       $('#evidencecustodians-title').val('');
       $('#evidencecustodians-dept').val('');
       $('#evidencecustodians-cust_email').val('');
    }
   
/* End : Used to load Add new custodian Form */

function downloadattachment(attach) {
        method = "get";
        var path = baseUrl + "media/downloadfiles&name="+attach;
        location.href=path;
       /* var form = document.createElement("form");
        form.setAttribute("method", method);
        form.setAttribute("action", path);
        alert(form.action);
        var hiddenField = document.createElement("input");
        hiddenField.setAttribute("type", "hidden");
        hiddenField.setAttribute("name", 'name');
        hiddenField.setAttribute("value", attach);
        form.appendChild(hiddenField);*/
       /* var hiddenField = document.createElement("input");
        hiddenField.setAttribute("type", "hidden");
        hiddenField.setAttribute("name", 'YII_CSRF_TOKEN');
        hiddenField.setAttribute("value", '<?php echo Yii::app()->request->csrfToken ?>');
        form.appendChild(hiddenField);*/
        //document.body.appendChild(form);
        //form.submit();
    }
    //deleteMedia

    /**
     * @abstract This code is written to Delete Media
     * @author jayant
     */
    function deletemedia(media_ids)
    {
        showLoader();
        $.ajax({
            url: baseUrl + "media/delete-media&records=" + media_ids,
            cache: false,
            dataType: 'html',
            success: function (data) {
                hideLoader();
                if (data == 'Denied'){
                    alert("Media #"+media_ids+" cannot be deleted because it is associated in a Case Production and/or Project.");
                }
                else
                {
                   location.href = baseUrl + "media/index";
                }
            }
        });
    }  
    
     /**
     * @abstract This code is written to load a Edit Evidence Form
     * 
     */
    function editmedia(media_ids)
    {
         $.ajax({
                url: baseUrl + "media/get-evidence-status&id=" + media_ids,
                cache: false,
                dataType: 'html',
                success: function (data) {		
                       if(data == 'false'){
                            alert('Media in Destroy Status cannot be Edited or Copied.');
                        }else{
                            showLoader();
                            $.ajax({
                                url: baseUrl + "media/edit-evidence&id=" + media_ids,
                                cache: false,
                                dataType: 'html',
                                success: function (data) {
                                    hideLoader();
                                    if (data != "") {
                                        jQuery('#media_container').html(data);
                                        $('#accordion-container .sidebar-acordian li a').removeClass('active');
                                        $('#accordion-container .sidebar-acordian li a#edit_media').addClass('active');
                                       // $('#evdRightImg').attr("src", imagePath + 'evidence_edit.png');
                                        //$("#evidTopWrd").html("Edit Media (#" + sel_row + ")");
                                        $('#client_case_filtter').hide();

                                    }
                                }
                            });  
                        }
                }
        });         
    }
    
     /**
     * @abstract This code is written to load a copy Evidence Form
     * 
     */
    function copymedia(media_ids)
    {   
        $.ajax({
                url: baseUrl + "media/get-evidence-status&id=" + media_ids,
                cache: false,
                dataType: 'html',
                success: function (data) {
                    hideLoader();
                    if (data != "") {
                        if(data == 'false'){
                            alert('Media in Destroy Status cannot be Edited or Copied.');
                        }else{
                            if(confirm('Are you sure you want to copy Media #'+media_ids+'?')){
                            showLoader();
                            $.ajax({
                                url: baseUrl + "media/copy&id=" + media_ids,
                                cache: false,
                                dataType: 'html',
                                success: function (data) {
                                    hideLoader();
                                    if (data != "") {                                       
                                            jQuery('#media_container').html(data);
                                            $('#accordion-container .sidebar-acordian li a').removeClass('active');
                                            $('#accordion-container .sidebar-acordian li a#edit_media').addClass('active');
                                            // $('#evdRightImg').attr("src", imagePath + 'evidence_edit.png');
                                            //$("#evidTopWrd").html("Edit Media (#" + sel_row + ")");
                                            $('#client_case_filtter').hide();                                        
                                    }
                                }
                            });
                        }
                        }
                    }
                }
            });       
    }
    // End of code to delete Evidence
    /**
     * @abstract This code is written to change check out in status media
     * 
     */
	function check_out_in(){
		if(!$('#checkoutin_media').hasClass('active'))
		{
			var media_ids = $('#media-grid').yiiGridView('getSelectedRows');
			if(!media_ids.length){
			   alert('Please select at least 1 record to perform this action.');
			} else {
				$.ajax({
				//	type:'post',
					url: baseUrl + "media/check-out-instatus&id=" + media_ids,
				//	data:"id="+media_ids,
					cache: false,
					dataType: 'html',
					success: function (data) {
						if (data.replace(/^\s+|\s+$/g, "") == 'Denied')
						{
							alert("Media in ‘Destroyed’ status cannot be checked back in.");
						}
						else{
						    showLoader();
							$.ajax({
							//	type:'post',
								url: baseUrl + "media/change-status&id=" + media_ids,
								// async: false,
							//	data:'id=' + media_ids,
								cache: false,
								dataType: 'html',
								success: function (data) {
									hideLoader();
									if (data != "") {
										$('.chainofcustody_media_li').hide();
										$('#media_container').html(data);
										$('#accordion-container .sidebar-acordian li a').removeClass('active');
										$('#accordion-container .sidebar-acordian li a#checkoutin_media').addClass('active');
									// $('#evdRightImg').attr("src", imagePath + 'evidence_edit.png');
									     $(".tag-header-red").html("Media Inventory  - Check Out/In (# " + media_ids + ")");
                                         $(".tag-header-red").attr("title","Media Inventory  - Check Out/In (# " + media_ids + ")");
                                         $('#media_filter_li').hide();
									}
								}
							});
						}
					}
				});
			}
		}
    }
	//End of Code for Check_Out_In Evidence Form

	/**
     * @abstract This code is written to display chain status of Media
     * 
     */
	function chain_of_custody(mod){
		//if(!$('#chainofcustody_media').hasClass('active')){
		if(!$('#transaction-pajax').hasClass('active')) {
			if(mod == 'all'){
				var media_ids = $('#evidNum').val();
				showLoader();
				$.ajax({
					url: baseUrl + "media/chain-of-custody&id=" + media_ids,
					cache: false,
					dataType: 'html',
					success: function (data) {
						hideLoader();
						if (data != "") {
							$('.checkoutin_media_li').hide();
							$('#media_container').html(data);
							if($('.table-responsive') && $('.kv-panel-pager')){
								var grid_height = $('.table-responsive').height()-$('.kv-panel-pager').height();
								$('.kv-grid-wrapper').height(grid_height);
							}
							$('#accordion-container .sidebar-acordian li a').removeClass('active');
							$('#accordion-container .sidebar-acordian li a#chainofcustody_media').addClass('active');
                            $(".tag-header-red").html("Media Inventory  - Chain Of Custody (# " + media_ids + ")");
                            $(".tag-header-red").attr("title","Media Inventory  - Chain Of Custody (# " + media_ids + ")");
                            $('#media_filter_li').hide();
						}
					}
				});
			}else{
				var media_ids = $('#media-grid').yiiGridView('getSelectedRows');
				if(media_ids.length > 1 || !media_ids.length){
				   alert('Please select a single record to perform this action.');
				} else {
					if(media_ids.length == 1){			
					   showLoader();
						$.ajax({
							url: baseUrl + "media/chain-of-custody&id=" + media_ids,
							cache: false,
							dataType: 'html',
							success: function (data) {
								hideLoader();
								if (data != "") {
									$('.checkoutin_media_li').hide();
									$('#media_container').html(data);
									if($('.table-responsive') && $('.kv-panel-pager'))
									{
										var grid_height = $('.table-responsive').height()-$('.kv-panel-pager').height();
										$('.kv-grid-wrapper').height(grid_height);
									}
									 $('#accordion-container .sidebar-acordian li a').removeClass('active');
									 $('#accordion-container .sidebar-acordian li a#chainofcustody_media').addClass('active');
                                     $(".tag-header-red").html("Media Inventory  - Chain Of Custody (# " + media_ids + ")");
                                     $(".tag-header-red").attr("title","Media Inventory  - Chain Of Custody (# " + media_ids + ")");
                                     $('#media_filter_li').hide();
									//$('#evdRightImg').attr("src", imagePath + 'evidence_edit.png');
									//$("#evidTopWrd").html("Chain of Custody (#" + sel_row + ")");
								}
							}
						});
					} else {
						alert('Please select a single record to perform this action.');
					}
				}
			}
		}
	}
	//End of Code for Chain of custody Evidence Form
	
/********************************  Barcode related Javascript Starts ****************************************************/	
	/**
     * @abstract This code is written to get evidence from barcode
     * 
     */
     function searchIt(){
	 var barcode=$('#scanned_barcode').val();
	 var trans_type=$('#evidencetransaction-trans_type').val();
	 var trans_requested_by=$('#evidencetransaction-trans_requested_by').val();
	 //var Trans_to=$('#EvidenceTransactions_Trans_to').val();
	 var moved_to=$('#evidencetransaction-moved_to').val();
	 if(barcode==''){
		 alert("Please scan a barcode to perform this action.");
		 $('#scanned_barcode').focus();
	 }
	 else if(trans_type==""){
		 alert("Please select Transaction Type.");
		 $('#evidencetransaction-trans_type').focus();
	 }	
	 else if(trans_requested_by==""){
		 alert("Please select Transaction Requested By.");
		 $('#evidencetransaction-trans_requested_by').focus();
	 } else {
		 if(trans_type==4){
			 if(moved_to==""){
				 alert("Please select Move to Stored Location.");
				 $('#evidencetransaction-moved_to').focus();
				 $("#scanned_barcode").val(null);
				 return false;
			}
		 }
		// var image_path=baseUrl;
		 var scanned_media=$('#scanned_mids').val();
		 var url=baseUrl+"barcode/getscannedmedia";
		 $.ajax({
			 
			 url:url,
			 type:'get',
             data:{barcode:barcode, scanned_media:scanned_media, trans_type:trans_type},
			 success:function(res){
				 if(res=="notallow"){
					 alert('The selected transaction cannot be applied due to the Media Status.');
					 $("#scanned_barcode").val(null);
				 }
				 else{
				 newscan_meids=scanned_media;
				 var obj = jQuery.parseJSON(res);
			 	 	$.each(obj, function(mid, bar) {
						if(!$('#scanmid_'+mid).length){
							if($('#scanned_mids').val()=="")
								$('#scanned_mids').val(mid);
							else
								$('#scanned_mids').val($('#scanned_mids').val()+','+mid);
							
							dtaaa='<tr id="scanmid_'+mid+'"><td>'+mid+' - ' +bar+'</td>'+
                                                                        '<td align="center"><a href="javascript:void(0)" onclick="delete_scanmedia('+mid+')" class="icon-fa" title="Delete"><em class="fa fa-close" aria-label="Remove" title="Delete"></em><span class="hide">Remove</span></a></td>'+
								'</tr>';
							$("#media_scanned").find('div.col-md-7 tbody').append(dtaaa);	
							/*dtaaa='<div class="" id="scanmid_'+mid+'">'+
							     '<label style="text-align:right;width:20px;float:left;">'+
									'<a title="Delete Content" class="icon-fa" onclick="delete_scanmedia('+mid+');" id="'+mid+'" href="javascript:void(0);"><em class="fa fa-times"></em></a>'+
					     		'</label>'+
					     		//'<div>'+mid+' - ' +bar+'</div>'+
					     		//'<span style="height: 20px;padding-top: 5px;">'+
					     			'<div style="padding-left:10px;float:left;">'+
					        			'<div class="left" style="width:100%;">'+mid+' - ' +bar+'</div>'+
						    		'</div>'+
						 		//'</span>'+
								'</div>'+
								'<div class="clear"></div>';
								$("#media_scanned").find('div.col-md-7').append(dtaaa);*/
						}
			 		});
					$("#scanned_barcode").val(null);
					$('#scanned_barcode').focus();
				}
			 },
			 beforeSend:function(){
				showLoader();
			 },
			 complete:function(){
				hideLoader();
			 }	
		 });
	 }
	 $("#scanned_barcode").val(null);
 }
 function delete_scanmedia(mids){
	 if(confirm('You are detaching the scanned Media, are you sure?')){
	 	var list=$("#scanned_mids").val();
	 	var newamt=removeValue(list,mids,',');
 	 	$("#scanned_mids").val(newamt);
 	 	$('#scanmid_'+mids).remove();
 	 }
 }
 function removeValue(list, value, separator) {
	  separator = separator || ",";
	  var values = list.split(separator);
	  for(var i = 0 ; i < values.length ; i++) {
	    if(values[i] == value) {
	      values.splice(i, 1);
	      return values.join(separator);
	    }
	  }
	  return list;
}
function check_outin_barcode(){
           showLoader();
            $.ajax({
                url: baseUrl + "barcode/check-out-in-barcode",
                cache: false,
                dataType: 'html',
                success: function (data) {
                    hideLoader();
                    if (data != "") {
			$('#media_container').html(data);
                        $(".mediaModules").removeClass('active');
                        $(".barcodeModules").addClass('active');
                        $(".mediamoduleList li:not(:first-child)").addClass("hide");
                    }
                }
            });
        
	}
	 function apply_check_outin_barcode()
	 {
		var cnt_error=0;
        var error = "<strong>Please Fix Below Given Error:-</strong><br>";
        var scanned_media = $("#scanned_mids").val();
        
        var trans_type = $('#evidencetransaction-trans_type').val();
        var trans_requested_by = $('#evidencetransaction-trans_requested_by').val();
        var Trans_to = $('#evidencetransaction-trans_to').val();
        var moved_to = $('#evidencetransaction-moved_to').val();
        var trans_reason = $('#evidencetransaction-trans_reason').val();
        var is_duplicate = 0;
        if ($('#is_duplicate').attr('checked'))
            is_duplicate = 1;


        if (trans_type == '') {
            $('#evidencetransaction-trans_type').blur();	
            cnt_error++;
        }
        if (trans_requested_by == '') {
            $('#evidencetransaction-trans_requested_by').blur();	
            cnt_error++;
        }
        if (trans_type == 4) {
            if (moved_to == "") {
				$('#evidencetransaction-moved_to').blur();
				cnt_error++;
            }
        }
        if (scanned_media == "") {
            error += 'Please scan Media<br>';
            cnt_error++;
        }
        if(cnt_error == 0)
        {
            $.ajax({
                type: "POST",
                url: baseUrl + "barcode/check-bulk-barcodechkinout",
                data: {evid: scanned_media, 'tran_type':trans_type},
                dataType: 'html',
                cache: false,
                success: function (data) {
                    if (data == "allow") {
                        var token = $("#token").val();
						showLoader();
                        $.ajax({
                            type: "POST",
                            url: baseUrl + "barcode/bulk-barcode-chkinout",
                            data: {is_duplicate: is_duplicate, evid: scanned_media, trans_type: trans_type, trans_requested_by: trans_requested_by, trans_reason: trans_reason, Trans_to: Trans_to, moved_to: moved_to},
                            dataType: 'html',
                            cache: false,
                            success: function (resdata) {
                               // $('#barcodechk_in_out_bulk').trigger('click');
                               // $('#loding').hide();
								hideLoader();
								check_outin_barcode();
                            }
                        });
                    } else {
						alert(data);
                        //error += data;
                        //openPopup();//Open a popup to Display Errors
                        //$('#errorContent').html(error);
                    }
                }
            });
        }   
    }
    function addevidcase()
    {		
        var client_id=$('#evidence-client_id').val();
        var client_name=$('#evidence-client_id option:selected').text();   
        var case_id=$('#evidence-client_case_id').val();   
        var case_name=$('#evidence-client_case_id option:selected').text(); 
        if(client_id != ''){
           if(case_id != ''){
                var str = $("#evidence-case_id").val();
                var arr_media = str.split(',');
                var flag=true;
                var str1=case_id+'|'+client_id;
                $.each(arr_media, function(index,value) {
                    if(value == str1 ){flag=false; return false;}
                });
                if(flag == true)
                {
                    var data='<tr id="sel_case_'+case_id+'" class="client_case_media_list"><td>'+client_name+'</td>'+
                            '<td>'+case_name+'</td>'+
                            '<td align="center"><a href="javascript:void(0);" onclick=delete_evidcase("'+case_id+'|'+client_id+'","'+case_id+'",0) class="icon-fa" title="Delete"><em class="fa fa-close" title="Delete"></em></a></td>'+
                            '</tr>';
                    $("#evid_case_list").append(data);

                    if($('#evidence-case_id').val()=="")
                        $('#evidence-case_id').val(case_id+'|'+client_id);
                    else
                        $('#evidence-case_id').val($('#evidence-case_id').val()+','+case_id+'|'+client_id);

                    if($('#evidence-case_id').val() !="")
                    {
                        $('#btn_add_content').show();
                    }
                }    
            }else{alert("Please select a Case to perform this action.");}    
        }
        else{alert("Please select a Client to perform this action.");}
    }
    function delete_evidcase(caseid,case_id,evid){
    	if(evid == 0){
    		var list=$("#evidence-case_id").val();
            var arr_case = list.split(',');
            var case_list=[];
            $.each(arr_case, function(index,value) {
                if(value != caseid)
                    case_list.push(value);
            });
		 	$("#evidence-case_id").val(case_list);
		 	$('#sel_case_'+case_id).remove();
    	}else{
    		$.ajax({
    			type: "GET",
                url: baseUrl + "media/chk-mediaattachtocase",
                data: {evid: evid,case_id:case_id},
                cache: false,
                success: function (resdata) {
                   if(resdata!=""){
                	   alert(resdata);
                   }else{
                	   var list=$("#evidence-case_id").val();
                       var arr_case = list.split(',');
                       var case_list=[];
                       $.each(arr_case, function(index,value) {
                           if(value != caseid)
                               case_list.push(value);
                        });
	           		 	$("#evidence-case_id").val(case_list);
	           		 	$('#sel_case_'+case_id).remove();
	           		 	$('#is_change_form_main').val('1');
                   }
                }
    		})
    	}
		//if(confirm('You are detaching the selected Client - Case, are you sure?')){}
    }
    function go_toMedia(id)
    {
        location.href=baseUrl + "media/index&id="+id;
    }
    function disableEnterKey(e)
    {
         var key;     
         if(window.event)
              key = window.event.keyCode; //IE
         else
              key = e.which; //firefox     

         return (key != 13);
    }
    //End of Code for Checkout in Barcode Form
/********************************  Barcode related Javascript Ends ****************************************************/		
