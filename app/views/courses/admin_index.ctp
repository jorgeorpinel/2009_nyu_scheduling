<h2><? echo $this->pageTitle; ?></h2>



<? if(!isset($noSearchFilter)): ?>
<?  if(false): // xxx: maybe later ?>
<?// filter and search: xxx: choose sent data by default. ?>
<div class="left padded selector" style="width: 384px; min-height: 92px;">
	<h3 class="left" style="margin-right: 1em;"><button>Filter</button></h3>
	
	<div class="left" style="padding-left: 1em; border-left: 1px solid #000;">
		<label class="short" for="fInstructorName">Instructor:</label>
		
		<select style="width: 18em;" id="fInstructorName">
			<option value="" selected="selected"></option>
	<? 	foreach($instructors as $i=>$instructor): ?>
			<option value="<?=$i?>"><?=$i.' '.$instructor?></option>
	<? 	endforeach; ?>
		</select>
	</div>
	
	<div class="clear kicker"></div>
	
	<div class="left padded">
		<label class="short" for="fCourseArea">Area</label>,
		<label class="short" for="fCourseNumber">Number</label>,
		<label class="short" for="fCourseSection">Section</label>
		<br/>
		
		<select class="small" id="fCourseArea">
			<option value="" selected></option>
	<? 	foreach($areas as $a=>$a1): ?>
			<option value="<?=$a?>"><?=$a?></option>
	<? 	endforeach; ?>
		</select> .
		
		<select class="small" id="fCourseNumber">
			<option value="" selected></option>
	<? 	foreach($cNumbers as $c=>$c1): ?>
			<option value="<?=$c?>"><?=$c?></option>
	<? 	endforeach; ?>
		</select> .
		
		<select class="small" id="fCourseSection">
			<option value="" selected></option>
	<? 	foreach($sections as $s=>$s1): ?>
			<option value="<?=$s?>"><?=$s?></option>
	<? 	endforeach; ?>
		</select>
	</div>
	
	<div class="left padded" style="border-left: 1px solid #000;">
		<label class="short" for="fDay">Day</label>
		<label class="short" for="fTime">Starting...</label>
		<label class="short" for="fLocation">Location</label>
		<br/>
		
		<select class="small" id="fDay">
			<option value="" selected></option>
	<? 	foreach($days as $day): ?>
			<option value="<?=$day?>"><?=$day?></option>
	<? 	endforeach; ?>
		</select>
		
		<select class="small" id="fTime">
			<option value="" selected></option>
	<? 	foreach($times as $time): ?>
			<option value="<?=$time?>"><?=$time?></option>
	<? 	endforeach; ?>
		</select>
		
		<select class="small" id="fLocation">
			<option value="" selected></option>
	<? 	foreach($locations as $l=>$l1): ?>
			<option value="<?=$l?>"><?=$l?></option>
	<? 	endforeach; ?>
		</select>
	</div>

</div>

<? endif;?>
<?=$form->create('Course', array(
	'class'=>'right isolated'.(isset($this->data['Course']['title'])?' marked':null),
	'style'=>'width: 330px; min-height: 94px;',
	'url'=>'/admin/courses') )?>
	<h3 class="left" style="margin-right: 1em;">Search</h3>

	<div class="left" style="padding-left: 1em; border-left: 1px solid #000;">
		<label for="CourseArea">Area</label>,
		<label class="short" for="CourseNumber">Number</label>,
		<label class="short" for="CourseSection">Section</label>
		
		<br/>
		
		<select id="CourseArea" name="data[Course][area]">
			<option value="" selected>Any</option>
			<option value="E85">E85</option>
			<option value="E89">E89</option>
		</select> .
		<input class="small" id="CourseNumber" name="data[Course][number]"/> .
		<input class="short" id="CourseSection" name="data[Course][section]"/> and/or
	</div>
	
	<div class="clear kicker"></div>
	
	<label for="CourseTitle">Title:</label>
	<input id="CourseTitle" name="data[Course][title]"/>
	
	<?// xxx: by professor name? ?>
	
	<button type="submit"><?=$html->image('icons/search.png')?> Go</button>

</form>

<div class="clear kicker"></div>
<? endif;?>



<!-- todo: Add Course -->
<?=$form->create('Course', array('url'=>'/admin/courses/add', 'style'=>'background: #fff;'))?>
	<button disabled="disabled">Add Course ...</button>
	<?=$html->link('-&gt; Go to Upload Form', '/'.$this->params['url']['url'].'#upload', array(), null, false)?>
<?=$form->end()?>


<!-- div class="right" style="margin-right: 5px;">
	Order by
	<select id="CourseOrder">
		<option value="cid">course number</option>
		<option value="cid">course title</option>
		<option value="nid">instructor NYU ID number</option>
		<option value="name">instructor name</option>
		<option value="time">most time available</option>
	</select>
</div -->









<h3><?php echo $currentPeriod['Period']['type'].' '.$currentPeriod['Period']['year'];?>
	Private Lesson Courses
	(<?=count($courses)?>)</h3>

<? if(isset($maxResults)): ?>
<div class="mini warning">Only 50 results fetched. Use Filter/Search to narrow down results if necessary.</div>
<? endif; ?>

<?php echo $form->create('Course', array('style'=>'background: #fff;', 'url'=>'/admin/courses/delete')); ?>
<?if(empty($courses)):?>
	<div class="warning">No courses to display.</div>
<?endif;?>
<?php	$i = 0;
      foreach($courses as $course): ?>

<div class="right">&nbsp;<?=$html->link('Add Faculty', "/admin/time_slots/create/{$course['id']}")?></div>
<div class="right">&nbsp;<?=$html->link('Edit', "/admin/courses/edit/{$course['id']}")?> |</div>
<table style="border: none; width: auto;"><tr>
	<th style="border: none;">
		<?php echo $form->input('Course.'.$i.'Id', array('type'=>'checkbox', 'name'=>'data[Course]['.$i++.'][id]', 'value'=>$course['id'], 'label'=>'', 'div'=>'left')); ?>
		&nbsp;<?php echo $course['id']; ?>&nbsp;&nbsp;&nbsp;</th>
	<th style="border: none;"><?=$html->link($course['title'], '/admin/courses/'.$course['id'])?>&nbsp;&nbsp;&nbsp;</th>
</tr></table>

<?php 	if(count($course['Faculty']) != 0): ?>
<div class="isolated"><table>
	<tr class="unbold"><th>NYU Number</th><th>Name</th><th>Email</th><th colspan="4">Times Available</th></tr>
<?php 	endif; ?>
	
<?php 		foreach($course['Faculty'] as $user): ?>

	<tr class="highlight">
		<td><?php echo $user['id']; ?></td>
		<td><b><?=$html->link($user['first_name'].' '.$user['last_name'], '/admin/faculty/'.$user['id'])?></b></td>
		<td><?php echo $user['email']; ?></td>
		<td><table>
<?if(empty($course['TimeSlot'])):?>
			<tr><td><b>TBA</b></td></tr>
<?endif;?>
<?php 		$noTs = true;
          foreach($course['TimeSlot'] as $ts=>$timeSlot):
						if($timeSlot['faculty_id'] == $user['id']) {
							$timeSlot['start_time'] = strftime('%I:%M %p', strtotime($timeSlot['start_time']));
							$timeSlot['end_time'] = strftime('%I:%M %p', strtotime($timeSlot['end_time'])); ?>
			<tr><td class="selectable" onClick="window.location='<?=$html->url('/time_slots/'.$timeSlot['id']); ?>'">
				<?=$html->link('<b>'.$timeSlot['day'].'</b> from '.$timeSlot['start_time'].' to '.$timeSlot['end_time'].' @ '.$timeSlot['location']
				, "/time_slots/{$timeSlot['id']}", array(), false, false)?>
			</td></tr>
<?php				unset($course['TimeSlot'][$ts]); $noTs = false; } 
					endforeach; ?>
		</table></td>
<?php       if($noTs):?>
    <td class="selectable center" onClick="window.location='<?=$html->url('/faculty/schedule/'.$user['id']); ?>'">
      <?=$html->link('Add Times', "/admin/time_slots/create/{$course['id']}/{$user['id']}")?></td><td></td>
<?php       else:?>
    <td class="selectable center" onClick="window.location='<?=$html->url('/faculty/schedule/'.$user['id']); ?>'">
      <?=$html->link('Rep', "/courses/section/{$course['id']}/{$user['id']}")?></td>
		<td class="selectable center" onClick="window.location='<?=$html->url('/faculty/schedule/'.$user['id']); ?>'">
			<?=$html->link('Sch', "/faculty/schedule/{$user['id']}")?></td>
    <td class="selectable center" onClick="window.location='<?=$html->url('/faculty/schedule/'.$user['id']); ?>'">
      <?=$html->link('Add', "/admin/time_slots/create/{$course['id']}/{$user['id']}")?></td>
<?php       endif; ?>
	</tr>

<?php 		endforeach; if(count($course['Faculty']) != 0): ?>
</table></div>
<?php 	else: ?>
<div class="mini warning">No faculty assigned yet. <?=$html->link('Assign now', "/admin/courses/edit/{$course['id']}#faculty")?></div>
<?php 	endif; ?>

<?php endforeach; ?>



<!-- todo: Delete selected courses -->
<?php echo $form->hidden('Period.id', array('value'=>$currentPeriod['Period']['type'])); ?>
	<?php echo $form->submit('Delete Selected Courses ...', array('id'=>'deleteSubmit', 'disabled'=>'disabled', 'onClick'=>'return confirm("If lessons are scheduled for the selected courses, no further information on the courses will be available when viewing the lessons\' schedule.");')); ?>
	<?php echo $form->end(); ?>






<!-- Upload courses spreadsheet -->
<a name="upload"></a>
<?php echo $form->create('Course', array('class'=>'isolated', 'type' => 'file', 'url'=>'/admin/courses/load')); ?>

	<h3>Load <?=$currentPeriod['Period']['type'].' '.$currentPeriod['Period']['year']?> Teacher-Course Time Availability Spreadsheet</h3>

	<div class="mini message">Max. File size for upload is 12MB.</div>
	
	<label for="CourseSpreadsheetFile">File: </label>
		<input type="hidden" name="MAX_FILE_SIZE" value="12582912" /><!-- exactly 12M -->
	<input id="CourseSpreadsheetFile" type="file" value="" name="data[Course][spreadsheet_file]"/>
	
	<!-- label for="PeriodId">Period: </label -->
	<!-- select id="PeriodId" name="data[Period][id]" -->
<?php //foreach($periods as $y=>$year): foreach($year as $p=>$period): ?>
		<!-- option value="<?php //echo $p; ?>"><?php //echo $period.' '.$y; ?></option -->
<?php //endforeach; endforeach; ?>
	<!-- /select -->
	
	<button type="submit"><?=$html->image('icons/upload.png')?> Upload</button>

</form>
<?//pr($currentPeriod);	// debug ?>
<?//pr($courses);	// debug ?>