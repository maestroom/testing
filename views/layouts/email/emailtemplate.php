<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<?php
$status_arr=array(0=>'Not Started',1=>'Start',3=>'Hold',4=>'Complete');
$url=Yii::$app->request->hostInfo.Yii::$app->request->baseUrl;
?>
<style type="text/css">
	#outlook a{padding:0}
	body{width:100%!important;-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%;}
	.ExternalClass{width:100%}
	.ExternalClass,.ExternalClass div,.ExternalClass font,.ExternalClass p,.ExternalClass span,.ExternalClass td{line-height:100%}
	#backgroundTable{margin:0;padding:0;width:100%!important;line-height:100%!important}
	img{outline:0;text-decoration:none;border:none;-ms-interpolation-mode:bicubic}
	a img{border:none}
	.image_fix{display:block}
	p{margin:0!important}
	table td{border-collapse:collapse}
	table{border-collapse:collapse;mso-table-lspace:0;mso-table-rspace:0}
	a{color:#2085f6;text-decoration:none}
	.ui-widget-content a {color:#2085f6 !important;text-decoration:none}
	.ui-dialog .ui-dialog-buttonpane{margin:0px !important}
	a:hover{color:#000;text-decoration:underline}
	table[class=full]{width:100%;clear:both}
	.devicewidth{width:100%;max-width:980px}
	table tr td { border :0px none !important}
	/*IPAD STYLES*/
	@media screen and (max-width:640px){
	a[href^=sms],a[href^=tel]{text-decoration:none;color:#2085f6;pointer-events:none;cursor:default}
	.mobile_link a[href^=sms],.mobile_link a[href^=tel]{text-decoration:default;color:#2085f6!important;pointer-events:auto;cursor:default}
	img[class=devicewidth],table[class=devicewidth],td[class=devicewidth]{width:440px!important;text-align:center!important}
	img[class=banner]{width:440px!important;height:147px!important}
	table[class=devicewidthinner]{width:420px!important;text-align:center!important}
	table[class=emhide]{display:none!important}
	}
	/*IPHONE STYLES*/
	@media screen and (max-width:480px){
	a[href^=sms],a[href^=tel]{text-decoration:none;color:#2085f6;pointer-events:none;cursor:default}
	.mobile_link a[href^=sms],.mobile_link a[href^=tel]{text-decoration:default;color:#2085f6!important;pointer-events:auto;cursor:default}
	img[class=devicewidth],table[class=devicewidth],td[class=devicewidth]{width:280px!important;text-align:center!important}
	img[class=banner]{width:280px!important;height:93px!important}
	table[class=devicewidthinner]{width:260px!important;text-align:center!important}
	table[class=emhide]{display:none!important}
	}
	.wysiwyg-color-black {color: black !important;}
	.wysiwyg-color-silver {color: silver !important;}
	.wysiwyg-color-gray {color: gray !important;}
	.wysiwyg-color-white {color: white !important;}
	.wysiwyg-color-maroon {color: maroon !important;}
	.wysiwyg-color-red {color: red !important;}
	.wysiwyg-color-purple {color: purple !important;}
	.wysiwyg-color-fuchsia {color: fuchsia !important;}
	.wysiwyg-color-green {color: green !important;}
	.wysiwyg-color-lime {color: lime !important;}
	.wysiwyg-color-olive {color: olive !important;}
	.wysiwyg-color-yellow {color: yellow !important;}
	.wysiwyg-color-navy {color: navy !important;}
	.wysiwyg-color-blue {color: blue !important;}
	.wysiwyg-color-teal {color: teal !important;}
	.wysiwyg-color-aqua {color: aqua !important;}
</style>
<!-- start textbox-with-title -->

<!-- start textbox-with-title -->
<table width="100%" cellpadding="0" cellspacing="0" border="0" id="backgroundTable">
                                          <tr>
                                             <td style="font-family:Georgia, 'Times New Roman', Times, serif; font-size:14px;text-align:left;line-height: 14px;">
												<strong><?php echo $body_header;?></strong>
												<hr/>
                                             </td>
                                          </tr>
                                          <!-- End of Title -->
                                          <!-- spacing -->
                                          <tr>
                                             <td height="5">&nbsp;</td>
                                          </tr>
                                          <!-- End of spacing -->
                                          <!-- content -->
                                          <tr>
                                             <td style="font-family: arial;font-size:14px; text-align:left;line-height:14px;">
												 
												<?php echo $body;?>
                                             
                                             <br /><br />
                                             </td>
                                          </tr>
                                          <!-- End of content -->
                                          <tr>
                                             <td>
											 	 <hr>
												<?php
												$linkname = $url;
												echo 'This notification was generated by IS-A-TASK.<br>
												To view your IS-A-TASK Project, visit: <a href="'.$linkname.'">'.$linkname.'</a><br>'.'To view your IS-A-TASK Project Instructions, visit: <a href="'.$linkname.'">'.$linkname.'</a>';
												?>
                                             </td>
                                          </tr>
                                          <!-- /button -->
                                          <!-- Spacing -->
                                          <tr>
                                             <td width="100%" height="20"></td>
                                          </tr>
									  
</table>