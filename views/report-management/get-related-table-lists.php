<?php echo "sdfsdf";
if(!empty($tables_list)){ ?>
<option value=""></option>
<?php foreach($tables_list as $table_name) {?>
<option value="<?= $table_name['id'] ?>" ><?= $table_name['table_name']; ?></option>
<?php } }?>
			
