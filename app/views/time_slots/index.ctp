<h2>Schedule a Lesson</h2>

<div style="height: 408px;">

<div id="coursenlesson">
	<h3>Course Details</h3>
	
	<dl>
		<dt>Period</dt>
			<dd><?=$timeSlot['Period']['type'].' '.$timeSlot['Period']['year']?></dd>
		<dt>Course</dt>
			<dd><?=$timeSlot['TimeSlot']['course_id'].' '.$html->link('<b>'.$timeSlot['Course']['title'].'</b>', '/admin/courses/'.$timeSlot['Course']['id'], array('class'=>'txt-ulined'), null, false)?></dd>
		<dt>Professor</dt>
			<dd>
			 <?=$timeSlot['TimeSlot']['faculty_id'].' '.$html->link('<b>'.$timeSlot['Faculty']['first_name'].' '.$timeSlot['Faculty']['last_name'].'</b>', '/admin/faculty/'.$timeSlot['Faculty']['id'], array('class'=>'txt-ulined'), null, false)?>
       <?=$html->link('See Schedule', '/faculty/schedule/'.$timeSlot['Faculty']['id'], array('class'=>'txt-ulined'))?></dd>
	</dl>
	
	
<?if(!isset($isFaculty)): ?>
	<?=$form->create('Lesson', array('class'=>'separated kicker', 'url'=>'/time_slots/'.$timeSlot['TimeSlot']['id'].(isset($student)?'/'.$student['id']:null)))?>
		<h3>Lesson Details</h3>
		
<?  if(isset($student)): ?>
		<?=$form->hidden('Student.id', array('value'=>$student['id']))?>
		<dl><dt>Student</dt><dd>
		  <?=$html->link($student['first_name'].' '.$student['last_name'].' '.$student['id'], '/admin/students/'.$student['id'], array('class'=>'txt-ulined'))?>
      <?=$html->link('See Schedule', '/students/schedule/'.$student['id'], array('class'=>'txt-ulined'))?></dd></dl>
		<dl><dt>Credits</dt><dd><?=$student['CoursesUser']['credits']?></dd></dl>
		<dl><dt>Duration</dt><dd><?=$student['CoursesUser']['duration']?> minutes</dd></dl>
<?  elseif(isset($students)): // xxx: this if should always be true if else entered... ?>
		<?=$form->input('Student.id', array('label'=>'Student', 'options'=>$students, 'empty'=>' - chose a registered student - '))?>
<?  endif; ?>
		
		<div class="input time">
      <label for="LessonStHour">Start Time</label>
      <select id="LessonStHour" name="data[Lesson][st][hour]">
<?for($h=$timeSlot['st'][0]; $h<=$timeSlot['et'][0]+1; $h++): ?>
        <option value="<?=str_pad($h, 2, '0', STR_PAD_LEFT)?>"><?=$h?></option>
<?endfor; ?>?>
      </select>
      :
      <select id="LessonStMin" name="data[Lesson][st][min]">
<?for($m=0; $m<60; $m+=5): ?>
        <option value="<?=str_pad($m, 2, '0', STR_PAD_LEFT)?>"><?=str_pad($m, 2, '0', STR_PAD_LEFT)?></option>
<?endfor; ?>?>
      </select>
      
      <input id="LessonMinute" type="text" class="short" value="" name="data[Lesson][minute]"/>
      <label for="LessonMinute" style="float: none; font-weight: normal;"><?=isset($invalidMinute)?'invalid minute':'other minute'?></label>
    </div>
		
		<div class="clear kicker"></div>
  <?=$form->end('Schedule')?>
<?endif; ?>
</div>



<h3 class="center" style="margin-bottom: 0;">Time-slot Usage : <?=$timeSlot['day']?></h3>
<p class="center kicker">From <b><?=substr($timeSlot['TimeSlot']['start_time'], 0, 5)?></b>
  to <b><?=substr($timeSlot['TimeSlot']['end_time'], 0, 5)?></b></p>

<table id="daytable">
<?for($h=8; $h<23; $h++): ?>
	<tr><td><?=str_pad($h, 2, '0', STR_PAD_LEFT)?></td></tr>
<?endfor; ?>
</table>

<?/*
Day table is 192px tall (10px left margin),
each pixel is 5 minutes in the day
(an hour is 12 pixeles).

$timeSlot['st'][0] has the start hour (pixels: * 60 / 5 or * 12)
$timeSlot['st'][1] has the start minute (/5) // 5 min resolution (%5 lost)
$timeSlot['duration'] has the duration in minutes (/5) // "
*/
$timeSlot['top'] = ($timeSlot['st'][0]-8)*24 + $timeSlot['st'][1]*2/5 - 2;
$timeSlot['height'] = ($timeSlot['duration']*2/5); // xxx: can the duration be negative here?
?>
<div class="available time-slot" style="top:<?=$timeSlot['top']?>px; height: <?=$timeSlot['height']?>px;"></div>

<div class="clear"></div>

<?$lessoned = false;
$accHeight = 0;
foreach($timeSlot['Lesson'] as $l=>$lesson):
	$lesson['st'] = explode(':', $lesson['start_time']);
	$lesson['et'] = explode(':', $lesson['end_time']);
	$lesson['duration'] = (60*$lesson['et'][0]+$lesson['et'][1]) - (60*$lesson['st'][0]+$lesson['st'][1]);
	$lesson['top'] = -360 + ($lesson['st'][0]-8)*24 + $lesson['st'][1]*2/5 - $accHeight - 1;
	$lesson['height'] = $lesson['duration']*2/5;
	$accHeight += $lesson['height']; // $timeSlot['height'] accumulates all height. ?>
	<div class="occupied ts-lesson" style="top: <?=$lesson['top']?>px; height: <?=$lesson['height']?>px;">
    <div class="left cap" style="height: 11px; line-height: 10px;"><?=substr($lesson['start_time'], 0, 5)?></div>
    <div class="right cap" style="height: 11px; line-height: 10px;<?=$lesson['duration']>30?' margin-top: 12px;':null?>"><?=substr($lesson['end_time'], 0, 5)?></div>
<?if(isset($student) and $lesson['student_id']==$student['id']): $lessoned = $l; ?>
    <div class="bold cap green" style="line-height: <?=$lesson['height']?>px;"><?=$student['first_name'].' '.$student['last_name']?></div>
<?else: ?>
    <div class="<?=isset($userId)?null:'bold '?>cap" style="line-height: <?=$lesson['height']?>px;">
<?  if(isset($isFaculty)) :?>
      <?=$lesson['Student']['first_name'].' '.$lesson['Student']['last_name']?>
<?  else: ?>
      <?=isset($userId)?'taken':$html->link($lesson['Student']['first_name'].' '.$lesson['Student']['last_name'], "/time_slots/{$timeSlot['TimeSlot']['id']}/".$lesson['student_id'])?>
<?  endif; ?>
    </div>
<?endif; ?>
  </div>
<?endforeach;

if(!isset($isFaculty)) {
  if($lessoned !== false): // can only happen if $student['id'] is set.
  $fromTo = substr($timeSlot['Lesson'][$lessoned]['start_time'], 0, 5).' to '.substr($timeSlot['Lesson'][$lessoned]['end_time'], 0, 5); ?>
<div class="warning message" style="position: relative; top: -<?=$accHeight + 160?>px; width: 433px;">
<?  if(!isset($userId)): ?>
  The student is <span class="green" style="color: #000;">&nbsp;scheduled (<?=$fromTo?>) </span>&nbsp;for this course.
  Re-scheduling him will move the existing lesson.<br/>
  You may also simply <?=$html->link($html->image('icons/delete16.png').' cancel his/her lesson', '/admin/time_slots/cancel/'.$timeSlot['Lesson'][$lessoned]['id'], array('class'=>'txt-ulined'), "You are canceling the student's lesson for this course.", false)?>
  without re-scheduling.
<?  else: ?>
  If you need to change/cancel your lesson please contact the music department directly.
<?  endif; ?>
</div>
<?elseif(isset($students)): ?>
<div class="message" style="position: relative; top: -<?=$accHeight + 180?>px; width: 433px;">
  Please look for the student you are selecting on the time-slot (right).<br/>
  If he/she appears, then he has a scheduled lesson for this course already. <b>Re-scheduling him/her will move the existing lesson.</b></div>
<?elseif($session->read('User.type') == 'student'): ?>
<div class="message" style="position: relative; top: -<?=$accHeight + 180?>px; width: 433px;">
  You may only schedule for a lesson <b>ONCE</b>.<br/>
  This action is irreversible so please be careful to choose the right times.</div>
<?endif;
}?>

</div>

<?//pr($timeSlot);	// debug?>