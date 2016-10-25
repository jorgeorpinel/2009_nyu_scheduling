<h2><? echo $this->pageTitle; ?></h2>

<div id="calendar">
	<div id="calHead">
		<div class="caption" style="margin-right: 3em; border: none;">
			<?=$user['User']['type']=='student'?'Scheduled Lesson':'Occupied'?></div>
		<div class="caption occupied" style="width: 15px; height: 15px;<?=$user['User']['type']=='student'?' border: 1px solid #CCC; border-top: 6px solid #CCC; border-bottom: 5px solid #CCC; margin-top: 2px;':null?>"></div>
		<div class="caption" style="margin-right: 2em; border: none;">
			<?=$user['User']['type']=='student'?'Open Time-slot':'Available'?></div>
		<div class="caption available" style="width: 15px; height: 15px;"></div>
		<div class="caption" style="border: none;"><?=$user['User']['type']=='student'?'Courses:':'Time-slots:'?></div>
		
		
	</div>
	
	<div id="calMain">
		<div id="calMainHead"><?=$period?> Week</div>
		<table class="calendar">
			<tr class="weekdays">
				<th style="width: 40px;"></th>
				<th<?if($today==0) echo ' class="current"'?>>Sun</th>
				<th<?if($today==1) echo ' class="current"'?>>Mon</th>
				<th<?if($today==2) echo ' class="current"'?>>Tues</th>
				<th<?if($today==3) echo ' class="current"'?>>Wednes</th>
				<th<?if($today==4) echo ' class="current"'?>>Thurs</th>
				<th<?if($today==5) echo ' class="current"'?>>Fri</th>
				<th<?if($today==6) echo ' class="current"'?>>Satur</th>
			</tr>
<?for($h=8; $h<23; $h++): // just builds the background empty table cells: ?>
			<tr class="time-slots">
				<td rowspan="4" class="hour"><?=$h?>:00</td>
<?	for($tdd=0; $tdd<7; $tdd++):?>
				<td class="slot"></td>
<?	endfor;?>
			</tr>
<?	for($tdi=0; $tdi<3; $tdi++):?>
			<tr class="time-slots">
<?		for($tdd=0; $tdd<7; $tdd++):?>
				<td class="slot"></td>
<?		endfor;?>
			</tr>
<?	endfor;?>
<?endfor;                 // built. ?>
			<tr class="weekdays">
				<th style="width: 40px;"></th>
				<th<?if($today==0) echo ' class="current"'?>>Sun</th>
				<th<?if($today==1) echo ' class="current"'?>>Mon</th>
				<th<?if($today==2) echo ' class="current"'?>>Tues</th>
				<th<?if($today==3) echo ' class="current"'?>>Wednes</th>
				<th<?if($today==4) echo ' class="current"'?>>Thurs</th>
				<th<?if($today==5) echo ' class="current"'?>>Fri</th>
				<th<?if($today==6) echo ' class="current"'?>>Satur</th>
			</tr>
		</table>
		
		<!-- the next divs are relative positioned after starting at 0 left, 960 from the table:
			Left position is 41 + 77 * day of the week (mon=1, fri=5).
			Height must be duration in minutes - 1 (border).
			Top position is -960px (table height) + headers tr (30px) + minutes from 8AM to starting hour
				- previous divs accummulated height. -->
		
		<!-- EXAMPLE div class="available course slot" style="left: 118px; top: -930px;"><b>Monday Course overfloooooww</b></div -->
	
	
	
<?	$accHgt = 0;

  // cycle the INSTRUCTOR's time-slots:
	
	if($user['User']['type'] == 'faculty') foreach($user['TimeSlot'] as $t=>$timeSlot) {
    $timeSlot['st'] = explode(':', $timeSlot['start_time']);
    $timeSlot['et'] = explode(':', $timeSlot['end_time']);
		$top = -990 + ($timeSlot['st'][0]-7)*60 + $timeSlot['st'][1];
		$left = 41 + 77*$days[$timeSlot['day']]; // todo: set availability with occupied css class
		$tDuration = (60*$timeSlot['et'][0]+$timeSlot['et'][1]) - (60*$timeSlot['st'][0]+$timeSlot['st'][1]);
		if($tDuration < 0) continue; // xxx: ignores wrong data (time-slot begins when its over). ?>

<div class="available course slot" style="<? // xxx: short hover behaves different ?>
	left: <?=$left?>px;
	top: <?=$top - $accHgt?>px;
	height: <?=$tDuration - 1?>px"
	onClick="window.location = '<?=$html->url('/time_slots/'.$timeSlot['id'])?>';">

<?	$accHgt += $tDuration;
		foreach($timeSlot['Lesson'] as $l=>$lesson):	// all calculations are done in minutes unit
		$lesson['st'] = explode(':', $lesson['start_time']);
		$lesson['et'] = explode(':', $lesson['end_time']);
		// xxx: ignores wrong data (lesson begins when its over) & minimum time is 15 min:
		if((60*$lesson['et'][0]+$lesson['et'][1]) < (60*$lesson['st'][0]+$lesson['st'][1]+15)) continue;
		
		$stDiff = (60*$lesson['st'][0]+$lesson['st'][1]) - (60*$timeSlot['st'][0]+$timeSlot['st'][1]);
		$etDiff = (60*$timeSlot['et'][0]+$timeSlot['et'][1]) - (60*$lesson['et'][0]+$lesson['et'][1]);
		
		if($stDiff<0 or $stDiff>($tDuration-15)) continue;	// xxx: ignores weird data: lesson out of timeslot bounds
		if($etDiff<0 or $etDiff>($tDuration-15)) continue;	// & minimum time is 15 min check
		
		$lDuration = (60*$lesson['et'][0]+$lesson['et'][1]) - (60*$lesson['st'][0]+$lesson['st'][1]);
?>
	<div class="occupied slot" style="position: absolute; left: 0;<? // absolute position works INSIDE a relative positioned div :)?>
		top: <?=$stDiff - 1?>px;
		height: <?=$lDuration - 1?>px;"></div>
<?	endforeach;	// end for each lesson in time-slot in course. ?>
			
	<div class="slot_txt">
		<?=$html->link($timeSlot['course_id']
		.' <b>'.$timeSlot['Course']['title'].'</b>'
		.' @ '.$timeSlot['location'], '/time_slots/'.$timeSlot['id'], null, array(), false).'<br/>'
		.substr($timeSlot['start_time'], 0, 5).'-'.substr($timeSlot['end_time'], 0, 5)?>
	</div>
	
</div>
<?


// *** *** STUDENT COURSES SCHEDULE *** ***


	}	// (end of faculty for/foreach)
	// 1. cycle the student's courses:
	elseif($user['User']['type'] == 'student'/*xxx: should be if db congruent*/) { foreach($user['Course'] as $c=>$course):
		$unscheduled = array();
		$lessoned = array();
		// 2. cycle the time-slots for a course the student is registered to:
		foreach($course['TimeSlot'] as $timeSlot):
		// +indent //
    $class = 'available'; // (1) the course us here assumed to be pending to schedule...
		// (1) if the time-slot for this course has a lesson, the time-slot is known to be scheduled:
		if($class=='available' and !empty($timeSlot['Lesson'])) $class = 'optional';
    $timeSlot['st'] = explode(':', $timeSlot['start_time']);
    $timeSlot['et'] = explode(':', $timeSlot['end_time']);
    $top = -990 + ($timeSlot['st'][0]-7)*60 + $timeSlot['st'][1];
		$left = 41 + 77*$days[$timeSlot['day']]; // todo: set availability with occupied css class
		$tDuration = (60*$timeSlot['et'][0]+$timeSlot['et'][1]) - (60*$timeSlot['st'][0]+$timeSlot['st'][1]);
		if($tDuration < 0) continue; // xx: ignores wrong data (time-slot begins when its over)
		
		// (2) displays the time-slot: ?>

<div class="<?=$class?><?=$timeSlot['id']==$ot?' active':null?> course slot" style="
	left: <?=$left?>px;
	top: <?=$top - $accHgt?>px;
	height: <?=$tDuration -1?>px;
	<?=empty($timeSlot['Lesson'][0])?" color: Black;":null//	(2) if no lesson in this time-slot, black font. ?>
	z-index: <?=$timeSlot['id']==$ot?'2':'1'?>;"
	onClick="window.location = '<?=$html->url("/time_slots/{$timeSlot['id']}/{$user['User']['id']}")?>';"><?=$timeSlot['id']==$ot?'<a id="slot_a" name="slot"></a>':null?>

<?	$accHgt += $tDuration + ($timeSlot['id']==$ot?1:0);
		// 2.1 if he has his lesson here already scheduled:
		if(!empty($timeSlot['Lesson'][0])): $lesson = $timeSlot['Lesson'][0];	// there should only be 1 xx: REST IGNORED
			$lesson['st'] = explode(':', $lesson['start_time']);
			$lesson['et'] = explode(':', $lesson['end_time']);
			// xxx: ignores wrong data (lesson begins when its over) & minimum time is 15 min:
			if((60*$lesson['et'][0]+$lesson['et'][1]) < (60*$lesson['st'][0]+$lesson['st'][1]+15)) continue;
			
			$stDiff = (60*$lesson['st'][0]+$lesson['st'][1]) - (60*$timeSlot['st'][0]+$timeSlot['st'][1]);
			$etDiff = (60*$timeSlot['et'][0]+$timeSlot['et'][1]) - (60*$lesson['et'][0]+$lesson['et'][1]);
			
			if($stDiff<0 or $stDiff>($tDuration-15)) continue;	// xxx: ignores weird data: lesson out of timeslot bounds
			if($etDiff<0 or $etDiff>($tDuration-15)) continue;	// & minimum time is 15 min check
			
			$lDuration = (60*$lesson['et'][0]+$lesson['et'][1]) - (60*$lesson['st'][0]+$lesson['st'][1]);
			
			// (2.1) displays the lesson time-block: ?>

	<div class="occupied lesson slot" style="position: absolute; left: 0;
		top: <?=$stDiff - 1?>px;
		height: <?=$lDuration - 1?>px;"></div>
			
<?	$lessoned[$c] = true;						// builds lessoned courses-array keys array ...
		// 2.2 if she doesn't have a lesson yet:
		else: $unscheduled[$c] = true;	// "      unscheduled "          "    "
		endif; ?>

	<div class="slot_txt">
		<?=$html->link($course['id']
		.' <b>'.$course['title'].'</b>'
		.' w/ '.$timeSlot['Faculty']['first_name'].' '.$timeSlot['Faculty']['last_name']
		.' @ '.$timeSlot['location'], "/time_slots/{$timeSlot['id']}/{$user['User']['id']}", null, array(), false).'<br/>'
    .substr($timeSlot['start_time'], 0, 5).'-'.substr($timeSlot['end_time'], 0, 5)?>
	</div>
	
</div>
		
<?endforeach;
	// -indent //
	endforeach; } ?>
	</div>
	
	<div id="right_column">
<?

// *** RIGHT COLUMN ***

	if($user['User']['type']=='faculty' and $session->read('User.type')!='faculty'): ?>

	<h3 class="txt-ulined" style="margin-top: 200px;">Schedule a Lesson</h3>
	
	<script type="text/javascript">
document.write(
	'<select style="max-width: 166px;" onChange="if(this.value != 0) window.location = \'<?=$html->url('/time_slots')?>\' + \'/\' + this.value">'
+	'	<option value="0"> - choose one - </option>'
<?	foreach($user['TimeSlot'] as $timeSlot): ?>
+	'	<option value="<?=$timeSlot['id']?>"><?=$timeSlot['Course']['title'].'</b> on '.$timeSlot['day']?></option>'
<?	endforeach; ?>
+	'</select>'
+	'<p style="color: #666;">&nbsp;you may also click on any available time-slot on the schedule view (left)</p>'
)
	</script>
	<noscript><form class="right-bar right" action="<?=$html->url('/users/route/time_slots/null')?>" method="POST">
		<select class="right-bar" name="data[Param][2]">
			<option value="0"> - choose one - </option>
<?	foreach($user['TimeSlot'] as $timeSlot): ?>
			<option value="<?=$timeSlot['id']?>"><?=$timeSlot['Course']['title'].'</b> on '.$timeSlot['day']?></option>
<?	endforeach; ?>
		</select><div class="clear kicker"></div>
		<button class="right" type="submit">Go</button>
		<p style="color: #666;">&nbsp;you may also click on any available time-slot course text on the schedule view (left)</p>
	</form></noscript>
	
<?elseif($user['User']['type'] == 'student'):	// xx: should always be student in this else ?>

	<div class="active right-bar right" style="width: 160px; margin-top:125px;">
		<h4 class="padded">Select Active Slot</h4>
	
<?$javascript->link('prototype.js', false); ?>
	<script type="text/javascript">
document.write(
	'	<p class="padded kicker">Bring a time-slot to the schedule&#39;s front.'
+	'		<span style="color: #666;">Others may overlap in the background.</span></p>'
		
+	'	<select id="Param2" style="max-width: 160px;" onChange="if(this.value != 0 && this.value != <?=$ot?>) window.location = \'<?=$html->url("/students/schedule/{$user['User']['id']}")?>\' + \'/\' + this.value + \'#slot\'">'
+	'		<option value="0"> - choose one - </option>'
<?	foreach($user['Course'] as $course) foreach($course['TimeSlot'] as $timeSlot): ?>
+	'		<option value="<?=$timeSlot['id']?>"<?=$timeSlot['id']==$ot?' selected="selected"':null?>><?=$course['title'].'</b> on '.$timeSlot['day']?></option>'
<?	 endforeach; ?>
+	'	</select>'
		
+	'	<p class="padded" style="color: #666; text-align: right;">'
+	'		<?=$html->link('Schedule this -&gt;', 'javascript:if($("Param2").value != 0) window.location = \''.$html->url('/time_slots').'/\''.' + $(\'Param2\').value + '.'\'/'.$user['User']['id']."'", array(), null, false)?><br/>'
+	'		&nbsp;you may also click on any time-slot in the schedule.'
+	'	</p>'
)
	</script>
	<noscript>
		<p class="padded kicker">Bring a time-slot to the schedule&#39;s front.
			<span style="color: #666;">Others may overlap in the background.</span></p>
	
		<form class="right" style="width: 150px;" action="<?=$html->url('/users/route/students/schedule/'.$user['User']['id'])?>" method="POST">
			<select style="max-width: 150px;" name="data[Param][2]">
				<option value="0"> - choose one - </option>
<?	foreach($user['Course'] as $course) foreach($course['TimeSlot'] as $timeSlot): ?>
				<option value="<?=$timeSlot['id']?>"<?=$timeSlot['id']==$ot?' selected="selected"':null?>><?=$course['title'].'</b> on '.$timeSlot['day']?></option>
<?	 endforeach; ?>
			</select><div class="clear kicker"></div>
			<button class="right" type="submit">Go</button>
			
			<p style="color: #666;">
				&nbsp;you may also click on any time-slot in the schedule.
			</p>
		</form>
	</noscript>
		
	</div>
	
<?endif; ?>
	
	</div>
	
</div>
<div class="clear"></div>

<?//pr($user)	// debug ?>