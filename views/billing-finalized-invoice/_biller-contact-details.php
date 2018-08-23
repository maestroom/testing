<?php
	use yii\helpers\Html;
?>
<td colspan="7">
	<div class="row" style="display:none;">
		<div class="col-sm-121">
			<table class="table table-striped" width="100%"> <!-- no-border -->
				<tr>
					<th>&nbsp;</th>
					<th>Street Address 1</th>
					<td><?= $contactList['add_1']; ?></td>
				</tr>
				<tr>
					<th>&nbsp;</th>
					<th>Street Address 2</th>
					<td><?= $contactList['add_2']; ?></td>
				</tr>
				<tr>
					<th>&nbsp;</th>
					<th>City, State, Zip Code</th>
					<td>
						<?php if($contactList['city'].$contactList['state'].$contactList['zip']!='')
							{
								echo $contactList['city'].' ,'.$contactList['state'].' ,'.$contactList['zip'];
							}
							else if($contactList['state'].$contactList['zip']!='')
							{ 
								echo $contactList['state'].' ,'.$contactList['zip'];
							}
							else if($contactList['city'].$contactList['zip']!='') 
							{
								echo $contactList['city'].' ,'.$contactList['zip'];
							} 
							else
							{
								echo $contactList['zip'];
							} 
						?>
					</td>
				</tr>
				<tr>
					<th>&nbsp;</th>
					<th>Email</th>
					<td><?= $contactList['email']; ?></td>
				</tr>
				<tr>
					<th>&nbsp;</th>
					<th>Phone</th>
					<td><?= $contactList['phone_o']; ?></td>
				</tr>
				<tr>
					<th>&nbsp;</th>
					<th>Mobile</th>
					<td><?= $contactList['phone_m']; ?></td>
				</tr>
			</table>		
		</div>
	</div>							
</td>
							
