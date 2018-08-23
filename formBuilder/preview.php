<style>
    .preview{top:150px !important;}
    #fancy_perview a {margin:0px !important;}
    <!--
    .preview .ui-dialog-content 
    {
        height:450px!important;

    }
    .p_ol ul li
    {
        list-style: inside none disc!important;	
        float: none !important;
        border:0px none!important;
        width: 100%;
    }
    .p_ol ol li
    {
        list-style: inside none decimal!important;	
        float: none !important;
        padding-left:5px;
        border:0px none!important;
        width: 100%;
    }
    -->
</style>
<?php
include '../formBuilder/formbuilder.php';

/*echo "<pre>";
print_r($_POST);
*/

/*
if(isset($_POST['properties'])) {
    foreach($_POST['properties'] as $el=>$val) {
            if($_POST['properties'][$el]['type']=='checkbox' || $_POST['properties'][$el]['type']=='radio' || $_POST['properties'][$el]['type']=='dropdown')
            {
                $_POST['properties'][$el]['values']=html_entity_decode($_POST['properties'][$el]['values']);
            }   
    }
}*/
/*echo "<pre>";
print_r($_POST);
die;*/

$items = $fb->build($_POST);

 /*echo "<pre>";
print_r($items);
echo "</pre>"; 
die;*/
if ($items):
    ?>
    <div class="create-form">
        <form class="fancy" id="fancy_perview" name="fancy" action="#" onsubmit="return validate(this);" autocomplete="off">
            
               
                    <?php foreach ($items as $item): 
                    $label="";
                    if (isset($item['label']) && trim($item['label'])!=""){
                        $label=(htmlspecialchars($item['label']));                
                    }
                    
                    ?>
               <div class="row border-saprater">
                   <fieldset>
                        <legend class="sr-only"><?= $label ?></legend>
                            <?php
                            if (isset($label) && $label!="") { ?>
                                <div class="col-md-3"><label class="form_label"><?php echo $label; ?></label></div>
                                <?php
                                    } else {
                                        if ($item['type'] != 'textarea' && $item['type'] != 'text') { ?>
                                            <div class="col-md-3"><label class="form_label"><?php echo 'No Label'; ?></label></div>
                                        <?php
                                        }
                                    }
                                    if (isset($item['text_val'])) {
                                        if ($item['type'] == 'text') { ?>
                                            <div  style="margin-left:0px!important;" class="col-md-12">
                                                <?php
                                                    $contents = (rawurldecode($item['text_val']));
                                                    $contents = str_replace(",", "", $contents);
                                                    echo $contents;
                                                ?>
                                            </div>
                                    <?php
                                    } else { ?> 
                                        <div class='block col-md-9'>
                                            <?php echo $item['html']; ?>
                                        </div>
                                    <?php
                                    }
                                } else {
                                ?>
                                <div class='block col-md-7'>
									<?php echo $item['html']; ?>
                                </div>
                            <?php
                            } 
                            ?>
                   </fieldset>
                   </div>
                        <?php endforeach; ?>
        </form>
<?php else: ?>
        <div class='warning'>No elements in form!</div>
    <?php endif; ?>
</div>
<div class="clear"></div>
<script type="text/javascript">

    function validate()
    {
        var err = '';
        var msg = '';
        var data = $('#fancy_perview').find('*:input');
        for (i = 0; i < (data.length); i++) {
            if ($(data[i]).attr('class') != '') {
                if ($(data[i]).is('.required')) {
                        if ($(data[i]).val() == "") {
                            msg = $(data[i]).parents().find('label').html() + ' Field is required';
                            alert(msg);
                            data[i].focus();
                            data[i].style.border = '1px solid red';
                            err++;
                        } else {
                            data[i].style.border = '1px solid #889CA6';
                        }
                } 
                if ($(data[i]).hasClass('number')) {
                    if ($(data[i]).val() != "") {
                        var pattern = /^[0-9]*$/;
                        if (!pattern.test($(data[i]).val())) {
                            msg = $(data[i]).parents().find('label').html() + ' Allow number only';
                            alert(msg);
                            data[i].focus();
                            data[i].style.border = '1px solid red';
                            err++;
                        } else {
                            data[i].style.border = '1px solid #889CA6';
                        }
                    } else {
                        if ($(data[i]).is('.required')) {
                           data[i].style.border = '1px solid red'; 
                        } else {
                            data[i].style.border = '1px solid #889CA6';
                        }
                    }
                }
                if (data[i].type == 'select-one') {
                    if ($(data[i]).val() == "") {
                        msg = $(data[i]).parents().find('label').html() + ' Field is required';
                        alert(msg);
                        data[i].focus();
                        err++;
                    } else {
                        //data[i].style.border='1px solid #889CA6';
                    }
                }
            }
        }

        $('#fancy_perview').find(':input').each(function () {
            //alert($(this).attr('type'));
            if ($(this).prop("tagName") == 'SELECT')
            {
                if ($(this).attr('class') != undefined)
                {
                    if ($(this).attr('class') == 'required')
                    {
                        if ($(this).val() == 0)
                        {
                            var options = this.getElementsByTagName("option");
                            var optionHTML = options[this.selectedIndex].innerHTML;
                            if (optionHTML == 'Please Select')
                            {
                                msg = $(this).parents().find('label').html() + ' Field is required';
                                this.style.borderColor = 'red';
                                alert(msg);
                            }
                            else
                            {
                                this.style.borderColor = '#889CA6';
                            }
                        }
                        else
                        {
                            this.style.borderColor = '#889CA6';
                        }
                    }
                }
            }
        });
        return false;
    }
    $('.datepickers').each(function(e){
		var datepicker_id = $(this).attr('id');
		var formElements={};
		formElements[datepicker_id] = "%m-%d-%Y";
		datePickerController.createDatePicker({formElements: formElements });	
	});
	

</script>
<noscript></noscript>
