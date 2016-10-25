<h2>Register Student <?=$student['User']['first_name'].' '.$student['User']['last_name']?> to a Course</h2>

<?//pr($this->data) // debug?>



<?if(!empty($courses)):?>
<?=$form->create('Course', array('style'=>'background: #ff0;','url'=>'/admin/students/register/'.$student['User']['id']))?>
	<?//=$form->hidden('Student.id')?>

	<h3>Choose a Course (search results)</h3>
	
	<label for="CourseId">Found:</label>
	<select id="CourseId" name="data[Course][id]">
<?	if(count($courses) > 1): ?>
		<option value="" selected="selected">click to see options</option>
<?	endif; ?>
<?	foreach($courses as $c=>$course): ?>
		<option value="<?=$course['id']?>"><?=$course['id'].' <b>'.$course['title'].'</b>'?></option>
<?	endforeach; ?>
	</select>
	
	<label for="CourseCredits">for:</label>
	<select id="CourseCredits" name="data[Course][credits]">
		<option value=""></option>
		<option value="2">2</option>
		<option value="2"><?=$student['User']['status']=='m'?'3':'4'?></option>
	</select> credits
	
	<button type="submit"><?=$html->image('icons/forward.png')?> Register</button>

</form>
<?endif;?>


<?if(!empty($student['Course'])):?>
<p style="padding-left: 5px;">
This <b><?=$student['User']['status']=='n'?'non-':null?>major</b> student is already registered to:
</p>
<?endif;?>

<ul>
<?$confim_message = 'This will permanently delete this period\'s scheduled lessons for this student-course (student and course will be preserved in the system).';
	foreach($student['Course'] as $course):?>
	<li><?=$course['id'].' <b>'.$html->link($course['title'], '/admin/courses/'.$course['id']).'</b> for '.$course['CoursesUser']['credits'].' credits '
		.$html->link($html->image('icons/delete16.png').' Drop',
			'/admin/students/drop/'.$student['User']['id'].'/'.$course['id'], array(), $confim_message, false)?></li>
<?endforeach;?>
</ul>

<p style="padding-left: 5px;">The course(s) above will not show in search results.</p>



<?=$form->create('Course', array('class'=>'isolated', 'url'=>'/admin/students/register/'.$student['User']['id']))?>
	<?//=$form->hidden('Student.id')?>

	<h3>Find a Course (given this period)</h3>
	
	<div class="mini message">Search by any combination of the following parameters.</div>

	<label for="CourseArea">Area</label>,
	<label class="short" for="CourseNumber">Number</label>,
	<label class="short" for="CourseSection">Section</label>
	
	<br/>
	
	<select id="CourseArea" name="data[Course][area]">
		<option value="" selected="selected">Any</option>
		<option value="E85">E85</option>
		<option value="E89">E89</option>
	</select> .
	<input class="small" id="CourseNumber" name="data[Course][number]"/> .
	<input class="short" id="CourseSection" name="data[Course][section]"/> and/or
	
	<div class="clear"></div>
	
	<label for="CourseTitle">Title:</label>
	<input id="CourseTitle" name="data[Course][title]"/>
	
	<?// www: by professor name?>
	
	<button type="submit"><?=$html->image('icons/search.png')?> Search</button>

</form>

<?//pr($student) // debug?>

<?//pr($courses) // debug?>