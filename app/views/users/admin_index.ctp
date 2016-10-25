<h2><?=$this->pageTitle?></h2>



<? if(!isset($noSearchFilter) and $type!='administrators'): ?>
<?// search: xxx: filter, choose sent data by default. ?>
<?=$form->create('User', array(
//	'class'=>'right isolated'.(isset($this->data['User']['name'])?' marked':null),
	'class'=>'isolated'.(isset($this->data['User']['name'])?' marked':null),
//	'style'=>'width: 330px; min-height: 94px;',
	'url'=>'/admin/'.$type) )?>
	<h3 class="left" style="margin-right: 1em;">Search</h3>

	<div class="left" style="padding-left: .5em; border-left: 1px solid #000;">
		<label for="UserId">NYU ID</label><br/>
		<input id="UserId" class="medium" name="data[User][id]"/>
	</div>
	
	<div class="left" style="padding: 0 .5em;">
		<label for="UserName">Name</label><br/>
		<input id="UserName" name="data[User][name]"/>
	</div>
	
	<button type="submit"><?=$html->image('icons/search.png')?> Go</button>

</form>

<div class="clear kicker"></div>
<? endif;?>



<!-- todo: Add Courses -->
<? $usersName = $type=='administrators'?'users':$type; ?>
<div class="isolated">
  <?=$html->link('Add User', "/admin/$usersName/add")?>
  <? if($type!='administrators')
	echo $html->link('| -&gt; Go to Upload Form', '/'.$this->params['url']['url'].'#upload', array(), null, false);?>
</div>


<?// if($type!='administrators'): ?>
<!-- div class="right" style="margin-right: 5px;">
	Order by
	<select id="UserOrder">
		<option value="nid">NYU ID number</option>
		<option value="name">name</option>
		<option value="time">pending to schedule</option>
	</select>
</div -->
<?// endif; ?>









<?switch($type) {
	case 'students':?>
<h3><?=$currentPeriod['Period']['type'].' '.$currentPeriod['Period']['year']?> Students and their Courses (<?=count($users)?>)</h3>
<?	break; ?>
<?case 'faculty':?>
<h3>Faculty and their <?=$currentPeriod['Period']['type'].' '.$currentPeriod['Period']['year']?> Courses (<?=count($users)?>)</h3>
<?	break; ?>
<?default: $lastTH = '(Edit)';?>
<h3><?=ucwords($type)?></h3>
<?	break;
} ?>

<?if(empty($users)):?>
	<div class="warning">No <?=$type?> to display.</div>
<?else:?>
<?=$form->create('User', array('style'=>'background: #fff;', 'url'=>'/admin/courses/delete'))?>
<div class="isolated"><table>
	<tr class="unbold"><th>NYU ID</th><th>Name</th><th>Contact</th><th<?=$type=='students'?' colspan="2"':null?>>Courses</th><th colspan="2"></th></tr>
<?endif;?>

<?$i = 0;
	if(!empty($users)) foreach($users as $user):
		if($type=='students' and !isset($user['User'])) $user['User'] = $user; // xx: repeats data. ?>
<?$javascript->link('users/admin_index.js', false);	// xx: too simple a script to use helper for import. ?>
	<tr class="highlight">
		<td>
			<?=$form->input('User.'.$i.'Id', array(
				'type'=>'checkbox', 'name'=>'data[User]['.$i++.'][id]', 'value'=>$user['User']['id'],
				'label'=>'', 'div'=>'left', 'onMouseOver'=>'go=false;', 'onMouseOut'=>'go=true;'))?>
			&nbsp;<?=$user['User']['id']?>
		</td>
		<td><b><?= $type == 'administrators' ?
			$user['User']['first_name'].' '.$user['User']['last_name'] :
			$html->link($user['User']['first_name'].' '.$user['User']['last_name'], "/admin/$type/".$user['User']['id'])?></b></td>
		<td><?=$user['User']['email']?><br/><?=$user['User']['telephone']?></td>
		<td><table>
<?	foreach($user['Course'] as $c=>$course): ?>
			<tr><td class="selectable" onClick="window.location = '<?=$html->url('/admin/courses/'.$course['id'])?>'"
				><?=$html->link($course['id'].' <b>'.$course['title'], '/admin/courses/'.$course['id'], array(), false, false)?></td></tr>
<?	endforeach; ?>
		</table></td>
<?	if($type=='students'):?>
		<td class="selectable center"
			onClick="if(go) window.location='<?php echo $html->url("/admin/students/register/{$user['User']['id']}"); ?>'">
			<?=$html->link('Reg', "/admin/students/register/{$user['User']['id']}")?></td>
<?	endif;?>
<?	if($type == 'administrators'): ?>
		<td class="selectable center"
			onClick="if(go) window.location='<?php echo $html->url("/users/edit/{$user['User']['id']}"); ?>'">
			<?=$html->link('Edit', "/users/edit/{$user['User']['id']}")?></td>
<?	else: ?>
		<td class="selectable center"
			onClick="if(go) window.location='<?php echo $html->url("/$type/schedule/{$user['User']['id']}"); ?>'">
			<?=$html->link('Sch', "/$type/schedule/{$user['User']['id']}")?></td>
		<td class="selectable center"
			onClick="if(go) window.location='<?php echo $html->url("/$type/edit/{$user['User']['id']}"); ?>'">
			<?=$html->link('Ed', "/$type/edit/{$user['User']['id']}")?></td>
<?	endif; ?>
	</tr>
<?endforeach; ?>

<?if(!empty($users)):?>
</table></div>
<?endif;?>


<?if(!empty($users)):?>
<!-- todo: Delete selected students -->
<? if($type == 'students') echo $form->hidden('Period.id', array('value'=>$currentPeriod['Period']['type'])); ?>
<? echo $form->submit('Delete Selected Students ...', array('id'=>'deleteSubmit', 'disabled'=>'disabled', 'onClick'=>'return confirm("This will also delete their lesosns.");')); ?>
<? echo $form->end(); ?>
<?endif;?>



<?if($type == 'students'): ?>
<!-- Upload students spreadsheet -->
<a name="upload"></a>
<?=$form->create('User', array('class'=>'isolated', 'type' => 'file', 'url'=>'/admin/users/load'))?>

	<h3>Load <?=$currentPeriod['Period']['type'].' '.$currentPeriod['Period']['year']?> Student-Course Spreadsheet</h3>
	
	<div class="mini message">Max. File size for upload is 12MB.</div>
	
	<label for="StudentSpreadsheetFile">File: </label>
		<input type="hidden" name="MAX_FILE_SIZE" value="12582912" /><!-- exactly 12M -->
	<input id="StudentSpreadsheetFile" type="file" value="" name="data[User][spreadsheet_file]"/>
	
	<button type="submit"><?=$html->image('icons/upload.png')?> Upload</button>

</form>
<? elseif($type == 'faculty'): ?>
<!-- Upload courses spreadsheet -->
<?=$form->create('Course', array('class'=>'isolated', 'type' => 'file', 'url'=>'/admin/courses/load'))?>

	<h3>Load <?=$currentPeriod['Period']['type'].' '.$currentPeriod['Period']['year']?> Teacher-Course Time Availability Spreadsheet</h3>

	<div class="mini message">Max. File size for upload is 12MB.</div>
	
	<label for="CourseSpreadsheetFile">File: </label>
		<input type="hidden" name="MAX_FILE_SIZE" value="12582912" /><!-- exactly 12M -->
	<input id="CourseSpreadsheetFile" type="file" value="" name="data[User][spreadsheet_file]"/>
	
	<button type="submit"><?=$html->image('icons/upload.png')?> Upload</button>

</form>
<? endif; ?>
<?//pr($users);	// debug ?>